<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel log_audit.
     */
    public function up(): void
    {
        Schema::create('log_audit', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_pengguna')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tindakan')->index();
            $table->string('tipe_model')->nullable();
            $table->unsignedBigInteger('id_model')->nullable();
            $table->string('alamat_ip', 45)->nullable();
            $table->text('agen_pengguna')->nullable();
            $table->json('rincian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_audit');
    }
};
