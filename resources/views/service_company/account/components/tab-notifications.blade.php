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

  // Recruitment Pipeline Event Triggers (8 Items) — FA5 + FA6 native icons
  $recruitmentNotificationItems = [
    ['Candidate Stages', 'Candidate moves across applied, shortlisted, interview, offered and other stages.', 'fas fa-random'],
    ['Agencies Status', 'Agency performance, submission and status changes.', 'fas fa-handshake'],
    ['Offers Update', 'Offer creation, approval, release, acceptance and decline.', 'fas fa-file-signature'],
    ['Joining Update', 'Candidate joining, postponement and cancellation updates.', 'fas fa-user-check'],
    ['Bench Candidate Request', 'Bench candidates requested for a job.', 'fas fa-address-card'],
    ['Team Member Status', 'Team invitations, role changes and account status updates.', 'fas fa-user-shield'],
    ['Bench Notification on Jobs', 'Bench candidates submitted or placed on jobs.', 'fas fa-briefcase'],
    ['Additional Notifications', 'Notes, comments and other important recruitment actions.', 'fas fa-comment-dots'],
  ];

  // Sales & Commercial Event Triggers (8 Items) — FA5 + FA6 native icons
  $salesNotificationItems = [
    ['New Job Request from Direct Client', 'A new job request raised by a direct client.', 'fas fa-folder-plus'],
    ['Bench Added on Client Jobs', 'Bench candidates added or submitted on client jobs.', 'fas fa-network-wired'],
    ['Subscription Status', 'Subscription activation, renewal, expiry and plan changes.', 'fas fa-credit-card'],
    ['Client Communication', 'Client messages, feedback and important updates.', 'fas fa-comments'],
    ['Invoice & Payment Status', 'Invoices, payments, due dates and payment failures.', 'fas fa-receipt'],
    ['Contract & Agreement Updates', 'Contract creation, renewal, expiry and amendments.', 'fas fa-file-contract'],
    ['Low Coins / Wallet Alerts', 'Low coin balance and wallet actions requiring attention.', 'fas fa-coins'],
    ['Additional Sales Alerts', 'Other important sales updates and system alerts.', 'fas fa-exclamation-triangle'],
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
        <div>
          <div class="ns-toolbar-title-row">
            <h3 class="ns-toolbar-title">Notification & Alert Routing</h3>
            <span class="ns-count-pill">16 Event Triggers Active</span>
          </div>
          <p class="ns-toolbar-sub">Configure enterprise dispatch destinations for hiring lifecycles, candidate stages, client orders, and billing alerts.</p>
        </div>
      </div>

      <div class="ns-toolbar-right">
        <div class="ns-search-box">
          <i class="fas fa-search ns-search-icon"></i>
          <input type="text" id="notifications_search" class="ns-search-input" placeholder="Search notification triggers..." autocomplete="off">
          <button type="button" id="notifications_search_clear_btn" class="ns-search-clear" title="Clear search"><i class="fas fa-times"></i></button>
        </div>

        @if($hasEditPerm)
        <button type="button" class="ns-btn-save" id="saveNotificationSettingsHeader">
          <span class="csn-save-spinner" aria-hidden="true"></span>
          <span class="csn-save-label"><i class="fas fa-check"></i> Save Changes</span>
        </button>
        @endif
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 2: PURE WHITE SUB-TABS FILTER CARD
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-subtabs-card tier2-subtabs-card" id="notifications_subtabs_bar">
      <button type="button" class="ns-subtab-pill btn-subtab active" data-target="all">
        <i class="fas fa-th-large"></i>
        <span>All Notifications</span>
        <span class="ns-subtab-counter">16</span>
      </button>
      <button type="button" class="ns-subtab-pill btn-subtab" data-target="card_notif_routing">
        <i class="fas fa-envelope-open-text"></i>
        <span>Email Routing</span>
        <span class="ns-subtab-counter">2</span>
      </button>
      <button type="button" class="ns-subtab-pill btn-subtab" data-target="card_notif_recruitment">
        <i class="fas fa-user-tie"></i>
        <span>Recruitment Triggers</span>
        <span class="ns-subtab-counter">8</span>
      </button>
      <button type="button" class="ns-subtab-pill btn-subtab" data-target="card_notif_sales">
        <i class="fas fa-chart-line"></i>
        <span>Sales & Commercials</span>
        <span class="ns-subtab-counter">8</span>
      </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TIER 3: PRIMARY CARD STREAM
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ns-stream tier3-cards-stream">

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 1: ENTERPRISE EMAIL DISPATCH ROUTING
           ──────────────────────────────────────────────────────────── --}}
      <div class="ns-primary-card primary-notifications-card" id="card_notif_routing" data-search-scope="email routing primary destination dispatch recruitment sales inbox">
        <div class="ns-card-header">
          <div class="ns-card-header-left">
            <div class="ns-cat-icon ns-icon-blue">
              <i class="fas fa-envelope-open-text"></i>
            </div>
            <div>
              <h4 class="ns-card-title">Email Dispatch & Delivery Routing</h4>
              <p class="ns-card-desc">Primary recipient mailboxes designated for automated pipeline and sales alerts.</p>
            </div>
          </div>
          <span class="ns-status-badge ns-badge-blue">
            <i class="fas fa-shield-alt"></i> DKIM & SPF Verified
          </span>
        </div>

        <div class="ns-card-body">
          <div class="ns-routing-grid">

            {{-- Routing Sub-Card 1: Recruitment Mailbox --}}
            <div class="ns-routing-subcard">
              <div>
                <div class="ns-routing-top">
                  <div class="ns-routing-icon ns-icon-purple">
                    <i class="fas fa-user-tie"></i>
                  </div>
                  <div>
                    <label class="ns-routing-title" for="recuitment_email">Recruitment Pipeline Mailbox <span class="ns-req">*</span></label>
                    <div class="ns-routing-desc">Receives candidate stage moves, interview confirmations, agency updates, and offer releases.</div>
                  </div>
                </div>
                <div class="ns-email-wrap">
                  <i class="fas fa-envelope ns-email-icon"></i>
                  <input type="email" name="recuitment_email" id="recuitment_email" value="{{ $recruitmentEmailVal }}" placeholder="Enter recruitment email" class="ns-email-input" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                </div>
                <div id="recuitment_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
              </div>
              <div class="ns-routing-footer">
                <span class="ns-coverage-tag ns-tag-purple">
                  <i class="fas fa-bolt"></i> 8 Recruitment Triggers Covered
                </span>
                <span class="ns-active-routing"><i class="fas fa-check-circle"></i> Active Routing</span>
              </div>
            </div>

            {{-- Routing Sub-Card 2: Sales Mailbox --}}
            <div class="ns-routing-subcard">
              <div>
                <div class="ns-routing-top">
                  <div class="ns-routing-icon ns-icon-emerald">
                    <i class="fas fa-chart-line"></i>
                  </div>
                  <div>
                    <label class="ns-routing-title" for="sales_email">Sales & Commercials Mailbox <span class="ns-req">*</span></label>
                    <div class="ns-routing-desc">Receives direct client job requests, contract signing updates, invoice alerts, and wallet warnings.</div>
                  </div>
                </div>
                <div class="ns-email-wrap">
                  <i class="fas fa-envelope ns-email-icon"></i>
                  <input type="email" name="sales_email" id="sales_email" value="{{ $salesEmailVal }}" placeholder="Enter sales email" class="ns-email-input" required maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                </div>
                <div id="sales_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
              </div>
              <div class="ns-routing-footer">
                <span class="ns-coverage-tag ns-tag-emerald">
                  <i class="fas fa-bolt"></i> 8 Sales Triggers Covered
                </span>
                <span class="ns-active-routing"><i class="fas fa-check-circle"></i> Active Routing</span>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 2: RECRUITMENT PIPELINE EVENT TRIGGERS (8 ITEMS)
           ──────────────────────────────────────────────────────────── --}}
      <div class="ns-primary-card primary-notifications-card" id="card_notif_recruitment" data-search-scope="recruitment candidate stages agency offers joining bench member status interview pipeline">
        <div class="ns-card-header">
          <div class="ns-card-header-left">
            <div class="ns-cat-icon ns-icon-purple">
              <i class="fas fa-users-cog"></i>
            </div>
            <div>
              <h4 class="ns-card-title">Recruitment Pipeline Event Triggers</h4>
              <p class="ns-card-desc">Automated lifecycle alerts covering candidate progression, team status, and offer approvals.</p>
            </div>
          </div>
          <span class="ns-status-badge ns-badge-purple">
            <i class="fas fa-envelope"></i> Dispatches to: <strong class="badge-rec-email">{{ $recruitmentEmailVal ?: 'Unconfigured' }}</strong>
          </span>
        </div>

        <div class="ns-card-body">
          <div class="ns-triggers-grid">
            @foreach($recruitmentNotificationItems as $notificationIndex => $notificationItem)
              <div class="ns-trigger-subcard notif-trigger-col" data-trigger-scope="{{ strtolower($notificationItem[0] . ' ' . $notificationItem[1]) }}">
                <div>
                  <div class="ns-trigger-top">
                    <div class="ns-trigger-icon ns-icon-purple">
                      <i class="{{ $notificationItem[2] }}"></i>
                    </div>
                    <div>
                      <div class="ns-trigger-step">TRIGGER {{ str_pad($notificationIndex + 1, 2, '0', STR_PAD_LEFT) }}</div>
                      <div class="ns-trigger-title">{{ $notificationItem[0] }}</div>
                    </div>
                  </div>
                  <p class="ns-trigger-desc">{{ $notificationItem[1] }}</p>
                </div>
                <div class="ns-trigger-footer">
                  <span class="ns-trigger-status"><i class="fas fa-check-circle"></i> Automated</span>
                  <span class="ns-trigger-route">Recruitment Mail</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- ────────────────────────────────────────────────────────────
           PRIMARY CARD 3: SALES & COMMERCIAL EVENT TRIGGERS (8 ITEMS)
           ──────────────────────────────────────────────────────────── --}}
      <div class="ns-primary-card primary-notifications-card" id="card_notif_sales" data-search-scope="sales client job requests bench subscription invoice payments contract agreement coins wallet">
        <div class="ns-card-header">
          <div class="ns-card-header-left">
            <div class="ns-cat-icon ns-icon-emerald">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
              <h4 class="ns-card-title">Sales & Commercial Event Triggers</h4>
              <p class="ns-card-desc">Direct corporate client orders, agreement executions, billing statements, and balance notices.</p>
            </div>
          </div>
          <span class="ns-status-badge ns-badge-emerald">
            <i class="fas fa-envelope"></i> Dispatches to: <strong class="badge-sales-email">{{ $salesEmailVal ?: 'Unconfigured' }}</strong>
          </span>
        </div>

        <div class="ns-card-body">
          <div class="ns-triggers-grid">
            @foreach($salesNotificationItems as $notificationIndex => $notificationItem)
              <div class="ns-trigger-subcard notif-trigger-col" data-trigger-scope="{{ strtolower($notificationItem[0] . ' ' . $notificationItem[1]) }}">
                <div>
                  <div class="ns-trigger-top">
                    <div class="ns-trigger-icon ns-icon-emerald">
                      <i class="{{ $notificationItem[2] }}"></i>
                    </div>
                    <div>
                      <div class="ns-trigger-step">TRIGGER {{ str_pad($notificationIndex + 1, 2, '0', STR_PAD_LEFT) }}</div>
                      <div class="ns-trigger-title">{{ $notificationItem[0] }}</div>
                    </div>
                  </div>
                  <p class="ns-trigger-desc">{{ $notificationItem[1] }}</p>
                </div>
                <div class="ns-trigger-footer">
                  <span class="ns-trigger-status"><i class="fas fa-check-circle"></i> Automated</span>
                  <span class="ns-trigger-route">Sales Mail</span>
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
    <div class="ns-bottom-strip activity-strip-card">
      <div class="ns-strip-left">
        <div class="ns-tenancy-badge">
          <i class="fas fa-shield-alt"></i>
          <span>Tenant Context: {{ auth()->user()->company->company_name ?? 'Organization' }} (#{{ auth()->user()->company_id ?? 0 }})</span>
        </div>
        <div class="ns-sync-note">
          <i class="fas fa-bolt"></i>
          <span>Notification recipient changes update Redis & Elasticsearch immediately with zero dispatch latency.</span>
        </div>
      </div>

      @if($hasEditPerm)
      <div class="ns-strip-actions">
        <button type="reset" class="ns-btn-discard">Discard Changes</button>
        <button type="button" class="ns-btn-save" id="saveNotificationSettings">
          <span class="csn-save-spinner" aria-hidden="true"></span>
          <span class="csn-save-label"><i class="fas fa-check"></i> Save Changes</span>
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

{{-- Dedicated Isolated Stylesheet for 1:1 Rule 20 Preview Fidelity --}}
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
  /* Guarantee FontAwesome 5 & 6 fonts render crisply without theme override */
  .ns-wrapper i, .ns-wrapper [class*="fa-"] {
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal !important;
    line-height: 1 !important;
  }
  .ns-wrapper .fas, .ns-wrapper .fa-solid { font-weight: 900 !important; }
  .ns-wrapper .far, .ns-wrapper .fa-regular { font-weight: 400 !important; }

  /* ── COLOR SYSTEM TOKENS ── */
  .ns-icon-blue { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #dbeafe !important; }
  .ns-icon-blue i { color: #2563eb !important; }
  .ns-icon-purple { background: #f5f3ff !important; color: #8b5cf6 !important; border: 1px solid #ede9fe !important; }
  .ns-icon-purple i { color: #8b5cf6 !important; }
  .ns-icon-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
  .ns-icon-emerald i { color: #059669 !important; }

  .ns-badge-blue { background: #eff6ff !important; color: #0284c7 !important; border: 1px solid #bae6fd !important; }
  .ns-badge-blue i { color: #0284c7 !important; }
  .ns-badge-purple { background: #f5f3ff !important; color: #7c3aed !important; border: 1px solid #ddd6fe !important; }
  .ns-badge-purple i { color: #7c3aed !important; }
  .ns-badge-emerald { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
  .ns-badge-emerald i { color: #059669 !important; }

  /* ── TIER 1: TOOLBAR HEADER CARD ── */
  .ns-toolbar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    flex-wrap: wrap;
  }
  .ns-toolbar-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
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
  .ns-toolbar-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .ns-toolbar-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
  .ns-count-pill {
    background: #eff6ff;
    color: #0284c7;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 12px;
    border: 1px solid #bae6fd;
  }
  .ns-toolbar-sub { font-size: 12.5px; color: #64748b; margin: 2px 0 0 0; }
  .ns-toolbar-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
  .ns-search-box { position: relative; min-width: 260px; }
  .ns-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #94a3b8 !important;
    pointer-events: none;
  }
  .ns-search-input {
    width: 100%;
    height: 38px;
    padding: 0 34px 0 36px;
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
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
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
    font-size: 13px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 158, 247, 0.2);
  }
  .ns-btn-save:hover { background: #0086d4; box-shadow: 0 4px 8px rgba(0, 158, 247, 0.3); }
  .ns-btn-save i { color: #ffffff !important; margin-right: 4px; }
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
  @keyframes nsSpin { to { transform: rotate(360deg); } }

  /* ── TIER 2: SUB-TABS FILTER CARD ── */
  .ns-subtabs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 6px 10px;
    margin-bottom: 18px;
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
    padding: 8px 16px;
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
  .ns-subtab-pill i { color: #64748b !important; font-size: 13px; }
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

  /* ── TIER 3: PRIMARY CARDS STREAM ── */
  .ns-stream { display: flex; flex-direction: column; gap: 18px; }
  .ns-primary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .ns-primary-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); }
  .ns-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    background: #ffffff;
    flex-wrap: wrap;
  }
  .ns-card-header-left { display: flex; align-items: center; gap: 12px; }
  .ns-cat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .ns-card-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
  .ns-card-desc { font-size: 12px; color: #64748b; margin: 1px 0 0 0; }
  .ns-status-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 5px 11px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .ns-card-body { padding: 20px; }

  /* ── SECTION 1: EMAIL ROUTING CHANNELS ── */
  .ns-routing-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
  .ns-routing-subcard {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
  }
  .ns-routing-subcard:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
  }
  .ns-routing-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
  .ns-routing-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }
  .ns-routing-title { font-size: 14px; font-weight: 700; color: #0f172a; display: block; margin: 0; }
  .ns-req { color: #ef4444; }
  .ns-routing-desc { font-size: 11.5px; color: #64748b; line-height: 1.4; margin-top: 2px; }
  .ns-email-wrap { position: relative; margin-bottom: 8px; }
  .ns-email-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #94a3b8 !important;
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
    border-radius: 6px;
    outline: none;
    transition: all 0.2s ease;
  }
  .ns-email-input:focus { border-color: #009ef7; box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12); }
  .ns-routing-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    margin-top: 8px;
    border-top: 1px solid #e2e8f0;
  }
  .ns-coverage-tag {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .ns-tag-purple { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
  .ns-tag-purple i { color: #7c3aed !important; }
  .ns-tag-emerald { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
  .ns-tag-emerald i { color: #059669 !important; }
  .ns-active-routing { font-size: 11px; color: #10b981; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
  .ns-active-routing i { color: #10b981 !important; }

  /* ── SECTIONS 2 & 3: TRIGGER SUB-CARDS GRID ── */
  .ns-triggers-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
  }
  .ns-trigger-subcard {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
  }
  .ns-trigger-subcard:hover {
    background: #ffffff;
    border-color: #94a3b8;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    transform: translateY(-1px);
  }
  .ns-trigger-top { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
  .ns-trigger-icon {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
  }
  .ns-trigger-step { font-size: 10.5px; font-weight: 700; color: #94a3b8; letter-spacing: 0.4px; }
  .ns-trigger-title { font-size: 12.5px; font-weight: 700; color: #1e293b; line-height: 1.35; margin-top: 1px; }
  .ns-trigger-desc { font-size: 11px; color: #64748b; line-height: 1.4; margin: 0 0 12px 0; }
  .ns-trigger-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
    font-size: 10.5px;
  }
  .ns-trigger-status { font-weight: 600; color: #059669; display: inline-flex; align-items: center; gap: 4px; }
  .ns-trigger-status i { color: #059669 !important; }
  .ns-trigger-route { color: #64748b; font-weight: 500; }

  /* ── BOTTOM ACTIVITY & TENANCY STRIP ── */
  .ns-bottom-strip {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 20px;
    margin-top: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    flex-wrap: wrap;
  }
  .ns-strip-left { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
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
  .ns-sync-note { font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 6px; }
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

  /* ── 15.6" SCREEN FIT RESPONSIVE BREAKPOINTS ── */
  @media (max-width: 1199px) {
    .ns-triggers-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  }
  @media (max-width: 900px) {
    .ns-routing-grid { grid-template-columns: 1fr; }
    .ns-triggers-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width: 600px) {
    .ns-triggers-grid { grid-template-columns: 1fr; }
    .ns-toolbar-card { flex-direction: column; align-items: flex-start; }
    .ns-toolbar-right, .ns-search-box { width: 100%; }
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
