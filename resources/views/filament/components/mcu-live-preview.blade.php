@php
    /** @var ?\App\Models\PemeriksaanMcu $record */
    $record = $record ?? null;
    $status = $status ?? (isset($get) ? $get('status_kelayakan') : null) ?? $record?->status_kelayakan;
    $nama = $nama ?? (isset($get) ? $get('nama_pasien') : null) ?? ($record?->pasien?->nama ?? '-');
    $perusahaan = $perusahaan ?? (isset($get) ? $get('nama_perusahaan') : null) ?? ($record?->nama_perusahaan ?? '-');
    $verifyUrl = $verifyUrl ?? ($record?->token_verifikasi
        ? route('mcu.verify', ['uuid' => $record->token_verifikasi])
        : null);

    $qrSvg = null;
    if ($verifyUrl) {
        $qrAction = app(\App\Actions\Mcu\GenerateQrCodeAction::class);
        $qrSvg = $qrAction->generateSvgDataUri($verifyUrl);
    }

    $statusUpper = $status ? strtoupper((string) $status) : null;
    $statusColor = match($statusUpper) {
        'FIT' => '#15803d',
        'UNFIT' => '#b45343',
        'DRAFT' => '#b45309',
        'DICABUT' => '#475569',
        default => '#475569',
    };
    $statusBg = match($statusUpper) {
        'FIT' => '#dcfce7',
        'UNFIT' => '#fee2e2',
        'DRAFT' => '#fef3c7',
        'DICABUT' => '#f1f5f9',
        default => '#f1f5f9',
    };
    $statusLabel = match($statusUpper) {
        'FIT' => 'FIT',
        'UNFIT' => 'UNFIT',
        'DRAFT' => 'DRAFT',
        'DICABUT' => 'DICABUT',
        default => 'BELUM DITENTUKAN',
    };
@endphp

<div class="flex flex-col items-center p-4 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 text-center">
    <div class="mb-3">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-300 border border-primary-200/50">
            Pratinjau Kode QR
        </span>
    </div>

    <!-- QR Code Container / Status Penerbitan -->
    @if($verifyUrl && $qrSvg)
        <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-200/80 mb-3 inline-block">
            <img src="{{ $qrSvg }}" alt="QR Code Sertifikat MCU" class="w-48 h-48 mx-auto" />
        </div>
    @else
        <div class="w-48 h-48 mx-auto p-4 flex flex-col items-center justify-center bg-white dark:bg-slate-800 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 mb-3 text-slate-400">
            <svg class="w-12 h-12 mb-2 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">QR Belum Diterbitkan</span>
            <span class="text-[10px] text-slate-400 mt-1">Aktif otomatis setelah disimpan</span>
        </div>
    @endif

    <!-- Mini Live Card Info -->
    <div class="w-full text-left bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700/80 mb-3 text-xs space-y-1.5 shadow-2xs">
        <div class="flex justify-between items-center pb-1 border-b border-slate-100 dark:border-slate-700">
            <span class="text-slate-500 font-medium">Status Hasil:</span>
            <span class="px-2 py-0.5 rounded-full font-bold uppercase text-[11px]" style="background-color: {{ $statusBg }}; color: {{ $statusColor }};">
                {{ $statusLabel }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Nama:</span>
            <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-37.5">{{ $nama }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Perusahaan:</span>
            <span class="text-slate-700 dark:text-slate-300 truncate max-w-37.5">{{ $perusahaan }}</span>
        </div>
    </div>

    @if($verifyUrl)
        <p class="text-[11px] text-slate-500 leading-tight mb-3">
            Arahkan kamera smartphone ke QR Code di atas untuk menguji keabsahan tautan sertifikat resmi.
        </p>
        <a 
            href="{{ $verifyUrl }}" 
            target="_blank" 
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center w-full min-h-11 px-3 py-2 text-xs font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600"
        >
            Buka Halaman Verifikasi ↗
        </a>
    @else
        <span class="text-[11px] text-slate-500 font-medium">
            (Token UUID dan tautan verifikasi resmi diterbitkan otomatis saat formulir disimpan)
        </span>
    @endif
</div>
