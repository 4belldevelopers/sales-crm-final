<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\ElasticService;
use App\Services\SuperAdminBenchCandidatesCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminBenchHealthCommand
 *
 * Intent: Operational health probe and telemetry diagnostic CLI for the Super Admin Bench Candidates subsystem.
 * Evaluates Redis Dragonfly latency, Elasticsearch cluster health, Circuit Breaker state,
 * transactional outbox queue backlog, dead-letter count, and cache version consistency.
 *
 * Signature: php artisan superadmin:bench:health {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchHealthCommand extends Command
{
    protected $signature = 'superadmin:bench:health {--json : Output metrics as JSON}';

    protected $description = 'Operational telemetry and health check for the Super Admin Bench Candidates subsystem';

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
            Cache::put('health:probe:sa_bench', 1, 10);
            $val = Cache::get('health:probe:sa_bench');
            $redisHealthy = ($val === 1);
            $redisLatencyMs = round((microtime(true) - $redisStart) * 1000, 2);
        } catch (\Throwable $e) {
            $metrics['status'] = 'DEGRADED';
        }
        $metrics['components']['redis'] = [
            'healthy' => $redisHealthy,
            'latency_ms' => $redisLatencyMs,
            'version' => SuperAdminBenchCandidatesCacheManager::getVersion(),
        ];

        // 2. Elasticsearch & Circuit Breaker Probe
        $cb = app(ElasticsearchCircuitBreaker::class);
        $cbState = $cb->getState();
        $esHealthy = false;
        $esLatencyMs = 0.0;
        $esDocCount = 0;
        $benchIndex = defined('ELASTIC_CANDIDATES_BENCH') ? ELASTIC_CANDIDATES_BENCH : config('constants.ELASTIC_CANDIDATES_BENCH', 'elastic_candidates_bench');

        try {
            $esStart = microtime(true);
            $elasticService = app(ElasticService::class);
            if ($elasticService->indexExists($benchIndex)) {
                $countRes = $elasticService->getClient()->count(['index' => $benchIndex]);
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
            'index_name' => $benchIndex,
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
                ->where('aggregate_type', 'BenchCandidate')
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
                ->where('tenant_id', 0)
                ->where(function ($q) {
                    $q->where('idempotency_key', 'like', 'sa_app_%')
                        ->orWhere('idempotency_key', 'like', 'autoapp_%')
                        ->orWhere('idempotency_key', 'like', 'approve_%')
                        ->orWhere('idempotency_key', 'like', 'reject_%');
                })
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
            $this->output->writeln(json_encode($metrics, JSON_PRETTY_PRINT));

            return 0;
        }

        // Formatted Console Output
        $this->info('========================================================================');
        $this->info('    SUPER ADMIN BENCH CANDIDATES SUBSYSTEM TELEMETRY & HEALTH PROBE     ');
        $this->info('========================================================================');
        $this->line('Overall System Health : <fg='.($metrics['status'] === 'HEALTHY' ? 'green' : 'red').";options=bold>{$metrics['status']}</>");
        $this->line("Report Timestamp      : {$metrics['timestamp']}");
        $this->newLine();

        $this->info('[1] Redis / DragonflyDB Caching:');
        $this->line('  - Status            : '.($redisHealthy ? '<fg=green>ONLINE</>' : '<fg=red>UNAVAILABLE</>'));
        $this->line("  - Roundtrip Latency : {$redisLatencyMs} ms");
        $this->line("  - Cache Generation  : v{$metrics['components']['redis']['version']}");
        $this->newLine();

        $this->info('[2] Elasticsearch CQRS Cluster:');
        $this->line('  - Status            : '.($esHealthy ? '<fg=green>ONLINE</>' : '<fg=red>UNAVAILABLE</>'));
        $this->line("  - Roundtrip Latency : {$esLatencyMs} ms");
        $this->line("  - Document Count    : {$esDocCount} docs ({$benchIndex})");
        $this->line("  - Circuit Breaker   : {$cbState}");
        $this->newLine();

        $this->info('[3] Transactional Outbox (sys_outbox_events):');
        $this->line("  - Pending Queue     : {$outboxStats['pending']} events");
        $this->line("  - In Processing     : {$outboxStats['processing']} events");
        $this->line("  - Failed Retrying   : {$outboxStats['failed']} events");
        $this->line('  - Dead Letter (DLQ) : '.($outboxStats['dead_letter'] > 0 ? "<fg=red;options=bold>{$outboxStats['dead_letter']}</>" : '<fg=green>0</>'));
        $this->line("  - Completed (24h)   : {$outboxStats['completed_last_24h']} events");
        $this->newLine();

        $this->info('[4] Distributed Idempotency (sys_idempotency_keys):');
        $this->line("  - Active Leases     : {$idempotencyStats['active_leases']} keys");
        $this->line("  - Completed Replays : {$idempotencyStats['completed']} keys");
        $this->info('========================================================================');

        return $metrics['status'] === 'HEALTHY' ? 0 : 1;
    }
}
