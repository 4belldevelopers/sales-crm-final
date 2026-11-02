<?php

namespace App\Observers;

use App\Events\StageMoved;
use App\Helper\Files;
use App\Jobs\DecrementApplicationCountersJob;
use App\Jobs\DecrementPipelineCountersJob;
use App\Jobs\UpdateApplicationCountersJob;
use App\Jobs\UpdatePipelineCountersJob;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Cache;

class JobApplicationObserver
{
    /**
     * Defer observer executions until active database transaction has committed.
     */
    public bool $afterCommit = true;

    public function saving(JobApplication $jobApplication): void
    {
        if (company()) {
            $jobApplication->company_id = company()->id;
        }
    }

    public function updating(JobApplication $jobApplication): void
    {
        $original = $jobApplication->getOriginal();
        if ($jobApplication->isDirty('photo')) {
            Files::deleteFile($original['photo'], 'candidate-photos');
        }
    }

    public function saved(JobApplication $app): void
    {
        // Bust the ES counter cache — key format must match GeneralMethod::custome_count_all().
        Cache::forget("custome_count_all:{$app->company_id}:{$app->job_id}");

        UpdatePipelineCountersJob::dispatch($app->id)->onQueue('counters')->afterCommit();
        UpdateApplicationCountersJob::dispatch($app->id)->onQueue('counters')->afterCommit();

        // Sync candidate state to Elasticsearch read model post-commit
        if (class_exists(\App\Jobs\SyncCandidateToElasticJob::class)) {
            \App\Jobs\SyncCandidateToElasticJob::dispatch($app->id)->afterCommit();
        }

        if ($app->company_id) {
            \App\Support\Cache\TenantCache::invalidateTenant($app->company_id);
        }

        // Emit Agency 360 Activity Log if candidate belongs to an agency partner
        $agencyId = (int) ($app->thirdparty_company_id ?? 0);
        if ($agencyId > 0 && $app->job_id) {
            try {
                $logger = app(\App\Services\AgencyActivityLoggerService::class);
                $jobTitle = $app->job ? $app->job->title : "Job #{$app->job_id}";
                $actorId = (int) ($app->submitted_by ?? (auth()->id() ?? 0));
                $actorName = auth()->check() ? auth()->user()->name : 'Agency Recruiter';

                if ($app->wasRecentlyCreated) {
                    $logger->logCandidateSubmitted(
                        $agencyId,
                        (int) $app->company_id,
                        (int) $app->id,
                        (string) ($app->full_name ?? "Candidate #{$app->id}"),
                        (int) $app->job_id,
                        $jobTitle,
                        $actorId,
                        $actorName
                    );
                } elseif ($app->wasChanged('master_status_id')) {
                    $newStatus = (string) ($app->master_status_id ?? 'Stage');
                    $logger->logEvent([
                        'agency_company_id' => $agencyId,
                        'service_company_id' => (int) $app->company_id,
                        'category' => 'CANDIDATES',
                        'event_type' => 'candidate.stage_changed',
                        'event_title' => "Stage Moved: {$app->full_name}",
                        'event_description' => "Candidate moved to stage {$newStatus} for {$jobTitle}.",
                        'actor_id' => (int) (auth()->id() ?? 0),
                        'actor_name' => auth()->check() ? auth()->user()->name : 'System',
                        'actor_role' => 'recruiter',
                        'source' => 'INTERNAL_ATS',
                        'entity_type' => 'Candidate',
                        'entity_id' => (int) $app->id,
                        'related_job_id' => (int) $app->job_id,
                        'related_job_title' => $jobTitle,
                        'related_candidate_id' => (int) $app->id,
                        'related_candidate_name' => (string) ($app->full_name ?? "Candidate #{$app->id}"),
                        'visibility_scope' => 'PUBLIC',
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('JobApplicationObserver: Failed to log agency activity', ['error' => $e->getMessage()]);
            }
        }

        // Broadcast stage move over Reverb so all open Apptracker tabs refresh
        // their counter badges in real time without a page reload.
        if ($app->wasChanged('master_status_id') && $app->job_id && $app->master_status_id) {
            // Also bust the Redis flexible cache so the next loadcounter() call
            // reads fresh data instead of serving the stale 30-120s window.
            Cache::forget("pipeline:{$app->job_id}");
            broadcast(new StageMoved(
                (int) $app->job_id,
                (int) $app->id,
                (int) $app->master_status_id,
                (int) $app->company_id,
            ))->toOthers();
        }
    }

    public function deleted(JobApplication $app): void
    {
        DecrementPipelineCountersJob::dispatch($app->id)->onQueue('counters');
        DecrementApplicationCountersJob::dispatch($app->id)->onQueue('counters');

        /* Intent: Re-open capacity counter when application is deleted/purged */
        if ($app->job_id) {
            try {
                app(\App\Services\JobApplicationCapacityService::class)->releaseSlot((int) $app->job_id);
            } catch (\Throwable) {
            }
        }
    }
}
