@php
    // full_name is nullable — cast to string before it ever reaches a string
    // function: Laravel promotes the "deprecated null argument" notice into a
    // thrown ErrorException (500), and `@` only suppresses the property
    // access itself, not the null value it produces. Kept as a hidden
    // element (not a visible hero card, removed per request) purely so each
    // page's drawer topbar JS can read the candidate's name into its subtitle.
    $csdcFullName = (string) (@$application->full_name ?? '');

    // Same palette as the plain-flow partial (stage_body_content.blade.php) —
    // duplicated here since each modal/drawer body partial is already
    // self-contained in this codebase (matches the existing pattern where
    // both partials independently render their own candidate-info table).
    // InReview's color is the one the OLD modal already used
    // (`bg-light-warning text-warning` on #lbl_master_status_8) — Metronic's
    // "warning" theme color, #ffa800 / #fff4de — not an invented one.
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

    $csdcQuickNotes = [
        'inreview'   => ['Reviewing profile against updated requirement.', 'Awaiting internal alignment before moving forward.'],
        'sourcing'   => ['Added to pipeline, awaiting initial screening.', 'Reached out, awaiting candidate response.'],
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

        .csd-notice { display: flex; align-items: center; gap: 8px; background: #fff8ec; border: 1px solid #ffe4b3; color: #a9670a; border-radius: 10px; padding: 8px 14px; font-size: 11px; font-weight: 500; margin-bottom: 14px; }
        .csd-notice i { color: #ffab00; }

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

    <form class="form" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data" data-csd-flow="client">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
        <input type="hidden" name="job_wise_condidates_id" id="job_wise_condidates_id" value="{{ @$application->job_wise_condidates_id }}" />

        <div class="csd-info-name d-none">{{ ucwords($csdcFullName) }}</div>

        <div class="csd-notice"><i class="fa fa-info-circle"></i> This is a client-managed job. In Review and Rejected are private to your team. Moving a candidate to Sourcing makes the profile visible to the client, after which all subsequent hiring stages are managed by the client.</div>

        <div class="csd-sec-head">
            <span class="csd-sec-num">1</span>
            <div><div class="csd-sec-title">Choose New Stage</div><div class="csd-sec-sub">Select the stage to move this candidate to</div></div>
        </div>
        <div class="csd-stage-row">
            @if(!empty($master_status))
                @php
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
                    <div class="csd-pill {{ $csdIsCurrent ? 'is-current' : '' }} client_master_status_div" data-master-status-id="{{ @$mstatus->id }}" style="color:{{ $csdMeta['color'] }};" onclick="csdcSelectStage(this)">
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

        @php
            // Sub Stage starts hidden only when: this application's current
            // stage is InReview(8) (agency-only, no sub-stages by design), or
            // simply when there's no data for it yet. requisition_owner==0
            // used to force-hide this for EVERY other stage too — including
            // Rejected(7) — which silently suppressed a recruiter's own
            // rejection-reason tiles on a client-managed job even though they
            // are the ones authorized to reject from InReview in the first
            // place. Removed: sub-stage visibility for every non-InReview
            // stage (Rejected included) is now decided purely by whether the
            // backend actually has data for it, matching the comment box
            // (which already shows for Rejected regardless of ownership).
            $csdcCurrentId = (int) (@$application->master_status_id ?? 0);
            $csdcSubHiddenInitially = empty($candidate_status_Arr)
                || $csdcCurrentId === 8;
        @endphp
        <div id="main_substage" @if($csdcSubHiddenInitially) style="display:none;" @endif>
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

        <div class="row" id="comment_div" style="display:none">
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
    window.CSDC_QUICK_NOTES = {!! json_encode($csdcQuickNotes) !!};
    window.CSD_STAGE_META = {!! json_encode($csdStageMeta) !!};
    window.CSD_SUBSTAGE_CACHE = window.CSD_SUBSTAGE_CACHE || {};

    /*
     * Reproduces the submit-actionable gating the old inline script had
     * (stage ids 1/9/7 only, comment box shown for 7) — rewritten cleanly
     * against the new pill markup instead of `.btn-check` toggle buttons.
     * Sub Stage visibility for Rejected(7) is no longer force-hidden in
     * favor of the comment box — it now shows its rejection-reason tiles
     * the same way every other fetchable stage does. The submit button
     * itself lives in the page's drawer topbar (#int_stage_submit, shown/
     * hidden here); its own click handler and client_stage_submit()
     * validation are untouched.
     */
    function csdcSelectStage(el) {
        var id = $(el).data('master-status-id');
        $(el).find('input.master_status').prop('checked', true);

        $('.csd-pill').removeClass('is-selected');
        $(el).addClass('is-selected');
        csdUpdateSubmitButton($(el).find('input.master_status').attr('id'));

        $('#comment_div').hide();

        if (id == 1 || id == 9 || id == 7) {
            $('#int_stage_submit').show();
        } else {
            $('#int_stage_submit').hide();
        }

        if (id == 7) {
            $('#comment_div').show();
        }

        // InReview(8) never has sub-stages by design, so there's nothing to
        // fetch. Every other stage's Sub Stage visibility — including
        // Rejected(7) — is decided purely by whether the backend actually
        // has data for it. (requisition_owner==0 used to also suppress this
        // for every non-InReview stage, which silently hid a recruiter's own
        // rejection-reason tiles on a client-managed job even though they're
        // the one authorized to reject from InReview in the first place —
        // removed.)
        if (id == 8) {
            $('#main_substage').hide();
        } else {
            csdcFetchSubStage(id);
        }

        csdRenderQuickNotes();
    }

    // Recolors/relabels #int_stage_submit to match the selected stage — same
    // idea as jobs/status.blade.php's jsdSelectStatus(). The button lives in
    // the page's drawer topbar, not this AJAX-loaded partial, hence by id.
    function csdUpdateSubmitButton(textName) {
        var meta = textName && window.CSD_STAGE_META[textName];
        if (!meta) { return; }
        $('#int_stage_submit').css('background', meta.color);
        $('#int_stage_submit .csdChangeLabel').text('Update Stage to ' + meta.label);
    }

    function csdcFetchSubStage(id) {
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
            contentType: false,
            cache: false,
            processData: false,
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

    // csdSelectSub/csdRenderQuickNotes/csdInsertNote are the shared,
    // flow-aware names also defined in stage_body_content.blade.php —
    // child_stage()'s AJAX response renders sub-stage tiles with a single
    // onclick="csdSelectSub(this)" regardless of which drawer is open, so
    // both partials define these identically and pick the right quick-notes
    // source from #status_frm's data-csd-flow at call time.
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

    // Initial button visibility matches the current stage, same as before.
    var csdcInitId = $('input[name="master_status"]:checked').val();
    if (csdcInitId == 1 || csdcInitId == 9 || csdcInitId == 7) { $('#int_stage_submit').show(); } else { $('#int_stage_submit').hide(); }
    csdRenderQuickNotes();
    csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
</script>
