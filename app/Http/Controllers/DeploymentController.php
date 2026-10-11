<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

final class DeploymentController
{
    /**
     * Jalankan rutinitas deployment dan optimasi produksi via web request terproteksi token.
     * Khusus hosting shared (Hostinger) yang tidak memiliki akses SSH langsung.
     */
    public function refresh(Request $request): Response
    {
        $expectedToken = config('app.deploy_token', 'IndoSehat2003DeploySecureToken');
        $providedToken = (string) $request->query('token', '');

        if ($providedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
            return new Response('Akses Ditolak: Token otorisasi deployment tidak valid.', 403, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        $commands = [
            'migrate --force' => fn () => Artisan::call('migrate', ['--force' => true]),
            'optimize:clear' => fn () => Artisan::call('optimize:clear'),
            'config:cache' => fn () => Artisan::call('config:cache'),
            'route:cache' => fn () => Artisan::call('route:cache'),
            'view:cache' => fn () => Artisan::call('view:cache'),
            'event:cache' => fn () => Artisan::call('event:cache'),
            'storage:link' => fn () => Artisan::call('storage:link'),
        ];

        $results = [];

        foreach ($commands as $name => $action) {
            try {
                $exitCode = $action();
                $output = trim(Artisan::output());
                $results[] = [
                    'command' => 'php artisan ' . $name,
                    'status' => $exitCode === 0 ? 'SUKSES' : 'PERINGATAN',
                    'code' => $exitCode,
                    'output' => $output !== '' ? $output : 'Perintah selesai tanpa keluaran teks.',
                ];
            } catch (\Throwable $e) {
                $results[] = [
                    'command' => 'php artisan ' . $name,
                    'status' => 'GAGAL',
                    'code' => 1,
                    'output' => $e->getMessage(),
                ];
            }
        }

        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Refresh & Deploy Produksi - Klinik Indosehat 2003</title>
    <style>
        body { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; background: #0f172a; color: #f8fafc; padding: 2rem; line-height: 1.5; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { font-size: 1.5rem; color: #38bdf8; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .status-SUKSES { color: #4ade80; font-weight: bold; }
        .status-GAGAL { color: #f87171; font-weight: bold; }
        .status-PERINGATAN { color: #fbbf24; font-weight: bold; }
        pre { background: #090d16; padding: 0.75rem; border-radius: 0.25rem; overflow-x: auto; font-size: 0.85rem; color: #cbd5e1; }
        .btn { display: inline-block; background: #C52242; color: #fff; text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 0.375rem; font-weight: 600; margin-top: 1rem; }
        .btn:hover { background: #941B34; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛠️ Hasil Eksekusi Refresh & Deploy Produksi</h1>
        <p style="color: #94a3b8; font-size: 0.9rem;">Waktu eksekusi: ' . htmlspecialchars(now()->translatedFormat('d F Y H:i:s'), ENT_QUOTES, 'UTF-8') . ' WIB</p>';

        foreach ($results as $item) {
            $statusClass = 'status-' . htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8');
            $html .= '<div class="card">';
            $html .= '<div><strong>' . htmlspecialchars($item['command'], ENT_QUOTES, 'UTF-8') . '</strong> &rarr; <span class="' . $statusClass . '">' . htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') . ' (Code: ' . $item['code'] . ')</span></div>';
            $html .= '<pre>' . htmlspecialchars($item['output'], ENT_QUOTES, 'UTF-8') . '</pre>';
            $html .= '</div>';
        }

        $html .= '<div style="margin-top: 2rem;">
            <a href="' . url('/admin') . '" class="btn">&larr; Masuk ke Panel Admin MCU</a>
        </div>
    </div>
</body>
</html>';

        return new Response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
