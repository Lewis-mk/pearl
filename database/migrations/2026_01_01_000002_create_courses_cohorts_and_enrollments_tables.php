<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('General'); // Technology, Business, Design, Cyber/Secretarial, etc.
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->text('curriculum_outline')->nullable();
            $table->integer('duration_weeks')->default(4);
            $table->decimal('total_fee', 10, 2);
            $table->decimal('deposit_required', 10, 2)->default(0.00);
            $table->boolean('requires_guardian_info')->default(false); // Flag for courses serving minors
            $table->string('status')->default('published'); // draft, pending_review, published, archived
            $table->string('featured_badge')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "January 2027 Intake", "Weekend Cohort 3"
            $table->string('code')->unique(); // e.g. "GD-2027-JAN"
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('max_capacity')->default(30);
            $table->string('status')->default('enrolling'); // enrolling, ongoing, completed, cancelled
            $table->foreignId('lead_trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending_approval')->index(); // pending_approval, active, completed, withdrawn, deferred
            
            // Guardian Info (conditional on requires_guardian_info)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_relationship')->nullable();

            // Financial summary per enrollment
            $table->decimal('fee_total', 10, 2)->default(0.00);
            $table->decimal('fee_paid', 10, 2)->default(0.00);
            $table->decimal('fee_balance', 10, 2)->default(0.00);
            $table->integer('completion_percentage')->default(0);

            // Admission audit
            $table->foreignId('admitted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admitted_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('withdrawal_reason')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'cohort_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('cohorts');
        Schema::dropIfExists('courses');
    }
};
