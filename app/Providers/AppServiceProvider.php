<?php

namespace App\Providers;

use App\Models\LampiranMcu;
use App\Models\PemeriksaanMcu;
use App\Policies\LampiranMcuPolicy;
use App\Policies\PemeriksaanMcuPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(PemeriksaanMcu::class, PemeriksaanMcuPolicy::class);
        Gate::policy(LampiranMcu::class, LampiranMcuPolicy::class);

        // Audit Log: Pencatatan otomatis saat pengguna keluar (logout) secara manual
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function (\Illuminate\Auth\Events\Logout $event): void {
                if ($event->user instanceof \App\Models\User) {
                    \App\Models\LogAudit::catat(
                        tindakan: 'keluar_sistem',
                        model: $event->user,
                        rincian: [
                            'keterangan' => 'Pengguna ' . ($event->user->nama ?? $event->user->username) . ' keluar dari sistem secara manual.',
                            'tipe_keluar' => 'manual',
                        ],
                        idPengguna: $event->user->id
                    );
                }
            }
        );
    }
}

