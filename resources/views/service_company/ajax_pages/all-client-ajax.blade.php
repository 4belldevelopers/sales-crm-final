@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)

    @php
    $row = (object) $rowArr;
    if (isset($relationMap) && isset($relationMap[$row->company_id])) {
        $relationget = $relationMap[$row->company_id];
    } else {
        $relationget = GeneralMethod::getRelationData($company_id, $row->company_id);
    }

    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;
    $from_created_at = $relationget->from_created_at ?? null;

    $row->final_status = $final_status;
    $row->staffing_company_relation_id = $recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->from_created_at = $from_created_at;

    /* ── logo / avatar ── */
    $background_image = '';
    if ($company_id != $row->company_id) {
        if (!empty(@$row->website) && empty(@$row->logo)) {
             $domainGet = GeneralMethod::getDomain($row->website);
             $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";
             $google = GeneralMethod::getDomainIcon($googleIcon, $row->id, true);
             $background_image = ($google) ? $googleIcon : '';
        }
        if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
            $background_image = @$row->logo;
        }
    } else {
        if (!empty(@$row->website) && empty(@$row->logo_logical_path)) {
             $domainGet = GeneralMethod::getDomain($row->website);
             $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";
             $google = GeneralMethod::getDomainIcon($googleIcon, $row->id, false);
             $background_image = ($google) ? $googleIcon : '';
        }
        if (!empty(@$row->logo_logical_path) && strlen(@$row->logo_logical_path) > 10) {
            $background_image = @$row->logo_logical_path;
        }
    }

    /* ── name / type ── */
    $client_type_css = 'show-detail';
    $isDirect = false;
    if ($company_id != $row->company_id && $row->staffing_company_relation_id > 0) {
        $client_type_css = 'show-detail-direct';
        $isDirect = true;
    }

    $client_Or_CompanyName = $row->name;
    if ($company_id != $row->company_id) {
        $client_Or_CompanyName = $row->company_name;
    }

    // avatar colour class (Metronic light backgrounds, keyed off first letter)
    $strVal = strtolower(trim(@$client_Or_CompanyName)[0] ?? '');
    $ascii = ord($strVal);
    if ($ascii >= 97 && $ascii <= 101) {
        $bgTextDecore = 'bg-light-danger text-danger';
    } elseif ($ascii >= 102 && $ascii <= 106) {
        $bgTextDecore = 'bg-light-warning text-warning';
    } elseif ($ascii >= 107 && $ascii <= 111) {
        $bgTextDecore = 'bg-light-primary text-primary';
    } elseif ($ascii >= 112 && $ascii <= 116) {
        $bgTextDecore = 'bg-light-success text-success';
    } elseif ($ascii >= 117 && $ascii <= 121) {
        $bgTextDecore = 'bg-dark text-white';
    } else {
        $bgTextDecore = 'bg-light-secondary text-dark';
    }
    $initials = strtoupper(implode('', array_map(fn($w) => mb_substr($w, 0, 1),
                    array_slice(explode(' ', trim(@$client_Or_CompanyName)), 0, 2))));

    $departmentArr = (@$row->ind_name) ? @$row->ind_name : @$row->main_ind_name;
    $client_id = $row->client_id;
    $rowTargetId = $isDirect ? ($row->client_id ?: ($row->from_company_id ?: ($row->company_id ?: ($row->id ?? 0)))) : ($row->client_id ?: ($row->id ?? 0));

    /* ── location ── */
    $country_name = $stateName = $city = '';
    if (count($row->client_location) > 0) {
        $country_name = ($row->client_location[0]['country_name']);
        $stateName = ($row->client_location[0]['state_name']);
        $city = ($row->client_location[0]['city_name']);
    }

    /* ── jobs (active / closed / hold) ── */
    if ($company_id == $row->clients_company_id) {
        $jKey = 'internal_' . $row->client_id;
        $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::InternalClientJobsCountAllStatus($company_id, $row->client_id);
    } else {
        $jKey = 'direct_' . ($from_company_id ?? 0) . '_' . ($to_company_id ?? 0);
        $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::DirectClientJobsCountAllStatus($from_company_id, $to_company_id);
    }
    $job_active   = $jobsCount->job_active ?? 0;
    $job_closed   = $jobsCount->job_inactive ?? 0;
    $job_hold     = $jobsCount->job_hold ?? 0;

    /* ── performance funnel ── */
    if ($company_id != $row->company_id) {
        $pKey = 'direct_' . ($from_company_id ?? 0) . '_' . ($to_company_id ?? 0);
        $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::DirectClientCandidatesCountList($from_company_id, $to_company_id);
    } else {
        $pKey = 'internal_' . $client_id;
        $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::InternalClientCandidatesCountList($client_id);
    }
    $sourced       = $perf->sourced ?? 0;
    $evaluated     = $perf->evaluated ?? 0;
    $interviews    = $perf->all_interview ?? 0;
    $offers        = $perf->offers ?? 0;
    $joined        = $perf->Joined ?? 0;

    /* ── connected on ── */
    if ($row->staffing_company_relation_id > 0 && $row->to_company_id == auth()->user()->company_id) {
        $newDate = date('d M, Y', strtotime($row->from_created_at));
        $daysAgo = GeneralMethod::timeAgo($row->from_created_at);
    } else {
        $newDate = date('d M, Y', strtotime($row->created_at));
        $daysAgo = GeneralMethod::timeAgo($row->created_at);
    }

    /* ── client type tag ── */
    $client_type = __('messages.internal_client_tag');
    if ($company_id != $row->company_id) {
        $client_type = __('messages.direct_clinet_tag');
    }
    @endphp

    <div class="tbl-r">
        <input type="checkbox" class="tbl-cb" onclick="event.stopPropagation()">

        <div class="tbl-a">
            @if (!empty($background_image) && strlen($background_image) > 10)
                <div class="tbl-av {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}">
                    <img src="{{ $background_image }}" data-row-id="{{ $rowTargetId }}" class="{{ $client_type_css }}">
                </div>
            @else
                <div class="tbl-av {{ $client_type_css }} {{ $bgTextDecore }}" data-row-id="{{ $rowTargetId }}">{{ $initials }}</div>
            @endif
            <div class="tbl-ai {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" data-toggle="tooltip1" data-html="true" title="{{ ucwords(@$client_Or_CompanyName) }}">
                <div class="tbl-n">{{ ucwords(mb_substr(@$client_Or_CompanyName, 0, 22)) }}</div>
                <div class="tbl-t">{{ @$departmentArr }}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>

        <div class="tbl-l">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <div>
                <div class="tbl-lc">{{ ucwords(mb_substr(@$city, 0, 15)) }}</div>
                <div class="tbl-ls" title="{{ ucwords(@$stateName) }}{{ $country_name ? ' | ' . ucwords($country_name) : '' }}">{{ ucwords(mb_substr(@$stateName, 0, 15)) }}{{ $country_name ? ' | ' . ucwords(mb_substr($country_name, 0, 6)) : '' }}</div>
            </div>
        </div>

        <div class="tbl-j">
            <div class="tbl-jr"><span class="tbl-jn">{{ $job_active }}</span><span class="tbl-js a">Active</span></div>
            <div class="tbl-jr"><span class="tbl-jn">{{ $job_closed }}</span><span class="tbl-js c">Closed</span></div>
            <div class="tbl-jr"><span class="tbl-jn">{{ $job_hold }}</span><span class="tbl-js d">Hold</span></div>
        </div>

        <div class="tbl-p">
            <div class="tbl-pi"><div class="tbl-pv">{{ $sourced }}</div><div class="tbl-pl">Sourced</div></div>
            <div class="tbl-pi"><div class="tbl-pv">{{ $evaluated }}</div><div class="tbl-pl">Evaluated</div></div>
            <div class="tbl-pi"><div class="tbl-pv">{{ $interviews }}</div><div class="tbl-pl">Interviews</div></div>
            <div class="tbl-pi"><div class="tbl-pv">{{ $offers }}</div><div class="tbl-pl">Offers</div></div>
            <div class="tbl-pi"><div class="tbl-pv">{{ $joined }}</div><div class="tbl-pl">Joined</div></div>
        </div>

        <div>
            <div class="tbl-co">{{ @$newDate }}</div>
            <div class="tbl-cv {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}">view client</div>
        </div>

        <div>
            <span class="tbl-badge {{ $isDirect ? 'direct' : '' }}">{{ $client_type }}</span>
        </div>
    </div>
    @endforeach
@else
    <div class="tbl-r" style="grid-template-columns: 1fr;">
        <div class="tbl-empty">No data available in table</div>
    </div>
@endif
