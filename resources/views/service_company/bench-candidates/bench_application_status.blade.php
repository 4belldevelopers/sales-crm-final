@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    // Avatar initial + color (same convention used across the bench pages).
    $bgTextDecore = $strVal = '';
    $str = trim(@$benchApplication->full_name);
    $str = $strVal = strtolower($str[0] ?? '?');
    $ordVal = ord($str);
    if ($ordVal >= 97 && $ordVal <= 101) {
        $bgTextDecore = 'bg-light-danger text-danger';
    } elseif ($ordVal >= 102 && $ordVal <= 106) {
        $bgTextDecore = 'bg-light-warning text-warning';
    } elseif ($ordVal >= 107 && $ordVal <= 111) {
        $bgTextDecore = 'bg-light-primary text-primary';
    } elseif ($ordVal >= 112 && $ordVal <= 116) {
        $bgTextDecore = 'bg-light-success text-success';
    } elseif ($ordVal >= 117 && $ordVal <= 121) {
        $bgTextDecore = 'bg-dark text-secondary';
    } else {
        $bgTextDecore = 'bg-light-secondary text-dark';
    }

    $company_id = auth()->user()->company_id;
    $id = $benchApplication->id;
    $currentStatusId = (int) $benchApplication->status;

    // Relationship Manager assigned to this talent (view-computed, mirrors the pre-existing pattern).
    $team = DB::table('users')
        ->leftjoin('bench_assign', function ($join) use ($id) {
            $join->on('bench_assign.user_id', '=', 'users.id');
            $join->on('bench_assign.bench_applications_id', '=', DB::raw("'" . $id . "'"));
        })
        ->select('users.id', 'users.name')
        ->where('users.company_id', $company_id)
        ->where('bench_assign.bench_applications_id', $id)
        ->where('users.status', '=', 'active')
        ->orderByDesc('bench_assign.user_id')
        ->orderBy('users.name', 'asc')
        ->groupBy('users.id')
        ->get();
    $managerName = $team[0]->name ?? 'Unassigned';

    // Availability badge (Immediate / Notice period N days / Available from date).
    $npArr = ['Notice period', 'Available from', 'Immediate'];
    $npDate = $benchApplication->np_id;
    $availTitle = $npArr[$benchApplication->serving_notice_period_mode] ?? 'Immediate';
    if ($benchApplication->serving_notice_period_mode == 0) {
        $days = (int) $benchApplication->np_id;
        $npDate = $days > 0 ? ($days == 1 ? '1 Day' : $days . ' Days') : '';
    } elseif ($benchApplication->serving_notice_period_mode == 1) {
        $npDate = date('d M Y', strtotime($benchApplication->np_id));
    } else {
        $npDate = '';
    }

    $employmentLabel = $benchApplication->outsourcing_type == 2 ? 'Contract Employee' : 'Internal Employee';
    $employmentType = $benchApplication->outsourcing_type == 2 ? 'Contract' : 'Full-time';

    // "Employee Since" — since date + human duration (e.g. "1 Year 11 Months").
    $sinceDt = !empty($benchApplication->created_at) ? Carbon::parse($benchApplication->created_at) : null;
    $sinceLabel = $sinceDt ? $sinceDt->format('d M Y') : '—';
    $sinceDuration = $sinceDt ? ucwords($sinceDt->diffForHumans(now(), Carbon::DIFF_ABSOLUTE, false, 2)) : '';

    $currentStatusSub = optional(GeneralMethod::bench_status_subline($currentStatusId))->name ?? '';

    // Status card icon/colour metadata (UI-only — not stored data).
    $statusMeta = [
        1 => ['icon' => 'fa-user', 'bg' => '#f1f2f6', 'color' => '#7e8299', 'desc' => 'Newly added to talent pool'],
        2 => ['icon' => 'fa-user-plus', 'bg' => '#eef3ff', 'color' => '#3e97ff', 'desc' => 'Available for opportunities'],
        4 => ['icon' => 'fa-pause', 'bg' => '#fff4de', 'color' => '#f6a609', 'desc' => 'Temporarily unavailable'],
        3 => ['icon' => 'fa-circle-check', 'bg' => '#e8fff3', 'color' => '#17c653', 'desc' => 'Assigned to a client'],
        5 => ['icon' => 'fa-circle-xmark', 'bg' => '#ffeef1', 'color' => '#f8285a', 'desc' => 'No longer with the company'],
    ];
@endphp

