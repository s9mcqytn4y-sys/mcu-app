<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
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
            'waktu_login_terakhir' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
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
        return $this->peran === 'administrator';
    }

    /**
     * Memeriksa apakah pengguna memiliki peran staf.
     */
    public function isStaf(): bool
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
}
