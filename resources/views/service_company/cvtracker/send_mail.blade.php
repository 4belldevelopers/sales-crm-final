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
        if (window.candidateEmailQuill && window.candidateEmailQuill.root) {
            window.candidateEmailQuill.root.innerHTML = cleanHtml;
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
                window.candidateEmailQuill.root.innerHTML = existingContent;
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
    .mm-add-email-btn { position: absolute; right: 0; top: 0; display: flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: #2563EB; background: none; border: none; cursor: pointer; padding: 9px 14px; }
    .mm-add-email-btn:hover { color: #1d4ed8; }
    .mm-input-with-btn { padding-right: 110px; }
    .mm-footer { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; border-top: 1px solid #E5E7EB; background: #F9FAFB; flex-shrink: 0; gap: 10px; flex-wrap: wrap; position: sticky; bottom: 0; z-index: 10; }
    .mm-footer-left { display: flex; align-items: center; gap: 8px; }
    .mm-footer-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }
    .mm-btn-cancel { padding: 8px 18px; border-radius: 8px; border: 1px solid #E5E7EB; background: #fff; color: #374151; font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; transition: background 0.15s; }
    .mm-btn-cancel:hover { background: #F3F4F6; }
    .mm-btn-send { padding: 8px 20px; border-radius: 8px; border: none; background: #2563EB; color: #fff; font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; transition: background 0.15s; }
    .mm-btn-send:hover { background: #1d4ed8; }
    </style>

    <div class="mm-wrap">
    <form id="form1" onsubmit="return false;">
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
              <option value="notify@jeroworks.com">{{ $userdata->name ?? $userdata->email }} &lt;notify@jeroworks.com&gt;</option>
            </select>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;font-size:12.5px;font-weight:600;color:#374151;">
              <span class="mm-label-icon mm-ic-purple" style="width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
              </span>
              Reply To
            </div>
            <select class="mm-select" name="reply_to_email" id="reply_to_email" style="flex:1;min-width:0;">
              <option value="{{ $userdata->email }}">{{ $userdata->email }}</option>
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
            <div style="position:relative;">
              <input class="mm-input mm-input-with-btn" name="sendmail_panel" id="sendmail_panel" value="" placeholder="Type an email...">
              <a href="javascript:;" class="mm-add-email-btn sendmail_panel">+ Add Email</a>
            </div>
            <div class="mm-hint">Add email addresses separated by commas</div>
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
                  @if (@$tdata->tempForInterview == 1 && $selfCompany == 1)
                    <option value="{{ $tdata->id }}">{{ str_replace(['[{COMPANY_NAME}]', '[{JOB_TITLE}]', '[{JOB_LOCATION}]'], [$companyTemp, $job_title, $location], $tdata->display_name) }}</option>
                  @endif
                  @if ($selfCompany == '')
                    <option value="{{ $tdata->id }}">{{ str_replace(['[{CLIENT_NAME}]', '[{COMPANY_NAME}]', '[{JOB_LOCATION}]', '[{JOB_TITLE}]'], [$clientTemp, $companyTemp, $location], $tdata->display_name, $job_title) }}</option>
                  @endif
                  @if (@$tdata->tempForInterview == 4 && $selfCompany == 1)
                    <option value="{{ $tdata->id }}">{{ str_replace(['[{COMPANY_NAME}]'], [$companyTemp], $tdata->display_name) }}</option>
                  @endif
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

      <div class="mm-footer">
        <div class="mm-footer-left">
          <button type="button" class="mm-btn-cancel" onclick="(function(){
              if (typeof window.hideCandidateSendMailDrawer === 'function') {
                  window.hideCandidateSendMailDrawer();
                  return;
              }
              var d = document.getElementById('kt_send_mail_showdetail_drawer') || document.getElementById('kt_mass_mail_drawer') || document.getElementById('kt_mass_mail_drawer_aptk');
              if (d && typeof KTDrawer !== 'undefined') {
                  var dr = KTDrawer.getInstance(d);
                  if (dr) dr.hide();
              }
              $('.sd-mail-overlay').remove();
          })()">Cancel</button>
        </div>
        <div class="mm-footer-right">
          <button type="button" class="mm-btn-send" name="btnSubmitSendMail" id="btnSubmitSendMail" value="btnSubmitSendMail">
            <span class="indicator-label">Send</span>
            <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
          </button>
        </div>
      </div>
    </form>
    </div>


<div id="kt_activitiesIntTeam_toggle_sendmail"></div>
<div id="kt_activitiesIntTeam_sendmail" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
    data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle_sendmail"
    data-kt-drawer-close="#kt_activitiesIntTeam_close_sendmail">
    <div class="card-body position-relative px-0 py-0" id="kt__to_body">
        <div id="kt_sendmail_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__sendmail_body"
            data-kt-scroll-dependencies="#kt__sendmail_header, #kt__sendmail_footer" data-kt-scroll-offset="5px"
            style="">
            <!--begin::Team card-->
            <div class="card card-custom mx-2">
                <!--begin::Team header-->
                <div class="card-header px-5 py-0" id="kt_to_header">
                    <div class="d-flex align-items-center py-1">
                        <div class=" d-flex align-items-center">
                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                    class="text-dark fw-bolder fs-6" id="light_to_name">Assign Members</a> </div>
                        </div>
                        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                            id="kt_activitiesIntTeam_close_sendmail"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                        fill="currentColor">
                                        <rect x="0" y="7" width="16" height="2"
                                            rx="1" />
                                        <rect opacity="0.5"
                                            transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                            x="0" y="7" width="16" height="2" rx="1" />
                                    </g>
                                </svg>
                            </span> </button>
                    </div>
                </div>
                <!--end::Team header-->
                <!--begin::Team Content-->
                <div class="card-body py-7">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_to">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="fw-bolder text-muted">
                                <th class="min-w-250px">Name</th>
                                <th class="min-w-250px">Contact</th>
                                <th class="min-w-250px">Company</th>
                                <th class="min-w-100px">Action</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="text-gray-600 fw-bold">
                            @if (!empty($cc_email_assignData))

                                @foreach ($cc_email_assignData as $teamVal)
                                    @php
                                        $bgTextDecore = $strVal = '';
                                        $str = trim(@$teamVal->name);
                                        $str = $strVal = strtolower($str[0]);
                                        $str = ord($str);
                                        if ($str >= 97 && $str <= 101) {
                                            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                                        } elseif ($str >= 102 && $str <= 106) {
                                            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                                        } elseif ($str >= 107 && $str <= 111) {
                                            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                                        } elseif ($str >= 112 && $str <= 116) {
                                            $bgTextDecore = 'fs-3 bg-light-success text-success';
                                        } elseif ($str >= 117 && $str <= 121) {
                                            $bgTextDecore = 'fs-3 bg-dark text-secondary';
                                        } else {
                                            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                                        }

                                        $working_status = $working_status_style = '';
                                        $working_status = @$teamVal->working_status == 1 ? 'Enable' : 'Disable';
                                        $working_status_style =
                                            @$teamVal->working_status == 1 ? 'text-success' : 'text-danger';
                                    @endphp

                                    <tr>
                                        <!--begin::Name-->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin::Avatar-->
                                                <div
                                                    class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
                                                    <a href="javascript:void(0);" class="">
                                                        <div
                                                            class="symbol-label {{ @$bgTextDecore }} border-radius-5">
                                                            {{ strtoupper(@$strVal) }}</div>
                                                    </a> </div>
                                                <!--end::Avatar-->
                                                <!--begin::Name details-->
                                                <div class="d-flex flex-column"> <span
                                                        class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span>
                                                    <span
                                                        class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span>
                                                </div>
                                                <!--end::Name details-->
                                            </div>
                                        </td>
                                        <!--end::Name-->
                                        <!--begin::Role-->
                                        <td>
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->email }}
                                            </div>
                                        </td>
                                        <!--begin::Role-->
                                        <td>
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6"></span>
                                                {{ @$teamVal->company_name }}</div>
                                        </td>
                                        <!--begin::Action-->
                                        <td>
                                            <div class="d-flex flex-column"> <a
                                                    id="sendmail_team_select_{{ @$teamVal->id }}"
                                                    href="javascript:void(0);"
                                                    class="sendmail_select btn btn-sm btn-light-primary w-130"
                                                    rel="{{ @$teamVal->id }}" data-assign="0"
                                                    data-title="{{ @$teamVal->name }}"
                                                    data-email="{{ @$teamVal->email }}">Add</a> </div>
                                        </td>
                                        <!--end::Action-->
                                    </tr>
                                @endforeach
                            @endif


                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Team Content-->
            </div>
            <!--end::Team card-->
        </div>
    </div>
</div>


<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
{{-- <script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script> --}}
<script>
    $('#templatemassmail').change(function() {


        var template = $('#templatemassmail').val();
        if (template > 0) {
            var id = $('#templatemassmail').val();
            var url = "{{ route('service_company.cvtracker.email_template', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var to_email = $('#to_email_mass_mail').val() || '';
            var from_email = $('#from_email_mass_mail').val() || '';
            var jobid = $('#jobid').val() || 0;
            var mailtype = 4;

            var selectedApplicantListing = [];
            var jqueryarray = <?php echo json_encode($applicationData); ?>;
            if (jqueryarray && Array.isArray(jqueryarray)) {
                jqueryarray.forEach(function(mval) {
                    if (mval && mval.id) selectedApplicantListing.push(mval.id);
                });
            }

            var jobID = jobid;

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
                    'jobID': jobID
                },
                success: function(response) {
                    if (response && response.status == "success") {
                        if (typeof window.setCandidateEmailBodyHtml === 'function') {
                            window.setCandidateEmailBodyHtml(response.template_description || '');
                        } else {
                            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1mass) {
                                CKEDITOR.instances.editor1mass.setData(response.template_description);
                            }
                            $('#editor1mass').val(response.template_description || '');
                        }
                        $('#subject_mass_mail').val(response.subject || '');
                    }
                }
            });

        } else {
            $('#editor').html('Choose email template');
        }



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

            var jqueryarray = <?php echo json_encode($applicationData); ?>;
            var sid = (jqueryarray && jqueryarray.length > 0) ? jqueryarray[0]['id'] : ($('#applicationDataId').val() || 0);
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
            $('.indicator-progress', $btn).show();

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
                    $('.indicator-label', $btn).show();
                    $('.indicator-progress', $btn).hide();
                }
            });
        });
    })();

    $(document).on('click','.sendmail_select', function(e){

        var rel = $(this).attr('rel');
        var assign = $('#sendmail_team_select_' + rel).attr("data-assign");
        var title = $('#sendmail_team_select_' + rel).attr("data-title");
        var email = $('#sendmail_team_select_' + rel).attr("data-email");

        if (assign == 0) {
            $(this).addClass("btn-light-danger");
            $(this).removeClass("btn-light-primary");
            $(this).text('Remove');
            $(this).attr("data-assign", 1);
            AddEventSend(title, email);
        } else if (assign == 1) {
            $(this).addClass("btn-light-primary");
            $(this).removeClass("btn-light-danger");
            $(this).text('Add');
            $(this).attr("data-assign", 0);
            DeleteEventTo(title, email);
        }

    });

    function AddEventSend(name, email) {
        var cleanEmail = (email || '').trim();
        if (!cleanEmail) return;
        var to_email = ($('#sendmail_panel').val() || '').trim();
        if (to_email !== '') {
            var emailArr = to_email.split(',').map(function(e){ return e.trim(); }).filter(Boolean);
            if (!emailArr.includes(cleanEmail)) {
                emailArr.push(cleanEmail);
                $('#sendmail_panel').val(emailArr.join(', '));
            }
        } else {
            $('#sendmail_panel').val(cleanEmail);
        }
    }

    function DeleteEventTo(contact_person_name, email) {
        var cleanEmail = (email || '').trim().toLowerCase();
        var to_email = ($('#sendmail_panel').val() || '').trim();
        if (to_email !== '') {
            var emailArr = to_email.split(',').map(function(e){ return e.trim(); }).filter(Boolean);
            var filtered = emailArr.filter(function(e) {
                return e.toLowerCase() !== cleanEmail;
            });
            $('#sendmail_panel').val(filtered.join(', '));
        }
    }
</script>
<script>
    // from_email_mass_mail is a <select> — no Tagify needed
    // to_email_mass_mail is readonly (hidden input) — no Tagify

    // var cc_email	=	document.querySelector('#cc_email');
    // new Tagify(cc_email);
</script>
