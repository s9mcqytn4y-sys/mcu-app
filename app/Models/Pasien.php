<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    /**
     * Nama tabel database terkait.
     */
    protected $table = 'pasien';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'no_ktp',
        'no_paspor',
    ];

    /**
     * Tipe data casts untuk atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Relasi ke riwayat pemeriksaan MCU pasien.
     *
     * @return HasMany<PemeriksaanMcu, $this>
     */
    public function pemeriksaanMcu(): HasMany
    {
        return $this->hasMany(PemeriksaanMcu::class, 'pasien_id');
    }
}
