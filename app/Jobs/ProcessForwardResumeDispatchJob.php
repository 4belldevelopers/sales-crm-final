<?php

namespace App\Jobs;

use App\Models\CoinReservation;
use App\Models\ForwardAttachment;
use App\Models\ForwardRequest;
use App\Models\OutboxEvent;
use App\Services\ForwardResume\ForwardResumeCoinService;
use App\Services\ForwardResume\ForwardResumeState;
use App\Services\ForwardResume\ForwardResumeStateMachine;
use App\Services\ForwardResume\Providers\ForwardResumeDispatchPayload;
use App\Services\ForwardResume\Providers\ForwardResumeProvider;
use App\Services\ForwardResumeUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class ProcessForwardResumeDispatchJob
 *
 * Intent: Horizon worker process executing forward resume email dispatch with
 * atomic Compare-And-Set (CAS) worker claim, provider idempotency, and ACID ledger commitment.
 */
class ProcessForwardResumeDispatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $forwardRequestUuid;
    public int $companyId;
    public int $userId;
    public array $to;
    public string $subject;
    public string $body;
    public array $attachmentUuids;
    public string $fromAddress;
    public string $senderName;
    public array $cc;
    public ?string $replyTo;
    public string $reservationId;
    public int $requiredCoins;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        string $forwardRequestUuid,
        int $companyId,
        int $userId,
        array $to,
        string $subject,
        string $body,
        array $attachmentUuids,
        string $fromAddress,
        string $senderName,
        array $cc,
        ?string $replyTo,
        string $reservationId,
        int $requiredCoins
    ) {
        $this->onQueue('emails');
        $this->forwardRequestUuid = $forwardRequestUuid;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->attachmentUuids = $attachmentUuids;
        $this->fromAddress = $fromAddress;
        $this->senderName = $senderName;
        $this->cc = $cc;
        $this->replyTo = $replyTo;
        $this->reservationId = $reservationId;
        $this->requiredCoins = $requiredCoins;
    }

    public function handle(
        ForwardResumeProvider $provider,
        ForwardResumeUploadService $uploadService,
        ForwardResumeCoinService $coinService
    ): void {
        $workerToken = (string) Str::uuid();

        // Step 1: Atomic CAS Worker Claim
        // Guarantees exactly one worker thread processes this forward request
        $affected = DB::table('forward_requests')
            ->where('uuid', $this->forwardRequestUuid)
            ->where('status', 'QUEUED')
            ->update([
                'status' => ForwardResumeState::PROCESSING,
                'worker_token' => $workerToken,
                'processing_started_at' => now(),
                'updated_at' => now(),
            ]);

        $forwardRequest = ForwardRequest::where('uuid', $this->forwardRequestUuid)->first();

        if ($affected === 0) {
            // Check if this worker already owns the claim or if it is already finished
            if (! $forwardRequest || $forwardRequest->worker_token !== $workerToken) {
                Log::info("[ProcessForwardResumeDispatchJob] CAS claim denied for {$this->forwardRequestUuid}. Another worker owns the job.");
                return;
            }
        }

        // Step 2: Stream PDF attachment bytes into memory (0 local disk writes)
        $inMemoryAttachments = [];
        $attachments = ForwardAttachment::whereIn('uuid', $this->attachmentUuids)
            ->where('company_id', $this->companyId)
            ->get();

        foreach ($attachments as $att) {
            $bytes = $uploadService->getR2ObjectBytes($att->r2_object_key);
            if ($bytes !== null && strlen($bytes) > 0) {
                $inMemoryAttachments[] = [
                    'filename' => $att->filename,
                    'bytes' => $bytes,
                    'mime' => $att->mime_type ?: 'application/pdf',
                ];
            } else {
                Log::warning("[ProcessForwardResumeDispatchJob] Failed to read attachment bytes for {$att->uuid}");
            }
        }

        // Step 3: Transition FSM to PROVIDER_SUBMITTING
        if ($forwardRequest) {
            ForwardResumeStateMachine::transition($forwardRequest, ForwardResumeState::PROVIDER_SUBMITTING, [
                'actor_type' => 'WORKER',
                'actor_id' => $this->userId,
                'metadata' => ['worker_token' => $workerToken],
            ]);
        }

        // Step 4: Dispatch via ForwardResumeProvider with canonical idempotency identity
        $payload = new ForwardResumeDispatchPayload(
            forwardRequestId: $this->forwardRequestUuid,
            companyId: $this->companyId,
            userId: $this->userId,
            to: $this->to,
            subject: $this->subject,
            body: $this->body,
            attachments: $inMemoryAttachments,
            fromAddress: $this->fromAddress,
            senderName: $this->senderName,
            cc: $this->cc,
            replyTo: $this->replyTo
        );

        $submissionResult = $provider->send($payload);

        if (! $submissionResult->isSuccessful) {
            Log::error("[ProcessForwardResumeDispatchJob] Provider delivery failed: " . $submissionResult->errorMessage);

            // Trigger refund and release reserved coins
            $coinService->release($this->companyId, $this->reservationId);

            if ($forwardRequest) {
                ForwardResumeStateMachine::transition($forwardRequest, ForwardResumeState::REFUND_PENDING, [
                    'actor_type' => 'WORKER',
                    'actor_id' => $this->userId,
                    'metadata' => ['error' => $submissionResult->errorMessage],
                ]);
            }
            return;
        }

        // Step 5: Provider Accepted
        if ($forwardRequest) {
            ForwardResumeStateMachine::transition($forwardRequest, ForwardResumeState::PROVIDER_ACCEPTED, [
                'actor_type' => 'WORKER',
                'actor_id' => $this->userId,
                'metadata' => ['provider_message_id' => $submissionResult->providerMessageId],
            ]);
        }

        // Step 6: Authoritative Ledger Commit & Outbox Event Completion
        $coinService->commit(
            $this->companyId,
            $this->userId,
            $this->forwardRequestUuid,
            $this->reservationId,
            $this->requiredCoins
        );

        if ($forwardRequest) {
            ForwardResumeStateMachine::transition($forwardRequest, ForwardResumeState::LEDGER_COMMITTED, [
                'actor_type' => 'WORKER',
                'actor_id' => $this->userId,
            ]);
            $forwardRequest->update(['completed_at' => now()]);
        }

        // Mark Outbox Event as PUBLISHED
        OutboxEvent::where('aggregate_id', $this->forwardRequestUuid)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'PUBLISHED',
                'published_at' => now(),
            ]);

        Log::info("[ProcessForwardResumeDispatchJob] Forward request {$this->forwardRequestUuid} successfully dispatched and ledger committed.");
    }
}
