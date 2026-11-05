<?php
$path = __DIR__ . '/../app/Services/ServiceCompany/ClientActivityReadService.php';
$c = file_get_contents($path);

// Apply POS 1 replacement
$target1 = <<<'EOD'
        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $isDirect = ($query->clientType === 'direct');

        // 1. Timeline Counts (Pipeline & Offers)
        if (! empty($clientJobIds) && (! $isDirect || ! empty($agencyCandidateAppIds)) && $hasTimeline) {
            try {
                $baseMust = [['terms' => ['job_id' => $clientJobIds]]];
                if ($isDirect) {
                    $baseMust[] = ['terms' => ['application_id' => $agencyCandidateAppIds]];
                }
EOD;

$replace1 = <<<'EOD'
        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $isRelationship = ($query->clientType === 'direct' || $query->clientType === 'agency' || $query->viewerType === 'agency');

        // 1. Timeline Counts (Pipeline & Offers)
        if (! empty($clientJobIds) && (! $isRelationship || ! empty($agencyCandidateAppIds)) && $hasTimeline) {
            try {
                $baseMust = [['terms' => ['job_id' => $clientJobIds]]];
                if ($isRelationship) {
                    $baseMust[] = ['terms' => ['application_id' => $agencyCandidateAppIds]];
                }
EOD;

if (strpos($c, $target1) === false) {
    die("Target 1 not found\n");
}
$c = str_replace($target1, $replace1, $c);

// Also replace the rest of isDirect in resolveKpiTotals
$target1_part2 = <<<'EOD'
        // 2. Interview Counts
        if ((! $isDirect || (! empty($clientJobIds) && ! empty($agencyCandidateAppIds))) && $this->elasticService->indexExists($indexInterviews)) {
            try {
                if ($isDirect) {
                    $candMatch = [
                        ['terms' => ['candidate_id' => $agencyCandidateAppIds]],
                        ['terms' => ['job_application_id' => $agencyCandidateAppIds]],
                        ['terms' => ['wiseID' => $agencyCandidateAppIds]],
                    ];
                    $must = [
                        ['terms' => ['job_id' => $clientJobIds]],
                        ['bool' => ['should' => $candMatch, 'minimum_should_match' => 1]],
                    ];
                } else {
                    $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                    if (! empty($clientJobIds)) {
                        $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                    }
                    $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
                }
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexInterviews,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['interview_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        // 3. Jobs Counts
        if ($isDirect) {
            $kpis['job_events'] = count($clientJobIds);
        } elseif ($this->elasticService->indexExists($indexJobs)) {
            try {
                $must = [['term' => ['client_id' => $query->clientId]]];
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexJobs,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['job_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        // 4. Notes Counts (Direct client agency view has 0 generic notes)
        if (! $isDirect && $this->elasticService->indexExists($indexNotes)) {
EOD;

$replace1_part2 = <<<'EOD'
        // 2. Interview Counts
        if ((! $isRelationship || (! empty($clientJobIds) && ! empty($agencyCandidateAppIds))) && $this->elasticService->indexExists($indexInterviews)) {
            try {
                if ($isRelationship) {
                    $candMatch = [
                        ['terms' => ['candidate_id' => $agencyCandidateAppIds]],
                        ['terms' => ['job_application_id' => $agencyCandidateAppIds]],
                        ['terms' => ['wiseID' => $agencyCandidateAppIds]],
                    ];
                    $must = [
                        ['terms' => ['job_id' => $clientJobIds]],
                        ['bool' => ['should' => $candMatch, 'minimum_should_match' => 1]],
                    ];
                } else {
                    $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                    if (! empty($clientJobIds)) {
                        $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                    }
                    $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
                }
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexInterviews,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['interview_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        // 3. Jobs Counts
        if ($isRelationship) {
            $kpis['job_events'] = count($clientJobIds);
        } elseif ($this->elasticService->indexExists($indexJobs)) {
            try {
                $must = [['term' => ['client_id' => $query->clientId]]];
                if ($timeRange) {
                    $must[] = ['range' => ['created_at' => $timeRange]];
                }

                $res = $this->elasticService->getClient()->count([
                    'index' => $indexJobs,
                    'body' => ['query' => ['bool' => ['must' => $must]]],
                ]);
                $kpis['job_events'] = (int) ($res['count'] ?? 0);
            } catch (\Throwable) {
            }
        }

        // 4. Notes Counts (Direct client agency view has 0 generic notes)
        if (! $isRelationship && $this->elasticService->indexExists($indexNotes)) {
EOD;

if (strpos($c, $target1_part2) === false) {
    die("Target 1 part 2 not found\n");
}
$c = str_replace($target1_part2, $replace1_part2, $c);

// Now apply POS 2 (fetchMultiIndexEvents)
$target2 = <<<'EOD'
        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $searchQuery = $query->search;
        $isDirect = ($query->clientType === 'direct');

        $msearchPayload = [];
        $indexKeys = [];

        // 1. Timeline Search Query (Pipeline & Offers)
        if (! empty($clientJobIds) && (! $isDirect || ! empty($agencyCandidateAppIds)) && in_array($query->category, ['all', 'pipeline', 'offers', 'candidates'], true) && $hasTimeline) {
            $must = [['terms' => ['job_id' => $clientJobIds]]];
            if ($isDirect) {
                // Strictly candidates submitted by this operating agency for this job
                $must[] = ['terms' => ['application_id' => $agencyCandidateAppIds]];
            }
EOD;

$replace2 = <<<'EOD'
        $timeRange = $this->getTimeRangeFilter($query->timeframe);
        $searchQuery = $query->search;
        $isRelationship = ($query->clientType === 'direct' || $query->clientType === 'agency' || $query->viewerType === 'agency');

        $msearchPayload = [];
        $indexKeys = [];

        // 1. Timeline Search Query (Pipeline & Offers)
        if (! empty($clientJobIds) && (! $isRelationship || ! empty($agencyCandidateAppIds)) && in_array($query->category, ['all', 'pipeline', 'offers', 'candidates'], true) && $hasTimeline) {
            $must = [['terms' => ['job_id' => $clientJobIds]]];
            if ($isRelationship) {
                // Strictly candidates submitted by this operating agency for this job
                $must[] = ['terms' => ['application_id' => $agencyCandidateAppIds]];
            }
EOD;

if (strpos($c, $target2) === false) {
    die("Target 2 not found\n");
}
$c = str_replace($target2, $replace2, $c);

$target2_part2 = <<<'EOD'
        // 2. Interviews Search Query
        if ((! $isDirect || (! empty($clientJobIds) && ! empty($agencyCandidateAppIds))) && in_array($query->category, ['all', 'interviews'], true) && $this->elasticService->indexExists($indexInterviews)) {
            if ($isDirect) {
                $candMatch = [
                    ['terms' => ['candidate_id' => $agencyCandidateAppIds]],
                    ['terms' => ['job_application_id' => $agencyCandidateAppIds]],
                    ['terms' => ['wiseID' => $agencyCandidateAppIds]],
                ];
                $must = [
                    ['terms' => ['job_id' => $clientJobIds]],
                    ['bool' => ['should' => $candMatch, 'minimum_should_match' => 1]],
                ];
            } else {
                $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                if (! empty($clientJobIds)) {
                    $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                }
                $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
            }
EOD;

$replace2_part2 = <<<'EOD'
        // 2. Interviews Search Query
        if ((! $isRelationship || (! empty($clientJobIds) && ! empty($agencyCandidateAppIds))) && in_array($query->category, ['all', 'interviews'], true) && $this->elasticService->indexExists($indexInterviews)) {
            if ($isRelationship) {
                $candMatch = [
                    ['terms' => ['candidate_id' => $agencyCandidateAppIds]],
                    ['terms' => ['job_application_id' => $agencyCandidateAppIds]],
                    ['terms' => ['wiseID' => $agencyCandidateAppIds]],
                ];
                $must = [
                    ['terms' => ['job_id' => $clientJobIds]],
                    ['bool' => ['should' => $candMatch, 'minimum_should_match' => 1]],
                ];
            } else {
                $interviewShould = [['term' => ['client_id' => $query->clientId]]];
                if (! empty($clientJobIds)) {
                    $interviewShould[] = ['terms' => ['job_id' => $clientJobIds]];
                }
                $must = [['bool' => ['should' => $interviewShould, 'minimum_should_match' => 1]]];
            }
EOD;

if (strpos($c, $target2_part2) === false) {
    die("Target 2 part 2 not found\n");
}
$c = str_replace($target2_part2, $replace2_part2, $c);

// Also replace jobs and notes in fetchMultiIndexEvents
$target2_part3 = <<<'EOD'
        // 3. Jobs Search Query
        if ((! $isDirect || ! empty($clientJobIds)) && in_array($query->category, ['all', 'jobs'], true) && $this->elasticService->indexExists($indexJobs)) {
            $must = $isDirect ? [['terms' => ['id' => $clientJobIds]]] : [['term' => ['client_id' => $query->clientId]]];
EOD;

$replace2_part3 = <<<'EOD'
        // 3. Jobs Search Query
        if ((! $isRelationship || ! empty($clientJobIds)) && in_array($query->category, ['all', 'jobs'], true) && $this->elasticService->indexExists($indexJobs)) {
            $must = $isRelationship ? [['terms' => ['id' => $clientJobIds]]] : [['term' => ['client_id' => $query->clientId]]];
EOD;

if (strpos($c, $target2_part3) === false) {
    die("Target 2 part 3 not found\n");
}
$c = str_replace($target2_part3, $replace2_part3, $c);

$target2_part4 = <<<'EOD'
        // 4. Notes Search Query (Internal clients only)
        if (! $isDirect && in_array($query->category, ['all', 'crm'], true) && $this->elasticService->indexExists($indexNotes)) {
EOD;

$replace2_part4 = <<<'EOD'
        // 4. Notes Search Query (Internal clients only)
        if (! $isRelationship && in_array($query->category, ['all', 'crm'], true) && $this->elasticService->indexExists($indexNotes)) {
EOD;

if (strpos($c, $target2_part4) === false) {
    die("Target 2 part 4 not found\n");
}
$c = str_replace($target2_part4, $replace2_part4, $c);

file_put_contents($path, $c);
echo "Successfully updated ClientActivityReadService.php\n";
