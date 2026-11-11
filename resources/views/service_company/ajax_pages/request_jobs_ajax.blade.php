@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
@endphp

@if (!empty($data) && count($data) > 0)
    <div class="cd-jobs-container">
        @foreach ($data as $rowArr)
            @php
                $row = (object) $rowArr;
                $row->id = $row->job_id;
                // ── 0% in-Blade DB reads: retrieved from ES projection or batch-prehydrated map ──
                $clientLocCity = @$row->city_name ?: (@$locationsMap[$row->id]->city_name ?? '');
                $clientLocCountry = @$row->country_name ?: (@$locationsMap[$row->id]->country_name ?? '');
                $city = mb_substr($clientLocCity, 0, 21);
                $country_name = mb_substr($clientLocCountry, 0, 6);

                $profile_detail_class = "client-show-detail";
                $bgTextDecore = $strVal = '';
                $str = trim(@$row->title);
                $str = $strVal = strtolower($str[0] ?? 'j');
                $strOrd = ord($str);
                if ($strOrd >= 97 && $strOrd <= 101) {
                    $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                } elseif ($strOrd >= 102 && $strOrd <= 106) {
                    $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                } elseif ($strOrd >= 107 && $strOrd <= 111) {
                    $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                } elseif ($strOrd >= 112 && $strOrd <= 116) {
                    $bgTextDecore = 'fs-3 bg-light-success text-success';
                } elseif ($strOrd >= 117 && $strOrd <= 121) {
                    $bgTextDecore = 'fs-3 bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                }
                $logo_path = '';
                if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                }
                
                $department = @$row->department ?: (@$categoriesMap[@$row->category_id] ?? 'Information Technology');

                $min_sal_mode = ['/Annum', '/Month', '/Hour'][$row->min_sal_mode ?? 0] ?? '';
                $min_salary_currency = $currencySymbole[@$row->min_salary_currency] ?? '$';

                $max_salary = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($row->max_salary ?? 0));
                $min_salary = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($row->min_salary ?? 0));

                $status = ucwords($row->status ?? 'active');
                if ($status == 'Active') {
                    $status = 'Hiring';
                } elseif ($status == 'Inactive') {
                    $status = 'Closed';
                }

                $statusCls = 'status-hiring';
                if ($status === 'Hold') {
                    $statusCls = 'status-hold';
                } elseif ($status === 'Closed') {
                    $statusCls = 'status-closed';
                }

                $recommendation_id = $row->recommendation_relation_id ?? 0;
                $isDirectClientJob = ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id);

                if (
                    ($row->final_status == 2 || $row->final_status == 3) &&
                    @$row->to_company_id == auth()->user()->company_id
                ) {
                    $status = 'Removed';
                    $statusCls = 'status-inactive';
                }
                
                $work_type = 'Work From Office';
                if (@$row->work_type == 'yes' || @$row->work_type == 'remote') {
                    $work_type = "Remote";
                } elseif (@$row->work_type == 'other' || @$row->work_type == 'hybrid') {
                    $work_type = "Hybrid";
                }
                
                $employment_type = ((int) (@$row->employment_type ?? 0) === 1) ? 'Contract' : 'Full-Time';

                $hiringManager = '--------';
                $hiringManagerdesignation = '--------';
                            
                $captain = @$captainsMap[$row->id] ?? null;
                if ($captain) {
                    $hiringManager = $captain->name ?? '--------';
                    $hiringManagerdesignation = $captain->designation ?? '--------';
                } elseif (!empty(@$row->hiring_manager)) {
                    $hiringManager = $row->hiring_manager;
                    $hiringManagerdesignation = $row->hiring_manager_designation ?? '--------';
                }

                $isSalConf = (\App\Services\SalaryVisibilityPolicy::normalize(@$row->salary_visibility) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL)
                    || ((empty($row->min_salary) || (float)$row->min_salary === 0.0) && (empty($row->max_salary) || (float)$row->max_salary === 0.0) && (@$row->salary_visibility === 'confidential'));

                $total_positions = number_format((float) (@$row->total_positions ?? 1));
                $newDate = GeneralMethod::ymdTodmyNotTime($row->created_at ?? now());

                $skills = $row->skillArr ?? [];
            @endphp

            <!-- Master Primary Card for Job Request -->
            <div class="cd-job-card js-job-card-clickable" data-entity="job" data-job-id="{{ $row->id }}" style="border-left: 3.5px solid #8b5cf6 !important; cursor: pointer;">
                <!-- Upper Sub-Cards Grid (5 Sections) -->
                <div class="cd-job-card-grid">
                    <!-- Sub-Card 1: Job Details -->
                    <div class="cd-job-subcard cd-subcard-details">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Job Details</span>
                            <div class="cd-header-badges">
                                <span class="cd-badge-client-type type-direct"><i class="fas fa-handshake me-1"></i>Direct Client</span>
                                <span class="cd-status-badge {{ $statusCls }}">{{ $status }}</span>
                            </div>
                        </div>
                        <div class="cd-details-body">
                            <div class="cd-logo-box {{ $bgTextDecore }}">
                                @if(!empty($logo_path)) 
                                    <img src="{{ str_replace('index.php', '', @$logo_path) }}" alt="Company Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span style="display:none; color: #009ef7; font-weight: 700;">{{ strtoupper($strVal) }}</span>
                                @else
                                    <span style="color: #009ef7; font-weight: 700;">{{ strtoupper($strVal) }}</span>
                                @endif
                            </div>
                            <div class="cd-details-text">
                                <a href="javascript:void(0);" class="cd-job-title {{ $profile_detail_class }}" data-row-id="{{ $row->id }}" title="{{ $row->title }}">
                                    {{ ucwords(mb_substr(@$row->title, 0, 32)) }}
                                </a>
                                <div class="cd-job-meta">
                                    <span class="cd-job-id">#{{ $row->id }}</span>
                                    <span class="cd-bullet">&bull;</span>
                                    <span class="cd-job-dept" title="{{ $department }}">{{ $department }}</span>
                                </div>
                                <div class="cd-job-client-row">
                                    <span class="cd-job-client-name" title="{{ @$row->company_name }}">{{ ucwords(mb_substr(@$row->company_name, 0, 20)) }}</span>
                                    <span class="cd-bullet">&bull;</span>
                                    <span class="cd-job-emp-type"><i class="fas fa-briefcase text-primary me-1"></i>{{ $employment_type }}</span>
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
                                @if($isSalConf)
                                    <span class="text-danger" style="font-weight: 600;"><i class="fas fa-lock me-1"></i>Confidential</span>
                                @else
                                    <strong>{{ $min_salary_currency }} {{ $min_salary }} - {{ $max_salary }} {{ $min_sal_mode }}</strong>
                                @endif
                            </div>
                            <div class="cd-val-exp">
                                <i class="far fa-clock text-muted me-1"></i><span>🕒 {{ @$row->min_exp }} - {{ @$row->max_exp }} Yrs</span>
                            </div>
                            <div class="cd-val-loc" title="{{ $city }} &bull; {{ $work_type }}">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ ucwords(mb_substr(@$city, 0, 16)) }}
                                <span class="cd-bullet">&bull;</span>
                                <span>{{ $work_type }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 3: Hiring Manager -->
                    <div class="cd-job-subcard cd-subcard-team">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fa fa-user-tie me-1 text-muted"></i>Hiring Manager</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-val-name client-hm-listing" data-row-id="{{ $row->id }}" title="{{ $hiringManager }}">
                                <strong>{{ ucwords(mb_substr(@$hiringManager, 0, 18)) }}</strong>
                            </div>
                            <div class="cd-val-sub">{{ $hiringManagerdesignation !== '--------' ? $hiringManagerdesignation : 'Client SPOC' }}</div>
                            <a href="javascript:void(0);" class="cd-action-link client-hm-listing" data-row-id="{{ $row->id }}">+ View HM Details</a>
                        </div>
                    </div>

                    <!-- Sub-Card 4: Positions & Terms -->
                    <div class="cd-job-subcard cd-subcard-agency">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label"><i class="fa fa-file-contract me-1 text-muted"></i>Positions &amp; Terms</span>
                        </div>
                        <div class="cd-subcard-body">
                            <div class="cd-agency-val"><strong>{{ $total_positions }} Openings</strong></div>
                            <div data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}" class="client-terms-listing mt-1">
                                <a href="javascript:;" class="btn btn-xs btn-light-primary py-1 px-2" style="font-size: 11px;">
                                    <i class="fas fa-file-contract me-1"></i>View Terms
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 5: Approval / Action -->
                    <div class="cd-job-subcard cd-subcard-pipeline">
                        <div class="cd-subcard-header">
                            <span class="cd-subcard-label">Request Status / Action</span>
                        </div>
                        <div class="cd-subcard-body d-flex flex-column justify-content-center">
                            @if ($row->final_status == 1)
                                <div><span class="badge badge-light-success fw-bold py-2 px-3 fs-7"><i class="fas fa-check-circle text-success me-1"></i>Accepted</span></div>
                            @elseif ($row->final_status == -1)
                                <div><span class="badge badge-light-danger fw-bold py-2 px-3 fs-7"><i class="fas fa-times-circle text-danger me-1"></i>Rejected</span></div>
                            @elseif ($row->final_status == 2)
                                <div><span class="badge badge-light-secondary fw-bold py-2 px-3 fs-7">Removed</span></div>
                            @elseif ($row->final_status == 3)
                                <div><span class="badge badge-light-danger fw-bold py-2 px-3 fs-7">Expired</span></div>
                            @else
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <button type="button" class="btn btn-sm btn-primary request_approval fw-bold py-1 px-3" data-row-status="accept" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">
                                        <i class="fas fa-check me-1"></i>Accept
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light-danger request_reject fw-bold py-1 px-3" data-row-status="ignore" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 6: Bottom Activity Strip -->
                <div class="cd-job-card-bottom">
                    <div class="cd-bottom-left">
                        <span><i class="far fa-calendar-alt text-muted me-1"></i>Requested on: <strong>{{ $newDate }}</strong></span>
                        @if(!empty($hiringManager) && $hiringManager !== '--------')
                            <span class="cd-bullet">&bull;</span>
                            <span><i class="far fa-id-badge text-primary me-1"></i>Client Contact: <strong>{{ $hiringManager }}</strong></span>
                        @endif
                    </div>
                    <div class="cd-bottom-skills">
                        @if (!empty($skills))
                            @foreach ($skills as $sval)
                                <span class="cd-skill-pill">{{ ucfirst($sval) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted" style="font-size: 10.5px;">No key skills specified</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="cd-jobs-empty text-center py-16 px-6 my-4" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;">
        <i class="fas fa-handshake fa-2x mb-3 text-muted d-block"></i>
        <div class="fw-bold fs-6 text-gray-700 mb-1">No Inbound Job Requests Found</div>
        <div class="text-muted fs-7">There are no pending or active client requisition requests matching your filter.</div>
    </div>
@endif
