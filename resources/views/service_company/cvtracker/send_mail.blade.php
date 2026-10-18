@php
    use App\Helper\GeneralMethod;
    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();

@endphp
<link rel="stylesheet" href="{{ asset('assets/modules/fwd-resume/quill/quill.snow.css') }}">
<script src="{{ asset('assets/modules/fwd-resume/quill/quill.min.js') }}"></script>
<script>
    window.candidateEmailQuill = null;

    window.getCandidateEmailBodyHtml = function() {
        if (window.candidateEmailQuill && window.candidateEmailQuill.root) {
            var html = window.candidateEmailQuill.root.innerHTML || '';
            return (html === '<p><br></p>' || html === '<p></p>') ? '' : html;
        }
        return $('#editor1mass').val() || '';
    };

    window.setCandidateEmailBodyHtml = function(html) {
        var cleanHtml = (html === null || html === undefined) ? '' : String(html);
        $('#editor1mass').val(cleanHtml);
        if (window.candidateEmailQuill) {
            if (window.candidateEmailQuill.clipboard && typeof window.candidateEmailQuill.clipboard.dangerouslyPasteHTML === 'function') {
                window.candidateEmailQuill.clipboard.dangerouslyPasteHTML(cleanHtml);
            } else if (window.candidateEmailQuill.root) {
                window.candidateEmailQuill.root.innerHTML = cleanHtml;
            }
        }
    };

    window.initCandidateEmailEditor = function() {
        var quillContainer = document.getElementById('candidate_email_quill');
        if (!quillContainer) return;

        // If Quill instance already exists and container is still attached, don't recreate
        if (window.candidateEmailQuill && window.candidateEmailQuill.root && quillContainer.contains(window.candidateEmailQuill.root)) {
            return;
        }

        if (typeof Quill === 'undefined') {
            setTimeout(window.initCandidateEmailEditor, 50);
            return;
        }

        try {
            var existingContent = $('#editor1mass').val() || '';
            quillContainer.innerHTML = '';
            window.candidateEmailQuill = new Quill('#candidate_email_quill', {
                theme: 'snow',
                placeholder: 'Compose email message...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'clean']
                    ]
                }
            });

            if (existingContent) {
                if (window.candidateEmailQuill.clipboard && typeof window.candidateEmailQuill.clipboard.dangerouslyPasteHTML === 'function') {
                    window.candidateEmailQuill.clipboard.dangerouslyPasteHTML(existingContent);
                } else if (window.candidateEmailQuill.root) {
                    window.candidateEmailQuill.root.innerHTML = existingContent;
                }
            }

            window.candidateEmailQuill.on('text-change', function() {
                var html = window.getCandidateEmailBodyHtml();
                $('#editor1mass').val(html);
            });
        } catch(err) {
            console.warn('[CandidateEmail] Quill init warning:', err);
        }
    };

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(window.initCandidateEmailEditor, 30);
    } else {
        document.addEventListener('DOMContentLoaded', window.initCandidateEmailEditor);
    }
    $(document).ready(function() {
        setTimeout(window.initCandidateEmailEditor, 50);
    });
</script>

