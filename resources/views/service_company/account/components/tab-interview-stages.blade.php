{{-- 
  JERO ATS — Service Company Interview Pipeline Builder (Stack-Based Card + Sub-Card Design)
  Rule 20 Compliant: Modern Card + Sub-Card Enterprise Design System
  Tenancy & Security: Tenant-scoped data isolation by company_id
  Screen Fit: 100% fluid fit for 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow
--}}

@php
  $sourcingList    = $interviewStage_sourcing ?? [];
  $screeningList   = $interviewStage_screening ?? [];
  $evaluationList  = $interviewStage_evaluation ?? [];
  $negotiationList = $interviewStage_negotiation ?? [];
  $offeredList     = $interviewStage_offered ?? [];
  $joinedList      = $interviewStage_joined ?? [];
  $rejectList      = $interviewStage_reject ?? [];

  $sourcingCount    = is_countable($sourcingList) ? count($sourcingList) : 0;
  $screeningCount   = is_countable($screeningList) ? count($screeningList) : 0;
  $evaluationCount  = is_countable($evaluationList) ? count($evaluationList) : 0;
  $negotiationCount = is_countable($negotiationList) ? count($negotiationList) : 0;
  $offeredCount     = is_countable($offeredList) ? count($offeredList) : 0;
  $joinedCount      = is_countable($joinedList) ? count($joinedList) : 0;
  $rejectCount      = is_countable($rejectList) ? count($rejectList) : 0;

  $totalSubStages = $sourcingCount + $screeningCount + $evaluationCount + $negotiationCount + $offeredCount + $joinedCount + $rejectCount;

  $stagesConfig = [
    [
      'id'          => 1,
      'key'         => 'sourcing',
      'root'        => 'root_sourcing',
      'class'       => 'bg-secondary',
      'step'        => '01',
      'title'       => 'Sourcing',
      'tag'         => 'Inbound / Outbound Pool',
      'desc'        => 'Initial candidate discovery, requisition outreach, referrals & resume intake.',
      'icon'        => 'fas fa-search-plus',
      'color'       => '#318ebb',
      'light'       => '#f0f7fa',
      'border'      => '#bfdbfe',
      'list'        => $sourcingList,
      'count'       => $sourcingCount,
      'empty_text'  => 'Candidates progress directly into Sourcing. Use the header Add Substage button to configure sub-stages.'
    ],
    [
      'id'          => 2,
      'key'         => 'screening',
      'root'        => 'root_screening',
      'class'       => 'screening-light',
      'step'        => '02',
      'title'       => 'Screening',
      'tag'         => 'Initial Qualification',
      'desc'        => 'Recruiter vetting, phone screening, resume verification & prerequisite checks.',
      'icon'        => 'fas fa-filter',
      'color'       => '#a855f7',
      'light'       => '#faf5ff',
      'border'      => '#e9d5ff',
      'list'        => $screeningList,
      'count'       => $screeningCount,
      'empty_text'  => 'Configure customized screening milestones like "Recruiter Vetting Call" or "Pre-Assessment Quiz".'
    ],
    [
      'id'          => 3,
      'key'         => 'evaluation',
      'root'        => 'root_evaluation',
      'class'       => 'evaluation-light',
      'step'        => '03',
      'title'       => 'Evaluation',
      'tag'         => 'Technical & Cultural Rounds',
      'desc'        => 'Panel interviews, client rounds, coding scorecards & technical assessments.',
      'icon'        => 'fas fa-user-check',
      'color'       => '#009ef7',
      'light'       => '#eff6ff',
      'border'      => '#bfdbfe',
      'list'        => $evaluationList,
      'count'       => $evaluationCount,
      'empty_text'  => 'Add evaluation milestones such as "L1 Technical Round", "Client Assessment", or "System Design".'
    ],
    [
      'id'          => 4,
      'key'         => 'negotation',
      'root'        => 'root_negotation',
      'class'       => 'negotation-light',
      'step'        => '04',
      'title'       => 'Negotiation',
      'tag'         => 'Compensation & Rates',
      'desc'        => 'Client rate alignment, candidate salary expectations & contract term finalization.',
      'icon'        => 'fas fa-comments-dollar',
      'color'       => '#ec4899',
      'light'       => '#fdf2f8',
      'border'      => '#fbcfe8',
      'list'        => $negotiationList,
      'count'       => $negotiationCount,
      'empty_text'  => 'Define sub-stages like "Rate Discussion", "Client Approval", or "Notice Period Buyout".'
    ],
    [
      'id'          => 5,
      'key'         => 'offer',
      'root'        => 'root_offer',
      'class'       => 'offer-light',
      'step'        => '05',
      'title'       => 'Offer',
      'tag'         => 'Letter Released & Acceptance',
      'desc'        => 'Offer generation, release to candidate, acceptance tracking & background check initiation.',
      'icon'        => 'fas fa-file-signature',
      'color'       => '#f59e0b',
      'light'       => '#fffbeb',
      'border'      => '#fde68a',
      'list'        => $offeredList,
      'count'       => $offeredCount,
      'empty_text'  => 'Track offer milestones like "Offer Released", "Candidate Accepted", or "Offer Countered".'
    ],
    [
      'id'          => 6,
      'key'         => 'joined',
      'root'        => 'root_joined',
      'class'       => 'joined-light',
      'step'        => '06',
      'title'       => 'Joined',
      'tag'         => 'Deployed & Active',
      'desc'        => 'Successful candidate placement, orientation, client day 1 billing start & retention.',
      'icon'        => 'fas fa-briefcase',
      'color'       => '#10b981',
      'light'       => '#ecfdf5',
      'border'      => '#a7f3d0',
      'list'        => $joinedList,
      'count'       => $joinedCount,
      'empty_text'  => 'Configure post-joining onboarding check-ins and client deployment confirmations.'
    ],
    [
      'id'          => 7,
      'key'         => 'rejected',
      'root'        => 'root_rejected',
      'class'       => 'rejected-light',
      'step'        => '07',
      'title'       => 'Rejected',
      'tag'         => 'Pipeline Exit Branch',
      'desc'        => 'Disqualified candidates, client declines, duplicate submissions or candidate dropouts.',
      'icon'        => 'fas fa-times-circle',
      'color'       => '#ef4444',
      'light'       => '#fef2f2',
      'border'      => '#fecaca',
      'list'        => $rejectList,
      'count'       => $rejectCount,
      'empty_text'  => 'Track rejection reasons like "Technical Mismatch", "Candidate Declined", or "Duplicate".'
    ],
  ];
