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
        Schema::create('teacher_subject_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_subject_id')->constrained()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject_notes');
    }
};
