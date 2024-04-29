<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('coopId')->unique();
            // create columns for other Member details
            $table->string('surname')->nullable();
            $table->string('otherNames')->nullable();
            $table->string('occupation')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('bankName')->nullable();
            $table->string('accountNumber')->nullable();
            $table->string('nextOfKinName')->nullable();
            $table->string('nextOfKinPhoneNumber')->nullable();
            $table->year('yearJoined')->nullable();
            $table->string('userId')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE members ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
