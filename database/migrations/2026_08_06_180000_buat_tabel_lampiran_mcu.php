<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel lampiran_mcu.
     */
    public function up(): void
    {
        Schema::create('lampiran_mcu', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pemeriksaan_mcu_id')->constrained('pemeriksaan_mcu')->cascadeOnDelete();
            $table->string('path_berkas');
            $table->string('nama_berkas');
            $table->string('tipe_mime');
            $table->unsignedBigInteger('ukuran_berkas');
            $table->string('jenis_lampiran')->default('sertifikat'); // sertifikat, laporan_medis, berkas_tambahan
            $table->string('hash_sha256', 64)->nullable();
            $table->foreignId('diunggah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran_mcu');
    }
};
