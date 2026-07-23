<?php

namespace App\Services;

use App\Exceptions\ResumeUploadException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Synchronously moves a resume from a temp upload location on R2 to its
 * permanent path under Companies/{co_slug}_{co_id}/Resumes/{y}/{m}/{name},
 * verifies the destination exists and has non-zero size, and returns the
 * canonical URL to be persisted on JobApplication.resume.
 *
 * Used by ServiceCompanyJobApplicationController::store() inside the same
 * DB transaction that writes the JobApplication row, so the resume column
 * is never committed without a verified file behind it.
 */
class ResumeUploadService
{
    private string $disk;
    private \Illuminate\Contracts\Filesystem\Filesystem $storage;

    public function __construct(string $disk = 'r2', ?\Illuminate\Contracts\Filesystem\Filesystem $storage = null)
    {
        $this->disk = $disk;
        // Fall back to the facade so unit tests can pass Storage::fake($disk).
        $this->storage = $storage ?? Storage::disk($disk);
    }

    /**
     * Copy a temp upload to its permanent destination and verify it.
     *
     * Contract:
     *  - throws ResumeUploadException for: missing/empty temp, copy failure,
     *    missing/empty final path.
     *  - returns the canonical public URL on success.
     *  - never returns silently with a phantom URL.
     */
    public function finalizeFromTemp(string $tempPath, string $finalName, string $companyName, string|int $companyId, ?Carbon $when = null): string
    {
        if ($tempPath === '' || $tempPath === null) {
            throw ResumeUploadException::missingInR2((string) $tempPath);
        }

        if (! $this->storage->exists($tempPath)) {
            throw ResumeUploadException::missingInR2($tempPath);
        }

        if ((int) $this->storage->size($tempPath) <= 0) {
            throw ResumeUploadException::empty($tempPath);
        }

        $destination = $this->computeDestination($companyName, $companyId, $finalName, $when);

        try {
            $this->storage->copy($tempPath, $destination['path']);
        } catch (Throwable $e) {
            throw ResumeUploadException::copyFailed($tempPath, $destination['path'], $e);
        }

        if (! $this->storage->exists($destination['path'])) {
            throw ResumeUploadException::postCopyMissing($tempPath, $destination['path']);
        }

        if ((int) $this->storage->size($destination['path']) <= 0) {
            throw ResumeUploadException::postCopyMissing($tempPath, $destination['path']);
        }

        return $destination['url'];
    }

    /**
     * Best-effort delete of the temp file after finalization. Never throws —
     * a leftover temp file is harmless and will be overwritten on next upload.
     */
    public function cleanTemp(string $tempPath): void
    {
        if ($tempPath === '' || $tempPath === null) {
            return;
        }
        try {
            if ($this->storage->exists($tempPath)) {
                $this->storage->delete($tempPath);
            }
        } catch (Throwable $e) {
            Log::warning('ResumeUploadService: failed to clean temp', [
                'temp_path' => $tempPath,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Pure path builder mirroring the controller's existing sanitization so
     * new files land in the same R2 directories as historical uploads:
     * Companies/{slug_company_name}_{company_id}/Resumes/{Y}/{m}/{name}.
     */
    public function computeDestination(string $companyName, string|int $companyId, string $finalName, ?Carbon $when = null): array
    {
        $slug = strtolower(trim((string) $companyName));
        $slug = preg_replace('/[^a-z0-9_\-]/i', '_', $slug ?? '');
        $slug = $slug !== '' ? $slug : 'unknown';
        $companySegment = $slug . '_' . (string) $companyId;

        $when = $when ?? now();
        $year = $when->format('Y');
        $month = $when->format('m');

        $path = "Companies/{$companySegment}/Resumes/{$year}/{$month}/{$finalName}";
        $url = "https://file.jero.works/{$path}";

        return ['path' => $path, 'url' => $url];
    }
}
