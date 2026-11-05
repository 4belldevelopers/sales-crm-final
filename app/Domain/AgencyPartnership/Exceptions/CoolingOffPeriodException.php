<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class CoolingOffPeriodException extends AgencyPartnershipException
{
    public function __construct(string $message = 'This agency recently declined your invitation. A 7-day cooling-off period applies.')
    {
        parent::__construct($message, 422);
    }
}
