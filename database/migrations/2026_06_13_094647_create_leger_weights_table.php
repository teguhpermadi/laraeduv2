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
        Schema::create('leger_weights', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignUlid('teacher_subject_id')->nullable()->constrained('teacher_subjects')->cascadeOnDelete();
            $table->integer('daily_weight')->default(0);
            $table->integer('mid_weight')->default(0);
            $table->integer('final_weight')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leger_weights');
    }
};
