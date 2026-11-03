<?php

namespace App\Enums;

/**
 * DeliveryStatus Backed Enum
 *
 * Tracks transport and dispatch state for team invitation emails,
 * strictly independent from core business lifecycle status (INV-04).
 */
enum DeliveryStatus: string
{
    case QUEUED = 'QUEUED';
    case SENT = 'SENT';
    case DELIVERED = 'DELIVERED';
    case BOUNCED_SOFT = 'BOUNCED_SOFT';
    case BOUNCED_HARD = 'BOUNCED_HARD';
    case FAILED = 'FAILED';

    public function isDelivered(): bool
    {
        return $this === self::DELIVERED;
    }

    public function isBounced(): bool
    {
        return in_array($this, [self::BOUNCED_SOFT, self::BOUNCED_HARD], true);
    }
}
