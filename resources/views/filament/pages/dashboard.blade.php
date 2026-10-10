<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Banner Operasional & Quick Action Utama --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-800 dark:bg-teal-950/60 dark:text-teal-300">
                        <span class="inline-block size-2 rounded-full bg-teal-500"></span>
                        Portal Operasional Klinik Indosehat 2003
                    </div>
                    <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Sistem Penerbitan & Verifikasi Hasil Medical Check-Up
                    </h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        Kelola data pasien pelaut/tenaga kerja, validasi kelayakan medis maritim, dan terbitkan sertifikat ber-QR terverifikasi.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        href="{{ $this->getUrlPemeriksaanBaru() }}"
                        class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 dark:bg-teal-600 dark:hover:bg-teal-500"
                    >
                        <x-heroicon-m-plus-circle class="size-5" />
                        Pemeriksaan Baru
                    </a>
                    <a
                        href="{{ $this->getUrlDaftarPasien() }}"
                        class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                    >
                        <x-heroicon-m-users class="size-5 text-slate-500 dark:text-slate-400" />
                        Cari Pasien
                    </a>
                </div>
            </div>
        </div>

        {{-- Grid Metrik Klinis (KPI Cards) --}}
        @php
            $statKelayakan = $this->getStatistikKelayakan();
            $hariIniCount = $this->getPemeriksaanHariIniCount();
            $totalPasien = $this->getTotalPasienCount();
            $totalMcu = $this->getTotalMcuCount();
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Kartu 1: Pemeriksaan Hari Ini --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Hari Ini</span>
                    <span class="rounded-full bg-teal-50 p-2 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300">
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
            </div>

            {{-- Kartu 2: Kelayakan Kerja --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rasio Kelayakan</span>
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
                        <span>UNFIT: <strong>{{ $statKelayakan['unfit'] }}</strong> pasien</span>
                    </div>
                </div>
            </div>

            {{-- Kartu 3: Total Pasien --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rekam Pasien</span>
                    <span class="rounded-full bg-blue-50 p-2 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                        <x-heroicon-o-identification class="size-5" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($totalPasien) }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Total rekam identitas terdaftar di sistem
                    </div>
                </div>
            </div>

            {{-- Kartu 4: Integritas & Total Arsip --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
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
                    <div class="mt-1 flex items-center gap-1 text-xs font-medium text-teal-700 dark:text-teal-400">
                        <span class="inline-block size-1.5 rounded-full bg-teal-500"></span>
                        Storage Privat & Audit Aktif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Aktivitas Pemeriksaan Terkini --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        Sertifikasi MCU Terkini
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Daftar 8 pemeriksaan medis yang terakhir didaftarkan atau diperbarui.
                    </p>
                </div>
                <a
                    href="{{ $this->getUrlDaftarPemeriksaan() }}"
                    class="text-xs font-semibold text-teal-700 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300"
                >
                    Lihat Semua Arsip &rarr;
                </a>
            </div>

            @php
                $pemeriksaanList = $this->getPemeriksaanTerbaru();
            @endphp

            @if($pemeriksaanList->isEmpty())
                <div class="px-6 py-12 text-center">
                    <x-heroicon-o-document-text class="mx-auto size-12 text-slate-300 dark:text-slate-600" />
                    <h4 class="mt-3 text-sm font-semibold text-slate-800 dark:text-slate-200">
                        Belum Ada Data Pemeriksaan
                    </h4>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Mulai daftarkan pemeriksaan MCU pertama untuk menerbitkan sertifikat dan QR verifikasi resmi.
                    </p>
                    <div class="mt-4">
                        <a
                            href="{{ $this->getUrlPemeriksaanBaru() }}"
                            class="inline-flex min-h-[44px] items-center gap-2 rounded-lg bg-teal-700 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-500"
                        >
                            <x-heroicon-m-plus class="size-4" />
                            Tambah Pemeriksaan Sekarang
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm" role="table">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nomor MCU</th>
                                <th scope="col" class="px-6 py-3">Pasien</th>
                                <th scope="col" class="px-6 py-3">Tanggal Periksa</th>
                                <th scope="col" class="px-6 py-3">Masa Berlaku</th>
                                <th scope="col" class="px-6 py-3">Kelayakan</th>
                                <th scope="col" class="px-6 py-3 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($pemeriksaanList as $mcu)
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $mcu->nomor_mcu }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $mcu->pasien->nama ?? '-' }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $mcu->pasien->no_ktp ? 'NIK: ' . $mcu->pasien->no_ktp : ($mcu->pasien->no_paspor ? 'Paspor: ' . $mcu->pasien->no_paspor : '-') }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600 dark:text-slate-300">
                                        {{ $mcu->tanggal_periksa ? $mcu->tanggal_periksa->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600 dark:text-slate-300">
                                        {{ $mcu->berlaku_sampai ? $mcu->berlaku_sampai->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($mcu->status_kelayakan === 'FIT')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                FIT
                                            </span>
                                        @elseif($mcu->status_kelayakan === 'UNFIT')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                                <span class="size-1.5 rounded-full bg-rose-500"></span>
                                                UNFIT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                {{ $mcu->status_kelayakan ?? '-' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            @if($mcu->token_verifikasi)
                                                <a
                                                    href="{{ url('/v/' . $mcu->token_verifikasi) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex min-h-[36px] items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                                    title="Lihat Verifikasi Publik"
                                                >
                                                    <x-heroicon-m-qr-code class="size-4 text-slate-500" />
                                                    QR
                                                </a>
                                            @endif
                                            <a
                                                href="{{ \App\Filament\Resources\PemeriksaanMcuResource::getUrl('edit', ['record' => $mcu->id]) }}"
                                                class="inline-flex min-h-[36px] items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                                            >
                                                <x-heroicon-m-pencil-square class="size-4 text-slate-500" />
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
