@php
    use App\Helper\GeneralMethod;

    // Avatar initial.
    $strVal = strtoupper(mb_substr(trim(@$benchApplication->full_name) ?: '?', 0, 1));
    $currentStatusId = (int) $benchApplication->status;

    // Status metadata (order + labels/descriptions match the design; icons/colours are UI-only).
    $order = [1, 2, 4, 3, 5];
    $statusMeta = [
        1 => ['name' => 'Pool',   'desc' => 'Newly added to talent pool',  'icon' => 'fa-user',          'color' => '#7239ea', 'bg' => '#f3effe'],
        2 => ['name' => 'Bench',  'desc' => 'Available for opportunities',  'icon' => 'fa-user-plus',     'color' => '#3e97ff', 'bg' => '#eef3ff'],
        4 => ['name' => 'Hold',   'desc' => 'Temporarily unavailable',      'icon' => 'fa-clock',         'color' => '#f6a609', 'bg' => '#fff4de'],
        3 => ['name' => 'Placed', 'desc' => 'Assigned to a client',         'icon' => 'fa-circle-check',  'color' => '#17c653', 'bg' => '#e8fff3'],
        5 => ['name' => 'Exited', 'desc' => 'No longer with the company',   'icon' => 'fa-right-from-bracket', 'color' => '#f8285a', 'bg' => '#ffeef1'],
    ];
    $curName = $statusMeta[$currentStatusId]['name'] ?? $benchApplication->master_status_name;
@endphp

<form id="status_frm" name="status_frm" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="bench_application_id" value="{{ $benchApplication->id }}" />
    <input type="hidden" name="placedid" value="{{ $placedid ?? '' }}" />
    {{-- Single source of truth for the chosen status; toggled by the cards via JS --}}
    <div class="d-none">
        @foreach ($order as $sid)
            <input type="radio" name="bench_master_status" value="{{ $sid }}" {{ $sid == $currentStatusId ? 'checked' : '' }}>
        @endforeach
    </div>

    {{-- ===== Candidate summary bar ===== --}}
    <div class="bs-card">
        <div class="bs-cand-bar">
            <div class="bs-cand-id">
                <div class="bs-cand-avatar">{{ $strVal }}</div>
                <div class="min-w-0">
                    <div class="bs-cand-name">{{ ucwords($benchApplication->full_name) }}</div>
                    <div class="bs-cand-role">{{ ucwords($benchApplication->current_designation) }}</div>
                </div>
            </div>
            <div class="bs-cand-stat"><div class="v">{{ $activeInterviews ?? 0 }}</div><div class="l">Active Interviews</div></div>
            <div class="bs-cand-stat"><div class="v">{{ $profileViewCount ?? 0 }}</div><div class="l">Profile Views</div></div>
            <div class="bs-cand-stat"><div class="v">{{ $clientSubmissions ?? 0 }}</div><div class="l">Client Submissions</div></div>
            <div class="bs-cand-stat"><div class="v">{{ $benchDurationDays ?? 0 }} Days</div><div class="l">Bench Duration</div></div>
            <div class="bs-cand-stat"><div class="v">{{ $resumeDownloadCount ?? 0 }}</div><div class="l">Resume Downloads</div></div>
        </div>
    </div>

    {{-- ===== Section 1: Select New Status ===== --}}
    <div class="bs-card">
        <div class="bs-sec-head">
            <span class="bs-sec-num">1</span>
            <span class="bs-sec-title">Select New Status</span>
        </div>
        <div class="bs-status-row">
            @foreach ($order as $i => $sid)
                @php $m = $statusMeta[$sid]; @endphp
                <div class="bs-status-cell">
                    <div class="bs-status-card {{ $sid == $currentStatusId ? 'is-current' : '' }}"
                        data-status-id="{{ $sid }}" data-status-label="{{ $m['name'] }}">
                        <span class="ic" style="background:{{ $m['bg'] }};color:{{ $m['color'] }};"><i class="fa {{ $m['icon'] }}"></i></span>
                        <div class="nm" style="color:{{ $m['color'] }};">{{ $m['name'] }}</div>
                        <div class="sb">{{ $m['desc'] }}</div>
                    </div>
                </div>
                @if ($i < count($order) - 1)
                    <div class="bs-status-arrow"><i class="fa fa-arrow-right"></i></div>
                @endif
            @endforeach
        </div>

        {{-- Impact strip (dynamic, shown after a status is picked) --}}
        <div class="bs-impact" id="bs_impact" style="display:none;">
            <div class="bs-impact-head"><i class="fa fa-shield-halved"></i> <span id="bs_impact_title">Impact of changing status</span></div>
            <div class="row g-1" id="bs_impact_list"></div>
        </div>
    </div>

    {{-- ===== Section 2: Select the Job / Project (only for target = Placed) ===== --}}
    <div class="bs-card" id="bs_job_card" style="display:none;">
        <div class="bs-sec-head">
            <span class="bs-sec-num">2</span>
            <span class="bs-sec-title">Select the Job / Project</span>
        </div>
        <div class="bs-job-search">
            <i class="fa fa-search"></i>
            <input type="text" id="bs_job_search_input" placeholder="Search by job, company, or hiring manager..." autocomplete="off" />
        </div>
        <div class="bs-job-table table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Job / Company</th><th>Experience</th><th>Location</th><th>Hiring Manager</th>
                        <th>Offer Amount</th><th>Join Date</th><th>Stage</th><th>Action</th>
                    </tr>
                </thead>
                <tbody id="bs_job_body">
                    <tr><td colspan="8" class="text-center text-muted py-4">Loading jobs…</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    initBenchStatusDrawer({
        currentId: {{ (int) $currentStatusId }},
        currentName: @json($curName),
        benchId: {{ (int) $currentBenchId }},
        placedId: {{ (int) ($placedid ?? 0) }}
    });
</script>