@endphp

<div class="ips-pipeline-wrapper">

  {{-- ══════════════════════════════════════════════════════════════
       TIER 1: PURE WHITE TOOLBAR HEADER CARD (Rule 20)
       Header Toolbar with dynamic Add Substage action
       ══════════════════════════════════════════════════════════════ --}}
  <div class="ips-toolbar-card">
    <div class="ips-toolbar-left">
      <div class="ips-toolbar-icon">
        <i class="fas fa-layer-group"></i>
      </div>
      <div class="ips-toolbar-info">
        <div class="ips-toolbar-title-row">
          <h2 class="ips-toolbar-title">Interview Pipeline Builder</h2>
        </div>
        <div class="ips-toolbar-sub">
          <span>Candidate lifecycle stages & configurable assessment sub-stages</span>
        </div>
      </div>
    </div>

    <div class="ips-toolbar-right">
      @if(in_array("add_settings", $userPermissions) || in_array("edit_settings", $userPermissions))
        <button type="button" class="btn ips-btn-reset" id="ipsResetPipelineBtn" title="Reset interview pipeline to system defaults">
          <i class="fas fa-undo-alt"></i> <span>Reset</span>
        </button>
        <button type="button" class="btn btn-primary ips-btn-primary interview_stage" id="ipsHeaderAddBtn" data-id="1" data-title="Sourcing" data-root="root_sourcing" data-class="bg-secondary" data-toggle="modal" data-target="#add_interview_sub_stage">
          <i class="fas fa-plus"></i> <span id="ipsHeaderAddBtnText">Add Substage</span>
        </button>
      @endif
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════════
       TIER 2: FILTER & STACK-JUMP SUB-TABS CARD (Rule 20)
       ══════════════════════════════════════════════════════════════ -->
  <div class="ips-tabs-card" id="ipsFilterTabs">
    <button type="button" class="ips-tab-btn active" data-filter="all" onclick="ipsFilterStage('all', this)">
      <i class="fas fa-th-list"></i> All Pipeline <span class="ips-tab-badge" id="ipsTabCountAll">{{ $totalSubStages }}</span>
    </button>
    @foreach($stagesConfig as $cfg)
      <button type="button" class="ips-tab-btn" data-filter="{{ $cfg['key'] }}" data-stage-id="{{ $cfg['id'] }}" data-stage-title="{{ $cfg['title'] }}" data-stage-root="{{ $cfg['root'] }}" data-stage-class="{{ $cfg['class'] }}" onclick="ipsFilterStage('{{ $cfg['key'] }}', this)">
        {{ $cfg['step'] }} {{ $cfg['title'] }} <span class="ips-tab-badge" id="ipsTabCount_{{ $cfg['key'] }}">{{ $cfg['count'] }}</span>
      </button>
    @endforeach
  </div>

  <!-- ══════════════════════════════════════════════════════════════
       TIER 3: STACK-BASED PIPELINE STREAM (Rule 20)
       ══════════════════════════════════════════════════════════════ -->
  <div id="kt_docs_jkanban_basic1" class="ips-stack-container">
    <div class="ips-stack-spine"></div>

    @foreach($stagesConfig as $cfg)
      <div class="ips-stack-card" id="ipsStageCard_{{ $cfg['key'] }}" data-stage="{{ $cfg['key'] }}" data-stage-id="{{ $cfg['id'] }}" data-stage-title="{{ $cfg['title'] }}" data-stage-root="{{ $cfg['root'] }}" data-stage-class="{{ $cfg['class'] }}" style="--stage-color: {{ $cfg['color'] }}; --stage-light: {{ $cfg['light'] }}; --stage-border: {{ $cfg['border'] }};">
        
        <!-- Master Stage Header Row with dedicated light background color of that stage -->
        <div class="ips-stack-header" onclick="ipsToggleStage('ipsStageCard_{{ $cfg['key'] }}', event)">
          <div class="ips-stack-header-left">
            <div class="ips-stack-step-pill">{{ $cfg['step'] }}</div>
            <i class="{{ $cfg['icon'] }} ips-stack-icon"></i>
            <div class="ips-stack-title-box">
              <div class="ips-stack-title-row">
                <span class="ips-stack-title">{{ $cfg['title'] }}</span>
                <span class="ips-stack-tag">{{ $cfg['tag'] }}</span>
              </div>
              <div class="ips-stack-desc">{{ $cfg['desc'] }}</div>
            </div>
          </div>

          <div class="ips-stack-header-right">
            <span class="ips-count-pill {{ $cfg['count'] > 0 ? 'has-items' : '' }}" id="ipsCountPill_{{ $cfg['key'] }}">
              <i class="fas fa-layer-group"></i> <span class="count-num">{{ $cfg['count'] }}</span> Sub-Stage{{ $cfg['count'] == 1 ? '' : 's' }}
            </span>

            {{-- + Add button removed from every stage card per user request --}}

            <button type="button" class="ips-collapse-toggle" title="Toggle Stage View"><i class="fas fa-chevron-down"></i></button>
          </div>
        </div>

        <!-- Master Stage Body: Discrete Sub-Cards Stream with Drag & Drop Moving -->
        <div class="ips-stack-body">
          <ul class="ips-subcards-stream kanban-drag" id="{{ $cfg['root'] }}" data-parent-id="{{ $cfg['id'] }}" data-stage-key="{{ $cfg['key'] }}">
            @if(!empty($cfg['list']) && count($cfg['list']) > 0)
              @foreach($cfg['list'] as $val)
                @php
                  $isProtected = (@$val->delete_status == 0);
                @endphp
                <li id="{{ $val->id }}" class="ips-subcard-item kanban-item root_child {{ $cfg['class'] }}" draggable="true" data-name="{{ $val->name }}" data-id="{{ $val->id }}">
                  <div class="ips-subcard-left">
                    <span class="ips-drag-handle" title="Drag to move or reorder sub-stage"><i class="fas fa-grip-vertical"></i></span>
                    <span class="ips-subcard-seq">Stage {{ $loop->iteration }}</span>
                    <span class="ips-subcard-name">{{ $val->name }}</span>
                    @if($isProtected)
                      <span class="ips-subcard-tag system-tag"><i class="fas fa-shield-alt me-1"></i> System Protected</span>
                    @else
                      <span class="ips-subcard-tag">Custom Sub-Stage</span>
                    @endif
                  </div>

                  <div class="ips-subcard-right">
                    <span class="ips-subcard-badge"><i class="fas fa-check me-1"></i> Active</span>
                    @if(!$isProtected && in_array("add_settings", $userPermissions))
                      <a class="stage_block ips-btn-del-subcard delete_process" href="javascript:void(0);" rel="{{ $val->id }}" title="Delete Sub-Stage" onclick="event.stopPropagation();">
                        <i class="far fa-trash-alt text-danger"></i>
                      </a>
                    @endif
                  </div>
                </li>
              @endforeach
            @endif
          </ul>

          <!-- Empty State when Stage has 0 Sub-Stages (Add button removed per spec) -->
          <div class="ips-subcards-empty" id="ipsEmpty_{{ $cfg['key'] }}" style="{{ $cfg['count'] > 0 ? 'display:none;' : '' }}">
            <div class="ips-empty-left">
              <div class="ips-empty-icon"><i class="fas fa-inbox"></i></div>
              <div>
                <div class="ips-empty-title">No custom sub-stages configured for {{ $cfg['title'] }}</div>
                <div class="ips-empty-sub">{{ $cfg['empty_text'] }}</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    @endforeach

  </div>

