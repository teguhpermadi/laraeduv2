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
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUlid('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignUlid('teacher_id')->constrained('teachers')->cascadeOnDelete();  
            $table->foreignUlid('project_theme_id')->constrained('project_themes')->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('phase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
