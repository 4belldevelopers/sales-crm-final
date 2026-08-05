<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Full recompute of all counter summary tables for a single company.
 * Dispatched by the Artisan command `counters:refresh {company_id}` or on-demand
 * when fresh_data=0 rows are detected. Not in the hot path — runs on 'counters' queue.
 */
class BulkCounterRefresherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 120, 300];
    public int $timeout = 300;

    public function __construct(public int $companyId)
    {
        $this->onQueue('counters');
    }

    public function handle(): void
    {
        $this->refreshJobCounters();
        $this->refreshPipelineCounters();
        $this->refreshApplicationCounters();
        $this->refreshJobAgencyCounters();
        $this->warmRedis();
    }

    private function refreshJobCounters(): void
    {
        $counts = DB::table('jobs')
            ->where('company_id', $this->companyId)
            ->selectRaw("
                COUNT(*) AS total_jobs,
                SUM(status = 'active') AS hiring_jobs,
                SUM(status = 'on_hold') AS on_hold_jobs,
                SUM(status = 'closed') AS closed_jobs,
                SUM(status = 'removed') AS removed_jobs,
                MAX(id) AS last_job_id
            ")
            ->first();

        DB::table('job_counters')->upsert(
            [
                'company_id'    => $this->companyId,
                'total_jobs'    => $counts->total_jobs ?? 0,
                'hiring_jobs'   => $counts->hiring_jobs ?? 0,
                'on_hold_jobs'  => $counts->on_hold_jobs ?? 0,
                'closed_jobs'   => $counts->closed_jobs ?? 0,
                'removed_jobs'  => $counts->removed_jobs ?? 0,
                'last_job_id'   => $counts->last_job_id ?? 0,
                'last_event_at' => now(),
            ],
            ['company_id'],
            ['total_jobs', 'hiring_jobs', 'on_hold_jobs', 'closed_jobs', 'removed_jobs', 'last_job_id', 'last_event_at']
        );
    }

    private function refreshPipelineCounters(): void
    {
        // Re-aggregate per job, then upsert all rows in a single query
        $stageMap = [
            1 => 'sourcing',
            2 => 'screening',
            3 => 'evaluation',
            4 => 'negotiation',
            5 => 'offered',
            6 => 'joined',
            7 => 'rejected',
        ];

        $rows = DB::table('job_applications')
            ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->where('jobs.company_id', $this->companyId)
            ->whereNull('job_applications.deleted_at')
            ->selectRaw('job_applications.job_id, job_applications.master_status_id, COUNT(*) as cnt')
            ->groupBy('job_applications.job_id', 'job_applications.master_status_id')
            ->get();

        $byJob = [];
        foreach ($rows as $row) {
            $jobId = $row->job_id;
            if (!isset($byJob[$jobId])) {
                $byJob[$jobId] = ['job_id' => $jobId, 'company_id' => $this->companyId, 'sourcing' => 0, 'screening' => 0, 'evaluation' => 0, 'negotiation' => 0, 'offered' => 0, 'joined' => 0, 'rejected' => 0, 'fresh_data' => 1, 'last_event_at' => now()];
            }
            $col = $stageMap[$row->master_status_id] ?? null;
            if ($col) {
                $byJob[$jobId][$col] = $row->cnt;
            }
        }

        if (!empty($byJob)) {
            DB::table('pipeline_counters')->upsert(
                array_values($byJob),
                ['job_id'],
                ['company_id', 'sourcing', 'screening', 'evaluation', 'negotiation', 'offered', 'joined', 'rejected', 'fresh_data', 'last_event_at']
            );
        }
    }

    private function refreshApplicationCounters(): void
    {
        $rows = DB::table('job_applications')
            ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            ->where('jobs.company_id', $this->companyId)
            ->whereNull('job_applications.deleted_at')
            ->selectRaw('job_applications.job_id, COUNT(*) as total_apps')
            ->groupBy('job_applications.job_id')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $upsertRows = $rows->map(fn($r) => [
            'job_id'        => $r->job_id,
            'total_apps'    => $r->total_apps,
            'last_event_at' => now(),
        ])->all();

        DB::table('application_counters')->upsert(
            $upsertRows,
            ['job_id'],
            ['total_apps', 'last_event_at']
        );
    }

    private function refreshJobAgencyCounters(): void
    {
        $benchRows = DB::table('bench_applications')
            ->join('jobs', 'jobs.id', '=', 'bench_applications.job_id')
            ->where('jobs.company_id', $this->companyId)
            ->whereNull('bench_applications.deleted_at')
            ->selectRaw('bench_applications.job_id, COUNT(*) as bench_total')
            ->groupBy('bench_applications.job_id')
            ->pluck('bench_total', 'job_id');

        $recRows = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->where('jobs.company_id', $this->companyId)
            ->selectRaw('recommendation_relation.job_id, final_status, COUNT(*) as cnt')
            ->groupBy('recommendation_relation.job_id', 'recommendation_relation.final_status')
            ->get();

        $byJob = [];
        foreach ($recRows as $row) {
            $jid = $row->job_id;
            if (!isset($byJob[$jid])) {
                $byJob[$jid] = ['job_id' => $jid, 'company_id' => $this->companyId, 'bench_total' => 0, 'rec_sent' => 0, 'rec_accepted' => 0, 'rec_rejected' => 0, 'last_event_at' => now()];
            }
            match ($row->final_status) {
                0 => $byJob[$jid]['rec_sent']     = $row->cnt,
                1 => $byJob[$jid]['rec_accepted']  = $row->cnt,
                2 => $byJob[$jid]['rec_rejected']  = $row->cnt,
                default => null,
            };
        }

        foreach ($benchRows as $jid => $total) {
            if (!isset($byJob[$jid])) {
                $byJob[$jid] = ['job_id' => $jid, 'company_id' => $this->companyId, 'bench_total' => 0, 'rec_sent' => 0, 'rec_accepted' => 0, 'rec_rejected' => 0, 'last_event_at' => now()];
            }
            $byJob[$jid]['bench_total'] = $total;
        }

        if (!empty($byJob)) {
            DB::table('job_agency_counter')->upsert(
                array_values($byJob),
                ['job_id'],
                ['bench_total', 'rec_sent', 'rec_accepted', 'rec_rejected', 'last_event_at']
            );
        }
    }

    private function warmRedis(): void
    {
        try {
            $jc = DB::table('job_counters')->where('company_id', $this->companyId)->first();
            if ($jc) {
                Redis::hmset("job_counters:{$this->companyId}", (array) $jc);
            }
        } catch (\Throwable) {
            // Redis unavailable — non-fatal
        }
    }
}
