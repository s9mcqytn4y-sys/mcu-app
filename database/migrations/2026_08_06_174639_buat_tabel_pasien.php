<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel pasien.
     */
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table): void {
            $table->id();
            $table->string('nama')->index();
            $table->string('jenis_kelamin')->nullable(); // 'Pria / Male', 'Wanita / Female'
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_ktp', 20)->nullable()->index();
            $table->string('no_paspor', 30)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
