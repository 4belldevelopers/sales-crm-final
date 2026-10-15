<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Class IllegalStateTransitionException
 *
 * Thrown when an illegal state transition is attempted on a marketplace channel
 * (e.g. attempting to jump from REJECTED directly to LIVE without moderation).
 */
class IllegalStateTransitionException extends RuntimeException
{
}
