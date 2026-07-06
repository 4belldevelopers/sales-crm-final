@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    // Avatar initial (first letter of name).
    $strVal = strtoupper(mb_substr(trim(@$benchApplication->full_name) ?: '?', 0, 1));

    $company_id = auth()->user()->company_id;
    $id = $benchApplication->id;
    $currentStatusId = (int) $benchApplication->status;

    // Relationship Manager assigned to this talent.
    $team = DB::table('users')
        ->leftjoin('bench_assign', function ($join) use ($id) {
            $join->on('bench_assign.user_id', '=', 'users.id');
            $join->on('bench_assign.bench_applications_id', '=', DB::raw("'" . $id . "'"));
        })
        ->select('users.name')
        ->where('users.company_id', $company_id)
        ->where('bench_assign.bench_applications_id', $id)
        ->where('users.status', '=', 'active')
        ->orderByDesc('bench_assign.user_id')
        ->groupBy('users.id')
        ->get();
    $managerName = $team[0]->name ?? 'Unassigned';

    // Availability (Immediate / Notice period N days / Available from date).
    $npMode = (int) $benchApplication->serving_notice_period_mode;
    if ($npMode == 2) {
        $availTitle = 'Available';
        $npDate = 'Immediate';
    } elseif ($npMode == 0) {
        $availTitle = 'Notice Period';
        $days = (int) $benchApplication->np_id;
        $npDate = $days > 0 ? ($days == 1 ? '1 Day' : $days . ' Days') : '—';
    } elseif ($npMode == 1) {
        $availTitle = 'Available From';
        $npDate = $benchApplication->np_id ? date('d M Y', strtotime($benchApplication->np_id)) : '—';
    } else {
        $availTitle = 'Available';
        $npDate = 'Immediate';
    }
    $availOn = ($npMode == 2);

    $employmentType = $benchApplication->outsourcing_type == 2 ? 'Contract' : 'Full-time';
    $employmentLabel = $benchApplication->outsourcing_type == 2 ? 'Contract Employee' : 'Internal Employee';

    // Employee Since — date + human duration.
    $sinceDt = !empty($benchApplication->created_at) ? Carbon::parse($benchApplication->created_at) : null;
    $sinceLabel = $sinceDt ? $sinceDt->format('d M Y') : '—';
    $sinceDuration = $sinceDt ? ucwords($sinceDt->diffForHumans(now(), Carbon::DIFF_ABSOLUTE, false, 2)) : '';

    // Status card metadata (order + labels/sublines match the design; icons/colours are UI-only).
    $order = [1, 2, 4, 3, 5];
    $statusMeta = [
        1 => ['name' => 'Pool',   'sub' => 'Talent Pool',                 'icon' => 'fa-user-group',   'color' => '#7239ea', 'bg' => '#f3effe', 'bullets' => ['For newly added talents', 'Not visible in searches']],
        2 => ['name' => 'Bench',  'sub' => 'Available for Opportunities',  'icon' => 'fa-couch',        'color' => '#3e97ff', 'bg' => '#eef3ff', 'bullets' => ['Visible in marketplace', 'Bench counter starts', 'Searchable by clients']],
        4 => ['name' => 'Hold',   'sub' => 'Temporarily Unavailable',      'icon' => 'fa-circle-pause', 'color' => '#f6a609', 'bg' => '#fff4de', 'bullets' => ['Not visible in searches', 'Bench counter paused', 'Availability on hold']],
        3 => ['name' => 'Placed', 'sub' => 'Assigned to Client',           'icon' => 'fa-circle-check', 'color' => '#17c653', 'bg' => '#e8fff3', 'bullets' => ['Active client assignment', 'Not visible in marketplace', 'Work in progress']],
        5 => ['name' => 'Exited', 'sub' => 'No Longer with Company',        'icon' => 'fa-circle-xmark', 'color' => '#f8285a', 'bg' => '#ffeef1', 'bullets' => ['End employment', 'Remove all access', 'Archive candidate']],
    ];
    $cur = $statusMeta[$currentStatusId] ?? ['name' => $benchApplication->master_status_name, 'sub' => '', 'color' => '#3e97ff', 'bg' => '#eef3ff'];
    $curSubline = optional(GeneralMethod::bench_status_subline($currentStatusId))->name ?? '';
@endphp

