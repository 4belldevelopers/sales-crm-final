{{-- 
  JERO ATS — Service Company Notification Email Routing Tab Component (Rule 20 Modern Card + Sub-Card Design System)
  Intent: 2-column channel-paired layout for Recruitment and Sales notifications with inline direct email editing,
          strict Add Candidate RFC 5321 validation, auto-restoration of Admin Email ID on empty submission,
          zero redundant header/footer controls, and guaranteed FontAwesome 5/6 glyph rendering.
  Tenancy & Security: Strictly scoped by company_id. Preserves recuitment_email & sales_email form keys,
                      JeroEmailBoxEngine client-first validation, and backend database/Elasticsearch synchronization.
  Screen Fit: 100% fluid fit for 15.6" screens (1366x768 & 1920x1080) with zero horizontal overflow.
--}}

@php
  $userPerms = $userPermissions ?? [];
  $hasViewPerm = in_array('view_settings', $userPerms);
  $hasEditPerm = in_array('edit_settings', $userPerms);
  $adminEmail = $account->company_email ?? '';

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

  $recruitmentEmailVal = (!empty($notification['Recuitment']->email)) ? $notification['Recuitment']->email : $adminEmail;
  $salesEmailVal = (!empty($notification['Sales']->email)) ? $notification['Sales']->email : $adminEmail;
@endphp

