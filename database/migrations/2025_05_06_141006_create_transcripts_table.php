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
        Schema::create('transcripts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUlid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->decimal('report_score')->default(0);
            $table->decimal('written_exam')->nullable();
            $table->decimal('practical_exam')->nullable();
            $table->decimal('average_score', 8, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'subject_id'], 'transcript_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
