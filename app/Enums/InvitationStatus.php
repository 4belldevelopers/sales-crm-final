<?php

namespace App\Enums;

/**
 * InvitationStatus Backed Enum
 *
 * Defines authoritative business lifecycle states for team invitations,
 * strictly decoupled from transport delivery states (INV-04).
 */
enum InvitationStatus: string
{
    case PENDING = 'PENDING';
    case LINK_ACCESSED = 'LINK_ACCESSED';
    case ACTIVATED = 'ACTIVATED';
    case EXPIRED = 'EXPIRED';
    case REVOKED = 'REVOKED';
    case DECLINED = 'DECLINED';

    /**
     * Determine if an invitation in this state can be accessed/activated.
     */
    public function isAcceptable(): bool
    {
        return in_array($this, [self::PENDING, self::LINK_ACCESSED], true);
    }

    /**
     * Determine if the invitation represents an active token slot.
     */
    public function isActiveSlot(): bool
    {
        return in_array($this, [self::PENDING, self::LINK_ACCESSED], true);
    }
}
