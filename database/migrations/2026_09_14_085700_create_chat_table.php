<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom tambahan di tabel users (hanya ditambahkan kalau belum ada)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kelas')) {
                $table->string('kelas', 3)->nullable();          // X, XI, atau XII
            }
            if (!Schema::hasColumn('users', 'is_moderator')) {
                $table->boolean('is_moderator')->default(false);
            }
            if (!Schema::hasColumn('users', 'muted_until')) {
                $table->timestamp('muted_until')->nullable();    // dibisukan sampai jam ini
            }
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('room', 10);                          // semua, x, xi, atau xii
            $table->string('body', 300);
            $table->boolean('hidden')->default(false);           // disembunyikan moderator / laporan
            $table->timestamps();

            $table->index(['room', 'id']);
        });

        Schema::create('chat_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // pelapor
            $table->timestamps();

            $table->unique(['message_id', 'user_id']);           // satu siswa satu laporan per pesan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_reports');
        Schema::dropIfExists('chat_messages');
        // Kolom di tabel users sengaja tidak dihapus supaya data kelas siswa tidak hilang
    }
};