<?php
$servicePath = __DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php';
$serviceCode = file_get_contents($servicePath);

// 1. In resolveClientJobIds: require final_status = 1 (active shared jobs only)
$target1 = <<<'EOD'
                    if ($viewerType === 'agency') {
                        $dirFilter = [
                            ['term' => ['recommendation_relation.from_company_id' => $companyId]],
                            ['terms' => ['recommendation_relation.final_status' => [1, 2]]],
                        ];
                        if ($targetPartnerId > 0) {
                            $dirFilter[] = ['term' => ['recommendation_relation.to_company_id' => $targetPartnerId]];
                        }
                    } else {
                        $dirFilter = [
                            ['term' => ['recommendation_relation.to_company_id' => $companyId]],
                            ['terms' => ['recommendation_relation.final_status' => [1, 2]]],
                        ];
                        if ($targetPartnerId > 0) {
                            $dirFilter[] = ['term' => ['recommendation_relation.from_company_id' => $targetPartnerId]];
                        }
                    }
EOD;

$replace1 = <<<'EOD'
                    if ($viewerType === 'agency') {
                        $dirFilter = [
                            ['term' => ['recommendation_relation.from_company_id' => $companyId]],
                            ['term' => ['recommendation_relation.final_status' => 1]],
                        ];
                        if ($targetPartnerId > 0) {
                            $dirFilter[] = ['term' => ['recommendation_relation.to_company_id' => $targetPartnerId]];
                        }
                    } else {
                        $dirFilter = [
                            ['term' => ['recommendation_relation.to_company_id' => $companyId]],
                            ['term' => ['recommendation_relation.final_status' => 1]],
                        ];
                        if ($targetPartnerId > 0) {
                            $dirFilter[] = ['term' => ['recommendation_relation.from_company_id' => $targetPartnerId]];
                        }
                    }
EOD;

if (strpos($serviceCode, $target1) !== false) {
    $serviceCode = str_replace($target1, $replace1, $serviceCode);
    echo "Target 1 replaced\n";
}

// 2. In resolveAgencyCandidateAppIds: use authoritative candidate index
$target2 = <<<'EOD'
        $candIds = [];
        $indices = 'ats_candidates_elastic,local_candidates_elastic';

        try {
            if ($this->elasticService->indexExists('ats_candidates_elastic') || $this->elasticService->indexExists('local_candidates_elastic')) {
EOD;

$replace2 = <<<'EOD'
        $candIds = [];
        $candIndex = defined('ELASTIC_CANDIDATES') ? ELASTIC_CANDIDATES : config('constants.ELASTIC_CANDIDATES', 'ats_candidates_elastic');
        $indices = $this->elasticService->indexExists($candIndex) ? $candIndex : ($this->elasticService->indexExists('ats_candidates_elastic') ? 'ats_candidates_elastic' : 'local_candidates_elastic');

        try {
            if ($this->elasticService->indexExists($indices)) {
EOD;

if (strpos($serviceCode, $target2) !== false) {
    $serviceCode = str_replace($target2, $replace2, $serviceCode);
    echo "Target 2 replaced\n";
}

// 3. In resolveKpiTotals: use authoritative timeline and interview indices (no comma-separated mixing)
$target3 = <<<'EOD'
        // Read counts across indices
        $timelineExisting = [];
        foreach (['ats_candidate_timeline', 'local_candidate_timeline'] as $candidateTimelineIndex) {
            if ($this->elasticService->indexExists($candidateTimelineIndex)) {
                $timelineExisting[] = $candidateTimelineIndex;
            }
        }
        $indexTimeline = ! empty($timelineExisting) ? implode(',', $timelineExisting) : 'ats_candidate_timeline';
        $hasTimeline = ! empty($timelineExisting);
        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'local_interview_tracker_elastic');
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'local_applicant_notes');
EOD;

$replace3 = <<<'EOD'
        // Authoritative Timeline Index (prefer ats_candidate_timeline, never mix with stale local_candidate_timeline)
        $indexTimeline = $this->elasticService->indexExists('ats_candidate_timeline')
            ? 'ats_candidate_timeline'
            : ($this->elasticService->indexExists('local_candidate_timeline') ? 'local_candidate_timeline' : 'ats_candidate_timeline');
        $hasTimeline = $this->elasticService->indexExists($indexTimeline);

        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'ats_interview_tracker_elastic');
        if (! $this->elasticService->indexExists($indexInterviews) && $this->elasticService->indexExists('local_interview_tracker_elastic')) {
            $indexInterviews = 'local_interview_tracker_elastic';
        }

        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        if (! $this->elasticService->indexExists($indexJobs) && $this->elasticService->indexExists('local_jobs_elastic')) {
            $indexJobs = 'local_jobs_elastic';
        }

        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'ats_applicant_notes');
