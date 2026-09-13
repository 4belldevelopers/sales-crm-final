<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class CleanupTempAttachments
 *
 * Automated Garbage Collector for client-compiled resume attachments in storage/app/temp-attachments.
 * Intent: Safely purges temporary PDF files older than 2 hours to guarantee zero disk leaks over time.
 * Tenancy: Iterates tenant company subdirectories under storage/app/temp-attachments/{company_id}.
 *
 * Operational:
 *   Scheduled hourly via routes/console.php:
 *   Schedule::command('temp-attachments:clean')->hourly();
 */
class CleanupTempAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp-attachments:clean {--hours=2 : Purge files older than this many hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary forwarded resume attachments from storage/app/temp-attachments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $baseDir = storage_path('app/temp-attachments');
        if (! is_dir($baseDir)) {
            $this->info('No temp-attachments directory found. Nothing to clean.');

            return self::SUCCESS;
        }

        $hours = (int) $this->option('hours');
        if ($hours <= 0) {
            $hours = 2;
        }
        $maxAgeSeconds = $hours * 3600;
        $now = time();
        $deletedFiles = 0;
        $deletedDirs = 0;

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isFile()) {
                $filePath = $item->getRealPath();
                $mtime = @filemtime($filePath);
                if ($mtime !== false && ($now - $mtime) > $maxAgeSeconds) {
                    if (@unlink($filePath)) {
                        $deletedFiles++;
                    }
                }
            } elseif ($item->isDir()) {
                $dirPath = $item->getRealPath();
                $contents = @scandir($dirPath);
                if ($contents !== false && count($contents) <= 2) {
                    if (@rmdir($dirPath)) {
                        $deletedDirs++;
                    }
                }
            }
        }

        $this->info("Cleaned up {$deletedFiles} temporary attachments and {$deletedDirs} empty company directories (older than {$hours}h).");

        return self::SUCCESS;
    }
}
