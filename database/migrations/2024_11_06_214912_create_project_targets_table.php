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
        Schema::create('project_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('phase');
            $table->foreignId('dimention_id')->constrained()->cascadeOnDelete();
            $table->foreignId('element_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_element_id')->constrained()->cascadeOnDelete();
            $table->foreignId('value_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_value_id')->constrained()->cascadeOnDelete();
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_targets');
    }
};
