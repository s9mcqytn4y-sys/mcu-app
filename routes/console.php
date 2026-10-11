<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Menampilkan kutipan motivasi');

// Scheduler pemrosesan antrean latar belakang (Queue Worker) untuk Shared/Single Hosting
// Memproses antrean email & notifikasi secara aman setiap menit tanpa proses tumpang tindih
Schedule::command('queue:work database --stop-when-empty --tries=3 --timeout=45')
    ->everyMinute()
    ->withoutOverlapping(10);

// Pembersihan berkas unduhan/cache sementara setiap hari
Schedule::command('queue:prune-failed --hours=48')
    ->daily();
