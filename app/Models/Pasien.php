<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\LogAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    /**
     * Booted model hooks untuk mencatat riwayat audit penambahan, perubahan, dan penghapusan pasien.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::created(function (Pasien $pasien): void {
            LogAudit::catat(
                tindakan: 'buat_pasien',
                model: $pasien,
                rincian: [
                    'nama' => $pasien->nama,
                    'no_ktp' => $pasien->no_ktp,
                    'jenis_kelamin' => $pasien->jenis_kelamin,
                ]
            );
        });

        static::updated(function (Pasien $pasien): void {
            $perubahan = [];
            foreach (['nama', 'jenis_kelamin', 'tanggal_lahir', 'no_ktp', 'no_paspor'] as $field) {
                if ($pasien->wasChanged($field)) {
                    $perubahan[$field] = [
                        'dari' => $pasien->getOriginal($field),
                        'menjadi' => $pasien->{$field},
                    ];
                }
            }

            if (! empty($perubahan)) {
                LogAudit::catat(
                    tindakan: 'ubah_pasien',
                    model: $pasien,
                    rincian: [
                        'nama' => $pasien->nama,
                        'no_ktp' => $pasien->no_ktp,
                        'perubahan' => $perubahan,
                    ]
                );
            }
        });

        static::deleted(function (Pasien $pasien): void {
            LogAudit::catat(
                tindakan: 'hapus_pasien',
                model: $pasien,
                rincian: [
                    'nama' => $pasien->nama,
                    'no_ktp' => $pasien->no_ktp,
                ]
            );
        });
    }

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
