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
