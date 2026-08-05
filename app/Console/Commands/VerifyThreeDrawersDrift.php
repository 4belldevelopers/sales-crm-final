<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Detects and optionally repairs Redis/DB counter drift for the V3 Three-Drawer system.
 *
 * Drift categories checked:
 *  (A) v3:status_job:{id}  — stale after Cache::forget() failed post status-update
 *  (B) v3:status_log:{id}  — stale for same reason as A
 *  (C) job_counters table  — hiring/hold/closed counts drifted from live jobs table
 *  (D) Team/agency drawer version keys — informational: missing after Redis flush
 *
 * Exits 0 (SUCCESS) when no hard drift, or all hard drift healed with --heal.
 * Exits 1 (FAILURE) when hard drift remains.
 * Idempotent — safe to run repeatedly.
 *
 * Tenancy note: all DB::table() calls include company_id or are aggregating across
 * all companies deliberately (this is an admin/ops command, not a per-tenant endpoint).
 */
class VerifyThreeDrawersDrift extends Command
{
    protected $signature = 'drawers:verify-drift
                            {--heal   : Attempt automatic repair of detected drift}
                            {--hours=24 : Look-back window in hours for recently updated jobs (A/B checks)}
                            {--limit=50 : Max rows to display per drift category}';

    protected $description = 'Verify and optionally heal Redis/DB counter drift for the V3 three-drawer system';

    private int $driftCount = 0;
    private int $healCount  = 0;

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $heal  = (bool) $this->option('heal');
        $limit = (int) $this->option('limit');

        $this->line("drawers:verify-drift  look-back:{$hours}h  heal:" . ($heal ? 'yes' : 'no'));
        $this->line(str_repeat('─', 60));

        $this->checkV3StatusCaches($hours, $heal, $limit);
        $this->checkJobCounters($heal, $limit);
        $this->checkVersionKeys($heal);

        $this->line(str_repeat('─', 60));

        if ($this->driftCount === 0) {
            $this->info('✓ No hard drift detected across all three drawers.');
            return self::SUCCESS;
        }

        if ($heal) {
            $remaining = $this->driftCount - $this->healCount;
            if ($remaining === 0) {
                $this->info("✓ All {$this->healCount} drift item(s) healed.");
                return self::SUCCESS;
            }
            $this->error("{$remaining} drift item(s) could not be healed — manual investigation required.");
            return self::FAILURE;
        }

        $this->error("{$this->driftCount} drift item(s) found. Run with --heal to attempt repair.");
        return self::FAILURE;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // (A/B) V3 status drawer Redis cache staleness
    // ─────────────────────────────────────────────────────────────────────────

