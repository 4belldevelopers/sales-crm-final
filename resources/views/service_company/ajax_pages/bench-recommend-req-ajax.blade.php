@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @php
        /* Page-level batch lookups — one query each for the whole page
           instead of one per row (was 2 Elasticsearch searches + 2 MySQL
           queries per job card, ~40 round-trips a page).
           Tenancy: the relation map is scoped to the viewer's company inside
           the bulk helper; skills are filtered per-row by each job's own
           comId below, exactly as the old per-row query did. */
        $pageJobIds  = array_values(array_filter(array_map(fn ($r) => $r['id'] ?? null, $data)));
        $pageViewerCompanyId = auth()->user()->company_id;
        $relationMap = GeneralMethod::getrRcommendationRelationDataBulk($pageViewerCompanyId, $pageJobIds);
        $benchReqMap = GeneralMethod::benchRequestCheckBulk($condidate_id, $pageJobIds);
        $skillsByJob = DB::table('job_metronics_skills')->whereIn('job_id', $pageJobIds)->get()->groupBy('job_id');
    @endphp
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    $targetJobId = $row->id;
    $company_id = auth()->user()->company_id;

    $relationget = $relationMap[$targetJobId] ?? (object) [];
    $from_company_id = $relationget->from_company_id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    $recommendation_id = $relationget->id ?? null;
    $final_status = $relationget->final_status ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->recommendation_relation_id =$recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId;
    $row->clients_name = $row->client_name;

    /* Client-type detection — distinguishes an Internal-route recommendation
       (sister/internal company in the from→to chain) from a Direct Client
       recommendation (external). Used to render the reusable .client-badge
       below. Pure data-derivation, no new controller calls — the relation map
       is already loaded above for $to_company_id/$from_company_id. */
    $vcid         = (int) auth()->user()->company_id;
    $relFromCid   = (int) ($row->from_company_id ?? 0);
    $relToCid     = (int) ($row->to_company_id ?? 0);
    $isInternalCx = ($relFromCid === $vcid) || ($relToCid === $vcid);

    // Enforce Active status: strictly skip any non-active jobs
    if (isset($row->status) && strtolower((string) $row->status) !== 'active') {
        continue;
    }

    $clientTypeKey   = 'direct';
    $clientTypeLabel = 'Direct Client';
    /* The numeric suffix "Internal Client · 1410" is the job-owning company
       (comId) — same on both badges, so users scan "Internal\D" first, ID
       second. */
    $clientTypeId   = (string) ($row->comId ?? $row->companyidcount ?? '');
    if ($clientTypeId === '' || $clientTypeId === '0') { $clientTypeId = ''; }

    $bgTextDecore = $strVal = '';
        $str = trim(@$row->title);
        $str = $strVal = strtolower($str[0]);
        $str = ord($str);
        if ($str >= 97 && $str <= 101) {
            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
        } elseif ($str >= 102 && $str <= 106) {
            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
        } elseif ($str >= 107 && $str <= 111) {
            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
        } elseif ($str >= 112 && $str <= 116) {
            $bgTextDecore = 'fs-3 bg-light-success text-success';
        } elseif ($str >= 117 && $str <= 121) {
            $bgTextDecore = 'fs-3 bg-dark text-secondary';
        } else {
            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
        }

        $country_id = $country_name = '';
        $jobid = $row->id ?? null;

        // Safely retrieve client_city_jobs using null coalescing operator ?? in case $row is an stdClass without this property
        $client_city_jobs = $row->client_city_jobs ?? [];
        $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;

        $employment_type = match ((int) ($row->employment_type ?? 0)) {
            1 => 'Contract',
            2 => 'Freelance',
            3 => 'Part-Time',
            default => 'Full-Time',
        };
        $employmentBadgeClass = ($employment_type === 'Contract') ? 'badge-inline badge-contract' : 'badge-inline badge-fulltime';

        $classAdd = "bench-match";
        $textAdd = "Bench Required";
        $textClassAdd = "bench-badge";

        $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';

        $mod = $sal_mode ?? 0;
        $cur = $min_salary_currency ?? 0;

        $min_sal_mode = $salaryMode[$mod];
        $max_sal_mode = $salaryMode[$mod];
        $salary_currency = $currencySymbole[@$cur];
        $currencymain = GeneralMethod::currency();
        $currency = $currencymain[$cur];
        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->max_yearly, 0);
        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->min_yearly, 0);

        $ectc = $convertedAmounEctc[$mod];
        $ctc = $convertedAmounCtc[$mod];

        $max_salary = '';
        $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
        $min_salary = '';
        $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

        $work_type = 'Work From Office';
        if($row->work_type=='no'){
            $work_type = "Work From Office";
        }elseif($row->work_type=='yes'){
            $work_type = "Remote";
        }elseif($row->work_type=='other'){
            $work_type = "Hybrid";
        }elseif($row->work_type=='any'){
            $work_type = "Any";
        }
        /* Work-mode badge class + icon — same icon sizing/color as the rest of
           the .job-meta-line pills in JOB DETAILS, so all inline badges share
           one visual rhythm. */
        if ($work_type === 'Remote') {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-remote';
            $workTypeIcon = 'ri-home-wifi-line';
        } elseif ($work_type === 'Hybrid') {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-hybrid';
            $workTypeIcon = 'ri-building-2-line';
        } elseif ($work_type === 'Any') {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-any';
            $workTypeIcon = 'ri-shuffle-line';
        } else {
            $workTypeBadgeClass = 'badge-inline badge-worktype badge-onsite';
            $workTypeIcon = 'ri-map-pin-user-line';
        }

    @endphp

                @php
                    $skillArr = ($skillsByJob[$row->id] ?? collect())
                        ->where('company_id', $row->comId)
                        ->values();
                    $jobRequiredSkillNames = $skillArr->pluck('skill_name')->filter()->values()->all();
                    $candidateSkillArr = array_values(array_filter(array_map('trim', explode(',', $currentRowJob->skills ?? ''))));
                    $matchedSkillsArr = \App\Helper\GeneralMethod::matchedSkillsList($candidateSkillArr, $jobRequiredSkillNames);

                    $locPrimary = 'Noida, India';
                    if (($row->work_type ?? null) == 'yes') {
                        $locPrimary = 'Remote';
                    } elseif (is_array($client_city_jobs) && count($client_city_jobs) > 0) {
                        $firstCity = collect($client_city_jobs)->first();
                        $locCity = ucwords(data_get($firstCity, 'city_name', 'Noida'));
                        $countryNm = ucwords(data_get($firstCity, 'country_name', 'India'));
                        $locPrimary = $locCity . ', ' . $countryNm;
                    }

                    $createdDateStr = GeneralMethod::ymdTodmyNotTime($row->created_at);
                    $postedDiff = \Carbon\Carbon::parse($row->created_at)->diffForHumans();
                    $updatedDiff = \Carbon\Carbon::parse($row->updated_at ?? $row->created_at)->diffForHumans();
                    $deadlineStr = date('d M Y', strtotime('+30 days', strtotime($row->created_at)));
                    $benchRequestCheck = $benchReqMap[$row->id] ?? null;
                    $requestsend = "checkrequestsend";
                    if ($currentRowJob->status == 2) {
                        $requestsend = "request-send";
                    }
                    $rawStatus = strtolower($row->status ?? 'active');
                    $statusLabel = $rawStatus === 'hold' ? 'HOLD' : 'OPEN';
                    $statusBadgeCls = $rawStatus === 'hold' ? 'jc-badge-hold' : 'jc-badge-open';
                    $statusDotCls = $rawStatus === 'hold' ? 'jc-dot-hold' : 'jc-dot-open';
                    $statusText = $rawStatus === 'hold' ? 'Hold' : 'Active · Open';
                    $typeBadgeCls = match ($employment_type) {
                        'Contract' => 'jc-badge-ct',
                        'Freelance' => 'jc-badge-fl',
                        'Part-Time' => 'jc-badge-pt',
                        default => 'jc-badge-ft',
                    };
                    $cardAccentCls = ($rawStatus === 'hold' || $employment_type === 'Contract') ? 'jc-accent-yellow' : 'jc-accent-green';
                    $totalPositions = @$row->total_positions ?: (@$row->vacancies ?: 1);
                    $salaryModeShort = mb_substr($min_sal_mode ?: 'Annual', 0, 1);
                    $salaryDisplay = trim(($salary_currency ?? '₹') . $min_salary . ' – ' . $max_salary . ' / ' . $salaryModeShort);
                    $benchMatchCount = (!empty($currentRowJob->id) && !empty($row->id)) ? 1 : 0;
                    $ct = $row->v2_commercial_terms ?? null;
                    $ctVal = function ($key, $default = null) use ($ct) {
                        if (is_array($ct)) { return $ct[$key] ?? $default; }
                        if (is_object($ct)) { return $ct->$key ?? $default; }
                        return $default;
                    };
                @endphp

                <div class="job-card {{ $cardAccentCls }}">
                    <div class="jc-col-main">
                        <div class="jc-badges-row">
                            <span class="jc-badge {{ $statusBadgeCls }}">
                                <span class="jc-dot-mini"></span>{{ $statusLabel }}
                            </span>
                            <span class="jc-badge {{ $typeBadgeCls }}">{{ $employment_type }}</span>
                            <span class="jc-req-id">REQ-JERO-{{ $row->id }}</span>
                        </div>
                        <h2 class="jc-job-title client-show-detail" data-row-id="{{ $row->id }}" title="{{ @$row->title }}">
                            {{ ucwords(mb_substr(@$row->title, 0, 40)) }}
                        </h2>
                        <div class="jc-job-dept" title="{{ @$row->clients_name ?: (@$row->client_name ?: '') }}">
                            {{ ucwords(@$row->clients_name ?: (@$row->client_name ?: 'Marketing / Content')) }}
                        </div>
                        <div class="skills-row">
                            @if(!empty($skillArr))
                                @foreach($skillArr as $key => $sval)
                                    @if($key <= 3)
                                        @php
                                            $isMatched = in_array($sval->skill_name, $matchedSkillsArr, true)
                                                || collect($matchedSkillsArr)->contains(fn($m) => strcasecmp($m, $sval->skill_name) === 0);
                                        @endphp
                                        <span class="skill-tag {{ $isMatched ? 'skill-matched' : '' }}">
                                            {{ ucfirst($sval->skill_name) }}
                                        </span>
                                    @endif
                                @endforeach
                                @if($skillArr->count() > 4)
                                    <span class="skill-tag skill-more client-show-detail" data-row-id="{{ $row->id }}">+{{ $skillArr->count() - 4 }}</span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="jc-col-meta">
                        <div class="jc-meta-item" title="Location">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $locPrimary }}</span>
                        </div>
                        <div class="jc-meta-item" title="Work Mode">
                            <i class="fa-solid fa-laptop"></i>
                            <span>{{ $work_type }}</span>
                        </div>
                        <div class="jc-meta-item" title="Experience">
                            <i class="fa-regular fa-user"></i>
                            <span>{{ ((@$row->min_exp) ? @$row->min_exp : '1') }} – {{ ((@$row->max_exp) ? @$row->max_exp : '3') }} Years</span>
                        </div>
                        <div class="jc-meta-item" title="Openings">
                            <i class="fa-solid fa-users"></i>
                            <span>{{ $totalPositions }} Openings</span>
                        </div>
                        <div class="jc-meta-item" title="Salary">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                            <span>{!! $salary_currency !!}{{ $min_salary }} – {{ $max_salary }} / {{ $salaryModeShort }}</span>
                        </div>
                        <div class="jc-meta-item" title="Industry">
                            <i class="fa-regular fa-building"></i>
                            <span>{{ ucwords(@$row->clients_name ?: 'Marketing / Content') }}</span>
                        </div>
                    </div>

                    <div class="jc-col-dates">
                        <div class="jc-date-item">
                            <i class="fa-regular fa-calendar"></i>
                            <div class="jc-date-content">
                                <span class="jc-date-label">Posted</span>
                                <span class="jc-date-val">{{ $createdDateStr }} ({{ $postedDiff }})</span>
                            </div>
                        </div>
                        <div class="jc-date-item">
                            <i class="fa-solid fa-rotate"></i>
                            <div class="jc-date-content">
                                <span class="jc-date-label">Last Updated</span>
                                <span class="jc-date-val jc-text-green">{{ $updatedDiff }}</span>
                            </div>
                        </div>
                        <div class="jc-date-item">
                            <i class="fa-regular fa-clock"></i>
                            <div class="jc-date-content">
                                <span class="jc-date-label">Submission Deadline</span>
                                <span class="jc-date-val jc-text-red">{{ $deadlineStr }}</span>
                            </div>
                        </div>
                        <div class="jc-status-row">
                            <span class="jc-date-label">Status</span>
                            <span class="jc-status-indicator">
                                <span class="{{ $statusDotCls }}"></span>{{ $statusText }}
                            </span>
                        </div>
                    </div>

                    <div class="jc-subcard jc-bench-subcard">
                        <div>
                            <div class="jc-bench-header">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Your Bench Match</span>
                            </div>
                            <div class="jc-bench-num">{{ $benchMatchCount }}</div>
                            <div class="jc-bench-desc">Matching candidates</div>
                        </div>
                        <div class="jc-subcard-link">View Matches &rarr;</div>
                    </div>

                    <div class="jc-subcard jc-terms-subcard">
                        <div>
                            <div class="jc-subcard-title">Commercial Terms</div>
                            <div class="jc-terms-rows">
                                @if($employment_type === 'Contract')
                                    <div class="jc-ct-row"><span class="jc-ct-label">Billing Model</span><span class="jc-ct-val">{{ ucfirst($ctVal('billing_model', 'Monthly')) }}</span></div>
                                    <div class="jc-ct-row"><span class="jc-ct-label">Billing Rate</span><span class="jc-ct-val">{{ $ctVal('resolved_rate', $salaryDisplay) }}</span></div>
                                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">{{ $ctVal('contract_payment_days', $ctVal('payment_days', 30)) }} Days</span></div>
                                @elseif($employment_type === 'Part-Time' || $employment_type === 'Freelance')
                                    <div class="jc-ct-row"><span class="jc-ct-label">Engagement</span><span class="jc-ct-val">{{ $employment_type }}</span></div>
                                    <div class="jc-ct-row"><span class="jc-ct-label">Rate / Budget</span><span class="jc-ct-val">{{ $ctVal('resolved_rate', $salaryDisplay) }}</span></div>
                                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">{{ $ctVal('contract_payment_days', $ctVal('payment_days', 30)) }} Days</span></div>
                                @else
                                    <div class="jc-ct-row"><span class="jc-ct-label">{{ $ctVal('fulltime_fee_label', 'Hiring Fee') }}</span><span class="jc-ct-val">{{ $ctVal('resolved_fulltime_fee', $ctVal('hiring_fee_pct', 8.33).'% of CTC') }}</span></div>
                                    <div class="jc-ct-row"><span class="jc-ct-label">Replacement</span><span class="jc-ct-val">{{ $ctVal('replacement_days', 90) }} Days</span></div>
                                    <div class="jc-ct-row"><span class="jc-ct-label">Payment Terms</span><span class="jc-ct-val">{{ $ctVal('payment_days', 30) }} Days</span></div>
                                @endif
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="jc-subcard-link client-terms-listing-find-job" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">
                            View Full Terms &rarr;
                        </a>
                    </div>

                    <div class="jc-col-actions">
                        @if ($benchRequestCheck == null)
                            <a href="javascript:void(0);" data-jobId="{{ $row->id }}" data-conId="{{ $currentRowJob->id }}" class="btn-cta-primary {{ $requestsend }}">
                                Submit Candidates
                            </a>
                        @else
                            @if ($benchRequestCheck->ac_status == 1)
                                <span class="action-status action-status--added"><i class="fa-solid fa-check"></i> Added</span>
                            @else
                                <span class="action-status action-status--sent"><i class="fa-regular fa-clock"></i> Request sent</span>
                            @endif
                        @endif

                        <button type="button" class="btn-cta-secondary client-show-detail" data-row-id="{{ $row->id }}">
                            View Requirement
                        </button>
                        <button type="button" class="btn-cta-secondary client-terms-listing-find-job" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}">
                            View Terms
                        </button>
                        <div class="jc-actions-footer">
                            <i class="fa-regular fa-bookmark jc-icon-btn" title="Save Job"></i>
                            <i class="fa-solid fa-ellipsis-vertical jc-icon-btn" title="More Options"></i>
                        </div>
                    </div>
                </div>

    @endforeach
@else
    <div class="rrj-empty">
        <div class="rrj-empty-icon"><i class="ti ti-briefcase-off"></i></div>
        <div class="rrj-empty-title">No matching jobs found</div>
        <div class="rrj-empty-desc">No active client jobs match this candidate's current search and filters. Try widening the skills, location or salary range.</div>
        <button type="button" class="btn btn-sm btn-light-primary" onclick="if(typeof rrjTagifySkills!=='undefined'){$('#rrj_clear_all').trigger('click');fetchBenchRecommendedReqCandidateList();}">
            <i class="ti ti-filter-off" style="margin-right:4px;"></i>Clear Filters
        </button>
    </div>
@endif
