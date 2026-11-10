<?php

namespace App\Services\Zoom\Exceptions;

use RuntimeException;

/**
 * Class ZoomApiException
 *
 * Intent: General exception representing an unexpected Zoom API response or network failure.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomApiException extends RuntimeException
{
    protected int $statusCode;

    protected array $responseBody;

    public function __construct(string $message, int $statusCode = 500, array $responseBody = [], ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
    }
}
