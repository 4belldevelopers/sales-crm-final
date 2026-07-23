<?php

namespace App\Services;

use App\Exceptions\ResumeUploadException;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Synchronous, in-place resume writer for JERO.
 *
 * Mode:
 *   - uploadDirect(...)         — write the candidate's chosen file to its FINAL
 *                                 R2 path on file pick. Returns the public URL
 *                                 the controller persists to JobApplication.resume.
 *   - headCheck(string $path)    — Storage::exists + size>0. Called from the
 *                                 submit() path (a few hundred ms after pick)
 *                                 to verify the URL the form sent us still
 *                                 resolves to a real file before committing the
 *                                 transaction.
 *
 * Path shape (unchanged from the prior two-stage design for migration safety):
 *   Companies/{slug_co}_{co_id}/Resumes/{Y}/{m}/{uniqid_fn_{ext}}
 * URL shape:  https://file.jero.works/{path}
 *
 * Why one-stage instead of temp→copy:
 *   Submit becomes ~200 ms (one HEAD + one DB transaction) instead of
 *   ~1-2 s (copy + verify + DB transaction). The form-filling interval
 *   (~20 s user time) becomes the in-flight durability window.
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
     * Write the candidate's file directly to its final R2 location. Returns
     * the canonical URL to be persisted on JobApplication.resume.
     *
     * Accepts either an UploadedFile or raw binary content (string). The
     * controller's /uploadresume passes an UploadedFile; the controller's
     * store()/submit handler can pass raw content for the small multipart
     * fallback path that bypasses the AJAX pre-upload.
     *
     * Contract:
     *   - throws ResumeUploadException for: null/empty content, missing
     *     filename, write failure, post-write verification failure.
     *   - returns ['path' => ..., 'url' => ..., 'final_name' => ...] on
     *     success.
     *   - never silently writes a phantom file: anything that returns has
     *     been verified.
     */
    public function uploadDirect(
        UploadedFile|string $content,
        ?string $originalName,
        string $companyName,
        string|int $companyId,
        ?Carbon $when = null
    ): array {
        if ($originalName === null || $originalName === '') {
            throw new ResumeUploadException('', 'invalid_filename', 'Resume upload requires a filename.');
        }

        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension === '') {
            // Fall back to mime-based detection for the multipart path.
            if ($content instanceof UploadedFile) {
                $ext = $content->getClientOriginalExtension();
                $extension = strtolower($ext);
            }
        }
        if ($extension === '') {
            $extension = 'bin';
        }

        $finalName = uniqid('JERO_', true) . '.' . $extension;
        $destination = $this->computeDestination($companyName, $companyId, $finalName, $when);

        try {
            if ($content instanceof UploadedFile) {
                $bytes = file_get_contents($content->getRealPath());
            } else {
                $bytes = $content;
            }
            if ($bytes === false || $bytes === '') {
                throw new ResumeUploadException($finalName, 'content_empty', 'Resume file is empty.');
            }
            $this->storage->put($destination['path'], $bytes);
        } catch (ResumeUploadException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ResumeUploadException($destination['path'], 'write_failed', "Could not write resume to {$destination['path']}: {$e->getMessage()}", 0, $e);
        }

        if (! $this->storage->exists($destination['path'])) {
            throw new ResumeUploadException($destination['path'], 'post_write_missing', "Resume final path missing after write: {$destination['path']}");
        }

        if ((int) $this->storage->size($destination['path']) <= 0) {
            throw new ResumeUploadException($destination['path'], 'post_write_empty', "Resume final path is empty after write: {$destination['path']}");
        }

        return [
            'path'       => $destination['path'],
            'url'        => $destination['url'],
            'final_name' => $finalName,
        ];
    }

    /**
     * Legacy two-stage helper kept so the 5 deferred `UploadResumeToTestRTwo`
     * dispatch sites (Staffing, Product, Admin, Bench, HomeController) can be
     * migrated without an extra name change. New code should call
     * uploadDirect() instead.
     *
     * Simulates the original temp→final copy contract for callers that still
     * assume the temp directory. NOT used by the service-company controller's
     * /uploadresume or store() — those go through uploadDirect + headCheck.
     */
    public function finalizeFromTemp(
        string $tempPath,
        string $finalName,
        string $companyName,
        string|int $companyId,
        ?Carbon $when = null
    ): string {
        if ($tempPath === '' || $tempPath === null) {
            throw new ResumeUploadException($tempPath, 'temp_missing', "Resume temp path is empty.");
        }
        if (! $this->storage->exists($tempPath)) {
            throw new ResumeUploadException($tempPath, 'temp_missing', "Resume temp file not found in storage: {$tempPath}", 0);
        }
        if ((int) $this->storage->size($tempPath) <= 0) {
            throw new ResumeUploadException($tempPath, 'temp_empty', "Resume temp file is empty: {$tempPath}");
        }

        $destination = $this->computeDestination($companyName, $companyId, $finalName, $when);

        try {
            $this->storage->copy($tempPath, $destination['path']);
        } catch (Throwable $e) {
            throw new ResumeUploadException($tempPath, 'copy_failed', "Could not copy resume from {$tempPath} to {$destination['path']}: {$e->getMessage()}", 0, $e);
        }

        if (! $this->storage->exists($destination['path'])) {
            throw new ResumeUploadException($tempPath, 'post_copy_missing', "Resume final path missing after copy: {$destination['path']}");
        }
        if ((int) $this->storage->size($destination['path']) <= 0) {
            throw new ResumeUploadException($tempPath, 'post_copy_missing', "Resume final path is empty after copy: {$destination['path']}");
        }

        return $destination['url'];
    }

    /**
     * Best-effort delete of a temp file after finalization. Never throws —
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
     * Verify-at-submit check. Called by the controller from inside the DB
     * transaction to ensure the URL the form sent still resolves to a real
     * file. ~50-100 ms on R2.
     *
     * Returns true if the path exists AND has non-zero size.
     * Returns false silently for the empty/null case — the controller
     * decides whether that's an error or a no-resume submission.
     */
    public function headCheck(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }
        try {
            if (! $this->storage->exists($path)) {
                return false;
            }
            return (int) $this->storage->size($path) > 0;
        } catch (Throwable $e) {
            // Head-check failures are not fatal — the row was already
            // asserted valid at pick time. Log and treat as missing.
            Log::warning('ResumeUploadService: headCheck failed', [
                'path'       => $path,
                'exception'  => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Pure path builder. Mirrors what finalizeFromTemp computed so existing
     * rows (written by the two-stage flow) live in the same R2 directories
     * as new rows (written by uploadDirect).
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
