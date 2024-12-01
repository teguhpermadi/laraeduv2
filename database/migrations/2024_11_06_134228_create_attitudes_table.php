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
        Schema::create('attitudes', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreignUlid('grade_id')->references('id')->on('grades')->cascadeOnDelete();
            $table->foreignUlid('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->integer('attitude_religius')->default(0);
            $table->integer('attitude_social')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attitudes');
    }
};
