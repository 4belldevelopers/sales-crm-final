<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8 — dedicated emergency stop, separate from
 * ownership-engine:flags:set on purpose: an emergency kill switch should be a single,
 * unambiguous, hard-to-misuse command, not one option among several on a general settings
 * command. Per Finding 7 (supersedes Finding 6): this is now the ONLY thing that stops live
 * event processing (attemptCompanyReservation()/claimForAgencySourcing() both check it
 * unconditionally) — a real strengthening from Finding 6, where it only stopped
 * backfillCandidate(). See docs/OWNERSHIP_ENGINE_DESIGN.md Finding 7.
 */
class OwnershipEngineKillSwitch extends Command
{
    protected $signature = 'ownership-engine:kill-switch
                           {action : activate | deactivate}
                           {--reason= : required when activating}';

    protected $description = 'Phase 8: emergency stop for the Ownership Engine — overrides every other flag unconditionally';

    public function handle(): int
    {
        $action = $this->argument('action');

        if (!in_array($action, ['activate', 'deactivate'], true)) {
            $this->error('Action must be "activate" or "deactivate".');

            return self::FAILURE;
        }

        if ($action === 'activate') {
            $reason = $this->option('reason');
            if (!$reason) {
                $this->error('--reason is required when activating the kill switch.');

                return self::FAILURE;
            }

            DB::table('ownership_engine_settings')->where('id', 1)->update([
                'kill_switch_active' => true,
                'kill_switch_reason' => $reason,
                'kill_switch_activated_at' => now(),
                'updated_at' => now(),
            ]);

            $this->warn('KILL SWITCH ACTIVATED. The Ownership Engine will not process anything — live or backfill — until deactivated.');
            $this->line('Reason recorded: '.$reason);

            return self::SUCCESS;
        }

        DB::table('ownership_engine_settings')->where('id', 1)->update([
            'kill_switch_active' => false,
            'kill_switch_reason' => null,
            'kill_switch_activated_at' => null,
            'updated_at' => now(),
        ]);

        $this->info('Kill switch deactivated. Normal flag hierarchy (global/company/job) resumes governing the engine.');

        return self::SUCCESS;
    }
}
