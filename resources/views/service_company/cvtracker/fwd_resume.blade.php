@php 
		use App\Helper\GeneralMethod;
		$salaryMode 					=	GeneralMethod::salaryMode();
		$currencySymbole 				=	GeneralMethod::currencySymbole();
			
@endphp 
{{--
    NOTE: AgyForwardResumeEngine.js and AgyForwardResumeHelper.js are intentionally
    NOT included here. This blade is injected into the DOM via jQuery's $.html() which
    silently strips all <script src="..."> external script tags. Those engines are
    pre-loaded on the host page (cvtracker/index.blade.php footer-script block) so that
    window.AgyForwardResumeEngine and window.AgyForwardResumeHelper are already defined
    when the inline IIFE below executes.

    CKEditor is loaded dynamically here because $.html() DOES execute inline <script> blocks,
    and CKEditor is needed only within this drawer context.
--}}
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
		try {
			if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['editor1']) {
				try {
					CKEDITOR.instances['editor1'].destroy(true);
				} catch(destroyErr) {
					console.warn('CKEditor cleanup error:', destroyErr);
				}
			}
		} catch(e) {}

		var editorElement = CKEDITOR.document.getById( 'editor1' );
		if ( !editorElement ) return;
						
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
.mm-wrap { font-family: 'Poppins', sans-serif; font-size: 13px; color: #374151; display: flex; flex-direction: column; height: 100%; min-height: 100%; }
#form1 { display: flex; flex-direction: column; height: 100%; flex: 1; min-height: 0; }
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
  border: 1px solid #E0E7FF;
}
.mm-header-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 2px;
  min-width: 0;
}
.mm-header-title {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #1E1B4B;
  letter-spacing: -0.2px;
  line-height: 1.25;
}
.mm-header-subtitle {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 400;
  color: #6B7280;
  line-height: 1.3;
}
.mm-header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}
.mm-header-btn-cancel {
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
.mm-header-btn-cancel:hover {
  background: #F9FAFB;
  border-color: #D1D5DB;
  color: #111827;
}
.mm-header-btn-send {
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
.mm-header-btn-send:hover {
  background: #1550C9;
}
.mm-header-btn-send:disabled {
  background: #93C5FD !important;
  cursor: not-allowed !important;
}

/* Hide host drawer/modal duplicate headers so this header is the exclusive header */
#kt_fwd_resume_drawer > .card > .card-header,
#kt_fwd_resume_drawer_aptk > .card > .card-header,
#fwd_resume .modal-header {
  display: none !important;
}

/* ══════════════════════════════════════════════════════════════════════════════
   ENTERPRISE ADD EMAIL RECIPIENT DRAWER STYLES
══════════════════════════════════════════════════════════════════════════════ */
#kt_recipient_drawer {
  width: 490px;
  max-width: 100vw;
  height: 100vh;
  background: #FFFFFF !important;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  z-index: 10055 !important;
  box-shadow: -8px 0 32px rgba(0, 0, 0, 0.16);
  position: fixed !important;
  top: 0 !important;
  right: 0 !important;
}
@media (max-width: 576px) {
  #kt_recipient_drawer { width: 100vw !important; }
}

.rcp-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 100%;
  background: #FFFFFF !important;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.rcp-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 20px 24px 14px 24px;
  flex-shrink: 0;
}
.rcp-header-title {
  margin: 0;
  font-size: 17.5px;
  font-weight: 700;
  color: #111827;
  letter-spacing: -0.2px;
  line-height: 1.25;
}
.rcp-header-subtitle {
  margin: 4px 0 0 0;
  font-size: 12.5px;
  color: #6B7280;
  font-weight: 400;
  line-height: 1.35;
}
.rcp-close-btn {
  background: none;
  border: none;
  padding: 6px;
  margin: -4px -6px 0 0;
  border-radius: 6px;
  color: #9CA3AF;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}
.rcp-close-btn:hover {
  background: #F3F4F6;
  color: #111827;
}

/* Segmented Tabs */
.rcp-tabs-wrap {
  display: flex;
  gap: 12px;
  padding: 0 24px 14px 24px;
  flex-shrink: 0;
}
.rcp-tab-card {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 9px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  cursor: pointer;
  transition: all 0.15s ease;
}
.rcp-tab-card:hover {
  border-color: #CBD5E1;
  background: #F8FAFC;
}
.rcp-tab-card.active {
  border: 1.5px solid #2563EB;
  background: #FFFFFF;
  box-shadow: 0 1px 3px rgba(37, 99, 235, 0.08);
}
.rcp-tab-icon {
  width: 34px;
  height: 34px;
  min-width: 34px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F3F4F6;
  color: #6B7280;
  transition: all 0.15s ease;
}
.rcp-tab-card.active .rcp-tab-icon {
  background: #EFF6FF;
  color: #2563EB;
}
.rcp-tab-title {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  line-height: 1.25;
}
.rcp-tab-sub {
  font-size: 11px;
  color: #6B7280;
  margin-top: 1px;
}

/* Search */
.rcp-search-wrap {
  padding: 0 24px 12px 24px;
  flex-shrink: 0;
}
.rcp-search-box {
  position: relative;
  display: flex;
  align-items: center;
}
.rcp-search-icon {
  position: absolute;
  left: 12px;
  pointer-events: none;
}
.rcp-search-input {
  width: 100%;
  padding: 9px 32px 9px 36px;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  font-size: 12.5px;
  font-family: 'Poppins', sans-serif;
  color: #1F2937;
  background: #FFFFFF;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.rcp-search-input:focus {
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.rcp-search-clear {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  font-size: 16px;
  color: #9CA3AF;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}
.rcp-ext-email-row {
  margin-top: 8px;
  display: flex;
  align-items: center;
}
.rcp-add-ext-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #2563EB;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.15s ease;
}
.rcp-add-ext-link:hover {
  color: #1D4ED8;
}
.rcp-ext-inline-box {
  margin-top: 8px;
  display: flex;
  gap: 6px;
}
.rcp-ext-input {
  flex: 1;
  padding: 7px 11px;
  border: 1px solid #D1D5DB;
  border-radius: 6px;
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  outline: none;
}
.rcp-ext-input:focus {
  border-color: #2563EB;
}
.rcp-ext-add-btn {
  padding: 7px 14px;
  background: #2563EB;
  color: #FFFFFF;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}
.rcp-ext-add-btn:hover {
  background: #1D4ED8;
}

/* Subhead */
.rcp-subhead-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 4px 24px 8px 24px;
  flex-shrink: 0;
}
.rcp-subhead-title {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
}
.rcp-sort-box {
  display: flex;
  align-items: center;
  gap: 4px;
}
.rcp-sort-label {
  font-size: 11.5px;
  color: #6B7280;
}
.rcp-sort-select {
  border: none;
  background: transparent;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  outline: none;
  font-family: 'Poppins', sans-serif;
  padding-right: 2px;
}

/* Contact List */
.rcp-list-wrap {
  flex: 1;
  overflow-y: auto;
  padding: 0 24px;
}
.rcp-contact-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #F3F4F6;
  gap: 12px;
  transition: background 0.1s ease;
}
.rcp-item-left {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}
.rcp-avatar-img {
  width: 38px;
  height: 38px;
  min-width: 38px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}
.rcp-avatar-initial {
  width: 38px;
  height: 38px;
  min-width: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
  text-transform: uppercase;
}
.rcp-info-col {
  display: flex;
  flex-direction: column;
  min-width: 0;
  gap: 2px;
  flex: 1;
}
.rcp-name-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.rcp-contact-name {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 170px;
}
.rcp-role-pill {
  background: #EFF6FF;
  color: #2563EB;
  border: 1px solid #DBEAFE;
  border-radius: 12px;
  padding: 1px 7px;
  font-size: 10.5px;
  font-weight: 500;
  white-space: nowrap;
}
.rcp-dept-line {
  font-size: 11.5px;
  color: #6B7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.rcp-email-line {
  font-size: 11.5px;
  color: #4B5563;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.rcp-item-right {
  flex-shrink: 0;
}
.rcp-toggle-btn {
  padding: 5px 16px;
  border-radius: 6px;
  border: 1px solid #D1D5DB;
  background: #FFFFFF;
  color: #2563EB;
  font-size: 12px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: all 0.15s ease;
}
.rcp-toggle-btn:hover {
  background: #EFF6FF;
  border-color: #2563EB;
}
.rcp-toggle-btn.is-added {
  background: #DCFCE7;
  border-color: #86EFAC;
  color: #16A34A;
  padding: 5px 12px;
}
.rcp-toggle-btn.is-added:hover {
  background: #FEE2E2;
  border-color: #FCA5A5;
  color: #DC2626;
}
.rcp-empty-state {
  text-align: center;
  padding: 36px 16px;
  color: #9CA3AF;
  font-size: 12.5px;
}

/* Footer */
.rcp-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 24px;
  border-top: 1px solid #E5E7EB;
  background: #FFFFFF;
  flex-shrink: 0;
  gap: 12px;
}
.rcp-footer-left {
  font-size: 12.5px;
  font-weight: 600;
  color: #111827;
}
.rcp-footer-right {
  display: flex;
  align-items: center;
  gap: 8px;
}
.rcp-btn-cancel {
  padding: 7px 16px;
  border-radius: 7px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  color: #374151;
  font-size: 12.5px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: all 0.15s ease;
}
.rcp-btn-cancel:hover {
  background: #F9FAFB;
  border-color: #D1D5DB;
}
.rcp-btn-done {
  padding: 7px 18px;
  border-radius: 7px;
  border: none;
  background: #2563EB;
  color: #FFFFFF;
  font-size: 12.5px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: background 0.15s ease;
}
.rcp-btn-done:hover {
  background: #1D4ED8;
}
</style>
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>

