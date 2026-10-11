<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Mcu\GenerateNomorMcuAction;
use App\Actions\Mcu\GenerateQrCodeAction;
use App\Models\LampiranMcu;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class McuWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan penerbitan nomor MCU mengalokasikan nomor urut atomik dengan format %04d/MCU-KIS/YYYY.
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
     * Memastikan relasi lampiran() dan accessor field alias pada PemeriksaanMcu dan LampiranMcu berfungsi.
     */
    public function test_kontrak_data_relasi_dan_alias_pemeriksaan_mcu(): void
    {
        $pasien = Pasien::create([
            'nama' => 'Pasien Kontrak Alias',
            'jenis_kelamin' => 'perempuan',
            'tanggal_lahir' => '1998-03-20',
        ]);

        $mcu = PemeriksaanMcu::create([
            'token_verifikasi' => Str::uuid()->toString(),
            'pasien_id' => $pasien->id,
            'nomor_mcu' => '0002/MCU-KIS/2026',
            'tanggal_pemeriksaan' => '2026-10-10',
            'tanggal_kedaluwarsa' => '2027-10-10',
            'status_kelayakan' => 'FIT',
        ]);

        $this->assertSame('2026-10-10', $mcu->tanggal_periksa->format('Y-m-d'));
        $this->assertSame('2027-10-10', $mcu->berlaku_sampai->format('Y-m-d'));

        $this->assertSame('2026-10-10', $mcu->tanggal_pemeriksaan->format('Y-m-d'));
        $this->assertSame('2027-10-10', $mcu->tanggal_kedaluwarsa->format('Y-m-d'));

        $lampiran = $mcu->lampiran()->create([
            'nama_berkas' => 'scan-rontgen.pdf',
            'tipe_lampiran' => 'rontgen_thorax',
            'path_berkas' => 'lampiran/scan-rontgen.pdf',
            'tipe_mime' => 'application/pdf',
            'ukuran_berkas' => 1024,
        ]);

        $this->assertSame('rontgen_thorax', $lampiran->jenis_lampiran);
        $this->assertSame('rontgen_thorax', $lampiran->tipe_lampiran);

        $this->assertCount(1, $mcu->lampiran);
        $this->assertCount(1, $mcu->lampiranMcu);
    }

    /**
     * Memastikan generator QR Code menghasilkan berkas PNG resolusi tinggi dan format SVG vektor.
     */
    public function test_generator_qrcode_menghasilkan_png_dan_svg_resolusi_tinggi(): void
    {
        $action = new GenerateQrCodeAction();
        $sampleUrl = 'https://klinikindosehat2003.com/v/' . Str::uuid()->toString();

        $pngData = $action->generatePngString($sampleUrl, 1024);
        $this->assertNotEmpty($pngData);
        $this->assertStringStartsWith("\x89PNG", $pngData);

        $svgData = $action->generateSvgString($sampleUrl, 512);
        $this->assertNotEmpty($svgData);
        $this->assertStringContainsString('<svg', $svgData);
    }

    /**
     * Memastikan verifikasi dokumen MCU melalui rute publik /v/{token} menampilkan data secara aman.
     */
    public function test_portal_mcu_dengan_uuid_valid_dan_invalid(): void
    {
        // 1. Token acak invalid mengembalikan 404
        $fakeToken = Str::uuid()->toString();
        $this->get('/v/' . $fakeToken)->assertStatus(404);

        // 2. Token valid menampilkan data pasien dan status MCU
        $pasien = Pasien::create([
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'laki-laki',
            'tanggal_lahir' => '1988-12-12',
            'no_ktp' => '3171098765430009',
        ]);

        $tokenValid = Str::uuid()->toString();
        PemeriksaanMcu::create([
            'token_verifikasi' => $tokenValid,
            'pasien_id' => $pasien->id,
            'nomor_mcu' => '0015/MCU-KIS/2026',
            'tanggal_periksa' => '2026-10-10',
            'berlaku_sampai' => '2027-10-10',
            'nama_perusahaan' => 'PT Maritim Prima Sejahtera',
            'status_kelayakan' => 'FIT',
        ]);

        $response = $this->get('/v/' . $tokenValid);
        $response->assertStatus(200);
        $response->assertSee('BUDI SANTOSO');
        $response->assertSee('Klinik Indosehat 2003');
        $response->assertSee('FIT');
    }

    /**
     * Memastikan lampiran medis terisolasi di bawah prinsip Zero Trust (UU PDP).
     */
    public function test_akses_lampiran_medis_publik_dengan_token_valid(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('lampiran/hasil-mcu.pdf', '%PDF-1.4 dummy content');

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
            'status_kelayakan' => 'FIT',
        ]);

        $mcu->lampiran()->create([
            'nama_berkas' => 'hasil-mcu.pdf',
            'jenis_lampiran' => 'rontgen_thorax',
            'path_berkas' => 'lampiran/hasil-mcu.pdf',
            'tipe_mime' => 'application/pdf',
            'ukuran_berkas' => 2048,
        ]);

        // Pengunjung anonim/publik DILARANG mengakses berkas fisik lampiran medis (Zero Trust)
        $this->get('/v/' . $uuid . '/file/0/view')->assertStatus(403);
        $this->get('/v/' . $uuid . '/file/0/download')->assertStatus(403);

        // Petugas yang terautentikasi dapat melihat dan mengunduh berkas fisik lampiran
        $user = User::factory()->create();

        $viewResponse = $this->actingAs($user)->get('/v/' . $uuid . '/file/0/view');
        $viewResponse->assertStatus(200);
        $viewResponse->assertHeader('X-Content-Type-Options', 'nosniff');

        $downloadResponse = $this->actingAs($user)->get('/v/' . $uuid . '/file/0/download');
        $downloadResponse->assertStatus(200);
        $downloadResponse->assertHeader('X-Content-Type-Options', 'nosniff');

        // Audit log tercatat
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
    }
}
