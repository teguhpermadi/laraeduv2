<?php

use App\Enums\CurriculumEnum;
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
        Schema::table('teacher_grades', function (Blueprint $table) {
            $table->string('curriculum')->default(CurriculumEnum::KURMER->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_grades', function (Blueprint $table) {
            $table->dropColumn('curriculum');
        });
    }
};
