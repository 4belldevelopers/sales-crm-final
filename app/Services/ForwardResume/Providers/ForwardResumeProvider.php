<?php

namespace App\Services\ForwardResume\Providers;

/**
 * Interface ForwardResumeProvider
 *
 * Intent: Provider abstraction enforcing canonical idempotency and lookup contract
 * for Forward Resume email dispatch.
 */
interface ForwardResumeProvider
{
    /**
     * Dispatches email with attachments using an immutable idempotency key.
     * The concrete provider adapter MUST implement idempotent submission
     * based on forward_request_id.
     */
    public function send(ForwardResumeDispatchPayload $payload): ProviderSubmissionResult;

    /**
     * Queries external provider logs/tags by forwardRequestId to recover delivery outcome
     * if the worker crashed before capturing provider_message_id.
     */
    public function findByIdempotencyKey(string $forwardRequestId): ?ProviderSubmissionResult;
}
