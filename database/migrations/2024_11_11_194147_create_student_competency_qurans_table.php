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
        Schema::create('student_competency_qurans', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUlid('quran_grade_id')->constrained('quran_grades')->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUlid('competency_quran_id')->constrained('competency_qurans')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->unique(['academic_year_id', 'quran_grade_id', 'competency_quran_id', 'student_id'], 'student_competency_quran_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_competency_qurans');
    }
};
