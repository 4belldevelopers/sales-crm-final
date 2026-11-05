<?php

namespace App\Domain\AgencyPartnership\Jobs;

use App\Jobs\ProcessAgencyOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * HandleMailBounceJob
 *
 * Implements the 10-Step Deterministic Hard-Bounce Refund Contract (F-04):
 * - Fast pre-transaction Redis deduplication check (0 DB hits on duplicates)
 * - MySQL sys_processed_events infallible consumer deduplication
 * - In-transaction status check (never refund if accepted or delivered)
 * - Database-enforced single refund guarantee via coin_ledger.refund_reference_id unique index
 * - Automatic active slot release (to_invitation_status = -3 -> active_invite_slot = NULL)
 * - Transactional outbox event publication
 */
class HandleMailBounceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $invitationId;

    public string $eventId;

    public ?string $bounceReason;

    public function __construct(int $invitationId, string $eventId, ?string $bounceReason = null)
    {
        $this->invitationId = $invitationId;
        $this->eventId = $eventId;
        $this->bounceReason = $bounceReason;
    }

    public function handle(): void
    {
        $consumerName = 'MailBounceRefund';
        $redisKey = "idempotency:event:{$consumerName}:{$this->eventId}";

        // 1. Fast Pre-Transaction Redis Fast-Check (0 MySQL hits on repeated bursts)
        if (Cache::has($redisKey)) {
            Log::info("HandleMailBounceJob: Event {$this->eventId} already processed (Redis fast-check hit).");

            return;
        }

        $outboxId = null;

        // 2. Begin MySQL ACID Transaction
        DB::beginTransaction();
        try {
            // 3. Infallible Deduplication Authority in sys_processed_events
            $inserted = DB::table('sys_processed_events')->insertOrIgnore([
                'event_id' => "{$consumerName}:{$this->eventId}",
                'consumer_group' => $consumerName,
                'processed_at' => Carbon::now(),
            ]);

            if (! $inserted) {
                // Event was already processed by another worker
                DB::rollBack();
                Cache::put($redisKey, 1, 86400);

                return;
            }

            // 4. Exclusively Lock Invitation
            $invitation = DB::table('staffing_company_invitation')
                ->where('id', $this->invitationId)
                ->lockForUpdate()
                ->first();

            if (! $invitation) {
                DB::rollBack();
                Log::warning("HandleMailBounceJob: Invitation {$this->invitationId} not found.");

                return;
            }

            // 5. Guard Conditions: Reject refund if accepted or already confirmed delivered
            if ((int) $invitation->to_invitation_status === 1) {
                DB::rollBack();
                Log::info("HandleMailBounceJob: Invitation {$this->invitationId} was already accepted. 0 coins refunded.");

                return;
            }

            if ($invitation->delivery_status === 'DELIVERED') {
                DB::rollBack();
                Log::info("HandleMailBounceJob: Invitation {$this->invitationId} was already marked DELIVERED. 0 coins refunded.");

                return;
            }

            if ($invitation->delivery_status === 'BOUNCED' || (int) $invitation->to_invitation_status === -3) {
                DB::rollBack();
                Log::info("HandleMailBounceJob: Invitation {$this->invitationId} was already processed as bounced.");

                return;
            }

            $fromCompanyId = (int) $invitation->from_company_id;

            // 6. Lock and Credit 5 Coins to company_coins
            $coinsRow = DB::table('company_coins')
                ->where('company_id', $fromCompanyId)
                ->where('status', 'valid')
                ->lockForUpdate()
                ->first();

            $newBalance = 5.00;
            if ($coinsRow) {
                $newBalance = (float) $coinsRow->balance_coin + 5.00;
                DB::table('company_coins')
                    ->where('id', $coinsRow->id)
                    ->update([
                        'balance_coin' => $newBalance,
                        'updated_at' => Carbon::now(),
                    ]);
            }

            // 7. Write Immutable Credit in coin_ledger with refund_reference_id
            $refundRef = 'REFUND_BOUNCE_INV_'.$this->invitationId;
            $txnId = 'TXN_REFUND_BOUNCE_'.$this->invitationId.'_'.time();

            DB::table('coin_ledger')->insert([
                'transaction_id' => $txnId,
                'company_id' => $fromCompanyId,
                'account_type' => 'operational',
                'coin_type' => 'standard',
                'entry_type' => 'CREDIT',
                'amount' => 5.00,
                'balance_after' => $newBalance,
                'reference_type' => 'AGENCY_INVITATION_BOUNCE_REFUND',
                'reference_id' => (string) $this->invitationId,
                'refund_reference_id' => $refundRef,
                'idempotency_key' => $refundRef,
                'metadata' => json_encode([
                    'invitation_id' => $this->invitationId,
                    'event_id' => $this->eventId,
                    'reason' => $this->bounceReason,
                ]),
                'created_at' => Carbon::now(),
            ]);

            // 8. Update Invitation to UNDELIVERABLE (-3)
            // Stored generated active_invite_slot automatically evaluates to NULL!
            DB::table('staffing_company_invitation')
                ->where('id', $this->invitationId)
                ->update([
                    'to_invitation_status' => -3, // UNDELIVERABLE
                    'delivery_status' => 'BOUNCED',
                    'updated_at' => Carbon::now(),
                ]);

            // 9. Transactional Outbox Event
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $fromCompanyId,
                'aggregate_type' => 'AgencyRequest',
                'aggregate_id' => $this->invitationId,
                'aggregate_version' => 3,
                'event_type' => 'AGENCY_INVITATION_HARD_BOUNCED',
                'payload' => json_encode([
                    'invitation_id' => $this->invitationId,
                    'from_company_id' => $fromCompanyId,
                    'to_email' => $invitation->to_email,
                    'refunded_coins' => 5,
                    'new_balance' => $newBalance,
                    'event_id' => $this->eventId,
                    'reason' => $this->bounceReason,
                    'bounced_at' => Carbon::now()->toIso8601String(),
                ]),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            // 10. Commit Transaction
            DB::commit();

            // Cache in Redis post-commit
            Cache::put($redisKey, 1, 86400);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('HandleMailBounceJob failed: '.$e->getMessage(), ['exception' => $e]);
            throw $e;
        }

        if ($outboxId) {
            try {
                ProcessAgencyOutboxEventJob::dispatch($outboxId)->afterCommit();
            } catch (\Throwable $e) {
                Log::warning('ProcessAgencyOutboxEventJob dispatch deferred on bounce refund: '.$e->getMessage());
            }
        }
    }
}
