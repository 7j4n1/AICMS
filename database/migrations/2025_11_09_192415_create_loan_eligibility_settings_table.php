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
        Schema::create('loan_eligibility_settings', function (Blueprint $table) {
            $table->id();
            $table->string('formula_key')->unique(); // 'default', 'custom_1', etc.
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('formula'); // e.g., '(savings + shares) * 2', 'savings * 2'
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_eligibility_settings');
    }
};
