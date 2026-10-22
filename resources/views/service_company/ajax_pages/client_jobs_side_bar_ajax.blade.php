@include('service_company.client.components.tab-jobs-style')

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Carbon;

    // Support both ViewModel injection and legacy fallback
    $jobs = isset($viewModel) ? $viewModel->jobs : (isset($data) ? $data : []);
    $currentStatus = isset($viewModel) ? $viewModel->getActiveFilter() : (request('custom_status') ?: 'all');

    // 8 Canonical recruitment pipeline stages
    $pipelineStages = [
        ['label' => 'INW', 'id' => 8],
        ['label' => 'SOU', 'id' => 1],
        ['label' => 'SCR', 'id' => 2],
        ['label' => 'EVA', 'id' => 3],
        ['label' => 'NEG', 'id' => 4],
        ['label' => 'OFF', 'id' => 5],
        ['label' => 'JOI', 'id' => 6],
        ['label' => 'REJ', 'id' => 7],
    ];
@endphp

<div class="cl-jobs-container">
    @if(isset($viewModel))
        <!-- 5-Option Status Filter Group (4 Canonical States + All Jobs) -->
        <div class="cl-jobs-filter-group">
            <button type="button" 
                    class="btn btn-sm btn-light js-job-filter-btn {{ $currentStatus === 'all' ? 'active' : '' }}" 
                    data-status="all">
                All Jobs <span class="badge badge-light-dark ms-1">{{ $viewModel->totalCount }}</span>
            </button>
            <button type="button" 
                    class="btn btn-sm btn-light js-job-filter-btn {{ $currentStatus === 'hiring' || $currentStatus === 'active' ? 'active' : '' }}" 
                    data-status="hiring">
                Hiring <span class="badge badge-light-success ms-1">{{ $viewModel->hiringCount }}</span>
            </button>
            <button type="button" 
                    class="btn btn-sm btn-light js-job-filter-btn {{ $currentStatus === 'hold' ? 'active' : '' }}" 
                    data-status="hold">
                On Hold <span class="badge badge-light-warning ms-1">{{ $viewModel->holdCount }}</span>
            </button>
            <button type="button" 
                    class="btn btn-sm btn-light js-job-filter-btn {{ $currentStatus === 'closed' ? 'active' : '' }}" 
                    data-status="closed">
                Closed <span class="badge badge-light-danger ms-1">{{ $viewModel->closedCount }}</span>
            </button>
            <button type="button" 
                    class="btn btn-sm btn-light js-job-filter-btn {{ $currentStatus === 'inactive' ? 'active' : '' }}" 
                    data-status="inactive">
                Inactive <span class="badge badge-light-secondary ms-1">{{ $viewModel->inactiveCount }}</span>
            </button>
        </div>
    @endif

    @if (!empty($jobs) && count($jobs) > 0)
        <div class="cl-jobs-list">
            @foreach ($jobs as $rowObj)
                @php
                    $job = (object) $rowObj;
                    $jobId = (int) ($job->id ?? 0);
                    $rawStatus = strtolower((string) ($job->status ?? 'active'));

                    // Map canonical status & visual CSS classes
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

                    // Pre-resolved branding from ViewModel or fallback
                    $logoData = isset($viewModel) && isset($viewModel->logoMap[$jobId]) 
                        ? $viewModel->logoMap[$jobId] 
                        : ['initial' => strtoupper(mb_substr(trim($job->title ?? 'J'), 0, 1)), 'color' => '#009ef7'];

                    // Employment type label
                    $empType = ((int) ($job->employment_type ?? 0)) === 1 ? 'Contract' : 'Full-time';

                    // Department & Company resolved from denormalized ES fields (0% MySQL Reads)
                    $deptName = $job->department_name ?? ($job->category_name ?? 'General Role');
                    $compName = $job->client_company ?? ($job->company_name ?? ($job->client_name ?? 'Client Account'));

                    // Formatted Salary
                    $minSal = !empty($job->min_salary) ? GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($job->min_salary)) : '0';
                    $maxSal = !empty($job->max_salary) ? GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($job->max_salary)) : '0';
                    $currSym = $job->min_salary_currency ?? '$';

                    // Pipeline Stages mapped from pre-aggregated ViewModel in O(1) time
                    $jobStageCounts = isset($viewModel) && isset($viewModel->pipelineStageMap[$jobId]) 
                        ? $viewModel->pipelineStageMap[$jobId] 
                        : [];
                @endphp

                <div class="cl-job-row job-row" data-entity="job" data-job-id="{{ $jobId }}">
                    <!-- Col 1: Job Branding & Details -->
                    <div class="cl-job-col-info">
                        <div class="cl-job-logo-box" style="background-color: {{ $logoData['color'] }};">
                            {{ $logoData['initial'] }}
                        </div>
                        <div class="cl-job-meta-title">
                            <!-- Semantic trigger with NO generic .show-detail class (Click Collision Resolved) -->
                            <a href="javascript:void(0);" 
                               class="cl-job-title-link js-job-drawer-trigger" 
                               data-entity="job" 
                               data-action="open-drawer" 
                               data-job-id="{{ $jobId }}" 
                               title="{{ $job->title }}">
                                {{ $job->title }}
                            </a>
                            <div class="cl-job-dept-text">{{ $deptName }}</div>
                            <div class="cl-job-client-tag">{{ $compName }}</div>
                        </div>
                    </div>

                    <!-- Col 2: Experience & Compensation -->
                    <div class="cl-job-col-exp">
                        <div class="cl-job-exp-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            <span>{{ $job->min_exp ?? 0 }} – {{ $job->max_exp ?? 0 }} Yrs</span>
                        </div>
                        <div class="cl-job-sal-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            <span>{{ $currSym }}{{ $minSal }} – {{ $maxSal }}</span>
                        </div>
                        <span class="cl-job-type-pill">{{ $empType }}</span>
                    </div>

                    <!-- Col 3: Status & Updated Timestamp -->
                    <div class="cl-job-col-status">
                        <span class="badge-hiring {{ $statusCls }}">{{ $statusLabel }}</span>
                        <span class="js-status-timestamp">Updated {{ !empty($job->updated_at) ? Carbon::parse($job->updated_at)->diffForHumans() : 'recently' }}</span>
                    </div>

                    <!-- Col 4: Compact Pipeline Strip -->
                    <div class="cl-job-col-pipeline">
                        @foreach ($pipelineStages as $stage)
                            @php
                                $stageCount = $jobStageCounts[$stage['id']] ?? 0;
                            @endphp
                            <div class="cl-pipeline-pill" title="{{ $stage['label'] }} Candidates">
                                <span class="cl-pipeline-label">{{ $stage['label'] }}</span>
                                <span class="cl-pipeline-count">{{ $stageCount }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Col 5: Actions -->
                    <div class="cl-job-col-actions">
                        <button type="button" 
                                class="btn btn-sm btn-icon btn-light-primary js-job-drawer-trigger" 
                                data-entity="job" 
                                data-action="open-drawer" 
                                data-job-id="{{ $jobId }}" 
                                title="View Job Details">
                            <i class="fas fa-chevron-right fs-7"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Metronic Empty State (Zero-Jobs) -->
        <div class="cl-jobs-empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
            <div class="cl-jobs-empty-title">No Job Requisitions Found</div>
            <div class="cl-jobs-empty-desc">
                No active or matching job requisitions are recorded under this client view.
            </div>
        </div>
    @endif
</div>
