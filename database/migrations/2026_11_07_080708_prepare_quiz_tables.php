<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Soal: tambah penjelasan jawaban, dan jadikan "type" teks biasa (bukan enum)
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable();
            }
            $table->string('type', 20)->default('multiple_choice')->change();
        });

        // 2. Nilai: tambah bintang dan jumlah jawaban benar
        Schema::table('scores', function (Blueprint $table) {
            if (!Schema::hasColumn('scores', 'stars')) {
                $table->unsignedTinyInteger('stars')->default(0);
            }
            if (!Schema::hasColumn('scores', 'correct_count')) {
                $table->unsignedSmallInteger('correct_count')->default(0);
            }
        });

        // 3. Catatan soal yang sudah pernah dijawab benar (supaya XP hanya diberikan sekali per soal)
        if (!Schema::hasTable('question_user')) {
            Schema::create('question_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'question_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('question_user');
        // Kolom tambahan sengaja tidak dihapus supaya data tidak hilang
    }
};