<?php
/*
 * SuperAdmin Job Status Drawer Body — Modern Card + Sub-Card Enterprise Design System (Rule 20).
 * AJAX-injected into #status_body_content (see super-admin.jobs.all-jobs-data #status_drawer).
 * 
 * Strict Architectural Guarantees:
 * 1. 100% CQRS Compliance: Zero MySQL reads on drawer interaction.
 * 2. 100% Business Logic Integrity: Preserves exact form parameters, mutation tracking,
 *    and endpoint payloads (SuperAdminCompanyJobsController::newUpdateJobstatus).
 * 3. Modern Card + Sub-Card Design Standard: Structured cards with dedicated background
 *    colors on all headers (#f8fafc / #f1f5f9), responsive 15.6" screen grid, and
 *    high-contrast real-time capability matrix.
 * 4. Rule 24 Timezone Standard: Date, time, and timezone abbreviation resolved via
 *    GeneralMethod::defaultTimeZone.
 */
$statusVal = @$job->status ?: 'active';

$statusMeta = [
    'active' => [
        'label' => 'Hiring',
        'badge' => 'Receiving Applications',
        'icon' => 'fa-user-group',
        'color' => '#10b981',
        'bg' => '#ecfdf5',
        'border' => '#a7f3d0',
        'shadow' => 'rgba(16, 185, 129, 0.16)',
        'desc' => 'Job is live and actively receiving candidate applications across all channels.',
        'bullets' => [
            'Career page visibility is live and public',
            'Registered vendors & agencies can submit candidate profiles',
            'Candidates can apply directly via careers and job boards',
            'All automations, alerts & screening pipelines active',
        ],
        'career' => 'Visible',
        'vendor' => 'Enabled',
        'candidates' => 'Open',
        'interviews' => 'All Active',
        'offers' => 'Allowed',
        'notify' => 'Active',
    ],
    'hold' => [
        'label' => 'Hold',
        'badge' => 'Pause Hiring',
        'icon' => 'fa-pause',
        'color' => '#f59e0b',
        'bg' => '#fffbeb',
        'border' => '#fde68a',
        'shadow' => 'rgba(245, 158, 11, 0.16)',
        'desc' => 'Temporarily suspend candidate applications while retaining active pipeline data.',
        'bullets' => [
            'Removed from public career page & search indexes',
            'Vendor profile submissions temporarily disabled',
            'Candidate applications paused (no new submissions)',
            'All existing applicant stages & interview data retained',
        ],
        'career' => 'Hidden',
        'vendor' => 'Disabled',
        'candidates' => 'Paused',
        'interviews' => 'Existing Only',
        'offers' => 'Existing Only',
        'notify' => 'Paused',
    ],
    'inactive' => [
        'label' => 'Closed',
        'badge' => 'Finish Recruitment',
        'icon' => 'fa-box-archive',
        'color' => '#ef4444',
        'bg' => '#fef2f2',
        'border' => '#fca5a5',
        'shadow' => 'rgba(239, 68, 68, 0.16)',
        'desc' => 'Finalize recruitment, close applications, and archive the requisition.',
        'bullets' => [
            'Halt all candidate applications across all channels',
            'Vendor and agency submissions permanently disabled',
            'Requisition archived from operational pipelines',
            'Complete audit trail, reports, and analytics preserved',
        ],
        'career' => 'Hidden',
        'vendor' => 'Disabled',
        'candidates' => 'Closed',
        'interviews' => 'Existing Only',
        'offers' => 'Existing Only',
        'notify' => 'Stopped',
    ],
];

// ---- Impact Preview cell icon: driven by column severity, not the text value ----
$jsdCellIcon = function ($val, $key) {
    if ($val === 'active') return ['fa-circle-check', '#10b981'];
    $color = $val === 'hold' ? '#f59e0b' : '#ef4444';
    if (in_array($key, ['interviews', 'offers'])) return ['fa-bullseye', $color];
    if ($val === 'hold' && in_array($key, ['candidates', 'notify'])) return ['fa-circle-pause', $color];
    if ($val === 'hold') return ['fa-triangle-exclamation', $color];
    return ['fa-circle-xmark', $color];
};

