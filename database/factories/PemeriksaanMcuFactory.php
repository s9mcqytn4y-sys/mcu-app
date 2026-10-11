<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PemeriksaanMcu>
 */
class PemeriksaanMcuFactory extends Factory
{
    protected $model = PemeriksaanMcu::class;

    public function definition(): array
    {
        return [
            'pasien_id' => Pasien::factory(),
            'nomor_mcu' => 'MCU-' . fake()->unique()->numerify('######'),
            'tanggal_periksa' => fake()->date('Y-m-d', 'now'),
            'berlaku_sampai' => fake()->date('Y-m-d', '+2 years'),
            'nama_perusahaan' => fake()->company(),
            'status_kelayakan' => 'FIT',
            'catatan' => fake()->sentence(),
            'token_verifikasi' => (string) Str::uuid(),
            'dibuat_oleh' => User::factory(),
        ];
    }
}
