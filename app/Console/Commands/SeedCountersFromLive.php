<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backfills all four counter summary tables from live MySQL data.
 * Safe to re-run (uses upsert / insertOrUpdate). Processes in chunks
 * to avoid locking large tables during a rolling deploy.
 */
class SeedCountersFromLive extends Command
{
    protected $signature   = 'counters:seed-from-live {--chunk=500 : Rows per batch insert}';
    protected $description = 'Seed job_counters, pipeline_counters, application_counters, and job_agency_counter from live data';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        $this->seedJobCounters($chunk);
        $this->seedPipelineCounters($chunk);
        $this->seedApplicationCounters($chunk);
        $this->seedJobAgencyCounter($chunk);

        $this->info('All counter tables seeded. Run counters:verify-drift to confirm zero drift.');
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // job_counters — one row per company
    // -------------------------------------------------------------------------
    private function seedJobCounters(int $chunk): void
    {
        $this->line('Seeding job_counters…');

        // jobs has no deleted_at (hard-delete only) — count all rows.
        // The observer writes $job->status as-is into {status}_jobs columns.
        // CASE covers both historical naming conventions: hiring/active,
        // on_hold/hold, closed/inactive. removed_jobs is event-driven (hard
        // deletes tracked by observer increment) and cannot be reconstructed
        // from live data — seeded as 0.
        $rows = DB::table('jobs')
            ->select([
                'company_id',
                DB::raw('COUNT(*) as total_jobs'),
                DB::raw("SUM(CASE WHEN status IN ('hiring','active')   THEN 1 ELSE 0 END) as hiring_jobs"),
                DB::raw("SUM(CASE WHEN status IN ('on_hold','hold')    THEN 1 ELSE 0 END) as on_hold_jobs"),
                DB::raw("SUM(CASE WHEN status IN ('closed','inactive') THEN 1 ELSE 0 END) as closed_jobs"),
                DB::raw('MAX(id) as last_job_id'),
            ])
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get();

        $rows->chunk($chunk)->each(function ($batch) {
            $inserts = $batch->map(fn($r) => [
                'company_id'    => $r->company_id,
                'total_jobs'    => $r->total_jobs,
                'hiring_jobs'   => $r->hiring_jobs,
                'on_hold_jobs'  => $r->on_hold_jobs,
                'closed_jobs'   => $r->closed_jobs,
                'removed_jobs'  => 0,
                'last_job_id'   => $r->last_job_id,
                'last_event_at' => now(),
            ])->all();

            DB::table('job_counters')->upsert(
                $inserts,
                ['company_id'],
                ['total_jobs', 'hiring_jobs', 'on_hold_jobs', 'closed_jobs', 'removed_jobs', 'last_job_id', 'last_event_at']
            );
        });

        $this->info("  job_counters: {$rows->count()} companies seeded.");
    }

