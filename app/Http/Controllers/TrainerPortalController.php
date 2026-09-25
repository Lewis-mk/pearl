<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use App\Models\Course;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\AbsenceRequest;
use App\Models\CourseMaterial;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSitinSlot;
use App\Models\ExamSubmission;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Payment;
use App\Models\MisconductReport;
use App\Models\ActivityLog;
use App\Services\NotificationService;
use App\Services\AdmissionNumberService;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TrainerPortalController extends Controller
{
    /**
     * Trainer Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Cohorts where this user is lead trainer or has taught sessions
        $cohorts = Cohort::where('lead_trainer_id', $user->id)
            ->with(['course', 'enrollments.student'])
            ->get();

        $cohortIds = $cohorts->pluck('id');

        $todayClasses = ClassSession::where('trainer_id', $user->id)
            ->whereDate('scheduled_start', today())
            ->with('cohort.course')
            ->orderBy('scheduled_start')
            ->get();

        $upcomingClasses = ClassSession::where('trainer_id', $user->id)
            ->where('scheduled_start', '>=', now())
            ->with('cohort.course')
            ->orderBy('scheduled_start')
            ->take(5)
            ->get();

        $pendingGrading = ExamSubmission::whereHas('exam', function ($q) use ($user, $cohortIds) {
                $q->where('created_by_user_id', $user->id)->orWhereIn('cohort_id', $cohortIds);
            })
            ->where('grade_status', 'pending')
            ->with(['exam', 'student'])
            ->get();

        $pendingAbsences = AbsenceRequest::whereIn('cohort_id', $cohortIds)
            ->where('status', 'pending')
            ->with(['student', 'cohort.course'])
            ->get();

        $totalStudents = Enrollment::whereIn('cohort_id', $cohortIds)->where('status', 'active')->count();

        return view('portal.trainer.dashboard', compact(
            'user',
            'cohorts',
            'todayClasses',
            'upcomingClasses',
            'pendingGrading',
            'pendingAbsences',
            'totalStudents'
        ));
    }

    /**
     * Classes & Schedule Management
     */
    public function classes()
    {
        $user = Auth::user();
        $cohorts = Cohort::where('lead_trainer_id', $user->id)->orWhereHas('classSessions', function ($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })->with('course')->get();

        $classes = ClassSession::where('trainer_id', $user->id)
            ->with(['cohort.course'])
            ->orderByDesc('scheduled_start')
            ->paginate(15);

        return view('portal.trainer.classes', compact('cohorts', 'classes'));
    }

    /**
     * Store New Class Session
     */
    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'cohort_id' => 'required|exists:cohorts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'delivery_mode' => 'required|in:physical,online,hybrid',
            'meeting_url' => 'nullable|url',
            'physical_location' => 'nullable|string|max:255',
        ]);

        $session = ClassSession::create(array_merge($validated, [
            'trainer_id' => Auth::id(),
            'status' => 'scheduled',
        ]));

        ActivityLog::log(
            'class.scheduled',
            "Trainer " . Auth::user()->name . " scheduled class '{$session->title}' for " . $session->scheduled_start->format('d M Y, h:i A'),
            $session
        );

        return back()->with('success', 'Class session scheduled successfully.');
    }

    /**
     * Postpone Class & Trigger Automated Broadcast
     */
    public function postponeClass(Request $request, ClassSession $classSession)
    {
        $validated = $request->validate([
            'postponement_reason' => 'required|string|max:500',
            'rescheduled_start' => 'nullable|date|after:now',
            'rescheduled_end' => 'nullable|date|after:rescheduled_start',
        ]);

        $classSession->update([
            'is_postponed' => true,
            'postponement_reason' => $validated['postponement_reason'],
            'rescheduled_start' => $validated['rescheduled_start'] ?? null,
            'rescheduled_end' => $validated['rescheduled_end'] ?? null,
            'status' => 'postponed',
        ]);

        // Auto-notify all enrolled students via SMS & Email
        NotificationService::notifyClassPostponed(Auth::user(), $classSession, $validated['postponement_reason']);

        return back()->with('success', "Class '{$classSession->title}' marked as postponed. Automated notifications sent to enrolled students.");
    }

    /**
     * Attendance Management & Marking
     */
    public function attendance(ClassSession $classSession)
    {
        $cohort = $classSession->cohort;
        $students = $cohort->enrollments()->where('status', 'active')->with('student')->get();
        $attendances = Attendance::where('class_session_id', $classSession->id)->get()->keyBy('student_id');

        return view('portal.trainer.attendance', compact('classSession', 'cohort', 'students', 'attendances'));
    }

    /**
     * Save Attendance for Class
     */
    public function saveAttendance(Request $request, ClassSession $classSession)
    {
        $attendanceData = $request->input('attendance', []); // [student_id => status]

        foreach ($attendanceData as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'class_session_id' => $classSession->id,
                    'student_id' => $studentId,
                ],
                [
                    'status' => $status,
                    'marked_by_user_id' => Auth::id(),
                ]
            );
        }

        $classSession->update(['status' => 'completed']);

        ActivityLog::log(
            'attendance.marked',
            "Trainer " . Auth::user()->name . " marked attendance for " . count($attendanceData) . " students in '{$classSession->title}'",
            $classSession
        );

        return redirect()->route('trainer.classes')->with('success', 'Attendance marked successfully.');
    }

    /**
     * Review Absence Requests
     */
    public function reviewAbsence(Request $request, AbsenceRequest $absenceRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'reviewer_notes' => 'nullable|string|max:500',
        ]);

        $absenceRequest->update([
            'status' => $validated['status'],
            'reviewer_notes' => $validated['reviewer_notes'] ?? null,
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::log(
            'attendance.absence_reviewed',
            "Trainer " . Auth::user()->name . " {$validated['status']} absence request for student {$absenceRequest->student->name}",
            $absenceRequest
        );

        return back()->with('success', "Absence request {$validated['status']} successfully.");
    }

    /**
     * Course Materials Management
     */
    public function materials()
    {
        $user = Auth::user();
        $cohorts = Cohort::where('lead_trainer_id', $user->id)->with('course')->get();
        $courses = Course::where('status', 'published')->get();

        $materials = CourseMaterial::where('uploaded_by_user_id', $user->id)
            ->with(['course', 'cohort'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('portal.trainer.materials', compact('cohorts', 'courses', 'materials'));
    }

    /**
     * Upload Course Material
     */
    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'cohort_id' => 'nullable|exists:cohorts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_type' => 'required|in:document,video_link,slides,zip',
            'file' => 'nullable|file|max:20480',
            'external_url' => 'nullable|url',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materials', 'public');
        }

        $material = CourseMaterial::create([
            'course_id' => $validated['course_id'],
            'cohort_id' => $validated['cohort_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_type' => $validated['file_type'],
            'file_path' => $filePath,
            'external_url' => $validated['external_url'] ?? null,
            'uploaded_by_user_id' => Auth::id(),
        ]);

        ActivityLog::log('materials.uploaded', "Uploaded material '{$material->title}'", $material);

        return back()->with('success', 'Learning material uploaded successfully.');
    }

    /**
     * Exam Builder & Sit-in Slots
     */
    public function exams()
    {
        $user = Auth::user();
        $cohorts = Cohort::where('lead_trainer_id', $user->id)->with('course')->get();
        $courses = Course::where('status', 'published')->get();

        $exams = Exam::where('created_by_user_id', $user->id)
            ->with(['course', 'cohort', 'questions', 'sitinSlots', 'submissions'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('portal.trainer.exams', compact('cohorts', 'courses', 'exams'));
    }

    /**
     * Store Exam
     */
    public function storeExam(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'cohort_id' => 'nullable|exists:cohorts,id',
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'type' => 'required|in:online_quiz,physical_sitin,hybrid',
            'duration_minutes' => 'required|integer|min:10|max:240',
            'total_marks' => 'required|integer|min:10',
            'pass_percentage' => 'required|integer|min:40|max:100',
            'randomize_questions' => 'boolean',
        ]);

        $exam = Exam::create(array_merge($validated, [
            'created_by_user_id' => Auth::id(),
            'status' => 'published',
        ]));

        ActivityLog::log('exam.created', "Created exam '{$exam->title}'", $exam);

        return redirect()->route('trainer.exam.questions', $exam->id)->with('success', 'Exam created! Now add questions to the question bank.');
    }

    /**
     * Manage Questions & Sit-in Slots for an Exam
     */
    public function examQuestions(Exam $exam)
    {
        $exam->load(['questions', 'sitinSlots', 'course']);
        return view('portal.trainer.exam-questions', compact('exam'));
    }

    /**
     * Add Question to Exam
     */
    public function storeQuestion(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,free_response',
            'marks' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
        ]);

        $options = null;
        if ($validated['type'] === 'multiple_choice' && !empty($request->options)) {
            $options = [];
            foreach ($request->options as $key => $text) {
                if (!empty(trim($text))) {
                    $options[] = ['key' => $key, 'text' => trim($text)];
                }
            }
        }

        $orderIndex = $exam->questions()->count() + 1;

        ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'] ?? null,
            'marks' => $validated['marks'],
            'order_index' => $orderIndex,
        ]);

        return back()->with('success', 'Question added to exam.');
    }

    /**
     * Add Sit-in Exam Slot with Capacity
     */
    public function storeSitinSlot(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'slot_datetime' => 'required|date|after:now',
            'venue' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:200',
        ]);

        ExamSitinSlot::create([
            'exam_id' => $exam->id,
            'slot_datetime' => $validated['slot_datetime'],
            'venue' => $validated['venue'],
            'capacity' => $validated['capacity'],
            'booked_count' => 0,
            'status' => 'open',
        ]);

        return back()->with('success', "Sit-in slot created with capacity of {$validated['capacity']} students.");
    }

    /**
     * Grade Submissions
     */
    public function gradeSubmissions(Exam $exam)
    {
        $submissions = ExamSubmission::where('exam_id', $exam->id)
            ->with(['student', 'sitinSlot'])
            ->orderByDesc('created_at')
            ->get();

        return view('portal.trainer.grade-submissions', compact('exam', 'submissions'));
    }

    /**
     * Submit Grade for Free Response Question / Practical
     */
    public function saveGrade(Request $request, ExamSubmission $submission)
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0',
            'trainer_feedback' => 'nullable|string',
        ]);

        $exam = $submission->exam;
        $percentage = ($validated['score'] / $exam->total_marks) * 100;
        $passed = $percentage >= $exam->pass_percentage;

        $submission->update([
            'score' => $validated['score'],
            'percentage' => $percentage,
            'grade_status' => 'graded',
            'passed' => $passed,
            'trainer_feedback' => $validated['trainer_feedback'] ?? null,
            'graded_by_user_id' => Auth::id(),
            'graded_at' => now(),
        ]);

        // Auto issue certificate if passed and enrolled
        if ($passed) {
            $enrollment = Enrollment::where('student_id', $submission->student_id)
                ->where('course_id', $exam->course_id)
                ->first();

            if ($enrollment && !$enrollment->certificate) {
                Certificate::create([
                    'certificate_number' => 'PTI-CERT-' . date('Y') . '-' . sprintf('%05d', rand(100, 99999)),
                    'verification_code' => 'VER-PTI-' . strtoupper(Str::random(8)),
                    'student_id' => $submission->student_id,
                    'course_id' => $exam->course_id,
                    'cohort_id' => $enrollment->cohort_id,
                    'enrollment_id' => $enrollment->id,
                    'student_full_name' => $submission->student->name,
                    'course_title' => $exam->course->title,
                    'grade' => $percentage >= 85 ? 'Distinction' : ($percentage >= 70 ? 'Credit' : 'Pass'),
                    'completion_date' => today(),
                    'issue_date' => today(),
                    'issued_by_user_id' => Auth::id(),
                ]);
            }
        }

        ActivityLog::log('exam.graded', "Graded exam submission for student {$submission->student->name} (Score: {$validated['score']}/{$exam->total_marks})", $submission);

        return back()->with('success', "Grade saved. Final percentage: {$percentage}% (" . ($passed ? 'Passed' : 'Failed') . ")");
    }

    /**
     * Course Creation / Proposal (if granted permission)
     */
    public function courseManagement()
    {
        if (!Auth::user()->hasPermission('course.create') && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not hold permission to create or edit courses.');
        }

        $myCourses = Course::where('created_by_user_id', Auth::id())->with('cohorts')->get();
        return view('portal.trainer.courses', compact('myCourses'));
    }

    /**
     * Submit Course Proposal
     */
    public function storeCourseProposal(Request $request)
    {
        if (!Auth::user()->hasPermission('course.create') && !Auth::user()->hasRole('admin')) {
            abort(403, 'Permission denied.');
        }

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
        ]);

        $course = Course::create(array_merge($validated, [
            'slug' => Str::slug($validated['title']) . '-' . rand(100, 999),
            'status' => Auth::user()->hasRole('admin') ? 'published' : 'pending_review',
            'created_by_user_id' => Auth::id(),
            'approved_by_user_id' => Auth::user()->hasRole('admin') ? Auth::id() : null,
        ]));

        ActivityLog::log('course.proposed', "Trainer " . Auth::user()->name . " proposed course '{$course->title}'", $course);

        return back()->with('success', 'Course submitted successfully. ' . (Auth::user()->hasRole('admin') ? 'Published live!' : 'Awaiting Administrator review.'));
    }

    /**
     * In-Person Admission & Cash Collection (if granted permissions)
     */
    public function inPersonAdmission()
    {
        if (!Auth::user()->hasPermission('student.admit') && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have the student.admit permission.');
        }

        $courses = Course::where('status', 'published')->with('activeCohorts')->get();
        return view('portal.trainer.in-person-admission', compact('courses'));
    }

    /**
     * Process In-Person Admission with Cash/Offline Payment
     */
    public function processInPersonAdmission(Request $request, MpesaService $mpesaService)
    {
        if (!Auth::user()->hasPermission('student.admit') && !Auth::user()->hasRole('admin')) {
            abort(403, 'Permission denied.');
        }

        $course = Course::findOrFail($request->course_id);
        $cohort = Cohort::findOrFail($request->cohort_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'course_id' => 'required|exists:courses,id',
            'cohort_id' => 'required|exists:cohorts,id',
            'initial_payment_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,mpesa_manual,none',
            'mpesa_receipt_number' => 'nullable|required_if:payment_method,mpesa_manual|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_relationship' => 'nullable|string|max:100',
        ]);

        // 1. Generate unique sequential admission number
        $admissionNumber = AdmissionNumberService::generateNextAdmissionNumber();

        // 2. Create student user
        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => MpesaService::formatPhoneNumber($validated['phone']),
            'admission_number' => $admissionNumber,
            'status' => 'active',
            'is_staff' => false,
            'email_verified_at' => now(),
            'password' => Hash::make('Pearl@' . date('Y')),
        ]);

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
        $student->roles()->attach($studentRole->id);

        // 3. Create active enrollment
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'cohort_id' => $cohort->id,
            'course_id' => $course->id,
            'status' => 'active',
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_phone' => isset($validated['guardian_phone']) ? MpesaService::formatPhoneNumber($validated['guardian_phone']) : null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'fee_total' => $course->total_fee,
            'fee_paid' => 0.00,
            'fee_balance' => $course->total_fee,
            'admitted_by_user_id' => Auth::id(),
            'admitted_at' => now(),
            'admin_notes' => "In-person admission by " . Auth::user()->name . " (" . Auth::user()->roles->pluck('display_name')->implode(', ') . ")",
        ]);

        // 4. Handle initial payment if collected
        $initialAmount = (float)($validated['initial_payment_amount'] ?? 0);
        $receipt = null;

        if ($initialAmount > 0) {
            if ($validated['payment_method'] === 'cash') {
                $receipt = $mpesaService->recordCashPayment(
                    Auth::user(),
                    $student,
                    $enrollment,
                    $initialAmount,
                    'deposit',
                    "In-person registration cash deposit collected by " . Auth::user()->name
                );
            } elseif ($validated['payment_method'] === 'mpesa_manual') {
                $receipt = $mpesaService->recordManualMpesaPayment(
                    Auth::user(),
                    $student,
                    $enrollment,
                    $initialAmount,
                    $validated['mpesa_receipt_number'],
                    $validated['phone'],
                    'deposit',
                    "In-person M-Pesa verified offline by " . Auth::user()->name
                );
            }
        }

        NotificationService::notifyAdmissionApproved($student, $admissionNumber, $course->title);

        ActivityLog::log(
            'student.admitted_in_person',
            "Admitted {$student->name} (Adm: {$admissionNumber}) into {$course->title}",
            $student,
            ['admitted_by' => Auth::id(), 'payment_amount' => $initialAmount]
        );

        return redirect()->route('trainer.admission')->with('success', "Student admitted successfully! Admission Number: {$admissionNumber}. Default password: Pearl@" . date('Y') . ($receipt ? " (Receipt #{$receipt->receipt_number} issued)" : ''));
    }
}
