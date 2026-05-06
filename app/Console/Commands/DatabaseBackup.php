<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

#[Signature('app:database-backup')]
#[Description('Create database and file backups')]
class DatabaseBackup extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupDir = storage_path('app/backups');
        $keepDays = 30;

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $dbBackupFile = $backupDir . '/db_backup_' . $timestamp . '.sql';

        try {
            // Database backup using mysqldump
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                $dbHost,
                $dbUser,
                $dbPass,
                $dbName,
                $dbBackupFile
            );

            exec($command, $output, $exitCode);

            if ($exitCode !== 0) {
                throw new \Exception("Database backup failed with exit code: $exitCode");
            }

            // Compress the backup
            if (File::exists($dbBackupFile)) {
                exec("gzip {$dbBackupFile}");
                $dbBackupFile .= '.gz';
            }

            $this->info("Database backup completed successfully: " . basename($dbBackupFile));

            // Clean up old backups
            $cutoffDate = now()->subDays($keepDays);
            $files = File::glob($backupDir . '/*_backup_*.gz');

            foreach ($files as $file) {
                if (File::lastModified($file) < $cutoffDate->timestamp) {
                    File::delete($file);
                    $this->info("Deleted old backup: " . basename($file));
                }
            }

            $this->info("Old backups cleaned up (keeping last {$keepDays} days)");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
