@include('service_company.client.components.tab-jobs-style')

@php
    use App\Helper\GeneralMethod;
    use App\Services\CurrencyService;
    use Illuminate\Support\Carbon;

    // Support both ViewModel injection and legacy fallback
    $jobs = isset($viewModel) ? $viewModel->jobs : (isset($data) ? $data : []);
    $currentStatus = isset($viewModel) ? $viewModel->getActiveFilter() : (request('custom_status') ?: request('status') ?: 'all');

    // 8 Canonical recruitment pipeline stages
    $pipelineStages = [
        ['label' => 'INW', 'cls' => 's-inw', 'id' => 8],
        ['label' => 'SOU', 'cls' => 's-sou', 'id' => 1],
        ['label' => 'SCR', 'cls' => 's-scr', 'id' => 2],
        ['label' => 'EVA', 'cls' => 's-eva', 'id' => 3],
        ['label' => 'NEG', 'cls' => 's-neg', 'id' => 4],
        ['label' => 'OFF', 'cls' => 's-off', 'id' => 5],
        ['label' => 'JOI', 'cls' => 's-joi', 'id' => 6],
        ['label' => 'REJ', 'cls' => 's-rej', 'id' => 7],
    ];
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
                $clientDisplayName = !empty($resolvedClient) ? strtoupper($resolvedClient) : 'CLIENT';

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

                if ($minSalRaw >= 100000) {
                    $minSalFmt = number_format($minSalRaw / 100000, 2) . ' L';
                } elseif ($minSalRaw > 0) {
                    $minSalFmt = number_format($minSalRaw);
                } else {
                    $minSalFmt = '0';
                }

                if ($maxSalRaw >= 100000) {
                    $maxSalFmt = number_format($maxSalRaw / 100000, 2) . ' L';
                } elseif ($maxSalRaw > 0) {
                    $maxSalFmt = number_format($maxSalRaw);
                } else {
                    $maxSalFmt = '0';
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

                // 4. Hiring Manager
                $contacts = $job->client_contact_info_jobs ?? [];
                $hmName = 'Hiring Manager';
                $hmTitle = 'Role';
                if (!empty($contacts) && isset($contacts[0])) {
                    $hmName = ucwords(strtolower($contacts[0]['contact_person_name'] ?? 'Hiring Manager'));
                    $hmTitle = ucwords(strtolower($contacts[0]['contact_person_designation'] ?? 'Role'));
                }

                // 5. Sub-Card 3: Team (Strictly Read-Only — No matching badges)
                $team = $job->job_assign ?? [];
                $teamCount = count($team);
                $teamName = 'Team';
                if (!empty($team) && isset($team[0])) {
                    $teamName = ucwords(strtolower($team[0]['user_name'] ?? 'Team'));
                }
                $teamMeta = $teamCount > 0 ? ($teamCount . ' ' . ($teamCount === 1 ? 'Member' : 'Members')) : '—';

                // 6. Sub-Card 4: Agency (Strictly Read-Only — Based on actual hired relationships, No matching badges)
                $agencies = is_array($job->recommendation_relation ?? null) ? $job->recommendation_relation : [];
                $hiredAgencies = collect($agencies)->filter(function($rel) {
                    if (is_array($rel)) {
                        return (isset($rel['final_status']) && (int)$rel['final_status'] === 1)
                            || (isset($rel['from_status']) && (int)$rel['from_status'] === 1)
                            || (!isset($rel['final_status']) && !isset($rel['from_status']));
                    } elseif (is_object($rel)) {
                        return (isset($rel->final_status) && (int)$rel->final_status === 1)
                            || (isset($rel->from_status) && (int)$rel->from_status === 1)
                            || (!isset($rel->final_status) && !isset($rel->from_status));
                    }
                    return false;
                });
                $hiredAgencyCount = $hiredAgencies->pluck('to_company_id')->filter()->unique()->count();
                if ($hiredAgencyCount === 0 && count($agencies) > 0) {
                    $hiredAgencyCount = count($agencies);
                }

                $agencyText = $hiredAgencyCount > 0 
                    ? ($hiredAgencyCount . ' ' . ($hiredAgencyCount === 1 ? 'Agency' : 'Agencies')) 
                    : '—';

                // 7. Sub-Card 5: Pipeline Stages (Strictly Read-Only)
                $jobStageCounts = isset($viewModel) && isset($viewModel->pipelineStageMap[$jobId]) 
                    ? $viewModel->pipelineStageMap[$jobId] 
                    : [];

                $totalApps = 0;
                $totalInterviews = 0;
                foreach ($pipelineStages as $st) {
                    $c = (int) ($jobStageCounts[$st['id']] ?? 0);
                    $totalApps += $c;
                    if (in_array($st['id'], [3, 4])) {
                        $totalInterviews += $c;
                    }
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
            @endphp

            <!-- Master Primary Job Card -->
            <div class="cd-job-card" data-entity="job" data-job-id="{{ $jobId }}">
                <!-- Upper Sub-Cards Grid (5 Sections) -->
                <div class="cd-job-card-grid">
                    <!-- Sub-Card 1: Job Details (Logo, Title, ID, Department, Client) -->
                    <div class="cd-job-subcard cd-subcard-details">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Job Details</span>
                            <span class="cd-status-badge {{ $statusCls }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="cd-details-body">
                            <div class="cd-logo-box" style="background-color: {{ $logoColor }};">
                                @if(!empty($logoUrl))
                                    <img src="{{ $logoUrl }}" alt="{{ $clientDisplayName }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <span style="display:none;">{{ $logoInitial }}</span>
                                @else
                                    <span>{{ $logoInitial }}</span>
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
                                <strong>{{ $currSym }}{{ $minSalFmt }} - {{ $maxSalFmt }}</strong>
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

                    <!-- Sub-Card 3: Team / Ownership (No Matching Badge) -->
                    <div class="cd-job-subcard cd-subcard-team">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fa fa-users me-1 text-muted"></i>Team</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-val-name" title="{{ $teamName }}">{{ $teamName }}</div>
                            <div class="cd-val-sub">{{ $teamMeta }}</div>
                        </div>
                    </div>

                    <!-- Sub-Card 4: Agency (No Matching Badge, Hired Count or "—") -->
                    <div class="cd-job-subcard cd-subcard-agency">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fa fa-handshake me-1 text-muted"></i>Agency</span>
                        </div>
                        <div class="cd-subcard-body">
                            @if($agencyText !== '—')
                                <div class="cd-agency-val">{{ $agencyText }}</div>
                            @else
                                <div class="cd-agency-empty">—</div>
                            @endif
                        </div>
                    </div>

                    <!-- Sub-Card 5: Hiring Pipeline (8 Stages + Counters) -->
                    <div class="cd-job-subcard cd-subcard-pipeline">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Hiring Pipeline</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-pipeline-track">
                                @foreach ($pipelineStages as $stage)
                                    @php
                                        $stageCount = $jobStageCounts[$stage['id']] ?? 0;
                                    @endphp
                                    <div class="cd-track-stage" title="{{ $stage['label'] }}: {{ $stageCount }}">
                                        <span class="cd-track-label">{{ $stage['label'] }}</span>
                                        <span class="cd-track-badge {{ $stage['cls'] }}">{{ $stageCount }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="cd-track-summary">
                                <span>Apps: <strong>{{ $totalApps }}</strong></span>
                                <span class="cd-bullet">&bull;</span>
                                <span>Interviews: <strong>{{ $totalInterviews }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Bottom Activity Strip (Timeline, HM & Skills) -->
                <div class="cd-job-card-bottom">
                    <div class="cd-bottom-left">
                        <span><i class="far fa-calendar-alt text-muted me-1"></i>Posted: <strong>{{ $formattedDate }}</strong></span>
                        @if(!empty($hmName) && $hmName !== 'Hiring Manager')
                            <span class="cd-bullet">&bull;</span>
                            <span title="{{ $hmName }} ({{ $hmTitle }})"><i class="far fa-user text-muted me-1"></i>HM: <strong>{{ $hmName }}</strong></span>
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
