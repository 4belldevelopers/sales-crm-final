<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CountersReconcile — Step 9 of the Apptracker performance plan.
 *
 * Runs every 5 minutes via Horizon/scheduler. Recomputes per-stage pipeline
 * counts from add_job_wise_condidates (MySQL write store — authoritative) and
 * upserts them into pipeline_counters, then busts the Redis flexible-cache
 * keys so the next loadcounter() call serves fresh data.
 *
 * Tenancy: all queries include company_id; raw DB:: calls are NOT auto-scoped.
 */
class CountersReconcile extends Command
{
    protected $signature   = 'counters:reconcile';
    protected $description = 'Sync pipeline stage counts from MySQL into pipeline_counters and bust Redis cache keys';

    public function handle(): int
    {
        // Scope to jobs active in the last 30 days + any job already tracked.
        // Chunking avoids loading all job IDs into memory at once.
        $processed = 0;
        $errors    = 0;
        $cutoff    = now()->subDays(30);

        DB::table('add_job_wise_condidates')
            ->select('job_id', 'company_id')
            ->where(function ($q) use ($cutoff) {
                $q->where('updated_at', '>=', $cutoff)
                  ->orWhereIn(
                      DB::raw('CONCAT(job_id, "-", company_id)'),
                      DB::table('pipeline_counters')->selectRaw('CONCAT(job_id, "-", company_id)')
                  );
            })
            ->whereNotNull('job_id')
            ->whereNotNull('company_id')
            ->distinct()
            ->orderBy('job_id')
            ->chunk(200, function ($pairs) use (&$processed, &$errors) {
                $jobIds = $pairs->pluck('job_id')->unique()->values()->all();

                // One aggregation query per batch — no N+1.
                // Mirrors the two-branch should logic in loadcounter:
                //   own candidates        : thirdparty_company_id = 0
                //   third-party candidates: thirdparty_company_id > 0, status NOT IN (8,9)
                $rows = DB::table('add_job_wise_condidates')
                    ->select(
                        'job_id',
                        'company_id',
                        DB::raw('COUNT(*) as all_candidate'),
                        DB::raw('SUM(CASE WHEN master_status_id = 1 THEN 1 ELSE 0 END) AS sourcing'),
                        DB::raw('SUM(CASE WHEN master_status_id = 2 THEN 1 ELSE 0 END) AS screening'),
                        DB::raw('SUM(CASE WHEN master_status_id = 3 THEN 1 ELSE 0 END) AS interview'),
                        DB::raw('SUM(CASE WHEN master_status_id = 4 THEN 1 ELSE 0 END) AS negotiation'),
                        DB::raw('SUM(CASE WHEN master_status_id = 5 THEN 1 ELSE 0 END) AS offered'),
                        DB::raw('SUM(CASE WHEN master_status_id = 6 THEN 1 ELSE 0 END) AS joined'),
                        DB::raw('SUM(CASE WHEN master_status_id = 7 THEN 1 ELSE 0 END) AS rejected')
                    )
                    ->whereIn('job_id', $jobIds)
                    ->where(function ($q) {
                        // Include own candidates OR third-party candidates not in terminal statuses.
                        $q->where('thirdparty_company_id', '=', 0)
                          ->orWhere(function ($q2) {
                              $q2->where('thirdparty_company_id', '>', 0)
                                 ->whereNotIn('master_status_id', [8, 9]);
                          });
                    })
                    ->groupBy('job_id', 'company_id')
                    ->get();

                foreach ($rows as $row) {
                    try {
                        DB::table('pipeline_counters')->upsert(
                            [[
                                'job_id'        => $row->job_id,
                                'company_id'    => $row->company_id,
                                'all_candidate' => $row->all_candidate,
                                'sourcing'      => $row->sourcing,
                                'screening'     => $row->screening,
                                'interview'     => $row->interview,
                                'negotiation'   => $row->negotiation,
                                'offered'       => $row->offered,
                                'joined'        => $row->joined,
                                'rejected'      => $row->rejected,
                                'computed_at'   => now(),
                                'updated_at'    => now(),
                            ]],
                            ['job_id', 'company_id'],
                            ['all_candidate', 'sourcing', 'screening', 'interview',
                             'negotiation', 'offered', 'joined', 'rejected', 'computed_at', 'updated_at']
                        );

                        // Bust the flexible cache so next loadcounter() reads the fresh DB row.
                        Cache::forget("pipeline:{$row->job_id}");
                        $processed++;
                    } catch (\Throwable $e) {
                        Log::warning("counters:reconcile failed for job {$row->job_id}: " . $e->getMessage());
                        $errors++;
                    }
                }
            });

        $this->info("counters:reconcile done — processed: {$processed}, errors: {$errors}");
        return self::SUCCESS;
    }
}
