@include('service_company.client.components.tab-jobs-style')

@php
    use App\Helper\GeneralMethod;
    use App\Services\CurrencyService;
    use Illuminate\Support\Carbon;

    // Support both ViewModel injection and legacy fallback
    $jobs = isset($viewModel) ? $viewModel->jobs : (isset($data) ? $data : []);
    $currentStatus = isset($viewModel) ? $viewModel->getActiveFilter() : (request('custom_status') ?: request('status') ?: 'all');

    // Determine overall view context from ViewModel or $custom flag
    // $custom = 'true' means Internal Client; $custom = 'false' means Direct Client or Agency
    $isInternalClientView = (isset($viewModel) && $viewModel->clientType === 'internal')
        || (isset($custom) && ($custom === 'true' || $custom === true));

    // 7 Canonical recruitment pipeline stages (Direct Client & Agency: strictly exclude status 8 INW and status 9)
    $pipelineStages = [
        ['label' => 'SOU', 'cls' => 's-sou', 'id' => 1],
        ['label' => 'SCR', 'cls' => 's-scr', 'id' => 2],
        ['label' => 'EVA', 'cls' => 's-eva', 'id' => 3],
        ['label' => 'NEG', 'cls' => 's-neg', 'id' => 4],
        ['label' => 'OFF', 'cls' => 's-off', 'id' => 5],
        ['label' => 'JOI', 'cls' => 's-joi', 'id' => 6],
        ['label' => 'REJ', 'cls' => 's-rej', 'id' => 7],
    ];

    // 8 Full pipeline stages (Internal Client only: INW is a valid internal staging state)
    $pipelineStagesInternal = [
        ['label' => 'INW', 'cls' => 's-inw', 'id' => 8],
        ['label' => 'SOU', 'cls' => 's-sou', 'id' => 1],
        ['label' => 'SCR', 'cls' => 's-scr', 'id' => 2],
        ['label' => 'EVA', 'cls' => 's-eva', 'id' => 3],
        ['label' => 'NEG', 'cls' => 's-neg', 'id' => 4],
        ['label' => 'OFF', 'cls' => 's-off', 'id' => 5],
        ['label' => 'JOI', 'cls' => 's-joi', 'id' => 6],
        ['label' => 'REJ', 'cls' => 's-rej', 'id' => 7],
    ];

    // Choose active pipeline stage list based on view context
    $activePipelineStages = $isInternalClientView ? $pipelineStagesInternal : $pipelineStages;
@endphp

