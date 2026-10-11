<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan UUID opaque pada tabel pasien dan users.
     */
    public function up(): void
    {
        Schema::table('pasien', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id')->index();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id')->index();
        });

        // Backfill UUID untuk entitas yang sudah ada sebelumnya
        DB::table('pasien')->whereNull('uuid')->orderBy('id')->each(function (object $pasien): void {
            DB::table('pasien')->where('id', $pasien->id)->update(['uuid' => (string) Str::uuid()]);
        });

        DB::table('users')->whereNull('uuid')->orderBy('id')->each(function (object $user): void {
            DB::table('users')->where('id', $user->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table('pasien', function (Blueprint $table): void {
            $table->unique('uuid');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('uuid');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });

        Schema::table('pasien', function (Blueprint $table): void {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
