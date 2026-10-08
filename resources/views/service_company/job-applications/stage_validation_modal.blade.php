{{--
  Intent: ATS Stage Change Validation Notification Modal (Replication of Add Candidate validation modal)
  Tenancy: Tenant-scoped to Service Company context (company_id).
  Visual Standard: Enterprise Metronic / Poppins, 15.6-inch screen responsive, amber warning header, red missing field pills, full-width blue CTA.
  Rule Compliance: Rule 7 (Zero logic mutation), Rule 14 (Metronic UI standard), Rule 17 (Isolated modal lifecycle).
--}}
<style>
  .rf-stage-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 99999999;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
  }
  .rf-stage-modal-box {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 480px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    padding: 32px 30px 26px;
    position: relative;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
    margin: auto;
    border: 1px solid #e2e8f0;
    animation: rfStageFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  @keyframes rfStageFadeIn {
    from { opacity: 0; transform: scale(0.96) translateY(-8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }
  .rf-stage-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    border: none;
    color: #64748b;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s, color 0.15s;
  }
  .rf-stage-modal-close:hover {
    background: #e2e8f0;
    color: #0f172a;
  }
  .rf-stage-warning-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #fef9c3;
    border: 2px solid #facc15;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
  }
  .rf-stage-warning-icon {
    font-size: 30px;
    font-weight: 700;
    color: #ca8a04;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
  }
  .rf-stage-modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    text-align: center;
    margin: 0 0 8px 0;
    line-height: 1.25;
  }
  .rf-stage-modal-desc {
    font-size: 13.5px;
    color: #64748b;
    text-align: center;
    line-height: 1.45;
    margin: 0 auto 22px;
    max-width: 360px;
  }
  .rf-stage-section-header {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.6px;
    color: #64748b;
    text-transform: uppercase;
    text-align: left;
    margin-bottom: 10px;
  }
  .rf-stage-fields-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-height: 240px;
    overflow-y: auto;
    padding-right: 4px;
    margin-bottom: 12px;
  }
  .rf-stage-fields-list::-webkit-scrollbar {
    width: 5px;
  }
  .rf-stage-fields-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .rf-stage-field-row {
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    margin-bottom: 0;
    transition: background 0.15s, border-color 0.15s, transform 0.1s;
    user-select: none;
  }
  .rf-stage-field-row:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    transform: translateY(-1px);
  }
  .rf-stage-field-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }
  .rf-stage-field-badge {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ef4444;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
  }
  .rf-stage-field-name {
    font-size: 13.5px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .rf-stage-helper-text {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    text-align: left;
  }
  .rf-stage-cta-btn {
    width: 100%;
    height: 44px;
    background: #1b84ff;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: background 0.15s, box-shadow 0.15s;
  }
  .rf-stage-cta-btn:hover {
    background: #006ae6;
    box-shadow: 0 4px 12px rgba(27, 132, 255, 0.35);
  }
</style>

<div id="stageValidationRequiredFieldsModal" class="rf-stage-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="rfStageModalTitle">
  <div class="rf-stage-modal-box">
    <button type="button" class="rf-stage-modal-close" id="rfStageModalClose" aria-label="Close modal">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>

    <!-- Warning Icon -->
    <div class="rf-stage-warning-wrap" aria-hidden="true">
      <span class="rf-stage-warning-icon">!</span>
    </div>

    <!-- Title -->
    <h3 class="rf-stage-modal-title" id="rfStageModalTitle">Complete Required Fields</h3>

    <!-- Description -->
    <p class="rf-stage-modal-desc" id="rfStageModalDesc">
      <span id="rfStageMissingCountText">0 required fields</span> need your attention before you can move this candidate to Sourcing.
    </p>

    <!-- Section Heading -->
    <div class="rf-stage-section-header">REQUIRED FIELDS</div>

    <!-- Interactive Fields List -->
    <div class="rf-stage-fields-list" id="rfStageFieldsList"></div>

    <!-- Helper text -->
    <div class="rf-stage-helper-text">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
      <span>Click "Review Required Fields" to edit this candidate.</span>
    </div>

    <!-- Primary CTA -->
    <button type="button" class="rf-stage-cta-btn" id="rfStagePrimaryCta">
      <span>Review Required Fields</span>
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
    </button>
  </div>
</div>

<script>
(function() {
  'use strict';

  // Ensure only single modal element in DOM
  var existingModals = document.querySelectorAll('#stageValidationRequiredFieldsModal');
  if (existingModals.length > 1) {
    for (var mIdx = 0; mIdx < existingModals.length - 1; mIdx++) {
      if (existingModals[mIdx] && existingModals[mIdx].parentNode) {
        existingModals[mIdx].parentNode.removeChild(existingModals[mIdx]);
      }
    }
  }

  var stageValidationState = {
    canEdit: false,
    editUrl: '',
    permissionMsg: "You do not have permission to edit this candidate's profile. Only the assigned Captain or the recruiter who submitted this candidate can edit it."
  };

  window.showStageRequiredFieldsModal = function(response) {
    if (!response) return;

    var missingList = [];
    if (Array.isArray(response.missing_fields) && response.missing_fields.length > 0) {
      missingList = response.missing_fields;
    } else if (response.message) {
      var div = document.createElement('div');
      div.innerHTML = response.message;
      var text = div.innerText || div.textContent || '';
      var lines = text.split('\n');
      lines.forEach(function(l) {
        var trimmed = l.trim().replace(/^[•\-\*]\s*/, '').trim();
        if (trimmed && trimmed.indexOf('Cannot move') === -1 && trimmed.indexOf('Missing required') === -1 && trimmed.indexOf('Please edit') === -1) {
          missingList.push(trimmed);
        }
      });
    }

    if (missingList.length === 0) {
      missingList = ['Required Candidate Profile Details'];
    }

    stageValidationState.canEdit = Boolean(response.can_edit);
    stageValidationState.editUrl = response.edit_url || '';
    stageValidationState.permissionMsg = response.permission_message || "You do not have permission to edit this candidate's profile. Only the assigned Captain or the recruiter who submitted this candidate can edit it.";

    var count = missingList.length;
    var countText = count === 1 ? '1 required field' : (count + ' required fields');
    var descText = countText + ' need' + (count === 1 ? 's' : '') + ' your attention before you can move this candidate to Sourcing.';

    var modal = document.getElementById('stageValidationRequiredFieldsModal');
    if (!modal) return;

    if (modal.parentNode !== document.body) {
      document.body.appendChild(modal);
    }
    var allModals = document.querySelectorAll('#stageValidationRequiredFieldsModal');
    if (allModals.length > 1) {
      for (var m = 0; m < allModals.length; m++) {
        if (allModals[m] !== modal && allModals[m].parentNode) {
          allModals[m].parentNode.removeChild(allModals[m]);
        }
      }
    }

    var countEl = document.getElementById('rfStageMissingCountText');
    var descEl = document.getElementById('rfStageModalDesc');
    var listEl = document.getElementById('rfStageFieldsList');

    if (!listEl) return;

    if (descEl) {
      descEl.innerHTML = '<span id="rfStageMissingCountText">' + countText + '</span> need' + (count === 1 ? 's' : '') + ' your attention before you can move this candidate to Sourcing.';
    }

    listEl.innerHTML = '';
    missingList.forEach(function(fieldName) {
      var row = document.createElement('div');
      row.className = 'rf-stage-field-row';
      row.setAttribute('role', 'button');
      row.setAttribute('tabindex', '0');
      row.setAttribute('aria-label', 'Field: ' + fieldName);
      row.innerHTML = '<div class="rf-stage-field-left">'
        + '<span class="rf-stage-field-badge" aria-hidden="true">!</span>'
        + '<span class="rf-stage-field-name">' + $('<div>').text(fieldName).html() + '</span>'
        + '</div>'
        + '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex-shrink:0;"><polyline points="9 18 15 12 9 6"></polyline></svg>';

      row.addEventListener('click', handleActionClick);
      row.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          handleActionClick();
        }
      });
      listEl.appendChild(row);
    });

    modal.style.display = 'flex';
  };

  function closeStageValidationModal() {
    var modal = document.getElementById('stageValidationRequiredFieldsModal');
    if (modal) {
      modal.style.display = 'none';
    }
  }

  function handleActionClick(e) {
    if (e) e.preventDefault();
    if (stageValidationState.canEdit && stageValidationState.editUrl) {
      closeStageValidationModal();
      // Close open stage drawers
      var closeBtns = document.querySelectorAll('#stage_sub_stage_close, #csd_drawer_close, #kt_activitiesApplication_close');
      closeBtns.forEach(function(b) { if (b) b.click(); });
      window.location.href = stageValidationState.editUrl;
    } else {
      Swal.fire({
        title: "Permission Denied",
        text: stageValidationState.permissionMsg,
        icon: "warning",
        confirmButtonText: "Ok, got it!",
        customClass: { confirmButton: "btn btn-primary" }
      });
    }
  }

  $(document).off('click', '#rfStagePrimaryCta').on('click', '#rfStagePrimaryCta', function(e) {
    handleActionClick(e);
  });

  $(document).off('click', '#rfStageModalClose').on('click', '#rfStageModalClose', function(e) {
    e.preventDefault();
    closeStageValidationModal();
  });

  $(document).off('click', '#stageValidationRequiredFieldsModal').on('click', '#stageValidationRequiredFieldsModal', function(e) {
    if (e.target === this) {
      closeStageValidationModal();
    }
  });

  $(document).off('keydown.rfStageModal').on('keydown.rfStageModal', function(e) {
    if (e.key === 'Escape') {
      var modal = document.getElementById('stageValidationRequiredFieldsModal');
      if (modal && $(modal).is(':visible')) {
        closeStageValidationModal();
      }
    }
  });
})();
</script>
