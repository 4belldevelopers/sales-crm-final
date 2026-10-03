@php 
    use App\Helper\GeneralMethod;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $cand = $applicationData[0] ?? null;
@endphp 

@include('service_company.bench-candidates.partials._bench_forward_resume_engine') 
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
if (typeof CKEDITOR !== 'undefined' && CKEDITOR.env && CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

function initBenchFwdCkEditor() {
    if (typeof CKEDITOR === 'undefined') {
        setTimeout(initBenchFwdCkEditor, 100);
        return;
    }
    var editorElement = document.getElementById('bench_editor1');
    if (!editorElement) {
        setTimeout(initBenchFwdCkEditor, 100);
        return;
    }
    if (CKEDITOR.instances && CKEDITOR.instances['bench_editor1']) {
        try {
            CKEDITOR.instances['bench_editor1'].destroy(true);
        } catch(destroyErr) {}
    }
    try {
        CKEDITOR.config.height = 280;
        CKEDITOR.config.width = '100%';
        CKEDITOR.replace('bench_editor1', {
            height: 280,
            width: '100%'
        });
    } catch(err) {
        console.warn('CKEditor bench init error:', err);
    }
}

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(initBenchFwdCkEditor, 50);
} else {
    document.addEventListener('DOMContentLoaded', initBenchFwdCkEditor);
}
$(document).ready(function() {
    setTimeout(initBenchFwdCkEditor, 100);
});
setTimeout(initBenchFwdCkEditor, 250);
</script>

<div class="main_container" id="bench_fwd_main_container">
@php
$to_email = '';
$cc_email = '';
if(!empty($cc_email_count) && $cc_email_count > 0 && !empty($cc_email_data)){
    $cc_email = implode(', ', $cc_email_data);
}
@endphp

