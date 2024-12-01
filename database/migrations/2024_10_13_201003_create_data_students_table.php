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
        Schema::create('data_students', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->string('student_address')->nullable();
            $table->string('student_province')->nullable();
            $table->string('student_city')->nullable();
            $table->string('student_district')->nullable();
            $table->string('student_village')->nullable();
            $table->string('religion')->nullable();
            $table->string('previous_school')->nullable();
            $table->date('date_received')->nullable();
            $table->string('grade_received')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_education')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_address')->nullable();
            $table->string('guardian_village')->nullable();
            $table->string('parent_address')->nullable();
            $table->string('parent_province')->nullable();
            $table->string('parent_city')->nullable();
            $table->string('parent_district')->nullable();
            $table->string('parent_village')->nullable();
            $table->timestamps();

            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_students');
    }
};
