<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\LogAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemeriksaanMcu extends Model
{
    use HasFactory;

    /**
     * Booted model hooks untuk mencatat audit pembuatan, pembaruan, dan penghapusan data pemeriksaan MCU.
     */
    protected static function booted(): void
    {
        static::created(function (PemeriksaanMcu $mcu): void {
            LogAudit::catat(
                tindakan: 'buat_mcu',
                model: $mcu,
                rincian: [
                    'nomor_mcu' => $mcu->nomor_mcu,
                    'pasien_id' => $mcu->pasien_id,
                    'nama_perusahaan' => $mcu->nama_perusahaan,
                    'status_kelayakan' => $mcu->status_kelayakan,
                    'tanggal_periksa' => $mcu->tanggal_periksa?->format('Y-m-d'),
                ]
            );
        });

        static::updated(function (PemeriksaanMcu $mcu): void {
            $perubahan = [];
            $kolomDipantau = [
                'status_kelayakan',
                'nomor_mcu',
                'nama_perusahaan',
                'berlaku_sampai',
                'tanggal_periksa',
                'wilayah_operasi',
                'departemen',
                'jenis_kapal',
                'catatan',
            ];

            foreach ($kolomDipantau as $kolom) {
                if ($mcu->wasChanged($kolom)) {
                    $lama = $mcu->getOriginal($kolom);
                    $baru = $mcu->getAttribute($kolom);
                    $perubahan[$kolom] = [
                        'lama' => $lama instanceof \Carbon\CarbonInterface ? $lama->format('Y-m-d') : $lama,
                        'baru' => $baru instanceof \Carbon\CarbonInterface ? $baru->format('Y-m-d') : $baru,
                    ];
                }
            }

            if (! empty($perubahan)) {
                LogAudit::catat(
                    tindakan: 'ubah_mcu',
                    model: $mcu,
                    rincian: [
                        'nomor_mcu' => $mcu->nomor_mcu,
                        'perubahan' => $perubahan,
                    ]
                );
            }
        });

        static::deleted(function (PemeriksaanMcu $mcu): void {
            LogAudit::catat(
                tindakan: 'hapus_mcu',
                model: $mcu,
                rincian: [
                    'nomor_mcu' => $mcu->nomor_mcu,
                    'nama_perusahaan' => $mcu->nama_perusahaan,
                    'status_kelayakan' => $mcu->status_kelayakan,
                ]
            );
        });
    }

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
        'tanggal_pemeriksaan',
        'tanggal_kedaluwarsa',
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
     * Accessor alias untuk tanggal_periksa (kompatibilitas form/viewer lama).
     */
    public function getTanggalPemeriksaanAttribute(): ?\Carbon\Carbon
    {
        return isset($this->attributes['tanggal_periksa']) && $this->attributes['tanggal_periksa'] !== null
            ? \Carbon\Carbon::parse($this->attributes['tanggal_periksa'])
            : null;
    }

    /**
     * Mutator alias untuk tanggal_pemeriksaan.
     */
    public function setTanggalPemeriksaanAttribute(mixed $value): void
    {
        $this->attributes['tanggal_periksa'] = $value;
    }

    /**
     * Accessor alias untuk berlaku_sampai (kompatibilitas form/viewer lama).
     */
    public function getTanggalKedaluwarsaAttribute(): ?\Carbon\Carbon
    {
        return isset($this->attributes['berlaku_sampai']) && $this->attributes['berlaku_sampai'] !== null
            ? \Carbon\Carbon::parse($this->attributes['berlaku_sampai'])
            : null;
    }

    /**
     * Mutator alias untuk tanggal_kedaluwarsa.
     */
    public function setTanggalKedaluwarsaAttribute(mixed $value): void
    {
        $this->attributes['berlaku_sampai'] = $value;
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
     * Alias relasi berkas lampiran untuk kompatibilitas form & resource Filament.
     *
     * @return HasMany<LampiranMcu, $this>
     */
    public function lampiran(): HasMany
    {
        return $this->lampiranMcu();
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
