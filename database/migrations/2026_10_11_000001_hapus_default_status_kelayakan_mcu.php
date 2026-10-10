<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menghilangkan default value FIT pada status kelayakan medis.
     */
    public function up(): void
    {
        Schema::table('pemeriksaan_mcu', function (Blueprint $table): void {
            $table->string('status_kelayakan')->default(null)->change();
        });
    }

    /**
     * Kembalikan migrasi jika diperlukan.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_mcu', function (Blueprint $table): void {
            $table->string('status_kelayakan')->default('FIT')->change();
        });
    }
};
