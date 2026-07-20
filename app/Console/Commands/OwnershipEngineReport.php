<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\MasksPii;
use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine, Phase 5 (Observability — revised scope, see
 * docs/OWNERSHIP_ENGINE_DESIGN.md "Phase 5: Observability", Finding 5). Read-only. Writes
 * nothing, ever. Does not compare legacy vs. engine as a pass/fail signal — the two answer
 * different questions (see the design doc) — it surfaces both side by side for a human to
 * read during the "production as observation environment" period.
 */
class OwnershipEngineReport extends Command
{
    use MasksPii;

    protected $signature = 'ownership-engine:report
                           {--company= : Report on this company id (job-owning company)}
                           {--job= : Report on this job id}';

    protected $description = 'Phase 5: read-only report of Ownership Engine state alongside legacy duplicate signals, scoped to one company or one job';

    public function handle(OwnershipEngine $engine): int
    {
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $jobId = $this->option('job') ? (int) $this->option('job') : null;

        if (!$companyId && !$jobId) {
            $this->error('Provide --company=<id> or --job=<id>. An unscoped report is not offered — this is a deliberate, bounded diagnostic, not a full-dataset dump.');

            return self::FAILURE;
        }

        $query = DB::table('add_job_wise_condidates as ajwc')
            ->join('job_applications as ja', 'ja.id', '=', 'ajwc.job_application_id')
            ->select(
                'ajwc.id', 'ajwc.job_id', 'ajwc.company_id', 'ajwc.thirdparty_company_id',
                'ajwc.duplicate as legacy_wise_duplicate', 'ajwc.ownership_state', 'ajwc.ownership_cluster_id',
                'ja.duplicate as legacy_profile_duplicate', 'ja.email', 'ja.phone',
            )
            ->orderBy('ajwc.ownership_cluster_id')
            ->orderBy('ajwc.id');

        if ($jobId) {
            $query->where('ajwc.job_id', $jobId);
        } else {
            $query->where('ajwc.company_id', $companyId);
        }

        $rows = $query->get();

        if ($rows->isEmpty()) {
            $this->info('No add_job_wise_condidates rows found in this scope.');

            return self::SUCCESS;
        }

        $this->info("Ownership Engine report — {$rows->count()} row(s) in scope.");
        $this->line('Legacy signals answer "does any match exist"; engine state answers "who owns this identity". They are not expected to always agree — see docs/OWNERSHIP_ENGINE_DESIGN.md Phase 5.');
        $this->line('');

        $currentCluster = 'none';
        foreach ($rows as $row) {
            $clusterLabel = $row->ownership_cluster_id ?? 'undetermined';
            if ($clusterLabel !== $currentCluster) {
                $this->line('--- Cluster: '.$clusterLabel.' ---');
                $currentCluster = $clusterLabel;
            }

            $isCompanyRow = (int) $row->thirdparty_company_id === 0;
            $engineStateLabel = match ($row->ownership_state) {
                1 => 'OWNER',
                2 => 'DUPLICATE',
                default => 'undetermined/private',
            };

            $this->line(sprintf(
                '  row #%d | job #%d | %s | identity %s / %s | legacy: wise_dup=%s profile_dup=%s | engine: %s',
                $row->id,
                $row->job_id,
                $isCompanyRow ? 'company' : 'agency',
                $this->maskEmail($row->email),
                $this->maskPhone($row->phone),
                $row->legacy_wise_duplicate ?? 'null',
                $row->legacy_profile_duplicate ?? 'null',
                $engineStateLabel,
            ));

            if ($row->ownership_state !== null) {
                $explanation = $engine->explain((int) $row->id);
                $this->line('      engine reason: '.$explanation->reason);
            }
        }

        return self::SUCCESS;
    }
}
