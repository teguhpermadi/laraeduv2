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
        Schema::create('grades', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('name');
            $table->string('grade');
            $table->string('phase');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);

            $table->unique(['name', 'grade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
