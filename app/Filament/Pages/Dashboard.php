<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Resources\LogAuditResource;
use App\Filament\Resources\PasienResource;
use App\Filament\Resources\PemeriksaanMcuResource;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use Filament\Pages\Dashboard as FilamentDashboard;
use Illuminate\Database\Eloquent\Collection;

class Dashboard extends FilamentDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Ringkasan Operasional';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return 'Ringkasan Operasional MCU';
    }

    public function getSubheading(): ?string
    {
        return 'Klinik Indosehat 2003 · Pemantauan sertifikasi kelayakan kerja dan arsip rekam medis maritim.';
    }

    /**
     * Hitung total pemeriksaan yang diterbitkan pada hari ini.
     */
    public function getPemeriksaanHariIniCount(): int
    {
        return PemeriksaanMcu::whereDate('tanggal_periksa', now()->toDateString())->count();
    }

    /**
     * Hitung total pasien yang terdaftar di sistem.
     */
    public function getTotalPasienCount(): int
    {
        return Pasien::count();
    }

    /**
     * Hitung total keseluruhan sertifikat MCU yang pernah diterbitkan.
     */
    public function getTotalMcuCount(): int
    {
        return PemeriksaanMcu::count();
    }

    /**
     * Dapatkan rekapitulasi status kelayakan (FIT vs UNFIT).
     *
     * @return array{fit: int, unfit: int, fit_persen: int}
     */
    public function getStatistikKelayakan(): array
    {
        $total = PemeriksaanMcu::count();
        $fit = PemeriksaanMcu::where('status_kelayakan', 'FIT')->count();
        $unfit = PemeriksaanMcu::where('status_kelayakan', 'UNFIT')->count();

        $fitPersen = $total > 0 ? (int) round(($fit / $total) * 100) : 0;

        return [
            'fit' => $fit,
            'unfit' => $unfit,
            'fit_persen' => $fitPersen,
        ];
    }

    /**
     * Ambil 8 data rekam pemeriksaan medis terbaru beserta relasi pasiennya.
     *
     * @return Collection<int, PemeriksaanMcu>
     */
    public function getPemeriksaanTerbaru(): Collection
    {
        return PemeriksaanMcu::with('pasien')
            ->latest('id')
            ->take(8)
            ->get();
    }


    public function getUrlPemeriksaanBaru(): string
    {
        return PemeriksaanMcuResource::getUrl('create');
    }

    public function getUrlDaftarPemeriksaan(): string
    {
        return PemeriksaanMcuResource::getUrl('index');
    }

    public function getUrlDaftarPasien(): string
    {
        return PasienResource::getUrl('index');
    }

    public function getUrlRegistrasiPasien(): string
    {
        return PasienResource::getUrl('create');
    }

    public function getUrlLogAudit(): ?string
    {
        $user = auth()->user();
        if ($user instanceof \App\Models\User && $user->apakahAdministrator()) {
            return LogAuditResource::getUrl('index');
        }

        return null;
    }
}
