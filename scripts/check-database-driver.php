<?php

declare(strict_types=1);

/**
 * Lightweight PHP CLI prerequisite check, before Laravel boots or opens a database session.
 * Never connects to the DB and never prints credentials.
 */
function configuredConnection(): string
{
    $fromEnvironment = getenv('DB_CONNECTION');
    if ($fromEnvironment !== false && trim($fromEnvironment) !== '') {
        return strtolower(trim($fromEnvironment, " \t\r\n\"'"));
    }

    $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
    if (is_readable($envFile)) {
        $contents = file_get_contents($envFile);
        if ($contents !== false && preg_match('/^\s*DB_CONNECTION\s*=\s*(.+)\s*$/m', $contents, $matches) === 1) {
            return strtolower(trim($matches[1], " \t\r\n\"'"));
        }
    }

    return 'sqlite'; // Matches the repository's .env.example default.
}

$connection = configuredConnection();
$requiredDriver = match ($connection) {
    'mysql', 'mariadb' => 'mysql',
    'sqlite' => 'sqlite',
    'pgsql' => 'pgsql',
    'sqlsrv' => 'sqlsrv',
    default => null,
};

$availableDrivers = class_exists(PDO::class) ? PDO::getAvailableDrivers() : [];
$ini = php_ini_loaded_file() ?: '(tidak ditemukan)';

echo 'PHP CLI: ' . PHP_VERSION . PHP_EOL;
echo 'PHP executable: ' . PHP_BINARY . PHP_EOL;
echo 'Loaded php.ini: ' . $ini . PHP_EOL;
echo 'Extension directory: ' . (ini_get('extension_dir') ?: '(tidak diketahui)') . PHP_EOL;
echo 'DB_CONNECTION: ' . $connection . PHP_EOL;
echo 'PDO drivers: ' . ($availableDrivers ? implode(', ', $availableDrivers) : '(tidak ada)') . PHP_EOL;

if ($requiredDriver === null) {
    fwrite(STDERR, "[ERROR] DB_CONNECTION tidak didukung oleh pemeriksa ini.\n");
    exit(2);
}

if (! in_array($requiredDriver, $availableDrivers, true)) {
    fwrite(STDERR, "[ERROR] Driver PDO {$requiredDriver} belum aktif pada PHP CLI ini.\n");
    if (PHP_OS_FAMILY === 'Windows') {
        fwrite(STDERR, "Windows: jalankan 'php --ini', periksa extension_dir dan aktifkan extension=pdo_{$requiredDriver} di php.ini.\n");
        fwrite(STDERR, "Pastikan ekstensi tersedia di folder ext untuk build PHP yang sama; restart php artisan serve.\n");
    } else {
        fwrite(STDERR, "Aktifkan/pasang ekstensi pdo_{$requiredDriver} untuk versi PHP yang sedang digunakan.\n");
    }
    fwrite(STDERR, "Periksa ulang: php -r \"print_r(PDO::getAvailableDrivers());\"\n");
    exit(1);
}

echo "[OK] Driver PDO {$requiredDriver} tersedia. Konektivitas dan kredensial database belum diuji.\n";
