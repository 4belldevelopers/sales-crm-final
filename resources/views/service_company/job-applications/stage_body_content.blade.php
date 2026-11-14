{{--
  Intent: Change Candidate Stage Drawer Body (Plain / Direct Recruitment Flow)
  Architecture: Modern Enterprise Card + Sub-Card Design System (Rule 20, UI_UX_STANDARDS §10).
  Features:
    - Live Stage Transition Overview (Current Stage -> Moving To Stage + direction/step cues).
    - Strict conditional Sub Stage section (only activated when >= 1 sub-stage exists; hidden otherwise).
    - Context-aware Comment Suggestions tailored dynamically to both selected Stage and Sub-Stage.
    - 15.6" screen responsive (1366x768 & 1920x1080) with zero horizontal overflow.
  Tenancy: Tenant-scoped via ServiceCompanyJobApplicationController::stage_body_content() & child_stage().
--}}
@php
    $csdFullName = (string) (@$application->full_name ?? '');
    $csdJobTitle = trim((string) (@$application->title ?? ''));
    $csdClientName = trim((string) (@$application->client_name ?? ''));
    $csdCurrentStageId = (int) (@$application->master_status_id ?? 0);
    $csdCurrentSubName = trim((string) (@$child_status_name ?? ''));
    $csdHasCurrentSub = ($csdCurrentSubName !== '' && strtoupper($csdCurrentSubName) !== 'N/A');

    $csdStageMeta = App\Helper\GeneralMethod::pipelineStageTokens();

    $csdQuickNotes = [
        'inreview'   => [
            'Reviewing profile against updated role requirements.',
            'Awaiting internal alignment before sourcing forward.',
            'Verifying candidate compensation and notice period details.',
        ],
        'sourcing'   => [
            'Profile validated and sourced into active pipeline.',
            'Reached out to candidate; awaiting initial confirmation.',
            'Submitted profile for hiring manager review.',
        ],
        'screening'  => [
            'Screening call scheduled with candidate.',
            'Cleared initial screening; core skills and availability confirmed.',
            'Candidate aligned on role scope, location, and CTC range.',
        ],
        'evaluation' => [
            'Technical / panel interview scheduled.',
            'Cleared evaluation round; awaiting panel scorecard.',
            'Proceeding to next assessment / hiring manager round.',
        ],
        'negotation' => [
            "Discussing candidate's revised compensation expectations.",
            'Negotiating notice period buyout and target joining date.',
            'Finalizing salary structure and offer terms.',
        ],
        'offer'      => [
            'Offer released; awaiting candidate formal acceptance.',
            'Offer accepted; initiating pre-onboarding verification.',
            'Following up with candidate on signed offer letter.',
        ],
        'joined'     => [
            'Candidate joined as per scheduled onboarding date.',
            'Day-1 joining formalities and onboarding completed.',
        ],
        'rejected'   => [
            'Skills mismatch for core role requirements.',
            'Candidate accepted a competing offer.',
            'Compensation expectations exceed approved budget.',
            'Candidate unresponsive after multiple follow-ups.',
        ],
    ];

    $csdCurrentKey = 'inreview';
    $csdCurrentLabel = (string) (@$application->master_status_name ?? 'InReview');
    if (!empty($master_status)) {
        foreach ($master_status as $csdM) {
            if ((int) $csdM->id === $csdCurrentStageId) {
                $csdCurrentKey = strtolower((string) (@$csdM->text_name ?? 'inreview'));
                $csdCurrentLabel = $csdStageMeta[$csdCurrentKey]['label'] ?? (string) (@$csdM->name ?? $csdCurrentLabel);
                break;
            }
        }
    }
    $csdCurrentMeta = $csdStageMeta[$csdCurrentKey] ?? [
        'label' => $csdCurrentLabel, 'order' => 1, 'step' => 'Stage',
        'color' => '#475569', 'accent' => '#64748b', 'bg' => '#f1f5f9',
        'icon' => 'M12 6v6l4 2',
    ];

    // IMPORTANT: $candidate_status_Arr is an Illuminate\Support\Collection — PHP empty($collection) is always false.
    $csdSubStageCount = isset($candidate_status_Arr) ? count($candidate_status_Arr) : 0;
    $csdHasSubStages = $csdSubStageCount > 0;
    $csdInitialCommentStep = $csdHasSubStages ? 3 : 2;
@endphp

