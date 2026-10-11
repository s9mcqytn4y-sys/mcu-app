-- ========================================================
-- MCU Indosehat 2003 - MySQL Production Migration Dump
-- Generated at: 2026-10-11 12:59:23
-- Target: Hostinger LiteSpeed / MariaDB / MySQL 8.x
-- ========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staf',
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  `waktu_login_terakhir` timestamp NULL DEFAULT NULL,
  `ip_login_terakhir` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_undangan` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `undangan_dikirim_pada` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_token_undangan_unique` (`token_undangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `password_reset_tokens`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `sessions`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `cache`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `jobs`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `pasien`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pasien` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_ktp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_paspor` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pasien_nama_index` (`nama`),
  KEY `pasien_no_ktp_index` (`no_ktp`),
  KEY `pasien_no_paspor_index` (`no_paspor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `nomor_mcu_sekuens`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `nomor_mcu_sekuens` (
  `tahun` smallint unsigned NOT NULL,
  `sekuens_terakhir` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `pemeriksaan_mcu`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pemeriksaan_mcu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pasien_id` bigint unsigned NOT NULL,
  `nomor_mcu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_periksa` date NOT NULL,
  `berlaku_sampai` date DEFAULT NULL,
  `nama_perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_kelayakan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FIT',
  `wilayah_operasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kapal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `token_verifikasi` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dibuat_oleh` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemeriksaan_mcu_nomor_mcu_unique` (`nomor_mcu`),
  UNIQUE KEY `pemeriksaan_mcu_token_verifikasi_unique` (`token_verifikasi`),
  KEY `pemeriksaan_mcu_pasien_id_foreign` (`pasien_id`),
  KEY `pemeriksaan_mcu_dibuat_oleh_foreign` (`dibuat_oleh`),
  CONSTRAINT `pemeriksaan_mcu_pasien_id_foreign` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_mcu_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `lampiran_mcu`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lampiran_mcu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pemeriksaan_mcu_id` bigint unsigned NOT NULL,
  `path_berkas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_berkas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_mime` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ukuran_berkas` bigint unsigned NOT NULL,
  `jenis_lampiran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sertifikat',
  `hash_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diunggah_oleh` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lampiran_mcu_pemeriksaan_mcu_id_foreign` (`pemeriksaan_mcu_id`),
  KEY `lampiran_mcu_diunggah_oleh_foreign` (`diunggah_oleh`),
  CONSTRAINT `lampiran_mcu_pemeriksaan_mcu_id_foreign` FOREIGN KEY (`pemeriksaan_mcu_id`) REFERENCES `pemeriksaan_mcu` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lampiran_mcu_diunggah_oleh_foreign` FOREIGN KEY (`diunggah_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `log_audit`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `log_audit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pengguna` bigint unsigned DEFAULT NULL,
  `tindakan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_model` bigint unsigned DEFAULT NULL,
  `alamat_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agen_pengguna` text COLLATE utf8mb4_unicode_ci,
  `rincian` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_audit_tindakan_index` (`tindakan`),
  KEY `log_audit_id_pengguna_foreign` (`id_pengguna`),
  CONSTRAINT `log_audit_id_pengguna_foreign` FOREIGN KEY (`id_pengguna`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dumping data for table `users`
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `peran`, `aktif`, `waktu_login_terakhir`, `ip_login_terakhir`, `remember_token`, `created_at`, `updated_at`, `token_undangan`, `undangan_dikirim_pada`) VALUES (1, 'Wira Pramudya', 'wira', 'wirapramudya2q@gmail.com', '2026-10-11 10:14:14', '$2y$12$6cqi/LN5vsTA3F1t9j0puOMxpI1zobqs2UfE//RRpbEXVia3AbLMi', 'administrator', 1, NULL, NULL, 'XCS5RiKpkTIc6WUat4RN7RkhidcHpbbtQpda01wWDp92QrpPTEM9KTSrZdmC', '2026-10-11 10:14:14', '2026-10-11 10:14:14', NULL, NULL);
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `peran`, `aktif`, `waktu_login_terakhir`, `ip_login_terakhir`, `remember_token`, `created_at`, `updated_at`, `token_undangan`, `undangan_dikirim_pada`) VALUES (2, 'Staf Operasional', 'staf', 'staf@klinikindosehat2003.com', NULL, '$2y$12$/BAttU/5yIun35KyDb9u5uT0jQGKAAWphYNjI7irmPvdjTpq8TOqm', 'staf', 1, NULL, NULL, NULL, '2026-10-11 12:58:40', '2026-10-11 12:58:40', NULL, NULL);

-- --------------------------------------------------------
-- Dumping data for table `pasien`
-- --------------------------------------------------------
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (1, 'Budi Santoso', 'L', '1988-05-14', '3275012345670001', 'B1234567', '2026-10-11 10:14:14', '2026-10-11 10:14:14');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (2, 'Siti Rahmawati', 'P', '1992-11-20', '3171098765430002', NULL, '2026-10-11 10:14:14', '2026-10-11 10:14:14');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (3, '[PERLU REVIEW MANUAL - ARSIP LEGACY]', 'Laki-laki', '1990-01-01', 'LEGACY-53fddba9-', NULL, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (4, '[PERLU REVIEW MANUAL - ARSIP LEGACY]', 'Laki-laki', '1990-01-01', 'LEGACY-680074f2-', NULL, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (5, '[PERLU REVIEW MANUAL - ARSIP LEGACY]', 'Laki-laki', '1990-01-01', 'LEGACY-a3c31e1e-', NULL, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (6, 'Aziz', 'Laki-laki', '2026-08-08', '123', '123', '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (7, 'Khairaf Lican', 'Laki-laki', '1993-04-28', '1371022804930004', 'X5787814', '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (8, 'Endy Armanda Tarigan', 'Laki-laki', '1975-04-24', '2171072404750002', NULL, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (9, 'M.KHAERUL', 'Laki-laki', '2001-09-27', '7315022709010001', 'X9274995', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (10, 'RIZKI SATRIA', 'Laki-laki', '2000-05-12', '3205421205000004', NULL, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (11, 'ROY YUNUS SILABAN', 'Laki-laki', '1998-05-29', '1218042905980002', NULL, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (12, 'DJUNAIDI KAHAGI', 'Laki-laki', '1985-01-04', '7171010401850002', 'X2266611', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (13, 'TAHAN SURATMAN SIBURIAN', 'Laki-laki', '1978-04-06', '3172020604780018', 'E 6621844', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (14, 'KEVIN JHON  PRENGKI', 'Laki-laki', '1983-10-11', '2171091110839012', 'C82755996', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (15, 'IHWANUL MUWAFIQY', 'Laki-laki', '2003-08-31', '3526013010030002', 'E7128183', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (16, 'NIKLAS BANARE', 'Laki-laki', '1992-11-03', '7103120311920001', 'C7919877', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (17, 'AGUS HERMAWAN', 'Laki-laki', '1973-08-22', '1271122208730001', 'X8928133', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (18, 'RUSMINI', 'Laki-laki', '2000-05-11', '317025105000015', 'C7919877', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (19, 'AULIA RIDHO AINUR RAHMAN', 'Laki-laki', '1997-04-07', '3510210704970005', 'X5708847', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (20, 'Abdul Haris umasangaji', 'Laki-laki', '1972-10-19', '6371031910720010', NULL, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (21, 'SAHRUL', 'Laki-laki', '1996-05-05', '7305020107970176', 'X9352970', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (22, 'APRIYANTO SULAEMAN', 'Laki-laki', '1990-04-10', '7371091004900004', 'X 9910526', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (23, 'RICKY LUSTY PANDE', 'Laki-laki', '1983-06-26', '5271022606830001', 'E 0201399', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (24, 'ANDI WILIAM SURBAKTI', 'Laki-laki', '1987-06-07', '1271030706870002', 'E 8157556', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (25, 'ZON HANDRICH PURBA', 'Laki-laki', '1997-05-11', '1216061105970001', 'X2616234', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (26, 'MUH ASRIL ASSUDAIS SIWATA', 'Laki-laki', '2002-04-12', '7373051204020001', 'E 0205715', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (27, 'JO YOHAN', 'Laki-laki', '1982-06-17', '3273101706820001', 'E 5592113', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (28, 'RIZKY ARI WIJAYASILA', 'Laki-laki', '2005-11-19', '5171011911050004', 'E 7263137', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (29, 'RIYAN', 'Laki-laki', '2004-09-05', '7317030505040002', 'E 4380102', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (30, 'TAUFIK HABIBI RAHMAN PURBA', 'Laki-laki', '2000-11-06', '1219050611000003', 'E 5787885', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (31, 'SANDRI FAHROZA', 'Laki-laki', '1988-08-14', '1404071308880001', 'X 7992399', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (32, 'RANDI KARDIAN', 'Laki-laki', '1989-10-13', '1507021310890002', 'X 8242434', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (33, 'MUHAMMAD RIZKY PRAYOGI', 'Laki-laki', '1995-12-27', '3172022712950007', 'X 7124936', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (34, 'BHIMA YUDHA ANUGRAH', 'Laki-laki', '1991-06-16', '1471071606910021', 'E7329770', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (35, 'NUR AFDHAL ILAHI', 'Laki-laki', '1985-07-11', '1471011107850001', 'X 3345106', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (36, 'FAIZIN', 'Laki-laki', '1983-10-26', '3305092610830001', 'X 5093855', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (37, 'YUSRIL IHZA MAHENDRA', 'Laki-laki', '1999-08-16', '3525171608990001', 'E 7132194', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (38, 'TALUNG', 'Laki-laki', '1976-06-15', '7317211506760001', 'X 7676792', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (39, 'OBBNER OXIN MANAIDA', 'Laki-laki', '1971-10-05', '7171020510710002', 'E 5590542', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (40, 'ISRORUDIN', 'Laki-laki', '1976-01-14', '3327141401760001', 'X 6755444', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (41, 'NURUL AGUNG', 'Laki-laki', '1999-12-25', '7314102512990001', 'X 9722895', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (42, 'WAHYU HERYANTO PRAYITNO', 'Laki-laki', '2001-03-06', '3526040603010004', 'X 7680866', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (43, 'INDRA GUNAWAN', 'Laki-laki', '2004-03-05', '5272010508040003', 'X 7960437', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (44, 'MULYONO', 'Laki-laki', '1973-05-05', '3172020505730038', 'E 4322190', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (45, 'ELIYAS FIRDAUS SEMBIRING', 'Laki-laki', '1992-10-03', '1207210310920003', 'X 7901355', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (46, 'MU\'MIN', 'Laki-laki', '2003-12-01', '7311050112030001', 'E 5835016', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (47, 'RAGIL STEVEN', 'Laki-laki', '2001-07-02', '1205060207010002', 'X 9587424', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (48, 'GALATIA SUKOY SALEMPANG PUTRA', 'Laki-laki', '2003-10-18', '6405021810030004', 'E4850909', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (49, 'AIMAL SALEH', 'Laki-laki', '1990-06-27', '7322112706910001', 'E 9711372', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (50, 'M DIAS SIDIK', 'Laki-laki', '2004-07-19', '7303071907040001', 'E 6731936', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (51, 'RAHMATULLAH', 'Laki-laki', '1993-11-13', '7305091311930002', 'Y1392504', '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pasien` (`id`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `no_ktp`, `no_paspor`, `created_at`, `updated_at`) VALUES (52, 'ZAENAL', 'Laki-laki', '1983-10-14', '7306021409830002', NULL, '2026-10-11 12:04:56', '2026-10-11 12:04:56');

-- --------------------------------------------------------
-- Dumping data for table `nomor_mcu_sekuens`
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Dumping data for table `pemeriksaan_mcu`
-- --------------------------------------------------------
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (1, 1, 'MCU-2026-0001', '2026-08-01', '2027-08-01', 'PT Pelayaran Samudera Jaya', 'FIT', 'Internasional', 'Deck', 'Container Vessel', 'Kondisi fisik prima, visus normal dengan kacamata.', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 1, '2026-10-11 10:14:14', '2026-10-11 10:14:14');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (2, 2, 'MCU-2026-0002', '2026-08-05', '2027-08-05', 'PT Maritim Nusantara', 'UNFIT', 'Domestik', 'Engine', 'Bulk Carrier', 'Disarankan kontrol rutin tekanan darah.', 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 1, '2026-10-11 10:14:14', '2026-10-11 10:14:14');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (3, 3, 'ARC-53FDDBA93C', '2026-08-07', '2028-08-07', 'Klinik Indosehat 2003 (Arsip)', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: 53fddba9-3c09-4698-9d27-ab21ab3f03db). Perlu rekonsiliasi manual.', '53fddba9-3c09-4698-9d27-ab21ab3f03db', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (4, 4, 'ARC-680074F289', '2026-08-07', '2028-08-07', 'Klinik Indosehat 2003 (Arsip)', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: 680074f2-8913-4f58-b988-e8c6479be035). Perlu rekonsiliasi manual.', '680074f2-8913-4f58-b988-e8c6479be035', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (5, 5, 'ARC-A3C31E1EFD', '2026-08-07', '2028-08-07', 'Klinik Indosehat 2003 (Arsip)', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: a3c31e1e-fd07-4b88-9944-4379d8a9d046). Perlu rekonsiliasi manual.', 'a3c31e1e-fd07-4b88-9944-4379d8a9d046', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (6, 6, 'ARC-1A076F4094', '2026-08-08', '2026-08-08', 'pt abc', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: 1a076f40-9410-4573-837f-353d58584ae8). Perlu rekonsiliasi manual.', '1a076f40-9410-4573-837f-353d58584ae8', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (7, 6, 'ARC-856B5EAE0F', '2026-08-08', '2026-08-08', 'pt abc', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: 856b5eae-0ffb-48c8-a119-8bdb99ef36aa). Perlu rekonsiliasi manual.', '856b5eae-0ffb-48c8-a119-8bdb99ef36aa', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (8, 6, 'ARC-E9EDFDDC31', '2026-08-08', '2026-08-08', 'abc', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: e9edfddc-3177-401a-a2b1-d56a84471b6b). Perlu rekonsiliasi manual.', 'e9edfddc-3177-401a-a2b1-d56a84471b6b', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (9, 7, 'ARC-2CF1319542', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 2cf13195-42b6-439c-b4e8-0c0286770122).', '2cf13195-42b6-439c-b4e8-0c0286770122', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (10, 8, 'ARC-FA95D8D100', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING  SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: fa95d8d1-00ec-4ad4-a386-1271a29492a1).', 'fa95d8d1-00ec-4ad4-a386-1271a29492a1', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (11, 8, 'ARC-E6AA5BFC85', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: e6aa5bfc-853d-409c-8cd9-99d13522eb62).', 'e6aa5bfc-853d-409c-8cd9-99d13522eb62', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (12, 6, 'ARC-C2A2D08E3E', '2026-08-08', '2026-08-08', 'abc', 'DRAF', NULL, NULL, NULL, 'Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: c2a2d08e-3e98-4c8a-a252-314cda0d9e89). Perlu rekonsiliasi manual.', 'c2a2d08e-3e98-4c8a-a252-314cda0d9e89', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (13, 9, 'ARC-6B0FE4A563', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 6b0fe4a5-63e6-4618-ada9-9168565ee219).', '6b0fe4a5-63e6-4618-ada9-9168565ee219', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (14, 10, 'ARC-383FEF2C51', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 383fef2c-5148-4877-864e-92dac38e824f).', '383fef2c-5148-4877-864e-92dac38e824f', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (15, 11, 'ARC-095702DFFB', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 095702df-fbf1-4a88-9e41-3014584fed85).', '095702df-fbf1-4a88-9e41-3014584fed85', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (16, 10, 'ARC-FF95FE7750', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: ff95fe77-5067-431d-9130-172006b04dfa).', 'ff95fe77-5067-431d-9130-172006b04dfa', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (17, 11, 'ARC-E2AF1A3C59', '2026-08-07', '2028-08-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: e2af1a3c-59c9-41e4-be2e-f98daa9c8378).', 'e2af1a3c-59c9-41e4-be2e-f98daa9c8378', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (18, 12, 'ARC-C7FECB78A5', '2026-08-08', '2028-08-08', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: c7fecb78-a5cd-4419-894e-6aa01011148b).', 'c7fecb78-a5cd-4419-894e-6aa01011148b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (19, 12, 'ARC-CBE8DFB2AB', '2026-08-10', '2026-08-10', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: cbe8dfb2-ab22-47db-85b6-b8b8ff989e2a).', 'cbe8dfb2-ab22-47db-85b6-b8b8ff989e2a', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (20, 12, 'ARC-5B09F39C25', '2026-08-10', '2028-08-10', 'PRESTIGE SHIPPING SERVICES', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 5b09f39c-25ca-45b0-803a-463cc8819346).', '5b09f39c-25ca-45b0-803a-463cc8819346', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (21, 13, 'ARC-2D0FDC7617', '2026-08-13', '2028-08-13', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 2d0fdc76-1775-41fb-96d6-eb3b9f8b85dd).', '2d0fdc76-1775-41fb-96d6-eb3b9f8b85dd', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (22, 13, 'ARC-B6BC52D9CE', '2026-08-13', '2028-08-13', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: b6bc52d9-ce7a-4569-9d76-c3be8d2685f7).', 'b6bc52d9-ce7a-4569-9d76-c3be8d2685f7', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (23, 13, 'ARC-C5271723A2', '2026-08-13', '2028-08-13', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: c5271723-a2f2-4952-8243-ee389e13e0c9).', 'c5271723-a2f2-4952-8243-ee389e13e0c9', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (24, 14, 'ARC-732B96153D', '2026-08-18', '2028-08-18', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 732b9615-3d0f-4585-9cd8-46705c630a8e).', '732b9615-3d0f-4585-9cd8-46705c630a8e', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (25, 14, 'ARC-51E2203F23', '2026-08-18', '2028-08-18', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 51e2203f-23b4-4ab4-a8e5-99bc2957ecb7).', '51e2203f-23b4-4ab4-a8e5-99bc2957ecb7', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (26, 15, 'ARC-61F56B98FC', '2026-08-18', '2028-08-18', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 61f56b98-fc61-4423-8dfd-98ee479ed2cf).', '61f56b98-fc61-4423-8dfd-98ee479ed2cf', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (27, 16, 'ARC-FE4B22EB24', '2026-08-20', '2028-08-20', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: fe4b22eb-24d2-40b5-8c10-560deb4246cf).', 'fe4b22eb-24d2-40b5-8c10-560deb4246cf', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (28, 16, 'ARC-93866FB5BE', '2026-08-20', '2028-08-20', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 93866fb5-be47-420b-85a7-c49057244486).', '93866fb5-be47-420b-85a7-c49057244486', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (29, 17, 'ARC-383E660B25', '2026-08-20', '2028-08-20', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 383e660b-2516-4f7e-9113-b12baa728d9d).', '383e660b-2516-4f7e-9113-b12baa728d9d', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (30, 18, 'ARC-9807B39D89', '2026-08-22', '2028-08-22', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 9807b39d-89e7-422d-b4cc-f0df39e0dd0c).', '9807b39d-89e7-422d-b4cc-f0df39e0dd0c', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (31, 19, 'ARC-6B7EABC5BD', '2026-08-21', '2028-08-21', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 6b7eabc5-bde4-41f9-8fce-1d5c8e7adf72).', '6b7eabc5-bde4-41f9-8fce-1d5c8e7adf72', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (32, 20, 'ARC-50A1369A0E', '0025-06-05', '0027-06-05', 'Shipworkz Marine services LLV=C', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 50a1369a-0e16-4c53-94e0-f1653caae735).', '50a1369a-0e16-4c53-94e0-f1653caae735', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (33, 21, 'ARC-110422873B', '2026-08-24', '2026-08-24', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 11042287-3b15-4785-b495-14d440ea81fd).', '11042287-3b15-4785-b495-14d440ea81fd', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (34, 22, 'ARC-9898078B78', '2026-08-24', '2028-08-24', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 9898078b-78d6-420d-bac8-ad3da391cbfc).', '9898078b-78d6-420d-bac8-ad3da391cbfc', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (35, 23, 'ARC-C897856FCA', '2026-08-24', '2028-08-24', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: c897856f-cafa-4084-9abb-29be679a44d7).', 'c897856f-cafa-4084-9abb-29be679a44d7', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (36, 24, 'ARC-DC1D9F2A48', '2026-08-27', '2028-08-27', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: dc1d9f2a-481e-403f-a88f-68e35a5033be).', 'dc1d9f2a-481e-403f-a88f-68e35a5033be', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (37, 24, 'ARC-A6CB9D18BB', '2026-08-27', '2028-08-27', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: a6cb9d18-bb1a-48bf-aebe-ea25748d4433).', 'a6cb9d18-bb1a-48bf-aebe-ea25748d4433', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (38, 25, 'ARC-53ADFC51A1', '2026-09-02', '2028-09-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 53adfc51-a1cb-41e5-9d6d-8ae022d85545).', '53adfc51-a1cb-41e5-9d6d-8ae022d85545', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (39, 26, 'ARC-AE65D2CCF2', '2026-11-02', '2028-11-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: ae65d2cc-f266-4f81-a499-58eb7b566da8).', 'ae65d2cc-f266-4f81-a499-58eb7b566da8', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (40, 27, 'ARC-FA7FEFC14D', '2026-11-02', '2028-11-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: fa7fefc1-4d41-409d-a260-78b4affcffdc).', 'fa7fefc1-4d41-409d-a260-78b4affcffdc', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (41, 28, 'ARC-4B1F6A0CFF', '2026-09-02', '2028-09-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 4b1f6a0c-ff06-42ae-9eea-11cb6a16c1ae).', '4b1f6a0c-ff06-42ae-9eea-11cb6a16c1ae', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (42, 28, 'ARC-9F6EC69A09', '2026-09-02', '2028-09-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 9f6ec69a-0947-441e-b6e7-6067cc9cc447).', '9f6ec69a-0947-441e-b6e7-6067cc9cc447', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (43, 27, 'ARC-974F603E5F', '2026-09-02', '2028-09-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 974f603e-5f6b-4014-b053-7b8fdc7e2a0b).', '974f603e-5f6b-4014-b053-7b8fdc7e2a0b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (44, 26, 'ARC-AEEC396FE4', '2026-09-02', '2028-09-02', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: aeec396f-e46a-4f69-9e35-37807de0427e).', 'aeec396f-e46a-4f69-9e35-37807de0427e', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (45, 29, 'ARC-EC059CF49F', '2026-09-02', '2028-09-02', 'PRESTIGE SHIPPING SERVICES', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: ec059cf4-9fb6-46eb-8e05-8f3999352f8b).', 'ec059cf4-9fb6-46eb-8e05-8f3999352f8b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (46, 30, 'ARC-F43423C32C', '2026-09-03', '2028-09-03', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: f43423c3-2ced-45bd-9fef-36d68c5936e3).', 'f43423c3-2ced-45bd-9fef-36d68c5936e3', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (47, 31, 'ARC-9CB515C828', '2026-09-03', '2028-09-03', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 9cb515c8-2853-4166-9ae4-b5265d269830).', '9cb515c8-2853-4166-9ae4-b5265d269830', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (48, 32, 'ARC-7EBB1D41EA', '2026-09-03', '2028-09-03', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 7ebb1d41-ea3e-4528-aa75-1e614117a18b).', '7ebb1d41-ea3e-4528-aa75-1e614117a18b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (49, 33, 'ARC-1882FBF7E8', '2026-09-05', '2028-09-05', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 1882fbf7-e8c9-4ad9-9f78-8c0e5e1647c6).', '1882fbf7-e8c9-4ad9-9f78-8c0e5e1647c6', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (50, 34, 'ARC-031DE6D0F3', '2026-09-05', '2026-09-05', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 031de6d0-f3e6-41b3-a13f-28f409425795).', '031de6d0-f3e6-41b3-a13f-28f409425795', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (51, 35, 'ARC-25787D0FF9', '2026-09-07', '2028-09-07', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 25787d0f-f987-4e1b-bfd0-6be460875e41).', '25787d0f-f987-4e1b-bfd0-6be460875e41', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (52, 36, 'ARC-85D1678D1F', '2026-09-08', '2026-09-08', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 85d1678d-1fd7-4cb2-a74d-af7531debed1).', '85d1678d-1fd7-4cb2-a74d-af7531debed1', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (53, 37, 'ARC-16E249EFE9', '2026-09-08', '2028-09-08', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 16e249ef-e97d-4394-9670-99308229d005).', '16e249ef-e97d-4394-9670-99308229d005', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (54, 37, 'ARC-52C072BF3D', '2026-09-08', '2026-09-08', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 52c072bf-3da0-4b71-a481-93e3d9f42886).', '52c072bf-3da0-4b71-a481-93e3d9f42886', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (55, 38, 'ARC-C34AA4DA9E', '2026-09-14', '2028-09-14', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: c34aa4da-9e72-41e6-a5f5-8729f1a471d3).', 'c34aa4da-9e72-41e6-a5f5-8729f1a471d3', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (56, 39, 'ARC-F9A002C707', '2026-09-15', '2028-09-15', 'PRESTIGE SHIPPING SERVICES PTE LTD', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: f9a002c7-072e-49a9-8e9a-49c37de50128).', 'f9a002c7-072e-49a9-8e9a-49c37de50128', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (57, 40, 'ARC-E29E1EE0E7', '2026-09-15', '2028-09-15', 'Prestige Shipping Services Pte Ltd', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: e29e1ee0-e7ec-43d8-a7e0-cf85f31a170b).', 'e29e1ee0-e7ec-43d8-a7e0-cf85f31a170b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (58, 41, 'ARC-4622362DB5', '2026-09-18', '2028-09-18', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 4622362d-b524-490b-b740-8c47107311c4).', '4622362d-b524-490b-b740-8c47107311c4', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (59, 42, 'ARC-9C2E18583B', '2026-03-23', '2028-03-23', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 9c2e1858-3b69-4536-b322-baeaacf3f6d0).', '9c2e1858-3b69-4536-b322-baeaacf3f6d0', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (60, 42, 'ARC-0565CADF3E', '2026-09-23', '2028-09-23', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 0565cadf-3e33-4e9d-adf0-6053bceae135).', '0565cadf-3e33-4e9d-adf0-6053bceae135', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (61, 43, 'ARC-CA3BC6D79B', '2026-09-23', '2028-09-23', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: ca3bc6d7-9baf-42e8-8ead-b01bc4baf88a).', 'ca3bc6d7-9baf-42e8-8ead-b01bc4baf88a', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (62, 44, 'ARC-2053E9D549', '2026-09-27', '2028-09-27', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 2053e9d5-4965-4fc0-bd49-5ea43a900fd4).', '2053e9d5-4965-4fc0-bd49-5ea43a900fd4', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (63, 45, 'ARC-E785D68787', '2026-10-05', '2028-10-05', 'PRESTIGE SHIPPING SERVICES PTE LTD', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: e785d687-874c-4a28-8a0b-9fc966caaa0d).', 'e785d687-874c-4a28-8a0b-9fc966caaa0d', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (64, 46, 'ARC-92A9969657', '2026-10-03', '2028-10-03', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 92a99696-5742-4144-9597-7fa813d42bd8).', '92a99696-5742-4144-9597-7fa813d42bd8', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (65, 47, 'ARC-525132C670', '2026-10-03', '2028-10-03', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 525132c6-7038-45ff-b25c-9f564a6769c4).', '525132c6-7038-45ff-b25c-9f564a6769c4', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (66, 48, 'ARC-4D30BBACA0', '2026-10-07', '2028-10-07', 'PRESTIGE SHIPPING SERVICE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 4d30bbac-a089-40bd-b042-a9fde6abbc04).', '4d30bbac-a089-40bd-b042-a9fde6abbc04', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (67, 49, 'ARC-2D9A3A03BA', '2026-10-09', '2028-10-09', 'PRESTIGE SHIPPING SERVICES PTE LTD', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 2d9a3a03-baf2-46e6-91f5-c44c18095d17).', '2d9a3a03-baf2-46e6-91f5-c44c18095d17', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (68, 50, 'ARC-AF46E8E004', '2026-10-10', '2028-10-10', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: af46e8e0-043a-4dbe-a265-4b8a873e8ed1).', 'af46e8e0-043a-4dbe-a265-4b8a873e8ed1', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (69, 51, 'ARC-55F695392C', '2026-10-10', '2028-10-10', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 55f69539-2c66-4d4a-b786-63f0ef06476a).', '55f69539-2c66-4d4a-b786-63f0ef06476a', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (70, 52, 'ARC-E77ABFAF0A', '2026-10-10', '2028-10-10', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: e77abfaf-0aaa-4757-a39d-008720dea00e).', 'e77abfaf-0aaa-4757-a39d-008720dea00e', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (71, 52, 'ARC-19B39A540D', '2026-10-10', '2028-10-10', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 19b39a54-0da7-48eb-9c04-0ad2d4e05f41).', '19b39a54-0da7-48eb-9c04-0ad2d4e05f41', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `pemeriksaan_mcu` (`id`, `pasien_id`, `nomor_mcu`, `tanggal_periksa`, `berlaku_sampai`, `nama_perusahaan`, `status_kelayakan`, `wilayah_operasi`, `departemen`, `jenis_kapal`, `catatan`, `token_verifikasi`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES (72, 52, 'ARC-1DF3117080', '2026-10-10', '2028-10-10', 'PRIVATE', 'FIT', NULL, NULL, NULL, 'Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: 1df31170-803b-4cb8-88c5-09464dea3543).', '1df31170-803b-4cb8-88c5-09464dea3543', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');

-- --------------------------------------------------------
-- Dumping data for table `lampiran_mcu`
-- --------------------------------------------------------
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (1, 3, 'lampiran-mcu/3/53fddba9-3c09-4698-9d27-ab21ab3f03db_1.jpeg', '53fddba9-3c09-4698-9d27-ab21ab3f03db_1.jpeg', 'image/jpeg', 492468, 'DOKUMEN_KLINIS', '4d175fb210e9b6579c995bb1f60595d4c09d00d516afce88c97c43bee9159239', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (2, 3, 'lampiran-mcu/3/53fddba9-3c09-4698-9d27-ab21ab3f03db_2.jpeg', '53fddba9-3c09-4698-9d27-ab21ab3f03db_2.jpeg', 'image/jpeg', 826474, 'DOKUMEN_KLINIS', '175907099f44c466f7276c8805f30681692bc75a38aec5ca2fb79b0b94c6fd04', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (3, 4, 'lampiran-mcu/4/680074f2-8913-4f58-b988-e8c6479be035_1.jpeg', '680074f2-8913-4f58-b988-e8c6479be035_1.jpeg', 'image/jpeg', 492468, 'DOKUMEN_KLINIS', '4d175fb210e9b6579c995bb1f60595d4c09d00d516afce88c97c43bee9159239', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (4, 4, 'lampiran-mcu/4/680074f2-8913-4f58-b988-e8c6479be035_2.jpeg', '680074f2-8913-4f58-b988-e8c6479be035_2.jpeg', 'image/jpeg', 826474, 'DOKUMEN_KLINIS', '175907099f44c466f7276c8805f30681692bc75a38aec5ca2fb79b0b94c6fd04', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (5, 5, 'lampiran-mcu/5/a3c31e1e-fd07-4b88-9944-4379d8a9d046_1.jpeg', 'a3c31e1e-fd07-4b88-9944-4379d8a9d046_1.jpeg', 'image/jpeg', 826474, 'DOKUMEN_KLINIS', '175907099f44c466f7276c8805f30681692bc75a38aec5ca2fb79b0b94c6fd04', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (6, 6, 'lampiran-mcu/6/1a076f40-9410-4573-837f-353d58584ae8_1.png', '1a076f40-9410-4573-837f-353d58584ae8_1.png', 'image/png', 5572789, 'DOKUMEN_KLINIS', '7ade008c2a61b026a566adc013c258ff521d7c1cea1c13924811db7a38c0471c', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (7, 9, 'lampiran-mcu/9/2cf13195-42b6-439c-b4e8-0c0286770122_1.pdf', '2cf13195-42b6-439c-b4e8-0c0286770122_1.pdf', 'application/pdf', 2590740, 'DOKUMEN_KLINIS', '3e9cad52d944e6c1ab1dadc2819e487bb50b9bb4ca527f458557e51bb3ec449e', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (8, 10, 'lampiran-mcu/10/fa95d8d1-00ec-4ad4-a386-1271a29492a1_1.pdf', 'fa95d8d1-00ec-4ad4-a386-1271a29492a1_1.pdf', 'application/pdf', 2459427, 'DOKUMEN_KLINIS', 'd6381f14b9c0b994f001fdd621c7bed6f89a1d2ffbfb339143cacf691bd5b8ea', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (9, 11, 'lampiran-mcu/11/e6aa5bfc-853d-409c-8cd9-99d13522eb62_1.pdf', 'e6aa5bfc-853d-409c-8cd9-99d13522eb62_1.pdf', 'application/pdf', 2484503, 'DOKUMEN_KLINIS', '70e2c6ba7c5bbb9cd16df406f7d11bf169c3884057a25200ee55d5a8bb3c6eda', 1, '2026-10-11 12:04:55', '2026-10-11 12:04:55');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (10, 13, 'lampiran-mcu/13/6b0fe4a5-63e6-4618-ada9-9168565ee219_1.pdf', '6b0fe4a5-63e6-4618-ada9-9168565ee219_1.pdf', 'application/pdf', 2429785, 'DOKUMEN_KLINIS', '88d6d435a4fc240447120506975c733ec923a8b4ec22c23dcdcfe80083e6430b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (11, 14, 'lampiran-mcu/14/383fef2c-5148-4877-864e-92dac38e824f_1.pdf', '383fef2c-5148-4877-864e-92dac38e824f_1.pdf', 'application/pdf', 2451349, 'DOKUMEN_KLINIS', '3540a6d257c27474ab54f1113225d683747a7fd90576a21830a544e745d5000b', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');
INSERT INTO `lampiran_mcu` (`id`, `pemeriksaan_mcu_id`, `path_berkas`, `nama_berkas`, `tipe_mime`, `ukuran_berkas`, `jenis_lampiran`, `hash_sha256`, `diunggah_oleh`, `created_at`, `updated_at`) VALUES (12, 17, 'lampiran-mcu/17/e2af1a3c-59c9-41e4-be2e-f98daa9c8378_1.pdf', 'e2af1a3c-59c9-41e4-be2e-f98daa9c8378_1.pdf', 'application/pdf', 2468459, 'DOKUMEN_KLINIS', '8e383a504bfa6a94807430e7e85a1f6628d266d19e40d6264ee3326c76ebe748', 1, '2026-10-11 12:04:56', '2026-10-11 12:04:56');

-- --------------------------------------------------------
-- Dumping data for table `log_audit`
-- --------------------------------------------------------
INSERT INTO `log_audit` (`id`, `id_pengguna`, `tindakan`, `tipe_model`, `id_model`, `alamat_ip`, `agen_pengguna`, `rincian`, `created_at`, `updated_at`) VALUES (1, NULL, 'import_arsip_legacy', 'App\\Models\\User', 1, '127.0.0.1', 'Symfony', '{\"waktu\":\"2026-10-11T12:04:56+07:00\",\"sumber_sqlite\":\"C:\\\\Users\\\\Acer\\\\Downloads\\\\database.sqlite\",\"total_diproses\":70,\"statistik\":{\"total_arsip\":70,\"pasien_baru\":47,\"pasien_eksisting\":20,\"pasien_manual_review\":7,\"mcu_valid\":63,\"mcu_draf_review\":7,\"mcu_duplikat_dilewati\":0,\"lampiran_ditemukan\":12,\"lampiran_hilang\":0}}', '2026-10-11 12:04:56', '2026-10-11 12:04:56');

SET FOREIGN_KEY_CHECKS=1;