EOD;

if (strpos($serviceCode, $target3) !== false) {
    $serviceCode = str_replace($target3, $replace3, $serviceCode);
    echo "Target 3 replaced\n";
}

// 4. In fetchMultiIndexEvents: authoritative timeline and interview indices
$target4 = <<<'EOD'
        $timelineExisting = [];
        foreach (['ats_candidate_timeline', 'local_candidate_timeline'] as $candidateTimelineIndex) {
            if ($this->elasticService->indexExists($candidateTimelineIndex)) {
                $timelineExisting[] = $candidateTimelineIndex;
            }
        }
        $indexTimeline = ! empty($timelineExisting) ? implode(',', $timelineExisting) : 'ats_candidate_timeline';
        $hasTimeline = ! empty($timelineExisting);
        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'local_interview_tracker_elastic');
        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'local_applicant_notes');
EOD;

$replace4 = <<<'EOD'
        // Authoritative Timeline Index (prefer ats_candidate_timeline, never mix with stale local_candidate_timeline)
        $indexTimeline = $this->elasticService->indexExists('ats_candidate_timeline')
            ? 'ats_candidate_timeline'
            : ($this->elasticService->indexExists('local_candidate_timeline') ? 'local_candidate_timeline' : 'ats_candidate_timeline');
        $hasTimeline = $this->elasticService->indexExists($indexTimeline);

        $indexInterviews = defined('ELASTIC_INERTVIEW_TRACKER') ? ELASTIC_INERTVIEW_TRACKER : config('constants.ELASTIC_INERTVIEW_TRACKER', 'ats_interview_tracker_elastic');
        if (! $this->elasticService->indexExists($indexInterviews) && $this->elasticService->indexExists('local_interview_tracker_elastic')) {
            $indexInterviews = 'local_interview_tracker_elastic';
        }

        $indexJobs = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
        if (! $this->elasticService->indexExists($indexJobs) && $this->elasticService->indexExists('local_jobs_elastic')) {
            $indexJobs = 'local_jobs_elastic';
        }

        $indexNotes = defined('ELASTIC_APPLICANT_NOTES') ? ELASTIC_APPLICANT_NOTES : config('constants.ELASTIC_APPLICANT_NOTES', 'ats_applicant_notes');
EOD;

if (strpos($serviceCode, $target4) !== false) {
    $serviceCode = str_replace($target4, $replace4, $serviceCode);
    echo "Target 4 replaced\n";
} else {
    die("Target 4 not found\n");
}