    // -------------------------------------------------------------------------
    // pipeline_counters — one row per job_id
    // Maps job_applications.master_status_id → stage column (mirrors UpdatePipelineCountersJob)
    // -------------------------------------------------------------------------
    private function seedPipelineCounters(int $chunk): void
    {
        $this->line('Seeding pipeline_counters…');

        $stageMap = [
            1 => 'sourcing',
            2 => 'screening',
            3 => 'evaluation',
            4 => 'negotiation',
            5 => 'offered',
            6 => 'joined',
            7 => 'rejected',
            9 => 'rejected',
        ];

        // Build a CASE expression to match UpdatePipelineCountersJob::stageFromMasterStatus()
        $caseExpr = "CASE master_status_id
            WHEN 1 THEN 'sourcing'
            WHEN 2 THEN 'screening'
            WHEN 3 THEN 'evaluation'
            WHEN 4 THEN 'negotiation'
            WHEN 5 THEN 'offered'
            WHEN 6 THEN 'joined'
            WHEN 7 THEN 'rejected'
            WHEN 9 THEN 'rejected'
            ELSE 'sourcing'
        END";

        // Aggregate by job_id only — job_applications.company_id can be NULL for
        // third-party applications, so we look up company_id from the jobs table instead.
        $rows = DB::table('job_applications')
            ->select([
                'job_id',
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'sourcing'    THEN 1 ELSE 0 END) as sourcing"),
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'screening'   THEN 1 ELSE 0 END) as screening"),
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'evaluation'  THEN 1 ELSE 0 END) as evaluation"),
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'negotiation' THEN 1 ELSE 0 END) as negotiation"),
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'offered'     THEN 1 ELSE 0 END) as offered"),
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'joined'      THEN 1 ELSE 0 END) as joined"),
                DB::raw("SUM(CASE WHEN ({$caseExpr}) = 'rejected'    THEN 1 ELSE 0 END) as rejected"),
            ])
            ->whereNull('deleted_at')
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->get();

        // Fetch company_id from jobs table (authoritative per-job owner)
        $jobCompanyIds = DB::table('jobs')
            ->whereIn('id', $rows->pluck('job_id')->all())
            ->pluck('company_id', 'id');

        $rows->chunk($chunk)->each(function ($batch) use ($jobCompanyIds) {
            $inserts = $batch->map(fn($r) => [
                'job_id'       => $r->job_id,
                'company_id'   => $jobCompanyIds[$r->job_id] ?? 0,
                'sourcing'     => $r->sourcing,
                'screening'    => $r->screening,
                'evaluation'   => $r->evaluation,
                'negotiation'  => $r->negotiation,
                'offered'      => $r->offered,
                'joined'       => $r->joined,
                'rejected'     => $r->rejected,
                'fresh_data'   => 0,
                'last_event_at' => now(),
            ])->all();

            DB::table('pipeline_counters')->upsert(
                $inserts,
                ['job_id'],
                ['company_id', 'sourcing', 'screening', 'evaluation', 'negotiation', 'offered', 'joined', 'rejected', 'fresh_data', 'last_event_at']
            );
        });

        $this->info("  pipeline_counters: {$rows->count()} jobs seeded.");
    }

    // -------------------------------------------------------------------------
    // application_counters — one row per job_id
    // -------------------------------------------------------------------------
    private function seedApplicationCounters(int $chunk): void
    {
        $this->line('Seeding application_counters…');

        $rows = DB::table('job_applications')
            ->select('job_id', DB::raw('COUNT(*) as total_apps'))
            ->whereNull('deleted_at')
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->get();

        $rows->chunk($chunk)->each(function ($batch) {
            $inserts = $batch->map(fn($r) => [
                'job_id'        => $r->job_id,
                'total_apps'    => $r->total_apps,
                'last_event_at' => now(),
            ])->all();

            DB::table('application_counters')->upsert(
                $inserts,
                ['job_id'],
                ['total_apps', 'last_event_at']
            );
        });

        $this->info("  application_counters: {$rows->count()} jobs seeded.");
    }

    // -------------------------------------------------------------------------
    // job_agency_counter — one row per job_id
    // Sources: recommendation_relation (rec_*) + bench_applications (bench_total)
    // -------------------------------------------------------------------------
    private function seedJobAgencyCounter(int $chunk): void
    {
        $this->line('Seeding job_agency_counter…');

        // Aggregate recommendation counts per job (mirrors UpdateJobAgencyCounterJob)
        $recRows = DB::table('recommendation_relation')
            ->select([
                'job_id',
                DB::raw('SUM(CASE WHEN final_status = 0 THEN 1 ELSE 0 END) as rec_sent'),
                DB::raw('SUM(CASE WHEN final_status = 1 THEN 1 ELSE 0 END) as rec_accepted'),
                DB::raw('SUM(CASE WHEN final_status = 2 THEN 1 ELSE 0 END) as rec_rejected'),
            ])
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->get()
            ->keyBy('job_id');

        // Aggregate bench totals per job (mirrors UpdateBenchCountersJob).
        // bench_applications has no deleted_at — count all rows.
        $benchRows = DB::table('bench_applications')
            ->select('job_id', DB::raw('COUNT(*) as bench_total'))
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->get()
            ->keyBy('job_id');

        // Fetch company_id per job (needed for job_agency_counter.company_id)
        $jobCompanies = DB::table('jobs')
            ->select('id as job_id', 'company_id')
            ->whereIn('id', array_unique(array_merge(
                $recRows->keys()->all(),
                $benchRows->keys()->all()
            )))
            ->pluck('company_id', 'job_id');

        $allJobIds = collect(array_unique(array_merge(
            $recRows->keys()->all(),
            $benchRows->keys()->all()
        )));

        $count = 0;
        $allJobIds->chunk($chunk)->each(function ($batch) use ($recRows, $benchRows, $jobCompanies, &$count) {
            $inserts = $batch->map(function ($jobId) use ($recRows, $benchRows, $jobCompanies) {
                $rec   = $recRows[$jobId] ?? null;
                $bench = $benchRows[$jobId] ?? null;
                return [
                    'job_id'        => $jobId,
                    'company_id'    => $jobCompanies[$jobId] ?? 0,
                    'bench_total'   => $bench ? $bench->bench_total : 0,
                    'rec_sent'      => $rec ? $rec->rec_sent      : 0,
                    'rec_accepted'  => $rec ? $rec->rec_accepted  : 0,
                    'rec_rejected'  => $rec ? $rec->rec_rejected  : 0,
                    'last_event_at' => now(),
                ];
            })->all();

            DB::table('job_agency_counter')->upsert(
                $inserts,
                ['job_id'],
                ['company_id', 'bench_total', 'rec_sent', 'rec_accepted', 'rec_rejected', 'last_event_at']
            );

            $count += count($inserts);
        });

        $this->info("  job_agency_counter: {$count} jobs seeded.");
    }
}
