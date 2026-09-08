@php
    use App\Helper\GeneralMethod;
    use Carbon\Carbon;
    $salaryMode       = GeneralMethod::salaryMode();
    $currencySymbole  = GeneralMethod::currencySymbole();
    $feedback_status  = GeneralMethod::feedback_status();
    $available_status = GeneralMethod::available_status();

    // Follow-up statuses — read-only display only (no actions)
    $followupMap = collect();
    if (\Illuminate\Support\Facades\Schema::hasColumn('interview_schedules', 'followup_status')) {
        $followupMap = DB::table('interview_schedules')
            ->whereIn('id', collect($data)->pluck('id')->all())
            ->get(['id', 'followup_status', 'followup_status_at'])
            ->keyBy('id');
    }
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
      $row = (object) $rowArr;
    @endphp
        <tr>
            {{-- Candidate name + avatar + follow-up status (view-only, no dropdown) --}}
            <td>
                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->full_name);
                    $str = $strVal = strtolower($str[0]);
                    $str = ord($str);
                    if ($str >= 97 && $str <= 101)      { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                    elseif ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                    elseif ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                    elseif ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
                    elseif ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
                    else                                { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }

                    // Follow-up read-only status badge
                    $fuRow    = $followupMap->get($row->id);
                    $fuStatus = is_object($fuRow) ? ($fuRow->followup_status ?? null) : null;
                    $fuAt     = is_object($fuRow) ? ($fuRow->followup_status_at ?? null) : null;
                    $fuLabels = ['confirmed' => 'Confirmed', 'no-response' => 'No Response', 'rescheduled' => 'Rescheduled'];
                    $fuLabel  = $fuLabels[$fuStatus] ?? null;
                    $fuStyle  = '';
                    if ($fuStatus === 'confirmed')    { $fuStyle = 'background:#e8fff3;color:#17a75a;border:1px solid #b8f0d4;'; }
                    elseif ($fuStatus === 'no-response')  { $fuStyle = 'background:#fff0f0;color:#d9534f;border:1px solid #ffd5d5;'; }
                    elseif ($fuStatus === 'rescheduled')  { $fuStyle = 'background:#eef2ff;color:#3b5fc8;border:1px solid #c8d8f8;'; }
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <div class="symbol symbol-circle symbol-38px overflow-hidden flex-shrink-0">
                        <div class="symbol-label {{ $bgTextDecore }} border-radius-5">{{ ucwords($strVal) }}</div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ ucwords(@$row->full_name) }}">
                            {{ ucwords(mb_substr(@$row->full_name, 0, 22)) }}
                        </span>
                        <span class="text-muted fw-bold d-block fs-7">
                            {{ ucwords(mb_substr(@$row->current_designation, 0, 22)) }}
                        </span>
                        {{-- Follow-up: show current status as a plain read-only pill, or nothing --}}
                        @if ($fuLabel)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;margin-top:3px;width:fit-content;{{ $fuStyle }}">
                                {{ $fuLabel }}
                            </span>
                            @if ($fuAt)
                                <span style="font-size:10px;color:#a1a5b7;margin-top:2px;">
                                    {{ Carbon::parse($fuAt)->format('d M, h:i A') }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </td>

            {{-- Job title + company + Internal/Client badge + Job ID (view-only) --}}
            <td>
                @php
                    $company_client_name = @$row->company_name ?: (@$row->client_name ?: '');
                    if (auth()->user()->company_id == (@$row->job_company_id ?? 0)) {
                        $company_client_name = @$row->client_name ?: (@$row->company_name ?: '');
                    }
                    $jobTypeLabel = 'Internal';
                    $jobTypeCls   = 'cit-badge-internal';
                    if (!empty($row->recommendation_id) && $row->recommendation_id > 0
                        && isset($row->to_company_id) && $row->to_company_id == auth()->user()->company_id) {
                        $jobTypeLabel = 'Client';
                        $jobTypeCls   = 'cit-badge-client';
                    }
                @endphp
                <div class="d-flex flex-column">
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{ ucwords($row->job_title) }}">
                        {{ ucwords(mb_substr($row->job_title, 0, 20)) }}
                    </span>
                    <span class="text-muted fw-bold d-block fs-7">
                        {{ ucwords(mb_substr($company_client_name, 0, 20)) }}
                    </span>
                    <div class="cit-job-tags">
                        <span class="cit-job-type-badge {{ $jobTypeCls }}">{{ $jobTypeLabel }}</span>
                        <span class="cit-dot-sep"></span>
                        <span class="cit-job-id-tag">{{ @$row->job_id }}</span>
                    </div>
                </div>
            </td>

            {{-- Mode pill (view-only) --}}
            <td>
                @php
                    $modeLower = strtolower(trim(@$row->mode_name ?? ''));
                    if (str_contains($modeLower, 'video') || str_contains($modeLower, 'online'))                                            { $modeCls = 'cit-mode-online'; }
                    elseif (str_contains($modeLower, 'phone') || str_contains($modeLower, 'call'))                                          { $modeCls = 'cit-mode-phone'; }
                    elseif (str_contains($modeLower, 'person') || str_contains($modeLower, 'office') || str_contains($modeLower, 'offline')) { $modeCls = 'cit-mode-offline'; }
                    else { $modeCls = 'cit-mode-default'; }
                @endphp
                <div class="d-flex flex-column align-items-start gap-1">
                    <span class="cit-mode-pill {{ $modeCls }}">{{ ucwords(mb_substr(@$row->mode_name, 0, 12)) }}</span>
                    @if (!empty($row->mode_lavel_name))
                        <span class="cit-level-tag">{{ ucwords(mb_substr($row->mode_lavel_name, 0, 14)) }}</span>
                    @endif
                </div>
            </td>

            {{-- Interviewers: name + company (view-only, matches screenshot layout) --}}
            <td>
                @php
                    $interviewerRows = collect();
                    if (!empty($row->interviewers)) {
                        $interviewersArr = array_filter(array_map('trim', explode(',', $row->interviewers)));
                        $interviewerRows = DB::table('users')
                            ->select('users.id', 'users.name')
                            ->where('users.company_id', '=', $row->company_id)
                            ->whereIn('users.name', $interviewersArr)
                            ->get();
                    }
                    $companyShort = ucwords(mb_substr(@$row->company_name, 0, 14));
                @endphp
                <div class="d-flex flex-column gap-1">
                    @if ($interviewerRows->isNotEmpty())
                        @foreach ($interviewerRows as $intVal)
                            <div class="d-flex flex-column" style="line-height:1.4;">
                                <span class="fw-bold text-dark" style="font-size:12.5px;">
                                    {{ ucwords($intVal->name) }}
                                </span>
                                <span style="font-size:11px; color:#009ef7; font-weight:500;">
                                    {{ $companyShort }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="d-flex flex-column" style="line-height:1.4;">
                            <span class="fw-bold text-dark" style="font-size:12.5px;">N/A</span>
                            <span style="font-size:11px; color:#009ef7; font-weight:500;">
                                {{ $companyShort }}
                            </span>
                        </div>
                    @endif
                </div>
            </td>

            {{-- Source badge (view-only) --}}
            <td>
                @php $src = trim(@$row->source ?? ''); @endphp
                <span class="cit-source-badge" data-toggle="tooltip" title="{{ $src }}">
                    {{ mb_strlen($src) ? mb_substr($src, 0, 14) : '—' }}
                </span>
            </td>

            {{-- Interview date + time (view-only) --}}
            <td>
                <div class="d-flex flex-column">
                    <span class="cit-date-bold">{{ Carbon::parse($row->schedule_date)->format('d M, Y') }}</span>
                    <span class="cit-date-time">
                        {{ $row->schedule_to_time }}{{ $row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '' }}
                    </span>
                </div>
            </td>

            {{-- Feedback status pill (view-only) --}}
            <td>
                @php
                    $available_status_name = '';
                    if ($row->available_status > 0) {
                        $available_status_name = @$available_status[@$row->available_status]['name'];
                    }
                    $feedback = ucwords(mb_substr(@$feedback_status[@$row->feedback]['name'], 0, 22));
                    $asLower  = strtolower($available_status_name);
                    if (str_contains($asLower, 'select'))                                      { $fbCls = 'cit-fb-selected'; }
                    elseif (str_contains($asLower, 'reject'))                                  { $fbCls = 'cit-fb-rejected'; }
                    elseif (str_contains($asLower, 'hold'))                                    { $fbCls = 'cit-fb-hold'; }
                    elseif (str_contains($asLower, 'next') || str_contains($asLower, 'round')) { $fbCls = 'cit-fb-next'; }
                    else                                                                       { $fbCls = 'cit-fb-default'; }
                    $fbLabel = $available_status_name ? ucwords(mb_substr($available_status_name, 0, 14)) : '—';
                @endphp
                <div class="d-flex flex-column align-items-start gap-1">
                    <span class="cit-fb-pill {{ $fbCls }}">{{ $fbLabel }}</span>
                    @if ($feedback)
                        <span class="cit-date-time">{{ mb_substr($feedback, 0, 14) }}</span>
                    @endif
                </div>
            </td>

            {{-- Star rating (view-only) --}}
            <td>
                @php
                    $average_star_rating = (int) ($row->average_star_rating ?? 0);
                    $ratingLabels = [1 => 'Bad', 2 => 'Poor', 3 => 'Average', 4 => 'Good', 5 => 'Excellent'];
                @endphp
                <div class="cit-stars">
                    @for ($s = 1; $s <= 5; $s++)
                        <i class="fas fa-star {{ $s <= $average_star_rating ? 'cit-star-fill' : 'cit-star-empty' }}"></i>
                    @endfor
                </div>
                @if ($average_star_rating > 0)
                    <div class="cit-rating-label">{{ $ratingLabels[$average_star_rating] ?? '' }}</div>
                @endif
            </td>

            {{-- Stage badge (view-only) --}}
            <td>
                @php
                    $stageSub = '';
                    if ((@$row->master_status_id ?? 0) == 8) { $stageSub = 'Private'; }
                    if (!empty(@$row->child_status_id) && @$row->child_status_id > 0) {
                        $childStatus = DB::table('candidate_status')->select('*')->where('id', $row->child_status_id)->first();
                        if (!empty($childStatus)) { $stageSub = @$childStatus->name; }
                    }
                @endphp
                <div class="d-flex flex-column align-items-start">
                    <span class="badge fw-bolder {{ @$row->css_style }} px-2 py-1" style="font-size:11px">
                        {{ mb_substr(@$row->master_status_name, 0, 12) }}
                    </span>
                    @if ($stageSub)
                        <span class="text-muted fw-bold d-block fs-7 mt-1">{{ $stageSub }}</span>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" style="text-align:center; padding:40px 20px; color:#a1a5b7; font-size:13px;">
            <i class="fas fa-calendar-alt" style="font-size:32px; color:#e4e6ef; display:block; margin-bottom:10px;"></i>
            No interviews scheduled for this client.
        </td>
    </tr>
@endif
