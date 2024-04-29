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
        Schema::create('loan_captures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('coopId');
            $table->unsignedDouble('loanAmount', null, 2)->default(0.00);
            $table->date('loanDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            // 4 Guarantors coopId
            $table->unsignedBigInteger('guarantor1')->nullable();
            $table->unsignedBigInteger('guarantor2')->nullable();
            $table->unsignedBigInteger('guarantor3')->nullable();
            $table->unsignedBigInteger('guarantor4')->nullable();
            $table->boolean('status')->default(0);
            $table->string('userId')->nullable();
            $table->date('repaymentDate')->nullable();

            $table->foreign('coopId')->references('coopId')->on('members');
            $table->foreign('guarantor1')->references('coopId')->on('members');
            $table->foreign('guarantor2')->references('coopId')->on('members');
            $table->foreign('guarantor3')->references('coopId')->on('members');
            $table->foreign('guarantor4')->references('coopId')->on('members');
            
            $table->timestamps();
        });

        DB::statement('ALTER TABLE loan_captures ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_captures');
    }
};