<form id="status_frm" name="status_frm" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="bench_application_id" value="{{ $benchApplication->id }}" />
    <input type="hidden" name="placedid" value="{{ $placedid ?? '' }}" />
    {{-- Single source of truth for the chosen status; toggled by the cards / journey via JS --}}
    <div class="d-none">
        @foreach ($order as $sid)
            <input type="radio" name="bench_master_status" value="{{ $sid }}" {{ $sid == $currentStatusId ? 'checked' : '' }}>
        @endforeach
    </div>

    <div class="bs-layout">
        {{-- ============ LEFT: candidate + status journey ============ --}}
        <div class="bs-side">
            <div class="bs-cand">
                <div class="d-flex align-items-center gap-3">
                    <div class="bs-cand-avatar">{{ $strVal }}<span class="bs-cand-dot {{ $availOn ? 'on' : 'off' }}"></span></div>
                    <div class="min-w-0">
                        <div class="bs-cand-name">{{ ucwords($benchApplication->full_name) }}</div>
                        <div class="bs-cand-role">{{ ucwords($benchApplication->current_designation) }}</div>
                        <div class="bs-cand-type"><i class="fa fa-user-tie"></i> {{ $employmentLabel }}</div>
                    </div>
                </div>
                <div class="bs-rm">
                    <span class="rm-ic"><i class="fa fa-user-shield"></i></span>
                    <div>
                        <div class="rm-l">Relationship Manager</div>
                        <div class="rm-n">{{ ucwords($managerName) }}</div>
                    </div>
                </div>
            </div>

            <div class="bs-card mt-3">
                <div class="bs-journey-title">Status Journey</div>
                @foreach ($order as $sid)
                    @php $m = $statusMeta[$sid]; @endphp
                    <div class="bs-journey-item {{ $sid == $currentStatusId ? 'is-current' : '' }}"
                        data-status-id="{{ $sid }}" data-status-label="{{ $m['name'] }}" data-status-sub="{{ $m['sub'] }}">
                        <span class="bs-journey-radio"></span>
                        <div class="flex-grow-1">
                            <div class="nm">{{ $m['name'] }}</div>
                            <div class="sb">{{ $m['sub'] }}</div>
                        </div>
                        @if ($sid == $currentStatusId)
                            <span class="bs-journey-badge">Current</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============ RIGHT: info cards + status chooser ============ --}}
        <div class="bs-main">
            {{-- Row: Employment / Availability / Recruitment Activity --}}
            <div class="row g-2 bs-row">
                <div class="col-lg-4">
                    <div class="bs-card h-100">
                        <div class="bs-card-head"><span class="ic" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-briefcase"></i></span><span class="tt">Employment</span></div>
                        <div class="row g-2 bs-eqcols">
                            <div class="col bs-stat"><div class="v">{!! $bill_rate_amount !!}</div><div class="l">Expected Salary</div><div class="s">{!! $pay_rate_amount !!}</div></div>
                            <div class="col bs-stat"><div class="v">{{ ucwords(optional($qualification)->name) ?: '—' }}</div><div class="l">Qualification</div><div class="s">{{ $employmentType }}</div></div>
                            <div class="col bs-stat"><div class="v">{{ $sinceLabel }}</div><div class="l">Employee Since</div><div class="s">{{ $sinceDuration }}</div></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bs-card h-100">
                        <div class="bs-card-head"><span class="ic" style="background:#e8fff3;color:#17c653;"><i class="fa fa-calendar-check"></i></span><span class="tt">Availability</span></div>
                        <div class="row g-2 bs-eqcols">
                            <div class="col bs-stat"><div class="v {{ $availOn ? 'text-success' : '' }}">{{ $availTitle }}</div><div class="l">Notice Period</div><div class="s">{{ $npDate }}</div></div>
                            <div class="col bs-stat"><div class="v">{{ $benchDurationDays ?? 0 }}</div><div class="l">Days on Bench</div><div class="s">Current Duration</div></div>
                            <div class="col bs-stat"><div><span class="bs-badge-status" style="background:{{ $cur['bg'] }};color:{{ $cur['color'] }};">{{ $benchApplication->master_status_name }}</span></div><div class="l mt-1">Current Status</div><div class="s">{{ $curSubline }}</div></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bs-card h-100">
                        <div class="bs-card-head"><span class="ic" style="background:#f3effe;color:#7239ea;"><i class="fa fa-graduation-cap"></i></span><span class="tt">Recruitment Activity</span></div>
                        <div class="row g-2 bs-eqcols">
                            <div class="col bs-rec"><span class="ic" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-comments"></i></span><div class="v">{{ $activeInterviews ?? 0 }}</div><div class="l">Interviews</div></div>
                            <div class="col bs-rec"><span class="ic" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-paper-plane"></i></span><div class="v">{{ $clientSubmissions ?? 0 }}</div><div class="l">Submissions</div></div>
                            <div class="col bs-rec"><span class="ic" style="background:#fff4de;color:#f6a609;"><i class="fa fa-eye"></i></span><div class="v">{{ $profileViewCount ?? 0 }}</div><div class="l">Profile Views</div></div>
                            <div class="col bs-rec"><span class="ic" style="background:#e8fff3;color:#17c653;"><i class="fa fa-download"></i></span><div class="v">{{ $resumeDownloadCount ?? 0 }}</div><div class="l">Resume<br>Downloads</div></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row: Transition preview / What will happen --}}
            <div class="row g-2 bs-row">
                <div class="col-lg-5">
                    <div class="bs-card h-100">
                        <div class="bs-card-head"><span class="ic" style="background:#eef3ff;color:#3e97ff;"><i class="fa fa-right-left"></i></span><span class="tt">Status Transition Preview</span></div>
                        <div class="bs-prev">
                            <div class="bs-prev-box cur">
                                <div class="nm">{{ $cur['name'] }}</div>
                                <div class="sb">{{ $cur['sub'] }}</div>
                            </div>
                            <div class="bs-prev-arrow"><i class="fa fa-arrow-right"></i></div>
                            <div class="bs-prev-box new">
                                <div class="nm" id="bs_prev_new_name">—</div>
                                <div class="sb" id="bs_prev_new_sub"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="bs-card bs-wh h-100" id="bs_whathappen" style="display:none;">
                        <div class="bs-card-head"><span class="ic" style="background:#e8fff3;color:#17c653;"><i class="fa fa-circle-check"></i></span><span class="tt" style="color:#17845a;">What Will Happen?</span></div>
                        <div class="row g-1" id="bs_wh_list"></div>
                    </div>
                    <div class="bs-card h-100 d-flex align-items-center justify-content-center text-muted" id="bs_whathappen_placeholder" style="font-size:12px;">
                        <div class="text-center"><i class="fa fa-hand-pointer mb-2 d-block" style="font-size:18px;color:#c2c9d6;"></i>Select a new status below to preview the impact</div>
                    </div>
                </div>
            </div>

            {{-- Choose new status --}}
            <div class="bs-card bs-row">
                <div class="bs-choose-title">Choose New Status</div>
                <div class="bs-choose-sub">Select the new status to update inhouse talent availability</div>
                <div class="row g-2 bs-eqcols">
                    @foreach ($order as $sid)
                        @php $m = $statusMeta[$sid]; @endphp
                        <div class="col">
                            <div class="bs-status-card" data-status-id="{{ $sid }}" data-status-label="{{ $m['name'] }}" data-status-sub="{{ $m['sub'] }}">
                                <span class="ic" style="background:{{ $m['bg'] }};color:{{ $m['color'] }};"><i class="fa {{ $m['icon'] }}"></i></span>
                                <div class="nm" style="color:{{ $m['color'] }};">{{ strtoupper($m['name']) }}</div>
                                <div class="sb">{{ $m['sub'] }}</div>
                                <div class="mt-auto">
                                    @foreach ($m['bullets'] as $b)
                                        <div class="bl"><i class="fa fa-check" style="color:{{ $m['color'] }};"></i> {{ $b }}</div>
                                    @endforeach
                                    <div class="radio"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Card: Select the Job / Project — only for target status = Placed --}}
            <div class="bs-card bs-row" id="bs_job_card" style="display:none;">
                <div class="bs-card-head"><span class="ic" style="background:#e8fff3;color:#17c653;"><i class="fa fa-briefcase"></i></span><span class="tt">Select the Job / Project</span></div>
                <div class="bs-choose-sub">Choose the client engagement this talent is being placed on</div>
                <div class="bs-job-table table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Job / Company</th><th>Experience</th><th>Location</th><th>Hiring Manager</th>
                                <th>Offer Amount</th><th>Join Date</th><th>Added Date</th><th>Stage</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="bs_job_body">
                            <tr><td colspan="9" class="text-center text-muted py-4">Select &quot;Placed&quot; above to load jobs.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    initBenchStatusDrawer({
        currentId: {{ (int) $currentStatusId }},
        currentName: @json($cur['name']),
        currentSub: @json($cur['sub']),
        benchId: {{ (int) $currentBenchId }},
        placedId: {{ (int) ($placedid ?? 0) }}
    });
</script>
