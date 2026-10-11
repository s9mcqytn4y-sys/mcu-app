<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat MCU - {{ $pemeriksaan->nomor_mcu }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: a4 portrait;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header-logo {
            width: 130px;
            vertical-align: middle;
        }

        .header-text {
            text-align: right;
            vertical-align: middle;
        }

        .clinic-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }

        .clinic-sub {
            font-size: 8.5pt;
            color: #4b5563;
            margin: 2px 0 0;
        }

        .title-section {
            text-align: center;
            margin-bottom: 22px;
        }

        .cert-title-id {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #111827;
            margin: 0;
        }

        .cert-title-en {
            font-size: 10pt;
            font-style: italic;
            color: #6b7280;
            margin: 2px 0 6px;
        }

        .cert-nomor {
            font-size: 10pt;
            font-weight: bold;
            color: #2563eb;
            background: #eff6ff;
            display: inline-block;
            padding: 4px 14px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
        }

        .section-label {
            font-size: 10pt;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table td {
            padding: 5px 4px;
            vertical-align: top;
            font-size: 9.5pt;
        }

        .data-table td.col-label {
            width: 28%;
            color: #4b5563;
        }

        .data-table td.col-colon {
            width: 3%;
            text-align: center;
            color: #9ca3af;
        }

        .data-table td.col-value {
            width: 69%;
            color: #111827;
            font-weight: 500;
        }

        .label-en {
            display: block;
            font-size: 8pt;
            font-style: italic;
            color: #9ca3af;
        }

        .status-box {
            margin: 18px 0;
            padding: 14px;
            border-radius: 6px;
            text-align: center;
        }

        .status-fit {
            background-color: #f0fdf4;
            border: 2px solid #16a34a;
            color: #15803d;
        }

        .status-unfit {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            color: #b91c1c;
        }

        .status-heading {
            font-size: 10pt;
            color: #374151;
            margin-bottom: 6px;
        }

        .status-badge {
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .validity-text {
            margin-top: 8px;
            font-size: 9pt;
            color: #4b5563;
        }

        .footer-table {
            width: 100%;
            margin-top: 25px;
            border-top: 1px dashed #d1d5db;
            padding-top: 15px;
        }

        .qr-td {
            width: 35%;
            vertical-align: middle;
            text-align: left;
        }

        .signature-td {
            width: 65%;
            vertical-align: middle;
            text-align: right;
            font-size: 8.5pt;
            color: #4b5563;
        }

        .legal-notice {
            font-size: 7.5pt;
            color: #6b7280;
            line-height: 1.3;
            margin-top: 6px;
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- Header Lembaga -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(file_exists(public_path('logo-klinik-indosehat-2003.png')))
                    <img src="{{ public_path('logo-klinik-indosehat-2003.png') }}" style="max-width: 120px; height: auto;" alt="Logo Indosehat">
                @elseif(file_exists(public_path('images/logo/logo-indosehat-horizontal.png')))
                    <img src="{{ public_path('images/logo/logo-indosehat-horizontal.png') }}" style="max-width: 120px; height: auto;" alt="Logo Indosehat">
                @else
                    <strong style="color: #1e3a8a; font-size: 14pt;">INDOSEHAT 2003</strong>
                @endif
            </td>
            <td class="header-text">
                <h1 class="clinic-name">KLINIK INDOSEHAT 2003</h1>
                <p class="clinic-sub">
                    Pusat Pelayanan Medical Check Up Standar Maritim & Ketenagakerjaan<br>
                    Sertifikasi Resmi Kesehatan Kerja &bull; Portal Verifikasi Dokumen Digital
                </p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="title-section">
        <h2 class="cert-title-id">Surat Keterangan Pemeriksaan Kesehatan</h2>
        <div class="cert-title-en">Medical Examination Certificate</div>
        <div class="cert-nomor">NOMOR: {{ $pemeriksaan->nomor_mcu }}</div>
    </div>

    <!-- Data Pasien -->
    <div class="section-label">I. Identitas Pasien / Patient Identity</div>
    <table class="data-table">
        <tr>
            <td class="col-label">
                Nama Lengkap
                <span class="label-en">Full Name</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value"><strong>{{ strtoupper($pemeriksaan->pasien->nama) }}</strong></td>
        </tr>
        <tr>
            <td class="col-label">
                Jenis Kelamin
                <span class="label-en">Gender</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">
                @php
                    $jk = strtoupper((string) ($pemeriksaan->pasien->jenis_kelamin ?? ''));
                    $jkStr = match($jk) {
                        'L', 'LAKI-LAKI', 'MALE' => 'Laki-laki / Male',
                        'P', 'PEREMPUAN', 'FEMALE' => 'Perempuan / Female',
                        default => $jk ?: '-'
                    };
                @endphp
                {{ $jkStr }}
            </td>
        </tr>
        <tr>
            <td class="col-label">
                Tanggal Lahir
                <span class="label-en">Date of Birth</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">
                {{ $pemeriksaan->pasien->tanggal_lahir ? $pemeriksaan->pasien->tanggal_lahir->format('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="col-label">
                No. KTP / NIK
                <span class="label-en">National ID</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $pemeriksaan->pasien->no_ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">
                No. Paspor
                <span class="label-en">Passport Number</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $pemeriksaan->pasien->no_paspor ?? '-' }}</td>
        </tr>
    </table>

    <!-- Data Pemeriksaan -->
    <div class="section-label">II. Rincian Pemeriksaan / Examination Details</div>
    <table class="data-table">
        <tr>
            <td class="col-label">
                Perusahaan
                <span class="label-en">Company</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">{{ $pemeriksaan->nama_perusahaan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">
                Wilayah Operasi / Posisi
                <span class="label-en">Operation Area / Rank</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">
                {{ $pemeriksaan->wilayah_operasi ?? '-' }} 
                @if($pemeriksaan->departemen) &bull; Dept: {{ $pemeriksaan->departemen }} @endif
                @if($pemeriksaan->jenis_kapal) &bull; Kapal: {{ $pemeriksaan->jenis_kapal }} @endif
            </td>
        </tr>
        <tr>
            <td class="col-label">
                Tanggal Periksa
                <span class="label-en">Date of Examination</span>
            </td>
            <td class="col-colon">:</td>
            <td class="col-value">
                {{ $pemeriksaan->tanggal_periksa ? $pemeriksaan->tanggal_periksa->format('d F Y') : '-' }}
            </td>
        </tr>
    </table>

    <!-- Kesimpulan Status Kelayakan -->
    @php
        $statusUpper = strtoupper((string) ($pemeriksaan->status_kelayakan ?? 'FIT'));
        $isFit = str_starts_with($statusUpper, 'FIT');
    @endphp
    <div class="status-box {{ $isFit ? 'status-fit' : 'status-unfit' }}">
        <div class="status-heading">KESIMPULAN KELAYAKAN MEDIS / MEDICAL FITNESS CONCLUSION</div>
        <div class="status-badge">{{ $statusUpper }}</div>
        <div class="validity-text">
            <strong>Berlaku Sampai / Valid Until:</strong> 
            {{ $pemeriksaan->berlaku_sampai ? $pemeriksaan->berlaku_sampai->format('d F Y') : '-' }}
        </div>
        @if(!empty($pemeriksaan->catatan))
            <div style="margin-top: 8px; font-size: 8.5pt; color: #4b5563;">
                <strong>Catatan / Notes:</strong> {{ $pemeriksaan->catatan }}
            </div>
        @endif
    </div>

    <!-- Verifikasi & Footer Legal -->
    <table class="footer-table">
        <tr>
            <td class="qr-td">
                <div style="font-size: 8pt; color: #6b7280; margin-bottom: 4px;">
                    <strong>Kode Verifikasi Digital:</strong>
                </div>
                <div style="font-family: monospace; font-size: 8.5pt; color: #1e3a8a;">
                    {{ $pemeriksaan->token_verifikasi }}
                </div>
                <div style="font-size: 7.5pt; color: #9ca3af; margin-top: 2px;">
                    Verifikasi daring: {{ url('/v/' . $pemeriksaan->token_verifikasi) }}
                </div>
            </td>
            <td class="signature-td">
                <div><strong>Klinik Indosehat 2003</strong></div>
                <div style="color: #6b7280; font-size: 8pt; margin-top: 2px;">
                    Dicetak otomatis oleh Sistem EMR Resmi pada {{ date('d F Y H:i:s') }} WIB
                </div>
                <div class="legal-notice">
                    Dokumen ini sah dan diterbitkan secara elektronik oleh Klinik Indosehat 2003 sesuai ketentuan UU ITE No. 11/2008 & UU PDP No. 27/2022. Keaslian sertifikat dapat dibuktikan secara instan melalui pemindaian QR code atau tautan verifikasi resmi.
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
