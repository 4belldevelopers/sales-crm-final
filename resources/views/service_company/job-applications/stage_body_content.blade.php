@php
    use App\Helper\GeneralMethod;
@endphp

@php
    /*
     * Candidate avatar initial — unchanged from the old modal, just kept as a
     * plain PHP value instead of building an HTML string, since the new markup
     * renders the initial itself.
     */
    // Several source fields (full_name, current_designation, qualification's
    // relation, city/state/country) are nullable — and when $qualification
    // isn't found the controller leaves it as '' (a string, not an object),
    // so ->name silently resolves to null too. Casting to string before every
    // strtoupper()/ucwords()/substr() call avoids passing null into them:
    // Laravel promotes that "deprecated null argument" notice into a thrown
    // ErrorException (500), and the `@` operator only suppresses the property
    // access itself, not the null value it produces.
    $csdFullName = (string) (@$application->full_name ?? '');
    $csdDesig = (string) (@$application->current_designation ?? '');
    $csdQualName = (string) (@$qualification->name ?? '');
    $csdCityName = (string) (@$row_city_name ?? '');
    $csdCountryName = (string) (@$row_country_name ?? '');
    $csdSubmittedBy = (string) (@$application->submitted_by_name ?? '');
    $csdJobTitle = (string) (@$application->title ?? '');

    $csdInitial = strtoupper(substr(trim($csdFullName), 0, 1)) ?: '?';

    $np = @$application->np_id;
    if (empty($np)) {
        $np = 'N/A';
    } elseif (@$application->serving_notice_period_mode == 0) {
        $np = $application->np_id . ($application->np_id == 1 ? ' Day' : ' Days');
    } elseif (@$application->serving_notice_period_mode == 1 && $np != 'Immediate') {
        $np = GeneralMethod::ymdTodmyNotTime($application->np_id);
    }

    /*
     * Stage color/icon palette — reuses the exact hex values already
     * established as the app-wide "Pipeline Stages" convention
     * ($hpStages in apptracker/index.blade.php). InReview has no existing
     * convention anywhere in the codebase; #7239ea/eye is a new, clearly
     * distinct choice for this agency-private stage, not a preserved one.
     */
    $csdStageMeta = [
        'inreview'   => ['label' => 'InReview',   'color' => '#7239ea', 'bg' => '#f8f5ff', 'icon' => 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-1-8-11-8Z M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
        'sourcing'   => ['label' => 'Sourcing',   'color' => '#7e8299', 'bg' => '#eef0f2', 'icon' => 'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
        'screening'  => ['label' => 'Screening',  'color' => '#c000ff', 'bg' => '#f9e6ff', 'icon' => 'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
        'evaluation' => ['label' => 'Evaluation', 'color' => '#2896ff', 'bg' => '#e8f4ff', 'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
        'negotation' => ['label' => 'Negotiation', 'color' => '#ff4098', 'bg' => '#ffe6f2', 'icon' => 'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
        'offer'      => ['label' => 'Offer',       'color' => '#ffab00', 'bg' => '#fff4e0', 'icon' => 'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
        'joined'     => ['label' => 'Joined',      'color' => '#06c732', 'bg' => '#e3fbe9', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
        'rejected'   => ['label' => 'Rejected',    'color' => '#ff0202', 'bg' => '#ffe3e3', 'icon' => 'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];
    $csdOrder = ['sourcing', 'screening', 'evaluation', 'negotation', 'offer', 'joined'];

    // Quick Notes — new, built from scratch (no equivalent existed before).
    // Purely a client-side convenience: clicking a chip appends the phrase to
    // the #comment textarea, it never changes what gets submitted otherwise.
    $csdQuickNotes = [
        'inreview'   => ['Reviewing profile against updated requirement.', 'Awaiting internal alignment before moving forward.'],
        'sourcing'   => ['Added to pipeline, awaiting initial screening.', 'Reached out, awaiting candidate response.'],
        'screening'  => ['Screening call scheduled.', 'Cleared initial screening, moving ahead.'],
        'evaluation' => ['Interview scheduled with panel.', 'Awaiting interview feedback.', 'Cleared technical round.'],
        'negotation' => ["Awaiting candidate's revised compensation expectations.", 'Negotiating notice period / joining date.'],
        'offer'      => ['Offer released, awaiting candidate response.', 'Offer accepted, preparing onboarding.'],
        'joined'     => ['Candidate joined as per offer.', 'Joining confirmed for scheduled date.'],
        'rejected'   => ['Skills mismatch for role requirements.', 'Candidate accepted a competing offer.', 'Salary expectations exceed budget.', 'Candidate unresponsive.'],
    ];
@endphp

<div class="csd-wrap">
    <style>
        .csd-wrap { padding: 4px 2px 10px; font-family: 'Poppins', sans-serif; color: #3f4254; }
        .csd-wrap * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

        .csd-info-card { display: flex; flex-wrap: wrap; gap: 18px; align-items: center; background: #f8f9fb; border: 1px solid #eef0f4; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; }
        .csd-info-avatar { width: 42px; height: 42px; border-radius: 10px; background: #eef4ff; color: #3e97ff; font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .csd-info-name { font-size: 14px; font-weight: 700; color: #181c32; }
        .csd-info-desig { font-size: 11px; color: #7e8299; }
        .csd-info-grid { display: flex; flex-wrap: wrap; gap: 18px 26px; flex: 1 1 auto; }
        .csd-info-item .lbl { font-size: 9.5px; text-transform: uppercase; letter-spacing: .3px; color: #a1a5b7; font-weight: 600; }
        .csd-info-item .val { font-size: 12px; font-weight: 600; color: #3f4254; margin-top: 1px; }

        .csd-sec-head { display: flex; align-items: center; gap: 10px; margin: 0 0 12px; }
        .csd-sec-num { width: 22px; height: 22px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 10.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .csd-sec-title { font-size: 13px; font-weight: 700; color: #181c32; }
        .csd-sec-sub { font-size: 10.5px; color: #7e8299; font-weight: 400; margin-top: 1px; }

        .csd-reco { display: flex; align-items: center; gap: 8px; background: #eef9f2; border: 1px solid #cdf0da; color: #17845a; border-radius: 10px; padding: 8px 14px; font-size: 11.5px; font-weight: 500; margin-bottom: 14px; }
        .csd-reco i { color: #17c653; }

        .csd-stage-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 22px; }
        .csd-pill { flex: 1 1 100px; max-width: 140px; border: 1.5px solid #eef0f4; border-radius: 12px; padding: 10px 8px; cursor: pointer; background: #fff; text-align: center; position: relative; transition: border-color .15s, box-shadow .15s, background .15s; }
        .csd-pill:hover { border-color: #cdd4e0; }
        .csd-pill .ic { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; }
        .csd-pill .ic svg { width: 15px; height: 15px; }
        .csd-pill .nm { font-size: 11px; font-weight: 700; color: #181c32; }
        .csd-pill.is-current { background: #f8f9fb; }
        .csd-pill.is-current .nm::after { content: 'Current'; display: block; font-size: 8.5px; font-weight: 500; color: #a1a5b7; text-transform: uppercase; margin-top: 2px; }
        .csd-pill.is-selected { box-shadow: 0 0 0 2px currentColor inset; }
        .csd-pill .chk { position: absolute; top: 8px; right: 8px; width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #cdd4e0; display: flex; align-items: center; justify-content: center; font-size: 8px; color: transparent; }
        .csd-pill.is-selected .chk { color: #fff; }

        .csd-sub-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
        .csd-sub-tile { flex: 1 1 140px; max-width: 190px; border: 1.5px solid #eef0f4; border-radius: 10px; padding: 9px 12px; cursor: pointer; background: #fff; display: flex; align-items: center; gap: 8px; transition: border-color .15s, box-shadow .15s; }
        .csd-sub-tile:hover { border-color: #cdd4e0; }
        .csd-sub-tile.is-selected { border-color: #3e97ff; box-shadow: 0 0 0 1px #3e97ff inset; background: #f5f9ff; }
        .csd-sub-tile i { color: #7e8299; font-size: 12px; flex-shrink: 0; }
        .csd-sub-tile.is-selected i { color: #3e97ff; }
        .csd-sub-tile span { font-size: 11.5px; font-weight: 600; color: #3f4254; }

        .csd-quick-notes { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
        .csd-qn-chip { border: 1px dashed #cdd4e0; background: #f8f9fb; color: #5e6278; border-radius: 20px; padding: 4px 11px; font-size: 10.5px; cursor: pointer; transition: background .15s, border-color .15s; }
        .csd-qn-chip:hover { background: #eef4ff; border-color: #3e97ff; color: #3e97ff; }

        textarea#comment.csd-comment { border-radius: 10px; font-size: 12px; }
    </style>

    <form class="form" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data" data-csd-flow="plain">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
        <input type="hidden" name="job_wise_condidates_id" id="job_wise_condidates_id" value="{{ @$application->job_wise_condidates_id }}" />

        <div class="csd-info-card">
            <div class="csd-info-avatar">{{ $csdInitial }}</div>
            <div>
                <div class="csd-info-name">{{ ucwords($csdFullName) }}</div>
                <div class="csd-info-desig">{{ ucwords($csdDesig) }}</div>
            </div>
            <div class="csd-info-grid">
                <div class="csd-info-item"><div class="lbl">Experience</div><div class="val">{{ @$application->total_exp }}+ yrs</div></div>
                <div class="csd-info-item"><div class="lbl">Salary</div><div class="val">{!! @$expected_ctc !!}</div></div>
                <div class="csd-info-item"><div class="lbl">Notice Period</div><div class="val">{{ @$np }}</div></div>
                <div class="csd-info-item"><div class="lbl">Qualification</div><div class="val">{{ ucwords($csdQualName) }}</div></div>
                <div class="csd-info-item"><div class="lbl">Location</div><div class="val">{{ $csdCityName }}, {{ strtoupper(substr($csdCountryName, 0, 2)) }}</div></div>
                <div class="csd-info-item"><div class="lbl">Source</div><div class="val">{{ $csdSubmittedBy }}</div></div>
                <div class="csd-info-item"><div class="lbl">Job</div><div class="val" data-toggle="tooltip" title="{{ $csdJobTitle }}">{{ substr(ucwords($csdJobTitle), 0, 22) }}</div></div>
            </div>
        </div>

        @php
            // $master_status is a Collection of stdClass rows — iterate it
            // directly rather than (array)-casting it, which would grab the
            // Collection object's own internal properties instead of its rows.
            $csdCurrentKey = null;
            foreach ($master_status ?? [] as $mstatus) {
                if ($mstatus->id == @$application->master_status_id) { $csdCurrentKey = strtolower((string) (@$mstatus->text_name ?? '')); }
            }
            $csdNextKey = null;
            if ($csdCurrentKey !== null) {
                $csdIdx = array_search($csdCurrentKey, $csdOrder);
                if ($csdIdx !== false && isset($csdOrder[$csdIdx + 1])) { $csdNextKey = $csdOrder[$csdIdx + 1]; }
            }
        @endphp
        @if($csdNextKey && isset($csdStageMeta[$csdNextKey]))
        <div class="csd-reco"><i class="fas fa-sparkles"></i> Recommended Next: <b>&nbsp;{{ $csdStageMeta[$csdNextKey]['label'] }}</b>&nbsp;— based on the candidate's current progress.</div>
        @endif

        <div class="csd-sec-head">
            <span class="csd-sec-num">1</span>
            <div><div class="csd-sec-title">Choose New Stage</div><div class="csd-sec-sub">Select the stage to move this candidate to</div></div>
        </div>
        <div class="csd-stage-row">
            @if(!empty($master_status))
                @foreach($master_status as $mstatus)
                    @php
                        $csdKey = strtolower((string) (@$mstatus->text_name ?? ''));
                        $csdMeta = $csdStageMeta[$csdKey] ?? ['label' => @$mstatus->name, 'color' => '#7e8299', 'bg' => '#eef0f2', 'icon' => 'M12 6v6l4 2'];
                        $csdChk = ($mstatus->id == @$application->master_status_id) ? 'checked="checked"' : '';
                        $csdIsCurrent = ($mstatus->id == @$application->master_status_id);
                    @endphp
                    <div class="csd-pill {{ $csdIsCurrent ? 'is-current' : '' }} master_status_div" data-master-status-id="{{ @$mstatus->id }}" style="color:{{ $csdMeta['color'] }};" onclick="csdSelectStage(this)">
                        <input type="radio" class="d-none master_status" name="master_status" value="{{ @$mstatus->id }}" {{ $csdChk }} id="{{ @$mstatus->text_name }}">
                        <span class="chk"><i class="fas fa-check"></i></span>
                        <span class="ic" style="background:{{ $csdMeta['bg'] }};color:{{ $csdMeta['color'] }};">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $csdMeta['icon'] }}"/></svg>
                        </span>
                        <div class="nm">{{ $csdMeta['label'] }}</div>
                    </div>
                @endforeach
            @endif
        </div>

        <div id="main_substage">
            <div class="csd-sec-head subStageSection">
                <span class="csd-sec-num">2</span>
                <div><div class="csd-sec-title">Sub Stage</div><div class="csd-sec-sub">Optional — narrows down the reason within this stage</div></div>
            </div>
            <div class="csd-sub-row" id="substage">
                @if(!empty($candidate_status_Arr))
                    @foreach($candidate_status_Arr as $cantData)
                        @php $csdSubChk = ($cantData->id == @$application->child_status_id); @endphp
                        <div class="csd-sub-tile {{ $csdSubChk ? 'is-selected' : '' }}" onclick="csdSelectSub(this)">
                            <input type="radio" class="d-none" name="sub_stages" value="{{ $cantData->id }}" id="sub_stage_{{ $cantData->id }}" {{ $csdSubChk ? 'checked="checked"' : '' }}>
                            <i class="fas {{ App\Helper\GeneralMethod::csdSubStatusIcon($cantData->name) ?? 'fa-circle-dot' }}"></i>
                            <span>{{ $cantData->name }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="row" id="comment_div">
            <div class="csd-sec-head">
                <span class="csd-sec-num">3</span>
                <div><div class="csd-sec-title">Comments</div><div class="csd-sec-sub">Quick notes — click to insert, then edit as needed</div></div>
            </div>
            <div class="csd-quick-notes" id="csd_quick_notes"></div>
            <textarea id="comment" name="comment" class="form-control form-control-solid csd-comment" rows="4" placeholder="Write comment here..."></textarea>
        </div>
    </form>
</div>

<script>
    window.CSD_QUICK_NOTES = {!! json_encode($csdQuickNotes) !!};

    // child_stage responses rarely change mid-session (candidate_status is
    // per-company admin-entered reference data) — caching per master_status_id
    // means re-selecting a previously-visited stage is instant on the 2nd+
    // click, with no extra round-trip. Scoped to this drawer's lifetime only.
    window.CSD_SUBSTAGE_CACHE = window.CSD_SUBSTAGE_CACHE || {};

    function csdSelectStage(el) {
        var id = $(el).data('master-status-id');
        $(el).find('input.master_status').prop('checked', true);

        $('.csd-pill').removeClass('is-selected');
        $(el).addClass('is-selected');

        // Same show/hide rules as the previous modal's own stage-click logic:
        // comment box stays available for every stage; sub-stage list hides
        // only for InReview (id 8). Comment being mandatory for InReview is
        // still enforced at submit time in #custom_stage_submit, unchanged.
        $('#comment_div').show();
        if (id == 8) {
            $('#main_substage').hide();
        } else {
            $('#main_substage').show();
            csdFetchSubStage(id);
        }

        csdRenderQuickNotes();
    }

    function csdFetchSubStage(id) {
        if (window.CSD_SUBSTAGE_CACHE[id] !== undefined) {
            $('#substage').html(window.CSD_SUBSTAGE_CACHE[id]);
            return;
        }
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var url = "{{ route('service_company.job-applications.child_stage', ':id') }}".replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function () { $('#substage').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
            success: function (response) {
                if (response.status == 'success') {
                    window.CSD_SUBSTAGE_CACHE[id] = response.html || '';
                    $('#substage').html(response.html || '');
                    if (!response.html) { $('#main_substage').hide(); }
                }
            }
        });
    }

    function csdSelectSub(el) {
        $('.csd-sub-tile').removeClass('is-selected');
        $(el).addClass('is-selected');
        $(el).find('input').prop('checked', true);
        csdRenderQuickNotes($(el).find('span').text());
    }

    function csdRenderQuickNotes(subName) {
        var textName = $('input[name="master_status"]:checked').attr('id');
        var notes = (textName && window.CSD_QUICK_NOTES[textName]) ? window.CSD_QUICK_NOTES[textName].slice() : [];
        if (subName) {
            var s = subName.toLowerCase();
            if (s.indexOf('duplicate') > -1) notes.unshift('Duplicate profile — already in pipeline for this or another role.');
            else if (s.indexOf('not interest') > -1) notes.unshift('Candidate not interested in this opportunity at this time.');
            else if (s.indexOf('mismatch') > -1) notes.unshift('Profile does not align with the role requirements.');
            else if (s.indexOf('no show') > -1 || s.indexOf('noshow') > -1) notes.unshift('Candidate did not show up for the scheduled round.');
            else if (s.indexOf('hold') > -1) notes.unshift('Keeping on hold pending further updates.');
            else if (s.indexOf('salary') > -1 || s.indexOf('compensation') > -1) notes.unshift('Compensation expectations under discussion.');
        }
        var html = '';
        notes.forEach(function (n) {
            html += '<span class="csd-qn-chip" onclick="csdInsertNote(this)">' + n.replace(/"/g, '&quot;') + '</span>';
        });
        $('#csd_quick_notes').html(html);
    }

    function csdInsertNote(el) {
        var txt = $(el).text();
        var existing = $('#comment').val();
        $('#comment').val(existing ? (existing + ' ' + txt) : txt);
    }

    csdRenderQuickNotes();
</script>
