<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $exception, Request $request) {
            \App\Models\LogAudit::rekam(
                tindakan: 'sesi_kadaluarsa',
                rincian: [
                    'url' => $request->fullUrl(),
                    'pesan' => 'Sesi pengguna telah kadaluarsa atau token CSRF tidak valid.',
                ],
                alamatIp: $request->ip(),
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman atau login kembali.',
                ], 419);
            }

            return response()->view('errors.419', [
                'exception' => $exception,
            ], 419);
        });
    })->create();
