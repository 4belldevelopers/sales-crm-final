<?php

namespace App\Domain\AgencyPartnership\Exceptions;

class DailyQuotaExceededException extends AgencyPartnershipException
{
    public function __construct(string $message = 'Daily partnership invitation limit reached (20/day). Please try again tomorrow.')
    {
        parent::__construct($message, 429);
    }
}
