<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class DeploymentControllerTest extends TestCase
{
    public function test_rejects_unauthorized_deploy_request(): void
    {
        $response = $this->get('/deploy/refresh');
        $response->assertStatus(403);
    }

    public function test_rejects_invalid_token(): void
    {
        $response = $this->get('/deploy/refresh?token=salah_token');
        $response->assertStatus(403);
    }

    public function test_accepts_valid_token_and_runs_commands(): void
    {
        $token = (string) config('app.deploy_token');
        $response = $this->get('/deploy/refresh?token=' . $token);
        $response->assertStatus(200);
        $response->assertSee('Hasil Eksekusi Refresh & Deploy Produksi', false);
        $response->assertSee('php artisan optimize:clear');
    }
}
