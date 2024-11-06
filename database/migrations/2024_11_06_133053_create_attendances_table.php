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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreignId('grade_id')->references('id')->on('grades')->cascadeOnDelete();
            $table->foreignId('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->string('sick')->default(0);
            $table->string('permission')->default(0);
            $table->string('absent')->default(0);
            $table->string('note')->nullable();
            $table->string('achievement')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'grade_id', 'student_id'], 'attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
