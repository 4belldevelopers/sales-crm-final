<?php

namespace App\Services\ForwardResume;

/**
 * Class ForwardResumeState
 *
 * Intent: Defines all canonical finite state machine states for the Forward Resume pipeline.
 * Tenancy: Immutable state definitions shared across multi-tenant dispatch operations.
 */
final class ForwardResumeState
{
    public const CREATE_INTENT        = 'CREATE_INTENT';
    public const AUTHORIZED           = 'AUTHORIZED';
    public const PRESIGNED            = 'PRESIGNED';
    public const UPLOADING            = 'UPLOADING';
    public const UPLOADED             = 'UPLOADED';
    public const VERIFIED             = 'VERIFIED';
    public const VERIFY_FAILED        = 'VERIFY_FAILED';
    public const READY_TO_SEND        = 'READY_TO_SEND';
    public const COINS_RESERVED       = 'COINS_RESERVED';
    public const RESERVED             = 'RESERVED';
    public const COMMITTING           = 'COMMITTING';
    public const COMMITTED            = 'COMMITTED';
    public const RELEASED             = 'RELEASED';
    public const QUEUED               = 'QUEUED';
    public const PROCESSING           = 'PROCESSING';
    public const PROVIDER_SUBMITTING  = 'PROVIDER_SUBMITTING';
    public const PROVIDER_ACCEPTED    = 'PROVIDER_ACCEPTED';
    public const PROVIDER_REJECTED    = 'PROVIDER_REJECTED';
    public const LEDGER_COMMITTED     = 'LEDGER_COMMITTED';
    public const REFUND_PENDING       = 'REFUND_PENDING';
    public const CANCELLED            = 'CANCELLED';
    public const EXPIRED              = 'EXPIRED';

    // Disallowed direct instantiation
    private function __construct() {}
}
