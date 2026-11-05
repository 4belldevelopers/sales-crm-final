<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class UnauthorizedAcceptanceException extends AgencyPartnershipException
{
    public function __construct(string $message = 'You do not have authorization to accept partnership invitations for this organization.')
    {
        parent::__construct($message, 403);
    }
}
