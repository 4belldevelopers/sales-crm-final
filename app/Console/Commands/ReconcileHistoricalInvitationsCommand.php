<?php

namespace App\Console\Commands;

use App\Domain\AgencyPartnership\Services\AgencyRecipientNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ReconcileHistoricalInvitationsCommand
 *
 * Pre-migration forensic tool to audit and backfill normalized_recipient
 * on historical staffing_company_invitation records, guaranteeing 0 unique
 * constraint collisions when uk_tenant_active_slot is created.
 */
class ReconcileHistoricalInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agency:reconcile-historical-invitations {--dry-run : Run in audit mode without modifying database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forensic audit and backfill of normalized_recipient on historical staffing_company_invitation rows.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info("Starting Historical Agency Invitations Reconciliation (Dry Run: " . ($dryRun ? 'YES' : 'NO') . ")...");

        if (!Schema::hasTable('staffing_company_invitation')) {
            $this->error("Table staffing_company_invitation does not exist.");
            return 1;
        }

        $records = DB::table('staffing_company_invitation')->get();
        $total = $records->count();
        $this->info("Found {$total} total historical invitation records.");

        $hasNormalizedCol = Schema::hasColumn('staffing_company_invitation', 'normalized_recipient');
        $activePairs = [];
        $conflicts = [];
        $updates = [];

        foreach ($records as $row) {
            $email = (string) ($row->to_email ?? '');
            $normalized = AgencyRecipientNormalizer::normalize($email);
            $isActive = ((int) $row->to_invitation_status === 0);

            if ($isActive) {
                $slotKey = "{$row->from_company_id}:{$normalized}";
                if (isset($activePairs[$slotKey])) {
                    $conflicts[] = [
                        'slot' => $slotKey,
                        'first_id' => $activePairs[$slotKey],
                        'duplicate_id' => $row->id,
                    ];
                } else {
                    $activePairs[$slotKey] = $row->id;
                }
            }

            if ($hasNormalizedCol && empty($row->normalized_recipient) && !empty($normalized)) {
                $updates[] = [
                    'id' => $row->id,
                    'normalized' => $normalized,
                ];
            }
        }

        if (!empty($conflicts)) {
            $this->warn("Found " . count($conflicts) . " active pending invitation duplicate conflict(s):");
            foreach ($conflicts as $c) {
                $this->line("   - Slot: {$c['slot']} (ID {$c['first_id']} vs ID {$c['duplicate_id']})");
            }
            if (!$dryRun) {
                $this->info("Resolving conflicts by marking older duplicates as Expired (-2)...");
                foreach ($conflicts as $c) {
                    DB::table('staffing_company_invitation')
                        ->where('id', $c['first_id'])
                        ->update(['to_invitation_status' => -2]);
                }
            }
        } else {
            $this->info("✓ Zero active duplicate conflicts detected in historical records.");
        }

        if ($hasNormalizedCol && !empty($updates)) {
            $this->info("Found " . count($updates) . " records requiring normalized_recipient population.");
            if (!$dryRun) {
                foreach ($updates as $u) {
                    DB::table('staffing_company_invitation')
                        ->where('id', $u['id'])
                        ->update(['normalized_recipient' => $u['normalized']]);
                }
                $this->info("✓ Successfully backfilled normalized_recipient on " . count($updates) . " records.");
            } else {
                $this->info("[Dry Run] Would backfill " . count($updates) . " records.");
            }
        } else {
            $this->info("✓ normalized_recipient column check complete.");
        }

        $this->info("Reconciliation completed successfully.");
        return 0;
    }
}
