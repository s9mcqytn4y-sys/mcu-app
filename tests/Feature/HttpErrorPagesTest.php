<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HttpErrorPagesTest extends TestCase
{
    /**
     * Memastikan halaman 404 menampilkan layout khusus bernuansa klinis dan WCAG compliant.
     */
    public function test_halaman_404_menampilkan_pesan_dan_navigasi(): void
    {
        $response = $this->get('/halaman-pasti-tidak-ada-404-check');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
    }

    /**
     * Memastikan view error disiapkan dan dapat dirender tanpa exception.
     */
    public function test_semua_view_error_dapat_dirender(): void
    {
        $views = [
            'errors.401',
            'errors.403',
            'errors.404',
            'errors.419',
            'errors.429',
            'errors.500',
            'errors.503',
        ];

        foreach ($views as $view) {
            $rendered = view($view)->render();
            $this->assertNotEmpty($rendered);
            $this->assertStringContainsString('Klinik Indosehat 2003', $rendered);
        }
    }
}
