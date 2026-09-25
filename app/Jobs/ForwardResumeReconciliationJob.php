<?php

namespace App\Jobs;

use App\Models\CoinReservation;
use App\Models\ForwardRequest;
use App\Models\ForwardResumeEvent;
use App\Models\OutboxEvent;
use App\Services\ForwardResume\ForwardResumeCoinService;
use App\Services\ForwardResume\ForwardResumeState;
use App\Services\ForwardResume\ForwardResumeStateMachine;
use App\Services\ForwardResume\Providers\ForwardResumeProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Class ForwardResumeReconciliationJob
 *
 * Intent: 5-minute self-healing cron job that audits Redis <-> MySQL consistency,
 * re-publishes un-dispatched outbox events, and settles stalled transactions.
 */
class ForwardResumeReconciliationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        ForwardResumeProvider $provider,
        ForwardResumeCoinService $coinService
    ): void {
        Log::info('[ForwardResumeReconciliationJob] Starting self-healing reconciliation scan...');

        // 1. Stalled Outbox Events Re-publishing
        $this->republishStalledOutboxEvents();

        // 2. Two-Way Redis <-> MySQL Reconciliation Rebuild
        $this->reconcileRedisAndMysql($coinService);

        // 3. Stalled Submissions Recovery (> 15 minutes)
        $this->recoverStalledSubmissions($provider, $coinService);

        Log::info('[ForwardResumeReconciliationJob] Reconciliation scan completed.');
    }

    /**
     * Finds outbox events pending > 2 minutes and re-enqueues them.
     */
    protected function republishStalledOutboxEvents(): void
    {
        try {
            $stalledEvents = OutboxEvent::where('status', 'PENDING')
                ->where('created_at', '<', now()->subMinutes(2))
                ->limit(50)
                ->get();

            foreach ($stalledEvents as $event) {
                if ($event->attempts >= 5) {
                    $event->update(['status' => 'FAILED']);
                    continue;
                }

                $payload = $event->payload;
                if (! empty($payload)) {
                    $job = new ProcessForwardResumeDispatchJob(
                        forwardRequestUuid: $payload['forward_request_uuid'] ?? $event->aggregate_id,
                        companyId: (int) ($payload['company_id'] ?? 0),
                        userId: (int) ($payload['user_id'] ?? 0),
                        to: $payload['to'] ?? [],
                        subject: $payload['subject'] ?? '',
                        body: $payload['body'] ?? '',
                        attachmentUuids: $payload['attachment_uuids'] ?? [],
                        fromAddress: $payload['from_address'] ?? '',
                        senderName: $payload['sender_name'] ?? 'JERO Recruitment',
                        cc: $payload['cc'] ?? [],
                        replyTo: $payload['reply_to'] ?? null,
                        reservationId: $payload['reservation_id'] ?? '',
                        requiredCoins: (int) ($payload['required_coins'] ?? 0)
                    );

                    dispatch($job);
                    $event->increment('attempts');
                    $event->update([
                        'status' => 'PUBLISHED',
                        'published_at' => now(),
                    ]);

                    Log::info("[ForwardResumeReconciliationJob] Re-enqueued stalled outbox event {$event->event_id}");
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[ForwardResumeReconciliationJob] republishStalledOutboxEvents error: ' . $e->getMessage());
        }
    }

    /**
     * Executes two-way Redis <-> MySQL synchronization.
     */
    protected function reconcileRedisAndMysql(ForwardResumeCoinService $coinService): void
    {
        try {
            // Forward Scan: MySQL -> Redis (Reconstruct missing Redis reservation keys)
            $activeMysqlReservations = CoinReservation::where('status', 'RESERVED')
                ->where('expires_at', '>', now())
                ->get();

            foreach ($activeMysqlReservations as $res) {
                $resKey = "coin_reservation:{$res->reservation_id}";
                $redisStatus = Redis::hget($resKey, 'status');

                if (! $redisStatus) {
                    // Redis crashed or lost key: Reconstruct
                    Redis::hmset($resKey, [
                        'status' => 'RESERVED',
                        'amount' => $res->amount,
                        'forward_request_id' => $res->forward_request_id,
                        'company_id' => $res->company_id,
                        'created_at' => time(),
                    ]);
                    Redis::expire($resKey, 3600);

                    // Sync tenant reserved balance in Redis
                    $tenantKey = "tenant:{$res->company_id}:coins";
                    Redis::hincrby($tenantKey, 'reserved', $res->amount);

                    Log::info("[ForwardResumeReconciliationJob] Reconstructed Redis key for reservation {$res->reservation_id}");
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[ForwardResumeReconciliationJob] Forward Scan error: ' . $e->getMessage());
        }

        try {
            // Reverse Scan: Purge expired or orphaned reservations (> 30 mins)
            $expiredReservations = CoinReservation::where('status', 'RESERVED')
                ->where('expires_at', '<=', now())
                ->limit(50)
                ->get();

            foreach ($expiredReservations as $exp) {
                $coinService->release($exp->company_id, $exp->reservation_id);
                $exp->update(['status' => 'EXPIRED']);

                // Record audit event
                ForwardResumeEvent::create([
                    'event_id' => (string) Str::uuid(),
                    'forward_request_id' => $exp->forward_request_id,
                    'event_type' => 'RECONCILIATION_EXPIRED_RESERVATION_RELEASED',
                    'previous_state' => 'RESERVED',
                    'new_state' => 'EXPIRED',
                    'actor_type' => 'RECONCILIATION',
                    'actor_id' => null,
                    'created_at' => now(),
                ]);

                Log::info("[ForwardResumeReconciliationJob] Expired orphaned reservation {$exp->reservation_id} released.");
            }
        } catch (\Throwable $e) {
            Log::warning('[ForwardResumeReconciliationJob] Reverse Scan error: ' . $e->getMessage());
        }
    }

    /**
     * Recovers stalled submissions (> 15 minutes).
     */
    protected function recoverStalledSubmissions(
        ForwardResumeProvider $provider,
        ForwardResumeCoinService $coinService
    ): void {
        try {
            $stalledRequests = ForwardRequest::whereIn('status', [
                ForwardResumeState::PROCESSING,
                ForwardResumeState::PROVIDER_SUBMITTING,
            ])
            ->where('updated_at', '<', now()->subMinutes(15))
            ->limit(20)
            ->get();

            foreach ($stalledRequests as $req) {
                $result = $provider->findByIdempotencyKey($req->uuid);

                if ($result && $result->isSuccessful) {
                    // Provider accepted! Commit ledger
                    if ($req->coin_reservation_id) {
                        $coinService->commit(
                            $req->company_id,
                            $req->user_id ?: 0,
                            $req->uuid,
                            $req->coin_reservation_id,
                            $req->recipient_count
                        );
                    }

                    ForwardResumeStateMachine::transition($req, ForwardResumeState::LEDGER_COMMITTED, [
                        'actor_type' => 'RECONCILIATION',
                        'metadata' => ['recovered' => true, 'provider_message_id' => $result->providerMessageId],
                    ]);
                    $req->update(['completed_at' => now()]);

                    Log::info("[ForwardResumeReconciliationJob] Recovered and committed stalled forward request {$req->uuid}");
                } else {
                    // Stalled with no external delivery record -> release reservation and refund
                    if ($req->coin_reservation_id) {
                        $coinService->release($req->company_id, $req->coin_reservation_id);
                    }

                    ForwardResumeStateMachine::transition($req, ForwardResumeState::REFUND_PENDING, [
                        'actor_type' => 'RECONCILIATION',
                        'metadata' => ['recovered_timeout' => true],
                    ]);

                    Log::info("[ForwardResumeReconciliationJob] Stalled forward request {$req->uuid} timed out and refunded.");
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[ForwardResumeReconciliationJob] recoverStalledSubmissions error: ' . $e->getMessage());
        }
    }
}
