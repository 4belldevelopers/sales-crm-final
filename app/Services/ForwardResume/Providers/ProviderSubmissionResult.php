<?php

namespace App\Services\ForwardResume\Providers;

/**
 * Class ProviderSubmissionResult
 *
 * Intent: Encapsulates delivery submission response from external email service.
 */
class ProviderSubmissionResult
{
    public function __construct(
        public readonly bool $isSuccessful,
        public readonly ?string $providerMessageId,
        public readonly ?string $errorMessage = null,
        public readonly array $rawResponse = []
    ) {}

    public static function success(string $providerMessageId, array $rawResponse = []): self
    {
        return new self(true, $providerMessageId, null, $rawResponse);
    }

    public static function failure(string $errorMessage, array $rawResponse = []): self
    {
        return new self(false, null, $errorMessage, $rawResponse);
    }
}
