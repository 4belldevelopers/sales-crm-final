<?php

namespace App\Jobs;

use App\Models\EmailSetting;
use App\Models\ForwardAttachment;
use App\Models\ForwardDeliveryAttempt;
use App\Models\ForwardRequest;
use App\Services\ForwardResumeUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendForwardResumeBatchMailJob
 *
 * Intent: Executes email delivery for forwarded candidate resumes.
 * Invariants:
 * - Streams PDF bytes directly from private Cloudflare R2 into RAM (0 local disk writes).
 * - Enforces Delivery Barrier via forward_delivery_attempts table (no race conditions).
 * - Enforces Two-Phase Cleanup: READY_FOR_CLEANUP -> DELETED on Cloudflare R2 confirmation.
 * - Enforces Traceability Message-ID and Application-Level Idempotency.
 */
class SendForwardResumeBatchMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $forwardRequestUuid;

    public $to;

    public $subject;

    public $view;

    public $data;

    public $attachmentUuids;

    public $fromAddress;

    public $senderName;

    public $cc;

    public $companyId;

    public $replyTo;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $forwardRequestUuid,
        array $to,
        string $subject,
        string $view,
        $data,
        array $attachmentUuids,
        string $fromAddress,
        string $senderName,
        array $cc,
        int $companyId,
        ?string $replyTo = null
    ) {
        $this->afterCommit = true;
        $this->onQueue('mail');
        $this->forwardRequestUuid = $forwardRequestUuid;
        $this->to = $to;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->attachmentUuids = $attachmentUuids;
        $this->fromAddress = $fromAddress;
        $this->senderName = $senderName;
        $this->cc = $cc;
        $this->companyId = $companyId;
        $this->replyTo = $replyTo;
    }

    /**
     * Execute the job.
     */
    public function handle(ForwardResumeUploadService $uploadService): void
    {
        $forwardRequest = ForwardRequest::byUuid($this->forwardRequestUuid)
            ->forCompany($this->companyId)
            ->first();

        if (! $forwardRequest) {
            \Log::warning("[ForwardResume: {$this->forwardRequestUuid}] Job aborted: ForwardRequest not found", [
                'company_id' => $this->companyId,
            ]);

            return;
        }

        $forwardRequest->update(['status' => 'PROCESSING']);

        // 1. Resolve attachments and stream bytes from Cloudflare R2 into worker RAM
        $inMemoryAttachments = [];
        $attachments = ForwardAttachment::whereIn('uuid', $this->attachmentUuids)
            ->forCompany($this->companyId)
            ->get();

        foreach ($attachments as $att) {
            // Validate application-level expiration cutoff (Correction 4)
            if ($att->expires_at && $att->expires_at->isPast()) {
                \Log::error("[ForwardResume: {$this->forwardRequestUuid}] Attachment expired before worker execution", [
                    'att_uuid' => $att->uuid,
                    'expires_at' => $att->expires_at->toIso8601String(),
                ]);
                $att->update(['status' => 'EXPIRED']);

                continue;
            }

            $att->update(['status' => 'PROCESSING']);
            $bytes = $uploadService->getR2ObjectBytes($att->r2_object_key);

            // Fallback: If R2 object bytes are missing, try fetching candidate's original resume directly
            if (($bytes === null || strlen($bytes) <= 50) && ! empty($att->candidate_id)) {
                try {
                    $cand = DB::table('job_applications')
                        ->where('id', $att->candidate_id)
                        ->where(function ($q) {
                            $q->where('company_id', $this->companyId)
                                ->orWhere('thirdparty_company_id', $this->companyId);
                        })
                        ->first();

                    if ($cand && ! empty($cand->resume)) {
                        $candResume = $cand->resume;
                        if (filter_var($candResume, FILTER_VALIDATE_URL)) {
                            $httpRes = \Illuminate\Support\Facades\Http::timeout(15)->get($candResume);
                            if ($httpRes->successful() && strlen($httpRes->body()) > 50) {
                                $bytes = $httpRes->body();
                            }
                        } elseif (\Illuminate\Support\Facades\Storage::disk('r2')->exists($candResume)) {
                            $bytes = \Illuminate\Support\Facades\Storage::disk('r2')->get($candResume);
                        } elseif (file_exists(public_path('user-uploads/resumes/'.$candResume))) {
                            $bytes = file_get_contents(public_path('user-uploads/resumes/'.$candResume));
                        } elseif (file_exists(public_path('user-uploads/resume/'.$candResume))) {
                            $bytes = file_get_contents(public_path('user-uploads/resume/'.$candResume));
                        }

                        if ($bytes !== null && strlen($bytes) > 50) {
                            // Recover R2 object for future cleanups
                            $uploadService->putR2ObjectBytes($att->r2_object_key, $bytes, $att->mime_type ?: 'application/pdf');
                            \Log::info("[ForwardResume: {$this->forwardRequestUuid}] Successfully recovered attachment bytes from original candidate resume", [
                                'candidate_id' => $att->candidate_id,
                                'size' => strlen($bytes),
                            ]);
                        }
                    }
                } catch (\Throwable $recovErr) {
                    \Log::warning("[ForwardResume: {$this->forwardRequestUuid}] Candidate resume recovery failed: ".$recovErr->getMessage());
                }
            }

            if ($bytes !== null && strlen($bytes) > 50) {
                $inMemoryAttachments[] = [
                    'bytes' => $bytes,
                    'filename' => $att->filename,
                    'mime' => $att->mime_type ?: 'application/pdf',
                ];
            } else {
                \Log::error("[ForwardResume: {$this->forwardRequestUuid}] Missing R2 object bytes for attachment", [
                    'att_uuid' => $att->uuid,
                    'key' => $att->r2_object_key,
                ]);
            }
        }

        // 2. Fetch SMTP Configuration
        $smtp = EmailSetting::where('status', '=', 0)->where('id', 9)->orderByDesc('id')->first();
        if (! $smtp) {
            $smtp = EmailSetting::where('status', '=', 0)->orderByDesc('id')->first() ?: EmailSetting::first();
        }
        if (! $smtp) {
            throw new \Exception('SMTP config not found for resume forward');
        }

        Mail::purge();
        $this->setDynamicMailConfig($smtp, $this->senderName);

        $fromAddressReply = ! empty($this->replyTo) ? $this->replyTo : $this->fromAddress;
        $fromNameReply = $this->senderName;
        $fromMailAddr = ! empty($smtp->mail_from_email) ? $smtp->mail_from_email : $this->fromAddress;
        $mailHost = substr(strrchr($fromMailAddr, '@'), 1) ?: 'jeroworks.in';

        // 3. Dispatch to each recipient with Application-Level Idempotency & Traceability
        foreach ($this->to as $recipient) {
            $trimmedTo = strtolower(trim((string) $recipient));
            if (empty($trimmedTo) || ! filter_var($trimmedTo, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $idempotencyKey = md5($this->forwardRequestUuid.':'.$trimmedTo);
            $providerMsgId = "{$idempotencyKey}@{$mailHost}";

            // Fetch or create delivery attempt record (Correction 2)
            $attempt = ForwardDeliveryAttempt::firstOrCreate(
                [
                    'forward_request_id' => $forwardRequest->id,
                    'recipient_email' => $trimmedTo,
                ],
                [
                    'status' => 'PENDING',
                    'attempt_count' => 0,
                    'idempotency_key' => $idempotencyKey,
                ]
            );

            // Skip already successfully sent attempts
            if ($attempt->status === 'SENT') {
                \Log::info("[ForwardResume: {$this->forwardRequestUuid}] Skipped already SENT attempt for {$trimmedTo}");

                continue;
            }

            $attempt->increment('attempt_count');
            $attempt->update(['status' => 'SENDING']);

            try {
                Mail::send($this->view, ['data' => $this->data], function ($msg) use (
                    $trimmedTo,
                    $fromMailAddr,
                    $fromAddressReply,
                    $fromNameReply,
                    $inMemoryAttachments,
                    $providerMsgId
                ) {
                    $msg->to($trimmedTo)
                        ->subject($this->subject)
                        ->from($fromMailAddr, $this->senderName);

                    // Traceability Message-ID aligned with envelope domain
                    $msg->getHeaders()->addIdHeader('Message-ID', $providerMsgId);

                    // Clean & deduplicate CC
                    $cleanCc = [];
                    if (! empty($this->cc) && is_array($this->cc)) {
                        foreach ($this->cc as $ccAddr) {
                            $c = strtolower(trim($ccAddr));
                            if (! empty($c) && $c !== $trimmedTo && filter_var($c, FILTER_VALIDATE_EMAIL)) {
                                $cleanCc[] = $c;
                            }
                        }
                    }
                    if (! empty($fromAddressReply)) {
                        $r = strtolower(trim($fromAddressReply));
                        if (! empty($r) && $r !== $trimmedTo && filter_var($r, FILTER_VALIDATE_EMAIL)) {
                            $cleanCc[] = $r;
                        }
                    }
                    foreach (array_values(array_unique($cleanCc)) as $ccTarget) {
                        $msg->cc($ccTarget);
                    }

                    if (! empty($fromAddressReply)) {
                        $msg->replyTo($fromAddressReply, $fromNameReply);
                    }

                    // In-memory attachment injection (0 local disk writes)
                    foreach ($inMemoryAttachments as $inMem) {
                        $msg->attachData($inMem['bytes'], $inMem['filename'], ['mime' => $inMem['mime']]);
                    }
                });

                // Mark attempt successful
                $attempt->update([
                    'status' => 'SENT',
                    'sent_at' => now(),
                    'provider_message_id' => $providerMsgId,
                ]);

                // Audit logging
                $created_at = now();
                DB::table('email_count_companies')->insertGetId([
                    'to_email' => $trimmedTo,
                    'subject' => $this->subject,
                    'mailBody' => json_encode($this->data),
                    'emailType' => 2,
                    'company_id' => $this->companyId,
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                ]);
            } catch (\Throwable $sendErr) {
                $attempt->update([
                    'status' => 'FAILED',
                    'last_error' => $sendErr->getMessage(),
                ]);
                \Log::error("[ForwardResume: {$this->forwardRequestUuid}] Delivery failed for {$trimmedTo}: ".$sendErr->getMessage());
                throw $sendErr; // Re-throw to allow Horizon retry with backoff
            }
        }

        // 4. Multi-Recipient Delivery Barrier (Correction 2 & 3):
        // Check if ANY delivery attempt for this forward request is still in a non-terminal state
        $hasPendingAttempts = ForwardDeliveryAttempt::where('forward_request_id', $forwardRequest->id)
            ->whereNotIn('status', ['SENT', 'FAILED'])
            ->exists();

        if (! $hasPendingAttempts) {
            // Two-phase cleanup: Transition attachments to READY_FOR_CLEANUP, then delete from R2
            foreach ($attachments as $att) {
                $att->update(['status' => 'READY_FOR_CLEANUP']);
                $deleted = $uploadService->deleteR2Attachment($att->r2_object_key);
                if ($deleted) {
                    $att->update([
                        'status' => 'DELETED',
                        'deleted_at' => now(),
                    ]);
                }
            }

            $forwardRequest->update([
                'status' => 'COMPLETED',
                'completed_at' => now(),
            ]);

            \Log::info("[ForwardResume: {$this->forwardRequestUuid}] All delivery attempts complete. Temporary R2 objects cleaned up.");
        }
    }

    private function setDynamicMailConfig($smtp, $fromName = ''): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtp->notification_mail_host,
            'mail.mailers.smtp.port' => $smtp->notification_mail_port,
            'mail.mailers.smtp.username' => $smtp->notification_mail_username,
            'mail.mailers.smtp.password' => $smtp->notification_mail_password,
            'mail.mailers.smtp.encryption' => $smtp->notification_mail_encryption,
            'mail.from.address' => ! empty($smtp->mail_from_email) ? $smtp->mail_from_email : config('mail.from.address'),
            'mail.from.name' => $fromName,
        ]);
    }
}
