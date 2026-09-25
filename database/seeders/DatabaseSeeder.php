<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Course;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PrintshopService;
use App\Models\ServiceRequest;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\CourseMaterial;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSitinSlot;
use App\Models\ExamSubmission;
use App\Models\Certificate;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator / Principal',
            'description' => 'Full administrative access to entire institution operations and finances',
        ]);

        $trainerRole = Role::create([
            'name' => 'trainer',
            'display_name' => 'Course Trainer / Instructor',
            'description' => 'Manages classes, attendance, exams, grading, and cohort course materials',
        ]);

        $cyberRole = Role::create([
            'name' => 'cyber_attendant',
            'display_name' => 'Cyber & Printshop Attendant',
            'description' => 'Handles print requests, cyber services, walk-ins, and cash collection',
        ]);

        $studentRole = Role::create([
            'name' => 'student',
            'display_name' => 'Enrolled Student',
            'description' => 'Accesses learning materials, timetable, quizzes, sit-in exam booking, and fee ledger',
        ]);

        // 2. Create Granular Permissions
        $permissions = [
            // Student & Admissions
            ['name' => 'student.admit', 'display_name' => 'Admit New Students', 'category' => 'students', 'description' => 'Approve pending student registrations and issue sequential admission numbers'],
            ['name' => 'student.view_all', 'display_name' => 'View All Students', 'category' => 'students', 'description' => 'Browse and search institution student directory'],
            
            // Payments & Financials
            ['name' => 'payment.collect_cash', 'display_name' => 'Collect & Receipt Cash Payments', 'category' => 'payments', 'description' => 'Issue official sequential cash payment receipts for courses & services'],
            ['name' => 'payment.record_manual_mpesa', 'display_name' => 'Record Manual M-Pesa Offline', 'category' => 'payments', 'description' => 'Verify and log offline M-Pesa transactions during network outages'],
            ['name' => 'reports.view_financial', 'display_name' => 'View Financial Ledger & Reports', 'category' => 'payments', 'description' => 'Access complete payment reconciliations, refunds, and revenue breakdown'],

            // Courses & Outlines
            ['name' => 'course.create', 'display_name' => 'Create New Courses', 'category' => 'courses', 'description' => 'Draft and propose new short courses for approval'],
            ['name' => 'course.edit_outline', 'display_name' => 'Edit Course Curriculum Outline', 'category' => 'courses', 'description' => 'Update syllabus and modules'],
            ['name' => 'course.publish', 'display_name' => 'Publish Courses Live', 'category' => 'courses', 'description' => 'Make courses publicly visible for student registration'],

            // Classes & Materials
            ['name' => 'class.schedule', 'display_name' => 'Schedule Classes & Meetings', 'category' => 'classes', 'description' => 'Set up physical and virtual class timetables'],
            ['name' => 'class.postpone', 'display_name' => 'Postpone Classes', 'category' => 'classes', 'description' => 'Postpone sessions and trigger automated SMS/email student alerts'],
            ['name' => 'materials.upload', 'display_name' => 'Upload Learning Materials', 'category' => 'classes', 'description' => 'Upload PDF guides, lecture notes, and video links'],
            ['name' => 'attendance.mark', 'display_name' => 'Mark & Manage Attendance', 'category' => 'classes', 'description' => 'Record daily student attendance and review absence requests'],

            // Exams & Grading
            ['name' => 'exam.schedule', 'display_name' => 'Schedule Exams & Sit-in Slots', 'category' => 'exams', 'description' => 'Create quizzes and configure physical sit-in venue dates/capacity'],
            ['name' => 'exam.grade', 'display_name' => 'Grade Exams & Issue Results', 'category' => 'exams', 'description' => 'Score free-response questions and release final scores/certificates'],

            // Printshop & Cyber
            ['name' => 'printshop.manage', 'display_name' => 'Manage Printshop Queue & Orders', 'category' => 'printshop', 'description' => 'Update service request progress and log printshop income'],

            // Misconduct & Whistleblower
            ['name' => 'misconduct.report', 'display_name' => 'Submit Misconduct Reports', 'category' => 'misconduct', 'description' => 'File confidential reports with whistleblower protections'],
            ['name' => 'misconduct.view_all', 'display_name' => 'View & Investigate Misconduct Reports', 'category' => 'misconduct', 'description' => 'Access raw confidential reports and manage investigation workflow'],

            // User Management & Roles
            ['name' => 'users.manage_roles', 'display_name' => 'Manage Users, Roles & Permissions', 'category' => 'admin', 'description' => 'Assign multi-roles, override individual permissions, and provision staff mailboxes'],
            ['name' => 'audit.view', 'display_name' => 'View System Audit Logs', 'category' => 'admin', 'description' => 'Inspect immutable activity logs across the entire system'],
        ];

        $createdPermissions = [];
        foreach ($permissions as $p) {
            $createdPermissions[$p['name']] = Permission::create($p);
        }

        // 3. Attach default permissions to roles
        // Admin gets all automatically via HasRolesAndPermissions, but let's sync explicitly:
        $adminRole->permissions()->sync(Permission::all());

        // Trainer default permissions:
        $trainerRole->permissions()->sync([
            $createdPermissions['class.schedule']->id,
            $createdPermissions['class.postpone']->id,
            $createdPermissions['materials.upload']->id,
            $createdPermissions['attendance.mark']->id,
            $createdPermissions['exam.schedule']->id,
            $createdPermissions['exam.grade']->id,
            $createdPermissions['misconduct.report']->id,
        ]);

        // Cyber Attendant default permissions:
        $cyberRole->permissions()->sync([
            $createdPermissions['printshop.manage']->id,
            $createdPermissions['payment.collect_cash']->id,
            $createdPermissions['misconduct.report']->id,
        ]);

        // Student default permissions:
        $studentRole->permissions()->sync([
            $createdPermissions['misconduct.report']->id,
        ]);

        // 4. Create Default Users
        // Admin User
        $adminUser = User::create([
            'name' => 'Dr. Paul Mwangi (Director)',
            'email' => 'admin@pearlinstitute.com',
            'phone' => '0711223344',
            'status' => 'active',
            'is_staff' => true,
            'staff_official_email' => 'admin@pearlinstitute.com',
            'staff_email_status' => 'provisioned',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $adminUser->roles()->attach($adminRole->id);

        // Lead Trainer User
        $trainerUser = User::create([
            'name' => 'Peter Kamau (Senior Tech Trainer)',
            'email' => 'peter.trainer@gmail.com',
            'phone' => '0722334455',
            'status' => 'active',
            'is_staff' => true,
            'staff_official_email' => 'peter.kamau@pearlinstitute.com',
            'staff_email_status' => 'provisioned',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $trainerUser->roles()->attach($trainerRole->id);

        // Cyber Attendant User
        $cyberUser = User::create([
            'name' => 'Mary Wanjiku (Cyber Specialist)',
            'email' => 'mary.cyber@gmail.com',
            'phone' => '0733445566',
            'status' => 'active',
            'is_staff' => true,
            'staff_official_email' => 'mary.wanjiku@pearlinstitute.com',
            'staff_email_status' => 'provisioned',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $cyberUser->roles()->attach($cyberRole->id);

        // Multi-Role Staff (Trainer + Cyber Attendant with cash & admission permissions granted)
        $multiRoleStaff = User::create([
            'name' => 'John Otieno (Trainer & Cyber Supervisor)',
            'email' => 'john.otieno@gmail.com',
            'phone' => '0744556677',
            'status' => 'active',
            'is_staff' => true,
            'staff_official_email' => 'john.otieno@pearlinstitute.com',
            'staff_email_status' => 'provisioned',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $multiRoleStaff->roles()->attach([$trainerRole->id, $cyberRole->id]);
        // Explicit permission overrides for John Otieno:
        $multiRoleStaff->setPermissionOverride('student.admit', true, $adminUser->id);
        $multiRoleStaff->setPermissionOverride('course.create', true, $adminUser->id);

        // Sample Students
        $student1 = User::create([
            'name' => 'Brian Kiprono',
            'email' => 'brian.kip@gmail.com',
            'phone' => '0799112233',
            'admission_number' => 'PTI/2026/00001',
            'status' => 'active',
            'is_staff' => false,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $student1->roles()->attach($studentRole->id);

        $student2 = User::create([
            'name' => 'Faith Mutua',
            'email' => 'faith.mutua@gmail.com',
            'phone' => '0788223344',
            'admission_number' => 'PTI/2026/00002',
            'status' => 'active',
            'is_staff' => false,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $student2->roles()->attach($studentRole->id);

        $student3Pending = User::create([
            'name' => 'Kelvin Omondi',
            'email' => 'kelvin.omondi@gmail.com',
            'phone' => '0777334455',
            'status' => 'pending_approval',
            'is_staff' => false,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $student3Pending->roles()->attach($studentRole->id);

        // 5. Create Courses
        $course1 = Course::create([
            'title' => 'Full Stack Web Development & Laravel',
            'slug' => 'full-stack-web-development-laravel',
            'category' => 'Technology',
            'short_description' => 'Master modern backend with PHP & Laravel, frontend Blade/Tailwind, and database architecture.',
            'description' => 'A comprehensive 12-week practical bootcamp designed to take you from foundational programming to building full-scale web platforms with auth, payments (M-Pesa), and REST APIs.',
            'curriculum_outline' => "Module 1: PHP 8 Modern Core & Object-Oriented Architecture\nModule 2: Laravel 11 Framework, Routing, Controllers & Blade\nModule 3: Database Design, Migrations, Eloquent ORM & Relationships\nModule 4: Authentication, Authorization & Granular RBAC\nModule 5: Payment Gateway Integrations (M-Pesa Daraja API STK Push & C2B)\nModule 6: Capstone Project & Cloud Deployment",
            'duration_weeks' => 12,
            'total_fee' => 35000.00,
            'deposit_required' => 10000.00,
            'requires_guardian_info' => false,
            'status' => 'published',
            'featured_badge' => 'Most Popular',
            'image_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80',
            'created_by_user_id' => $adminUser->id,
            'approved_by_user_id' => $adminUser->id,
        ]);

        $course2 = Course::create([
            'title' => 'Graphic Design, Branding & UI/UX',
            'slug' => 'graphic-design-branding-ui-ux',
            'category' => 'Creative Design',
            'short_description' => 'Professional visual design, corporate identity, Adobe Suite (Photoshop, Illustrator) & Figma.',
            'description' => 'Learn visual hierarchy, logo design, marketing collateral creation, typography, and interactive UI/UX prototyping for digital products.',
            'curriculum_outline' => "Module 1: Principles of Graphic Design & Color Theory\nModule 2: Adobe Photoshop Mastery (Photo editing & Manipulation)\nModule 3: Adobe Illustrator (Vector Art & Corporate Logos)\nModule 4: Publication Design & Print Production (InDesign)\nModule 5: UI/UX Wireframing & Prototyping with Figma\nModule 6: Client Portfolio Development",
            'duration_weeks' => 8,
            'total_fee' => 25000.00,
            'deposit_required' => 8000.00,
            'requires_guardian_info' => false,
            'status' => 'published',
            'featured_badge' => 'High Demand',
            'image_url' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=800&q=80',
            'created_by_user_id' => $adminUser->id,
            'approved_by_user_id' => $adminUser->id,
        ]);

        $course3 = Course::create([
            'title' => 'Computerized Accounting & QuickBooks',
            'slug' => 'computerized-accounting-quickbooks',
            'category' => 'Business',
            'short_description' => 'Practical business accounting, ledger entries, payroll, KRA iTax, and QuickBooks Online.',
            'description' => 'Ideal for business owners, accountants, and finance assistants seeking hands-on mastery in modern computerized bookkeeping.',
            'curriculum_outline' => "Module 1: Fundamentals of Financial Accounting & Chart of Accounts\nModule 2: Setting up Company Books in QuickBooks Online\nModule 3: Invoicing, Accounts Receivable & Accounts Payable\nModule 4: Inventory Management & Bank Reconciliation\nModule 5: Payroll Processing & Kenyan Statutory Deductions (NSSF, SHIF, PAYE)\nModule 6: KRA iTax Filing & VAT Returns",
            'duration_weeks' => 6,
            'total_fee' => 18000.00,
            'deposit_required' => 6000.00,
            'requires_guardian_info' => false,
            'status' => 'published',
            'featured_badge' => 'Career Starter',
            'image_url' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&q=80',
            'created_by_user_id' => $adminUser->id,
            'approved_by_user_id' => $adminUser->id,
        ]);

        $course4 = Course::create([
            'title' => 'Computer Packages & Digital Literacy (Junior / Teens)',
            'slug' => 'computer-packages-digital-literacy',
            'category' => 'Basic Computing',
            'short_description' => 'Comprehensive beginner to intermediate computing skills including MS Office and internet research.',
            'description' => 'Foundational training covering computer concepts, Windows OS, Word, Excel, PowerPoint, Access, Publisher, Typing, and Online Safety.',
            'curriculum_outline' => "Module 1: Introduction to Computers & Operating Systems\nModule 2: Touch Typing & Keyboard Mastery\nModule 3: Microsoft Word (Document Formatting & Reports)\nModule 4: Microsoft Excel (Formulas, Functions & Data Analysis)\nModule 5: Microsoft PowerPoint & Presentations\nModule 6: Cyber Security Awareness & Safe Internet Browsing",
            'duration_weeks' => 4,
            'total_fee' => 7500.00,
            'deposit_required' => 3000.00,
            'requires_guardian_info' => true, // Flagged for minors
            'status' => 'published',
            'featured_badge' => 'Holiday Special',
            'image_url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800&q=80',
            'created_by_user_id' => $adminUser->id,
            'approved_by_user_id' => $adminUser->id,
        ]);

        // 6. Create Cohorts
        $cohort1 = Cohort::create([
            'course_id' => $course1->id,
            'name' => 'May 2026 Morning Cohort',
            'code' => 'FSW-2026-MAY-MORN',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->startOfMonth()->addWeeks(12),
            'max_capacity' => 25,
            'status' => 'enrolling',
            'lead_trainer_id' => $trainerUser->id,
        ]);

        $cohort2 = Cohort::create([
            'course_id' => $course2->id,
            'name' => 'June 2026 Evening Cohort',
            'code' => 'GD-2026-JUN-EVE',
            'start_date' => now()->addWeeks(1)->startOfWeek(),
            'end_date' => now()->addWeeks(9),
            'max_capacity' => 20,
            'status' => 'enrolling',
            'lead_trainer_id' => $multiRoleStaff->id,
        ]);

        // 7. Create Enrollments
        $enrollment1 = Enrollment::create([
            'student_id' => $student1->id,
            'cohort_id' => $cohort1->id,
            'course_id' => $course1->id,
            'status' => 'active',
            'fee_total' => 35000.00,
            'fee_paid' => 20000.00,
            'fee_balance' => 15000.00,
            'completion_percentage' => 45,
            'admitted_by_user_id' => $adminUser->id,
            'admitted_at' => now()->subDays(20),
            'admin_notes' => 'Admitted with initial 10k M-Pesa deposit + 10k installment',
        ]);

        $enrollment2 = Enrollment::create([
            'student_id' => $student2->id,
            'cohort_id' => $cohort2->id,
            'course_id' => $course2->id,
            'status' => 'active',
            'fee_total' => 25000.00,
            'fee_paid' => 25000.00,
            'fee_balance' => 0.00,
            'completion_percentage' => 80,
            'admitted_by_user_id' => $adminUser->id,
            'admitted_at' => now()->subDays(30),
            'admin_notes' => 'Paid in full via M-Pesa',
        ]);

        $enrollment3Pending = Enrollment::create([
            'student_id' => $student3Pending->id,
            'cohort_id' => $cohort1->id,
            'course_id' => $course1->id,
            'status' => 'pending_approval',
            'fee_total' => 35000.00,
            'fee_paid' => 0.00,
            'fee_balance' => 35000.00,
            'completion_percentage' => 0,
            'admin_notes' => 'Registered via website, awaiting staff approval and deposit payment',
        ]);

        // 8. Create Payments (M-Pesa + Cash Unified Ledger with Sequential Receipt Numbers)
        $payment1 = Payment::create([
            'receipt_number' => 'PRL-RCP-202608-00001',
            'user_id' => $student1->id,
            'enrollment_id' => $enrollment1->id,
            'amount' => 10000.00,
            'payment_method' => 'mpesa_stk',
            'status' => 'completed',
            'purpose' => 'deposit',
            'mpesa_receipt_number' => 'QK89AB1234',
            'phone_number' => '254799112233',
            'notes' => 'Registration Deposit via STK Push',
            'created_at' => now()->subDays(20),
        ]);

        $payment2 = Payment::create([
            'receipt_number' => 'PRL-RCP-202608-00002',
            'user_id' => $student1->id,
            'enrollment_id' => $enrollment1->id,
            'amount' => 10000.00,
            'payment_method' => 'cash',
            'status' => 'completed',
            'purpose' => 'installment',
            'collected_by_user_id' => $multiRoleStaff->id,
            'notes' => 'Second installment paid in cash at campus front-desk to John Otieno',
            'created_at' => now()->subDays(5),
        ]);

        $payment3 = Payment::create([
            'receipt_number' => 'PRL-RCP-202608-00003',
            'user_id' => $student2->id,
            'enrollment_id' => $enrollment2->id,
            'amount' => 25000.00,
            'payment_method' => 'mpesa_stk',
            'status' => 'completed',
            'purpose' => 'full',
            'mpesa_receipt_number' => 'QK91CD5678',
            'phone_number' => '254788223344',
            'notes' => 'Full course fee payment via Daraja STK Push',
            'created_at' => now()->subDays(30),
        ]);

        // 9. Create Printshop Services & Sample Service Requests
        $services = [
            ['name' => 'Color Printing & Photocopying (A4)', 'category' => 'printing', 'unit_price' => 20.00, 'unit_label' => 'per page', 'description' => 'High-resolution vibrant color output on 80gsm bond paper.'],
            ['name' => 'Black & White Printing (A4)', 'category' => 'printing', 'unit_price' => 5.00, 'unit_label' => 'per page', 'description' => 'Crisp laser printing for documents, assignments, and CVs.'],
            ['name' => 'Spiral & Thermal Document Binding', 'category' => 'printing', 'unit_price' => 150.00, 'unit_label' => 'per document', 'description' => 'Professional report and project bookbinding with clear plastic covers.'],
            ['name' => 'KRA PIN Registration & iTax Filing', 'category' => 'cyber_services', 'unit_price' => 300.00, 'unit_label' => 'per return', 'description' => 'Annual tax returns, nil returns, and PIN amendment assistance.'],
            ['name' => 'Passport Photo Printing (Set of 4)', 'category' => 'design', 'unit_price' => 250.00, 'unit_label' => 'per set', 'description' => 'Standard compliant passport and visa photos on premium photo paper.'],
            ['name' => 'Graphic Design & Flyer Typesetting', 'category' => 'design', 'unit_price' => 1000.00, 'unit_label' => 'per design', 'description' => 'Custom promotional posters, business cards, and church bulletins.'],
        ];

        foreach ($services as $srv) {
            PrintshopService::create($srv);
        }

        // Printshop Service Request
        $sr1 = ServiceRequest::create([
            'request_code' => 'SR-202608-0001',
            'customer_name' => 'Grace Njeri',
            'customer_phone' => '0712345678',
            'customer_email' => 'grace.njeri@outlook.com',
            'service_type' => 'Color Printing & Spiral Binding',
            'quantity' => 45,
            'instructions' => 'Print 45 pages in full color and spiral bind 2 copies before 4:00 PM.',
            'quoted_amount' => 1200.00,
            'paid_amount' => 1200.00,
            'payment_status' => 'paid',
            'status' => 'completed',
            'handled_by_user_id' => $cyberUser->id,
            'staff_notes' => 'Job printed on Konica Minolta press, collected by customer.',
            'completed_at' => now()->subHours(2),
        ]);

        $sr2 = ServiceRequest::create([
            'request_code' => 'SR-202608-0002',
            'customer_name' => 'Samuel Kibet',
            'customer_phone' => '0723456789',
            'customer_email' => 'sam.kibet@gmail.com',
            'service_type' => 'KRA PIN Registration & Nil Return',
            'quantity' => 1,
            'instructions' => 'File 2025 income tax return for personal PIN.',
            'quoted_amount' => 300.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
            'status' => 'in_progress',
            'handled_by_user_id' => $cyberUser->id,
            'staff_notes' => 'Awaiting customer ID copy verification.',
        ]);

        // 10. Create Class Sessions & Postponement Demonstration
        $class1 = ClassSession::create([
            'cohort_id' => $cohort1->id,
            'trainer_id' => $trainerUser->id,
            'title' => 'Building Database Schemas with Eloquent ORM',
            'description' => 'Hands-on session creating migrations, foreign keys, polymorphic links, and seeders.',
            'scheduled_start' => now()->subDays(2)->setTime(9, 0),
            'scheduled_end' => now()->subDays(2)->setTime(12, 0),
            'delivery_mode' => 'hybrid',
            'meeting_url' => 'https://meet.google.com/pti-fsw-class',
            'physical_location' => 'Main Lab 1, Pearl Campus',
            'status' => 'completed',
        ]);

        Attendance::create([
            'class_session_id' => $class1->id,
            'student_id' => $student1->id,
            'status' => 'present',
            'marked_by_user_id' => $trainerUser->id,
        ]);

        $class2Postponed = ClassSession::create([
            'cohort_id' => $cohort1->id,
            'trainer_id' => $trainerUser->id,
            'title' => 'Daraja API STK Push Integration Lab',
            'description' => 'Connecting Safaricom sandbox, generating OAuth access tokens, and initiating STK prompts.',
            'scheduled_start' => now()->addDays(1)->setTime(14, 0),
            'scheduled_end' => now()->addDays(1)->setTime(17, 0),
            'delivery_mode' => 'online',
            'meeting_url' => 'https://meet.google.com/pti-fsw-daraja',
            'is_postponed' => true,
            'postponement_reason' => 'Scheduled campus power maintenance by Kenya Power. Rescheduled to Saturday 9:00 AM.',
            'rescheduled_start' => now()->addDays(3)->setTime(9, 0),
            'rescheduled_end' => now()->addDays(3)->setTime(12, 0),
            'status' => 'postponed',
        ]);

        // 11. Create Course Materials
        CourseMaterial::create([
            'course_id' => $course1->id,
            'cohort_id' => $cohort1->id,
            'title' => 'Lecture 1-4 Slides: PHP Modern Fundamentals & OOP',
            'description' => 'Comprehensive slide deck covering namespaces, traits, anonymous classes, and types.',
            'file_type' => 'document',
            'file_path' => 'materials/php_modern_fundamentals.pdf',
            'uploaded_by_user_id' => $trainerUser->id,
        ]);

        CourseMaterial::create([
            'course_id' => $course1->id,
            'cohort_id' => $cohort1->id,
            'title' => 'Daraja API Developer Cheat Sheet & Sandbox Keys',
            'description' => 'Official payload specifications, test credentials, and cURL snippets for M-Pesa testing.',
            'file_type' => 'document',
            'file_path' => 'materials/daraja_api_cheatsheet.pdf',
            'uploaded_by_user_id' => $trainerUser->id,
        ]);

        // 12. Create Exam, Questions, Sit-in Slots & Certificate
        $exam1 = Exam::create([
            'course_id' => $course1->id,
            'cohort_id' => $cohort1->id,
            'title' => 'Full Stack Web Mid-Term Practical Exam',
            'instructions' => 'Complete all 5 questions within 60 minutes. Once submitted, your score will be computed.',
            'type' => 'hybrid',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'pass_percentage' => 60,
            'randomize_questions' => true,
            'max_attempts' => 1,
            'status' => 'published',
            'created_by_user_id' => $trainerUser->id,
        ]);

        ExamQuestion::create([
            'exam_id' => $exam1->id,
            'question_text' => 'Which Laravel artisan command is used to run database migrations and populate seeders simultaneously?',
            'type' => 'multiple_choice',
            'options' => [
                ['key' => 'A', 'text' => 'php artisan migrate:refresh --seed'],
                ['key' => 'B', 'text' => 'php artisan db:seed --force'],
                ['key' => 'C', 'text' => 'php artisan make:migration --run'],
                ['key' => 'D', 'text' => 'php artisan schema:dump'],
            ],
            'correct_answer' => 'A',
            'marks' => 20,
            'order_index' => 1,
        ]);

        ExamQuestion::create([
            'exam_id' => $exam1->id,
            'question_text' => 'What is the standard HTTP method and endpoint structure used when receiving Safaricom Daraja STK Push callbacks?',
            'type' => 'multiple_choice',
            'options' => [
                ['key' => 'A', 'text' => 'GET request with query params'],
                ['key' => 'B', 'text' => 'POST request containing JSON Body with ResultCode and CallbackMetadata'],
                ['key' => 'C', 'text' => 'PUT request via WebSocket'],
                ['key' => 'D', 'text' => 'SOAP XML payload over port 8080'],
            ],
            'correct_answer' => 'B',
            'marks' => 20,
            'order_index' => 2,
        ]);

        ExamQuestion::create([
            'exam_id' => $exam1->id,
            'question_text' => 'Explain the architectural difference between Role-Based Access Control (RBAC) and Granular Permission Overrides, citing an example from Pearl Institute platform.',
            'type' => 'free_response',
            'options' => null,
            'correct_answer' => 'RBAC grants broad permissions via roles (e.g. Trainer), while Granular Overrides allow specific permissions (e.g. student.admit) to be granted/revoked per user without altering the entire role.',
            'marks' => 60,
            'order_index' => 3,
        ]);

        // Physical Sit-in Exam Slots
        ExamSitinSlot::create([
            'exam_id' => $exam1->id,
            'slot_datetime' => now()->addDays(5)->setTime(10, 0),
            'venue' => 'Main Exam Lab 1 (Pearl Campus)',
            'capacity' => 20,
            'booked_count' => 8,
            'status' => 'open',
        ]);

        ExamSitinSlot::create([
            'exam_id' => $exam1->id,
            'slot_datetime' => now()->addDays(6)->setTime(14, 0),
            'venue' => 'Main Exam Lab 2 (Pearl Campus)',
            'capacity' => 15,
            'booked_count' => 15,
            'status' => 'full',
        ]);

        // Sample Certificate for Faith Mutua who completed Graphic Design
        Certificate::create([
            'certificate_number' => 'PTI-CERT-2026-00001',
            'verification_code' => 'VER-PTI-' . strtoupper(Str::random(8)),
            'student_id' => $student2->id,
            'course_id' => $course2->id,
            'cohort_id' => $cohort2->id,
            'enrollment_id' => $enrollment2->id,
            'student_full_name' => $student2->name,
            'course_title' => $course2->title,
            'grade' => 'Distinction (92%)',
            'completion_date' => now()->subDays(3),
            'issue_date' => now()->subDays(2),
            'issued_by_user_id' => $adminUser->id,
        ]);

        // 13. Create System Settings
        $settings = [
            ['key' => 'institution_name', 'value' => 'Pearl Training Institute', 'type' => 'string', 'group' => 'general', 'label' => 'Institution Name', 'description' => 'Official name of the institute'],
            ['key' => 'campus_address', 'value' => 'Pearl Towers, 3rd Floor, Moi Avenue, Nairobi, Kenya', 'type' => 'string', 'group' => 'general', 'label' => 'Campus Physical Address', 'description' => 'Displayed on public contact page and certificates'],
            ['key' => 'support_phone', 'value' => '+254 700 123 456', 'type' => 'string', 'group' => 'general', 'label' => 'Helpline Phone Number', 'description' => 'Main admissions hotline'],
            ['key' => 'support_email', 'value' => 'admissions@pearlinstitute.com', 'type' => 'string', 'group' => 'general', 'label' => 'Admissions & Help Email', 'description' => 'Public contact email'],
            ['key' => 'daraja_paybill', 'value' => '174379', 'type' => 'string', 'group' => 'daraja', 'label' => 'M-Pesa Business Shortcode', 'description' => 'Paybill or Till number used for C2B payments'],
            ['key' => 'misconduct_escalation_contact', 'value' => 'governance@pearlinstitute.com', 'type' => 'string', 'group' => 'governance', 'label' => 'Misconduct Escalation Email', 'description' => 'Secondary trustee/board contact notified when complaints are filed against the Principal/Admin'],
            ['key' => 'refund_forfeit_percentage', 'value' => '20', 'type' => 'integer', 'group' => 'finance', 'label' => 'Withdrawal Admin Fee (%)', 'description' => 'Percentage deducted when processing approved student course withdrawal refunds'],
        ];

        foreach ($settings as $st) {
            SystemSetting::create($st);
        }

        // 14. Initial Activity Logs
        ActivityLog::log('system.seed', 'Initialized database schema, roles, permissions, and sample institution data', $adminUser);
    }
}
