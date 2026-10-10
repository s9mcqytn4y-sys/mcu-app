# MCU (Medical Check Up) Document & Verification Gateway
**Klinik Indosehat 2003** — Sistem Rekam Medis Elektronik, Manajemen Pasien & Portal Verifikasi Resmi Keaslian Dokumen MCU Berbasis QR Code UUID v4.

---

## 📌 Ringkasan Sistem & Arsitektur
MCU App adalah platform manajemen dan verifikasi keabsahan dokumen Medical Check-Up (MCU) yang dirancang khusus untuk memenuhi standar operasional maritim dan ketenagakerjaan berintegritas tinggi. Dilengkapi dengan portal verifikasi publik bilingual (ID/EN) yang ramah aksesibilitas (WCAG 2.2 AA compliant, Semantic HTML5) dan Panel Manajemen Staf/Administrator berbasis **Filament v3**.

### 🛠️ Tech Stack Utama
- **Backend & Framework**: PHP 8.3+ (Strict Typing), Laravel 13.x
- **Admin Panel & Interactivity**: Filament v3, Livewire v3, Alpine.js
- **Frontend / Portal Publik**: Semantic HTML5, Tailwind CSS v4, Vanilla CSS Custom Variables
- **Database**: MySQL 8.x / MariaDB (Mendukung fallback SQLite 3.x)
- **Keamanan & Standar**: Zero Trust, Argon2id/Bcrypt hash, Audit Log Immutable, UUID v4 token publik

---

## 🏛️ Arsitektur & Penamaan Bahasa Indonesia Murni
Seluruh lapisan data domain, model, migrasi, dan relasi database menggunakan konvensi **Bahasa Indonesia Murni**:

| Entitas Domain | Nama Tabel | Deskripsi & Atribut Kunci |
| :--- | :--- | :--- |
| **Pasien** | `pasien` | `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor` |
| **Pemeriksaan MCU** | `pemeriksaan_mcu` | `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `token_verifikasi`, `dibuat_oleh` |
| **Lampiran MCU** | `lampiran_mcu` | `pemeriksaan_mcu_id`, `nama_berkas`, `jalur_berkas`, `tipe_mime`, `ukuran_berkas` |
| **Log Audit** | `log_audit` | `id_pengguna`, `tindakan`, `nama_tabel`, `id_rekaman`, `nilai_lama`, `nilai_baru`, `alamat_ip`, `user_agent` |
| **Pengguna Sistem** | `users` | `name`, `username`, `email`, `peran` (`administrator` / `staf`), `aktif` |

---

## 👥 Hak Akses & RBAC (Role-Based Access Control)
Sistem memisahkan tanggung jawab operasional menjadi dua peran utama:
1. **Administrator**:
   - Kontrol penuh atas seluruh data pasien dan pemeriksaan MCU.
   - Manajemen akun pengguna (`UserResource`): pembuatan, pengubahan sandi, dan aktivasi akun staf.
   - Hak memantau jejak audit log (`LogAuditResource`).
2. **Staf**:
   - Input rekam pasien baru dan pengunggahan berkas hasil pemeriksaan MCU.
   - Penerbitan sertifikat QR code dan cetak tanda bukti.
   - Tidak memiliki izin mengelola akun pengguna atau menghapus log audit.

### Akun Pengembangan
Seeder akun hanya untuk pengembangan dan sengaja tidak berjalan pada `APP_ENV=production`.
Jangan memakai akun/password contoh pada lingkungan nyata. Buat administrator produksi melalui prosedur provisioning yang aman, dan ganti kredensial jika seed pernah dijalankan pada server publik.

*Login Filament mendukung username atau email.*

---

## 🔒 Fitur Keamanan & Audit Log
1. **Public Token Verification**: Sertifikat MCU publik tidak mengekspos ID inkremental database, melainkan menggunakan `token_verifikasi` (UUID v4 acak).
2. **Immutable Audit Trail**: Setiap verifikasi sertifikat publik dan tindakan modifikasi data dicatat secara otomatis ke dalam tabel `log_audit` lengkap dengan IP pengguna, waktu, dan *User-Agent*.
3. **Throttling & Anti-Brute Force**: Pembatasan laju percobaan login (Rate Limiting) pada portal login admin.

---

## 🚀 Panduan Instalasi Lokal

```bash
# 1. Clone repository
git clone <repository-url>
cd mcu-app

# 2. Pasang dependensi PHP & JS
composer install
npm install && npm run build

# 3. Konfigurasi Environment
cp .env.example .env
php artisan key:generate

# 4. Periksa driver database PHP (tanpa mencetak password)
php scripts/check-database-driver.php
# Atau: composer doctor

# 5. Jalankan Migrasi & Seeder (khusus database baru/pengembangan)
php artisan migrate --seed

# 6. Hubungkan symlink hanya bila ada aset publik yang membutuhkannya
# Jangan simpan berkas medis di storage/app/public
php artisan storage:link

