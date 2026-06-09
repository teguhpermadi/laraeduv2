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
        Schema::create('student_rdms', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nisn')->nullable();
            $table->string('nis')->nullable();
            $table->string('rdm_id')->nullable();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_rdms');
    }
};
