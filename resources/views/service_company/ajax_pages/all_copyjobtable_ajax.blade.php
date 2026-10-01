@php
    use App\Helper\GeneralMethod;
    use App\Services\ReferenceDataCache;
    use Illuminate\Support\Facades\DB;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = ReferenceDataCache::feedbackStatus();
    $currency        = GeneralMethod::currency();
    $job_status_arr  = GeneralMethod::job_status();
    $job_status      = GeneralMethod::job_status();
@endphp

@php
    /* Sort jobs by skill match % DESC when candidate skills are provided */
    if (!empty($candidateSkills) && count($data) > 0) {
        $candSkillsForSort = $candidateSkills;
        usort($data, function($a, $b) use ($candSkillsForSort) {
            $calcPct = function($row) use ($candSkillsForSort) {
                $jobSkills = collect($row['job_metronics_skills'] ?? [])->pluck('skill_name')->map(fn($s) => strtolower(trim($s)))->toArray();
                $total = count($jobSkills);
                if ($total === 0) return -1;
                $matched = count(array_intersect($jobSkills, $candSkillsForSort));
                return round(($matched / $total) * 100);
            };
            return $calcPct($b) <=> $calcPct($a);
        });
    }
@endphp
@if (count($data) > 0)
    @php
        /**
         * Intent: Bulk pre-fetch recommendation relations, company/client logos, cached job categories,
         * and partner company details once for the entire page so @foreach runs 0 per-row DB or ES queries.
         * Tenancy: Scoped by $company_id.
         */
        $company_id = auth()->user()->company_id;
        $pageJobIds = array_values(array_filter(array_map(fn($r) => (int) (is_object($r) ? ($r->id ?? 0) : ($r['id'] ?? 0)), $data)));
        $relationsMap = GeneralMethod::getrRcommendationRelationDataBulk($company_id, $pageJobIds);
        $logosMap = GeneralMethod::companyDetailsBulk($data, $company_id);
        $jobCategoriesMap = ReferenceDataCache::jobCategories();

        $toCompanyIds = [];
        foreach ($data as $rArr) {
            $rObj = (object) $rArr;
            $firstRec = collect($rObj->recommendation_relation ?? [])
                ->where('from_company_id', $company_id)
                ->where('job_id', $rObj->id ?? 0)
                ->where('from_status', 1)
                ->first();
            if ($firstRec && !empty($firstRec['to_company_id'])) {
                $toCompanyIds[] = (int) $firstRec['to_company_id'];
            }
        }
        $toCompaniesMap = !empty($toCompanyIds) ? GeneralMethod::companyDetailsGetBulk($toCompanyIds) : [];
    @endphp
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;

        $targetJobId   = $row->id;

        $relationget       = $relationsMap[$targetJobId] ?? (object) [];
        $from_company_id   = $relationget->from_company_id ?? null;
        $recommendation_id = $relationget->to_company_id   ?? null;
        $final_status      = $relationget->final_status     ?? null;

        $row->final_status      = $final_status;
        $row->recommendation_id = $recommendation_id;
        $row->to_company_id     = $recommendation_id;
        $row->from_company_id   = $from_company_id;
        $row->companyidcount    = $row->comId;
        $row->clients_name      = $row->client_name;

        /* ── Avatar colour from job title ── */
        $bgTextDecore = $strVal = '';
        $str = trim(@$row->title);
        $strVal = strtolower($str[0] ?? 'a');
        $ord = ord($strVal);
        if      ($ord >= 97  && $ord <= 101) { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
        elseif  ($ord >= 102 && $ord <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
        elseif  ($ord >= 107 && $ord <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
        elseif  ($ord >= 112 && $ord <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
        elseif  ($ord >= 117 && $ord <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
        else                                 { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }

        $profile_detail_class = 'show-detail';
        $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 25));

        if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
            $profile_detail_class = 'client-show-detail';
            $clients_name = ucwords(mb_substr(@$row->company_name, 0, 25));
        }

        /* ── Company logo (bulk pre-fetched) ── */
        $logo_path = $logosMap[(int) $row->id] ?? '';

        /* ── Skills + match ── */
        $candSkills = $candidateSkills ?? [];
        if (is_string($candSkills)) {
            $candSkills = array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', $candSkills)))));
        } elseif (is_array($candSkills)) {
            $candSkills = array_values(array_filter(array_map('strtolower', array_map('trim', $candSkills))));
        }

        $jobSkillNames = collect($row->job_metronics_skills ?? [])->pluck('skill_name')->map(fn($s) => strtolower(trim($s)))->toArray();
        $matchedCount = 0;
        $totalJobSkills = count($jobSkillNames);
        $skilltag = '';

        if (collect($row->job_metronics_skills)->isNotEmpty()) {
            $matchedSkills = [];
            $unmatchedSkills = [];
            foreach ($row->job_metronics_skills as $sval) {
                $svalObj = (object) $sval;
                $svalNameLower = strtolower(trim($svalObj->skill_name ?? ''));
                $isMatch = in_array($svalNameLower, $candSkills);
                if ($isMatch) {
                    $matchedCount++;
                    $matchedSkills[] = $svalObj;
                } else {
                    $unmatchedSkills[] = $svalObj;
                }
            }

            $allSkillsList = array_merge($matchedSkills, $unmatchedSkills);
            $displaySkills = array_slice($allSkillsList, 0, 2);
            $remainingCount = count($allSkillsList) - count($displaySkills);

            foreach ($displaySkills as $sval) {
                $svalNameLower = strtolower(trim($sval->skill_name ?? ''));
                $isMatch = in_array($svalNameLower, $candSkills);
                if ($isMatch) {
                    $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;padding:3px 8px;font-size:10.5px;border:1px solid #bbf7d0;">' . ucfirst($sval->skill_name) . '</span> ';
                } else {
                    $skilltag .= '<span class="badge badge-light my-1" style="font-weight:500;padding:3px 8px;font-size:10.5px;">' . ucfirst($sval->skill_name) . '</span> ';
                }
            }

            if ($remainingCount > 0) {
                $remainingSkillNames = array_map(fn($s) => ucfirst($s->skill_name ?? ''), array_slice($allSkillsList, 2));
                $skilltag .= '<span class="badge badge-light my-1" style="font-weight:500;color:#64748b;padding:3px 6px;font-size:10px;" title="' . e(implode(', ', $remainingSkillNames)) . '">+' . $remainingCount . ' more</span>';
            }
        }

        $matchPct   = ($totalJobSkills > 0) ? (!empty($candSkills) ? round(($matchedCount / $totalJobSkills) * 100) : 0) : null;
        $matchStyle = $matchPct !== null ? GeneralMethod::getSkillMatchBadgeStyle($matchPct) : ['bg' => '#fee2e2', 'color' => '#dc2626'];
        $matchColor = $matchStyle['color'];
        $matchBg    = $matchStyle['bg'];

        /* ── Department (cached ReferenceDataCache::jobCategories) ── */
        $department = $jobCategoriesMap[$row->category_id ?? 0] ?? '';

        /* ── Experience ── */
        $min_exp   = @$row->min_exp ?? 0;
        $max_exp   = @$row->max_exp ?? 0;
        $expLabel  = $min_exp . ' - ' . $max_exp . ' Years';

        /* ── Salary ── */
        $min_sal_mode        = strtoupper(mb_substr($salaryMode[$row->min_sal_mode ?? 0] ?? 'Annual', 0, 1));
        $min_salary_currency = $currencySymbole[@$row->min_salary_currency] ?? '₹';
        $max_salary          = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($row->max_salary ?? 0));
        $min_salary          = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($row->min_salary ?? 0));
        $salaryLabel         = $min_salary_currency . ' ' . $min_salary . ' - ' . $max_salary . ' / ' . $min_sal_mode;

        /* ── Status ── */
        $status   = ucwords($row->status ?? '');
        $jobClass = $job_status[$row->status] ?? '';
        if ($status == 'Active')   { $status = 'Hiring'; }
        if ($status == 'Inactive') { $status = 'Closed'; }
        if (($row->final_status == 2 || $row->final_status == 3) && @$row->to_company_id == $company_id) {
            $status = 'Removed'; $jobClass = 'closed-bg';
        }

        /* ── Internal / Client badge ── */
        $svg = '<svg width="9" height="9" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5"/>
                    <path d="M8 12h8M12 8v8" stroke="currentColor" stroke-width="2.5"/>
                </svg>';
        $badgeClass = 'badge-internal';
        $badgeText  = 'Internal';
        if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
            $svg = '<svg width="9" height="9" fill="none" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2.5"></path>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2.5"></circle>
                    </svg>';
            $badgeClass = 'badge-client';
            $badgeText  = 'Client';
        }

        /* ── Location & work type ── */
        $employment_type = $row->employment_type == 1 ? 'Contract' : 'Full-time';
        $locationLabel   = 'Remote';
        $work_type       = 'Remote';
        if ($row->work_type == 'no') {
            $work_type = 'Office';
            $firstCity = collect($row->client_city_jobs ?? [])->first();
            $cityName  = $firstCity['city_name'] ?? '';
            $locationLabel = $cityName ? ucwords(mb_substr($cityName, 0, 14)) : 'Multiple';
        } elseif ($row->work_type == 'other') {
            $work_type = 'Hybrid';
        }

        /* ── Hiring Manager ── */
        $hiringManager     = '—';
        $hiringManagerRole = 'Recruiter';
        if (!empty($row->client_contact_info_jobs) && count($row->client_contact_info_jobs) > 0) {
            $hiringManager     = ucwords($row->client_contact_info_jobs[0]['contact_person_name'] ?? '—');
            $hiringManagerRole = ucwords($row->client_contact_info_jobs[0]['designation'] ?? 'Recruiter');
        }

        /* ── Team / job_assign ── */
        $job_assign    = $row->job_assign ?? [];
        $jobAssign     = collect($job_assign);
        $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $row->id)->unique('user_id')->count();
        $firstMember   = $jobAssign->where('company_id', $company_id)->where('job_id', $row->id)
                            ->filter(fn($r) => ($r['user_id'] ?? null) == ($r['spok_user_id'] ?? null))
                            ->sortBy('user_name')->first();
        $firstMemberName = $firstMember ? ucwords(mb_substr($firstMember['user_name'] ?? '', 0, 10)) : '—';

        if ($recommendation_id > 0 && @$row->to_company_id == $company_id) {
            $recommendationRelation = collect($row->recommendation_relation ?? []);
            $partnersCount   = $recommendationRelation->where('from_company_id', $company_id)->where('job_id', $row->id)->where('from_status', 1)->count();
            $firstRec        = $recommendationRelation->where('from_company_id', $company_id)->where('job_id', $row->id)->where('from_status', 1)->first();
            if ($firstRec) {
                $toCid = (int) ($firstRec['to_company_id'] ?? 0);
                $cd = $toCompaniesMap[$toCid] ?? GeneralMethod::companyDetailsGet($toCid);
                $firstMemberName = ucwords(mb_substr(@$cd->company_name ?? '', 0, 10));
            }
        }
        if ($partnersCount > 1) { $displayCount = '+' . ($partnersCount - 1); } else { $displayCount = $partnersCount; }
    @endphp

    <div class="cj-job-row" data-match-pct="{{ $matchPct ?? 0 }}">

        {{-- Checkbox hidden --}}

        {{-- JOB DETAILS --}}
        <div class="col-job" style="position:relative; padding-bottom: 28px;">
            <div class="cj-job-top">
                <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="{{ $profile_detail_class }}">
                    <div class="job-logo {{ !empty($logo_path) ? '' : $bgTextDecore }}">
                        @if(!empty($logo_path))
                            <img src="{{ str_replace('index.php', '', @$logo_path) }}" alt="Logo" class="w-100">
                        @else
                            {{ strtoupper($strVal) }}
                        @endif
                    </div>
                </a>
                <div class="job-info">
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}"
                       class="job-title {{ $profile_detail_class }}"
                       data-toggle="tooltip" title="{{ ucwords(@$row->title) }}">
                        {{ ucwords(mb_substr(@$row->title, 0, 28)) }}
                    </a>
                    <div class="job-dept">{{ $department }}</div>
                    <div class="job-client">{{ $clients_name }}</div>
                </div>
            </div>
            @if($skilltag)
            <div style="position:absolute; bottom:-7px; left:1px; white-space:nowrap; overflow:hidden;">
                <div class="job-meta-line" style="display:flex;align-items:center;gap:6px;">
                    @if($matchPct !== null)
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:{{ $matchBg }};color:{{ $matchColor }};white-space:nowrap;flex-shrink:0;">
                        {{ $matchPct }}% Match
                    </span>
                    @endif
                    <span class="skills-inline">{!! $skilltag !!}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- EXPERIENCE --}}
        <div class="col-exp">
            <div class="exp-row">
                <svg fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.8"/></svg>
                {{ $expLabel }}
            </div>
            <div class="sal-row">
                {!! $salaryLabel !!}
            </div>
            <span class="badge-type {{ $badgeClass }}">
                {!! $svg !!}
                <span>{{ $badgeText }} · {{ $row->id }}</span>
            </span>
        </div>

        {{-- HIRING & LOCATION --}}
        <div class="col-hl">
            <span class="badge-hiring {{ $jobClass }}">{{ $status }}</span>
            <div class="loc-row">
                <svg fill="none" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
                {{ $locationLabel }}
            </div>
            <div class="mode-row">
                <svg fill="none" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8"/><polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="1.8"/></svg>
                {{ $work_type }}
            </div>
        </div>

        {{-- HIRING MANAGER --}}
        <div class="col-hm">
            <div class="hm-name">{{ $hiringManager }}</div>
            <div class="hm-title">{{ $hiringManagerRole }}</div>
            <div class="hm-company">{{ $employment_type }}</div>
        </div>

        {{-- TEAM --}}
        <div class="col-action">
            <div class="hm-name">{{ $firstMemberName }}</div>
            <div class="action-count">{{ $displayCount }} Member{{ $partnersCount != 1 ? 's' : '' }}</div>
            <div class="action-more">+ Add More</div>
        </div>

        {{-- ACTION: single job add --}}
        <div class="col-single-action">
            <button class="btn-add-single chk_copy_single_add" data-job-id="{{ $row->id }}">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                {{ empty($findTalentAdd ?? null) ? 'Copy Candidate' : 'Submit Candidate' }}
            </button>
        </div>

    </div>
    @endforeach
@else
    <div style="text-align:center;padding:40px;color:#6b7280;font-size:13px;">
        No jobs available
        @if(in_array("add_jobs", $userPermissions))
        <br><a href="{{ url('service_company/jobs/create') }}" target="_blank"
           class="btn btn-sm btn-primary mt-3">Add Job</a>
        @endif
    </div>
@endif