@if($hasViewPerm)
<form id="notification_content" class="form" method="post" novalidate="novalidate">
  @csrf
  <input type="hidden" name="pagename" value="settings">

  <div class="ns-wrapper notifications-card-system">

    {{-- ══════════════════════════════════════════════════════════════
         TIER 1: PURE WHITE HEADER SUMMARY CARD
         (Search bar, header save button, count pill & subtabs completely removed per spec)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-toolbar-card tier1-toolbar-card">
      <div class="ns-toolbar-left">
        <div class="ns-toolbar-icon-box">
          <i class="fas fa-bell"></i>
        </div>
        <div class="ns-toolbar-copy">
          <h3 class="ns-toolbar-title">Notification Email Routing</h3>
          <p class="ns-toolbar-sub">Configure and directly update primary recipient mailboxes for automated recruitment pipeline activities and commercial client alerts.</p>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 3: UNIFIED 2-COLUMN DOMAIN CARDS (EMAIL INLINE EDIT + 8 SERVICES)
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-channels-grid tier3-cards-stream" id="notifications_channels_grid">

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 1: RECRUITMENT NOTIFICATIONS
           ──────────────────────────────────────────────────────────── --}}
      <section class="ns-primary-card primary-notifications-card" id="card_notif_recruitment">
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
          {{-- Sub-Card A: Primary Recipient Email Input & Inline Direct Edit --}}
          <div class="ns-inbox-subcard ns-inbox-purple" id="box_recuitment_email">
            <div class="ns-inbox-head">
              <label class="ns-inbox-label" for="recuitment_email">
                Primary Recruitment Email ID <span class="ns-req">*</span>
              </label>
              <span class="ns-inbox-scope ns-badge-purple">
                <i class="fas fa-arrow-down"></i> Receives all 8 services below
              </span>
            </div>
            <p class="ns-inbox-help">Every automated recruitment alert listed below will be dispatched exclusively to this email address.</p>

            <div class="ns-email-interactive-group">
              <div class="ns-email-input-wrap">
                <i class="fas fa-envelope ns-email-icon"></i>
                <input type="email"
                       name="recuitment_email"
                       id="recuitment_email"
                       value="{{ $recruitmentEmailVal }}"
                       data-admin-fallback="{{ $adminEmail }}"
                       data-current-val="{{ $recruitmentEmailVal }}"
                       placeholder="Enter recruitment email (e.g., talent@company.com)"
                       class="ns-email-input is-locked"
                       readonly
                       required
                       maxlength="254"
                       inputmode="email"
                       autocomplete="email"
                       autocapitalize="none"
                       spellcheck="false">
              </div>

              <div class="ns-inline-btn-group">
                @if($hasEditPerm)
                {{-- Default View State: Edit Action --}}
                <button type="button" class="ns-btn-inline ns-btn-edit" data-target="recuitment_email" title="Edit Recruitment Email">
                  <i class="fas fa-pencil-alt"></i> <span>Edit</span>
                </button>

                {{-- Active Edit State: Save & Cancel Actions (Hidden initially) --}}
                <div class="ns-edit-actions-cluster" style="display: none;">
                  <button type="button" class="ns-btn-inline ns-btn-save-inline" data-target="recuitment_email" title="Save Recruitment Email">
                    <span class="ns-inline-spinner" style="display: none;"></span>
                    <i class="fas fa-check"></i> <span>Save</span>
                  </button>
                  <button type="button" class="ns-btn-inline ns-btn-cancel-inline" data-target="recuitment_email" title="Cancel Editing">
                    <i class="fas fa-times"></i> <span>Cancel</span>
                  </button>
                </div>
                @endif
              </div>
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
                <div class="ns-service-subcard">
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
      <section class="ns-primary-card primary-notifications-card" id="card_notif_sales">
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
          {{-- Sub-Card A: Primary Recipient Email Input & Inline Direct Edit --}}
          <div class="ns-inbox-subcard ns-inbox-emerald" id="box_sales_email">
            <div class="ns-inbox-head">
              <label class="ns-inbox-label" for="sales_email">
                Primary Sales Email ID <span class="ns-req">*</span>
              </label>
              <span class="ns-inbox-scope ns-badge-emerald">
                <i class="fas fa-arrow-down"></i> Receives all 8 services below
              </span>
            </div>
            <p class="ns-inbox-help">Every automated sales, client, and billing alert listed below will be dispatched exclusively to this email address.</p>

            <div class="ns-email-interactive-group">
              <div class="ns-email-input-wrap">
                <i class="fas fa-envelope ns-email-icon"></i>
                <input type="email"
                       name="sales_email"
                       id="sales_email"
                       value="{{ $salesEmailVal }}"
                       data-admin-fallback="{{ $adminEmail }}"
                       data-current-val="{{ $salesEmailVal }}"
                       placeholder="Enter sales email (e.g., business@company.com)"
                       class="ns-email-input is-locked"
                       readonly
                       required
                       maxlength="254"
                       inputmode="email"
                       autocomplete="email"
                       autocapitalize="none"
                       spellcheck="false">
              </div>

              <div class="ns-inline-btn-group">
                @if($hasEditPerm)
                {{-- Default View State: Edit Action --}}
                <button type="button" class="ns-btn-inline ns-btn-edit" data-target="sales_email" title="Edit Sales Email">
                  <i class="fas fa-pencil-alt"></i> <span>Edit</span>
                </button>

                {{-- Active Edit State: Save & Cancel Actions (Hidden initially) --}}
                <div class="ns-edit-actions-cluster" style="display: none;">
                  <button type="button" class="ns-btn-inline ns-btn-save-inline" data-target="sales_email" title="Save Sales Email">
                    <span class="ns-inline-spinner" style="display: none;"></span>
                    <i class="fas fa-check"></i> <span>Save</span>
                  </button>
                  <button type="button" class="ns-btn-inline ns-btn-cancel-inline" data-target="sales_email" title="Cancel Editing">
                    <i class="fas fa-times"></i> <span>Cancel</span>
                  </button>
                </div>
                @endif
              </div>
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
                <div class="ns-service-subcard">
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

  /* ── TIER 1: HEADER SUMMARY CARD ── */
  .ns-toolbar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
  }
  .ns-toolbar-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
  .ns-toolbar-copy { min-width: 0; }
  .ns-toolbar-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #eff6ff;
    color: #009ef7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid #dbeafe;
    flex-shrink: 0;
  }
  .ns-toolbar-icon-box i { color: #009ef7 !important; }
  .ns-toolbar-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
  .ns-toolbar-sub { font-size: 12.5px; color: #64748b; margin: 3px 0 0 0; line-height: 1.4; }

  /* ── TIER 3: 2-COLUMN UNIFIED PRIMARY CARDS ── */
  .ns-channels-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    align-items: stretch;
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

  /* ── SUB-CARD A: EMAIL DESTINATION INBOX & INLINE ACTIONS ── */
  .ns-inbox-subcard {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 9px;
    padding: 14px 15px;
    transition: border-color 0.2s ease, background 0.2s ease;
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

  /* ── INLINE EMAIL INTERACTIVE GROUP ── */
  .ns-email-interactive-group {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
  }
  .ns-email-input-wrap {
    flex: 1;
    position: relative;
    min-width: 0;
  }
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
    border-radius: 7px;
    outline: none;
    transition: all 0.2s ease;
    border: 1px solid #cbd5e1;
  }
  .ns-email-input.is-locked {
    background: #f1f5f9;
    color: #1e293b;
    border-color: #e2e8f0;
    cursor: default;
  }
  .ns-email-input.is-editing {
    background: #ffffff;
    color: #0f172a;
    border-color: #009ef7;
    box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
  }
  .ns-email-input.has-error {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
  }

  .ns-inline-btn-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }
  .ns-btn-inline {
    height: 38px;
    padding: 0 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s ease;
    white-space: nowrap;
    border: 1px solid transparent;
    outline: none;
  }
  .ns-btn-edit {
    background: #eff6ff;
    color: #009ef7;
    border-color: #bae6fd;
  }
  .ns-btn-edit:hover {
    background: #009ef7;
    color: #ffffff;
    border-color: #009ef7;
  }
  .ns-btn-edit i { color: inherit !important; font-size: 11.5px; }

  .ns-edit-actions-cluster {
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .ns-btn-save-inline {
    background: #009ef7;
    color: #ffffff;
    border-color: #009ef7;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
  }
  .ns-btn-save-inline:hover {
    background: #0086d4;
    border-color: #0086d4;
    box-shadow: 0 4px 8px rgba(0, 158, 247, 0.28);
  }
  .ns-btn-save-inline i { color: #ffffff !important; font-size: 11px; }

  .ns-btn-cancel-inline {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
  }
  .ns-btn-cancel-inline:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
  }
  .ns-btn-cancel-inline i { color: #64748b !important; font-size: 11px; }

  .ns-inline-spinner {
    width: 13px;
    height: 13px;
    border: 2px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: nsInlineSpin 0.6s linear infinite;
    display: inline-block;
  }
  @keyframes nsInlineSpin { to { transform: rotate(360deg); } }

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

  /* ── 15.6" SCREEN FIT & MOBILE RESPONSIVE BREAKPOINTS ── */
  @media (max-width: 1080px) {
    .ns-channels-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 600px) {
    .ns-email-interactive-group { flex-direction: column; align-items: stretch; }
    .ns-inline-btn-group { justify-content: flex-end; }
  }
</style>

<script>
  $(document).ready(function() {
    var adminCompanyEmail = "{{ addslashes($adminEmail) }}";

    // ── 1. Inline Edit Mode Toggle ──
    $(document).on('click', '.ns-btn-edit', function(e) {
      e.preventDefault();
      var target = $(this).data('target');
      var $container = $('#box_' + target);
      var $input = $('#' + target);

      // Store pre-edit value for Cancel restoration
      $input.data('pre-edit-val', $input.val());

      // Unlock input for editing
      $input.prop('readonly', false).removeClass('is-locked').addClass('is-editing').focus();
      var len = $input.val().length;
      if ($input[0].setSelectionRange) {
        $input[0].setSelectionRange(len, len);
      }

      // Toggle button cluster
      $container.find('.ns-btn-edit').hide();
      $container.find('.ns-edit-actions-cluster').show();
    });

    // ── 2. Inline Cancel Mode ──
    $(document).on('click', '.ns-btn-cancel-inline', function(e) {
      e.preventDefault();
      var target = $(this).data('target');
      var $container = $('#box_' + target);
      var $input = $('#' + target);
      var original = $input.data('current-val') || $input.data('pre-edit-val') || '';

      // Revert value
      $input.val(original);
      $input.prop('readonly', true).removeClass('is-editing has-error').addClass('is-locked');

      // Clear typo/validation messages
      $('#' + target + '_typo_hint').hide().empty();

      // Toggle buttons back
      $container.find('.ns-edit-actions-cluster').hide();
      $container.find('.ns-btn-edit').show();
    });

    // ── 3. Real-time Cleaning, Syntax Validation & Typo Detection (Add Candidate standard) ──
    $(document).on('paste', '#recuitment_email, #sales_email', function() {
      var self = this;
      setTimeout(function() {
        var raw = $(self).val();
        var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
        if (clean !== raw) {
          $(self).val(clean).trigger('input');
        }
      }, 10);
    });

    $(document).on('blur', '#recuitment_email, #sales_email', function() {
      if ($(this).prop('readonly')) return;
      var targetId = $(this).attr('id');
      var raw = $(this).val();
      var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
      if (clean !== raw) {
        $(this).val(clean);
      }
      var $hint = $('#' + targetId + '_typo_hint');
      if (!clean) {
        $hint.hide().empty();
        return;
      }
      if (window.JeroEmailBoxEngine) {
        var syntax = window.JeroEmailBoxEngine.validateSyntax(clean);
        if (!syntax.isValid) {
          $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
          return;
        }
        var suggestion = window.JeroEmailBoxEngine.getSuggestion(clean);
        if (suggestion) {
          $hint.html('<i class="fa fa-lightbulb text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix fw-bold text-primary ms-1" data-target="#' + targetId + '" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
        } else {
          $hint.hide().empty();
        }
      }
    });

    // ── 4. Typo Suggestion 'Apply' Click ──
    $(document).on('click', '.apply-email-fix', function(e) {
      e.preventDefault();
      var target = $(this).data('target');
      var fixed = $(this).data('fixed');
      if (target && fixed) {
        $(target).val(fixed).trigger('input').trigger('change').removeClass('has-error');
        $(this).closest('.email-typo-hint').hide().empty();
      }
    });

    // ── 5. Inline Save Action with Add Candidate Validation & Empty Fallback ──
    $(document).on('click', '.ns-btn-save-inline', function(e) {
      e.preventDefault();
      var $saveBtn = $(this);
      var targetField = $saveBtn.data('target');
      var $container = $('#box_' + targetField);
      var $input = $('#' + targetField);
      var $hint = $('#' + targetField + '_typo_hint');
      var fallbackEmail = $input.data('admin-fallback') || adminCompanyEmail;
      var rawVal = $input.val();
      var cleanVal = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(rawVal) : (rawVal || '').trim();

      // Rule: Email ID must be mandatory and cannot be left empty.
      // If user attempts to save an empty email field, display clear validation notification & auto-restore Admin Email ID.
      if (!cleanVal || cleanVal === '') {
        $input.val(fallbackEmail).addClass('has-error');
        $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> <strong>Email is mandatory.</strong> Automatically restored to original Admin Email ID (' + fallbackEmail + ').').show();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Email Address Mandatory',
            text: 'Email ID cannot be left empty. It has been automatically restored to your organization Admin Email ID (' + fallbackEmail + ').',
            confirmButtonColor: '#009ef7',
            confirmButtonText: 'Understood'
          });
        }
        return;
      }

      // Standard Add Candidate RFC 5321 Syntax Validation
      if (window.JeroEmailBoxEngine) {
        var syntax = window.JeroEmailBoxEngine.validateSyntax(cleanVal);
        if (!syntax.isValid) {
          $input.addClass('has-error').focus();
          $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Invalid Email Address',
              text: syntax.error,
              confirmButtonColor: '#009ef7'
            });
          }
          return;
        }
      } else {
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(cleanVal)) {
          $input.addClass('has-error').focus();
          $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> The value is not a valid email address.').show();
          return;
        }
      }

      $input.removeClass('has-error');
      $hint.hide().empty();

      // Build payload
      var postData = new FormData();
      postData.append('_token', $('meta[name="csrf-token"]').attr('content'));
      postData.append('pagename', 'settings');
      var recEmailVal = (targetField === 'recuitment_email') ? cleanVal : ($('#recuitment_email').val() || fallbackEmail);
      var salesEmailVal = (targetField === 'sales_email') ? cleanVal : ($('#sales_email').val() || fallbackEmail);
      postData.append('recuitment_email', recEmailVal);
      postData.append('sales_email', salesEmailVal);

      // UI Loading State
      $saveBtn.prop('disabled', true);
      $saveBtn.find('.ns-inline-spinner').show();
      $saveBtn.find('i.fa-check').hide();
      $saveBtn.find('span:not(.ns-inline-spinner)').text('Saving...');

      $.ajax({
        url: '{{ !empty($account->id) ? route('service_company.account.update', $account->id) : url('service_company/account/update/0') }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: postData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status === 'warning') {
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'warning',
                title: 'Validation Notice',
                text: response.message || 'Validation warning encountered.',
                confirmButtonColor: '#009ef7'
              });
            }
            return;
          }

          if (response.status === 'success') {
            // Update active cached value
            $input.val(cleanVal);
            $input.data('current-val', cleanVal);
            $input.prop('readonly', true).removeClass('is-editing has-error').addClass('is-locked');

            // Update live destination badge pill
            if (targetField === 'recuitment_email') {
              $('.badge-rec-email').text(cleanVal);
            } else {
              $('.badge-sales-email').text(cleanVal);
            }

            // Toggle buttons back to Edit mode
            $container.find('.ns-edit-actions-cluster').hide();
            $container.find('.ns-btn-edit').show();

            // Clean feedback
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: (targetField === 'recuitment_email' ? 'Recruitment' : 'Sales') + ' email updated successfully.',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
              });
            }
            return;
          }

          if (typeof Swal !== 'undefined') {
            Swal.fire('Unable to save', response.message || 'Please try again.', 'error');
          }
        },
        error: function(xhr) {
          var res = xhr.responseJSON || {};
          var msg = res.message || 'Email could not be saved. Please try again.';
          if (typeof Swal !== 'undefined') {
            Swal.fire('Unable to save', msg, 'error');
          }
        },
        complete: function() {
          $saveBtn.prop('disabled', false);
          $saveBtn.find('.ns-inline-spinner').hide();
          $saveBtn.find('i.fa-check').show();
          $saveBtn.find('span:not(.ns-inline-spinner)').text('Save');
        }
      });
    });
  });
</script>
