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
        Schema::create('repay_captures', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->unsignedBigInteger('coopId')->nullable();
            $table->uuid('item_capture_id')->nullable();
            $table->decimal('amountToRepay', 15, 2)->default(0);
            $table->decimal('loanBalance', 15, 2)->default(0);
            $table->date('repaymentDate')->default(date('Y-m-d'));
            $table->decimal('serviceCharge')->default(0);
            $table->string('userId')->nullable();
            $table->json('editDates')->nullable();
            $table->json('editedBy')->nullable();
            $table->timestamps();

            $table->foreign('item_capture_id')->on('item_captures')->references('id')->nullOnDelete();
            $table->foreign('coopId')->on('members')->references('coopId')->nullOnDelete();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repay_captures');
    }
};
