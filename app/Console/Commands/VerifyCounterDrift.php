<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Compares each counter summary table against live MySQL data.
 * Exits 0 with a success message if drift is zero; exits 1 with a diff table otherwise.
 * Safe to run in production (read-only, no writes).
 */
class VerifyCounterDrift extends Command
{
    protected $signature   = 'counters:verify-drift {--limit=20 : Max drift rows to display per table}';
    protected $description = 'Verify zero drift between counter summary tables and live MySQL data';

    private int $driftTotal = 0;

    public function handle(): int
    {
        $this->verifyJobCounters();
        $this->verifyPipelineCounters();
        $this->verifyApplicationCounters();
        $this->verifyJobAgencyCounter();

        if ($this->driftTotal === 0) {
            $this->info('No counter drift detected.');
            return self::SUCCESS;
        }

        $this->error("Total drift rows found: {$this->driftTotal}. Run php artisan counters:seed-from-live to resync.");
        return self::FAILURE;
    }

    // -------------------------------------------------------------------------
    // job_counters
    // -------------------------------------------------------------------------
    private function verifyJobCounters(): void
    {
        $this->line('Checking job_counters…');
        $limit = (int) $this->option('limit');

        // jobs has no deleted_at (hard-delete only) — count all rows.
        $live = DB::table('jobs')
            ->select('company_id', DB::raw('COUNT(*) as total_jobs'))
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->pluck('total_jobs', 'company_id');

        $cached = DB::table('job_counters')
            ->pluck('total_jobs', 'company_id');

        $drift = [];
        foreach ($live as $companyId => $liveCount) {
            $cachedCount = $cached[$companyId] ?? 0;
            if ((int) $liveCount !== (int) $cachedCount) {
                $drift[] = [
                    'company_id' => $companyId,
                    'live'       => $liveCount,
                    'cached'     => $cachedCount,
                    'diff'       => (int) $liveCount - (int) $cachedCount,
                ];
            }
        }

        $this->reportDrift('job_counters (total_jobs)', $drift, $limit);
    }

    // -------------------------------------------------------------------------
    // pipeline_counters
    // -------------------------------------------------------------------------
    private function verifyPipelineCounters(): void
    {
        $this->line('Checking pipeline_counters…');
        $limit = (int) $this->option('limit');

        $caseExpr = "CASE master_status_id
            WHEN 1 THEN 'sourcing'
            WHEN 2 THEN 'screening'
            WHEN 3 THEN 'evaluation'
            WHEN 4 THEN 'negotiation'
            WHEN 5 THEN 'offered'
            WHEN 6 THEN 'joined'
            WHEN 7 THEN 'rejected'
            WHEN 9 THEN 'rejected'
            ELSE NULL
        END";

        $live = DB::table('job_applications')
            ->select('job_id', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->pluck('total', 'job_id');

        $cached = DB::table('pipeline_counters')
            ->select(DB::raw('job_id, (sourcing + screening + evaluation + negotiation + offered + joined + rejected) as total'))
            ->get()
            ->pluck('total', 'job_id');

        $drift = [];
        foreach ($live as $jobId => $liveCount) {
            $cachedCount = $cached[$jobId] ?? 0;
            if ((int) $liveCount !== (int) $cachedCount) {
                $drift[] = [
                    'job_id' => $jobId,
                    'live'   => $liveCount,
                    'cached' => $cachedCount,
                    'diff'   => (int) $liveCount - (int) $cachedCount,
                ];
            }
        }

        $this->reportDrift('pipeline_counters (total per job)', $drift, $limit);
    }

    // -------------------------------------------------------------------------
    // application_counters
    // -------------------------------------------------------------------------
    private function verifyApplicationCounters(): void
    {
        $this->line('Checking application_counters…');
        $limit = (int) $this->option('limit');

        $live = DB::table('job_applications')
            ->select('job_id', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->pluck('total', 'job_id');

        $cached = DB::table('application_counters')
            ->pluck('total_apps', 'job_id');

        $drift = [];
        foreach ($live as $jobId => $liveCount) {
            $cachedCount = $cached[$jobId] ?? 0;
            if ((int) $liveCount !== (int) $cachedCount) {
                $drift[] = [
                    'job_id' => $jobId,
                    'live'   => $liveCount,
                    'cached' => $cachedCount,
                    'diff'   => (int) $liveCount - (int) $cachedCount,
                ];
            }
        }

        $this->reportDrift('application_counters (total_apps)', $drift, $limit);
    }

    // -------------------------------------------------------------------------
    // job_agency_counter
    // -------------------------------------------------------------------------
    private function verifyJobAgencyCounter(): void
    {
        $this->line('Checking job_agency_counter…');
        $limit = (int) $this->option('limit');

        // Verify rec_sent (final_status = 0) as the primary indicator
        $liveRec = DB::table('recommendation_relation')
            ->select('job_id', DB::raw('SUM(CASE WHEN final_status = 0 THEN 1 ELSE 0 END) as rec_sent'))
            ->whereNotNull('job_id')
            ->groupBy('job_id')
            ->pluck('rec_sent', 'job_id');

        $cachedRec = DB::table('job_agency_counter')
            ->pluck('rec_sent', 'job_id');

        $drift = [];
        foreach ($liveRec as $jobId => $liveCount) {
            $cachedCount = $cachedRec[$jobId] ?? 0;
            if ((int) $liveCount !== (int) $cachedCount) {
                $drift[] = [
                    'job_id' => $jobId,
                    'live'   => $liveCount,
                    'cached' => $cachedCount,
                    'diff'   => (int) $liveCount - (int) $cachedCount,
                ];
            }
        }

        $this->reportDrift('job_agency_counter (rec_sent)', $drift, $limit);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------
    private function reportDrift(string $label, array $drift, int $limit): void
    {
        if (empty($drift)) {
            $this->line("  {$label}: ✓ no drift");
            return;
        }

        $this->driftTotal += count($drift);
        $this->warn("  {$label}: " . count($drift) . ' row(s) drifted' . (count($drift) > $limit ? " (showing first {$limit})" : ''));

        $headers = array_keys($drift[0]);
        $this->table($headers, array_slice($drift, 0, $limit));
    }
}
