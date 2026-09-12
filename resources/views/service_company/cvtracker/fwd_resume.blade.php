@php 
		use App\Helper\GeneralMethod;
		$salaryMode 					=	GeneralMethod::salaryMode();
		$currencySymbole 				=	GeneralMethod::currencySymbole();
			
@endphp 
<script>
if (typeof define === 'function' && define.amd) {
    window.__savedAmdDefine = define.amd;
    define.amd = false;
}
</script>
<script src="{{ asset('js/pdf-lib.min.js') }}"></script>
<script>
if (window.__savedAmdDefine) {
    define.amd = window.__savedAmdDefine;
    delete window.__savedAmdDefine;
}
</script>
<script src="{{ asset('js/mammoth.browser.min.js') }}" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js'"></script>
<script src="{{ asset('js/opendoc/AgyForwardResumeHelper.js') }}"></script>
<script src="{{ asset('js/opendoc/AgyWasmEngine.js') }}"></script>
<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
<script>
									
if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
	
CKEDITOR.tools.enableHtml5Elements( document );
CKEDITOR.config.height 		=	350;
CKEDITOR.config.width 		=	'auto';

var initSample 				= ( function() 
{
		var wysiwygareaAvailable 	= isWysiwygareaAvailable(),
		isBBCodeBuiltIn 			= !!CKEDITOR.plugins.get( 'bbcode' );

	return function() {
								var editorElement = CKEDITOR.document.getById( 'editor1' );
						
								if ( isBBCodeBuiltIn ) {
									editorElement.setHtml(
										'Hello world!\n\n' +
										'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
									);
								}
								if ( wysiwygareaAvailable ) {
									CKEDITOR.replace( 'editor1' );
								} else {
									editorElement.setAttribute( 'contenteditable', 'true' );   
									CKEDITOR.inline( 'editor1' );
								}							
					};
	function isWysiwygareaAvailable() 
	{
		if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
			return true;
		}
		return !!CKEDITOR.plugins.get( 'wysiwygarea' );
	}
	
} )();


initSample();

</script>

<div class="main_container" id="main_container">
@php
$template_body = '';
$to_email = '';
if($to_email_from_count > 0){
    $to_email = implode(', ', $to_email_from_data);
}
$cc_email = '';
if($cc_email_count > 0){
    $cc_email = implode(', ', $cc_email_data);
}
@endphp

