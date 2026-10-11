<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\UndanganPenggunaNotification;
use Database\Seeders\McuSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan route /setup-hosting ditutup dan mengembalikan 404 untuk mencegah eksploitasi remote.
     */
    public function test_route_setup_hosting_telah_ditutup(): void
    {
        $response = $this->get('/setup-hosting');

        $response->assertStatus(404);
    }

    /**
     * Memastikan pesan kegagalan autentikasi telah terlokalisasi ke Bahasa Indonesia.
     */
    public function test_pesan_kegagalan_autentikasi_menggunakan_bahasa_indonesia(): void
    {
        app()->setLocale('id');

        $this->assertSame(
            'Nama pengguna/email atau kata sandi tidak valid.',
            trans('auth.failed')
        );
        $this->assertSame(
            'Kata sandi yang dimasukkan tidak valid.',
            trans('auth.password')
        );
    }

    /**
     * Memastikan password pengguna selalu di-hash menggunakan algoritma aman (Bcrypt / Argon2id).
     */
    public function test_password_pengguna_di_hash_dengan_aman(): void
    {
        $user = User::create([
            'username' => 'dr_andi',
            'name' => 'dr. Andi Kurniawan',
            'email' => 'andi@indosehat2003.com',
            'password' => Hash::make('KlinikSehat#2026'),
            'peran' => 'staf',
            'aktif' => true,
        ]);

        $this->assertTrue(Hash::check('KlinikSehat#2026', $user->password));
        $this->assertFalse(Hash::check('password_salah', $user->password));
        $this->assertNotSame('KlinikSehat#2026', $user->password);
    }

    /**
     * Memastikan UserSeeder dan McuSeeder tidak menimpa data kredensial di lingkungan produksi.
     */
    public function test_seeder_tidak_menulis_ulang_kredensial_dan_data_di_produksi(): void
    {
        App::detectEnvironment(fn () => 'production');

        $userSeeder = new UserSeeder();
        $userSeeder->run();

        $this->assertDatabaseMissing('users', [
            'username' => 'wira',
        ]);
        $this->assertDatabaseMissing('users', [
            'username' => 'admin',
        ]);
        $this->assertDatabaseMissing('users', [
            'username' => 'staff',
        ]);

        $mcuSeeder = new McuSeeder();
        $mcuSeeder->run();

        $this->assertDatabaseMissing('pasien', [
            'no_ktp' => '3275012345670001',
        ]);
    }

    /**
     * Memastikan notifikasi email undangan pengguna dan reset password menggunakan format konsisten tanpa tanda tangan unit kaku.
     */
    public function test_format_surel_notifikasi_menggunakan_salutation_resmi_klinik(): void
    {
        $user = User::create([
            'username' => 'petugas_baru',
            'name' => 'Petugas Administrasi',
            'email' => 'petugas@indosehat2003.com',
            'password' => Hash::make('rahasia123'),
            'peran' => 'staf',
            'aktif' => true,
        ]);

        // 1. Uji Notifikasi Undangan Pengguna
        $undangan = new UndanganPenggunaNotification('token-undangan-123');
        $mailUndangan = $undangan->toMail($user);

        $this->assertSame('Undangan Aktivasi Akun - Klinik Indosehat 2003', $mailUndangan->subject);
        $this->assertStringContainsString('Salam hangat,', $mailUndangan->salutation);
        $this->assertStringContainsString('Klinik Indosehat 2003', $mailUndangan->salutation);
        $this->assertStringNotContainsString('Tim IT & Keamanan Sistem', $mailUndangan->salutation);

        // 2. Uji Notifikasi Reset Password
        $reset = new ResetPasswordNotification('token-reset-456');
        $mailReset = $reset->toMail($user);

        $this->assertSame('Permintaan Pemulihan Kata Sandi - Klinik Indosehat 2003', $mailReset->subject);
        $this->assertStringContainsString('Salam hangat,', $mailReset->salutation);
        $this->assertStringContainsString('Klinik Indosehat 2003', $mailReset->salutation);
        $this->assertStringNotContainsString('Tim IT & Keamanan Sistem', $mailReset->salutation);
    }
}
