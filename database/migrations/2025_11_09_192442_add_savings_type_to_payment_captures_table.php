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
        Schema::table('payment_captures', function (Blueprint $table) {
            $table->foreignId('savings_type_id')->nullable()->after('specialSaveAmount')->constrained('savings_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_captures', function (Blueprint $table) {
            $table->dropForeign(['savings_type_id']);
            $table->dropColumn('savings_type_id');
        });
    }
};
