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

        /* ── name / type ── */
        $client_type_css = 'show-detail';
        $isDirect = false;
        if ($company_id != $row->company_id && $row->staffing_company_relation_id > 0) {
            $client_type_css = 'show-detail-direct';
            $isDirect = true;
        }

        $client_Or_CompanyName = $row->name ?? '';
        if ($company_id != $row->company_id) {
            $client_Or_CompanyName = $row->company_name ?? $row->name ?? '';
        }

        $departmentArr = (@$row->ind_name) ? @$row->ind_name : @$row->main_ind_name;
        $client_id = $row->client_id ?? ($row->id ?? 0);
        $rowTargetId = $isDirect ? ($row->client_id ?: ($row->from_company_id ?: ($row->company_id ?: ($row->id ?? 0)))) : ($row->client_id ?: ($row->id ?? 0));

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
        if ($company_id == ($row->clients_company_id ?? 0)) {
            $jKey = 'internal_' . $client_id;
            $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::InternalClientJobsCountAllStatus($company_id, $client_id);
        } else {
            $jKey = 'direct_' . ($from_company_id ?? 0) . '_' . ($to_company_id ?? 0);
            $jobsCount = $jobsCountsMap[$jKey] ?? GeneralMethod::DirectClientJobsCountAllStatus($from_company_id, $to_company_id);
        }
        $job_active   = (int)($jobsCount->job_active ?? 0);
        $job_closed   = (int)($jobsCount->job_inactive ?? 0);
        $job_hold     = (int)($jobsCount->job_hold ?? 0);

        /* ── performance funnel ── */
        if ($company_id != $row->company_id) {
            $pKey = 'direct_' . ($from_company_id ?? 0) . '_' . ($to_company_id ?? 0);
            $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::DirectClientCandidatesCountList($from_company_id, $to_company_id);
        } else {
            $pKey = 'internal_' . $client_id;
            $perf = $candidateCountsMap[$pKey] ?? GeneralMethod::InternalClientCandidatesCountList($client_id);
        }
        $sourced       = (int)($perf->sourced ?? 0);
        $evaluated     = (int)($perf->evaluated ?? 0);
        $interviews    = (int)($perf->all_interview ?? 0);
        $offers        = (int)($perf->offers ?? 0);
        $joined        = (int)($perf->Joined ?? 0);

        /* ── connected on ── */
        if ($row->staffing_company_relation_id > 0 && ($row->to_company_id ?? 0) == auth()->user()->company_id) {
            $createdTimestamp = $row->from_created_at ?? $row->created_at ?? null;
        } else {
            $createdTimestamp = $row->created_at ?? null;
        }
        $newDate = $createdTimestamp ? date('d M, Y', strtotime($createdTimestamp)) : '-';
        $daysAgo = $createdTimestamp ? GeneralMethod::timeAgo($createdTimestamp) : '';

        /* ── client type tag ── */
        $client_type = __('messages.internal_client_tag');
        if ($company_id != $row->company_id) {
            $client_type = __('messages.direct_clinet_tag');
        }

        $locMain = '';
        if ($city && $stateName) {
            $locMain = ucwords($city) . ', ' . ucwords($stateName);
        } elseif ($city) {
            $locMain = ucwords($city);
        } elseif ($stateName) {
            $locMain = ucwords($stateName);
        }

        /* ── logo / avatar calculation ── */
        $background_image = '';
        if ($company_id != $row->company_id) {
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $background_image = @$row->logo;
            } elseif (!empty(@$row->website)) {
                $domainGet = GeneralMethod::getDomain($row->website);
                if ($domainGet) {
                    $background_image = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=128";
                }
            }
        } else {
            // Internal client: strictly own uploaded logo or website favicon (never parent agency logo)
            if (!empty(@$row->logo_logical_path) && strlen(@$row->logo_logical_path) > 10) {
                $background_image = @$row->logo_logical_path;
            } elseif (!empty(@$row->website)) {
                $domainGet = GeneralMethod::getDomain($row->website);
                if ($domainGet) {
                    $background_image = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=128";
                }
            }
        }

        if (!empty($background_image) && !str_starts_with($background_image, 'http') && !str_starts_with($background_image, '//')) {
            $background_image = asset($background_image);
        }

        // initials & background color
        $cleanName = trim(@$client_Or_CompanyName ?: 'Client');
        $strVal = strtolower($cleanName[0] ?? 'c');
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
            $bgTextDecore = 'bg-light-info text-info';
        } else {
            $bgTextDecore = 'bg-light-primary text-primary';
        }

        $nameParts = array_values(array_filter(explode(' ', $cleanName)));
        $initials = '';
        if (count($nameParts) >= 2) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
        } elseif (count($nameParts) === 1) {
            $initials = strtoupper(mb_substr($nameParts[0], 0, 2));
        } else {
            $initials = 'CL';
        }
    @endphp

    <div class="tbl-r">
        {{-- Checkbox --}}
        <div class="tbl-cell-check">
            <input type="checkbox" class="tbl-cb" value="{{ $rowTargetId }}" onclick="event.stopPropagation()">
        </div>

        {{-- Client (with Company Logo / Initials Avatar) --}}
        <div class="tbl-cell-client {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}" title="{{ ucwords(@$client_Or_CompanyName) }}">
            <div class="cl-company-wrapper">
                <div class="cl-logo-box">
                    @if (!empty($background_image) && strlen($background_image) > 10)
                        <img src="{{ $background_image }}" alt="{{ $initials }}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
                        <span class="cl-initials-fallback {{ $bgTextDecore }}" style="display:none;">{{ $initials }}</span>
                    @else
                        <span class="cl-initials-fallback {{ $bgTextDecore }}">{{ $initials }}</span>
                    @endif
                </div>
                <div class="cl-details-box">
                    <div class="cl-name">
                        {{ ucwords(@$client_Or_CompanyName) }}
                    </div>
                    @if(!empty($departmentArr))
                        <div class="cl-ind" title="{{ @$departmentArr }}">{{ @$departmentArr }}</div>
                    @endif
                    @if(!empty($companySizeText))
                        <div class="cl-size">{{ $companySizeText }}</div>
                    @endif
                </div>
            </div>
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

        {{-- Date (formerly Type & Connected On) --}}
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
            <div class="cl-type-name {{ $isDirect ? 'type-direct' : 'type-internal' }} {{ $client_type_css }}" data-row-id="{{ $rowTargetId }}">
                {{ $client_type }}
            </div>
        </div>

        {{-- Last Activity --}}
        <div class="tbl-cell-activity">
            <div class="cl-act-main">{{ $daysAgo ? $daysAgo : 'No activity yet' }}</div>
            <div class="cl-act-desc">{{ $daysAgo ? 'Active in system' : 'Be the first to add a job or candidate.' }}</div>
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
        <div class="cl-empty-title">No clients found</div>
        <div class="cl-empty-desc">No clients match your selected filters or search query.</div>
    </div>
@endif
