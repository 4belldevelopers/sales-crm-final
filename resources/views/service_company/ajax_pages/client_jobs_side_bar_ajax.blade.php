@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode      = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $currency        = GeneralMethod::currency();
    $job_status      = GeneralMethod::job_status();

    // Pipeline stage definitions (label → master_status_id → CSS class)
    $_stageMapLocal = [
        ['label'=>'INW','id'=>8,'cls'=>'s-inw'],
        ['label'=>'SOU','id'=>1,'cls'=>'s-sou'],
        ['label'=>'SCR','id'=>2,'cls'=>'s-scr'],
        ['label'=>'EVA','id'=>3,'cls'=>'s-eva'],
        ['label'=>'NEG','id'=>4,'cls'=>'s-neg'],
        ['label'=>'OFF','id'=>5,'cls'=>'s-off'],
        ['label'=>'JOI','id'=>6,'cls'=>'s-joi'],
        ['label'=>'REJ','id'=>7,'cls'=>'s-rej'],
    ];
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;
        $targetJobId = $row->id;

        $relationget      = GeneralMethod::getrRcommendationRelationData($company_id, $targetJobId);
        $from_company_id  = $relationget->from_company_id ?? null;
        $recommendation_id = $relationget->to_company_id ?? null;
        $final_status     = $relationget->final_status ?? null;

        $row->final_status      = $final_status;
        $row->recommendation_id = $recommendation_id;
        $row->to_company_id     = $recommendation_id;
        $row->from_company_id   = $from_company_id;
        $row->companyidcount    = $row->comId ?? 0;
        $row->clients_name      = $row->client_name ?? '';

        $job_assign = $row->job_assign ?? [];
        $jobAssign  = collect($job_assign);

        // ── Company display name ──
        if ($row->self_company > 0 && $row->comId == $company_id) {
            $company_name = ucwords(mb_substr(@$row->company_name, 0, 28));
        } elseif ($row->comId != $company_id) {
            $company_name = ucwords(mb_substr(@$row->company_name, 0, 28));
        } else {
            $company_name = ucwords(mb_substr(@$row->client_company, 0, 28));
        }

        // ── Logo / avatar ──
        $strVal = strtolower(mb_substr(trim(@$row->title), 0, 1));
        $strOrd = ord($strVal);
        if      ($strOrd >= 97  && $strOrd <= 101) { $bgLetterCls = 'bg-light-danger text-danger'; $logoColor = '#f64e60'; }
        elseif  ($strOrd >= 102 && $strOrd <= 106) { $bgLetterCls = 'bg-light-warning text-warning'; $logoColor = '#ffc700'; }
        elseif  ($strOrd >= 107 && $strOrd <= 111) { $bgLetterCls = 'bg-light-primary text-primary'; $logoColor = '#009ef7'; }
        elseif  ($strOrd >= 112 && $strOrd <= 116) { $bgLetterCls = 'bg-light-success text-success'; $logoColor = '#50cd89'; }
        elseif  ($strOrd >= 117 && $strOrd <= 121) { $bgLetterCls = 'bg-dark text-secondary'; $logoColor = '#3f4254'; }
        else                                        { $bgLetterCls = 'bg-light-secondary text-dark'; $logoColor = '#a1a5b7'; }
        $logo_path = GeneralMethod::companyDetails($row->id);

        // ── Status pill ──
        $status = ucwords($row->status);
        if ($status == 'Active')   $status = 'Hiring';
        if ($status == 'Inactive') $status = 'Closed';
        $statusCls = 'active-bg';
        if ($status == 'Closed') $statusCls = 'closed-bg';
        elseif ($status == 'Hold') $statusCls = 'hold-bg';

        // ── Internal / client tag ──
        $parentCompName = $row->company_name ?? ($row->clients_name ?? '');
        $isDirectClientJob = ($row->comId != $company_id);
        $isSelfCompany = ((int) ($row->self_company ?? 0)) === 1;
        if ($isDirectClientJob) {
            $recommendation_text = 'Direct Client';
        } elseif ($isSelfCompany) {
            $recommendation_text = __('messages.internal_tag');
        } else {
            $recommendation_text = 'Internal Client';
        }
        $tagCls = $isDirectClientJob ? 'badge-client' : 'badge-internal';

        $deptName = !empty(@$row->department_name)
            ? @$row->department_name
            : (!empty(@$row->category_name)
                ? @$row->category_name
                : (\DB::table('job_categories')->where('id', @$row->category_id)->value('name') ?? ''));

        // ── Experience ──
        $minExp = @$row->min_exp ?? '0';
        $maxExp = @$row->max_exp ?? '0';

        // ── Salary ──
        $min_sal_mode        = strtoupper(mb_substr($salaryMode[$row->min_sal_mode] ?? '', 0, 1));
        $min_salary_currency = $currencySymbole[@$row->min_salary_currency] ?? '';
        $max_salary_currency = $currencySymbole[@$row->max_salary_currency] ?? $min_salary_currency;
        $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
        $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
        $min_salary_abbr = GeneralMethod::formatAmount($min_salary);
        $max_salary_abbr = GeneralMethod::formatAmount($max_salary);

        // ── Employment type ──
        $employment_type = ($row->employment_type == 1) ? 'Contract' : 'Full-time';

        // ── Location ──
        $client_city_jobs  = $row->client_city_jobs ?? [];
        $jobCitySelected   = is_array($client_city_jobs) ? count($client_city_jobs) : 0;
        $locationName      = 'Remote';
        $workTypeLabel     = 'Office';
        if ($row->work_type == 'yes') {
            $locationName = 'Remote';
            $workTypeLabel = 'Remote';
        } elseif (in_array($row->work_type, ['no', 'other'])) {
            $firstCity     = collect($client_city_jobs)->first();
            $locationName  = ($jobCitySelected > 1) ? 'Multiple' : ucwords(mb_substr($firstCity['city_name'] ?? 'Office', 0, 12));
            $workTypeLabel = 'Office';
        } elseif ($row->work_type == 'any') {
            $firstCity     = collect($client_city_jobs)->first();
            $locationName  = ($jobCitySelected > 1) ? 'Multiple' : ucwords(mb_substr($firstCity['city_name'] ?? 'Office', 0, 12));
            $workTypeLabel = 'Hybrid';
        }

        // ── Hiring Manager ──
        $hiringManager = '—';
        $hmRole        = '';
        if (!empty($row->client_contact_info_jobs) && count($row->client_contact_info_jobs) > 0) {
            $hiringManager = ucwords(mb_substr($row->client_contact_info_jobs[0]['contact_person_name'] ?? '—', 0, 18));
            $hmRole        = ucwords(mb_substr($row->client_contact_info_jobs[0]['designation'] ?? '', 0, 16));
        }
        if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
            $jac = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $row->comId)->filter()->values()->all();
            if (count($jac) > 0 && $row->comId != auth()->user()->company_id) {
                $hiringManager = ucwords(mb_substr($jac[0]['user_name'] ?? '—', 0, 18));
                $hmRole        = ucwords(mb_substr($jac[0]['designation'] ?? '', 0, 16));
            }
        }

        // ── Team column: first member name + total count ──
        $teamName = '';
        $teamCnt  = 0;
        if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
            // Direct client job — show client-side HM name
            $pFirst  = $jobAssign->where('company_id', $from_company_id)->where('job_id', $row->id)->first();
            $teamCnt = $jobAssign->where('company_id', $from_company_id)->where('job_id', $row->id)->count();
            $teamName = ucwords(mb_substr($pFirst['user_name'] ?? '', 0, 18));
            $teamListClass = 'client-hm-listing';
        } else {
            // Custom client job — show own team
            $pFirst  = $jobAssign->where('company_id', $company_id)->where('job_id', $row->id)
                ->filter(fn($r) => $r['user_id'] == $r['spok_user_id'])
                ->sortBy('user_name')->first();
            $teamCnt = $jobAssign->where('company_id', $company_id)->where('job_id', $row->id)->count();
            $teamName = ucwords(mb_substr($pFirst['user_name'] ?? '', 0, 18));
            $teamListClass = 'hm-listing';
        }

        // ── Agency column: add-agency link + matching count / own company name ──
        $ownCompanyName = \DB::table('companies')->where('id', $company_id)->value('company_name') ?? '';
        $agencyMatchCnt = 0;
        $agencyAssigned = 0;
        $recRel = collect($row->recommendation_relation ?? []);
        $agencyAssigned = $recRel->where('from_company_id', $company_id)->where('job_id', $row->id)->where('from_status', 1)->count();
        // Matching count from row field if available
        $agencyMatchCnt = (int)(@$row->matching_agency_count ?? $agencyAssigned);

        // ── URL base for pipeline links ──
        $urlBase = '';
        if ($custom == 'true') {
            $urlBase = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/');
        }
        if (@$row->to_company_id == $company_id && @$row->recommendation_id > 0) {
            $urlBase = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/');
        }

        // ── Pipeline stage counts (batch) ──
        $_stageCounts = [];
        foreach ($_stageMapLocal as $_st) {
            $_cnt = ($custom == 'true')
                ? GeneralMethod::statusCount($company_id, $row->id, $_st['id'])
                : GeneralMethod::statusCountThirdparty($company_id, $row->id, $_st['id']);
            $_stageCounts[] = [
                'label' => $_st['label'],
                'cls'   => $_st['cls'],
                'cnt'   => (int)$_cnt,
                'url'   => $urlBase ? $urlBase . $_st['id'] : '',
            ];
        }

        // ── Total applications + INW count ──
        $totalApps = ($custom == 'true')
            ? GeneralMethod::allStatusCount($company_id, $row->id)
            : GeneralMethod::allStatusCountThirdparty($company_id, $row->id);
        $interviewCnt = $_stageCounts[0]['cnt']; // INW (index 0)

        // ── Date ──
        $newDate = GeneralMethod::ymdTodmyNotTime($row->created_at);

        // ── Skills — extract text names only; skip numeric IDs from key_skills ──
        $skillsArr = [];
        $_rawSkills = !empty(@$row->skills) && is_array($row->skills) ? $row->skills
                    : (!empty(@$row->key_skills) && is_array($row->key_skills) ? $row->key_skills : []);
        foreach (array_slice($_rawSkills, 0, 5) as $_s) {
            $_name = '';
            if (is_array($_s))        $_name = $_s['name'] ?? $_s['skill_name'] ?? '';
            elseif (is_object($_s))   $_name = $_s->name ?? $_s->skill_name ?? '';
            elseif (is_string($_s) && !is_numeric(trim($_s))) $_name = trim($_s);
            if ($_name !== '') $skillsArr[] = ucwords($_name);
        }
    @endphp

    <div class="job-row">

        {{-- ── Col 1: Job Details ── --}}
        <div class="col-job">
            <div class="drag-col">
                <i class="fas fa-grip-vertical" style="font-size:12px;"></i>
            </div>
            @if($logo_path)
                <div class="job-logo" style="border-radius:10px; overflow:hidden; background:#f1f3fa;">
                    <img src="{{ $logo_path }}" alt="">
                </div>
            @else
                <div class="job-logo" style="background:{{ $logoColor }}; border-radius:10px;">
                    {{ strtoupper($strVal) }}
                </div>
            @endif
            <div class="job-info">
                <div class="job-title">
                    <a href="javascript:void(0);" data-row-id="{{ $row->id }}" class="show-detail" title="{{ ucwords(@$row->title) }}">
                        {{ ucwords(mb_substr(@$row->title, 0, 28)) }}
                    </a>
                </div>
                <div class="job-dept">{{ ucwords(mb_substr($deptName ?: 'IT (Information Technology)', 0, 30)) }}</div>
                <div class="job-client">{{ $company_name }}</div>
            </div>
        </div>

        {{-- ── Col 2: Experience + Salary ── --}}
        <div class="col-exp">
            <div class="exp-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                {{ $minExp }} – {{ $maxExp }} Yrs
            </div>
            <div class="sal-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                {!! $min_salary_currency !!}{{ $min_salary_abbr }} – {{ $max_salary_abbr }} {{ $min_sal_mode }}
            </div>
            <span class="badge-type badge-direct" style="margin-top:4px; width:fit-content;">{{ $employment_type }}</span>
        </div>

        {{-- ── Col 3: Hiring & Location ── --}}
        <div class="col-hl">
            <span class="badge-hiring {{ $statusCls }}">{{ $status }}</span>
            <div class="loc-row" style="margin-top:5px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                {{ $locationName }}
            </div>
            <div class="mode-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                {{ $workTypeLabel }}
            </div>
        </div>

        {{-- ── Col 4: Hiring Manager ── --}}
        <div class="col-hm">
            <div class="hm-name">{{ $hiringManager }}</div>
            <div class="hm-title">{{ !empty($hmRole) ? $hmRole : 'Account Owner' }}</div>
            <div class="hm-company hm-client-type"><span class="it-job-type {{ $tagCls }}">{{ $recommendation_text }}</span> &bull; <a href="javascript:void(0);" class="it-job-id">{{ $row->id }}</a></div>
        </div>

        {{-- ── Col 5: Team ── --}}
        <div class="col-action {{ $teamListClass }}" data-row-id="{{ $row->id }}">
            @if($teamName)
                <div style="font-size:12.5px; font-weight:700; color:#181c32;">{{ $teamName }}</div>
            @endif
            <div style="font-size:11px; color:#a1a5b7; margin-top:2px;">+{{ $teamCnt }} Members</div>
        </div>

        {{-- ── Col 6: Agency ── --}}
        <div class="col-action">
            @if($isDirectClientJob)
                <div class="hm-name" style="font-size:12.5px; font-weight:700; color:#181c32;">{{ ucwords(mb_substr($ownCompanyName, 0, 24)) }}</div>
            @else
                <div class="action-more spartner-listing" data-row-id="{{ $row->id }}">+ Add Agency</div>
                @if($agencyMatchCnt > 0)
                    <div style="font-size:11px; color:#a1a5b7; margin-top:2px;">{{ $agencyMatchCnt }} Matching</div>
                @endif
            @endif
        </div>

        {{-- ── Col 7: Hiring Pipeline ── --}}
        <div class="col-pipeline">
            <div class="pipeline-stages">
                @foreach($_stageCounts as $_sc)
                    <div class="stage">
                        <span class="stage-label">{{ $_sc['label'] }}</span>
                        <span class="stage-badge {{ $_sc['cls'] }}">
                            @if($_sc['url'])
                                <a href="{{ $_sc['url'] }}">{{ $_sc['cnt'] }}</a>
                            @else
                                {{ $_sc['cnt'] }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
            <div class="pipeline-total">
                Total: <strong>{{ $totalApps }} Applications</strong>
                @if($interviewCnt > 0)
                    &nbsp;|&nbsp; Interview: <strong>{{ $interviewCnt }}</strong>
                @endif
            </div>
            <div class="add-date">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ $newDate }}
            </div>
        </div>

    </div>
    @if(count($skillsArr))
    <div class="job-row-skills">
        <span class="sk-label">Skills:</span>
        @foreach($skillsArr as $_skName)
            <span class="sk-tag">{{ $_skName }}</span>
        @endforeach
    </div>
    @endif
    @endforeach
@else
    <div class="job-empty">
        <i class="fas fa-briefcase" style="font-size:32px; color:#e4e6ef; display:block; margin-bottom:10px;"></i>
        No jobs found for this client.
    </div>
@endif
