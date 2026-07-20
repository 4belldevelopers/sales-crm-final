<?php

namespace App\Console\Commands;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8 — single-row inspector, read-only. Thin wrapper over explain().
 */
class OwnershipEngineCandidate extends Command
{
    protected $signature = 'ownership-engine:candidate {rowId}';

    protected $description = 'Phase 8: inspect one add_job_wise_condidates row\'s ownership state and explanation';

    public function handle(OwnershipEngine $engine): int
    {
        $rowId = (int) $this->argument('rowId');

        $row = DB::table('add_job_wise_condidates')->where('id', $rowId)->first();
        if (!$row) {
            $this->error("Row #{$rowId} does not exist.");

            return self::FAILURE;
        }

        $explanation = $engine->explain($rowId);

        $this->info("Row #{$rowId}");
        $this->table(['Field', 'Value'], [
            ['Job', $row->job_id],
            ['Company (scoping)', $row->company_id],
            ['Type', (int) $row->thirdparty_company_id === 0 ? 'company' : 'agency'],
            ['ownership_state', $explanation->state === null ? '(undetermined)' : $explanation->state],
            ['Owner row', $explanation->ownerRowId ?? '(n/a)'],
            ['Owner type', $explanation->ownerType ?? '(n/a)'],
            ['Decision rule', $explanation->decisionRule ?? '(n/a)'],
            ['Scenario ref', $explanation->scenarioRef ?? '(n/a)'],
            ['Decided at', $explanation->decidedAt ?? '(n/a)'],
        ]);

        $this->line('');
        $this->info('Reason: '.$explanation->reason);

        return self::SUCCESS;
    }
}
