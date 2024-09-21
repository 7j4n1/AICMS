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
        Schema::create('completed_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coopId')->nullable();
            $table->unsignedDouble('loanAmount')->default(0.00);
            $table->unsignedDouble('loanPaid')->default(0.00);
            $table->unsignedDouble('loanBalance')->default(0.00);
            $table->date('loanDate')->nullable();
            $table->date('repaymentDate')->nullable();
            $table->date('lastPaymentDate')->nullable();
            $table->string('userId')->nullable();
            $table->timestamps();

            $table->foreign('coopId')->references('coopId')->on('members')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completed_loans');
    }
};
