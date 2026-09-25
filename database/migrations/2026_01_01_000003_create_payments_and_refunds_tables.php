<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique()->index(); // e.g. PRL-RCP-202608-00001 (Sequential & Immutable)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // student or customer
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->index(); // mpesa_stk, mpesa_c2b, mpesa_manual, cash
            $table->string('status')->default('completed')->index(); // pending, completed, failed, refunded
            $table->string('purpose')->default('installment')->index(); // deposit, installment, full, printshop, exam_fee
            
            // M-Pesa specifics
            $table->string('mpesa_receipt_number')->nullable()->index(); // Daraja TransID e.g. QK89123456
            $table->string('phone_number')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->string('checkout_request_id')->nullable()->index();
            $table->text('mpesa_raw_response')->nullable();

            // Staff & cash handling audit
            $table->foreignId('collected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_number')->unique();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->decimal('refund_amount', 10, 2)->default(0.00);
            $table->decimal('forfeited_deposit_amount', 10, 2)->default(0.00);
            $table->text('reason');
            $table->string('status')->default('approved'); // pending, approved, paid, rejected
            $table->foreignId('processed_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
    }
};
