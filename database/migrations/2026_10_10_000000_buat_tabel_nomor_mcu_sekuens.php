<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel nomor_mcu_sekuens.
     */
    public function up(): void
    {
        Schema::create('nomor_mcu_sekuens', function (Blueprint $table): void {
            $table->unsignedSmallInteger('tahun')->primary();
            $table->unsignedInteger('sekuens_terakhir')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomor_mcu_sekuens');
    }
};
