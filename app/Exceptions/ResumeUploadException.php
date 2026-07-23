<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Signals that a resume could not be durably stored on the configured
 * storage disk. Raised by App\Services\ResumeUploadService when the
 * temp file is missing, empty, fails to copy, or the copy cannot be
 * verified afterwards. Surfaced to AJAX as HTTP 422 so the UI can
 * stop showing the misleading success state.
 */
class ResumeUploadException extends RuntimeException
{
    public string $tempPath;
    public string $reason;

    public function __construct(string $tempPath, string $reason, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->tempPath = $tempPath;
        $this->reason = $reason;
        parent::__construct($message !== '' ? $message : $reason, $code, $previous);
    }

    /** Temp file not present on the configured storage disk before copy. */
    public static function missingInR2(string $tempPath, ?Throwable $previous = null): self
    {
        return new self($tempPath, 'temp_missing', "Resume temp file not found in storage: {$tempPath}", 0, $previous);
    }

    /** Temp file present but its reported size is zero. */
    public static function empty(string $tempPath): self
    {
        return new self($tempPath, 'temp_empty', "Resume temp file is empty: {$tempPath}");
    }

    /** Storage copy from temp path to final path failed. */
    public static function copyFailed(string $tempPath, string $finalPath, Throwable $previous): self
    {
        return new self($tempPath, 'copy_failed', "Could not copy resume from {$tempPath} to {$finalPath}: {$previous->getMessage()}", 0, $previous);
    }

    /** Final path does not exist or its size is zero after copy. */
    public static function postCopyMissing(string $tempPath, string $finalPath): self
    {
        return new self($tempPath, 'post_copy_missing', "Resume final path missing or empty after copy: {$finalPath}");
    }

    /** Resume URL the form submitted cannot be verified on R2 at submit time. */
    public static function submitMissing(string $url): self
    {
        return new self($url, 'submit_missing', "Resume URL submitted by the form is missing or unreadable on storage: {$url}");
    }
}
