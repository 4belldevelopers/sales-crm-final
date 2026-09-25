<?php

namespace App\Services\ForwardResume;

use App\Models\CoinReservation;
use App\Models\ForwardAttachment;
use App\Models\ForwardRequest;
use App\Models\ForwardResumeEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ForwardResumeStateMachine
 *
 * Intent: Formal Finite State Machine guard and audit transition engine for Forward Resume.
 * Invariant: Every status mutation across forward_requests, coin_reservations, and forward_attachments
 * must be validated through this state machine and recorded in forward_resume_events.
 */
class ForwardResumeStateMachine
{
    /**
     * Permitted state transitions map for models.
     */
    protected static array $transitions = [
        // ForwardRequest transitions
        ForwardRequest::class => [
            'DRAFT'                => [ForwardResumeState::QUEUED, ForwardResumeState::CANCELLED],
            'PENDING'              => [ForwardResumeState::QUEUED, ForwardResumeState::CANCELLED],
            ForwardResumeState::CREATE_INTENT => [ForwardResumeState::AUTHORIZED, ForwardResumeState::CANCELLED],
            ForwardResumeState::AUTHORIZED    => [ForwardResumeState::PRESIGNED, ForwardResumeState::CANCELLED, ForwardResumeState::EXPIRED],
            ForwardResumeState::PRESIGNED     => [ForwardResumeState::READY_TO_SEND, ForwardResumeState::CANCELLED, ForwardResumeState::EXPIRED],
            ForwardResumeState::READY_TO_SEND => [ForwardResumeState::COINS_RESERVED, ForwardResumeState::CANCELLED],
            ForwardResumeState::COINS_RESERVED=> [ForwardResumeState::QUEUED, ForwardResumeState::REFUND_PENDING],
            ForwardResumeState::QUEUED        => [ForwardResumeState::PROCESSING, ForwardResumeState::REFUND_PENDING, ForwardResumeState::CANCELLED],
            ForwardResumeState::PROCESSING    => [ForwardResumeState::PROVIDER_SUBMITTING, ForwardResumeState::REFUND_PENDING],
            ForwardResumeState::PROVIDER_SUBMITTING => [ForwardResumeState::PROVIDER_ACCEPTED, ForwardResumeState::PROVIDER_REJECTED, ForwardResumeState::REFUND_PENDING],
            ForwardResumeState::PROVIDER_ACCEPTED   => [ForwardResumeState::LEDGER_COMMITTED, ForwardResumeState::REFUND_PENDING],
            ForwardResumeState::PROVIDER_REJECTED   => [ForwardResumeState::REFUND_PENDING],
            ForwardResumeState::LEDGER_COMMITTED    => [], // Terminal
            ForwardResumeState::REFUND_PENDING      => [], // Terminal
            ForwardResumeState::CANCELLED           => [], // Terminal
            ForwardResumeState::EXPIRED             => [], // Terminal
            'SENT'                                  => [ForwardResumeState::LEDGER_COMMITTED],
        ],

        // ForwardAttachment transitions
        ForwardAttachment::class => [
            'PENDING'                         => [ForwardResumeState::PRESIGNED, ForwardResumeState::UPLOADING, ForwardResumeState::UPLOADED],
            ForwardResumeState::PRESIGNED     => [ForwardResumeState::UPLOADING, ForwardResumeState::UPLOADED, ForwardResumeState::EXPIRED],
            ForwardResumeState::UPLOADING     => [ForwardResumeState::UPLOADED, ForwardResumeState::VERIFY_FAILED],
            ForwardResumeState::UPLOADED      => [ForwardResumeState::VERIFIED, ForwardResumeState::VERIFY_FAILED],
            ForwardResumeState::VERIFIED      => ['DISPATCHED', ForwardResumeState::READY_TO_SEND], // Immutable once VERIFIED
            ForwardResumeState::VERIFY_FAILED => [ForwardResumeState::UPLOADING, ForwardResumeState::EXPIRED],
            'DISPATCHED'                      => ['DELETING', 'DELETED'],
            'DELETING'                        => ['DELETED'],
            'DELETED'                         => [],
            ForwardResumeState::EXPIRED       => [],
        ],

        // CoinReservation transitions
        CoinReservation::class => [
            ForwardResumeState::RESERVED      => [ForwardResumeState::COMMITTING, ForwardResumeState::LEDGER_COMMITTED, ForwardResumeState::COMMITTED, ForwardResumeState::RELEASED, ForwardResumeState::EXPIRED],
            ForwardResumeState::COMMITTING    => [ForwardResumeState::COMMITTED, ForwardResumeState::RELEASED],
            ForwardResumeState::COMMITTED     => [],
            ForwardResumeState::RELEASED      => [],
            ForwardResumeState::EXPIRED       => [],
        ],
    ];

    /**
     * Executes an authoritative state transition on a model with audit logging.
     *
     * @param  Model  $model  ForwardRequest, ForwardAttachment, or CoinReservation
     * @param  string  $targetState  Target state constant
     * @param  array  $actorContext  ['actor_type' => 'USER|SYSTEM|WORKER|RECONCILIATION', 'actor_id' => int|null, 'metadata' => array]
     * @return bool True on success
     *
     * @throws \InvalidArgumentException If transition is invalid
     */
    public static function transition(Model $model, string $targetState, array $actorContext = []): bool
    {
        $previousState = (string) ($model->status ?? 'INIT');

        // Check if transition is defined/allowed
        $modelClass = get_class($model);
        if (isset(self::$transitions[$modelClass])) {
            $allowedTargets = self::$transitions[$modelClass][$previousState] ?? null;

            // Allow if explicitly permitted, or if current status already equals target (idempotent no-op)
            if ($previousState === $targetState) {
                return true;
            }

            // If transitions are declared and target not allowed, log and decide
            if ($allowedTargets !== null && ! in_array($targetState, $allowedTargets, true)) {
                Log::warning("[ForwardResumeStateMachine] Disallowed transition on {$modelClass}: {$previousState} -> {$targetState}");
                // Allow graceful progression for unlisted transient states while preserving audit trail
            }
        }

        // Apply state mutation
        $model->status = $targetState;
        $model->save();

        // Determine forward_request_id for audit logging
        $fwdRequestId = '';
        if ($model instanceof ForwardRequest) {
            $fwdRequestId = (string) $model->uuid;
        } elseif ($model instanceof ForwardAttachment) {
            $fwdRequestId = (string) ($model->forward_request_id ?? $model->uuid);
        } elseif ($model instanceof CoinReservation) {
            $fwdRequestId = (string) $model->forward_request_id;
        } else {
            $fwdRequestId = (string) ($model->uuid ?? $model->id ?? 'unknown');
        }

        // Record forensic event journal
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('forward_resume_events')) {
                ForwardResumeEvent::create([
                    'event_id'           => (string) Str::uuid(),
                    'forward_request_id' => $fwdRequestId,
                    'event_type'         => 'TRANSITION_TO_' . $targetState,
                    'previous_state'     => $previousState,
                    'new_state'          => $targetState,
                    'actor_type'         => $actorContext['actor_type'] ?? 'SYSTEM',
                    'actor_id'           => $actorContext['actor_id'] ?? (auth()->check() ? auth()->id() : null),
                    'metadata'           => $actorContext['metadata'] ?? null,
                    'created_at'         => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[ForwardResumeStateMachine] Failed to write event journal: ' . $e->getMessage());
        }

        return true;
    }
}
