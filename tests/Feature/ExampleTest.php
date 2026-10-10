<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Memastikan route root dialihkan ke portal admin Filament.
     */
    public function test_halaman_root_dialihkan_ke_portal_admin(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/admin');
    }

    /**
     * Memastikan halaman login admin dapat diakses.
     */
    public function test_halaman_login_admin_dapat_diakses(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }
}
