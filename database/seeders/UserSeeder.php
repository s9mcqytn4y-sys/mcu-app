<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder akun pengguna.
     */
    public function run(): void
    {
        // Proteksi P0: Dilarang keras mengeksekusi seeder akun default pada environment produksi!
        if (app()->isProduction()) {
            return;
        }

        // 1. Akun Pengguna Wira (Staff Senior & Administrator)
        User::firstOrCreate(
            ['username' => 'wira'],
            [
                'name' => 'Wira',
                'email' => 'wira@klinikindosehat.com',
                'peran' => 'administrator',
                'password' => Hash::make('WiraIndosehat2026!'),
                'aktif' => true,
            ]
        );

        // 2. Akun Utama Administrator Indosehat
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Indosehat',
                'email' => 'admin@klinikindosehat2003.com',
                'peran' => 'administrator',
                'password' => Hash::make('Indosehat2003!'),
                'aktif' => true,
            ]
        );

        // 3. Akun Staf Pelaksana MCU
        User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staf Pelaksana MCU',
                'email' => 'staff@klinikindosehat2003.com',
                'peran' => 'staf',
                'password' => Hash::make('StaffMCU2003!'),
                'aktif' => true,
            ]
        );
    }
}
