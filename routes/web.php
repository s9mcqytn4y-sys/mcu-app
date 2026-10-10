<?php

use App\Http\Controllers\McuViewerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::get('/v/{uuid}', [McuViewerController::class, 'show'])->name('mcu.verify');
Route::get('/v/{uuid}/file/{index}/view', [McuViewerController::class, 'viewFile'])->name('mcu.file.view');
Route::get('/v/{uuid}/file/{index}/download', [McuViewerController::class, 'downloadFile'])->name('mcu.file.download');

