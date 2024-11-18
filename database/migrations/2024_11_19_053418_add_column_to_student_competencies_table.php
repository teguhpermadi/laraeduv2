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
        Schema::table('student_competencies', function (Blueprint $table) {
            // add column score_skill
            $table->integer('score_skill')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_competencies', function (Blueprint $table) {
            $table->dropColumn('score_skill');
        });
    }
};
