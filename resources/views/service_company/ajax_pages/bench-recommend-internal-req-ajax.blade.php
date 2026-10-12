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
        $pageJobIds  = array_values(array_filter(array_map(fn ($r) => $r['id'] ?? null, $data)));
        $pageViewerCompanyId = auth()->user()->company_id;
        $relationMap = GeneralMethod::getrRcommendationRelationDataBulk($pageViewerCompanyId, $pageJobIds);
        $benchReqMap = GeneralMethod::benchRequestCheckBulk($condidate_id, $pageJobIds);
        $skillsByJob = !empty($pageJobIds) ? cache()->remember('ats:job_skills:'.md5(implode(',', $pageJobIds)), 300, function() use ($pageJobIds) {
            return DB::table('job_metronics_skills')->whereIn('job_id', $pageJobIds)->get()->groupBy('job_id');
        }) : collect();
        $pageCategoryIds = array_values(array_filter(array_map(fn ($r) => $r['category_id'] ?? null, $data)));
        $categoriesMap = !empty($pageCategoryIds) ? cache()->remember('ats:job_cats:'.implode(',', $pageCategoryIds), 3600, function() use ($pageCategoryIds) {
            return DB::table('job_categories')->whereIn('id', $pageCategoryIds)->pluck('name', 'id')->toArray();
        }) : [];
        $pageComIds = array_values(array_filter(array_map(fn ($r) => $r['comId'] ?? ($r['company_id'] ?? null), $data)));
        $companiesMap = !empty($pageComIds) ? cache()->remember('ats:comps:'.implode(',', $pageComIds), 3600, function() use ($pageComIds) {
            return DB::table('companies')->whereIn('id', $pageComIds)->pluck('company_name', 'id')->toArray();
        }) : [];
        $jobClientCompanyMap = !empty($pageJobIds) ? cache()->remember('ats:job_client_comp:'.md5(implode(',', $pageJobIds)), 300, function() use ($pageJobIds) {
            return DB::table('jobs')
                ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
                ->whereIn('jobs.id', $pageJobIds)
                ->select(
                    'jobs.id as job_id',
                    'clients.name as client_name',
                    'clients.logo_logical_path as client_logo',
                    'clients.self_company',
                    'companies.company_name',
                    'companies.logo as company_logo'
                )
                ->get()
                ->keyBy('job_id');
        }) : collect();
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
            $row->recommendation_id = $recommendation_id;
            $row->recommendation_relation_id = $recommendation_id;
            $row->to_company_id = $to_company_id;
            $row->from_company_id = $from_company_id;
            $row->companyidcount = $row->comId ?? null;
            $row->clients_name = $row->client_name ?? null;

            $vcid = (int) auth()->user()->company_id;
            $relFromCid = (int) ($row->from_company_id ?? 0);
            $relToCid = (int) ($row->to_company_id ?? 0);

            if (isset($row->status) && strtolower((string) $row->status) !== 'active') {
                continue;
            }

            $isDirectClientJob = ($recommendation_id > 0 && $relToCid === $vcid)
                || ((int) ($row->comId ?? 0) > 0 && (int) $row->comId !== $vcid);

            $cDetails = $jobClientCompanyMap[$targetJobId] ?? null;
            if ($cDetails) {
                $isSelfComp = ((int) ($cDetails->self_company ?? 0)) === 1;
                if (!$isSelfComp && !empty($cDetails->client_name)) {
                    // Internal Client Job (self_company == 0) -> Show Client Name & Client Logo
                    $parentCompName = $cDetails->client_name;
                    $logo_path = !empty($cDetails->client_logo) ? $cDetails->client_logo : (!empty($cDetails->company_logo) ? $cDetails->company_logo : ($row->logo ?? ''));
                } else {
                    // In-house company job (self_company == 1)
                    $parentCompName = !empty($cDetails->company_name) ? $cDetails->company_name : (!empty($cDetails->client_name) ? $cDetails->client_name : ($row->company_name ?? $row->client_name ?? 'Internal Client'));
                    $logo_path = !empty($cDetails->company_logo) ? $cDetails->company_logo : (!empty($cDetails->client_logo) ? $cDetails->client_logo : ($row->logo ?? ''));
                }
            } else {
                $dbCompName = $companiesMap[$row->comId ?? 0] ?? ($companiesMap[$row->company_id ?? 0] ?? '');
                $parentCompName = !empty($row->client_name) ? $row->client_name : (!empty($row->company_name) ? $row->company_name : (!empty($dbCompName) ? $dbCompName : 'Internal Client'));
                $logo_path = $row->logo ?? '';
            }

            if ($isDirectClientJob) {
                $clientTypeKey = 'direct';
                $clientBadgeLabel = 'Direct Client';
            } else {
                $clientTypeKey = 'internal';
                $isSelf = isset($cDetails) ? (((int) ($cDetails->self_company ?? 0)) === 1) : false;
                $clientBadgeLabel = $isSelf ? 'Internal' : 'Internal Client';
            }
            $clientTypeId = (string) ($row->comId ?? '');

            $deptName = $row->v2_category_name ?? ($row->category_name ?? ($categoriesMap[$row->category_id ?? 0] ?? ''));
            if (empty($deptName)) {
                $deptName = 'IT (Information Technology)';
            }

            $str = trim(@$row->title);
            $strVal = strtolower($str[0] ?? 'j');
            $ord = ord($strVal);
            if ($ord >= 97 && $ord <= 101) { $initBg = '#FEE2E2'; $initClr = '#DC2626'; }
            elseif ($ord >= 102 && $ord <= 106) { $initBg = '#FEF3C7'; $initClr = '#D97706'; }
            elseif ($ord >= 107 && $ord <= 111) { $initBg = '#DBEAFE'; $initClr = '#2563EB'; }
            elseif ($ord >= 112 && $ord <= 116) { $initBg = '#DCFCE7'; $initClr = '#16A34A'; }
            elseif ($ord >= 117 && $ord <= 121) { $initBg = '#1E293B'; $initClr = '#94A3B8'; }
            else { $initBg = '#F1F5F9'; $initClr = '#475569'; }

            $client_city_jobs = $row->client_city_jobs ?? [];
            $employment_type = match ((int) ($row->employment_type ?? 0)) {
                1 => 'Contract',
                2 => 'Freelance',
                3 => 'Part-Time',
                default => 'Full-time',
            };
            $employmentClass = ($employment_type === 'Contract') ? 'bxr-pos-contract' : 'bxr-pos-fulltime';

            $mod = $sal_mode ?? 0;
            $cur = $min_salary_currency ?? 0;

            $min_sal_mode_str = $salaryMode[$mod] ?? 'Annual';
            $salSuffix = '/' . mb_substr($min_sal_mode_str, 0, 1);
            $salary_currency = $currencySymbole[@$cur] ?? '₹';
            $currencymain = GeneralMethod::currency();
            $currencyName = $currencymain[$cur] ?? 'INR';
            $convertedAmounEctc = GeneralMethod::conversionRates($currencyName, $row->max_yearly ?? 0, 0);
            $convertedAmounCtc = GeneralMethod::conversionRates($currencyName, $row->min_yearly ?? 0, 0);

            $ectc = $convertedAmounEctc[$mod] ?? 0;
            $ctc = $convertedAmounCtc[$mod] ?? 0;

            $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);
            if (empty($min_salary) || $min_salary == '0') {
                $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary ?? 0);
                $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary ?? 0);
            }
            $salaryDisplay = $min_salary . ' - ' . $max_salary;

            $work_type = 'Office';
            if (($row->work_type ?? null) == 'yes') {
                $work_type = 'Remote';
            } elseif (($row->work_type ?? null) == 'other') {
                $work_type = 'Hybrid';
            } elseif (($row->work_type ?? null) == 'any') {
                $work_type = 'Any';
            }

            $skillArr = ($skillsByJob[$row->id] ?? collect())
                ->where('company_id', $row->comId ?? 0)
                ->values();
            $jobRequiredSkillNames = $skillArr->pluck('skill_name')->filter()->values()->all();
            if (empty($jobRequiredSkillNames) && !empty($row->job_metronics_skills)) {
                $jobRequiredSkillNames = collect($row->job_metronics_skills)->pluck('skill_name')->filter()->values()->all();
            }
            $candidateSkillArr = array_values(array_filter(array_map('trim', explode(',', $currentRowJob->skills ?? ''))));
            $matchedSkillsArr = \App\Helper\GeneralMethod::matchedSkillsList($candidateSkillArr, $jobRequiredSkillNames);

            $locPrimary = 'Noida';
            if (($row->work_type ?? null) == 'yes') {
                $locPrimary = 'Remote';
            } elseif (is_array($client_city_jobs) && count($client_city_jobs) > 0) {
                $firstCity = collect($client_city_jobs)->first();
                $locCity = ucwords(data_get($firstCity, 'city_name', 'Noida'));
                $locPrimary = $locCity;
            }

            $createdDateStr = GeneralMethod::ymdTodmyNotTime($row->created_at ?? date('Y-m-d'));
            $benchRequestCheck = $benchReqMap[$row->id] ?? null;
            $requestsend = "checkrequestsend";
            if ($currentRowJob->status == 2) {
                $requestsend = "request-send";
            }

            $rawStatus = strtolower($row->status ?? 'active');
            $statusLabel = match($rawStatus) {
                'hold' => 'Hold',
                'inactive', 'closed' => 'Closed',
                default => 'Hiring',
            };
            $statusClass = match($statusLabel) {
                'Hold' => 'bxr-job-status bxr-job-status--hold',
                'Closed' => 'bxr-job-status bxr-job-status--closed',
                default => 'bxr-job-status bxr-job-status--hiring',
            };

            $hiringManager = '—';
            $hiringDesig = '';
            if (!empty($row->client_contact_info_jobs) && count($row->client_contact_info_jobs) > 0) {
                $hiringManager = ucwords($row->client_contact_info_jobs[0]['contact_person_name'] ?? '—');
                $hiringDesig = ucwords($row->client_contact_info_jobs[0]['designation'] ?? '');
            }
            if ($hiringManager === '—' && !empty($row->posted_by)) {
                $hiringManager = ucwords($row->posted_by);
            }

            $jobAssign = collect($row->job_assign ?? []);
            $firstMember = $jobAssign->first();
            $firstMemberName = !empty($firstMember['user_name']) ? ucwords(mb_substr($firstMember['user_name'], 0, 14)) : (!empty($hiringManager) && $hiringManager !== '—' ? $hiringManager : '—');

            // Render skills HTML
            $skillHtml = '';
            $displayedSkills = array_slice($jobRequiredSkillNames, 0, 4);
            foreach ($displayedSkills as $skName) {
                $skillHtml .= '<span class="bxr-skill" style="background:#F1F5F9;color:#334155;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:500;">' . ucfirst($skName) . '</span>';
            }
            $remSkills = count($jobRequiredSkillNames) - count($displayedSkills);
            if ($remSkills > 0) {
                $skillHtml .= '<span class="bxr-skill" style="background:#F1F5F9;color:#475569;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:600;">+' . $remSkills . '</span>';
            }
        @endphp

        <tr style="height: auto; position: relative;">
            {{-- ① JOB DETAILS --}}
            <td style="vertical-align: top !important; padding: 14px 16px;">
                <div class="bxr-job-row" style="display:flex;align-items:flex-start;gap:12px;">
                    @if(!empty($logo_path))
                        <img src="{{ asset($logo_path) }}" alt="" class="bxr-co-logo client-show-detail"
                             data-row-id="{{ $row->id }}" style="cursor:pointer;width:48px;height:48px;border-radius:12px;object-fit:cover;flex-shrink:0;">
                    @else
                        <div class="bxr-co-init client-show-detail" data-row-id="{{ $row->id }}"
                             style="background:{{ $initBg }};color:{{ $initClr }};cursor:pointer;width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;font-weight:800;flex-shrink:0;">
                            {{ strtoupper($strVal) }}
                        </div>
                    @endif

                    <div class="bxr-job-info" style="min-width:0;flex:1;">
                        <div class="bxr-job-title client-show-detail"
                             data-row-id="{{ $row->id }}"
                             title="{{ ucwords(@$row->title) }}"
                             style="cursor:pointer;font-size:14px;font-weight:700;color:#0F172A;margin-bottom:3px;line-height:1.25;">
                            {{ ucwords(mb_substr(@$row->title, 0, 36)) }}
                        </div>
                        <div class="bxr-job-dept" style="font-size:12px;color:#64748B;font-weight:500;margin-bottom:3px;">{{ ucwords($deptName) }}</div>
                        <div class="bxr-job-co" style="font-size:11px;color:#475569;font-weight:600;text-transform:uppercase;letter-spacing:0.2px;">{{ ucwords($parentCompName) }}</div>
                    </div>
                </div>
                <div class="bxr-skills" style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-top:10px;">
                    <span class="bxr-skill" style="background:#F1F5F9;color:#475569;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:500;display:inline-flex;align-items:center;gap:4px;">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color:#64748B;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $createdDateStr }}
                    </span>
                    {!! $skillHtml !!}
                </div>
            </td>

            {{-- ② EXPERIENCE --}}
            <td style="vertical-align: top !important; padding: 14px 16px;">
                <div class="bxr-col-exp-stack" style="display:flex;flex-direction:column;gap:5px;">
                    <div class="bxr-col-exp-row" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#0F172A;">
                        <span class="bxr-icon-box" style="color:#94A3B8;"><svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                        <span>{{ ((@$row->min_exp)?@$row->min_exp:'1') }} - {{ ((@$row->max_exp)?@$row->max_exp:'3') }} Years</span>
                    </div>
                    @if($salaryDisplay)
                    <div class="bxr-col-exp-row" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#0F172A;">
                        <span class="bxr-icon-box" style="color:#94A3B8;">{!! $salary_currency !!}</span>
                        <span>{{ $salaryDisplay }}<span class="bxr-col-exp-mode" style="font-size:10.5px;color:#2563EB;font-weight:500;">{{ $salSuffix }}</span></span>
                    </div>
                    @endif
                    <div>
                        <span class="bxr-pos-badge {{ $employmentClass }}" style="display:inline-flex;align-items:center;gap:4px;font-size:11.5px;font-weight:600;color:#2563EB;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 12h18"/></svg>
                            {{ $employment_type }}
                        </span>
                    </div>
                </div>
            </td>

            {{-- ③ LOCATION --}}
            <td style="vertical-align: top !important; padding: 14px 16px;">
                <div class="bxr-col-loc-stack" style="display:flex;flex-direction:column;gap:5px;">
                    <div>
                        <span class="{{ $statusClass }}" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:#16A34A;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="bxr-col-loc-row" style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#64748B;font-weight:500;">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M12 22s-7-7.5-7-12a7 7 0 1 1 14 0c0 4.5-7 12-7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <span>{{ $locPrimary }}</span>
                    </div>
                    <div class="bxr-col-loc-mode" style="display:flex;align-items:center;gap:6px;font-size:11.5px;color:#64748B;font-weight:500;">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="#94A3B8" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16M9 21v-6h6v6M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01"/></svg>
                        {{ $work_type }}
                    </div>
                </div>
            </td>

            {{-- ④ HIRING MANAGER --}}
            <td style="vertical-align: top !important; padding: 14px 16px;">
                <div class="bxr-col-hm-row" style="display:flex;flex-direction:column;gap:2px;">
                    <span class="bxr-col-hm-name hmlisting" data-row-id="{{ $row->id }}" style="font-size:12.5px;font-weight:700;color:#0F172A;cursor:pointer;">{{ $hiringManager }}</span>
                    <span class="bxr-col-hm-role" style="font-size:11px;color:#64748B;">{{ $hiringDesig ?: 'HR / Recruiter' }}</span>
                    <div style="margin-top:3px;">
                        <span style="font-size:11.5px;font-weight:600;color:{{ $clientTypeKey === 'direct' ? '#16A34A' : '#2563EB' }};">
                            {{ $clientBadgeLabel }}
                        </span>
                        @if($clientTypeId)
                        <span style="font-size:11.5px;font-weight:700;color:#0F172A;margin-left:2px;">• {{ $clientTypeId }}</span>
                        @endif
                    </div>
                </div>
            </td>

            {{-- ⑤ TEAM --}}
            <td style="vertical-align: top !important; padding: 14px 16px;">
                <div class="bxr-team-cell" style="display:flex;flex-direction:column;gap:3px;">
                    <span class="bxr-team-name team-listing" data-row-id="{{ $row->id }}" style="font-size:12.5px;font-weight:700;color:#0F172A;cursor:pointer;">{{ $firstMemberName }}</span>
                    <a href="javascript:void(0);" class="team-listing" data-row-id="{{ $row->id }}" style="font-size:11px;color:#475569;text-decoration:none;">+1 Members</a>
                </div>
            </td>

            {{-- ⑥ AGENCY & ACTIONS --}}
            <td style="vertical-align: top !important; padding: 14px 16px;">
                <div class="d-flex flex-column align-items-end gap-1">
                    @if(!empty($clientTypeKey) && $clientTypeKey === 'direct' && !empty($parentCompName) && !in_array($parentCompName, ['Internal Client', 'Direct Client']))
                        <div class="fw-bold text-dark text-truncate text-end" style="font-size:11.5px;max-width:130px;color:#0F172A;" title="{{ $parentCompName }}">
                            {{ ucwords(mb_substr($parentCompName, 0, 14)) }}
                        </div>
                    @else
                        <div class="text-muted text-end me-1" style="font-size:12px;">—</div>
                    @endif

                    @if ($benchRequestCheck == null)
                        <button type="button" data-jobId="{{ $row->id }}" data-conId="{{ $currentRowJob->id }}" class="btn btn-sm btn-primary py-1 px-2 w-100 mt-1 {{ $requestsend }}" style="font-size:11.5px;font-weight:700;">
                            <i class="ti ti-send me-1"></i> Submit Candidate
                        </button>
                    @else
                        @if ($benchRequestCheck->ac_status == 1)
                            <span class="badge badge-light-success py-1 px-2 w-100 text-center mt-1" style="font-size:10.5px;font-weight:600;">
                                <i class="ti ti-check me-1"></i> Added
                            </span>
                        @else
                            <span class="badge badge-light-warning py-1 px-2 w-100 text-center mt-1" style="font-size:10.5px;font-weight:600;">
                                <i class="ti ti-clock me-1"></i> Request Sent
                            </span>
                        @endif
                    @endif

                    <div class="d-flex align-items-center justify-content-between w-100 mt-1">
                        <a href="javascript:void(0);" class="client-show-detail text-muted" data-row-id="{{ $row->id }}" style="font-size:10.5px;font-weight:500;" title="View Requirements">
                            <i class="ti ti-eye me-1"></i>Details
                        </a>
                        <a href="javascript:void(0);" class="client-terms-listing-find-job text-muted" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ $row->recommendation_relation_id }}" style="font-size:10.5px;font-weight:500;" title="View Terms">
                            <i class="ti ti-file-text me-1"></i>Terms
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" style="text-align:center;padding:40px 0;color:#94A3B8;">
            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:block;margin:0 auto 8px;"><path d="M3 13l3-8h12l3 8M3 13v7h18v-7M3 13h5l2 3h4l2-3h5"/></svg>
            No active jobs found for this candidate
        </td>
    </tr>
@endif
