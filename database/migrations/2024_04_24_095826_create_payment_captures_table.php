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
            $table->unsignedDouble('splitOption', null, 2)->default(0.00);
            $table->unsignedDouble('loanAmount', null, 2)->default(0.00);
            $table->unsignedDouble('savingAmount', null, 2)->default(0.00);
            $table->unsignedDouble('totalAmount', null, 2)->default(0.00);
            $table->date('paymentDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedDouble('others', null, 2)->default(0.00);
            $table->unsignedDouble('shareAmount', null, 2)->default(0.00);
            $table->string('userId')->nullable();

            $table->foreign('coopId')->references('coopId')->on('members');

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
