<?php

namespace App\Domain\AgencyPartnership\Exceptions;

use Exception;

class AgencyPartnershipException extends Exception
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode = 400, ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
