<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PemeriksaanMcu;
use App\Models\User;

class PemeriksaanMcuPolicy
{
    /**
     * Menentukan apakah pengguna dapat melihat daftar pemeriksaan MCU.
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->aktif;
    }

    /**
     * Menentukan apakah pengguna dapat melihat detail pemeriksaan MCU tertentu.
     */
    public function view(User $user, PemeriksaanMcu $mcu): bool
    {
        return (bool) $user->aktif;
    }

    /**
     * Menentukan apakah pengguna dapat membuat pemeriksaan MCU baru.
     */
    public function create(User $user): bool
    {
        return (bool) $user->aktif;
    }

    /**
     * Menentukan apakah pengguna dapat memperbarui pemeriksaan MCU.
     * Staf operasional dilarang mengedit jika sertifikat sudah terbit (FIT, UNFIT, DICABUT).
     * Administrator dapat melakukan pembaruan/revisi.
     */
    public function update(User $user, PemeriksaanMcu $mcu): bool
    {
        if (! (bool) $user->aktif) {
            return false;
        }

        if ($user->apakahAdministrator()) {
            return true;
        }

        // Staf hanya boleh mengubah pemeriksaan yang masih berstatus DRAF
        return strtoupper((string) $mcu->status_kelayakan) === 'DRAF';
    }

    /**
     * Menentukan apakah pengguna dapat menghapus rekam pemeriksaan MCU.
     * Dibatasi ketat hanya untuk Administrator aktif demi integritas data medis.
     */
    public function delete(User $user, PemeriksaanMcu $mcu): bool
    {
        return (bool) $user->aktif && $user->apakahAdministrator();
    }

    /**
     * Menentukan apakah pengguna dapat memulihkan rekam pemeriksaan MCU.
     */
    public function restore(User $user, PemeriksaanMcu $mcu): bool
    {
        return (bool) $user->aktif && $user->apakahAdministrator();
    }

    /**
     * Menentukan apakah pengguna dapat menghapus permanen rekam pemeriksaan MCU.
     */
    public function forceDelete(User $user, PemeriksaanMcu $mcu): bool
    {
        return (bool) $user->aktif && $user->apakahAdministrator();
    }
}