<div class="mm-wrap">
<form id="form1">
  {{-- Replicated Header --}}
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
      <button type="button" class="mm-header-btn-cancel" onclick="closeFwdResumeDrawer()">Cancel</button>
      <button type="button" class="mm-header-btn-send" name="btnSubmit" id="btnSubmit" value="btnSubmit">
        <span class="indicator-label" style="display:inline-flex;align-items:center;gap:7px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Email
        </span>
        <span class="indicator-progress" style="display:none;align-items:center;gap:7px;">
          <span class="spinner-border spinner-border-sm" style="width:13px;height:13px;border-width:2px;display:inline-block;"></span>
          <span id="fwd_btn_progress_text">Sending...</span>
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
              <option value="1" selected data-label="Original Candidate Resume" data-desc="Download the candidate's uploaded resume exactly as received, with no edits or formatting changes.">1. Original Resume (Default)</option>
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

          <!-- Live Attachment Format Summary & Deliverability Badge -->
          <div id="fwd_attachment_preview_card" style="margin-top:8px;padding:9px 12px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div style="display:flex;align-items:center;gap:8px;min-width:0;">
              <span style="width:26px;height:26px;border-radius:6px;background:#DCFCE7;color:#15803D;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:12px;font-weight:700;">PDF</span>
              <div style="min-width:0;">
                <div id="fwd_preview_format_title" style="font-size:12.5px;font-weight:600;color:#166534;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  1. Original Candidate Resume
                </div>
                <div id="fwd_preview_desc" style="font-size:11px;color:#15803D;margin-top:1px;">
                  Pristine resume file as originally uploaded by candidate/agency.
                </div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
              <span id="fwd_batch_count_badge" style="font-size:11px;font-weight:600;color:{{ count($applicationData ?? []) > 5 ? '#B91C1C' : '#15803D' }};background:{{ count($applicationData ?? []) > 5 ? '#FEE2E2' : '#DCFCE7' }};padding:3px 9px;border-radius:12px;white-space:nowrap;">
                📎 {{ count($applicationData ?? []) }} / 5 Max Resumes
              </span>
            </div>
          </div>

          <!-- Multi-Profile Attached Resumes Roster -->
          <div id="fwd_candidate_roster" style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">
            @foreach($applicationData as $idx => $cand)
              @php
                $candSafeName = !empty($cand->full_name) ? str_replace(' ', '_', preg_replace('/[^\w\s.-]/', '', $cand->full_name)) : ('Candidate_' . $cand->id);
                $candDisplayName = $cand->full_name ?? ('Candidate #' . ($idx + 1));
                $candDesignation = $cand->current_designation ?? ($cand->job_title ?? 'Profile');
              @endphp
              <div class="fwd-cand-card" id="fwd_cand_card_{{ $cand->id }}" style="padding:10px 14px;background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;transition:all 0.2s ease;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                  <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1;">
                    {{-- Avatar / Sequence badge --}}
                    <div style="position:relative;flex-shrink:0;">
                      <span id="fwd_cand_avatar_{{ $cand->id }}" style="width:30px;height:30px;border-radius:7px;background:#DCFCE7;color:#15803D;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                        {{ $idx + 1 }}
                      </span>
                      <span id="fwd_cand_check_{{ $cand->id }}" style="position:absolute;bottom:-3px;right:-3px;width:14px;height:14px;border-radius:50%;background:#10B981;color:#fff;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:bold;box-shadow:0 1px 2px rgba(0,0,0,0.15);">✓</span>
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
                      <div class="fwd-cand-filename" id="fwd_cand_filename_{{ $cand->id }}" data-safename="{{ $candSafeName }}" style="font-size:11px;font-family:monospace;color:#059669;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        Jeroworks_{{ $candSafeName }}.pdf
                      </div>
                    </div>
                  </div>

                  {{-- Candidate Status Pill --}}
                  <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    <div id="fwd_cand_status_{{ $cand->id }}" style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:12px;background:#DCFCE7;color:#15803D;display:inline-flex;align-items:center;gap:5px;white-space:nowrap;">
                      ✓ Attached (100%)
                    </div>
                  </div>
                </div>

                {{-- Candidate Progress Bar (Active Rendering) --}}
                <div id="fwd_cand_progress_wrap_{{ $cand->id }}" style="display:none;margin-top:8px;">
                  <div style="height:4px;width:100%;background:#E5E7EB;border-radius:2px;overflow:hidden;">
                    <div id="fwd_cand_progress_bar_{{ $cand->id }}" style="height:100%;width:0%;background:linear-gradient(90deg, #0D9488, #059669);border-radius:2px;transition:width 0.2s ease;"></div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Master Resume Compilation Progress Bar -->
          <div id="fwd_compile_progress_container" style="display:none;margin-top:10px;padding:11px 14px;background:#F0FDFA;border:1px solid #99F6E4;border-radius:8px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
              <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                <span id="fwd_compile_spinner" class="spinner-border spinner-border-sm" style="width:14px;height:14px;border-width:2px;color:#0D9488!important;display:inline-block;"></span>
                <span id="fwd_compile_status_text" style="font-size:12px;font-weight:600;color:#0F766E;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  Preparing custom resume attachments...
                </span>
              </div>
              <span id="fwd_compile_pct_badge" style="font-size:12px;font-weight:700;color:#0D9488;background:#CCFBF1;padding:2px 8px;border-radius:10px;border:1px solid #99F6E4;flex-shrink:0;">
                0%
              </span>
            </div>
            <div style="height:7px;width:100%;background:#CCFBF1;border-radius:4px;overflow:hidden;">
              <div id="fwd_compile_progress_bar" style="height:100%;width:0%;background:linear-gradient(90deg, #0D9488, #059669);border-radius:4px;transition:width 0.25s ease;"></div>
            </div>
            <div id="fwd_compile_hint" style="font-size:11px;color:#115E59;margin-top:5px;display:flex;align-items:center;justify-content:space-between;">
              <span id="fwd_compile_step_detail">Client-side document engine compiling high-resolution vector PDF...</span>
              <span id="fwd_compile_count">0 / {{ count($applicationData ?? []) }}</span>
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
</form>
</div>




