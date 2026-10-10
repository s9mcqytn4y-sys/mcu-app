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
        // 1. Akun Pengguna Wira (Staff Senior & Administrator)
        User::updateOrCreate(
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
        User::updateOrCreate(
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
        User::updateOrCreate(
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
