@php
    $qrAction = app(\App\Actions\Mcu\GenerateQrCodeAction::class);
    $qrSvg = $qrAction->generateSvgDataUri($verifyUrl);
    $statusColor = strtoupper($status) === 'FIT' ? '#15803d' : '#b45343';
    $statusBg = strtoupper($status) === 'FIT' ? '#dcfce7' : '#fee2e2';
@endphp

<div class="flex flex-col items-center p-4 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-800 text-center">
    <div class="mb-3">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-300 border border-primary-200/50">
            Real-time Verification Link
        </span>
    </div>

    <!-- QR Code Container -->
    <div class="p-3 bg-white rounded-xl shadow-sm border border-slate-200/80 mb-3 inline-block">
        <img src="{{ $qrSvg }}" alt="QR Code Sertifikat MCU" class="w-48 h-48 mx-auto" />
    </div>

    <!-- Mini Live Card Info -->
    <div class="w-full text-left bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700/80 mb-3 text-xs space-y-1.5 shadow-2xs">
        <div class="flex justify-between items-center pb-1 border-b border-slate-100 dark:border-slate-700">
            <span class="text-slate-500 font-medium">Status Hasil:</span>
            <span class="px-2 py-0.5 rounded-full font-bold uppercase" style="background-color: {{ $statusBg }}; color: {{ $statusColor }};">
                {{ $status }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Nama:</span>
            <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[150px]">{{ $nama }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500 font-medium">Perusahaan:</span>
            <span class="text-slate-700 dark:text-slate-300 truncate max-w-[150px]">{{ $perusahaan }}</span>
        </div>
    </div>

    <p class="text-[11px] text-slate-500 leading-tight mb-3">
        Arahkan kamera smartphone ke QR Code di atas untuk menguji keabsahan tautan sertifikat.
    </p>

    @if($record)
        <a 
            href="{{ $verifyUrl }}" 
            target="_blank" 
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center w-full px-3 py-2 text-xs font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-colors"
        >
            Buka Halaman Verifikasi ↗
        </a>
    @else
        <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">
            (QR aktif tersimpan otomatis saat tombol 'Terbitkan Sertifikat' ditekan)
        </span>
    @endif
</div>
