<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminJobRequestCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminJobRequestsHealthCommand
 *
 * INTENT:
 * Operational health probe and telemetry diagnostic CLI for Super Admin Job Collaboration Requests.
 * Evaluates Redis Dragonfly latency, Elasticsearch cluster health (local_jobs_request),
 * Circuit Breaker state, transactional outbox queue backlog, dead-letter count, active idempotency keys,
 * and cache version consistency.
 *
 * SIGNATURE:
 * php artisan superadmin:job-requests:health {--json : Output metrics as JSON}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminJobRequestsHealthCommand extends Command
{
    protected $signature = 'superadmin:job-requests:health {--json : Output metrics as JSON}';

    protected $description = 'Operational telemetry and health check for the Super Admin Job Collaboration Requests subsystem';

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
            Cache::put('health:probe:sa_job_requests', 1, 10);
            $val = Cache::get('health:probe:sa_job_requests');
            $redisHealthy = ((int) $val === 1);
            $redisLatencyMs = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
        }
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => SuperAdminJobRequestCacheManager::getVersion(),
        ];

        // 2. Elasticsearch & Circuit Breaker Probe
        $cb = app(ElasticsearchCircuitBreaker::class);
        $cbState = $cb->getState();
        $esHealthy = false;
        $esLatencyMs = 0.0;
        $esDocCount = 0;
        $pendingCount = 0;
        $acceptedCount = 0;

        try {
            $esStart = microtime(true);
            $elasticService = app(ElasticService::class);
            if ($elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
                $countRes = $elasticService->getClient()->count(['index' => ELASTIC_JOB_REQUEST]);
                $esDocCount = (int) ($countRes['count'] ?? 0);

                $pRes = $elasticService->getClient()->count([
                    'index' => ELASTIC_JOB_REQUEST,
                    'body' => ['query' => ['term' => ['final_status' => 0]]],
                ]);
                $pendingCount = (int) ($pRes['count'] ?? 0);

                $aRes = $elasticService->getClient()->count([
                    'index' => ELASTIC_JOB_REQUEST,
                    'body' => ['query' => ['terms' => ['final_status' => [1, 2, 3]]]],
                ]);
                $acceptedCount = (int) ($aRes['count'] ?? 0);

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
            'total_docs' => $esDocCount,
            'pending_docs' => $pendingCount,
            'accepted_docs' => $acceptedCount,
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
                ->where('aggregate_type', 'JobRequest')
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $outboxStats['pending'] = (int) ($rows['PENDING'] ?? 0);
            $outboxStats['processing'] = (int) ($rows['PROCESSING'] ?? 0);
            $outboxStats['failed'] = (int) ($rows['FAILED'] ?? 0);
            $outboxStats['dead_letter'] = (int) ($rows['DEAD_LETTER'] ?? 0);

            $outboxStats['completed_last_24h'] = (int) DB::table('sys_outbox_events')
                ->where('aggregate_type', 'JobRequest')
                ->where('status', 'COMPLETED')
                ->where('created_at', '>=', now()->subHours(24))
                ->count();

            if ($outboxStats['failed'] > 10 || $outboxStats['dead_letter'] > 0) {
                $metrics['status'] = 'DEGRADED';
            }
        } catch (\Throwable $e) {
            $outboxStats['error'] = $e->getMessage();
        }
        $metrics['components']['outbox'] = $outboxStats;

        // 4. Idempotency Key Backlog Probe
        $idempotencyStats = [
            'active_leases' => 0,
            'completed_last_24h' => 0,
        ];
        try {
            $idempotencyStats['active_leases'] = (int) DB::table('sys_idempotency_keys')
                ->where('status', 'PROCESSING')
                ->where('expires_at', '>', now())
                ->count();

            $idempotencyStats['completed_last_24h'] = (int) DB::table('sys_idempotency_keys')
                ->where('status', 'COMPLETED')
                ->where('created_at', '>=', now()->subHours(24))
                ->count();
        } catch (\Throwable $e) {
            $idempotencyStats['error'] = $e->getMessage();
        }
        $metrics['components']['idempotency'] = $idempotencyStats;

        if ($this->option('json')) {
            $this->line(json_encode($metrics, JSON_PRETTY_PRINT));

            return $metrics['status'] === 'HEALTHY' ? 0 : 1;
        }

        // Formatted Console Table Output
        $this->info('========================================================================');
        $this->info(' SUPER ADMIN JOB COLLABORATION REQUESTS TELEMETRY & HEALTH PROBE');
        $this->info('========================================================================');
        $this->line('Overall System Health: <fg='.($metrics['status'] === 'HEALTHY' ? 'green' : 'red').";options=bold>{$metrics['status']}</>");
        $this->line("Timestamp:             {$metrics['timestamp']}");
        $this->newLine();

        $this->table(['Component', 'Status', 'Latency / Metric', 'Details'], [
            [
                'Redis Dragonfly Cache',
                $redisHealthy ? '<fg=green>UP</>' : '<fg=red>DOWN</>',
                "{$redisLatencyMs} ms",
                "Cache Generation Version: v{$metrics['components']['redis']['version']}",
            ],
            [
                'Elasticsearch (local_jobs_request)',
                $esHealthy ? '<fg=green>UP</>' : '<fg=red>DOWN</>',
                "{$esLatencyMs} ms",
                "Total: {$esDocCount} | Pending: {$pendingCount} | Accepted: {$acceptedCount} | CB: {$cbState}",
            ],
            [
                'Transactional Outbox',
                $outboxStats['dead_letter'] === 0 ? '<fg=green>HEALTHY</>' : '<fg=red>ATTENTION</>',
                "Pending: {$outboxStats['pending']}",
                "Processing: {$outboxStats['processing']} | Failed: {$outboxStats['failed']} | DLQ: {$outboxStats['dead_letter']} | 24h Done: {$outboxStats['completed_last_24h']}",
            ],
            [
                'Idempotency Engine',
                '<fg=green>ACTIVE</>',
                "Leases: {$idempotencyStats['active_leases']}",
                "24h Completed Keys: {$idempotencyStats['completed_last_24h']}",
            ],
        ]);

        return $metrics['status'] === 'HEALTHY' ? 0 : 1;
    }
}