<style>
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr{width: max-content!important;}
  .table > :not(caption) > * > *{padding: 0rem;}
  td:nth-child(n+2), th:nth-child(n+2){width: revert!important;}
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesIntTeamsss{width: 750px;}}
@media only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesIntTeamsss{width: 750;}}

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
.mm-ic-orange { background: #FFF7ED; color: #EA580C; }
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
/* Chip display for readonly To field */
.mm-chips-box { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; border: 1px solid #E5E7EB; border-radius: 8px; padding: 6px 10px; background: #F9FAFB; min-height: 40px; }
.mm-chip { display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 20px; padding: 3px 10px 3px 4px; font-size: 12.5px; color: #1E40AF; font-family: 'Poppins', sans-serif; }
.mm-chip-av { width: 22px; height: 22px; border-radius: 50%; background: #DC2626; color: #fff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mm-chip-text { max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mm-chip-x { background: none; border: none; padding: 0; margin-left: 2px; color: #93C5FD; cursor: default; font-size: 13px; line-height: 1; }
.mm-editor-wrap { border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; }
.mm-row-body { align-items: flex-start; }
.mm-add-email-btn { position: absolute; right: 0; top: 0; display: flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; color: #2563EB; background: none; border: none; cursor: pointer; padding: 9px 14px; }
.mm-add-email-btn:hover { color: #1d4ed8; }
.mm-input-with-btn { padding-right: 110px; }
.mm-footer { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; border-top: 1px solid #E5E7EB; background: #F9FAFB; flex-shrink: 0; gap: 10px; flex-wrap: wrap; }
.mm-footer-left { display: flex; align-items: center; gap: 8px; }
.mm-footer-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }
.mm-btn-cancel { padding: 8px 18px; border-radius: 8px; border: 1px solid #E5E7EB; background: #fff; color: #374151; font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; transition: background 0.15s; }
.mm-btn-cancel:hover { background: #F3F4F6; }
.mm-btn-send { padding: 8px 20px; border-radius: 8px; border: none; background: #2563EB; color: #fff; font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; cursor: pointer; transition: background 0.15s; }
.mm-btn-send:hover { background: #1d4ed8; }
</style>
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>

<div class="mm-wrap">
<form id="form1">
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
        <select class="mm-select" name="from_email" id="from_email" style="flex:1;min-width:0;">
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
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        To Email ID <span class="mm-req">*</span>
      </div>
      <div class="mm-field-col">
        <div style="position:relative;">
          <input class="mm-input mm-input-with-btn" name="to_email" id="to_email" value="" placeholder="Enter recipient email">
          <a href="javascript:;" class="mm-add-email-btn to_email_panel">+ Add Email</a>
        </div>
        <div class="mm-hint">Please enter recipient email</div>
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
          <input class="mm-input mm-input-with-btn" name="cc_email_panel" id="cc_email_panel" value="" placeholder="Enter CC email">
          <a href="javascript:;" class="mm-add-email-btn cc_email_panel">+ Add Email</a>
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
        <select name="template" id="template" class="mm-select">
          <option value="">Select Email Template...</option>
          @if(!empty($templateData))
            @foreach($templateData as $tdata)
              <option value="{{ $tdata->id }}">{{ $tdata->display_name }}</option>
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
        <input class="mm-input" name="subject" id="subject" placeholder="Enter email subject" value="">
      </div>
    </div>

    {{-- Resume Attachment Format Selector (Download Resume Center Integration) --}}
    <div class="mm-row">
      <div class="mm-label-col">
        <span class="mm-label-icon mm-ic-teal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </span>
        Attach Resume
      </div>
      <div class="mm-field-col">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600;color:#1F2937;">
            <input type="checkbox" id="fwd_attach_cv_toggle" name="attached_cv" value="1" checked style="width:16px;height:16px;accent-color:#0D9488;cursor:pointer;">
            <span>Attach Resume to Email</span>
          </label>
        </div>

        <div id="fwd_format_container">
          <select name="attached_cv_format" id="fwd_resume_format" class="mm-select" style="font-weight:500;">
            <optgroup label="1. Standard Resume Options">
              <option value="1" selected data-label="Original Candidate Resume" data-desc="Pristine resume file as originally uploaded by candidate/agency.">1. Original Resume (Default)</option>
              <option value="41" data-label="Resume + Company Branding Logo" data-desc="Resume with your agency/company logo header & confidential watermark on Page 1.">41. Resume + Company Branding Logo</option>
            </optgroup>
            <optgroup label="2. Pre-Screened Report">
              <option value="5" data-label="Candidate Pre-Screened Report PDF" data-desc="Complete verification & interview evaluation assessment dossier.">5. Candidate Pre-Screened Report PDF</option>
            </optgroup>
            <optgroup label="3. Recruiter Packages (Report + Resume)">
              <option value="42" data-label="Dossier: Pre-Screened Report + Original Resume" data-desc="Combined evaluation report followed by original candidate resume.">42. Pre-Screened Report + Original Resume</option>
              <option value="43" data-label="Dossier: Pre-Screened Report + Branded Resume" data-desc="Combined evaluation report with company-branded resume.">43. Pre-Screened Report + Branded Resume</option>
            </optgroup>
            <optgroup label="4. Highlighted Skills">
              <option value="44" data-label="Resume + Highlighted Skills" data-desc="Candidate resume with matching ATS keywords highlighted in yellow.">44. Resume + Highlighted Skills</option>
              <option value="45" data-label="Resume + Company Branding + Highlights" data-desc="Company logo header on Page 1 plus highlighted matching skills.">45. Resume + Company Branding + Highlights</option>
              <option value="46" data-label="Dossier: Report + Highlighted Resume" data-desc="Evaluation dossier with highlighted candidate resume.">46. Pre-Screened Report + Highlighted Resume</option>
              <option value="47" data-label="Dossier: Report + Branding + Highlights" data-desc="Full presentation package: Report + Company logo header + Highlighted skills.">47. Full Recruiter Dossier (Report + Branding + Highlights)</option>
            </optgroup>
            <optgroup label="5. Privacy & Anonymization (Anti-Poaching Shield)">
              <option value="48" data-label="Anonymized (Masked Contact Info)" data-desc="Candidate email, phone number, and LinkedIn redacted for privacy.">48. Masked Contact Info (Anonymized)</option>
              <option value="49" data-label="Anonymized + Company Branding" data-desc="Masked candidate contact info with your agency header on Page 1.">49. Masked Contact Info + Company Branding</option>
              <option value="50" data-label="Anonymized + Highlights" data-desc="Masked candidate contact info with highlighted matching skills.">50. Masked Contact Info + Highlights</option>
              <option value="51" data-label="Full Anonymized Recruiter Dossier" data-desc="Complete anonymized package: Report + Masked contact info + Agency branding.">51. Full Anonymized Recruiter Dossier</option>
            </optgroup>
          </select>

          <!-- Live Attachment Preview Badge -->
          <div id="fwd_attachment_preview_card" style="margin-top:8px;padding:9px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div style="display:flex;align-items:center;gap:8px;min-width:0;">
              <span style="width:26px;height:26px;border-radius:6px;background:#DCFCE7;color:#15803D;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:700;">PDF</span>
              <div style="min-width:0;">
                <div id="fwd_preview_filename" style="font-size:12.5px;font-weight:600;color:#166534;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  {{ 'JERO_' . (!empty($applicationData[0]->full_name) ? str_replace(' ', '_', $applicationData[0]->full_name) : 'Candidate') . '_Resume.pdf' }}
                </div>
                <div id="fwd_preview_desc" style="font-size:11px;color:#15803D;margin-top:1px;">
                  Pristine resume file as originally uploaded by candidate/agency.
                </div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
              <span style="font-size:11px;font-weight:600;color:{{ count($applicationData ?? []) > 10 ? '#B91C1C' : '#15803D' }};background:{{ count($applicationData ?? []) > 10 ? '#FEE2E2' : '#DCFCE7' }};padding:3px 9px;border-radius:12px;white-space:nowrap;">
                📎 {{ count($applicationData ?? []) }} / 10 Max Resumes
              </span>
            </div>
          </div>
          @if(count($applicationData ?? []) > 10)
            <div style="margin-top:7px;padding:7px 11px;background:#FEF2F2;border:1px solid #FECACA;border-radius:7px;color:#991B1B;font-size:11.5px;display:flex;align-items:center;gap:6px;">
              <span>⚠️</span>
              <span><strong>Notice:</strong> You selected {{ count($applicationData) }} candidates. To ensure 100% email deliverability and avoid 25MB mail server rejections, the first 10 resumes will be attached.</span>
            </div>
          @endif
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
          <textarea id="editor1" name="editor1" rows="10">{{ @$template_body }}</textarea>
        </div>
      </div>
    </div>

  </div>

  <div class="mm-footer">
    <div class="mm-footer-left">
      <button type="button" class="mm-btn-cancel" onclick="(function(){var d=document.getElementById('kt_fwd_resume_drawer')||document.getElementById('kt_fwd_resume_drawer_aptk');if(d&&typeof KTDrawer!=='undefined'){var dr=KTDrawer.getInstance(d);if(dr)dr.hide();}})()">Cancel</button>
    </div>
    <div class="mm-footer-right">
      <button type="button" class="mm-btn-send" name="btnSubmit" id="btnSubmit" value="btnSubmit">
        <span class="indicator-label">Send</span>
        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
      </button>
    </div>
  </div>
</form>
</div>




<div id="kt_activitiesIntTeam_toggle_cc"></div>
<div id="kt_activitiesIntTeam_cc" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle_cc" data-kt-drawer-close="#kt_activitiesIntTeam_close_cc">
  <div class="card-body position-relative px-0 py-0" id="kt__cc_body">
  <div id="kt_cc_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__cc_body" data-kt-scroll-dependencies="#kt__cc_header, #kt__cc_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div class="card-header px-5 py-0" id="kt__cc_header">
          <div class="d-flex align-items-center py-1">
            <div class=" d-flex align-items-center"> 
              <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light__cc_name">Assign Members</a> </div>
            </div>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
          <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesIntTeam_close_cc"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                <rect x="0" y="7" width="16" height="2" rx="1" />
                <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
              </g>
            </svg>
            </span> </button>
          </div>
        </div>
      <!--end::Team header-->
      <!--begin::Team Content-->
      <div class="card-body py-7">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_cc">
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
           	@if(!empty($cc_email_assignData))
           
							@foreach($cc_email_assignData as $teamVal )
							
								@php
                                	$bgTextDecore = $strVal = '';
									$str 			= trim(@$teamVal->name);
									$str 			= $strVal = strtolower($str[0]);
									$str = ord($str);
									if ($str >= 97 && $str <= 101) {
										$bgTextDecore = 'fs-3 bg-light-danger text-danger';
									} 
									else if ($str >= 102 && $str <= 106) {
										$bgTextDecore = 'fs-3 bg-light-warning text-warning';
									} 
									else if ($str >= 107 && $str <= 111) {
										$bgTextDecore = 'fs-3 bg-light-primary text-primary';
									} 
									else if ($str >= 112 && $str <= 116) {
										$bgTextDecore = 'fs-3 bg-light-success text-success';
									} 
									else if ($str >= 117 && $str <= 121) {
										$bgTextDecore = 'fs-3 bg-dark text-secondary';
									} else {
										$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
									}

									$working_status	= $working_status_style	=	'';
									$working_status			=  @$teamVal->working_status ==1 ? 'Enable' : 'Disable';
									$working_status_style	=  @$teamVal->working_status ==1 ? 'text-success' : 'text-danger';
									@endphp
									
							 <tr>
              <!--begin::Name-->
              <td><div class="d-flex align-items-center">
                  <!--begin::Avatar-->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                    <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::Name details-->
                  <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span> <span class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span> </div>
                  <!--end::Name details-->
                </div></td>
              <!--end::Name-->
              <!--begin::Role-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->email }}</div></td>
              <!--begin::Role-->
             <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->company_name }}</div></td>
              <!--begin::Action-->
              <td><div class="d-flex flex-column"> <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select btn btn-sm btn-light-primary w-130" 
											 rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
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



<div id="kt_activitiesIntTeam_toggle_to"></div>
<div id="kt_activitiesIntTeam_to" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesIntTeam_toggle_to" data-kt-drawer-close="#kt_activitiesIntTeam_close_to">
  <div class="card-body position-relative px-0 py-0" id="kt__to_body">
  <div id="kt_to_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__to_body" data-kt-scroll-dependencies="#kt__to_header, #kt__to_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div class="card-header px-5 py-0" id="kt_to_header">
          <div class="d-flex align-items-center py-1">
            <div class=" d-flex align-items-center"> 
              <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_to_name">Assign Members</a> </div>
            </div>
            <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
          <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesIntTeam_close_to"> <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                <rect x="0" y="7" width="16" height="2" rx="1" />
                <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
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
           	@if(!empty($cc_email_assignData))
           
							@foreach($cc_email_assignData as $teamVal )
							
								@php
                                	$bgTextDecore = $strVal = '';
									$str 			= trim(@$teamVal->name);
									$str 			= $strVal = strtolower($str[0]);
									$str = ord($str);
									if ($str >= 97 && $str <= 101) {
										$bgTextDecore = 'fs-3 bg-light-danger text-danger';
									} 
									else if ($str >= 102 && $str <= 106) {
										$bgTextDecore = 'fs-3 bg-light-warning text-warning';
									} 
									else if ($str >= 107 && $str <= 111) {
										$bgTextDecore = 'fs-3 bg-light-primary text-primary';
									} 
									else if ($str >= 112 && $str <= 116) {
										$bgTextDecore = 'fs-3 bg-light-success text-success';
									} 
									else if ($str >= 117 && $str <= 121) {
										$bgTextDecore = 'fs-3 bg-dark text-secondary';
									} else {
										$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
									}

									$working_status	= $working_status_style	=	'';
									$working_status			=  @$teamVal->working_status ==1 ? 'Enable' : 'Disable';
									$working_status_style	=  @$teamVal->working_status ==1 ? 'text-success' : 'text-danger';
									@endphp
									
							 <tr>
              <!--begin::Name-->
              <td><div class="d-flex align-items-center">
                  <!--begin::Avatar-->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                    <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::Name details-->
                  <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 15)) }}</span> <span class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 15)) }}</span> </div>
                  <!--end::Name details-->
                </div></td>
              <!--end::Name-->
              <!--begin::Role-->
              <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->email }}</div></td>
              <!--begin::Role-->
             <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"></span> {{ @$teamVal->company_name }}</div></td>
              <!--begin::Action-->
              <td><div class="d-flex flex-column"> <a id="to_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="to_select btn btn-sm btn-light-primary w-130" 
											 rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
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






