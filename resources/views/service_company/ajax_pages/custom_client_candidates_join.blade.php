@php
    use App\Helper\GeneralMethod;
    use App\Services\CurrencyService;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
        $row = (object) $rowArr;
        $row->thirdparty_company_id = $row->ClientComID ?? 0;

        // 1. Candidate Avatar & Styling
        $fullName = trim((string) (@$row->full_name ?? ''));
        $firstChar = !empty($fullName) ? strtolower($fullName[0]) : '';
        $charOrd = !empty($firstChar) ? ord($firstChar) : 0;

        if ($charOrd >= 97 && $charOrd <= 101) { // a-e
            $avatarClass = 'oj-avatar-red';
        } elseif ($charOrd >= 102 && $charOrd <= 106) { // f-j
            $avatarClass = 'oj-avatar-orange';
        } elseif ($charOrd >= 107 && $charOrd <= 111) { // k-o
            $avatarClass = 'oj-avatar-blue';
        } elseif ($charOrd >= 112 && $charOrd <= 116) { // p-t
            $avatarClass = 'oj-avatar-green';
        } elseif ($charOrd >= 117 && $charOrd <= 122) { // u-z
            $avatarClass = 'oj-avatar-purple';
        } else {
            $avatarClass = 'oj-avatar-gray';
        }

        $profileSidebarCss = ($row->thirdparty_company_id > 0) ? 'client-show-application-detail' : 'show-application-detail';
        $duplicate = $row->duplicate ?? 0;
        $initial = !empty($fullName) ? strtoupper($fullName[0]) : 'C';
        $designation = trim((string) (@$row->current_designation ?? ''));

        // 2. Job Info
        $jobTitle = trim((string) (@$row->title ?? ''));
        $clientName = trim((string) (@$row->client_name ?? ''));

        // 3. Job Status, ID & Client
        $jobStatus = !empty($row->job_status) ? ucwords(trim($row->job_status)) : 'Hiring';
        $isInternal = (@$row->client_comapny_id == ($company_id ?? auth()->user()->company_id) || empty($row->ClientComID) || $row->ClientComID == 0);
        $relationshipLabel = $isInternal ? 'Internal' : 'Direct';
        $jobId = @$row->jobId ?: '—';

        // 4. Ownership
        $recruiterName = !empty($row->posted_by) ? $row->posted_by : (!empty($row->submitted_by_name) ? $row->submitted_by_name : (!empty($row->spokperson_name) ? $row->spokperson_name : (!empty($row->upload_by_name) ? $row->upload_by_name : (@$row->source ?: 'Recruiter'))));
        $organizationName = @$row->submitted_by_company_name ?: (@$row->post_company_name ?: (@$row->uc_company_name ?: (@$row->from_company_name ?: (@$row->company_name ?: '—'))));

        // 5. Compensation
        $compAmt = !empty($row->of_offer_amount) ? $row->of_offer_amount : (!empty($row->proposed_ctc) ? $row->proposed_ctc : (!empty($row->ctc) ? $row->ctc : null));
        $currCode = !empty($row->of_offer_currency) ? CurrencyService::resolveCurrencyCode($row->of_offer_currency) : (!empty($row->current_ctc_cur) ? CurrencyService::resolveCurrencyCode($row->current_ctc_cur) : 'INR');
        $modeKey = isset($row->of_salary_mode) ? $row->of_salary_mode : (isset($row->current_ctc_mode) ? $row->current_ctc_mode : 0);
        $modeAbbr = isset($salaryMode[$modeKey]) ? mb_substr($salaryMode[$modeKey], 0, 1) : 'A';
        $formattedComp = (!empty($compAmt) && is_numeric($compAmt)) ? number_format((float) $compAmt) : '—';
        $employmentType = !empty($row->employmentType) ? ucwords($row->employmentType) : 'Full-time';

        // 6. Offer Status & Stage Detection
        $masterStatusId = (int) (@$row->job_master_status_id ?? 0);
        $isJoined = ($masterStatusId === 6 || in_array($masterStatusId, [6, '6'], true));

        // 7. Joining Target Date & Countdown
        $targetJoiningDate = !empty($row->of_joined_date) ? $row->of_joined_date : (!empty($row->joining_date) ? $row->joining_date : null);
        $formattedJoinDate = '—';
        $joinSubText = '9:00 AM (GMT+05:30)';
        $countdownHtml = '';

        if (!empty($targetJoiningDate)) {
            try {
                $joinCarbon = Carbon::parse($targetJoiningDate);
                $formattedJoinDate = $joinCarbon->format('d M Y');
                
                if (strlen($targetJoiningDate) > 10 && strpos($targetJoiningDate, ':') !== false) {
                    $joinSubText = $joinCarbon->format('g:i A (T)');
                } else {
                    $joinSubText = '9:00 AM (GMT+05:30)';
                }

                $today = Carbon::today();
                $diffDays = $today->diffInDays($joinCarbon->copy()->startOfDay(), false);

                if ($isJoined) {
                    $countdownHtml = '<span class="oj-countdown-pill oj-countdown-joined"><i class="fas fa-check-circle me-1"></i>Joined</span>';
                } else {
                    if ($diffDays > 1) {
                        $countdownHtml = '<span class="oj-countdown-pill oj-countdown-soon">in ' . $diffDays . ' days</span>';
                    } elseif ($diffDays == 1) {
                        $countdownHtml = '<span class="oj-countdown-pill oj-countdown-tomorrow">Tomorrow</span>';
                    } elseif ($diffDays == 0) {
                        $countdownHtml = '<span class="oj-countdown-pill oj-countdown-today">Joining Today</span>';
                    } else {
                        $countdownHtml = '<span class="oj-countdown-pill oj-countdown-overdue">Overdue (' . abs($diffDays) . 'd)</span>';
                    }
                }
            } catch (\Exception $e) {
                $formattedJoinDate = $targetJoiningDate;
                $countdownHtml = '<span class="oj-countdown-pill oj-countdown-neutral">Date Set</span>';
            }
        } else {
            $formattedJoinDate = 'Date Pending';
            $joinSubText = !empty($row->np_id) ? ('Notice: ' . $row->np_id . ' Days') : 'Notice: Immediate';
            $countdownHtml = $isJoined 
                ? '<span class="oj-countdown-pill oj-countdown-joined"><i class="fas fa-check-circle me-1"></i>Joined</span>'
                : '<span class="oj-countdown-pill oj-countdown-neutral">Awaiting DOJ</span>';
        }

        // 8. Stage Name
        if (isset($statusMap[@$row->child_status_id])) {
            $stageChildName = $statusMap[@$row->child_status_id];
        } elseif (!empty($row->child_status_name)) {
            $stageChildName = $row->child_status_name;
        } else {
            $stageChildName = $isJoined ? 'Joined' : 'Offer Extended';
        }

        $stageDefaultCss = ($row->thirdparty_company_id > 0) ? 'client_stage_sub_stage' : 'stage_sub_stage';
    @endphp

        <!-- Primary Card for Candidate Offer & Joining -->
        <div class="oj-primary-card" data-candidate-id="{{ @$row->wiseId }}">
            <!-- 1. Candidate Sub-Card -->
            <div class="oj-subcard oj-subcard-cand">
                <div class="oj-cand-avatar {{ $avatarClass }}">
                    {{ $initial }}
                </div>
                <div class="oj-cand-info">
                    <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}"
                       class="oj-cand-name {{ $profileSidebarCss }} {{ $duplicate == 1 ? 'text-danger' : '' }}"
                       title="{{ ucwords($fullName) }}">
                        {{ ucwords(mb_substr($fullName, 0, 28)) }}
                    </a>
                    <span class="oj-cand-role" title="{{ ucwords($designation) }}">
                        {{ !empty($designation) ? ucwords(mb_substr($designation, 0, 28)) : '—' }}
                    </span>
                </div>
            </div>

            <!-- 2. Job Sub-Card -->
            <div class="oj-subcard oj-subcard-job">
                <div class="oj-job-title" title="{{ ucwords($jobTitle) }}">
                    {{ !empty($jobTitle) ? ucwords(mb_substr($jobTitle, 0, 26)) : '—' }}
                </div>
                <div class="oj-job-company" title="{{ ucwords($clientName) }}">
                    {{ !empty($clientName) ? ucwords(mb_substr($clientName, 0, 24)) : 'Client Account' }}
                </div>
            </div>

            <!-- 3. Job Status, ID & Client Sub-Card -->
            <div class="oj-subcard oj-subcard-meta">
                <span class="oj-meta-status-badge">
                    <i class="fas fa-circle oj-meta-status-dot"></i> {{ $jobStatus }}
                </span>
                <span class="oj-meta-sub">
                    {{ $relationshipLabel }} &bull; {{ $jobId }}
                </span>
            </div>

            <!-- 4. Ownership Sub-Card -->
            <div class="oj-subcard oj-subcard-owner">
                <div class="oj-owner-name" title="{{ ucwords((string) $recruiterName) }}">
                    {{ ucwords(mb_substr((string) $recruiterName, 0, 22)) }}
                </div>
                <div class="oj-owner-comp" title="{{ ucwords((string) $organizationName) }}">
                    {{ ucwords(mb_substr((string) $organizationName, 0, 24)) }}
                </div>
            </div>

            <!-- 5. Compensation Sub-Card -->
            <div class="oj-subcard oj-subcard-comp">
                <div class="oj-comp-amount">
                    @if ($formattedComp !== '—')
                        {{ $currCode }} {{ $formattedComp }}/{{ $modeAbbr }}
                    @else
                        —
                    @endif
                </div>
                <div class="oj-comp-details">
                    <span class="oj-comp-type">{{ $employmentType }}</span>
                    <span class="oj-comp-sep">&bull;</span>
                    <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}" class="oj-comp-change {{ $profileSidebarCss }}">
                        Change
                    </a>
                </div>
            </div>

            <!-- 6. Offer Status Sub-Card -->
            <div class="oj-subcard oj-subcard-offer-status">
                @if ($isJoined)
                    <div class="oj-status-pill oj-pill-joined">
                        <i class="fas fa-check-circle"></i>
                        <span>Joined</span>
                    </div>
                    <span class="oj-status-subtext">Confirmed</span>
                @else
                    <div class="oj-status-pill oj-pill-offered">
                        <i class="fas fa-paper-plane"></i>
                        <span>Offer Released</span>
                    </div>
                    <span class="oj-status-subtext">Offer Sent</span>
                @endif
            </div>

            <!-- 7. Joining Sub-Card -->
            <div class="oj-subcard oj-subcard-joining">
                <div class="oj-join-date">
                    {{ $formattedJoinDate }}
                </div>
                <div class="oj-join-time">
                    {{ $joinSubText }}
                </div>
                <div class="oj-join-countdown">
                    {!! $countdownHtml !!}
                </div>
            </div>

            <!-- 8. Stage Sub-Card -->
            <div class="oj-subcard oj-subcard-stage">
                <a href="javascript:void(0);" data-row-id="{{ @$row->wiseId }}"
                   class="oj-stage-badge-link {{ $stageDefaultCss }}"
                   data-toggle="modal" data-target="#{{ $stageDefaultCss }}">
                    @if ($isJoined)
                        <span class="oj-stage-outline-badge oj-stage-badge-green">
                            Joined
                        </span>
                    @else
                        <span class="oj-stage-outline-badge oj-stage-badge-orange">
                            Offered
                        </span>
                    @endif
                    <span class="oj-stage-subtext" title="{{ $stageChildName }}">
                        {{ mb_substr($stageChildName, 0, 16) }}
                    </span>
                </a>
            </div>
        </div>
    @endforeach
@else
    <div class="oj-empty-card">
        <i class="fas fa-file-signature oj-empty-icon"></i>
        <div class="oj-empty-title">No candidate offers or joinings found</div>
        <div class="oj-empty-desc">No candidates have been moved to Offered or Joined stages for this client yet.</div>
    </div>
@endif
