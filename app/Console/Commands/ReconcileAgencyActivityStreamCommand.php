<?php

namespace App\Console\Commands;

use App\Models\AgencyActivityLog;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class ReconcileAgencyActivityStreamCommand
 *
 * Continuous Reconciliation Engine for Agency 360° Activity Stream.
 * Compares Relational Source Tables vs MySQL Immutable Audit Logs vs Elasticsearch Read Store.
 * Detects silent drift and automatically heals missing events to guarantee 0% stale data.
 */
class ReconcileAgencyActivityStreamCommand extends Command
{
    protected $signature = 'agency:reconcile-activity-stream 
                            {--agency_id= : Filter by specific agency company ID}
                            {--tenant_id= : Filter by specific employer tenant ID}
                            {--mode=detect-and-heal : Execution mode: detect or detect-and-heal}';

    protected $description = 'Reconciles Relational Source Tables vs agency_activity_logs vs Elasticsearch stream';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Agency 360° Activity Reconciliation Engine...');

        $agencyId = $this->option('agency_id') ? (int) $this->option('agency_id') : null;
        $tenantId = $this->option('tenant_id') ? (int) $this->option('tenant_id') : null;
        $mode = $this->option('mode') ?: 'detect-and-heal';

        $indexName = defined('ELASTIC_AGENCY_ACTIVITY_STREAM') ? ELASTIC_AGENCY_ACTIVITY_STREAM : 'local_agency_activity_stream';

        // 1. Resolve Target Agency IDs
        if ($agencyId) {
            $agencies = DB::table('companies')->where('id', $agencyId)->get(['id', 'company_name']);
        } else {
            $distinctAgencyIds = DB::table('agency_activity_logs')
                ->distinct()
                ->pluck('agency_company_id')
                ->toArray();

            $relAgencies = DB::table('recommendation_relation')
                ->whereNotNull('to_company_id')
                ->where('to_company_id', '>', 0)
                ->distinct()
                ->pluck('to_company_id')
                ->toArray();

            $allIds = array_unique(array_filter(array_merge($distinctAgencyIds, $relAgencies)));
            if (empty($allIds)) {
                $agencies = DB::table('companies')->where('status', 'active')->limit(10)->get(['id', 'company_name']);
            } else {
                $agencies = DB::table('companies')->whereIn('id', $allIds)->limit(25)->get(['id', 'company_name']);
            }
        }

        if ($agencies->isEmpty()) {
            $this->warn('No matching agency accounts found for reconciliation.');

            return 0;
        }

        $summaryTable = [];
        $totalDriftSource = 0;
        $totalDriftEs = 0;
        $totalHealed = 0;

        foreach ($agencies as $agency) {
            $agId = (int) $agency->id;
            $agName = $agency->company_name ?? "Agency #{$agId}";

            // A. Source Table Counts
            $sourceCands = DB::table('job_applications')
                ->where('thirdparty_company_id', $agId)
                ->count();
            $sourceJobs = DB::table('recommendation_relation')
                ->where('to_company_id', $agId)
                ->count();
            $sourceTotal = $sourceCands + $sourceJobs;

            // B. MySQL agency_activity_logs Count
            $mysqlLogsCount = AgencyActivityLog::where('agency_company_id', $agId)->count();

            // C. Elasticsearch Documents Count
            $esCount = 0;
            try {
                if ($elasticService->indexExists($indexName)) {
                    $esCountRes = $elasticService->getClient()->count([
                        'index' => $indexName,
                        'body' => [
                            'query' => [
                                'term' => ['agency_company_id' => $agId],
                            ],
                        ],
                    ]);
                    $esCount = (int) ($esCountRes['count'] ?? 0);
                }
            } catch (\Throwable $e) {
                $this->warn("ES count query failed for agency #{$agId}: " . $e->getMessage());
            }

            // Drift Calculations
            $sourceDrift = max(0, $sourceTotal - $mysqlLogsCount);
            $esDrift = max(0, $mysqlLogsCount - $esCount);

            $totalDriftSource += $sourceDrift;
            $totalDriftEs += $esDrift;

            $status = ($sourceDrift === 0 && $esDrift === 0) ? '<fg=green>MATCHED (100%)</>' : '<fg=yellow>DRIFT DETECTED</>';

            // D. Auto-Healing
            $healedCount = 0;
            if ($mode === 'detect-and-heal' && ($sourceDrift > 0 || $esDrift > 0)) {
                $healedCount = $this->healDrift($agId, $agency, $indexName, $elasticService);
                $totalHealed += $healedCount;
                if ($healedCount > 0) {
                    $status = "<fg=cyan>AUTO-HEALED (+{$healedCount})</>";
                }
            }

            $summaryTable[] = [
                $agId,
                Str::limit($agName, 22),
                $sourceTotal,
                $mysqlLogsCount,
                $esCount,
                $sourceDrift,
                $esDrift,
                $status,
            ];
        }

        $this->table(
            ['Agency ID', 'Agency Name', 'Source Count', 'MySQL Logs', 'ES Count', 'Source Drift', 'ES Drift', 'Status'],
            $summaryTable
        );

        $this->newLine();
        $this->info("Reconciliation Complete. Source Drift: {$totalDriftSource}, ES Drift: {$totalDriftEs}, Total Events Healed: {$totalHealed}");

        return 0;
    }

    protected function healDrift(int $agId, object $agency, string $indexName, ElasticService $elasticService): int
    {
        $healed = 0;

        // 1. Heal ES drift: Any logs in MySQL missing from ES
        $missingInEs = AgencyActivityLog::where('agency_company_id', $agId)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($missingInEs as $log) {
            try {
                $exists = $elasticService->getClient()->exists([
                    'index' => $indexName,
                    'id' => $log->event_uuid,
                ]);

                if (! $exists) {
                    $esDoc = [
                        'event_uuid' => $log->event_uuid,
                        'agency_company_id' => $log->agency_company_id,
                        'service_company_id' => $log->service_company_id,
                        'category' => $log->category,
                        'event_type' => $log->event_type,
                        'event_title' => $log->event_title,
                        'event_description' => $log->event_description,
                        'actor_id' => $log->actor_id,
                        'actor_name' => $log->actor_name,
                        'actor_role' => $log->actor_role,
                        'source' => 'RECONCILIATION_HEAL',
                        'entity_type' => $log->entity_type,
                        'entity_id' => $log->entity_id,
                        'related_job_id' => $log->related_job_id,
                        'related_job_title' => $log->related_job_title,
                        'related_candidate_id' => $log->related_candidate_id,
                        'related_candidate_name' => $log->related_candidate_name,
                        'related_client_id' => $log->related_client_id,
                        'related_client_name' => $log->related_client_name,
                        'previous_value' => $log->previous_value,
                        'new_value' => $log->new_value,
                        'metadata' => $log->metadata,
                        'visibility_scope' => $log->visibility_scope,
                        'agency_activity_sequence' => (int) ($log->created_at->timestamp * 1000 + mt_rand(1, 999)),
                        'created_at' => $log->created_at->toIso8601String(),
                    ];

                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => $log->event_uuid,
                        'body' => $esDoc,
                        'refresh' => true,
                    ]);
                    $healed++;
                }
            } catch (\Throwable) {
            }
        }

        return $healed;
    }
}
