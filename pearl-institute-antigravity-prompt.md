# Build Prompt — Pearl Training Institute Platform
*(Copy everything below into Antigravity as your project prompt/spec)*

---

You are building a full-stack web platform for **Pearl Training Institute**, a short-course school in Kenya, hosted at **pearlinstitute.com**. The platform combines a public marketing website with a role-based LMS and operations system. Build this as a production-grade application, not a prototype — with proper authentication, validation, and data integrity throughout.

## 1. Tech Stack

- **Backend:** Laravel (PHP) — use Laravel's built-in auth, policies/gates for RBAC, and queues for async jobs (emails, SMS, M-Pesa callbacks)
- **Frontend:** Laravel Livewire + Blade (server-rendered, minimal JS) — prioritize speed of delivery over SPA complexity
- **Database:** MySQL
- **Payments:** Safaricom Daraja API (M-Pesa STK Push + C2B)
- **SMS:** Africa's Talking API
- **Staff email:** Zoho Mail API (for auto-provisioning @pearlinstitute.com accounts for staff only)
- **File storage:** Local disk with S3-compatible driver interface (so it can move to S3 later without refactor)
- **Video classes:** Store an external class link field (Zoom/Meet/Jitsi URL) — no video hosting needed
- Mobile-responsive throughout; assume many users are on low-bandwidth mobile connections — keep pages light, paginate lists, compress uploaded media.

## 2. Core Architecture Principle

Build **one application with one user table and full role-based access control (RBAC)** — not separate apps per role.

- A `User` can hold **multiple roles simultaneously** (e.g., a Trainer who also covers the cybercafe) — do not model role as a single field on the user; use a `role_user` pivot table.
- Roles: `admin`, `trainer`, `cyber_attendant`, `student`.
- Build a **granular permissions system**, not role-hardcoded logic:
  - `permissions` table (key, description)
  - `permission_role` pivot (default permissions per role)
  - `permission_user` pivot for **per-user overrides** (grant/revoke), so Admin can, e.g., give one specific Trainer or Cyber Attendant the `student.admit` or `payment.collect_cash` permission without changing the whole role.
  - Example permission keys to implement: `student.admit`, `payment.collect_cash`, `course.create`, `course.edit_outline`, `exam.schedule`, `exam.grade`, `attendance.mark`, `misconduct.report`, `misconduct.view_all`, `class.schedule`, `class.postpone`, `materials.upload`, `reports.view_financial`, `users.manage_roles`.
  - All controllers/Livewire components must check permissions via Laravel policies/gates — never hardcode `if ($user->role === 'trainer')`.
- Maintain an **audit log** (`activity_log` table or a package like `spatie/laravel-activitylog`) recording who did what, especially for cash collection, admissions, and permission changes.

## 3. Public Website

Build these pages, publicly accessible, SEO-friendly, no auth required:

- **Home** — hero section, featured courses, testimonials, call-to-action to enroll
- **Courses listing + individual course pages** — pulled from the `courses` table (description, duration, fee structure, intake dates/cohorts, "Enroll Now" button)
- **Printshop page** ("Ideal Print Shop" section) — lists cyber/print services with pricing; includes a "Request Service" form that creates a `service_request` record and appears in the Cyber Attendant portal queue
- **About**
- **Contact** (with location/map embed)
- **Login** — single login form; after auth, redirect based on the user's role(s) to the correct portal/dashboard

## 4. Student Registration & Admission Flow

