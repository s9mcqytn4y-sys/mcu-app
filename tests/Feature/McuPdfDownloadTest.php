<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class McuPdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan unduh PDF dengan UUID yang valid berhasil dan mengembalikan PDF.
     */
    public function test_unduh_pdf_dengan_uuid_valid_berhasil(): void
    {
        $pasien = Pasien::create([
            'nama' => 'Joko Susanto',
            'jenis_kelamin' => 'laki-laki',
            'tanggal_lahir' => '1988-03-20',
            'no_ktp' => '3271012345670002',
            'no_paspor' => 'C9876543',
        ]);

        $token = Str::uuid()->toString();

        $pemeriksaan = PemeriksaanMcu::create([
            'token_verifikasi' => $token,
            'pasien_id' => $pasien->id,
            'nomor_mcu' => 'MCU-2026-0042',
            'tanggal_periksa' => '2026-04-10',
            'berlaku_sampai' => '2027-04-10',
            'nama_perusahaan' => 'PT Samudera Maritim Sentosa',
            'wilayah_operasi' => 'Internasional',
            'departemen' => 'Dek',
            'jenis_kapal' => 'Kapal Kargo',
            'status_kelayakan' => 'fit',
            'catatan' => 'Pemeriksaan fisik dan laboratorium lengkap tanpa catatan abnormal.',
        ]);

        $response = $this->get('/v/' . $token . '/pdf');

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('MCU-2026-0042', (string) $response->headers->get('content-disposition'));

        // Memastikan log audit tersimpan dengan benar
        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'UNDUH_PDF_HASIL_MCU',
            'tipe_model' => PemeriksaanMcu::class,
            'id_model' => (string) $pemeriksaan->id,
        ]);
    }

    /**
     * Memastikan unduh PDF dengan UUID invalid mengembalikan status 404.
     */
    public function test_unduh_pdf_dengan_uuid_invalid_mengembalikan_404(): void
    {
        $invalidToken = Str::uuid()->toString();

        $response = $this->get('/v/' . $invalidToken . '/pdf');

        $response->assertStatus(404);
    }
}
