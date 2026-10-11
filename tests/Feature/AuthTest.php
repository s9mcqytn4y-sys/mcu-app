<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\UndanganPenggunaNotification;
use Database\Seeders\McuSeeder;
use Database\Seeders\UserSeeder;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        $userSeeder = new UserSeeder;
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

        $mcuSeeder = new McuSeeder;
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

    /**
     * Memastikan pengguna belum aktif (aktif = 0) dengan token_undangan dapat mengakses alur reset kata sandi,
     * tetapi ditolak saat mencoba login/mengakses panel secara langsung tanpa token.
     */
    public function test_pengguna_belum_aktif_dengan_token_undangan_diizinkan_pada_alur_reset_password_tetapi_ditolak_pada_panel_biasa(): void
    {
        $panel = Filament::getCurrentPanel() ?? Filament::getPanel('admin');

        $inactiveUser = User::create([
            'username' => 'staf_baru',
            'name' => 'Staf Baru Medis',
            'email' => 'stafbaru@indosehat2003.com',
            'password' => Hash::make('KlinikSehat#2026'),
            'peran' => 'staf',
            'aktif' => false,
            'token_undangan' => Str::random(60),
        ]);

        // 1. Pada konteks biasa (bukan reset password), canAccessPanel harus FALSE
        $this->assertFalse($inactiveUser->canAccessPanel($panel));

        // 2. Pada konteks rute password-reset, canAccessPanel harus TRUE
        $this->get('/admin/password-reset/request');
        $this->assertTrue($inactiveUser->canAccessPanel($panel));
    }

    /**
     * Memastikan event PasswordReset mengaktifkan pengguna, mengisi email_verified_at,
     * membersihkan token_undangan, dan mencatat log audit.
     */
    public function test_password_reset_event_mengaktifkan_pengguna_dan_mencatat_audit(): void
    {
        $inactiveUser = User::create([
            'username' => 'staf_aktivasi',
            'name' => 'Staf Aktivasi',
            'email' => 'aktivasi@indosehat2003.com',
            'password' => Hash::make('KlinikSehat#2026'),
            'peran' => 'staf',
            'aktif' => false,
            'token_undangan' => 'token-undangan-awal',
            'email_verified_at' => null,
        ]);

        event(new PasswordReset($inactiveUser));

        $inactiveUser->refresh();

        $this->assertTrue((bool) $inactiveUser->aktif);
        $this->assertNotNull($inactiveUser->email_verified_at);
        $this->assertNull($inactiveUser->token_undangan);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'aktivasi_akun_sukses',
            'id_pengguna' => $inactiveUser->id,
        ]);
    }
}
