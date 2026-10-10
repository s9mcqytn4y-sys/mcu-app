<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampiranMcu extends Model
{
    use HasFactory;

    /**
     * Nama tabel database terkait.
     */
    protected $table = 'lampiran_mcu';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'pemeriksaan_mcu_id',
        'path_berkas',
        'nama_berkas',
        'tipe_mime',
        'ukuran_berkas',
        'jenis_lampiran',
        'tipe_lampiran',
        'hash_sha256',
        'diunggah_oleh',
    ];

    /**
     * Tipe data casts untuk atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ukuran_berkas' => 'integer',
        ];
    }

    /**
     * Accessor alias untuk jenis_lampiran.
     */
    public function getTipeLampiranAttribute(): ?string
    {
        return $this->attributes['jenis_lampiran'] ?? null;
    }

    /**
     * Mutator alias untuk jenis_lampiran.
     */
    public function setTipeLampiranAttribute(mixed $value): void
    {
        $this->attributes['jenis_lampiran'] = $value;
    }

    /**
     * Relasi ke catatan pemeriksaan MCU pemilik berkas ini.
     *
     * @return BelongsTo<PemeriksaanMcu, $this>
     */
    public function pemeriksaanMcu(): BelongsTo
    {
        return $this->belongsTo(PemeriksaanMcu::class, 'pemeriksaan_mcu_id');
    }

    /**
     * Relasi ke akun staf yang mengunggah berkas.
     *
     * @return BelongsTo<User, $this>
     */
    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
