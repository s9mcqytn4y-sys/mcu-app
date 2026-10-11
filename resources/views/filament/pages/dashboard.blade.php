<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Banner Operasional & Quick Action Utama --}}
        <x-filament::section>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-md bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                        <span class="inline-block size-2 rounded-full bg-rose-600"></span>
                        Ringkasan Operasional
                    </div>
                    <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Pusat Kendali Pemeriksaan & Sertifikasi MCU
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        Kelola data pasien, validasi kelayakan medis maritim, dan terbitkan sertifikat ber-QR terverifikasi secara resmi.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-filament::button
                        tag="a"
                        href="{{ $this->getUrlPemeriksaanBaru() }}"
                        icon="heroicon-m-plus-circle"
                        color="danger"
                        size="lg"
                        class="min-h-11 shadow-sm transition hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Input MCU Baru
                    </x-filament::button>
                    <x-filament::button
                        tag="a"
                        href="{{ $this->getUrlRegistrasiPasien() }}"
                        icon="heroicon-m-user-plus"
                        color="gray"
                        size="lg"
                        class="min-h-11 transition hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Registrasi Pasien
                    </x-filament::button>
                    <x-filament::button
                        tag="a"
                        href="{{ $this->getUrlDaftarPasien() }}"
                        icon="heroicon-m-magnifying-glass"
                        color="gray"
                        outlined="true"
                        size="lg"
                        class="min-h-11 transition hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Cari Pasien
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Grid Metrik Klinis (KPI Cards) --}}
        @php
            $statKelayakan = $this->getStatistikKelayakan();
            $hariIniCount = $this->getPemeriksaanHariIniCount();
            $totalPasien = $this->getTotalPasienCount();
            $totalMcu = $this->getTotalMcuCount();
        @endphp

        <x-filament::grid default="1" sm="2" lg="4" class="gap-4">
            {{-- Kartu 1: Pemeriksaan Hari Ini --}}
            <x-filament::section class="transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pemeriksaan Hari Ini</span>
                    <span class="rounded-full bg-rose-50 p-2 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                        <x-heroicon-o-calendar-days class="size-5" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($hariIniCount) }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Pemeriksaan diterbitkan hari ini ({{ now()->translatedFormat('d M Y') }})
                    </div>
                </div>
            </x-filament::section>

            {{-- Kartu 2: Rasio Laik Kerja --}}
            <x-filament::section class="transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kelayakan Medis</span>
                    <span class="rounded-full bg-emerald-50 p-2 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                        <x-heroicon-o-check-badge class="size-5" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ $statKelayakan['fit_persen'] }}%
                        </span>
                        <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400">
                            FIT ({{ $statKelayakan['fit'] }})
                        </span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <span>UNFIT: <strong class="font-bold text-rose-600 dark:text-rose-400">{{ $statKelayakan['unfit'] }}</strong> pasien</span>
                    </div>
                </div>
            </x-filament::section>

            {{-- Kartu 3: Total Pasien --}}
            <x-filament::section class="transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Pasien Terdaftar</span>
                    <span class="rounded-full bg-blue-50 p-2 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                        <x-heroicon-o-identification class="size-5" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($totalPasien) }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Total rekam identitas pasien terdaftar
                    </div>
                </div>
            </x-filament::section>

            {{-- Kartu 4: Total Arsip MCU --}}
            <x-filament::section class="transition-all duration-200 hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Arsip MCU</span>
                    <span class="rounded-full bg-slate-100 p-2 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <x-heroicon-o-shield-check class="size-5" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($totalMcu) }}
                    </div>
                    <div class="mt-1 flex items-center gap-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                        <span class="inline-block size-1.5 rounded-full bg-emerald-500"></span>
                        Arsip terenkripsi dengan log audit
                    </div>
                </div>
            </x-filament::section>
        </x-filament::grid>



        {{-- Tabel Aktivitas Pemeriksaan Terkini --}}
        <x-filament::section>
            <x-slot name="heading">
                Sertifikasi MCU Terkini
            </x-slot>
            <x-slot name="description">
                Daftar pemeriksaan medis yang terakhir didaftarkan atau diperbarui.
            </x-slot>
            <x-slot name="headerEnd">
                <x-filament::link
                    href="{{ $this->getUrlDaftarPemeriksaan() }}"
                    color="danger"
                    size="sm"
                    icon="heroicon-m-arrow-right"
                    icon-position="after"
                >
                    Lihat Semua Arsip
                </x-filament::link>
            </x-slot>

            @php
                $pemeriksaanList = $this->getPemeriksaanTerbaru();
            @endphp

            @if($pemeriksaanList->isEmpty())
                <div class="py-12 text-center">
                    <x-heroicon-o-document-text class="mx-auto size-12 text-slate-300 dark:text-slate-600" />
                    <h4 class="mt-3 text-sm font-semibold text-slate-800 dark:text-slate-200">
                        Belum Ada Data Pemeriksaan
                    </h4>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Mulai daftarkan pemeriksaan MCU pertama untuk menerbitkan sertifikat dan QR verifikasi resmi.
                    </p>
                    <div class="mt-4">
                        <x-filament::button
                            tag="a"
                            href="{{ $this->getUrlPemeriksaanBaru() }}"
                            icon="heroicon-m-plus"
                            color="danger"
                            size="sm"
                        >
                            Tambah Pemeriksaan Sekarang
                        </x-filament::button>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full text-left text-sm" role="table">
                        <thead class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Nomor MCU</th>
                                <th scope="col" class="px-4 py-3">Pasien</th>
                                <th scope="col" class="px-4 py-3">Tanggal Periksa</th>
                                <th scope="col" class="px-4 py-3">Masa Berlaku</th>
                                <th scope="col" class="px-4 py-3">Kelayakan</th>
                                <th scope="col" class="px-4 py-3 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($pemeriksaanList as $mcu)
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                    <td class="whitespace-nowrap px-4 py-3.5 font-semibold text-slate-900 dark:text-white">
                                        {{ $mcu->nomor_mcu }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $mcu->pasien->nama ?? '-' }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $mcu->pasien->no_ktp ? 'NIK: ' . $mcu->pasien->no_ktp : ($mcu->pasien->no_paspor ? 'Paspor: ' . $mcu->pasien->no_paspor : '-') }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                        {{ $mcu->tanggal_periksa ? $mcu->tanggal_periksa->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                        {{ $mcu->berlaku_sampai ? $mcu->berlaku_sampai->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">
                                        @if($mcu->status_kelayakan === 'FIT')
                                            <x-filament::badge color="success">
                                                FIT
                                            </x-filament::badge>
                                        @elseif($mcu->status_kelayakan === 'UNFIT')
                                            <x-filament::badge color="danger">
                                                UNFIT
                                            </x-filament::badge>
                                        @elseif($mcu->status_kelayakan === 'DRAFT')
                                            <x-filament::badge color="gray">
                                                DRAF
                                            </x-filament::badge>
                                        @else
                                            <x-filament::badge color="warning">
                                                {{ $mcu->status_kelayakan ?? '-' }}
                                            </x-filament::badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            @if($mcu->token_verifikasi)
                                                <x-filament::button
                                                    tag="a"
                                                    href="{{ url('/v/' . $mcu->token_verifikasi) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    icon="heroicon-m-qr-code"
                                                    color="gray"
                                                    size="xs"
                                                    title="Lihat Verifikasi Publik"
                                                >
                                                    QR
                                                </x-filament::button>
                                            @if (auth()->user()?->apakahAdministrator())
                                                <x-filament::button
                                                    tag="a"
                                                    href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $mcu->id]) }}"
                                                    icon="heroicon-m-pencil-square"
                                                    color="gray"
                                                    size="xs"
                                                >
                                                    Edit
                                                </x-filament::button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
