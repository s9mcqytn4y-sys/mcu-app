@php
    /** @var ?\App\Models\PemeriksaanMcu $record */
    $record = $record ?? null;
    $status = $status ?? (isset($get) ? $get('status_kelayakan') : null) ?? $record?->status_kelayakan;
    $nama = $nama ?? (isset($get) ? $get('nama_pasien') : null) ?? ($record?->pasien?->nama ?? '-');
    $perusahaan = $perusahaan ?? (isset($get) ? $get('nama_perusahaan') : null) ?? ($record?->nama_perusahaan ?? '-');
    $nomorMcu = $nomorMcu ?? (isset($get) ? $get('nomor_mcu') : null) ?? ($record?->nomor_mcu ?? 'BARU');
    $verifyUrl = $verifyUrl ?? ($record?->token_verifikasi
        ? route('mcu.verify', ['uuid' => $record->token_verifikasi])
        : null);

    $qrSvg = null;
    $qrPngUri = null;
    $cleanNomor = str_replace(['/', '\\', ' '], '-', (string) $nomorMcu);
    $cleanNama = \Illuminate\Support\Str::slug((string) $nama);

    if ($verifyUrl) {
        $qrAction = app(\App\Actions\Mcu\GenerateQrCodeAction::class);
        $qrSvg = $qrAction->generateSvgDataUri($verifyUrl);
        $qrPngUri = $qrAction->generatePngDataUri($verifyUrl, 1024);
    }

    $statusUpper = $status ? strtoupper((string) $status) : null;
    $statusColor = match($statusUpper) {
        'FIT' => 'success',
        'UNFIT' => 'danger',
        'DRAFT' => 'warning',
        'DICABUT' => 'gray',
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

<div class="w-full flex flex-col items-center space-y-4 text-center">
    <!-- Badge Header Indikator -->
    <div class="w-full flex justify-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-950/60 text-rose-300 border border-rose-800/50 shadow-xs">
            <x-heroicon-m-qr-code class="w-3.5 h-3.5 text-rose-400" />
            Keabsahan Dokumen & Barcode Resmi
        </span>
    </div>

    <!-- QR Code Container: Frame Putih Bersih (Quiet Zone) agar Kamera & Scanner Terbaca Cepat -->
    @if($verifyUrl && $qrSvg)
        <div class="p-4 bg-white rounded-2xl shadow-md border border-slate-200 dark:border-slate-700/50 inline-flex flex-col items-center justify-center">
            <img src="{{ $qrSvg }}" alt="QR Code Sertifikat MCU Resmi" class="w-48 h-48 block" />
            <span class="text-[10px] text-slate-500 font-mono mt-2 tracking-wider uppercase font-semibold">Quiet Zone 16px • High Error Correction</span>
        </div>
    @else
        <div class="w-full max-w-[260px] aspect-square mx-auto p-6 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-700/80 bg-slate-800/40 text-slate-400">
            <x-heroicon-o-qr-code class="w-14 h-14 mb-3 text-slate-500" />
            <span class="text-sm font-semibold text-slate-300">QR Belum Diterbitkan</span>
            <span class="text-xs text-slate-400 mt-1.5 max-w-[180px] leading-relaxed">Dihasilkan otomatis saat berkas disimpan</span>
        </div>
    @endif

    <!-- Panel Detail Sertifikat (Full Width, Kontras Tinggi, Tanpa Truncate) -->
    <div class="w-full text-left bg-slate-800/90 dark:bg-slate-900/95 p-4 rounded-xl border border-slate-700/70 shadow-xs divide-y divide-slate-700/60 text-xs">
        <div class="flex justify-between items-center pb-2.5">
            <span class="text-slate-400 font-medium">Status Kelayakan:</span>
            <x-filament::badge :color="$statusColor" size="sm">
                {{ $statusLabel }}
            </x-filament::badge>
        </div>
        <div class="flex justify-between items-start py-2.5 gap-2">
            <span class="text-slate-400 font-medium shrink-0">Nama Pasien:</span>
            <span class="font-semibold text-slate-100 text-right break-words">{{ $nama }}</span>
        </div>
        <div class="flex justify-between items-start py-2.5 gap-2">
            <span class="text-slate-400 font-medium shrink-0">Nomor MCU:</span>
            <span class="font-mono font-medium text-slate-200 text-right break-all">{{ $nomorMcu }}</span>
        </div>
        <div class="flex justify-between items-start pt-2.5 gap-2">
            <span class="text-slate-400 font-medium shrink-0">Perusahaan:</span>
            <span class="font-medium text-slate-300 text-right break-words">{{ $perusahaan }}</span>
        </div>
    </div>

    @if($verifyUrl && $qrPngUri && $qrSvg)
        <!-- Opsi Unduh & Konfigurasi Ekspor QR Code -->
        <div class="w-full space-y-3 pt-1">
            <div class="text-xs font-semibold text-slate-300 text-left flex items-center gap-1.5">
                <x-heroicon-m-arrow-down-tray class="w-4 h-4 text-rose-400" />
                <span>Opsi Unduh Barcode Resmi:</span>
            </div>
            
            <div class="grid grid-cols-2 gap-2.5">
                <a
                    href="{{ $qrPngUri }}"
                    download="QR-MCU-{{ $cleanNomor }}-{{ $cleanNama }}.png"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-emerald-400 min-h-11"
                    title="Unduh format PNG resolusi tinggi 1024x1024px untuk dicetak pada sertifikat kertas"
                >
                    <x-heroicon-m-arrow-down-tray class="w-4 h-4" />
                    <span>PNG (1024px)</span>
                </a>

                <a
                    href="{{ $qrSvg }}"
                    download="QR-MCU-{{ $cleanNomor }}-{{ $cleanNama }}.svg"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs font-semibold rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-sky-400 min-h-11"
                    title="Unduh format Vektor SVG untuk desain digital tanpa pecah"
                >
                    <x-heroicon-m-code-bracket class="w-4 h-4" />
                    <span>Vektor (SVG)</span>
                </a>
            </div>

            <div x-data="{ copied: false }" class="w-full">
                <button
                    type="button"
                    x-on:click="window.navigator.clipboard.writeText('{{ $verifyUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700/80 text-slate-200 border border-slate-700 shadow-sm transition-colors focus-visible:ring-2 focus-visible:ring-rose-400 min-h-11"
                >
                    <x-heroicon-m-clipboard-document-check x-show="copied" class="w-4 h-4 text-emerald-400" />
                    <x-heroicon-m-clipboard-document x-show="!copied" class="w-4 h-4 text-slate-400" />
                    <span x-text="copied ? 'Tautan Verifikasi Disalin!' : 'Salin Tautan Verifikasi'">Salin Tautan Verifikasi</span>
                </button>
            </div>

            <x-filament::button
                tag="a"
                href="{{ $verifyUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                icon="heroicon-m-arrow-top-right-on-square"
                color="primary"
                size="md"
                class="w-full min-h-11"
            >
                Buka Halaman Verifikasi
            </x-filament::button>
        </div>
    @else
        <div class="w-full p-3 rounded-lg bg-slate-800/40 border border-slate-700/60 text-center">
            <p class="text-xs text-slate-400 leading-relaxed">
                Tautan verifikasi dan barcode resmi aktif otomatis saat data disimpan.
            </p>
        </div>
    @endif
</div>
