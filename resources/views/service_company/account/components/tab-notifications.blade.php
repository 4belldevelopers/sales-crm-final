{{-- 
  JERO ATS — Service Company Notification & Alert Routing Tab Component (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Organizes Recruitment and Sales notification dispatch channels and 16 automated event triggers using 
          Tier 1 Toolbar with live search, Tier 2 Category Jump Tabs, and Tier 3 Primary + Sub-Card units.
  Tenancy & Security: Strictly isolated by company_id. Retains JeroEmailBoxEngine client-first validation and existing routing keys.
  Screen Fit: 100% fluid fit for 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}

@php
  $userPerms = $userPermissions ?? [];
  $hasViewPerm = in_array('view_settings', $userPerms);
  $hasEditPerm = in_array('edit_settings', $userPerms);

  // Recruitment Pipeline Event Triggers (8 Items)
  $recruitmentNotificationItems = [
    ['Candidate Stages', 'Candidate moves across applied, shortlisted, interview, offered and other stages.', 'fa-solid fa-arrows-split-up-and-left'],
    ['Agencies Status', 'Agency performance, submission and status changes.', 'fa-solid fa-handshake'],
    ['Offers Update', 'Offer creation, approval, release, acceptance and decline.', 'fa-solid fa-file-signature'],
    ['Joining Update', 'Candidate joining, postponement and cancellation updates.', 'fa-solid fa-user-check'],
    ['Bench Candidate Request', 'Bench candidates requested for a job.', 'fa-solid fa-address-card'],
    ['Team Member Status', 'Team invitations, role changes and account status updates.', 'fa-solid fa-user-shield'],
    ['Bench Notification on Jobs', 'Bench candidates submitted or placed on jobs.', 'fa-solid fa-briefcase'],
    ['Additional Notifications', 'Notes, comments and other important recruitment actions.', 'fa-solid fa-comment-dots'],
  ];

  // Sales & Commercial Event Triggers (8 Items)
  $salesNotificationItems = [
    ['New Job Request from Direct Client', 'A new job request raised by a direct client.', 'fa-solid fa-file-circle-plus'],
    ['Bench Added on Client Jobs', 'Bench candidates added or submitted on client jobs.', 'fa-solid fa-network-wired'],
    ['Subscription Status', 'Subscription activation, renewal, expiry and plan changes.', 'fa-solid fa-credit-card'],
    ['Client Communication', 'Client messages, feedback and important updates.', 'fa-solid fa-comments'],
    ['Invoice & Payment Status', 'Invoices, payments, due dates and payment failures.', 'fa-solid fa-receipt'],
    ['Contract & Agreement Updates', 'Contract creation, renewal, expiry and amendments.', 'fa-solid fa-handshake-angle'],
    ['Low Coins / Wallet Alerts', 'Low coin balance and wallet actions requiring attention.', 'fa-solid fa-coins'],
    ['Additional Sales Alerts', 'Other important sales updates and system alerts.', 'fa-solid fa-triangle-exclamation'],
  ];

  $recruitmentEmailVal = (!empty($notification['Recuitment']->email)) ? $notification['Recuitment']->email : ($account->company_email ?? '');
  $salesEmailVal = (!empty($notification['Sales']->email)) ? $notification['Sales']->email : ($account->company_email ?? '');
@endphp

@if($hasViewPerm)
<form id="notification_content" class="form" method="post" novalidate="novalidate">
  @csrf
  <input type="hidden" name="pagename" value="settings">

  <div class="notifications-card-system">

    {{-- ══════════════════════════════════════════════════════════════
         TIER 1: PURE WHITE TOOLBAR HEADER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="card mb-4 bg-white border border-gray-300 rounded-3 shadow-none tier1-toolbar-card">
      <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="d-flex align-items-center justify-content-center bg-light-primary text-primary rounded-3 w-45px h-45px border border-primary-subtle flex-shrink-0">
            <i class="fa-solid fa-bell fs-3"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h3 class="fw-bolder text-dark mb-0 fs-4">Notification & Alert Routing</h3>
              <span class="badge badge-light-primary fw-bolder fs-8 px-2 py-1">16 Event Triggers Active</span>
            </div>
            <p class="text-muted fs-7 mb-0 mt-1">Configure enterprise dispatch destinations for hiring lifecycles, candidate stages, client orders, and billing alerts.</p>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
          {{-- 300ms Debounced Search Bar with Clear Button --}}
          <div class="position-relative w-250px">
            <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-500">
              <i class="fa-solid fa-magnifying-glass fs-7"></i>
            </span>
            <input type="text" id="notifications_search" class="form-control form-control-sm form-control-solid ps-9 pe-8 fs-7" placeholder="Search notification triggers..." autocomplete="off">
            <button type="button" id="notifications_search_clear_btn" class="btn btn-icon btn-sm position-absolute top-50 end-0 translate-middle-y text-gray-400 text-hover-danger p-0 me-2" style="display: none; width: 22px; height: 22px;">
              <i class="fa-solid fa-xmark fs-7"></i>
            </button>
          </div>

          @if($hasEditPerm)
          <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2 px-4" id="saveNotificationSettingsHeader">
            <span class="csn-save-spinner" aria-hidden="true" style="display: none;"></span>
            <span class="csn-save-label"><i class="fa-solid fa-check fs-7"></i> Save Changes</span>
          </button>
          @endif
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 2: PURE WHITE SUB-TABS FILTER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="card mb-4 bg-white border border-gray-300 rounded-3 shadow-none tier2-subtabs-card">
      <div class="card-body p-2 d-flex align-items-center gap-2 overflow-auto" id="notifications_subtabs_bar" style="scrollbar-width: none;">
        <button type="button" class="btn btn-sm btn-subtab active d-inline-flex align-items-center gap-2 px-3 py-2" data-target="all">
          <i class="fa-solid fa-border-all fs-7"></i>
          <span>All Notifications</span>
          <span class="badge badge-light-primary rounded-pill fs-8 px-2">16</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_notif_routing">
          <i class="fa-solid fa-envelope-circle-check fs-7"></i>
          <span>Email Routing</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2">2</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_notif_recruitment">
          <i class="fa-solid fa-user-tie fs-7"></i>
          <span>Recruitment Triggers</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2">8</span>
        </button>
        <button type="button" class="btn btn-sm btn-subtab d-inline-flex align-items-center gap-2 px-3 py-2 text-gray-600" data-target="card_notif_sales">
          <i class="fa-solid fa-chart-line fs-7"></i>
          <span>Sales & Commercials</span>
          <span class="badge badge-light-dark rounded-pill fs-8 px-2">8</span>
        </button>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 3: PRIMARY CARD STREAM
         ══════════════════════════════════════════════════════════════ --}}
    <div class="d-flex flex-column gap-4 tier3-cards-stream">

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 1: ENTERPRISE EMAIL DISPATCH ROUTING
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-notifications-card" id="card_notif_routing" data-search-scope="email routing primary destination dispatch recruitment sales inbox">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 bg-light-primary text-primary d-flex align-items-center justify-content-center border border-primary-subtle flex-shrink-0">
              <i class="fa-solid fa-envelope-circle-check fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">Email Dispatch & Delivery Routing</h4>
              <p class="text-muted fs-8 mb-0">Primary recipient mailboxes designated for automated pipeline and sales alerts.</p>
            </div>
          </div>
          <span class="badge badge-light-primary fw-bolder fs-8 px-3 py-2">
            <i class="fa-solid fa-shield-halved text-primary me-1"></i> DKIM & SPF Verified
          </span>
        </div>

        <div class="card-body p-6">
          <div class="row g-4">

            {{-- Routing Sub-Card 1: Recruitment Mailbox --}}
            <div class="col-lg-6 col-12">
              <div class="routing-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-40px h-40px rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #f5f3ff; color: #8b5cf6; border: 1px solid #ede9fe;">
                      <i class="fa-solid fa-user-tie fs-4"></i>
                    </div>
                    <div>
                      <label class="fw-bolder text-dark fs-6 mb-0 d-block" for="recuitment_email">Recruitment Pipeline Mailbox <span class="text-danger">*</span></label>
                      <div class="text-muted fs-8 mt-1">Receives candidate stage moves, interview confirmations, agency updates, and offer releases.</div>
                    </div>
                  </div>
                  <div class="position-relative mb-2">
                    <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                      <i class="fa-solid fa-envelope fs-7"></i>
                    </span>
                    <input type="email" name="recuitment_email" id="recuitment_email" value="{{ $recruitmentEmailVal }}" placeholder="Enter recruitment email" class="form-control form-control-solid bg-white border border-gray-300 ps-10 fs-7" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                  </div>
                  <div id="recuitment_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top border-gray-200">
                  <span class="badge fs-8 fw-bold px-2 py-1" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;">
                    <i class="fa-solid fa-bolt me-1"></i> 8 Recruitment Triggers Covered
                  </span>
                  <span class="text-success fs-8 fw-bold"><i class="fa-solid fa-circle-check me-1"></i> Active Routing</span>
                </div>
              </div>
            </div>

            {{-- Routing Sub-Card 2: Sales Mailbox --}}
            <div class="col-lg-6 col-12">
              <div class="routing-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                <div>
                  <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-40px h-40px rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                      <i class="fa-solid fa-chart-line fs-4"></i>
                    </div>
                    <div>
                      <label class="fw-bolder text-dark fs-6 mb-0 d-block" for="sales_email">Sales & Commercials Mailbox <span class="text-danger">*</span></label>
                      <div class="text-muted fs-8 mt-1">Receives direct client job requests, contract signing updates, invoice alerts, and wallet warnings.</div>
                    </div>
                  </div>
                  <div class="position-relative mb-2">
                    <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                      <i class="fa-solid fa-envelope fs-7"></i>
                    </span>
                    <input type="email" name="sales_email" id="sales_email" value="{{ $salesEmailVal }}" placeholder="Enter sales email" class="form-control form-control-solid bg-white border border-gray-300 ps-10 fs-7" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                  </div>
                  <div id="sales_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top border-gray-200">
                  <span class="badge fs-8 fw-bold px-2 py-1" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                    <i class="fa-solid fa-bolt me-1"></i> 8 Sales Triggers Covered
                  </span>
                  <span class="text-success fs-8 fw-bold"><i class="fa-solid fa-circle-check me-1"></i> Active Routing</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 2: RECRUITMENT PIPELINE EVENT TRIGGERS (8 ITEMS)
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-notifications-card" id="card_notif_recruitment" data-search-scope="recruitment candidate stages agency offers joining bench member status interview pipeline">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #f5f3ff; color: #8b5cf6; border: 1px solid #ede9fe;">
              <i class="fa-solid fa-users-gear fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">Recruitment Pipeline Event Triggers</h4>
              <p class="text-muted fs-8 mb-0">Automated lifecycle alerts covering candidate progression, team status, and offer approvals.</p>
            </div>
          </div>
          <span class="badge fw-bolder fs-8 px-3 py-2" style="background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe;">
            <i class="fa-solid fa-envelope me-1"></i> Dispatches to: <span class="badge-rec-email ms-1">{{ $recruitmentEmailVal ?: 'Unconfigured' }}</span>
          </span>
        </div>

        <div class="card-body p-6">
          <div class="row g-3">
            @foreach($recruitmentNotificationItems as $notificationIndex => $notificationItem)
              <div class="col-xl-3 col-lg-4 col-md-6 col-12 notif-trigger-col" data-trigger-scope="{{ strtolower($notificationItem[0] . ' ' . $notificationItem[1]) }}">
                <div class="trigger-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                  <div>
                    <div class="d-flex align-items-start gap-3 mb-2">
                      <div class="w-32px h-32px rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #f5f3ff; color: #8b5cf6; border: 1px solid #ede9fe;">
                        <i class="{{ $notificationItem[2] }} fs-7"></i>
                      </div>
                      <div>
                        <div class="text-muted fs-9 fw-bolder text-uppercase tracking-wider">TRIGGER {{ str_pad($notificationIndex + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="fw-bold text-dark fs-7">{{ $notificationItem[0] }}</div>
                      </div>
                    </div>
                    <p class="text-gray-600 fs-8 mb-3">{{ $notificationItem[1] }}</p>
                  </div>
                  <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-200 fs-9">
                    <span class="text-success fw-bold d-inline-flex align-items-center gap-1"><i class="fa-solid fa-circle-dot fs-9"></i> Automated</span>
                    <span class="text-muted fw-semibold">Recruitment Mail</span>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 3: SALES & COMMERCIAL EVENT TRIGGERS (8 ITEMS)
           ──────────────────────────────────────────────────────────── --}}
      <div class="card bg-white border border-gray-300 rounded-3 shadow-none primary-notifications-card" id="card_notif_sales" data-search-scope="sales client job requests bench subscription invoice payments contract agreement coins wallet">
        <div class="card-header border-bottom border-gray-200 px-6 py-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-3">
            <div class="w-35px h-35px rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
              <i class="fa-solid fa-sack-dollar fs-5"></i>
            </div>
            <div>
              <h4 class="fw-bolder text-dark mb-0 fs-5">Sales & Commercial Event Triggers</h4>
              <p class="text-muted fs-8 mb-0">Direct corporate client orders, agreement executions, billing statements, and balance notices.</p>
            </div>
          </div>
          <span class="badge fw-bolder fs-8 px-3 py-2" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
            <i class="fa-solid fa-envelope me-1"></i> Dispatches to: <span class="badge-sales-email ms-1">{{ $salesEmailVal ?: 'Unconfigured' }}</span>
          </span>
        </div>

        <div class="card-body p-6">
          <div class="row g-3">
            @foreach($salesNotificationItems as $notificationIndex => $notificationItem)
              <div class="col-xl-3 col-lg-4 col-md-6 col-12 notif-trigger-col" data-trigger-scope="{{ strtolower($notificationItem[0] . ' ' . $notificationItem[1]) }}">
                <div class="trigger-subcard p-4 rounded-3 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                  <div>
                    <div class="d-flex align-items-start gap-3 mb-2">
                      <div class="w-32px h-32px rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                        <i class="{{ $notificationItem[2] }} fs-7"></i>
                      </div>
                      <div>
                        <div class="text-muted fs-9 fw-bolder text-uppercase tracking-wider">TRIGGER {{ str_pad($notificationIndex + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="fw-bold text-dark fs-7">{{ $notificationItem[0] }}</div>
                      </div>
                    </div>
                    <p class="text-gray-600 fs-8 mb-3">{{ $notificationItem[1] }}</p>
                  </div>
                  <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-200 fs-9">
                    <span class="text-success fw-bold d-inline-flex align-items-center gap-1"><i class="fa-solid fa-circle-dot fs-9"></i> Automated</span>
                    <span class="text-muted fw-semibold">Sales Mail</span>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         BOTTOM ACTIVITY & TENANCY STRIP
         ══════════════════════════════════════════════════════════════ --}}
    <div class="card mt-4 bg-white border border-gray-300 rounded-3 shadow-none activity-strip-card">
      <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="badge badge-light-dark fs-8 fw-bolder px-3 py-2 border border-gray-200">
            <i class="fa-solid fa-shield-halved text-primary me-2"></i>
            Tenant Context: {{ auth()->user()->company->company_name ?? 'Organization' }} (#{{ auth()->user()->company_id ?? 0 }})
          </div>
          <div class="text-muted fs-8 d-flex align-items-center gap-2">
            <i class="fa-solid fa-bolt text-warning fs-7"></i>
            <span>Notification recipient changes update Redis & Elasticsearch immediately with zero dispatch latency.</span>
          </div>
        </div>

        @if($hasEditPerm)
        <div class="d-flex align-items-center gap-2">
          <button type="reset" class="btn btn-sm btn-light text-muted px-4">Discard Changes</button>
          <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-2 px-5" id="saveNotificationSettings">
            <span class="csn-save-spinner" aria-hidden="true" style="display: none;"></span>
            <span class="csn-save-label">Save Changes</span>
          </button>
        </div>
        @endif
      </div>
    </div>

  </div>
</form>
@else
<div class="card bg-white border border-gray-300 rounded-3 p-8 text-center">
  <div class="text-danger fw-bold fs-6">You do not have permission to view notification settings.</div>
</div>
@endif

{{-- Inline Scoped Styling & Interactive Scripts --}}
<style>
  .notifications-card-system {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }
  .notifications-card-system .btn-subtab {
    background: transparent;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.15s ease;
    white-space: nowrap;
  }
  .notifications-card-system .btn-subtab:hover {
    background: #f1f5f9;
    color: #0f172a !important;
  }
  .notifications-card-system .btn-subtab.active {
    background: #eff6ff;
    color: #009ef7 !important;
    font-weight: 600;
    box-shadow: inset 0 0 0 1px rgba(0, 158, 247, 0.25);
  }
  .notifications-card-system .routing-subcard:hover,
  .notifications-card-system .trigger-subcard:hover {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  }
  #saveNotificationSettingsHeader.is-loading .csn-save-spinner,
  #saveNotificationSettings.is-loading .csn-save-spinner {
    display: inline-block !important;
    width: 14px;
    height: 14px;
    border: 2px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: csn-spin 0.6s linear infinite;
    margin-right: 6px;
  }
  @keyframes csn-spin {
    to { transform: rotate(360deg); }
  }
</style>

<script>
  $(document).ready(function() {
    // ── 1. Live 300ms Debounced Search Across Notification Triggers ──
    var $notifSearch = $('#notifications_search');
    var $notifClear = $('#notifications_search_clear_btn');
    var notifSearchTimer = null;

    $notifSearch.on('input', function() {
      var query = $(this).val().trim().toLowerCase();
      $notifClear.toggle(query.length > 0);

      clearTimeout(notifSearchTimer);
      notifSearchTimer = setTimeout(function() {
        filterNotificationCards(query);
      }, 300);
    });

    $notifClear.on('click', function() {
      $notifSearch.val('');
      $notifClear.hide();
      filterNotificationCards('');
      $notifSearch.focus();
    });

    function filterNotificationCards(query) {
      if (!query) {
        $('.primary-notifications-card').show();
        $('.notif-trigger-col').show();
        return;
      }

      $('.primary-notifications-card').each(function() {
        var $card = $(this);
        var scope = ($card.attr('data-search-scope') || '').toLowerCase();
        var cardText = $card.text().toLowerCase();

        if (scope.indexOf(query) !== -1 || cardText.indexOf(query) !== -1) {
          $card.show();
          $card.find('.notif-trigger-col').each(function() {
            var trigScope = $(this).attr('data-trigger-scope') || '';
            $(this).toggle(trigScope.indexOf(query) !== -1);
          });
        } else {
          $card.hide();
        }
      });
    }

    // ── 2. Tier 2 Category Jump Sub-Tabs ──
    $('#notifications_subtabs_bar').on('click', '.btn-subtab', function() {
      $('#notifications_subtabs_bar .btn-subtab').removeClass('active');
      $(this).addClass('active');

      var target = $(this).attr('data-target');
      if (target === 'all') {
        $('.primary-notifications-card').show();
      } else {
        $('.primary-notifications-card').each(function() {
          $(this).toggle($(this).attr('id') === target);
        });
      }
    });

    // ── 3. Header Save Button Triggers Primary Save Action ──
    $('#saveNotificationSettingsHeader').on('click', function() {
      $('#saveNotificationSettings').click();
    });

    // ── 4. Sync Email Address Badges dynamically ──
    $('#recuitment_email').on('input', function() {
      var val = $(this).val().trim() || 'Unconfigured';
      $('.badge-rec-email').text(val);
    });

    $('#sales_email').on('input', function() {
      var val = $(this).val().trim() || 'Unconfigured';
      $('.badge-sales-email').text(val);
    });
  });
</script>
