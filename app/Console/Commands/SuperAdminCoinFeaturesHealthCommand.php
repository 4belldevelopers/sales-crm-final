<?php

namespace App\Console\Commands;

use App\Services\CoinFeatureCacheManager;
use App\Services\ElasticCoinEventsIndex;
use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminCoinFeaturesHealthCommand
 *
 * Intent: Operational health probe and telemetry diagnostic CLI for the Super Admin Coin Features subsystem.
 * Evaluates Redis / Dragonfly latency, Elasticsearch cluster health, Circuit Breaker state,
 * transactional outbox queue backlog, dead-letter count, and cache version consistency.
 *
 * Signature: php artisan superadmin:coin-features:health {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminCoinFeaturesHealthCommand extends Command
{
    protected $signature = 'superadmin:coin-features:health {--json : Output metrics as JSON}';

    protected $description = 'Operational telemetry and health check for the Super Admin Coin Features subsystem';

    public function handle(
        ElasticCoinEventsIndex $elasticIndex,
        ElasticService $elasticService,
        ElasticsearchCircuitBreaker $circuitBreaker
    ): int {
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
            Cache::put('health:probe:sa_coin_features', 1, 10);
            $val = Cache::get('health:probe:sa_coin_features');
            $redisHealthy = ($val == 1 || $val === '1' || $val === true);
            $redisLatencyMs = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
        }
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => CoinFeatureCacheManager::getVersion(),
        ];

        // 2. Elasticsearch & Circuit Breaker Probe
        $cbState = $circuitBreaker->getState();
        $esHealthy = false;
        $esLatencyMs = 0.0;
        $esDocCount = 0;
        $indexName = $elasticIndex->getIndexName();

        try {
            $esStart = microtime(true);
            $esHealthy = $elasticService->indexExists($indexName);
            if ($esHealthy) {
                $stats = $elasticService->getClient()->count(['index' => $indexName]);
                $esDocCount = (int) ($stats['count'] ?? 0);
            }
            $esLatencyMs = round((microtime(true) - $esStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
        }
        $metrics['components']['elasticsearch'] = [
            'healthy' => $esHealthy,
            'index' => $indexName,
            'doc_count' => $esDocCount,
            'latency_ms' => $esLatencyMs,
            'circuit_breaker' => $cbState,
        ];

        // 3. MySQL Database Count
        $mysqlStart = microtime(true);
        $dbCount = 0;
        $dbHealthy = false;
        try {
            $dbCount = DB::table('coin_events')->count();
            $dbHealthy = true;
            $dbLatencyMs = round((microtime(true) - $mysqlStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'UNHEALTHY';
            $dbLatencyMs = 0.0;
        }
        $metrics['components']['mysql'] = [
            'healthy' => $dbHealthy,
            'table' => 'coin_events',
            'row_count' => $dbCount,
            'latency_ms' => $dbLatencyMs,
        ];

        // 4. Outbox Telemetry Probe
        $outboxPending = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->where('status', 'PENDING')
            ->count();

        $outboxProcessing = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->where('status', 'PROCESSING')
            ->count();

        $outboxFailed = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->where('status', 'FAILED')
            ->count();

        $outboxDlq = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->where('status', 'DEAD_LETTER')
            ->count();

        if ($outboxDlq > 0) {
            $metrics['status'] = 'DEGRADED';
        }

        $metrics['components']['outbox'] = [
            'pending' => $outboxPending,
            'processing' => $outboxProcessing,
            'failed' => $outboxFailed,
            'dead_letter' => $outboxDlq,
        ];

        // 5. Idempotency Key Storage Probe
        $idempotencyCount = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->count();

        $metrics['components']['idempotency'] = [
            'active_leases' => $idempotencyCount,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($metrics, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->info('===============================================================');
        $this->info(" Super Admin Coin Features Subsystem Health: [{$metrics['status']}]");
        $this->info('===============================================================');
        $this->table(
            ['Component', 'Status', 'Key Metrics'],
            [
                ['Redis / Dragonfly L2 Cache', $redisHealthy ? 'HEALTHY' : 'UNHEALTHY', "Latency: {$redisLatencyMs}ms | Version: {$metrics['components']['redis']['version']}"],
                ['Elasticsearch (local_coin_events)', $esHealthy ? 'HEALTHY' : 'UNHEALTHY', "Doc Count: {$esDocCount} | Latency: {$esLatencyMs}ms | CB: {$cbState}"],
                ['MySQL DB (coin_events)', $dbHealthy ? 'HEALTHY' : 'UNHEALTHY', "Rows: {$dbCount} | Latency: {$dbLatencyMs}ms"],
                ['Transactional Outbox', ($outboxDlq === 0) ? 'HEALTHY' : 'DEGRADED', "Pending: {$outboxPending} | Proc: {$outboxProcessing} | Fail: {$outboxFailed} | DLQ: {$outboxDlq}"],
                ['Idempotency Leases', 'ACTIVE', "Total Global Leases: {$idempotencyCount}"],
            ]
        );

        return ($metrics['status'] === 'UNHEALTHY') ? self::FAILURE : self::SUCCESS;
    }
}
