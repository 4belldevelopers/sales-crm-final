{{--
  JERO ATS — Service Company Notification Email Routing Tab Component (Rule 20 Modern Card + Sub-Card Design System)
  Intent: Preserves the intuitive 2-column original workflow where each domain card pairs the Primary Recipient Email
          input directly above the 8 automated service sub-cards routed to that inbox, upgraded with Rule 20 visual
          architecture, enterprise copy, isolated CSS tokens, and guaranteed FontAwesome 5/6 glyph rendering.
  Tenancy & Security: Strictly scoped by company_id. Preserves recuitment_email & sales_email form keys and
                      JeroEmailBoxEngine client-first validation hooks.
  Screen Fit: 100% fluid fit for 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}

@php
  $userPerms = $userPermissions ?? [];
  $hasViewPerm = in_array('view_settings', $userPerms);
  $hasEditPerm = in_array('edit_settings', $userPerms);

  // Recruitment Email Services (8 Automated Triggers) — Universal FA5/FA6 solid icons
  $recruitmentNotificationItems = [
    [
      'title' => 'Candidate Pipeline & Stage Moves',
      'desc' => 'Sent when candidates progress through Applied, Shortlisted, Evaluation, Interview, or Rejected stages.',
      'icon' => 'fas fa-random',
    ],
    [
      'title' => 'Staffing Agency Submissions & Status',
      'desc' => 'Sent when partner agencies submit profiles, update candidate statuses, or trigger compliance changes.',
      'icon' => 'fas fa-handshake',
    ],
    [
      'title' => 'Job Offers, Approvals & Responses',
      'desc' => 'Sent when an offer letter is created, internally approved, released to a candidate, accepted, or declined.',
      'icon' => 'fas fa-file-signature',
    ],
    [
      'title' => 'Candidate Onboarding & Joining Status',
      'desc' => 'Sent for confirmed joining dates, onboarding postponements, or pre-joining candidate cancellations.',
      'icon' => 'fas fa-user-check',
    ],
    [
      'title' => 'Incoming Bench Candidate Requests',
      'desc' => 'Sent immediately when hiring teams or partner organizations request your available bench consultants.',
      'icon' => 'fas fa-address-card',
    ],
    [
      'title' => 'Team Invitations & Permission Changes',
      'desc' => 'Sent when workspace members are invited, assigned updated roles, activated, or deactivated.',
      'icon' => 'fas fa-user-shield',
    ],
    [
      'title' => 'Bench Talent Submissions & Placements',
      'desc' => 'Sent whenever bench resources are submitted against open requisitions or marked as placed.',
      'icon' => 'fas fa-briefcase',
    ],
    [
      'title' => 'Recruiter Mentions, Notes & Alerts',
      'desc' => 'Sent for internal collaborator notes, @mentions on candidate records, and auxiliary hiring updates.',
      'icon' => 'fas fa-comment-dots',
    ],
  ];

  // Sales & Commercial Email Services (8 Automated Triggers) — Universal FA5/FA6 solid icons
  $salesNotificationItems = [
    [
      'title' => 'Direct Client Job Requisitions',
      'desc' => 'Sent immediately whenever a direct corporate client creates, opens, or assigns a new job request.',
      'icon' => 'fas fa-folder-plus',
    ],
    [
      'title' => 'Bench Submissions on Client Jobs',
      'desc' => 'Sent when bench candidates are mapped, submitted, or shortlisted against active client requirements.',
      'icon' => 'fas fa-network-wired',
    ],
    [
      'title' => 'Subscription Plan & Renewal Lifecycle',
      'desc' => 'Sent for plan activations, upcoming billing renewals, subscription expiries, and tier modifications.',
      'icon' => 'fas fa-credit-card',
    ],
    [
      'title' => 'Client Messages & Hiring Feedback',
      'desc' => 'Sent when direct clients send inquiries, interview feedback, or review decisions on shared profiles.',
      'icon' => 'fas fa-comments',
    ],
    [
      'title' => 'Invoices, Payments & Settlement Alerts',
      'desc' => 'Sent for newly issued invoices, confirmed payments, upcoming due dates, and failed transactions.',
      'icon' => 'fas fa-receipt',
    ],
    [
      'title' => 'Contracts, MSAs & Agreement Updates',
      'desc' => 'Sent for agreement creation, signature milestones, upcoming expiries, renewals, and addenda.',
      'icon' => 'fas fa-file-contract',
    ],
    [
      'title' => 'Low Coin Balance & Wallet Warnings',
      'desc' => 'Sent proactively when workspace credits or coins fall below operational thresholds and need top-up.',
      'icon' => 'fas fa-coins',
    ],
    [
      'title' => 'Priority Commercial & System Notices',
      'desc' => 'Sent for critical revenue operations escalations, commercial summaries, and account-level alerts.',
      'icon' => 'fas fa-exclamation-triangle',
    ],
  ];

  $recruitmentEmailVal = (!empty($notification['Recuitment']->email)) ? $notification['Recuitment']->email : ($account->company_email ?? '');
  $salesEmailVal = (!empty($notification['Sales']->email)) ? $notification['Sales']->email : ($account->company_email ?? '');
@endphp

@if($hasViewPerm)
<form id="notification_content" class="form" method="post" novalidate="novalidate">
  @csrf
  <input type="hidden" name="pagename" value="settings">

  <div class="ns-wrapper notifications-card-system">

    {{-- ══════════════════════════════════════════════════════════════
         TIER 1: PURE WHITE TOOLBAR HEADER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-toolbar-card tier1-toolbar-card">
      <div class="ns-toolbar-left">
        <div class="ns-toolbar-icon-box">
          <i class="fas fa-bell"></i>
        </div>
        <div class="ns-toolbar-copy">
          <div class="ns-toolbar-title-row">
            <h3 class="ns-toolbar-title">Notification Email Routing</h3>
            <span class="ns-count-pill">2 Dedicated Inboxes &bull; 16 Services</span>
          </div>
          <p class="ns-toolbar-sub">Enter a primary email address in each channel card to receive automated alerts for every service listed underneath it.</p>
        </div>
      </div>

      <div class="ns-toolbar-right">
        <div class="ns-search-box">
          <i class="fas fa-search ns-search-icon"></i>
          <input type="text" id="notifications_search" class="ns-search-input" placeholder="Search notification services..." autocomplete="off">
          <button type="button" id="notifications_search_clear_btn" class="ns-search-clear" title="Clear search"><i class="fas fa-times"></i></button>
        </div>

        @if($hasEditPerm)
        <button type="button" class="ns-btn-save" id="saveNotificationSettingsHeader">
          <span class="csn-save-spinner" aria-hidden="true"></span>
          <i class="fas fa-check"></i>
          <span class="csn-save-label">Save Changes</span>
        </button>
        @endif
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 2: PURE WHITE SUB-TABS FILTER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-subtabs-card tier2-subtabs-card" id="notifications_subtabs_bar">
      <button type="button" class="ns-subtab-pill btn-subtab active" data-target="all">
        <i class="fas fa-columns"></i>
        <span>All Notification Channels</span>
        <span class="ns-subtab-counter">2</span>
      </button>
      <button type="button" class="ns-subtab-pill btn-subtab" data-target="card_notif_recruitment">
        <i class="fas fa-user-tie"></i>
        <span>Recruitment Services</span>
        <span class="ns-subtab-counter">8</span>
      </button>
      <button type="button" class="ns-subtab-pill btn-subtab" data-target="card_notif_sales">
        <i class="fas fa-chart-line"></i>
        <span>Sales &amp; Commercial Services</span>
        <span class="ns-subtab-counter">8</span>
      </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 3: UNIFIED 2-COLUMN DOMAIN CARDS (EMAIL ABOVE + 8 SERVICES BELOW)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-channels-grid tier3-cards-stream" id="notifications_channels_grid">

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 1: RECRUITMENT NOTIFICATIONS
           ──────────────────────────────────────────────────────────── --}}
      <section class="ns-primary-card primary-notifications-card" id="card_notif_recruitment" data-search-scope="recruitment email candidate stages agency offers joining bench team member notes">
        <div class="ns-card-header">
          <div class="ns-card-header-left">
            <div class="ns-cat-icon ns-icon-purple">
              <i class="fas fa-user-tie"></i>
            </div>
            <div>
              <h4 class="ns-card-title">Recruitment Notifications</h4>
              <p class="ns-card-desc">Email destination for hiring pipeline, agency activity, offers, and bench staffing updates.</p>
            </div>
          </div>
          <span class="ns-status-badge ns-badge-purple">
            <i class="fas fa-bolt"></i> 8 Services
          </span>
        </div>

        <div class="ns-card-body">
          {{-- Sub-Card A: Primary Recipient Email Input --}}
          <div class="ns-inbox-subcard ns-inbox-purple">
            <div class="ns-inbox-head">
              <label class="ns-inbox-label" for="recuitment_email">
                Primary Recruitment Email ID <span class="ns-req">*</span>
              </label>
              <span class="ns-inbox-scope ns-badge-purple">
                <i class="fas fa-arrow-down"></i> Receives all 8 services below
              </span>
            </div>
            <p class="ns-inbox-help">Every automated recruitment alert listed below will be dispatched exclusively to this email address.</p>
            <div class="ns-email-wrap">
              <i class="fas fa-envelope ns-email-icon"></i>
              <input type="email" name="recuitment_email" id="recuitment_email" value="{{ $recruitmentEmailVal }}" placeholder="Enter recruitment email (e.g., talent@company.com)" class="ns-email-input" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
            </div>
            <div id="recuitment_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 6px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 5px 9px;"></div>
          </div>

          {{-- Sub-Card B: Covered Services List Directly Underneath --}}
          <div class="ns-services-section">
            <div class="ns-services-bar">
              <span class="ns-services-heading">Services Delivered to This Email (8)</span>
              <span class="ns-dest-pill ns-dest-purple">
                <i class="fas fa-paper-plane"></i>
                <span>To: <strong class="badge-rec-email">{{ $recruitmentEmailVal ?: 'Unconfigured' }}</strong></span>
              </span>
            </div>

            <div class="ns-services-list">
              @foreach($recruitmentNotificationItems as $notificationIndex => $notificationItem)
                <div class="ns-service-subcard notif-trigger-col" data-trigger-scope="{{ strtolower($notificationItem['title'] . ' ' . $notificationItem['desc']) }}">
                  <div class="ns-service-icon ns-icon-purple">
                    <i class="{{ $notificationItem['icon'] }}"></i>
                  </div>
                  <div class="ns-service-content">
                    <div class="ns-service-title-row">
                      <span class="ns-service-num ns-num-purple">{{ str_pad($notificationIndex + 1, 2, '0', STR_PAD_LEFT) }}</span>
                      <span class="ns-service-title">{{ $notificationItem['title'] }}</span>
                    </div>
                    <p class="ns-service-desc">{{ $notificationItem['desc'] }}</p>
                  </div>
                  <span class="ns-service-active" title="Routed to Primary Recruitment Email">
                    <i class="fas fa-check-circle"></i>
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </section>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 2: SALES & COMMERCIAL NOTIFICATIONS
           ──────────────────────────────────────────────────────────── --}}
      <section class="ns-primary-card primary-notifications-card" id="card_notif_sales" data-search-scope="sales commercial email direct client job requests bench subscription invoice payment contract wallet coins">
        <div class="ns-card-header">
          <div class="ns-card-header-left">
            <div class="ns-cat-icon ns-icon-emerald">
              <i class="fas fa-chart-line"></i>
            </div>
            <div>
              <h4 class="ns-card-title">Sales &amp; Commercial Notifications</h4>
              <p class="ns-card-desc">Email destination for direct client job orders, agreements, billing, and coin balance alerts.</p>
            </div>
          </div>
          <span class="ns-status-badge ns-badge-emerald">
            <i class="fas fa-bolt"></i> 8 Services
          </span>
        </div>

        <div class="ns-card-body">
          {{-- Sub-Card A: Primary Recipient Email Input --}}
          <div class="ns-inbox-subcard ns-inbox-emerald">
            <div class="ns-inbox-head">
              <label class="ns-inbox-label" for="sales_email">
                Primary Sales Email ID <span class="ns-req">*</span>
              </label>
              <span class="ns-inbox-scope ns-badge-emerald">
                <i class="fas fa-arrow-down"></i> Receives all 8 services below
              </span>
            </div>
            <p class="ns-inbox-help">Every automated sales, client, and billing alert listed below will be dispatched exclusively to this email address.</p>
            <div class="ns-email-wrap">
              <i class="fas fa-envelope ns-email-icon"></i>
              <input type="email" name="sales_email" id="sales_email" value="{{ $salesEmailVal }}" placeholder="Enter sales email (e.g., business@company.com)" class="ns-email-input" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
            </div>
            <div id="sales_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 6px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 5px 9px;"></div>
          </div>

          {{-- Sub-Card B: Covered Services List Directly Underneath --}}
          <div class="ns-services-section">
            <div class="ns-services-bar">
              <span class="ns-services-heading">Services Delivered to This Email (8)</span>
              <span class="ns-dest-pill ns-dest-emerald">
                <i class="fas fa-paper-plane"></i>
                <span>To: <strong class="badge-sales-email">{{ $salesEmailVal ?: 'Unconfigured' }}</strong></span>
              </span>
            </div>

            <div class="ns-services-list">
              @foreach($salesNotificationItems as $notificationIndex => $notificationItem)
                <div class="ns-service-subcard notif-trigger-col" data-trigger-scope="{{ strtolower($notificationItem['title'] . ' ' . $notificationItem['desc']) }}">
                  <div class="ns-service-icon ns-icon-emerald">
                    <i class="{{ $notificationItem['icon'] }}"></i>
                  </div>
                  <div class="ns-service-content">
                    <div class="ns-service-title-row">
                      <span class="ns-service-num ns-num-emerald">{{ str_pad($notificationIndex + 1, 2, '0', STR_PAD_LEFT) }}</span>
                      <span class="ns-service-title">{{ $notificationItem['title'] }}</span>
                    </div>
                    <p class="ns-service-desc">{{ $notificationItem['desc'] }}</p>
                  </div>
                  <span class="ns-service-active" title="Routed to Primary Sales Email">
                    <i class="fas fa-check-circle"></i>
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </section>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         BOTTOM ACTIVITY & TENANCY STRIP
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-bottom-strip activity-strip-card">
      <div class="ns-strip-left">
        <div class="ns-tenancy-badge">
          <i class="fas fa-shield-alt"></i>
          <span>Tenant Context: {{ auth()->user()->company->company_name ?? 'Organization' }} (#{{ auth()->user()->company_id ?? 0 }})</span>
        </div>
        <div class="ns-sync-note">
          <i class="fas fa-bolt"></i>
          <span>Notification recipient changes update Redis &amp; Elasticsearch immediately with zero dispatch latency.</span>
        </div>
      </div>

      @if($hasEditPerm)
      <div class="ns-strip-actions">
        <button type="reset" class="ns-btn-discard" id="discardNotificationSettings">Discard Changes</button>
        <button type="button" class="ns-btn-save" id="saveNotificationSettings">
          <span class="csn-save-spinner" aria-hidden="true"></span>
          <i class="fas fa-check"></i>
          <span class="csn-save-label">Save Changes</span>
        </button>
      </div>
      @endif
    </div>

  </div>
</form>
@else
<div class="ns-primary-card" style="padding: 36px; text-align: center;">
  <div style="color: #ef4444; font-weight: 600; font-size: 14px;">You do not have permission to view notification settings.</div>
</div>
@endif

{{-- Dedicated Isolated Stylesheet for Rule 20 Modern Card + Sub-Card Design --}}
<style>
  .ns-wrapper {
    width: 100%;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #0f172a;
    box-sizing: border-box;
  }
  .ns-wrapper *, .ns-wrapper *::before, .ns-wrapper *::after {
    box-sizing: border-box;
  }
  /* Guarantee FontAwesome 5 & 6 glyphs render crisply without theme font overrides */
  .ns-wrapper i, .ns-wrapper [class*="fa-"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal !important;
    line-height: 1 !important;
  }
  .ns-wrapper .fas, .ns-wrapper .fa-solid { font-weight: 900 !important; }
  .ns-wrapper .far, .ns-wrapper .fa-regular { font-weight: 400 !important; }

  /* ── COLOR TOKENS ── */
  .ns-icon-blue { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #dbeafe !important; }
  .ns-icon-blue i { color: #2563eb !important; }
  .ns-icon-purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ede9fe !important; }
  .ns-icon-purple i { color: #7c3aed !important; }
  .ns-icon-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
  .ns-icon-emerald i { color: #059669 !important; }

  .ns-badge-purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }
  .ns-badge-purple i { color: #7c3aed !important; }
  .ns-badge-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
  .ns-badge-emerald i { color: #059669 !important; }

  /* ── TIER 1: TOOLBAR HEADER CARD ── */
  .ns-toolbar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px 18px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    flex-wrap: wrap;
  }
  .ns-toolbar-left { display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1 1 380px; }
  .ns-toolbar-copy { min-width: 0; }
  .ns-toolbar-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    color: #009ef7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    border: 1px solid #dbeafe;
    flex-shrink: 0;
  }
  .ns-toolbar-icon-box i { color: #009ef7 !important; }
  .ns-toolbar-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .ns-toolbar-title { font-size: 15.5px; font-weight: 700; color: #0f172a; margin: 0; }
  .ns-count-pill {
    background: #eff6ff;
    color: #0284c7;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 12px;
    border: 1px solid #bae6fd;
  }
  .ns-toolbar-sub { font-size: 12px; color: #64748b; margin: 2px 0 0 0; line-height: 1.4; }
  .ns-toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .ns-search-box { position: relative; min-width: 245px; }
  .ns-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12.5px;
    color: #94a3b8 !important;
    pointer-events: none;
  }
  .ns-search-input {
    width: 100%;
    height: 38px;
    padding: 0 32px 0 35px;
    font-size: 12.5px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: all 0.2s ease;
  }
  .ns-search-input:focus {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
  }
  .ns-search-clear {
    position: absolute;
    right: 9px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #94a3b8;
    background: none;
    border: none;
    cursor: pointer;
    display: none;
    padding: 2px;
  }
  .ns-search-clear:hover i { color: #ef4444 !important; }
  .ns-btn-save {
    height: 38px;
    padding: 0 18px;
    background: #009ef7;
    color: #ffffff !important;
    font-size: 12.5px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
    white-space: nowrap;
  }
  .ns-btn-save:hover { background: #0086d4; box-shadow: 0 4px 8px rgba(0, 158, 247, 0.3); }
  .ns-btn-save i { color: #ffffff !important; font-size: 12px; }
  .ns-btn-save .csn-save-spinner {
    display: none;
    width: 14px;
    height: 14px;
    border: 2px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: nsSpin 0.6s linear infinite;
  }
  .ns-btn-save.is-loading .csn-save-spinner { display: inline-block; }
  .ns-btn-save:disabled { cursor: not-allowed; opacity: 0.78; }
  @keyframes nsSpin { to { transform: rotate(360deg); } }

  /* ── TIER 2: SUB-TABS FILTER CARD ── */
  .ns-subtabs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 6px 10px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    overflow-x: auto;
    scrollbar-width: none;
  }
  .ns-subtabs-card::-webkit-scrollbar { display: none; }
  .ns-subtab-pill {
    background: transparent;
    border: none;
    border-radius: 7px;
    padding: 8px 15px;
    font-size: 12.5px;
    font-weight: 500;
    color: #64748b;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s ease;
    white-space: nowrap;
  }
  .ns-subtab-pill i { color: #64748b !important; font-size: 12.5px; }
  .ns-subtab-pill:hover { background: #f1f5f9; color: #1e293b; }
  .ns-subtab-pill:hover i { color: #1e293b !important; }
  .ns-subtab-pill.active {
    background: #eff6ff;
    color: #009ef7;
    font-weight: 600;
    box-shadow: inset 0 0 0 1px rgba(0, 158, 247, 0.3);
  }
  .ns-subtab-pill.active i { color: #009ef7 !important; }
  .ns-subtab-counter {
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 10px;
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
  }
  .ns-subtab-pill.active .ns-subtab-counter { background: #009ef7; color: #ffffff; }

  /* ── TIER 3: 2-COLUMN UNIFIED PRIMARY CARDS ── */
  .ns-channels-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    align-items: stretch;
  }
  .ns-channels-grid.ns-single-focus {
    grid-template-columns: 1fr;
  }
  .ns-channels-grid.ns-single-focus .ns-services-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }
  .ns-primary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.025);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .ns-primary-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.045);
  }
  .ns-card-header {
    padding: 15px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #ffffff;
    flex-wrap: wrap;
  }
  .ns-card-header-left { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; }
  .ns-cat-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .ns-card-title { font-size: 14.5px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.3; }
  .ns-card-desc { font-size: 11.5px; color: #64748b; margin: 2px 0 0 0; line-height: 1.35; }
  .ns-status-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
    white-space: nowrap;
  }
  .ns-card-body {
    padding: 16px 18px 18px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    flex: 1;
  }

  /* ── SUB-CARD A: EMAIL DESTINATION INBOX ── */
  .ns-inbox-subcard {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    padding: 14px 15px;
    transition: border-color 0.2s ease, background 0.2s ease;
  }
  .ns-inbox-subcard:focus-within {
    background: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 2px 10px rgba(0, 158, 247, 0.08);
  }
  .ns-inbox-purple { border-left: 3.5px solid #7c3aed; }
  .ns-inbox-emerald { border-left: 3.5px solid #059669; }
  .ns-inbox-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
  }
  .ns-inbox-label {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    cursor: pointer;
  }
  .ns-req { color: #ef4444; }
  .ns-inbox-scope {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .ns-inbox-help {
    font-size: 11.5px;
    color: #475569;
    margin: 0 0 10px 0;
    line-height: 1.4;
  }
  .ns-email-wrap { position: relative; }
  .ns-email-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #64748b !important;
    pointer-events: none;
  }
  .ns-email-input {
    width: 100%;
    height: 38px;
    padding: 0 12px 0 36px;
    font-size: 12.5px;
    font-weight: 500;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    outline: none;
    transition: all 0.2s ease;
  }
  .ns-email-input:focus {
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
  }

  /* ── SUB-CARD B: SERVICES DELIVERED TO THIS EMAIL ── */
  .ns-services-section { display: flex; flex-direction: column; gap: 9px; flex: 1; }
  .ns-services-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    padding: 0 2px;
  }
  .ns-services-heading {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    letter-spacing: 0.4px;
    text-transform: uppercase;
  }
  .ns-dest-pill {
    font-size: 10.5px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .ns-dest-purple { background: #f5f3ff; color: #6d28d9; border: 1px solid #ede9fe; }
  .ns-dest-purple i { color: #7c3aed !important; }
  .ns-dest-emerald { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
  .ns-dest-emerald i { color: #059669 !important; }

  .ns-services-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .ns-service-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 11px;
    transition: all 0.16s ease;
  }
  .ns-service-subcard:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 7px rgba(15, 23, 42, 0.04);
  }
  .ns-service-icon {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
  }
  .ns-service-content { flex: 1; min-width: 0; }
  .ns-service-title-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
  .ns-service-num {
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    line-height: 1.4;
    flex-shrink: 0;
  }
  .ns-num-purple { background: #ede9fe; color: #6d28d9; }
  .ns-num-emerald { background: #d1fae5; color: #047857; }
  .ns-service-title {
    font-size: 12.5px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
  }
  .ns-service-desc {
    font-size: 11px;
    color: #64748b;
    margin: 2px 0 0 0;
    line-height: 1.38;
  }
  .ns-service-active {
    font-size: 13px;
    color: #10b981;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
  }
  .ns-service-active i { color: #10b981 !important; }

  /* ── BOTTOM ACTIVITY & TENANCY STRIP ── */
  .ns-bottom-strip {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 13px 18px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    flex-wrap: wrap;
  }
  .ns-strip-left { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
  .ns-tenancy-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
  }
  .ns-tenancy-badge i { color: #009ef7 !important; }
  .ns-sync-note { font-size: 11.5px; color: #64748b; display: flex; align-items: center; gap: 6px; }
  .ns-sync-note i { color: #f59e0b !important; }
  .ns-strip-actions { display: flex; align-items: center; gap: 10px; }
  .ns-btn-discard {
    height: 38px;
    padding: 0 16px;
    background: #f1f5f9;
    color: #475569 !important;
    font-size: 12.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .ns-btn-discard:hover { background: #e2e8f0; color: #0f172a !important; }

  /* ── 15.6" SCREEN FIT & MOBILE RESPONSIVE BREAKPOINTS ── */
  @media (max-width: 1080px) {
    .ns-channels-grid { grid-template-columns: 1fr; }
    .ns-channels-grid.ns-single-focus .ns-services-list { grid-template-columns: 1fr; }
  }
  @media (max-width: 600px) {
    .ns-toolbar-card { flex-direction: column; align-items: flex-start; }
    .ns-toolbar-right, .ns-search-box { width: 100%; }
  }
</style>

<script>
  $(document).ready(function() {
    // ── 1. Live 300ms Debounced Search Across Notification Services ──
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
        var matchCount = 0;

        $card.find('.notif-trigger-col').each(function() {
          var trigScope = $(this).attr('data-trigger-scope') || '';
          var matches = trigScope.indexOf(query) !== -1;
          $(this).toggle(matches);
          if (matches) matchCount++;
        });

        // Show card if any child service matched OR card-level scope/email text matched
        var emailMatch = ($card.find('.ns-email-input').val() || '').toLowerCase().indexOf(query) !== -1;
        if (matchCount > 0 || scope.indexOf(query) !== -1 || emailMatch) {
          $card.show();
          if (matchCount === 0 && (scope.indexOf(query) !== -1 || emailMatch)) {
            $card.find('.notif-trigger-col').show();
          }
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
      var $grid = $('#notifications_channels_grid');
      if (target === 'all') {
        $grid.removeClass('ns-single-focus');
        $('.primary-notifications-card').show();
      } else {
        $grid.addClass('ns-single-focus');
        $('.primary-notifications-card').each(function() {
          $(this).toggle($(this).attr('id') === target);
        });
      }
    });

    // ── 3. Header Save Button Triggers Primary Save Action ──
    $('#saveNotificationSettingsHeader').on('click', function() {
      $('#saveNotificationSettings').click();
    });

    // ── 4. Sync Destination Email Badges Dynamically & On Reset ──
    function syncNotificationDestinationBadges() {
      var recVal = $('#recuitment_email').val().trim() || 'Unconfigured';
      var salesVal = $('#sales_email').val().trim() || 'Unconfigured';
      $('.badge-rec-email').text(recVal);
      $('.badge-sales-email').text(salesVal);
    }

    $('#recuitment_email, #sales_email').on('input change', syncNotificationDestinationBadges);

    $('#notification_content').on('reset', function() {
      setTimeout(function() {
        syncNotificationDestinationBadges();
        $('.email-typo-hint').hide().empty();
      }, 0);
    });
  });
</script>