{{-- <script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script> --}}

<script>

$('#ck-editor__editable_inline').html("testing");



        


$('#template').change(function(){

	
		var template	=	$('#template').val();
		if(template > 0)
		{
						var id        	=	$('#template').val();
						var url       	=	"{{ route('service_company.cvtracker.email_template',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";
						var to_email = $('#to_email').val()
						var mailtype = 1;
						if(to_email==''){
						    to_email = 'info@jero.co.in';
						}
						
						var selectedApplicantListing	=	[];
						var jqueryarray 				=	<?php echo json_encode($applicationData); ?>;
						jqueryarray.map((mval, mid, marr)=>{
							selectedApplicantListing.push( mval.id);	
						});
						var WijobIdArr = [];
						$('.chk_list_item:checked').each(function() {
							var WiidjobId = $(this).attr('data-wiseid');
							WijobIdArr.push(WiidjobId);
						});
	   
						$.ajax({
										type		: 'POST',
										url			: url,
										data		: {'_token': token, '_method': 'POST', 'template' : template, 'selectedApplicantListing': selectedApplicantListing,'to_email':to_email,'mailtype':mailtype,'WijobIdArr':WijobIdArr},
										beforeSend	: function() {
																	//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
										},
										success 	: function (response) {
																				
																				
																				if (response.status == "success") {
																							
																							
																							CKEDITOR.instances.editor1.setData(response.template_description); 
																							setTimeout(() => {
																						$('#editor1').val(response.template_description);
																						$('#subject').val(response.subject);
																							}, 500);
																							
																				}
											}
               				}); 
							
		}
		else
		{
			$('#editor').html('Choose email template');	
		} 
		
		 

});


