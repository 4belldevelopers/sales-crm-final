<?php

namespace App\Console\Commands;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine, Phase 4 (historical backfill) — docs/OWNERSHIP_ENGINE_DESIGN.md "Phase 4:
 * Historical Backfill". Not reachable from any controller/route/UI/API — the only way to run
 * this is from the command line, satisfying "do not change any controllers, UI, APIs, or
 * business workflows" (Phase 4 requirement). Purely additive: only ever writes to
 * ownership_events, ownership_clusters, and the three cache columns on
 * add_job_wise_condidates — the same three objects Phase 2/3 already write to live. Never
 * touches any legacy table.
 *
 * Idempotent and safe to re-run: OwnershipEngine::backfillCandidate() skips any row that
 * already has an event (from a previous backfill run or from Phase 3's live wiring), so
 * running this command twice, or running it after Phase 3 has been live for a while, never
 * double-processes anything.
 */
class BackfillOwnershipEngine extends Command
{
    protected $signature = 'ownership-engine:backfill
                           {--company= : Only backfill candidates scoped to this company id}
                           {--chunk=500 : Rows examined per chunk}
                           {--dry-run : Report how many rows would be processed, without writing anything}';

    protected $description = 'Phase 4: synthesize historical Ownership Engine events for candidate rows that predate Phase 3 (shadow mode, additive-only)';

    public function handle(OwnershipEngine $engine): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $dryRun = (bool) $this->option('dry-run');

        $query = DB::table('add_job_wise_condidates as ajwc')
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('ownership_events')
                    ->whereColumn('ownership_events.subject_row_id', 'ajwc.id');
            })
            ->orderBy('ajwc.id');

        if ($companyId !== null) {
            $query->where('ajwc.company_id', $companyId);
        }

        $totalCandidates = (clone $query)->count();

        if ($dryRun) {
            $this->info("Dry run: {$totalCandidates} row(s) currently have no ownership_events and would be examined.");
            $this->line('No writes performed.');

            return self::SUCCESS;
        }

        $this->info("Backfilling up to {$totalCandidates} row(s) (chunk size {$chunkSize})...");
        $progressBar = $this->output->createProgressBar($totalCandidates);

        $rowsExamined = 0;
        $rowsProcessed = 0; // rows that actually got a new event this run
        $rowsSkippedAlreadyCovered = 0; // rows another row's cluster processing already covered this run
        $rowsFailed = 0;

        $query->select('ajwc.id')->chunk($chunkSize, function ($rows) use (
            $engine, &$rowsExamined, &$rowsProcessed, &$rowsSkippedAlreadyCovered, &$rowsFailed, $progressBar
        ) {
            foreach ($rows as $row) {
                $rowsExamined++;
                $progressBar->advance();

                // Another row in this same chunk (or an earlier chunk) may already have
                // covered this row's whole cluster — cheap re-check before doing real work.
                $alreadyCovered = DB::table('ownership_events')->where('subject_row_id', $row->id)->exists();
                if ($alreadyCovered) {
                    $rowsSkippedAlreadyCovered++;

                    continue;
                }

                try {
                    $results = $engine->backfillCandidate((int) $row->id);
                    $rowsProcessed += count($results);
                } catch (\Throwable $e) {
                    $rowsFailed++;
                    \Illuminate\Support\Facades\Log::warning('OwnershipEngine::backfillCandidate failed (Phase 4 backfill, non-blocking)', [
                        'row_id' => $row->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });

        $progressBar->finish();
        $this->line('');
        $this->info('Backfill complete.');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rows examined', $rowsExamined],
                ['Rows/clusters given a new event this run', $rowsProcessed],
                ['Rows already covered (skipped)', $rowsSkippedAlreadyCovered],
                ['Rows failed (logged, non-blocking)', $rowsFailed],
            ]
        );

        if ($rowsFailed > 0) {
            $this->warn("{$rowsFailed} row(s) failed — see storage/logs for 'OwnershipEngine::backfillCandidate failed' entries. Safe to re-run this command; failed rows will be retried.");
        }

        return self::SUCCESS;
    }
}
