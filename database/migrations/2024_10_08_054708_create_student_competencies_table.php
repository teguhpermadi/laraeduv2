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
        Schema::create('student_competencies', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->foreignUlid('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->timestamps();

            $table->unique(['student_id', 'competency_id', 'teacher_subject_id'], 'student_competency_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_competencies');
    }
};
