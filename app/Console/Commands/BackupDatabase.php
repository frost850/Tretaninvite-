<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature   = 'db:backup {--keep=7 : Jumlah file backup yang disimpan (default: 7 hari)}';
    protected $description = 'Backup database MySQL ke storage/backups/ dan hapus file lama.';

    public function handle(): int
    {
        $connection = config('database.default');
        if ($connection !== 'mysql') {
            $this->error("Backup otomatis hanya mendukung MySQL. Koneksi saat ini: {$connection}");
            return self::FAILURE;
        }

        $host     = config('database.connections.mysql.host', '127.0.0.1');
        $port     = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        if (empty($database) || empty($username)) {
            $this->error('Konfigurasi database tidak lengkap (DB_DATABASE / DB_USERNAME).');
            return self::FAILURE;
        }

        // Pastikan direktori backup ada
        $backupDir = storage_path('backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0750, true);
        }

        $filename  = sprintf('backup_%s_%s.sql.gz', $database, now()->format('Y-m-d_His'));
        $filepath  = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Bangun perintah mysqldump
        $passwordArg = $password !== ''
            ? '-p' . escapeshellarg($password)
            : '';

        $cmd = sprintf(
            'mysqldump --single-transaction --quick --lock-tables=false -h %s -P %s -u %s %s %s | gzip > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($filepath) || filesize($filepath) < 100) {
            $detail = implode(' ', $output);
            Log::error('Database backup failed', ['exit_code' => $exitCode, 'detail' => $detail]);
            $this->error("Backup gagal (exit code: {$exitCode}). Lihat log untuk detail.");
            return self::FAILURE;
        }

        $sizeMb = round(filesize($filepath) / 1024 / 1024, 2);
        $this->info("✅ Backup selesai: {$filename} ({$sizeMb} MB)");
        Log::info('Database backup created', ['file' => $filename, 'size_mb' => $sizeMb]);

        // Hapus backup lama melebihi limit --keep
        $keep    = (int) $this->option('keep');
        $pattern = $backupDir . DIRECTORY_SEPARATOR . 'backup_' . $database . '_*.sql.gz';
        $files   = glob($pattern);
        if ($files && count($files) > $keep) {
            sort($files); // urut ascending = terlama di depan
            $toDelete = array_slice($files, 0, count($files) - $keep);
            foreach ($toDelete as $old) {
                unlink($old);
                $this->line('🗑  Dihapus: ' . basename($old));
                Log::info('Old backup deleted', ['file' => basename($old)]);
            }
        }

        return self::SUCCESS;
    }
}
