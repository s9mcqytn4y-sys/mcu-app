<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel pemeriksaan_mcu.
     */
    public function up(): void
    {
        Schema::create('pemeriksaan_mcu', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->string('nomor_mcu')->unique()->index();
            $table->date('tanggal_periksa');
            $table->date('berlaku_sampai')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('status_kelayakan')->default('FIT');
            $table->string('wilayah_operasi')->nullable();
            $table->string('departemen')->nullable();
            $table->string('jenis_kapal')->nullable();
            $table->text('catatan')->nullable();
            $table->uuid('token_verifikasi')->unique()->index();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_mcu');
    }
};
