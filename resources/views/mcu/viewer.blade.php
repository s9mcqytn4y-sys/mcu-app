<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Tag Meta Utama -->
    <title>Verifikasi Sertifikat MCU | Klinik Indosehat 2003</title>
    <meta name="title" content="Verifikasi Sertifikat MCU | Klinik Indosehat 2003">
    <meta name="description" content="Portal Verifikasi Resmi Sertifikat Medical Check-Up Klinik Indosehat 2003.">
    
    <!-- Privasi & Penolakan Indeks Mesin Pencari -->
    <meta name="robots" content="noindex, nofollow, noarchive">
    
    <!-- Open Graph / WhatsApp / Facebook (Data Minimization) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Verifikasi Sertifikat MCU | Klinik Indosehat 2003">
    <meta property="og:description" content="Portal Verifikasi Resmi Sertifikat Medical Check-Up Klinik Indosehat 2003.">
    <meta property="og:image" content="{{ asset('images/logo/logo-klinik-indosehat-2003.png') }}">
    <meta property="og:site_name" content="Klinik Indosehat 2003">

    <!-- Twitter Card -->
    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="Verifikasi Sertifikat MCU | Klinik Indosehat 2003">
    <meta property="twitter:description" content="Portal Verifikasi Resmi Sertifikat Medical Check-Up Klinik Indosehat 2003.">
    <meta property="twitter:image" content="{{ asset('images/logo/logo-klinik-indosehat-2003.png') }}">

    <!-- Favicon & Icons Otentik -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Tipografi Modern -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #287B85; /* Muted Clinical Teal */
            --color-primary-dark: #16364A; /* Dark Slate */
            --color-success: #15803D; /* Medical Green */
            --color-danger: #B45343; /* Alert Crimson */
            --color-warning: #D97706;
            --color-neutral-900: #0F172A;
            --color-neutral-700: #334155;
            --color-neutral-500: #64748B;
            --color-neutral-200: #E2E8F0;
            --color-neutral-50: #F8FAFC;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            color: var(--color-neutral-900);
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .main-wrapper {
            width: 100%;
            max-width: 640px;
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .header-brand {
            padding: 28px 24px 20px;
            text-align: center;
            border-bottom: 1px solid var(--color-neutral-200);
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }

        .header-brand img {
            max-width: 260px;
            height: auto;
            display: inline-block;
        }

        .page-title {
            margin: 14px 0 4px;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-primary-dark);
            letter-spacing: -0.01em;
        }

        .page-subtitle {
            margin: 0;
            font-size: 0.85rem;
            color: var(--color-neutral-500);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }

        .content-body {
            padding: 24px;
            flex: 1 0 auto;
        }

        .info-card {
            background-color: var(--color-neutral-50);
            border: 1px solid var(--color-neutral-200);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 24px;
        }

        .table-meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.925rem;
        }

        .table-meta tr:not(:last-child) {
            border-bottom: 1px dashed var(--color-neutral-200);
        }

        .table-meta td {
            padding: 10px 0;
            vertical-align: top;
        }

        .table-meta td.label-col {
            width: 44%;
        }

        .table-meta td.colon-col {
            width: 6%;
            text-align: center;
            color: var(--color-neutral-500);
        }

        .table-meta td.value-col {
            width: 50%;
            font-weight: 600;
            color: var(--color-neutral-900);
        }

        .label-id {
            display: block;
            font-weight: 600;
            color: var(--color-neutral-900);
            line-height: 1.3;
        }

        .label-en {
            display: block;
            font-size: 0.8rem;
            font-style: italic;
            color: var(--color-neutral-500);
            line-height: 1.3;
        }

        .result-section {
            text-align: center;
            margin: 28px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .result-statement {
            font-size: 0.925rem;
            color: var(--color-neutral-700);
            line-height: 1.55;
            margin-bottom: 16px;
        }

        .badge-status {
            display: inline-block;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            padding: 10px 32px;
            border-radius: 9999px;
            color: #ffffff;
            text-transform: uppercase;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .badge-fit {
            background-color: var(--color-success);
        }

        .badge-unfit {
            background-color: var(--color-danger);
        }

        .badge-custom {
            background-color: var(--color-primary);
        }

        .attachments-section {
            margin-top: 32px;
            text-align: center;
        }

        .attachments-desc {
            font-size: 0.9rem;
            color: var(--color-neutral-700);
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .btn-action-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }

        .btn-attachment {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            max-width: 340px;
            min-height: 48px;
            padding: 12px 24px;
            border-radius: 9999px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            color: #ffffff;
            background-color: var(--color-primary);
            box-shadow: 0 4px 10px -2px rgba(40, 123, 133, 0.3);
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn-attachment:hover {
            background-color: var(--color-primary-dark);
            transform: translateY(-1px);
        }

        .btn-attachment:focus-visible {
            outline: 2px solid var(--color-primary-dark);
            outline-offset: 2px;
        }

        .btn-preview {
            background-color: var(--color-primary-dark);
            box-shadow: 0 4px 10px -2px rgba(22, 54, 74, 0.3);
        }

        .btn-preview:hover {
            background-color: #0f2735;
        }

        /* Responsivitas Mobile: Vertical Stack untuk Label-Value (<640px) */
        @media (max-width: 639px) {
            .table-meta,
            .table-meta tbody,
            .table-meta tr,
            .table-meta td {
                display: block;
                width: 100% !important;
            }

            .table-meta tr {
                padding: 12px 0;
            }

            .table-meta td.colon-col {
                display: none;
            }

            .table-meta td.label-col {
                padding: 0 0 4px 0;
            }

            .table-meta td.value-col {
                padding: 0 0 4px 0;
                font-size: 1.05rem;
                font-weight: 700;
                color: var(--color-neutral-900);
            }

            .content-body {
                padding: 16px;
            }

            .header-brand {
                padding: 20px 16px 16px;
            }

            .result-section {
                padding: 16px 12px;
            }

            .btn-attachment {
                max-width: 100%;
            }
        }

        .validity-box {
            margin-top: 36px;
            padding: 16px;
            background-color: #f1f5f9;
            border-radius: 8px;
            text-align: center;
            font-size: 0.875rem;
            color: var(--color-neutral-700);
        }

        .validity-box strong {
            color: var(--color-neutral-900);
            display: block;
            margin-top: 4px;
            font-size: 1rem;
        }

        .footer-compliance {
            padding: 24px;
            text-align: center;
            font-size: 0.775rem;
            color: var(--color-neutral-500);
            border-top: 1px solid var(--color-neutral-200);
            background-color: #fcfcfc;
        }

        .footer-compliance p {
            margin: 4px 0;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<main class="main-wrapper">
    <!-- Header Identitas Fasilitas Kesehatan -->
    <header class="header-brand">
        <img 
            src="{{ asset('images/logo/logo-indosehat-horizontal.png') }}" 
            alt="Klinik Indosehat 2003"
            onerror="this.onerror=null; this.src='{{ asset('images/logo/logo-klinik-indosehat-2003.png') }}';"
        >
        <h1 class="page-title">Sertifikat Hasil Medical Check-Up</h1>
        <p class="page-subtitle">Electronic Verification System</p>
    </header>

    <!-- Badan Data Rekam Pemeriksaan -->
    <section class="content-body" aria-label="Informasi Pasien dan Pemeriksaan">
        <div class="info-card">
            <table class="table-meta" role="table">
                <tbody>
                    <tr>
                        <td class="label-col">
                            <span class="label-id">Nama Lengkap</span>
                            <span class="label-en">Full Name</span>
                        </td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ strtoupper($pemeriksaan->pasien->nama ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">
                            <span class="label-id">Nomor MCU</span>
                            <span class="label-en">MCU Number</span>
                        </td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ $pemeriksaan->nomor_mcu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">
                            <span class="label-id">Jenis Kelamin</span>
                            <span class="label-en">Gender</span>
                        </td>
                        <td class="colon-col">:</td>
                        <td class="value-col">
                            @php
                                $jk = $pemeriksaan->pasien->jenis_kelamin ?? '';
                                $jkLabel = match(strtoupper((string) $jk)) {
                                    'L', 'LAKI-LAKI', 'MALE' => 'LAKI-LAKI / MALE',
                                    'P', 'PEREMPUAN', 'FEMALE' => 'PEREMPUAN / FEMALE',
                                    default => strtoupper((string) ($jk ?: '-'))
                                };
                            @endphp
                            {{ $jkLabel }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">
                            <span class="label-id">Tahun Lahir</span>
                            <span class="label-en">Year of Birth</span>
                        </td>
                        <td class="colon-col">:</td>
                        <td class="value-col">
                            @if($pemeriksaan->pasien->tanggal_lahir)
                                <time datetime="{{ $pemeriksaan->pasien->tanggal_lahir->format('Y') }}">
                                    Tahun {{ $pemeriksaan->pasien->tanggal_lahir->format('Y') }}
                                </time>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">
                            <span class="label-id">Nomor KTP (NIK)</span>
                            <span class="label-en">ID Card Number</span>
                        </td>
                        <td class="colon-col">:</td>
                        <td class="value-col">
                            @php
                                $rawKtp = (string) ($pemeriksaan->pasien->no_ktp ?? '');
                                if (strlen($rawKtp) >= 10) {
                                    $maskedKtp = substr($rawKtp, 0, 6) . str_repeat('*', max(0, strlen($rawKtp) - 10)) . substr($rawKtp, -4);
                                } elseif (strlen($rawKtp) > 4) {
                                    $maskedKtp = substr($rawKtp, 0, 2) . str_repeat('*', strlen($rawKtp) - 4) . substr($rawKtp, -2);
                                } else {
                                    $maskedKtp = $rawKtp ?: '-';
                                }
                            @endphp
                            {{ $maskedKtp }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">
                            <span class="label-id">Nomor Paspor</span>
                            <span class="label-en">Passport Number</span>
                        </td>
                        <td class="colon-col">:</td>
                        <td class="value-col">
                            @php
                                $rawPaspor = (string) ($pemeriksaan->pasien->no_paspor ?? '');
                                if (strlen($rawPaspor) >= 4) {
                                    $maskedPaspor = substr($rawPaspor, 0, 3) . str_repeat('*', strlen($rawPaspor) - 3);
                                } else {
                                    $maskedPaspor = $rawPaspor ?: '-';
                                }
                            @endphp
                            {{ $maskedPaspor }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bagian Hasil Pemeriksaan -->
        <article class="result-section" aria-label="Hasil Pemeriksaan MCU">
            <div class="result-statement">
                Telah melakukan <strong>Medical Check Up</strong> pada tanggal
                <strong>
                    @if($pemeriksaan->tanggal_pemeriksaan)
                        <time datetime="{{ $pemeriksaan->tanggal_pemeriksaan->format('Y-m-d') }}">
                            {{ $pemeriksaan->tanggal_pemeriksaan->format('d F Y') }}
                        </time>
                    @else
                        -
                    @endif
                </strong>, dengan hasil:<br>
                <em>Has completed the <strong>Medical Check Up</strong> on
                    <strong>{{ $pemeriksaan->tanggal_pemeriksaan ? $pemeriksaan->tanggal_pemeriksaan->format('d F Y') : '-' }}</strong>, with the results:</em>
            </div>

            @php
                $statusUpper = strtoupper((string) ($pemeriksaan->status_kelayakan ?? ''));
                $badgeClass = match($statusUpper) {
                    'FIT', 'FIT FOR SEA DUTY' => 'badge-fit',
                    'UNFIT' => 'badge-unfit',
                    default => 'badge-custom'
                };
            @endphp

            @if($pemeriksaan->status_kelayakan)
                <div class="badge-status {{ $badgeClass }}">
                    {{ $statusUpper }}
                </div>
            @else
                <div class="badge-status" style="background-color: var(--color-neutral-500);">-</div>
            @endif
        </article>

        <!-- Bagian Status Berkas Lampiran & Proteksi Rekam Medis (UU PDP & Permenkes) -->
        <section class="attachments-section" aria-label="Keamanan Dokumen Hasil MCU">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 20px; text-align: left;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#007F94" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    <span style="font-weight: 700; font-size: 0.9rem; color: #16364A;">Arsip Rekam Medis Terproteksi</span>
                </div>
                <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.5;">
                    Sesuai UU PDP No. 27/2022 dan Permenkes No. 24/2022 tentang Rekam Medis, berkas fisik hasil pemeriksaan disimpan dalam repositori terenkripsi dan hanya dapat diakses oleh petugas medis berwenang.
                </p>
            </div>

            @auth
                @if($pemeriksaan->lampiranMcu && $pemeriksaan->lampiranMcu->isNotEmpty())
                    <div class="btn-action-group">
                        @foreach($pemeriksaan->lampiranMcu as $index => $lampiran)
                            @php
                                $namaLabel = $pemeriksaan->lampiranMcu->count() > 1 ? ' (' . ($index + 1) . ')' : '';
                            @endphp
                            <a 
                                href="{{ route('mcu.file.view', ['uuid' => $pemeriksaan->token_verifikasi, 'index' => $index]) }}" 
                                class="btn-attachment btn-preview"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Akses internal berkas MCU lampiran {{ $index + 1 }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                AKSES BERKAS PETUGAS{{ $namaLabel }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endauth
        </section>

        <!-- Masa Berlaku Dokumen -->
        <aside class="validity-box" aria-label="Masa Berlaku Sertifikat">
            <span>Berlaku sampai dengan tanggal / <em>Valid until the date</em>:</span>
            <strong>
                @if($pemeriksaan->tanggal_kedaluwarsa)
                    <time datetime="{{ $pemeriksaan->tanggal_kedaluwarsa->format('Y-m-d') }}">
                        {{ $pemeriksaan->tanggal_kedaluwarsa->format('d F Y') }}
                    </time>
                @else
                    -
                @endif
            </strong>
        </aside>
    </section>

    <!-- Footer Kepatuhan & Keamanan Sistem -->
    <footer class="footer-compliance">
        <p><strong>Klinik Indosehat 2003</strong> &bull; Medical Check-Up Verification Gateway</p>
        <p>Dokumen ini adalah data hasil verifikasi elektronik resmi. Segala bentuk pemalsuan dapat ditindak sesuai peraturan perundang-undangan.</p>
        <p>&copy; {{ date('Y') }} Klinik Indosehat 2003. Hak Cipta Dilindungi.</p>
    </footer>
</main>

</body>
</html>
