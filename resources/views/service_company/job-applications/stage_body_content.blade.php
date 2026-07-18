@php
    /*
     * Candidate full name — full_name is nullable, cast to string before it
     * ever reaches a string function: Laravel promotes the "deprecated null
     * argument" notice into a thrown ErrorException (500), and `@` only
     * suppresses the property access itself, not the null value it produces.
     * Kept as a hidden element (not a visible hero card, removed per request)
     * purely so each page's drawer topbar JS can read the candidate's name
     * into its subtitle.
     */
    $csdFullName = (string) (@$application->full_name ?? '');

    /*
     * Stage color/icon palette — reuses the exact hex values already
     * established as the app-wide "Pipeline Stages" convention
     * ($hpStages in apptracker/index.blade.php). InReview's color is the
     * one the OLD modal already used (`bg-light-warning text-warning` on
     * #lbl_master_status_8) — Metronic's "warning" theme color, #ffa800 /
     * #fff4de (see public/css/style.bundle.css) — not an invented one.
     */
    $csdStageMeta = [
        'inreview'   => ['label' => 'InReview',   'color' => '#ffa800', 'bg' => '#fff4de', 'icon' => 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-1-8-11-8Z M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
        'sourcing'   => ['label' => 'Sourcing',   'color' => '#7e8299', 'bg' => '#eef0f2', 'icon' => 'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z'],
        'screening'  => ['label' => 'Screening',  'color' => '#c000ff', 'bg' => '#f9e6ff', 'icon' => 'M9 4.5h6M9 4.5a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 4.5H9ZM9 4.5H7.5A1.5 1.5 0 0 0 6 6v13.5A1.5 1.5 0 0 0 7.5 21h9a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H15M9 12l2 2 4-4'],
        'evaluation' => ['label' => 'Evaluation', 'color' => '#2896ff', 'bg' => '#e8f4ff', 'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
        'negotation' => ['label' => 'Negotiation', 'color' => '#ff4098', 'bg' => '#ffe6f2', 'icon' => 'M8 3 4 7l4 4M4 7h13M16 21l4-4-4-4M20 17H7'],
        'offer'      => ['label' => 'Offer',       'color' => '#ffab00', 'bg' => '#fff4e0', 'icon' => 'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-9 0h12A1.5 1.5 0 0 1 19.5 7.5V18a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V7.5A1.5 1.5 0 0 1 6 6Zm-1.5 5.25h15'],
        'joined'     => ['label' => 'Joined',      'color' => '#06c732', 'bg' => '#e3fbe9', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 4.556-3.04 8.36-7.2 9.575a1.5 1.5 0 0 1-1.6 0C8.04 20.36 5 16.556 5 12V6.75a1.5 1.5 0 0 1 1.5-1.5c1.657 0 3.156-.64 4.243-1.686a1.5 1.5 0 0 1 2.014 0A6.703 6.703 0 0 0 17.5 5.25 1.5 1.5 0 0 1 19 6.75V12Z'],
        'rejected'   => ['label' => 'Rejected',    'color' => '#ff0202', 'bg' => '#ffe3e3', 'icon' => 'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];

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

        .csd-sec-head { display: flex; align-items: center; gap: 10px; margin: 0 0 12px; }
        .csd-sec-num { width: 22px; height: 22px; border-radius: 50%; background: #3e97ff; color: #fff; font-size: 10.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .csd-sec-title { font-size: 13px; font-weight: 700; color: #181c32; }
        .csd-sec-sub { font-size: 10.5px; color: #7e8299; font-weight: 400; margin-top: 1px; }

        .csd-stage-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 22px; }
        .csd-pill { flex: 1 1 96px; max-width: 128px; border: 1.5px solid #eef0f4; border-radius: 12px; padding: 10px 8px; cursor: pointer; background: #fff; text-align: center; position: relative; transition: border-color .15s, box-shadow .15s, background .15s; }
        .csd-pill:hover { border-color: #cdd4e0; }
        .csd-pill .ic { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; }
        .csd-pill .ic svg { width: 15px; height: 15px; }
        .csd-pill .nm { font-size: 11px; font-weight: 700; color: #181c32; }
        .csd-pill.is-current { background: #f8f9fb; }
        .csd-pill.is-current .nm::after { content: 'Current'; display: block; font-size: 8.5px; font-weight: 500; color: #a1a5b7; text-transform: uppercase; margin-top: 2px; }
        .csd-pill.is-selected { box-shadow: 0 0 0 2px currentColor inset; }
        .csd-pill .chk { position: absolute; top: 8px; right: 8px; width: 16px; height: 16px; border-radius: 50%; border: 1.5px solid #cdd4e0; background: #fff; display: flex; align-items: center; justify-content: center; }
        .csd-pill .chk svg { width: 9px; height: 9px; stroke: transparent; }
        .csd-pill.is-selected .chk { background: currentColor; border-color: currentColor; }
        .csd-pill.is-selected .chk svg { stroke: #fff; }
        .csd-stage-sep { flex-shrink: 0; color: #a1a5b7; display: flex; align-items: center; }
        .csd-stage-sep svg { width: 12px; height: 12px; }
        .csd-stage-sep-bar { flex-shrink: 0; color: #a1a5b7; font-size: 18px; font-weight: 300; line-height: 1; margin: 0 2px; }

        .csd-sub-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 18px; }
        .csd-sub-tile { border: 1.5px solid #eef0f4; border-radius: 10px; padding: 10px 12px; cursor: pointer; background: #fff; display: flex; align-items: flex-start; gap: 10px; transition: border-color .15s, box-shadow .15s; }
        .csd-sub-tile:hover { border-color: #cdd4e0; }
        .csd-sub-tile.is-selected { border-color: #3e97ff; box-shadow: 0 0 0 1px #3e97ff inset; background: #f5f9ff; }
        .csd-sub-tile .ic { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .csd-sub-tile .ic svg { width: 14px; height: 14px; }
        .csd-sub-tile .txt .nm { font-size: 11.5px; font-weight: 700; color: #181c32; }
        .csd-sub-tile .txt .ds { font-size: 9.5px; color: #a1a5b7; font-weight: 500; margin-top: 1px; }

        .csd-quick-notes { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .csd-qn-chip { border: 1px dashed #cdd4e0; background: #f8f9fb; color: #5e6278; border-radius: 20px; padding: 4px 11px; font-size: 10.5px; cursor: pointer; transition: background .15s, border-color .15s; }
        .csd-qn-chip:hover { background: #eef4ff; border-color: #3e97ff; color: #3e97ff; }

        textarea#comment.csd-comment { border-radius: 10px; font-size: 12px; }
    </style>

    <form class="form" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data" data-csd-flow="plain">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
        <input type="hidden" name="job_wise_condidates_id" id="job_wise_condidates_id" value="{{ @$application->job_wise_condidates_id }}" />

        <div class="csd-info-name d-none">{{ ucwords($csdFullName) }}</div>

        <div class="csd-sec-head">
            <span class="csd-sec-num">1</span>
            <div><div class="csd-sec-title">Choose New Stage</div><div class="csd-sec-sub">Select the stage to move this candidate to</div></div>
        </div>
        <div class="csd-stage-row">
            @if(!empty($master_status))
                @php
                    // Precomputed so the loop can look one step ahead — the
                    // connector before Rejected is a "|" divider (it's a
                    // separate, always-reachable action, not the "next" step
                    // in the sequential pipeline), every other gap is a ">".
                    $csdKeysList = [];
                    foreach ($master_status as $csdM) { $csdKeysList[] = strtolower((string) (@$csdM->text_name ?? '')); }
                @endphp
                @foreach($master_status as $mstatus)
                    @php
                        $csdKey = strtolower((string) (@$mstatus->text_name ?? ''));
                        $csdMeta = $csdStageMeta[$csdKey] ?? ['label' => @$mstatus->name, 'color' => '#7e8299', 'bg' => '#eef0f2', 'icon' => 'M12 6v6l4 2'];
                        $csdChk = ($mstatus->id == @$application->master_status_id) ? 'checked="checked"' : '';
                        $csdIsCurrent = ($mstatus->id == @$application->master_status_id);
                    @endphp
                    <div class="csd-pill {{ $csdIsCurrent ? 'is-current' : '' }} master_status_div" data-master-status-id="{{ @$mstatus->id }}" style="color:{{ $csdMeta['color'] }};" onclick="csdSelectStage(this)">
                        <input type="radio" class="d-none master_status" name="master_status" value="{{ @$mstatus->id }}" {{ $csdChk }} id="{{ @$mstatus->text_name }}">
                        <span class="chk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                        <span class="ic" style="background:{{ $csdMeta['bg'] }};color:{{ $csdMeta['color'] }};">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $csdMeta['icon'] }}"/></svg>
                        </span>
                        <div class="nm">{{ $csdMeta['label'] }}</div>
                    </div>
                    @unless($loop->last)
                        @if(($csdKeysList[$loop->index + 1] ?? null) === 'rejected')
                            <span class="csd-stage-sep-bar">|</span>
                        @else
                            <span class="csd-stage-sep"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
                        @endif
                    @endunless
                @endforeach
            @endif
        </div>

        <div id="main_substage" @if(empty($candidate_status_Arr)) style="display:none;" @endif>
            <div class="csd-sec-head subStageSection">
                <span class="csd-sec-num">2</span>
                <div><div class="csd-sec-title">Sub Stage</div><div class="csd-sec-sub">Select the specific reason for this stage change</div></div>
            </div>
            <div class="csd-sub-row" id="substage">
                @if(!empty($candidate_status_Arr))
                    @foreach($candidate_status_Arr as $cantData)
                        @php
                            $csdSubChk = ($cantData->id == @$application->child_status_id);
                            $csdSubMeta = App\Helper\GeneralMethod::csdSubStatusMeta($cantData->name);
                        @endphp
                        <div class="csd-sub-tile {{ $csdSubChk ? 'is-selected' : '' }}" onclick="csdSelectSub(this)">
                            <input type="radio" class="d-none" name="sub_stages" value="{{ $cantData->id }}" id="sub_stage_{{ $cantData->id }}" {{ $csdSubChk ? 'checked="checked"' : '' }}>
                            <span class="ic" style="background:{{ $csdSubMeta['bg'] }};color:{{ $csdSubMeta['color'] }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $csdSubMeta['icon'] !!}</svg>
                            </span>
                            <div class="txt">
                                <div class="nm">{{ $cantData->name }}</div>
                                @if($csdSubMeta['desc'])<div class="ds">{{ $csdSubMeta['desc'] }}</div>@endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="row" id="comment_div">
            <div class="csd-sec-head">
                <span class="csd-sec-num">3</span>
                <div><div class="csd-sec-title">Comments</div><div class="csd-sec-sub">Add any additional context for this stage change</div></div>
            </div>
            <textarea id="comment" name="comment" class="form-control form-control-solid csd-comment" rows="4" placeholder="Write comment here..."></textarea>
            <div class="csd-quick-notes" id="csd_quick_notes"></div>
        </div>
    </form>
</div>

<script>
    window.CSD_QUICK_NOTES = {!! json_encode($csdQuickNotes) !!};
    window.CSD_STAGE_META = {!! json_encode($csdStageMeta) !!};

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
        csdUpdateSubmitButton($(el).find('input.master_status').attr('id'));

        // Comment box stays available for every stage (mandatory-for-InReview
        // is still enforced at submit time in #custom_stage_submit, unchanged).
        // Sub Stage visibility is decided purely by whether the backend
        // actually has candidate_status rows for the chosen stage — always
        // fetch and let csdFetchSubStage's response show/hide the section,
        // rather than assuming any particular stage never has any.
        $('#comment_div').show();
        csdFetchSubStage(id);

        csdRenderQuickNotes();
    }

    // Recolors/relabels the drawer's own footer submit button ("Update Stage
    // to X") to match the selected stage — same idea as jobs/status.blade.php's
    // jsdSelectStatus(), just against #custom_stage_submit instead of
    // #status_confirm_btn. The button itself lives in the page's drawer
    // topbar, not in this AJAX-loaded partial, so it's targeted by id.
    function csdUpdateSubmitButton(textName) {
        var meta = textName && window.CSD_STAGE_META[textName];
        if (!meta) { return; }
        $('#custom_stage_submit').css('background', meta.color);
        $('#custom_stage_submit .csdChangeLabel').text('Update Stage to ' + meta.label);
    }

    function csdFetchSubStage(id) {
        if (window.CSD_SUBSTAGE_CACHE[id] !== undefined) {
            $('#substage').html(window.CSD_SUBSTAGE_CACHE[id]);
            $('#main_substage').toggle(window.CSD_SUBSTAGE_CACHE[id] !== '');
            return;
        }
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var url = "{{ route('service_company.job-applications.child_stage', ':id') }}".replace(':id', id);
        $('#main_substage').show();
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function () { $('#substage').html('<img src="' + baseurl + '" height="35px" width="100%">'); },
            success: function (response) {
                if (response.status == 'success') {
                    window.CSD_SUBSTAGE_CACHE[id] = response.html || '';
                    $('#substage').html(response.html || '');
                    $('#main_substage').toggle(!!response.html);
                }
            }
        });
    }

    // csdSelectSub/csdRenderQuickNotes/csdInsertNote are shared, flow-aware
    // function names — child_stage()'s AJAX response (shared by both the
    // plain and client-gated drawers) renders sub-stage tiles with a single
    // onclick="csdSelectSub(this)" regardless of which drawer is currently
    // open, so both stage_body_content.blade.php and
    // client_stage_body_content.blade.php define these identically and pick
    // the right quick-notes source from #status_frm's data-csd-flow.
    function csdSelectSub(el) {
        $('.csd-sub-tile').removeClass('is-selected');
        $(el).addClass('is-selected');
        $(el).find('input').prop('checked', true);
        csdRenderQuickNotes($(el).find('.nm').text());
    }

    function csdRenderQuickNotes(subName) {
        var flow = $('#status_frm').data('csd-flow');
        var source = (flow === 'client') ? window.CSDC_QUICK_NOTES : window.CSD_QUICK_NOTES;
        var textName = $('input[name="master_status"]:checked').attr('id');
        var notes = (source && textName && source[textName]) ? source[textName].slice() : [];
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
    csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
</script>
