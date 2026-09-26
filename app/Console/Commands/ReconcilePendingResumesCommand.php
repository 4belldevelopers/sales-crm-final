<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOutboxEventJob;
use App\Models\ResumeAsset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Intent: Reconciles pending resume attachments, recovers sessions after browser crashes,
 * and purges abandoned or orphaned resume assets.
 */
class ReconcilePendingResumesCommand extends Command
{
    protected $signature = 'candidates:reconcile-pending-resumes {--limit=100}';
    protected $description = 'Reconcile pending or orphaned resume attachments and process pending outbox events';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Starting resume reconciliation (limit: {$limit})...");

        // 1. Process any pending outbox events
        dispatch(new ProcessOutboxEventJob());
        $this->info("Dispatched ProcessOutboxEventJob.");

        // 2. Reconcile unattached or pending assets
        $pendingAssets = DB::table('resume_assets')
            ->where('status', 'CLEANUP_PENDING')
            ->orWhere(function ($query) {
                $query->whereNull('candidate_id')
                    ->where('created_at', '<', now()->subHours(2));
            })
            ->limit($limit)
            ->get();

        $reconciledCount = 0;
        foreach ($pendingAssets as $asset) {
            // Check if there is an existing candidate with matching upload session
            if (! empty($asset->upload_session_id)) {
                $candidate = DB::table('job_applications')
                    ->where('company_id', $asset->company_id)
                    ->where('client_submission_uuid', $asset->upload_session_id)
                    ->first();

                if ($candidate && empty($candidate->current_resume_id)) {
                    // Tenancy linkage verified: session.company_id == candidate.company_id
                    DB::table('job_applications')
                        ->where('id', $candidate->id)
                        ->where('company_id', $asset->company_id)
                        ->update([
                            'current_resume_id' => $asset->id,
                            'resume' => $asset->object_key,
                            'updated_at' => now(),
                        ]);

                    DB::table('resume_assets')
                        ->where('id', $asset->id)
                        ->update([
                            'candidate_id' => $candidate->id,
                            'status' => 'ACTIVE',
                            'attached_at' => now(),
                        ]);

                    $reconciledCount++;
                    continue;
                }
            }

            // If older than 24 hours without attachment, mark ORPHANED
            if (strtotime($asset->created_at) < (time() - 86400)) {
                DB::table('resume_assets')
                    ->where('id', $asset->id)
                    ->update(['status' => 'ORPHANED']);
            }
        }

        // 3. Purge REMOVAL_PENDING assets past 7 days retention
        dispatch(new \App\Jobs\PurgeRemovedResumeAssetsJob());
        $this->info("Dispatched PurgeRemovedResumeAssetsJob.");

        $this->info("Reconciliation complete. {$reconciledCount} assets attached.");
        return 0;
    }
}
