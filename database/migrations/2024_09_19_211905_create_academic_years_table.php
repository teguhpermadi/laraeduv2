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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('year');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->foreignUlid('teacher_id')->nullable()->constrained('teachers')->cascadeOnDelete();
            $table->date('date_report_half')->nullable();
            $table->date('date_report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
