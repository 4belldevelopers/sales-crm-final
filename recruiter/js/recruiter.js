/**
 * JERO ATS — Recruiter Workspace Master Frontend Engine
 * Handles client-side navigation, interactive UI components, dummy data filtering,
 * modal triggers, and client-first resume simulation.
 * Zero backend dependency.
 */

(function () {
  'use strict';

  // Navigation state
  const validScreens = [
    'dashboard',
    'jobs',
    'job-requests',
    'job-details',
    'candidate-submission',
    'candidates',
    'applications',
    'interview-tracker',
    'earnings',
    'payments',
    'notifications',
    'profile',
    'support',
    'settings',
    'login',
    'register'
  ];

  // Screen Breadcrumb Labels
  const screenMeta = {
    'dashboard': { title: 'Dashboard', parent: 'Overview' },
    'jobs': { title: 'Assigned & Marketplace Mandates', parent: 'Job Mandates' },
    'job-requests': { title: 'Inbound Job Requests & Mandates', parent: 'Job Mandates' },
    'job-details': { title: 'Mandate Specification', parent: 'Jobs' },
    'candidate-submission': { title: 'Candidate Intake & Representation', parent: 'Submissions' },
    'candidates': { title: 'My Talent Bank', parent: 'Candidates' },
    'applications': { title: 'Pipeline Tracker', parent: 'Applications' },
    'interview-tracker': { title: 'Interview Rounds & Schedule', parent: 'Interviews' },
    'earnings': { title: 'Commission & Placement Analytics', parent: 'Earnings' },
    'payments': { title: 'Invoices & Payout Settlements', parent: 'Billing' },
    'notifications': { title: 'Activity Center & Alerts', parent: 'Notifications' },
    'profile': { title: 'Recruiter Profile & KYC', parent: 'Account' },
    'support': { title: 'Help Desk & Dispute Support', parent: 'Support' },
    'settings': { title: 'Workspace Preferences & Security', parent: 'Settings' },
    'login': { title: 'Recruiter Sign In', parent: 'Authentication' },
    'register': { title: 'Recruiter Registration', parent: 'Authentication' }
  };

  /**
   * Switch Active Screen View
   */
  function navigateTo(screenId, pushHash = true) {
    if (!validScreens.includes(screenId)) {
      screenId = 'dashboard';
    }

    // Toggle screen container visibility
    document.querySelectorAll('.rw-view-screen').forEach(el => {
      el.classList.remove('active');
    });

    const targetEl = document.getElementById('view-' + screenId);
    if (targetEl) {
      targetEl.classList.add('active');
    }

    // Toggle active sidebar navigation
    document.querySelectorAll('.rw-nav-item').forEach(nav => {
      if (nav.getAttribute('data-screen') === screenId) {
        nav.classList.add('active');
      } else {
        nav.classList.remove('active');
      }
    });

    // Update Topbar Breadcrumb & Title
    const meta = screenMeta[screenId] || { title: 'Recruiter Workspace', parent: 'JERO' };
    const breadcrumbActive = document.getElementById('rw-breadcrumb-active');
    const breadcrumbParent = document.getElementById('rw-breadcrumb-parent');
    if (breadcrumbActive) breadcrumbActive.textContent = meta.title;
    if (breadcrumbParent) breadcrumbParent.textContent = meta.parent;

    // Control shell visibility (hide shell on login/register view if needed)
    const shell = document.querySelector('.rw-app-wrapper');
    const authWrapper = document.getElementById('rw-auth-screens-wrapper');

    if (screenId === 'login' || screenId === 'register') {
      if (shell) shell.style.display = 'none';
      if (authWrapper) authWrapper.style.display = 'flex';
      const authTarget = document.getElementById('auth-view-' + screenId);
      document.querySelectorAll('.rw-auth-screen').forEach(a => a.style.display = 'none');
      if (authTarget) authTarget.style.display = 'block';
    } else {
      if (shell) shell.style.display = 'flex';
      if (authWrapper) authWrapper.style.display = 'none';
    }

    if (pushHash && window.location.hash !== '#' + screenId) {
      window.location.hash = screenId;
    }

    // Scroll top
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /**
   * Initialize Global Event Listeners
   */
  function initNavigation() {
    // Hash change handler
    window.addEventListener('hashchange', () => {
      const hash = window.location.hash.replace('#', '') || 'dashboard';
      navigateTo(hash, false);
    });

    // Delegated click on data-screen elements
    document.addEventListener('click', (e) => {
      const target = e.target.closest('[data-screen]');
      if (target) {
        e.preventDefault();
        const screen = target.getAttribute('data-screen');
        navigateTo(screen);
      }
    });

    // Sidebar Toggle Collapse
    const toggleBtn = document.getElementById('rw-sidebar-toggle');
    const sidebar = document.querySelector('.rw-sidebar');
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
      });
    }

    // Mobile Hamburger
    const mobileToggle = document.getElementById('rw-mobile-toggle');
    if (mobileToggle && sidebar) {
      mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
      });
    }
  }

  /**
   * Sub-Tab Switching Logic
   */
  function initSubTabs() {
    document.querySelectorAll('.rw-subtabs-card').forEach(tabGroup => {
      tabGroup.addEventListener('click', (e) => {
        const btn = e.target.closest('.rw-tab-btn');
        if (!btn) return;
        
        tabGroup.querySelectorAll('.rw-tab-btn').forEach(b => b.classList.remove('active'));
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

  /**
   * Live Search Filter Logic
   */
  function initSearchFilters() {
    document.querySelectorAll('.rw-search-input').forEach(input => {
      const targetListId = input.getAttribute('data-target-list');
      const clearBtn = input.parentElement.querySelector('.rw-search-clear');

      input.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (targetListId) {
          const list = document.getElementById(targetListId);
          if (list) {
            list.querySelectorAll('.rw-entity-card, .rw-table tbody tr').forEach(item => {
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

  /**
   * Modals Engine
   */
  window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('active');
    }
  };

  window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('active');
    }
  };

  // Close modal when clicking backdrop
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('rw-modal-backdrop')) {
      e.target.classList.remove('active');
    }
  });

  /**
   * Toast Notification Helper
   */
  window.showToast = function (message, type = 'success') {
    let container = document.querySelector('.rw-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'rw-toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `rw-toast toast-${type}`;
    const icon = type === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-triangle text-amber-500';
    toast.innerHTML = `
      <i class="fas ${icon}" style="font-size: 16px; color: ${type === 'success' ? '#10b981' : '#ef4444'};"></i>
      <div style="flex:1; font-size: 12px; font-weight: 500; color: #0f172a;">${message}</div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      toast.style.transition = 'all 0.3s';
      setTimeout(() => toast.remove(), 300);
    }, 3800);
  };

  /**
   * Candidate Intake & Client-First Parser Simulation
   */
  window.simulateResumeUpload = function () {
    const statusText = document.getElementById('rw-upload-status');
    const autoFields = document.getElementById('rw-parsed-fields-container');
    
    if (statusText) {
      statusText.innerHTML = `
        <i class="fas fa-spinner fa-spin text-primary" style="font-size: 20px; color: #009ef7;"></i>
        <div style="font-weight: 600; color: #009ef7; margin-top: 6px;">JeroIdentityEngine (Browser WASM) Parsing Resume in Client RAM...</div>
        <div style="font-size: 11px; color: #64748b;">Triangulating 5-vector identity & Chronology Resolver (0% server load)</div>
      `;
    }

    setTimeout(() => {
      if (statusText) {
        statusText.innerHTML = `
          <i class="fas fa-check-circle" style="font-size: 24px; color: #10b981;"></i>
          <div style="font-weight: 600; color: #0f172a; margin-top: 4px;">Vikram_Aditya_Senior_Backend_Resume.pdf (142 KB)</div>
          <div style="font-size: 11px; color: #10b981;"><i class="fas fa-shield-alt"></i> 100% Parsed in Client RAM &bull; 90-Day Ownership Lock Available</div>
        `;
      }

      // Populate dummy extracted values into form
      const nameInput = document.getElementById('sub-candidate-name');
      const emailInput = document.getElementById('sub-candidate-email');
      const phoneInput = document.getElementById('sub-candidate-phone');
      const expInput = document.getElementById('sub-candidate-exp');
      const companyInput = document.getElementById('sub-candidate-company');
      const ctcInput = document.getElementById('sub-candidate-ctc');
      const ectcInput = document.getElementById('sub-candidate-ectc');
      const npInput = document.getElementById('sub-candidate-np');

      if (nameInput) nameInput.value = 'Vikram Aditya';
      if (emailInput) emailInput.value = 'vikram.aditya.tech@gmail.com';
      if (phoneInput) phoneInput.value = '+91 98765 43210';
      if (expInput) expInput.value = '8.5 Years';
      if (companyInput) companyInput.value = 'CRED (Dreamplug Technologies)';
      if (ctcInput) ctcInput.value = '₹28,00,000';
      if (ectcInput) ectcInput.value = '₹36,00,000';
      if (npInput) npInput.value = '30 Days (Serving Notice)';

      if (autoFields) {
        autoFields.style.display = 'block';
      }

      showToast('Resume parsed successfully! Extracted 12 profile attributes in 28ms.');
    }, 1100);
  };

  /**
   * Handle Candidate Submission Form
   */
  window.submitCandidateForm = function (e) {
    if (e) e.preventDefault();
    showToast('Candidate successfully submitted! 90-day exclusive attribution lock activated.', 'success');
    setTimeout(() => {
      navigateTo('applications');
    }, 1200);
  };

  /**
   * Slide-out Drawer Controls (Metronic & Transitions.dev Standard)
   */
  window.openDrawer = function (drawerId) {
    const drawer = document.getElementById(drawerId);
    if (drawer) {
      drawer.style.display = 'flex';
      setTimeout(() => drawer.classList.add('active'), 10);
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeDrawer = function (drawerId) {
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
   * Job Request Acceptance & Rejection Engine (Parity with JERO ATS Service Company)
   */
  let pendingAcceptJobId = null;
  let pendingDeclineJobId = null;

  window.openAcceptTermsModal = function (jobId, title, client, fee, guarantee) {
    pendingAcceptJobId = jobId;
    const titleEl = document.getElementById('rw-accept-job-title');
    const clientEl = document.getElementById('rw-accept-client-name');
    const feeEl = document.getElementById('rw-accept-fee');
    const guaranteeEl = document.getElementById('rw-accept-guarantee');
    const chkEl = document.getElementById('rw-accept-terms-check');

    if (titleEl) titleEl.textContent = title || 'Direct Client Mandate';
    if (clientEl) clientEl.textContent = client || 'Client Organization';
    if (feeEl) feeEl.textContent = fee || '10.00% of Fixed Gross CTC';
    if (guaranteeEl) guaranteeEl.textContent = guarantee || '90-Day Free Replacement SLA';
    if (chkEl) chkEl.checked = false;

    openModal('acceptMandateModal');
  };

  window.confirmAcceptMandate = function () {
    const chkEl = document.getElementById('rw-accept-terms-check');
    if (chkEl && !chkEl.checked) {
      showToast('Please check and accept the commercial SLA and non-circumvention terms.', 'error');
      return;
    }

    if (pendingAcceptJobId) {
      const card = document.getElementById('rw-req-card-' + pendingAcceptJobId);
      if (card) {
        card.classList.remove('state-pending');
        card.classList.add('state-accepted');
        card.setAttribute('data-category', 'accepted');
        
        // Update Action subcard to Accepted badge + Submit Candidate button
        const actionSubcard = card.querySelector('.rw-req-action-col');
        if (actionSubcard) {
          actionSubcard.innerHTML = `
            <div style="display:flex; flex-direction:column; gap:6px;">
              <span class="type-direct" style="background:#ecfdf5; color:#059669; font-weight:700; padding:5px 10px; font-size:11.5px; border-radius:6px; text-align:center;">
                <i class="fas fa-check-circle me-1"></i> Accepted & Active
              </span>
              <button class="rw-btn rw-btn-primary rw-btn-sm" style="font-size:11px; padding:4px 10px;" onclick="navigateTo('candidate-submission')">
                <i class="fas fa-user-plus me-1"></i> Submit Candidate
              </button>
            </div>
          `;
        }

        // Update header badges
        const headerBadge = card.querySelector('.rw-status-badge');
        if (headerBadge) {
          headerBadge.className = 'rw-status-badge rw-status-hiring';
          headerBadge.textContent = 'Active Mandate';
        }
      }

      // Update counters
      const pendingCount = document.getElementById('rw-kpi-pending-reqs');
      const acceptedCount = document.getElementById('rw-kpi-accepted-reqs');
      if (pendingCount) {
        const curP = parseInt(pendingCount.textContent, 10) || 2;
        pendingCount.textContent = Math.max(0, curP - 1);
      }
      if (acceptedCount) {
        const curA = parseInt(acceptedCount.textContent, 10) || 1;
        acceptedCount.textContent = curA + 1;
      }

      // Update sidebar badge
      const navBadge = document.querySelector('.rw-nav-item[data-screen="job-requests"] .rw-nav-badge');
      if (navBadge) {
        const curN = parseInt(navBadge.textContent, 10) || 3;
        navBadge.textContent = Math.max(0, curN - 1) + ' New';
      }
    }

    closeModal('acceptMandateModal');
    showToast('Direct client mandate accepted! Contract terms confirmed and job added to active workspace.', 'success');
  };

  window.openDeclineModal = function (jobId, title, client) {
    pendingDeclineJobId = jobId;
    const titleEl = document.getElementById('rw-decline-job-title');
    const clientEl = document.getElementById('rw-decline-client-name');
    if (titleEl) titleEl.textContent = title || 'Direct Client Mandate';
    if (clientEl) clientEl.textContent = client || 'Client Organization';

    openModal('declineMandateModal');
  };

  window.confirmDeclineMandate = function () {
    if (pendingDeclineJobId) {
      const card = document.getElementById('rw-req-card-' + pendingDeclineJobId);
      if (card) {
        card.classList.remove('state-pending');
        card.classList.add('state-declined');
        card.setAttribute('data-category', 'declined');

        const reasonSelect = document.getElementById('rw-decline-reason');
        const reasonText = reasonSelect ? reasonSelect.value : 'Declined by recruiter';

        // Update Action subcard to Declined badge
        const actionSubcard = card.querySelector('.rw-req-action-col');
        if (actionSubcard) {
          actionSubcard.innerHTML = `
            <span style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca; font-weight:600; padding:5px 8px; font-size:11px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
              <i class="fas fa-times-circle"></i> Declined
            </span>
            <span style="font-size:10px; color:var(--rw-text-muted); margin-top:4px;">${reasonText}</span>
          `;
        }

        const headerBadge = card.querySelector('.rw-status-badge');
        if (headerBadge) {
          headerBadge.className = 'rw-status-badge rw-status-closed';
          headerBadge.textContent = 'Declined';
        }
      }

      // Update counters
      const pendingCount = document.getElementById('rw-kpi-pending-reqs');
      const declinedCount = document.getElementById('rw-kpi-declined-reqs');
      if (pendingCount) {
        const curP = parseInt(pendingCount.textContent, 10) || 2;
        pendingCount.textContent = Math.max(0, curP - 1);
      }
      if (declinedCount) {
        const curD = parseInt(declinedCount.textContent, 10) || 1;
        declinedCount.textContent = curD + 1;
      }
    }

    closeModal('declineMandateModal');
    showToast('Job request declined. The client has been notified of your response.', 'info');
  };

  window.filterJobRequests = function (category) {
    document.querySelectorAll('#rw-req-subtabs .rw-tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.getAttribute('data-filter') === category);
    });

    const cards = document.querySelectorAll('#rw-job-requests-stream .rw-req-card');
    cards.forEach(card => {
      const cat = card.getAttribute('data-category') || '';
      if (category === 'all' || cat.includes(category)) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  };

  window.openJobSpecDrawer = function (jobId) {
    openDrawer('jobSpecDrawer');
  };

  window.openHmDrawer = function (hmId) {
    openDrawer('hmDetailsDrawer');
  };

  /**
   * On Document Ready
   */
  document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initSubTabs();
    initSearchFilters();

    // Route based on initial hash
    const initialHash = window.location.hash.replace('#', '') || 'dashboard';
    navigateTo(initialHash, false);
  });

})();