@if (!empty($jobs) && count($jobs) > 0)
    <div class="cd-jobs-container">
        @foreach ($jobs as $rowObj)
            @php
                $job = (object) $rowObj;
                $jobId = (int) ($job->id ?? 0);
                $rawStatus = strtolower((string) ($job->status ?? 'active'));

                // 1. Map canonical status & visual CSS classes
                $statusLabel = 'Hiring';
                $statusCls = 'status-hiring';
                if ($rawStatus === 'active' || $rawStatus === 'hiring') {
                    $statusLabel = 'Hiring';
                    $statusCls = 'status-hiring';
                } elseif ($rawStatus === 'hold') {
                    $statusLabel = 'On Hold';
                    $statusCls = 'status-hold';
                } elseif ($rawStatus === 'closed') {
                    $statusLabel = 'Closed';
                    $statusCls = 'status-closed';
                } elseif ($rawStatus === 'inactive') {
                    $statusLabel = 'Inactive';
                    $statusCls = 'status-inactive';
                }

                // 2. Sub-Card 1: Job Details (Logo, Title, ID, Department, Client)
                $rawTitle = trim((string) ($job->title ?? 'Untitled Job'));
                $jobTitle = preg_replace_callback('/\b[a-z]|(?<=[+_\/-])[a-z]/i', fn($m) => strtoupper($m[0]), strtolower($rawTitle));
                $logoInitial = strtoupper(mb_substr($jobTitle, 0, 1));
                $logoColor = isset($viewModel) && isset($viewModel->logoMap[$jobId]['color'])
                    ? $viewModel->logoMap[$jobId]['color']
                    : '#5850ec';

                $logoUrl = !empty($job->logo_logical_path) ? $job->logo_logical_path : (!empty($job->logo) ? $job->logo : '');

                // Department & Client name resolved dynamically (Strictly 0% MySQL Reads)
                $deptName = $job->department_name ?? ($job->category_name ?? 'Information Technology');
                $isDirectClientView = ($custom === 'false') || (isset($viewModel) && $viewModel->clientType === 'direct');
                if ($isDirectClientView && !empty($viewModel->clientName)) {
                    // Requirement 2: Strict internal client confidentiality for Agency viewers
                    $resolvedClient = $viewModel->clientName;
                } else {
                    $resolvedClient = trim((string) (
                        $job->client_name 
                        ?? $job->clients_name 
                        ?? ($viewModel->clientName ?? '')
                        ?? $job->client_company 
                        ?? ''
                    ));
                    if (empty($resolvedClient) && !empty($viewModel->clientName)) {
                        $resolvedClient = $viewModel->clientName;
                    }
                }
                $clientDisplayName = !empty($resolvedClient) ? strtoupper($resolvedClient) : 'CLIENT';

                // Client Type Resolution (Direct Client vs Internal Client)
                $isDirect = false;
                if (isset($job->self_company) && (int) $job->self_company === 1) {
                    $isDirect = true;
                } elseif (isset($job->client_type) && strtolower((string) $job->client_type) === 'direct') {
                    $isDirect = true;
                } elseif (isset($custom) && ($custom === 'false' || $custom === false)) {
                    $isDirect = true;
                } elseif (isset($viewModel) && !empty($viewModel->clientType) && $viewModel->clientType === 'direct') {
                    $isDirect = true;
                }

                $clientTypeLabel = $isDirect ? 'Direct Client' : 'Internal Client';
                $clientTypeCls   = $isDirect ? 'type-direct' : 'type-internal';

                // 3. Sub-Card 2: Experience & Compensation
                $minExp = (int) ($job->min_exp ?? 0);
                $maxExp = (int) ($job->max_exp ?? 0);
                if ($minExp > 0 || $maxExp > 0) {
                    $expText = ($minExp === $maxExp) ? "{$minExp} Yrs" : "{$minExp} - {$maxExp} Yrs";
                } else {
                    $expText = '0 - 1 Yrs';
                }

                $currCode = CurrencyService::resolveCurrencyCode($job->min_salary_currency ?? 132);
                $currSym = CurrencyService::getCurrencySymbol($currCode);

                $minSalRaw = (float) ($job->min_salary ?? 0);
                $maxSalRaw = (float) ($job->max_salary ?? 0);

                // Global compact K/M formatting (0% Lakhs, Lacs, Crores, or India-specific notation)
                $fmtK = function($num) {
                    $num = (float) $num;
                    if ($num <= 0) return '0';
                    if ($num >= 1000000) {
                        $v = round($num / 1000000, 1);
                        return ($v == (int)$v ? (int)$v : $v) . 'M';
                    }
                    if ($num >= 1000) {
                        $v = round($num / 1000, 1);
                        return ($v == (int)$v ? (int)$v : $v) . 'K';
                    }
                    return (string) round($num);
                };

                $minK = $fmtK($minSalRaw);
                $maxK = $fmtK($maxSalRaw);

                if ($minSalRaw > 0 && $maxSalRaw > 0) {
                    $compText = "{$currSym} {$minK} – {$maxK}";
                } elseif ($minSalRaw > 0) {
                    $compText = "{$currSym} {$minK}";
                } elseif ($maxSalRaw > 0) {
                    $compText = "{$currSym} {$maxK}";
                } else {
                    $compText = "{$currSym} 0";
                }

                $empType = ((int) ($job->employment_type ?? 0)) === 1 ? 'Contract' : 'Full-Time';

                // Location & Work Mode (Standardized "Work From Office")
                $cities = $job->client_city_jobs ?? [];
                $cityCount = count($cities);
                $cityName = 'Work From Office';
                if ($cityCount > 0) {
                    $cityName = ucwords(strtolower($cities[0]['city_name'] ?? ''));
                }
                $cityExtra = $cityCount > 1 ? ($cityCount - 1) : 0;

                $workTypeRaw = strtolower((string) ($job->work_type ?? ''));
                $workMode = 'Work From Office';
                if ($workTypeRaw === 'remote' || $workTypeRaw === 'wfh') {
                    $workMode = 'Remote';
                } elseif ($workTypeRaw === 'hybrid') {
                    $workMode = 'Hybrid';
                }

                // 4. Hiring Manager & Client Representative Attribution
                $contacts = $job->client_contact_info_jobs ?? [];
                $hmName = 'Hiring Manager';
                $hmTitle = 'Role';
                if (!empty($contacts) && isset($contacts[0])) {
                    $hmName = ucwords(strtolower($contacts[0]['contact_person_name'] ?? 'Hiring Manager'));
                    $hmTitle = ucwords(strtolower($contacts[0]['contact_person_designation'] ?? 'Role'));
                }

                // 5. Sub-Card 3: Team (Captain / Primary Representative First)
                $team = $job->job_assign ?? [];
                $teamCount = count($team);
                $primaryRepName = null;

                if (!empty($team)) {
                    foreach ($team as $member) {
                        $mArr = (array) $member;
                        $uId = (int) ($mArr['user_id'] ?? 0);
                        $spokId = (int) ($mArr['spok_user_id'] ?? 0);
                        $isCap = !empty($mArr['is_captain']) || ($uId > 0 && $uId === $spokId);
                        if ($isCap && !empty($mArr['user_name'])) {
                            $primaryRepName = ucwords(strtolower(trim($mArr['user_name'])));
                            break;
                        }
                    }
                    if (empty($primaryRepName) && isset($team[0])) {
                        $firstMember = (array) $team[0];
                        if (!empty($firstMember['user_name'])) {
                            $primaryRepName = ucwords(strtolower(trim($firstMember['user_name'])));
                        }
                    }
                }

                if (empty($primaryRepName)) {
                    $primaryRepName = 'Team';
                }

                $otherCount = max(0, $teamCount - 1);
                if ($otherCount > 0) {
                    $teamMeta = '+ ' . $otherCount . ' other member' . ($otherCount > 1 ? 's' : '');
                } else {
                    $teamMeta = 'No other members';
                }

                // 6. Sub-Card 4: Agency Name Resolution (Strictly displays actual Agency Name, NEVER a count like "3 Agencies")
                $agencyName = null;
                $viewerCompanyId = (int) (auth()->user()->company_id ?? ($company_id ?? 0));
                if ($viewerCompanyId <= 1) {
                    $viewerCompanyId = 2; // Default to agency company 4Bell Technology
                }
                $agencies = is_array($job->recommendation_relation ?? null) ? $job->recommendation_relation : [];
                if (!empty($agencies)) {
                    $selectedRel = null;
                    foreach ($agencies as $rel) {
                        $relArr = (array) $rel;
                        $toCompId = (int) ($relArr['to_company_id'] ?? 0);
                        if ($viewerCompanyId > 0 && $toCompId === $viewerCompanyId) {
                            $selectedRel = $relArr;
                            break;
                        }
                    }
                    if (!$selectedRel && isset($agencies[0])) {
                        $selectedRel = (array) $agencies[0];
                    }

                    if ($selectedRel) {
                        $targetAgencyId = (int) ($selectedRel['to_company_id'] ?? 0);
                        if ($targetAgencyId <= 0 && !empty($viewerCompanyId)) {
                            $targetAgencyId = $viewerCompanyId;
                        }
                        if ($targetAgencyId > 0) {
                            $cacheCompKey = "ats:tenant_comp_name:{$targetAgencyId}";
                            $cachedCompName = \Illuminate\Support\Facades\Redis::get($cacheCompKey);
                            if (!empty($cachedCompName)) {
                                $agencyName = (string) $cachedCompName;
                            } else {
                                try {
                                    $cRow = \Illuminate\Support\Facades\DB::table('companies')->where('id', $targetAgencyId)->first(['company_name']);
                                    if ($cRow) {
                                        $agencyName = $cRow->company_name ?? null;
                                        if ($agencyName) {
                                            \Illuminate\Support\Facades\Redis::setex($cacheCompKey, 86400, $agencyName);
                                        }
                                    }
                                } catch (\Throwable) {}
                            }
                        }
                    }
                }

                if (empty($agencyName)) {
                    if (!empty($job->agency_name)) {
                        $agencyName = $job->agency_name;
                    } elseif (!empty($viewerCompanyId)) {
                        $cacheCompKey = "ats:tenant_comp_name:{$viewerCompanyId}";
                        $cachedCompName = \Illuminate\Support\Facades\Redis::get($cacheCompKey);
                        if (!empty($cachedCompName)) {
                            $agencyName = (string) $cachedCompName;
                        } else {
                            try {
                                $cRow = \Illuminate\Support\Facades\DB::table('companies')->where('id', $viewerCompanyId)->first(['company_name']);
                                $agencyName = $cRow->company_name ?? '4Bell Technology';
                                \Illuminate\Support\Facades\Redis::setex($cacheCompKey, 86400, $agencyName);
                            } catch (\Throwable) {
                                $agencyName = '4Bell Technology';
                            }
                        }
                    } else {
                        $agencyName = '4Bell Technology';
                    }
                }
                $agencyText = $agencyName;

                // Authoritative Agency Representative: The specific user who shared this job with the agency
                $agencyRepName = null;
                if (!empty($agencies)) {
                    $firstRel = is_array($agencies[0]) ? $agencies[0] : (array)$agencies[0];
                    $sharerId = (int) ($firstRel['from_user_id'] ?? 0);
                    if ($sharerId > 0) {
                        $agencyRepName = $firstRel['from_user_name'] ?? $firstRel['user_name'] ?? null;
                        if (!$agencyRepName && class_exists(\App\Services\CompanyRepresentativeService::class)) {
                            $repSummary = app(\App\Services\CompanyRepresentativeService::class)->resolveUserSummary($sharerId, (int)($job->company_id ?? 0));
                            if (!empty($repSummary['name'])) {
                                $agencyRepName = $repSummary['name'];
                            }
                        }
                    }
                }

                // Job Creator Name (Internal Author)
                $jobCreatorName = null;
                $creatorId = (int) ($job->user_id ?? $job->posted_by ?? 0);
                if ($creatorId > 0 && class_exists(\App\Services\CompanyRepresentativeService::class)) {
                    $creatorSummary = app(\App\Services\CompanyRepresentativeService::class)->resolveUserSummary($creatorId, (int)($job->company_id ?? 0));
                    if (!empty($creatorSummary['name'])) {
                        $jobCreatorName = $creatorSummary['name'];
                    }
                }

                // 7. Sub-Card 5: Pipeline Stages (Strictly Read-Only)
                $jobStageCounts = isset($viewModel) && isset($viewModel->pipelineStageMap[$jobId]) 
                    ? $viewModel->pipelineStageMap[$jobId] 
                    : [];

                $totalApps = 0;
                $totalInterviews = 0;
                $totalOffers = 0;
                $totalJoined = 0;
                $totalSourcing = (int) ($jobStageCounts[1] ?? 0);

                foreach ($activePipelineStages as $st) {
                    $sId = (int) $st['id'];
                    // Always exclude status 9 (Archived). For direct/agency, INW (8) is not in $activePipelineStages.
                    // For internal client, INW (8) is included in $activePipelineStages and counts toward totalApps.
                    if ($sId === 9) {
                        continue;
                    }
                    $c = (int) ($jobStageCounts[$sId] ?? 0);
                    $totalApps += $c;
                    if (in_array($sId, [3, 4])) {
                        $totalInterviews += $c;
                    } elseif ($sId === 5) {
                        $totalOffers += $c;
                    } elseif ($sId === 6) {
                        $totalJoined += $c;
                    }
                }

                $resolvedInterviews = isset($viewModel) && isset($viewModel->interviewMap[$jobId])
                    ? max($totalInterviews, (int) $viewModel->interviewMap[$jobId])
                    : $totalInterviews;

                // Client Representative & Company Attribution (Actual User · Company Name)
                $clientCompany = !empty($resolvedClient) ? ucwords(strtolower($resolvedClient)) : 'Client';
                $clientRepUser = null;

                if (!empty($contacts) && isset($contacts[0])) {
                    $cName = trim((string)($contacts[0]['contact_person_name'] ?? ''));
                    if (!empty($cName) && !in_array(strtolower($cName), ['hiring manager', 'client', 'contact'], true)) {
                        $clientRepUser = ucwords(strtolower($cName));
                    }
                }

                if (empty($clientRepUser) && !empty($agencies)) {
                    $firstRel = (array) $agencies[0];
                    $fromUName = trim((string)($firstRel['from_user_name'] ?? $firstRel['user_name'] ?? ''));
                    if (!empty($fromUName)) {
                        $clientRepUser = ucwords(strtolower($fromUName));
                    }
                }

                if (empty($clientRepUser) && !empty($jobCreatorName)) {
                    $clientRepUser = $jobCreatorName;
                }

                if (!empty($clientRepUser) && !empty($clientCompany)) {
                    $clientRepAttribution = "{$clientRepUser} · {$clientCompany}";
                } elseif (!empty($clientRepUser)) {
                    $clientRepAttribution = $clientRepUser;
                } else {
                    $clientRepAttribution = $clientCompany;
                }

                // 8. Sub-Card 6: Bottom Activity Strip (Skills & Date)
                $formattedDate = !empty($job->created_at) 
                    ? Carbon::parse($job->created_at)->format('d M y') 
                    : date('d M y');

                $skills = [];
                if (!empty($job->job_metronics_skills)) {
                    foreach ($job->job_metronics_skills as $sk) {
                        if (!empty($sk['skill_name'])) {
                            $skills[] = ucwords(strtolower($sk['skill_name']));
                        }
                    }
                }
                $normalizedStatus = ($rawStatus === 'active' || $rawStatus === 'hiring') ? 'hiring' : $rawStatus;
                $searchHaystack = strtolower(implode(' ', array_filter([
                    $jobTitle,
                    '#' . $jobId,
                    (string) $jobId,
                    $deptName,
                    $cityName,
                    $workMode,
                    $hmName,
                    $primaryRepName,
                    $clientRepAttribution,
                    implode(' ', $skills),
                    $empType,
                    $clientDisplayName
                ])));
            @endphp

            <!-- Master Primary Job Card -->
            <div class="cd-job-card js-job-card-clickable" 
                 data-entity="job" 
                 data-job-id="{{ $jobId }}"
                 data-status="{{ $normalizedStatus }}"
                 data-client-type="{{ $isDirect ? 'direct' : 'internal' }}"
                 data-min-sal="{{ $minSalRaw }}"
                 data-max-sal="{{ $maxSalRaw }}"
                 data-currency="{{ $currCode }}"
                 data-search="{{ $searchHaystack }}"
                 style="cursor: pointer;">
                <!-- Upper Sub-Cards Grid (5 Sections) -->
                <div class="cd-job-card-grid">
                    <!-- Sub-Card 1: Job Details (Logo, Title, ID, Department, Client) -->
                    <div class="cd-job-subcard cd-subcard-details">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Job Details</span>
                            <div class="cd-header-badges">
                                <span class="cd-badge-client-type {{ $clientTypeCls }}"><i class="fas fa-building me-1"></i>{{ $clientTypeLabel }}</span>
                                <span class="cd-status-badge {{ $statusCls }}">{{ $statusLabel }}</span>
                            </div>
                        </div>
                        <div class="cd-details-body">
                            <div class="cd-logo-box" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                @if(!empty($logoUrl))
                                    <img src="{{ $logoUrl }}" alt="{{ $clientDisplayName }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <span style="display:none; color: #009ef7; font-weight: 700;">{{ $logoInitial }}</span>
                                @else
                                    <span style="color: #009ef7; font-weight: 700;">{{ $logoInitial }}</span>
                                @endif
                            </div>
                            <div class="cd-details-text">
                                <a href="javascript:void(0);" 
                                   class="cd-job-title js-job-drawer-trigger" 
                                   data-job-id="{{ $jobId }}" 
                                   title="{{ $jobTitle }}">
                                    {{ $jobTitle }}
                                </a>
                                <div class="cd-job-meta">
                                    <span class="cd-job-id">#{{ $jobId }}</span>
                                    <span class="cd-bullet">&bull;</span>
                                    <span class="cd-job-dept" title="{{ $deptName }}">{{ $deptName }}</span>
                                </div>
                                <div class="cd-job-client-row">
                                    <span class="cd-job-client-name" title="{{ $clientDisplayName }}">{{ $clientDisplayName }}</span>
                                    <span class="cd-bullet">&bull;</span>
                                    <span class="cd-job-emp-type"><i class="fas fa-briefcase text-primary me-1"></i>{{ $empType }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 2: Compensation & Experience -->
                    <div class="cd-job-subcard cd-subcard-comp-loc">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Compensation &amp; Exp</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-val-salary">
                                <strong>{{ $compText }}</strong>
                            </div>
                            <div class="cd-val-exp">
                                <i class="far fa-clock text-muted me-1"></i><span>{{ $expText }}</span>
                            </div>
                            <div class="cd-val-loc" title="{{ $cityName }} &bull; {{ $workMode }}">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $cityName }}@if($cityExtra > 0)<span class="cd-loc-badge">+{{ $cityExtra }}</span>@endif
                                <span class="cd-bullet">&bull;</span>
                                <span>{{ $workMode }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 3: Team / Ownership (Captain / Primary Rep First) -->
                    <div class="cd-job-subcard cd-subcard-team">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fa fa-users me-1 text-muted"></i>Team</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-val-name" title="{{ $primaryRepName }}"><strong>{{ $primaryRepName }}</strong></div>
                            <div class="cd-val-sub">{{ $teamMeta }}</div>
                        </div>
                    </div>

                    <!-- Sub-Card 4: Agency (Display Actual Agency Name) -->
                    <div class="cd-job-subcard cd-subcard-agency">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fa fa-handshake me-1 text-muted"></i>Agency</span>
                        </div>
                        <div class="cd-subcard-body">
                            @if($agencyText !== '—')
                                <div class="cd-agency-val" title="{{ $agencyText }}">{{ $agencyText }}</div>
                                @if(!empty($agencyRepName))
                                    <div class="cd-val-sub text-success" title="Agency Representative: {{ $agencyRepName }} (User who shared this job with agency)" style="font-size: 10px; font-weight: 600;">
                                        <i class="fa fa-user-check me-1"></i>Rep: {{ \Illuminate\Support\Str::limit($agencyRepName, 12) }}
                                    </div>
                                @endif
                            @else
                                <div class="cd-agency-empty">—</div>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 5: Hiring Pipeline (Stages: 8 for Internal, 7 for Direct/Agency) -->
                    <div class="cd-job-subcard cd-subcard-pipeline">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Hiring Pipeline</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-pipeline-track">
                                @foreach ($activePipelineStages as $stage)
                                    @php
                                        $stageCount = (int) ($jobStageCounts[$stage['id']] ?? 0);
                                    @endphp
                                    <div class="cd-track-stage" title="{{ $stage['label'] }}: {{ $stageCount }}">
                                        <span class="cd-track-label">{{ $stage['label'] }}</span>
                                        <span class="cd-track-badge {{ $stage['cls'] }}">{{ $stageCount }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="cd-track-summary">
                                <span>Applications: <strong>{{ $totalApps }}</strong></span>
                                <span class="cd-bullet">&bull;</span>
                                <span>Interviews: <strong>{{ $resolvedInterviews }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Bottom Activity Strip (Timeline, Author, HM, Agency Rep & Skills) -->
                <div class="cd-job-card-bottom">
                    <div class="cd-bottom-left">
                        <span><i class="far fa-calendar-alt text-muted me-1"></i>Posted: <strong>{{ $formattedDate }}</strong></span>
                        @if(!empty($jobCreatorName))
                            <span class="cd-bullet">&bull;</span>
                            <span title="Job Created By: {{ $jobCreatorName }} (Internal Author)"><i class="far fa-user text-muted me-1"></i>Created by: <strong>{{ \Illuminate\Support\Str::limit($jobCreatorName, 12) }}</strong></span>
                        @endif
                        @if(!empty($clientRepAttribution))
                            <span class="cd-bullet">&bull;</span>
                            <span title="Client Representative: {{ $clientRepAttribution }}"><i class="far fa-id-badge text-primary me-1"></i>Client Rep: <strong>{{ $clientRepAttribution }}</strong></span>
                        @elseif(!empty($hmName) && $hmName !== 'Hiring Manager')
                            <span class="cd-bullet">&bull;</span>
                            <span title="Client SPOC: {{ $hmName }} ({{ $hmTitle }})"><i class="far fa-id-badge text-primary me-1"></i>Client Rep: <strong>{{ \Illuminate\Support\Str::limit($hmName, 12) }}</strong></span>
                        @endif
                        @if(!empty($agencyRepName))
                            <span class="cd-bullet">&bull;</span>
                            <span title="Agency Representative: {{ $agencyRepName }} (Shared by this user)"><i class="fa fa-handshake text-success me-1"></i>Agency Rep: <strong>{{ \Illuminate\Support\Str::limit($agencyRepName, 12) }}</strong></span>
                        @endif
                    </div>
                    <div class="cd-bottom-skills">
                        @if(!empty($skills))
                            @foreach ($skills as $skill)
                                <span class="cd-skill-pill">{{ $skill }}</span>
                            @endforeach
                        @else
                            <span class="text-muted" style="font-size: 10px;">No skills specified</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="cd-jobs-empty">
        <i class="fas fa-briefcase fa-2x mb-3 text-muted d-block"></i>
        <div class="fw-bold fs-6 text-gray-700 mb-1">No Job Requisitions Found</div>
        <div class="text-muted fs-7">No job requisitions match the current criteria for this client.</div>
    </div>
@endif