@php
$getInitials = function($name) {
    $parts = preg_split('/\s+/', trim((string) ($name ?? '')));
    if (count($parts) >= 2 && !empty($parts[0]) && !empty($parts[1])) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }
    return strtoupper(substr((string) ($name ?? 'U'), 0, 1));
};

$getAvatarBg = function($name) {
    $colors = [
        ['bg' => '#1E293B', 'text' => '#FFFFFF'],
        ['bg' => '#DCFCE7', 'text' => '#15803D'],
        ['bg' => '#F3E8FF', 'text' => '#7E22CE'],
        ['bg' => '#FCE7F3', 'text' => '#BE185D'],
        ['bg' => '#EFF6FF', 'text' => '#1D4ED8'],
        ['bg' => '#FEF3C7', 'text' => '#B45309'],
        ['bg' => '#EDE9FE', 'text' => '#6D28D9'],
        ['bg' => '#E0F2FE', 'text' => '#0369A1'],
    ];
    $hash = crc32(strtolower(trim((string) ($name ?? ''))));
    return $colors[abs($hash) % count($colors)];
};
@endphp

{{-- Fallback triggers for legacy layout handlers --}}
<div id="kt_activitiesIntTeam_toggle_cc" style="display:none;"></div>
<div id="kt_activitiesIntTeam_toggle_to" style="display:none;"></div>
<div id="kt_activitiesIntTeam_cc" style="display:none;"></div>
<div id="kt_activitiesIntTeam_to" style="display:none;"></div>

{{-- ══════════════════════════════════════════════════════════════════════════════
     ENTERPRISE ADD EMAIL RECIPIENT DRAWER (REPLICATED DESIGN)
══════════════════════════════════════════════════════════════════════════════ --}}
<div id="kt_recipient_drawer_toggle" style="display:none;"></div>
<div id="kt_recipient_drawer" class="bg-white shadow" data-kt-drawer="true" data-kt-drawer-name="recipient-drawer" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'100%', 'sm': '480px', 'lg': '490px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_recipient_drawer_toggle" data-kt-drawer-close="#kt_recipient_drawer_close">
  <div class="rcp-container">
    {{-- Drawer Header --}}
    <div class="rcp-header">
      <div class="rcp-header-left">
        <h3 class="rcp-header-title">Add Email Recipient</h3>
        <p class="rcp-header-subtitle">Choose a contact from your internal team or client company.</p>
      </div>
      <button type="button" class="rcp-close-btn" id="kt_recipient_drawer_close" title="Close drawer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    {{-- Tabs / Segmented Cards --}}
    <div class="rcp-tabs-wrap">
      <div class="rcp-tab-card active" id="rcp_tab_internal" data-tab="internal">
        <div class="rcp-tab-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
        <div class="rcp-tab-info">
          <div class="rcp-tab-title">Internal Team</div>
          <div class="rcp-tab-sub">{{ count($internal_team_data ?? []) }} available</div>
        </div>
      </div>

      <div class="rcp-tab-card" id="rcp_tab_client" data-tab="client">
        <div class="rcp-tab-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
            <path d="M9 22v-4h6v4"></path>
            <path d="M8 6h.01"></path>
            <path d="M16 6h.01"></path>
            <path d="M8 10h.01"></path>
            <path d="M16 10h.01"></path>
            <path d="M8 14h.01"></path>
            <path d="M16 14h.01"></path>
          </svg>
        </div>
        <div class="rcp-tab-info">
          <div class="rcp-tab-title">Client Contacts</div>
          <div class="rcp-tab-sub">{{ count($client_contacts_data ?? []) }} available</div>
        </div>
      </div>
    </div>

    {{-- Search & External Email --}}
    <div class="rcp-search-wrap">
      <div class="rcp-search-box">
        <svg class="rcp-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="rcp_search_input" class="rcp-search-input" placeholder="Search by name, email, role or department..." autocomplete="off">
        <button type="button" id="rcp_search_clear" class="rcp-search-clear" style="display:none;">&times;</button>
      </div>

      <div class="rcp-ext-email-row">
        <a href="javascript:;" id="rcp_add_external_email_btn" class="rcp-add-ext-link">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
          </svg>
          <span>Add external email</span>
        </a>
      </div>

      <div id="rcp_ext_email_inline_box" class="rcp-ext-inline-box" style="display:none;">
        <input type="email" id="rcp_ext_email_input" placeholder="e.g. contact@clientcorp.com" class="rcp-ext-input">
        <button type="button" id="rcp_ext_email_add_btn" class="rcp-ext-add-btn">Add</button>
      </div>
    </div>

    {{-- Section Subheading with Count & Sort --}}
    <div class="rcp-subhead-row">
      <div id="rcp_list_heading" class="rcp-subhead-title">Team Members ({{ count($internal_team_data ?? []) }})</div>
      <div class="rcp-sort-box">
        <span class="rcp-sort-label">Sort by:</span>
        <select id="rcp_sort_select" class="rcp-sort-select">
          <option value="name_asc">Name</option>
          <option value="name_desc">Name (Z-A)</option>
          <option value="role">Role</option>
        </select>
      </div>
    </div>

    {{-- Contact List Wrap --}}
    <div class="rcp-list-wrap" id="rcp_list_wrap">
      {{-- Internal Team Items --}}
      <div class="rcp-list-section" id="rcp_section_internal">
        @if(!empty($internal_team_data) && count($internal_team_data) > 0)
          @foreach($internal_team_data as $iUser)
            @php
              $cInitials = $getInitials($iUser->name);
              $cCol = $getAvatarBg($iUser->name);
              $cRole = !empty($iUser->display_name) ? $iUser->display_name : (!empty($iUser->role_name) ? $iUser->role_name : '');
              $cDept = !empty($iUser->role_name) ? $iUser->role_name : 'Leadership';
              $cComp = !empty($iUser->company_name) ? $iUser->company_name : ($companyName ?? 'Internal Team');
            @endphp
            <div class="rcp-contact-item" data-name="{{ strtolower($iUser->name ?? '') }}" data-email="{{ strtolower($iUser->email ?? '') }}" data-role="{{ strtolower($cRole) }}" data-dept="{{ strtolower($cDept . ' ' . $cComp) }}">
              <div class="rcp-item-left">
                @if(!empty($iUser->image) && file_exists(public_path('user-uploads/avatar/'.$iUser->image)))
                  <img src="{{ asset('user-uploads/avatar/'.$iUser->image) }}" class="rcp-avatar-img" alt="{{ $iUser->name }}">
                @else
                  <div class="rcp-avatar-initial" style="background: {{ $cCol['bg'] }}; color: {{ $cCol['text'] }};">
                    {{ $cInitials }}
                  </div>
                @endif
                <div class="rcp-info-col">
                  <div class="rcp-name-row">
                    <span class="rcp-contact-name" title="{{ $iUser->name }}">{{ $iUser->name }}</span>
                    @if(!empty($cRole))
                      <span class="rcp-role-pill">{{ $cRole }}</span>
                    @endif
                  </div>
                  <div class="rcp-dept-line">{{ $cDept }} &bull; {{ $cComp }}</div>
                  <div class="rcp-email-line">{{ $iUser->email }}</div>
                </div>
              </div>
              <div class="rcp-item-right">
                <button type="button" class="rcp-toggle-btn" data-email="{{ $iUser->email }}" data-name="{{ $iUser->name }}">
                  <span class="rcp-btn-label-add">Add</span>
                  <span class="rcp-btn-label-added" style="display:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Added
                  </span>
                </button>
              </div>
            </div>
          @endforeach
        @else
          <div class="rcp-empty-state">No internal team members found.</div>
        @endif
      </div>

      {{-- Client Contacts Items --}}
      <div class="rcp-list-section" id="rcp_section_client" style="display:none;">
        @if(!empty($client_contacts_data) && count($client_contacts_data) > 0)
          @foreach($client_contacts_data as $cUser)
            @php
              $cInitials = $getInitials($cUser->name);
              $cCol = $getAvatarBg($cUser->name);
              $cRole = !empty($cUser->display_name) ? $cUser->display_name : 'Client Contact';
              $cComp = !empty($cUser->company_name) ? $cUser->company_name : 'Client Company';
            @endphp
            <div class="rcp-contact-item" data-name="{{ strtolower($cUser->name ?? '') }}" data-email="{{ strtolower($cUser->email ?? '') }}" data-role="{{ strtolower($cRole) }}" data-dept="{{ strtolower($cComp) }}">
              <div class="rcp-item-left">
                <div class="rcp-avatar-initial" style="background: {{ $cCol['bg'] }}; color: {{ $cCol['text'] }};">
                  {{ $cInitials }}
                </div>
                <div class="rcp-info-col">
                  <div class="rcp-name-row">
                    <span class="rcp-contact-name" title="{{ $cUser->name }}">{{ $cUser->name }}</span>
                    @if(!empty($cRole))
                      <span class="rcp-role-pill">{{ $cRole }}</span>
                    @endif
                  </div>
                  <div class="rcp-dept-line">{{ $cComp }}</div>
                  <div class="rcp-email-line">{{ $cUser->email }}</div>
                </div>
              </div>
              <div class="rcp-item-right">
                <button type="button" class="rcp-toggle-btn" data-email="{{ $cUser->email }}" data-name="{{ $cUser->name }}">
                  <span class="rcp-btn-label-add">Add</span>
                  <span class="rcp-btn-label-added" style="display:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Added
                  </span>
                </button>
              </div>
            </div>
          @endforeach
        @else
          <div class="rcp-empty-state">No client contacts found.</div>
        @endif
      </div>

      {{-- No Search Results State --}}
      <div id="rcp_no_results" class="rcp-empty-state" style="display:none;">
        No contacts match your search.
      </div>
    </div>

    {{-- Sticky Bottom Footer --}}
    <div class="rcp-footer">
      <div class="rcp-footer-left">
        <span id="rcp_footer_count_text">0 recipients selected</span>
      </div>
      <div class="rcp-footer-right">
        <button type="button" class="rcp-btn-cancel" id="rcp_footer_cancel_btn">Cancel</button>
        <button type="button" class="rcp-btn-done" id="rcp_footer_done_btn">Done</button>
      </div>
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
</script>

