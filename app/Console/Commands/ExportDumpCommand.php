<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LampiranMcu;
use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Illuminate\Console\Command;

class ExportDumpCommand extends Command
{
    protected $signature = 'mcu:export-mysql';
    protected $description = 'Ekspor seluruh data produksi & arsip ke dump SQL MySQL';

    public function handle(): int
    {
        $dir = database_path('dumps');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $sqlPath = $dir . '/mcu_indosehat_migrated.sql';
        $fp = fopen($sqlPath, 'w');

        fwrite($fp, "-- ========================================================\n");
        fwrite($fp, "-- MCU Indosehat 2003 - MySQL Production Migration Dump\n");
        fwrite($fp, "-- Generated at: " . date('Y-m-d H:i:s') . "\n");
        fwrite($fp, "-- Target: Hostinger LiteSpeed / MariaDB / MySQL 8.x\n");
        fwrite($fp, "-- ========================================================\n\n");
        fwrite($fp, "SET NAMES utf8mb4;\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($fp, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n");

        $schemaSql = <<<'SQL'
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

SQL;

        fwrite($fp, $schemaSql . "\n");

        $escape = function (mixed $v): string {
            if ($v === null) {
                return 'NULL';
            }
            if (is_bool($v)) {
                return $v ? '1' : '0';
            }
            if (is_int($v) || is_float($v)) {
                return (string) $v;
            }

            return "'" . addslashes((string) $v) . "'";
        };

        $tables = [
            'users' => User::all(),
            'pasien' => Pasien::all(),
            'nomor_mcu_sekuens' => \Illuminate\Support\Facades\DB::table('nomor_mcu_sekuens')->get(),
            'pemeriksaan_mcu' => PemeriksaanMcu::all(),
            'lampiran_mcu' => LampiranMcu::all(),
            'log_audit' => LogAudit::all(),
        ];

        foreach ($tables as $table => $rows) {
            $this->info("Mengekspor data tabel: {$table} (" . count($rows) . " baris)...");
            fwrite($fp, "-- --------------------------------------------------------\n");
            fwrite($fp, "-- Dumping data for table `{$table}`\n");
            fwrite($fp, "-- --------------------------------------------------------\n");
            foreach ($rows as $row) {
                $attrs = is_array($row) ? $row : (method_exists($row, 'getAttributes') ? $row->getAttributes() : (array) $row);
                $cols = array_keys($attrs);
                $vals = array_map($escape, array_values($attrs));
                fwrite($fp, "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $vals) . ");\n");
            }
            fwrite($fp, "\n");
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fp);

        $this->info("Dump lengkap DDL + Data berhasil disimpan di: {$sqlPath} (Ukuran: " . number_format(filesize($sqlPath)) . " bytes)");

        return self::SUCCESS;
    }
}
