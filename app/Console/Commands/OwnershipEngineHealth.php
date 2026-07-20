<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8, Production Monitoring — read-only. Reports flag state, table
 * reachability, and recent activity. Not a substitute for ownership-engine:verify (which
 * checks correctness) — this checks "is the system up and reachable," not "is the data right."
 */
class OwnershipEngineHealth extends Command
{
    protected $signature = 'ownership-engine:health';

    protected $description = 'Phase 8: Ownership Engine health check — flag state, table reachability, recent activity';

    public function handle(): int
    {
        $this->info('Ownership Engine health check');
        $this->line('');

        $settings = DB::table('ownership_engine_settings')->where('id', 1)->first();
        if (!$settings) {
            $this->error('UNHEALTHY: ownership_engine_settings has no row with id = 1 — migrations may not have run.');

            return self::FAILURE;
        }

        $this->line('Kill switch: '.($settings->kill_switch_active ? '🔴 ACTIVE — '.$settings->kill_switch_reason : '🟢 inactive'));
        $this->line('Live processing: 🟢 always on (subject only to app_duplicate and the kill switch — Finding 7)');
        $this->line('Globally authoritative (future cutover flag, currently inert): '.($settings->globally_enabled ? 'yes' : 'no (default)'));

        $companyOverrideCount = DB::table('companies')->whereNotNull('ownership_engine_enabled')->count();
        $jobOverrideCount = DB::table('jobs')->whereNotNull('ownership_engine_enabled')->count();
        $this->line("Company overrides set: {$companyOverrideCount}");
        $this->line("Job overrides set: {$jobOverrideCount}");
        $this->line('');

        try {
            $clusterCount = DB::table('ownership_clusters')->count();
            $eventCount = DB::table('ownership_events')->count();
            $this->line("ownership_clusters: reachable, {$clusterCount} row(s)");
            $this->line("ownership_events: reachable, {$eventCount} row(s)");
        } catch (\Throwable $e) {
            $this->error('UNHEALTHY: could not query ownership_clusters/ownership_events — '.$e->getMessage());

            return self::FAILURE;
        }

        $last24h = DB::table('ownership_events')->where('created_at', '>=', now()->subDay())->count();
        $lastLive = DB::table('ownership_events')->where('created_at', '>=', now()->subDay())
            ->whereIn('trigger_source', ['candidate_creation', 'stage_transition'])->count();
        $lastBackfill = DB::table('ownership_events')->where('created_at', '>=', now()->subDay())
            ->where('trigger_source', 'manual_rebuild')->count();
        $this->line('');
        $this->line("Events in last 24h: {$last24h} total ({$lastLive} live, {$lastBackfill} backfill)");

        $recentFailureLog = 'grep "OwnershipEngine::" storage/logs/laravel.log | tail -5';
        $this->line('');
        $this->comment("For recent failures, run: {$recentFailureLog}");

        $this->line('');
        $this->info('Health check complete — no structural problems detected. This does not verify data correctness; use ownership-engine:verify for that.');

        return self::SUCCESS;
    }
}
