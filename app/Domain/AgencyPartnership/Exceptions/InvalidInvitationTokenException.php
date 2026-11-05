<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class InvalidInvitationTokenException extends AgencyPartnershipException
{
    public function __construct(string $message = 'Invitation link is invalid, already used, or expired.')
    {
        parent::__construct($message, 410);
    }
}
