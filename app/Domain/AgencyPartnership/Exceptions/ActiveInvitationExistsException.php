<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class ActiveInvitationExistsException extends AgencyPartnershipException
{
    public function __construct(string $message = 'An active invitation is already pending for this agency recipient.')
    {
        parent::__construct($message, 409);
    }
}
