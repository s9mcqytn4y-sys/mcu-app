<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Tests\TestCase;

class McuSecurityAndImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_staf_dilarang_mengubah_mcu_yang_telah_terbit(): void
    {
        $staf = User::factory()->create([
            'peran' => 'staf',
            'aktif' => true,
        ]);

        $mcuTerbit = PemeriksaanMcu::factory()->create([
            'status_kelayakan' => 'FIT',
        ]);

        $this->actingAs($staf);

        $bisaUpdate = Gate::allows('update', $mcuTerbit);
        $bisaDelete = Gate::allows('delete', $mcuTerbit);

        $this->assertFalse($bisaUpdate, 'Staf tidak boleh memiliki izin mengedit MCU terbit');
        $this->assertFalse($bisaDelete, 'Staf tidak boleh memiliki izin menghapus MCU terbit');
    }

    public function test_admin_diizinkan_mengubah_dan_menghapus_mcu_terbit(): void
    {
        $admin = User::factory()->create([
            'peran' => 'administrator',
            'aktif' => true,
        ]);

        $mcuTerbit = PemeriksaanMcu::factory()->create([
            'status_kelayakan' => 'FIT',
        ]);

        $this->actingAs($admin);

        $this->assertTrue(Gate::allows('update', $mcuTerbit));
        $this->assertTrue(Gate::allows('delete', $mcuTerbit));
    }

    public function test_staf_hanya_dapat_mengubah_mcu_berstatus_draf(): void
    {
        $staf = User::factory()->create([
            'peran' => 'staf',
            'aktif' => true,
        ]);

        $mcuDraf = PemeriksaanMcu::factory()->create([
            'status_kelayakan' => 'DRAF',
        ]);

        $this->actingAs($staf);

        $this->assertTrue(Gate::allows('update', $mcuDraf));
    }

    public function test_pemeriksaan_legacy_dapat_diverifikasi_publik_via_uuid_asli(): void
    {
        $legacyUuid = (string) Str::uuid();

        $pasien = Pasien::factory()->create([
            'nama' => 'Ahmad Pelaut',
            'no_ktp' => '3201012345678901',
        ]);

        $pemeriksaan = PemeriksaanMcu::factory()->create([
            'pasien_id' => $pasien->id,
            'nomor_mcu' => 'ARC-LEGACY001',
            'token_verifikasi' => $legacyUuid,
            'status_kelayakan' => 'FIT',
        ]);

        $response = $this->get('/v/' . $legacyUuid);

        $response->assertStatus(200);
        $response->assertSee('AHMAD PELAUT');
        $response->assertSee('3201012345678901');
        $response->assertSee('FIT');

        $this->assertDatabaseHas('pemeriksaan_mcu', [
            'token_verifikasi' => $legacyUuid,
            'nomor_mcu' => 'ARC-LEGACY001',
        ]);
    }

    public function test_mcu_berstatus_draf_tidak_dapat_diverifikasi_publik(): void
    {
        $draftUuid = (string) Str::uuid();

        $pasien = Pasien::factory()->create();

        PemeriksaanMcu::factory()->create([
            'pasien_id' => $pasien->id,
            'token_verifikasi' => $draftUuid,
            'status_kelayakan' => 'DRAF',
        ]);

        $response = $this->get('/v/' . $draftUuid);

        // Halaman verifikasi publik menolak sertifikat yang masih draf
        $response->assertStatus(404);
    }
}
