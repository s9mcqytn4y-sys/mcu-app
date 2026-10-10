<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogAudit extends Model
{
    use HasFactory;

    /**
     * Nama tabel database terkait.
     */
    protected $table = 'log_audit';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_pengguna',
        'tindakan',
        'tipe_model',
        'id_model',
        'alamat_ip',
        'agen_pengguna',
        'rincian',
    ];

    /**
     * Tipe data casts untuk atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rincian' => 'array',
        ];
    }

    /**
     * Relasi ke akun staf atau admin yang melakukan tindakan.
     *
     * @return BelongsTo<User, $this>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Relasi alias kompatibilitas untuk pengguna.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->pengguna();
    }

    /**
     * Accessor alias untuk tindakan (aksi).
     */
    public function getAksiAttribute(): ?string
    {
        return $this->tindakan;
    }

    /**
     * Accessor alias untuk tipe_model (tipe_entitas).
     */
    public function getTipeEntitasAttribute(): ?string
    {
        return $this->tipe_model;
    }

    /**
     * Accessor alias untuk agen_pengguna (user_agent).
     */
    public function getUserAgentAttribute(): ?string
    {
        return $this->agen_pengguna;
    }

    /**
     * Helper statis untuk mencatat entri log audit secara konsisten.
     * Mendukung pemanggilan tradisional maupun named arguments (aksi, model).
     *
     * @param array<string, mixed>|null $rincian
     */
    public static function rekam(
        string $tindakan = '',
        string|Model|null $tipeModel = null,
        ?int $idModel = null,
        ?array $rincian = null,
        ?int $idPengguna = null,
        ?string $aksi = null,
        ?Model $model = null,
        ?string $keterangan = null
    ): self {
        $tindakanFinal = $aksi ?? $tindakan;

        $targetModel = $model ?? ($tipeModel instanceof Model ? $tipeModel : null);
        $tipeModelFinal = $targetModel ? get_class($targetModel) : (is_string($tipeModel) ? $tipeModel : null);
        $idModelFinal = $targetModel ? (int) $targetModel->getKey() : $idModel;

        $rincianPayload = $rincian ?? [];
        if ($keterangan !== null && $keterangan !== '') {
            $rincianPayload['keterangan'] = $keterangan;
        }

        return self::create([
            'id_pengguna' => $idPengguna ?? Auth::id(),
            'tindakan' => $tindakanFinal,
            'tipe_model' => $tipeModelFinal,
            'id_model' => $idModelFinal,
            'alamat_ip' => Request::ip() ?? '127.0.0.1',
            'agen_pengguna' => substr(Request::userAgent() ?? 'CLI/System', 0, 500),
            'rincian' => !empty($rincianPayload) ? $rincianPayload : null,
        ]);
    }

    /**
     * Helper baru yang direkomendasikan dengan kontrak eksplisit.
     *
     * @param array<string, mixed> $rincian
     */
    public static function catat(
        string $tindakan,
        Model|string|null $model = null,
        ?int $idModel = null,
        array $rincian = [],
        ?int $idPengguna = null
    ): self {
        return self::rekam(
            tindakan: $tindakan,
            tipeModel: is_string($model) ? $model : null,
            idModel: $idModel,
            rincian: $rincian,
            idPengguna: $idPengguna,
            model: $model instanceof Model ? $model : null
        );
    }
}