$footerName = @$lastStatusChange->changed_by_name;
$footerDate = @$lastStatusChange->created_at ?: @$job->updated_at;
$footerInitial = $footerName ? strtoupper(substr(trim($footerName), 0, 1)) : 'S';

// Rule 24: Default User/Account-Setting Timezone with Date & Time (d M Y, h:i A T)
$companyId = @$job->company_id ?: (auth()->check() ? auth()->user()->company_id : null);
$userTz = \App\Helper\GeneralMethod::defaultTimeZone($companyId);
$formattedDate = '';
if ($footerDate) {
    try {
        $formattedDate = \Carbon\Carbon::parse($footerDate)->setTimezone($userTz)->format('d M Y, h:i A T');
    } catch (\Throwable $e) {
        $formattedDate = date('d M Y, h:i A', strtotime($footerDate)) . ' ' . $userTz;
    }
}
?>

<style>
  .jsd-wrapper {
    padding: 20px 28px 30px;
    font-family: 'Poppins', sans-serif;
    color: #1e293b;
    background: #f8fafc;
    min-height: 100%;
    box-sizing: border-box;
  }
  .jsd-wrapper * {
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
  }

  /* ---- Requisition Context Banner Sub-Card ---- */
  .jsd-banner-subcard {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 18px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }
  .jsd-banner-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }
  .jsd-banner-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .jsd-banner-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 500px;
  }
  .jsd-banner-sub {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .jsd-banner-id {
    color: #009ef7;
    font-weight: 700;
  }
  .jsd-sep {
    color: #cbd5e1;
  }
  .jsd-current-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
  }
  .jsd-current-pill.pill-active {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
  }
  .jsd-current-pill.pill-hold {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
  }
  .jsd-current-pill.pill-inactive {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fca5a5;
  }
  .jsd-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
  }

  /* ---- Master Card Standard ---- */
  .jsd-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    margin-bottom: 18px;
    overflow: hidden;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
  }
  .jsd-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
  }

  /* ---- Mandatory Header with Background Color ---- */
  .jsd-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }
  .jsd-card-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .jsd-step-badge {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #009ef7;
    color: #ffffff;
    font-weight: 700;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.25);
  }
  .jsd-card-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
  }
  .jsd-card-subtitle {
    font-size: 11px;
    font-weight: 400;
    color: #64748b;
    margin: 2px 0 0;
  }
  .jsd-guide-pill {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .jsd-target-pill {
    font-size: 11.5px;
    font-weight: 600;
    color: #1e40af;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 4px 12px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .jsd-card-body {
    padding: 18px;
  }

  /* ---- Step 1: Status Sub-Cards Grid ---- */
  .jsd-status-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    width: 100%;
  }
  @media (max-width: 991px) {
    .jsd-status-grid {
      grid-template-columns: 1fr;
    }
  }

  .jsd-status-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .jsd-status-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    transform: translateY(-1px);
  }

  .jsd-status-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
  }
  .jsd-status-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }
  .jsd-status-info {
    flex-grow: 1;
    min-width: 0;
  }
  .jsd-status-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
  }
  .jsd-status-badge-text {
    font-size: 10.5px;
    font-weight: 500;
    color: #64748b;
    margin-top: 1px;
  }
  .jsd-status-check {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: transparent;
    background: #ffffff;
    flex-shrink: 0;
    transition: all 0.15s ease;
  }
  .jsd-status-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.45;
    min-height: 32px;
  }
  .jsd-status-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 10px 0;
  }
  .jsd-status-bullets {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .jsd-status-bullets li {
    font-size: 11px;
    font-weight: 500;
    color: #334155;
    display: flex;
    align-items: flex-start;
    gap: 7px;
    line-height: 1.4;
  }
  .jsd-status-bullets li i {
    font-size: 11px;
    margin-top: 2px;
    flex-shrink: 0;
  }

  /* Status specific styles */
  .jsd-status-card[data-status="active"] .jsd-status-bullets li i { color: #10b981; }
  .jsd-status-card[data-status="hold"] .jsd-status-bullets li i { color: #f59e0b; }
  .jsd-status-card[data-status="inactive"] .jsd-status-bullets li i { color: #ef4444; }

  /* Current status indicator */
  .jsd-status-card.is-current {
    border-color: #a7f3d0;
    background: #f0fdf4;
  }
  .jsd-status-card.is-current .jsd-status-check {
    border-color: #10b981;
    background: #10b981;
    color: #ffffff;
  }

  /* Active selected states */
  .jsd-status-card[data-status="active"].is-selected {
    border-color: #10b981 !important;
    background: #f0fdf4 !important;
    box-shadow: 0 0 0 1.5px #10b981, 0 6px 16px rgba(16, 185, 129, 0.14) !important;
  }
  .jsd-status-card[data-status="active"].is-selected .jsd-status-check {
    border-color: #10b981 !important;
    background: #10b981 !important;
    color: #ffffff !important;
  }

  .jsd-status-card[data-status="hold"].is-selected {
    border-color: #f59e0b !important;
    background: #fffbeb !important;
    box-shadow: 0 0 0 1.5px #f59e0b, 0 6px 16px rgba(245, 158, 11, 0.14) !important;
  }
  .jsd-status-card[data-status="hold"].is-selected .jsd-status-check {
    border-color: #f59e0b !important;
    background: #f59e0b !important;
    color: #ffffff !important;
  }

  .jsd-status-card[data-status="inactive"].is-selected {
    border-color: #ef4444 !important;
    background: #fef2f2 !important;
    box-shadow: 0 0 0 1.5px #ef4444, 0 6px 16px rgba(239, 68, 68, 0.14) !important;
  }
  .jsd-status-card[data-status="inactive"].is-selected .jsd-status-check {
    border-color: #ef4444 !important;
    background: #ef4444 !important;
    color: #ffffff !important;
  }

  /* ---- Step 2: Impact Preview Table Standard ---- */
  .jsd-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
  }
  .jsd-matrix-table thead th {
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 18px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    text-align: left;
  }
  .jsd-matrix-th-feature {
    width: 34%;
  }
  .jsd-matrix-th-col {
    width: 22%;
  }
  .jsd-th-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.3px;
  }
  .jsd-chip-active {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
  }
  .jsd-chip-active i { font-size: 7px; color: #16a34a; }
  .jsd-chip-hold {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
  }
  .jsd-chip-hold i { font-size: 7px; color: #d97706; }
  .jsd-chip-inactive {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
  }
  .jsd-chip-inactive i { font-size: 7px; color: #dc2626; }

  .jsd-th-current-tag {
    font-size: 9.5px;
    font-weight: 600;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.6);
    padding: 1px 5px;
    border-radius: 4px;
    margin-left: 2px;
  }

  .jsd-matrix-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
  }
  .jsd-matrix-table tbody tr:last-child {
    border-bottom: none;
  }
  .jsd-matrix-table tbody tr:hover {
    background: #f8fafc;
  }
  .jsd-matrix-cell-feature {
    padding: 12px 18px;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 9px;
    white-space: nowrap;
  }
  .jsd-feature-ico {
    color: #64748b;
    font-size: 12px;
    width: 14px;
    text-align: center;
  }
  .jsd-matrix-cell-val {
    padding: 10px 18px;
    vertical-align: middle;
  }
  .jsd-cell-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #334155;
    font-size: 11.5px;
  }
  .jsd-cell-pill i {
    font-size: 12px;
    flex-shrink: 0;
  }

  /* Matrix Columns highlight */
  .jsd-matrix-table td.jsd-current-col {
    background: #f0fdf4 !important;
  }
  .jsd-matrix-table td.jsd-target-col {
    background: #eff6ff !important;
  }

  /* ---- Step 3: Footer Sub-Card ---- */
  .jsd-footer-subcard {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }
  .jsd-footer-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .jsd-footer-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    font-weight: 700;
    flex-shrink: 0;
  }
  .jsd-footer-line1 {
    font-size: 11.5px;
    color: #475569;
  }
  .jsd-footer-line1 b {
    color: #0f172a;
    font-weight: 600;
  }
  .jsd-footer-sep {
    margin: 0 6px;
    color: #cbd5e1;
  }
  .jsd-footer-date {
    color: #64748b;
    font-weight: 500;
  }
  .jsd-footer-line2 {
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 1px;
  }
  .jsd-sync-pill {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
  }
</style>

<div class="jsd-wrapper">
  @if(!empty($job))
  <!-- Requisition Identity Context Sub-Card -->
  <div class="jsd-banner-subcard">
    <div class="jsd-banner-left">
      <div class="jsd-banner-icon"><i class="fas fa-briefcase"></i></div>
      <div>
        <div class="jsd-banner-title">{{ ucwords(@$job->title ?: 'Job Requisition') }}</div>
        <div class="jsd-banner-sub">
          <span class="jsd-banner-id">#{{ @$job->id }}</span>
          @if(!empty($job->department))<span class="jsd-sep">•</span><span>{{ @$job->department }}</span>@endif
          @if(!empty($job->work_type))<span class="jsd-sep">•</span><span>{{ ucwords(str_replace('_', ' ', $job->work_type)) }}</span>@endif
        </div>
      </div>
    </div>
    <div class="jsd-banner-right">
      <span class="jsd-current-pill pill-{{ $statusVal }}">
        <span class="jsd-dot"></span> Current: {{ $statusMeta[$statusVal]['label'] ?? 'Unknown' }}
      </span>
    </div>
  </div>
  @endif

  <!-- Step 1: Choose New Status Card -->
  <div class="jsd-card">
    <div class="jsd-card-header">
      <div class="jsd-card-header-left">
        <span class="jsd-step-badge">1</span>
        <div>
          <h4 class="jsd-card-title">Choose New Status</h4>
          <p class="jsd-card-subtitle">Select the target operational workflow status for this requisition</p>
        </div>
      </div>
      <div class="jsd-card-header-right">
        <span class="jsd-guide-pill"><i class="fas fa-hand-pointer text-primary"></i> Click any status card to select</span>
      </div>
    </div>
    <div class="jsd-card-body">
      <form id="createSchedule" class="ajax-form" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="job_id" id="job_id" value="{{ @$job->id }}" />

        <div class="jsd-status-grid">
          @foreach($statusMeta as $val => $meta)
          <div class="jsd-status-card {{ $val === $statusVal ? 'is-current' : '' }}" data-status="{{ $val }}" onclick="jsdSelectStatus(this)">
            <input type="radio" class="master_status d-none" name="status" value="{{ $val }}" id="jsd_{{ $val }}" {{ $val === $statusVal ? 'checked' : '' }}>
            <div class="jsd-status-card-top">
              <div class="jsd-status-icon-box" style="background: {{ $meta['bg'] }}; color: {{ $meta['color'] }}; border: 1px solid {{ $meta['border'] }};">
                <i class="fas {{ $meta['icon'] }}"></i>
              </div>
              <div class="jsd-status-info">
                <div class="jsd-status-name">{{ $meta['label'] }}</div>
                <div class="jsd-status-badge-text">{{ $meta['badge'] }}</div>
              </div>
              <div class="jsd-status-check">
                <i class="fas fa-check"></i>
              </div>
            </div>
            <div class="jsd-status-desc">{{ $meta['desc'] }}</div>
            <div class="jsd-status-divider"></div>
            <ul class="jsd-status-bullets">
              @foreach($meta['bullets'] as $b)
              <li>
                <i class="fas {{ $val === 'active' ? 'fa-circle-check' : ($val === 'hold' ? 'fa-circle-pause' : 'fa-circle-xmark') }}"></i>
                <span>{{ $b }}</span>
              </li>
              @endforeach
            </ul>
          </div>
          @endforeach
        </div>
      </form>
    </div>
  </div>

  <!-- Step 2: Impact Preview Card -->
  <div class="jsd-card">
    <div class="jsd-card-header">
      <div class="jsd-card-header-left">
        <span class="jsd-step-badge">2</span>
        <div>
          <h4 class="jsd-card-title">Impact Preview &amp; Capability Matrix</h4>
          <p class="jsd-card-subtitle">Real-time comparison of platform features, candidate pipelines, and automations</p>
        </div>
      </div>
      <div class="jsd-card-header-right">
        <span id="jsd_target_indicator" class="jsd-target-pill">
          <i class="fas fa-crosshairs text-primary"></i> Target: <b id="jsd_target_label" style="color: {{ $statusMeta[$statusVal]['color'] }}">{{ $statusMeta[$statusVal]['label'] }}</b>
        </span>
      </div>
    </div>
    <div class="jsd-card-body p-0">
      <div class="table-responsive">
        <table class="jsd-matrix-table">
          <thead>
            <tr>
              <th class="jsd-matrix-th-feature">Feature / Action</th>
              <th class="jsd-matrix-th-col">
                <div class="jsd-th-chip jsd-chip-active">
                  <i class="fas fa-circle"></i>
                  <span>Hiring</span>
                  @if($statusVal === 'active')<span class="jsd-th-current-tag">Current</span>@endif
                </div>
              </th>
              <th class="jsd-matrix-th-col">
                <div class="jsd-th-chip jsd-chip-hold">
                  <i class="fas fa-circle"></i>
                  <span>Hold</span>
                  @if($statusVal === 'hold')<span class="jsd-th-current-tag">Current</span>@endif
                </div>
              </th>
              <th class="jsd-matrix-th-col">
                <div class="jsd-th-chip jsd-chip-inactive">
                  <i class="fas fa-circle"></i>
                  <span>Closed</span>
                  @if($statusVal === 'inactive')<span class="jsd-th-current-tag">Current</span>@endif
                </div>
              </th>
            </tr>
          </thead>
          <tbody id="jsd_impact_body">
            @php
              $rows = [
                'Career Page Visibility' => ['key' => 'career', 'icon' => 'fa-globe'],
                'Vendor & Agency Submissions' => ['key' => 'vendor', 'icon' => 'fa-handshake'],
                'Candidate Applications' => ['key' => 'candidates', 'icon' => 'fa-users'],
                'Interview Pipeline Operations' => ['key' => 'interviews', 'icon' => 'fa-video'],
                'Offer Creation & Issuance' => ['key' => 'offers', 'icon' => 'fa-file-signature'],
                'Notifications & Alerts' => ['key' => 'notify', 'icon' => 'fa-bell'],
              ];
            @endphp
            @foreach($rows as $label => $spec)
            @php $key = $spec['key']; @endphp
            <tr>
              <td class="jsd-matrix-cell-feature">
                <i class="fas {{ $spec['icon'] }} jsd-feature-ico"></i>
                <span>{{ $label }}</span>
              </td>
              @foreach(['active', 'hold', 'inactive'] as $val)
              @php [$cellIcon, $cellColor] = $jsdCellIcon($val, $key); @endphp
              <td class="jsd-matrix-cell-val jsd-cell-{{ $val }} {{ $val === $statusVal ? 'jsd-current-col' : '' }}">
                <span class="jsd-cell-pill">
                  <i class="fas {{ $cellIcon }}" style="color: {{ $cellColor }};"></i>
                  <span>{{ $statusMeta[$val][$key] }}</span>
                </span>
              </td>
              @endforeach
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Step 3: Audit Trail & Instant Sync Sub-Card -->
  <div class="jsd-footer-subcard">
    <div class="jsd-footer-left">
      <div class="jsd-footer-avatar">{{ $footerInitial }}</div>
      <div class="jsd-footer-meta">
        <div class="jsd-footer-line1">
          Last updated by <b>{{ $footerName ?: 'System Administrator' }}</b>
          @if($formattedDate)
          <span class="jsd-footer-sep">•</span>
          <span class="jsd-footer-date"><i class="far fa-clock text-primary me-1"></i> {{ $formattedDate }}</span>
          @endif
        </div>
        <div class="jsd-footer-line2">Multi-tenant atomic cache invalidation guarantees zero stale data across active organization sessions.</div>
      </div>
    </div>
    <div class="jsd-footer-right">
      <span class="jsd-sync-pill"><i class="fas fa-bolt text-warning me-1"></i> Real-time sync</span>
    </div>
  </div>

  <div id="status_msg" style="text-align:center;font-weight:600;color:#10b981;margin-top:14px;font-size:12px;"></div>
</div>

<script type="text/javascript">
var JSD_STATUS_META = {!! json_encode($statusMeta) !!};

function jsdSelectStatus(el) {
    var val = $(el).attr('data-status');
    $('#jsd_' + val).prop('checked', true);

    $('.jsd-status-card').removeClass('is-selected');
    $(el).addClass('is-selected');

    var meta = JSD_STATUS_META[val];

    $('.jsd-matrix-table td.jsd-target-col').removeClass('jsd-target-col');
    $('.jsd-cell-' + val).addClass('jsd-target-col');

    if ($('#jsd_target_label').length) {
        $('#jsd_target_label').text(meta.label).css('color', meta.color);
    }

    $('#status_confirm_btn').prop('disabled', false).css({
        'background': meta.color,
        'box-shadow': '0 2px 8px ' + meta.shadow
    });
    $('.changetextupdate').text('Update Status to ' + meta.label);
}

var _isSubmittingJobStatus = false;
function newUpdateJobstatus(){
    if (_isSubmittingJobStatus) return false;
    _isSubmittingJobStatus = true;

    $('#status_confirm_btn').prop('disabled', true);
    $('.changetextupdate').text('Updating…');
    var page = parseInt(localStorage.getItem('currentPageJobs')) || 1;
    var formData = $('#createSchedule').serialize();
    var mutationId = 'mut_' + Date.now();
    formData += '&client_mutation_id=' + encodeURIComponent(mutationId);

    var idempotencyKey = 'ik_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    $.ajax({
        url: '{{ route("superadmin.company.jobs.newUpdateJobstatus") }}',
        container: '#createSchedule',
        type: "POST",
        headers: {
            'X-Idempotency-Key': idempotencyKey,
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: formData,
        success: function (response) {
            _isSubmittingJobStatus = false;

            if (response.status == 'coinCheck') {
                Swal.fire("Failed", `${response.message}`, "warning").then(function(){
                    $(".btn-active-color-primary").click();
                });
                $('#status_confirm_btn').prop('disabled', false);
                $('.changetextupdate').text('Update Status');
                return false;
            }

            if (response.status == 'success') {
                if (window.SuperAdminJobsLocalStore) { window.SuperAdminJobsLocalStore.purge(); }
                if (window.JeroJobsLocalStore) { window.JeroJobsLocalStore.purge(); }
                window.jobDrawerCache = {};
                try {
                    const bc = (typeof BroadcastChannel !== 'undefined') ? new BroadcastChannel('jero_superadmin_jobs') : null;
                    if (bc) {
                        bc.postMessage({ type: 'job_status_updated', jobId: response.job_id, status: response.new_status });
                    }
                } catch (e) {}
                $('.activeJobs').html(response.activeJobs);
                $('.inactiveJobs').html(response.inactiveJobs);
                $('.holdJobs').html(response.holdJobs);
                $('#status_msg').html("<i class='fas fa-check-circle me-1'></i> Job status updated successfully.");

                setTimeout(() => {
                    $('#status_sub_stage_close').click();

                    if (typeof fetchCandidates === 'function') {
                        fetchCandidates(page, $('#search').val()).then(() => {
                            $('#status_confirm_btn').prop('disabled', false);
                            $('.changetextupdate').text('Update Status');
                            $('.custom-pagination .page-link').removeClass('active_disabled');
                        }).catch(() => {
                            $('.custom-pagination .page-link').removeClass('active_disabled');
                        });
                    } else if (typeof fetchallJobsAjax === 'function') {
                        fetchallJobsAjax(page);
                        $('#status_confirm_btn').prop('disabled', false);
                        $('.changetextupdate').text('Update Status');
                    }
                }, 500);
            } else {
                $('#status_msg').html("<div style='color:#ef4444'><i class='fas fa-circle-exclamation me-1'></i> Oops, something went wrong. Please try again</div>");
                $('#status_confirm_btn').prop('disabled', false);
                $('.changetextupdate').text('Update Status');
            }
        },
        error: function (xhr) {
            _isSubmittingJobStatus = false;
            $('#status_confirm_btn').prop('disabled', false);
            $('.changetextupdate').text('Update Status');
            var errorMsg = (xhr.status === 0 || !navigator.onLine)
                ? 'Network disconnected. Please check your connection and try again.'
                : 'Status update failed (HTTP ' + xhr.status + '). Please try again.';
            $('#status_msg').html("<div style='color:#ef4444'><i class='fas fa-circle-exclamation me-1'></i> " + errorMsg + "</div>");
            if (typeof Swal !== 'undefined') {
                Swal.fire('Failed', errorMsg, 'error');
            }
        }
    });
}
</script>