</div>

{{-- Scoped Styles for Stack-Based Card + Sub-Card Enterprise Layout --}}
<style>
  .ips-pipeline-wrapper {
    width: 100%;
    position: relative;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  /* TIER 1: TOOLBAR HEADER CARD */
  .ips-toolbar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    flex-wrap: wrap;
  }
  .ips-toolbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
  }
  .ips-toolbar-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #eff6ff;
    color: #009ef7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    border: 1px solid #bfdbfe;
  }
  .ips-toolbar-info {
    min-width: 0;
  }
  .ips-toolbar-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .ips-toolbar-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.2px;
    margin: 0;
  }
  .ips-toolbar-sub {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .ips-toolbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .ips-btn-reset {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #ffffff !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    padding: 8.5px 16px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }
  .ips-btn-reset:hover {
    color: #e11d48 !important;
    background: #fff1f2 !important;
    border-color: #fecdd3 !important;
    box-shadow: 0 2px 4px rgba(225, 29, 72, 0.12);
    transform: translateY(-1px);
  }
  .ips-btn-reset:active {
    transform: translateY(0);
  }
  .ips-btn-reset:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
  }
  .ips-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #009ef7 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 9px 18px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25);
  }
  .ips-btn-primary:hover {
    background: #0086d4 !important;
    box-shadow: 0 4px 8px rgba(0, 158, 247, 0.35);
    transform: translateY(-1px);
  }

  /* TIER 2: FILTER & JUMP-TO SUB-TABS CARD */
  .ips-tabs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 6px 10px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    overflow-x: auto;
    scrollbar-width: none;
  }
  .ips-tabs-card::-webkit-scrollbar {
    display: none;
  }
  .ips-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 13px;
    border-radius: 7px;
    border: 1px solid transparent;
    background: transparent;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    white-space: nowrap;
  }
  .ips-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }
  .ips-tab-btn.active {
    background: #009ef7;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(0, 158, 247, 0.2);
  }
  .ips-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
    background: #e2e8f0;
    color: #475569;
  }
  .ips-tab-btn.active .ips-tab-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }

  /* TIER 3: STACK-BASED PIPELINE STREAM */
  .ips-stack-container {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin: 0;
    padding: 0;
  }
  .ips-stack-spine {
    position: absolute;
    top: 35px;
    bottom: 60px;
    left: 35px;
    width: 2px;
    background: linear-gradient(180deg, #318ebb 0%, #a855f7 20%, #009ef7 40%, #ec4899 60%, #f59e0b 80%, #10b981 100%);
    z-index: 1;
    opacity: 0.35;
  }
  .ips-stack-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    position: relative;
    z-index: 2;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .ips-stack-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
  }
  .ips-stack-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
    background: var(--stage-color, #009ef7);
  }

  /* Stage Header Row with dedicated light background color of that stage */
  .ips-stack-header {
    padding: 14px 20px 14px 18px;
    background: var(--stage-light, #f8fafc);
    border-bottom: 1px solid var(--stage-border, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    cursor: pointer;
    user-select: none;
    transition: all 0.18s ease;
  }
  .ips-stack-header:hover {
    filter: brightness(0.985);
  }
  .ips-stack-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
  }
  .ips-stack-step-pill {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #ffffff;
    color: var(--stage-color, #009ef7);
    border: 1px solid var(--stage-border, #bfdbfe);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  }
  .ips-stack-icon {
    font-size: 16px;
    color: var(--stage-color, #009ef7);
    width: 22px;
    text-align: center;
  }
  .ips-stack-title-box {
    min-width: 0;
  }
  .ips-stack-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .ips-stack-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
  }
  .ips-stack-tag {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1px 7px;
    border-radius: 4px;
    background: #ffffff;
    color: var(--stage-color, #475569);
    border: 1px solid var(--stage-border, #e2e8f0);
  }
  .ips-stack-desc {
    font-size: 11.5px;
    color: #475569;
    margin-top: 1px;
  }

  .ips-stack-header-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .ips-count-pill {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--stage-color, #009ef7);
    background: #ffffff;
    border: 1px solid var(--stage-border, #bfdbfe);
    padding: 3px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .ips-collapse-toggle {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .ips-collapse-toggle:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #0f172a;
  }
  .ips-collapse-toggle i {
    transition: transform 0.2s ease;
  }
  .ips-stack-card.collapsed .ips-collapse-toggle i {
    transform: rotate(-90deg);
  }

  /* Stack Body & Sub-Cards Stream */
  .ips-stack-body {
    padding: 12px 20px 16px 20px;
    background: #fafbfc;
    border-top: 1px solid transparent;
    display: block;
  }
  .ips-stack-card.collapsed .ips-stack-body {
    display: none;
  }

  .ips-subcards-stream {
    display: flex;
    flex-direction: column;
    gap: 8px;
    list-style: none;
    margin: 0;
    padding: 0;
    min-height: 20px;
  }
  .ips-subcards-stream.drag-over {
    background: rgba(0, 158, 247, 0.04);
    border: 1.5px dashed #009ef7;
    border-radius: 8px;
  }

  /* Sub-Card Item with Drag & Drop Visuals */
  .ips-subcard-item {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    margin-bottom: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
    transition: all 0.15s ease-in-out;
    cursor: default;
  }
  .ips-subcard-item:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04) !important;
    background: #ffffff !important;
  }
  .ips-subcard-item.is-dragging {
    opacity: 0.45 !important;
    border: 1.5px dashed #009ef7 !important;
    background: #eff6ff !important;
  }
  .ips-subcard-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }
  .ips-drag-handle {
    color: #94a3b8;
    cursor: grab;
    cursor: -webkit-grab;
    font-size: 13px;
    padding: 2px 5px;
    border-radius: 4px;
    transition: color 0.15s ease, background 0.15s ease;
  }
  .ips-drag-handle:hover {
    color: #009ef7;
    background: #eff6ff;
  }
  .ips-drag-handle:active {
    cursor: grabbing;
    cursor: -webkit-grabbing;
  }
  .ips-subcard-seq {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 1.5px 7px;
    border-radius: 5px;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }
  .ips-subcard-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
  }
  .ips-subcard-tag {
    font-size: 10.5px;
    font-weight: 500;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    padding: 1px 7px;
    border-radius: 4px;
  }
  .ips-subcard-tag.system-tag {
    color: #0284c7;
    background: #f0f9ff;
    border-color: #e0f2fe;
  }

  .ips-subcard-right {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .ips-subcard-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 4px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
  }
  .ips-btn-del-subcard {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid transparent;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
  }
  .ips-btn-del-subcard:hover {
    background: #fef2f2;
    color: #ef4444;
    border-color: #fecaca;
  }

  /* Empty State */
  .ips-subcards-empty {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
  }
  .ips-empty-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .ips-empty-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }
  .ips-empty-title {
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
  }
  .ips-empty-sub {
    font-size: 11px;
    color: #94a3b8;
  }

  @media (max-width: 992px) {
    .ips-toolbar-card {
      flex-direction: column;
      align-items: stretch;
    }
    .ips-toolbar-right {
      justify-content: flex-end;
    }
    .ips-stack-spine {
      display: none;
    }
  }
</style>
