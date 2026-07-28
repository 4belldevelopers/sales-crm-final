<?php
namespace App\Enums;

// Intent: All valid states for a resume upload session, with transition rules.
// State machine: Pending → Uploading → Uploaded → Completed
// Any non-terminal state can → Failed | Cancelled | Expired → Reconciled

enum ResumeUploadStatus: string
{
    case Pending     = 'Pending';     // Presigned URL created, upload hasn't started
    case Uploading   = 'Uploading';   // Browser is actively PUTting bytes to R2
    case Uploaded    = 'Uploaded';    // PUT completed, R2 object verified
    case Completed   = 'Completed';   // Candidate created and linked
    case Failed      = 'Failed';      // Parse/upload error, terminal
    case Cancelled   = 'Cancelled';   // User cancelled, terminal
    case Expired     = 'Expired';     // TTL passed, terminal
    case Reconciled  = 'Reconciled';  // Cron swept, terminal

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
            self::Cancelled,
            self::Expired,
            self::Reconciled,
        ], true);
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending    => in_array($target, [self::Uploading, self::Uploaded, self::Failed, self::Cancelled, self::Expired]),
            self::Uploading  => in_array($target, [self::Uploaded, self::Failed, self::Cancelled, self::Expired]),
            self::Uploaded   => in_array($target, [self::Completed, self::Failed, self::Expired]),
            self::Completed  => false,
            self::Failed     => false,
            self::Cancelled  => false,
            self::Expired    => in_array($target, [self::Reconciled]),
            self::Reconciled => false,
        };
    }

    public static function validStatuses(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
