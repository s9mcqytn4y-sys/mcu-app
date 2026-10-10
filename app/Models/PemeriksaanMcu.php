<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemeriksaanMcu extends Model
{
    use HasFactory;

    /**
     * Nama tabel database terkait.
     */
    protected $table = 'pemeriksaan_mcu';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'pasien_id',
        'nomor_mcu',
        'tanggal_periksa',
        'berlaku_sampai',
        'nama_perusahaan',
        'status_kelayakan',
        'wilayah_operasi',
        'departemen',
        'jenis_kapal',
        'catatan',
        'token_verifikasi',
        'dibuat_oleh',
    ];

    /**
     * Tipe data casts untuk atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_periksa' => 'date',
            'berlaku_sampai' => 'date',
        ];
    }

    /**
     * Relasi ke data pasien terkait.
     *
     * @return BelongsTo<Pasien, $this>
     */
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    /**
     * Relasi ke berkas lampiran MCU.
     *
     * @return HasMany<LampiranMcu, $this>
     */
    public function lampiranMcu(): HasMany
    {
        return $this->hasMany(LampiranMcu::class, 'pemeriksaan_mcu_id');
    }

    /**
     * Relasi ke staf atau dokter pembuat catatan pemeriksaan ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
