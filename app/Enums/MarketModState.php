<?php

namespace App\Enums;

/**
 * MarketModState Enum
 *
 * Defines the SuperAdmin governance & risk engine moderation verdict.
 */
enum MarketModState: string
{
    case NOT_REQUIRED = 'not_required';
    case PENDING = 'pending';
    case AUTO_APPROVED = 'auto_approved';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';
}
