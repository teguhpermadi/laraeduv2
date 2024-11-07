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
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_subject_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->integer('sum')->default(0);
            $table->integer('rank')->default(0);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->boolean('is_half_semester')->default(false);
            $table->timestamps();

            $table->unique(['academic_year_id', 'student_id', 'teacher_subject_id'], 'leger_unique');
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
