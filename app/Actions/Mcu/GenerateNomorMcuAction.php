<?php

declare(strict_types=1);

namespace App\Actions\Mcu;

use App\Models\PemeriksaanMcu;
use Illuminate\Support\Facades\DB;

class GenerateNomorMcuAction
{
    /**
     * Alias untuk execute() demi kompatibilitas pemanggilan.
     */
    public function generate(?int $tahun = null): string
    {
        return $this->execute($tahun);
    }

    /**
     * Tampilkan perkiraan nomor MCU berikutnya tanpa memutasi sekuens database.
     */
    public function generatePreview(?int $tahun = null): string
    {
        $tahunTarget = $tahun ?? (int) date('Y');

        $sekuens = DB::table('nomor_mcu_sekuens')
            ->where('tahun', $tahunTarget)
            ->first();

        if (! $sekuens) {
            $hitungEksisting = PemeriksaanMcu::whereYear('tanggal_periksa', $tahunTarget)
                ->orWhereYear('created_at', $tahunTarget)
                ->count();

            return sprintf('%04d/MCU-KIS/%d', $hitungEksisting + 1, $tahunTarget);
        }

        return sprintf('%04d/MCU-KIS/%d', ((int) $sekuens->sekuens_terakhir) + 1, $tahunTarget);
    }

    /**
     * Hasilkan nomor MCU berikutnya secara atomik dan bebas dari race condition.
     * Format: %04d/MCU-KIS/%s (contoh: 0001/MCU-KIS/2026)
     */
    public function execute(?int $tahun = null): string
    {
        $tahunTarget = $tahun ?? (int) date('Y');

        // Inisialisasi baris tahun secara aman (idempotent / aman dari duplicate-key race condition)
        $hitungEksisting = PemeriksaanMcu::whereYear('tanggal_periksa', $tahunTarget)
            ->orWhereYear('created_at', $tahunTarget)
            ->count();

        DB::table('nomor_mcu_sekuens')->insertOrIgnore([
            'tahun' => $tahunTarget,
            'sekuens_terakhir' => $hitungEksisting,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::transaction(function () use ($tahunTarget): string {
            /** @var object{tahun: int, sekuens_terakhir: int}|null $sekuens */
            $sekuens = DB::table('nomor_mcu_sekuens')
                ->where('tahun', $tahunTarget)
                ->lockForUpdate()
                ->first();

            $sekuensBaru = ((int) ($sekuens->sekuens_terakhir ?? 0)) + 1;

            DB::table('nomor_mcu_sekuens')
                ->where('tahun', $tahunTarget)
                ->update([
                    'sekuens_terakhir' => $sekuensBaru,
                    'updated_at' => now(),
                ]);

            return sprintf('%04d/MCU-KIS/%d', $sekuensBaru, $tahunTarget);
        });
    }
}