{{-- ══════════════════════════════════════════════════════════════════════════════
     FORWARD RESUME ENGINE — INLINED PARTIAL (ZERO 404 GUARANTEE)
══════════════════════════════════════════════════════════════════════════════ --}}
@include('service_company.cvtracker._forward_resume_engine')

<script>
// ══════════════════════════════════════════════════════════════════════════════
// PERCENTAGE-BASED RESUME COMPILATION & SUBMIT STATE CONTROLLER
// ══════════════════════════════════════════════════════════════════════════════
window._fwdCompiledAttachments = window._fwdCompiledAttachments || {};
window._fwdIsCompiling = false;
window._fwdGlobalToken = (window._fwdGlobalToken || 0) + 1;
var _fwdCompiledAttachments = window._fwdCompiledAttachments;
var _fwdIsCompiling = window._fwdIsCompiling;
var _fwdGlobalToken = window._fwdGlobalToken;

window.closeFwdResumeDrawer = function() {
    if (typeof window.closeEmailRecipientDrawer === 'function') {
        try { window.closeEmailRecipientDrawer(); } catch(e) {}
    }
    if (typeof $ !== 'undefined') {
        $('body > #kt_recipient_drawer').remove();
        $('.drawer-overlay').filter(function() {
            var z = parseInt($(this).css('z-index'), 10);
            return z >= 1000;
        }).remove();
    }
    if (typeof window._fwdGlobalToken !== 'undefined') {
        window._fwdGlobalToken++;
        window._fwdIsCompiling = false;
    }
    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['editor1']) {
        try { CKEDITOR.instances['editor1'].destroy(true); } catch(e) {}
    }
    var d = document.getElementById('kt_fwd_resume_drawer') || document.getElementById('kt_fwd_resume_drawer_aptk');
    if (d && typeof KTDrawer !== 'undefined') {
        var dr = KTDrawer.getInstance(d);
        if (dr) dr.hide();
    }
    if (typeof $ !== 'undefined') {
        if ($('#fwd_resume').length) {
            $('#fwd_resume').modal('hide');
        }
        $('#kt_fwd_resume_drawer_close, #kt_fwd_resume_drawer_close_aptk').trigger('click');
    }
};

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    var k = 1024;
    var sizes = ['B', 'KB', 'MB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function updateRosterFilenames(optId) {
    $('.fwd-cand-filename').each(function () {
        var safeName = $(this).attr('data-safename') || 'Candidate';
        var filename = 'Jeroworks_' + safeName + '.pdf';
        if (optId === 5) {
            filename = 'Prescreen_Report_' + safeName + '.pdf';
        } else if (optId === 49) {
            filename = 'Jeroworks_Resume.pdf';
        }
        $(this).text(filename);
    });
}