// Attachment Format interaction handlers
$(document).on('change', '#fwd_resume_format', function () {
    var sel = $(this).find('option:selected');
    var label = sel.attr('data-label') || sel.text();
    var desc = sel.attr('data-desc') || '';
    var optVal = $(this).val();

    var candName = "{{ !empty($applicationData[0]->full_name) ? str_replace(' ', '_', $applicationData[0]->full_name) : 'Candidate' }}";
    var filename = 'JERO_' + candName + '_' + (optVal === '1' ? 'Resume.pdf' : (optVal === '5' ? 'Report.pdf' : 'Branded_Resume.pdf'));

    $('#fwd_preview_filename').text(filename);
    $('#fwd_preview_desc').text(desc);
});

$(document).on('change', '#fwd_attach_cv_toggle', function () {
    if ($(this).is(':checked')) {
        $('#fwd_format_container').slideDown(150);
    } else {
        $('#fwd_format_container').slideUp(150);
    }
});

document.getElementById("btnSubmit").addEventListener("click", async function () {

    var isAttachChecked = $('#fwd_attach_cv_toggle').is(':checked');
    var attached_cv = isAttachChecked ? 1 : 0;
    var optId = parseInt($('#fwd_resume_format').val(), 10) || 1;

    $('.indicator-label').hide();
    $('.indicator-progress').html('Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>').show();
    document.getElementById('btnSubmit').disabled = true;

    var id      = 1;
    var url     = "{{ route('service_company.cvtracker.fwd_resume_send',':id') }}";
    url         = url.replace(':id', id);
    var token   = "{{ csrf_token() }}";

    var arr = [];
    $('.chk_list_item:checked').each(function () { arr.push($(this).val()); });

    var template    = $('#template').val();
    var from_email  = $('#from_email').val();
    var to_email    = $('#to_email').val();
    var cc_email    = $('#cc_email_panel').val();
    var subject     = $('#subject').val();
    var editor1     = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.editor1) ? CKEDITOR.instances.editor1.getData() : ($('#editor1').val() || '');

    var formData = new FormData();
    formData.append('_token', token);
    formData.append('_method', 'POST');
    formData.append('template', template);
    formData.append('from_email', from_email);
    formData.append('to_email', to_email);
    formData.append('cc_email', cc_email);
    formData.append('subject', subject);
    formData.append('attached_cv', attached_cv);
    formData.append('attached_cv_format', optId);
    formData.append('editor1', editor1);
    for (var ai = 0; ai < arr.length; ai++) {
        formData.append('arr[]', arr[ai]);
    }

    // If custom format is chosen (optId !== 1) and attached_cv is enabled:
    if (attached_cv === 1 && optId !== 1 && window.AgyForwardResumeHelper) {
        try {
            var candidatesList = @json($applicationData ?? []);
            var companyData = {
                companyName: @json($companyName ?? 'Hiring Partner'),
                companyLogo: @json($companyLogo ?? '')
            };
            var jobSkills = @json($jobSkills ?? []);

            for (var c = 0; c < candidatesList.length; c++) {
                var cand = candidatesList[c];
                $('.indicator-progress').html('Preparing ' + (cand.full_name || 'Resume') + ' (' + (c + 1) + '/' + candidatesList.length + ')... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>');

                var pdfBlob = await window.AgyForwardResumeHelper.generateAttachmentForCandidate(cand, optId, companyData, jobSkills, function (statusMsg) {
                    $('.indicator-progress').html(statusMsg + ' <span class="spinner-border spinner-border-sm align-middle ms-2"></span>');
                });

                if (pdfBlob) {
                    var safeCandName = (cand.full_name || 'Candidate').replace(/[^\w\s.-]/g, '').replace(/\s+/g, '_');
                    var outName = 'JERO_' + safeCandName + '_' + cand.id + '.pdf';
                    formData.append('custom_attachments[' + cand.id + ']', pdfBlob, outName);
                }
            }
        } catch (genErr) {
            // HARD ABORT: Never submit without completed custom attachments.
            // Server enforces 422 rejection anyway — show clear error to recruiter.
            console.error('WASM resume compilation failed:', genErr);
            $('.indicator-progress').hide();
            $('.indicator-label').show();
            document.getElementById('btnSubmit').disabled = false;
            Swal.fire(
                'Resume Generation Error',
                'Could not prepare custom format: ' + (genErr.message || 'WASM processing error') + '. Email not sent.',
                'error'
            );
            return; // STOP — do NOT proceed to $.ajax
        }
    }

    $('.indicator-progress').html('Sending Email... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>');

    $.ajax({
        type : 'POST',
        url  : url,
        data : formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('.indicator-progress').hide();
            $('.indicator-label').show();
            document.getElementById('btnSubmit').disabled = false;

            if (response.status === 'coinCheck') {
                Swal.fire('Failed', response.message, 'warning').then(function () {
                    $(".btn-active-color-primary").click();
                });
                return;
            }
            if (response.status === 'success') {
                Swal.fire('Success!', 'Selected Applicant Forwarded.', 'success').then(function () {
                    location.reload();
                });
            }
        },
        error: function (xhr) {
            $('.indicator-progress').hide();
            $('.indicator-label').show();
            document.getElementById('btnSubmit').disabled = false;
            var msg = (xhr.status === 0 || !navigator.onLine)
                ? 'You are offline. Please check your connection and try again.'
                : 'Something went wrong (HTTP ' + xhr.status + '). Please try again.';
            Swal.fire('Error', msg, 'error');
        }
    });

});


 $('.cp_select').on('click', function(e) {
   			
			
			var rel						= 	$(this).attr('rel');
			var assign					=	$('#team_select_'+rel).attr("data-assign");
			var title					=	$('#team_select_'+rel).attr("data-title");
			var email					=	$('#team_select_'+rel).attr("data-email");
			
			if(assign ==0)
			{
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				AddEvent(title, email);
			}
			else if(assign ==1)
			{
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);	
				DeleteEvent(title, email);
			}
			
	});
	
	
	$('.to_select').on('click', function(e) {
   			
			
			var rel						= 	$(this).attr('rel');
			var assign					=	$('#to_team_select_'+rel).attr("data-assign");
			var title					=	$('#to_team_select_'+rel).attr("data-title");
			var email					=	$('#to_team_select_'+rel).attr("data-email");
			
			if(assign ==0)
			{
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				AddEventTo(title, email);
			}
			else if(assign ==1)
			{
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Add');
				$(this).attr("data-assign", 0);	
				DeleteEventTo(title, email);
			}
			
	});
	
	

