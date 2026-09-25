<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('misconduct_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique(); // e.g. RPT-202608-0042
            $table->foreignId('reporter_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reporter_role')->default('student'); // student, trainer, staff
            
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reported_party_name')->nullable(); // In case reported party is external or not in user table
            $table->string('reported_party_type')->default('staff'); // staff, admin, trainer, student, general_management
            
            $table->string('category'); // harassment, bribery, unprofessional_conduct, academic_dishonesty, abuse_of_office, cyber_misconduct, other
            $table->string('subject_summary');
            $table->text('incident_details');
            $table->date('incident_date')->nullable();
            $table->string('location')->nullable();
            $table->string('evidence_file_path')->nullable();

            // Whistleblower protection & escalation controls
            $table->boolean('hide_reporter_from_subject')->default(true);
            $table->boolean('is_escalated_to_secondary_contact')->default(false);
            $table->string('secondary_contact_notified_to')->nullable();

            // Workflow status
            $table->string('status')->default('open')->index(); // open, investigating, escalated, resolved, dismissed
            $table->text('resolution_summary')->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('misconduct_reports');
    }
};
