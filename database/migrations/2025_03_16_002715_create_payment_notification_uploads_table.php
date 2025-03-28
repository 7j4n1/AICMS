<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_notification_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coopId')->nullable();
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->date('payment_date')->nullable();
            $table->string('payment_time')->nullable();
            $table->string('bank_used')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('depositor_name')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('additional_details')->nullable();
            $table->string('status')->default('pending');
            $table->string('approved_by')->nullable();
            $table->date('approved_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->date('rejected_at')->nullable();
            $table->string('rejected_reason')->nullable();
           
            $table->foreign('coopId')->on('members')->references('coopId')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_notification_uploads');
    }
};
