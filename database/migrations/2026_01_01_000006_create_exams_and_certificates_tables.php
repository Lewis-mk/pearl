<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->string('type')->default('online_quiz'); // online_quiz, physical_sitin, hybrid
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_marks')->default(100);
            $table->integer('pass_percentage')->default(60);
            $table->boolean('randomize_questions')->default(true);
            $table->integer('max_attempts')->default(1);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            $table->string('status')->default('published'); // draft, published, closed
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->string('type')->default('multiple_choice'); // multiple_choice, free_response
            $table->json('options')->nullable(); // JSON array of options for MCQ e.g. [{"key":"A","text":"..."},{"key":"B","text":"..."}]
            $table->string('correct_answer')->nullable(); // e.g. "A" or text keyword
            $table->integer('marks')->default(10);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        Schema::create('exam_sitin_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->dateTime('slot_datetime');
            $table->string('venue'); // e.g. "Main Exam Hall, Room 101"
            $table->integer('capacity')->default(25);
            $table->integer('booked_count')->default(0);
            $table->string('status')->default('open'); // open, full, completed, cancelled
            $table->timestamps();
        });

        Schema::create('exam_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sitin_slot_id')->nullable()->constrained('exam_sitin_slots')->nullOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->boolean('retake_granted')->default(false);
            $table->foreignId('retake_granted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->json('student_answers')->nullable(); // JSON: {question_id: answer_value}
            
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade_status')->default('pending'); // pending, auto_graded, graded, released
            $table->boolean('passed')->nullable();
            $table->text('trainer_feedback')->nullable();
            $table->foreignId('graded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();

            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique(); // e.g. PTI-CERT-2026-0012
            $table->string('verification_code')->unique(); // alphanumeric hash for verification URL
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->string('student_full_name');
            $table->string('course_title');
            $table->string('grade')->default('Pass with Merit'); // Distinction, Credit, Pass, Pass with Merit
            $table->date('completion_date');
            $table->date('issue_date');
            $table->foreignId('issued_by_user_id')->constrained('users');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('exam_submissions');
        Schema::dropIfExists('exam_sitin_slots');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};