<div class="main_container" id="main_container">
    @php
        $template_body = '';
        $to_email = '';
        if ($to_email_from_count > 0) {
            $to_email = implode(', ', $to_email_from_data);
        }
        $cc_email = '';
        if ($cc_email_count > 0) {
            $cc_email = implode(', ', $cc_email_data);
        }
        $applicationDataID = '';
        if(count($applicationData) > 0){
            $applicationDataID = $applicationData[0]->id;
        }
    @endphp

    <style>
    /* mm-* form design — matches mass_mail style */
    .mm-wrap { font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; display: flex; flex-direction: column; height: 100%; }
    .mm-form-body { flex: 1; overflow-y: auto; }
    .mm-row { display: flex; align-items: flex-start; padding: 16px 24px; border-bottom: 1px solid #F3F4F6; gap: 16px; }
    .mm-label-col { width: 150px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding-top: 9px; font-size: 12.5px; font-weight: 600; color: #374151; }
    .mm-label-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mm-label-icon svg { width: 14px; height: 14px; }
    .mm-ic-blue   { background: #EFF6FF; color: #2563EB; }
    .mm-ic-green  { background: #DCFCE7; color: #16A34A; }
    .mm-ic-purple { background: #F5F3FF; color: #7C3AED; }
    .mm-ic-teal   { background: #F0FDF4; color: #0D9488; }
    .mm-req { color: #EF4444; margin-left: 1px; }
    .mm-field-col { flex: 1; min-width: 0; position: relative; }
    .mm-input { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB; outline: none; transition: border-color 0.15s; }
    .mm-input:focus { border-color: #93C5FD; background: #fff; }
    .mm-input[readonly] { color: #6B7280; cursor: default; background: #F3F4F6; }
    .mm-hint { font-size: 11.5px; color: #9CA3AF; margin-top: 5px; line-height: 1.4; }
    .mm-field-col .tagify { border: 1px solid #E5E7EB !important; border-radius: 8px !important; background: #F9FAFB !important; padding: 4px 10px !important; font-family: 'Poppins', sans-serif !important; min-height: 40px !important; }
    .mm-field-col .tagify:focus-within { border-color: #93C5FD !important; background: #fff !important; }
    .mm-field-col .tagify--readonly { background: #F3F4F6 !important; }
    .mm-select { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; padding: 9px 36px 9px 14px; font-size: 13px; color: #374151; font-family: 'Poppins', sans-serif; background: #F9FAFB url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center; appearance: none; -webkit-appearance: none; outline: none; cursor: pointer; transition: border-color 0.15s; }
    .mm-select:focus { border-color: #93C5FD; background-color: #fff; }
    /* Chip / tag display for readonly To field */
    .mm-chips-box { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; border: 1px solid #E5E7EB; border-radius: 8px; padding: 6px 10px; background: #F9FAFB; min-height: 40px; }
    .mm-chip { display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 20px; padding: 3px 10px 3px 4px; font-size: 12.5px; color: #1E40AF; font-family: 'Poppins', sans-serif; }
    .mm-chip-av { width: 22px; height: 22px; border-radius: 50%; background: #DC2626; color: #fff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .mm-chip-text { max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mm-chip-x { background: none; border: none; padding: 0; margin-left: 2px; color: #93C5FD; cursor: default; font-size: 13px; line-height: 1; }
    .mm-addfrom { font-size: 12px; font-weight: 600; color: #9CA3AF; margin-top: 6px; display: flex; align-items: center; gap: 6px; }
    .mm-addfrom a { color: #2563EB; text-decoration: none; font-weight: 600; }
    .mm-addfrom a:hover { text-decoration: underline; }

    /* CC Tag-Box styles */
    .mm-tag-box {
      width: 100%;
      min-height: 44px;
      border: 1px solid #E5E7EB;
      border-radius: 8px;
      padding: 5px 140px 5px 10px;
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
    .mm-tag-box.is-focused { border-color: #93C5FD; background-color: #FFFFFF; box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.25); }
    .mm-tag-list { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
    .mm-email-tag { display: inline-flex; align-items: center; gap: 5px; background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; font-size: 12px; font-weight: 500; padding: 3px 8px; border-radius: 6px; line-height: 1.35; user-select: none; }
    .mm-email-tag.is-invalid { background: #FEF2F2; border-color: #FECACA; color: #DC2626; }
    .mm-email-tag-text { max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .mm-email-tag-remove { display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; border-radius: 50%; color: #6B7280; cursor: pointer; font-size: 14px; font-weight: 700; line-height: 1; margin-left: 2px; }
    .mm-email-tag-remove:hover { background: #DBEAFE; color: #1E3A8A; }
    .mm-tag-input { border: none !important; background: transparent !important; padding: 4px 6px !important; font-size: 13px !important; font-family: 'Poppins', sans-serif !important; color: #374151 !important; outline: none !important; box-shadow: none !important; flex: 1; min-width: 140px; margin: 0; }

    /* Yellow theme button for "+ Add Recipient" in CC box */
    .mm-add-recipient-btn {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11.5px;
      font-weight: 600;
      color: #92400E;
      background: #FEF3C7;
      border: 1px solid #FCD34D;
      border-radius: 6px;
      padding: 5px 12px;
      cursor: pointer;
      text-decoration: none !important;
      transition: all 0.15s ease-in-out;
      z-index: 5;
      box-shadow: 0 1px 2px rgba(217, 119, 6, 0.08);
    }
    .mm-add-recipient-btn:hover {
      background: #FDE68A;
      border-color: #F59E0B;
      color: #78350F;
      text-decoration: none !important;
      box-shadow: 0 2px 4px rgba(217, 119, 6, 0.15);
    }

    /* Modern Header (Design #2: Envelope Icon + Title/Subtitle + Close + Send Email button) */
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
      border-radius: 10px;
      background: #EFF6FF;
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
    .mm-header-btn-close {
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      border: 1px solid #E5E7EB;
      background: #FFFFFF;
      color: #64748B;
      cursor: pointer;
      transition: all 0.15s ease-in-out;
      padding: 0;
    }
    .mm-header-btn-close:hover {
      background: #F8FAFC;
      border-color: #CBD5E1;
      color: #1E293B;
    }
    .mm-header-btn-send {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: 9px 20px;
      border-radius: 8px;
      border: none;
      background: #2563EB;
      color: #FFFFFF;
      font-size: 13.5px;
      font-weight: 600;
      font-family: 'Poppins', sans-serif;
      cursor: pointer;
      transition: background-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
      box-shadow: 0 1px 2px rgba(37, 99, 235, 0.15);
    }
    .mm-header-btn-send:hover {
      background: #1D4ED8;
    }
    .mm-header-btn-send:disabled,
    .mm-header-btn-send.disabled {
      opacity: 0.65;
      cursor: not-allowed;
      box-shadow: none;
    }

    /* Recipient Slide-Over Drawer Styles (Isolated to Send Email) */
    #kt_sendmail_recipient_drawer {
      width: 490px;
      max-width: 100vw;
      height: 100vh;
      background: #FFFFFF !important;
      font-family: 'Poppins', sans-serif;
      z-index: 1000060 !important;
      box-shadow: -8px 0 32px rgba(0, 0, 0, 0.25);
      position: fixed !important;
      top: 0 !important;
      right: 0 !important;
      display: none;
    }
    @media (max-width: 576px) { #kt_sendmail_recipient_drawer { width: 100vw !important; } }
    .sendmail-rcp-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.35); z-index: 1000055 !important; cursor: pointer; display: none; }
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
    .rcp-footer { padding: 14px 20px; border-top: 1px solid #E5E7EB; background: #fff; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
    .rcp-btn-cancel { padding: 8px 16px; border: 1px solid #E5E7EB; background: #fff; border-radius: 7px; font-size: 13px; font-weight: 600; color: #374151; cursor: pointer; }
    .rcp-btn-done { padding: 8px 20px; border: none; background: #2563EB; border-radius: 7px; font-size: 13px; font-weight: 600; color: #fff; cursor: pointer; }

    .mm-editor-wrap { width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; background: #FFFFFF; }
    .mm-editor-wrap .ql-toolbar.ql-snow {
      border: none !important;
      border-bottom: 1px solid #E5E7EB !important;
      background: #F8FAFC;
      padding: 8px 12px;
      font-family: 'Poppins', sans-serif;
    }
    .mm-editor-wrap .ql-container.ql-snow {
      border: none !important;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      color: #1E293B;
      min-height: 220px;
    }
    .mm-editor-wrap .ql-editor {
      min-height: 220px;
      max-height: 380px;
      overflow-y: auto;
      line-height: 1.6;
      padding: 12px 14px;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
    }
    .mm-editor-wrap .ql-editor.ql-blank::before {
      color: #94A3B8;
      font-style: normal;
      left: 14px;
      right: 14px;
    }
    .mm-row-body { align-items: flex-start; }
    </style>

    <div class="mm-wrap">
    <form id="form1" onsubmit="return false;">
      {{-- Header: Modern Design matching 2nd design with icon, title, subtitle, close button & Send Email button --}}
      <div class="mm-header">
        <div class="mm-header-left">
          <div class="mm-header-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
          </div>
          <div class="mm-header-text">
            <h3 class="mm-header-title">Send Email</h3>
            <p class="mm-header-subtitle">Compose and send an email to the selected recipient(s)</p>
          </div>
        </div>
        <div class="mm-header-actions">
          <button type="button" class="mm-header-btn-close" id="kt_send_mail_showdetail_close" onclick="(function(){ if(typeof window.closeCandidateSendMailDrawer === 'function') { window.closeCandidateSendMailDrawer(); } else if(typeof window.hideCandidateSendMailDrawer === 'function') { window.hideCandidateSendMailDrawer(); } else { $('.sd-mail-overlay').remove(); $('#kt_send_mail_showdetail_drawer').removeClass('drawer-on'); } })()" title="Close">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
          <button type="button" class="mm-header-btn-send" name="btnSubmitSendMail" id="btnSubmitSendMail" value="btnSubmitSendMail">
            <span class="indicator-label" style="display:inline-flex;align-items:center;gap:7px;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
              Send Email
            </span>
            <span class="indicator-progress" style="display:none;align-items:center;gap:7px;">
              <span class="spinner-border spinner-border-sm" style="width:13px;height:13px;border-width:2px;display:inline-block;"></span>
              <span>Sending...</span>
            </span>
          </button>
        </div>
      </div>

      <div class="mm-form-body">

        {{-- Hidden fields --}}
        <input type="hidden" value="{{ $jobid }}" name="jobid" id="jobid">
        <input type="hidden" value="{{ $applicationDataID }}" name="applicationDataId" id="applicationDataId">
        <input name="attached_cv" id="attached_cv" type="checkbox" value="1" checked="checked" class="d-none" />

        {{-- From + Reply To (aligned with label column) --}}
        <div class="mm-row">
          <div class="mm-label-col">
            <span class="mm-label-icon mm-ic-blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
            </span>
            From
          </div>
          <div class="mm-field-col" style="display:flex;gap:12px;align-items:center;">
            <select class="mm-select" name="from_email_mass_mail" id="from_email_mass_mail" style="flex:1;min-width:0;">
              <option value="notify@jeroworks.com">{{ $userdata?->name ?? ($userdata?->email ?? (auth()->user()?->name ?? 'Recruiter')) }} &lt;notify@jeroworks.com&gt;</option>
            </select>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12.5px;font-weight:600;color:#374151;">
              <span class="mm-label-icon mm-ic-purple" style="width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
              </span>
              Reply To
            </div>
            <select class="mm-select" name="reply_to_email" id="reply_to_email" style="flex:1;min-width:0;">
              <option value="{{ $userdata?->email ?? (auth()->user()?->email ?? 'noreply@jeroworks.com') }}">{{ $userdata?->email ?? (auth()->user()?->email ?? 'noreply@jeroworks.com') }}</option>
            </select>
          </div>
        </div>

        {{-- To --}}
        @php
          $toChipEmails = array_values(array_filter(array_map('trim', explode(',', @$cc_email ?? ''))));
          // avatar colour palette cycling by index
          $chipColors = ['#DC2626','#7C3AED','#0891B2','#16A34A','#D97706','#DB2777'];
        @endphp
        <div class="mm-row" style="align-items:flex-start;">
          <div class="mm-label-col" style="padding-top:10px;">
            <span class="mm-label-icon mm-ic-blue">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            To <span class="mm-req">*</span>
          </div>
          <div class="mm-field-col">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
              <div style="flex:1;min-width:0;">
                {{-- hidden input for form submission --}}
                <input type="hidden" name="to_email_mass_mail" id="to_email_mass_mail" value="{{ @$cc_email }}">
                <div class="mm-chips-box">
                  @forelse($toChipEmails as $ci => $temail)
                    @php
                      $localPart = explode('@', $temail)[0] ?? 'U';
                      $parts     = preg_split('/[.\-_]/', $localPart);
                      $initials  = strtoupper(substr($parts[0] ?? 'U', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                      $avColor   = $chipColors[$ci % count($chipColors)];
                    @endphp
                    <span class="mm-chip">
                      <span class="mm-chip-av" style="background:{{ $avColor }};">{{ $initials }}</span>
                      <span class="mm-chip-text">{{ $temail }}</span>
                      <span class="badge badge-light-primary ms-1" style="font-size:10px;padding:2px 6px;">Primary</span>
                    </span>
                  @empty
                    <span style="font-size:12.5px;color:#9CA3AF;">No recipient</span>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- CC --}}
        <div class="mm-row">
          <div class="mm-label-col">
            <span class="mm-label-icon mm-ic-green">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            CC Email ID
          </div>
          <div class="mm-field-col">
            <div class="mm-tag-box" id="sendmail_cc_email_box" onclick="$('#sendmail_cc_email_input').focus()">
              <div class="mm-tag-list" id="sendmail_cc_email_tags"></div>
              <input type="text" class="mm-tag-input" id="sendmail_cc_email_input" placeholder="Type CC email and press Enter...">
              <a href="javascript:;" class="mm-add-recipient-btn" onclick="openSendMailRecipientDrawer(); return false;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <line x1="19" y1="8" x2="19" y2="14"/>
                  <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
                + Add Recipient
              </a>
            </div>
            <input type="hidden" name="sendmail_panel" id="sendmail_panel" value="">
            <div class="mm-hint" style="display:flex;justify-content:space-between;align-items:center;">
              <span>Type CC address and press Enter or comma, or click "+ Add Recipient"</span>
              <span style="background:#FFFBEB;border:1px solid #FEF3C7;color:#B45309;padding:2px 8px;border-radius:4px;font-weight:600;font-size:11px;">
                Deliverability Safeguard: Max 5 Total (1 To + up to 4 CC)
              </span>
            </div>
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
            <select name="templatemassmail" id="templatemassmail" class="mm-select">
              <option value="">Select Email Template...</option>
              @if (!empty($templateData))
                @foreach ($templateData as $tdata)
                  @php
                    $tId = $tdata->e_temp_id ?? ($tdata->id ?? '');
                    if (empty($tId)) continue;
                    // Render all active templates configured in Superadmin
                    if (isset($tdata->status) && (int) $tdata->status !== 1) continue;

                    $cName = !empty($companyTemp) ? $companyTemp : (auth()->user()->company->company_name ?? 'Our Company');
                    $clName = !empty($clientTemp) ? $clientTemp : $cName;
                    $jTitle = !empty($job_title) ? $job_title : 'Candidate Application';
                    $jLoc = !empty($location) ? $location : '';

                    $displayName = \App\Helper\GeneralMethod::replaceTemplateVariables(
                      $tdata->display_name ?? '',
                      [
                        'company_name' => $cName,
                        'client_name' => $clName,
                        'job_location' => $jLoc,
                        'work_location' => $jLoc,
                        'job_title' => $jTitle,
                      ]
                    );
                  @endphp
                  <option value="{{ $tId }}">{{ $displayName }}</option>
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
            <input class="mm-input" name="subject" id="subject_mass_mail" placeholder="Enter email subject" value="">
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
              <div id="candidate_email_quill"></div>
              <textarea id="editor1mass" name="editor1mass" class="d-none" rows="10">{{ @$template_body }}</textarea>
            </div>
          </div>
        </div>

      </div>
    </form>
    </div>


{{-- Isolated Enterprise Add Email Recipient Drawer for Send Email --}}
<div class="sendmail-rcp-overlay" id="kt_sendmail_recipient_overlay" onclick="closeSendMailRecipientDrawer()"></div>
<div id="kt_sendmail_recipient_drawer" class="bg-white shadow">
  <div class="rcp-container">
    <div class="rcp-header">
      <div class="rcp-header-left">
        <h3 class="rcp-header-title">Add Email Recipient</h3>
        <p class="rcp-header-subtitle">
          @if(!empty($client_contacts_data) && count($client_contacts_data) > 0)
            Choose a contact from your organization or client company to CC.
          @else
            Choose a contact from your organization to CC.
          @endif
        </p>
      </div>
      <button type="button" class="rcp-close-btn" onclick="closeSendMailRecipientDrawer()" title="Close drawer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <div class="rcp-tabs-wrap" id="sendmail_rcp_tabs_wrap">
      <div class="rcp-tab-card active" id="sendmail_rcp_tab_internal" data-tab="internal">
        <div class="rcp-tab-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="rcp-tab-info">
          <div class="rcp-tab-title">My Organization</div>
          <div class="rcp-tab-sub">{{ count($internal_team_data ?? []) }} active</div>
        </div>
      </div>
      @if(!empty($client_contacts_data) && count($client_contacts_data) > 0)
      <div class="rcp-tab-card" id="sendmail_rcp_tab_client" data-tab="client">
        <div class="rcp-tab-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M8 10h.01"></path><path d="M16 10h.01"></path><path d="M8 14h.01"></path><path d="M16 14h.01"></path></svg>
        </div>
        <div class="rcp-tab-info">
          <div class="rcp-tab-title">Client Contacts</div>
          <div class="rcp-tab-sub">{{ count($client_contacts_data) }} available</div>
        </div>
      </div>
      @endif
    </div>

    <div class="rcp-search-wrap">
      <div class="rcp-search-box">
        <span class="rcp-search-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </span>
        <input type="text" id="sendmail_rcp_search_input" placeholder="Search by name, email, or role...">
      </div>
    </div>

    <div class="rcp-body-list">
      <div id="sendmail_rcp_section_internal">
        @if(!empty($internal_team_data) && count($internal_team_data) > 0)
          @foreach($internal_team_data as $itUser)
            <div class="rcp-contact-item sendmail-contact-item" data-name="{{ strtolower($itUser->name ?? '') }}" data-display-name="{{ $itUser->name ?? '' }}" data-email="{{ strtolower($itUser->email ?? '') }}" data-role="{{ strtolower($itUser->display_name ?? ($itUser->role_name ?? '')) }}">
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
              <button type="button" class="rcp-toggle-btn sendmail-rcp-toggle-btn" data-email="{{ $itUser->email }}">
                <span class="rcp-btn-label-add">+ Add</span>
                <span class="rcp-btn-label-added" style="display:none;">✓ Added</span>
              </button>
            </div>
          @endforeach
        @else
          <div style="text-align:center;padding:36px 20px;color:#9CA3AF;font-size:13px;">No active internal team members found.</div>
        @endif
      </div>

      @if(!empty($client_contacts_data) && count($client_contacts_data) > 0)
      <div id="sendmail_rcp_section_client" style="display:none;">
        @foreach($client_contacts_data as $ccUser)
          @php
            $roleTag = $ccUser->role_tag ?? '';
            $badgeBg = '#DCFCE7';
            $badgeColor = '#15803D';
            if ($roleTag === 'Hiring Manager') {
                $badgeBg = '#EEF2FF';
                $badgeColor = '#4F46E5';
            } elseif (str_contains($roleTag, 'Agency Added By')) {
                $badgeBg = '#FEF3C7';
                $badgeColor = '#D97706';
            }
          @endphp
          <div class="rcp-contact-item sendmail-contact-item" data-name="{{ strtolower($ccUser->name ?? '') }}" data-display-name="{{ $ccUser->name ?? '' }}" data-email="{{ strtolower($ccUser->email ?? '') }}" data-role="{{ strtolower($ccUser->display_name ?? ($ccUser->company_name ?? '')) }}">
            <div style="display:flex;align-items:center;min-width:0;flex:1;">
              <div class="rcp-avatar" style="background:#DCFCE7;color:#15803D;">{{ strtoupper(substr($ccUser->name ?? 'C', 0, 1)) }}</div>
              <div class="rcp-info">
                <div style="display:flex;align-items:center;gap:6px;">
                  <span class="rcp-name">{{ $ccUser->name ?? 'Client Contact' }}</span>
                  @if(!empty($roleTag))
                    <span style="font-size:10px;padding:1px 6px;border-radius:10px;background:{{ $badgeBg }};color:{{ $badgeColor }};font-weight:600;">{{ $roleTag }}</span>
                  @endif
                </div>
                <div class="rcp-email">{{ $ccUser->email }}</div>
                <div class="rcp-role">{{ $ccUser->display_name ?: ($ccUser->company_name ?? 'Client') }}</div>
              </div>
            </div>
            <button type="button" class="rcp-toggle-btn sendmail-rcp-toggle-btn" data-email="{{ $ccUser->email }}">
              <span class="rcp-btn-label-add">+ Add</span>
              <span class="rcp-btn-label-added" style="display:none;">✓ Added</span>
            </button>
          </div>
        @endforeach
      </div>
      @endif
    </div>

    <div class="rcp-footer">
      <span id="sendmail_rcp_footer_count_text" style="font-size:12px;color:#6B7280;">0 recipients selected</span>
      <div style="display:flex;gap:8px;">
        <button type="button" class="rcp-btn-cancel" onclick="closeSendMailRecipientDrawer()">Close</button>
        <button type="button" class="rcp-btn-done" onclick="closeSendMailRecipientDrawer()">Done</button>
      </div>
    </div>
  </div>
</div>


<script>
    $(document).off('change', '#templatemassmail').on('change', '#templatemassmail', function() {
        var template = $(this).val();
        if (!template || parseInt(template, 10) <= 0) {
            return;
        }

        var $subjectInput = $('#subject_mass_mail');
        var origSubjectPlaceholder = $subjectInput.attr('placeholder') || 'Enter email subject';
        $subjectInput.attr('placeholder', 'Loading template...').val('');

        var url = "{{ route('service_company.cvtracker.email_template', ':id') }}".replace(':id', template);
        var token = "{{ csrf_token() }}";
        var to_email = $('#to_email_mass_mail').val() || '';
        var from_email = $('#from_email_mass_mail').val() || '';
        var jobid = $('#jobid').val() || 0;
        var mailtype = 4;

        var selectedApplicantListing = [];
        var rawAppData = <?php echo json_encode($applicationData ?? []); ?>;
        if (rawAppData) {
            var list = Array.isArray(rawAppData) ? rawAppData : Object.values(rawAppData);
            list.forEach(function(mval) {
                if (mval && mval.id) selectedApplicantListing.push(mval.id);
            });
        }
        if (selectedApplicantListing.length === 0) {
            var fallbackAppId = $('#applicationDataId').val();
            if (fallbackAppId) {
                selectedApplicantListing.push(fallbackAppId);
            }
        }

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': token,
                '_method': 'POST',
                'template': template,
                'selectedApplicantListing': selectedApplicantListing,
                'to_email': to_email,
                'from_email': from_email,
                'mailtype': mailtype,
                'jobID': jobid
            },
            dataType: 'json',
            success: function(response) {
                $subjectInput.attr('placeholder', origSubjectPlaceholder);
                if (response && response.status === 'success') {
                    if (typeof window.setCandidateEmailBodyHtml === 'function') {
                        window.setCandidateEmailBodyHtml(response.template_description || '');
                    } else {
                        $('#editor1mass').val(response.template_description || '');
                    }
                    $subjectInput.val(response.subject || '');
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.warning((response && response.message) ? response.message : 'Could not load the selected template.');
                    }
                }
            },
            error: function(xhr, status, error) {
                $subjectInput.attr('placeholder', origSubjectPlaceholder);
                console.error('[CandidateEmail] Template load error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load email template. Please try again.');
                }
            }
        });
    });


    /* Intent: Scoped event handler binding, Enter-key submission prevention, & submission lock state to prevent duplicate email sends from repeated drawer loads or rapid clicks */
    (function() {
        var isSendingEmail = false;

        $('#form1').off('submit keydown keypress').on('submit keydown keypress', function(e) {
            if (e.type === 'submit' || e.which === 13 || e.keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#form1').off('click', '#btnSubmitSendMail').on('click', '#btnSubmitSendMail', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);

            if (isSendingEmail || $btn.data('sending') === true || $btn.is(':disabled')) {
                return false;
            }

            // Client-generated UUID Idempotency Key retained during retry
            if (!$btn.data('idempotency-key')) {
                $btn.data('idempotency-key', (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : ('idemp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)));
            }
            var idempotencyKey = $btn.data('idempotency-key');

            isSendingEmail = true;
            $btn.data('sending', true);
            $btn.prop('disabled', true).addClass('disabled');

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            var id = 1;
            var url = "{{ route('service_company.cvtracker.send_mail_for_candidate', ':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";

            var rawAppData = <?php echo json_encode($applicationData ?? []); ?>;
            var appList = rawAppData ? (Array.isArray(rawAppData) ? rawAppData : Object.values(rawAppData)) : [];
            var sid = (appList.length > 0 && appList[0] && appList[0].id) ? appList[0].id : ($('#applicationDataId').val() || 0);
            var arr = [sid];

            var template = $('#templatemassmail').val();
            var from_email = $('#from_email_mass_mail').val();
            var to_email = $('#to_email_mass_mail').val();
            var cc_email = $('#sendmail_panel').val();
            var subject = $('#subject_mass_mail').val();
            var jobid = $('#jobid').val();
            var applicationDataId = $('#applicationDataId').val() || sid;
            var attached_cv = 0;
            var editor1mass = (typeof window.getCandidateEmailBodyHtml === 'function')
                ? window.getCandidateEmailBodyHtml()
                : ((typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1mass) ? CKEDITOR.instances.editor1mass.getData() : ($('#editor1mass').val() || ''));

            if (!subject || !subject.trim()) {
                Swal.fire("Warning", "Please enter an email subject.", "warning");
                isSendingEmail = false;
                $btn.data('sending', false);
                $btn.prop('disabled', false).removeClass('disabled');
                return false;
            }

            $('.indicator-label', $btn).hide();
            $('.indicator-progress', $btn).css('display', 'inline-flex');

            function closeDrawerTargeted() {
                if (typeof window.hideCandidateSendMailDrawer === 'function') {
                    window.hideCandidateSendMailDrawer();
                } else {
                    $('#kt_send_mail_showdetail_close').trigger('click');
                }
            }

            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    'X-Idempotency-Key': idempotencyKey
                },
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'idempotency_key': idempotencyKey,
                    'template': template,
                    'from_email': from_email,
                    'to_email': to_email,
                    'cc_email': cc_email,
                    'subject': subject,
                    'attached_cv': attached_cv,
                    'editor1': editor1mass,
                    'jobid': jobid,
                    'applicationDataId': applicationDataId,
                    'arr': arr
                },
                success: function(response) {
                    if (response.status == 'coinCheck') {
                        Swal.fire("Failed", ` ${response.message}`, "warning").then(function() {
                            closeDrawerTargeted();
                        });
                        return false;
                    }

                    if (response.status == "success") {
                        $btn.removeData('idempotency-key');
                        swalWithBootstrapButtons.fire(
                            'Success!',
                            'Email has been queued and sent.',
                            'success'
                        ).then(function() {
                            closeDrawerTargeted();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire("Error", "An error occurred while sending email. Please try again.", "error");
                },
                complete: function() {
                    /* Intent: Restore button state only after the AJAX request completely resolves */
                    isSendingEmail = false;
                    $btn.data('sending', false);
                    $btn.prop('disabled', false).removeClass('disabled');
                    $('.indicator-label', $btn).css('display', 'inline-flex');
                    $('.indicator-progress', $btn).hide();
                }
            });
        });
    })();

    window._sendMailCcTags = [];
    var MAX_SENDMAIL_TOTAL_RECIPIENTS = 5;

    window.getSendMailTotalRecipientsCount = function() {
        var toCount = ($('#to_email_mass_mail').val() || '').trim() ? 1 : 0;
        return toCount + window._sendMailCcTags.length;
    };

    window.openSendMailRecipientDrawer = function() {
        $('body > #kt_sendmail_recipient_drawer').not('#application_send_mail_body_content #kt_sendmail_recipient_drawer').remove();
        $('#kt_sendmail_recipient_overlay').show();
        $('#kt_sendmail_recipient_drawer').show();
        syncSendMailRecipientDrawerState();
    };

    window.closeSendMailRecipientDrawer = function() {
        $('#kt_sendmail_recipient_drawer').hide();
        $('#kt_sendmail_recipient_overlay').hide();
    };
    window.closeCandidateEmailRecipientDrawer = window.closeSendMailRecipientDrawer;

    // Tab switching (Internal vs Client)
    $(document).off('click', '#sendmail_rcp_tab_internal, #sendmail_rcp_tab_client').on('click', '#sendmail_rcp_tab_internal, #sendmail_rcp_tab_client', function() {
        var tab = $(this).attr('data-tab');
        $('#sendmail_rcp_tab_internal, #sendmail_rcp_tab_client').removeClass('active');
        $(this).addClass('active');
        if (tab === 'internal') {
            $('#sendmail_rcp_section_internal').show();
            $('#sendmail_rcp_section_client').hide();
        } else {
            $('#sendmail_rcp_section_internal').hide();
            $('#sendmail_rcp_section_client').show();
        }
        applySendMailRcpSearch();
    });

    // Real-time in-memory search
    function applySendMailRcpSearch() {
        var q = ($('#sendmail_rcp_search_input').val() || '').toLowerCase().trim();
        var isInternal = $('#sendmail_rcp_tab_internal').hasClass('active');
        var $sec = isInternal ? $('#sendmail_rcp_section_internal') : $('#sendmail_rcp_section_client');
        $sec.find('.sendmail-contact-item').each(function() {
            var name = $(this).attr('data-name') || '';
            var email = $(this).attr('data-email') || '';
            var role = $(this).attr('data-role') || '';
            var match = !q || name.indexOf(q) !== -1 || email.indexOf(q) !== -1 || role.indexOf(q) !== -1;
            $(this).toggle(match);
        });
    }
    $(document).off('input', '#sendmail_rcp_search_input').on('input', '#sendmail_rcp_search_input', applySendMailRcpSearch);

    // Toggle button click in drawer
    $(document).off('click', '.sendmail-rcp-toggle-btn').on('click', '.sendmail-rcp-toggle-btn', function(e) {
        e.preventDefault();
        var email = ($(this).attr('data-email') || '').trim().toLowerCase();
        if (!email) return;
        var idx = window._sendMailCcTags.indexOf(email);
        if (idx === -1) {
            if (getSendMailTotalRecipientsCount() >= MAX_SENDMAIL_TOTAL_RECIPIENTS) {
                Swal.fire({
                    title: 'Recipient Limit Reached',
                    text: 'Maximum ' + MAX_SENDMAIL_TOTAL_RECIPIENTS + ' total recipients (1 Candidate + up to 4 CC) allowed to maintain high email deliverability. Please remove an existing CC recipient first.',
                    icon: 'warning',
                    confirmButtonColor: '#2563EB'
                });
                return;
            }
            window._sendMailCcTags.push(email);
        } else {
            window._sendMailCcTags.splice(idx, 1);
        }
        renderSendMailCcTags();
        syncSendMailRecipientDrawerState();
    });

    function renderSendMailCcTags() {
        var html = '';
        window._sendMailCcTags.forEach(function(email) {
            html += '<span class="mm-email-tag">' +
                '<span class="mm-email-tag-text">' + email + '</span>' +
                '<span class="mm-email-tag-remove" onclick="removeSendMailCcTag(\'' + email + '\')">&times;</span>' +
                '</span>';
        });
        $('#sendmail_cc_email_tags').html(html);
        $('#sendmail_panel').val(window._sendMailCcTags.join(','));
        $('#sendmail_rcp_footer_count_text').text(window._sendMailCcTags.length + ' CC recipient(s) selected');
    }

    window.removeSendMailCcTag = function(email) {
        var idx = window._sendMailCcTags.indexOf(email.toLowerCase());
        if (idx !== -1) {
            window._sendMailCcTags.splice(idx, 1);
            renderSendMailCcTags();
            syncSendMailRecipientDrawerState();
        }
    };

    function syncSendMailRecipientDrawerState() {
        $('.sendmail-rcp-toggle-btn').each(function() {
            var email = ($(this).attr('data-email') || '').trim().toLowerCase();
            var isAdded = window._sendMailCcTags.indexOf(email) !== -1;
            $(this).toggleClass('is-added', isAdded);
            $(this).find('.rcp-btn-label-add').toggle(!isAdded);
            $(this).find('.rcp-btn-label-added').toggle(isAdded);
        });
    }

    // Tag input key handling (Enter, comma, Backspace)
    $(document).off('keydown', '#sendmail_cc_email_input').on('keydown', '#sendmail_cc_email_input', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            var val = $(this).val().replace(/,/g, '').trim().toLowerCase();
            if (val) {
                if (getSendMailTotalRecipientsCount() >= MAX_SENDMAIL_TOTAL_RECIPIENTS) {
                    Swal.fire({
                        title: 'Recipient Limit Reached',
                        text: 'Maximum ' + MAX_SENDMAIL_TOTAL_RECIPIENTS + ' total recipients allowed.',
                        icon: 'warning',
                        confirmButtonColor: '#2563EB'
                    });
                    return;
                }
                if (window._sendMailCcTags.indexOf(val) === -1) {
                    window._sendMailCcTags.push(val);
                    renderSendMailCcTags();
                    syncSendMailRecipientDrawerState();
                }
                $(this).val('');
            }
        } else if (e.key === 'Backspace' && !$(this).val() && window._sendMailCcTags.length > 0) {
            window._sendMailCcTags.pop();
            renderSendMailCcTags();
            syncSendMailRecipientDrawerState();
        }
    });

    // Initialize existing CC tags if present in #sendmail_panel on load
    (function initSendMailCcTags() {
        var initCc = ($('#sendmail_panel').val() || '').trim();
        if (initCc) {
            var arr = initCc.split(',').map(function(s) { return s.trim().toLowerCase(); }).filter(Boolean);
            window._sendMailCcTags = arr.slice(0, 4); // max 4 CC
            renderSendMailCcTags();
        }
    })();
</script>
