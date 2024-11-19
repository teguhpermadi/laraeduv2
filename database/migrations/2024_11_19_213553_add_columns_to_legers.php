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
        Schema::table('legers', function (Blueprint $table) {
            $table->integer('score_skill')->default(0);
            $table->integer('sum_skill')->default(0);
            $table->string('description_skill')->nullable();
            $table->integer('subject_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legers', function (Blueprint $table) {
            $table->dropColumn(['score_skill', 'sum_skill', 'description_skill', 'subject_order']);
        });
    }
};
