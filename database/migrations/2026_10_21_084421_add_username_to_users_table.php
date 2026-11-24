<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('username')->unique()->after('name');
        $table->string('email')->nullable()->change();
        $table->unsignedInteger('xp')->default(0);
        $table->unsignedInteger('level')->default(1);
    });
}
};
