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
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('phase');
            $table->foreignUlid('dimention_id')->constrained('dimentions')->cascadeOnDelete();
            $table->foreignUlid('element_id')->constrained('elements')->cascadeOnDelete();
            $table->foreignUlid('sub_element_id')->constrained('sub_elements')->cascadeOnDelete();
            $table->foreignUlid('value_id')->constrained('values')->cascadeOnDelete();
            $table->foreignUlid('sub_value_id')->constrained('sub_values')->cascadeOnDelete();
            $table->foreignUlid('target_id')->constrained('targets')->cascadeOnDelete();
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
