<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Course;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\ServiceRequest;
use App\Models\PrintshopService;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Certificate;
use App\Models\MisconductReport;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Services\AdmissionNumberService;
use App\Services\NotificationService;
use App\Services\StaffMailProvisionerInterface;
use App\Services\ZohoMailProvisioner;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminPortalController extends Controller
{
    /**
     * Admin Overview Dashboard & Analytics
     */
    public function dashboard()
    {
        $totalStudents = User::whereHas('roles', fn($q) => $q->where('name', 'student'))->count();
        $activeEnrollments = Enrollment::where('status', 'active')->count();
        $pendingAdmissions = Enrollment::where('status', 'pending_approval')->count();
        
        $monthFeesIncome = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->whereIn('purpose', ['deposit', 'installment', 'full', 'exam_fee'])
            ->sum('amount');

        $monthPrintshopIncome = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('purpose', 'printshop')
            ->sum('amount');

        $monthRevenue = $monthFeesIncome + $monthPrintshopIncome;

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalCash = Payment::where('status', 'completed')->where('payment_method', 'cash')->sum('amount');
        $totalMpesa = Payment::where('status', 'completed')->whereIn('payment_method', ['mpesa_stk', 'mpesa_c2b', 'mpesa_manual'])->sum('amount');

        $pendingMisconduct = MisconductReport::whereIn('status', ['open', 'investigating', 'escalated'])->count();
        $activeCourses = Course::where('status', 'published')->count();
        $pendingCourses = Course::where('status', 'pending_review')->count();

        $latestPendingStudents = User::whereHas('enrollments', fn($q) => $q->where('status', 'pending_approval'))
            ->with(['enrollments.course', 'enrollments.cohort'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentMisconduct = MisconductReport::with(['reporter', 'reportedUser'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentAuditLogs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $courses = Course::withCount('enrollments')->get();

        return view('portal.admin.dashboard', compact(
            'totalStudents',
            'activeEnrollments',
            'pendingAdmissions',
            'monthRevenue',
            'monthFeesIncome',
            'monthPrintshopIncome',
            'totalRevenue',
            'totalCash',
            'totalMpesa',
            'pendingMisconduct',
            'activeCourses',
            'pendingCourses',
            'latestPendingStudents',
            'recentMisconduct',
            'recentAuditLogs',
            'courses'
        ));
    }

    /**
     * User Management & Role Assignment
     */
    public function users(Request $request)
    {
        $roleFilter = $request->query('role');
        $search = $request->query('q');

        $query = User::with(['roles', 'permissionOverrides']);

        if ($roleFilter) {
            $query->whereHas('roles', fn($q) => $q->where('name', $roleFilter));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(20);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('portal.admin.users', compact('users', 'roles', 'permissions', 'roleFilter', 'search'));
    }

    /**
     * Store New User (Staff or Student)
     */
    public function storeUser(Request $request, ZohoMailProvisioner $mailProvisioner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'roles' => 'required|array|min:1',
            'is_staff' => 'boolean',
            'provision_staff_email' => 'boolean',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => MpesaService::formatPhoneNumber($validated['phone']),
            'is_staff' => $request->boolean('is_staff'),
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync($validated['roles']);

        // Auto-provision staff @pearlinstitute.com mailbox if requested
        if ($request->boolean('provision_staff_email') && $request->boolean('is_staff')) {
            $desiredUsername = Str::slug($user->name, '.');
            $mailProvisioner->provisionStaffAccount($user, $desiredUsername);
        }

        ActivityLog::log('user.created_by_admin', "Admin created user {$user->name} ({$user->email})", $user);

        return back()->with('success', "User {$user->name} created successfully with assigned roles.");
    }

    /**
     * Edit User Roles & Permission Overrides
     */
    public function updateUserPermissions(Request $request, User $user, ZohoMailProvisioner $mailProvisioner)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'is_staff' => 'boolean',
            'status' => 'required|in:active,pending_approval,suspended,withdrawn',
            'provision_staff_email' => 'boolean',
            'overrides' => 'nullable|array', // [permission_id => 'grant'|'revoke'|'default']
        ]);

        $user->roles()->sync($validated['roles']);
        $user->is_staff = $request->boolean('is_staff');
        $user->status = $validated['status'];
        $user->save();

        // Process granular per-user overrides
        if (!empty($validated['overrides'])) {
            $syncData = [];
            foreach ($validated['overrides'] as $permId => $overrideAction) {
                if ($overrideAction === 'grant') {
                    $syncData[$permId] = ['is_granted' => true, 'assigned_by_user_id' => Auth::id()];
                } elseif ($overrideAction === 'revoke') {
                    $syncData[$permId] = ['is_granted' => false, 'assigned_by_user_id' => Auth::id()];
                }
            }
            $user->permissionOverrides()->sync($syncData);
        }

        // Provision staff mailbox if requested and not yet provisioned
        if ($request->boolean('provision_staff_email') && $user->is_staff && $user->staff_email_status !== 'provisioned') {
            $mailProvisioner->provisionStaffAccount($user, Str::slug($user->name, '.'));
        }

        ActivityLog::log(
            'user.roles_permissions_updated',
            "Admin updated roles and permission overrides for {$user->name}",
            $user,
            ['roles' => $user->roles->pluck('name'), 'status' => $user->status]
        );

        return back()->with('success', "Permissions and roles updated for {$user->name}.");
    }

    /**
     * Admissions Management Queue
     */
    public function admissions(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = User::whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->with(['enrollments.course', 'enrollments.cohort']);

        if ($status === 'pending') {
            $query->where(function($q) {
                $q->where('status', 'pending_approval')
                  ->orWhereNull('admission_number')
                  ->orWhereHas('enrollments', fn($sq) => $sq->where('status', 'pending_approval'));
            });
        } elseif ($status === 'active') {
            $query->where('status', 'active')->whereNotNull('admission_number');
        } elseif ($status === 'deactivated') {
            $query->whereIn('status', ['suspended', 'withdrawn', 'deactivated']);
        }

        $students = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $pendingAdmissions = Enrollment::where('status', 'pending_approval')
            ->with(['student', 'course', 'cohort'])
            ->orderByDesc('created_at')
            ->get();

        $activeAdmissions = Enrollment::where('status', 'active')
            ->with(['student', 'course', 'cohort', 'admittedBy'])
            ->orderByDesc('admitted_at')
            ->paginate(15);

        $trainers = User::whereHas('roles', fn($q) => $q->where('name', 'trainer'))->get();
        $cohorts = Cohort::with('course')->where('status', 'enrolling')->get();

        return view('portal.admin.admissions', compact('students', 'pendingAdmissions', 'activeAdmissions', 'trainers', 'cohorts', 'status'));
    }

    /**
     * Approve Pending Admission & Issue Sequential Admission Number
     */
    public function approveAdmission(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);
        if ($enrollment) {
            $student = $enrollment->student;
        } else {
            $student = User::findOrFail($id);
            $enrollment = $student->enrollments()->latest()->first();
        }

        // Generate sequential permanent admission number if not already present
        if (empty($student->admission_number)) {
            $student->admission_number = AdmissionNumberService::generateNextAdmissionNumber();
        }

        $student->status = 'active';
        $student->save();

        if ($enrollment) {
            $enrollment->update([
                'status' => 'active',
                'admitted_by_user_id' => Auth::id(),
                'admitted_at' => now(),
                'admin_notes' => 'Admission approved by Administrator',
            ]);
            // Send welcome email + SMS
            NotificationService::notifyAdmissionApproved($student, $student->admission_number, $enrollment->course->title);
        }

        ActivityLog::log(
            'student.admission_approved',
            "Approved admission for {$student->name} (Issued Admission No: {$student->admission_number})",
            $student,
            ['enrollment_id' => $enrollment?->id, 'admission_number' => $student->admission_number]
        );

        return back()->with('success', "Student {$student->name} approved! Assigned Admission Number: {$student->admission_number}.");
    }

    /**
     * Deactivate / Suspend Student Account
     */
    public function deactivateStudent(Request $request, User $user)
    {
        $user->status = 'suspended';
        $user->deactivated_at = now();
        $user->save();

        ActivityLog::log('student.deactivated', "Admin deactivated student {$user->name} ({$user->admission_number})", $user);

        return back()->with('success', "Student {$user->name} has been deactivated.");
    }

    /**
     * Reassign Student Cohort / Trainer
     */
    public function reassignCohort(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'cohort_id' => 'required|exists:cohorts,id',
        ]);

        $oldCohort = $enrollment->cohort->name;
        $newCohort = Cohort::findOrFail($validated['cohort_id']);

        $enrollment->update(['cohort_id' => $newCohort->id]);

        ActivityLog::log(
            'student.cohort_reassigned',
            "Reassigned student {$enrollment->student->name} from '{$oldCohort}' to '{$newCohort->name}'",
            $enrollment
        );

        return back()->with('success', "Student successfully reassigned to {$newCohort->name}.");
    }

    /**
     * Financial Ledger & Reconciliation
     */
    public function financials(Request $request)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());
        $method = $request->query('method');
        $purpose = $request->query('purpose');

        $query = Payment::with(['student', 'enrollment.course', 'collectedBy'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($method) {
            $query->where('payment_method', $method);
        }
        if ($purpose) {
            $query->where('purpose', $purpose);
        }

        $payments = $query->orderByDesc('created_at')->paginate(20);

        // Financial KPI Metrics
        $totalCollected = (clone $query)->where('status', 'completed')->sum('amount');
        $totalCash = (clone $query)->where('status', 'completed')->where('payment_method', 'cash')->sum('amount');
        $totalMpesa = (clone $query)->where('status', 'completed')->whereIn('payment_method', ['mpesa_stk', 'mpesa_c2b', 'mpesa_manual'])->sum('amount');

        $courseRevenue = (clone $query)->where('status', 'completed')->whereIn('purpose', ['deposit', 'installment', 'full', 'exam_fee'])->sum('amount');
        $printshopRevenue = (clone $query)->where('status', 'completed')->where('purpose', 'printshop')->sum('amount');

        $refunds = Refund::with(['enrollment.student', 'enrollment.course', 'processedBy'])
            ->orderByDesc('created_at')
            ->get();

        $totalRefunds = $refunds->sum('refund_amount');
        $totalForfeited = $refunds->sum('forfeited_deposit_amount');

        $outstandingBalances = Enrollment::where('status', 'active')
            ->where('fee_balance', '>', 0)
            ->with(['student', 'course', 'cohort'])
            ->orderByDesc('fee_balance')
            ->get();

        return view('portal.admin.financials', compact(
            'payments',
            'startDate',
            'endDate',
            'method',
            'purpose',
            'totalCollected',
            'totalCash',
            'totalMpesa',
            'courseRevenue',
            'printshopRevenue',
            'refunds',
            'totalRefunds',
            'totalForfeited',
            'outstandingBalances'
        ));
    }

    /**
     * Process Course Withdrawal & Refund (with Forfeited Deposit Calculation)
     */
    public function processRefund(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'refund_amount' => 'required|numeric|min:0',
            'forfeited_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
        ]);

        $enrollment = Enrollment::with('student', 'course')->findOrFail($validated['enrollment_id']);

        $refundNumber = 'RFD-' . date('Ym') . '-' . sprintf('%04d', rand(100, 9999));

        $refund = Refund::create([
            'refund_number' => $refundNumber,
            'enrollment_id' => $enrollment->id,
            'refund_amount' => $validated['refund_amount'],
            'forfeited_deposit_amount' => $validated['forfeited_amount'],
            'reason' => $validated['reason'],
            'status' => 'approved',
            'processed_by_user_id' => Auth::id(),
        ]);

        $enrollment->update([
            'status' => 'withdrawn',
            'withdrawal_reason' => $validated['reason'],
        ]);

        ActivityLog::log(
            'finance.refund_processed',
            "Processed withdrawal refund of KES {$validated['refund_amount']} (Forfeited: KES {$validated['forfeited_amount']}) for student {$enrollment->student->name}",
            $refund
        );

        return back()->with('success', "Withdrawal refund record {$refundNumber} created successfully.");
    }

    /**
     * Course Management
     */
    public function courses()
    {
        $courses = Course::with(['cohorts', 'creator'])->orderByDesc('created_at')->get();
        $trainers = User::whereHas('roles', fn($q) => $q->where('name', 'trainer'))->get();

        return view('portal.admin.courses', compact('courses', 'trainers'));
    }

    /**
     * Store Course (Admin Full CRUD)
     */
    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'short_description' => 'required|string|max:500',
            'description' => 'required|string',
            'curriculum_outline' => 'required|string',
            'duration_weeks' => 'required|integer|min:1',
            'total_fee' => 'required|numeric|min:0',
            'deposit_required' => 'required|numeric|min:0',
            'requires_guardian_info' => 'boolean',
            'status' => 'required|in:draft,pending_review,published',
            'featured_badge' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $course = Course::create(array_merge($validated, [
            'slug' => Str::slug($validated['title']) . '-' . rand(100, 999),
            'created_by_user_id' => Auth::id(),
            'approved_by_user_id' => Auth::id(),
        ]));

        ActivityLog::log('course.created_by_admin', "Created course '{$course->title}'", $course);

        return back()->with('success', "Course '{$course->title}' created successfully.");
    }

    /**
     * Approve or Update Course Status
     */
    public function updateCourseStatus(Request $request, Course $course)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,pending_review,published',
        ]);

        $course->update([
            'status' => $validated['status'],
            'approved_by_user_id' => Auth::id(),
        ]);

        ActivityLog::log('course.status_updated', "Updated course '{$course->title}' status to '{$validated['status']}'", $course);

        return back()->with('success', "Course status updated to '{$validated['status']}'.");
    }

    /**
     * Direct Publish Approved Course
     */
    public function approveCourseDirect(Request $request, Course $course)
    {
        $course->update([
            'status' => 'published',
            'approved_by_user_id' => Auth::id(),
        ]);

        ActivityLog::log('course.published', "Admin approved and published course '{$course->title}'", $course);

        return back()->with('success', "Course '{$course->title}' is now published and accepting registrations.");
    }

    /**
     * Store Cohort for Course
     */
    public function storeCohort(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:cohorts,code',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_capacity' => 'required|integer|min:1',
            'lead_trainer_id' => 'nullable|exists:users,id',
        ]);

        $cohort = Cohort::create(array_merge($validated, [
            'course_id' => $course->id,
            'status' => 'enrolling',
        ]));

        ActivityLog::log('cohort.created', "Created intake cohort '{$cohort->name}' for {$course->title}", $cohort);

        return back()->with('success', "Cohort '{$cohort->name}' created successfully.");
    }

    /**
     * Misconduct & Whistleblower Reports Inbox
     */
    public function misconductInbox()
    {
        $reports = MisconductReport::with(['reporter', 'reportedUser', 'resolver'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('portal.admin.misconduct', compact('reports'));
    }

    /**
     * Update Misconduct Report Status & Resolution
     */
    public function resolveMisconduct(Request $request, MisconductReport $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,investigating,escalated,resolved,dismissed',
            'resolution_summary' => 'required|string',
        ]);

        $report->update([
            'status' => $validated['status'],
            'resolution_summary' => $validated['resolution_summary'],
            'resolved_by_user_id' => Auth::id(),
            'resolved_at' => now(),
        ]);

        ActivityLog::log(
            'misconduct.resolved',
            "Resolved misconduct report {$report->reference_code} (Status: {$validated['status']})",
            $report
        );

        return back()->with('success', "Misconduct report {$report->reference_code} updated to '{$validated['status']}'.");
    }

    /**
     * Attendance Oversight & Chronic Absenteeism Monitor
     */
    public function attendanceOversight()
    {
        $cohorts = Cohort::with(['course', 'classSessions.attendances'])->get();
        $cohortStats = $cohorts->map(function ($coh) {
            $totalAttendances = 0;
            $presentAttendances = 0;
            foreach ($coh->classSessions as $session) {
                $totalAttendances += $session->attendances->count();
                $presentAttendances += $session->attendances->where('status', 'present')->count();
            }
            $rate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 100;
            return [
                'name' => $coh->name,
                'course' => $coh->course->title,
                'rate' => $rate,
                'present' => $presentAttendances,
                'total' => $totalAttendances,
            ];
        });

        $pendingAbsences = \App\Models\AbsenceRequest::with(['student', 'cohort'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('portal.admin.attendance-oversight', compact('cohortStats', 'pendingAbsences'));
    }

    /**
     * Admin Override Review for Absence Request
     */
    public function reviewAbsenceRequest(Request $request, \App\Models\AbsenceRequest $absenceRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $absenceRequest->update([
            'status' => $validated['status'],
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
            'reviewer_notes' => 'Reviewed by Administrator override',
        ]);

        ActivityLog::log(
            'attendance.absence_reviewed',
            "Admin reviewed absence request for {$absenceRequest->student->name} ({$validated['status']})",
            $absenceRequest
        );

        return back()->with('success', "Absence request updated to '{$validated['status']}'.");
    }

    /**
     * Audit Log Viewer
     */
    public function auditLogs(Request $request)
    {
        $action = $request->query('action');
        $query = ActivityLog::with('user');

        if ($action) {
            $query->where('action', 'like', "%{$action}%");
        }

        $logs = $query->orderByDesc('created_at')->paginate(25);
        $distinctActions = ActivityLog::distinct()->pluck('action');

        return view('portal.admin.audit-logs', compact('logs', 'distinctActions', 'action'));
    }

    /**
     * System Settings Editor
     */
    public function settings()
    {
        $settings = SystemSetting::all()->groupBy('group');
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('portal.admin.settings', compact('settings', 'roles', 'permissions'));
    }

    /**
     * Save System Settings & Permission Matrix
     */
    public function updateSettings(Request $request)
    {
        $settingsData = $request->input('settings', []);
        foreach ($settingsData as $key => $val) {
            SystemSetting::set($key, $val);
        }

        // Update default role permission matrix if provided
        $rolePermissions = $request->input('role_permissions', []);
        foreach ($rolePermissions as $roleId => $permIds) {
            $role = Role::find($roleId);
            if ($role && $role->name !== 'admin') {
                $role->permissions()->sync($permIds);
            }
        }

        ActivityLog::log('system.settings_updated', "Updated institution system settings and permission matrix", Auth::user());

        return back()->with('success', 'System settings and permission matrix updated successfully.');
    }
}
