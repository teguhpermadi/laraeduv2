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
        Schema::create('student_extracurriculars', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('student_id')->constrained('students');
            $table->foreignUlid('extracurricular_id')->constrained('extracurriculars');
            $table->foreignUlid('academic_year_id')->constrained('academic_years');
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_extracurriculars');
    }
};