function updateSendButtonState() {
    var isAttach = $('#fwd_attach_cv_toggle').is(':checked');
    var optId = parseInt($('#fwd_resume_format').val(), 10) || 1;
    var btn = document.getElementById('btnSubmit');
    if (!btn) return;

    updateRosterFilenames(optId);

    var candidatesList = @json($applicationData ?? []);
    var selectedIds = [];
    $('.chk_list_item:checked').each(function () { selectedIds.push(String($(this).val())); });
    var candidatesToProcess = candidatesList.filter(function (cand) {
        return selectedIds.length === 0 || selectedIds.indexOf(String(cand.id)) !== -1;
    });
    if (candidatesToProcess.length === 0) {
        candidatesToProcess = candidatesList;
    }

    // Option 1 (Original Resume) or unchecking Attach Resume: Send is immediately active via R2
    if (!isAttach) {
        _fwdGlobalToken++; // abort active compile
        _fwdIsCompiling = false;
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btn.title = '';
        $('#fwd_compile_progress_container').hide();
        candidatesToProcess.forEach(function (c) {
            $('#fwd_cand_card_' + c.id).css({ 'background': '#F9FAFB', 'border-color': '#E5E7EB' });
            $('#fwd_cand_check_' + c.id).hide();
            $('#fwd_cand_status_' + c.id).css({ 'background': '#F3F4F6', 'color': '#6B7280' }).text('Not Attached');
            $('#fwd_cand_progress_wrap_' + c.id).hide();
        });
        return;
    }

    if (optId === 1) {
        _fwdGlobalToken++; // abort active compile
        _fwdIsCompiling = false;
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btn.title = '';
        $('#fwd_compile_progress_container').hide();
        candidatesToProcess.forEach(function (c) {
            $('#fwd_cand_card_' + c.id).css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
            $('#fwd_cand_check_' + c.id).css('display', 'flex');
            $('#fwd_cand_status_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text('✓ Attached (100%)');
            $('#fwd_cand_progress_wrap_' + c.id).hide();
        });
        return;
    }

    // Custom format (Option 5 or 41-53):
    var cached = _fwdCompiledAttachments[optId] || {};
    var allReady = candidatesToProcess.length > 0 && candidatesToProcess.every(function (c) {
        return !!cached[c.id];
    });

    if (allReady) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btn.title = '';
        $('#fwd_compile_progress_container').show();
        $('#fwd_compile_spinner').hide();
        $('#fwd_compile_status_text').html('✓ All ' + candidatesToProcess.length + ' candidate resumes compiled & verified (100%)');
        $('#fwd_compile_pct_badge').text('100%').css({ 'color': '#0D9488', 'background': '#CCFBF1', 'border-color': '#99F6E4' });
        $('#fwd_compile_progress_bar').css({ 'width': '100%', 'background': '#059669' });
        $('#fwd_compile_count').text(candidatesToProcess.length + ' / ' + candidatesToProcess.length);
        $('#fwd_compile_step_detail').text('All candidate attachments ready. You can send now.');

        candidatesToProcess.forEach(function (c) {
            var blob = cached[c.id];
            var sizeStr = blob ? (' • ' + formatBytes(blob.size)) : '';
            $('#fwd_cand_card_' + c.id).css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
            $('#fwd_cand_check_' + c.id).css('display', 'flex');
            $('#fwd_cand_status_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text('✓ Attached (100%)' + sizeStr);
            $('#fwd_cand_progress_wrap_' + c.id).hide();
        });
    } else {
        // Mark cached candidates as ready, and uncached as Queued
        candidatesToProcess.forEach(function (c) {
            if (cached[c.id]) {
                var sizeStr = ' • ' + formatBytes(cached[c.id].size);
                $('#fwd_cand_card_' + c.id).css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
                $('#fwd_cand_check_' + c.id).css('display', 'flex');
                $('#fwd_cand_status_' + c.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text('✓ Attached (100%)' + sizeStr);
                $('#fwd_cand_progress_wrap_' + c.id).hide();
            } else {
                $('#fwd_cand_card_' + c.id).css({ 'background': '#F9FAFB', 'border-color': '#E5E7EB' });
                $('#fwd_cand_check_' + c.id).hide();
                $('#fwd_cand_status_' + c.id).css({ 'background': '#F3F4F6', 'color': '#6B7280' }).text('Queued');
                $('#fwd_cand_progress_wrap_' + c.id).hide();
            }
        });

        // Disable Send until compilation reaches 100%
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.style.cursor = 'not-allowed';
        btn.title = 'Please wait while resume attachments are prepared (100% required)';
        $('#fwd_compile_progress_container').show();
        $('#fwd_compile_spinner').show();
        startCompilationForOption(optId);
    }
}

async function startCompilationForOption(optId) {
    if (optId === 1) return;
    _fwdIsCompiling = true;
    var thisToken = ++_fwdGlobalToken;

    var btn = document.getElementById('btnSubmit');
    if (btn) {
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.style.cursor = 'not-allowed';
        btn.title = 'Preparing resume attachments...';
    }

    $('#fwd_compile_progress_container').show();
    $('#fwd_compile_spinner').show();
    $('#fwd_compile_progress_bar').css({ 'width': '5%', 'background': 'linear-gradient(90deg, #0D9488, #059669)' });
    $('#fwd_compile_pct_badge').text('5%').css({ 'color': '#0D9488', 'background': '#CCFBF1', 'border-color': '#99F6E4' });
    $('#fwd_compile_status_text').text('Initializing document rendering engine...');
    $('#fwd_compile_step_detail').text('Loading Rust WASM core and vector fonts...');

    try {
        var engine = window.AgyForwardResumeEngine || window.AgyForwardResumeHelper;
        if (!engine || typeof engine.generateAttachmentForCandidate !== 'function') {
            throw new Error('Forward Resume engine is not initialized');
        }

        if (thisToken !== _fwdGlobalToken) return;

        $('#fwd_compile_progress_bar').css('width', '10%');
        $('#fwd_compile_pct_badge').text('10%');
        $('#fwd_compile_status_text').text('Engine ready. Preparing candidate resumes...');

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

        if (!_fwdCompiledAttachments[optId]) {
            _fwdCompiledAttachments[optId] = {};
        }

        var total = candidatesToProcess.length;
        for (var i = 0; i < total; i++) {
            if (thisToken !== _fwdGlobalToken) return;

            var cand = candidatesToProcess[i];
            var candName = cand.full_name || ('Candidate #' + (i + 1));
            var startPct = 10 + Math.round((i / total) * 90);
            var endPct = 10 + Math.round(((i + 1) / total) * 90);

            // Active candidate card rendering state
            $('#fwd_cand_card_' + cand.id).css({ 'background': '#F0FDFA', 'border-color': '#99F6E4' });
            $('#fwd_cand_progress_wrap_' + cand.id).show();
            $('#fwd_cand_progress_bar_' + cand.id).css('width', '25%');
            $('#fwd_cand_status_' + cand.id).css({ 'background': '#CCFBF1', 'color': '#0D9488' }).html(
                '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;border-width:1.5px;margin-right:4px;"></span> Rendering...'
            );

            $('#fwd_compile_progress_bar').css('width', startPct + '%');
            $('#fwd_compile_pct_badge').text(startPct + '%');
            $('#fwd_compile_status_text').text('Rendering ' + candName + ' (' + (i + 1) + '/' + total + ')...');
            $('#fwd_compile_count').text(i + ' / ' + total);

            if (!_fwdCompiledAttachments[optId][cand.id]) {
                var pdfBlob = await engine.generateAttachmentForCandidate(
                    cand,
                    optId,
                    companyData,
                    jobSkills,
                    function (subMsg) {
                        if (thisToken !== _fwdGlobalToken) return;
                        var midPct = Math.round(startPct + (endPct - startPct) * 0.5);
                        $('#fwd_compile_progress_bar').css('width', midPct + '%');
                        $('#fwd_compile_pct_badge').text(midPct + '%');
                        $('#fwd_compile_step_detail').text(subMsg);
                        $('#fwd_cand_progress_bar_' + cand.id).css('width', '65%');
                    }
                );

                if (!pdfBlob) {
                    throw new Error('Failed to render resume for ' + candName);
                }
                _fwdCompiledAttachments[optId][cand.id] = pdfBlob;
            }

            if (thisToken !== _fwdGlobalToken) return;

            // Candidate completed — mark 100% attached on card
            var blob = _fwdCompiledAttachments[optId][cand.id];
            var sizeStr = blob ? (' • ' + formatBytes(blob.size)) : '';
            $('#fwd_cand_card_' + cand.id).css({ 'background': '#F0FDF4', 'border-color': '#BBF7D0' });
            $('#fwd_cand_check_' + cand.id).css('display', 'flex');
            $('#fwd_cand_progress_wrap_' + cand.id).hide();
            $('#fwd_cand_status_' + cand.id).css({ 'background': '#DCFCE7', 'color': '#15803D' }).text('✓ Attached (100%)' + sizeStr);

            $('#fwd_compile_progress_bar').css('width', endPct + '%');
            $('#fwd_compile_pct_badge').text(endPct + '%');
            $('#fwd_compile_count').text((i + 1) + ' / ' + total);
        }

        if (thisToken !== _fwdGlobalToken) return;

        // 100% completed successfully
        _fwdIsCompiling = false;
        $('#fwd_compile_progress_bar').css({ 'width': '100%', 'background': '#059669' });
        $('#fwd_compile_pct_badge').text('100%').css({ 'color': '#059669', 'background': '#DCFCE7', 'border-color': '#86EFAC' });
        $('#fwd_compile_spinner').hide();
        $('#fwd_compile_status_text').html('✓ All ' + total + ' candidate profiles compiled & verified (100%)');
        $('#fwd_compile_step_detail').text('All attachments generated and verified. Ready to send.');

        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            btn.title = '';
        }
    } catch (err) {
        if (thisToken !== _fwdGlobalToken) return;
        _fwdIsCompiling = false;
        console.error('Resume compilation error:', err);
        $('#fwd_compile_spinner').hide();
        $('#fwd_compile_progress_bar').css({ 'background': '#EF4444' });
        $('#fwd_compile_pct_badge').text('Error').css({ 'color': '#DC2626', 'background': '#FEE2E2', 'border-color': '#FCA5A5' });
        $('#fwd_compile_status_text').html('⚠️ Could not prepare custom format: ' + (err.message || 'WASM error'));
        $('#fwd_compile_step_detail').text('Please re-select the format or switch to Option 1 (Original Resume).');
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }
    }
}

