<?php

namespace App\Services\ForwardResume\Providers;

/**
 * Class ForwardResumeDispatchPayload
 *
 * Intent: Immutable DTO containing all necessary data for external email dispatch.
 */
class ForwardResumeDispatchPayload
{
    public function __construct(
        public readonly string $forwardRequestId,
        public readonly int $companyId,
        public readonly int $userId,
        public readonly array $to,
        public readonly string $subject,
        public readonly string $body,
        public readonly array $attachments, // array of ['filename' => string, 'bytes' => string, 'mime' => string]
        public readonly string $fromAddress,
        public readonly string $senderName,
        public readonly array $cc = [],
        public readonly ?string $replyTo = null,
        public readonly array $metadata = []
    ) {}
}
