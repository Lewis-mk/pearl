<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\TrainerPortalController;
use App\Http\Controllers\CyberAttendantController;
use App\Http\Controllers\AdminPortalController;
use App\Http\Controllers\MpesaApiController;

/*
|--------------------------------------------------------------------------
| Public Marketing & Information Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/courses', [PublicController::class, 'courses'])->name('courses');
Route::get('/courses/{slug}', [PublicController::class, 'courseDetail'])->name('courses.detail');
Route::get('/printshop', [PublicController::class, 'printshop'])->name('printshop');
Route::post('/printshop/request', [PublicController::class, 'submitServiceRequest'])->name('printshop.request');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/verify-certificate', [PublicController::class, 'verifyCertificate'])->name('certificate.verify');
Route::get('/receipts/{receiptNumber}', [StudentPortalController::class, 'viewReceipt'])->middleware('auth')->name('receipt.view');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/register/success', [AuthController::class, 'registerSuccess'])->name('register.success');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Student Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('portal/student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/classes', [StudentPortalController::class, 'classes'])->name('classes');
    Route::get('/materials', [StudentPortalController::class, 'materials'])->name('materials');
    Route::get('/payments', [StudentPortalController::class, 'payments'])->name('payments');
    Route::post('/payments/stk-push', [StudentPortalController::class, 'initiatePayment'])->name('payments.stk');
    Route::get('/exams', [StudentPortalController::class, 'exams'])->name('exams');
    Route::get('/exams/{exam}/take', [StudentPortalController::class, 'takeExam'])->name('exams.take');
    Route::post('/exams/{exam}/submit', [StudentPortalController::class, 'submitExam'])->name('exams.submit');
    Route::post('/exams/slots/{slot}/book', [StudentPortalController::class, 'bookSitinSlot'])->name('exams.book_slot');
    Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
    Route::post('/attendance/absence-request', [StudentPortalController::class, 'submitAbsenceRequest'])->name('attendance.absence');
    Route::get('/misconduct', [StudentPortalController::class, 'misconduct'])->name('misconduct');
    Route::post('/misconduct', [StudentPortalController::class, 'submitMisconductReport'])->name('misconduct.submit');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Trainer Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('portal/trainer')->name('trainer.')->middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/dashboard', [TrainerPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/classes', [TrainerPortalController::class, 'classes'])->name('classes');
    Route::post('/classes', [TrainerPortalController::class, 'storeClass'])->name('classes.store');
    Route::post('/classes/{classSession}/postpone', [TrainerPortalController::class, 'postponeClass'])->name('classes.postpone');
    Route::get('/classes/{classSession}/attendance', [TrainerPortalController::class, 'attendance'])->name('classes.attendance');
    Route::post('/classes/{classSession}/attendance', [TrainerPortalController::class, 'saveAttendance'])->name('classes.attendance.save');
    Route::post('/absences/{absenceRequest}/review', [TrainerPortalController::class, 'reviewAbsence'])->name('absences.review');
    Route::get('/materials', [TrainerPortalController::class, 'materials'])->name('materials');
    Route::post('/materials', [TrainerPortalController::class, 'storeMaterial'])->name('materials.store');
    Route::get('/exams', [TrainerPortalController::class, 'exams'])->name('exams');
    Route::post('/exams', [TrainerPortalController::class, 'storeExam'])->name('exams.store');
    Route::get('/exams/{exam}/questions', [TrainerPortalController::class, 'examQuestions'])->name('exam.questions');
    Route::post('/exams/{exam}/questions', [TrainerPortalController::class, 'storeQuestion'])->name('exam.questions.store');
    Route::post('/exams/{exam}/sitin-slots', [TrainerPortalController::class, 'storeSitinSlot'])->name('exam.sitin.store');
    Route::get('/exams/{exam}/grade', [TrainerPortalController::class, 'gradeSubmissions'])->name('exam.grade');
    Route::post('/exams/submissions/{submission}/grade', [TrainerPortalController::class, 'saveGrade'])->name('exam.grade.save');
    
    // Permission Gated Trainer Features
    Route::get('/courses', [TrainerPortalController::class, 'courseManagement'])->name('courses');
    Route::post('/courses', [TrainerPortalController::class, 'storeCourseProposal'])->name('courses.store');
    Route::get('/admission', [TrainerPortalController::class, 'inPersonAdmission'])->name('admission');
    Route::post('/admission', [TrainerPortalController::class, 'processInPersonAdmission'])->name('admission.submit');
});

/*
|--------------------------------------------------------------------------
| Cyber Attendant Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('portal/cyber')->name('cyber.')->middleware(['auth', 'role:cyber_attendant'])->group(function () {
    Route::get('/dashboard', [CyberAttendantController::class, 'dashboard'])->name('dashboard');
    Route::post('/walk-in', [CyberAttendantController::class, 'storeWalkinRequest'])->name('walkin.store');
    Route::post('/requests/{serviceRequest}/status', [CyberAttendantController::class, 'updateRequestStatus'])->name('requests.status');
    Route::post('/course-payment', [CyberAttendantController::class, 'collectCoursePayment'])->name('course_payment');
    Route::get('/daily-summary', [CyberAttendantController::class, 'dailySummary'])->name('daily_summary');
});

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('portal/admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminPortalController::class, 'users'])->name('users');
    Route::post('/users', [AdminPortalController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{user}/roles', [AdminPortalController::class, 'updateUserPermissions'])->name('users.roles');
    Route::post('/users/{user}/permissions', [AdminPortalController::class, 'updateUserPermissions'])->name('users.permissions');
    Route::get('/admissions', [AdminPortalController::class, 'admissions'])->name('admissions');
    Route::post('/admissions/{enrollment}/approve', [AdminPortalController::class, 'approveAdmission'])->name('admissions.approve');
    Route::post('/admissions/{user}/deactivate', [AdminPortalController::class, 'deactivateStudent'])->name('admissions.deactivate');
    Route::post('/admissions/{enrollment}/reassign', [AdminPortalController::class, 'reassignCohort'])->name('admissions.reassign');
    Route::get('/financials', [AdminPortalController::class, 'financials'])->name('financials');
    Route::post('/financials/refund', [AdminPortalController::class, 'processRefund'])->name('financials.refund');
    Route::get('/courses', [AdminPortalController::class, 'courses'])->name('courses');
    Route::post('/courses', [AdminPortalController::class, 'storeCourse'])->name('courses.store');
    Route::post('/courses/{course}/status', [AdminPortalController::class, 'updateCourseStatus'])->name('courses.status');
    Route::post('/courses/{course}/approve', [AdminPortalController::class, 'approveCourseDirect'])->name('courses.approve');
    Route::post('/courses/{course}/cohorts', [AdminPortalController::class, 'storeCohort'])->name('courses.cohorts.store');
    Route::post('/courses/{course}/add-cohort', [AdminPortalController::class, 'storeCohort'])->name('cohorts.store');
    Route::get('/misconduct', [AdminPortalController::class, 'misconductInbox'])->name('misconduct');
    Route::post('/misconduct/{report}/resolve', [AdminPortalController::class, 'resolveMisconduct'])->name('misconduct.resolve');
    Route::get('/attendance-oversight', [AdminPortalController::class, 'attendanceOversight'])->name('attendance');
    Route::post('/attendance-oversight/absence/{absenceRequest}', [AdminPortalController::class, 'reviewAbsenceRequest'])->name('attendance.absence');
    Route::get('/audit-logs', [AdminPortalController::class, 'auditLogs'])->name('audit');
    Route::get('/settings', [AdminPortalController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminPortalController::class, 'updateSettings'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| M-Pesa Callbacks (Daraja API)
|--------------------------------------------------------------------------
*/
Route::post('/api/mpesa/callback', [MpesaApiController::class, 'stkCallback'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
Route::post('/api/mpesa/c2b/validation', [MpesaApiController::class, 'c2bValidation'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
Route::post('/api/mpesa/c2b/confirmation', [MpesaApiController::class, 'c2bConfirmation'])->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