// Attachment Format dropdown change
$(document).off('change', '#fwd_resume_format').on('change', '#fwd_resume_format', function () {
    var sel = $(this).find('option:selected');
    var label = sel.attr('data-label') || sel.text();
    var desc = sel.attr('data-desc') || '';
    var optVal = $(this).val();

    $('#fwd_preview_format_title').text(label);
    $('#fwd_preview_desc').text(desc);

    updateSendButtonState();
});

// Toggle checkbox for "Attach Resume"
$(document).off('change', '#fwd_attach_cv_toggle').on('change', '#fwd_attach_cv_toggle', function () {
    if ($(this).is(':checked')) {
        $('#fwd_format_container').slideDown(150);
    } else {
        $('#fwd_format_container').slideUp(150);
    }
    updateSendButtonState();
});

// Initial synchronization on drawer load
updateSendButtonState();

// Input highlight reset listener
$(document).off('input', '#to_email, #subject, #cc_email_panel').on('input', '#to_email, #subject, #cc_email_panel', function() {
    $(this).css('border-color', '#E5E7EB');
});

// Submit click handler
document.getElementById("btnSubmit").addEventListener("click", async function () {
    var isAttachChecked = $('#fwd_attach_cv_toggle').is(':checked');
    var attached_cv = isAttachChecked ? 1 : 0;
    var optId = parseInt($('#fwd_resume_format').val(), 10) || 1;

    // ══════════════════════════════════════════════════════════════════════════════
    // 1. STRICT RECIPIENT & FORM VALIDATION (CLIENT-SIDE)
    // ══════════════════════════════════════════════════════════════════════════════
    var to_email = ($('#to_email').val() || '').trim();
    var cc_email = ($('#cc_email_panel').val() || '').trim();
    var subject  = ($('#subject').val() || '').trim();

    var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // A. Validate To Email (Mandatory)
    if (!to_email) {
        $('#to_email').css('border-color', '#EF4444').focus();
        Swal.fire({
            title: 'Recipient Email Required',
            text: 'Please enter at least one valid recipient email address in the "To Email ID" field.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }

    var toList = to_email.split(',').map(function(e) { return e.trim(); }).filter(function(e) { return e.length > 0; });
    if (toList.length === 0) {
        $('#to_email').css('border-color', '#EF4444').focus();
        Swal.fire({
            title: 'Recipient Email Required',
            text: 'Please enter at least one valid recipient email address in the "To Email ID" field.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }

    for (var ti = 0; ti < toList.length; ti++) {
        if (!emailRegex.test(toList[ti])) {
            $('#to_email').css('border-color', '#EF4444').focus();
            Swal.fire({
                title: 'Invalid Email Address',
                html: 'The recipient email address <code>' + $('<div>').text(toList[ti]).html() + '</code> is invalid. Please enter a valid email address.',
                icon: 'warning',
                confirmButtonColor: '#0D9488'
            });
            return;
        }
    }
    $('#to_email').css('border-color', '#E5E7EB');

    // B. Validate CC Email (Optional, but if entered, must be valid)
    if (cc_email) {
        var ccList = cc_email.split(',').map(function(e) { return e.trim(); }).filter(function(e) { return e.length > 0; });
        for (var ci = 0; ci < ccList.length; ci++) {
            if (!emailRegex.test(ccList[ci])) {
                $('#cc_email_panel').css('border-color', '#EF4444').focus();
                Swal.fire({
                    title: 'Invalid CC Email Address',
                    html: 'The CC email address <code>' + $('<div>').text(ccList[ci]).html() + '</code> is invalid. Please enter a valid email address.',
                    icon: 'warning',
                    confirmButtonColor: '#0D9488'
                });
                return;
            }
        }
        $('#cc_email_panel').css('border-color', '#E5E7EB');
    }

    // C. Validate Subject (Mandatory)
    if (!subject) {
        $('#subject').css('border-color', '#EF4444').focus();
        Swal.fire({
            title: 'Subject Required',
            text: 'Please enter an email subject before forwarding resumes.',
            icon: 'warning',
            confirmButtonColor: '#0D9488'
        });
        return;
    }
    $('#subject').css('border-color', '#E5E7EB');

    // Guard: Prevent sending if still compiling custom attachments
    if (attached_cv === 1 && optId !== 1 && _fwdIsCompiling) {
        Swal.fire('Please Wait', 'Resume attachments are still being prepared. Please wait until progress reaches 100%.', 'info');
        return;
    }

    // ══════════════════════════════════════════════════════════════════════════════
    // 2. IMMEDIATE BUTTON LOADING & 10-SECOND COUNTDOWN CONTROLLER
    // ══════════════════════════════════════════════════════════════════════════════
    var btn = document.getElementById("btnSubmit");
    btn.disabled = true;
    btn.style.cursor = 'not-allowed';
    btn.style.opacity = '0.85';

    $('.indicator-label').hide();
    $('.indicator-progress').css('display', 'inline-flex').show();
    $('#fwd_btn_progress_text').text('Processing...');

    var countdownTimer = null;
    var remainingSeconds = 10;

    function setBtnStatus(text) {
        $('#fwd_btn_progress_text').text(text);
    }

    function startCountdown(label) {
        if (countdownTimer) clearInterval(countdownTimer);
        remainingSeconds = 10;
        var prefix = label || 'Sending';
        setBtnStatus(prefix + ' (' + remainingSeconds + 's)...');
        countdownTimer = setInterval(function () {
            remainingSeconds--;
            if (remainingSeconds > 0) {
                setBtnStatus(prefix + ' (' + remainingSeconds + 's)...');
            } else {
                setBtnStatus('Finalizing dispatch...');
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
        }, 1000);
    }

    function stopCountdown() {
        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }
    }

    function restoreBtn() {
        stopCountdown();
        $('.indicator-progress').hide();
        $('.indicator-label').css('display', 'inline-flex').show();
        if (btn) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            btn.style.opacity = '1';
        }
    }

    var id      = 1;
    var url     = "{{ route('service_company.cvtracker.fwd_resume_send',':id') }}";
    url         = url.replace(':id', id);
    var token   = "{{ csrf_token() }}";

    var arr = [];
    $('.chk_list_item:checked').each(function () { arr.push($(this).val()); });

    var template    = $('#template').val();
    var from_email  = $('#from_email').val();
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

    // Attach pre-compiled custom PDFs if custom format was chosen
    if (attached_cv === 1 && optId !== 1) {
        setBtnStatus('Preparing files...');
        var cached = _fwdCompiledAttachments[optId] || {};
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
                    setBtnStatus('Rendering ' + (cand.full_name || 'Resume') + '...');
                    var companyData = {
                        companyName: @json($companyName ?? 'Hiring Partner'),
                        companyLogo: @json($companyLogo ?? '')
                    };
                    var engine = window.AgyForwardResumeEngine || window.AgyForwardResumeHelper;
                    if (!engine || typeof engine.generateAttachmentForCandidate !== 'function') {
                        throw new Error('Forward Resume engine not available');
                    }
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

            // Step 2: Upload to Cloudflare R2 cloud (Direct-to-Cloud Pipeline)
            var r2UploadedSuccess = false;
            var uploadedAttachmentIds = [];

            if (filesToInitiate.length > 0) {
                setBtnStatus('Uploading to Cloud...');

                try {
                    var initiateJson = await new Promise(function(resolve, reject) {
                        $.ajax({
                            url: "{{ route('service_company.cvtracker.fwd_attachments_initiate') }}",
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

                    if (initiateJson && initiateJson.status === 'success' && initiateJson.attachments) {
                        setBtnStatus('Cloud sync...');

                        var uploadResults = await Promise.allSettled(filesToInitiate.map(async function (item) {
                            var initInfo = initiateJson.attachments[item.candidate_id];
                            var candDisplayName = item.candidate_name || item.filename || ('Candidate #' + item.candidate_id);
                            if (!initInfo || !initInfo.upload_url) {
                                throw new Error(candDisplayName + ': Missing pre-signed upload URL');
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
                                throw new Error(candDisplayName + ': Storage returned HTTP ' + r2Res.status);
                            }
                            return {
                                candidate_id: item.candidate_id,
                                attachment_id: initInfo.attachment_id,
                                name: candDisplayName
                            };
                        }));

                        var allDirectSucceeded = true;
                        uploadResults.forEach(function (result) {
                            if (result.status === 'fulfilled') {
                                uploadedAttachmentIds.push(result.value.attachment_id);
                            } else {
                                allDirectSucceeded = false;
                            }
                        });

                        if (allDirectSucceeded && uploadedAttachmentIds.length === filesToInitiate.length) {
                            r2UploadedSuccess = true;
                            for (var u = 0; u < uploadedAttachmentIds.length; u++) {
                                formData.append('attachment_ids[]', uploadedAttachmentIds[u]);
                            }
                        }
                    }
                } catch (r2Err) {
                    console.warn('[ForwardResume] Cloud storage upload bypassed, using direct attachment:', r2Err);
                }

                // Attach files directly to request as guaranteed fallback
                for (var candId in candidateBlobs) {
                    if (candidateBlobs.hasOwnProperty(candId)) {
                        var bInfo = candidateBlobs[candId];
                        if (bInfo && bInfo.blob) {
                            formData.append('custom_attachments[' + candId + ']', bInfo.blob, bInfo.filename);
                        }
                    }
                }
            }
        } catch (genErr) {
            console.error('Resume preparation failed:', genErr);
            restoreBtn();
            Swal.fire(
                'Compilation Error',
                'Could not prepare candidate resume: ' + (genErr.message || 'Engine error') + '. Please try again.',
                'error'
            );
            return;
        }
    }

    // ══════════════════════════════════════════════════════════════════════════════
    // 3. DISPATCH WITH ACTIVE 10-SECOND COUNTDOWN TICKER
    // ══════════════════════════════════════════════════════════════════════════════
    startCountdown('Sending');

    $.ajax({
        type : 'POST',
        url  : url,
        data : formData,
        processData: false,
        contentType: false,
        success: function (response) {
            restoreBtn();

            if (response.status === 'error') {
                Swal.fire('Validation Error', response.message || 'Please check your input and try again.', 'warning');
                return;
            }

            if (response.status === 'coinCheck') {
                Swal.fire('Failed', response.message, 'warning').then(function () {
                    $(".btn-active-color-primary").click();
                });
                return;
            }
            if (response.status === 'success') {
                window._fwdCompiledAttachments = {};
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances['editor1']) {
                    try { CKEDITOR.instances['editor1'].destroy(true); } catch(e) {}
                }
                Swal.fire('Success!', 'Selected Applicant Forwarded.', 'success').then(function () {
                    location.reload();
                });
            }
        },
        error: function (xhr) {
            restoreBtn();
            var serverMsg = xhr.responseJSON && xhr.responseJSON.message;
            var msg = (xhr.status === 0 || !navigator.onLine)
                ? 'You are offline. Please check your connection and try again.'
                : (serverMsg || ('Something went wrong (HTTP ' + xhr.status + '). Please try again.'));
            Swal.fire('Error', msg, 'error');
        }
    });
});

// Initialize button state immediately
$(document).ready(function () {
    setTimeout(updateSendButtonState, 100);
});
updateSendButtonState();

// ══════════════════════════════════════════════════════════════════════════════
// ENTERPRISE ADD EMAIL RECIPIENT DRAWER CONTROLLER
// ══════════════════════════════════════════════════════════════════════════════
var _currentRcpTarget = 'to'; // 'to' or 'cc'

function getRcpTargetInput() {
    return _currentRcpTarget === 'to' ? $('#to_email') : $('#cc_email_panel');
}

function getExistingEmails() {
    var raw = getRcpTargetInput().val() || '';
    return raw.split(',').map(function(e) { return e.trim().toLowerCase(); }).filter(function(e) { return e.length > 0; });
}

function syncRecipientDrawerState() {
    var existing = getExistingEmails();

    $('.rcp-toggle-btn').each(function() {
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

    updateRcpFooterCount(existing.length);
}

function updateRcpFooterCount(totalCount) {
    var c = (typeof totalCount === 'number') ? totalCount : getExistingEmails().length;
    var text = c + (c === 1 ? ' recipient selected' : ' recipients selected');
    $('#rcp_footer_count_text').text(text);
    if (c > 0) {
        $('#rcp_footer_done_btn').text('Add ' + c + (c === 1 ? ' Recipient' : ' Recipients'));
    } else {
        $('#rcp_footer_done_btn').text('Done');
    }
}

function addEmailToTarget(email) {
    if (!email) return;
    var $input = getRcpTargetInput();
    var current = ($input.val() || '').trim();
    var list = current ? current.split(',').map(function(e) { return e.trim(); }).filter(Boolean) : [];
    var lowerList = list.map(function(e) { return e.toLowerCase(); });

    if (lowerList.indexOf(email.toLowerCase()) === -1) {
        list.push(email);
        $input.val(list.join(', ')).trigger('input');
    }
    syncRecipientDrawerState();
}

function removeEmailFromTarget(email) {
    if (!email) return;
    var $input = getRcpTargetInput();
    var current = ($input.val() || '').trim();
    var list = current ? current.split(',').map(function(e) { return e.trim(); }).filter(Boolean) : [];
    var lowerEmail = email.toLowerCase();
    list = list.filter(function(e) { return e.toLowerCase() !== lowerEmail; });
    $input.val(list.join(', ')).trigger('input');
    syncRecipientDrawerState();
}

window.openEmailRecipientDrawer = function(target) {
    _currentRcpTarget = target || 'to';
    syncRecipientDrawerState();
    $('#rcp_search_input').val('');
    $('#rcp_ext_email_inline_box').hide();
    $('#rcp_ext_email_input').val('');
    applyRcpSearch();

    var drawerEl = document.getElementById('kt_recipient_drawer');
    if (drawerEl) {
        // Move to document.body to avoid being trapped under .drawer-overlay stacking context
        if (drawerEl.parentElement !== document.body) {
            $('body > #kt_recipient_drawer').not(drawerEl).remove();
            document.body.appendChild(drawerEl);
        }
        if (typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (!drawer) {
                KTDrawer.createInstances();
                drawer = KTDrawer.getInstance(drawerEl);
            }
            if (drawer) { 
                drawer.show(); 
            }
        }
    }
};

window.closeEmailRecipientDrawer = function() {
    var drawerEl = document.getElementById('kt_recipient_drawer');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer) { drawer.hide(); }
    }
    $('.drawer-overlay').filter(function() {
        var z = parseInt($(this).css('z-index'), 10);
        return z >= 1000;
    }).remove();
};

// Immediate relocation of recipient drawer to body on drawer load
$(document).ready(function () {
    var drawerEl = document.getElementById('kt_recipient_drawer');
    if (drawerEl && drawerEl.parentElement !== document.body) {
        $('body > #kt_recipient_drawer').not(drawerEl).remove();
        document.body.appendChild(drawerEl);
        if (typeof KTDrawer !== 'undefined') {
            KTDrawer.createInstances();
        }
    }
});

// Override / capture clicks on Add Email buttons
$(document).off('click', '.to_email_panel').on('click', '.to_email_panel', function(e) {
    e.preventDefault();
    e.stopPropagation();
    openEmailRecipientDrawer('to');
});
$(document).off('click', '.cc_email_panel').on('click', '.cc_email_panel', function(e) {
    e.preventDefault();
    e.stopPropagation();
    openEmailRecipientDrawer('cc');
});

// Legacy triggers compatibility
$(document).off('click', '#kt_activitiesIntTeam_toggle_to').on('click', '#kt_activitiesIntTeam_toggle_to', function(e) {
    e.preventDefault();
    openEmailRecipientDrawer('to');
});
$(document).off('click', '#kt_activitiesIntTeam_toggle_cc').on('click', '#kt_activitiesIntTeam_toggle_cc', function(e) {
    e.preventDefault();
    openEmailRecipientDrawer('cc');
});

// Tab switching (Internal Team vs Client Contacts)
$(document).off('click', '.rcp-tab-card').on('click', '.rcp-tab-card', function() {
    var tab = $(this).attr('data-tab');
    $('.rcp-tab-card').removeClass('active');
    $(this).addClass('active');

    if (tab === 'internal') {
        $('#rcp_section_internal').show();
        $('#rcp_section_client').hide();
    } else {
        $('#rcp_section_internal').hide();
        $('#rcp_section_client').show();
    }
    applyRcpSearch();
});

// Toggle Add / Added button click
$(document).off('click', '.rcp-toggle-btn').on('click', '.rcp-toggle-btn', function(e) {
    e.preventDefault();
    var email = $(this).attr('data-email');
    if (!email) return;
    if ($(this).hasClass('is-added')) {
        removeEmailFromTarget(email);
    } else {
        addEmailToTarget(email);
    }
});

// Search filter
function applyRcpSearch() {
    var q = ($('#rcp_search_input').val() || '').toLowerCase().trim();
    var activeTab = $('.rcp-tab-card.active').attr('data-tab');
    var $activeSection = (activeTab === 'internal') ? $('#rcp_section_internal') : $('#rcp_section_client');

    $('#rcp_search_clear').toggle(q.length > 0);

    var visibleCount = 0;
    $activeSection.find('.rcp-contact-item').each(function() {
        var name = $(this).attr('data-name') || '';
        var email = $(this).attr('data-email') || '';
        var role = $(this).attr('data-role') || '';
        var dept = $(this).attr('data-dept') || '';

        var match = !q || name.indexOf(q) !== -1 || email.indexOf(q) !== -1 || role.indexOf(q) !== -1 || dept.indexOf(q) !== -1;
        $(this).toggle(match);
        if (match) visibleCount++;
    });

    var titlePrefix = (activeTab === 'internal') ? 'Team Members' : 'Client Contacts';
    $('#rcp_list_heading').text(titlePrefix + ' (' + visibleCount + ')');
    $('#rcp_no_results').toggle(visibleCount === 0);
}

$(document).off('input', '#rcp_search_input').on('input', '#rcp_search_input', applyRcpSearch);
$(document).off('click', '#rcp_search_clear').on('click', '#rcp_search_clear', function() {
    $('#rcp_search_input').val('').trigger('input');
});

// Add external email inline tool
$(document).off('click', '#rcp_add_external_email_btn').on('click', '#rcp_add_external_email_btn', function(e) {
    e.preventDefault();
    $('#rcp_ext_email_inline_box').toggle();
    if ($('#rcp_ext_email_inline_box').is(':visible')) {
        $('#rcp_ext_email_input').focus();
    }
});

$(document).off('click', '#rcp_ext_email_add_btn').on('click', '#rcp_ext_email_add_btn', function() {
    var email = ($('#rcp_ext_email_input').val() || '').trim();
    var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!email || !emailRegex.test(email)) {
        Swal.fire('Invalid Email', 'Please enter a valid email address.', 'warning');
        return;
    }
    addEmailToTarget(email);
    $('#rcp_ext_email_input').val('');
    $('#rcp_ext_email_inline_box').hide();
});

$(document).off('keypress', '#rcp_ext_email_input').on('keypress', '#rcp_ext_email_input', function(e) {
    if (e.which === 13) {
        e.preventDefault();
        $('#rcp_ext_email_add_btn').click();
    }
});

// Sorting
$(document).off('change', '#rcp_sort_select').on('change', '#rcp_sort_select', function() {
    var val = $(this).val();
    ['#rcp_section_internal', '#rcp_section_client'].forEach(function(secId) {
        var $sec = $(secId);
        var $items = $sec.children('.rcp-contact-item').get();
        $items.sort(function(a, b) {
            var an = $(a).attr('data-name') || '';
            var bn = $(b).attr('data-name') || '';
            var ar = $(a).attr('data-role') || '';
            var br = $(b).attr('data-role') || '';
            if (val === 'name_asc') return an.localeCompare(bn);
            if (val === 'name_desc') return bn.localeCompare(an);
            if (val === 'role') return ar.localeCompare(br);
            return 0;
        });
        $.each($items, function(i, it) { $sec.append(it); });
    });
});

// Close drawer buttons
$(document).off('click', '#rcp_footer_cancel_btn, #rcp_footer_done_btn, #kt_recipient_drawer_close').on('click', '#rcp_footer_cancel_btn, #rcp_footer_done_btn, #kt_recipient_drawer_close', function(e) {
    e.preventDefault();
    closeEmailRecipientDrawer();
});

// Backward-compatible global bridge functions
window.AddEvent = function(name, email) { addEmailToTarget(email); };
window.DeleteEvent = function(name, email) { removeEmailFromTarget(email); };
window.AddEventTo = function(name, email) { addEmailToTarget(email); };
window.DeleteEventTo = function(name, email) { removeEmailFromTarget(email); };



</script> 
<script>
	
	// from_email is a <select> — no Tagify needed
	// cc_email panel is a plain input (no Tagify)
		 
</script>

