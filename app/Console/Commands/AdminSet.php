<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AdminSet extends Command
{
    protected $signature = 'admin:set {username : Username akun yang sudah terdaftar} {--cabut : Cabut hak admin}';

    protected $description = 'Jadikan sebuah akun sebagai admin (atau cabut hak adminnya dengan --cabut)';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))->first();

        if (!$user) {
            $this->error('Pengguna dengan username itu tidak ditemukan. Daftar dulu lewat halaman register.');

            return self::FAILURE;
        }

        $jadiAdmin = !$this->option('cabut');
        $user->forceFill(['is_admin' => $jadiAdmin])->save();

        $this->info($jadiAdmin
            ? "{$user->username} sekarang admin."
            : "Hak admin {$user->username} sudah dicabut.");

        return self::SUCCESS;
    }
}
