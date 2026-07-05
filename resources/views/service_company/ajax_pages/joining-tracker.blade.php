{{--
    Joining Tracker — table rows (AJAX partial for status_cus=6)
    WHY: renders the redesigned 5-column joining table (Candidate · Job & Department ·
    HR Owner · Joining Status · Joining Date) from the Elasticsearch _source documents
    returned by cvtracker.allcandidates. It reuses only real ES fields (of_joined,
    of_joined_date, of_offer_acceptance) so the per-row status matches the KPI/tab counts.
    Preserved functionality: candidate profile drawer (show-application-detail /
    data-row-id=wiseId), the [Bench] marker, and the stage-change modal (stage_sub_stage)
    is reachable by clicking the Joining Status cell — no existing action is lost.
--}}
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $company_id = auth()->user()->company_id;
    $today = strtotime(date('Y-m-d'));

    // Loaded once (static lookup arrays) and reused for the Job Status / Offer Amount / Stage cells.
    $job_status_arr  = GeneralMethod::job_status();
    $currencySymbole = GeneralMethod::currencySymbole();
    $salaryModeOffer = GeneralMethod::salaryModeOffer();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
            $row = (object) $rowArr;

            /* ---- Candidate avatar (photo, else coloured initial) ---- */
            $full_name = trim((string) @$row->full_name);
            $initial   = $full_name !== '' ? strtoupper(mb_substr($full_name, 0, 1)) : '?';
            $candTone  = GeneralMethod::icoinText($full_name ?: '?');
            $bench     = (@$row->condidate_type == 'bench_condidate') ? '<span class="jt-bench">Bench</span>' : '';

            /* Best-effort experience (real ES field total_exp). Kept exact (3.0, 5.6…), drop ".00". */
            $exp = trim((string) @$row->total_exp);
            if ($exp !== '' && strpos($exp, '.') !== false) {
                [$w, $d] = array_pad(explode('.', $exp), 2, '');
                $exp = ($d === '00') ? $w : $exp;
            }
            $expLabel = ($exp !== '' && $exp != 0) ? $exp . ' Yrs' : '';

            $designation = ucwords(mb_substr((string) @$row->current_designation, 0, 22));
            $candSub = $designation; // experience intentionally omitted per design

            /* ---- Job & Department (client acts as department proxy) ---- */
            $jobTitle = ucwords(mb_substr((string) @$row->title, 0, 24));
            $deptRaw  = GeneralMethod::companyDetailsByClient($row->jobId) ?: @$row->client_name;
            $dept     = ucwords(mb_substr((string) $deptRaw, 0, 22));

            /* ---- HR Owner (posted_by + company as the owner sub-label) ---- */
            if ($company_id != $row->ClientComID && $row->ClientComID != 0 && $row->ClientComID > 0) {
                $spok = DB::table('job_assign')->select('users.name')
                    ->join('users', 'users.id', 'job_assign.spok_user_id')
                    ->where('job_assign.job_id', $row->jobId)
                    ->where('job_assign.company_id', $row->ClientComID)->first();
                $owner = $spok ? $spok->name : $row->posted_by;
                $owner = $row->rec_candidate_assign_id ? $row->posted_by : $owner;
            } else {
                $owner = $row->posted_by;
            }
            $owner     = (string) $owner;
            $ownerName = ucwords(mb_substr($owner, 0, 16));
            $ownerTone = GeneralMethod::icoinText($owner ?: '?');
            $ownerImg  = !empty($row->posted_by_image) ? $row->posted_by_image : '';
            $ownerCompany = mb_substr((string) (@$row->post_company_name ?: @$row->company_name), 0, 16);

            /* ---- Joining status (derived from real ES fields) ---- */
            $joined      = (int) @$row->of_joined;
            $acceptance  = (int) @$row->of_offer_acceptance;
            $jdRaw       = @$row->of_joined_date;
            $hasDate     = ($jdRaw && $jdRaw != '0000-00-00 00:00:00');
            $jdTs        = $hasDate ? strtotime($jdRaw) : null;
            $jdDay       = $hasDate ? strtotime(date('Y-m-d', $jdTs)) : null;
            $diffDays    = $hasDate ? (int) floor(($jdDay - $today) / 86400) : null;

            if ($joined === 1) {
                $stLabel = 'Joined';       $stBg = '#dcfce7'; $stClr = '#16a34a'; $stSub = 'Joined Successfully';
            } elseif ($acceptance === 3) {
                $stLabel = 'No Show';      $stBg = '#fee2e2'; $stClr = '#dc2626'; $stSub = 'Candidate Unresponsive';
            } elseif ($hasDate && $diffDays === 0) {
                $stLabel = 'Joining Today';$stBg = '#ede9fe'; $stClr = '#7c3aed'; $stSub = 'Confirmed';
            } elseif ($hasDate && $diffDays < 0) {
                $stLabel = 'Delayed';      $stBg = '#ffedd5'; $stClr = '#ea580c'; $stSub = 'Reschedule Pending';
            } elseif ($acceptance === 1) {
                $stLabel = 'Pending Docs'; $stBg = '#fef3c7'; $stClr = '#d97706'; $stSub = 'Docs in Progress';
            } else {
                $stLabel = 'Pre-Joining';  $stBg = '#d1fae5'; $stClr = '#059669'; $stSub = 'Docs in Progress';
            }
            /* ---- Job Status (live job hiring state + requisition id + client/internal tag) ---- */
            $jobStatusRow   = GeneralMethod::jobDetails($row->jobId);
            $jobStatusRaw   = $jobStatusRow->status ?? '';
            $jobStatusClass = $job_status_arr[$jobStatusRaw] ?? 'text-muted';
            $jobStatusLabel = ucwords($jobStatusRaw);
            if ($jobStatusLabel == 'Active')   { $jobStatusLabel = 'Hiring'; }
            if ($jobStatusLabel == 'Inactive') { $jobStatusLabel = 'Closed'; }
            $recTag = __('messages.clinet_tag');
            if (((int) @$row->recommendation_id > 0 && @$row->client_comapny_id == $company_id) || @$row->client_comapny_id == $company_id) {
                $recTag = __('messages.internal_tag');
            }

            /* ---- Offer Amount (currency symbol + amount + /mode, honouring confidentiality) ---- */
            $employment_type = @$row->of_employment_type ?: '';
            $offer_currency  = $currencySymbole[$row->of_offer_currency ? @$row->of_offer_currency : 0];
            $offer_amount    = GeneralMethod::thousandsCurrencyFormat($row->of_offer_amount ? @$row->of_offer_amount : 0);
            $offer_mode      = $salaryModeOffer[$row->of_offer_mode ? @$row->of_offer_mode : 0];
            if (@$row->confidential == 1) {
                $offerAmount = 'Confidential';
                if (GeneralMethod::confidentialAmontShow($row->jobId)) {
                    $offerAmount = @$offer_currency . ' ' . @$offer_amount . ' / ' . mb_substr(@$offer_mode, 0, 1);
                }
            } else {
                $offerAmount = @$offer_currency . ' ' . @$offer_amount . ' / ' . mb_substr(@$offer_mode, 0, 1);
            }

            /* ---- Stage (pipeline master-status badge + configured sub-status) ---- */
            $stageName  = ucwords(mb_substr((string) @$row->name, 0, 14));
            $stageBadge = @$row->css_style;
            // "Joined" pipeline stage always reads green, regardless of the stored css_style.
            $stageStyle = '';
            if (strtolower(trim((string) @$row->name)) === 'joined') {
                $stageStyle = 'background:#dcfce7;color:#16a34a;';
                $stageBadge = '';
            }
            $stageChild = '';
            if (!empty(@$row->childStatus) && @$row->childStatus > 0) {
                $childStatus = DB::table('candidate_status')->select('name')->where('id', $row->childStatus)->first();
                if ($childStatus && !empty($childStatus->name)) {
                    $stageChild = $childStatus->name;
                }
            }

            /* ---- Joining date display + relative countdown ---- */
            $dateMain = $timeMain = $relTxt = ''; $relClr = '#94a3b8';
            if ($hasDate) {
                $dateMain = date('d M Y', $jdTs);
                $timeMain = date('h:i A', $jdTs) . ' IST';
                if ($diffDays === 0)      { $relTxt = 'Today';    $relClr = '#7c3aed'; }
                elseif ($diffDays === 1)  { $relTxt = 'Tomorrow'; $relClr = '#d97706'; }
                elseif ($diffDays > 1)    { $relTxt = 'In ' . $diffDays . ' Days'; $relClr = '#64748b'; }
                else {
                    $ago = abs($diffDays);
                    // Overdue indicator intentionally suppressed per design; joined-in-the-past still shows "N Days Ago".
                    if ($joined === 1) { $relTxt = $ago . ' Days Ago'; $relClr = '#16a34a'; }
                    else               { $relTxt = ''; }
                }
            }

            /* Stage modal trigger (client-owned jobs at some stages are not editable here). */
            $stageCss = 'stage_sub_stage';
            if ($row->thirdparty_company_id > 0 && $row->thirdparty_company_id == $company_id) {
                $stageCss = 'client_stage_sub_stage';
                if ($row->job_master_status_id != 8) { $stageCss = ''; }
            }
        @endphp
        <tr class="jt-row">
            {{-- Candidate --}}
            <td class="jt-td">
                <div class="jt-cand">
                    <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="show-application-detail jt-avatar-link">
                        @if(!empty($row->photo))
                            <img src="{{ $row->photo }}" class="jt-avatar-img" alt="">
                        @else
                            <span class="jt-avatar {{ $candTone }}">{{ $initial }}</span>
                        @endif
                    </a>
                    <div class="jt-cand-info">
                        <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="show-application-detail jt-cand-name" title="{{ ucwords($full_name) }}">
                            {{ ucwords(mb_substr($full_name, 0, 22)) }}{!! $bench !!}
                        </a>
                        <span class="jt-cand-role" title="{{ ucwords((string) @$row->current_designation) }}">{{ $candSub }}</span>
                    </div>
                </div>
            </td>

            {{-- Job & Department --}}
            <td class="jt-td">
                <span class="jt-job-title" title="{{ @$row->title }}">{{ $jobTitle }}</span>
                <span class="jt-job-type" title="{{ $deptRaw }}">{{ $dept }}</span>
            </td>

            {{-- Job Status --}}
            <td class="jt-td">
                <span class="jt-jobstatus {{ $jobStatusClass }}">{{ $jobStatusLabel }}</span>
                <span class="jt-jobstatus-sub">{{ @$row->jobId }} · {{ $recTag }}</span>
            </td>

            {{-- HR Owner --}}
            <td class="jt-td">
                <div class="jt-cand-info">
                    <span class="jt-owner-name" title="{{ $owner }}">{{ $ownerName }}</span>
                    <span class="jt-owner-role" title="{{ @$row->post_company_name }}">{{ $ownerCompany }}</span>
                </div>
            </td>

            {{-- Offer Amount --}}
            <td class="jt-td">
                <span class="jt-comp-amount">{!! $offerAmount !!}</span>
                <span class="jt-comp-type">{{ $employment_type }}</span>
            </td>

            {{-- Joining Date --}}
            <td class="jt-td">
                @if($hasDate)
                    <div class="jt-join">
                        <span class="jt-join-date">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            {{ $dateMain }}
                        </span>
                        <span class="jt-join-time">{{ $timeMain }}</span>
                        <span class="jt-join-countdown" style="color:{{ $relClr }}">{{ $relTxt }}</span>
                    </div>
                @else
                    <span class="jt-na">—</span>
                    <span class="jt-na-sub">Not scheduled</span>
                @endif
            </td>

            {{-- Stage (editable pipeline stage — opens the stage-change modal) --}}
            <td class="jt-td jt-td-stage">
                @if($stageCss)
                    <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}"
                       class="jt-stage-link {{ $stageCss }}" data-toggle="modal" data-target="#{{ $stageCss }}">
                        <span class="jt-stage-pill badge {{ $stageBadge }}" style="{{ $stageStyle }}">{{ $stageName ?: 'Stage' }}</span>
                        <span class="jt-stage-sub">{{ $stageChild }}</span>
                    </a>
                @else
                    <span class="jt-stage-pill badge {{ $stageBadge }}" style="{{ $stageStyle }}">{{ $stageName ?: '—' }}</span>
                    <span class="jt-stage-sub">{{ $stageChild }}</span>
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr class="jt-empty">
        <td colspan="7">
            <div class="jt-empty-inner">
                <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <p>No joining records found</p>
            </div>
        </td>
    </tr>
@endif
