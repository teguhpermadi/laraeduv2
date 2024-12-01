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
        Schema::create('teacher_extracurriculars', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();    
            $table->foreignUlid('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignUlid('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['academic_year_id', 'teacher_id', 'extracurricular_id'], 'teacher_extracurricular_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_extracurriculars');
    }
};
