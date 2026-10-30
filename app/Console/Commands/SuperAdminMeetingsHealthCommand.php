<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\SuperAdminMeetingScheduleCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMeetingsHealthCommand
 *
 * Intent: Comprehensive operational health probe and telemetry diagnostic CLI for Super Admin Meetings domain.
 * Evaluates Redis/Dragonfly latency, cache hit/miss ratio, transactional outbox backlog,
 * oldest event age, expired leases, dead-letter count, and cache version consistency.
 *
 * Signature: php artisan superadmin:meetings:health {--json : Output metrics as JSON}
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingsHealthCommand extends Command
{
    protected $signature = 'superadmin:meetings:health {--json : Output metrics as JSON}';

    protected $description = 'Operational telemetry and health check for the Super Admin Meeting Tracker subsystem';

    public function handle(): int
    {
        $metrics = [
            'status' => 'HEALTHY',
            'timestamp' => now()->toIso8601String(),
            'alerts' => [],
            'components' => [],
        ];

        // 1. Redis Cache & Latency Probe + Telemetry
        $redisStart = microtime(true);
        $redisHealthy = false;
        $redisLatencyMs = 0.0;
        try {
            Cache::put('health:probe:sa_meetings', 1, 10);
            $val = Cache::get('health:probe:sa_meetings');
            $redisHealthy = ((int) $val === 1);
            $redisLatencyMs = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
            $metrics['alerts'][] = 'Redis connection failure: '.$e->getMessage();
        }

        $telemetry = SuperAdminMeetingScheduleCacheManager::getTelemetryMetrics();
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => $telemetry['current_version'],
            'hits' => $telemetry['hits'],
            'misses' => $telemetry['misses'],
            'hit_ratio_percent' => $telemetry['hit_ratio_percent'],
        ];

        // 2. Circuit Breaker Probe
        $cb = app(ElasticsearchCircuitBreaker::class);
        $cbState = $cb->getState();
        if ($cbState === ElasticsearchCircuitBreaker::STATE_OPEN) {
            $metrics['status'] = 'DEGRADED';
            $metrics['alerts'][] = 'Circuit Breaker is OPEN (serving degraded snapshots)';
        }

        $metrics['components']['circuit_breaker'] = [
            'state' => $cbState,
            'healthy' => $cbState !== ElasticsearchCircuitBreaker::STATE_OPEN,
        ];

        // 3. Transactional Outbox Backlog & DLQ Probe
        $outboxStats = [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0,
            'dead_letter' => 0,
            'oldest_pending_age_seconds' => 0,
        ];

        try {
            $rows = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'MeetingSchedule')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $outboxStats['pending'] = (int) ($rows['PENDING'] ?? 0);
            $outboxStats['processing'] = (int) ($rows['PROCESSING'] ?? 0);
            $outboxStats['completed'] = (int) ($rows['COMPLETED'] ?? 0);
            $outboxStats['failed'] = (int) ($rows['FAILED'] ?? 0);

            // DLQ deliveries
            $dlqCount = DB::table('sys_outbox_deliveries')
                ->where('status', 'DEAD_LETTER')
                ->whereIn('outbox_id', function ($query) {
                    $query->select('id')->from('sys_outbox_events')->where('aggregate_type', 'MeetingSchedule');
                })
                ->count();
            $outboxStats['dead_letter'] = $dlqCount;

            $oldestPending = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'MeetingSchedule')
                ->where('status', 'PENDING')
                ->orderBy('created_at', 'asc')
                ->value('created_at');

            if ($oldestPending) {
                $outboxStats['oldest_pending_age_seconds'] = Carbon::parse($oldestPending)->diffInSeconds(now());
            }

            if ($outboxStats['pending'] > 500) {
                $metrics['status'] = 'WARNING';
                $metrics['alerts'][] = "High pending outbox backlog: {$outboxStats['pending']} events";
            }
            if ($outboxStats['dead_letter'] > 0) {
                $metrics['alerts'][] = "Dead-Letter Queue contains {$outboxStats['dead_letter']} poisoned events";
            }
        } catch (\Throwable $e) {
            $metrics['alerts'][] = 'Outbox probe failed: '.$e->getMessage();
        }

        $metrics['components']['outbox'] = $outboxStats;

        // 4. Idempotency Leases Probe
        $idempStats = [
            'total_keys' => 0,
            'processing_leases' => 0,
            'stuck_leases' => 0,
        ];

        try {
            $idempStats['total_keys'] = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', 'LIKE', 'sa_meet_%')
                ->count();

            $idempStats['processing_leases'] = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', 'LIKE', 'sa_meet_%')
                ->where('status', 'PROCESSING')
                ->count();

            $idempStats['stuck_leases'] = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', 'LIKE', 'sa_meet_%')
                ->where('status', 'PROCESSING')
                ->where('updated_at', '<', now()->subMinutes(2))
                ->count();
        } catch (\Throwable $e) {
        }

        $metrics['components']['idempotency'] = $idempStats;

        // 5. Output
        if ($this->option('json')) {
            $this->output->writeln(json_encode($metrics, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        }

        $this->info('===============================================================');
        $this->info('   JERO SUPER ADMIN MEETINGS SUBSYSTEM HEALTH TELEMETRY');
        $this->info('===============================================================');
        $this->line('Status:    [<fg='.($metrics['status'] === 'HEALTHY' ? 'green' : 'yellow').">{$metrics['status']}</>]");
        $this->line("Timestamp: {$metrics['timestamp']}");
        $this->newLine();

        $this->comment('--- Redis / Dragonfly L2 Cache ---');
        $this->line('Healthy:       '.($redisHealthy ? '<fg=green>YES</>' : '<fg=red>NO</>'));
        $this->line("Probe Latency: {$redisLatencyMs} ms");
        $this->line("Gen Version:   {$telemetry['current_version']}");
        $this->line("Hits / Misses: {$telemetry['hits']} / {$telemetry['misses']} (Ratio: {$telemetry['hit_ratio_percent']}%)");
        $this->newLine();

        $this->comment('--- Transactional Outbox & DLQ ---');
        $this->line("Pending:       {$outboxStats['pending']}");
        $this->line("Processing:    {$outboxStats['processing']}");
        $this->line("Completed:     {$outboxStats['completed']}");
        $this->line("Dead-Letter:   {$outboxStats['dead_letter']}");
        $this->line("Oldest Pending Age: {$outboxStats['oldest_pending_age_seconds']}s");
        $this->newLine();

        $this->comment('--- Idempotency Keys ---');
        $this->line("Total Tracked: {$idempStats['total_keys']}");
        $this->line("In-Flight:     {$idempStats['processing_leases']}");
        $this->line("Stuck Leases:  {$idempStats['stuck_leases']}");
        $this->newLine();

        if (! empty($metrics['alerts'])) {
            $this->warn('--- Active Alerts ---');
            foreach ($metrics['alerts'] as $alert) {
                $this->line(" • <fg=yellow>{$alert}</>");
            }
            $this->newLine();
        }

        return Command::SUCCESS;
    }
}
