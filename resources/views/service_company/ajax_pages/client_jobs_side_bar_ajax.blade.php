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
    @foreach ($jobs as $rowObj)
        @php
            $job = (object) $rowObj;
            $jobId = (int) ($job->id ?? 0);
            $rawStatus = strtolower((string) ($job->status ?? 'active'));

            // 1. Map canonical status & visual CSS classes
            $statusLabel = 'Hiring';
            $statusCls = 'active-bg';
            if ($rawStatus === 'active' || $rawStatus === 'hiring') {
                $statusLabel = 'Hiring';
                $statusCls = 'active-bg';
            } elseif ($rawStatus === 'hold') {
                $statusLabel = 'Hold';
                $statusCls = 'hold-bg';
            } elseif ($rawStatus === 'closed') {
                $statusLabel = 'Closed';
                $statusCls = 'closed-bg';
            } elseif ($rawStatus === 'inactive') {
                $statusLabel = 'Inactive';
                $statusCls = 'inactive-bg';
            }

            // 2. Col 1: Job Details (Logo, Title, Department, Client)
            $rawTitle = trim((string) ($job->title ?? 'Untitled Job'));
            $jobTitle = preg_replace_callback('/\b[a-z]|(?<=[+_\/-])[a-z]/i', fn($m) => strtoupper($m[0]), strtolower($rawTitle));
            $logoInitial = strtoupper(mb_substr($jobTitle, 0, 1));
            $logoColor = isset($viewModel) && isset($viewModel->logoMap[$jobId]['color'])
                ? $viewModel->logoMap[$jobId]['color']
                : '#5850ec';

            // Department & Company name resolved from ES (0% MySQL Reads)
            $deptName = $job->department_name ?? ($job->category_name ?? 'Information Technology');
            $compName = strtoupper($job->client_company ?? ($job->company_name ?? ($job->client_name ?? 'Client Account')));

            // 3. Col 2: Experience & Compensation
            $minExp = $job->min_exp ?? 0;
            $maxExp = $job->max_exp ?? 0;

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

            $empType = ((int) ($job->employment_type ?? 0)) === 1 ? 'Contract' : 'Full-time';

            // 4. Col 3: Hiring & Location
            $cities = $job->client_city_jobs ?? [];
            $cityCount = count($cities);
            $cityName = 'Office';
            if ($cityCount > 0) {
                $cityName = ucwords(strtolower($cities[0]['city_name'] ?? ''));
            }
            $cityExtra = $cityCount > 1 ? ($cityCount - 1) : 0;

            $workTypeRaw = strtolower((string) ($job->work_type ?? ''));
            $workMode = 'Office';
            if ($workTypeRaw === 'remote' || $workTypeRaw === 'wfh') {
                $workMode = 'Remote';
            } elseif ($workTypeRaw === 'hybrid') {
                $workMode = 'Hybrid';
            }

            // 5. Col 4: Hiring Manager
            $contacts = $job->client_contact_info_jobs ?? [];
            $hmName = 'Hiring Manager';
            $hmTitle = 'Role';
            if (!empty($contacts) && isset($contacts[0])) {
                $hmName = ucwords(strtolower($contacts[0]['contact_person_name'] ?? 'Hiring Manager'));
                $hmTitle = ucwords(strtolower($contacts[0]['contact_person_designation'] ?? 'Role'));
            }
            $clientTag = 'Internal Client &bull; ' . $jobId;

            // 6. Col 5: Team (Strictly Read-Only)
            $team = $job->job_assign ?? [];
            $teamCount = count($team);
            $teamName = 'Team';
            if (!empty($team) && isset($team[0])) {
                $teamName = ucwords(strtolower($team[0]['user_name'] ?? 'Team'));
            }
            $teamMeta = $teamCount . ' ' . ($teamCount === 1 ? 'Member' : 'Members');

            // 7. Col 6: Agency (Strictly Read-Only)
            $agencies = $job->recommendation_relation ?? [];
            $agencyName = 'Direct Partner';
            $agencyMeta = 'Direct';
            if (!empty($agencies) && isset($agencies[0])) {
                $agencyName = ucwords(strtolower($agencies[0]['partner_company_name'] ?? 'Partner Agency'));
                $agencyMeta = 'Agency';
            }
            $matchingCount = $cityCount > 0 ? $cityCount : 15;

            // 8. Col 7: Pipeline Stages (Strictly Read-Only)
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

            // 9. Bottom Row: Skills & Date
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

        <!-- 7-Column Grid Job Row (Screenshot 2 Match) -->
        <div class="job-row" data-entity="job" data-job-id="{{ $jobId }}">
            <!-- Col 1: Job Details -->
            <div class="col-job">
                <div class="job-logo" style="background-color: {{ $logoColor }};">
                    {{ $logoInitial }}
                </div>
                <div class="job-info">
                    <div class="job-title" title="{{ $jobTitle }}">{{ $jobTitle }}</div>
                    <div class="job-dept">{{ $deptName }}</div>
                    <div class="job-client">{{ $compName }}</div>
                </div>
            </div>

            <!-- Col 2: Experience & Salary -->
            <div class="col-exp">
                <div class="exp-row">
                    <i class="far fa-clock"></i>
                    <span>{{ $minExp }} - {{ $maxExp }} Yrs</span>
                </div>
                <div class="sal-row">
                    <span>{{ $currSym }}{{ $minSalFmt }} - {{ $maxSalFmt }}</span>
                </div>
                <div class="emp-row">
                    <i class="fas fa-briefcase"></i>
                    <span>{{ $empType }}</span>
                </div>
            </div>

            <!-- Col 3: Hiring & Location -->
            <div class="col-hl">
                <div class="badge-hiring {{ $statusCls }}">
                    <span class="status-dot"></span> {{ $statusLabel }}
                </div>
                <div class="loc-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $cityName }}</span>
                    @if($cityExtra > 0)
                        <span class="loc-badge">+{{ $cityExtra }}</span>
                    @endif
                </div>
                <div class="mode-row">
                    <i class="fas fa-building"></i>
                    <span>{{ $workMode }}</span>
                </div>
            </div>

            <!-- Col 4: Hiring Manager -->
            <div class="col-hm">
                <div class="hm-name" title="{{ $hmName }}">{{ $hmName }}</div>
                <div class="hm-title" title="{{ $hmTitle }}">{{ $hmTitle }}</div>
                <div class="hm-client-tag">{!! $clientTag !!}</div>
            </div>

            <!-- Col 5: Team (Read-Only) -->
            <div class="col-action">
                <div class="hm-name" title="{{ $teamName }}">{{ $teamName }}</div>
                <div class="team-meta">{{ $teamMeta }}</div>
                <div class="match-meta">{{ $matchingCount }} Matching</div>
            </div>

            <!-- Col 6: Agency (Read-Only) -->
            <div class="col-action">
                <div class="hm-name" title="{{ $agencyName }}">{{ $agencyName }}</div>
                <div class="agency-meta">{{ $agencyMeta }}</div>
                <div class="match-meta">{{ $matchingCount }} Matching</div>
            </div>

            <!-- Col 7: Pipeline Stages (Read-Only) -->
            <div class="col-pipeline">
                <div class="pipeline-stages">
                    @foreach ($pipelineStages as $stage)
                        @php
                            $stageCount = $jobStageCounts[$stage['id']] ?? 0;
                        @endphp
                        <div class="stage" title="{{ $stage['label'] }}: {{ $stageCount }}">
                            <span class="stage-label">{{ $stage['label'] }}</span>
                            <span class="stage-badge {{ $stage['cls'] }}">{{ $stageCount }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="pipeline-total">
                    <span>Total Applications: <strong>{{ $totalApps }}</strong></span>
                    <span class="mx-1">|</span>
                    <span>Total Interviews: <strong>{{ $totalInterviews }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Bottom Skills & Date Bar -->
        <div class="job-row-skills">
            <div class="job-date">
                <i class="far fa-calendar-alt"></i>
                <span>{{ $formattedDate }}</span>
            </div>
            @foreach ($skills as $skill)
                <span class="sk-tag">{{ $skill }}</span>
            @endforeach
        </div>
    @endforeach
@else
    <div class="job-empty">
        <i class="fas fa-briefcase fa-2x mb-3 text-muted d-block"></i>
        <div class="fw-bold fs-6 text-gray-700 mb-1">No Job Requisitions Found</div>
        <div class="text-muted fs-7">No job requisitions match the current criteria for this client.</div>
    </div>
@endif