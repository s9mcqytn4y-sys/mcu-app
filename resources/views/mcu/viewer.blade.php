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
            --color-primary: #15803d; /* Vibrant Emerald Green for FIT */
            --color-danger: #dc2626; /* Crimson Red for UNFIT */
            --color-text-main: #1f2937;
            --color-text-muted: #6b7280;
            --color-border: #e5e7eb;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 24px 16px;
            background-color: #f3f4f6;
            color: var(--color-text-main);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .main-wrapper {
            width: 100%;
            max-width: 580px;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        /* Header Brand matching Reference */
        .header-brand {
            padding: 32px 24px 16px;
            text-align: center;
        }

        .header-brand img {
            max-width: 250px;
            height: auto;
            display: inline-block;
        }

        .content-body {
            padding: 16px 28px 32px;
        }

        /* Metadata Table matching Screenshot layout */
        .table-meta {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 14px;
            font-size: 0.95rem;
        }

        .table-meta td {
            vertical-align: top;
            padding: 0;
        }

        .table-meta td.label-col {
            width: 44%;
        }

        .table-meta td.colon-col {
            width: 6%;
            text-align: center;
            font-weight: 600;
            color: var(--color-text-main);
        }

        .table-meta td.value-col {
            width: 50%;
            font-weight: 700;
            color: var(--color-text-main);
            word-break: break-word;
        }

        .label-id {
            display: block;
            font-weight: 700;
            color: var(--color-text-main);
            line-height: 1.25;
        }

        .label-en {
            display: block;
            font-size: 0.825rem;
            font-style: italic;
            color: var(--color-text-muted);
            line-height: 1.25;
            margin-top: 2px;
        }

        /* Result Statement */
        .result-section {
            text-align: center;
            margin: 28px 0 20px;
        }

        .result-statement {
            font-size: 0.95rem;
            color: var(--color-text-main);
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .result-statement strong {
            font-weight: 700;
        }

        .result-statement em {
            display: block;
            font-style: italic;
            color: var(--color-text-muted);
            margin-top: 4px;
        }

        /* Capsule / Pill Status Badge matching Screenshot */
        .badge-status-container {
            display: flex;
            justify-content: center;
            margin-bottom: 26px;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 130px;
            padding: 10px 36px;
            border-radius: 9999px;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: #ffffff;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .badge-fit {
            background-color: var(--color-primary);
        }

        .badge-unfit {
            background-color: var(--color-danger);
        }

        /* Validity block */
        .validity-block {
            text-align: center;
            font-size: 0.925rem;
            color: var(--color-text-main);
            margin-top: 10px;
        }

        .validity-block strong {
            font-weight: 700;
        }

        .validity-block em {
            font-style: italic;
            color: var(--color-text-muted);
        }

        .validity-date {
            display: block;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--color-text-main);
            margin-top: 4px;
        }

        /* Stacked Attachment Section (Section 2 Stack) */
        .stacked-section {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 2px dashed var(--color-border);
        }

        .stacked-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stacked-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--color-text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stacked-btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-attachment-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            min-width: 44px;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-attachment-action:focus-visible {
            outline: 2px solid #0284c7;
            outline-offset: 2px;
        }

        .btn-open-full {
            background-color: #0284c7;
            color: #ffffff;
        }

        .btn-open-full:hover {
            background-color: #0369a1;
        }

        .btn-download {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid var(--color-border);
        }

        .btn-download:hover {
            background-color: #e2e8f0;
        }

        .attachment-embed-wrapper {
            background-color: #f8fafc;
            border: 1px solid var(--color-border);
            border-radius: 12px;
            overflow: hidden;
            padding: 8px;
            display: flex;
            justify-content: center;
        }

        .attachment-iframe {
            width: 100%;
            height: 750px;
            border: none;
            border-radius: 8px;
            background: #ffffff;
        }

        .attachment-img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        /* Footer */
        .footer-compliance {
            padding: 20px 24px;
            text-align: center;
            font-size: 0.775rem;
            color: var(--color-text-muted);
            border-top: 1px solid var(--color-border);
            background-color: #fafafa;
        }

        .footer-compliance p {
            margin: 4px 0;
        }

        @media (max-width: 480px) {
            body {
                padding: 12px 8px;
            }

            .content-body {
                padding: 12px 18px 24px;
            }

            .table-meta {
                border-spacing: 0 10px;
            }

            .attachment-iframe {
                height: 520px;
            }
        }
    </style>
</head>
<body>

<main class="main-wrapper">
    <!-- Header Brand: Logo Otentik Klinik Indosehat 2003 Group -->
    <header class="header-brand">
        <img 
            src="{{ asset('images/logo/logo-indosehat-horizontal.png') }}" 
            alt="INDOSEHAT 2003 GROUP CLINIC & MEDICAL CHECK-UP"
            onerror="this.onerror=null; this.src='{{ asset('images/logo/logo-klinik-indosehat-2003.png') }}';"
        >
    </header>

    <!-- Content Body: Exact Field Order & Bilingual Format matching reference -->
    <section class="content-body" aria-label="Informasi Pasien dan Sertifikat MCU">
        <table class="table-meta" role="table">
            <tbody>
                <!-- 1. Nama / Name -->
                <tr>
                    <td class="label-col">
                        <span class="label-id">Nama</span>
                        <span class="label-en">Name</span>
                    </td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ strtoupper($pemeriksaan->pasien->nama ?? '-') }}</td>
                </tr>

                <!-- 2. Jenis Kelamin / Gender -->
                <tr>
                    <td class="label-col">
                        <span class="label-id">Jenis Kelamin</span>
                        <span class="label-en">Gender</span>
                    </td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        @php
                            $jk = $pemeriksaan->pasien->jenis_kelamin ?? '';
                            $jkFormatted = match(strtoupper((string) $jk)) {
                                'L', 'LAKI-LAKI', 'MALE' => 'PRIA/MALE',
                                'P', 'PEREMPUAN', 'FEMALE' => 'WANITA/FEMALE',
                                default => strtoupper((string) ($jk ?: '-'))
                            };
                        @endphp
                        {{ $jkFormatted }}
                    </td>
                </tr>

                <!-- 3. Tanggal Lahir / Date of Birth -->
                <tr>
                    <td class="label-col">
                        <span class="label-id">Tanggal Lahir</span>
                        <span class="label-en">Date of Birth</span>
                    </td>
                    <td class="colon-col">:</td>
                    <td class="value-col">
                        {{ $pemeriksaan->pasien->tanggal_lahir ? $pemeriksaan->pasien->tanggal_lahir->format('d F Y') : '-' }}
                    </td>
                </tr>

                <!-- 4. KTP / ID Number (Unmasked as per design) -->
                <tr>
                    <td class="label-col">
                        <span class="label-id">KTP</span>
                        <span class="label-en">ID Number</span>
                    </td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $pemeriksaan->pasien->no_ktp ?? '-' }}</td>
                </tr>

                <!-- 5. Paspor / Passport (Unmasked as per design) -->
                <tr>
                    <td class="label-col">
                        <span class="label-id">Paspor</span>
                        <span class="label-en">Passport</span>
                    </td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $pemeriksaan->pasien->no_paspor ?? '-' }}</td>
                </tr>

                <!-- 6. Perusahaan / Company -->
                <tr>
                    <td class="label-col">
                        <span class="label-id">Perusahaan</span>
                        <span class="label-en">Company</span>
                    </td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $pemeriksaan->nama_perusahaan ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Result Statement matching reference -->
        <article class="result-section" aria-label="Hasil Pemeriksaan MCU">
            @php
                $tglPeriksa = $pemeriksaan->tanggal_periksa ? $pemeriksaan->tanggal_periksa->format('d F Y') : ($pemeriksaan->created_at ? $pemeriksaan->created_at->format('d F Y') : '-');
                $statusUpper = strtoupper((string) ($pemeriksaan->status_kelayakan ?? 'FIT'));
                $isFit = str_starts_with($statusUpper, 'FIT');
            @endphp
            <div class="result-statement">
                Telah melakukan <strong>Medical Check Up</strong> pada <strong>{{ $tglPeriksa }}</strong>, dengan hasil :<br>
                <em>Has carried out the <strong>Medical Check Up</strong> on <strong>{{ $tglPeriksa }}</strong>, with the results :</em>
            </div>

            <div class="badge-status-container">
                <span class="badge-status {{ $isFit ? 'badge-fit' : 'badge-unfit' }}">
                    {{ $statusUpper }}
                </span>
            </div>

            <aside class="validity-block" aria-label="Masa Berlaku">
                <strong>Berlaku sampai dengan tanggal</strong> / <em>valid until the date</em>
                <time class="validity-date">
                    {{ $pemeriksaan->berlaku_sampai ? $pemeriksaan->berlaku_sampai->format('F d, Y') : '-' }}
                </time>
            </aside>
        </article>

        <!-- Section 2: Lampiran File (Dibatasi Akses Petugas Terautentikasi / UU PDP) -->
        @if($pemeriksaan->lampiranMcu && $pemeriksaan->lampiranMcu->isNotEmpty())
            @auth
                @foreach($pemeriksaan->lampiranMcu as $index => $lampiran)
                    @php
                        $isPdf = str_ends_with(strtolower((string) $lampiran->path_berkas), '.pdf');
                        $fileUrl = route('mcu.file.view', ['uuid' => $pemeriksaan->token_verifikasi, 'index' => $index]);
                        $downloadUrl = route('mcu.file.download', ['uuid' => $pemeriksaan->token_verifikasi, 'index' => $index]);
                    @endphp
                    <section class="stacked-section" aria-label="Dokumen Fisik Lampiran MCU">
                        <div class="stacked-header">
                            <span class="stacked-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                Lampiran Hasil Pemeriksaan Asli
                            </span>
                            <div class="stacked-btn-group">
                                <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="btn-attachment-action btn-open-full">
                                    Buka Berkas Penuh ↗
                                </a>
                                <a href="{{ $downloadUrl }}" class="btn-attachment-action btn-download">
                                    Unduh Dokumen ⤓
                                </a>
                            </div>
                        </div>

                        <div class="attachment-embed-wrapper">
                            @if($isPdf)
                                <iframe 
                                    src="{{ $fileUrl }}#toolbar=0" 
                                    class="attachment-iframe" 
                                    title="Hasil Cetak PDF MCU"
                                    loading="lazy"
                                ></iframe>
                            @else
                                <img 
                                    src="{{ $fileUrl }}" 
                                    alt="Foto Dokumen Hasil Cetak MCU" 
                                    class="attachment-img"
                                    loading="lazy"
                                >
                            @endif
                        </div>
                    </section>
                @endforeach
            @else
                <section class="stacked-section" aria-label="Proteksi Data Medis">
                    <div style="background-color: var(--color-bg-base); border: 1px solid var(--color-border); border-radius: 8px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-primary); flex-shrink: 0; margin-top: 2px;" aria-hidden="true">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <div style="font-size: 0.875rem; line-height: 1.5; color: var(--color-text-main);">
                            <strong style="display: block; font-weight: 700; margin-bottom: 4px; color: var(--color-text-main);">
                                Perlindungan Dokumen Rekam Medis (UU PDP No. 27/2022)
                            </strong>
                            <span>
                                Terdapat <strong>{{ $pemeriksaan->lampiranMcu->count() }} berkas lampiran</strong> hasil pemeriksaan medis laboratorium/radiologi. Demi melindungi kerahasiaan data kesehatan pasien, akses fisik berkas dibatasi hanya untuk petugas medis dan instansi berwenang yang telah terotentikasi.
                            </span>
                        </div>
                    </div>
                </section>
            @endauth
        @endif
    </section>

    <!-- Footer Otentikasi -->
    <footer class="footer-compliance">
        <p><strong>Klinik Indosehat 2003 Group</strong> &bull; Medical Check-Up Verification System</p>
        <p>Dokumen ini diterbitkan secara sah dan dilindungi hak cipta &copy; {{ date('Y') }}.</p>
    </footer>
</main>

</body>
</html>
