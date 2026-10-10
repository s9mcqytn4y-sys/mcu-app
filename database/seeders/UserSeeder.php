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

        // 1. Akun Pengguna Wira (Satu-satunya akun default: Administrator & Penanggung Jawab Sistem)
        User::updateOrCreate(
            ['username' => 'wira'],
            [
                'name' => 'Wira Pramudya',
                'email' => 'wirapramudya2q@gmail.com',
                'peran' => 'administrator',
                'password' => Hash::make('Wrprmdy13'),
                'aktif' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
