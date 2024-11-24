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
            $table->id()->from(1600);
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quran_grade_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_quran_id')->constrained()->cascadeOnDelete();
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
