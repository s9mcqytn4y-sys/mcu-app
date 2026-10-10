<?php

use App\Http\Controllers\McuViewerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::get('/v/{uuid}', [McuViewerController::class, 'show'])->name('mcu.verify');
Route::get('/v/{uuid}/file/{index}/view', [McuViewerController::class, 'viewFile'])->name('mcu.file.view');
Route::get('/v/{uuid}/file/{index}/download', [McuViewerController::class, 'downloadFile'])->name('mcu.file.download');

Route::get('/setup-hosting', function () {
    $output = [];
    $isSuccess = true;
    try {
        // 1. Bersihkan Cache
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output[] = ['status' => 'success', 'message' => 'Cache dan Konfigurasi berhasil dibersihkan.'];

        // 1.5. Eksekusi Migrasi Database (Jika Diminta)
        if (request()->has('migrate')) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output[] = ['status' => 'success', 'message' => 'Migrasi Database Berhasil: ' . \Illuminate\Support\Facades\Artisan::output()];
        }

        // 2. Buat symlink manual tanpa Artisan yang memanggil exec()
        $target = storage_path('app/public');
        $link = public_path('storage');
        
        if (file_exists($link)) {
            if (is_link($link)) {
                unlink($link);
            } else {
                @rmdir($link);
            }
        }
        
        if (!file_exists($link)) {
            if (function_exists('symlink')) {
                symlink($target, $link);
                $output[] = ['status' => 'success', 'message' => 'Symlink folder Storage berhasil dibuat.'];
            } else {
                $isSuccess = false;
                $output[] = ['status' => 'warning', 'message' => 'Fungsi symlink() dimatikan oleh Hostinger. Harap buat symlink manual via SSH.'];
            }
        } else {
            $isSuccess = false;
            $output[] = ['status' => 'error', 'message' => 'Folder "storage" sudah ada di public_html dan tidak bisa dihapus. Tolong hapus manual.'];
        }
    } catch (\Throwable $e) {
        $isSuccess = false;
        $output[] = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage() . ' (Baris ' . $e->getLine() . ')'];
    }

    return view('setup-hosting', compact('output', 'isSuccess'));
});
