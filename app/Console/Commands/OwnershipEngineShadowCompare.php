<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8, Shadow Comparison Engine — docs/OWNERSHIP_ENGINE_DESIGN.md
 * "Shadow comparison". Persists a row per comparison into ownership_engine_shadow_comparisons,
 * categorized descriptively (never a bare "mismatch" boolean — Finding 5: the legacy
 * `duplicate` flags and the engine's `ownership_state` answer different questions). Read-only
 * with respect to every legacy/engine table it reads from; only ever writes to the new
 * shadow-comparisons table.
 */
class OwnershipEngineShadowCompare extends Command
{
    protected $signature = 'ownership-engine:shadow-compare
                           {--company= : Compare rows scoped to this company id}
                           {--job= : Compare rows scoped to this job id}
                           {--chunk=500 : Rows per chunk}';

    protected $description = 'Phase 8: record legacy-vs-engine comparisons for structured reporting (never affects legacy behavior)';

    public function handle(): int
    {
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $jobId = $this->option('job') ? (int) $this->option('job') : null;
        $chunkSize = max(1, (int) $this->option('chunk'));

        if (!$companyId && !$jobId) {
            $this->error('Provide --company=<id> or --job=<id>.');

            return self::FAILURE;
        }

        $query = DB::table('add_job_wise_condidates as ajwc')
            ->join('job_applications as ja', 'ja.id', '=', 'ajwc.job_application_id')
            ->select(
                'ajwc.id', 'ajwc.job_id', 'ajwc.company_id',
                'ajwc.duplicate as legacy_wise_duplicate', 'ajwc.ownership_state', 'ajwc.ownership_cluster_id',
                'ja.duplicate as legacy_profile_duplicate',
            );

        if ($jobId) {
            $query->where('ajwc.job_id', $jobId);
        } else {
            $query->where('ajwc.company_id', $companyId);
        }

        $total = (clone $query)->count();
        $this->info("Comparing {$total} row(s)...");
        $progressBar = $this->output->createProgressBar($total);

        $now = now();
        $recorded = 0;

        $query->orderBy('ajwc.id')->chunk($chunkSize, function ($rows) use (&$recorded, $now, $progressBar) {
            $ownerLookup = DB::table('ownership_clusters')
                ->whereIn('id', $rows->pluck('ownership_cluster_id')->filter()->unique())
                ->select('id', 'owner_row_id', 'decision_rule')
                ->get()
                ->keyBy('id');

            $inserts = [];
            foreach ($rows as $row) {
                $cluster = $row->ownership_cluster_id ? ($ownerLookup[$row->ownership_cluster_id] ?? null) : null;

                $legacyFlagged = ($row->legacy_wise_duplicate == 1) || ($row->legacy_profile_duplicate == 1);
                $category = $this->categorize($legacyFlagged, $row->ownership_state);

                $inserts[] = [
                    'subject_row_id' => $row->id,
                    'scoping_company_id' => $row->company_id,
                    'job_id' => $row->job_id,
                    'legacy_wise_duplicate' => $row->legacy_wise_duplicate,
                    'legacy_profile_duplicate' => $row->legacy_profile_duplicate,
                    'engine_ownership_state' => $row->ownership_state,
                    'engine_owner_row_id' => $cluster?->owner_row_id,
                    'engine_decision_rule' => $cluster?->decision_rule,
                    'category' => $category,
                    'compared_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $progressBar->advance();
            }

            if (!empty($inserts)) {
                DB::table('ownership_engine_shadow_comparisons')->insert($inserts);
                $recorded += count($inserts);
            }
        });

        $progressBar->finish();
        $this->line('');
        $this->info("Recorded {$recorded} comparison(s).");
        $this->comment('These are observations, not verdicts — see docs/OWNERSHIP_ENGINE_DESIGN.md "Shadow comparison" for why legacy and engine are expected to differ.');

        return self::SUCCESS;
    }

    private function categorize(bool $legacyFlagged, $engineState): string
    {
        if ($engineState === null) {
            return $legacyFlagged ? 'legacy_flagged_engine_undetermined' : 'both_clear';
        }

        if ((int) $engineState === 1) {
            return $legacyFlagged ? 'legacy_flagged_engine_owner' : 'legacy_clear_engine_owner';
        }

        return $legacyFlagged ? 'legacy_flagged_engine_duplicate' : 'legacy_clear_engine_duplicate';
    }
}
