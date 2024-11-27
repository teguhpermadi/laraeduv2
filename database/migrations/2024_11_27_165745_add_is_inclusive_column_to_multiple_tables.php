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
        // tambahkan kolom is_inclusive ke table students
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('is_inclusive')->default(false);
        });

        // tambahkan kolom is_inclusive ke table student_grades
        Schema::table('student_grades', function (Blueprint $table) {
            $table->boolean('is_inclusive')->default(false);
        });

        // tambahkan kolom is_inclusive ke table competencies
        Schema::table('competencies', function (Blueprint $table) {
            $table->boolean('is_inclusive')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('is_inclusive');
        });

        Schema::table('student_grades', function (Blueprint $table) {
            $table->dropColumn('is_inclusive');
        });

        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn('is_inclusive');
        });
    }
};
