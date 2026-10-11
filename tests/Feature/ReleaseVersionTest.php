<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ReleaseVersionTest extends TestCase
{
    public function test_endpoint_version_menolak_akses_tanpa_token(): void
    {
        $response = $this->getJson('/api/health/version');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_endpoint_version_menolak_akses_dengan_token_salah(): void
    {
        $response = $this->withHeaders([
            'X-Health-Token' => 'token-salah-123',
        ])->getJson('/api/health/version');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_endpoint_version_berhasil_dengan_token_valid(): void
    {
        $validToken = (string) config('app.health_check_token');

        $response = $this->withHeaders([
            'X-Health-Token' => $validToken,
        ])->getJson('/api/health/version');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'app',
                'version',
                'commit_sha',
                'environment',
                'timezone',
                'database',
                'queue_driver',
                'checked_at',
            ])
            ->assertJson([
                'status' => 'healthy',
            ]);
    }
}
