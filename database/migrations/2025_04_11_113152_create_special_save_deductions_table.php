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
        Schema::create('special_save_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coopId')->nullable();
            $table->string('type')->nullable();
            $table->unsignedDouble('credit', 15, 2)->default(0.00);
            $table->unsignedDouble('debit', 15, 2)->default(0.00);
            $table->unsignedDouble('balance', 15, 2)->nullable();
            $table->date('paymentDate')->nullable();
            $table->string('userId')->nullable();
            $table->json('editDates')->nullable();
            $table->json('editedBy')->nullable();

            $table->foreign('coopId')->references('coopId')->on('members')->nullOnDelete();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('special_save_deductions');
    }
};
