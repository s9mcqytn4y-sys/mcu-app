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
        'FIT' => 'success',
        'UNFIT' => 'danger',
        'DRAFT' => 'gray',
        'DICABUT' => 'warning',
        default => 'gray',
    };
    $statusLabel = match($statusUpper) {
        'FIT' => 'FIT',
        'UNFIT' => 'UNFIT',
        'DRAFT' => 'DRAF',
        'DICABUT' => 'DICABUT',
        default => 'DRAF',
    };
@endphp

<div class="flex flex-col items-center p-4 rounded-xl border border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-900/40 text-center">
    <div class="mb-3">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/50">
            Pratinjau Keabsahan QR
        </span>
    </div>

    <!-- QR Code Container / Status Penerbitan -->
    @if($verifyUrl && $qrSvg)
        <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-200 mb-3 inline-block">
            <img src="{{ $qrSvg }}" alt="QR Code Sertifikat MCU Resmi" class="w-44 h-44 mx-auto" />
        </div>
    @else
        <div class="w-44 h-44 mx-auto p-4 flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-white/60 dark:border-slate-700 dark:bg-slate-800/40 mb-3 text-slate-400">
            <x-heroicon-o-qr-code class="w-12 h-12 mb-2 text-slate-400 dark:text-slate-500" />
            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">QR Belum Diterbitkan</span>
            <span class="text-[10px] text-slate-400 mt-1">Dihasilkan otomatis saat data disimpan</span>
        </div>
    @endif

    <!-- Mini Live Card Info -->
    <div class="w-full text-left bg-white dark:bg-slate-800/90 p-3.5 rounded-lg border border-slate-200 dark:border-slate-700 mb-3 text-xs space-y-2 shadow-xs">
        <div class="flex justify-between items-center pb-2 border-b border-slate-100 dark:border-slate-700">
            <span class="text-slate-500 dark:text-slate-400 font-medium">Status:</span>
            <x-filament::badge :color="$statusColor">
                {{ $statusLabel }}
            </x-filament::badge>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-slate-500 dark:text-slate-400 font-medium">Nama Pasien:</span>
            <span class="font-semibold text-slate-900 dark:text-white truncate max-w-[150px]">{{ $nama }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-slate-500 dark:text-slate-400 font-medium">Perusahaan:</span>
            <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[150px]">{{ $perusahaan }}</span>
        </div>
    </div>

    @if($verifyUrl)
        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight mb-3">
            Arahkan kamera smartphone ke QR Code untuk memvalidasi keabsahan dokumen verifikasi resmi.
        </p>
        <x-filament::button
            tag="a"
            href="{{ $verifyUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            icon="heroicon-m-arrow-top-right-on-square"
            color="primary"
            size="sm"
            class="w-full"
        >
            Buka Halaman Verifikasi
        </x-filament::button>
    @else
        <p class="text-[11px] text-slate-400 dark:text-slate-500">
            Tautan verifikasi dan barcode resmi aktif otomatis saat data disimpan.
        </p>
    @endif
</div>
