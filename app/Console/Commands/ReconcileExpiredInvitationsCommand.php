<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAgencyOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * ReconcileExpiredInvitationsCommand
 *
 * Scans staffing_company_invitation for active pending invitations (status = 0)
 * whose TTL window has elapsed (expires_at < NOW()). Automatically transitions
 * them to Expired (-2), which deterministically evaluates the STORED generated
 * column active_invite_slot to NULL, releasing the unique tenant slot so that
 * partners can be re-invited without collision.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ReconcileExpiredInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agency:reconcile-expired-invitations {--dry-run : Run in audit mode without modifying database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconciles and transitions expired agency invitations to status -2, releasing unique tenant slots.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info("Scanning for expired agency invitations (Dry Run: " . ($dryRun ? 'YES' : 'NO') . ")...");

        if (!Schema::hasTable('staffing_company_invitation')) {
            $this->error("Table staffing_company_invitation does not exist.");
            return 1;
        }

        $now = Carbon::now();
        $expiredRecords = DB::table('staffing_company_invitation')
            ->where('to_invitation_status', 0)
            ->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereNotNull('expires_at')
                        ->where('expires_at', '<', $now);
                })->orWhere(function ($sub) use ($now) {
                    // Fallback: 7 days after from_created_at if expires_at is null
                    $sub->whereNull('expires_at')
                        ->where('from_created_at', '<', $now->copy()->subDays(7));
                });
            })
            ->get();

        $count = $expiredRecords->count();
        if ($count === 0) {
            $this->info("✓ Zero expired active invitations detected. All records within valid TTL.");
            return 0;
        }

        $this->warn("Found {$count} expired invitation(s) requiring lifecycle transition to Expired (-2):");

        $outboxIds = [];
        foreach ($expiredRecords as $inv) {
            $this->line("   - ID: {$inv->id} | Recipient: {$inv->to_email} | Tenant: {$inv->from_company_id} | Expired At: " . ($inv->expires_at ?? '7d after creation'));

            if (! $dryRun) {
                DB::beginTransaction();
                try {
                    DB::table('staffing_company_invitation')
                        ->where('id', $inv->id)
                        ->where('to_invitation_status', 0)
                        ->update([
                            'to_invitation_status' => -2, // Expired / Released
                            'updated_at' => Carbon::now(),
                        ]);

                    // Publish Outbox Event for ES sync and WebSocket broadcast
                    $outboxId = DB::table('sys_outbox_events')->insertGetId([
                        'tenant_id' => $inv->from_company_id,
                        'aggregate_type' => 'AgencyRequest',
                        'aggregate_id' => $inv->id,
                        'aggregate_version' => 2,
                        'event_type' => 'AGENCY_INVITATION_EXPIRED',
                        'payload' => json_encode([
                            'invitation_id' => $inv->id,
                            'from_company_id' => $inv->from_company_id,
                            'to_company_id' => $inv->to_company_id,
                            'to_email' => $inv->to_email,
                            'expired_at' => Carbon::now()->toIso8601String(),
                        ]),
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => Carbon::now(),
                        'created_at' => Carbon::now(),
                    ]);

                    $outboxIds[] = $outboxId;
                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    Log::error("Failed to reconcile expired invitation #{$inv->id}: " . $e->getMessage());
                }
            }
        }

        if (! $dryRun && ! empty($outboxIds)) {
            foreach ($outboxIds as $oid) {
                try {
                    ProcessAgencyOutboxEventJob::dispatch($oid)->afterCommit();
                } catch (\Throwable $e) {
                    Log::warning("ProcessAgencyOutboxEventJob dispatch deferred on expiry reconciliation: " . $e->getMessage());
                }
            }
            $this->info("✓ Successfully transitioned {$count} expired invitation(s) to status -2.");
        }

        return 0;
    }
}
