<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\ActivityLog;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show single login view
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Process single login (supports email OR phone number)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim($request->input('login_identifier'));
        $password = $request->input('password');

        // Check if identifier is email or phone number
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $user = null;

        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
        } else {
            // Try matching phone (clean numbers)
            $formattedPhone = MpesaService::formatPhoneNumber($identifier);
            $user = User::where('phone', $identifier)
                ->orWhere('phone', $formattedPhone)
                ->orWhere('phone', '0' . substr($formattedPhone, 3))
                ->first();
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withErrors([
                'login_identifier' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('login_identifier'));
        }

        if (!$user->isActive()) {
            if ($user->status === 'pending_approval') {
                return back()->with('info', 'Your admission registration is currently pending staff approval. You will receive an SMS and email once approved.');
            }
            return back()->with('error', 'Your account has been deactivated. Please contact campus administration.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLog::log('auth.login', "User {$user->name} ({$user->email}) logged in", $user);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Show student registration form
     */
    public function showRegister(Request $request)
    {
        $courses = Course::where('status', 'published')->with('activeCohorts')->get();
        $selectedCourseId = $request->query('course_id');
        $selectedCohortId = $request->query('cohort_id');

        return view('auth.register', compact('courses', 'selectedCourseId', 'selectedCohortId'));
    }

    /**
     * Process student registration
     */
    public function register(Request $request)
    {
        $course = Course::findOrFail($request->course_id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'course_id' => 'required|exists:courses,id',
            'cohort_id' => 'required|exists:cohorts,id',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Conditional guardian requirements if course is flagged
        if ($course->requires_guardian_info) {
            $rules['guardian_name'] = 'required|string|max:255';
            $rules['guardian_phone'] = 'required|string|max:20';
            $rules['guardian_relationship'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules);

        $cohort = Cohort::findOrFail($validated['cohort_id']);

        // 1. Create Student User (personal email login, pending approval)
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => MpesaService::formatPhoneNumber($validated['phone']),
            'status' => 'pending_approval',
            'is_staff' => false,
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // Simulated instant verification link
        ]);

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['display_name' => 'Student']);
        $user->roles()->attach($studentRole->id);

        // 2. Create pending enrollment
        $enrollment = Enrollment::create([
            'student_id' => $user->id,
            'cohort_id' => $cohort->id,
            'course_id' => $course->id,
            'status' => 'pending_approval',
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_phone' => isset($validated['guardian_phone']) ? MpesaService::formatPhoneNumber($validated['guardian_phone']) : null,
            'guardian_relationship' => $validated['guardian_relationship'] ?? null,
            'fee_total' => $course->total_fee,
            'fee_paid' => 0.00,
            'fee_balance' => $course->total_fee,
            'admin_notes' => 'Self-registered via website portal',
        ]);

        ActivityLog::log(
            'student.registered',
            "Student {$user->name} registered online for {$course->title} ({$cohort->name})",
            $user,
            ['course' => $course->title, 'cohort' => $cohort->name]
        );

        return redirect()->route('register.success', ['user' => $user->id, 'enrollment' => $enrollment->id]);
    }

    /**
     * Registration success & Deposit prompt
     */
    public function registerSuccess(Request $request)
    {
        $user = User::findOrFail($request->user);
        $enrollment = Enrollment::with(['course', 'cohort'])->findOrFail($request->enrollment);

        return view('auth.register-success', compact('user', 'enrollment'));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::log('auth.logout', "User {$user->name} logged out", $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user to primary portal based on their assigned roles
     */
    public function redirectBasedOnRole(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('trainer')) {
            return redirect()->route('trainer.dashboard');
        }
        if ($user->hasRole('cyber_attendant')) {
            return redirect()->route('cyber.dashboard');
        }
        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('home');
    }
}
