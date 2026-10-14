<?php

namespace App\Enums;

/**
 * CommercialState Enum
 *
 * Tracks the commercial configuration and terms status between employer and agencies.
 */
enum CommercialState: string
{
    case CONFIGURED = 'configured';
    case INCOMPLETE = 'incomplete';
    case LOCKED = 'locked';
    case DISPUTED = 'disputed';
}
