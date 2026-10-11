<?php

use App\Http\Controllers\McuViewerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::middleware(\App\Http\Middleware\PreventSensitiveCaching::class)->group(function () {
    Route::get('/v/{uuid}', [McuViewerController::class, 'show'])->name('mcu.verify');
    Route::get('/v/{uuid}/file/{index}/view', [McuViewerController::class, 'viewFile'])->name('mcu.file.view');
    Route::get('/v/{uuid}/file/{index}/download', [McuViewerController::class, 'downloadFile'])->name('mcu.file.download');
});

Route::get('/api/health/version', [\App\Http\Controllers\HealthCheckController::class, 'version'])
    ->name('api.health.version');

Route::get('/deploy/refresh', [\App\Http\Controllers\DeploymentController::class, 'refresh'])
    ->name('deploy.refresh');