<form id="status_frm" name="status_frm" method="post" class="p-3">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="bench_application_id" id="bench_application_id" value="{{ $benchApplication->id }}" />
    <input type="hidden" name="placedid" id="placedid" value="{{ $placedid ?? '' }}" />

    {{-- Candidate summary + stat tiles --}}
    <div class="bs-card p-3 mb-2">
        <div class="row g-3">
            <div class="col-lg-4 d-flex align-items-start gap-3 pe-lg-3" style="border-right: 1px solid #eef0f4;">
                <div class="symbol symbol-circle symbol-60px overflow-hidden flex-shrink-0">
                    <div class="symbol-label {{ $bgTextDecore }} fw-bolder d-flex align-items-center justify-content-center w-100 h-100" style="font-size:18px !important;">{{ strtoupper($strVal) }}</div>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="bs-name">{{ ucwords($benchApplication->full_name) }}</span>
                        <span class="badge {{ $availTitle == 'Immediate' ? 'badge-light-success' : 'badge-light-warning' }}">{{ $availTitle }}</span>
                    </div>
                    <div class="bs-designation">{{ ucwords($benchApplication->current_designation) }} &middot; {{ $employmentLabel }}</div>
                    <div class="lbl mt-2">Talent ID <span class="text-dark fw-bolder">#{{ $benchApplication->id }}</span></div>
                    <div class="bs-sub d-inline-flex align-items-center gap-2 mt-2 px-3 py-2">
                        <i class="fa fa-user-tie text-primary" style="font-size:14px;"></i>
                        <div>
                            <div class="lbl">Relationship Manager</div>
                            <div class="bs-rm-name">{{ ucwords($managerName) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row g-2 mb-2 bs-grid-5">
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-wallet"></i></span>
                            <div class="txt"><div class="lbl">Expected Salary</div><div class="val">{!! $bill_rate_amount !!}</div><div class="sub">{!! $pay_rate_amount !!}</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-clipboard-check"></i></span>
                            <div class="txt"><div class="lbl">Current Status</div><div class="val">{{ $benchApplication->master_status_name }}</div><div class="sub">{{ $currentStatusSub }}</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#fff4de;color:#f6a609;"><i class="fa fa-hourglass-half"></i></span>
                            <div class="txt"><div class="lbl">Notice Period</div><div class="val">{{ $availTitle }}</div><div class="sub">{{ $npDate }}</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#eef3ff;color:#7239ea;"><i class="fa fa-graduation-cap"></i></span>
                            <div class="txt"><div class="lbl">Qualification</div><div class="val">{{ ucwords(optional($qualification)->name) ?: '—' }}</div><div class="sub">{{ $employmentType }}</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#f1f2f6;color:#3f4254;"><i class="fa fa-calendar-days"></i></span>
                            <div class="txt"><div class="lbl">Employee Since</div><div class="val">{{ $sinceLabel }}</div><div class="sub">{{ $sinceDuration }}</div></div>
                        </div>
                    </div>
                </div>
                <div class="row g-2 bs-grid-5">
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-comments"></i></span>
                            <div class="txt"><div class="val">{{ $activeInterviews ?? 0 }}</div><div class="lbl">Active Interviews</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#fff4de;color:#f6a609;"><i class="fa fa-eye"></i></span>
                            <div class="txt"><div class="val">{{ $profileViewCount ?? 0 }}</div><div class="lbl">Profile View</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#eef3ff;color:#7239ea;"><i class="fa fa-paper-plane"></i></span>
                            <div class="txt"><div class="val">{{ $clientSubmissions ?? 0 }}</div><div class="lbl">Client Submissions</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#f1f2f6;color:#3f4254;"><i class="fa fa-clock"></i></span>
                            <div class="txt"><div class="val">{{ $benchDurationDays ?? 0 }} Days</div><div class="lbl">Bench Duration</div></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bs-sub bs-tile"><span class="ico" style="background:#e8fff3;color:#17c653;"><i class="fa fa-download"></i></span>
                            <div class="txt"><div class="val">{{ $resumeDownloadCount ?? 0 }}</div><div class="lbl">Resume Download</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 1: Select New Status --}}
    <div class="bs-card p-3 mb-2">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:12px !important;">1</span>
            <span class="fw-bolder text-dark" style="font-size:14px !important;">Select New Status</span>
        </div>
        <div class="lbl mb-3">Choose the new status for this inhouse talent</div>
        <div class="row g-2 bs-grid-5">
            @foreach ($master_status as $mstatus)
                @php $meta = $statusMeta[$mstatus->id] ?? ['icon' => 'fa-circle', 'bg' => '#f1f2f6', 'color' => '#7e8299', 'desc' => '']; @endphp
                <div class="col">
                    <div class="bs-status-card {{ $mstatus->id == $currentStatusId ? 'is-current' : '' }}"
                        data-status-id="{{ $mstatus->id }}" data-status-label="{{ $mstatus->name }}">
                        <input type="radio" name="bench_master_status" value="{{ $mstatus->id }}" class="d-none" {{ $mstatus->id == $currentStatusId ? 'checked' : '' }}>
                        <div class="radio-dot"></div>
                        <div class="ico" style="background:{{ $meta['bg'] }}; color:{{ $meta['color'] }};"><i class="fa {{ $meta['icon'] }}"></i></div>
                        <div class="lbl">{{ $mstatus->name }}</div>
                        <div class="desc">{{ $meta['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bs-impact mt-3" id="bs_impact_wrap" style="display:none;">
            <div class="fw-bolder text-success mb-2" style="font-size:13px !important;"><i class="fa fa-shield-halved"></i> Impact of changing status</div>
            <div class="row g-2" id="bs_impact_list"></div>
        </div>
    </div>

    {{-- Card 2: Select the Job / Project — only shown when target status = Placed --}}
    <div class="bs-card p-3 mb-2" id="bs_job_card" style="display:none;">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:12px !important;">2</span>
            <span class="fw-bolder text-dark" style="font-size:14px !important;">Select the Job / Project</span>
        </div>
        <div class="lbl mb-2">Jobs associated with this talent</div>
        <div class="bs-job-table table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Job / Company</th>
                        <th>Experience</th>
                        <th>Location</th>
                        <th>Hiring Manager</th>
                        <th>Offer Amount</th>
                        <th>Join Date</th>
                        <th>Added Date</th>
                        <th>Stage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="bs_job_body">
                    <tr><td colspan="9" class="text-center text-muted py-4">Select &quot;Placed&quot; above to load jobs.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    initBenchStatusDrawer({
        currentId: {{ (int) $currentStatusId }},
        currentName: @json($benchApplication->master_status_name),
        benchId: {{ (int) $currentBenchId }},
        placedId: {{ (int) ($placedid ?? 0) }}
    });
</script>
