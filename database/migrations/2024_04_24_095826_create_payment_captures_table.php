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
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        Schema::create('payment_captures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('coopId');
            $table->unsignedDouble('splitOption')->default(0);
            $table->unsignedDouble('loanAmount')->default(0);
            $table->unsignedDouble('savingAmount')->default(0);
            $table->unsignedDouble('totalAmount')->default(0);
            $table->date('paymentDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedDouble('others')->default(0);
            $table->unsignedDouble('shareAmount')->default(0);
            $table->unsignedBigInteger('adminCharge')->default(0);
            $table->string('userId')->nullable();

            $table->foreign('coopId')->references('coopId')->on('members');

            $table->timestamps();
        });
        DB::statement('ALTER TABLE payment_captures ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_captures');
    }
};
