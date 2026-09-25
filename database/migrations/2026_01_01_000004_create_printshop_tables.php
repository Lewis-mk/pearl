<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printshop_services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Color Printing A4, Document Binding, Passport Photo, KRA PIN Registration, Laminating
            $table->string('category')->default('printing'); // printing, design, cyber_services, stationery
            $table->decimal('unit_price', 10, 2);
            $table->string('unit_label')->default('per page'); // per page, per item, per session
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique(); // e.g. SR-202608-0012
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('service_type'); // e.g. Printing, Typesetting, Binding, KRA Return, ID/Passport
            $table->integer('quantity')->default(1);
            $table->text('instructions')->nullable();
            $table->string('attachment_path')->nullable();
            $table->decimal('quoted_amount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid
            $table->string('status')->default('pending')->index(); // pending, in_progress, completed, cancelled
            
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('staff_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('printshop_services');
    }
};