function AddEvent(name, email)
{
		
		var cc_email				=	$('#cc_email_panel').val();
		if(cc_email !='')
		{
			var includeStr			=	cc_email.includes(email);
			if(includeStr  === false)
			{
				var assignNewEmail	=	cc_email +' , '+email;	
				$('#cc_email_panel').val(assignNewEmail);
			}							
		}
		else   
		{
			$('#cc_email_panel').val(email);
		}
}
	 
function DeleteEvent(contact_person_name, email)
{	 

	var team_hiring_manager_email		=	''; 
	var tempEmail						=	[]; 
	var cc_email						=	$('#cc_email_panel').val();
	
	if(cc_email !='')
	{
		
		var emailArr 	= cc_email.split(',');
		for(var inc=0; inc<emailArr.length; inc++)
		{
			if(email.trim() != emailArr[inc].trim()){
				tempEmail.push(emailArr[inc]);
			
			}			
		}
		team_hiring_manager_email	=	tempEmail.toString();
		
				 
	}	
	
	$('#cc_email_panel').val(team_hiring_manager_email);			
}


function AddEventTo(name, email)
{
		
		var to_email				=	$('#to_email').val();
		if(to_email !='')
		{
			var includeStr			=	to_email.includes(email);
			if(includeStr  === false)
			{
				var assignNewEmail	=	to_email +' , '+email;	
				$('#to_email').val(assignNewEmail);
				
			}							
		}
		else   
		{
			$('#to_email').val(email);
				
		}
     

}
	 
function DeleteEventTo(contact_person_name, email)
{	 

	var team_hiring_manager_email		=	''; 
	var tempEmail						=	[]; 
	var to_email						=	$('#to_email').val();
	
	if(to_email !='')
	{
		
		var emailArr 	= to_email.split(',');
		for(var inc=0; inc<emailArr.length; inc++)
		{
			if(email.trim() != emailArr[inc].trim()){
				tempEmail.push(emailArr[inc]);
			
			}			
		}
		team_hiring_manager_email	=	tempEmail.toString();
		
				 
	}	
	
	$('#to_email').val(team_hiring_manager_email);
	
}



</script> 
<script>
	
	// from_email is a <select> — no Tagify needed
	// cc_email panel is a plain input (no Tagify)
		 
</script>

