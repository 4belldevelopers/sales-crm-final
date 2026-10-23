@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = $salaryMode ?? GeneralMethod::salaryMode();
    $currencySymbole = $currencySymbole ?? GeneralMethod::currencySymbole();
    $job_status = $job_status ?? GeneralMethod::job_status();
@endphp
@if (!empty($data) && count($data) > 0)
    @php
        $categoryIds = collect($data)->pluck('category_id')->filter()->unique()->toArray();
        $categoriesMap = !empty($categoryIds) ? DB::table('job_categories')->select('id', 'name')->whereIn('id', $categoryIds)->get()->keyBy('id') : collect();
    @endphp
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $targetJobId = $row->id;
            $company_id = $row->comId ?? null;

            // Recommendation relation matching
            $match = collect($row->recommendation_relation ?? [])->first(function ($item) use ($company_id) {
                return ($item['from_company_id'] ?? null) == $company_id
                    || ($item['to_company_id'] ?? null) == $company_id;
            });
            $from_company_id = ($match['to_company_id'] ?? null) == $company_id ? ($match['from_company_id'] ?? null) : null;
            $recommendation_id = ($match['to_company_id'] ?? null) == $company_id ? ($match['to_company_id'] ?? null) : null;
            $final_status = ($match['to_company_id'] ?? null) == $company_id ? ($match['final_status'] ?? null) : null;

            $row->final_status = $final_status;
            $row->recommendation_id = $recommendation_id;
            $row->to_company_id = $recommendation_id;
            $row->from_company_id = $from_company_id;
            $row->companyidcount = $row->comId ?? null;
            $row->clients_name = $row->client_name ?? '';

            // Initials & Background decoration
            $str = trim(@$row->title ?? '');
            $strVal = strtolower($str[0] ?? 'j');
            $strAscii = ord($strVal);
            if ($strAscii >= 97 && $strAscii <= 101) {
                $bgTextDecore = 'bg-light-danger text-danger';
            } elseif ($strAscii >= 102 && $strAscii <= 106) {
                $bgTextDecore = 'bg-light-warning text-warning';
            } elseif ($strAscii >= 107 && $strAscii <= 111) {
                $bgTextDecore = 'bg-light-primary text-primary';
            } elseif ($strAscii >= 112 && $strAscii <= 116) {
                $bgTextDecore = 'bg-light-success text-success';
            } elseif ($strAscii >= 117 && $strAscii <= 121) {
                $bgTextDecore = 'bg-dark text-secondary';
            } else {
                $bgTextDecore = 'bg-light-secondary text-dark';
            }

            $profile_detail_class = 'show-detail';
            if ($row->recommendation_id > 0) {
                $profile_detail_class = 'client-show-detail';
            }

            $clients_name = ucwords(mb_substr(@$row->company_name ?: (@$row->clients_name ?: @$row->client_name), 0, 30));

            $logo_path = GeneralMethod::companyDetailsSupper($row->id);
            if (empty($logo_path) && !empty($row->logo_logical_path)) {
                $logo_path = $row->logo_logical_path;
            }

            // Department / Category
            $department = @$row->category_name;
            if (empty($department)) {
                $deptRow = $categoriesMap->get(@$row->category_id);
                $department = $deptRow->name ?? '';
            }
            if (empty($department)) {
                $department = 'Information Technology';
            }

            // Experience & Salary
            $min_sal_mode = strtoupper(mb_substr($salaryMode[$row->min_sal_mode ?? ''] ?? '', 0, 1));
            $max_sal_mode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode ?? ''] ?? '', 0, 1));
            $min_salary_currency = $currencySymbole[@$row->min_salary_currency] ?? '₹';
            $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary ?? 0);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary ?? 0);
            $max_salary = GeneralMethod::formatAmount($max_salary);
            $min_salary = GeneralMethod::formatAmount($min_salary);

            $recommendation_text = $row->recommendation_id > 0 ? 'Client' : 'Custom';
            $badge = $row->recommendation_id > 0 ? 'badge-client' : 'badge-internal';
            $svg = '<svg width="9" height="9" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5"/>
                    <path d="M8 12h8M12 8v8" stroke="currentColor" stroke-width="2.5"/>
                  </svg>';

            // Status & Work Mode
            $status = ucwords($row->status ?? 'Active');
            $statusClass = $job_status[$row->status ?? ''] ?? '';
            if ($status == 'Active') {
                $status = 'Hiring';
            } elseif ($status == 'Inactive') {
                $status = 'Closed';
            }

            $ulink = '<span class="badge-hiring ' . @$statusClass . '">' . $status . '</span>';
            if (($row->final_status == 2 || $row->final_status == 3)) {
                $status = 'Removed';
                $statusClass = 'closed-bg';
                $ulink = '<span class="badge-hiring ' . @$statusClass . '">' . $status . '</span>';
            }

            $work_type = 'Office';
            if ($row->work_type == 'yes') {
                $work_type = 'Remote';
            } elseif ($row->work_type == 'other' || $row->work_type == 'any') {
                $work_type = 'Hybrid';
            }

            $employment_type = ($row->employment_type == 1) ? 'Contract' : 'Full-time';
            $created_at = $row->created_at ?? date('Y-m-d');
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at);

            // Skills
            $skilltag = '';
            if (!empty($row->job_metronics_skills)) {
                foreach ($row->job_metronics_skills as $sval) {
                    $sval = (object) $sval;
                    $sName = $sval->skill_name ?? ($sval->name ?? '');
                    if (!empty($sName)) {
                        $skilltag .= '<span class="badge badge-light my-1 me-1">' . htmlspecialchars(ucfirst($sName)) . '</span> ';
                    }
                }
            }
            if (empty($skilltag) && empty($limit)) {
                $skillArr = DB::table('job_metronics_skills')
                    ->select('skill_name')
                    ->where('job_id', $row->id)
                    ->where('company_id', '=', $row->comId)
                    ->get();
                if (!empty($skillArr)) {
                    foreach ($skillArr as $sval) {
                        $skilltag .= '<span class="badge badge-light my-1 me-1">' . htmlspecialchars(ucfirst($sval->skill_name)) . '</span> ';
                    }
                }
            }
            if (empty($skilltag)) {
                $skilltag = '<span class="text-muted" style="font-size: 11px;">Not specified</span>';
            }

            // Location
            $cityName = null;
            if (!empty($row->client_city_jobs)) {
                $firstCity = collect($row->client_city_jobs)->first();
                $cityName = is_array($firstCity) ? ($firstCity['city_name'] ?? null) : ($firstCity->city_name ?? null);
            }
            if (empty($cityName)) {
                $locRow = DB::table('client_city_jobs')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                    ->select('city_list.city as city_name')
                    ->where('client_city_jobs.job_id', $row->id)
                    ->first();
                $cityName = $locRow->city_name ?? null;
            }
            if (empty($cityName)) {
                $locRow = DB::table('client_location_jobs')
                    ->join('client_location', 'client_location.id', '=', 'client_location_jobs.client_location_id')
                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                    ->select('city_list.city as city_name', 'client_location.city as cl_city')
                    ->where('client_location_jobs.job_id', $row->id)
                    ->first();
                $cityName = $locRow->city_name ?? ($locRow->cl_city ?? 'Multiple');
            }
            $city = mb_substr($cityName ?: 'Multiple', 0, 15);

            // Hiring Manager
            $hiringManager = '--------';
            $hiringManagerdesignation = '';
            if (!empty($row->client_contact_info_jobs)) {
                $firstHm = collect($row->client_contact_info_jobs)->first();
                $hiringManager = is_array($firstHm) ? ($firstHm['contact_person_name'] ?? '--------') : ($firstHm->contact_person_name ?? '--------');
                $hiringManagerdesignation = is_array($firstHm) ? ($firstHm['contact_person_designation'] ?? '') : ($firstHm->contact_person_designation ?? '');
            }
            if ($hiringManager === '--------') {
                $hmRow = DB::table('client_contact_info_jobs')
                    ->join('client_contact_info', 'client_contact_info.id', '=', 'client_contact_info_jobs.client_contact_info_id')
                    ->where('client_contact_info_jobs.job_id', $row->id)
                    ->select('client_contact_info.contact_person_name', 'client_contact_info.contact_person_designation')
                    ->first();
                if ($hmRow) {
                    $hiringManager = $hmRow->contact_person_name;
                    $hiringManagerdesignation = $hmRow->contact_person_designation;
                }
            }

            // Team
            $teamLeadName = 'Unassigned';
            $teamMemberCount = 0;
            if (!empty($row->job_assign)) {
                $jobAssignColl = collect($row->job_assign);
                $teamMemberCount = $jobAssignColl->unique('user_id')->count();
                $firstMember = $jobAssignColl->first();
                $teamLeadName = is_array($firstMember) ? ($firstMember['user_name'] ?? '') : ($firstMember->user_name ?? '');
            }
            if (empty($teamLeadName) || $teamLeadName === 'Unassigned') {
                $partnersCount = DB::table('job_assign')
                    ->join('users', 'users.id', '=', 'job_assign.user_id')
                    ->where('job_assign.job_id', '=', $row->id)
                    ->where('job_assign.company_id', '=', $company_id)
                    ->where('users.company_id', '=', $company_id)
                    ->count();
                $partnersData = DB::table('job_assign')
                    ->join('users', 'users.id', '=', 'job_assign.user_id')
                    ->select('users.name')
                    ->where('job_assign.job_id', '=', $row->id)
                    ->where('job_assign.company_id', '=', $company_id)
                    ->where('users.company_id', '=', $company_id)
                    ->orderBy('users.name', 'ASC')
                    ->first();
                if ($partnersData) {
                    $teamLeadName = $partnersData->name;
                    $teamMemberCount = $partnersCount;
                }
            }
            $teamMemberCountText = ($teamMemberCount > 1) ? '+' . ($teamMemberCount - 1) . ' Members' : ($teamMemberCount . ' Member');

            // Agency
            $agencyCountData = GeneralMethod::agencyCount($company_id, $row->id, 1);
            $agencyCountNum = is_countable($agencyCountData) ? count($agencyCountData) : 0;
            $matchNumber = !empty($row->recommendation_relation) ? count($row->recommendation_relation) : 0;

            // Pipeline Counters
            $applicationCounter = DB::table('add_job_wise_condidates as job_applications')
                ->select('job_applications.id')
                ->where('job_applications.job_id', '=', $row->id)
                ->where('job_applications.thirdparty_company_id', '=', 0)
                ->orWhere(function ($query) use ($targetJobId) {
                    return $query
                        ->where('job_applications.thirdparty_company_id', '>', 0)
                        ->where('job_applications.job_id', '=', $targetJobId)
                        ->where('job_applications.master_status_id', '!=', 8)
                        ->where('job_applications.master_status_id', '!=', 9);
                })
                ->count();

            $inreview_counter = DB::table('add_job_wise_condidates as job_applications')
                ->select('job_applications.id')
                ->where('job_applications.job_id', '=', $row->id)
                ->where('job_applications.thirdparty_company_id', '=', 0)
                ->where('job_applications.master_status_id', '=', 8)
                ->count();

            $cnt_sou = GeneralMethod::statusCount($row->companyidcount, $row->id, 1);
            $cnt_scr = GeneralMethod::statusCount($row->companyidcount, $row->id, 2);
            $cnt_eva = GeneralMethod::statusCount($row->companyidcount, $row->id, 3);
            $cnt_neg = GeneralMethod::statusCount($row->companyidcount, $row->id, 4);
            $cnt_off = GeneralMethod::statusCount($row->companyidcount, $row->id, 5);
            $cnt_joi = GeneralMethod::statusCount($row->companyidcount, $row->id, 6);
            $cnt_rej = GeneralMethod::statusCount($row->companyidcount, $row->id, 7);

            // Salary confidentiality check
            $isSalConf = (\App\Services\SalaryVisibilityPolicy::normalize(@$row->salary_visibility) === \App\Services\SalaryVisibilityPolicy::FULLY_CONFIDENTIAL)
                || ((empty($row->min_salary) || (float)$row->min_salary === 0.0) && (empty($row->max_salary) || (float)$row->max_salary === 0.0) && (@$row->salary_visibility === 'confidential'));
        @endphp

        <!-- ══ JOB ROW ══ -->
        <div class="job-row" data-job-id="{{ $row->id }}" data-status="{{ strtolower($row->status ?? '') }}">
            <!-- Col 1: Job Details -->
            <div style="display: flex; gap: 8px; align-items: flex-start">
                <div class="drag-col">
                    <div class="drag-handle" aria-hidden="true" title="Reorder job">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24">
                            <circle cx="9" cy="5" r="1.5" fill="currentColor" />
                            <circle cx="15" cy="5" r="1.5" fill="currentColor" />
                            <circle cx="9" cy="12" r="1.5" fill="currentColor" />
                            <circle cx="15" cy="12" r="1.5" fill="currentColor" />
                            <circle cx="9" cy="19" r="1.5" fill="currentColor" />
                            <circle cx="15" cy="19" r="1.5" fill="currentColor" />
                        </svg>
                    </div>
                </div>
                <div class="col-job {{ $profile_detail_class }}" data-row-id="{{ $row->id }}" tabindex="0" role="button" aria-label="View details for job {{ @$row->title }}">
                    <div class="job-logo {{ $bgTextDecore }}" aria-hidden="true">
                        @if(!empty($logo_path))
                            <img src="{{ str_replace('index.php', '', @$logo_path) }}" alt="Company Logo" class="border-radius-5 w-100">
                        @else
                            {{ ucwords($strVal) }}
                        @endif
                    </div>
                    <div class="job-info">
                        <div class="job-title" data-toggle="tooltip1" title="{{ ucwords(@$row->title ?? '') }}">{{ ucwords(mb_substr(@$row->title ?? '', 0, 30)) }}</div>
                        <div class="job-dept">{{ @$department }}</div>
                        <div class="job-client">{{ $clients_name }}</div>
                    </div>
                </div>
            </div>

            <!-- Col 2: Experience & Salary -->
            <div class="col-exp">
                <div class="exp-row">
                    <svg fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                        <polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    {{ @$row->min_exp }} - {{ @$row->max_exp }} Years
                </div>
                <div class="sal-row">
                    @if($isSalConf)
                        <span class="sal-amount sal-amount-confidential" style="font-weight: 500; color: #dc2626; display: inline-flex; align-items: center; gap: 4px;"><i class="fas fa-lock" aria-hidden="true" style="font-size: 10px; color: #dc2626;"></i> Confidential</span>
                    @else
                        <svg fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="2" y="6" width="20" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M6 12h.01M18 12h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        {!! $min_salary_currency !!} {{ $min_salary }} - {{ $max_salary }} {{ $min_sal_mode }}
                    @endif
                </div>
                <span class="badge-type">
                    {!! $svg !!}
                    <span class="it-job-type {{ $badge }}">{{ $recommendation_text }}</span>
                    <span class="it-dot-separator" aria-hidden="true"></span>
                    <a href="javascript:void(0);" class="it-job-id" aria-label="Job ID {{ @$row->id }}">{{ @$row->id }}</a>
                </span>
            </div>

            <!-- Col 3: Hiring & Location -->
            <div class="col-hl">
                {!! $ulink !!}
                <div class="loc-row">
                    <svg fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8"/>
                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    {{ ucwords(mb_substr(@$city, 0, 15)) }}
                </div>
                <div class="mode-row">
                    <svg fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8"/>
                        <polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    {{ $work_type }}
                </div>
            </div>

            <!-- Col 4: Hiring Manager -->
            <div class="col-hm">
                <div class="hm-name">{{ ucwords(mb_substr(@$hiringManager, 0, 15)) }}</div>
                <div class="hm-title">{{ @$hiringManagerdesignation ?: 'Hiring Manager' }}</div>
                <div class="hm-company">{{ ucwords(@$employment_type) }}</div>
            </div>

            <!-- Col 5: Team -->
            <div class="col-action">
                <div class="hm-name" data-row-id="{{ $row->id }}">{{ ucwords(mb_substr(@$teamLeadName, 0, 15)) }}</div>
                <div class="action-count">{{ $teamMemberCountText }}</div>
            </div>

            <!-- Col 6: Agency -->
            <div class="col-action">
                @if($agencyCountNum > 0)
                    <div class="hm-name">{{ $agencyCountNum }} {{ __('messages.Agency') }}</div>
                    <div class="action-count">{{ $matchNumber > 0 ? $matchNumber : $agencyCountNum }} Matching</div>
                @else
                    <div class="hm-name text-muted">No Agency</div>
                    <div class="action-count">{{ $matchNumber > 0 ? $matchNumber : 0 }} Matching</div>
                @endif
            </div>

            <!-- Col 7: Pipeline -->
            <div class="col-pipeline">
                <div class="pipeline-stages">
                    <div class="stage">
                        <div class="stage-label">INW</div>
                        <div class="stage-badge s-inw"><a href="javascript:;" class="s-inw fw-bolder fs-6" aria-label="Inward stage: {{ $inreview_counter }} applications">{{ $inreview_counter }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">SOU</div>
                        <div class="stage-badge s-sou"><a href="javascript:;" class="s-sou fs-6" aria-label="Sourced stage: {{ @$cnt_sou }} candidates">{{ @$cnt_sou }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">SCR</div>
                        <div class="stage-badge s-scr"><a href="javascript:;" class="s-scr fs-6" aria-label="Screened stage: {{ @$cnt_scr }} candidates">{{ @$cnt_scr }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">EVA</div>
                        <div class="stage-badge s-eva"><a href="javascript:;" class="s-eva fs-6" aria-label="Evaluated stage: {{ @$cnt_eva }} candidates">{{ @$cnt_eva }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">NEG</div>
                        <div class="stage-badge s-neg"><a href="javascript:;" class="s-neg fs-6" aria-label="Negotiation stage: {{ @$cnt_neg }} candidates">{{ @$cnt_neg }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">OFF</div>
                        <div class="stage-badge s-off"><a href="javascript:;" class="s-off fs-6" aria-label="Offered stage: {{ @$cnt_off }} candidates">{{ @$cnt_off }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">JOI</div>
                        <div class="stage-badge s-joi"><a href="javascript:;" class="s-joi fs-6" aria-label="Joined stage: {{ @$cnt_joi }} candidates">{{ @$cnt_joi }}</a></div>
                    </div>
                    <div class="stage">
                        <div class="stage-label">REJ</div>
                        <div class="stage-badge s-rej"><a href="javascript:;" class="s-rej fs-6" aria-label="Rejected stage: {{ @$cnt_rej }} candidates">{{ @$cnt_rej }}</a></div>
                    </div>
                </div>
                <div class="pipeline-total">
                    Total: <strong>{{ number_format($applicationCounter) }} Applications</strong> &nbsp;|&nbsp;
                    Interview: <strong>{{ @$cnt_eva }}</strong>
                </div>
                <div class="add-date" style="display:flex; justify-content: flex-end; align-items: center; gap: 4px;">
                    <svg fill="none" viewBox="0 0 24 24" aria-hidden="true" style="width: 12px; height: 12px; color: #a1a5b7;">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    <span>{{ $newDate }}</span>
                </div>
            </div>

            <!-- Skills at bottom line -->
            <div style="position: absolute; bottom: 5px; left: 30px; white-space: nowrap; width: 90%; overflow: hidden;">
                <div class="job-meta-line">
                    <span class="skills-inline">
                        <b>Skills:</b> {!! $skilltag !!}
                    </span>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div style="text-align: center; color: #a1a5b7; padding: 40px 12px; font-size: 13px;">
        No data available in table
    </div>
@endif
