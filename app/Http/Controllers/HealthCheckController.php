<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends Controller
{
    /**
     * Verifikasi rilis, commit SHA, dan status sistem untuk CI/CD pipeline.
     * Terproteksi menggunakan secret header X-Health-Token.
     */
    public function version(Request $request): JsonResponse
    {
        $expectedToken = (string) config('app.health_check_token');
        $providedToken = (string) $request->header('X-Health-Token', $request->query('token', ''));

        if ($expectedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak: token verifikasi kesehatan rilis tidak valid.',
            ], Response::HTTP_FORBIDDEN);
        }

        $commitSha = $this->resolveCommitSha();

        return response()->json([
            'status' => 'healthy',
            'app' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
            'commit_sha' => $commitSha,
            'environment' => config('app.env'),
            'timezone' => config('app.timezone'),
            'database' => config('database.default'),
            'queue_driver' => config('queue.default'),
            'checked_at' => now()->toIso8601String(),
        ], Response::HTTP_OK);
    }

    /**
     * Dapatkan commit SHA pendek dari git atau berkas build cache.
     */
    private function resolveCommitSha(): string
    {
        $versionFile = base_path('version.json');
        if (is_file($versionFile)) {
            $data = json_decode((string) file_get_contents($versionFile), true);
            if (is_array($data) && ! empty($data['commit_sha'])) {
                return (string) $data['commit_sha'];
            }
        }

        $gitHead = base_path('.git/HEAD');
        if (is_file($gitHead)) {
            $headContent = trim((string) file_get_contents($gitHead));
            if (str_starts_with($headContent, 'ref: ')) {
                $refPath = base_path('.git/' . substr($headContent, 5));
                if (is_file($refPath)) {
                    return substr(trim((string) file_get_contents($refPath)), 0, 7);
                }
            } elseif (strlen($headContent) >= 7) {
                return substr($headContent, 0, 7);
            }
        }

        $sha = @shell_exec('git rev-parse --short HEAD');
        if (is_string($sha) && trim($sha) !== '') {
            return trim($sha);
        }

        return 'prod-release';
    }
}
