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

    $relationget =  GeneralMethod::getRelationData($company_id, $row->company_id);
    $from_company_id = $relationget->from_company_id;
    $recommendation_id = $relationget->id;
    $to_company_id = $relationget->to_company_id;
    $final_status = $relationget->final_status;
    $from_created_at = $relationget->from_created_at;

    $row->final_status = $final_status;
    $row->staffing_company_relation_id = $recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->from_created_at = $from_created_at;

    /* ── logo / avatar ── */
    $background_image = '';
    if (!empty(@$row->website) && empty(@$row->logo)) {
        $domainGet = GeneralMethod::getDomain($row->website);
        $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";
        $google = GeneralMethod::getDomainIcon($googleIcon, $row->id, true);
        $background_image = ($google) ? $googleIcon : '';
    }
    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
        $background_image = @$row->logo;
    }

    $company_name = ucwords($row->company_name);
    $ind_name = (@$row->ind_name) ? @$row->ind_name : $row->main_ind_name;

    // avatar colour class (Metronic light backgrounds, keyed off first letter)
    $strVal = strtolower(trim(@$company_name)[0] ?? '');
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
                    array_slice(explode(' ', trim(@$company_name)), 0, 2))));

    /* ── location ── */
    $country_name = $stateName = $city = '';
    if (count($row->client_location) > 0) {
        $country_name = ($row->client_location[0]['country_name']);
        $stateName = ($row->client_location[0]['state_name']);
        $city = ($row->client_location[0]['city_name']);
    }

    /* ── jobs (active / closed / hold) ── */
    $jobsCount = GeneralMethod::DirectClientJobsCountAllStatus($row->from_company_id, $row->to_company_id);
    $job_active = $jobsCount->job_active ?? 0;
    $job_closed = $jobsCount->job_inactive ?? 0;
    $job_hold   = $jobsCount->job_hold ?? 0;

    /* ── performance funnel ── */
    $perf = GeneralMethod::DirectClientCandidatesCountList($row->from_company_id, $row->to_company_id);
    $sourced    = $perf->sourced ?? 0;
    $evaluated  = $perf->evaluated ?? 0;
    $interviews = $perf->all_interview ?? 0;
    $offers     = $perf->offers ?? 0;
    $joined     = $perf->Joined ?? 0;

    /* ── connected on ── */
    $newDate = date('d M, Y', strtotime($row->from_created_at));
    $daysAgo = GeneralMethod::timeAgo($row->from_created_at);
    @endphp

    <div class="tbl-r">
        <input type="checkbox" class="tbl-cb" onclick="event.stopPropagation()">

        <div class="tbl-a">
            @if (!empty($background_image) && strlen($background_image) > 10)
                <div class="tbl-av show-detail-direct" data-row-id="{{ $row->client_id }}">
                    <img src="{{ $background_image }}" data-row-id="{{ $row->client_id }}" class="show-detail-direct">
                </div>
            @else
                <div class="tbl-av show-detail-direct {{ $bgTextDecore }}" data-row-id="{{ $row->client_id }}">{{ $initials }}</div>
            @endif
            <div class="tbl-ai show-detail-direct" data-row-id="{{ $row->client_id }}" data-toggle="tooltip1" data-html="true" title="{{ $company_name }}">
                <div class="tbl-n">{{ ucwords(mb_substr(@$company_name, 0, 22)) }}</div>
                <div class="tbl-t">{{ @$ind_name }}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>

        <div class="tbl-l">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <div>
                <div class="tbl-lc">{{ ucwords(mb_substr(@$city, 0, 20)) }}</div>
                <div class="tbl-ls">{{ ucwords(@$stateName) }}{{ $country_name ? ' | ' . ucwords($country_name) : '' }}</div>
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
            <div class="tbl-cv show-detail-direct" data-row-id="{{ $row->client_id }}">view client</div>
        </div>

        <div>
            <span class="tbl-badge direct">{{ __('messages.direct_clinet_tag') }}</span>
        </div>
    </div>
    @endforeach
@else
    <div class="tbl-r" style="grid-template-columns: 1fr;">
        <div class="tbl-empty">No data available in table</div>
    </div>
@endif
