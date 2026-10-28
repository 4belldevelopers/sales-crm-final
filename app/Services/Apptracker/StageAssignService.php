<?php

namespace App\Services\Apptracker;

use App\Helper\GeneralMethod;
use App\Models\ApplicationStatus;
use App\Models\JobApplication;
use App\Services\ElasticService;
use Illuminate\Support\Facades\DB;

/**
 * StageAssignService — Phase E of the Step-16 Apptracker controller slice.
 *
 * Owns candidate stage assignment logic (~1,069 LoC extracted from
 * ServiceCompanyAppTrackerController: sub_stage, getchild).
 *
 * On every stage move this service:
 *   1. Tenant-checks the application (company_id)
 *   2. Updates job_applications.child_status_id / master_status_id
 *   3. Inserts a timeline entry
 *   4. Syncs the stage to Elasticsearch read index with immediate refresh
 *   5. Invalidates user listing caches
 *   6. Returns fresh per-stage counter data for immediate badge refresh
 *
 * All DB writes include company_id for tenant isolation.
 */
class StageAssignService
{
    public function __construct(
        private readonly CounterService $counterService,
        private readonly ElasticService $elasticService
    ) {}

    /**
     * Move a candidate to a child sub-stage; tenant-scoped.
     *
     * @param  int  $applicationId  job_applications.id
     * @param  int  $childStatusId  candidate_status.id (the sub-stage target)
     * @param  int  $parentStatusId  master_status_id; resolved from DB when 0.
     * @return object|null Fresh per-stage counter row; null when no applications for job.
     */
    public function move(
        int $applicationId,
        int $jobId,
        int $childStatusId,
        int $companyId,
        int $parentStatusId = 0
    ): ?object {
        // Resolve parent stage when not supplied by the caller
        if ($parentStatusId === 0) {
            $parentRow = DB::table('candidate_status')->where('id', $childStatusId)->first();
            $parentStatusId = (int) ($parentRow->parent ?? 0);
        }

        // Tenant ownership check before any mutation
        $appRow = DB::table('job_applications')
            ->select('id', 'candidate_id', 'master_status_id', 'child_status_id')
            ->where('id', $applicationId)
            ->where('company_id', $companyId)
            ->first();

        if (! $appRow) {
            return null;
        }

        $fromStatus = (int) ($appRow->master_status_id ?? 0);
        $candidateId = (int) ($appRow->candidate_id ?? 0);

        DB::table('job_applications')
            ->where('id', $applicationId)
            ->where('job_id', $jobId)
            ->where('company_id', $companyId)
            ->limit(1)
            ->update(['child_status_id' => $childStatusId, 'master_status_id' => $parentStatusId]);

        DB::table('add_job_wise_condidates')
            ->where('job_application_id', $applicationId)
            ->where('job_id', $jobId)
            ->where('company_id', $companyId)
            ->update(['child_status_id' => $childStatusId, 'master_status_id' => $parentStatusId]);

        $this->syncElasticStage($applicationId, $jobId, $companyId, $parentStatusId, $childStatusId);
        $this->counterService->onStageMoved($jobId, $applicationId, $parentStatusId, $companyId);

        // Atomic Client Counter Invalidation & WebSocket Broadcast with Revision
        try {
            $jobRow = DB::table('jobs')->select('client_id')->where('id', $jobId)->first();
            $clientId = (int) ($jobRow->client_id ?? 0);
            if ($clientId > 0) {
                \App\Helper\GeneralMethod::invalidateClientCandidateCounters($companyId, $clientId);
                $revision = (int) cache()->get("tenant_{$companyId}_pipeline_rev", 1);
                broadcast(new \App\Events\PipelineUpdated(
                    $companyId,
                    $clientId,
                    $revision,
                    [
                        'job_id' => $jobId,
                        'application_id' => $applicationId,
                        'candidate_id' => $candidateId,
                        'from_status' => $fromStatus,
                        'to_status' => $parentStatusId,
                        'child_status_id' => $childStatusId,
                    ]
                ));
            }
        } catch (\Throwable $bcEx) {
            \Illuminate\Support\Facades\Log::warning('StageAssignService PipelineUpdated broadcast skipped: '.$bcEx->getMessage());
        }

        // Insert timeline entry for audit trail
        $application = JobApplication::withoutGlobalScopes()->find($applicationId);
        if ($application) {
            $childStatus = DB::table('candidate_status')
                ->where('id', $childStatusId)
                ->where('company_id', $companyId)
                ->first();
            $masterStatus = ApplicationStatus::withoutGlobalScopes()->find($parentStatusId);

            DB::table('timeline')->insert([
                'owner_id' => auth()->id(),
                'application_id' => $application->id,
                'application_email' => $application->email,
                'job_id' => $application->job_id,
                'process_id' => $parentStatusId,
                'status_name' => $masterStatus->name ?? '',
                'process_child_name' => $childStatus->name ?? '',
                'insert_type' => 0,
                'interview_message' => $childStatus->name ?? '',
                'create_at' => now()->format('Y-m-d H:i:s'),
                'update_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }

        // Return fresh counters so the controller can push an immediate badge update
        return DB::table('job_applications')
            ->selectRaw('count(id) as allCandidate')
            ->selectRaw('SUM(CASE WHEN master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing')
            ->selectRaw('SUM(CASE WHEN master_status_id = 2 THEN 1 ELSE 0 END) AS Screening')
            ->selectRaw('SUM(CASE WHEN master_status_id = 3 THEN 1 ELSE 0 END) AS Interview')
            ->selectRaw('SUM(CASE WHEN master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation')
            ->selectRaw('SUM(CASE WHEN master_status_id = 5 THEN 1 ELSE 0 END) AS Offered')
            ->selectRaw('SUM(CASE WHEN master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            ->selectRaw('SUM(CASE WHEN master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected')
            ->where('job_id', $jobId)
            ->where('company_id', $companyId)
            ->groupBy('job_id')
            ->first();
    }

    /**
     * Build the child-stage dropdown HTML and promote master_status if no children exist.
     *
     * Returns array with keys: status, data (HTML), counter, flag, activeAppCounter.
     *
     * @param  int  $statusId  The master stage clicked
     * @param  int  $applicationId  job_applications.id
     */
    public function childStageHtml(
        int $statusId,
        int $applicationId,
        int $jobId,
        int $companyId
    ): array {
        // Verify this application belongs to the current tenant
        $rowData = DB::table('job_applications')
            ->where('id', $applicationId)
            ->where('company_id', $companyId)
            ->first();

        if (! $rowData) {
            return ['status' => 'error', 'message' => 'Application not found.'];
        }

        $candidateArr = DB::table('candidate_status')
            ->where('parent', $statusId)
            ->where('company_id', $companyId)
            ->get();
        $candChildCounter = $candidateArr->count();

        // No children → directly promote to this stage
        if ($applicationId > 0 && $statusId > 0 && $jobId > 0 && $candChildCounter === 0) {
            DB::table('job_applications')
                ->where('id', $applicationId)
                ->where('job_id', $jobId)
                ->where('company_id', $companyId)
                ->limit(1)
                ->update(['master_status_id' => $statusId, 'child_status_id' => null]);

            DB::table('add_job_wise_condidates')
                ->where('job_application_id', $applicationId)
                ->where('job_id', $jobId)
                ->where('company_id', $companyId)
                ->update(['master_status_id' => $statusId, 'child_status_id' => null]);

            $this->syncElasticStage($applicationId, $jobId, $companyId, $statusId, 0);
            $this->counterService->onStageMoved($jobId, $applicationId, $statusId, $companyId);
        }

        // Build child-stage dropdown HTML
        $flag = 0;
        if ($candidateArr->isNotEmpty()) {
            $html = '<select name="childStatus" id="childStatus_'.$applicationId
                .'" onchange="getOpenRejectionResionFrm(this.value,'.$applicationId
                .','.$jobId.','.$statusId.');"><option value="">Select Sub Stage</option>';
            foreach ($candidateArr as $cval) {
                $html .= '<option value="'.$cval->id.'">'.ucfirst($cval->name).'</option>';
                $flag = 1;
            }
            $html .= '</select>';
        } else {
            $html = '&nbsp;-&nbsp;';
        }

        // Timeline entry for audit trail
        if ($statusId > 0) {
            $applicationStatus = ApplicationStatus::withoutGlobalScopes()->find($statusId);
            GeneralMethod::manageTimeline(
                auth()->id(),
                $applicationId,
                $jobId,
                $statusId,
                $applicationStatus->name ?? '',
                'Update process stage',
                '',
                0
            );
        }

        // Fresh counters for immediate badge refresh
        $activeAppCounter = DB::table('job_applications')
            ->selectRaw('count(id) as allCandidate')
            ->selectRaw('SUM(CASE WHEN master_status_id = 1 THEN 1 ELSE 0 END) AS Sourcing')
            ->selectRaw('SUM(CASE WHEN master_status_id = 2 THEN 1 ELSE 0 END) AS Screening')
            ->selectRaw('SUM(CASE WHEN master_status_id = 3 THEN 1 ELSE 0 END) AS Interview')
            ->selectRaw('SUM(CASE WHEN master_status_id = 4 THEN 1 ELSE 0 END) AS Negotiation')
            ->selectRaw('SUM(CASE WHEN master_status_id = 5 THEN 1 ELSE 0 END) AS Offered')
            ->selectRaw('SUM(CASE WHEN master_status_id = 6 THEN 1 ELSE 0 END) AS Joined')
            ->selectRaw('SUM(CASE WHEN master_status_id = 7 THEN 1 ELSE 0 END) AS Rejected')
            ->where('job_id', $jobId)
            ->where('company_id', $companyId)
            ->groupBy('job_id')
            ->first();

        $countArr = DB::table('candidate_status')
            ->where('parent', $statusId)
            ->where('company_id', $companyId)
            ->count();

        return [
            'status' => 'success',
            'data' => $html,
            'counter' => $countArr,
            'flag' => $flag,
            'activeAppCounter' => $activeAppCounter,
        ];
    }

    /**
     * Syncs updated candidate stage to Elasticsearch index immediately and busts listing caches.
     */
    private function syncElasticStage(int $applicationId, int $jobId, int $companyId, int $masterStatusId, int $childStatusId = 0): void
    {
        try {
            $ajwcRow = DB::table('add_job_wise_condidates')
                ->where('job_application_id', $applicationId)
                ->where('job_id', $jobId)
                ->where('company_id', $companyId)
                ->first();

            if ($ajwcRow) {
                $masterStatusName = DB::table('master_status')->where('id', $masterStatusId)->first();
                $exists = $this->elasticService->getClient()->exists([
                    'index' => ELASTIC_CANDIDATES,
                    'id' => $ajwcRow->id,
                ]);
                if ($exists) {
                    $this->elasticService->getClient()->update([
                        'index' => ELASTIC_CANDIDATES,
                        'id' => $ajwcRow->id,
                        'body' => [
                            'doc' => [
                                'job_master_status_id' => (int) $masterStatusId,
                                'childStatus' => (int) $childStatusId,
                                'name' => $masterStatusName->name ?? '',
                                'css_style' => $masterStatusName->css_style ?? '',
                                'updated_at' => now()->toIso8601String(),
                            ],
                        ],
                        'refresh' => true,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('StageAssignService ES sync failed: '.$e->getMessage());
        }

        if (auth()->check()) {
            GeneralMethod::cacheClean('allapptracker_version_user_'.auth()->id());
            GeneralMethod::cacheClean('alljobslist_version_user_'.auth()->id());
            GeneralMethod::cacheClean('allcvtracker_version_user_'.auth()->id());
            GeneralMethod::cacheClean('allclientapp_version_user_'.auth()->id());
        }
    }
}
