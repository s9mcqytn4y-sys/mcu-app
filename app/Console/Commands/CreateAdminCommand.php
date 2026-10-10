<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LogAudit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcu:admin-create
                            {--username= : Username unik untuk akun administrator}
                            {--email= : Alamat email akun}
                            {--name= : Nama lengkap penanggung jawab}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat akun administrator sistem MCU Indosehat secara interaktif dan aman tanpa menyimpan password di repositori';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Inisialisasi Akun Administrator MCU Indosehat 2003 ===');

        $username = (string) ($this->option('username') ?: $this->ask('Masukkan username administrator'));
        $name = (string) ($this->option('name') ?: $this->ask('Masukkan nama lengkap'));
        $email = (string) ($this->option('email') ?: $this->ask('Masukkan alamat email'));

        $validator = Validator::make([
            'username' => $username,
            'name' => $name,
            'email' => $email,
        ], [
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $password = $this->secret('Masukkan kata sandi (minimal 8 karakter)');
        $passwordConfirm = $this->secret('Konfirmasi kata sandi');

        if (empty($password) || strlen($password) < 8) {
            $this->error('Kata sandi harus minimal 8 karakter.');
            return self::FAILURE;
        }

        if ($password !== $passwordConfirm) {
            $this->error('Konfirmasi kata sandi tidak cocok.');
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'username' => strtolower($username),
            'email' => strtolower($email),
            'peran' => 'administrator',
            'password' => Hash::make($password),
            'aktif' => true,
            'email_verified_at' => now(),
        ]);

        LogAudit::rekam(
            tindakan: 'BUAT_AKUN_ADMINISTRATOR_CLI',
            tipeModel: User::class,
            idModel: $user->id,
            rincian: [
                'username' => $user->username,
                'email' => $user->email,
                'peran' => $user->peran,
            ]
        );

        $this->info("Akun administrator '{$user->username}' ({$user->name}) berhasil dibuat.");
        return self::SUCCESS;
    }
}
