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
            $kanbanData = $this->getKanbanPemeriksaan();
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

        {{-- Papan Kanban Visual Status Alur Kerja MCU --}}
        <x-filament::section>
            <x-slot name="heading">
                Papan Status Kelayakan Medis (Alur Kerja Klinis)
            </x-slot>
            <x-slot name="description">
                Pemantauan visual alur sertifikasi kerja medis berdasarkan status kelayakan pasien terkini.
            </x-slot>
            <x-slot name="headerEnd">
                <x-filament::link
                    href="{{ $this->getUrlDaftarPemeriksaan() }}"
                    color="gray"
                    size="sm"
                    icon="heroicon-m-arrows-pointing-out"
                    icon-position="after"
                >
                    Kelola di Tabel
                </x-filament::link>
            </x-slot>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                {{-- Kolom 1: Draf / Proses --}}
                <div class="flex flex-col rounded-xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-900/60">
                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-slate-400"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Draf / Proses</h3>
                        </div>
                        <span class="rounded-full bg-slate-200/80 px-2 py-0.5 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ $kanbanData['DRAF']->count() }}
                        </span>
                    </div>
                    <div class="mt-3 flex flex-1 flex-col gap-2.5">
                        @forelse($kanbanData['DRAF'] as $item)
                            <div class="group relative rounded-lg border border-slate-200 bg-white p-3 shadow-xs transition hover:border-slate-400 dark:border-slate-800 dark:bg-slate-950 dark:hover:border-slate-700">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="font-mono text-xs font-semibold text-slate-900 dark:text-white">{{ $item->nomor_mcu }}</span>
                                    <x-filament::badge color="gray" size="sm">Draf</x-filament::badge>
                                </div>
                                <div class="mt-1.5 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $item->pasien?->nama ?? 'Pasien Belum Ditautkan' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $item->nama_perusahaan ?? 'Pemeriksaan Mandiri' }}
                                </div>
                                <div class="mt-2.5 flex items-center justify-between border-t border-slate-100 pt-2 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <span>{{ $item->tanggal_periksa ? $item->tanggal_periksa->format('d/m/Y') : '-' }}</span>
                                    <a href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $item->id]) }}" class="font-semibold text-primary-600 hover:underline dark:text-primary-400">
                                        Lanjutkan &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400 dark:border-slate-800">
                                Tidak ada draf tertunda
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom 2: FIT --}}
                <div class="flex flex-col rounded-xl border border-emerald-200 bg-emerald-50/40 p-3 dark:border-emerald-950/60 dark:bg-emerald-950/20">
                    <div class="flex items-center justify-between border-b border-emerald-200/80 pb-2.5 dark:border-emerald-900/50">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-emerald-500"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">FIT (Laik Kerja)</h3>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">
                            {{ $kanbanData['FIT']->count() }}
                        </span>
                    </div>
                    <div class="mt-3 flex flex-1 flex-col gap-2.5">
                        @forelse($kanbanData['FIT'] as $item)
                            <div class="group relative rounded-lg border border-emerald-100 bg-white p-3 shadow-xs transition hover:border-emerald-300 dark:border-emerald-900/40 dark:bg-slate-950 dark:hover:border-emerald-700">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="font-mono text-xs font-semibold text-slate-900 dark:text-white">{{ $item->nomor_mcu }}</span>
                                    <x-filament::badge color="success" size="sm">FIT</x-filament::badge>
                                </div>
                                <div class="mt-1.5 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $item->pasien?->nama ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $item->nama_perusahaan ?? 'Perusahaan Umum' }}
                                </div>
                                <div class="mt-2.5 flex items-center justify-between border-t border-slate-100 pt-2 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <span>Exp: {{ $item->berlaku_sampai ? $item->berlaku_sampai->format('d/m/Y') : '-' }}</span>
                                    <div class="flex items-center gap-2">
                                        @if($item->token_verifikasi)
                                            <a href="{{ url('/v/' . $item->token_verifikasi) }}" target="_blank" class="font-medium text-emerald-600 hover:underline dark:text-emerald-400">
                                                QR
                                            </a>
                                        @endif
                                        <a href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $item->id]) }}" class="font-medium text-slate-600 hover:underline dark:text-slate-400">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-emerald-200 p-6 text-center text-xs text-emerald-600/70 dark:border-emerald-900/60">
                                Belum ada sertifikat FIT
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom 3: TEMPORARY UNFIT --}}
                <div class="flex flex-col rounded-xl border border-amber-200 bg-amber-50/40 p-3 dark:border-amber-950/60 dark:bg-amber-950/20">
                    <div class="flex items-center justify-between border-b border-amber-200/80 pb-2.5 dark:border-amber-900/50">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-amber-500"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Temporary Unfit</h3>
                        </div>
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-900/60 dark:text-amber-200">
                            {{ $kanbanData['TEMPORARY UNFIT']->count() }}
                        </span>
                    </div>
                    <div class="mt-3 flex flex-1 flex-col gap-2.5">
                        @forelse($kanbanData['TEMPORARY UNFIT'] as $item)
                            <div class="group relative rounded-lg border border-amber-100 bg-white p-3 shadow-xs transition hover:border-amber-300 dark:border-amber-900/40 dark:bg-slate-950 dark:hover:border-amber-700">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="font-mono text-xs font-semibold text-slate-900 dark:text-white">{{ $item->nomor_mcu }}</span>
                                    <x-filament::badge color="warning" size="sm">Temp Unfit</x-filament::badge>
                                </div>
                                <div class="mt-1.5 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $item->pasien?->nama ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $item->nama_perusahaan ?? '-' }}
                                </div>
                                <div class="mt-2.5 flex items-center justify-between border-t border-slate-100 pt-2 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <span>Tgl: {{ $item->tanggal_periksa ? $item->tanggal_periksa->format('d/m/Y') : '-' }}</span>
                                    <a href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $item->id]) }}" class="font-semibold text-amber-600 hover:underline dark:text-amber-400">
                                        Evaluasi &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-amber-200 p-6 text-center text-xs text-amber-600/70 dark:border-amber-900/60">
                                Tidak ada pasien evaluasi
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom 4: UNFIT / DICABUT --}}
                <div class="flex flex-col rounded-xl border border-rose-200 bg-rose-50/40 p-3 dark:border-rose-950/60 dark:bg-rose-950/20">
                    <div class="flex items-center justify-between border-b border-rose-200/80 pb-2.5 dark:border-rose-900/50">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full bg-rose-500"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-rose-800 dark:text-rose-300">UNFIT / Dicabut</h3>
                        </div>
                        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-semibold text-rose-800 dark:bg-rose-900/60 dark:text-rose-200">
                            {{ $kanbanData['UNFIT']->count() }}
                        </span>
                    </div>
                    <div class="mt-3 flex flex-1 flex-col gap-2.5">
                        @forelse($kanbanData['UNFIT'] as $item)
                            <div class="group relative rounded-lg border border-rose-100 bg-white p-3 shadow-xs transition hover:border-rose-300 dark:border-rose-900/40 dark:bg-slate-950 dark:hover:border-rose-700">
                                <div class="flex items-start justify-between gap-1">
                                    <span class="font-mono text-xs font-semibold text-slate-900 dark:text-white">{{ $item->nomor_mcu }}</span>
                                    <x-filament::badge color="danger" size="sm">UNFIT</x-filament::badge>
                                </div>
                                <div class="mt-1.5 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $item->pasien?->nama ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $item->nama_perusahaan ?? '-' }}
                                </div>
                                <div class="mt-2.5 flex items-center justify-between border-t border-slate-100 pt-2 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <span>Tgl: {{ $item->tanggal_periksa ? $item->tanggal_periksa->format('d/m/Y') : '-' }}</span>
                                    <a href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $item->id]) }}" class="font-semibold text-rose-600 hover:underline dark:text-rose-400">
                                        Tinjau &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-rose-200 p-6 text-center text-xs text-rose-600/70 dark:border-rose-900/60">
                                Tidak ada pasien UNFIT
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </x-filament::section>

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
                                            @endif
                                            <x-filament::button
                                                tag="a"
                                                href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $mcu->id]) }}"
                                                icon="heroicon-m-pencil-square"
                                                color="gray"
                                                size="xs"
                                            >
                                                Edit
                                            </x-filament::button>
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
