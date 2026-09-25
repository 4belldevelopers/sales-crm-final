<?php

namespace App\Services\ForwardResume\Providers;

use App\Models\EmailSetting;
use App\Models\ForwardDeliveryAttempt;
use App\Models\ForwardRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

/**
 * Class SesForwardResumeProvider
 *
 * Intent: SES and SMTP email delivery adapter with canonical forward_request_id idempotency.
 * Satisfies the ForwardResumeProvider interface contract.
 */
class SesForwardResumeProvider implements ForwardResumeProvider
{
    /**
     * Dispatches email with attachments using an immutable idempotency key.
     */
    public function send(ForwardResumeDispatchPayload $payload): ProviderSubmissionResult
    {
        try {
            $companyId = $payload->companyId;
            $replyEmailSetting = EmailSetting::where('company_id', $companyId)->first();

            if ($replyEmailSetting) {
                if ($replyEmailSetting->mail_driver === 'smtp') {
                    Config::set('mail.mailers.smtp.host', $replyEmailSetting->mail_host);
                    Config::set('mail.mailers.smtp.port', $replyEmailSetting->mail_port);
                    Config::set('mail.mailers.smtp.username', $replyEmailSetting->mail_username);
                    Config::set('mail.mailers.smtp.password', $replyEmailSetting->mail_password);
                    Config::set('mail.mailers.smtp.encryption', $replyEmailSetting->mail_encryption);
                }
            }

            $mailHost = parse_url(config('app.url', 'jero.works'), PHP_URL_HOST) ?: 'jero.works';
            $canonicalMsgId = sprintf('fwd-%s@%s', $payload->forwardRequestId, $mailHost);

            $recipients = array_values(array_filter(array_map('trim', $payload->to)));
            if (empty($recipients)) {
                return ProviderSubmissionResult::failure('No valid recipients provided.');
            }

            // We dispatch email to the designated recipients with the in-memory attachments
            $fromAddress = $payload->fromAddress ?: config('mail.from.address');
            $senderName = $payload->senderName ?: config('mail.from.name', 'JERO Recruitment');

            Mail::html($payload->body, function ($msg) use ($payload, $recipients, $fromAddress, $senderName, $canonicalMsgId) {
                $msg->to($recipients)
                    ->subject($payload->subject)
                    ->from($fromAddress, $senderName);

                $headers = $msg->getHeaders();
                $headers->addTextHeader('X-Forward-Request-ID', $payload->forwardRequestId);
                $headers->addIdHeader('Message-ID', $canonicalMsgId);

                if (! empty($payload->replyTo)) {
                    $msg->replyTo($payload->replyTo);
                }

                if (! empty($payload->cc)) {
                    foreach ($payload->cc as $ccAddr) {
                        if (filter_var($ccAddr, FILTER_VALIDATE_EMAIL)) {
                            $msg->cc($ccAddr);
                        }
                    }
                }

                foreach ($payload->attachments as $att) {
                    if (! empty($att['bytes'])) {
                        $msg->attachData(
                            $att['bytes'],
                            $att['filename'] ?? 'Resume.pdf',
                            ['mime' => $att['mime'] ?? 'application/pdf']
                        );
                    }
                }
            });

            // Cache delivery confirmation in Redis for rapid idempotency checks (24-hour TTL)
            try {
                Redis::set("fwd_prov_msg:{$payload->forwardRequestId}", $canonicalMsgId, 'EX', 86400);
            } catch (\Throwable $redisErr) {
                Log::warning('[SesForwardResumeProvider] Redis cache warning: ' . $redisErr->getMessage());
            }

            return ProviderSubmissionResult::success($canonicalMsgId, [
                'provider' => 'ses_smtp',
                'forward_request_id' => $payload->forwardRequestId,
                'recipients_count' => count($recipients),
            ]);
        } catch (\Throwable $e) {
            Log::error('[SesForwardResumeProvider] Dispatch failed: ' . $e->getMessage(), [
                'forward_request_id' => $payload->forwardRequestId,
            ]);

            return ProviderSubmissionResult::failure($e->getMessage());
        }
    }

    /**
     * Queries external provider logs/tags by forwardRequestId to recover delivery outcome.
     */
    public function findByIdempotencyKey(string $forwardRequestId): ?ProviderSubmissionResult
    {
        // 1. Check Redis fast delivery marker
        try {
            $cachedMsgId = Redis::get("fwd_prov_msg:{$forwardRequestId}");
            if ($cachedMsgId) {
                return ProviderSubmissionResult::success($cachedMsgId, ['source' => 'redis_cache']);
            }
        } catch (\Throwable $e) {
            // Ignore Redis connectivity error and proceed to DB check
        }

        // 2. Check forward_requests table status
        try {
            $fwd = ForwardRequest::where('uuid', $forwardRequestId)->first();
            if ($fwd && in_array($fwd->status, ['SENT', 'PROVIDER_ACCEPTED', 'LEDGER_COMMITTED'])) {
                $mailHost = parse_url(config('app.url', 'jero.works'), PHP_URL_HOST) ?: 'jero.works';
                return ProviderSubmissionResult::success("fwd-{$forwardRequestId}@{$mailHost}", ['source' => 'db_fwd_requests']);
            }
        } catch (\Throwable $e) {
            Log::warning('[SesForwardResumeProvider] findByIdempotencyKey DB check warning: ' . $e->getMessage());
        }

        return null;
    }
}
