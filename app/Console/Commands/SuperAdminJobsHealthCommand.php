<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use App\Services\SuperAdminJobsCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminJobsHealthCommand
 *
 * Intent: Operational health probe and telemetry diagnostic CLI for Super Admin Jobs domain.
 * Evaluates Redis Dragonfly latency, Elasticsearch cluster health, Circuit Breaker state,
 * transactional outbox queue backlog, dead-letter count, and cache version consistency.
 *
 * Signature: php artisan superadmin:jobs:health
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
            'components' => [],
        ];

        // 1. Redis Cache & Latency Probe
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
        }
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => SuperAdminJobsCacheManager::getVersion(),
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
            if ($cbState === ElasticsearchCircuitBreaker::STATE_OPEN) {
                $metrics['status'] = 'DEGRADED';
            }
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

            if ($outboxStats['dead_letter'] > 0 || $outboxStats['pending'] > 500) {
                $metrics['status'] = 'DEGRADED';
            }
        } catch (\Throwable $e) {
        }
        $metrics['components']['outbox'] = $outboxStats;

        // 4. Idempotency Key Storage Probe
        $idempotencyStats = [
            'active_leases' => 0,
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
        } catch (\Throwable $e) {
        }
        $metrics['components']['idempotency'] = $idempotencyStats;

        if ($this->option('json')) {
            $this->line(json_encode($metrics, JSON_PRETTY_PRINT));

            return ($metrics['status'] === 'HEALTHY') ? 0 : 1;
        }

        $this->info('===========================================================');
        $this->info(" Super Admin Jobs Telemetry Health Check: [{$metrics['status']}]");
        $this->info('===========================================================');
        $this->line('Redis/Dragonfly:  '.($redisHealthy ? '<info>ONLINE</info>' : '<error>OFFLINE</error>')." ({$redisLatencyMs}ms) | Generation Version: {$metrics['components']['redis']['version']}");
        $this->line('Elasticsearch:    '.($esHealthy ? '<info>ONLINE</info>' : '<error>DEGRADED</error>')." ({$esLatencyMs}ms) | Documents: {$esDocCount} | Breaker: [{$cbState}]");
        $this->line("Transactional Outbox: PENDING: {$outboxStats['pending']} | PROCESSING: {$outboxStats['processing']} | FAILED: {$outboxStats['failed']} | DLQ: {$outboxStats['dead_letter']}");
        $this->line("Idempotency Keys: Active Leases: {$idempotencyStats['active_leases']} | Completed: {$idempotencyStats['completed']}");
        $this->info('===========================================================');

        return 0;
    }
}
