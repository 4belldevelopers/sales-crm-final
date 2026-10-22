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
        $client_id = $row->id ?? ($row->client_id ?? 0);
        $client_Or_CompanyName = $row->name ?? '';

        $departmentArr = '------';
        if (!empty($department) && !empty($row->industry)) {
            foreach ($department as $val) {
                if ($row->industry == $val->id) {
                    $departmentArr = $val->ind_name;
                }
            }
        }
        if (!empty($row->ind_name)) {
            $departmentArr = $row->ind_name;
        }

        /* ── location ── */
        $country_name = $stateName = $city = '';
        if (!empty($row->client_location) && count($row->client_location) > 0) {
            $country_name = $row->client_location[0]['country_name'] ?? '';
            $stateName = $row->client_location[0]['state_name'] ?? '';
            $city = $row->client_location[0]['city_name'] ?? '';
        }

        /* ── company size ── */
        $companySizeText = '';
        $fromSize = $row->company_size_from ?? null;
        $toSize = $row->company_sizes ?? $row->company_size ?? null;
        if (!empty($fromSize) && !empty($toSize)) {
            $companySizeText = number_format((int)$fromSize) . ' - ' . number_format((int)$toSize) . ' employees';
        } elseif (!empty($toSize)) {
            $companySizeText = is_numeric($toSize) ? number_format((int)$toSize) . ' employees' : $toSize . ' employees';
        }

        /* ── jobs (active / closed / hold) ── */
        $jKey = 'internal_' . $client_id;
        $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::InternalClientJobsCountAllStatus($company_id, $client_id);
        $job_active = (int)($jobsCount->job_active ?? 0);
        $job_closed = (int)($jobsCount->job_inactive ?? 0);
        $job_hold   = (int)($jobsCount->job_hold ?? 0);

        /* ── performance funnel ── */
        $pKey = 'internal_' . $client_id;
        $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::InternalClientCandidatesCountList($client_id);
        $sourced    = (int)($perf->sourced ?? 0);
        $evaluated  = (int)($perf->evaluated ?? 0);
        $interviews = (int)($perf->all_interview ?? 0);
        $offers     = (int)($perf->offers ?? 0);
        $joined     = (int)($perf->Joined ?? 0);

        /* ── connected on ── */
        $createdTimestamp = $row->created_at ?? null;
        $newDate = $createdTimestamp ? date('d M, Y', strtotime($createdTimestamp)) : '-';
        $daysAgo = $createdTimestamp ? GeneralMethod::timeAgo($createdTimestamp) : '';

        $locMain = '';
        if ($city && $stateName) {
            $locMain = ucwords($city) . ', ' . ucwords($stateName);
        } elseif ($city) {
            $locMain = ucwords($city);
        } elseif ($stateName) {
            $locMain = ucwords($stateName);
        }
    @endphp

    <div class="tbl-r">
        {{-- Checkbox --}}
        <div class="tbl-cell-check">
            <input type="checkbox" class="tbl-cb" value="{{ $client_id }}" onclick="event.stopPropagation()">
        </div>

        {{-- Client (NO ICON as requested) --}}
        <div class="tbl-cell-client show-detail" data-row-id="{{ $client_id }}" title="{{ ucwords(@$client_Or_CompanyName) }}">
            <div class="cl-name show-detail" data-row-id="{{ $client_id }}">{{ ucwords(@$client_Or_CompanyName) }}</div>
            @if(!empty($departmentArr))
                <div class="cl-ind">{{ @$departmentArr }}</div>
            @endif
            @if(!empty($companySizeText))
                <div class="cl-size">{{ $companySizeText }}</div>
            @endif
        </div>

        {{-- Location (NO ICON as requested) --}}
        <div class="tbl-cell-loc">
            @if(!empty($locMain))
                <div class="cl-loc-city" title="{{ $locMain }}">{{ $locMain }}</div>
            @endif
            @if(!empty($country_name))
                <div class="cl-loc-country">{{ ucwords($country_name) }}</div>
            @endif
            @if(empty($locMain) && empty($country_name))
                <div class="cl-loc-empty">-</div>
            @endif
        </div>

        {{-- Connected On & Type --}}
        <div class="tbl-cell-type">
            <div class="cl-date-box">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>{{ $newDate }}</span>
            </div>
            <div class="cl-conn-caption">Connected on</div>
            <div class="cl-type-name type-internal show-detail" data-row-id="{{ $client_id }}">
                {{ __('messages.internal_client_tag') }}
            </div>
        </div>

        {{-- Last Activity --}}
        <div class="tbl-cell-activity">
            <div class="cl-act-main">{{ $daysAgo ? $daysAgo : 'No activity yet' }}</div>
            <div class="cl-act-desc">{{ $daysAgo ? 'Active in system' : 'Be the first to add a job or candidate.' }}</div>
        </div>

        {{-- Jobs (Order: Hiring, Hold, Closed with conditional status colors) --}}
        <div class="tbl-cell-jobs">
            <div class="cl-job-line {{ $job_active > 0 ? 'has-count cl-job-hiring' : 'no-count' }}">
                <span class="cl-job-bullet {{ $job_active > 0 ? 'bullet-hiring' : 'bullet-neutral' }}"></span>
                <span class="cl-job-num">{{ $job_active }}</span>
                <span class="cl-job-label">Hiring</span>
            </div>
            <div class="cl-job-line {{ $job_hold > 0 ? 'has-count cl-job-hold' : 'no-count' }}">
                <span class="cl-job-bullet {{ $job_hold > 0 ? 'bullet-hold' : 'bullet-neutral' }}"></span>
                <span class="cl-job-num">{{ $job_hold }}</span>
                <span class="cl-job-label">Hold</span>
            </div>
            <div class="cl-job-line {{ $job_closed > 0 ? 'has-count cl-job-closed' : 'no-count' }}">
                <span class="cl-job-bullet {{ $job_closed > 0 ? 'bullet-closed' : 'bullet-neutral' }}"></span>
                <span class="cl-job-num">{{ $job_closed }}</span>
                <span class="cl-job-label">Closed</span>
            </div>
        </div>

        {{-- Hiring Pipeline Funnel (No arrows) --}}
        <div class="tbl-cell-pipeline">
            <div class="cl-pipeline-flow">
                <div class="cl-pipe-step">
                    <span class="cl-pipe-pill pill-sourced">{{ $sourced }}</span>
                    <span class="cl-pipe-label">Sourced</span>
                </div>
                <div class="cl-pipe-step">
                    <span class="cl-pipe-pill pill-evaluated">{{ $evaluated }}</span>
                    <span class="cl-pipe-label">Evaluated</span>
                </div>
                <div class="cl-pipe-step">
                    <span class="cl-pipe-pill pill-interviews">{{ $interviews }}</span>
                    <span class="cl-pipe-label">Interviews</span>
                </div>
                <div class="cl-pipe-step">
                    <span class="cl-pipe-pill pill-offers">{{ $offers }}</span>
                    <span class="cl-pipe-label">Offers</span>
                </div>
                <div class="cl-pipe-step">
                    <span class="cl-pipe-pill pill-joined">{{ $joined }}</span>
                    <span class="cl-pipe-label">Joined</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="cl-empty-state">
        <div class="cl-empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
        </div>
        <div class="cl-empty-title">No internal clients found</div>
        <div class="cl-empty-desc">No internal clients match your selected filters.</div>
    </div>
@endif
