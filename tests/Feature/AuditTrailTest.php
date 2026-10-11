<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan LogAudit::rekam() dan catat() kompatibel dengan variasi format parameter.
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
            'status_kelayakan' => 'FIT',
        ]);

        // 1. Pemanggilan gaya dashboard (aksi: dan model:)
        LogAudit::rekam(aksi: 'simpan_pemeriksaan', model: $pemeriksaan);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'simpan_pemeriksaan',
            'tipe_model' => PemeriksaanMcu::class,
            'id_model' => $pemeriksaan->id,
        ]);

        // 2. Pemanggilan gaya kanonikal (tindakan:, tipeModel:, idModel:)
        LogAudit::rekam(tindakan: 'verifikasi_qr', tipeModel: Pasien::class, idModel: $pasien->id);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'verifikasi_qr',
            'tipe_model' => Pasien::class,
            'id_model' => $pasien->id,
        ]);

        // 3. Pemanggilan alias catat()
        LogAudit::catat('cetak_sertifikat', PemeriksaanMcu::class, $pemeriksaan->id, ['status' => 'berhasil']);

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'cetak_sertifikat',
            'tipe_model' => PemeriksaanMcu::class,
            'id_model' => $pemeriksaan->id,
        ]);
    }

    /**
     * Memastikan audit log mencatat IP pengguna secara akurat dari request context.
     */
    public function test_log_audit_merekam_ip_klien_secara_akurat(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.195']);

        LogAudit::catat('akses_verifikasi_ip', Pasien::class, 1, ['ip_uji' => true], alamatIp: '203.0.113.195');

        $log = LogAudit::where('tindakan', 'akses_verifikasi_ip')->first();

        $this->assertNotNull($log);
        $this->assertSame('203.0.113.195', $log->alamat_ip);
    }

    /**
     * Memastikan data log audit tersimpan dengan user ID yang valid jika ada sesi login.
     */
    public function test_log_audit_mengaitkan_pengguna_saat_terautentikasi(): void
    {
        $petugas = User::factory()->create([
            'username' => 'petugas_audit',
            'peran' => 'staf',
        ]);

        $this->actingAs($petugas);

        LogAudit::rekam(tindakan: 'buka_data_pasien');

        $this->assertDatabaseHas('log_audit', [
            'tindakan' => 'buka_data_pasien',
            'id_pengguna' => $petugas->id,
        ]);
    }
}
