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
        Schema::create('competencies', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('passing_grade')->default(0);
            $table->boolean('half_semester')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies');
    }
};