<style>
/* mm-* form design — isolated for bench candidates forward resume drawer */
.bench-mm-wrap { font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; display: flex; flex-direction: column; height: 100%; min-height: 100%; }
#bench_fwd_form { display: flex; flex-direction: column; height: 100%; flex: 1; min-height: 0; }
.bench-mm-form-body { flex: 1; overflow-y: auto; scrollbar-gutter: stable; }
.bench-mm-row { display: flex; align-items: flex-start; padding: 14px 24px; border-bottom: 1px solid #F3F4F6; gap: 16px; }
.bench-mm-label-col { width: 150px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding-top: 9px; font-size: 12.5px; font-weight: 600; color: #374151; }
.bench-mm-label-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.bench-mm-label-icon svg { width: 14px; height: 14px; }
.bench-mm-ic-blue   { background: #EFF6FF; color: #2563EB; }
.bench-mm-ic-green  { background: #DCFCE7; color: #16A34A; }
.bench-mm-ic-purple { background: #F5F3FF; color: #7C3AED; }
.bench-mm-ic-teal   { background: #F0FDF4; color: #0D9488; }
.bench-mm-ic-orange { background: #FFF7ED; color: #EA580C; }
.bench-mm-req { color: #EF4444; margin-left: 1px; }
.bench-mm-field-col { flex: 1; min-width: 0; position: relative; }
.bench-mm-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB; outline: none; transition: border-color 0.15s; }
.bench-mm-input:focus { border-color: #93C5FD; background: #fff; }
.bench-mm-input[readonly] { color: #6B7280; cursor: default; background: #F3F4F6; }
.bench-mm-hint { font-size: 11.5px; color: #9CA3AF; margin-top: 5px; line-height: 1.4; }
.bench-mm-select { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 36px 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; -webkit-appearance: none; outline: none; cursor: pointer; transition: border-color 0.15s; }
.bench-mm-select:focus { border-color: #93C5FD; background-color: #fff; }

.bench-mm-editor-wrap { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; background: #FFFFFF; }
.bench-mm-editor-wrap .cke { width: 100% !important; border: none !important; }
#bench_editor1 { width: 100% !important; min-height: 240px; padding: 12px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; box-sizing: border-box; }
.bench-mm-add-email-btn { position: absolute; right: 0; top: 0; display: flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: #2563EB; background: none; border: none; cursor: pointer; padding: 9px 14px; z-index: 10; }
.bench-mm-add-email-btn:hover { color: #1d4ed8; }

/* Tag / Chip input container for To and CC email addresses */
.bench-mm-tag-box {
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
.bench-mm-tag-box.is-focused {
  border-color: #93C5FD;
  background-color: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.25);
}
.bench-mm-tag-list {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 6px;
}
.bench-mm-email-tag {
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
.bench-mm-email-tag.is-invalid {
  background: #FEF2F2;
  border-color: #FECACA;
  color: #DC2626;
}
.bench-mm-email-tag-text {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.bench-mm-email-tag-remove {
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
.bench-mm-email-tag-remove:hover {
  background: #DBEAFE;
  color: #1E3A8A;
}
.bench-mm-tag-input {
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
.bench-mm-header {
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
.bench-mm-header-left {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}
.bench-mm-header-icon-box {
  width: 42px;
  height: 42px;
  min-width: 42px;
  border-radius: 9px;
  background: #EEF2FF;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #E0E7FF;
}
.bench-mm-header-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 2px;
  min-width: 0;
}
.bench-mm-header-title {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #1E1B4B;
  letter-spacing: -0.2px;
  line-height: 1.25;
}
.bench-mm-header-subtitle {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 400;
  color: #6B7280;
  line-height: 1.3;
}
.bench-mm-header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}
.bench-mm-header-btn-cancel {
  padding: 7px 18px;
  border-radius: 7px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  color: #374151;
  font-size: 13px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  transition: all 0.15s ease;
}
.bench-mm-header-btn-cancel:hover {
  background: #F9FAFB;
  border-color: #D1D5DB;
  color: #111827;
}
.bench-mm-header-btn-send {
  padding: 7px 20px;
  border-radius: 7px;
  border: none;
  background: #1B64F2;
  color: #FFFFFF;
  font-size: 13px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-width: 125px;
  box-shadow: 0 1px 3px rgba(27, 100, 242, 0.25);
  transition: all 0.15s ease;
}
.bench-mm-header-btn-send:hover { background: #1550C9; }
.bench-mm-header-btn-send:disabled { background: #93C5FD !important; cursor: not-allowed !important; }

/* ══════════════════════════════════════════════════════════════════════════════
   BENCH RECIPIENT DRAWER STYLES
══════════════════════════════════════════════════════════════════════════════ */
#kt_bench_recipient_drawer {
  width: 480px;
  max-width: 100vw;
  height: 100vh;
  background: #FFFFFF !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  z-index: 10060 !important;
  box-shadow: -8px 0 32px rgba(0, 0, 0, 0.16);
  position: fixed !important;
  top: 0 !important;
  right: 0 !important;
  display: none;
}
@media (max-width: 576px) {
  #kt_bench_recipient_drawer { width: 100vw !important; }
}
.bench-rcp-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 100%;
  background: #FFFFFF !important;
  overflow: hidden;
  position: relative;
  z-index: 1;
}
.bench-rcp-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 18px 24px 14px 24px;
  flex-shrink: 0;
  border-bottom: 1px solid #F3F4F6;
}
.bench-rcp-header-title {
  margin: 0;
  font-size: 16.5px;
  font-weight: 700;
  color: #111827;
  letter-spacing: -0.2px;
}
.bench-rcp-header-subtitle {
  margin: 3px 0 0 0;
  font-size: 12px;
  color: #6B7280;
  font-weight: 400;
}
.bench-rcp-close-btn {
  background: none;
  border: none;
  padding: 6px;
  margin: -4px -6px 0 0;
  border-radius: 6px;
  color: #9CA3AF;
  cursor: pointer;
}
.bench-rcp-close-btn:hover { background: #F3F4F6; color: #111827; }

/* Tabs */
.bench-rcp-tabs-wrap {
  display: flex;
  gap: 12px;
  padding: 14px 24px 12px 24px;
  flex-shrink: 0;
}
.bench-rcp-tab-card {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  cursor: pointer;
  transition: all 0.15s ease;
}
.bench-rcp-tab-card:hover { border-color: #CBD5E1; background: #F8FAFC; }
.bench-rcp-tab-card.active { border: 1.5px solid #2563EB; background: #FFFFFF; }
.bench-rcp-tab-title { font-size: 12.5px; font-weight: 700; color: #111827; line-height: 1.2; }
.bench-rcp-tab-sub { font-size: 11px; color: #6B7280; margin-top: 1px; }

/* Search */
.bench-rcp-search-wrap { padding: 0 24px 12px 24px; flex-shrink: 0; }
.bench-rcp-search-input {
  width: 100%;
  padding: 8px 12px 8px 34px;
  border: 1px solid #E5E7EB;
  border-radius: 7px;
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  color: #1F2937;
  outline: none;
}
.bench-rcp-search-input:focus { border-color: #3B82F6; }

/* List */
.bench-rcp-list-wrap { flex: 1; overflow-y: auto; padding: 0 24px; }
.bench-rcp-contact-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #F3F4F6;
  gap: 12px;
}
.bench-rcp-contact-name { font-size: 12.5px; font-weight: 700; color: #111827; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bench-rcp-email-line { font-size: 11px; color: #6B7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bench-rcp-role-pill { background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; border-radius: 10px; padding: 1px 6px; font-size: 10px; font-weight: 500; }
.bench-rcp-toggle-btn {
  padding: 4px 14px;
  border-radius: 6px;
  border: 1px solid #D1D5DB;
  background: #FFFFFF;
  color: #2563EB;
  font-size: 11.5px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
}
.bench-rcp-toggle-btn:hover { background: #EFF6FF; border-color: #2563EB; }
.bench-rcp-toggle-btn.is-added { background: #DCFCE7; border-color: #86EFAC; color: #16A34A; }
.bench-rcp-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 24px;
  border-top: 1px solid #E5E7EB;
  background: #FFFFFF;
  flex-shrink: 0;
}
</style>

<div class="bench-mm-wrap">
<form id="bench_fwd_form" enctype="multipart/form-data">
  @csrf
  {{-- Replicated Header --}}
  <div class="bench-mm-header">
    <div class="bench-mm-header-left">
      <div class="bench-mm-header-icon-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="4" width="20" height="16" rx="2"/>
          <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
        </svg>
      </div>
      <div class="bench-mm-header-text">
        <h3 class="bench-mm-header-title">Forward Candidate Resume</h3>
        <p class="bench-mm-header-subtitle">Send candidate profile and formatted dossier to client or hiring stakeholder.</p>
      </div>
    </div>
    <div class="bench-mm-header-actions">
      <button type="button" class="bench-mm-header-btn-cancel" onclick="closeBenchFwdResumeDrawer()">Cancel</button>
      <button type="button" class="bench-mm-header-btn-send" id="btnBenchFwdSubmit" onclick="onBenchFwdSubmitClick(event)" disabled>
        <span class="indicator-label" style="display:inline-flex;align-items:center;gap:7px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Email
        </span>
        <span class="indicator-progress" style="display:none;align-items:center;gap:7px;">
          <span class="spinner-border spinner-border-sm" style="width:13px;height:13px;border-width:2px;display:inline-block;"></span>
          <span id="bench_fwd_btn_progress_text">Compiling resume PDF...</span>
        </span>
      </button>
    </div>
  </div>

  <div class="bench-mm-form-body">

    {{-- From + Reply To --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
        </span>
        From
      </div>
      <div class="bench-mm-field-col" style="display:flex;gap:12px;align-items:center;">
        <select class="bench-mm-select" name="from_email" id="bench_fwd_from_email" style="flex:1;min-width:0;">
          <option value="notify@jeroworks.com">{{ $userdata->name ?? $userdata->email }} &lt;notify@jeroworks.com&gt;</option>
        </select>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12.5px;font-weight:600;color:#374151;">
          <span class="bench-mm-label-icon bench-mm-ic-purple" style="width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
          </span>
          Reply To
        </div>
        <select class="bench-mm-select" name="reply_to_email" id="bench_fwd_reply_to_email" style="flex:1;min-width:0;">
          <option value="{{ $userdata->email }}">{{ $userdata->email }}</option>
        </select>
      </div>
    </div>

    {{-- To Email ID --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        To Email ID <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <div class="bench-mm-tag-box" id="bench_to_email_tag_box" onclick="$('#bench_to_email_input').focus();">
          <div class="bench-mm-tag-list" id="bench_to_email_tags"></div>
          <input type="text" class="bench-mm-tag-input" id="bench_to_email_input" placeholder="Type recipient email and press Enter...">
          <a href="javascript:;" class="bench-mm-add-email-btn" onclick="openBenchRecipientDrawer('to')">+ Add Email</a>
        </div>
        <input type="hidden" name="to_email" id="bench_to_email" value="">
        <div class="bench-mm-hint">Type email address and press Enter or comma, or click "+ Add Email"</div>
      </div>
    </div>

    {{-- CC Email ID --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        CC Email ID
      </div>
      <div class="bench-mm-field-col">
        <div class="bench-mm-tag-box" id="bench_cc_email_tag_box" onclick="$('#bench_cc_email_input').focus();">
          <div class="bench-mm-tag-list" id="bench_cc_email_tags"></div>
          <input type="text" class="bench-mm-tag-input" id="bench_cc_email_input" placeholder="Type CC email and press Enter...">
          <a href="javascript:;" class="bench-mm-add-email-btn" onclick="openBenchRecipientDrawer('cc')">+ Add Email</a>
        </div>
        <input type="hidden" name="cc_email" id="bench_cc_email" value="{{ $cc_email }}">
        <div class="bench-mm-hint">Type CC address and press Enter or comma, or click "+ Add Email"</div>
      </div>
    </div>

    {{-- Template --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-purple">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        Template
      </div>
      <div class="bench-mm-field-col">
        <select name="template" id="bench_fwd_template" class="bench-mm-select" onchange="onBenchFwdTemplateChange(this)">
          <option value="">Select Email Template...</option>
          @if(!empty($templateData))
            @foreach($templateData as $tdata)
              <option value="{{ $tdata->e_temp_id ?? ($tdata->id ?? '') }}" data-subject="{{ $tdata->template_subject ?? '' }}" data-body="{{ htmlspecialchars($tdata->template_description ?? '', ENT_QUOTES) }}">{{ $tdata->display_name ?? ($tdata->template_name ?? 'Template #' . ($tdata->e_temp_id ?? $tdata->id)) }}</option>
            @endforeach
          @endif
        </select>
        <div class="bench-mm-hint">Choosing a template replaces the Subject and Body below</div>
      </div>
    </div>

    {{-- Subject --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </span>
        Subject <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <input class="bench-mm-input" name="subject" id="bench_fwd_subject" placeholder="Enter email subject" value="{{ $cand ? ('Candidate Profile: ' . $cand->full_name . ' - ' . ($cand->current_designation ?? 'Profile')) : '' }}">
      </div>
    </div>

    {{-- Resume Format Selector --}}
    <div class="bench-mm-row">
      <div class="bench-mm-label-col">
        <span class="bench-mm-label-icon bench-mm-ic-teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </span>
        Resume Format <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <input type="hidden" id="bench_fwd_attach_cv_toggle" name="attached_cv" value="1">
        <select name="attached_cv_format" id="bench_fwd_resume_format" class="bench-mm-select" style="font-weight:500;" onchange="onBenchFwdFormatChange(this)">
          <option value="" selected disabled>Select Resume Format...</option>
          <optgroup label="1. Standard Resume Options">
            <option value="1" data-label="Original Candidate Resume" data-desc="Download the candidate's uploaded resume exactly as received, with no edits or formatting changes.">1.a Original Resume (Default)</option>
            <option value="41" data-label="Resume + Company Branding" data-desc="Add your company logo and shared-by header above the resume while keeping all candidate details visible.">1.b Resume + Company Branding</option>
          </optgroup>
          <optgroup label="2. In-House Evaluation Report">
            <option value="5" data-label="In-House Evaluation Report" data-desc="Generate a concise hiring report with fit summary, key skills, and verification status for recruiter review.">2.a In-House Evaluation Report PDF</option>
          </optgroup>
          <optgroup label="3. Recruiter Packages (Report + Resume)">
            <option value="42" data-label="In-House Evaluation Report + Resume + Company Branding" data-desc="Deliver one polished PDF with the in-house evaluation report first, followed by the candidate resume with company branding.">3.a In-House Evaluation Report + Resume + Company Branding</option>
            <option value="43" data-label="In-House Evaluation Report + Resume without Company Branding" data-desc="Download the same report-and-resume package without company logo or company name on the report page.">3.b In-House Evaluation Report + Resume without Company Branding</option>
          </optgroup>
          <optgroup label="4. Highlighted Skills">
            <option value="44" data-label="Resume + Highlighted Keywords" data-desc="Download a clean resume copy with the most relevant skills highlighted for quick recruiter review.">4.a Resume + Highlighted Keywords</option>
            <option value="45" data-label="Resume + Highlighted Keywords + Company Branding" data-desc="Download a branded resume with job-matching keywords highlighted for faster recruiter screening.">4.b Resume + Highlighted Keywords + Company Branding</option>
            <option value="46" data-label="In-House Evaluation Report + Resume + Highlighted Keywords + Company Branding" data-desc="Create the recruiter package: in-house evaluation report, branded resume pages, and highlighted job-matching skills.">4.c In-House Evaluation Report + Resume + Highlighted Keywords + Company Branding</option>
            <option value="47" data-label="In-House Evaluation Report + Resume + Highlighted Keywords without Company Branding" data-desc="Create the recruiter package with report, resume, and highlighted skills, without company branding.">4.d In-House Evaluation Report + Resume + Highlighted Keywords without Company Branding</option>
          </optgroup>
          <optgroup label="5. Privacy & Anonymization (Anti-Poaching Shield)">
            <option value="48" data-label="Hide Contact Details" data-desc="Keep candidate name visible while hiding email, phone, mobile, contact and LinkedIn details in browser.">5.a Hide Contact Details</option>
            <option value="49" data-label="Hide Candidate Name & Contacts Details" data-desc="Hide candidate name, email, and phone numbers in a browser-only resume copy.">5.b Hide Candidate Name & Contacts Details</option>
            <option value="50" data-label="Hide Contact Details + Company Branding" data-desc="Keep the name visible, hide contact and LinkedIn details, then add your company header.">5.c Hide Contact Details + Company Branding</option>
            <option value="51" data-label="Hide Contact Details + In-House Evaluation Report + Company Branding" data-desc="Keep the name visible, hide contact and LinkedIn details, then merge the in-house evaluation report with company branding.">5.d Hide Contact Details + In-House Evaluation Report + Company Branding</option>
            <option value="52" data-label="Hide Contact Details + In-House Evaluation Report without Company Branding" data-desc="Keep the name visible, hide contact and LinkedIn details, then merge a no-brand report.">5.e Hide Contact Details + In-House Evaluation Report without Company Branding</option>
            <option value="53" data-label="Hide Contact Details + In-House Evaluation Report + Highlighted Keywords + Company Branding" data-desc="Full privacy package: masked contacts, highlighted skills, in-house evaluation report dossier, and company branding banner.">5.f Hide Contact Details + In-House Evaluation Report + Highlighted Keywords + Company Branding</option>
            <option value="54" data-label="Hide Contact Details + In-House Evaluation Report + Highlighted Keywords without Company Branding" data-desc="Full privacy package without company logo: masked contacts, highlighted skills, and in-house evaluation report dossier — no branding applied.">5.g Hide Contact Details + In-House Evaluation Report + Highlighted Keywords without Company Branding</option>
          </optgroup>
        </select>

        <!-- Format Details Section: Unified Single Enterprise Attachment & Progress Card -->
        <div id="bench_fwd_format_details_section" style="display:none;margin-top:8px;">

          @if(!empty($cand) && empty($cand->has_resume))
            <div id="bench_fwd_missing_resume_banner" style="margin-bottom:8px;padding:8px 12px;background:#FFFBEB;border:1px solid #FCD34D;border-radius:6px;display:flex;align-items:center;gap:8px;">
              <span style="color:#D97706;font-size:14px;flex-shrink:0;">⚠</span>
              <div style="font-size:11.5px;color:#92400E;line-height:1.35;">
                <strong>Resume Notice:</strong> Candidate has no resume document uploaded. You can attach using Option 2.a (In-House Evaluation Report) or upload a CV.
              </div>
            </div>
          @endif

          <!-- Unified Single Master Attachment & Progress Card -->
          @if($cand)
            @php
              $hasResume = !empty($cand->has_resume);
              $candSafeName = $cand->safe_name ?? ('Candidate_' . $cand->id);
              $candDisplayName = $cand->full_name ?? 'Candidate';
              $candDesignation = $cand->current_designation ?? 'Inhouse Talent';
              $candResumeUrl = $cand->resume_r2_url ?? ($cand->resume_url ?? ($cand->download_url ?? ''));
            @endphp
            <div id="bench_fwd_cand_card_{{ $cand->id }}" data-cand-id="{{ $cand->id }}" data-has-resume="{{ $hasResume ? '1' : '0' }}" style="position:relative;background:#FFFFFF;border:1px solid #E2E8F0;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.03);transition:all 0.2s ease;">
              
              <!-- Card Body -->
              <div style="padding:9px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                
                <!-- Left: Badge + Candidate Meta + Filename -->
                <div style="display:flex;align-items:center;gap:9px;min-width:0;flex:1;">
                  <span style="width:30px;height:30px;border-radius:6px;background:#F1F5F9;border:1px solid #E2E8F0;color:#0F766E;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
                    PDF
                  </span>
                  <div style="min-width:0;flex:1;">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:nowrap;">
                      <span style="font-size:12.5px;font-weight:600;color:#1E293B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $candDisplayName }}
                      </span>
                      <span style="font-size:11px;color:#64748B;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        • {{ $candDesignation }}
                      </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:2px;">
                      <div id="bench_fwd_cand_filename" data-safename="{{ $candSafeName }}" style="font-size:11px;font-family:monospace;color:{{ $hasResume ? '#059669' : '#B45309' }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        @if($hasResume)
                          <a href="javascript:void(0)" onclick="openBenchOriginalResume({{ $cand->id }})" style="color:#059669;text-decoration:underline;cursor:pointer;display:inline-flex;align-items:center;gap:3px;" title="Click to view resume">
                            📄 Jeroworks_{{ $candSafeName }}.pdf <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                          </a>
                        @else
                          <span>No resume uploaded</span>
                        @endif
                      </div>
                      <span id="bench_fwd_compile_step_detail" style="display:none;font-size:10.5px;color:#0D9488;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
                    </div>
                  </div>
                </div>

                <!-- Right: Action Buttons & Live Status -->
                <div style="display:flex;align-items:center;gap:7px;flex-shrink:0;">
                  @if($hasResume)
                    <button type="button" id="bench_fwd_view_cv_btn" class="btn btn-xs btn-light-info fw-bold" onclick="openBenchOriginalResume({{ $cand->id }})" style="font-size:11px;padding:3px 9px;border-radius:6px;display:inline-flex;align-items:center;gap:4px;" title="View candidate resume">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      View CV
                    </button>
                  @endif
                  <div id="bench_fwd_cand_status_badge" style="font-size:11px;font-weight:600;padding:2px 9px;border-radius:12px;background:{{ $hasResume ? '#F8FAFC' : '#FEF3C7' }};color:{{ $hasResume ? '#475569' : '#B45309' }};border:1px solid {{ $hasResume ? '#E2E8F0' : '#FDE68A' }};display:inline-flex;align-items:center;gap:4px;white-space:nowrap;">
                    {{ $hasResume ? '1 Resume Available' : '⚠ No Resume' }}
                  </div>
                </div>
              </div>

              <!-- Sleek Integrated 3px Baseline Progress Bar -->
              <div id="bench_fwd_compile_progress_container" style="display:none;height:3px;width:100%;background:#F1F5F9;overflow:hidden;">
                <div id="bench_fwd_compile_progress_bar" style="height:100%;width:0%;background:linear-gradient(90deg, #0D9488, #10B981);transition:width 0.25s ease;"></div>
              </div>
              
              <!-- Compatibility hidden anchors -->
              <span id="bench_fwd_compile_status_text" style="display:none;"></span>
              <span id="bench_fwd_compile_pct_badge" style="display:none;"></span>
              <span id="bench_fwd_preview_format_title" style="display:none;"></span>
              <span id="bench_fwd_preview_desc" style="display:none;"></span>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Message Body --}}
    <div class="bench-mm-row bench-mm-row-body">
      <div class="bench-mm-label-col" style="padding-top:10px;">
        <span class="bench-mm-label-icon bench-mm-ic-orange">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </span>
        Message Body <span class="bench-mm-req">*</span>
      </div>
      <div class="bench-mm-field-col">
        <div class="bench-mm-editor-wrap">
          <textarea name="editor1" id="bench_editor1">
<p>Dear [HR_NAME],</p>
<p>I would like to share the profile of <strong>{{ $cand->full_name ?? 'Candidate' }}</strong> ({{ $cand->current_designation ?? 'Profile' }}) with you for your hiring requirements.</p>
<p><strong>Candidate Summary:</strong></p>
<ul>
  <li><strong>Total Experience:</strong> {{ $cand->experience ?? '--' }}</li>
  <li><strong>Current Location:</strong> {{ $cand->location ?? '--' }}</li>
  <li><strong>Notice Period:</strong> {{ $cand->notice ?? '--' }}</li>
  @if(!empty($cand->skills))
  <li><strong>Talent Skills & Expertise:</strong> {{ $cand->skills }}</li>
  @endif
  @if(!empty($cand->rm_comments))
  <li><strong>Relationship Manager Comments:</strong> {{ strip_tags($cand->rm_comments) }}</li>
  @endif
</ul>
<p>Please find the formatted profile attached for your review. We look forward to your valuable feedback.</p>
<p>Best regards,<br>
<strong>{{ $userdata->name ?? 'Recruitment Team' }}</strong><br>
{{ $companyName ?? 'Hiring Partner' }}</p>
          </textarea>
        </div>
        <div class="bench-mm-hint">Use <code>[HR_NAME]</code> to automatically insert recipient's name in personalized emails.</div>
      </div>
    </div>

  </div>

  <input type="hidden" name="candidate_id" id="bench_fwd_candidate_id" value="{{ $cand->id ?? 0 }}">
  <input type="hidden" name="arr[]" value="{{ $cand->id ?? 0 }}">
</form>
</div>

<!-- ==============================================================================
     SLIDE-OUT RECIPIENT PICKER DRAWER
============================================================================== -->
<div id="kt_bench_recipient_drawer">
  <div class="bench-rcp-container">
    <div class="bench-rcp-header">
      <div>
        <h4 class="bench-rcp-header-title">Select Email Recipient</h4>
        <p class="bench-rcp-header-subtitle">Choose from internal team or client contacts</p>
      </div>
      <button type="button" class="bench-rcp-close-btn" onclick="closeBenchRecipientDrawer()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <!-- Segmented Tabs -->
    <div class="bench-rcp-tabs-wrap">
      <div class="bench-rcp-tab-card active" id="bench_rcp_tab_internal" onclick="switchBenchRecipientTab('internal')">
        <div>
          <div class="bench-rcp-tab-title">Internal Team</div>
          <div class="bench-rcp-tab-sub">{{ count($internal_team_data ?? []) }} colleagues</div>
        </div>
      </div>
      <div class="bench-rcp-tab-card" id="bench_rcp_tab_client" onclick="switchBenchRecipientTab('client')">
        <div>
          <div class="bench-rcp-tab-title">Client Contacts</div>
          <div class="bench-rcp-tab-sub">{{ count($client_contacts_data ?? []) }} contacts</div>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="bench-rcp-search-wrap">
      <input type="text" class="bench-rcp-search-input" id="bench_rcp_search_input" placeholder="Search by name, role or email..." onkeyup="filterBenchRecipients(this.value)">
    </div>

    <!-- Contact Lists -->
    <div class="bench-rcp-list-wrap">
      <!-- Internal Team Roster -->
      <div id="bench_rcp_internal_list">
        @if(!empty($internal_team_data))
          @foreach($internal_team_data as $userItem)
            <div class="bench-rcp-contact-item" data-search="{{ strtolower(($userItem->name ?? '') . ' ' . ($userItem->email ?? '') . ' ' . ($userItem->role_name ?? '')) }}">
              <div style="min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:6px;">
                  <span class="bench-rcp-contact-name">{{ $userItem->name ?? 'Team Member' }}</span>
                  <span class="bench-rcp-role-pill">{{ $userItem->role_name ?? 'Recruiter' }}</span>
                </div>
                <div class="bench-rcp-email-line">{{ $userItem->email }}</div>
              </div>
              <div>
                <button type="button" class="bench-rcp-toggle-btn" data-email="{{ $userItem->email }}" onclick="toggleBenchRecipient('{{ $userItem->email }}', this)">+ Add</button>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      <!-- Client Contacts Roster -->
      <div id="bench_rcp_client_list" style="display:none;">
        @if(!empty($client_contacts_data))
          @foreach($client_contacts_data as $clientItem)
            <div class="bench-rcp-contact-item" data-search="{{ strtolower(($clientItem->name ?? '') . ' ' . ($clientItem->email ?? '') . ' ' . ($clientItem->company_name ?? '')) }}">
              <div style="min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:6px;">
                  <span class="bench-rcp-contact-name">{{ $clientItem->name ?? 'Contact Person' }}</span>
                  <span class="bench-rcp-role-pill" style="background:#F0FDF4;color:#16A34A;border-color:#DCFCE7;">{{ $clientItem->company_name ?? 'Client' }}</span>
                </div>
                <div class="bench-rcp-email-line">{{ $clientItem->email }}</div>
              </div>
              <div>
                <button type="button" class="bench-rcp-toggle-btn" data-email="{{ $clientItem->email }}" onclick="toggleBenchRecipient('{{ $clientItem->email }}', this)">+ Add</button>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>

    <!-- Footer -->
    <div class="bench-rcp-footer">
      <button type="button" class="bench-mm-header-btn-cancel" onclick="closeBenchRecipientDrawer()">Cancel</button>
      <button type="button" class="bench-mm-header-btn-send" style="min-width:100px;" onclick="closeBenchRecipientDrawer()">Done</button>
    </div>
  </div>
</div>

<script>
// Scoped recipient tag manager for Bench Forward Resume
var benchToEmails = [];
var benchCcEmails = [];
var activeBenchRecipientTarget = 'to'; // 'to' or 'cc'
var _benchCompiledAttachment = null;
var _benchCompilationSeq = 0;
var _benchIsCompiling = false;

// Open candidate's authoritative original resume in a new tab via same-origin streaming proxy
window.openBenchOriginalResume = function(candId) {
    var candObj = @json($cand ?? []);
    var cId = candId || (candObj && candObj.id) || 0;
    var ext = (candObj.resume_extension || (candObj.resume_filename ? candObj.resume_filename.split('.').pop() : '')).toLowerCase();

    // For Word documents, open the pre-converted vector PDF in new tab for immediate visual inspection
    if (ext === 'doc' || ext === 'docx') {
        window.open('/service_company/bench-candidates/download/' + cId + '?as_pdf=1', '_blank');
        return;
    }

    // Stream original document directly from same-origin proxy (Content-Disposition: inline)
    window.open('/service_company/bench-candidates/download/' + cId, '_blank');
};

// Initialize chips if values exist
function renderBenchTags(target) {
    var list = (target === 'to') ? benchToEmails : benchCcEmails;
    var container = $('#bench_' + target + '_email_tags');
    var hiddenInput = $('#bench_' + target + '_email');
    container.empty();

    list.forEach(function(email, idx) {
        var isVal = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        var chip = $('<div class="bench-mm-email-tag ' + (isVal ? '' : 'is-invalid') + '">' +
            '<span class="bench-mm-email-tag-text">' + email + '</span>' +
            '<span class="bench-mm-email-tag-remove" onclick="removeBenchEmailTag(\'' + target + '\', ' + idx + ')">&times;</span>' +
            '</div>');
        container.append(chip);
    });

    hiddenInput.val(list.join(','));
    syncBenchRecipientDrawerButtons();
}

function addBenchEmailTag(target, email) {
    var clean = (email || '').trim().replace(/,/g, '');
    if (!clean) return;
    var list = (target === 'to') ? benchToEmails : benchCcEmails;
    if (list.indexOf(clean) === -1) {
        list.push(clean);
        renderBenchTags(target);
    }
}

function removeBenchEmailTag(target, idx) {
    var list = (target === 'to') ? benchToEmails : benchCcEmails;
    list.splice(idx, 1);
    renderBenchTags(target);
}

// Input keyboard handler (Enter, comma, blur)
$('#bench_to_email_input').on('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addBenchEmailTag('to', $(this).val());
        $(this).val('');
    } else if (e.key === 'Backspace' && !$(this).val() && benchToEmails.length > 0) {
        removeBenchEmailTag('to', benchToEmails.length - 1);
    }
}).on('blur', function() {
    if ($(this).val()) {
        addBenchEmailTag('to', $(this).val());
        $(this).val('');
    }
});

$('#bench_cc_email_input').on('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addBenchEmailTag('cc', $(this).val());
        $(this).val('');
    } else if (e.key === 'Backspace' && !$(this).val() && benchCcEmails.length > 0) {
        removeBenchEmailTag('cc', benchCcEmails.length - 1);
    }
}).on('blur', function() {
    if ($(this).val()) {
        addBenchEmailTag('cc', $(this).val());
        $(this).val('');
    }
});

// Recipient drawer slide-in / out
function openBenchRecipientDrawer(target) {
    activeBenchRecipientTarget = target || 'to';
    syncBenchRecipientDrawerButtons();
    $('#kt_bench_recipient_drawer').css('display', 'block');
}

function closeBenchRecipientDrawer() {
    $('#kt_bench_recipient_drawer').css('display', 'none');
}

function switchBenchRecipientTab(tab) {
    if (tab === 'internal') {
        $('#bench_rcp_tab_internal').addClass('active');
        $('#bench_rcp_tab_client').removeClass('active');
        $('#bench_rcp_internal_list').show();
        $('#bench_rcp_client_list').hide();
    } else {
        $('#bench_rcp_tab_client').addClass('active');
        $('#bench_rcp_tab_internal').removeClass('active');
        $('#bench_rcp_client_list').show();
        $('#bench_rcp_internal_list').hide();
    }
}

function filterBenchRecipients(query) {
    var q = (query || '').toLowerCase().trim();
    $('.bench-rcp-contact-item').each(function() {
        var str = $(this).attr('data-search') || '';
        $(this).toggle(!q || str.indexOf(q) !== -1);
    });
}

function toggleBenchRecipient(email, btn) {
    var list = (activeBenchRecipientTarget === 'to') ? benchToEmails : benchCcEmails;
    var idx = list.indexOf(email);
    if (idx !== -1) {
        list.splice(idx, 1);
    } else {
        list.push(email);
    }
    renderBenchTags(activeBenchRecipientTarget);
}

function syncBenchRecipientDrawerButtons() {
    var list = (activeBenchRecipientTarget === 'to') ? benchToEmails : benchCcEmails;
    $('.bench-rcp-toggle-btn').each(function() {
        var email = $(this).attr('data-email');
        if (list.indexOf(email) !== -1) {
            $(this).addClass('is-added').text('Added ✓');
        } else {
            $(this).removeClass('is-added').text('+ Add');
        }
    });
}

// Template selection handler
function onBenchFwdTemplateChange(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    var subj = opt.getAttribute('data-subject');
    var body = opt.getAttribute('data-body');

    if (subj) {
        $('#bench_fwd_subject').val(subj);
    }
    if (body && typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['bench_editor1']) {
        var candName = @json($cand->full_name ?? 'Candidate');
        var candDesig = @json($cand->current_designation ?? 'Profile');
        var parsedBody = body.replace(/\[CANDIDATE_NAME\]/g, candName)
                             .replace(/\[DESIGNATION\]/g, candDesig);
        CKEDITOR.instances['bench_editor1'].setData(parsedBody);
    }
}

// Format selection handler
function onBenchFwdFormatChange(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) {
        $('#bench_fwd_format_details_section').hide();
        _benchCompiledAttachment = null;
        var btn = document.getElementById('btnBenchFwdSubmit');
        if (btn) {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }
        return;
    }
    var optId = parseInt(opt.value, 10);
    var label = opt.getAttribute('data-label') || opt.text;
    var desc = opt.getAttribute('data-desc') || '';

    $('#bench_fwd_preview_format_title').text(label);
    $('#bench_fwd_preview_desc').text(desc);
    $('#bench_fwd_format_details_section').show();

    var safeName = $('#bench_fwd_cand_filename').attr('data-safename') || 'Candidate';
    var candId = @json($cand->id ?? 0);
    var candHasResume = @json(!empty($cand->has_resume));
    var dispName = '';
    if (optId === 1) {
        var ext = @json(pathinfo($cand->resume_filename ?? '', PATHINFO_EXTENSION) ?: 'pdf');
        dispName = 'Jeroworks_' + safeName + '.' + ext;
    } else if (optId === 5) {
        dispName = 'Prescreen_Report_' + safeName + '.pdf';
    } else {
        dispName = 'Jeroworks_' + safeName + '_Formatted.pdf';
    }

    if (candHasResume || optId === 5) {
        $('#bench_fwd_cand_filename').html(
            '<a href="javascript:void(0)" onclick="openBenchOriginalResume(' + candId + ')" style="color:#059669;text-decoration:underline;cursor:pointer;display:inline-flex;align-items:center;gap:4px;" title="Click to view original resume">' +
            '📄 ' + $('<div>').text(dispName).html() + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>'
        );
    } else {
        $('#bench_fwd_cand_filename').html('<span>No resume uploaded</span>');
    }

    var candHasResume = @json(!empty($cand->has_resume));
    if (!candHasResume && optId !== 5) {
        $('#bench_fwd_cand_status_badge')
            .html('⚠ No Resume')
            .css({ background: '#FEF3C7', color: '#B45309' });
        $('#bench_fwd_compile_progress_container').hide();
        var btn = document.getElementById('btnBenchFwdSubmit');
        if (btn) {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }
        Swal.fire({
            icon: 'warning',
            title: 'Resume Missing',
            text: 'This candidate has no uploaded resume document. Please select Option 2.a (In-House Evaluation Report PDF) to forward their candidate profile dossier.',
            confirmButtonText: 'Select Option 2.a',
            showCancelButton: true,
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                $('#bench_fwd_resume_format').val('5').trigger('change');
            }
        });
        return;
    }

    // Immediately start the engine for conversion!
    triggerBenchResumeCompilation(optId);
}

// Trigger conversion engine immediately upon format selection
async function triggerBenchResumeCompilation(optId) {
    var seq = ++_benchCompilationSeq;
    _benchIsCompiling = true;
    _benchCompiledAttachment = null;

    var btn = document.getElementById('btnBenchFwdSubmit');
    if (btn) {
        btn.disabled = true;
        btn.style.cursor = 'not-allowed';
        $(btn).find('.indicator-label').hide();
        $(btn).find('.indicator-progress').css('display', 'inline-flex').show();
        $('#bench_fwd_btn_progress_text').text('Compiling resume PDF...');
    }

    $('#bench_fwd_compile_progress_container').show();
    $('#bench_fwd_compile_progress_bar').css({ width: '20%', background: 'linear-gradient(90deg, #0D9488, #10B981)' });
    $('#bench_fwd_compile_pct_badge').text('20%');
    $('#bench_fwd_compile_status_text').text('Compiling document...').css('color', '#0F766E');
    $('#bench_fwd_compile_step_detail').text('Compiling document...').show();

    $('#bench_fwd_cand_status_badge')
        .html('<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:1.5px;color:#0D9488!important;display:inline-block;margin-right:3px;"></span> 20%')
        .css({ background: '#F0FDFA', color: '#0F766E', borderColor: '#99F6E4' });

    try {
        var candObj = @json($cand ?? []);
        var companyData = {
            companyName: @json($companyName ?? 'Hiring Partner'),
            companyLogo: @json($companyLogo ?? '')
        };
        var jobSkills = [];

        var engine = window.AgyBenchForwardResumeEngine;
        if (!engine || typeof engine.generateAttachmentForCandidate !== 'function') {
            throw new Error('Client WASM document engine is initializing. Please wait a moment.');
        }

        var blob = await engine.generateAttachmentForCandidate(
            candObj,
            optId,
            companyData,
            jobSkills,
            function(pct, statusText, stepDetail) {
                if (seq !== _benchCompilationSeq) return;
                if (pct !== undefined) {
                    $('#bench_fwd_compile_progress_bar').css('width', pct + '%');
                    $('#bench_fwd_compile_pct_badge').text(pct + '%');
                    $('#bench_fwd_cand_status_badge')
                        .html('<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:1.5px;color:#0D9488!important;display:inline-block;margin-right:3px;"></span> ' + pct + '%')
                        .css({ background: '#F0FDFA', color: '#0F766E', borderColor: '#99F6E4' });
                }
                if (statusText || stepDetail) {
                    var msg = statusText || stepDetail;
                    $('#bench_fwd_compile_status_text').text(msg);
                    $('#bench_fwd_compile_step_detail').text(msg).show();
                }
            }
        );

        if (seq !== _benchCompilationSeq) return; // Superseded by another format selection

        if (!blob) {
            throw new Error('Document compiler returned empty output.');
        }

        var safeName = (candObj.full_name || 'Candidate').replace(/[^\w\s.-]/g, '').replace(/\s+/g, '_');
        var defaultName = (optId === 5) ? ('Prescreen_Report_' + safeName + '.pdf') : ('Jeroworks_' + safeName + '.pdf');
        var outName = (blob && blob.filename) ? blob.filename : defaultName;

        _benchCompiledAttachment = {
            blob: blob.blob || blob,
            filename: outName,
            formatId: optId
        };
        _benchIsCompiling = false;

        // Progress bar 100%
        $('#bench_fwd_compile_progress_bar').css({ width: '100%', background: '#10B981' });
        $('#bench_fwd_compile_pct_badge').text('100%');
        $('#bench_fwd_compile_status_text').text('✓ Document compiled successfully!').css('color', '#059669');
        $('#bench_fwd_compile_step_detail').hide();
        
        var previewUrl = null;
        try {
            var rawBlob = blob.blob || blob;
            if (rawBlob instanceof Blob) {
                previewUrl = URL.createObjectURL(rawBlob);
            }
        } catch (e) {}

        if (previewUrl) {
            $('#bench_fwd_cand_filename').html(
                '<a href="' + previewUrl + '" target="_blank" style="color:#059669;text-decoration:underline;cursor:pointer;display:inline-flex;align-items:center;gap:4px;" title="Click to preview compiled resume">' +
                '📄 ' + $('<div>').text(outName).html() + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>'
            );
            $('#bench_fwd_view_cv_btn').attr('onclick', "window.open('" + previewUrl + "', '_blank')");
        } else {
            $('#bench_fwd_cand_filename').html(
                '<a href="javascript:void(0)" onclick="openBenchOriginalResume(' + (candObj.id || 0) + ')" style="color:#059669;text-decoration:underline;cursor:pointer;display:inline-flex;align-items:center;gap:4px;" title="Click to view resume">' +
                '📄 ' + $('<div>').text(outName).html() + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>'
            );
            $('#bench_fwd_view_cv_btn').attr('onclick', "openBenchOriginalResume(" + (candObj.id || 0) + ")");
        }

        $('#bench_fwd_cand_status_badge')
            .html('✓ Attached (100%)')
            .css({ background: '#DCFCE7', color: '#15803D', borderColor: '#BBF7D0' });

        // Enable Send Email button now that conversion is complete!
        if (btn) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }

    } catch (compileErr) {
        if (seq !== _benchCompilationSeq) return;
        _benchIsCompiling = false;
        _benchCompiledAttachment = null;
        console.error('[BenchForwardResume] Compilation failed:', compileErr);

        if (btn) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            $(btn).find('.indicator-progress').hide();
            $(btn).find('.indicator-label').css('display', 'inline-flex').show();
        }

        $('#bench_fwd_compile_status_text').text('Compilation notice: ' + (compileErr.message || 'Processing Error')).css('color', '#DC2626');
        $('#bench_fwd_compile_step_detail').text('Please choose another format or continue with Option 1.a.');
        $('#bench_fwd_cand_status_badge').html('⚠ Conversion Issue').css({ background: '#FEE2E2', color: '#DC2626' });

        Swal.fire({
            icon: 'warning',
            title: 'Format Notice',
            text: 'We encountered an issue preparing format #' + optId + ' (' + (compileErr.message || 'Processing Error') + '). Would you like to use Option 1.a (Original Resume) or Option 2.a (In-House Evaluation Report) instead?',
            showCancelButton: true,
            confirmButtonText: 'Use Option 1.a (Original)',
            cancelButtonText: 'Select Format'
        }).then(function(result) {
            if (result.isConfirmed) {
                $('#bench_fwd_resume_format').val('1').trigger('change');
            }
        });
    }
}

// Send forward email handler
async function onBenchFwdSubmitClick(e) {
    if (e) e.preventDefault();

    if (_benchIsCompiling) {
        Swal.fire('Please Wait', 'Resume conversion is still in progress. The email will be sent once conversion completes.', 'info');
        return;
    }

    var toEmail = $('#bench_to_email').val();
    if (!toEmail) {
        Swal.fire('Recipient Required', 'Please enter at least one recipient email address (To).', 'warning');
        $('#bench_to_email_input').focus();
        return;
    }

    var subject = $('#bench_fwd_subject').val().trim();
    if (!subject) {
        Swal.fire('Subject Required', 'Please enter an email subject line.', 'warning');
        $('#bench_fwd_subject').focus();
        return;
    }

    var formatId = parseInt($('#bench_fwd_resume_format').val(), 10);
    if (!formatId || isNaN(formatId)) {
        Swal.fire('Format Required', 'Please select a resume format before sending.', 'warning');
        $('#bench_fwd_resume_format').focus();
        return;
    }

    var candHasResume = @json(!empty($cand->has_resume));
    if (!candHasResume && formatId !== 5) {
        Swal.fire('Resume Missing', 'This candidate does not have an uploaded resume file to merge. Please select "Option 2.a: In-House Evaluation Report PDF" to generate their standalone dossier.', 'warning');
        return;
    }

    // Ensure attachment is compiled and ready in RAM
    if (!_benchCompiledAttachment || _benchCompiledAttachment.formatId !== formatId) {
        await triggerBenchResumeCompilation(formatId);
        if (!_benchCompiledAttachment) {
            Swal.fire('Attachment Error', 'Resume attachment could not be compiled. Please select another format.', 'error');
            return;
        }
    }

    var bodyHtml = '';
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['bench_editor1']) {
        bodyHtml = CKEDITOR.instances['bench_editor1'].getData();
    } else {
        bodyHtml = $('#bench_editor1').val();
    }

    var cleanBody = $('<div>').html(bodyHtml).text().trim();
    if (!cleanBody && bodyHtml.indexOf('<img') === -1) {
        Swal.fire('Message Required', 'Please provide a message body before sending.', 'warning');
        return;
    }

    var btn = document.getElementById('btnBenchFwdSubmit');
    if (btn) {
        btn.disabled = true;
        btn.style.cursor = 'not-allowed';
        $(btn).find('.indicator-label').hide();
        $(btn).find('.indicator-progress').css('display', 'inline-flex').show();
        $('#bench_fwd_btn_progress_text').text('Sending email...');
    }

    var formData = new FormData(document.getElementById('bench_fwd_form'));
    formData.set('editor1', bodyHtml);
    formData.set('subject', subject);
    formData.set('to_email', toEmail);
    formData.set('cc_email', $('#bench_cc_email').val());
    formData.set('attached_cv_format', formatId);

    var candObj = @json($cand ?? []);
    if (_benchCompiledAttachment && _benchCompiledAttachment.blob) {
        formData.append('custom_attachments[' + candObj.id + ']', _benchCompiledAttachment.blob, _benchCompiledAttachment.filename);
    }

    $.ajax({
        url: "{{ route('service_company.bench-candidates.fwd_resume_send') }}",
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        success: function(res) {
            if (btn) {
                btn.disabled = false;
                btn.style.cursor = 'pointer';
                $(btn).find('.indicator-progress').hide();
                $(btn).find('.indicator-label').css('display', 'inline-flex').show();
            }
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Forwarded!',
                    text: res.message || 'Candidate resume forwarded successfully via email.',
                    confirmButtonText: 'Done',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(function() {
                    closeBenchFwdResumeDrawer();
                });
            } else if (res.status === 'coinCheck') {
                Swal.fire('Insufficient Coins', res.message || 'Please recharge your company coin balance.', 'warning');
            } else {
                Swal.fire('Failed', res.message || 'Could not send forward email. Please try again.', 'error');
            }
        },
        error: function(err) {
            if (btn) {
                btn.disabled = false;
                btn.style.cursor = 'pointer';
                $(btn).find('.indicator-progress').hide();
                $(btn).find('.indicator-label').css('display', 'inline-flex').show();
            }
            var errMsg = 'Server error occurred while sending email.';
            if (err.responseJSON && err.responseJSON.message) {
                errMsg = err.responseJSON.message;
            }
            Swal.fire('Error', errMsg, 'error');
        }
    });
}

function closeBenchFwdResumeDrawer() {
    var drawerEl = document.getElementById('kt_bench_fwd_resume_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { 
            drawer.hide(); 
            return; 
        }
    }
    $('#kt_bench_fwd_resume_drawer').removeClass('drawer-on');
    var overlays = $('.drawer-overlay');
    if (overlays.length > 1) {
        overlays.last().remove();
    } else if (!$('#kt_activitiesBenchApplication').hasClass('drawer-on')) {
        overlays.remove();
    }
}
</script>
