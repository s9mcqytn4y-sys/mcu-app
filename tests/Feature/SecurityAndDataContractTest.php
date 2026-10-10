<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Mcu\GenerateNomorMcuAction;
use App\Models\LampiranMcu;
use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityAndDataContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan route /setup-hosting ditutup dan mengembalikan 404.
     */
    public function test_route_setup_hosting_telah_ditutup(): void
    {
        $response = $this->get('/setup-hosting');

        $response->assertStatus(404);
    }

    /**
     * Memastikan UserSeeder dan McuSeeder tidak mengeksekusi operasi berbahaya di lingkungan produksi.
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

        $mcuSeeder = new \Database\Seeders\McuSeeder();
        $mcuSeeder->run();

        $this->assertDatabaseMissing('pasien', [
            'no_ktp' => '3275012345670001',
        ]);
    }

    /**
     * Memastikan akses lampiran dokumen medis publik dengan token verifikasi valid dan aman.
     */
    public function test_akses_lampiran_medis_publik_dengan_token_valid(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Storage::disk('local')->put('lampiran/hasil-mcu.pdf', '%PDF-1.4 dummy content');

        $pasien = Pasien::create([
            'nama' => 'Pasien Anonim',
            'jenis_kelamin' => 'laki-laki',
            'tanggal_lahir' => '1990-01-01',
        ]);

        $uuid = Str::uuid()->toString();
        $mcu = PemeriksaanMcu::create([
            'token_verifikasi' => $uuid,
            'pasien_id' => $pasien->id,
            'nomor_mcu' => '0099/MCU-KIS/2026',
            'tanggal_periksa' => '2026-10-10',
            'berlaku_sampai' => '2027-10-10',
            'status_kelayakan' => 'fit',
        ]);

        $mcu->lampiran()->create([
            'nama_berkas' => 'hasil-mcu.pdf',
            'jenis_lampiran' => 'rontgen_thorax',
            'path_berkas' => 'lampiran/hasil-mcu.pdf',
            'tipe_mime' => 'application/pdf',
            'ukuran_berkas' => 2048,
        ]);

        // Pengunjung dengan token valid dapat melihat berkas (inline)
        $viewResponse = $this->get('/v/' . $uuid . '/file/0/view');
        $viewResponse->assertStatus(200);
        $viewResponse->assertHeader('X-Content-Type-Options', 'nosniff');

        // Pengunjung dengan token valid dapat mengunduh berkas
        $downloadResponse = $this->get('/v/' . $uuid . '/file/0/download');
        $downloadResponse->assertStatus(200);
        $downloadResponse->assertHeader('X-Content-Type-Options', 'nosniff');

        // Memastikan aksi akses lampiran tercatat di audit log
        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'LIHAT_LAMPIRAN_MCU',
            'tipe_model' => PemeriksaanMcu::class,
            'id_model' => $mcu->id,
        ]);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'UNDUH_LAMPIRAN_MCU',
            'tipe_model' => PemeriksaanMcu::class,
            'id_model' => $mcu->id,
        ]);

        // Token palsu atau index lampiran tidak ada wajib mengembalikan 404
        $fakeUuid = Str::uuid()->toString();
        $this->get('/v/' . $fakeUuid . '/file/0/view')->assertStatus(404);
        $this->get('/v/' . $uuid . '/file/99/view')->assertStatus(404);
    }

    /**
     * Memastikan CreatePemeriksaanMcu mengalokasikan nomor_mcu resmi saat form disimpan.
     */
    public function test_penerbitan_mcu_mengalokasikan_nomor_mcu_resmi(): void
    {
        $pasien = Pasien::create([
            'nama' => 'Pasien Uji Sekuens',
            'jenis_kelamin' => 'laki-laki',
            'tanggal_lahir' => '1992-05-10',
        ]);

        $tahunIni = (int) date('Y');
        $expectedNomor = sprintf('%04d/MCU-KIS/%d', 1, $tahunIni);

        $action = new GenerateNomorMcuAction();
        $nomorBaru = $action->generate($tahunIni);

        $this->assertSame($expectedNomor, $nomorBaru);
    }

    /**
     * Memastikan method apakahAdministrator dan apakahStaf pada model User bekerja konsisten.
     */
    public function test_rbac_user_helper_methods(): void
    {
        $admin = User::create([
            'username' => 'admin_mcu',
            'name' => 'Admin Utama',
            'email' => 'admin@indosehat2003.com',
            'password' => bcrypt('secret123'),
            'peran' => 'administrator',
            'aktif' => true,
        ]);

        $staf = User::create([
            'username' => 'staf_mcu',
            'name' => 'Staf Operasional',
            'email' => 'staf@indosehat2003.com',
            'password' => bcrypt('secret123'),
            'peran' => 'staf',
            'aktif' => true,
        ]);

        $this->assertTrue($admin->apakahAdministrator());
        $this->assertTrue($admin->isAdministrator());
        $this->assertFalse($admin->apakahStaf());

        $this->assertTrue($staf->apakahStaf());
        $this->assertFalse($staf->apakahAdministrator());
        $this->assertFalse($staf->isAdministrator());
    }

    /**
     * Memastikan GenerateNomorMcuAction menghasilkan nomor urut atomik dengan format %04d/MCU-KIS/YYYY.
     */
    public function test_generate_nomor_mcu_action_atomik_dan_berurutan(): void
    {
        $action = new GenerateNomorMcuAction();
        $tahun = 2026;

        $nomor1 = $action->generate($tahun);
        $nomor2 = $action->generate($tahun);
        $nomor3 = $action->generate($tahun);

        $this->assertSame('0001/MCU-KIS/2026', $nomor1);
        $this->assertSame('0002/MCU-KIS/2026', $nomor2);
        $this->assertSame('0003/MCU-KIS/2026', $nomor3);

        $this->assertDatabaseHas('nomor_mcu_sekuens', [
            'tahun' => 2026,
            'sekuens_terakhir' => 3,
        ]);
    }

    /**
     * Memastikan pemanggilan LogAudit::rekam() dengan named arguments PHP 8 tidak melempar error.
     */
    public function test_log_audit_rekam_dan_catat_kompatibel_dengan_semua_kontrak(): void
    {
        $pasien = Pasien::create([
            'nama' => 'Pasien Audit Test',
            'jenis_kelamin' => 'laki-laki',
            'tanggal_lahir' => '1995-01-01',
        ]);

        $pemeriksaan = PemeriksaanMcu::create([
            'token_verifikasi' => Str::uuid()->toString(),
            'pasien_id' => $pasien->id,
            'nomor_mcu' => '0001/MCU-KIS/2026',
            'tanggal_periksa' => '2026-10-10',
            'berlaku_sampai' => '2027-10-10',
            'status_kelayakan' => 'fit',
        ]);

        // Uji pemanggilan gaya Dashboard (aksi: dan model:)
        LogAudit::rekam(aksi: 'simpan_pemeriksaan', model: $pemeriksaan);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'simpan_pemeriksaan',
            'tipe_model' => PemeriksaanMcu::class,
            'id_model' => $pemeriksaan->id,
        ]);

        // Uji pemanggilan gaya canonical (tindakan: dan objek:)
        LogAudit::rekam(tindakan: 'verifikasi_qr', tipeModel: Pasien::class, idModel: $pasien->id);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'verifikasi_qr',
            'tipe_model' => Pasien::class,
            'id_model' => $pasien->id,
        ]);

        // Uji pemanggilan alias catat()
        LogAudit::catat('cetak_sertifikat', PemeriksaanMcu::class, $pemeriksaan->id, ['detail' => 'sukses']);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'cetak_sertifikat',
        ]);
    }

    /**
     * Memastikan relasi lampiran() dan accessor field alias pada PemeriksaanMcu dan LampiranMcu berfungsi.
     */
    public function test_kontrak_data_relasi_dan_alias_pemeriksaan_mcu(): void
    {
        $pasien = Pasien::create([
            'nama' => 'Pasien Kontrak Alias',
            'jenis_kelamin' => 'perempuan',
            'tanggal_lahir' => '1998-03-20',
        ]);

        // Menggunakan alias tanggal_pemeriksaan dan tanggal_kedaluwarsa saat create
        $mcu = PemeriksaanMcu::create([
            'token_verifikasi' => Str::uuid()->toString(),
            'pasien_id' => $pasien->id,
            'nomor_mcu' => '0002/MCU-KIS/2026',
            'tanggal_pemeriksaan' => '2026-10-10',
            'tanggal_kedaluwarsa' => '2027-10-10',
            'status_kelayakan' => 'fit',
        ]);

        // Kolom database asli harus terisi
        $this->assertSame('2026-10-10', $mcu->tanggal_periksa->format('Y-m-d'));
        $this->assertSame('2027-10-10', $mcu->berlaku_sampai->format('Y-m-d'));

        // Akses via alias harus mengembalikan nilai yang sama
        $this->assertSame('2026-10-10', $mcu->tanggal_pemeriksaan->format('Y-m-d'));
        $this->assertSame('2027-10-10', $mcu->tanggal_kedaluwarsa->format('Y-m-d'));

        // Tambah lampiran menggunakan alias tipe_lampiran
        $lampiran = $mcu->lampiran()->create([
            'nama_berkas' => 'scan-rontgen.pdf',
            'tipe_lampiran' => 'rontgen_thorax',
            'path_berkas' => 'lampiran/scan-rontgen.pdf',
            'tipe_mime' => 'application/pdf',
            'ukuran_berkas' => 1024,
        ]);

        // Kolom database asli jenis_lampiran harus terisi
        $this->assertSame('rontgen_thorax', $lampiran->jenis_lampiran);
        $this->assertSame('rontgen_thorax', $lampiran->tipe_lampiran);

        // Relasi lampiran() maupun lampiranMcu() harus mengembalikan relasi yang sama
        $this->assertCount(1, $mcu->lampiran);
        $this->assertCount(1, $mcu->lampiranMcu);
    }

    /**
     * Memastikan GenerateQrCodeAction dapat menghasilkan keluaran PNG resolusi tinggi dan SVG vektor.
     */
    public function test_generator_qrcode_menghasilkan_png_dan_svg_resolusi_tinggi(): void
    {
        $action = new \App\Actions\Mcu\GenerateQrCodeAction();
        $sampleUrl = 'https://klinikindosehat2003.com/v/' . Str::uuid()->toString();

        $pngData = $action->generatePngString($sampleUrl, 1024);
        $this->assertNotEmpty($pngData);
        // Header PNG biner (\x89PNG)
        $this->assertStringStartsWith("\x89PNG", $pngData);

        $svgData = $action->generateSvgString($sampleUrl, 512);
        $this->assertNotEmpty($svgData);
        $this->assertStringContainsString('<svg', $svgData);
    }

    /**
     * Memastikan pesan autentikasi gagal telah terlokalisasi ke Bahasa Indonesia.
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
}
