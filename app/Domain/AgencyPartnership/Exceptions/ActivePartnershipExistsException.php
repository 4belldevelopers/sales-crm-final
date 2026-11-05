<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class ActivePartnershipExistsException extends AgencyPartnershipException
{
    public function __construct(string $message = 'This agency is already an active connected partner.')
    {
        parent::__construct($message, 409);
    }
}
