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
    }
}

