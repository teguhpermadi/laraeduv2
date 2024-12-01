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
        Schema::create('leger_qurans', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUlid('quran_grade_id')->constrained('quran_grades')->cascadeOnDelete();
            $table->foreignUlid('teacher_quran_grade_id')->constrained('teacher_quran_grades')->cascadeOnDelete();
            $table->integer('score');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('sum');
            $table->integer('rank');
            $table->timestamps();

            $table->unique(['academic_year_id', 'student_id', 'quran_grade_id', 'teacher_quran_grade_id'], 'leger_quran_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leger_qurans');
    }
};
