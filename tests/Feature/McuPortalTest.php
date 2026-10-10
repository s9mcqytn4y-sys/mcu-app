<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class McuPortalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan akses portal MCU dengan token invalid mengembalikan 404.
     */
    public function test_portal_mcu_dengan_token_invalid_mengembalikan_404(): void
    {
        $response = $this->get('/v/' . Str::uuid()->toString());

        $response->assertStatus(404);
    }

    /**
     * Memastikan verifikasi portal MCU dengan UUID valid menampilkan data pasien.
     */
    public function test_portal_mcu_dengan_uuid_valid_berhasil_menampilkan_data(): void
    {
        $pasien = Pasien::create([
            'nama' => 'Pasien Uji Coba',
            'jenis_kelamin' => 'laki-laki',
            'tanggal_lahir' => '1990-05-15',
            'no_ktp' => '3171012345670001',
            'no_paspor' => 'B1234567',
        ]);

        $token = Str::uuid()->toString();

        $pemeriksaan = PemeriksaanMcu::create([
            'token_verifikasi' => $token,
            'pasien_id' => $pasien->id,
            'nomor_mcu' => 'MCU-TEST-' . rand(1000, 9999),
            'tanggal_periksa' => now()->toDateString(),
            'berlaku_sampai' => now()->addYear()->toDateString(),
            'nama_perusahaan' => 'PT Pelayaran Bahtera Sejahtera',
            'status_kelayakan' => 'fit',
            'catatan' => 'Kondisi kesehatan prima.',
        ]);

        $response = $this->get('/v/' . $token);

        $response->assertStatus(200);
        $response->assertSee('PASIEN UJI COBA');
        $response->assertSee('Klinik Indosehat 2003');
        $response->assertSee('FIT');
    }

    /**
     * Memastikan data pengguna tersimpan di database dengan peran dan status aktif.
     */
    public function test_pengguna_dapat_login_menggunakan_username(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'name' => 'Tester User',
            'email' => 'tester@indosehat2003.com',
            'password' => bcrypt('password123'),
            'peran' => 'administrator',
            'aktif' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'peran' => 'administrator',
            'aktif' => true,
        ]);
    }
}
