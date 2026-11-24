<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'content') && !Schema::hasColumn('notes', 'body')) {
                $table->renameColumn('content', 'body');
            }
            if (!Schema::hasColumn('notes', 'color')) {
                $table->string('color', 10)->default('krem');
            }
        });
    }

    public function down(): void
    {
        // sengaja dikosongkan supaya data lama tidak ikut berubah
    }
};

   