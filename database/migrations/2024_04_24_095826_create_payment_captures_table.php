<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_captures', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->unsignedBigInteger('coopId');
            $table->decimal('splitOption', 15, 2)->default(0.00);
            $table->decimal('loanAmount', 15, 2)->default(0.00);
            $table->decimal('savingAmount', 15, 2)->default(0.00);
            $table->decimal('totalAmount', 15, 2)->default(0.00);
            $table->date('paymentDate')->nullable();
            $table->decimal('hajj_savings', 15, 2)->default(0.00);
            $table->decimal('special_savings', 15, 2)->default(0.00);
            $table->decimal('ileya_savings', 15, 2)->default(0.00);
            $table->decimal('school_fees_savings', 15, 2)->default(0.00);
            $table->decimal('kids_savings', 15, 2)->default(0.00);
            $table->decimal('others', 15, 2)->default(0.00);
            $table->decimal('shareAmount', 15, 2)->default(0.00);
            $table->unsignedBigInteger('adminCharge')->default(0);
            $table->string('loan_type')->default('normal');
            $table->string('userId')->nullable();

            $table->foreign('coopId')->references('coopId')->on('members')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_captures');
    }
};
