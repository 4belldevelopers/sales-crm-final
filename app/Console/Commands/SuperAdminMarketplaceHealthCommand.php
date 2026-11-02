<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminMarketplaceCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMarketplaceHealthCommand
 *
 * Intent: Operational health probe and telemetry diagnostic CLI for Super Admin Marketplace Operations & Job Public Market.
 * Evaluates Redis Dragonfly latency, Elasticsearch cluster health, Circuit Breaker state,
 * transactional outbox queue backlog, dead-letter count, and cache version consistency.
 *
 * Signature: php artisan superadmin:marketplace:health {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMarketplaceHealthCommand extends Command
{
    protected $signature = 'superadmin:marketplace:health {--json : Output metrics as JSON}';

    protected $description = 'Operational telemetry and health check for Super Admin Marketplace Operations subsystem';

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
            Cache::put('health:probe:sa_marketplace', 1, 10);
            $val = Cache::get('health:probe:sa_marketplace');
            $redisHealthy = ((int) $val === 1);
            $redisLatencyMs = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
        }
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => SuperAdminMarketplaceCacheManager::getVersion(),
        ];

        // 2. Elasticsearch & Circuit Breaker Probe
        $cb = app(ElasticsearchCircuitBreaker::class);
        $cbState = $cb->getState();
        $esHealthy = false;
        $esLatencyMs = 0.0;
        $esDocCount = 0;
        $jobsIndex = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';

        try {
            $esStart = microtime(true);
            $elasticService = app(ElasticService::class);
            if ($elasticService->indexExists($jobsIndex)) {
                $countRes = $elasticService->getClient()->count(['index' => $jobsIndex]);
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
            'index_name' => $jobsIndex,
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
                ->where('aggregate_type', 'MarketJob')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $outboxStats['pending'] = (int) ($rows['PENDING'] ?? 0);
            $outboxStats['processing'] = (int) ($rows['PROCESSING'] ?? 0);
            $outboxStats['failed'] = (int) ($rows['FAILED'] ?? 0);
            $outboxStats['dead_letter'] = (int) ($rows['DEAD_LETTER'] ?? 0);

            $outboxStats['completed_last_24h'] = (int) DB::table('sys_outbox_events')
                ->where('aggregate_type', 'MarketJob')
                ->where('status', 'COMPLETED')
                ->where('processed_at', '>=', now()->subDay())
                ->count();
        } catch (\Throwable $e) {
        }

        if ($outboxStats['dead_letter'] > 0 || $outboxStats['failed'] > 5) {
            $metrics['status'] = 'WARNING';
        }
        $metrics['components']['outbox'] = $outboxStats;

        // 4. Idempotency Key Store Probe
        $idempotencyStats = [
            'active_leases' => 0,
            'completed_last_24h' => 0,
        ];
        try {
            $idempotencyStats['active_leases'] = (int) DB::table('sys_idempotency_keys')
                ->where('idempotency_key', 'like', 'sa_mkt_%')
                ->where('status', 'PROCESSING')
                ->where('processing_until', '>', now())
                ->count();

            $idempotencyStats['completed_last_24h'] = (int) DB::table('sys_idempotency_keys')
                ->where('idempotency_key', 'like', 'sa_mkt_%')
                ->where('status', 'COMPLETED')
                ->where('completed_at', '>=', now()->subDay())
                ->count();
        } catch (\Throwable $e) {
        }
        $metrics['components']['idempotency'] = $idempotencyStats;

        // Output format
        if ($this->option('json')) {
            $this->output->write(json_encode($metrics, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return 0;
        }

        $this->newLine();
        $this->info('================================================================================');
        $this->info(' SUPER ADMIN MARKETPLACE OPERATIONS & PUBLIC MARKET — TELEMETRY PROBE ');
        $this->info('================================================================================');
        $this->line('Overall System Health: <fg='.($metrics['status'] === 'HEALTHY' ? 'green' : ($metrics['status'] === 'WARNING' ? 'yellow' : 'red')).";options=bold>{$metrics['status']}</>");
        $this->line("Timestamp: {$metrics['timestamp']}");
        $this->newLine();

        $this->comment('Redis Dragonfly Caching:');
        $this->line('  Status:     '.($redisHealthy ? '<fg=green>ONLINE</>' : '<fg=red>OFFLINE</>'));
        $this->line("  Latency:    {$redisLatencyMs} ms");
        $this->line("  Generation: v{$metrics['components']['redis']['version']}");

        $this->newLine();
        $this->comment('Elasticsearch CQRS Cluster:');
        $this->line('  Status:          '.($esHealthy ? '<fg=green>ONLINE</>' : '<fg=red>UNAVAILABLE</>'));
        $this->line("  Latency:         {$esLatencyMs} ms");
        $this->line('  Document Count:  '.number_format($esDocCount));
        $this->line('  Circuit Breaker: <fg='.($cbState === 'CLOSED' ? 'green' : 'yellow').">{$cbState}</>");
        $this->line("  Index:           {$jobsIndex}");

        $this->newLine();
        $this->comment('Transactional Outbox Queue:');
        $this->line("  Pending:         {$outboxStats['pending']}");
        $this->line("  Processing:      {$outboxStats['processing']}");
        $this->line("  Failed:          {$outboxStats['failed']}");
        $this->line('  Dead Letter:     '.($outboxStats['dead_letter'] > 0 ? "<fg=red;options=bold>{$outboxStats['dead_letter']}</>" : '<fg=green>0</>'));
        $this->line("  Completed (24h): {$outboxStats['completed_last_24h']}");

        $this->newLine();
        $this->comment('Idempotency Lease Ledger:');
        $this->line("  Active In-Flight Leases: {$idempotencyStats['active_leases']}");
        $this->line("  Completed Keys (24h):    {$idempotencyStats['completed_last_24h']}");
        $this->newLine();

        return 0;
    }
}
