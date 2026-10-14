<?php

namespace App\Enums;

/**
 * LegacyMarketStatus Enum
 *
 * Compatibility mapping for legacy public_market_status integer column.
 * Strictly read-only projection in V2.2.
 */
enum LegacyMarketStatus: int
{
    case PRIVATE = 0;
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
    case VENDOR_LIMIT = 4;
    case SUSPENDED = 5;
    case UNPUBLISHED = 6;
}