// 5. In resolveCandidateProfiles: authoritative candidate index
$target5 = <<<'EOD'
                $indices = 'ats_candidates_elastic,local_candidates_elastic';
                $params = [
                    'index' => $indices,
                    'size' => max((count($unresolvedIds) + count($unresolvedEmails)) * 2, 20),
EOD;

$replace5 = <<<'EOD'
                $candIndex = defined('ELASTIC_CANDIDATES') ? ELASTIC_CANDIDATES : config('constants.ELASTIC_CANDIDATES', 'ats_candidates_elastic');
                $indices = $this->elasticService->indexExists($candIndex) ? $candIndex : ($this->elasticService->indexExists('ats_candidates_elastic') ? 'ats_candidates_elastic' : 'local_candidates_elastic');
                $params = [
                    'index' => $indices,
                    'size' => max((count($unresolvedIds) + count($unresolvedEmails)) * 2, 20),
EOD;

if (strpos($serviceCode, $target5) !== false) {
    $serviceCode = str_replace($target5, $replace5, $serviceCode);
    echo "Target 5 replaced\n";
} else {
    die("Target 5 not found\n");
}

// 6. In resolveAgencyCaptain: grab to_user_id (the Agency Captain), not from_user_id (the Direct Client user)
$target6 = <<<'EOD'
        if ($jobProfile && ! empty($jobProfile['recommendation_relation']) && is_array($jobProfile['recommendation_relation'])) {
            foreach ($jobProfile['recommendation_relation'] as $rec) {
                $toComp = (int) ($rec['to_company_id'] ?? 0);
                if ($toComp === $agencyCompanyId && in_array((int) ($rec['final_status'] ?? 0), [1, 2], true)) {
                    $repUserId = (int) ($rec['from_user_id'] ?? 0);
                    break;
                }
            }
        }
EOD;

$replace6 = <<<'EOD'
        if ($jobProfile && ! empty($jobProfile['recommendation_relation']) && is_array($jobProfile['recommendation_relation'])) {
            foreach ($jobProfile['recommendation_relation'] as $rec) {
                $toComp = (int) ($rec['to_company_id'] ?? 0);
                if ($toComp === $agencyCompanyId && (int) ($rec['final_status'] ?? 0) === 1) {
                    $repUserId = (int) ($rec['to_user_id'] ?? 0);
                    break;
                }
            }
        }
EOD;

if (strpos($serviceCode, $target6) !== false) {
    $serviceCode = str_replace($target6, $replace6, $serviceCode);
    echo "Target 6 replaced\n";
} else {
    die("Target 6 not found\n");
}

// 7. In resolveJobProfiles: use authoritative jobs index
$target7 = <<<'EOD'
                $cleanJids = array_values(array_unique(array_filter($unresolved)));
                $indices = 'ats_jobs_elastic,local_jobs_elastic';
                $params = [
                    'index' => $indices,
                    'size' => max(count($cleanJids), 10),
EOD;

$replace7 = <<<'EOD'
                $cleanJids = array_values(array_unique(array_filter($unresolved)));
                $jobIndex = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : config('constants.ELASTIC_JOBS', 'ats_jobs_elastic');
                $indices = $this->elasticService->indexExists($jobIndex) ? $jobIndex : ($this->elasticService->indexExists('ats_jobs_elastic') ? 'ats_jobs_elastic' : 'local_jobs_elastic');
                $params = [
                    'index' => $indices,
                    'size' => max(count($cleanJids), 10),
EOD;

if (strpos($serviceCode, $target7) !== false) {
    $serviceCode = str_replace($target7, $replace7, $serviceCode);
    echo "Target 7 replaced\n";
} else {
    die("Target 7 not found\n");
}

// 8. In normalizeAndSortEvents:
// Fix Job Company Context & Privacy based on true viewer role:
// When viewing Client Drawer (viewerType === 'client'), viewer is the Agency -> internal client MUST BE MASKED.
// When viewing Agency Drawer (viewerType === 'agency'), viewer is Direct Client -> internal client is VISIBLE.
$target8 = <<<'EOD'
                if ($isAgencyViewer) {
                    $displayJobCompanyName = $jobProfile['direct_client_company'] ?? ($jobProfile['creator_company'] ?? 'Direct Client');
                    $displayJobCompanyLogo = $jobProfile['direct_client_logo'] ?? null;
                    $isInternalClientContext = false;
                } else {
                    if (! empty($jobProfile['has_internal_client']) && ! empty($jobProfile['internal_client_name'])) {
                        $displayJobCompanyName = $jobProfile['internal_client_name'];
                        $displayJobCompanyLogo = $jobProfile['internal_client_logo'] ?: ($jobProfile['direct_client_logo'] ?? null);
                        $isInternalClientContext = true;
                    } else {
                        $displayJobCompanyName = $jobProfile['direct_client_company'] ?? ($jobProfile['creator_company'] ?? 'Direct Client');
                        $displayJobCompanyLogo = $jobProfile['direct_client_logo'] ?? null;
                        $isInternalClientContext = false;
                    }
                }
EOD;

$replace8 = <<<'EOD'
                if ($query->viewerType === 'client') {
                    // Agency Viewer in Client Drawer: ALWAYS mask internal client, show external Direct Client identity
                    $displayJobCompanyName = $jobProfile['direct_client_company'] ?? ($jobProfile['creator_company'] ?? 'Direct Client');
                    $displayJobCompanyLogo = $jobProfile['direct_client_logo'] ?? null;
                    $isInternalClientContext = false;
                } else {
                    // Direct Client Viewer in Agency Drawer: Full internal client context allowed
                    if (! empty($jobProfile['has_internal_client']) && ! empty($jobProfile['internal_client_name'])) {
                        $displayJobCompanyName = $jobProfile['internal_client_name'];
                        $displayJobCompanyLogo = $jobProfile['internal_client_logo'] ?: ($jobProfile['direct_client_logo'] ?? null);
                        $isInternalClientContext = true;
                    } else {
                        $displayJobCompanyName = $jobProfile['direct_client_company'] ?? ($jobProfile['creator_company'] ?? 'Direct Client');
                        $displayJobCompanyLogo = $jobProfile['direct_client_logo'] ?? null;
                        $isInternalClientContext = false;
                    }
                }
EOD;

if (strpos($serviceCode, $target8) !== false) {
    $serviceCode = str_replace($target8, $replace8, $serviceCode);
    echo "Target 8 replaced\n";
} else {
    die("Target 8 not found\n");
}

// 9. In candidate submission attribution: prefer actual submitter from event if available, otherwise resolved captain
$target9 = <<<'EOD'
                if ($isSubmission) {
                    // Agency Candidate Submission: Display the Assigned Agency Captain on that job
                    $agencyCaptain = $this->resolveAgencyCaptain($jobId, $targetAgencyId, $jobProfile);
                    $rawCapName = (! empty($agencyCaptain['name']) && $agencyCaptain['name'] !== 'Agency Captain')
                        ? $agencyCaptain['name']
                        : ($uploaderName ?: 'Agency Representative');

                    // Requirement 2: Remove "Agency Captain" text everywhere. Display only the actual Captain's name.
                    $cleanCapName = preg_replace('/^agency\s+captain\s*:?\s*/i', '', trim($rawCapName));
                    $displayActorName = ucwords($cleanCapName);
                    $actorRole = 'Agency Captain';
                    $actorCompany = $agencyCompanyName;
                    $attributionLabel = 'Submitted by';
                    $isClientActor = false;
EOD;

$replace9 = <<<'EOD'
                if ($isSubmission) {
                    // Agency Candidate Submission: Display the Assigned Agency Captain or Actual Submitter from agency
                    $agencyCaptain = $this->resolveAgencyCaptain($jobId, $targetAgencyId, $jobProfile);
                    $rawCapName = (! empty($agencyCaptain['name']) && $agencyCaptain['name'] !== 'Agency Captain')
                        ? $agencyCaptain['name']
                        : ($uploaderName ?: (! empty($performerRawName) ? $performerRawName : 'Agency Representative'));

                    // Prefer actual submitter name if event was performed by agency staff
                    if (! empty($performerRawName) && (! empty($src['cur_companyname']) && strtolower((string) $src['cur_companyname']) === strtolower($agencyCompanyName))) {
                        $rawCapName = $performerRawName;
                    }

                    // Remove "Agency Captain" label text everywhere. Display only the actual Captain's name.
                    $cleanCapName = preg_replace('/^agency\s+(captain|representative)\s*:?\s*/i', '', trim($rawCapName));
                    $displayActorName = ucwords($cleanCapName);
                    $actorRole = 'Agency Representative';
                    $actorCompany = $agencyCompanyName;
                    $attributionLabel = 'Submitted by';
                    $isClientActor = false;
EOD;

if (strpos($serviceCode, $target9) !== false) {
    $serviceCode = str_replace($target9, $replace9, $serviceCode);
    echo "Target 9 replaced\n";
} else {
    die("Target 9 not found\n");
}

// 10. Fix Job Shared Badge: Never "Shared with Your Agency" in Agency Drawer
$target10 = <<<'EOD'
                $sharedWithAgency = $isAgencyViewer ? 'Your Agency' : ($jobProfile['agency_name'] ?? $this->resolveTenantCompanyName($query->serviceCompanyId));
                $actorRole = 'Direct Client Representative';
                $attributionLabel = $isDirectShared ? 'Shared by' : 'Created by';
                $transitionText = $isDirectShared ? ('Shared with '.$sharedWithAgency) : ($statusLabel.' Requisition');
EOD;

$replace10 = <<<'EOD'
                if ($query->viewerType === 'agency') {
                    // Agency Drawer: Viewer is Direct Client, Target is Agency. Badge: "Shared with [Target Agency Name]"
                    $targetPartnerName = $jobProfile['agency_name'] ?? $this->resolveTenantCompanyName($query->clientId ?: ($query->partnerCompanyId ?? 0));
                    $sharedWithAgency = ! empty($targetPartnerName) ? $targetPartnerName : 'Agency Partner';
                } else {
                    // Client Drawer: Viewer is Agency, Target is Direct Client. Badge: "Shared with [Viewer Agency Name]"
                    $operatingAgencyName = $this->resolveTenantCompanyName($query->serviceCompanyId ?: $query->tenantId);
                    $sharedWithAgency = ! empty($operatingAgencyName) ? $operatingAgencyName : '4Bell Technology';
                }
                $actorRole = 'Direct Client Representative';
                $attributionLabel = $isDirectShared ? 'Shared by' : 'Created by';
                $transitionText = $isDirectShared ? ('Shared with '.$sharedWithAgency) : ($statusLabel.' Requisition');
EOD;

if (strpos($serviceCode, $target10) !== false) {
    $serviceCode = str_replace($target10, $replace10, $serviceCode);
    echo "Target 10 replaced\n";
} else {
    die("Target 10 not found\n");
}

file_put_contents($servicePath, $serviceCode);
echo "Successfully updated ClientActivityReadService.php\n";

// Now update ServiceCompanyConsultancyController.php lines 1315 and 1338
$ctrlPath = __DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php';
$ctrlCode = file_get_contents($ctrlPath);

$targetCtrl = <<<'EOD'
        $sharedJobIds = DB::table('recommendation_relation')
            ->where('from_company_id', $company_id)
            ->where('to_company_id', $partnerCompanyId)
            ->whereIn('final_status', [1, 2])
            ->pluck('job_id')
            ->unique()
            ->toArray();

        $this->sharedJobs = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->leftJoin('job_locations', 'job_locations.id', '=', 'jobs.location_id')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.status',
                'jobs.total_positions',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.min_exp',
                'jobs.max_exp',
                'jobs.created_at',
                'job_locations.location as city_name',
                'recommendation_relation.created_at as shared_at'
            )
            ->where('recommendation_relation.from_company_id', '=', $company_id)
            ->where('recommendation_relation.to_company_id', '=', $partnerCompanyId)
            ->whereIn('recommendation_relation.final_status', [1, 2])
            ->orderBy('recommendation_relation.id', 'desc')
            ->limit(50)
            ->get();
EOD;

$replaceCtrl = <<<'EOD'
        $sharedJobIds = DB::table('recommendation_relation')
            ->where('from_company_id', $company_id)
            ->where('to_company_id', $partnerCompanyId)
            ->where('final_status', 1)
            ->pluck('job_id')
            ->unique()
            ->toArray();

        $this->sharedJobs = DB::table('recommendation_relation')
            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
            ->leftJoin('job_locations', 'job_locations.id', '=', 'jobs.location_id')
            ->select(
                'jobs.id',
                'jobs.title',
                'jobs.status',
                'jobs.total_positions',
                'jobs.min_salary',
                'jobs.max_salary',
                'jobs.min_exp',
                'jobs.max_exp',
                'jobs.created_at',
                'job_locations.location as city_name',
                'recommendation_relation.created_at as shared_at'
            )
            ->where('recommendation_relation.from_company_id', '=', $company_id)
            ->where('recommendation_relation.to_company_id', '=', $partnerCompanyId)
            ->where('recommendation_relation.final_status', 1)
            ->orderBy('recommendation_relation.id', 'desc')
            ->limit(50)
            ->get();
EOD;

if (strpos($ctrlCode, $targetCtrl) !== false) {
    $ctrlCode = str_replace($targetCtrl, $replaceCtrl, $ctrlCode);
    file_put_contents($ctrlPath, $ctrlCode);
    echo "Successfully updated ServiceCompanyConsultancyController.php\n";
} else {
    echo "Target Ctrl already updated or not found\n";
}
