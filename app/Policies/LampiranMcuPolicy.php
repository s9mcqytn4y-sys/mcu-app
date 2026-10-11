<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LampiranMcu;
use App\Models\User;

class LampiranMcuPolicy
{
    /**
     * Menentukan apakah pengguna dapat melihat daftar lampiran rekam medis.
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->aktif;
    }

    /**
     * Menentukan apakah pengguna dapat mengakses dan mengunduh berkas fisik lampiran.
     */
    public function view(User $user, LampiranMcu $lampiran): bool
    {
        return (bool) $user->aktif;
    }

    /**
     * Menentukan apakah pengguna dapat mengunggah lampiran baru.
     */
    public function create(User $user): bool
    {
        return (bool) $user->aktif;
    }

    /**
     * Menentukan apakah pengguna dapat memperbarui metadata berkas lampiran.
     */
    public function update(User $user, LampiranMcu $lampiran): bool
    {
        return (bool) $user->aktif && $user->apakahAdministrator();
    }

    /**
     * Menentukan apakah pengguna dapat menghapus berkas lampiran rekam medis.
     * Hanya Administrator yang berwenang demi menjaga rekam jejak pemeriksaan.
     */
    public function delete(User $user, LampiranMcu $lampiran): bool
    {
        return (bool) $user->aktif && $user->apakahAdministrator();
    }
}
