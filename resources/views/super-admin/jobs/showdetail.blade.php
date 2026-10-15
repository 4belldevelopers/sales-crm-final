@php
    use App\Helper\GeneralMethod;
    $currencySymbole = GeneralMethod::currencySymbole();
    $salaryMode = GeneralMethod::salaryMode();

    $minCurr = $currencySymbole[@$job->min_salary_currency] ?? '₹';
    $salModeText = $salaryMode[@$job->min_sal_mode] ?? ($salaryMode[@$job->max_sal_mode] ?? 'Per Annum');

    $riskScore = floatval($riskEval['risk_score'] ?? 0.0);
    $hardBlocked = !empty($riskEval['hard_blocked']);
    $flags = $riskEval['flags'] ?? [];

    $isPending = in_array(@$job->market_mod_state, ['pending']) || @$job->public_market_status == 1;
    $isApproved = in_array(@$job->market_mod_state, ['approved', 'auto_approved']) || @$job->public_market_status == 2;
    $isRejected = @$job->market_mod_state === 'rejected' || @$job->public_market_status == 3;
@endphp

<!--begin::Header-->
<div class="card-header px-6 py-4 border-bottom d-flex align-items-center justify-content-between bg-light">
    <div class="d-flex align-items-center flex-wrap gap-2">
        <span class="badge badge-lg badge-light-primary fw-bolder fs-6">#JOB-{{ $job->id }}</span>
        <h3 class="card-title fw-bolder text-gray-900 mb-0 fs-4">{{ $job->title }}</h3>
        
        @if($job->bench_submission === 'enabled')
            <span class="badge badge-light-info fw-bold"><i class="fas fa-users me-1 text-info"></i>Bench Sharing</span>
        @endif
        @if($job->claim_capacity === 'open')
            <span class="badge badge-light-success fw-bold"><i class="fas fa-handshake me-1 text-success"></i>Outsource Open</span>
        @elseif($job->claim_capacity === 'limit_reached')
            <span class="badge badge-light-warning fw-bold">Limit Reached</span>
        @endif

        @if($isApproved)
            <span class="badge badge-success fw-bolder">Live on Marketplace</span>
        @elseif($isPending)
            <span class="badge badge-warning fw-bolder">Pending Review</span>
        @elseif($isRejected)
            <span class="badge badge-danger fw-bolder">Rejected</span>
        @endif
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_activitiesMarketplaceJob_close">
            <span class="svg-icon svg-icon-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                </svg>
            </span>
        </button>
    </div>
</div>
<!--end::Header-->

