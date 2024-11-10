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
        Schema::create('previous_ledger2023s', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->unsignedBigInteger('coopId')->nullable();
            $table->unsignedDouble('splitOption')->default(0);
            $table->unsignedDouble('loanAmount')->default(0);
            $table->unsignedDouble('savingAmount')->default(0);
            $table->unsignedDouble('totalAmount')->default(0);
            $table->date('paymentDate')->nullable();
            $table->unsignedDouble('others')->default(0);
            $table->unsignedDouble('shareAmount')->default(0);
            $table->unsignedBigInteger('adminCharge')->default(0);
            $table->string('userId')->nullable();
            $table->json('editDates')->nullable();
            $table->json('editedBy')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('previous_ledger2023s');
    }
};
