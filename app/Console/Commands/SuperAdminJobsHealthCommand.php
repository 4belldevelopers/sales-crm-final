<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use App\Services\SuperAdminJobsCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminJobsHealthCommand
 *
 * Intent: Comprehensive operational health probe and telemetry diagnostic CLI for Super Admin Jobs domain.
 * Evaluates Redis/Dragonfly latency, Elasticsearch cluster health, Circuit Breaker state,
 * cache hit/miss ratio, transactional outbox backlog, oldest event age, expired leases,
 * dead-letter count, and cache version consistency against defined operational alert thresholds.
 *
 * Signature: php artisan superadmin:jobs:health {--json : Output metrics as JSON}
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminJobsHealthCommand extends Command
{
    protected $signature = 'superadmin:jobs:health {--json : Output metrics as JSON}';

    protected $description = 'Operational telemetry and health check for the Super Admin Jobs Requisitions subsystem';

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
            Cache::put('health:probe:sa_jobs', 1, 10);
            $val = Cache::get('health:probe:sa_jobs');
            $redisHealthy = ($val === 1);
            $redisLatencyMs = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
            $metrics['alerts'][] = 'Redis/Dragonfly connection failure: '.$e->getMessage();
        }

        $telemetry = SuperAdminJobsCacheManager::getTelemetryMetrics();
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => $telemetry['current_version'],
            'hits' => $telemetry['hits'],
            'misses' => $telemetry['misses'],
            'hit_ratio_percent' => $telemetry['hit_ratio_percent'],
        ];

        // 2. Elasticsearch & Circuit Breaker Probe
        $cb = app(ElasticsearchCircuitBreaker::class);
        $cbState = $cb->getState();
        $esHealthy = false;
        $esLatencyMs = 0.0;
        $esDocCount = 0;

        try {
            $esStart = microtime(true);
            $elasticService = app(ElasticService::class);
            if ($elasticService->indexExists(ELASTIC_JOBS)) {
                $countRes = $elasticService->getClient()->count(['index' => ELASTIC_JOBS]);
                $esDocCount = (int) ($countRes['count'] ?? 0);
                $esHealthy = true;
            }
            $esLatencyMs = round((microtime(true) - $esStart) * 1000, 2);
        } catch (\Throwable $e) {
            $esHealthy = false;
        }

        if ($cbState === ElasticsearchCircuitBreaker::STATE_OPEN) {
            $metrics['status'] = 'DEGRADED';
            $metrics['alerts'][] = 'Elasticsearch Circuit Breaker is OPEN (serving degraded snapshots)';
        }

        $metrics['components']['elasticsearch'] = [
            'healthy' => $esHealthy,
            'latency_ms' => $esLatencyMs,
            'doc_count' => $esDocCount,
            'circuit_breaker' => $cbState,
        ];

        // 3. Transactional Outbox Backlog & DLQ Probe
        $outboxStats = [
            'pending' => 0,
            'processing' => 0,
            'failed' => 0,
            'dead_letter' => 0,
            'completed_last_24h' => 0,
            'oldest_pending_age_seconds' => 0,
        ];

        try {
            $rows = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'job')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $outboxStats['pending'] = (int) ($rows['PENDING'] ?? 0);
            $outboxStats['processing'] = (int) ($rows['PROCESSING'] ?? 0);
            $outboxStats['failed'] = (int) ($rows['FAILED'] ?? 0);
            $outboxStats['dead_letter'] = (int) ($rows['DEAD_LETTER'] ?? 0);
            $outboxStats['completed_last_24h'] = (int) ($rows['COMPLETED'] ?? 0);

            // Compute oldest pending/failed event age
            $oldestEvent = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'job')
                ->whereIn('status', ['PENDING', 'FAILED'])
                ->orderBy('created_at', 'asc')
                ->first();

            if ($oldestEvent && $oldestEvent->created_at) {
                $outboxStats['oldest_pending_age_seconds'] = max(0, Carbon::now()->diffInSeconds(Carbon::parse($oldestEvent->created_at)));
            }

            // Alert Thresholds
            if ($outboxStats['dead_letter'] > 0) {
                $metrics['status'] = 'DEGRADED';
                $metrics['alerts'][] = "DLQ Alert: {$outboxStats['dead_letter']} poisoned event(s) in dead-letter queue! Run `php artisan superadmin:jobs:dlq`";
            }
            if ($outboxStats['oldest_pending_age_seconds'] > 300) {
                $metrics['status'] = 'DEGRADED';
                $metrics['alerts'][] = "Outbox Latency Alert: Oldest pending event is {$outboxStats['oldest_pending_age_seconds']}s old (> 300s threshold)";
            }
            if ($outboxStats['pending'] > 500) {
                $metrics['status'] = 'DEGRADED';
                $metrics['alerts'][] = "Outbox Saturation Alert: {$outboxStats['pending']} pending events in queue";
            }
        } catch (\Throwable $e) {
            $metrics['alerts'][] = 'Outbox query failure: '.$e->getMessage();
        }
        $metrics['components']['outbox'] = $outboxStats;

        // 4. Idempotency Key Storage & Expired Leases Probe
        $idempotencyStats = [
            'active_leases' => 0,
            'expired_leases' => 0,
            'completed' => 0,
        ];
        try {
            $idRows = DB::table('sys_idempotency_keys')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $idempotencyStats['active_leases'] = (int) ($idRows['PROCESSING'] ?? 0);
            $idempotencyStats['completed'] = (int) ($idRows['COMPLETED'] ?? 0);

            // Check for stuck/expired leases (> 2 minutes or past processing_until)
            $expiredCount = DB::table('sys_idempotency_keys')
                ->where('status', 'PROCESSING')
                ->where(function ($q) {
                    $q->where('processing_until', '<', Carbon::now())
                        ->orWhere('created_at', '<', Carbon::now()->subMinutes(2));
                })
                ->count();

            $idempotencyStats['expired_leases'] = $expiredCount;

            if ($expiredCount > 5) {
                $metrics['alerts'][] = "Idempotency Warning: {$expiredCount} expired lease(s) detected (reclaimed on next request)";
            }
        } catch (\Throwable $e) {
            $metrics['alerts'][] = 'Idempotency query failure: '.$e->getMessage();
        }
        $metrics['components']['idempotency'] = $idempotencyStats;

        if ($this->option('json')) {
            $this->line(json_encode($metrics, JSON_PRETTY_PRINT));

            return ($metrics['status'] === 'HEALTHY') ? 0 : 1;
        }

        $this->info('================================================================================');
        $this->info(" Super Admin Jobs Telemetry & Health Probe: [{$metrics['status']}]");
        $this->info('================================================================================');
        $this->line('Redis/Dragonfly:  '.($redisHealthy ? '<info>ONLINE</info>' : '<error>OFFLINE</error>')." ({$redisLatencyMs}ms) | Generation v{$metrics['components']['redis']['version']} | Cache Hits: {$telemetry['hits']} | Misses: {$telemetry['misses']} (Hit Ratio: {$telemetry['hit_ratio_percent']}%)");
        $this->line('Elasticsearch:    '.($esHealthy ? '<info>ONLINE</info>' : '<error>DEGRADED</error>')." ({$esLatencyMs}ms) | Documents: {$esDocCount} | Breaker: [{$cbState}]");
        $this->line("Transactional Outbox: PENDING: {$outboxStats['pending']} | PROCESSING: {$outboxStats['processing']} | FAILED: {$outboxStats['failed']} | DLQ: {$outboxStats['dead_letter']} | Oldest Age: {$outboxStats['oldest_pending_age_seconds']}s");
        $this->line("Idempotency Keys: Active Leases: {$idempotencyStats['active_leases']} | Expired Leases: {$idempotencyStats['expired_leases']} | Completed: {$idempotencyStats['completed']}");

        if (! empty($metrics['alerts'])) {
            $this->newLine();
            $this->error(' Operational Alerts Detected:');
            foreach ($metrics['alerts'] as $alert) {
                $this->warn("  [!] {$alert}");
            }
        }

        $this->info('================================================================================');

        return ($metrics['status'] === 'HEALTHY') ? 0 : 1;
    }
}
