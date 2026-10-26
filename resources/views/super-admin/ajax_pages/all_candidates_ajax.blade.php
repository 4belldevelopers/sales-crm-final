@php
    use App\Helper\GeneralMethod;

    $salaryMode = $salaryMode ?? GeneralMethod::salaryMode();
    $currencySymbole = $currencySymbole ?? GeneralMethod::currencySymbole();
    $job_status_arr = $job_status_arr ?? GeneralMethod::job_status();
    $companyMap = $companyMap ?? [];
    $cityMap = $cityMap ?? [];
    $jobDetailsMap = $jobDetailsMap ?? [];
    $mandatoryBasis = $mandatorySkillsForDisplay ?? [];
    $optionalBasis = $optionalSkillsForDisplay ?? [];
@endphp

@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;
            $row->thirdparty_company_id = $row->ClientComID ?? 0;
            $candidateId = $row->candidate_id ?? $row->id;
            $wiseId = $row->wiseId ?? $row->id;
            $jobId = $row->jobId ?? 0;
            $hasResume = !empty($row->resume);

            // Skills & Match Percentage Calculation (Pure Client-side / In-RAM, 0 MySQL)
            $skillArr = !empty($row->skills) ? explode(',', (string) $row->skills) : [];
            $candidateSkillsLower = array_map(fn($s) => strtolower(trim($s)), $skillArr);
            $percentBasis = array_merge($mandatoryBasis, $optionalBasis);
            $percentage = '0%';

            if (!empty($percentBasis)) {
                $matches = 0;
                foreach ($percentBasis as $req) {
                    foreach ($candidateSkillsLower as $cand) {
                        if (GeneralMethod::skillMatchesPhrasePrefix($req, $cand)) {
                            $matches++;
                            break;
                        }
                    }
                }
                $percentage = ((int) round(min(($matches / count($percentBasis)) * 100, 100))) . '%';
            } elseif (!empty($skillArr)) {
                $percentage = '85%';
            }

            $numericPct = (int) round((float) rtrim(@$percentage ?: '0', '%'));
            $matchStyle = GeneralMethod::getSkillMatchBadgeStyle($percentage);
            $matchBadge = '<span class="badge my-1 jw-row-match-badge" data-candidate-id="' . $candidateId . '" data-wise-id="' . $wiseId . '" data-match-pct="' . $numericPct . '" style="background:' . $matchStyle['bg'] . ';color:' . $matchStyle['color'] . ';font-weight:600;font-size:10px;padding:2px 6px;">Match ' . $percentage . '</span>';

            // Highlighting mandatory/optional skills
            $skilltag = '';
            foreach ($skillArr as $sval) {
                $svalClean = trim($sval);
                $svalLower = strtolower($svalClean);
                $isMandatory = in_array($svalLower, $mandatoryBasis, true);
                $isOptional = in_array($svalLower, $optionalBasis, true);

                if ($isMandatory) {
                    $skilltag .= '<span class="badge my-1" style="background:#dbeafe;color:#1d4ed8;font-weight:600;border:1px solid #bfdbfe;font-size:10px;padding:2px 6px;" title="Mandatory Skill">★ ' . htmlspecialchars(ucfirst($svalClean)) . '</span> ';
                } elseif ($isOptional) {
                    $skilltag .= '<span class="badge my-1" style="background:#dcfce7;color:#15803d;font-weight:600;border:1px solid #bbf7d0;font-size:10px;padding:2px 6px;" title="Required Skill">' . htmlspecialchars(ucfirst($svalClean)) . '</span> ';
                } else {
                    $skilltag .= '<span class="badge badge-light my-1 skill" style="font-weight:400;font-size:10px;padding:2px 6px;">' . htmlspecialchars(ucfirst($svalClean)) . '</span> ';
                }
            }

            // Avatar initial styling
            $fullName = trim((string) ($row->full_name ?? ''));
            $initial = !empty($fullName) ? strtoupper(mb_substr($fullName, 0, 1)) : 'C';
            $ord = !empty($fullName) ? ord(strtolower($initial)) : 100;
            if ($ord >= 97 && $ord <= 101) {
                $avatarBg = 'bg-light-danger text-danger';
            } elseif ($ord >= 102 && $ord <= 106) {
                $avatarBg = 'bg-light-warning text-warning';
            } elseif ($ord >= 107 && $ord <= 111) {
                $avatarBg = 'bg-light-primary text-primary';
            } elseif ($ord >= 112 && $ord <= 116) {
                $avatarBg = 'bg-light-success text-success';
            } else {
                $avatarBg = 'bg-light-info text-info';
            }

            $duplicate = (int) ($row->duplicate ?? 0);
            $benchTag = (@$row->condidate_type === 'bench_condidate') ? '<sup style="color:#0865dd!important;font-weight:bold;">[Bench]</sup>' : '';
        @endphp

        <tr style="height: 102px; position: relative;" data-candidate-id="{{ $candidateId }}" data-wise-id="{{ $wiseId }}" data-job-id="{{ $jobId }}">
            {{-- Checkbox --}}
            <td style="width: 30px;">
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input chk_list_item" name="chk_list_item[]" type="checkbox"
                        data-jobId="{{ $jobId }}" data-wiseId="{{ $wiseId }}" data-candidate-id="{{ $candidateId }}"
                        data-match="{{ $numericPct }}" data-skills="{{ e($row->skills ?? '') }}"
                        data-cname="{{ e($fullName) }}" data-has-resume="{{ $hasResume ? '1' : '0' }}"
                        value="{{ $candidateId }}" />
                </div>
            </td>

            {{-- Candidate Name + Avatar + Designation + Match Badge + Skills --}}
            <td>
                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left;">
                    <a href="javascript:void(0);" data-row-id="{{ $wiseId }}" class="show-application-detail {{ $duplicate === 1 ? 'btn btn-icon btn-light-danger pulse pulse-danger mr-5' : '' }}">
                        <div class="symbol-label {{ $avatarBg }} fw-bolder fs-4">{{ $initial }}</div>
                        @if($duplicate === 1)
                            <span class="pulse-ring"></span>
                        @endif
                    </a>
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <a href="javascript:void(0);" data-row-id="{{ $wiseId }}" class="show-application-detail fw-bolder fs-6 {{ $duplicate === 1 ? 'text-danger' : 'text-dark' }} text-hover-primary" title="{{ htmlspecialchars($fullName) }}">
                            {{ htmlspecialchars(ucwords(mb_substr($fullName, 0, 26))) }} {!! $benchTag !!}
                        </a>
                    </div>
                    <a href="javascript:void(0);" data-row-id="{{ $wiseId }}" class="show-application-detail text-muted fw-bold d-block fs-7 text-hover-primary" title="{{ htmlspecialchars(@$row->current_designation ?? '') }}">
                        {{ htmlspecialchars(ucwords(mb_substr(@$row->current_designation ?? 'N/A', 0, 28))) }}
                    </a>
                </div>
                <div style="position: absolute; bottom: 4px; white-space: nowrap; width: 94%; overflow: hidden;">
                    {!! $matchBadge !!}
                    {!! $skilltag !!}
                </div>
            </td>

            {{-- Experience --}}
            <td>
                @php
                    $totalExp = trim((string) (@$row->total_exp ?? '0'));
                    $relExp = trim((string) (@$row->rel_exp ?? '0'));
                    $totalExpFmt = (strpos($totalExp, '.') !== false && explode('.', $totalExp)[1] === '00') ? explode('.', $totalExp)[0] : $totalExp;
                    $relExpFmt = (strpos($relExp, '.') !== false && explode('.', $relExp)[1] === '00') ? explode('.', $relExp)[0] : $relExp;
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ $totalExpFmt }} Y</a>
                    <span class="text-muted fw-bold d-block fs-7">{{ $relExpFmt }} Y</span>
                </div>
            </td>

            {{-- Salary (CTC / ECTC) --}}
            <td>
                @php
                    $currencySym = $currencySymbole[@$row->current_ctc_cur] ?? $currencySymbole[0] ?? '₹';
                    $ectcSym = $currencySymbole[@$row->current_ectc_cur] ?? $currencySym;
                    $ctcVal = GeneralMethod::thousandsCurrencyFormat($row->ctc ?? 0);
                    $ectcVal = GeneralMethod::thousandsCurrencyFormat($row->ectc ?? 0);
                    $ctcValClean = explode('.', $ctcVal)[0];
                    $ectcValClean = explode('.', $ectcVal)[0];
                    $mode = $salaryMode[@$row->current_ctc_mode] ?? 'Annual';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">
                        {!! $ectcSym !!} {{ $ectcValClean }}<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/{{ $mode }}</span>
                    </a>
                    <span class="text-muted fw-bold d-block fs-7">
                        {!! $currencySym !!} {{ $ctcValClean }}<span style="font-size:.7em;font-weight:500;color:inherit;white-space:nowrap;">/{{ $mode }}</span>
                    </span>
                </div>
            </td>

            {{-- Notice Period & Current Company --}}
            <td>
                @php
                    $npRaw = (string) (@$row->np_id ?? 'N/A');
                    $npColor = '#1e293b';
                    $npDisplay = $npRaw;

                    if ($npRaw === 'Immediate' || @$row->immediate_join === 'Immediate') {
                        $npDisplay = 'Immediate';
                        $npColor = '#16a34a';
                    } elseif (@$row->serving_notice_period_mode == 0 && is_numeric($npRaw)) {
                        $npDisplay = $npRaw . ' Days';
                        $npColor = '#d97706';
                    } elseif (@$row->serving_notice_period_mode == 1) {
                        $npFormatted = GeneralMethod::ymdTodmyNotTime($npRaw);
                        if (date('Y-m-d', strtotime($npRaw)) < date('Y-m-d')) {
                            $npDisplay = 'Immediate';
                            $npColor = '#16a34a';
                        } else {
                            $npDisplay = 'SNP LWD ' . $npFormatted;
                            $npColor = '#9333ea';
                        }
                    }

                    $currentCompany = @$row->apl_company_name ?: (@$row->current_company ?: (@$row->company_name ?: 'N/A'));
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="fw-bolder fs-6" style="color:{{ $npColor }};">{{ $npDisplay }}</a>
                    <span class="text-muted fw-bold d-block fs-7" title="{{ htmlspecialchars(ucwords($currentCompany)) }}">
                        {{ htmlspecialchars(ucwords(mb_substr($currentCompany, 0, 14))) }}
                    </span>
                </div>
            </td>

            {{-- Location & Education --}}
            <td>
                @php
                    $cityName = @$row->city_name ?: (@$cityMap[@$row->city_id] ?: (@$row->city ?: '----'));
                    $countryName = @$row->country_name ?: (@$row->country ?: '----');
                    $countryCode = strtoupper(mb_substr($countryName, 0, 2));
                    $qualification = @$row->qualification_name ?: 'N/A';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" title="{{ htmlspecialchars(ucwords($cityName) . ', ' . ucwords($countryName)) }}">
                        {{ htmlspecialchars(ucwords(mb_substr($cityName, 0, 12))) }}, {{ $countryCode }}
                    </a>
                    <span class="text-muted fw-bold d-block fs-7" title="{{ htmlspecialchars(strtoupper($qualification)) }}">
                        {{ htmlspecialchars(strtoupper(mb_substr($qualification, 0, 16))) }}
                    </span>
                </div>
            </td>

            {{-- Ownership / Source (Recruiter & Tenant Company) --}}
            <td>
                @php
                    $postedBy = @$row->posted_by ?: 'System';
                    $tenantCompanyId = (int) (@$row->company_id ?? 0);
                    $tenantCompanyName = $companyMap[$tenantCompanyId] ?? (@$row->company_name ?: 'Platform');
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" title="{{ htmlspecialchars(ucwords($postedBy)) }}">
                        {{ htmlspecialchars(ucwords(mb_substr($postedBy, 0, 12))) }}
                    </a>
                    <span class="text-muted fw-bold d-block fs-7" title="{{ htmlspecialchars($tenantCompanyName) }}">
                        {{ htmlspecialchars(mb_substr($tenantCompanyName, 0, 14)) }}
                    </span>
                </div>
            </td>

            {{-- Jobs & Client Company --}}
            <td>
                @php
                    $jobObj = $jobDetailsMap[$jobId] ?? null;
                    $jobTitle = $jobObj->title ?? (@$row->title ?: 'General Application');
                    $clientName = $jobObj->client_name ?? (@$row->client_name ?: 'Direct');
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" title="{{ htmlspecialchars($jobTitle) }}">
                        {{ htmlspecialchars(ucwords(mb_substr($jobTitle, 0, 16))) }}
                    </a>
                    <span class="text-muted fw-bold d-block fs-7" title="{{ htmlspecialchars($clientName) }}">
                        {{ htmlspecialchars(ucwords(mb_substr($clientName, 0, 18))) }}
                    </span>
                </div>
            </td>

            {{-- Status --}}
            <td>
                @php
                    $jobStatus = $jobObj->status ?? (@$row->job_status ?: 'active');
                    $jobStatusLabel = ($jobStatus === 'active') ? 'Hiring' : (($jobStatus === 'inactive') ? 'Closed' : ucwords($jobStatus));
                    $statusClass = $job_status_arr[$jobStatus] ?? 'text-success';

                    $recommendation_id = (int) (@$row->recommendation_id ?? 0);
                    $recommendation_text = __('messages.clinet_tag') ?: 'Client';
                    if (
                        ($recommendation_id > 0 && @$row->client_comapny_id == @$row->company_id) ||
                        @$row->client_comapny_id == @$row->company_id ||
                        empty(@$row->client_comapny_id) ||
                        @$row->client_comapny_id == 0 ||
                        (int) (@$row->thirdparty_company_id ?? 0) === 0
                    ) {
                        $recommendation_text = __('messages.internal_tag') ?: 'Internal';
                    }
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="d-flex flex-column fw-bolder fs-6 {{ $statusClass }}">
                        {{ $jobStatusLabel }}
                    </a>
                    <span class="fw-bold text-muted d-block fs-7">{{ $jobId }} -{{ $recommendation_text }}</span>
                </div>
            </td>

            {{-- Stage (Drawer Trigger) --}}
            <td>
                @php
                    $stageName = @$row->name ?: (@$row->master_status_name ?: 'InReview');
                    $subStageName = @$row->child_status_name ?: (@$row->child_process_name ?: 'Private');
                    $stageCss = @$row->css_style ?: 'bg-light-primary text-primary';
                @endphp
                <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="{{ $wiseId }}" class="d-flex flex-column client_stage_sub_stage">
                        <span class="badge fw-bolder fs-6 {{ $stageCss }} px-2 py-1">
                            {{ htmlspecialchars(ucwords(mb_substr($stageName, 0, 12))) }}
                        </span>
                        <span class="text-muted fw-bold d-block fs-7 px-1 py-1">
                            {{ htmlspecialchars(mb_substr($subStageName, 0, 12)) }}
                        </span>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">
        <td colspan="10" style="text-align: center; color: #5e6278; font-weight: 500; padding: 40px 0;">
            No candidates match the specified criteria
        </td>
    </tr>
@endif
