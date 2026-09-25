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

            // Load authoritative SMTP settings from smtp_settings (ID 12 is AWS SES, fallback to ID 9 or default active)
            $smtp = \Illuminate\Support\Facades\Cache::remember('smtp_settings:forward_resume', 3600, function () {
                $record = EmailSetting::where('status', '=', 0)->where('id', 12)->first();
                if (! $record) {
                    $record = EmailSetting::where('status', '=', 0)->where('id', 9)->first();
                }
                if (! $record) {
                    $record = EmailSetting::where('status', '=', 0)->orderByDesc('id')->first() ?: EmailSetting::first();
                }

                return $record;
            });

            if ($smtp) {
                $host = $smtp->notification_mail_host ?: $smtp->mail_host;
                $port = $smtp->notification_mail_port ?: $smtp->mail_port;
                $user = $smtp->notification_mail_username ?: $smtp->mail_username;
                $pass = $smtp->notification_mail_password ?: $smtp->mail_password;
                $enc  = $smtp->notification_mail_encryption ?: $smtp->mail_encryption;

                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $host,
                    'mail.mailers.smtp.port' => (int) $port,
                    'mail.mailers.smtp.username' => $user,
                    'mail.mailers.smtp.password' => $pass,
                    'mail.mailers.smtp.encryption' => $enc,
                    'mail.from.address' => ! empty($smtp->mail_from_email) ? $smtp->mail_from_email : config('mail.from.address'),
                    'mail.from.name' => $payload->senderName ?: config('mail.from.name', 'JERO Recruitment'),
                ]);
                Mail::purge();
            }

            $mailHost = parse_url(config('app.url', 'jero.works'), PHP_URL_HOST) ?: 'jero.works';
            $canonicalMsgId = sprintf('fwd-%s@%s', $payload->forwardRequestId, $mailHost);

            $recipients = array_values(array_filter(array_map('trim', $payload->to)));
            if (empty($recipients)) {
                return ProviderSubmissionResult::failure('No valid recipients provided.');
            }

            // Envelope From must match verified domain/SES identity (notify@jeroworks.com)
            $fromAddress = ! empty($smtp->mail_from_email) ? $smtp->mail_from_email : (config('mail.from.address') ?: 'notify@jeroworks.com');
            $senderName = $payload->senderName ?: config('mail.from.name', 'JERO Recruitment');
            $replyToEmail = ! empty($payload->replyTo) ? $payload->replyTo : ($payload->fromAddress ?: $fromAddress);

            // Render email using branded JERO template
            try {
                $htmlBody = view('email.resume-forward.forward-resume', [
                    'data' => (object) [
                        'message' => $payload->body,
                        'subject' => $payload->subject,
                        'company_name' => config('app.name', 'JERO'),
                        'candidate_skills' => [],
                    ],
                ])->render();
            } catch (\Throwable $viewErr) {
                Log::warning('[SesForwardResumeProvider] Blade render fallback: ' . $viewErr->getMessage());
                $htmlBody = $payload->body;
            }

            Mail::html($htmlBody, function ($msg) use ($payload, $recipients, $fromAddress, $senderName, $replyToEmail, $canonicalMsgId) {
                $msg->to($recipients)
                    ->subject($payload->subject)
                    ->from($fromAddress, $senderName);

                $headers = $msg->getHeaders();
                $headers->addTextHeader('X-Forward-Request-ID', $payload->forwardRequestId);
                $headers->addIdHeader('Message-ID', $canonicalMsgId);

                if (! empty($replyToEmail)) {
                    $msg->replyTo($replyToEmail, $senderName);
                }

                if (! empty($payload->cc)) {
                    foreach ($payload->cc as $ccAddr) {
                        $cleanCc = strtolower(trim((string) $ccAddr));
                        if (! empty($cleanCc) && filter_var($cleanCc, FILTER_VALIDATE_EMAIL)) {
                            $msg->cc($cleanCc);
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

            // Delivery attempt tracking & multi-recipient tracking
            try {
                $fwd = ForwardRequest::where('uuid', $payload->forwardRequestId)->first();
                if ($fwd && \Illuminate\Support\Facades\Schema::hasTable('forward_delivery_attempts')) {
                    foreach ($recipients as $recipient) {
                        $attempt = ForwardDeliveryAttempt::firstOrCreate(
                            [
                                'forward_request_id' => $fwd->id,
                                'recipient_email' => $recipient,
                            ],
                            [
                                'status' => 'PENDING',
                                'attempt_count' => 0,
                                'idempotency_key' => md5($payload->forwardRequestId . ':' . $recipient),
                            ]
                        );
                        $attempt->increment('attempt_count');
                        $attempt->update([
                            'status' => 'SENT',
                            'sent_at' => now(),
                            'provider_message_id' => $canonicalMsgId,
                        ]);
                    }
                }
            } catch (\Throwable $attErr) {
                Log::warning('[SesForwardResumeProvider] Delivery attempt tracking warning: ' . $attErr->getMessage());
            }

            // Company email count audit logging
            try {
                $createdAt = now();
                foreach ($recipients as $recipient) {
                    DB::table('email_count_companies')->insertGetId([
                        'to_email' => $recipient,
                        'subject' => $payload->subject,
                        'mailBody' => json_encode(['message' => $payload->body]),
                        'emailType' => 2,
                        'company_id' => $payload->companyId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            } catch (\Throwable $auditErr) {
                Log::warning('[SesForwardResumeProvider] email_count_companies logging skipped: ' . $auditErr->getMessage());
            }

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
