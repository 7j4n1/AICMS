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
            $table->unsignedBigInteger('coopId');
            $table->decimal('annual_savings')->default(0.00);
            $table->decimal('annual_fee')->default(0.00);
            $table->decimal('total_savings')->default(0.00);
            $table->year('annual_year');
            $table->string('userId')->nullable();
            $table->timestamps();

            $table->foreign('coopId')->references('coopId')->on('members');
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