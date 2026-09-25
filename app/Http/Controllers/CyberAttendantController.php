<?php

namespace App\Http\Controllers;

use App\Models\PrintshopService;
use App\Models\ServiceRequest;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\MpesaService;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CyberAttendantController extends Controller
{
    /**
     * Cyber Attendant Dashboard & Queue
     */
    public function dashboard()
    {
        $services = PrintshopService::where('is_active', true)->get();

        $pendingRequests = ServiceRequest::where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $inProgressRequests = ServiceRequest::where('status', 'in_progress')
            ->orderByDesc('created_at')
            ->get();

        $recentCompleted = ServiceRequest::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->orderByDesc('completed_at')
            ->get();

        // Today's summary metrics
        $todayRevenue = Payment::where('collected_by_user_id', Auth::id())
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('amount');

        $todayJobsCount = ServiceRequest::where('handled_by_user_id', Auth::id())
            ->whereDate('completed_at', today())
            ->where('status', 'completed')
            ->count();

        return view('portal.cyber.dashboard', compact(
            'services',
            'pendingRequests',
            'inProgressRequests',
            'recentCompleted',
            'todayRevenue',
            'todayJobsCount'
        ));
    }

    /**
     * Log Walk-in Cyber / Print Request
     */
    public function storeWalkinRequest(Request $request, MpesaService $mpesaService)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'service_type' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,mpesa_manual',
            'mpesa_receipt_number' => 'nullable|required_if:payment_method,mpesa_manual|string',
            'instructions' => 'nullable|string',
        ]);

        $requestCode = 'SR-' . date('Ym') . '-' . sprintf('%04d', rand(1000, 9999));
        $amount = (float)$validated['amount_paid'];

        $payment = null;
        if ($amount > 0) {
            if ($validated['payment_method'] === 'cash') {
                $payment = $mpesaService->recordCashPayment(
                    Auth::user(),
                    null,
                    null,
                    $amount,
                    'printshop',
                    "Printshop service '{$validated['service_type']}' for {$validated['customer_name']}"
                );
            } else {
                $payment = $mpesaService->recordManualMpesaPayment(
                    Auth::user(),
                    null,
                    null,
                    $amount,
                    $validated['mpesa_receipt_number'],
                    $validated['customer_phone'],
                    'printshop',
                    "Printshop M-Pesa '{$validated['service_type']}' for {$validated['customer_name']}"
                );
            }
        }

        $serviceRequest = ServiceRequest::create([
            'request_code' => $requestCode,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => MpesaService::formatPhoneNumber($validated['customer_phone']),
            'service_type' => $validated['service_type'],
            'quantity' => $validated['quantity'],
            'instructions' => $validated['instructions'] ?? null,
            'quoted_amount' => $amount,
            'paid_amount' => $amount,
            'payment_status' => $amount > 0 ? 'paid' : 'unpaid',
            'status' => 'completed',
            'payment_id' => $payment?->id,
            'handled_by_user_id' => Auth::id(),
            'completed_at' => now(),
            'staff_notes' => 'Walk-in service completed at counter.',
        ]);

        ActivityLog::log(
            'printshop.walkin_served',
            "Cyber attendant " . Auth::user()->name . " served walk-in customer {$validated['customer_name']} (KES {$amount})",
            $serviceRequest
        );

        return back()->with('success', "Service recorded successfully! Request Code: {$requestCode} " . ($payment ? "(Receipt #{$payment->receipt_number})" : ''));
    }

    /**
     * Update Request Status (pending -> in_progress -> completed)
     */
    public function updateRequestStatus(Request $request, ServiceRequest $serviceRequest, MpesaService $mpesaService)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'amount_collected' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,mpesa_manual',
            'mpesa_receipt_number' => 'nullable|string',
            'staff_notes' => 'nullable|string',
        ]);

        $amount = (float)($validated['amount_collected'] ?? 0);
        $payment = $serviceRequest->payment;

        if ($amount > 0 && !$payment) {
            $method = $validated['payment_method'] ?? 'cash';
            if ($method === 'cash') {
                $payment = $mpesaService->recordCashPayment(
                    Auth::user(),
                    null,
                    null,
                    $amount,
                    'printshop',
                    "Service collection for {$serviceRequest->request_code} ({$serviceRequest->customer_name})"
                );
            } else {
                $payment = $mpesaService->recordManualMpesaPayment(
                    Auth::user(),
                    null,
                    null,
                    $amount,
                    $validated['mpesa_receipt_number'] ?? 'M-PESA-VERIFIED',
                    $serviceRequest->customer_phone,
                    'printshop',
                    "Service collection M-Pesa for {$serviceRequest->request_code}"
                );
            }
            $serviceRequest->payment_id = $payment->id;
            $serviceRequest->paid_amount = $amount;
            $serviceRequest->payment_status = 'paid';
        }

        $serviceRequest->status = $validated['status'];
        $serviceRequest->handled_by_user_id = Auth::id();
        $serviceRequest->staff_notes = $validated['staff_notes'] ?? $serviceRequest->staff_notes;

        if ($validated['status'] === 'completed') {
            $serviceRequest->completed_at = now();
        }

        $serviceRequest->save();

        ActivityLog::log(
            'printshop.status_updated',
            "Updated service request {$serviceRequest->request_code} status to '{$validated['status']}'",
            $serviceRequest
        );

        return back()->with('success', "Service request {$serviceRequest->request_code} updated to '{$validated['status']}'.");
    }

    /**
     * Collect Cash for Enrolled Course (if granted permission)
     */
    public function collectCoursePayment(Request $request, MpesaService $mpesaService)
    {
        if (!Auth::user()->hasPermission('payment.collect_cash') && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not hold permission to collect student course fees.');
        }

        $validated = $request->validate([
            'student_identifier' => 'required|string',
            'amount' => 'required|numeric|min:10',
            'purpose' => 'required|in:deposit,installment,full,exam_fee',
            'payment_method' => 'required|in:cash,mpesa_manual',
            'mpesa_receipt_number' => 'nullable|required_if:payment_method,mpesa_manual|string',
            'notes' => 'nullable|string',
        ]);

        $identifier = trim($validated['student_identifier']);
        $student = User::where('admission_number', $identifier)
            ->orWhere('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhere('phone', MpesaService::formatPhoneNumber($identifier))
            ->first();

        if (!$student) {
            return back()->withErrors(['student_identifier' => 'Student not found with that Admission Number, email, or phone.']);
        }

        $enrollment = Enrollment::where('student_id', $student->id)->whereIn('status', ['active', 'pending_approval'])->first();
        if (!$enrollment) {
            return back()->withErrors(['student_identifier' => 'Student does not have an active or pending course enrollment.']);
        }

        $amount = (float)$validated['amount'];
        $receipt = null;

        if ($validated['payment_method'] === 'cash') {
            $receipt = $mpesaService->recordCashPayment(
                Auth::user(),
                $student,
                $enrollment,
                $amount,
                $validated['purpose'],
                $validated['notes'] ?? "Course fee collected by Cyber Attendant " . Auth::user()->name
            );
        } else {
            $receipt = $mpesaService->recordManualMpesaPayment(
                Auth::user(),
                $student,
                $enrollment,
                $amount,
                $validated['mpesa_receipt_number'],
                $student->phone ?? '0700000000',
                $validated['purpose'],
                $validated['notes'] ?? "Manual M-Pesa verified by Cyber Attendant " . Auth::user()->name
            );
        }

        return back()->with('success', "Payment of KES " . number_format($amount, 2) . " recorded for {$student->name} (Adm: {$student->admission_number}). Receipt #{$receipt->receipt_number} issued.");
    }

    /**
     * Daily Shift & Summary Report
     */
    public function dailySummary(Request $request)
    {
        $selectedDate = $request->query('date', today()->toDateString());

        $payments = Payment::where('collected_by_user_id', Auth::id())
            ->whereDate('created_at', $selectedDate)
            ->with(['student', 'enrollment.course'])
            ->get();

        $completedJobs = ServiceRequest::where('handled_by_user_id', Auth::id())
            ->whereDate('completed_at', $selectedDate)
            ->where('status', 'completed')
            ->get();

        $totalCash = $payments->where('payment_method', 'cash')->sum('amount');
        $totalMpesa = $payments->whereIn('payment_method', ['mpesa_manual', 'mpesa_stk', 'mpesa_c2b'])->sum('amount');
        $totalPrintshopRevenue = $payments->where('purpose', 'printshop')->sum('amount');
        $totalCourseRevenue = $payments->whereIn('purpose', ['deposit', 'installment', 'full', 'exam_fee'])->sum('amount');

        return view('portal.cyber.daily-summary', compact(
            'selectedDate',
            'payments',
            'completedJobs',
            'totalCash',
            'totalMpesa',
            'totalPrintshopRevenue',
            'totalCourseRevenue'
        ));
    }
}
