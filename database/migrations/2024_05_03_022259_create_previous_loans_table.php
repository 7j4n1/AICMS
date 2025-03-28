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
        Schema::create('previous_loans', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->unsignedBigInteger('coopId')->nullable();
            $table->unsignedDouble('loanAmount', null, 2)->default(0.00);
            $table->date('loanDate')->nullable();
            // 4 Guarantors coopId
            $table->string('guarantor1')->nullable();
            $table->string('guarantor2')->nullable();
            $table->string('guarantor3')->nullable();
            $table->string('guarantor4')->nullable();
            $table->string('status')->default(0);
            $table->string('userId')->nullable();
            $table->json('editDates')->nullable();
            $table->json('editedBy')->nullable();
            $table->date('repaymentDate')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('previous_loans');
    }
};
