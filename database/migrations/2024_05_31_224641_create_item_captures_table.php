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
            $table->unsignedBigInteger('coopId')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->decimal('price', 15, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->date('buyingDate')->default(date('Y-m-d'));
            $table->bigInteger('payment_timeframe')->nullable();
            $table->boolean('payment_status')->default(1);
            $table->string('userId')->nullable();
            $table->date('repaymentDate')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('loanPaid')->default(0);
            $table->decimal('loanBalance')->default(0.00);
            // $table->string('userId')->nullable();
            $table->json('editDates')->nullable();
            $table->json('editedBy')->nullable();

            $table->timestamps();

            $table->foreign('coopId')->references('coopId')->on('members')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('item_categories')->nullOnDelete();
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