    private function checkV3StatusCaches(int $hours, bool $heal, int $limit): void
    {
        $this->line('(A/B) Checking v3:status_job:{id} cache staleness…');

        $cutoff = now()->subHours($hours);
        $stale  = [];

        // Walk only recently changed jobs — the look-back window bounds this scan.
        // Drift is only possible for jobs touched since the last run, so 24h is generous.
        DB::table('jobs')
            ->select('id', 'status', 'company_id', 'updated_at')
            ->where('updated_at', '>=', $cutoff)
            ->orderBy('id')
            ->chunk(200, function ($batch) use (&$stale, $heal) {
                foreach ($batch as $job) {
                    $cached = Cache::get("v3:status_job:{$job->id}");

                    if ($cached === null) {
                        continue; // no cache entry — no drift possible
                    }

                    if ((string) ($cached->status ?? '') !== (string) $job->status) {
                        $stale[] = [
                            'job_id'        => $job->id,
                            'company_id'    => $job->company_id,
                            'db_status'     => $job->status,
                            'cached_status' => $cached->status ?? '(null)',
                            'updated_at'    => $job->updated_at,
                        ];
                        $this->driftCount++;

                        if ($heal) {
                            try {
                                Cache::forget("v3:status_job:{$job->id}");
                                Cache::forget("v3:status_log:{$job->id}");
                                $this->healCount++;
                            } catch (\Throwable $e) {
                                Log::warning("drawers:verify-drift heal(A/B) failed for job {$job->id}: " . $e->getMessage());
                            }
                        }
                    }
                }
            });

        if (empty($stale)) {
            $this->line("  v3:status_job: ✓ no stale entries in {$hours}h window");
            return;
        }

        $tag = $heal ? ' (healed)' : ' — run with --heal to fix';
        $this->warn('  v3:status_job: ' . count($stale) . ' stale entr(ies)' . $tag);
        $this->table(
            ['job_id', 'company_id', 'db_status', 'cached_status', 'updated_at'],
            array_slice($stale, 0, $limit)
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // (C) Job status summary counters (job_counters table)
    // ─────────────────────────────────────────────────────────────────────────

    private function checkJobCounters(bool $heal, int $limit): void
    {
        $this->line('(C) Checking job_counters vs live jobs table…');

        if (!DB::getSchemaBuilder()->hasTable('job_counters')) {
            $this->line('  job_counters table absent — skipping (run counters:seed-from-live first)');
            return;
        }

        // Compute live aggregates per company — mirrors SeedCountersFromLive::seedJobCounters().
        // status aliases: hiring=active, on_hold=hold, closed=inactive (both old and current naming).
        $live = DB::table('jobs')
            ->select(
                'company_id',
                DB::raw('COUNT(*) as total_jobs'),
                DB::raw("SUM(CASE WHEN status IN ('hiring','active')   THEN 1 ELSE 0 END) as hiring_jobs"),
                DB::raw("SUM(CASE WHEN status IN ('on_hold','hold')    THEN 1 ELSE 0 END) as on_hold_jobs"),
                DB::raw("SUM(CASE WHEN status IN ('closed','inactive') THEN 1 ELSE 0 END) as closed_jobs")
            )
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        $cached = DB::table('job_counters')->get()->keyBy('company_id');

        $drift  = [];
        $cols   = ['total_jobs', 'hiring_jobs', 'on_hold_jobs', 'closed_jobs'];

        foreach ($live as $cid => $liveRow) {
            $row = $cached[$cid] ?? null;
            if (!$row) {
                continue; // not yet seeded — counters:seed-from-live handles initial seeding
            }

            $drifted = false;
            foreach ($cols as $col) {
                if ((int) ($liveRow->$col ?? 0) !== (int) ($row->$col ?? 0)) {
                    $drifted = true;
                    break;
                }
            }

            if (!$drifted) {
                continue;
            }

            $drift[] = [
                'company_id'   => $cid,
                'total live'   => $liveRow->total_jobs,
                'total cached' => $row->total_jobs,
                'hiring live'  => $liveRow->hiring_jobs,
                'hiring cached'=> $row->hiring_jobs ?? 0,
                'hold live'    => $liveRow->on_hold_jobs,
                'hold cached'  => $row->on_hold_jobs ?? 0,
                'closed live'  => $liveRow->closed_jobs,
                'closed cached'=> $row->closed_jobs ?? 0,
            ];
            $this->driftCount++;

            if ($heal) {
                try {
                    DB::table('job_counters')->upsert(
                        [[
                            'company_id'    => $cid,
                            'total_jobs'    => $liveRow->total_jobs,
                            'hiring_jobs'   => $liveRow->hiring_jobs,
                            'on_hold_jobs'  => $liveRow->on_hold_jobs,
                            'closed_jobs'   => $liveRow->closed_jobs,
                            'last_event_at' => now(),
                        ]],
                        ['company_id'],
                        ['total_jobs', 'hiring_jobs', 'on_hold_jobs', 'closed_jobs', 'last_event_at']
                    );
                    $this->healCount++;
                } catch (\Throwable $e) {
                    Log::warning("drawers:verify-drift heal(C) failed for company {$cid}: " . $e->getMessage());
                }
            }
        }

        if (empty($drift)) {
            $this->line('  job_counters: ✓ no drift (checked ' . $live->count() . ' companies)');
            return;
        }

        $tag = $heal ? ' (heal applied)' : ' — run with --heal to fix';
        $this->warn('  job_counters: ' . count($drift) . ' company(ies) drifted' . $tag);
        $this->table(array_keys($drift[0]), array_slice($drift, 0, $limit));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // (D) Team/agency version key presence — informational
    // ─────────────────────────────────────────────────────────────────────────

    private function checkVersionKeys(bool $heal): void
    {
        $this->line('(D) Checking team/agency drawer version keys (informational)…');

        // Team drawer: all users who appear in job_assign should have a version key.
        // Missing key means cacheClean() has never been called for them — not harmful
        // (defaults to 1 consistently), but records the gap for observability.
        $assignedUsers = DB::table('job_assign')
            ->distinct()
            ->pluck('user_id')
            ->filter()
            ->all();

        $missingTeam = [];
        foreach ($assignedUsers as $uid) {
            if (Cache::get('allassignedagency_version_user_' . $uid) === null) {
                $missingTeam[] = $uid;
                if ($heal) {
                    try {
                        Cache::forever('allassignedagency_version_user_' . $uid, 1);
                    } catch (\Throwable $e) {
                        Log::warning("drawers:verify-drift: version key seed failed for user {$uid}: " . $e->getMessage());
                    }
                }
            }
        }

        // Agency drawer: users whose company has agency relations should have a version key.
        $agencyCompanies = DB::table('recommendation_relation')
            ->distinct()
            ->pluck('company_id')
            ->filter()
            ->all();

        $missingAgency = [];
        if (!empty($agencyCompanies)) {
            $agencyUsers = DB::table('users')
                ->whereIn('company_id', $agencyCompanies)
                ->distinct()
                ->pluck('id')
                ->all();

            foreach ($agencyUsers as $uid) {
                if (Cache::get('allregisteredagency_version_user_' . $uid) === null) {
                    $missingAgency[] = $uid;
                    if ($heal) {
                        try {
                            Cache::forever('allregisteredagency_version_user_' . $uid, 1);
                        } catch (\Throwable $e) {
                            Log::warning("drawers:verify-drift: agency version key seed failed for user {$uid}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        $healNote = $heal ? ' (seeded to 1)' : '';

        if (empty($missingTeam)) {
            $this->line('  allassignedagency_version_user_*: ✓ all ' . count($assignedUsers) . ' key(s) present');
        } else {
            $this->line(
                '  allassignedagency_version_user_*: ' . count($missingTeam) . '/' . count($assignedUsers) . ' missing' . $healNote
                . ' (not counted as hard drift — cache TTL provides self-healing)'
            );
        }

        if (empty($missingAgency)) {
            $this->line('  allregisteredagency_version_user_*: ✓ consistent');
        } else {
            $this->line(
                '  allregisteredagency_version_user_*: ' . count($missingAgency) . ' missing' . $healNote
                . ' (informational only)'
            );
        }
    }
}
