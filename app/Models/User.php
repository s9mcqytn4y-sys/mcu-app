<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'peran',
        'aktif',
        'token_undangan',
        'undangan_dikirim_pada',
        'waktu_login_terakhir',
        'ip_login_terakhir',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token_undangan',
    ];

    /**
     * Definisi casting tipe atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'undangan_dikirim_pada' => 'datetime',
            'waktu_login_terakhir' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Booted model hooks untuk mencatat perubahan pengguna ke log audit.
     */
    protected static function booted(): void
    {
        static::created(function (User $user): void {
            LogAudit::catat(
                tindakan: 'buat_pengguna',
                model: $user,
                rincian: [
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'peran' => $user->peran,
                    'aktif' => (bool) $user->aktif,
                ]
            );
        });

        static::updated(function (User $user): void {
            $perubahan = [];
            if ($user->wasChanged('peran')) {
                $perubahan['peran'] = [
                    'dari' => $user->getOriginal('peran'),
                    'menjadi' => $user->peran,
                ];
            }
            if ($user->wasChanged('aktif')) {
                $perubahan['aktif'] = [
                    'dari' => (bool) $user->getOriginal('aktif'),
                    'menjadi' => (bool) $user->aktif,
                ];
            }
            if (!empty($perubahan)) {
                LogAudit::catat(
                    tindakan: 'MODIFIKASI_AKSES_PENGGUNA',
                    model: $user,
                    rincian: [
                        'username' => $user->username,
                        'perubahan' => $perubahan,
                    ]
                );
            }
        });

        static::deleted(function (User $user): void {
            LogAudit::catat(
                tindakan: 'hapus_pengguna',
                model: $user,
                rincian: [
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'peran' => $user->peran,
                ]
            );
        });
    }

    /**
     * Verifikasi hak akses ke Filament Panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->aktif && in_array($this->peran, ['administrator', 'staf'], true);
    }

    /**
     * Memeriksa apakah pengguna memiliki peran administrator.
     */
    public function isAdministrator(): bool
    {
        return $this->apakahAdministrator();
    }

    /**
     * Memeriksa apakah pengguna memiliki peran administrator (konvensi Bahasa Indonesia).
     */
    public function apakahAdministrator(): bool
    {
        return $this->peran === 'administrator';
    }

    /**
     * Memeriksa apakah pengguna memiliki peran staf.
     */
    public function isStaf(): bool
    {
        return $this->apakahStaf();
    }

    /**
     * Memeriksa apakah pengguna memiliki peran staf (konvensi Bahasa Indonesia).
     */
    public function apakahStaf(): bool
    {
        return $this->peran === 'staf';
    }

    /**
     * Relasi ke data pemeriksaan MCU yang dibuat oleh pengguna ini.
     */
    public function pemeriksaanMcu(): HasMany
    {
        return $this->hasMany(PemeriksaanMcu::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke log audit aktivitas pengguna ini.
     */
    public function logAudit(): HasMany
    {
        return $this->hasMany(LogAudit::class, 'id_pengguna');
    }

    /**
     * Mengirim notifikasi pemulihan kata sandi dengan format resmi Klinik Indosehat 2003.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * Memeriksa apakah pengguna sedang online (tercatat aktif dalam 5 menit terakhir via Cache).
     */
    public function apakahOnline(): bool
    {
        return \Illuminate\Support\Facades\Cache::has('user-online-' . $this->id);
    }
}

