# GUIDELINES & ARCHITECTURE STANDARDS: MCU APP INDOSEHAT 2003

## 1. Konvensi Penamaan (Indonesian Domain Primacy)
- **Database Tables & Columns**: Selalu gunakan snake_case dalam Bahasa Indonesia murni.
  - Tabel: `pasien`, `pemeriksaan_mcu`, `lampiran_mcu`, `log_audit`.
  - Kolom Pasien: `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`.
  - Kolom Pemeriksaan: `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`.
- **Model Eloquent**:
  - `App\Models\Pasien`
  - `App\Models\PemeriksaanMcu`
  - `App\Models\LampiranMcu`
  - `App\Models\LogAudit`
  - `App\Models\User` (dengan kolom `peran`, `aktif`, `username`).
- **Filament Resources**:
  - `PasienResource`, `PemeriksaanMcuResource`, `LogAuditResource`, `UserResource`.
  - Model label dan navigation label wajib berbahasa Indonesia (contoh: "Rekam Pasien", "Pemeriksaan MCU", "Log Audit", "Pengguna Sistem").

## 2. Keamanan & Zero Trust
- **Audit Logging**: Setiap akses verifikasi publik di `McuViewerController` dan modifikasi administratif harus mencatat log ke tabel `log_audit`.
- **UUID Verifikasi**: Dokumen MCU diakses publik menggunakan `token_verifikasi` (UUID v4) via rute `/v/{token}`. Jangan gunakan ID numerik tabel secara terbuka.
- **Throttling & Password**: Throttling login admin aktif (maks. 5 percobaan). Password di-hash menggunakan Argon2id atau Bcrypt.

## 3. UI/UX & Aksesibilitas (WCAG 2.2 AA)
- Semantic HTML5 landmark (`<main>`, `<header>`, `<section>`, `<article>`, `<aside>`, `<footer>`).
- Target sentuh minimal 44x44px (`min-h-11`) untuk perangkat mobile pada tombol dan link aksi.
- Focus-visible ring yang jelas dan rasio kontras warna tinggi (di atas 4.5:1) pada light mode dan dark mode.
- Desain antislop: tipografi bersih, layout profesional klinis, tanpa ornamen berlebih.
- **Form Ergonomics & Key Bindings**:
  - `autofocus()` aktif pada field pertama form rekam medis (`nama_pasien`).
  - Disable browser autocomplete yang mengganggu pada form medis (`->autocomplete('off')`).
  - Shortcut keyboard cepat: `Ctrl+S` / `Cmd+S` untuk simpan/terbitkan, `Esc` untuk batal.
  - Optimal virtual keyboard: `inputmode => numeric` untuk NIK/KTP.
- **Tailwind v4 & Filament Theme**:
  - Tema panel didaftarkan via `->viteTheme('resources/css/app.css')`.
  - Direktori views dan Filament disertakan dalam pemindaian via `@source` di `app.css`.

## 4. Testing & QA
- Setiap perubahan pada domain MCU wajib diverifikasi dengan `php artisan test`.
- Testing menggunakan konfigurasi in-memory SQLite yang terisolasi.
- Kompilasi aset Vite divalidasi dengan `npm run build`.