1. A visitor registers via the public site **or** is admitted in person by any staff member holding the `student.admit` permission (Admin by default; Trainer/Cyber Attendant if granted).
2. Registration form captures: full name, **personal email** (this is the student's login — do not issue an @pearlinstitute.com email to students), phone number (required — used for M-Pesa and as a fallback identifier), course selection, cohort/intake batch, and — if the course category is flagged as commonly serving minors — a guardian name/contact field (make this conditional on a `requires_guardian_info` flag on the course).
3. Verify the student's email via a confirmation link before activating login.
4. Student pays a deposit via M-Pesa STK Push (or is recorded as paying cash by an admitting staff member).
5. Registration status = `pending_approval` until an authorized staff member approves it.
6. On approval:
   - Generate a unique, sequential **admission number** for the student (independent of email — used on receipts, certificates, ID references). This must never change even if the student later updates their email.
   - Activate their portal account (login = their personal email + password they set).
   - Send a welcome notification (email + SMS) with next steps.
7. Support login by **phone number as a fallback** to email, in case email delivery becomes unreliable for a student.

## 5. Student Portal

Build a dashboard and the following modules, scoped to the logged-in student only:

- **Dashboard** — enrolled course(s)/cohort, progress %, upcoming classes, outstanding balance, recent notifications
- **Payments**
  - Pay via M-Pesa STK Push (deposit or installment)
  - View full payment history with **sequential, immutable receipt numbers** for both M-Pesa and cash payments
  - Running balance tracker per course (handle partial/installment payments correctly)
- **Classes** — timetable; join link for online classes; physical class location/schedule; see postponement notices
- **Materials** — download learning materials (documents/videos) scoped to their enrolled course/cohort
- **Exams**
  - Take online exams/quizzes on the portal (support both auto-graded multiple choice and manually-graded free response)
  - Book a slot for a **physical sit-in exam** from dates the trainer has published (must respect capacity limits per slot if configured)
  - View results and download a certificate (auto-generated PDF) on course completion
  - Basic exam integrity: enforce a time limit, randomize question order per attempt, lock to one attempt unless a trainer grants a retake
- **Attendance** — view own attendance record; submit an absence request (reason + optional evidence attachment) for trainer/admin review and approval/rejection
- **Misconduct reporting** — a form to report a trainer or staff member. Capture the student's identity internally, but **hide the reporter's identity from the reported staff member by default**; only reveal it if the case is escalated to a formal investigation. Reports route to Admin. If the report is about the Admin/Owner themselves, route it to a configurable secondary contact (support a `misconduct_escalation_contact` setting for this case).
- **Progress tracker** — modules/units completed vs. total, visual completion percentage
- **Profile/settings** — update contact info, change password

## 6. Trainer Portal

- **Dashboard** — today's classes, pending grading, attendance to take
- **Class management** — schedule classes (single or recurring) for their cohorts; post the online class link and/or physical location; mark a class as **postponed** (auto-notify enrolled students via email/SMS); upload materials scoped to a class/module
- **Attendance** — take attendance per session; review and approve/reject student absence requests
- **Exams** — build exams/quizzes with a question bank (multiple choice + free response); publish **physical sit-in exam dates** with optional capacity limits for students to book; grade submissions; release results
- **Student progress** — view per-student completion % and performance history for their own cohorts
- **Course management** *(only if granted the `course.create`/`course.edit_outline` permission)* — create new courses and edit outlines; changes may require Admin approval before publishing live (`draft` → `pending_review` → `published` status flow)
- **Admissions** *(only if granted `student.admit`/`payment.collect_cash`)* — admit new students, log cash payments
- **Misconduct & complaints** — report a fellow staff member or a student; file general complaints to Admin
- **Messaging/announcements** — post announcements visible to their own students only

## 7. Cyber Attendant Portal

- **Service request queue** — view incoming requests submitted via the public Printshop form, plus ability to log walk-in requests manually; update status (`pending` → `in_progress` → `completed`)
- Log cash transactions for print/cyber services
- *If granted permission:* admit new students, collect/confirm course payments (cash or M-Pesa confirmation)
- **Daily summary report** — jobs completed and revenue collected for their shift/day

## 8. Admin Portal

- **User & role management** — create/deactivate any user, assign one or more roles, grant/revoke individual permission overrides, trigger staff @pearlinstitute.com email provisioning
- **Course management** — full CRUD on all courses, approve/publish trainer-submitted courses and outline edits
- **Student management** — view/approve all pending admissions, reassign students between trainers/cohorts
- **Financials**
  - Full ledger of all payments (M-Pesa + cash), with reconciliation view
  - Outstanding balances report across all students
  - Revenue breakdown by course, trainer, and time period
  - Printshop revenue tracked and reported **separately** from course revenue
  - **Refund/withdrawal handling**: support recording a partial refund or forfeited-deposit outcome when a student withdraws, with a documented reason field
- **Misconduct & complaints inbox** — centralized view of all reports (student→staff, staff→staff, staff→student) with status workflow (`open` → `investigating` → `resolved`) and strict visibility controls — only Admin (and any explicitly designated reviewer role) can see raw report content
- **Exams oversight** — view all exam schedules, sit-in bookings, and results across the school
- **Attendance oversight** — flag students with chronic absenteeism
- **Reports & analytics dashboards** — enrollment trends, completion rates, trainer performance, cash vs. M-Pesa payment split
- **System settings** — fee structures per course/cohort, refund policy parameters, SMS/email templates, the permission matrix editor, and the misconduct-escalation-contact setting
- **Audit log viewer**

## 9. M-Pesa Integration (Daraja API)

- Implement **STK Push** for student-initiated deposit/installment payments from the portal
- Implement **C2B** handling for payments made directly via Paybill/Till outside the portal, reconciled via callback
- Callback/confirmation endpoint must be idempotent and publicly reachable over HTTPS; on success, update the payment ledger and trigger a receipt notification automatically
- Persist: transaction ID, amount, phone number, timestamp, student reference, and payment purpose (deposit/installment/full/printshop)
- Support **partial/installment payments** with a running balance per student per course
- Cash payments recorded by any staff member with the appropriate permission must flow into the **same unified payment ledger** as M-Pesa transactions, each with a sequential receipt number
- **Build a manual fallback path**: since M-Pesa API access isn't always available, ensure staff can still record a cash or manually-confirmed M-Pesa payment during an outage without the system blocking admissions

Since Daraja production access approval can take time, build against the **sandbox environment first** and structure the integration so switching to production credentials is a config change only.

## 10. Staff Email Provisioning

- **Students use their own personal email to log in** — do not provision any mailbox for them.
- **Staff (Admin, Trainer, Cyber Attendant)** get a **custom @pearlinstitute.com mailbox**, provisioned via the Zoho Mail API when Admin approves a new staff account.
- Build this as an isolated service class so the email provider can be swapped later without touching the rest of the app.

## 11. Cohorts / Intake Batches

- Model courses as having **cohorts/intake batches** (e.g., "Graphic Design — Jan 2027 Intake") rather than open-ended enrollment.
- Students, class schedules, exam dates, and progress tracking should all be scoped to a cohort, not just a course — this keeps scheduling and reporting coherent.

## 12. Notifications

- Email + SMS (Africa's Talking) for: registration confirmation, admission approval, payment receipts, payment reminders, class postponement, exam date reminders, absence request outcome, misconduct report acknowledgment.
- Build notifications as queued jobs, not inline/blocking calls.

## 13. Data & Compliance

- Design the schema and access controls with **Kenya's Data Protection Act (2019)** in mind: minimize collection of sensitive personal data, restrict access to PII by role/permission, and support data export/deletion requests for a given user.
- Keep financial records (payments, receipts) immutable once created — corrections should be new adjusting entries, not edits to historical records.

## 14. Build Order (recommended)

1. **Foundation** — auth, multi-role RBAC + permissions system, public website pages, course/cohort models, student registration + approval flow, basic Admin user management. Start the Daraja sandbox application process in parallel with this phase.
2. **Payments & core portals** — M-Pesa STK Push + cash payment ledger with receipt numbering, Student portal (dashboard/payments/materials), Trainer portal (classes/attendance/materials), staff email auto-provisioning.
3. **LMS depth** — exams (online + sit-in booking with capacity limits), progress tracking, certificate generation, absence request workflow, misconduct/complaint system with escalation-contact handling.
4. **Cyber Attendant & polish** — Printshop request queue and portal, permission-based admission/cash collection for non-admin roles, refund/withdrawal handling, analytics dashboards, audit log viewer, security review.

## 15. Non-negotiables (do not skip these)

- Multi-role support per user (pivot table, not a single role column)
- Granular, database-driven permissions (not hardcoded role checks)
- Sequential, immutable receipt numbers for every payment, cash or M-Pesa
- Admission numbers independent of email
- Misconduct reports hidden from the reported party by default, with a working escalation path when the report is about Admin
- A functioning manual/cash fallback for admissions and payments when M-Pesa is unavailable
- Full audit logging on cash handling, admissions, and permission changes

Build incrementally, and after each phase, summarize what was implemented and any schema/API decisions made so the plan stays traceable.