<div class="csd-wrap"
     data-current-stage-id="{{ $csdCurrentStageId }}"
     data-current-stage-key="{{ $csdCurrentKey }}"
     data-current-stage-label="{{ $csdCurrentMeta['label'] }}"
     data-current-stage-order="{{ $csdCurrentMeta['order'] }}"
     data-current-sub-name="{{ $csdHasCurrentSub ? $csdCurrentSubName : '' }}">

    <style>
        /* Enterprise Drawer Header Background (Rule 20 / Mandatory Dedicated Header Background) */
        #csd_drawer .sd-topbar {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 20 !important;
        }
        #csd_drawer .sd-topbar-ico {
            background: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe !important;
            box-shadow: 0 1px 2px rgba(37, 99, 235, 0.08) !important;
        }
        #csd_drawer .sd-topbar-title {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        #csd_drawer .sd-topbar-sub {
            color: #64748b !important;
        }
        #csd_drawer .sd-btn-cancel {
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
            transition: all 0.15s ease !important;
        }
        #csd_drawer .sd-btn-cancel:hover {
            background: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        #csd_drawer .sd-divider {
            background: #e2e8f0 !important;
        }
        #csd_drawer .sd-close {
            color: #64748b !important;
            border-radius: 8px !important;
            transition: all 0.15s ease !important;
        }
        #csd_drawer .sd-close:hover {
            background: #fee2e2 !important;
            color: #dc2626 !important;
        }

        /* Enterprise Slate Canvas for Drawer Body & Modal */
        #csd_drawer > .d-flex > .flex-grow-1 {
            background: #f1f5f9 !important;
            padding: 16px 20px !important;
        }
        #stage_sub_stage .modal-header {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .csd-wrap {
            font-family: 'Poppins', sans-serif;
            color: #334155;
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }
        #stage_sub_stage .modal-body .csd-wrap {
            background: #eef2f7;
            padding: 16px;
            border-radius: 12px;
        }
        .csd-wrap *, .csd-wrap *::before, .csd-wrap *::after {
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        /* Primary Card Container (Rule 20 / §10) */
        .csd-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.02);
            overflow: hidden;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .csd-summary-card {
            position: relative;
            padding-left: 4px;
            --csd-summary-accent: {{ $csdCurrentMeta['accent'] }};
        }
        .csd-summary-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--csd-summary-accent);
        }
        .csd-summary-card .csd-card-body {
            padding: 10px 14px;
        }
        .csd-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-wrap: wrap;
        }
        .csd-card-head-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .csd-card-ico {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .csd-card-ico svg { width: 16px; height: 16px; }
        .csd-step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #009ef7;
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0, 158, 247, 0.25);
        }
        .csd-card-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
        }
        .csd-card-sub {
            font-size: 11.5px;
            font-weight: 400;
            color: #64748b;
            margin-top: 1px;
            line-height: 1.35;
        }
        .csd-card-head-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .csd-card-body {
            padding: 14px 16px;
        }

        /* Status & Legend Pills */
        .csd-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
            background: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }
        .csd-status-pill.is-advance { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
        .csd-status-pill.is-back    { background: #fffbeb; color: #d97706; border-color: #fde68a; }
        .csd-status-pill.is-reject  { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        .csd-status-pill.is-reopen  { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .csd-status-pill.is-warn    { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .csd-status-pill.is-req     { background: #fffbeb; color: #b45309; border-color: #fde68a; }

        .csd-legend {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
        }
        .csd-legend-item { display: inline-flex; align-items: center; gap: 5px; }
        .csd-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        /* Card 1: Current -> Moving To Sub-Cards */
        .csd-transition-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            gap: 12px;
            align-items: stretch;
        }
        .csd-trans-subcard {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 14px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
            min-width: 0;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }
        .csd-trans-subcard.is-target-active {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }
        .csd-trans-kicker {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
        }
        .csd-trans-main {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .csd-trans-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .csd-trans-icon svg { width: 17px; height: 17px; }
        .csd-trans-info { min-width: 0; flex: 1; }
        .csd-trans-name-row {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }
        .csd-trans-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.25;
        }
        .csd-trans-badge {
            font-size: 9.5px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background: #e2e8f0;
            color: #475569;
        }
        .csd-trans-submeta {
            font-size: 11px;
            font-weight: 400;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .csd-trans-arrow-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 0 2px;
        }
        .csd-trans-arrow-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .csd-trans-arrow-circle.is-moving {
            background: #eff6ff;
            border-color: #93c5fd;
            color: #2563eb;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.16);
        }
        .csd-trans-arrow-circle svg { width: 15px; height: 15px; }
        .csd-trans-arrow-lbl {
            font-size: 9.5px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .csd-seq-hint {
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 11px;
            font-weight: 500;
            display: none;
            align-items: center;
            gap: 8px;
        }
        .csd-seq-hint svg { width: 15px; height: 15px; flex-shrink: 0; color: #d97706; }

        /* Card 2: Pipeline Stage Sub-Cards */
        .csd-stage-row {
            display: flex;
            align-items: stretch;
            gap: 6px;
            flex-wrap: wrap;
        }
        .csd-pill {
            flex: 1 1 94px;
            min-width: 88px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 8px 9px;
            cursor: pointer;
            background: #ffffff;
            text-align: center;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, transform 0.12s ease;
        }
        .csd-pill:hover {
            border-color: #94a3b8;
            background: #f8fafc;
            transform: translateY(-1px);
        }
        .csd-pill.is-current {
            background: #f8fafc;
            border-style: dashed;
            border-color: #94a3b8;
        }
        .csd-pill.is-selected {
            border-style: solid;
            border-color: var(--csd-pill-color, currentColor);
            background: #ffffff;
            box-shadow: 0 0 0 1px var(--csd-pill-color, currentColor) inset, 0 4px 10px rgba(15, 23, 42, 0.06);
        }
        .csd-pill-top {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            margin-bottom: 6px;
            min-height: 16px;
        }
        .csd-badge-current {
            font-size: 8.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 1.5px 5px;
            border-radius: 4px;
            background: #e2e8f0;
            color: #334155;
            line-height: 1.2;
        }
        .csd-badge-step {
            font-size: 9px;
            font-weight: 500;
            color: #94a3b8;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
        }
        .csd-badge-moveto {
            display: none;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 2px 5px;
            border-radius: 4px;
            line-height: 1.2;
        }
        .csd-pill.is-selected:not(.is-current) .csd-badge-step { display: none; }
        .csd-pill.is-selected:not(.is-current) .csd-badge-moveto { display: inline-block; background: var(--csd-pill-color, currentColor); }
        .csd-pill.is-selected:not(.is-current) .csd-badge-moveto span { color: var(--csd-pill-btn-text, #ffffff); }

        .csd-pill .chk {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-left: auto;
        }
        .csd-pill .chk svg { width: 9px; height: 9px; stroke: transparent; }
        .csd-pill.is-selected .chk { background: currentColor; border-color: currentColor; }
        .csd-pill.is-selected .chk svg { stroke: var(--csd-pill-btn-text, #ffffff); }

        .csd-pill .ic {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
        }
        .csd-pill .ic svg { width: 16px; height: 16px; }
        .csd-pill .nm {
            font-size: 11.5px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.25;
        }
        .csd-stage-sep {
            flex-shrink: 0;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            align-self: center;
        }
        .csd-stage-sep svg { width: 13px; height: 13px; }
        .csd-stage-sep-bar {
            flex-shrink: 0;
            width: 1px;
            align-self: stretch;
            background: #e2e8f0;
            margin: 4px 4px;
        }

        /* Card 3: Sub-Stage Sub-Cards Grid */
        .csd-sub-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(192px, 1fr));
            gap: 8px;
        }
        .csd-sub-tile {
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 8px 10px;
            cursor: pointer;
            background: #ffffff;
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .csd-sub-tile:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }
        .csd-sub-tile.is-selected {
            border-color: #009ef7;
            box-shadow: 0 0 0 1px #009ef7 inset;
            background: #f1faff;
        }
        .csd-sub-tile .ic {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .csd-sub-tile .ic svg { width: 15px; height: 15px; }
        .csd-sub-tile .txt { flex: 1; min-width: 0; }
        .csd-sub-tile .txt .nm {
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.3;
        }
        .csd-sub-tile .txt .ds {
            font-size: 10.5px;
            color: #64748b;
            font-weight: 400;
            margin-top: 2px;
            line-height: 1.3;
        }
        .csd-sub-chk {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .csd-sub-chk svg { width: 9px; height: 9px; stroke: transparent; }
        .csd-sub-tile.is-selected .csd-sub-chk {
            background: #009ef7;
            border-color: #009ef7;
        }
        .csd-sub-tile.is-selected .csd-sub-chk svg { stroke: #ffffff; }

        /* Card 4: Comments & Suggestions Sub-Card */
        textarea#comment.csd-comment {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            font-size: 12.5px;
            color: #0f172a;
            padding: 10px 12px;
            resize: vertical;
            min-height: 78px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        textarea#comment.csd-comment:focus {
            border-color: #009ef7;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
            outline: none;
        }
        .csd-suggestions-box {
            margin-top: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
        }
        .csd-suggestions-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }
        .csd-suggestions-title {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
        }
        .csd-suggestions-title svg { width: 13px; height: 13px; color: #009ef7; }
        .csd-qn-context {
            font-size: 10.5px;
            font-weight: 500;
            color: #0284c7;
            background: #e0f2fe;
            padding: 2px 8px;
            border-radius: 999px;
        }
        .csd-clear-comment {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 6px;
            display: none;
        }
        .csd-clear-comment:hover { background: #e2e8f0; color: #0f172a; }
        .csd-quick-notes {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .csd-qn-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            line-height: 1.3;
            transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }
        .csd-qn-chip:hover {
            background: #f1faff;
            border-color: #009ef7;
            color: #0077c2;
        }
        .csd-qn-chip.is-substage {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #1e40af;
        }
        .csd-qn-chip.is-substage:hover {
            background: #dbeafe;
            border-color: #3b82f6;
        }
        .csd-qn-chip.is-used {
            border-color: #10b981;
            background: #ecfdf5;
            color: #047857;
        }
        .csd-qn-tag {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 1px 5px;
            border-radius: 4px;
            background: rgba(37, 99, 235, 0.12);
            color: #1d4ed8;
            flex-shrink: 0;
        }

        /* 15.6" & Smaller Screen Responsive Reflow */
        @media (max-width: 768px) {
            .csd-transition-grid {
                grid-template-columns: minmax(0, 1fr);
            }
            .csd-trans-arrow-col {
                flex-direction: row;
                padding: 2px 0;
            }
            .csd-trans-arrow-circle svg {
                transform: rotate(90deg);
            }
            .csd-stage-sep, .csd-stage-sep-bar { display: none; }
        }
    </style>

    <form class="form" id="status_frm" name="status_frm" method="post" enctype="multipart/form-data" data-csd-flow="plain" style="display:flex;flex-direction:column;gap:10px;">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
        <input type="hidden" name="job_wise_condidates_id" id="job_wise_condidates_id" value="{{ @$application->job_wise_condidates_id }}" />

        <div class="csd-info-name d-none">{{ ucwords($csdFullName) ?: 'Candidate' }}{{ $csdJobTitle !== '' ? ' • ' . $csdJobTitle : '' }}{{ $csdClientName !== '' ? ' (' . $csdClientName . ')' : '' }}</div>

        {{-- CARD 1: Compact Stage Transition Strip (Current Stage -> Moving To Stage) --}}
        <div class="csd-card csd-summary-card" id="csd_summary_card">
            <div class="csd-card-body">
                <div class="csd-transition-grid">
                    {{-- Sub-Card A: Current Stage --}}
                    <div class="csd-trans-subcard" id="csd_current_subcard">
                        <div class="csd-trans-kicker">
                            <span>Current Stage</span>
                            <span>Active Now</span>
                        </div>
                        <div class="csd-trans-main">
                            <span class="csd-trans-icon" style="background:{{ $csdCurrentMeta['bg'] }};color:{{ $csdCurrentMeta['color'] }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $csdCurrentMeta['icon'] }}"/></svg>
                            </span>
                            <div class="csd-trans-info">
                                <div class="csd-trans-name-row">
                                    <span class="csd-trans-name">{{ $csdCurrentMeta['label'] }}</span>
                                    <span class="csd-trans-badge">Current</span>
                                </div>
                                <div class="csd-trans-submeta" title="{{ $csdHasCurrentSub ? $csdCurrentSubName : 'No sub-stage assigned' }}">
                                    @if($csdHasCurrentSub)
                                        Sub-stage: {{ $csdCurrentSubName }}
                                    @else
                                        No sub-stage assigned
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Center Flow Indicator --}}
                    <div class="csd-trans-arrow-col">
                        <span class="csd-trans-arrow-circle" id="csd_trans_arrow_circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </span>
                        <span class="csd-trans-arrow-lbl" id="csd_trans_arrow_lbl">Same Stage</span>
                    </div>

                    {{-- Sub-Card B: Moving To Stage --}}
                    <div class="csd-trans-subcard" id="csd_target_subcard">
                        <div class="csd-trans-kicker">
                            <span id="csd_target_kicker">Moving To Stage</span>
                            <span id="csd_target_step_lbl">{{ $csdCurrentMeta['step'] }}</span>
                        </div>
                        <div class="csd-trans-main">
                            <span class="csd-trans-icon" id="csd_target_icon" style="background:{{ $csdCurrentMeta['bg'] }};color:{{ $csdCurrentMeta['color'] }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $csdCurrentMeta['icon'] }}"/></svg>
                            </span>
                            <div class="csd-trans-info">
                                <div class="csd-trans-name-row">
                                    <span class="csd-trans-name" id="csd_target_name">{{ $csdCurrentMeta['label'] }}</span>
                                    <span class="csd-trans-badge" id="csd_target_pill">No Change</span>
                                </div>
                                <div class="csd-trans-submeta" id="csd_target_sub_meta">
                                    @if($csdHasSubStages)
                                        {{ $csdHasCurrentSub ? ('Sub-stage: ' . $csdCurrentSubName) : 'Select a sub-stage below' }}
                                    @else
                                        Select a target stage below to move
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="csd-seq-hint" id="csd_sequence_hint">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span id="csd_sequence_hint_text"></span>
                </div>
            </div>
        </div>

        {{-- CARD 2: Choose Pipeline Stage --}}
        <div class="csd-card">
            <div class="csd-card-head">
                <div class="csd-card-head-left">
                    <span class="csd-step-num">1</span>
                    <div>
                        <div class="csd-card-title">Choose New Stage</div>
                        <div class="csd-card-sub">Select the target stage to move this candidate to</div>
                    </div>
                </div>
                <div class="csd-card-head-right">
                    <div class="csd-legend">
                        <span class="csd-legend-item"><span class="csd-dot" style="background:#94a3b8;"></span> Current Stage</span>
                        <span class="csd-legend-item"><span class="csd-dot" id="csd_legend_moveto_dot" style="background:{{ $csdCurrentMeta['accent'] }};"></span> Moving To</span>
                    </div>
                </div>
            </div>
            <div class="csd-card-body">
                <div class="csd-stage-row">
                    @if(!empty($master_status))
                        @php
                            $csdKeysList = [];
                            foreach ($master_status as $csdM) { $csdKeysList[] = strtolower((string) (@$csdM->text_name ?? '')); }
                        @endphp
                        @foreach($master_status as $mstatus)
                            @php
                                $csdKey = strtolower((string) (@$mstatus->text_name ?? ''));
                                $csdMeta = $csdStageMeta[$csdKey] ?? [
                                    'label' => @$mstatus->name, 'order' => $loop->iteration, 'step' => 'Step '.$loop->iteration,
                                    'color' => '#475569', 'accent' => '#64748b', 'bg' => '#eef2f6', 'icon' => 'M12 6v6l4 2'
                                ];
                                $csdIsCurrent = ((int) $mstatus->id === $csdCurrentStageId);
                                $csdChk = $csdIsCurrent ? 'checked="checked"' : '';
                            @endphp
                            <div class="csd-pill {{ $csdIsCurrent ? 'is-current is-selected' : '' }} master_status_div"
                                 data-master-status-id="{{ @$mstatus->id }}"
                                 data-stage-key="{{ $csdKey }}"
                                 data-stage-label="{{ $csdMeta['label'] }}"
                                 data-stage-order="{{ $csdMeta['order'] }}"
                                 data-stage-step="{{ $csdMeta['step'] }}"
                                 data-stage-color="{{ $csdMeta['accent'] }}"
                                 data-stage-text-color="{{ $csdMeta['color'] }}"
                                 data-stage-bg="{{ $csdMeta['bg'] }}"
                                 data-stage-btn-text="{{ $csdMeta['btn_text'] ?? '#ffffff' }}"
                                 style="--csd-pill-color:{{ $csdMeta['accent'] }};--csd-pill-btn-text:{{ $csdMeta['btn_text'] ?? '#ffffff' }};color:{{ $csdMeta['accent'] }};"
                                 onclick="csdSelectStage(this)">
                                <input type="radio" class="d-none master_status" name="master_status" value="{{ @$mstatus->id }}" {{ $csdChk }} id="{{ @$mstatus->text_name }}">
                                <div class="csd-pill-top">
                                    @if($csdIsCurrent)
                                        <span class="csd-badge-current">Current</span>
                                    @else
                                        <span class="csd-badge-step">{{ $csdMeta['step'] }}</span>
                                        <span class="csd-badge-moveto"><span>Move To</span></span>
                                    @endif
                                    <span class="chk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                                </div>
                                <span class="ic" style="background:{{ $csdMeta['bg'] }};color:{{ $csdMeta['color'] }};">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $csdMeta['icon'] }}"/></svg>
                                </span>
                                <div class="nm">{{ $csdMeta['label'] }}</div>
                            </div>
                            @unless($loop->last)
                                @if(($csdKeysList[$loop->index + 1] ?? null) === 'rejected')
                                    <span class="csd-stage-sep-bar" aria-hidden="true"></span>
                                @else
                                    <span class="csd-stage-sep" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg></span>
                                @endif
                            @endunless
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- CARD 3: Sub Stage (Strictly hidden unless selected stage has >= 1 sub-stage) --}}
        <div id="main_substage" class="csd-card" @if(!$csdHasSubStages) style="display:none;" @endif>
            <div class="csd-card-head subStageSection">
                <div class="csd-card-head-left">
                    <span class="csd-step-num">2</span>
                    <div>
                        <div class="csd-card-title">Sub Stage</div>
                        <div class="csd-card-sub" id="csd_substage_subtitle">Select the specific reason for {{ $csdCurrentMeta['label'] }}</div>
                    </div>
                </div>
                <div class="csd-card-head-right">
                    <span class="csd-status-pill" id="csd_substage_count">{{ $csdSubStageCount }} {{ $csdSubStageCount === 1 ? 'Option' : 'Options' }}</span>
                    <span class="csd-status-pill is-req">Required</span>
                </div>
            </div>
            <div class="csd-card-body">
                <div class="csd-sub-row" id="substage">
                    @if($csdHasSubStages)
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
                                <span class="csd-sub-chk" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- CARD 4: Comments & Stage/Sub-Stage Suggestions --}}
        <div id="comment_div" class="csd-card">
            <div class="csd-card-head">
                <div class="csd-card-head-left">
                    <span class="csd-step-num" id="csd_comment_step_num">{{ $csdInitialCommentStep }}</span>
                    <div>
                        <div class="csd-card-title">Comments</div>
                        <div class="csd-card-sub">Add context or notes for this stage change</div>
                    </div>
                </div>
                <div class="csd-card-head-right">
                    <span class="csd-status-pill {{ $csdCurrentKey === 'inreview' ? 'is-req' : '' }}" id="csd_comment_req_badge">
                        {{ $csdCurrentKey === 'inreview' ? 'Required for InReview' : 'Optional' }}
                    </span>
                </div>
            </div>
            <div class="csd-card-body">
                <textarea id="comment" name="comment" class="form-control csd-comment" rows="3" placeholder="Write comment here or select a suggested note below..."></textarea>
                <div class="csd-suggestions-box">
                    <div class="csd-suggestions-head">
                        <div class="csd-suggestions-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <span>Suggested Comments</span>
                            <span class="csd-qn-context" id="csd_qn_context">For {{ $csdCurrentMeta['label'] }}</span>
                        </div>
                        <button type="button" class="csd-clear-comment" id="csd_clear_comment" onclick="csdClearComment()">Clear Comment</button>
                    </div>
                    <div class="csd-quick-notes" id="csd_quick_notes"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    window.CSD_QUICK_NOTES = {!! json_encode($csdQuickNotes) !!};
    window.CSD_STAGE_META = {!! json_encode($csdStageMeta) !!};
    window.CSD_SUBSTAGE_CACHE = window.CSD_SUBSTAGE_CACHE || {};

    function csdCountSubTiles(html) {
        if (!html || !$.trim(html)) { return 0; }
        var tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.querySelectorAll('.csd-sub-tile').length;
    }

    function csdApplySubStageState(html, stageLabel) {
        var count = csdCountSubTiles(html);
        if (count > 0) {
            $('#substage').html(html);
            $('#csd_substage_subtitle').text('Select the specific reason for ' + (stageLabel || 'this stage'));
            $('#csd_substage_count').text(count + (count === 1 ? ' Option' : ' Options'));
            $('#main_substage').show();
            $('#csd_comment_step_num').text('3');
        } else {
            $('#substage').empty();
            $('#main_substage').hide();
            $('#csd_comment_step_num').text('2');
        }
        csdUpdateTransitionSummary();
    }

    function csdUpdateTransitionSummary() {
        var $wrap = $('.csd-wrap').first();
        if (!$wrap.length) { return; }

        var curId = parseInt($wrap.attr('data-current-stage-id'), 10) || 0;
        var curKey = ($wrap.attr('data-current-stage-key') || '').toLowerCase();
        var curLabel = $wrap.attr('data-current-stage-label') || 'Current';
        var curOrder = parseInt($wrap.attr('data-current-stage-order'), 10) || 1;

        var $sel = $('.csd-pill.is-selected').first();
        if (!$sel.length) { return; }

        var selId = parseInt($sel.data('master-status-id'), 10) || 0;
        var selKey = ($sel.data('stage-key') || '').toLowerCase();
        var selLabel = $sel.data('stage-label') || '';
        var selOrder = parseInt($sel.data('stage-order'), 10) || 1;
        var selStep = $sel.data('stage-step') || '';
        var selColor = $sel.data('stage-color') || '#009ef7';
        var selTextColor = $sel.data('stage-text-color') || selColor;
        var selBg = $sel.data('stage-bg') || '#eff6ff';
        var selMeta = window.CSD_STAGE_META[selKey];

        // Update summary card accent & Moving To legend dot
        $('#csd_summary_card').css('--csd-summary-accent', selColor);
        $('#csd_legend_moveto_dot').css('background', selColor);

        // Update Target Sub-Card
        var isSame = (selId === curId);
        var $targetCard = $('#csd_target_subcard');
        $targetCard.toggleClass('is-target-active', !isSame);
        $targetCard.css('border-color', isSame ? '#e2e8f0' : selColor);

        $('#csd_target_kicker').text(isSame ? 'Selected Stage' : 'Moving To Stage').css('color', isSame ? '#64748b' : selTextColor);
        $('#csd_target_step_lbl').text(selStep);
        $('#csd_target_name').text(selLabel);
        $('#csd_target_icon').css({ background: selBg, color: selTextColor });
        if (selMeta && selMeta.icon) {
            $('#csd_target_icon').html('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="' + selMeta.icon + '"/></svg>');
        }

        var $pill = $('#csd_target_pill');
        if (isSame) {
            $pill.text('No Change').css({ background: '#e2e8f0', color: '#475569' });
        } else {
            $pill.text('Target').css({ background: selBg, color: selTextColor });
        }

        // Sub-stage text in target sub-card
        var selectedSubName = $('.csd-sub-tile.is-selected .nm').first().text().trim();
        var subVisible = $('#main_substage').is(':visible');
        if (selectedSubName) {
            $('#csd_target_sub_meta').text('Sub-stage: ' + selectedSubName);
        } else if (subVisible) {
            $('#csd_target_sub_meta').text('Select a sub-stage below (required)');
        } else if (isSame) {
            $('#csd_target_sub_meta').text('Select a different stage below to move');
        } else {
            $('#csd_target_sub_meta').text('No sub-stage required');
        }

        // Arrow + Transition Status Badge + Sequence Advisory
        var $arrowCircle = $('#csd_trans_arrow_circle');
        var $arrowLbl = $('#csd_trans_arrow_lbl');
        var $badge = $('#csd_transition_badge');
        var $seqHint = $('#csd_sequence_hint');

        $badge.removeClass('is-advance is-back is-reject is-reopen is-warn');
        $seqHint.hide();

        if (isSame) {
            $arrowCircle.removeClass('is-moving').css({ background: '', borderColor: '', color: '' });
            $arrowLbl.text('Same Stage').css('color', '#64748b');
            $badge.text(selectedSubName ? 'Updating Sub-Stage' : 'Staying in Current Stage');
        } else {
            $arrowCircle.addClass('is-moving').css({ background: selBg, borderColor: selColor, color: selTextColor });
            $arrowLbl.text('Moving To').css('color', selTextColor);

            if (selKey === 'rejected') {
                $badge.addClass('is-reject').text(curLabel + ' \u2192 Rejected');
            } else if (curKey === 'rejected') {
                $badge.addClass('is-reopen').text('Reopening \u2192 ' + selLabel);
            } else {
                var diff = selOrder - curOrder;
                if (diff === 1) {
                    $badge.addClass('is-advance').text('Advancing +1 Step');
                } else if (diff === -1) {
                    $badge.addClass('is-back').text('Moving Back 1 Step');
                } else if (diff > 1) {
                    $badge.addClass('is-warn').text('Jumping +' + diff + ' Steps');
                    $('#csd_sequence_hint_text').text('Pipeline stages normally advance one step at a time from ' + curLabel + '. Jumping directly to ' + selLabel + ' may be blocked by stage sequence rules.');
                    $seqHint.css('display', 'flex');
                } else if (diff < -1) {
                    $badge.addClass('is-warn').text('Moving Back ' + Math.abs(diff) + ' Steps');
                    $('#csd_sequence_hint_text').text('Pipeline stages normally move one step at a time from ' + curLabel + '. Moving back multiple steps to ' + selLabel + ' may be blocked by stage sequence rules.');
                    $seqHint.css('display', 'flex');
                }
            }
        }

        // Comment requirement badge (InReview requires a comment on submit)
        if (selKey === 'inreview') {
            $('#csd_comment_req_badge').addClass('is-req').text('Required for InReview');
        } else {
            $('#csd_comment_req_badge').removeClass('is-req').text('Optional');
        }
    }

    function csdSelectStage(el) {
        var id = $(el).data('master-status-id');
        var stageLabel = $(el).data('stage-label') || '';
        $(el).find('input.master_status').prop('checked', true);

        $('.csd-pill').removeClass('is-selected');
        $(el).addClass('is-selected');
        csdUpdateSubmitButton($(el).find('input.master_status').attr('id'));

        $('#comment_div').show();
        csdFetchSubStage(id, stageLabel);
        csdRenderQuickNotes();
        csdUpdateTransitionSummary();
    }

    function csdUpdateSubmitButton(textName) {
        var meta = textName && window.CSD_STAGE_META[textName];
        if (!meta) { return; }
        var btnBg = meta.btn_bg || meta.accent || meta.color;
        var btnText = meta.btn_text || '#ffffff';
        $('#custom_stage_submit').css({
            'background': btnBg,
            'color': btnText
        });
        $('#custom_stage_submit .csdChangeLabel').text('Update Stage to ' + meta.label);
    }

    function csdFetchSubStage(id, stageLabel) {
        if (window.CSD_SUBSTAGE_CACHE[id] !== undefined) {
            var cached = window.CSD_SUBSTAGE_CACHE[id];
            var cachedHtml = (typeof cached === 'string') ? cached : (cached.html || '');
            csdApplySubStageState(cachedHtml, stageLabel);
            return;
        }
        // Keep #main_substage strictly hidden until we verify >= 1 sub-stage exists
        $('#substage').empty();
        $('#main_substage').hide();
        $('#csd_comment_step_num').text('2');

        var url = "{{ route('service_company.job-applications.child_stage', ':id') }}".replace(':id', id);
        $.ajax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response && response.status == 'success') {
                    var html = response.html || '';
                    window.CSD_SUBSTAGE_CACHE[id] = { html: html, count: csdCountSubTiles(html) };
                    // Only apply if this stage is still the currently selected one
                    if (String($('input[name="master_status"]:checked').val()) === String(id)) {
                        csdApplySubStageState(html, stageLabel);
                    }
                }
            }
        });
    }

    function csdSelectSub(el) {
        $('.csd-sub-tile').removeClass('is-selected');
        $(el).addClass('is-selected');
        $(el).find('input').prop('checked', true);
        var subName = $(el).find('.nm').text().trim();
        csdRenderQuickNotes(subName);
        csdUpdateTransitionSummary();
    }

    function csdSubStageSuggestions(subName) {
        if (!subName) { return []; }
        var s = subName.toLowerCase();
        var list = [];
        if (s.indexOf('duplicate') > -1) {
            list.push('Duplicate profile — candidate is already active in pipeline for this role.', 'Merged reference with existing candidate submission.');
        } else if (s.indexOf('not interest') > -1 || s.indexOf('not looking') > -1) {
            list.push('Candidate is not interested in this opportunity at this time.', 'Candidate prefers current role; retaining profile for future openings.');
        } else if (s.indexOf('not relevant') > -1 || s.indexOf('mismatch') > -1) {
            list.push('Profile experience does not align with core role requirements.', 'Domain / tech stack mismatch against the job specification.');
        } else if (s.indexOf('stability') > -1) {
            list.push('Frequent job transitions noted; stability criteria not met.', 'Tenure history flagged during screening review.');
        } else if (s.indexOf('salary') > -1 || s.indexOf('compensation') > -1 || s.indexOf('ctc') > -1 || s.indexOf('budget') > -1) {
            list.push('Compensation expectations exceed approved role budget.', 'Unable to align on revised CTC / rate expectations.');
        } else if (s.indexOf('notice') > -1) {
            list.push('Notice period exceeds immediate project joining timeline.', 'Notice period non-negotiable for target onboarding window.');
        } else if (s.indexOf('interview reject') > -1) {
            list.push('Did not clear technical / panel evaluation criteria.', 'Panel feedback noted competency gaps for this role.');
        } else if (s.indexOf('fake') > -1 || s.indexOf('proxy') > -1) {
            list.push('Profile integrity / verification discrepancy flagged.', 'Inconsistent background details observed during evaluation.');
        } else if (s.indexOf('no show') > -1 || s.indexOf('noshow') > -1) {
            list.push('Candidate did not attend the scheduled interview slot.', 'No response from candidate at scheduled call time.');
        } else if (s.indexOf('back') > -1) {
            list.push('Candidate backed out prior to onboarding due to competing offer / personal reasons.', 'Candidate declined to proceed with joining as scheduled.');
        } else if (s.indexOf('onboard') > -1) {
            list.push('Candidate successfully onboarded and confirmed active.', 'All joining and onboarding formalities completed.');
        } else if (s.indexOf('hold') > -1) {
            list.push('Application kept on hold pending hiring manager / client update.', 'Role requirements temporarily paused; keeping candidate warm.');
        } else if (s.indexOf('schedul') > -1) {
            list.push('Interview slot confirmed and calendar invite shared with panel.', 'Coordinating availability for scheduled evaluation round.');
        } else if (s.indexOf('background') > -1 || s.indexOf('bgv') > -1) {
            list.push('Background verification (BGV) initiated; awaiting clearance.', 'Pending employment and education verification documents.');
        } else if (s.indexOf('withdraw') > -1) {
            list.push('Candidate voluntarily withdrew application from consideration.', 'Withdrawn at candidate request due to personal priorities.');
        } else if (s.indexOf('unresponsive') > -1 || s.indexOf('not contact') > -1) {
            list.push('Candidate unreachable after multiple call and email attempts.', 'No response received after repeated follow-ups.');
        } else if (s.indexOf('declin') > -1) {
            list.push('Candidate declined the offer / next interview step.', 'Declined due to location or compensation preferences.');
        } else if (s.indexOf('shortlist') > -1) {
            list.push('Profile shortlisted by hiring panel for next round.', 'Shortlisted for upcoming evaluation.');
        } else if (s.indexOf('screen') > -1) {
            list.push('Initial screening completed; summary shared with hiring team.', 'Awaiting screening outcome confirmation.');
        } else if (s.indexOf('offer') > -1) {
            list.push('Offer terms discussed and shared for candidate review.', 'Awaiting formal offer acceptance confirmation.');
        } else if (s.indexOf('other') > -1) {
            list.push('Stage updated with custom recruiter remarks below.');
        } else {
            list.push('Stage updated under "' + subName + '" — proceeding as discussed.', 'Recorded sub-stage: ' + subName + '.');
        }
        return list;
    }

    function csdRenderQuickNotes(subName) {
        var flow = $('#status_frm').data('csd-flow');
        var source = (flow === 'client') ? window.CSDC_QUICK_NOTES : window.CSD_QUICK_NOTES;
        var textName = $('input[name="master_status"]:checked').attr('id');
        var stageMeta = (textName && window.CSD_STAGE_META[textName]) ? window.CSD_STAGE_META[textName] : null;
        var stageLabel = stageMeta ? stageMeta.label : (textName || 'Stage');

        if (!subName) {
            subName = $('.csd-sub-tile.is-selected .nm').first().text().trim();
        }

        var subNotes = csdSubStageSuggestions(subName);
        var stageNotes = (source && textName && source[textName]) ? source[textName].slice() : [];

        $('#csd_qn_context').text(subName ? ('For ' + stageLabel + ' \u203A ' + subName) : ('For ' + stageLabel));

        var existingVal = ($('#comment').val() || '');
        var html = '';

        subNotes.forEach(function (n) {
            var used = existingVal.indexOf(n) > -1 ? ' is-used' : '';
            var safe = $('<div>').text(n).html();
            html += '<span class="csd-qn-chip is-substage' + used + '" onclick="csdInsertNote(this)" data-note="' + safe.replace(/"/g, '&quot;') + '">'
                  + '<span class="csd-qn-tag">Sub-stage</span>'
                  + '<span class="csd-qn-txt">' + safe + '</span>'
                  + '</span>';
        });

        stageNotes.forEach(function (n) {
            var used = existingVal.indexOf(n) > -1 ? ' is-used' : '';
            var safe = $('<div>').text(n).html();
            html += '<span class="csd-qn-chip' + used + '" onclick="csdInsertNote(this)" data-note="' + safe.replace(/"/g, '&quot;') + '">'
                  + '<span class="csd-qn-txt">' + safe + '</span>'
                  + '</span>';
        });

        $('#csd_quick_notes').html(html);
    }

    function csdInsertNote(el) {
        var txt = $(el).attr('data-note') || $(el).find('.csd-qn-txt').text() || $(el).text();
        var existing = ($('#comment').val() || '').trim();
        if (existing.indexOf(txt) === -1) {
            $('#comment').val(existing ? (existing + ' ' + txt) : txt).trigger('input');
        }
        $(el).addClass('is-used');
    }

    function csdClearComment() {
        $('#comment').val('').trigger('input').focus();
        $('.csd-qn-chip').removeClass('is-used');
    }

    $('#comment').on('input', function () {
        var val = ($(this).val() || '').trim();
        $('#csd_clear_comment').toggle(val.length > 0);
    });

    // Initial state sync
    var initialSubName = $('.csd-sub-tile.is-selected .nm').first().text().trim();
    csdRenderQuickNotes(initialSubName);
    csdUpdateSubmitButton($('input[name="master_status"]:checked').attr('id'));
    csdUpdateTransitionSummary();
</script>

@include('service_company.job-applications.stage_validation_modal')