# 7. Jalankan Server Lokal
php artisan serve
```

---

## 🛠 Troubleshooting Windows: Laravel `could not find driver` (MySQL)

Jika muncul `PDOException: could not find driver` ketika Laravel membaca tabel `sessions`,
masalah utamanya biasanya **PHP yang menjalankan aplikasi tidak memuat ekstensi `pdo_mysql`**.
Ini adalah konfigurasi runtime PHP lokal, **bukan bug query Laravel atau migrasi database**.
`SESSION_DRIVER=database` membuat kegagalan driver langsung terlihat pada request pertama.

Di PowerShell, jalankan dari root proyek:

```powershell
Get-Command php -All
where.exe php
php -v
php --ini
php -m | findstr /I "PDO pdo_mysql mysqli"
php -r "print_r(PDO::getAvailableDrivers());"
php scripts/check-database-driver.php
# Alternatif yang sama: composer doctor
```

Jika `pdo_mysql` belum muncul:
1. Buka **Loaded Configuration File** yang ditampilkan oleh `php --ini`, bukan sembarang `php.ini`.
2. Cari dan aktifkan `extension=pdo_mysql` (hilangkan awalan `;` bila ada).
3. Pastikan `extension_dir` mengarah ke folder ekstensi instalasi PHP yang aktif dan `php_pdo_mysql.dll` tersedia serta cocok dengan versi/build PHP tersebut.
4. Simpan `php.ini`, **hentikan server PHP lama**, dan jalankan kembali `php artisan serve`. Jika memakai web server lain, cek konfigurasi PHP server itu secara terpisah.
5. Verifikasi ulang:

```powershell
php -r "var_dump(extension_loaded('pdo_mysql'));"
php scripts/check-database-driver.php
php artisan migrate:status
php artisan serve
```

Target pemeriksaan ekstensi: `bool(true)`. Jika setelah itu muncul `Connection refused` atau
`Access denied`, berarti driver sudah teratasi namun layanan MySQL, host, port, akun, atau database perlu diperiksa.
Jangan kirim nilai `APP_KEY`, `DB_PASSWORD`, atau cookie sesi saat membagikan log.

**Penting:**
- Jangan mengganti `DB_CONNECTION` ke SQLite hanya untuk menyembunyikan error ketika data asli ada di MySQL.
- Mengubah `SESSION_DRIVER=file` hanya menunda kegagalan pada operasi DB lain; itu bukan perbaikan driver.
- Jangan menjalankan `php artisan migrate:fresh` pada database berisi data: perintah ini menghapus tabel.
- Jalankan `php artisan optimize:clear` dan cek kembali setelah ekstensi berhasil dimuat bila konfigurasi Laravel pernah di-cache.
- Preflight memeriksa **PHP CLI**, bukan konektivitas MySQL atau PHP yang dijalankan web server lain.

---

## 🌐 Panduan Deployment Hostinger (Tanpa Akses SSH)

Jika paket hosting Hostinger Anda tidak menyertakan akses terminal SSH (Web Hosting biasa), gunakan alur instalasi berikut:

### 1. Persiapan Berkas Bundling Lokal
Di komputer lokal Anda, lakukan kompilasi aset dan pembuatan arsip zip:
```bash
# Kompilasi aset frontend
npm run build

# Buat arsip zip aplikasi (abaikan node_modules)
# Pastikan folder vendor ikut disertakan jika composer tidak tersedia di server:
composer install --optimize-autoloader --no-dev
```
Kompres seluruh folder proyek menjadi `mcu-app.zip` (kecuali folder `node_modules` dan file `.git`).

### 2. Ekstraksi di File Manager hPanel
1. Buka **hPanel Hostinger > File Manager**.
2. Masuk ke folder root akun Anda (sejajar atau di dalam `public_html`).
3. Ekstrak `mcu-app.zip`.
4. Agar domain utama langsung mengarah ke folder `public`:
   - Pindahkan seluruh isi folder `public/` ke dalam `public_html/`.
   - Buka `public_html/index.php` dan sesuaikan jalur require autoload & bootstrap:
     ```php
     require __DIR__.'/../mcu-app/vendor/autoload.php';
     $app = require_once __DIR__.'/../mcu-app/bootstrap/app.php';
     ```
   - Atau atur Document Root sub-domain/domain langsung mengarah ke `mcu-app/public`.

### 3. Setup Database MySQL di hPanel
1. Masuk ke menu **Databases > MySQL Databases**.
2. Buat database baru (contoh: `u123456_mcudb`) beserta user dan password.
3. Ekspor database lokal Anda (`php artisan db:backup` atau melalui phpMyAdmin lokal) dan impor file `.sql` tersebut melalui **phpMyAdmin** Hostinger.
4. Perbarui file `.env` di Hostinger:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domainanda.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u123456_mcudb
   DB_USERNAME=u123456_user
   DB_PASSWORD=PasswordDatabaseAnda
   ```

---

## 🔄 Prosedur Backup & Migrasi (SQLite ↔ MySQL)

### Backup SQLite Lokal:
```bash
cp database/database.sqlite database/backups/database_$(date +%Y%m%d).sqlite
```

### Migrasi ke MySQL:
1. Pastikan servis MySQL aktif (`127.0.0.1:3306`).
2. Buat database `mcu_app_db` di MySQL:
   ```sql
   CREATE DATABASE IF NOT EXISTS mcu_app_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Ubah konfigurasi `.env` ke driver `mysql`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mcu_app_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Jalankan migrasi (tidak menghapus tabel yang sudah ada):
   ```bash
   php artisan migrate
   ```

---

## 🧪 Pengujian Otomatis (Automated Testing)
Jalankan pengujian unit dan fitur terintegrasi dengan perintah:
```bash
php artisan test
```
Semua test menggunakan basis data in-memory SQLite terisolasi dengan perlindungan *RefreshDatabase*.

---

*© 2026 Klinik Indosehat 2003. Hak Cipta Dilindungi.*
