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
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quran_grade_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_quran_grade_id')->constrained()->cascadeOnDelete();
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