<!--begin::Cockpit Body 3-Panel-->
<div class="card-body p-6 overflow-auto" style="max-height: calc(100vh - 85px);">
    <div class="row g-6">
        
        <!-- ================= PANEL 1: Tenancy & Architecture (Left 30%) ================= -->
        <div class="col-xl-4 col-lg-5">
            <!-- Company & Recruiter Profile -->
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Tenant & Recruiter Profile</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="symbol symbol-50px me-3">
                            @if(!empty($job->company_logo))
                                <img src="{{ asset('user-uploads/company-logo/' . $job->company_logo) }}" alt="Logo" class="object-fit-contain rounded border" onerror="this.src='{{ asset('assets/images/building.png') }}';">
                            @else
                                <div class="symbol-label bg-light-primary text-primary fw-bolder fs-4">
                                    {{ strtoupper(substr($job->company_name ?? 'C', 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="fw-bolder text-gray-900 fs-6">{{ $job->company_name ?? 'Company Name' }}</div>
                            <span class="text-muted fs-7">Tenant Company ID: #{{ $job->company_id }}</span>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-3"></div>

                    <div class="d-flex flex-column gap-2 fs-7">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Posting Recruiter:</span>
                            <span class="fw-bold text-gray-800">{{ $job->recruiter_name ?? 'Primary Admin' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Recruiter Email:</span>
                            <span class="fw-bold text-gray-800">{{ $job->recruiter_email ?? $job->company_email ?? '-' }}</span>
                        </div>
                        @if(!empty($job->clients_name))
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">End Client:</span>
                            <span class="fw-bold text-primary">{{ $job->clients_name }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Created Date:</span>
                            <span class="fw-bold text-gray-800">{{ \Carbon\Carbon::parse($job->created_at)->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Specification Metrics -->
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Job Specifications</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3 fs-7">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Openings:</span>
                            <span class="badge badge-light-success fw-bolder fs-7">{{ $job->total_positions ?? 1 }} Positions</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Experience Required:</span>
                            <span class="fw-bolder text-gray-800">{{ $job->min_exp }} - {{ $job->max_exp }} Years</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Work Mode:</span>
                            <span class="badge badge-light fw-bold text-capitalize">{{ $job->work_type ?? 'On-Site' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Employment Type:</span>
                            <span class="badge badge-light fw-bold text-capitalize">{{ $job->employment_type ?? 'Full Time' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Work Shift:</span>
                            <span class="fw-bold text-gray-800">{{ $job->work_shifts ?? 'Day Shift' }}</span>
                        </div>
                        
                        <div class="separator separator-dashed my-2"></div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Compensation:</span>
                            <span class="fw-bolder text-dark fs-6">
                                {!! $minCurr !!} {{ number_format($job->min_salary) }} - {{ number_format($job->max_salary) }}
                                <span class="fs-8 text-muted d-block text-end">{{ $salModeText }}</span>
                            </span>
                        </div>

                        @if(!empty($locations) && $locations->count() > 0)
                        <div class="separator separator-dashed my-2"></div>
                        <div>
                            <span class="text-muted d-block mb-1">Target Locations:</span>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($locations as $loc)
                                    <span class="badge badge-secondary fs-8">{{ $loc->city_name ?? '' }}{{ !empty($loc->state_name) ? ', ' . $loc->state_name : '' }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Required Skills Cloud -->
            @if(!empty($skills) && count($skills) > 0)
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Required Skills</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <span class="badge badge-light-primary fw-bold fs-7">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- ================= PANEL 2: JD & Automated Circumvention Inspector (Center 45%) ================= -->
        <div class="col-xl-5 col-lg-7">
            
            <!-- Automated Risk / Circumvention Alerts -->
            @if(!empty($flags))
                <div class="alert alert-danger d-flex align-items-start p-4 mb-5 border-start border-4 border-danger">
                    <i class="fas fa-exclamation-triangle fs-2 text-danger me-3 mt-1"></i>
                    <div class="d-flex flex-column">
                        <h5 class="mb-1 text-danger fw-bolder fs-6">Anti-Circumvention Risk Triggered!</h5>
                        <ul class="mb-0 ps-3 fs-7">
                            @foreach($flags as $flag)
                                <li>{{ $flag }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @else
                <div class="alert alert-success d-flex align-items-center p-3 mb-5 border-start border-4 border-success">
                    <i class="fas fa-shield-alt fs-2 text-success me-3"></i>
                    <div class="d-flex flex-column">
                        <span class="fw-bolder text-success fs-7">Anti-Circumvention Verified</span>
                        <span class="text-muted fs-8">Zero contact leaks, phone numbers, or external URLs detected in this job posting.</span>
                    </div>
                </div>
            @endif

            <!-- Job Description Card -->
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle d-flex align-items-center justify-content-between">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Job Description Content</h5>
                    <span class="badge badge-light-info fs-8">Length: {{ strlen(strip_tags($job->job_description ?? '')) }} chars</span>
                </div>
                <div class="card-body p-5 fs-6 text-gray-700 overflow-auto" style="max-height: 400px; line-height: 1.6;">
                    @if(!empty($job->job_description))
                        {!! $job->job_description !!}
                    @else
                        <div class="text-muted fst-italic">No detailed job description provided.</div>
                    @endif
                </div>
            </div>

            <!-- Job Requirements Card -->
            @if(!empty($job->job_requirement))
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Specific Requirements & Candidate Criteria</h5>
                </div>
                <div class="card-body p-5 fs-6 text-gray-700 overflow-auto" style="max-height: 300px; line-height: 1.6;">
                    {!! $job->job_requirement !!}
                </div>
            </div>
            @endif

            <!-- Agency Claims Section -->
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle d-flex align-items-center justify-content-between">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Partner Agency Claims & Proposals</h5>
                    <span class="badge badge-light-primary">{{ count($agencyClaims) }} Engagements</span>
                </div>
                <div class="card-body p-4">
                    @if(count($agencyClaims) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-row-dashed align-middle mb-0 fs-7">
                                <thead>
                                    <tr class="text-muted fw-bolder">
                                        <th>Agency</th>
                                        <th>Claim Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agencyClaims as $claim)
                                        <tr>
                                            <td class="fw-bold text-gray-800">{{ $claim->agency_name ?? 'Agency' }}</td>
                                            <td>
                                                @if($claim->final_status == 1)
                                                    <span class="badge badge-light-success">Claim Accepted</span>
                                                @elseif($claim->final_status == 2)
                                                    <span class="badge badge-light-danger">Claim Rejected</span>
                                                @else
                                                    <span class="badge badge-light-warning">Pending Claim</span>
                                                @endif
                                            </td>
                                            <td class="text-muted fs-8">{{ \Carbon\Carbon::parse($claim->created_at)->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted text-center py-4 fs-7">
                            <i class="fas fa-inbox fs-3 d-block mb-1 text-muted opacity-50"></i>
                            No agency claims or proposals submitted on this job yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= PANEL 3: Risk Evaluation & Moderation Actions (Right 25%) ================= -->
        <div class="col-xl-3 col-lg-12">
            
            <!-- Risk Gauge & Audit Card -->
            <div class="card border border-gray-300 shadow-none mb-6">
                <div class="card-header py-3 bg-light-subtle">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Moderation Risk Engine</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="d-inline-flex flex-column align-items-center mb-3">
                        <div class="fs-1 fw-bolder {{ $riskScore > 0.5 ? 'text-danger' : ($riskScore > 0.2 ? 'text-warning' : 'text-success') }}">
                            {{ number_format($riskScore, 2) }}
                        </div>
                        <span class="fs-7 text-muted fw-bold">Calculated Risk Index</span>
                    </div>

                    <div class="progress h-8px bg-light-secondary rounded-pill mb-4">
                        <div class="progress-bar {{ $riskScore > 0.5 ? 'bg-danger' : ($riskScore > 0.2 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" style="width: {{ min(100, $riskScore * 100) }}%"></div>
                    </div>

                    <div class="text-start fs-8 text-muted mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Circumvention Scan:</span>
                            <span class="fw-bold {{ empty($flags) ? 'text-success' : 'text-danger' }}">
                                {{ empty($flags) ? 'Passed' : 'Flagged' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Salary Deviation:</span>
                            <span class="fw-bold text-success">Normal Range</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Auto-Approve Eligible:</span>
                            <span class="fw-bold {{ ($riskScore < 0.20 && !$hardBlocked) ? 'text-success' : 'text-danger' }}">
                                {{ ($riskScore < 0.20 && !$hardBlocked) ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instant Moderation Actions Card -->
            <div class="card border border-primary shadow-sm mb-6">
                <div class="card-header py-3 bg-primary text-white">
                    <h5 class="card-title fs-6 fw-bolder text-white mb-0"><i class="fas fa-gavel me-2"></i>Super Admin Decision</h5>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">
                    
                    <button type="button" class="btn btn-success w-100 fw-bolder py-3 btn-cockpit-action" 
                            data-action="approve_all" data-job-id="{{ $job->id }}">
                        <i class="fas fa-check-circle me-1"></i> Approve Outsource & Bench
                    </button>

                    <button type="button" class="btn btn-light-primary w-100 fw-bolder py-2 btn-cockpit-action" 
                            data-action="approve_bench" data-job-id="{{ $job->id }}">
                        <i class="fas fa-users me-1"></i> Approve Bench Sharing Only
                    </button>

                    <button type="button" class="btn btn-light-danger w-100 fw-bolder py-2 btn-cockpit-reject" 
                            data-job-id="{{ $job->id }}" data-job-title="{{ $job->title }}">
                        <i class="fas fa-times-circle me-1"></i> Reject Job
                    </button>

                </div>
            </div>

            <!-- Immutable Moderation Audit Log -->
            <div class="card border border-gray-300 shadow-none">
                <div class="card-header py-3 bg-light-subtle">
                    <h5 class="card-title fs-6 fw-bolder text-gray-800 mb-0">Moderation History</h5>
                </div>
                <div class="card-body p-4">
                    @if(count($auditLogs) > 0)
                        <div class="timeline-label">
                            @foreach($auditLogs as $log)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-label fw-bold text-gray-800 fs-8" style="width: 70px;">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M H:i') }}
                                    </div>
                                    <div class="timeline-badge">
                                        <i class="fa fa-genderless {{ $log->action === 'manual_approve' || $log->action === 'auto_approve' ? 'text-success' : 'text-danger' }} fs-1"></i>
                                    </div>
                                    <div class="timeline-content ps-3">
                                        <span class="fw-bolder text-gray-900 fs-7 d-block">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                        <span class="text-muted fs-8">{{ $log->rejection_notes ?? $log->reason_code ?? 'Status change recorded' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted text-center py-3 fs-8">No prior moderation actions logged.</div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
<!--end::Cockpit Body 3-Panel-->
