<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('token_undangan', 64)->nullable()->unique()->after('remember_token');
            $table->timestamp('undangan_dikirim_pada')->nullable()->after('token_undangan');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['token_undangan', 'undangan_dikirim_pada']);
        });
    }
};
