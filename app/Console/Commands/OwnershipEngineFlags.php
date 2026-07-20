<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8 — read-only view of the feature-flag hierarchy. Per Finding 7:
 * this hierarchy does NOT control live processing (that runs unconditionally, stopped only
 * by the kill switch) — it's reserved for the not-yet-built authoritative-cutover mechanism,
 * currently inert. Setting flags is a separate command (ownership-engine:flags:set) so
 * "show" can never accidentally be run with a typo'd write flag.
 */
class OwnershipEngineFlags extends Command
{
    protected $signature = 'ownership-engine:flags';

    protected $description = 'Phase 8: show the current Ownership Engine feature-flag state (global, kill switch, and any company/job overrides)';

    public function handle(): int
    {
        $settings = DB::table('ownership_engine_settings')->where('id', 1)->first();

        if (!$settings) {
            $this->error('ownership_engine_settings has no row with id = 1 — migrations may not have run.');

            return self::FAILURE;
        }

        $this->info('Ownership Engine feature flags');
        $this->line('Live processing runs unconditionally regardless of these flags — see Finding 7.');
        $this->table(['Setting', 'Value'], [
            ['Globally authoritative (future cutover, currently inert)', $settings->globally_enabled ? 'YES' : 'no'],
            ['Kill switch active (stops all live processing)', $settings->kill_switch_active ? 'YES — everything stopped' : 'no'],
            ['Kill switch reason', $settings->kill_switch_reason ?? '(none)'],
            ['Kill switch activated at', $settings->kill_switch_activated_at ?? '(n/a)'],
        ]);

        $companyOverrides = DB::table('companies')
            ->whereNotNull('ownership_engine_enabled')
            ->select('id', 'company_name', 'ownership_engine_enabled')
            ->get();

        if ($companyOverrides->isNotEmpty()) {
            $this->line('');
            $this->info('Company-level overrides:');
            $this->table(
                ['Company ID', 'Name', 'Override'],
                $companyOverrides->map(fn ($c) => [$c->id, $c->company_name, $c->ownership_engine_enabled ? 'ON' : 'OFF'])->all()
            );
        } else {
            $this->line('No company-level overrides set.');
        }

        $jobOverrides = DB::table('jobs')
            ->whereNotNull('ownership_engine_enabled')
            ->select('id', 'company_id', 'ownership_engine_enabled')
            ->get();

        if ($jobOverrides->isNotEmpty()) {
            $this->line('');
            $this->info('Job-level overrides:');
            $this->table(
                ['Job ID', 'Company ID', 'Override'],
                $jobOverrides->map(fn ($j) => [$j->id, $j->company_id, $j->ownership_engine_enabled ? 'ON' : 'OFF'])->all()
            );
        } else {
            $this->line('No job-level overrides set.');
        }

        return self::SUCCESS;
    }
}
