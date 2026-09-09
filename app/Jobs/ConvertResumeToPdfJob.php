<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Intent: Convert DOC/DOCX resumes outside web requests and cache the converted
 * PDF in R2 so the Resume Center can scale without pinning Octane workers.
 */
class ConvertResumeToPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 60;

    public int $timeout = 180;

    public function __construct(
        private readonly string $resume,
        private readonly string $hash,
        private readonly string $ext
    ) {
        $this->onQueue('uploads');
    }

    public function handle(): void
    {
        $statusKey = 'resume_pdf_convert_status:'.$this->hash;
        $r2CacheKey = 'Resumes/converted_'.$this->hash.'.pdf';

        Cache::put($statusKey, 'processing', now()->addMinutes(30));

        $lock = Cache::lock('resume_pdf_convert_run:'.$this->hash, 120);
        if (! $lock->get()) {
            return;
        }

        try {
            if (Storage::disk('r2')->exists($r2CacheKey)) {
                Cache::put($statusKey, 'ready', now()->addDay());

                return;
            }

            $bytes = $this->fetchResumeBytes($this->resume);
            if ($bytes === null || strlen($bytes) < 10) {
                Cache::put($statusKey, 'missing', now()->addMinutes(15));

                return;
            }

            $cacheDir = storage_path('app/resume-pdf-cache');
            if (! is_dir($cacheDir)) {
                @mkdir($cacheDir, 0775, true);
            }

            $srcFile = $cacheDir.DIRECTORY_SEPARATOR.$this->hash.'.'.$this->ext;
            $pdfCache = $cacheDir.DIRECTORY_SEPARATOR.$this->hash.'.pdf';
            file_put_contents($srcFile, $bytes);

            $soffice = $this->sofficeBinary();
            if (! $soffice) {
                @unlink($srcFile);
                Cache::put($statusKey, 'failed', now()->addMinutes(15));
                Log::warning('Resume PDF converter unavailable', ['hash' => $this->hash]);

                return;
            }

            $profile = 'file:///'.str_replace('\\', '/', $cacheDir.DIRECTORY_SEPARATOR.'lo_'.$this->hash);
            $this->runConversion($soffice, $profile, $cacheDir, $srcFile);
            @unlink($srcFile);

            if (! file_exists($pdfCache) || filesize($pdfCache) < 100) {
                Cache::put($statusKey, 'failed', now()->addMinutes(15));

                return;
            }

            Storage::disk('r2')->put($r2CacheKey, file_get_contents($pdfCache));
            Cache::put($statusKey, 'ready', now()->addDay());
        } catch (\Throwable $e) {
            Cache::put($statusKey, 'failed', now()->addMinutes(15));
            Log::warning('Resume PDF conversion job failed', [
                'hash' => $this->hash,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } finally {
            optional($lock)->release();
        }
    }

    private function fetchResumeBytes(string $resume): ?string
    {
        try {
            if (strpos($resume, 'https://file.jero.works/') === 0) {
                $key = ltrim((string) parse_url($resume, PHP_URL_PATH), '/');

                return Storage::disk('r2')->exists($key) ? Storage::disk('r2')->get($key) : null;
            }

            if (! filter_var($resume, FILTER_VALIDATE_URL)) {
                $key = ltrim($resume, '/');

                return Storage::disk('r2')->exists($key) ? Storage::disk('r2')->get($key) : null;
            }

            $response = Http::timeout(15)->withOptions(['verify' => false])->get($resume);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable $e) {
            Log::warning('Resume conversion source fetch failed', [
                'hash' => $this->hash,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function sofficeBinary(): ?string
    {
        $candidates = [
            config('services.soffice.path'),
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            '/usr/bin/soffice',
            '/usr/bin/libreoffice',
            '/opt/libreoffice/program/soffice',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate && @file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function runConversion(string $soffice, string $profile, string $cacheDir, string $srcFile): void
    {
        $process = new Process([
            $soffice,
            '--headless',
            '--norestore',
            '--nofirststartwizard',
            '-env:UserInstallation='.$profile,
            '--convert-to',
            'pdf',
            '--outdir',
            $cacheDir,
            $srcFile,
        ]);
        $process->setTimeout(45);
        $process->run();
    }
}
