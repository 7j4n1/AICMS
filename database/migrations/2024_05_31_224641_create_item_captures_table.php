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
        Schema::create('item_captures', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->foreign('coopId')->on('members')->references('coopId')->cascadeOnDelete();
            $table->bigInteger('quantity')->nullable();
            $table->date('buyingDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->bigInteger('payment_timeframe')->nullable();
            $table->boolean('payment_status')->default(1);
            $table->foreign('userId')->on('admins')->references('id');
            $table->date('repaymentDate')->nullable();
            $table->foreign('category_id')->on('item_categories')->references('id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_captures');
    }
};
