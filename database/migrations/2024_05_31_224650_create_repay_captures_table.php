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
            $table->foreign('coopId')->on('members')->references('coopId')->cascadeOnDelete();
            $table->foreign('item_capture_id')->on('item_captures')->references('id')->cascadeOnDelete();
            $table->decimal('amountToRepay')->default(0);
            $table->date('repaymentDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('serviceCharge')->default(0);
            $table->foreign('userId')->on('admins')->references('id');
            $table->timestamps();
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
