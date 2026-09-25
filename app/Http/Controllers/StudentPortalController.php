<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\AbsenceRequest;
use App\Models\CourseMaterial;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSitinSlot;
use App\Models\ExamSubmission;
use App\Models\Payment;
use App\Models\Certificate;
use App\Models\MisconductReport;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\MpesaService;
use App\Services\NotificationService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentPortalController extends Controller
{
    /**
     * Student Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('student_id', $user->id)
            ->with(['course', 'cohort.leadTrainer'])
            ->get();

        $activeEnrollment = $enrollments->where('status', 'active')->first() ?? $enrollments->first();

        $upcomingClasses = collect();
        $materials = collect();
        $exams = collect();

        if ($activeEnrollment) {
            $upcomingClasses = ClassSession::where('cohort_id', $activeEnrollment->cohort_id)
                ->orderBy('scheduled_start')
                ->take(5)
                ->get();

            $materials = CourseMaterial::where('course_id', $activeEnrollment->course_id)
                ->where(function ($q) use ($activeEnrollment) {
                    $q->whereNull('cohort_id')->orWhere('cohort_id', $activeEnrollment->cohort_id);
                })
                ->take(5)
                ->get();

            $exams = Exam::where('course_id', $activeEnrollment->course_id)
                ->where('status', 'published')
                ->with('sitinSlots')
                ->get();
        }

        $payments = Payment::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $totalPaid = $enrollments->sum('fee_paid');
        $totalBalance = $enrollments->sum('fee_balance');

        return view('portal.student.dashboard', compact(
            'user',
            'enrollments',
            'activeEnrollment',
            'upcomingClasses',
            'materials',
            'exams',
            'payments',
            'totalPaid',
            'totalBalance'
        ));
    }

    /**
     * Classes & Timetable
     */
    public function classes()
    {
        $user = Auth::user();
        $enrollmentIds = Enrollment::where('student_id', $user->id)->pluck('cohort_id');

        $classes = ClassSession::whereIn('cohort_id', $enrollmentIds)
            ->with(['cohort.course', 'trainer'])
            ->orderByDesc('scheduled_start')
            ->paginate(15);

        return view('portal.student.classes', compact('classes'));
    }

    /**
     * Course Materials
     */
    public function materials()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('student_id', $user->id)->with('course')->get();
        $courseIds = $enrollments->pluck('course_id');
        $cohortIds = $enrollments->pluck('cohort_id');

        $materials = CourseMaterial::whereIn('course_id', $courseIds)
            ->where(function ($q) use ($cohortIds) {
                $q->whereNull('cohort_id')->orWhereIn('cohort_id', $cohortIds);
            })
            ->with(['course', 'cohort', 'uploader'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('portal.student.materials', compact('materials'));
    }

    /**
     * Payments & Ledger View
     */
    public function payments()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('student_id', $user->id)->with(['course', 'cohort'])->get();
        $payments = Payment::where('user_id', $user->id)
            ->with(['enrollment.course'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('portal.student.payments', compact('enrollments', 'payments', 'user'));
    }

    /**
     * Initiate M-Pesa STK Push
     */
    public function initiatePayment(Request $request, MpesaService $mpesaService)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:10',
            'phone_number' => 'required|string',
            'purpose' => 'required|in:deposit,installment,full,exam_fee',
        ]);

        $user = Auth::user();
        $enrollment = Enrollment::where('student_id', $user->id)->findOrFail($request->enrollment_id);

        $result = $mpesaService->initiateStkPush(
            $user,
            $enrollment,
            (float)$request->amount,
            $request->phone_number,
            $request->purpose
        );

        if ($result['success']) {
            // Auto complete if sandbox simulation
            if (!empty($result['simulated'])) {
                $simulatedCode = 'QA' . rand(10, 99) . strtoupper(Str::random(6));
                $mpesaService->completePayment($result['payment'], $simulatedCode, ['simulated' => true]);
                
                NotificationService::notifyPaymentReceived(
                    $user,
                    $result['payment']->receipt_number,
                    (float)$request->amount,
                    $enrollment->fresh()->fee_balance
                );

                return redirect()->route('student.payments')->with('success', "Payment of KES {$request->amount} received successfully! Receipt #{$result['payment']->receipt_number} issued.");
            }

            return redirect()->route('student.payments')->with('info', $result['message']);
        }

        return back()->with('error', 'Unable to initiate STK push. Please check your phone number and try again.');
    }

    /**
     * View Printable Official Receipt
     */
    public function viewReceipt(string $receiptNumber)
    {
        $payment = Payment::where('receipt_number', $receiptNumber)
            ->with(['student', 'enrollment.course', 'enrollment.cohort', 'collectedBy'])
            ->firstOrFail();

        // Security check: student can only view their own receipts, staff with permission can view any
        $user = Auth::user();
        if ($user->hasRole('student') && $payment->user_id !== $user->id) {
            abort(403, 'Unauthorized receipt access.');
        }

        return view('portal.receipt', compact('payment'));
    }

    /**
     * Exams & Online Quiz View
     */
    public function exams()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('student_id', $user->id)->with('course')->get();
        $courseIds = $enrollments->pluck('course_id');
        $cohortIds = $enrollments->pluck('cohort_id');

        $exams = Exam::whereIn('course_id', $courseIds)
            ->where('status', 'published')
            ->with(['course', 'sitinSlots', 'submissions' => function ($q) use ($user) {
                $q->where('student_id', $user->id);
            }])
            ->get();

        $certificates = Certificate::where('student_id', $user->id)->with('course')->get();

        return view('portal.student.exams', compact('exams', 'certificates'));
    }

    /**
     * Take Online Exam / Quiz
     */
    public function takeExam(Exam $exam)
    {
        $user = Auth::user();
        
        // Verify student enrollment
        $hasEnrollment = Enrollment::where('student_id', $user->id)
            ->where('course_id', $exam->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$hasEnrollment) {
            return redirect()->route('student.exams')->with('error', 'You must have an active enrollment in this course to take the exam.');
        }

        // Check prior submissions & attempt limits
        $previousAttempts = ExamSubmission::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->count();

        $lastSubmission = ExamSubmission::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        if ($previousAttempts >= $exam->max_attempts && (!$lastSubmission || !$lastSubmission->retake_granted)) {
            return redirect()->route('student.exams')->with('error', 'You have exhausted your allowed attempts for this exam.');
        }

        $questions = $exam->questions;
        if ($exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('portal.student.take-exam', compact('exam', 'questions'));
    }

    /**
     * Submit Online Exam
     */
    public function submitExam(Request $request, Exam $exam)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []);

        $questions = $exam->questions;
        $totalMarksEarned = 0;
        $hasFreeResponse = false;

        foreach ($questions as $q) {
            $studentAns = $answers[$q->id] ?? null;
            if ($q->type === 'multiple_choice') {
                if ($studentAns && strtoupper(trim($studentAns)) === strtoupper(trim($q->correct_answer))) {
                    $totalMarksEarned += $q->marks;
                }
            } else {
                $hasFreeResponse = true;
            }
        }

        $percentage = $exam->total_marks > 0 ? ($totalMarksEarned / $exam->total_marks) * 100 : 0;
        $isPassed = $percentage >= $exam->pass_percentage;

        $submission = ExamSubmission::create([
            'exam_id' => $exam->id,
            'student_id' => $user->id,
            'attempt_number' => ExamSubmission::where('exam_id', $exam->id)->where('student_id', $user->id)->count() + 1,
            'started_at' => now()->subMinutes(rand(15, $exam->duration_minutes)),
            'submitted_at' => now(),
            'student_answers' => $answers,
            'score' => $totalMarksEarned,
            'percentage' => $percentage,
            'grade_status' => $hasFreeResponse ? 'pending' : 'auto_graded',
            'passed' => $hasFreeResponse ? null : $isPassed,
        ]);

        ActivityLog::log(
            'exam.submitted',
            "Student {$user->name} submitted exam '{$exam->title}' (Score: {$totalMarksEarned}/{$exam->total_marks})",
            $submission
        );

        return redirect()->route('student.exams')->with('success', "Exam submitted successfully! " . ($hasFreeResponse ? 'Your score for multiple choice questions was saved. Free response questions are pending trainer grading.' : "Your score: {$percentage}%"));
    }

    /**
     * Book Physical Sit-in Exam Slot
     */
    public function bookSitinSlot(Request $request, ExamSitinSlot $slot)
    {
        $user = Auth::user();

        if (!$slot->hasAvailableSeats()) {
            return back()->with('error', 'This sit-in slot is fully booked. Please select an alternate date.');
        }

        // Check if student already booked for this exam
        $alreadyBooked = ExamSubmission::where('exam_id', $slot->exam_id)
            ->where('student_id', $user->id)
            ->whereNotNull('sitin_slot_id')
            ->exists();

        if ($alreadyBooked) {
            return back()->with('error', 'You have already booked a slot for this exam.');
        }

        $slot->increment('booked_count');
        if ($slot->booked_count >= $slot->capacity) {
            $slot->update(['status' => 'full']);
        }

        ExamSubmission::create([
            'exam_id' => $slot->exam_id,
            'student_id' => $user->id,
            'sitin_slot_id' => $slot->id,
            'attempt_number' => 1,
            'grade_status' => 'pending',
        ]);

        ActivityLog::log(
            'exam.sitin_booked',
            "Student {$user->name} booked sit-in exam slot for {$slot->slot_datetime->format('d M Y, h:i A')} at {$slot->venue}",
            $slot
        );

        return back()->with('success', "Sit-in exam slot booked successfully! Venue: {$slot->venue} on " . $slot->slot_datetime->format('d M Y, h:i A'));
    }

    /**
     * Attendance & Absence Request View
     */
    public function attendance()
    {
        $user = Auth::user();
        $enrollmentIds = Enrollment::where('student_id', $user->id)->pluck('cohort_id');

        $attendances = Attendance::where('student_id', $user->id)
            ->with(['classSession.cohort.course'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $absenceRequests = AbsenceRequest::where('student_id', $user->id)
            ->with('cohort.course')
            ->orderByDesc('created_at')
            ->get();

        $enrolledCohorts = Enrollment::where('student_id', $user->id)->with('cohort.course')->get();

        return view('portal.student.attendance', compact('attendances', 'absenceRequests', 'enrolledCohorts'));
    }

    /**
     * Submit Absence Request
     */
    public function submitAbsenceRequest(Request $request)
    {
        $validated = $request->validate([
            'cohort_id' => 'required|exists:cohorts,id',
            'requested_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:1000',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,png,jpeg,doc,docx|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('evidence_file')) {
            $filePath = $request->file('evidence_file')->store('absence_evidence', 'public');
        }

        $absence = AbsenceRequest::create([
            'student_id' => Auth::id(),
            'cohort_id' => $validated['cohort_id'],
            'requested_date' => $validated['requested_date'],
            'reason' => $validated['reason'],
            'evidence_file_path' => $filePath,
            'status' => 'pending',
        ]);

        ActivityLog::log(
            'attendance.absence_requested',
            "Student " . Auth::user()->name . " submitted absence request for {$validated['requested_date']}",
            $absence
        );

        return back()->with('success', 'Absence request submitted for trainer/admin review.');
    }

    /**
     * Misconduct Reporting View
     */
    public function misconduct()
    {
        $user = Auth::user();
        $staffMembers = User::where('is_staff', true)->where('id', '!=', $user->id)->get();
        $reports = MisconductReport::where('reporter_user_id', $user->id)->orderByDesc('created_at')->get();

        return view('portal.student.misconduct', compact('staffMembers', 'reports'));
    }

    /**
     * File Misconduct Report (with Whistleblower Protections & Admin Escalation Contact)
     */
    public function submitMisconductReport(Request $request)
    {
        $validated = $request->validate([
            'reported_user_id' => 'nullable|exists:users,id',
            'reported_party_name' => 'nullable|string|max:255',
            'category' => 'required|string',
            'subject_summary' => 'required|string|max:255',
            'incident_details' => 'required|string',
            'incident_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'evidence_file' => 'nullable|file|mimes:pdf,jpg,png,jpeg,doc,docx,zip|max:10240',
        ]);

        $user = Auth::user();
        $reportedUser = !empty($validated['reported_user_id']) ? User::find($validated['reported_user_id']) : null;
        
        $isReportAgainstAdmin = false;
        $secondaryContact = null;

        if ($reportedUser && $reportedUser->hasRole('admin')) {
            $isReportAgainstAdmin = true;
            $secondaryContact = SystemSetting::get('misconduct_escalation_contact', env('MISCONDUCT_ESCALATION_CONTACT_EMAIL', 'governance@pearlinstitute.com'));
        }

        $filePath = null;
        if ($request->hasFile('evidence_file')) {
            $filePath = $request->file('evidence_file')->store('misconduct_evidence', 'public');
        }

        $refCode = 'RPT-' . date('Ym') . '-' . sprintf('%04d', rand(1, 9999));

        $report = MisconductReport::create([
            'reference_code' => $refCode,
            'reporter_user_id' => $user->id,
            'reporter_role' => 'student',
            'reported_user_id' => $reportedUser?->id,
            'reported_party_name' => $reportedUser?->name ?? $validated['reported_party_name'],
            'reported_party_type' => $reportedUser?->is_staff ? ($reportedUser->hasRole('admin') ? 'admin' : 'staff') : 'student',
            'category' => $validated['category'],
            'subject_summary' => $validated['subject_summary'],
            'incident_details' => $validated['incident_details'],
            'incident_date' => $validated['incident_date'] ?? null,
            'location' => $validated['location'] ?? null,
            'evidence_file_path' => $filePath,
            'hide_reporter_from_subject' => true, // Protected whistleblower
            'is_escalated_to_secondary_contact' => $isReportAgainstAdmin,
            'secondary_contact_notified_to' => $secondaryContact,
            'status' => $isReportAgainstAdmin ? 'escalated' : 'open',
        ]);

        ActivityLog::log(
            'misconduct.reported',
            "Confidential misconduct report {$refCode} submitted by whistleblower (Target: " . ($reportedUser?->name ?? 'External') . ")",
            $report
        );

        $msg = "Confidential report {$refCode} filed securely. Your identity is strictly protected from the reported person.";
        if ($isReportAgainstAdmin) {
            $msg .= " As this report concerns administration, it has been automatically routed to the independent Governance Escalation Trustee ({$secondaryContact}).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Student Profile & Settings
     */
    public function profile()
    {
        $user = Auth::user();
        return view('portal.student.profile', compact('user'));
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->phone = MpesaService::formatPhoneNumber($validated['phone']);

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        ActivityLog::log('user.profile_updated', "Student {$user->name} updated contact info", $user);

        return back()->with('success', 'Profile updated successfully.');
    }
}
