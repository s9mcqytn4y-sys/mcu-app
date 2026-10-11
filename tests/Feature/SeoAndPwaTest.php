<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SeoAndPwaTest extends TestCase
{
    /**
     * Memastikan robots.txt tersedia dan memiliki arahan yang sesuai standar.
     */
    public function test_robots_txt_tersedia_dan_valid(): void
    {
        $filePath = public_path('robots.txt');
        $this->assertFileExists($filePath);

        $content = (string) file_get_contents($filePath);
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /v/', $content);
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('sitemap.xml', $content);
    }

    /**
     * Memastikan berkas sitemap.xml tersedia dan valid secara format XML.
     */
    public function test_sitemap_xml_tersedia(): void
    {
        $filePath = public_path('sitemap.xml');
        $this->assertFileExists($filePath);

        $content = (string) file_get_contents($filePath);
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('indosehat2003.com', $content);
    }

    /**
     * Memastikan manifest PWA dan service worker terpasang di direktori publik.
     */
    public function test_pwa_manifest_dan_service_worker_tersedia(): void
    {
        $manifestPath = public_path('manifest.webmanifest');
        $this->assertFileExists($manifestPath);

        $manifestContent = (string) file_get_contents($manifestPath);
        $manifestJson = json_decode($manifestContent, true);
        $this->assertIsArray($manifestJson);
        $this->assertEquals('Indosehat 2003 Portal MCU', $manifestJson['name']);
        $this->assertEquals('standalone', $manifestJson['display']);

        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);
        $swContent = (string) file_get_contents($swPath);
        $this->assertStringContainsString('indosehat-mcu-v1.1.0', $swContent);
    }
}
