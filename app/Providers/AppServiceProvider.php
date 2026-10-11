<?php

namespace App\Providers;

use App\Models\LampiranMcu;
use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use App\Policies\LampiranMcuPolicy;
use App\Policies\PemeriksaanMcuPolicy;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::policy(PemeriksaanMcu::class, PemeriksaanMcuPolicy::class);
        Gate::policy(LampiranMcu::class, LampiranMcuPolicy::class);

        // Kebijakan Kekuatan Kata Sandi Global (Minimal 12 Karakter + Huruf & Angka)
        Password::defaults(function () {
            $rule = Password::min(12)->letters()->numbers();

            return app()->isProduction() ? $rule->uncompromised() : $rule;
        });

        // Audit Log: Pencatatan otomatis saat pengguna keluar (logout) secara manual
        Event::listen(
            Logout::class,
            function (Logout $event): void {
                if ($event->user instanceof User) {
                    LogAudit::catat(
                        tindakan: 'keluar_sistem',
                        model: $event->user,
                        rincian: [
                            'keterangan' => 'Pengguna '.($event->user->nama ?? $event->user->username).' keluar dari sistem secara manual.',
                            'tipe_keluar' => 'manual',
                        ],
                        idPengguna: $event->user->id
                    );
                }
            }
        );

        // Lifecycle Aktivasi & Reset Password: Otomatis aktifkan akun, verifikasi email, dan catat audit
        Event::listen(
            PasswordReset::class,
            function (PasswordReset $event): void {
                $user = $event->user;
                if ($user instanceof User) {
                    $wasInactive = ! (bool) $user->aktif;

                    $user->forceFill([
                        'aktif' => true,
                        'email_verified_at' => $user->email_verified_at ?? now(),
                        'token_undangan' => null,
                    ])->saveQuietly();

                    LogAudit::catat(
                        tindakan: $wasInactive ? 'aktivasi_akun_sukses' : 'reset_kata_sandi_sukses',
                        model: $user,
                        rincian: [
                            'keterangan' => $wasInactive
                                ? 'Pengguna '.($user->name ?? $user->username).' berhasil mengaktifkan akun dan menyetel kata sandi baru.'
                                : 'Pengguna '.($user->name ?? $user->username).' berhasil mengatur ulang kata sandi.',
                            'email' => $user->email,
                            'username' => $user->username,
                        ],
                        idPengguna: $user->id
                    );
                }
            }
        );
    }
}
