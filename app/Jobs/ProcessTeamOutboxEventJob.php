<?php

namespace App\Jobs;

use App\Helper\GeneralMethod;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessTeamOutboxEventJob
 *
 * Intent: Decoupled Outbox Worker for Team Member mutations.
 * Claims pending sys_outbox_events for aggregate_type 'TeamMember',
 * delivers asynchronous emails outside of database transactions,
 * keeps Elasticsearch indices in sync, guarantees at-least-once event delivery,
 * and marks outbox records as COMPLETED or DEAD_LETTER.
 */
class ProcessTeamOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 15;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('team-outbox');
    }

    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing if called without specific ID (scheduler / sweeper)
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'TeamMember')
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->where('available_at', '<=', now())
            ->where('attempts', '<', $this->tries)
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId);
        }
    }

    protected function processEvent(int $eventId): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('aggregate_type', 'TeamMember')
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if (! $claimed) {
            return;
        }

        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        try {
            $payload = json_decode($event->payload, true) ?: [];
            $targetUserId = (int) ($event->aggregate_id ?: ($payload['user_id'] ?? 0));
            $eventType = $event->event_type;

            // 1. Deliver email asynchronously if this event requires it (e.g. team_member.invited, team_member.resend_invitation)
            if (in_array($eventType, ['team_member.invited', 'team_member.resend_invitation', 'team_member.created']) && ! empty($payload['email_data'])) {
                $emailData = $payload['email_data'];
                $to = $emailData['to'] ?? ($payload['email'] ?? null);
                $subject = $payload['email_subject'] ?? 'Account Credential';
                $view = $payload['email_template'] ?? 'email.team.create-account';
                $senderName = $payload['sender_name'] ?? '';
                $fromAddress = $payload['from_address'] ?? '';
                $companyId = (int) ($event->tenant_id ?: ($payload['company_id'] ?? 0));

                if (! empty($to)) {
                    $mailService = app(MailService::class);
                    $result = $mailService->sendEmail(
                        $to,
                        $subject,
                        $view,
                        (object) $emailData,
                        [],
                        $fromAddress,
                        $senderName,
                        [],
                        $companyId
                    );

                    if ($result !== true) {
                        throw new \RuntimeException('Mail sending failed: '.(is_string($result) ? $result : 'unknown error'));
                    }
                }
            }

            // 2. Synchronize to Elasticsearch if user exists
            if ($targetUserId > 0) {
                try {
                    GeneralMethod::userIndexingupload($targetUserId);
                    SyncUserToElasticJob::dispatch($targetUserId)->afterCommit();
                } catch (\Throwable $e) {
                    Log::warning("ProcessTeamOutboxEventJob: ES index sync warning for user {$targetUserId}: ".$e->getMessage());
                }
            }

            // 3. Mark COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error("ProcessTeamOutboxEventJob failed for event {$eventId}: ".$e->getMessage());

            $finalStatus = ($event->attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';
            $backoffSeconds = $this->backoff * max(1, $event->attempts);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $finalStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($backoffSeconds),
                ]);

            throw $e;
        }
    }
}
