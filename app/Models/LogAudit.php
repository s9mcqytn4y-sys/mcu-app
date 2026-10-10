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
     * Helper statis untuk mencatat entri log audit secara konsisten.
     *
     * @param array<string, mixed>|null $rincian
     */
    public static function rekam(
        string $tindakan,
        ?string $tipeModel = null,
        ?int $idModel = null,
        ?array $rincian = null,
        ?int $idPengguna = null
    ): self {
        return self::create([
            'id_pengguna' => $idPengguna ?? Auth::id(),
            'tindakan' => $tindakan,
            'tipe_model' => $tipeModel,
            'id_model' => $idModel,
            'alamat_ip' => Request::ip() ?? '127.0.0.1',
            'agen_pengguna' => substr(Request::userAgent() ?? 'CLI/System', 0, 500),
            'rincian' => $rincian,
        ]);
    }
}
