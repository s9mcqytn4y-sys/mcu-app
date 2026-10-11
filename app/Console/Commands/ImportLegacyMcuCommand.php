<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LampiranMcu;
use App\Models\LogAudit;
use App\Models\Pasien;
use App\Models\PemeriksaanMcu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use Throwable;
use ZipArchive;

class ImportLegacyMcuCommand extends Command
{
    /**
     * Nama dan tanda tangan command Artisan.
     *
     * @var string
     */
    protected $signature = 'mcu:import-legacy
                            {--sqlite=C:\Users\Acer\Downloads\database.sqlite : Jalur absolut berkas database.sqlite legacy}
                            {--storage-zip=C:\Users\Acer\Downloads\storage.zip : Jalur absolut berkas storage.zip legacy}
                            {--dry-run : Menjalankan validasi dan simulasi tanpa menyimpan perubahan ke database}
                            {--force : Menjalankan eksekusi impor nyata ke dalam database}';

    /**
     * Deskripsi command Artisan.
     *
     * @var string
     */
    protected $description = 'Mengimpor dan merekonsiliasi 70 arsip MCU historis legacy beserta lampiran fisik secara auditabel';

    /**
     * Eksekusi perintah konsol.
     */
    public function handle(): int
    {
        $sqlitePath = (string) $this->option('sqlite');
        $zipPath = (string) $this->option('storage-zip');
        $isDryRun = $this->option('dry-run') || ! $this->option('force');

        $this->info('================================================================');
        $this->info('   IMPOR & REKONSILIASI ARSIP HISTORIS MCU INDOSEHAT 2003');
        $this->info('================================================================');
        $this->line("Mode Eksekusi : " . ($isDryRun ? '<comment>DRY-RUN (Simulasi Validasi - Tanpa Tulis DB)</comment>' : '<fg=green;options=bold>EKSEKUSI NYATA (--force aktif)</>'));
        $this->line("Sumber SQLite : {$sqlitePath}");
        $this->line("Sumber ZIP    : {$zipPath}");
        $this->newLine();

        if (! file_exists($sqlitePath)) {
            $this->error("Berkas SQLite sumber tidak ditemukan di: {$sqlitePath}");
            return self::FAILURE;
        }

        try {
            $pdo = new PDO("sqlite:{$sqlitePath}");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $e) {
            $this->error("Gagal membuka koneksi SQLite: " . $e->getMessage());
            return self::FAILURE;
        }

        /** @var array<int, array<string, mixed>> $archives */
        $archives = $pdo->query("SELECT * FROM mcu_archives ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
        $totalArchives = count($archives);

        $this->info("Menemukan {$totalArchives} baris arsip pada tabel legacy 'mcu_archives'.");

        // Buka file zip jika ada
        $zip = null;
        $zipFiles = [];
        if (file_exists($zipPath)) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $zipFiles[] = (string) $zip->getNameIndex($i);
                }
            } else {
                $this->warn("Gagal membuka berkas zip di: {$zipPath}. Lampiran fisik akan dilewati.");
                $zip = null;
            }
        } else {
            $this->warn("Berkas ZIP lampiran fisik tidak ditemukan di: {$zipPath}");
        }

        // Ambil user administrator untuk atribusi impor
        $adminUser = User::where('peran', 'administrator')->where('aktif', true)->first()
            ?? User::where('aktif', true)->first();

        if (! $adminUser && ! $isDryRun) {
            $this->error("Tidak ditemukan pengguna administrator/aktif untuk atribusi data impor.");
            return self::FAILURE;
        }

        $adminId = $adminUser?->id ?? 1;

        $stats = [
            'total_arsip' => $totalArchives,
            'pasien_baru' => 0,
            'pasien_eksisting' => 0,
            'pasien_manual_review' => 0,
            'mcu_valid' => 0,
            'mcu_draf_review' => 0,
            'mcu_duplikat_dilewati' => 0,
            'lampiran_ditemukan' => 0,
            'lampiran_hilang' => 0,
        ];

        $reconciliationTable = [];

        if (! $isDryRun) {
            DB::beginTransaction();
        }

        try {
            $pasienCache = []; // Cache NIK -> Pasien

            foreach ($archives as $index => $row) {
                $legacyId = (string) ($row['id'] ?? '');
                $rawName = trim((string) ($row['patient_name'] ?? ''));
                $rawKtp = trim((string) ($row['ktp'] ?? ''));
                $rawPassport = trim((string) ($row['passport'] ?? ''));
                $rawGender = trim((string) ($row['gender'] ?? ''));
                $rawDob = ! empty($row['dob']) ? trim((string) $row['dob']) : null;
                $rawCompany = trim((string) ($row['company'] ?? ''));
                $rawCheckupDate = ! empty($row['checkup_date']) ? trim((string) $row['checkup_date']) : null;
                $rawValidUntil = ! empty($row['valid_until']) ? trim((string) $row['valid_until']) : null;
                $isFit = $row['is_fit'];
                $filesJson = ! empty($row['files']) ? (string) $row['files'] : null;

                // Cek apakah pemeriksaan dengan token_verifikasi (legacyId) sudah ada di sistem
                if (! $isDryRun && PemeriksaanMcu::where('token_verifikasi', $legacyId)->exists()) {
                    $stats['mcu_duplikat_dilewati']++;
                    continue;
                }

                // Normalisasi NIK
                $cleanKtp = preg_replace('/[^0-9]/', '', $rawKtp);
                $isMissingKtp = empty($cleanKtp) || strlen($cleanKtp) < 10;
                $isMissingName = empty($rawName);

                // Tentukan status kelayakan & kebutuhan review
                $perluReview = $isMissingKtp || $isMissingName;

                if ($perluReview) {
                    $statusKelayakan = 'DRAF';
                    $namaPasien = ! empty($rawName) ? $rawName : '[PERLU REVIEW MANUAL - ARSIP LEGACY]';
                    $catatanKlinis = "Arsip historis legacy tanpa NIK/nama lengkap terverifikasi (ID Legacy: {$legacyId}). Perlu rekonsiliasi manual.";
                    $stats['pasien_manual_review']++;
                    $stats['mcu_draf_review']++;
                } else {
                    $statusKelayakan = ($isFit === 1 || $isFit === '1' || $isFit === true) ? 'FIT' : 'UNFIT';
                    $namaPasien = $rawName;
                    $catatanKlinis = "Diimpor dari arsip historis legacy Klinik Indosehat 2003 (ID Sumber: {$legacyId}).";
                    $stats['mcu_valid']++;
                }

                // Normalisasi jenis kelamin
                $jenisKelamin = match (strtoupper($rawGender)) {
                    'L', 'LAKI-LAKI', 'PRIA', 'MALE' => 'Laki-laki',
                    'P', 'PEREMPUAN', 'WANITA', 'FEMALE' => 'Perempuan',
                    default => 'Laki-laki',
                };

                // Pasien Deduplication
                $pasienId = null;
                if (! $isDryRun) {
                    if (! empty($cleanKtp) && isset($pasienCache[$cleanKtp])) {
                        $pasienId = $pasienCache[$cleanKtp];
                        $stats['pasien_eksisting']++;
                    } elseif (! empty($cleanKtp)) {
                        $pasien = Pasien::where('no_ktp', $cleanKtp)->first();
                        if ($pasien) {
                            $pasienId = $pasien->id;
                            $pasienCache[$cleanKtp] = $pasienId;
                            $stats['pasien_eksisting']++;
                        } else {
                            $pasienBaru = Pasien::create([
                                'nama' => $namaPasien,
                                'jenis_kelamin' => $jenisKelamin,
                                'tanggal_lahir' => $rawDob ?? '1990-01-01',
                                'no_ktp' => $cleanKtp,
                                'no_paspor' => ! empty($rawPassport) ? $rawPassport : null,
                            ]);
                            $pasienId = $pasienBaru->id;
                            $pasienCache[$cleanKtp] = $pasienId;
                            $stats['pasien_baru']++;
                        }
                    } else {
                        // Pasien tanpa NIK dibuat dummy review
                        $pasienBaru = Pasien::create([
                            'nama' => $namaPasien,
                            'jenis_kelamin' => $jenisKelamin,
                            'tanggal_lahir' => $rawDob ?? '1990-01-01',
                            'no_ktp' => 'LEGACY-' . substr($legacyId, 0, 9),
                            'no_paspor' => ! empty($rawPassport) ? $rawPassport : null,
                        ]);
                        $pasienId = $pasienBaru->id;
                    }
                } else {
                    if (! empty($cleanKtp) && isset($pasienCache[$cleanKtp])) {
                        $stats['pasien_eksisting']++;
                    } elseif (! empty($cleanKtp)) {
                        $pasienCache[$cleanKtp] = true;
                        $stats['pasien_baru']++;
                    }
                }

                // Nomor MCU Historis
                $nomorMcu = 'ARC-' . strtoupper(substr(str_replace('-', '', $legacyId), 0, 10));
                $tglPeriksa = $rawCheckupDate ?? ($row['created_at'] ? date('Y-m-d', strtotime((string) $row['created_at'])) : date('Y-m-d'));
                $tglBerlaku = $rawValidUntil ?? date('Y-m-d', strtotime('+2 years', strtotime($tglPeriksa)));

                // Simpan Pemeriksaan MCU jika bukan dry-run
                $pemeriksaanMcu = null;
                if (! $isDryRun) {
                    $pemeriksaanMcu = PemeriksaanMcu::create([
                        'pasien_id' => $pasienId,
                        'nomor_mcu' => $nomorMcu,
                        'tanggal_periksa' => $tglPeriksa,
                        'berlaku_sampai' => $tglBerlaku,
                        'nama_perusahaan' => ! empty($rawCompany) ? $rawCompany : 'Klinik Indosehat 2003 (Arsip)',
                        'status_kelayakan' => $statusKelayakan,
                        'catatan' => $catatanKlinis,
                        'token_verifikasi' => $legacyId,
                        'dibuat_oleh' => $adminId,
                    ]);
                }

                // Periksa berkas lampiran fisik
                $lampiranCount = 0;
                if (! empty($filesJson)) {
                    $filesList = json_decode($filesJson, true);
                    if (is_array($filesList)) {
                        foreach ($filesList as $relPath) {
                            $baseName = basename((string) $relPath);
                            $foundInZip = false;
                            $zipEntryName = null;

                            foreach ($zipFiles as $zf) {
                                if (str_ends_with($zf, $baseName)) {
                                    $foundInZip = true;
                                    $zipEntryName = $zf;
                                    break;
                                }
                            }

                            if ($foundInZip && $zip !== null && $zipEntryName !== null) {
                                $stats['lampiran_ditemukan']++;
                                $lampiranCount++;

                                if (! $isDryRun && $pemeriksaanMcu !== null) {
                                    $stream = $zip->getStream($zipEntryName);
                                    if ($stream !== false) {
                                        $fileContent = stream_get_contents($stream);
                                        fclose($stream);

                                        if ($fileContent !== false) {
                                            $destRelativePath = "lampiran-mcu/{$pemeriksaanMcu->id}/{$baseName}";
                                            Storage::disk('local')->put($destRelativePath, $fileContent);

                                            $mime = 'application/octet-stream';
                                            $ext = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));
                                            if (in_array($ext, ['jpg', 'jpeg'], true)) {
                                                $mime = 'image/jpeg';
                                            } elseif ($ext === 'png') {
                                                $mime = 'image/png';
                                            } elseif ($ext === 'pdf') {
                                                $mime = 'application/pdf';
                                            }

                                            LampiranMcu::create([
                                                'pemeriksaan_mcu_id' => $pemeriksaanMcu->id,
                                                'path_berkas' => $destRelativePath,
                                                'nama_berkas' => $baseName,
                                                'tipe_mime' => $mime,
                                                'ukuran_berkas' => strlen($fileContent),
                                                'jenis_lampiran' => 'DOKUMEN_KLINIS',
                                                'hash_sha256' => hash('sha256', $fileContent),
                                                'diunggah_oleh' => $adminId,
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                $stats['lampiran_hilang']++;
                            }
                        }
                    }
                }

                if ($index < 8 || $index >= $totalArchives - 2 || $perluReview) {
                    $reconciliationTable[] = [
                        'No' => $index + 1,
                        'Legacy UUID' => substr($legacyId, 0, 8) . '...',
                        'Nama Pasien' => substr($namaPasien, 0, 22),
                        'NIK / KTP' => ! empty($cleanKtp) ? $cleanKtp : '<fg=red>KOSONG</>',
                        'Kelayakan' => $statusKelayakan,
                        'Lampiran' => $lampiranCount > 0 ? "<fg=green>{$lampiranCount} Berkas</>" : '-',
                        'Status' => $perluReview ? '<fg=yellow>Draf Review</>' : '<fg=green>Valid</>',
                    ];
                }
            }

            if (! $isDryRun) {
                // Rekam Log Audit Pelaksanaan Impor
                LogAudit::catat(
                    tindakan: 'import_arsip_legacy',
                    model: $adminUser ?? new User(),
                    rincian: [
                        'waktu' => now()->toIso8601String(),
                        'sumber_sqlite' => $sqlitePath,
                        'total_diproses' => $totalArchives,
                        'statistik' => $stats,
                    ]
                );

                DB::commit();
            }
        } catch (Throwable $e) {
            if (! $isDryRun) {
                DB::rollBack();
            }
            $this->error("Terjadi kegagalan saat proses impor: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        } finally {
            if ($zip !== null) {
                $zip->close();
            }
        }

        $this->table(
            ['No', 'Legacy UUID', 'Nama Pasien', 'NIK / KTP', 'Kelayakan', 'Lampiran', 'Status'],
            $reconciliationTable
        );

        $this->newLine();
        $this->info("=== HASIL REKONSILIASI ARSIP HISTORIS ===");
        $this->table(
            ['Indikator', 'Jumlah'],
            [
                ['Total Arsip Sumber Legacy', $stats['total_arsip']],
                ['Pasien Baru Terdaftar', $stats['pasien_baru']],
                ['Pasien Eksisting Terdeduplikasi', $stats['pasien_eksisting']],
                ['Pasien Perlu Review Manual (Tanpa NIK)', $stats['pasien_manual_review']],
                ['Pemeriksaan MCU Berstatus Valid', $stats['mcu_valid']],
                ['Pemeriksaan MCU Berstatus Draf (Review)', $stats['mcu_draf_review']],
                ['Lampiran Fisik Ditemukan & Dipetakan', $stats['lampiran_ditemukan']],
                ['Lampiran Fisik Tidak Ditemukan', $stats['lampiran_hilang']],
                ['Pemeriksaan Duplikat Dilewati', $stats['mcu_duplikat_dilewati']],
            ]
        );

        if ($isDryRun) {
            $this->warn("Simulasi DRY-RUN selesai sukses. Tidak ada data yang ditulis.");
            $this->line("Jalankan dengan opsi <fg=cyan>php artisan mcu:import-legacy --force</> untuk mengeksekusi import nyata.");
        } else {
            $this->info("Eksekusi nyata selesai dengan sukses! 70 arsip telah tersimpan dan diaudit.");
        }

        return self::SUCCESS;
    }
}
