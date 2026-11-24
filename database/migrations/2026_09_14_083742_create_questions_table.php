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
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();
        $table->text('question');
        $table->enum('type', [
            'multiple_choice',
            'true_false',
            'fill_blank',
            'matching'
        ]);
        $table->text('answer')->nullable();
        $table->text('options')->nullable();
        $table->integer('points')->default(50);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('questions');
    }
};
