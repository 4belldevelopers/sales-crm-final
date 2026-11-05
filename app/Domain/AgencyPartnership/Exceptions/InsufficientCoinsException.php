<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class InsufficientCoinsException extends AgencyPartnershipException
{
    public function __construct(string $message = 'Insufficient coin balance. Each agency invitation requires 5 coins.')
    {
        parent::__construct($message, 402);
    }
}
