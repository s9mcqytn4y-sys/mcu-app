<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\LogAuditResource;
use App\Filament\Resources\PemeriksaanMcuResource;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan helper method apakahAdministrator, apakahStaf, dan isAdministrator bekerja akurat.
     */
    public function test_rbac_user_helper_methods(): void
    {
        $admin = User::create([
            'username' => 'admin_mcu',
            'name' => 'Admin Utama',
            'email' => 'admin@indosehat2003.com',
            'password' => Hash::make('secret123'),
            'peran' => 'administrator',
            'aktif' => true,
        ]);

        $staf = User::create([
            'username' => 'staf_mcu',
            'name' => 'Staf Operasional',
            'email' => 'staf@indosehat2003.com',
            'password' => Hash::make('secret123'),
            'peran' => 'staf',
            'aktif' => true,
        ]);

        $this->assertTrue($admin->apakahAdministrator());
        $this->assertTrue($admin->isAdministrator());
        $this->assertFalse($admin->apakahStaf());

        $this->assertTrue($staf->apakahStaf());
        $this->assertFalse($staf->apakahAdministrator());
        $this->assertFalse($staf->isAdministrator());
    }

    /**
     * Memastikan staf operasional tidak dapat mengakses resource sistem administrasi (Log Audit & Manajemen Pengguna).
     */
    public function test_staf_terisolasi_dari_resource_log_audit_dan_pengguna(): void
    {
        $staf = User::create([
            'username' => 'petugas_klinik',
            'name' => 'Petugas Front Desk',
            'email' => 'petugas@indosehat2003.com',
            'password' => Hash::make('password123'),
            'peran' => 'staf',
            'aktif' => true,
        ]);

        $this->actingAs($staf);

        // Filament Resource canViewAny check
        $this->assertFalse(UserResource::canViewAny());
        $this->assertFalse(LogAuditResource::canViewAny());

        // Staf dapat mengakses Pemeriksaan MCU
        $this->assertTrue(PemeriksaanMcuResource::canViewAny());
    }

    /**
     * Memastikan administrator memiliki hak akses penuh ke seluruh resource.
     */
    public function test_administrator_memiliki_akses_ke_seluruh_resource(): void
    {
        $admin = User::create([
            'username' => 'super_admin',
            'name' => 'Super Administrator',
            'email' => 'superadmin@indosehat2003.com',
            'password' => Hash::make('password123'),
            'peran' => 'administrator',
            'aktif' => true,
        ]);

        $this->actingAs($admin);

        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(LogAuditResource::canViewAny());
        $this->assertTrue(PemeriksaanMcuResource::canViewAny());
    }
}
