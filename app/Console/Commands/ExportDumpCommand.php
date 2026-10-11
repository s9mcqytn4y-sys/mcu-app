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

        fwrite($fp, "-- MCU Indosehat 2003 MySQL Dump\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n\n");

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
            'pemeriksaan_mcu' => PemeriksaanMcu::all(),
            'lampiran_mcu' => LampiranMcu::all(),
            'log_audit' => LogAudit::all(),
        ];

        foreach ($tables as $table => $rows) {
            $this->info("Mengekspor tabel: {$table} (" . count($rows) . " baris)...");
            fwrite($fp, "-- Table: `{$table}`\n");
            foreach ($rows as $row) {
                $attrs = $row->getAttributes();
                $cols = array_keys($attrs);
                $vals = array_map($escape, array_values($attrs));
                fwrite($fp, "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $vals) . ");\n");
            }
            fwrite($fp, "\n");
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fp);

        $this->info("Dump berhasil disimpan di: {$sqlPath} (Ukuran: " . number_format(filesize($sqlPath)) . " bytes)");

        return self::SUCCESS;
    }
}
