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
        Schema::create('legers', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->foreignUlid('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignUlid('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->integer('sum')->default(0);
            $table->integer('rank')->default(0);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('category');
            $table->timestamps();

            $table->unique(['academic_year_id', 'student_id', 'teacher_subject_id', 'category'], 'leger_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legers');
    }
};
