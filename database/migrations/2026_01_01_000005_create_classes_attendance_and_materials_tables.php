<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->string('delivery_mode')->default('hybrid'); // physical, online, hybrid
            $table->string('meeting_url')->nullable(); // Zoom, Google Meet, Jitsi URL
            $table->string('physical_location')->nullable(); // e.g. "Lab 2, 2nd Floor, Pearl Campus"
            
            // Postponement
            $table->boolean('is_postponed')->default(false);
            $table->text('postponement_reason')->nullable();
            $table->dateTime('rescheduled_start')->nullable();
            $table->dateTime('rescheduled_end')->nullable();

            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, postponed
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('present'); // present, absent, excused, late
            $table->foreignId('marked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['class_session_id', 'student_id']);
        });

        Schema::create('absence_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_session_id')->nullable()->constrained()->nullOnDelete();
            $table->date('requested_date');
            $table->text('reason');
            $table->string('evidence_file_path')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reviewer_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained()->cascadeOnDelete(); // nullable means available to all cohorts of course
            $table->foreignId('class_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_type')->default('document'); // document, video_link, slides, zip
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->foreignId('uploaded_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('absence_requests');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('class_sessions');
    }
};
