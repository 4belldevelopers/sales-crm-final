@php 
    use App\Helper\GeneralMethod;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
@endphp 

@include('service_company.cvtracker._forward_resume_engine') 

<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
if (typeof CKEDITOR !== 'undefined' && CKEDITOR.env && CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

function initTestFwdCkEditor() {
    if (typeof CKEDITOR === 'undefined') {
        setTimeout(initTestFwdCkEditor, 100);
        return;
    }
    var editorElement = document.getElementById('test_editor1');
    if (!editorElement) {
        setTimeout(initTestFwdCkEditor, 100);
        return;
    }
    if (CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
        try {
            CKEDITOR.instances['test_editor1'].destroy(true);
        } catch(destroyErr) {}
    }
    try {
        CKEDITOR.config.height = 300;
        CKEDITOR.config.width = '100%';
        CKEDITOR.replace('test_editor1', {
            height: 300,
            width: '100%'
        });
    } catch(err) {
        console.warn('CKEditor test init error:', err);
    }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(initTestFwdCkEditor, 50);
} else {
    document.addEventListener('DOMContentLoaded', initTestFwdCkEditor);
}
$(document).ready(function() {
    setTimeout(initTestFwdCkEditor, 100);
});
setTimeout(initTestFwdCkEditor, 250);
</script>

<div class="main_container" id="test_main_container">
@php
$template_body = '';
$to_email = '';
$cc_email = '';
if(!empty($cc_email_count) && $cc_email_count > 0 && !empty($cc_email_data)){
    $cc_email = implode(', ', $cc_email_data);
}
@endphp

<style>
/* mm-* form design — identical to production forward resume drawer */
.mm-wrap { font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; display: flex; flex-direction: column; height: 100%; min-height: 100%; }
#test_form1 { display: flex; flex-direction: column; height: 100%; flex: 1; min-height: 0; }
.mm-form-body { flex: 1; overflow-y: auto; }
.mm-row { display: flex; align-items: flex-start; padding: 16px 24px; border-bottom: 1px solid #F3F4F6; gap: 16px; }
.mm-label-col { width: 150px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding-top: 9px; font-size: 12.5px; font-weight: 600; color: #374151; }
.mm-label-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mm-label-icon svg { width: 14px; height: 14px; }
.mm-ic-blue   { background: #EFF6FF; color: #2563EB; }
.mm-ic-green  { background: #DCFCE7; color: #16A34A; }
.mm-ic-purple { background: #F5F3FF; color: #7C3AED; }
.mm-ic-teal   { background: #F0FDF4; color: #0D9488; }
.mm-ic-orange { background: #FFF7ED; color: #EA580C; }
.mm-req { color: #EF4444; margin-left: 1px; }
.mm-field-col { flex: 1; min-width: 0; position: relative; }
.mm-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB; outline: none; transition: border-color 0.15s; }
.mm-input:focus { border-color: #93C5FD; background: #fff; }
.mm-input[readonly] { color: #6B7280; cursor: default; background: #F3F4F6; }
.mm-hint { font-size: 11.5px; color: #9CA3AF; margin-top: 5px; line-height: 1.4; }
.mm-select { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 36px 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; -webkit-appearance: none; outline: none; cursor: pointer; transition: border-color 0.15s; }
.mm-select:focus { border-color: #93C5FD; background-color: #fff; }

.mm-editor-wrap { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; background: #FFFFFF; }
.mm-editor-wrap .cke { width: 100% !important; border: none !important; }
#test_editor1 { width: 100% !important; min-height: 250px; padding: 12px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; box-sizing: border-box; }
.mm-row-body { align-items: flex-start; }
.mm-add-email-btn { position: absolute; right: 0; top: 0; display: flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: #2563EB; background: none; border: none; cursor: pointer; padding: 9px 14px; z-index: 10; }
.mm-add-email-btn:hover { color: #1d4ed8; }
.mm-input-with-btn { padding-right: 110px; }

/* Tag / Chip input container for To and CC email addresses */
.mm-tag-box {
  width: 100%;
  min-height: 44px;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  padding: 5px 105px 5px 8px;
  font-size: 13px;
  font-family: 'Poppins', sans-serif;
  background: #F9FAFB;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
  position: relative;
  cursor: text;
  transition: border-color 0.15s, background-color 0.15s, box-shadow 0.15s;
  box-sizing: border-box;
}
.mm-tag-box.is-focused {
  border-color: #93C5FD;
  background-color: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.25);
}
.mm-tag-list {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}
.mm-email-tag {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #EFF6FF;
  border: 1px solid #BFDBFE;
  color: #1E40AF;
  font-size: 12px;
  font-weight: 500;
  padding: 3px 8px;
  border-radius: 6px;
  line-height: 1.35;
  transition: all 0.15s ease;
  user-select: none;
}
.mm-email-tag.is-invalid {
  background: #FEF2F2;
  border-color: #FECACA;
  color: #DC2626;
}
.mm-email-tag-text {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.mm-email-tag-remove {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  color: #6B7280;
  cursor: pointer;
  font-size: 14px;
  font-weight: 700;
  line-height: 1;
  transition: all 0.15s;
  margin-left: 2px;
}
.mm-email-tag-remove:hover {
  background: #DBEAFE;
  color: #1E3A8A;
}
.mm-email-tag.is-invalid .mm-email-tag-remove:hover {
  background: #FEE2E2;
  color: #991B1B;
}
.mm-tag-input {
  border: none !important;
  background: transparent !important;
  outline: none !important;
  font-size: 12.5px !important;
  font-family: 'Poppins', sans-serif !important;
  color: #374151 !important;
  min-width: 160px;
  flex: 1;
  padding: 4px 2px !important;
  box-shadow: none !important;
}

/* Replicated Modern Header */
.mm-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 24px;
  background: #FFFFFF;
  border-bottom: 1px solid #E5E7EB;
  position: sticky;
  top: 0;
  z-index: 50;
  flex-shrink: 0;
  gap: 16px;
}
.mm-header-left {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}
.mm-header-icon-box {
  width: 42px;
  height: 42px;
  min-width: 42px;
  border-radius: 9px;
  background: #EEF2FF;
  display: flex;
  align-items: center;
  justify-content: center;
}
.mm-header-text { min-width: 0; }
.mm-header-title {
  margin: 0;
  font-size: 16px;
  font-weight: 700;
  color: #111827;
  letter-spacing: -0.2px;
  line-height: 1.3;
}
.mm-header-subtitle {
  margin: 2px 0 0;
  font-size: 12px;
  color: #6B7280;
  line-height: 1.3;
}
.mm-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}
.mm-header-btn-cancel {
  display: inline-flex;
  align-items: center;
  padding: 8px 16px;
  border-radius: 7px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  color: #374151;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.mm-header-btn-cancel:hover {
  background: #F9FAFB;
  border-color: #D1D5DB;
}
.mm-header-btn-send {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 20px;
  border-radius: 7px;
  border: none;
  background: #2563EB;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.mm-header-btn-send:hover {
  background: #1D4ED8;
}
.mm-header-btn-send:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Recipient Drawer Styles */
.rcp-container { display: flex; flex-direction: column; height: 100%; font-family: 'Poppins', sans-serif; }
.rcp-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid #E5E7EB; background: #fff; flex-shrink: 0; }
.rcp-header-title { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
.rcp-header-subtitle { font-size: 12px; color: #6B7280; margin: 2px 0 0; }
.rcp-close-btn { background: none; border: none; cursor: pointer; color: #9CA3AF; padding: 4px; border-radius: 6px; }
.rcp-close-btn:hover { color: #374151; background: #F3F4F6; }
.rcp-tabs-wrap { display: flex; gap: 10px; padding: 12px 20px; background: #F9FAFB; border-bottom: 1px solid #E5E7EB; flex-shrink: 0; }
.rcp-tab-card { flex: 1; display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #fff; border: 1.5px solid #E5E7EB; border-radius: 8px; cursor: pointer; transition: all 0.15s; }
.rcp-tab-card.active { border-color: #2563EB; background: #EFF6FF; }
.rcp-tab-icon { color: #6B7280; }
.rcp-tab-card.active .rcp-tab-icon { color: #2563EB; }
.rcp-tab-title { font-size: 13px; font-weight: 600; color: #111827; }
.rcp-tab-card.active .rcp-tab-title { color: #1D4ED8; }
.rcp-tab-sub { font-size: 11px; color: #6B7280; }
.rcp-search-wrap { padding: 12px 20px; border-bottom: 1px solid #E5E7EB; background: #fff; flex-shrink: 0; display: flex; gap: 8px; }
.rcp-search-box { flex: 1; position: relative; }
.rcp-search-box input { width: 100%; padding: 8px 30px 8px 34px; font-size: 12.5px; border: 1px solid #E5E7EB; border-radius: 7px; background: #F9FAFB; outline: none; }
.rcp-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9CA3AF; pointer-events: none; }
.rcp-body-list { flex: 1; overflow-y: auto; padding: 12px 20px; }
.rcp-contact-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border: 1px solid #F3F4F6; border-radius: 8px; margin-bottom: 8px; background: #fff; transition: all 0.15s; }
.rcp-contact-item:hover { border-color: #E5E7EB; background: #F9FAFB; }
.rcp-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; }
.rcp-info { margin-left: 10px; min-width: 0; flex: 1; }
.rcp-name { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rcp-email { font-size: 11.5px; color: #6B7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rcp-role { font-size: 11px; color: #9CA3AF; }
.rcp-toggle-btn { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid #DBEAFE; background: #EFF6FF; color: #2563EB; transition: all 0.15s; }
.rcp-toggle-btn.is-added { border-color: #DCFCE7; background: #DCFCE7; color: #16A34A; }
.rcp-toggle-btn:hover { opacity: 0.85; }
.rcp-footer { padding: 14px 20px; border-top: 1px solid #E5E7EB; background: #fff; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.rcp-btn-cancel { padding: 8px 16px; border: 1px solid #E5E7EB; background: #fff; border-radius: 7px; font-size: 13px; font-weight: 600; color: #374151; cursor: pointer; }
.rcp-btn-done { padding: 8px 20px; border: none; background: #2563EB; border-radius: 7px; font-size: 13px; font-weight: 600; color: #fff; cursor: pointer; }
</style>

<div class="mm-wrap">
<form id="test_form1">
  {{-- Header: Identical layout to Production Forward Resume Drawer --}}
  <div class="mm-header">
    <div class="mm-header-left">
      <div class="mm-header-icon-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="4" width="20" height="16" rx="2"/>
          <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
        </svg>
      </div>
      <div class="mm-header-text">
        <h3 class="mm-header-title">Forward Candidate Resumes</h3>
        <p class="mm-header-subtitle">Send selected candidate profiles to a client or hiring stakeholder.</p>
      </div>
    </div>
    <div class="mm-header-actions">
      <button type="button" class="mm-header-btn-cancel" onclick="closeTestFwdResumeDrawer()">Cancel</button>
      <button type="button" class="mm-header-btn-send" name="btnTestSubmit" id="btnTestSubmit" value="btnTestSubmit" onclick="if(window.onTestFwdSubmitClick) window.onTestFwdSubmitClick(event);">
        <span class="indicator-label" style="display:inline-flex;align-items:center;gap:7px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Email
        </span>
        <span class="indicator-progress" style="display:none;align-items:center;gap:7px;">
          <span class="spinner-border spinner-border-sm" style="width:13px;height:13px;border-width:2px;display:inline-block;"></span>
          <span id="test_fwd_btn_progress_text">Sending...</span>
        </span>
      </button>
    </div>
  </div>

  <div class="mm-form-body">

    {{-- From + Reply To --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
        </span>
        From
      </div>
      <div class="mm-field-col" style="display:flex;gap:12px;align-items:center;">
        <select class="mm-select" name="from_email" id="test_from_email" style="flex:1;min-width:0;">
          <option value="notify@jeroworks.com">{{ $userdata->name ?? $userdata->email }} &lt;notify@jeroworks.com&gt;</option>
          @if(!empty($userdata->email) && $userdata->email !== 'notify@jeroworks.com')
            <option value="{{ $userdata->email }}">{{ $userdata->name ?? $userdata->email }} &lt;{{ $userdata->email }}&gt;</option>
          @endif
        </select>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12.5px;font-weight:600;color:#374151;">
          <span class="mm-label-icon mm-ic-purple" style="width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
          </span>
          Reply To
        </div>
        <select class="mm-select" name="reply_to_email" id="test_reply_to_email" style="flex:1;min-width:0;">
          <option value="{{ $userdata->email }}">{{ $userdata->email }}</option>
          @if(!empty(auth()->user()->company->company_email) && auth()->user()->company->company_email !== $userdata->email)
            <option value="{{ auth()->user()->company->company_email }}">{{ auth()->user()->company->company_email }}</option>
          @endif
        </select>
      </div>
    </div>

    {{-- To --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </span>
        To <span class="mm-req">*</span>
      </div>
      <div class="mm-field-col">
        <div class="mm-tag-box" id="test_to_email_tag_box" onclick="$('#test_to_email_input').focus();">
          <div class="mm-tag-list" id="test_to_email_tags"></div>
          <input type="text" class="mm-tag-input" id="test_to_email_input" placeholder="Type recipient email and press Enter...">
          <a href="javascript:;" class="mm-add-email-btn test_to_email_panel">+ Add Email</a>
        </div>
        <input type="hidden" name="to_email" id="test_to_email" value="">
        <div class="mm-hint">Type email address and press Enter or comma, or click "+ Add Email"</div>
      </div>
    </div>

    {{-- CC --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        CC
      </div>
      <div class="mm-field-col">
        <div class="mm-tag-box" id="test_cc_email_tag_box" onclick="$('#test_cc_email_input').focus();">
          <div class="mm-tag-list" id="test_cc_email_tags"></div>
          <input type="text" class="mm-tag-input" id="test_cc_email_input" placeholder="Type CC email and press Enter...">
          <a href="javascript:;" class="mm-add-email-btn test_cc_email_panel">+ Add Email</a>
        </div>
        <input type="hidden" name="cc_email_panel" id="test_cc_email_panel" value="{{ $cc_email }}">
        <div class="mm-hint">Type CC address and press Enter or comma, or click "+ Add Email"</div>
      </div>
    </div>

    {{-- Template --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        Template
      </div>
      <div class="mm-field-col">
        <select name="template" id="test_template" class="mm-select" onchange="if(window.onTestTemplateChange) window.onTestTemplateChange(this);">
          <option value="">Select Email Template...</option>
          @if(!empty($templateData))
            @foreach($templateData as $tdata)
              <option value="{{ $tdata->e_temp_id ?? ($tdata->id ?? '') }}">{{ $tdata->display_name ?? ($tdata->template_name ?? 'Template #' . ($tdata->e_temp_id ?? $tdata->id)) }}</option>
            @endforeach
          @endif
        </select>
        <div class="mm-hint">Choosing a template replaces the Subject and Body below</div>
      </div>
    </div>

    {{-- Subject --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </span>
        Subject <span class="mm-req">*</span>
      </div>
      <div class="mm-field-col">
        <input class="mm-input" name="subject" id="test_subject" placeholder="Enter email subject" value="Forwarded Candidate Profiles: {{ !empty($applicationData[0]->full_name) ? $applicationData[0]->full_name : 'Profiles' }}">
      </div>
    </div>

    {{-- Resume Attachment Format Selector (Download Resume Center Integration - All Options 1, 5, 41-53) --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </span>
        Resume Format <span class="mm-req">*</span>
      </div>
      <div class="mm-field-col">
        <input type="hidden" id="test_fwd_attach_cv_toggle" name="attached_cv" value="1">

        <div id="test_fwd_format_container">
          <select name="attached_cv_format" id="test_fwd_resume_format" class="mm-select" style="font-weight:500;" onchange="if(window.onTestFwdFormatChange) window.onTestFwdFormatChange(this);">
            <option value="" selected disabled>Select Resume Format...</option>
            <optgroup label="1. Standard Resume Options">
              <option value="1" data-label="Original Candidate Resume" data-desc="Download the candidate's uploaded resume exactly as received, with no edits or formatting changes.">1. Original Resume (Default)</option>
              <option value="41" data-label="Resume + Company Branding" data-desc="Add your company logo and shared-by header above the resume while keeping all candidate details visible.">41. Resume + Company Branding</option>
            </optgroup>
            <optgroup label="2. Pre-Screened Report">
              <option value="5" data-label="Pre-Screened Report" data-desc="Generate a concise hiring report with fit summary, key skills, and verification status for recruiter review.">5. Pre-Screened Report PDF</option>
            </optgroup>
            <optgroup label="3. Recruiter Packages (Report + Resume)">
              <option value="42" data-label="Pre-Screened Report + Resume + Company Branding" data-desc="Deliver one polished PDF with the pre-screened report first, followed by the candidate resume with company branding.">42. Pre-Screened Report + Resume + Company Branding</option>
              <option value="43" data-label="Pre-Screened Report + Resume without Company Branding" data-desc="Download the same report-and-resume package without company logo or company name on the report page.">43. Pre-Screened Report + Resume without Company Branding</option>
            </optgroup>
            <optgroup label="4. Highlighted Skills">
              <option value="44" data-label="Resume + Highlighted Keywords" data-desc="Download a clean resume copy with the most relevant skills highlighted for quick recruiter review.">44. Resume + Highlighted Keywords</option>
              <option value="45" data-label="Resume + Highlighted Keywords + Company Branding" data-desc="Download a branded resume with job-matching keywords highlighted for faster recruiter screening.">45. Resume + Highlighted Keywords + Company Branding</option>
              <option value="46" data-label="Pre-Screened Report + Resume + Highlighted Keywords + Company Branding" data-desc="Create the recruiter package: pre-screened report, branded resume pages, and highlighted job-matching skills.">46. Pre-Screened Report + Resume + Highlighted Keywords + Company Branding</option>
              <option value="47" data-label="Pre-Screened Report + Resume + Highlighted Keywords without Company Branding" data-desc="Create the recruiter package with report, resume, and highlighted skills, without company branding.">47. Pre-Screened Report + Resume + Highlighted Keywords without Company Branding</option>
            </optgroup>
            <optgroup label="5. Privacy & Anonymization (Anti-Poaching Shield)">
              <option value="48" data-label="Hide Contact Details" data-desc="Keep candidate name visible while hiding email, phone, mobile, contact and LinkedIn details in browser.">48. Hide Contact Details</option>
              <option value="49" data-label="Hide Candidate Name & Contacts Details" data-desc="Hide candidate name, email, and phone numbers in a browser-only resume copy.">49. Hide Candidate Name & Contacts Details</option>
              <option value="50" data-label="Hide Contact Details + Company Branding" data-desc="Keep the name visible, hide contact and LinkedIn details, then add your company header.">50. Hide Contact Details + Company Branding</option>
              <option value="51" data-label="Hide Contact Details + Pre-Screened Report (Branded)" data-desc="Keep the name visible, hide contact and LinkedIn details, then merge the pre-screened report.">51. Hide Contact Details + Pre-Screened Report (Branded)</option>
              <option value="52" data-label="Hide Contact Details + Pre-Screened Report without Company Branding" data-desc="Keep the name visible, hide contact and LinkedIn details, then merge a no-brand report.">52. Hide Contact Details + Pre-Screened Report without Company Branding</option>
              <option value="53" data-label="Hide Contact Details + Pre-Screened Report + Highlighted Keywords" data-desc="Full privacy package: masked contacts, highlighted skills, and pre-screened report dossier.">53. Hide Contact Details + Pre-Screened Report + Highlighted Keywords</option>
            </optgroup>
          </select>

          <!-- Hidden by default until user selects a format from the dropdown above -->
          <div id="test_fwd_format_details_section" style="display:none;">
            <!-- Live Attachment Format Summary & Deliverability Badge -->
            <div id="test_fwd_attachment_preview_card" style="margin-top:8px;padding:9px 12px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
              <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                <span style="width:26px;height:26px;border-radius:6px;background:#E2E8F0;color:#475569;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:700;">PDF</span>
                <div style="min-width:0;">
                  <div id="test_fwd_preview_format_title" style="font-size:12.5px;font-weight:600;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    Select Resume Format
                  </div>
                  <div id="test_fwd_preview_desc" style="font-size:11px;color:#64748B;margin-top:1px;">
                    Choose a resume format above to attach candidate resumes.
                  </div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                <span id="test_fwd_batch_count_badge" style="font-size:11px;font-weight:600;color:{{ count($applicationData ?? []) > 5 ? '#B91C1C' : '#15803D' }};background:{{ count($applicationData ?? []) > 5 ? '#FEE2E2' : '#DCFCE7' }};padding:3px 9px;border-radius:12px;white-space:nowrap;">
                  📎 {{ count($applicationData ?? []) }} / 5 Max Resumes
                </span>
              </div>
            </div>

            @if(($candidatesWithoutResumeCount ?? 0) > 0)
              <div id="test_fwd_missing_resume_banner" style="margin-top:10px;padding:9px 13px;background:#FFFBEB;border:1px solid #FCD34D;border-radius:8px;display:flex;align-items:center;gap:10px;">
                <span style="color:#D97706;font-size:16px;flex-shrink:0;">⚠</span>
                <div style="font-size:12px;color:#92400E;line-height:1.4;">
                  <strong>Notice:</strong> {{ $candidatesWithoutResumeCount }} of {{ count($applicationData ?? []) }} selected candidate profiles do not have a resume attached. You may attach a resume below or proceed using Option 5 (Pre-Screened Report).
                </div>
              </div>
            @endif

            <!-- Multi-Profile Attached Resumes Roster -->
            <div id="test_fwd_candidate_roster" style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
              @foreach($applicationData as $idx => $cand)
                @php
                  $hasResume = !empty($cand->has_resume);
                  $candSafeName = !empty($cand->full_name) ? str_replace(' ', '_', preg_replace('/[^\w\s.-]/', '', $cand->full_name)) : ('Candidate_' . $cand->id);
                  $candDisplayName = $cand->full_name ?? ('Candidate #' . ($idx + 1));
                  $candDesignation = $cand->current_designation ?? ($cand->job_title ?? 'Profile');
                @endphp
                <div class="test-fwd-cand-card" id="test_fwd_cand_card_{{ $cand->id }}" data-cand-id="{{ $cand->id }}" data-has-resume="{{ $hasResume ? '1' : '0' }}" style="padding:10px 14px;background:{{ $hasResume ? '#F0FDF4' : '#FFFBEB' }};border:1px solid {{ $hasResume ? '#BBF7D0' : '#FDE68A' }};border-radius:8px;transition:all 0.2s ease;">
                  <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1;">
                      {{-- Avatar / Sequence badge --}}
                      <div style="position:relative;flex-shrink:0;">
                        <span id="test_fwd_cand_avatar_{{ $cand->id }}" style="width:30px;height:30px;border-radius:7px;background:{{ $hasResume ? '#DCFCE7' : '#FEF3C7' }};color:{{ $hasResume ? '#15803D' : '#B45309' }};display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                          {{ $idx + 1 }}
                        </span>
                        <span id="test_fwd_cand_check_{{ $cand->id }}" style="position:absolute;bottom:-3px;right:-3px;width:14px;height:14px;border-radius:50%;background:{{ $hasResume ? '#10B981' : '#F59E0B' }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:bold;box-shadow:0 1px 2px rgba(0,0,0,0.15);">
                          {{ $hasResume ? '✓' : '!' }}
                        </span>
                      </div>

                      {{-- Candidate Name & Dynamic Filename --}}
                      <div style="min-width:0;flex:1;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:nowrap;">
                          <span style="font-size:12.5px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $candDisplayName }}
                          </span>
                          <span style="font-size:11px;color:#6B7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            • {{ $candDesignation }}
                          </span>
                        </div>
                        <div class="test-fwd-cand-filename" id="test_fwd_cand_filename_{{ $cand->id }}" data-safename="{{ $candSafeName }}" style="font-size:11px;font-family:monospace;color:{{ $hasResume ? '#059669' : '#B45309' }};margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                          {{ $hasResume ? ('Jeroworks_' . $candSafeName . '.pdf') : 'No resume uploaded' }}
                        </div>
                      </div>
                    </div>

                    {{-- Candidate Status Pill & Inline Upload Action --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                      <div id="test_fwd_cand_status_{{ $cand->id }}" style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:12px;background:{{ $hasResume ? '#DCFCE7' : '#FEF3C7' }};color:{{ $hasResume ? '#15803D' : '#B45309' }};display:inline-flex;align-items:center;gap:5px;white-space:nowrap;">
                        {{ $hasResume ? '✓ Attached (100%)' : '⚠ No Resume' }}
                      </div>

                      <div id="test_fwd_quick_upload_wrap_{{ $cand->id }}" style="{{ $hasResume ? 'display:none;' : 'display:inline-flex;' }}">
                        <input type="file" id="test_fwd_quick_file_{{ $cand->id }}" accept=".pdf,.doc,.docx" style="display:none;" onchange="uploadCandidateResumeInline({{ $cand->id }}, this)" />
                        <button type="button" class="btn btn-xs btn-light-primary fw-bold" onclick="document.getElementById('test_fwd_quick_file_{{ $cand->id }}').click();" style="font-size:10.5px;padding:3px 8px;border-radius:6px;" title="Upload Resume Document">
                          <span id="test_fwd_upload_spin_{{ $cand->id }}" class="spinner-border spinner-border-sm me-1" style="display:none;width:10px;height:10px;"></span>
                          <span id="test_fwd_upload_txt_{{ $cand->id }}">+ Upload CV</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  {{-- Candidate Progress Bar --}}
                  <div id="test_fwd_cand_progress_wrap_{{ $cand->id }}" style="display:none;margin-top:6px;">
                    <div style="height:3px;width:100%;background:#E5E7EB;border-radius:2px;overflow:hidden;">
                      <div id="test_fwd_cand_progress_bar_{{ $cand->id }}" style="height:100%;width:0%;background:#0D9488;border-radius:2px;transition:width 0.2s ease;"></div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Live Compilation Progress Bar Container -->
            <div id="test_fwd_compile_progress_container" style="display:none;margin-top:10px;padding:10px 14px;background:#F0FDFA;border:1px solid #99F6E4;border-radius:8px;">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:8px;">
                  <span id="test_fwd_compile_spinner" class="spinner-border spinner-border-sm" style="width:13px;height:13px;border-width:2px;color:#0D9488;"></span>
                  <span id="test_fwd_compile_status_text" style="font-size:12px;font-weight:600;color:#134E4A;">Preparing custom format in browser memory...</span>
                </div>
                <span id="test_fwd_compile_pct_badge" style="font-size:11px;font-weight:700;color:#0D9488;background:#CCFBF1;padding:2px 7px;border-radius:10px;border:1px solid #99F6E4;">
                  0%
                </span>
              </div>
              <div style="height:7px;width:100%;background:#CCFBF1;border-radius:4px;overflow:hidden;">
                <div id="test_fwd_compile_progress_bar" style="height:100%;width:0%;background:linear-gradient(90deg, #0D9488, #059669);border-radius:4px;transition:width 0.25s ease;"></div>
              </div>
              <div id="test_fwd_compile_hint" style="font-size:11px;color:#115E59;margin-top:5px;display:flex;align-items:center;justify-content:space-between;">
                <span id="test_fwd_compile_step_detail">Client-side document engine compiling high-resolution vector PDF...</span>
                <span id="test_fwd_compile_count">0 / {{ count($applicationData ?? []) }}</span>
              </div>
            </div>
            @if(count($applicationData ?? []) > 5)
              <div style="margin-top:7px;padding:7px 11px;background:#FEF2F2;border:1px solid #FECACA;border-radius:7px;color:#991B1B;font-size:11.5px;display:flex;align-items:center;gap:6px;">
                <span>⚠️</span>
                <span><strong>Notice:</strong> You selected {{ count($applicationData) }} candidates. To ensure 100% email deliverability and avoid mail server rejections, a maximum of 5 resumes will be attached per batch.</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Body --}}
    <div class="mm-row mm-row-body">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </span>
        Email Body
      </div>
      <div class="mm-field-col">
        <div class="mm-editor-wrap">
          <textarea id="test_editor1" name="editor1" rows="10">{{ @$template_body }}</textarea>
        </div>
      </div>
    </div>

  </div>
</form>
</div>

{{-- Enterprise Add Email Recipient Drawer --}}
<div id="kt_test_recipient_drawer_toggle" style="display:none;"></div>
<div id="kt_test_recipient_drawer" class="bg-white shadow" data-kt-drawer="true" data-kt-drawer-name="recipient-drawer-test" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'100%', 'sm': '480px', 'lg': '490px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_test_recipient_drawer_toggle" data-kt-drawer-close="#kt_test_recipient_drawer_close">
  <div class="rcp-container">
    <div class="rcp-header">
      <div class="rcp-header-left">
        <h3 class="rcp-header-title">Add Email Recipient</h3>
        <p class="rcp-header-subtitle">Choose a contact from your internal team or client company.</p>
      </div>
      <button type="button" class="rcp-close-btn" id="kt_test_recipient_drawer_close" onclick="closeTestEmailRecipientDrawer()" title="Close drawer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <div class="rcp-tabs-wrap">
      <div class="rcp-tab-card active" id="test_rcp_tab_internal" data-tab="internal">
        <div class="rcp-tab-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="rcp-tab-info">
          <div class="rcp-tab-title">Internal Team</div>
          <div class="rcp-tab-sub">{{ count($internal_team_data ?? []) }} active</div>
        </div>
      </div>
      <div class="rcp-tab-card" id="test_rcp_tab_client" data-tab="client">
        <div class="rcp-tab-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M8 10h.01"></path><path d="M16 10h.01"></path><path d="M8 14h.01"></path><path d="M16 14h.01"></path></svg>
        </div>
        <div class="rcp-tab-info">
          <div class="rcp-tab-title">Client Contacts</div>
          <div class="rcp-tab-sub">{{ count($client_contacts_data ?? []) }} available</div>
        </div>
      </div>
    </div>

    <div class="rcp-search-wrap">
      <div class="rcp-search-box">
        <span class="rcp-search-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </span>
        <input type="text" id="test_rcp_search_input" placeholder="Search by name, email, or role...">
      </div>
    </div>

    <div class="rcp-body-list">
      <div id="test_rcp_section_internal">
        @if(!empty($internal_team_data) && count($internal_team_data) > 0)
          @foreach($internal_team_data as $itUser)
            <div class="rcp-contact-item" data-name="{{ strtolower($itUser->name ?? '') }}" data-email="{{ strtolower($itUser->email ?? '') }}" data-role="{{ strtolower($itUser->display_name ?? ($itUser->role_name ?? '')) }}">
              <div style="display:flex;align-items:center;min-width:0;flex:1;">
                <div style="position:relative;margin-right:10px;flex-shrink:0;">
                  <div class="rcp-avatar" style="background:#EFF6FF;color:#2563EB;margin-right:0;">{{ strtoupper(substr($itUser->name ?? 'U', 0, 1)) }}</div>
                  <span style="position:absolute;bottom:0;right:0;width:8px;height:8px;border-radius:50%;background:#10B981;border:1.5px solid #fff;" title="Currently Active"></span>
                </div>
                <div class="rcp-info">
                  <div style="display:flex;align-items:center;gap:6px;">
                    <span class="rcp-name">{{ $itUser->name ?? 'Team Member' }}</span>
                    <span style="font-size:10px;padding:1px 6px;border-radius:10px;background:#DCFCE7;color:#15803D;font-weight:600;">Active</span>
                  </div>
                  <div class="rcp-email">{{ $itUser->email }}</div>
                  <div class="rcp-role">{{ $itUser->display_name ?? ($itUser->role_name ?? 'Team Member') }}</div>
                </div>
              </div>
              <button type="button" class="rcp-toggle-btn test-rcp-toggle-btn" data-email="{{ $itUser->email }}">
                <span class="rcp-btn-label-add">+ Add</span>
                <span class="rcp-btn-label-added" style="display:none;">✓ Added</span>
              </button>
            </div>
          @endforeach
        @else
          <div style="text-align:center;padding:36px 20px;color:#9CA3AF;font-size:13px;">No active internal team members found.</div>
        @endif
      </div>
      <div id="test_rcp_section_client" style="display:none;">
        @if(!empty($client_contacts_data))
          @foreach($client_contacts_data as $ccUser)
            <div class="rcp-contact-item" data-name="{{ strtolower($ccUser->name ?? '') }}" data-email="{{ strtolower($ccUser->email ?? '') }}" data-role="{{ strtolower($ccUser->display_name ?? '') }}">
              <div style="display:flex;align-items:center;min-width:0;flex:1;">
                <div class="rcp-avatar" style="background:#DCFCE7;color:#15803D;">{{ strtoupper(substr($ccUser->name ?? 'C', 0, 1)) }}</div>
                <div class="rcp-info">
                  <div class="rcp-name">{{ $ccUser->name ?? 'Client Contact' }}</div>
                  <div class="rcp-email">{{ $ccUser->email }}</div>
                  <div class="rcp-role">{{ $ccUser->company_name ?? 'Client' }}</div>
                </div>
              </div>
              <button type="button" class="rcp-toggle-btn test-rcp-toggle-btn" data-email="{{ $ccUser->email }}">
                <span class="rcp-btn-label-add">+ Add</span>
                <span class="rcp-btn-label-added" style="display:none;">✓ Added</span>
              </button>
            </div>
          @endforeach
        @endif
      </div>
    </div>

    <div class="rcp-footer">
      <span id="test_rcp_footer_count_text" style="font-size:12px;color:#6B7280;">0 recipients selected</span>
      <div style="display:flex;gap:8px;">
        <button type="button" class="rcp-btn-cancel" onclick="closeTestEmailRecipientDrawer()">Cancel</button>
        <button type="button" class="rcp-btn-done" onclick="closeTestEmailRecipientDrawer()">Done</button>
      </div>
    </div>
  </div>
</div>
</div>

<script>
window._testFwdCompiledAttachments = window._testFwdCompiledAttachments || {};
window._testFwdR2UploadedUuids = window._testFwdR2UploadedUuids || [];
var _testFwdGlobalToken = 0;
var _testFwdIsCompiling = false;

window.closeTestFwdResumeDrawer = function() {
    _testFwdGlobalToken++;
    _testFwdIsCompiling = false;
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
        try { CKEDITOR.instances['test_editor1'].destroy(true); } catch(e) {}
    }
    var drawerEl = document.getElementById('kt_test_fwd_resume_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { drawer.hide(); }
    }
    var drawerAptkEl = document.getElementById('kt_test_fwd_resume_drawer_aptk');
    if (drawerAptkEl && typeof KTDrawer !== 'undefined') {
        var drawerAptk = KTDrawer.getInstance(drawerAptkEl);
        if (drawerAptk) { drawerAptk.hide(); }
    }
    closeTestEmailRecipientDrawer();
    $('#application_test_fwd_resume_body_content').html('');
};

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    var k = 1024;
    var sizes = ['B', 'KB', 'MB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

window.uploadCandidateResumeInline = function(candidateId, inputEl) {
    if (!inputEl || !inputEl.files || inputEl.files.length === 0) return;
    var file = inputEl.files[0];

    var ext = file.name.split('.').pop().toLowerCase();
    if (['pdf', 'doc', 'docx'].indexOf(ext) === -1) {
        Swal.fire('Invalid File', 'Please select a PDF, DOC, or DOCX document.', 'warning');
        return;
    }
    if (file.size > 15 * 1024 * 1024) {
        Swal.fire('File Too Large', 'Maximum resume file size allowed is 15MB.', 'warning');
        return;
    }

    var spin = document.getElementById('test_fwd_upload_spin_' + candidateId);
    var txt = document.getElementById('test_fwd_upload_txt_' + candidateId);
    if (spin) spin.style.display = 'inline-block';
    if (txt) txt.textContent = 'Uploading...';

    var fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    fd.append('candidate_id', candidateId);
    fd.append('resume', file);

    $.ajax({
        url: "{{ route('service_company.cvtracker.quick_upload_resume') }}",
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(res) {
            if (spin) spin.style.display = 'none';
            if (txt) txt.textContent = '✓ Uploaded';

            if (res.status === 'success') {
                var card = $('#test_fwd_cand_card_' + candidateId);
                card.attr('data-has-resume', '1');
                card.css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
                $('#test_fwd_cand_avatar_' + candidateId).css({ 'background': '#DCFCE7', 'color': '#15803D' });
                $('#test_fwd_cand_check_' + candidateId).css({ 'background': '#10B981' }).text('✓');
                $('#test_fwd_cand_filename_' + candidateId).css({ 'color': '#059669' }).text(res.display_filename || 'Jeroworks_Resume.pdf');
                $('#test_fwd_cand_status_' + candidateId).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text('✓ Attached (100%)');
                $('#test_fwd_quick_upload_wrap_' + candidateId).html(
                    '<button type="button" class="btn btn-xs btn-light-success fw-bold" onclick="document.getElementById(\'test_fwd_quick_file_' + candidateId + '\').click();" style="font-size:10.5px;padding:3px 8px;border-radius:6px;" title="Replace CV">' +
                    '✓ Resume Attached <span style="font-size:9px;opacity:0.7;">(Change)</span></button>'
                );

                var remainingMissing = $('.test-fwd-cand-card[data-has-resume="0"]').length;
                if (remainingMissing === 0) {
                    $('#test_fwd_missing_resume_banner').fadeOut(200);
                }

                if (window._testFwdCompiledAttachments) {
                    for (var k in window._testFwdCompiledAttachments) {
                        if (window._testFwdCompiledAttachments[k]) {
                            delete window._testFwdCompiledAttachments[k][candidateId];
                        }
                    }
                }

                updateTestSendButtonState();

                if (typeof cvhToast !== 'undefined') {
                    cvhToast.fire({ icon: 'success', title: 'Resume attached successfully' });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Resume Attached',
                        text: 'Resume document has been attached to the candidate profile successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                if (txt) txt.textContent = '+ Upload CV';
                Swal.fire('Upload Failed', res.message || 'Could not upload resume', 'error');
            }
        },
        error: function(xhr) {
            if (spin) spin.style.display = 'none';
            if (txt) txt.textContent = '+ Upload CV';
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error uploading resume';
            Swal.fire('Upload Error', msg, 'error');
        }
    });
};

function updateTestRosterFilenames(optId) {
    $('.test-fwd-cand-filename').each(function () {
        var card = $(this).closest('.test-fwd-cand-card');
        var hasResume = card.attr('data-has-resume') === '1';
        var safeName = $(this).attr('data-safename') || 'Candidate';
        if (!optId) {
            if (hasResume) {
                $(this).css('color', '#374151').text(safeName + '.pdf');
            } else {
                $(this).css('color', '#B45309').text('No resume uploaded');
            }
            return;
        }
        var filename = 'Jeroworks_' + safeName + '.pdf';
        if (optId === 5) {
            filename = 'Prescreen_Report_' + safeName + '.pdf';
            $(this).css('color', '#059669').text(filename);
        } else if (hasResume) {
            if (optId === 49) {
                filename = 'Jeroworks_Resume.pdf';
            }
            $(this).css('color', '#059669').text(filename);
        } else {
            $(this).css('color', '#B45309').text('No resume uploaded');
        }
    });
}

function updateTestSendButtonState() {
    var rawOpt = $('#test_fwd_resume_format').val();
    var optId = rawOpt ? parseInt(rawOpt, 10) : 0;
    var btn = document.getElementById('btnTestSubmit');
    if (!btn) return;

    updateTestRosterFilenames(optId);

    var candidatesList = @json($applicationData ?? []);
    var selectedIds = [];
    $('.chk_list_item:checked').each(function () { selectedIds.push(String($(this).val())); });
    var candidatesToProcess = candidatesList.filter(function (cand) {
        return selectedIds.length === 0 || selectedIds.indexOf(String(cand.id)) !== -1;
    });
    if (candidatesToProcess.length === 0) {
        candidatesToProcess = candidatesList;
    }

    // Default state: No format chosen yet -> Keep Send ready for validation prompt, no background compilation
    if (!optId) {
        $('#test_fwd_format_details_section').hide();
        _testFwdGlobalToken++;
        _testFwdIsCompiling = false;
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        $('#test_fwd_compile_progress_container').hide();
        candidatesToProcess.forEach(function (c) {
            var card = $('#test_fwd_cand_card_' + c.id);
            var hasResume = card.attr('data-has-resume') === '1' || !!c.has_resume;
            if (hasResume) {
                card.css({ 'background': '#F9FAFB', 'border-color': '#E5E7EB' });
                $('#test_fwd_cand_avatar_' + c.id).css({ 'background': '#F3F4F6', 'color': '#374151' });
                $('#test_fwd_cand_check_' + c.id).css({ 'background': '#10B981', 'display': 'flex' }).text('✓');
                $('#test_fwd_cand_status_' + c.id).css({ 'background': '#F3F4F6', 'color': '#4B5563' }).text('Select Format Above');
            } else {
                card.css({ 'background': '#FFFBEB', 'border-color': '#FDE68A' });
                $('#test_fwd_cand_avatar_' + c.id).css({ 'background': '#FEF3C7', 'color': '#B45309' });
                $('#test_fwd_cand_check_' + c.id).css({ 'background': '#F59E0B', 'display': 'flex' }).text('!');
                $('#test_fwd_cand_status_' + c.id).css({ 'background': '#FEF3C7', 'color': '#B45309' }).text('⚠ No Resume');
            }
            $('#test_fwd_cand_progress_wrap_' + c.id).hide();
        });
        return;
    }

    $('#test_fwd_format_details_section').show();

    // Option 1: Send is immediately ready (no client compilation needed)
    if (optId === 1) {
        _testFwdGlobalToken++;
        _testFwdIsCompiling = false;
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        $('#test_fwd_compile_progress_container').hide();
        candidatesToProcess.forEach(function (c) {
            var card = $('#test_fwd_cand_card_' + c.id);
            var hasResume = card.attr('data-has-resume') === '1' || !!c.has_resume;
            if (hasResume) {
                card.css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
                $('#test_fwd_cand_avatar_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' });
                $('#test_fwd_cand_check_' + c.id).css({ 'background': '#10B981', 'display': 'flex' }).text('✓');
                $('#test_fwd_cand_status_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text('✓ Attached (100%)');
            } else {
                card.css({ 'background': '#FFFBEB', 'border-color': '#FDE68A' });
                $('#test_fwd_cand_avatar_' + c.id).css({ 'background': '#FEF3C7', 'color': '#B45309' });
                $('#test_fwd_cand_check_' + c.id).css({ 'background': '#F59E0B', 'display': 'flex' }).text('!');
                $('#test_fwd_cand_status_' + c.id).css({ 'background': '#FEF3C7', 'color': '#B45309' }).text('⚠ No Resume');
            }
            $('#test_fwd_cand_progress_wrap_' + c.id).hide();
        });
        return;
    }

    // Custom format (Option 5 or 41-53): Check if already compiled
    var cached = window._testFwdCompiledAttachments[optId] || {};
    var allReady = candidatesToProcess.length > 0 && candidatesToProcess.every(function (c) {
        var card = $('#test_fwd_cand_card_' + c.id);
        var hasResume = card.attr('data-has-resume') === '1' || !!c.has_resume;
        if (optId !== 5 && !hasResume) return true;
        return !!cached[c.id];
    });

    if (allReady) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        $('#test_fwd_compile_progress_container').show();
        $('#test_fwd_compile_spinner').hide();
        $('#test_fwd_compile_status_text').html('✓ All ready attachments prepared & verified (100%)');
        $('#test_fwd_compile_pct_badge').text('100%').css({ 'color': '#0D9488', 'background': '#CCFBF1', 'border-color': '#99F6E4' });
        $('#test_fwd_compile_progress_bar').css({ 'width': '100%', 'background': '#059669' });
        $('#test_fwd_compile_count').text(candidatesToProcess.length + ' / ' + candidatesToProcess.length);
        $('#test_fwd_compile_step_detail').text('Candidate attachments ready. You can send now.');

        candidatesToProcess.forEach(function (c) {
            var card = $('#test_fwd_cand_card_' + c.id);
            var hasResume = card.attr('data-has-resume') === '1' || !!c.has_resume;
            var blob = cached[c.id];
            var sizeStr = blob ? (' • ' + formatBytes(blob.size)) : '';

            if (optId === 5 || hasResume) {
                card.css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
                $('#test_fwd_cand_avatar_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' });
                $('#test_fwd_cand_check_' + c.id).css({ 'background': '#10B981', 'display': 'flex' }).text('✓');
                $('#test_fwd_cand_status_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text(
                    (optId === 5 ? '✓ Report Ready' : '✓ Attached (100%)') + sizeStr
                );
            } else {
                card.css({ 'background': '#FFFBEB', 'border-color': '#FDE68A' });
                $('#test_fwd_cand_avatar_' + c.id).css({ 'background': '#FEF3C7', 'color': '#B45309' });
                $('#test_fwd_cand_check_' + c.id).css({ 'background': '#F59E0B', 'display': 'flex' }).text('!');
                $('#test_fwd_cand_status_' + c.id).css({ 'background': '#FEF3C7', 'color': '#B45309' }).text('⚠ No Resume Attached');
            }
            $('#test_fwd_cand_progress_wrap_' + c.id).hide();
        });
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.style.cursor = 'not-allowed';
        $('#test_fwd_compile_progress_container').show();
        $('#test_fwd_compile_spinner').show();
        startTestCompilationForOption(optId);
    }
}

async function startTestCompilationForOption(optId) {
    if (!optId || optId === 1) return;
    _testFwdIsCompiling = true;
    var thisToken = ++_testFwdGlobalToken;

    var btn = document.getElementById('btnTestSubmit');
    if (btn) {
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.style.cursor = 'not-allowed';
    }

    $('#test_fwd_compile_progress_container').show();
    $('#test_fwd_compile_spinner').show();
    $('#test_fwd_compile_progress_bar').css({ 'width': '5%', 'background': 'linear-gradient(90deg, #0D9488, #059669)' });
    $('#test_fwd_compile_pct_badge').text('5%').css({ 'color': '#0D9488', 'background': '#CCFBF1', 'border-color': '#99F6E4' });
    $('#test_fwd_compile_status_text').text('Preparing resume attachments for delivery...');
    $('#test_fwd_compile_step_detail').text('Formatting candidate documents...');

    try {
        var engine = window.AgyForwardResumeEngine || window.AgyForwardResumeHelper;
        if (!engine || typeof engine.generateAttachmentForCandidate !== 'function') {
            throw new Error('Forward Resume engine core is not loaded.');
        }

        if (thisToken !== _testFwdGlobalToken) return;

        $('#test_fwd_compile_progress_bar').css('width', '10%');
        $('#test_fwd_compile_pct_badge').text('10%');
        $('#test_fwd_compile_status_text').text('Formatting ready. Preparing candidate resumes...');

        var candidatesList = @json($applicationData ?? []);
        var companyData = {
            companyName: @json($companyName ?? 'Hiring Partner'),
            companyLogo: @json($companyLogo ?? '')
        };
        var jobSkills = @json($jobSkills ?? []);

        var selectedIds = [];
        $('.chk_list_item:checked').each(function () { selectedIds.push(String($(this).val())); });
        var candidatesToProcess = candidatesList.filter(function (cand) {
            return selectedIds.length === 0 || selectedIds.indexOf(String(cand.id)) !== -1;
        });
        if (candidatesToProcess.length === 0) {
            candidatesToProcess = candidatesList;
        }

        if (!window._testFwdCompiledAttachments[optId]) {
            window._testFwdCompiledAttachments[optId] = {};
        }

        var total = candidatesToProcess.length;
        for (var i = 0; i < total; i++) {
            if (thisToken !== _testFwdGlobalToken) return;

            var cand = candidatesToProcess[i];
            var candName = cand.full_name || ('Candidate #' + (i + 1));
            var startPct = 10 + Math.round((i / total) * 90);
            var endPct = 10 + Math.round(((i + 1) / total) * 90);
            var card = $('#test_fwd_cand_card_' + cand.id);
            var hasResume = card.attr('data-has-resume') === '1' || !!cand.has_resume;

            if (optId !== 5 && !hasResume) {
                card.css({ 'background': '#FFFBEB', 'border-color': '#FDE68A' });
                $('#test_fwd_cand_avatar_' + cand.id).css({ 'background': '#FEF3C7', 'color': '#B45309' });
                $('#test_fwd_cand_check_' + cand.id).css({ 'background': '#F59E0B', 'display': 'flex' }).text('!');
                $('#test_fwd_cand_status_' + cand.id).css({ 'background': '#FEF3C7', 'color': '#B45309' }).text('⚠ No Resume Attached');
                $('#test_fwd_cand_progress_wrap_' + cand.id).hide();
                continue;
            }

            card.css({ 'background': '#F0FDFA', 'border-color': '#99F6E4' });
            $('#test_fwd_cand_progress_wrap_' + cand.id).show();
            $('#test_fwd_cand_progress_bar_' + cand.id).css('width', '25%');
            $('#test_fwd_cand_status_' + cand.id).css({ 'background': '#CCFBF1', 'color': '#0D9488' }).html(
                '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:1.5px;margin-right:4px;"></span> Preparing...'
            );

            $('#test_fwd_compile_progress_bar').css('width', startPct + '%');
            $('#test_fwd_compile_pct_badge').text(startPct + '%');
            $('#test_fwd_compile_status_text').text('Preparing ' + candName + ' (' + (i + 1) + '/' + total + ')...');
            $('#test_fwd_compile_count').text(i + ' / ' + total);

            if (!window._testFwdCompiledAttachments[optId][cand.id]) {
                var pdfBlob = await engine.generateAttachmentForCandidate(
                    cand,
                    optId,
                    companyData,
                    jobSkills,
                    function (subMsg) {
                        if (thisToken !== _testFwdGlobalToken) return;
                        var midPct = Math.round(startPct + (endPct - startPct) * 0.5);
                        $('#test_fwd_compile_progress_bar').css('width', midPct + '%');
                        $('#test_fwd_compile_pct_badge').text(midPct + '%');
                        $('#test_fwd_compile_step_detail').text(subMsg);
                        $('#test_fwd_cand_progress_bar_' + cand.id).css('width', '65%');
                    }
                );

                if (!pdfBlob) {
                    throw new Error('Failed to prepare resume for ' + candName);
                }
                window._testFwdCompiledAttachments[optId][cand.id] = pdfBlob;
            }

            if (thisToken !== _testFwdGlobalToken) return;

            var blob = window._testFwdCompiledAttachments[optId][cand.id];
            var sizeStr = blob ? (' • ' + formatBytes(blob.size)) : '';
            card.css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
            $('#test_fwd_cand_avatar_' + cand.id).css({ 'background': '#DCFCE7', 'color': '#15803D' });
            $('#test_fwd_cand_check_' + cand.id).css({ 'background': '#10B981', 'display': 'flex' }).text('✓');
            $('#test_fwd_cand_progress_wrap_' + cand.id).hide();
            $('#test_fwd_cand_status_' + cand.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text(
                (optId === 5 ? '✓ Report Ready' : '✓ Attached (100%)') + sizeStr
            );

            $('#test_fwd_compile_progress_bar').css('width', endPct + '%');
            $('#test_fwd_compile_pct_badge').text(endPct + '%');
            $('#test_fwd_compile_count').text((i + 1) + ' / ' + total);
        }

        if (thisToken !== _testFwdGlobalToken) return;

        _testFwdIsCompiling = false;
        $('#test_fwd_compile_progress_bar').css({ 'width': '100%', 'background': '#059669' });
        $('#test_fwd_compile_pct_badge').text('100%').css({ 'color': '#059669', 'background': '#DCFCE7', 'border-color': '#86EFAC' });
        $('#test_fwd_compile_spinner').hide();
        $('#test_fwd_compile_status_text').html('✓ All attachments prepared & verified (100%)');
        $('#test_fwd_compile_step_detail').text('All candidate attachments are verified and ready to send.');

        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    } catch (err) {
        if (thisToken !== _testFwdGlobalToken) return;
        _testFwdIsCompiling = false;
        console.error('Test resume compilation error:', err);
        $('#test_fwd_compile_spinner').hide();
        $('#test_fwd_compile_progress_bar').css({ 'background': '#EF4444' });
        $('#test_fwd_compile_pct_badge').text('Error').css({ 'color': '#DC2626', 'background': '#FEE2E2', 'border-color': '#FCA5A5' });
        $('#test_fwd_compile_status_text').html('⚠️ Unable to prepare the selected resume format: ' + (err.message || 'Formatting error'));
        $('#test_fwd_compile_step_detail').text('Please re-select the format or switch to Option 1 (Original Resume).');
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    }
}

window.onTestFwdFormatChange = function (el) {
    var $el = $(el || '#test_fwd_resume_format');
    var val = $el.val();
    if (!val) {
        $('#test_fwd_format_details_section').slideUp(150);
        $('#test_fwd_preview_format_title').text('Select Resume Format');
        $('#test_fwd_preview_desc').text('Choose a resume format above to attach candidate resumes.');
        $('#test_fwd_attachment_preview_card').css({ 'background': '#F8FAFC', 'border-color': '#E2E8F0' });
        $el.css('border-color', '#E5E7EB');
        updateTestSendButtonState();
        return;
    }
    $('#test_fwd_format_details_section').slideDown(150);
    var sel = $el.find('option:selected');
    var label = sel.attr('data-label') || sel.text();
    var desc = sel.attr('data-desc') || '';

    $('#test_fwd_preview_format_title').text(label);
    $('#test_fwd_preview_desc').text(desc);
    $('#test_fwd_attachment_preview_card').css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
    $el.css('border-color', '#E5E7EB');

    updateTestSendButtonState();
};

window.onTestFwdAttachToggle = function (el) {
    var $el = $(el || '#test_fwd_attach_cv_toggle');
    if ($el.is(':checked')) {
        $('#test_fwd_format_container').slideDown(150);
    } else {
        $('#test_fwd_format_container').slideUp(150);
    }
    updateTestSendButtonState();
};

// Template change populates Subject and Editor
window.onTestTemplateChange = function(selectEl) {
    var template = $(selectEl).val();
    if (template > 0) {
        var id = template;
        var url = "{{ route('service_company.cvtracker.email_template', ':id') }}";
        url = url.replace(':id', id);
        var token = "{{ csrf_token() }}";
        var to_email = ($('#test_to_email').val() || '').trim() || 'info@jero.co.in';

        var selectedApplicantListing = [];
        var jqueryarray = @json($applicationData ?? []);
        jqueryarray.forEach(function(mval) {
            selectedApplicantListing.push(mval.id);
        });
        var WijobIdArr = [];
        $('.chk_list_item:checked').each(function() {
            var wid = $(this).attr('data-wiseid');
            if (wid) WijobIdArr.push(wid);
        });

        $('#test_subject').attr('placeholder', 'Loading template...');

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                '_method': 'POST',
                'template': template,
                'selectedApplicantListing': selectedApplicantListing,
                'to_email': to_email,
                'mailtype': 1,
                'WijobIdArr': WijobIdArr
            },
            success: function(response) {
                if (response.status == "success") {
                    var desc = response.template_description || response.template_body || '';
                    $('#test_subject').val(response.subject || '');
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
                        try {
                            CKEDITOR.instances['test_editor1'].setData(desc);
                        } catch(e) {}
                    }
                    $('#test_editor1').val(desc);
                } else if (response.message) {
                    console.warn('Template load warning:', response.message);
                }
            },
            error: function(xhr) {
                console.error('Failed to load email template:', xhr);
            }
        });
    } else {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['test_editor1']) {
            try { CKEDITOR.instances['test_editor1'].setData(''); } catch(e) {}
        }
        $('#test_editor1').val('');
        $('#test_subject').val('');
    }
};

$(document).off('change', '#test_template').on('change', '#test_template', function() {
    window.onTestTemplateChange(this);
});

// Submit click handler
window.onTestFwdSubmitClick = async function (e) {
    if (e && e.preventDefault) e.preventDefault();
    var attached_cv = 1;
    var rawOpt = $('#test_fwd_resume_format').val();
    var optId = rawOpt ? parseInt(rawOpt, 10) : 0;

    // Validate Resume Format (Mandatory)
    if (!optId) {
        $('#test_fwd_resume_format').css('border-color', '#EF4444').focus();
        Swal.fire({
            title: 'Resume Format Required',
            text: 'Please select a resume format from the dropdown before sending.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }
    $('#test_fwd_resume_format').css('border-color', '#E5E7EB');

    // Commit any typed text in the tag inputs
    if (typeof processTestTagInputText === 'function') {
        var pendingTo = ($('#test_to_email_input').val() || '').trim();
        if (pendingTo) {
            processTestTagInputText('to', pendingTo);
            $('#test_to_email_input').val('');
        }
        var pendingCc = ($('#test_cc_email_input').val() || '').trim();
        if (pendingCc) {
            processTestTagInputText('cc', pendingCc);
            $('#test_cc_email_input').val('');
        }
    }

    var toList = (_testEmailTags && _testEmailTags.to) ? _testEmailTags.to : [];
    var ccList = (_testEmailTags && _testEmailTags.cc) ? _testEmailTags.cc : [];
    var to_email = toList.join(', ');
    var cc_email = ccList.join(', ');
    var subject  = ($('#test_subject').val() || '').trim();

    var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (toList.length === 0) {
        $('#test_to_email_tag_box').css('border-color', '#EF4444');
        $('#test_to_email_input').focus();
        Swal.fire({
            title: 'Recipient Email Required',
            text: 'Please enter at least one valid recipient email address in the "To" field.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }

    for (var ti = 0; ti < toList.length; ti++) {
        if (!emailRegex.test(toList[ti])) {
            $('#test_to_email_tag_box').css('border-color', '#EF4444');
            $('#test_to_email_input').focus();
            Swal.fire({
                title: 'Invalid Email Address',
                html: 'The recipient email address <code>' + $('<div>').text(toList[ti]).html() + '</code> is invalid.',
                icon: 'warning',
                confirmButtonColor: '#0D9488'
            });
            return;
        }
    }
    $('#test_to_email_tag_box').css('border-color', '#E5E7EB');

    if (ccList.length > 0) {
        for (var ci = 0; ci < ccList.length; ci++) {
            if (!emailRegex.test(ccList[ci])) {
                $('#test_cc_email_tag_box').css('border-color', '#EF4444');
                $('#test_cc_email_input').focus();
                Swal.fire({
                    title: 'Invalid CC Email Address',
                    html: 'The CC email address <code>' + $('<div>').text(ccList[ci]).html() + '</code> is invalid.',
                    icon: 'warning',
                    confirmButtonColor: '#0D9488'
                });
                return;
            }
        }
        $('#test_cc_email_tag_box').css('border-color', '#E5E7EB');
    }

    if (!subject) {
        $('#test_subject').css('border-color', '#EF4444').focus();
        Swal.fire({
            title: 'Subject Required',
            text: 'Please enter an email subject before forwarding resumes.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }
    $('#test_subject').css('border-color', '#E5E7EB');

    // Validate Email Body (Mandatory)
    var editor1 = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.test_editor1) ? CKEDITOR.instances.test_editor1.getData() : ($('#test_editor1').val() || '');
    var textOnly = $('<div>').html(editor1).text().trim().replace(/[\s\u00A0]+/g, '');
    if (!textOnly && editor1.indexOf('<img') === -1) {
        Swal.fire({
            title: 'Email Message Required',
            text: 'Email message body cannot be blank. Please enter a message before sending.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }

    // Missing Resume Safeguards
    if (attached_cv === 1 && optId !== 5) {
        var withResumeCount = 0;
        var missingCandNames = [];
        $('.test-fwd-cand-card').each(function() {
            var hasResume = $(this).attr('data-has-resume') === '1';
            var cname = $(this).find('.test-fwd-cand-filename').attr('data-safename') || 'Candidate';
            if (hasResume) {
                withResumeCount++;
            } else {
                missingCandNames.push(cname.replace(/_/g, ' '));
            }
        });

        if (withResumeCount === 0) {
            Swal.fire({
                title: 'No Resume Document Found',
                html: '<div style="font-size:13.5px;line-height:1.6;color:#374151;">' +
                      '<p style="margin-bottom:8px;">None of the selected candidate profiles have an uploaded resume file.</p>' +
                      '<p style="color:#6B7280;margin:0;">Please use the <strong>+ Upload CV</strong> button on the candidate card to upload a resume, or select <strong>5. Pre-Screened Report</strong>, or uncheck <em>Attach Candidate Resume</em> to send an email introduction only.</p>' +
                      '</div>',
                icon: 'warning',
                confirmButtonColor: '#0D9488',
                confirmButtonText: 'OK, Got It'
            });
            return;
        }

        if (missingCandNames.length > 0 && !window._testFwdConfirmedMissing) {
            Swal.fire({
                title: 'Missing Resume Notice',
                html: '<div style="font-size:13.5px;line-height:1.6;color:#374151;">' +
                      '<p style="margin-bottom:8px;"><strong>' + missingCandNames.length + ' candidate(s)</strong> (' + missingCandNames.slice(0, 3).join(', ') + (missingCandNames.length > 3 ? '...' : '') + ') do not have an uploaded resume document.</p>' +
                      '<p style="color:#4B5563;margin:0;">Resume attachments will only be included for the remaining <strong>' + withResumeCount + ' candidate(s)</strong>. Do you want to proceed?</p>' +
                      '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0D9488',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Proceed & Send',
                cancelButtonText: 'Review & Upload CV'
            }).then(function(result) {
                if (result.isConfirmed) {
                    window._testFwdConfirmedMissing = true;
                    $('#btnTestSubmit').trigger('click');
                }
            });
            return;
        }
        window._testFwdConfirmedMissing = false;
    }

    if (attached_cv === 1 && optId !== 1 && _testFwdIsCompiling) {
        Swal.fire('Please Wait', 'Resume attachments are still being prepared. Please wait until progress reaches 100%.', 'info');
        return;
    }

    var btn = document.getElementById("btnTestSubmit");
    btn.disabled = true;
    btn.style.cursor = 'not-allowed';
    btn.style.opacity = '0.85';

    $(btn).find('.indicator-label').hide();
    $(btn).find('.indicator-progress').css('display', 'inline-flex').show();
    $('#test_fwd_btn_progress_text').text('Sending...');

    function restoreBtn() {
        $(btn).find('.indicator-progress').hide();
        $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        btn.disabled = false;
        btn.style.cursor = 'pointer';
        btn.style.opacity = '1';
    }

    var token = "{{ csrf_token() }}";
    var arr = [];
    $('.chk_list_item:checked').each(function () { arr.push($(this).val()); });

    var template = $('#test_template').val();
    var from_email = $('#test_from_email').val();
    var reply_to_email = $('#test_reply_to_email').val() || from_email;

    var uploadedAttachmentIds = [];

    // If attached_cv == 1 and optId != 1, prepare attachments
    if (attached_cv === 1 && optId !== 1) {
        var cached = window._testFwdCompiledAttachments[optId] || {};
        var candidatesList = @json($applicationData ?? []);
        var selectedIds = arr.map(function(aid) { return String(aid); });
        var candidatesToProcess = candidatesList.filter(function (cand) {
            return selectedIds.length === 0 || selectedIds.indexOf(String(cand.id)) !== -1;
        });
        if (candidatesToProcess.length === 0) {
            candidatesToProcess = candidatesList;
        }

        try {
            var filesToInitiate = [];
            var candidateBlobs = {};

            for (var c = 0; c < candidatesToProcess.length; c++) {
                var cand = candidatesToProcess[c];
                var blob = cached[cand.id];
                if (!blob) {
                    var companyData = {
                        companyName: @json($companyName ?? 'Hiring Partner'),
                        companyLogo: @json($companyLogo ?? '')
                    };
                    var jobSkills = @json($jobSkills ?? []);
                    var engine = window.AgyForwardResumeEngine || window.AgyForwardResumeHelper;
                    blob = await engine.generateAttachmentForCandidate(cand, optId, companyData, jobSkills);
                    cached[cand.id] = blob;
                }
                if (blob) {
                    var safeCandName = (cand.full_name || 'Candidate').replace(/[^\w\s.-]/g, '').replace(/\s+/g, '_');
                    var defaultName = (optId === 5) ? ('Prescreen_Report_' + safeCandName + '.pdf') : ('Jeroworks_' + safeCandName + '.pdf');
                    var outName = (blob && blob.filename) ? blob.filename : defaultName;
                    candidateBlobs[cand.id] = { blob: blob, filename: outName };
                    filesToInitiate.push({
                        candidate_id: cand.id,
                        candidate_name: cand.full_name || ('Candidate #' + cand.id),
                        filename: outName,
                        size: blob.size,
                        mime: blob.type || 'application/pdf'
                    });
                }
            }

            if (filesToInitiate.length > 0) {
                var initiateJson = await new Promise(function(resolve, reject) {
                    $.ajax({
                        url: "{{ route('service_company.cvtracker.test_fwd_resume_intent') }}",
                        type: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token
                        },
                        data: JSON.stringify({ files: filesToInitiate }),
                        success: resolve,
                        error: function(xhr, status, error) {
                            resolve({ status: 'fallback', message: (xhr.responseJSON && xhr.responseJSON.message) || error });
                        }
                    });
                });

                var attMap = (initiateJson && (initiateJson.attachments || initiateJson.uploads)) ? (initiateJson.attachments || initiateJson.uploads) : null;

                if (attMap) {
                    var uploadResults = await Promise.allSettled(filesToInitiate.map(async function (item) {
                        var initInfo = attMap[item.candidate_id];
                        if (!initInfo || !initInfo.upload_url) {
                            throw new Error('Missing upload URL for candidate ' + item.candidate_id);
                        }
                        var blobInfo = candidateBlobs[item.candidate_id];
                        var r2Res = await fetch(initInfo.upload_url, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': item.mime || 'application/pdf'
                            },
                            body: blobInfo.blob
                        });
                        if (!r2Res.ok) {
                            throw new Error('Upload service returned HTTP ' + r2Res.status);
                        }
                        return {
                            candidate_id: item.candidate_id,
                            attachment_id: initInfo.attachment_id
                        };
                    }));

                    uploadResults.forEach(function (res) {
                        if (res.status === 'fulfilled' && res.value && res.value.attachment_id) {
                            uploadedAttachmentIds.push(res.value.attachment_id);
                        }
                    });
                }
            }
        } catch (prepErr) {
            console.error('Attachment preparation error:', prepErr);
        }
    }

    $.ajax({
        url: "{{ route('service_company.cvtracker.test_fwd_resume_send') }}",
        type: 'POST',
        data: {
            '_token': token,
            'template': template,
            'from_email': from_email,
            'reply_to_email': reply_to_email,
            'to_email': to_email,
            'cc_email': cc_email,
            'subject': subject,
            'attached_cv': attached_cv,
            'attached_cv_format': optId,
            'editor1': editor1,
            'arr': arr,
            'attachment_uuids': uploadedAttachmentIds,
            'attachment_ids': uploadedAttachmentIds
        },
        success: function (response) {
            restoreBtn();
            if (response.status === 'success') {
                window._testFwdCompiledAttachments = {};
                window._testFwdR2UploadedUuids = [];
                if (typeof window.closeTestFwdResumeDrawer === 'function') {
                    try { window.closeTestFwdResumeDrawer(); } catch(e) {}
                }
                if (typeof window.resetCvTrackerSelection === 'function') {
                    try { window.resetCvTrackerSelection(); } catch(e) {}
                } else if (typeof window.clearCvSelection === 'function') {
                    try { window.clearCvSelection(); } catch(e) {}
                } else {
                    $('.chk_list, .chk_list_item, #cvh_selection_master_chk, #chk_all_list').prop('checked', false);
                    $('#myTable tbody tr').removeClass('table-active selected');
                    $('.fwd_resume, .test_fwd_resume, .send_email, .mass_mail, .app_copy, .downloadexcell, .application_copy').addClass('d-none');
                    $('.custom_hide').removeClass('d-none');
                    $('#cvh_selected_count').text('0');
                    $('#cvh_selection_bar').removeClass('show');
                }
                Swal.fire({
                    title: 'Forwarded Successfully',
                    text: response.message || 'Resumes forwarded successfully to ' + toList.join(', '),
                    icon: 'success',
                    confirmButtonColor: '#0D9488'
                });
            } else if (response.status === 'coinCheck') {
                Swal.fire({
                    title: 'Insufficient Coins',
                    text: response.message || 'Your account does not have enough coins.',
                    icon: 'warning',
                    confirmButtonColor: '#0D9488'
                });
            } else {
                Swal.fire({
                    title: 'Dispatch Notice',
                    text: response.message || 'Server error forwarding resumes.',
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            }
        },
        error: function (xhr) {
            restoreBtn();
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to dispatch email';
            Swal.fire('Error', msg, 'error');
        }
    });
};

// ══════════════════════════════════════════════════════════════════════════════
// EMAIL TAG / CHIP CONTROLLER (TO & CC)
// ══════════════════════════════════════════════════════════════════════════════
window._testEmailTags = {
    to: [],
    cc: []
};

var emailTagRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

window.renderTestTags = function(target) {
    var list = window._testEmailTags[target] || [];
    var $container = (target === 'to') ? $('#test_to_email_tags') : $('#test_cc_email_tags');
    var $hidden = (target === 'to') ? $('#test_to_email') : $('#test_cc_email_panel');
    var $input = (target === 'to') ? $('#test_to_email_input') : $('#test_cc_email_input');

    $container.empty();
    list.forEach(function(email, idx) {
        var isValid = emailTagRegex.test(email);
        var $tag = $(
            '<span class="mm-email-tag' + (isValid ? '' : ' is-invalid') + '" title="' + $('<div>').text(email).html() + '">' +
              '<span class="mm-email-tag-text">' + $('<div>').text(email).html() + '</span>' +
              '<span class="mm-email-tag-remove" data-target="' + target + '" data-idx="' + idx + '" title="Remove email">&times;</span>' +
            '</span>'
        );
        $container.append($tag);
    });

    $hidden.val(list.join(', ')).trigger('input');

    if (list.length > 0) {
        $input.attr('placeholder', 'Add another email...');
    } else {
        $input.attr('placeholder', (target === 'to') ? 'Type recipient email and press Enter...' : 'Type CC email and press Enter...');
    }

    syncTestRecipientDrawerState();
};

window.addTestEmailTag = function(target, email) {
    if (!email) return false;
    email = String(email).trim();
    if (!email) return false;

    if (!window._testEmailTags[target]) window._testEmailTags[target] = [];
    var lower = email.toLowerCase();
    var exists = window._testEmailTags[target].some(function(e) { return e.toLowerCase() === lower; });
    if (!exists) {
        window._testEmailTags[target].push(email);
        renderTestTags(target);
        return true;
    }
    return false;
};

window.removeTestEmailTag = function(target, index) {
    if (window._testEmailTags[target] && window._testEmailTags[target][index] !== undefined) {
        window._testEmailTags[target].splice(index, 1);
        renderTestTags(target);
    }
};

window.removeTestEmailByAddress = function(target, email) {
    if (!window._testEmailTags[target]) return;
    var lower = String(email || '').trim().toLowerCase();
    var idx = window._testEmailTags[target].findIndex(function(e) { return e.toLowerCase() === lower; });
    if (idx !== -1) {
        window._testEmailTags[target].splice(idx, 1);
        renderTestTags(target);
    }
};

window.processTestTagInputText = function(target, text) {
    if (!text) return;
    var items = String(text).split(/[\s,;]+/).map(function(s) { return s.trim(); }).filter(Boolean);
    items.forEach(function(item) {
        addTestEmailTag(target, item);
    });
};

function setupTestTagInput(target) {
    var $box = (target === 'to') ? $('#test_to_email_tag_box') : $('#test_cc_email_tag_box');
    var $input = (target === 'to') ? $('#test_to_email_input') : $('#test_cc_email_input');

    $input.off('focus blur keydown paste').on('focus', function() {
        $box.addClass('is-focused');
    }).on('blur', function() {
        $box.removeClass('is-focused');
        var val = $(this).val().trim();
        if (val) {
            processTestTagInputText(target, val);
            $(this).val('');
        }
    }).on('keydown', function(e) {
        var val = $(this).val();
        if (e.key === 'Enter' || e.key === ',' || e.keyCode === 13 || e.keyCode === 188 || e.key === 'Tab' || e.keyCode === 9) {
            if (val.trim()) {
                e.preventDefault();
                processTestTagInputText(target, val);
                $(this).val('');
            }
        } else if ((e.key === 'Backspace' || e.keyCode === 8) && !val) {
            if (window._testEmailTags[target].length > 0) {
                removeTestEmailTag(target, window._testEmailTags[target].length - 1);
            }
        }
    }).on('paste', function(e) {
        var pasted = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text') : '';
        if (pasted && (pasted.indexOf(',') !== -1 || pasted.indexOf(';') !== -1 || pasted.indexOf('\n') !== -1 || pasted.indexOf(' ') !== -1)) {
            e.preventDefault();
            processTestTagInputText(target, pasted);
            $(this).val('');
        }
    });

    $box.off('click', '.mm-email-tag-remove').on('click', '.mm-email-tag-remove', function(e) {
        e.stopPropagation();
        var t = $(this).attr('data-target') || target;
        var idx = parseInt($(this).attr('data-idx'), 10);
        removeTestEmailTag(t, idx);
    });
}

// ══════════════════════════════════════════════════════════════════════════════
// RECIPIENT DRAWER CONTROLLER
// ══════════════════════════════════════════════════════════════════════════════
var _testCurrentRcpTarget = 'to';

function getTestExistingEmails() {
    var list = (window._testEmailTags && window._testEmailTags[_testCurrentRcpTarget]) ? window._testEmailTags[_testCurrentRcpTarget] : [];
    return list.map(function(e) { return e.trim().toLowerCase(); }).filter(Boolean);
}

function syncTestRecipientDrawerState() {
    var existing = getTestExistingEmails();
    $('.test-rcp-toggle-btn').each(function() {
        var email = ($(this).attr('data-email') || '').trim().toLowerCase();
        if (email && existing.indexOf(email) !== -1) {
            $(this).addClass('is-added');
            $(this).find('.rcp-btn-label-add').hide();
            $(this).find('.rcp-btn-label-added').css('display', 'inline-flex').show();
        } else {
            $(this).removeClass('is-added');
            $(this).find('.rcp-btn-label-added').hide();
            $(this).find('.rcp-btn-label-add').show();
        }
    });
    var count = existing.length;
    $('#test_rcp_footer_count_text').text(count + (count === 1 ? ' recipient selected' : ' recipients selected'));
}

window.openTestEmailRecipientDrawer = function(target) {
    _testCurrentRcpTarget = target || 'to';
    syncTestRecipientDrawerState();
    $('#test_rcp_search_input').val('');
    applyTestRcpSearch();

    var drawerEl = document.getElementById('kt_test_recipient_drawer');
    if (drawerEl) {
        if (drawerEl.parentElement !== document.body) {
            $('body > #kt_test_recipient_drawer').not(drawerEl).remove();
            document.body.appendChild(drawerEl);
        }
        if (typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (!drawer) {
                KTDrawer.createInstances();
                drawer = KTDrawer.getInstance(drawerEl);
            }
            if (drawer) { drawer.show(); }
        }
    }
};

window.closeTestEmailRecipientDrawer = function() {
    var drawerEl = document.getElementById('kt_test_recipient_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { drawer.hide(); }
    }
};

$(document).off('click', '.test_to_email_panel').on('click', '.test_to_email_panel', function(e) {
    e.preventDefault();
    openTestEmailRecipientDrawer('to');
});
$(document).off('click', '.test_cc_email_panel').on('click', '.test_cc_email_panel', function(e) {
    e.preventDefault();
    openTestEmailRecipientDrawer('cc');
});

// Tab switching
$(document).off('click', '#test_rcp_tab_internal, #test_rcp_tab_client').on('click', '#test_rcp_tab_internal, #test_rcp_tab_client', function() {
    var tab = $(this).attr('data-tab');
    $('#test_rcp_tab_internal, #test_rcp_tab_client').removeClass('active');
    $(this).addClass('active');
    if (tab === 'internal') {
        $('#test_rcp_section_internal').show();
        $('#test_rcp_section_client').hide();
    } else {
        $('#test_rcp_section_internal').hide();
        $('#test_rcp_section_client').show();
    }
    applyTestRcpSearch();
});

// Toggle Add/Added button in recipient drawer
$(document).off('click', '.test-rcp-toggle-btn').on('click', '.test-rcp-toggle-btn', function(e) {
    e.preventDefault();
    var email = ($(this).attr('data-email') || '').trim();
    if (!email) return;

    var existing = getTestExistingEmails();
    var lower = email.toLowerCase();
    if (existing.indexOf(lower) === -1) {
        addTestEmailTag(_testCurrentRcpTarget, email);
    } else {
        removeTestEmailByAddress(_testCurrentRcpTarget, email);
    }
    syncTestRecipientDrawerState();
});

function applyTestRcpSearch() {
    var q = ($('#test_rcp_search_input').val() || '').toLowerCase().trim();
    var isInternal = $('#test_rcp_tab_internal').hasClass('active');
    var $sec = isInternal ? $('#test_rcp_section_internal') : $('#test_rcp_section_client');

    $sec.find('.rcp-contact-item').each(function() {
        var name = $(this).attr('data-name') || '';
        var email = $(this).attr('data-email') || '';
        var role = $(this).attr('data-role') || '';
        var match = !q || name.indexOf(q) !== -1 || email.indexOf(q) !== -1 || role.indexOf(q) !== -1;
        $(this).toggle(match);
    });
}
$(document).off('input', '#test_rcp_search_input').on('input', '#test_rcp_search_input', applyTestRcpSearch);

// Initial synchronization on drawer load
function initTestDrawerTagsAndSendState() {
    // Populate tags from initial hidden inputs
    var initTo = ($('#test_to_email').val() || '').trim();
    window._testEmailTags.to = initTo ? initTo.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];
    renderTestTags('to');

    var initCc = ($('#test_cc_email_panel').val() || '').trim();
    window._testEmailTags.cc = initCc ? initCc.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];
    renderTestTags('cc');

    setupTestTagInput('to');
    setupTestTagInput('cc');

    updateTestSendButtonState();
}

$(document).ready(function () {
    setTimeout(initTestDrawerTagsAndSendState, 100);
});
setTimeout(initTestDrawerTagsAndSendState, 200);
</script>
