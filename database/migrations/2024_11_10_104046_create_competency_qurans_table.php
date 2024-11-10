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
        Schema::create('competency_qurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_quran_grade_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->text('description');
            $table->integer('passing_grade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_qurans');
    }
};
