<?php

namespace App\Enums;

/**
 * ClaimCapacity Enum
 *
 * Tracks whether the job has reached its max concurrent agency partner limit.
 */
enum ClaimCapacity: string
{
    case OPEN = 'open';
    case LIMIT_REACHED = 'limit_reached';
}
