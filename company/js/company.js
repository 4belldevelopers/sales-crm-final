/**
 * JERO ATS — Enterprise Company Workspace Frontend Engine
 * Client-Side screen router, filters, modals, and interactive UI logic.
 * Zero backend dependency.
 */

(function () {
  'use strict';

  const validScreens = [
    'dashboard',
    'jobs',
    'job-requests',
    'job-create',
    'candidates',
    'pipeline',
    'partners',
    'interviews',
    'payouts',
    'team',
    'profile',
    'settings',
    'login',
    'register'
  ];

  const screenMeta = {
    'dashboard': { title: 'Enterprise Hiring Dashboard', parent: 'Company Portal' },
    'jobs': { title: 'Active Mandates & Requisitions', parent: 'Jobs' },
    'job-requests': { title: 'Outbound Job Requests & Mandates', parent: 'Talent Acquisition' },
    'job-create': { title: 'Create & Distribute Mandate', parent: 'Jobs' },
    'candidates': { title: 'Enterprise Candidate Bank', parent: 'Candidates' },
    'pipeline': { title: 'ATS Sourcing Pipeline', parent: 'Applications' },
    'partners': { title: 'Freelance & Agency Recruiter Network', parent: 'Partners' },
    'interviews': { title: 'Interview Evaluation & Scheduling', parent: 'Interviews' },
    'payouts': { title: 'Commission Payout & Guarantee Approvals', parent: 'Billing' },
    'team': { title: 'Internal Recruiting Team & Access', parent: 'Organization' },
    'profile': { title: 'Company Profile & Employer Branding', parent: 'Organization' },
    'settings': { title: 'Company Settings & ATS Workflows', parent: 'Settings' },
    'login': { title: 'Company Sign In', parent: 'Authentication' },
    'register': { title: 'Employer Registration', parent: 'Authentication' }
  };

  function navigateTo(screenId, pushHash = true) {
    if (!validScreens.includes(screenId)) {
      screenId = 'dashboard';
    }

    document.querySelectorAll('.cw-view-screen').forEach(el => {
      el.classList.remove('active');
    });

    const targetEl = document.getElementById('view-' + screenId);
    if (targetEl) {
      targetEl.classList.add('active');
    }

    document.querySelectorAll('.cw-nav-item').forEach(nav => {
      if (nav.getAttribute('data-screen') === screenId) {
        nav.classList.add('active');
      } else {
        nav.classList.remove('active');
      }
    });

    const meta = screenMeta[screenId] || { title: 'Company Workspace', parent: 'JERO' };
    const breadcrumbActive = document.getElementById('cw-breadcrumb-active');
    const breadcrumbParent = document.getElementById('cw-breadcrumb-parent');
    if (breadcrumbActive) breadcrumbActive.textContent = meta.title;
    if (breadcrumbParent) breadcrumbParent.textContent = meta.parent;

    const shell = document.querySelector('.cw-app-wrapper');
    const authWrapper = document.getElementById('cw-auth-screens-wrapper');

    if (screenId === 'login' || screenId === 'register') {
      if (shell) shell.style.display = 'none';
      if (authWrapper) authWrapper.style.display = 'flex';
      document.querySelectorAll('.cw-auth-screen').forEach(a => a.style.display = 'none');
      const authTarget = document.getElementById('auth-view-' + screenId);
      if (authTarget) authTarget.style.display = 'block';
    } else {
      if (shell) shell.style.display = 'flex';
      if (authWrapper) authWrapper.style.display = 'none';
    }

    if (pushHash && window.location.hash !== '#' + screenId) {
      window.location.hash = screenId;
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function initNavigation() {
    window.addEventListener('hashchange', () => {
      const hash = window.location.hash.replace('#', '') || 'dashboard';
      navigateTo(hash, false);
    });

    document.addEventListener('click', (e) => {
      const target = e.target.closest('[data-screen]');
      if (target) {
        e.preventDefault();
        const screen = target.getAttribute('data-screen');
        navigateTo(screen);
      }
    });

    const toggleBtn = document.getElementById('cw-sidebar-toggle');
    const sidebar = document.querySelector('.cw-sidebar');
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
      });
    }
  }

  function initSubTabs() {
    document.querySelectorAll('.cw-subtabs-card').forEach(tabGroup => {
      tabGroup.addEventListener('click', (e) => {
        const btn = e.target.closest('.cw-tab-btn');
        if (!btn) return;
        
        tabGroup.querySelectorAll('.cw-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filter = btn.getAttribute('data-filter') || 'all';
        const targetContainer = tabGroup.getAttribute('data-target-container');
        if (targetContainer) {
          const container = document.getElementById(targetContainer);
          if (container) {
            container.querySelectorAll('[data-category]').forEach(card => {
              if (filter === 'all' || card.getAttribute('data-category').includes(filter)) {
                card.style.display = '';
              } else {
                card.style.display = 'none';
              }
            });
          }
        }
      });
    });
  }

  function initSearchFilters() {
    document.querySelectorAll('.cw-search-input').forEach(input => {
      const targetListId = input.getAttribute('data-target-list');
      const clearBtn = input.parentElement.querySelector('.cw-search-clear');

      input.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (targetListId) {
          const list = document.getElementById(targetListId);
          if (list) {
            list.querySelectorAll('.cw-entity-card, .cw-table tbody tr').forEach(item => {
              const text = item.textContent.toLowerCase();
              if (text.includes(query)) {
                item.style.display = '';
              } else {
                item.style.display = 'none';
              }
            });
          }
        }
      });

      if (clearBtn) {
        clearBtn.addEventListener('click', () => {
          input.value = '';
          input.dispatchEvent(new Event('input'));
        });
      }
    });
  }

  window.openCompanyModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('active');
  };

  window.closeCompanyModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
  };

  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('cw-modal-backdrop')) {
      e.target.classList.remove('active');
    }
  });

  window.showCompanyToast = function (message, type = 'success') {
    let container = document.querySelector('.cw-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'cw-toast-container';
      container.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 2000; display: flex; flex-direction: column; gap: 10px;';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.style.cssText = `background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid ${type === 'success' ? '#10b981' : '#2563eb'}; border-radius: 8px; padding: 12px 18px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 12px; min-width: 280px; font-family: var(--jw-font);`;
    toast.innerHTML = `
      <i class="fas ${type === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-info-circle text-blue-500'}" style="font-size: 16px; color: ${type === 'success' ? '#10b981' : '#2563eb'};"></i>
      <div style="flex:1; font-size: 12px; font-weight: 500; color: #0f172a;">${message}</div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'all 0.3s';
      setTimeout(() => toast.remove(), 300);
    }, 3800);
  };

  /**
   * Company Slide-out Drawer Controls
   */
  window.openCompanyDrawer = function (drawerId) {
    const drawer = document.getElementById(drawerId);
    if (drawer) {
      drawer.style.display = 'flex';
      setTimeout(() => drawer.classList.add('active'), 10);
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeCompanyDrawer = function (drawerId) {
    const drawer = document.getElementById(drawerId);
    if (drawer) {
      drawer.classList.remove('active');
      setTimeout(() => {
        drawer.style.display = 'none';
        document.body.style.overflow = '';
      }, 280);
    }
  };

  /**
   * Outbound Job Request Sending & Management (JERO ATS Parity)
   */
  window.openSendJobRequestModal = function (jobTitle, jobId, recruiterName) {
    const jobSelect = document.getElementById('cw-send-job-select');
    const partnerSelect = document.getElementById('cw-send-partner-select');
    
    if (jobSelect && jobTitle) {
      for (let i = 0; i < jobSelect.options.length; i++) {
        if (jobSelect.options[i].text.includes(jobTitle) || (jobId && jobSelect.options[i].value === String(jobId))) {
          jobSelect.selectedIndex = i;
          break;
        }
      }
    }

    if (partnerSelect && recruiterName) {
      for (let i = 0; i < partnerSelect.options.length; i++) {
        if (partnerSelect.options[i].text.includes(recruiterName)) {
          partnerSelect.selectedIndex = i;
          break;
        }
      }
    }

    openCompanyModal('sendJobRequestModal');
  };

  window.sendJobRequestSubmit = function (e) {
    if (e) e.preventDefault();

    const jobSelect = document.getElementById('cw-send-job-select');
    const partnerSelect = document.getElementById('cw-send-partner-select');
    const feeInput = document.getElementById('cw-send-fee-rate');

    const jobName = jobSelect ? jobSelect.options[jobSelect.selectedIndex].text : 'Direct Mandate';
    const partnerName = partnerSelect ? partnerSelect.options[partnerSelect.selectedIndex].text.split('•')[0].trim() : 'Recruiter';
    const fee = feeInput ? feeInput.value : '10.00%';

    closeCompanyModal('sendJobRequestModal');
    showCompanyToast(`Job request dispatched to ${partnerName} with ${fee} commercial agreement! JIT expiration set to 7 days.`, 'success');

    // Dynamically insert into Outbound Stream if present
    const stream = document.getElementById('cw-outbound-requests-stream');
    if (stream) {
      const newCard = document.createElement('div');
      newCard.className = 'cw-entity-card';
      newCard.setAttribute('data-category', 'pending');
      newCard.style.borderLeft = '4px solid #8b5cf6';
      newCard.innerHTML = `
        <div class="cw-entity-card-content">
          <div class="cw-subcard-col">
            <div class="cw-subcard-title">
              ${jobName}
              <span class="cw-tag-pill" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;"><i class="fas fa-paper-plane me-1"></i>Job Request</span>
              <span class="cw-tag-pill" style="background:#fef3c7; color:#92400e;">Awaiting Response</span>
            </div>
            <div class="cw-subcard-meta">
              <span><i class="fa fa-user-tie text-muted"></i> Assigned Recruiter: <strong>${partnerName}</strong></span>
              <span><i class="far fa-calendar-alt text-muted"></i> Dispatched: <strong>Just Now</strong></span>
              <span><i class="far fa-clock text-amber-500"></i> Expires in 7 Days (JIT)</span>
            </div>
          </div>
          <div class="cw-subcard-col">
            <span style="font-size: 11px; color: var(--jw-text-muted);">Commercial Agreement</span>
            <span style="font-weight: 700; font-size: 13px; color: #059669;">${fee} of Fixed CTC</span>
            <span style="font-size: 11px; color: var(--jw-text-muted);">90-Day Free Replacement SLA</span>
          </div>
          <div class="cw-subcard-col">
            <span style="font-size: 11px; color: var(--jw-text-muted);">Status & Allocation</span>
            <span class="cw-tag-pill" style="background:#fef3c7; color:#92400e; font-weight:600;"><i class="fas fa-hourglass-half me-1"></i>Pending Acceptance</span>
          </div>
          <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
            <button class="cw-btn cw-btn-secondary cw-btn-sm" onclick="showCompanyToast('Resending notification ping to ${partnerName}...')"><i class="fas fa-redo me-1"></i>Resend</button>
            <button class="cw-btn cw-btn-secondary cw-btn-sm" style="color:#ef4444; border-color:#fca5a5;" onclick="this.closest('.cw-entity-card').remove(); showCompanyToast('Request revoked.');"><i class="fas fa-times me-1"></i>Revoke</button>
          </div>
        </div>
        <div class="cw-activity-strip">
          <div><i class="fas fa-shield-alt text-primary me-1"></i>6-Month Exclusive Candidate Lock clause applied upon acceptance.</div>
          <span class="cw-tag-pill" style="background: var(--jw-bg-subtle);">Mandate #REQ-${Math.floor(1000 + Math.random() * 9000)}</span>
        </div>
      `;
      stream.insertBefore(newCard, stream.firstChild);

      // Update counters
      const pendingEl = document.getElementById('cw-kpi-outbound-pending');
      const allEl = document.getElementById('cw-kpi-outbound-all');
      if (pendingEl) pendingEl.textContent = (parseInt(pendingEl.textContent, 10) || 2) + 1;
      if (allEl) allEl.textContent = (parseInt(allEl.textContent, 10) || 6) + 1;
    }
  };

  window.filterOutboundRequests = function (category) {
    document.querySelectorAll('#cw-outbound-subtabs .cw-tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('data-filter') === category);
    });

    const cards = document.querySelectorAll('#cw-outbound-requests-stream .cw-entity-card');
    cards.forEach(card => {
      const cat = card.getAttribute('data-category') || '';
      if (category === 'all' || cat.includes(category)) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initSubTabs();
    initSearchFilters();

    const initialHash = window.location.hash.replace('#', '') || 'dashboard';
    navigateTo(initialHash, false);
  });

})();
