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
        Schema::create('userables', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('userable_id');
            $table->string('userable_type');
            $table->timestamps();
            
            $table->unique(['userable_id', 'userable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userables');
    }
};
