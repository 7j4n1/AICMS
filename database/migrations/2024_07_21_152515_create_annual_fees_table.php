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
        Schema::create('annual_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coopId')->nullable();
            $table->decimal('annual_savings', 15, 2)->default(0.00);
            $table->decimal('annual_fee', 15, 2)->default(0.00);
            $table->decimal('total_savings', 15, 2)->default(0.00);
            $table->year('annual_year');
            $table->string('status')->default('pending');
            $table->string('userId')->nullable();
            $table->json('editDates')->nullable();
            $table->json('editedBy')->nullable();
            $table->timestamps();

            $table->foreign('coopId')->references('coopId')->on('members')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_fees');
    }
};
