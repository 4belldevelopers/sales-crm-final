<?php

namespace App\Services\Zoom\Exceptions;

use RuntimeException;

/**
 * Class ZoomAuthenticationException
 *
 * Intent: Thrown when Zoom Server-to-Server OAuth credentials fail validation
 * or return HTTP 401/403 authorization rejections.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomAuthenticationException extends RuntimeException
{
    protected ?string $errorCode;

    public function __construct(string $message = 'Failed to authenticate with Zoom Server-to-Server OAuth.', ?string $errorCode = null, ?\Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, 401, $previous);
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
}
