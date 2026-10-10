<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class McuSeeder extends Seeder
{
    /**
     * Jalankan seeder contoh data pemeriksaan MCU.
     */
    public function run(): void
    {
        $pembuat = User::where('username', 'wira')->first() ?? User::first();

        // Contoh Pasien 1
        $pasien1 = Pasien::updateOrCreate(
            ['no_ktp' => '3275012345670001'],
            [
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1988-05-14',
                'no_paspor' => 'B1234567',
            ]
        );

        PemeriksaanMcu::updateOrCreate(
            ['nomor_mcu' => 'MCU-2026-0001'],
            [
                'pasien_id' => $pasien1->id,
                'tanggal_periksa' => '2026-08-01',
                'berlaku_sampai' => '2027-08-01',
                'nama_perusahaan' => 'PT Pelayaran Samudera Jaya',
                'status_kelayakan' => 'Fit for Sea Duty',
                'wilayah_operasi' => 'Internasional',
                'departemen' => 'Deck',
                'jenis_kapal' => 'Container Vessel',
                'catatan' => 'Kondisi fisik prima, visus normal dengan kacamata.',
                'token_verifikasi' => 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11',
                'dibuat_oleh' => $pembuat?->id,
            ]
        );

        // Contoh Pasien 2
        $pasien2 = Pasien::updateOrCreate(
            ['no_ktp' => '3171098765430002'],
            [
                'nama' => 'Siti Rahmawati',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '1992-11-20',
                'no_paspor' => null,
            ]
        );

        PemeriksaanMcu::updateOrCreate(
            ['nomor_mcu' => 'MCU-2026-0002'],
            [
                'pasien_id' => $pasien2->id,
                'tanggal_periksa' => '2026-08-05',
                'berlaku_sampai' => '2027-08-05',
                'nama_perusahaan' => 'PT Maritim Nusantara',
                'status_kelayakan' => 'Fit for Duty with Restriction',
                'wilayah_operasi' => 'Domestik',
                'departemen' => 'Engine',
                'jenis_kapal' => 'Bulk Carrier',
                'catatan' => 'Disarankan kontrol rutin tekanan darah.',
                'token_verifikasi' => 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22',
                'dibuat_oleh' => $pembuat?->id,
            ]
        );
    }
}
