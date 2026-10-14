<?php

namespace App\Enums;

/**
 * BenchSubmissionState Enum
 *
 * Tracks whether agencies are permitted to submit bench talent profiles directly to this job.
 */
enum BenchSubmissionState: string
{
    case DISABLED = 'disabled';
    case ENABLED = 'enabled';
    case PAUSED = 'paused';
}
