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
     * Hook lifecycle untuk memastikan metadata berkas terisi otomatis.
     */
    protected static function booted(): void
    {
        static::saving(function (LampiranMcu $lampiran): void {
            if (!empty($lampiran->path_berkas) && \Illuminate\Support\Facades\Storage::disk('local')->exists($lampiran->path_berkas)) {
                $disk = \Illuminate\Support\Facades\Storage::disk('local');
                if (empty($lampiran->ukuran_berkas)) {
                    $lampiran->ukuran_berkas = (int) $disk->size($lampiran->path_berkas);
                }
                if (empty($lampiran->tipe_mime)) {
                    $lampiran->tipe_mime = $disk->mimeType($lampiran->path_berkas) ?? 'application/octet-stream';
                }
                if (empty($lampiran->nama_berkas)) {
                    $lampiran->nama_berkas = basename($lampiran->path_berkas);
                }
                if (empty($lampiran->hash_sha256)) {
                    $fullPath = $disk->path($lampiran->path_berkas);
                    if (file_exists($fullPath)) {
                        $lampiran->hash_sha256 = hash_file('sha256', $fullPath);
                    }
                }
            } else {
                if (empty($lampiran->ukuran_berkas)) {
                    $lampiran->ukuran_berkas = 0;
                }
                if (empty($lampiran->tipe_mime)) {
                    $lampiran->tipe_mime = 'application/octet-stream';
                }
                if (empty($lampiran->nama_berkas)) {
                    $lampiran->nama_berkas = basename($lampiran->path_berkas ?? 'dokumen.pdf');
                }
            }

            if (empty($lampiran->diunggah_oleh) && auth()->check()) {
                $lampiran->diunggah_oleh = auth()->id();
            }
        });
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
