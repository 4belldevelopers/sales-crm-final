@php 
		use App\Helper\GeneralMethod;
		$salaryMode 					=	GeneralMethod::salaryMode();
		$currencySymbole 				=	GeneralMethod::currencySymbole();
			
@endphp 
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

<div class="main_container" id="main_container" >
@php  
$template_body 	=	'';




$to_email	=	'';
if($to_email_from_count > 0){
		$to_email = implode(', ', $to_email_from_data);		
}


$cc_email	=	'';
if($cc_email_count > 0){
		$cc_email = implode(', ', $cc_email_data);		
}


@endphp 

<style>


@media  only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr{width: max-content!important;}
  .table > :not(caption) > * > *{padding: 0rem;}
  td:nth-child(n+2),
  th:nth-child(n+2){width: revert!important;}
}




@media only screen and (min-width: 1024px) and (max-width: 1440px){
  #kt_activitiesIntTeamsss{width: 750px;}
}
@media only screen and (min-width: 1440px) and (max-width: 2560px){
  #kt_activitiesIntTeamsss{width: 750;}
}

/* ════════════ Modern Card + Sub-Card Approver Recipient Drawer (Rule 20) ════════════
   Scoped to .co-appr-* so it doesn't affect any other views or modules. */
.co-appr-picker, .co-appr-picker * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
.co-appr-picker { font-size: 12px; }

/* Self-contained sliding drawer */
.co-appr-drawer {
    position: absolute !important; top: 0 !important; bottom: 0 !important; right: -100% !important; left: auto !important;
    width: 100% !important; height: 100% !important; background: #ffffff !important;
    box-shadow: -8px 0 32px rgba(15, 23, 42, 0.16); z-index: 2147483000 !important;
    transition: right .28s cubic-bezier(0.16, 1, 0.3, 1); overflow-y: auto; display: block !important;
}
.co-appr-drawer.co-appr-drawer--open { right: 0 !important; }
.co-appr-drawer-backdrop {
    display: none; position: absolute !important; inset: 0; background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px); z-index: 2147482999 !important;
}
.co-appr-drawer-backdrop.co-appr-drawer-backdrop--open { display: block !important; }

/* Tier 2: Source selector sub-cards */
.co-appr-source-row { display: flex; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
.co-appr-source-card {
    flex: 1 1 230px; display: flex; align-items: center; gap: 12px; cursor: pointer;
    background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 11px 14px;
    position: relative; transition: all 0.18s ease; user-select: none;
}
.co-appr-source-card:hover { border-color: #cbd5e1; background: #f8fafc; }
.co-appr-source-card--active {
    border-color: #009ef7 !important; background: #f0f7ff !important;
    box-shadow: 0 1px 4px rgba(0, 158, 247, 0.12);
}
.co-appr-source-ico {
    width: 36px; height: 36px; border-radius: 8px; background: #f8fafc;
    border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0; transition: all 0.18s ease;
}
.co-appr-source-card--active .co-appr-source-ico {
    background: #009ef7; border-color: #009ef7; color: #ffffff;
}
.co-appr-source-ico svg { width: 17px; height: 17px; }
.co-appr-source-meta { flex: 1; min-width: 0; }
.co-appr-source-title {
    font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 700;
    color: #1e293b; line-height: 1.25;
}
.co-appr-source-card--active .co-appr-source-title { color: #009ef7; }
.co-appr-source-sub { margin-top: 3px; }
.co-appr-count-pill {
    display: inline-block; font-family: 'Poppins', sans-serif; font-size: 10.5px;
    font-weight: 600; padding: 2px 7px; border-radius: 6px; background: #f1f5f9;
    border: 1px solid #e2e8f0; color: #64748b; line-height: 1.2;
}
.co-appr-source-card--active .co-appr-count-pill {
    background: #e0f2fe; border-color: #bae6fd; color: #0284c7;
}
.co-appr-source-check {
    width: 18px; height: 18px; border-radius: 50%; border: 1.5px solid #cbd5e1;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    background: #ffffff; transition: all 0.15s ease;
}
.co-appr-source-card--active .co-appr-source-check {
    border-color: #009ef7; background: #ffffff;
}
.co-appr-dot {
    width: 8px; height: 8px; border-radius: 50%; background: transparent;
    transition: background 0.15s ease;
}
.co-appr-source-card--active .co-appr-dot { background: #009ef7; }

/* Tier 1: Search toolbar card */
.co-appr-toolbar {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 8px 12px; margin-bottom: 14px; display: flex; align-items: center; gap: 12px;
}
.co-appr-searchbox {
    flex: 1; display: flex; align-items: center; background: #ffffff;
    border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 10px; height: 36px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.co-appr-searchbox:focus-within {
    border-color: #009ef7; box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
}
.co-appr-search-ico {
    width: 14px; height: 14px; color: #94a3b8; margin-right: 8px; flex-shrink: 0;
}
.co-appr-search-input {
    flex: 1; border: none !important; outline: none !important; background: transparent !important;
    font-family: 'Poppins', sans-serif !important; font-size: 12px !important; color: #0f172a !important;
    box-shadow: none !important; padding: 0 !important; height: 100% !important;
}
.co-appr-search-input::placeholder { color: #94a3b8 !important; }
.co-appr-search-clear {
    border: none; background: transparent; color: #94a3b8; font-size: 16px;
    cursor: pointer; padding: 0 4px; line-height: 1;
}
.co-appr-search-clear:hover { color: #0f172a; }
.co-appr-counter-badge {
    font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 500;
    color: #64748b; white-space: nowrap; padding-right: 4px;
}
.co-appr-active-count { font-weight: 700; color: #0f172a; }

/* Tier 3: Approver table card & mandatory header background (Rule 20) */
.co-appr-table-card {
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px;
    overflow: hidden; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.co-appr-table {
    width: 100%; margin-bottom: 0 !important; border-collapse: separate; border-spacing: 0;
}
.co-appr-table thead tr {
    background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important;
}
.co-appr-table thead th {
    font-family: 'Poppins', sans-serif !important; font-size: 10.5px !important;
    font-weight: 700 !important; color: #64748b !important; letter-spacing: 0.04em !important;
    text-transform: uppercase !important; padding: 10px 14px !important;
    border-bottom: 1px solid #e2e8f0 !important; border-top: none !important;
}
.co-appr-table tbody tr { transition: background 0.15s ease; }
.co-appr-table tbody tr:hover { background: #f8fafc; }
.co-appr-table tbody td {
    padding: 11px 14px !important; vertical-align: middle;
    border-bottom: 1px solid #f1f5f9 !important; border-top: none !important;
}
.co-appr-table tbody tr:last-child td { border-bottom: none !important; }
.co-appr-avatar {
    width: 34px; height: 34px; border-radius: 50%; display: flex;
    align-items: center; justify-content: center; font-family: 'Poppins', sans-serif;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.co-appr-name {
    font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 600;
    color: #0f172a; line-height: 1.25;
}
.co-appr-role-tag {
    font-family: 'Poppins', sans-serif; font-size: 10.5px; font-weight: 500;
    color: #64748b; margin-top: 2px; line-height: 1.2;
}
.co-appr-contact, .co-appr-company {
    display: inline-flex; align-items: center; gap: 6px; max-width: 100%;
}
.co-appr-contact-ico, .co-appr-company-ico {
    width: 13px; height: 13px; color: #94a3b8; flex-shrink: 0;
}
.co-appr-email-text, .co-appr-company-text {
    font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 500;
    color: #334155;
}
.co-appr-status-badge {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    gap: 4px !important; font-family: 'Poppins', sans-serif !important; font-size: 11px !important;
    font-weight: 600 !important; padding: 3px 8px !important; border-radius: 6px !important;
    white-space: nowrap !important; line-height: 1.2 !important;
}
.co-appr-status--added {
    background: #ecfdf5 !important; color: #047857 !important; border: 1px solid #a7f3d0 !important;
}
.co-appr-status--not-added {
    background: #f8fafc !important; color: #64748b !important; border: 1px solid #e2e8f0 !important;
}
.co-appr-remove-btn {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    gap: 5px !important; height: 28px !important; padding: 0 12px !important;
    font-family: 'Poppins', sans-serif !important; font-size: 11.5px !important;
    font-weight: 600 !important; color: #dc2626 !important; background: #fef2f2 !important;
    border: 1px solid #fecaca !important; border-radius: 6px !important; cursor: pointer !important;
    text-decoration: none !important; white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(220, 38, 38, 0.06) !important; transition: all 0.15s ease !important;
}
.co-appr-remove-btn:hover {
    background: #fee2e2 !important; border-color: #fca5a5 !important; color: #b91c1c !important;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.12) !important;
}
.co-appr-add-btn {
    display: inline-flex !important; align-items: center !important; justify-content: center !important;
    gap: 5px !important; height: 28px !important; padding: 0 14px !important;
    font-family: 'Poppins', sans-serif !important; font-size: 11.5px !important;
    font-weight: 600 !important; color: #ffffff !important; background: #1e293b !important;
    border: 1px solid #1e293b !important; border-radius: 6px !important; cursor: pointer !important;
    text-decoration: none !important; white-space: nowrap !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08) !important; transition: all 0.15s ease !important;
}
.co-appr-add-btn:hover {
    background: #0f172a !important; border-color: #0f172a !important; color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.14) !important;
}
.co-appr-add-btn.co-appr-btn--remove {
    background: #ecfdf5 !important; border-color: #a7f3d0 !important; color: #059669 !important;
}
.co-appr-add-btn.co-appr-btn--remove:hover {
    background: #d1fae5 !important; border-color: #6ee7b7 !important; color: #047857 !important;
}
.co-appr-empty-cell { padding: 32px 16px !important; text-align: center; }
.co-appr-empty-inner { display: flex; flex-direction: column; align-items: center; gap: 8px; }
.co-appr-empty-ico {
    width: 38px; height: 38px; border-radius: 50%; background: #f1f5f9;
    color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.co-appr-empty-msg {
    font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 500; color: #64748b;
}

/* ════════════ Send Offer Letter (candidate flow, mtype=1) — polished composer ════════════
   Scoped entirely to .co-sendoffer so the shared approval composer (mtype=2) is untouched.
   Poppins, capped at 14px, restyle only — every field id/name/handler stays exactly as-is. */
.co-sendoffer, .co-sendoffer * { font-family: 'Poppins', sans-serif; }
.co-sendoffer { font-size: 13px; color: #3f4254; }

/* Candidate summary header card */
.co-so-head { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; background: #f9fafd; border: 1px solid #eef1f7; border-radius: 12px; padding: 14px 16px; margin-bottom: 18px; }
.co-so-avatar { width: 52px; height: 52px; border-radius: 50%; background: #eef3ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; flex-shrink: 0; }
.co-so-ident { min-width: 160px; }
.co-so-name { font-size: 14px !important; font-weight: 700; color: #181c32; }
.co-so-role { font-size: 12px; color: #1b84ff; font-weight: 600; }
.co-so-meta { font-size: 11px; color: #7e8299; margin-top: 3px; display: flex; gap: 12px; flex-wrap: wrap; }
.co-so-meta span { display: inline-flex; align-items: center; gap: 4px; }
.co-so-meta svg { width: 12px; height: 12px; }
.co-so-facts { display: flex; gap: 22px; flex-wrap: wrap; margin-left: auto; }
.co-so-fact-label { font-size: 10px; color: #a1a5b7; }
.co-so-fact-value { font-size: 12px; font-weight: 700; color: #181c32; margin-top: 2px; }
.co-so-fact-value.co-so-ctc { color: #0f9463; }
.co-so-status { border-radius: 10px; padding: 8px 12px; text-align: center; align-self: stretch; display: flex; flex-direction: column; justify-content: center; min-width: 150px; }
.co-so-status--approved { background: #e3f7e9; border: 1px solid #b7ebc6; }
.co-so-status--pending { background: #fff8e1; border: 1px solid #f6e6b8; }
.co-so-status-title { font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; justify-content: center; }
.co-so-status--approved .co-so-status-title { color: #0f7a3d; }
.co-so-status--pending .co-so-status-title { color: #8a6d00; }
.co-so-status-title svg { width: 14px; height: 14px; }
.co-so-status-sub { font-size: 10px; color: #5e6278; margin-top: 3px; }

/* Fields: keep the existing label-left / field-right structure, just polish it + add icons. */
.co-sendoffer .form-group.row { margin-bottom: 16px; align-items: flex-start; }
.co-sendoffer .col-form-label { font-size: 12px !important; font-weight: 600 !important; color: #3f4254 !important; padding-top: 8px; }
.co-sendoffer .col-form-label .co-so-licon { color: #4f46e5; vertical-align: -2px; margin-right: 6px; }
/* Apply icon size for ALL mtypes — co-sendoffer only exists for mtype=1 */
.col-form-label .co-so-licon { color: #4f46e5; vertical-align: -2px; margin-right: 6px; display: inline-flex; align-items: center; }
.col-form-label .co-so-licon svg { width: 16px !important; height: 16px !important; flex-shrink: 0; }
.col-form-label { font-size: 13px !important; font-weight: 600 !important; }
.co-sendoffer .col-form-label .co-so-licon svg { width: 15px; height: 15px; }
.co-sendoffer .form-control, .co-sendoffer .form-select {
    background: #fff !important; border: 1px solid #e4e6ef !important; border-radius: 8px !important;
    box-shadow: none !important; font-size: 13px !important; color: #3f4254 !important; min-height: 40px;
}
.co-sendoffer .form-control:focus, .co-sendoffer .form-select:focus { border-color: #1b84ff !important; }
.co-sendoffer .form-text { font-size: 10px !important; color: #a1a5b7 !important; }
/* Tagify (From field renders as chips) — align its look to the polished inputs */
.co-sendoffer .tagify { border: 1px solid #e4e6ef !important; border-radius: 8px !important; background: #fff !important; min-height: 40px; }
.co-sendoffer .tagify__tag > div { font-size: 12px; }
/* "+ Add Email"/"Add Approver" side buttons */
.co-sendoffer .to_email_panel, .co-sendoffer .cc_email_panel { background: #eef3ff !important; border: 1px solid #e0e7ff !important; color: #4f46e5 !important; font-size: 11px !important; font-weight: 600; border-radius: 7px; padding: 6px 12px !important; }
.co-sendoffer .to_email_panel:hover, .co-sendoffer .cc_email_panel:hover { background: #e0e7ff !important; }
/* Signature radios → clean inline chips */
.co-sendoffer .form-check { padding: 6px 12px; border: 1px solid #e4e6ef; border-radius: 8px; margin-bottom: 0; display: inline-flex; align-items: center; gap: 6px; }
.co-sendoffer .form-check .form-check-label { font-size: 12px; color: #3f4254; margin: 0; }

/* Footer: right-aligned, gradient Send button (matches screenshot) */
.co-sendoffer-footer { display: flex; justify-content: flex-end; align-items: center; gap: 10px; border-top: 1px solid #eef1f7; padding: 14px 0 4px; margin-top: 6px; }
.co-sendoffer-footer .co-so-cancel { background: #fff !important; border: 1px solid #d7dae3 !important; color: #5e6278 !important; font-weight: 600 !important; font-size: 12px !important; min-width: 100px; height: 42px; border-radius: 9px; }
.co-sendoffer-footer .co-so-send { background: linear-gradient(90deg,#4f46e5,#1b84ff) !important; border: none !important; color: #fff !important; font-weight: 600 !important; font-size: 13px !important; min-width: 180px; height: 42px; border-radius: 9px; }
.co-sendoffer-footer .co-so-send:hover { filter: brightness(.95); }
</style>
<div class="co-appr-drawer-backdrop" id="co_appr_drawer_backdrop"></div>
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>


@php
// For the approval flow (mtype=2): auto-find the approval email template
// so subject/body can be pre-populated without the user touching the form.
$_approvalTpl = null;
if ((int) @$mtype === 2 && !empty($templateData)) {
    foreach ($templateData as $_t) {
        if (stripos((string)@$_t->display_name, 'approval') !== false) { $_approvalTpl = $_t; break; }
    }
    if (!$_approvalTpl) {
        $_approvalTpl = ($templateData instanceof \Illuminate\Support\Collection)
            ? $templateData->first()
            : ($templateData[0] ?? null);
    }
}
@endphp
<form id="form1">
  {{-- For mtype=2 (approval): all visible form rows are hidden; only hidden fields remain.
       The user picks approvers from the Select Approver drawer only. --}}
  @if((int) @$mtype === 2)
  <div style="display:none;">
      <input type="hidden" id="mtypeId" name="mtype"     value="{{ $mtype }}">
      <input type="hidden" id="offerId" name="offerId"   value="{{ $offerId }}">
      <input type="hidden" id="to_email" name="to_email" value="">
      <input type="hidden" id="from_email" name="from_email" value="notify@jeroworks.com">
      <input type="hidden" id="template"  name="template"  value="{{ @$_approvalTpl->id }}">
      <input type="hidden" id="subject"   name="subject"    value="">
      <input type="hidden" name="signature" value="0">
      <textarea id="editor1" name="editor1"></textarea>
      <input id="cc_email_panel" value="">
  </div>
  @else
  <!--begin::Modal body-->
  <div class="modal-body scroll-y py-0 px-lg-7 {{ (int) @$mtype === 1 ? 'co-sendoffer' : '' }}">
    <div data-kt-element="options">
      <div class="py-5">
        @if((int) @$mtype === 1)
          @php
              $so_app   = (isset($applicationData) && count($applicationData) > 0) ? $applicationData[0] : null;
              $so_name  = trim((string) @$so_app->full_name) ?: 'Candidate';
              $so_role  = @$so_app->current_designation ?: @$so_app->job_title;
              $so_exp   = @$so_app->total_exp;
              $so_email = @$so_app->email;
              $so_initial = strtoupper(substr($so_name, 0, 1) ?: 'C');
              $so_currencyArr = ['INR','EUR','JPY','GBP','USD','AED'];
              $so_curr  = $so_currencyArr[(int) @$co_send_offer->currency] ?? '';
              $so_ctc   = @$co_send_offer->salary_ctc ?: @$co_send_offer->yearly_final_gross;
              $so_join  = @$co_send_offer->joined_date;
              $so_mgmt  = (int) @$co_send_offer->management_status;
              $so_appr_name = trim((string) (@$co_send_appr->name_hr ?: @$co_send_appr->email_id_hr ?? ''));
              $so_appr_date = @$co_send_offer->approved_date_time;
          @endphp
          <div class="co-so-head">
              <div class="co-so-avatar">{{ $so_initial }}</div>
              <div class="co-so-ident">
                  <div class="co-so-name">{{ ucwords($so_name) }}</div>
                  @if(!empty($so_role))<div class="co-so-role">{{ ucwords($so_role) }}</div>@endif
                  <div class="co-so-meta">
                      @if(!empty($so_exp))<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>Exp: {{ $so_exp }} Years</span>@endif
                      @if(!empty($so_email))<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>{{ $so_email }}</span>@endif
                  </div>
              </div>
              <div class="co-so-facts">
                  <div><div class="co-so-fact-label">Offer ID</div><div class="co-so-fact-value">OFF-{{ $offerId }}</div></div>
                  @if(!empty($so_ctc))<div><div class="co-so-fact-label">Offered CTC</div><div class="co-so-fact-value co-so-ctc">{{ $so_curr }} {{ number_format((float) $so_ctc) }}</div></div>@endif
                  @if(!empty($so_join))<div><div class="co-so-fact-label">Joining Date</div><div class="co-so-fact-value">{{ date('d M Y', strtotime($so_join)) }}</div></div>@endif
              </div>
              @if($so_mgmt === 2 || $so_mgmt === 1)
              <div class="co-so-status co-so-status--{{ $so_mgmt === 2 ? 'approved' : 'pending' }}">
                  <span class="co-so-status-title">
                      @if($so_mgmt === 2)
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Approved
                      @else
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Pending Approval
                      @endif
                  </span>
                  @if($so_mgmt === 2 && !empty($so_appr_name))<div class="co-so-status-sub">by {{ ucwords($so_appr_name) }}</div>@endif
                  @if($so_mgmt === 2 && !empty($so_appr_date))<div class="co-so-status-sub">on {{ date('d M Y, h:i A', strtotime($so_appr_date)) }}</div>@endif
              </div>
              @endif
          </div>
        @endif
        <!--begin::Template-->

        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>From</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="from_email" id="from_email"  value="notify@jeroworks.com" readonly="" data-role="from_email" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <!--end::From email-->
        <!--begin::To email-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20a7 7 0 0 1 14 0"/></svg></span>To</label>
          <div class="col-lg-10 col-xl-10" style="position: relative;">
            <input type="hidden" id="mtypeId" name="mtype" value="{{ $mtype }}">
            <input type="hidden" id="offerId" name="offerId" value="{{ $offerId }}">
            <input type="email" class="form-control form-control-solid" name="to_email" id="to_email"  value="{{ (@$mtype==1) ? @$toEmailId : '' }}" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
            <div id="to_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
            <span class="form-text text-muted">Please enter email</span> 
            @if(@$mtype==2)
             <div class="" style="position: absolute; right: 13px; top: 4px;">
                <a href="javascript:;" class="btn btn-primary btn-sm to_email_panel" data-co-appr-open="to">+ Add Approver</a>
             </div>
             @endif
            </div>
           
        </div>
        <!--end::To email-->
        <!--begin::CC email-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="14" height="14" rx="2"/><path d="M3 15V5a2 2 0 0 1 2-2h10"/></svg></span>CC</label>
          <div class="col-lg-10 col-xl-10" style="position: relative;">

			  <!--<select class="form-control select2" id="kt_select2_2" data-placeholder="Select a client..." name="cc_email[]" multiple="multiple">-->
					<!--<optgroup label="select a client">-->
					
					<!--	@if(!empty($cc_email_assignData)){ -->
					<!--		@foreach($cc_email_assignData as $tdata )-->
					<!--			<option value="{{ $tdata->email }}">{{ $tdata->name }} - ({{ $tdata->email }})</option>-->
					<!--		@endforeach-->
					<!--	@endif				-->
					<!--</optgroup>-->
			  <!--</select>-->

            <input type="text" class="form-control form-control-solid" name="cc_email_panel" id="cc_email_panel"  value="" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
            <div id="cc_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
            <span class="form-text text-muted">Please enter email</span>
            
            <div class="" style="position: absolute; right: 13px; top: 4px;">
            <a href="javascript:;" class="btn btn-primary btn-sm cc_email_panel" data-co-appr-open="cc">Add E-mail ID</a>
           </div>
           
            </div>
            
        </div>
        <!--end::CC email-->
		 <!--begin::Subject-->
		 <div class="form-group row mb-4">
			@if(@$mtype==2)
			<input type="hidden" class="form-check-input signature" value="0" name="signature" id="signature2">
			@else
			<label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3-1 6-8 8-8s2 3 4 3 3-2 6-2"/><path d="m15 5 4 4"/></svg></span>Signature</label>
			  <div class="col-lg-10 col-xl-3">
				<div class="form-check">
				  <input type="radio" class="form-check-input signature" value="1" name="signature" id="signature1" checked>
				  <label class="form-check-label" for="signature1">With e-signature</label>
				</div>
			  </div>
			  <div class="col-lg-10 col-xl-3">
				<div class="form-check">
				  <input type="radio" class="form-check-input signature" value="0" name="signature" id="signature2">
				  <label class="form-check-label" for="signature2">Without e-signature</label>
				</div>
			  </div>
			  @endif
		  <!--end::Subject-->
		   <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></span>Template</label>
          <div class="col-lg-10 col-xl-10">
            <select name="template" id="template" class="form-select form-select-solid" aria-label="Select template">
              <option value="">Select Email Template...</option>
               
					@if(!empty($templateData)){ 
						@foreach($templateData as $tdata )
							<option value="{{ $tdata->id }}">{{ $tdata->display_name }}</option>
						@endforeach
					@endif				
			  
            </select>
            <span class="form-text text-muted">Please select email template</span> </div>
        </div>
        <!--begin::Subject-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 8.4 12 3 3.4 8.4M4 10v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="m4 10 8 5 8-5"/></svg></span>Subject</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="subject" id="subject" placeholder="Enter subject" value="" >
            <span class="form-text text-muted">Please enter subject</span> </div>
        </div>
        <!--end::Subject-->
        <!--begin::Body-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></svg></span>Body</label>
          <div class="col-lg-10 col-xl-10">
            <textarea  id="editor1" name="editor1" row="10" >
				{{ @$template_body }}
			</textarea>
            <span class="form-text text-muted"> Please enter email template body content </span> </div>
        </div>
       
      </div>
    </div>
  </div>
  <!--begin::Modal body-->
  <!--begin::Modal footer-->
  <div class="modal-footer flex-center {{ (int) @$mtype === 1 ? 'co-sendoffer-footer' : '' }}">
    <div class="div1">
      <!--begin::Cancel-->
      <button type="close" id="" class="btn btn-light-dark me-3 co-so-cancel" data-dismiss="modal">Cancel</button>
      <!--begin::Send-->
      <button type="button" class="btn btn-light-primary co-so-send" name="btnSubmit" id="btnSubmit" value="btnSubmit"> <span class="indicator-label">{{ (int) @$mtype === 1 ? 'Send Offer Letter' : 'Send' }}</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
      <!--end::Send-->
    </div>
    <!--<span style="position: absolute;right: 2%;"></span>-->
    <div class="div2 position-absolute end-0 me-5 d-none"> </div>
  </div>
  <!--end::Modal footer-->
  <!--</form>-->
  </div>
@endif {{-- end mtype !== 2 --}}
</form>




<div id="kt_activitiesIntTeam_cc" class="co-appr-drawer">
  <div class="position-relative h-100 d-flex flex-column bg-white" id="kt__cc_body">
    <!--begin::Team header (Rule 20 Anchor Background)-->
    <div id="kt__cc_header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;min-height:56px;flex-shrink:0;">
        <div class="d-flex align-items-center gap-3">
            <div style="width:34px;height:34px;border-radius:8px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:center;font-size:13.5px;flex-shrink:0;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <span class="fw-bold text-dark" style="font-family:'Poppins',sans-serif;font-size:14px;color:#0f172a;letter-spacing:-0.01em;">Select Approver (CC)</span>
                <div style="font-size:10.5px;color:#64748b;">Add team members to CC on offer notifications</div>
            </div>
        </div>
        <button type="button" id="kt_activitiesIntTeam_close_cc" class="co-drawer-topbar-close" aria-label="Close CC drawer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <!--end::Team header-->
    <!--begin::Team Content-->
    <div class="co-appr-picker flex-grow-1 overflow-auto p-4 p-md-5" id="kt_cc_scroll">
        {{-- Tier 2 Sub-Tabs / Source Cards --}}
        <div class="co-appr-source-row">
            <div class="co-appr-source-card co-appr-source-card--active" data-source-for="cc" data-source="internal">
                <input type="radio" name="apprSourceCc" value="internal" checked style="display:none;">
                <span class="co-appr-source-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/></svg>
                </span>
                <div class="co-appr-source-meta">
                    <div class="co-appr-source-title">Internal Company Approvers</div>
                    <div class="co-appr-source-sub"><span class="co-appr-count-pill">{{ count($cc_email_internal_data ?? []) }} available</span></div>
                </div>
                <div class="co-appr-source-check"><span class="co-appr-dot"></span></div>
            </div>
            @if(!empty($cc_email_client_data))
            <div class="co-appr-source-card" data-source-for="cc" data-source="client">
                <input type="radio" name="apprSourceCc" value="client" style="display:none;">
                <span class="co-appr-source-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <div class="co-appr-source-meta">
                    <div class="co-appr-source-title">Client Company Approvers</div>
                    <div class="co-appr-source-sub"><span class="co-appr-count-pill">{{ count($cc_email_client_data) }} available</span></div>
                </div>
                <div class="co-appr-source-check"><span class="co-appr-dot"></span></div>
            </div>
            @endif
        </div>

        {{-- Tier 1 Toolbar / Search Card --}}
        <div class="co-appr-toolbar">
            <div class="co-appr-searchbox">
                <svg class="co-appr-search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="co-appr-search-input" data-search-for="cc" placeholder="Search by name, email or company...">
                <button type="button" class="co-appr-search-clear d-none" aria-label="Clear search">&times;</button>
            </div>
            <div class="co-appr-counter-badge">
                <span class="co-appr-active-count">{{ count($cc_email_internal_data ?? []) }}</span> approvers
            </div>
        </div>

        {{-- Tier 3 Table Card --}}
        <div class="co-appr-table-card">
            <table class="co-appr-table align-middle" id="Iteam_Table_cc">
              <thead>
                <tr>
                  <th style="min-width:180px;">APPROVER</th>
                  <th style="min-width:170px;">CONTACT</th>
                  <th style="min-width:140px;">COMPANY</th>
                  <th style="min-width:80px;width:80px;text-align:center;">ACTION</th>
                </tr>
              </thead>
              <tbody>
                @foreach ([['data' => $cc_email_internal_data ?? [], 'source' => 'internal'], ['data' => $cc_email_client_data ?? [], 'source' => 'client']] as $group)
                    @foreach ($group['data'] as $teamVal)
                        @php
                            $teamName = trim((string) @$teamVal->name);
                            $initChar = strtoupper(substr($teamName, 0, 1) ?: 'U');
                            $charOrd = ord(strtolower($initChar));
                            $palettes = [
                                ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
                                ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0'],
                                ['bg' => '#fef3c7', 'text' => '#b45309', 'border' => '#fde68a'],
                                ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe'],
                                ['bg' => '#fdf2f8', 'text' => '#be185d', 'border' => '#fbcfe8'],
                                ['bg' => '#f0fdfa', 'text' => '#0f766e', 'border' => '#99f6e4'],
                            ];
                            $pal = $palettes[$charOrd % count($palettes)];
                            $roleText = trim((string) (@$teamVal->display_name ?: @$teamVal->role));
                        @endphp
                        <tr class="co-appr-row co-appr-row--{{ $group['source'] }} {{ $group['source'] === 'client' ? 'd-none' : '' }}"
                            data-search-name="{{ strtolower((string) @$teamVal->name) }}" data-search-email="{{ strtolower((string) @$teamVal->email) }}" data-search-company="{{ strtolower((string) @$teamVal->company_name) }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="co-appr-avatar" style="background:{{ $pal['bg'] }};color:{{ $pal['text'] }};border:1px solid {{ $pal['border'] }};">
                                        {{ $initChar }}
                                    </div>
                                    <div class="d-flex flex-column min-w-0">
                                        <span class="co-appr-name text-truncate">{{ ucwords(substr(@$teamVal->name, 0, 24)) }}</span>
                                        @if(!empty($roleText))
                                            <span class="co-appr-role-tag text-truncate">{{ ucwords(substr($roleText, 0, 25)) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="co-appr-contact">
                                    <svg class="co-appr-contact-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                    <span class="co-appr-email-text text-truncate">{{ @$teamVal->email }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="co-appr-company">
                                    <svg class="co-appr-company-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/>
                                    </svg>
                                    <span class="co-appr-company-text text-truncate">{{ @$teamVal->company_name ?: 'Internal Team' }}</span>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select co-appr-add-btn"
                                    rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    <span>Add</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="co-appr-row co-appr-empty-row co-appr-empty-row--internal {{ !empty($cc_email_internal_data) ? 'd-none' : '' }}">
                    <td colspan="4" class="co-appr-empty-cell">
                        <div class="co-appr-empty-inner">
                            <div class="co-appr-empty-ico"><i class="fas fa-users-slash"></i></div>
                            <div class="co-appr-empty-msg">No internal approvers found</div>
                        </div>
                    </td>
                </tr>
                <tr class="co-appr-row co-appr-empty-row co-appr-empty-row--client d-none">
                    <td colspan="4" class="co-appr-empty-cell">
                        <div class="co-appr-empty-inner">
                            <div class="co-appr-empty-ico"><i class="fas fa-users-slash"></i></div>
                            <div class="co-appr-empty-msg">No client approvers found</div>
                        </div>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
    <!--end::Team Content-->
  </div>
</div>




<div id="kt_activitiesIntTeam_to" class="co-appr-drawer">
  <div class="position-relative h-100 d-flex flex-column bg-white" id="kt__to_body">
    <!--begin::Team header (Rule 20 Anchor Background)-->
    <div id="kt_to_header" style="display:none;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:12px 20px;align-items:center;justify-content:space-between;min-height:56px;flex-shrink:0;">
        <div class="d-flex align-items-center gap-3">
            <div style="width:34px;height:34px;border-radius:8px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:center;font-size:13.5px;flex-shrink:0;">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <span class="fw-bold text-dark" style="font-family:'Poppins',sans-serif;font-size:14px;color:#0f172a;letter-spacing:-0.01em;">Select Approval</span>
                <div style="font-size:10.5px;color:#64748b;">Assign organization or client approvers to review and sign off</div>
            </div>
        </div>
        <button type="button" id="kt_activitiesIntTeam_close_to" class="co-drawer-topbar-close" aria-label="Close approver drawer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <!--end::Team header-->
    <!--begin::Team Content-->
    <div class="co-appr-picker flex-grow-1 overflow-auto p-4 p-md-5" id="kt_to_scroll">
        {{-- Tier 2 Sub-Tabs / Source Cards --}}
        <div class="co-appr-source-row">
            <div class="co-appr-source-card co-appr-source-card--active" data-source-for="to" data-source="internal">
                <input type="radio" name="apprSourceTo" value="internal" checked style="display:none;">
                <span class="co-appr-source-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/></svg>
                </span>
                <div class="co-appr-source-meta">
                    <div class="co-appr-source-title">Internal Company Approvers</div>
                    <div class="co-appr-source-sub"><span class="co-appr-count-pill">{{ count($cc_email_internal_data ?? []) }} available</span></div>
                </div>
                <div class="co-appr-source-check"><span class="co-appr-dot"></span></div>
            </div>
            @if(!empty($cc_email_client_data))
            <div class="co-appr-source-card" data-source-for="to" data-source="client">
                <input type="radio" name="apprSourceTo" value="client" style="display:none;">
                <span class="co-appr-source-ico">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <div class="co-appr-source-meta">
                    <div class="co-appr-source-title">Client Company Approvers</div>
                    <div class="co-appr-source-sub"><span class="co-appr-count-pill">{{ count($cc_email_client_data) }} available</span></div>
                </div>
                <div class="co-appr-source-check"><span class="co-appr-dot"></span></div>
            </div>
            @endif
        </div>

        {{-- Tier 1 Toolbar / Search Card --}}
        <div class="co-appr-toolbar">
            <div class="co-appr-searchbox">
                <svg class="co-appr-search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="co-appr-search-input" data-search-for="to" placeholder="Search by name, email or company...">
                <button type="button" class="co-appr-search-clear d-none" aria-label="Clear search">&times;</button>
            </div>
            <div class="co-appr-counter-badge">
                <span class="co-appr-active-count">{{ count($cc_email_internal_data ?? []) }}</span> approvers
            </div>
        </div>

        {{-- Tier 3 Table Card --}}
        <div class="co-appr-table-card">
            <table class="co-appr-table align-middle" id="Iteam_Table_to">
              <thead>
                <tr>
                  <th style="min-width:170px;">APPROVER</th>
                  <th style="min-width:160px;">CONTACT</th>
                  <th style="min-width:130px;">COMPANY</th>
                  <th style="min-width:125px;text-align:center;">STATUS</th>
                  <th style="min-width:90px;width:90px;text-align:center;">ACTION</th>
                </tr>
              </thead>
              <tbody>
                @foreach ([['data' => $cc_email_internal_data ?? [], 'source' => 'internal'], ['data' => $cc_email_client_data ?? [], 'source' => 'client']] as $group)
                    @foreach ($group['data'] as $teamVal)
                        @php
                            $teamName = trim((string) @$teamVal->name);
                            $initChar = strtoupper(substr($teamName, 0, 1) ?: 'U');
                            $charOrd = ord(strtolower($initChar));
                            $palettes = [
                                ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
                                ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0'],
                                ['bg' => '#fef3c7', 'text' => '#b45309', 'border' => '#fde68a'],
                                ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe'],
                                ['bg' => '#fdf2f8', 'text' => '#be185d', 'border' => '#fbcfe8'],
                                ['bg' => '#f0fdfa', 'text' => '#0f766e', 'border' => '#99f6e4'],
                            ];
                            $pal = $palettes[$charOrd % count($palettes)];
                            $roleText = trim((string) (@$teamVal->display_name ?: @$teamVal->role));
                            $approverEmailClean = strtolower(trim((string) @$teamVal->email));
                            $isAlreadyAdded = in_array($approverEmailClean, $already_added_approvers ?? []);
                        @endphp
                        <tr class="co-appr-row co-appr-row--{{ $group['source'] }} {{ $group['source'] === 'client' ? 'd-none' : '' }}"
                            data-search-name="{{ strtolower((string) @$teamVal->name) }}" data-search-email="{{ strtolower((string) @$teamVal->email) }}" data-search-company="{{ strtolower((string) @$teamVal->company_name) }}"
                            data-approver-email="{{ $approverEmailClean }}" data-row-id="{{ @$teamVal->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="co-appr-avatar" style="background:{{ $pal['bg'] }};color:{{ $pal['text'] }};border:1px solid {{ $pal['border'] }};">
                                        {{ $initChar }}
                                    </div>
                                    <div class="d-flex flex-column min-w-0">
                                        <span class="co-appr-name text-truncate">{{ ucwords(substr(@$teamVal->name, 0, 24)) }}</span>
                                        @if(!empty($roleText))
                                            <span class="co-appr-role-tag text-truncate">{{ ucwords(substr($roleText, 0, 25)) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="co-appr-contact">
                                    <svg class="co-appr-contact-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                    </svg>
                                    <span class="co-appr-email-text text-truncate">{{ @$teamVal->email }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="co-appr-company">
                                    <svg class="co-appr-company-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/>
                                    </svg>
                                    <span class="co-appr-company-text text-truncate">{{ @$teamVal->company_name ?: 'Internal Team' }}</span>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="co-appr-status-badge {{ $isAlreadyAdded ? 'co-appr-status--added' : 'co-appr-status--not-added' }}" id="co_appr_status_badge_{{ @$teamVal->id }}">
                                    @if($isAlreadyAdded)
                                        <i class="fas fa-check-circle me-1"></i> Already Added
                                    @else
                                        <i class="far fa-circle me-1"></i> Not Added
                                    @endif
                                </span>
                            </td>
                            <td style="text-align:center;">
                                {{-- Remove Button (shown when already added) --}}
                                <button type="button" class="to_remove co-appr-remove-btn {{ $isAlreadyAdded ? '' : 'd-none' }}"
                                    id="to_team_remove_{{ @$teamVal->id }}"
                                    rel="{{ @$teamVal->id }}"
                                    data-title="{{ @$teamVal->name }}"
                                    data-email="{{ @$teamVal->email }}"
                                    data-offerid="{{ $offerId }}">
                                    <i class="fas fa-trash-alt me-1 fs-9"></i> Remove
                                </button>
                                {{-- Add Button (shown when not yet added) --}}
                                <a id="to_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="to_select co-appr-add-btn {{ $isAlreadyAdded ? 'd-none' : '' }}"
                                    rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}" data-email="{{ @$teamVal->email }}">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    <span>Add</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="co-appr-row co-appr-empty-row co-appr-empty-row--internal {{ !empty($cc_email_internal_data) ? 'd-none' : '' }}">
                    <td colspan="5" class="co-appr-empty-cell">
                        <div class="co-appr-empty-inner">
                            <div class="co-appr-empty-ico"><i class="fas fa-users-slash"></i></div>
                            <div class="co-appr-empty-msg">No internal approvers found</div>
                        </div>
                    </td>
                </tr>
                <tr class="co-appr-row co-appr-empty-row co-appr-empty-row--client d-none">
                    <td colspan="5" class="co-appr-empty-cell">
                        <div class="co-appr-empty-inner">
                            <div class="co-appr-empty-ico"><i class="fas fa-users-slash"></i></div>
                            <div class="co-appr-empty-msg">No client approvers found</div>
                        </div>
                    </td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
    <!--end::Team Content-->
  </div>
</div>






<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>

<script>

// ══════════════════════════════════════════════════════════════════════════════
// 100% CLIENT-FIRST GOD-LEVEL EMAIL VALIDATION ENGINE (ZERO SERVER STRAIN)
// ══════════════════════════════════════════════════════════════════════════════
window.JeroEmailBoxEngine = {
    domainTypos: {
        'gamil.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaill.com': 'gmail.com', 'gmai.com': 'gmail.com',
        'gmial.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gamil.co': 'gmail.com', 'gmail.con': 'gmail.com',
        'gmail.co': 'gmail.com', 'gmaill.co': 'gmail.com', 'gmaill.con': 'gmail.com', 'gmail.cpm': 'gmail.com',
        'gmail.cm': 'gmail.com', 'gmail.om': 'gmail.com', 'g.mail.com': 'gmail.com',
        'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com', 'hotmaill.com': 'hotmail.com',
        'hotmial.co': 'hotmail.com', 'hotmail.con': 'hotmail.com', 'hotmsil.com': 'hotmail.com',
        'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'yaho.co': 'yahoo.com', 'yahoo.con': 'yahoo.com',
        'yaho.in': 'yahoo.in', 'yahooo.in': 'yahoo.in', 'yaho.co.in': 'yahoo.co.in',
        'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com', 'outlook.con': 'outlook.com',
        'outlok.co': 'outlook.com', 'outlock.com': 'outlook.com',
        'iclud.com': 'icloud.com', 'icoud.com': 'icloud.com', 'icloud.con': 'icloud.com'
    },
    cleanDeterministic: function(val) {
        if (!val || typeof val !== 'string') return '';
        var cleaned = val.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, ' ')
                         .trim()
                         .replace(/^mailto:\s*/i, '')
                         .replace(/^<+|>+$/g, '')
                         .replace(/^"+|"+$/g, '')
                         .replace(/^'+|'+$/g, '')
                         .replace(/[.,;:]+$/, '')
                         .trim();
        return cleaned;
    },
    validateSyntax: function(email) {
        if (!email || typeof email !== 'string') return false;
        var trimmed = email.trim();
        if (trimmed.length === 0 || trimmed.length > 254) return false;
        if ((trimmed.match(/@/g) || []).length !== 1) return false;
        var parts = trimmed.split('@');
        var local = parts[0];
        var domain = parts[1];
        if (!local || !domain) return false;
        if (local.length > 64) return false;
        if (local.startsWith('.') || local.endsWith('.') || local.indexOf('..') !== -1) return false;
        if (domain.startsWith('.') || domain.endsWith('.') || domain.startsWith('-') || domain.endsWith('-')) return false;
        if (domain.indexOf('..') !== -1) return false;
        var rfcPattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
        return rfcPattern.test(trimmed);
    },
    suggestCorrection: function(email) {
        if (!email || typeof email !== 'string') return null;
        var raw = email.trim();
        if (raw.indexOf(',') !== -1) {
            raw = raw.replace(/,/g, '.');
        }
        var atMatches = raw.match(/@/g) || [];
        if (atMatches.length !== 1) return null;
        var parts = raw.split('@');
        var local = parts[0].trim();
        var domain = (parts[1] || '').trim().toLowerCase();
        if (!local || !domain) return null;
        if (this.domainTypos[domain]) {
            return local + '@' + this.domainTypos[domain];
        }
        var tldFixes = {
            'con': 'com', 'cpm': 'com', 'comm': 'com', 'cmo': 'com', 'ocm': 'com',
            'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
        };
        var dParts = domain.split('.');
        if (dParts.length >= 2) {
            var last = dParts[dParts.length - 1];
            if (tldFixes[last]) {
                dParts[dParts.length - 1] = tldFixes[last];
                var suggestedDomain = dParts.join('.');
                if (suggestedDomain !== domain) {
                    return local + '@' + suggestedDomain;
                }
            }
        }
        return null;
    },
    renderTypoPill: function(containerSelector, suggestedEmail, targetInputSelector) {
        var $hint = $(containerSelector);
        if (!$hint.length) return;
        if (suggestedEmail) {
            $hint.html(
                '<div class="d-inline-flex align-items-center bg-light-primary text-primary px-2 py-1 rounded fs-8 fw-semibold mt-1">' +
                    '<i class="ki-outline ki-information-2 fs-7 me-1"></i>' +
                    '<span>Did you mean <strong>' + $('<div>').text(suggestedEmail).html() + '</strong>?</span>' +
                    '<button type="button" class="btn btn-xs btn-link text-primary fw-bold p-0 ms-2 text-decoration-underline apply-email-fix" data-target="' + targetInputSelector + '" data-suggestion="' + $('<div>').text(suggestedEmail).html() + '">Apply</button>' +
                '</div>'
            ).slideDown(150);
        } else {
            $hint.slideUp(100, function() { $(this).empty(); });
        }
    },
    setupEmailBox: function(inputSelector, hintSelector) {
        var self = this;
        var $input = $(inputSelector);
        if (!$input.length) return;

        $input.on('paste', function(e) {
            var pasted = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text') : '';
            if (pasted) {
                e.preventDefault();
                var cleaned = self.cleanDeterministic(pasted);
                var current = $input.val();
                if ($input.is('#cc_email_panel') && current) {
                    $input.val(current + ', ' + cleaned);
                } else {
                    $input.val(cleaned);
                }
                $input.trigger('input').trigger('change');
            }
        });

        $input.on('input', function() {
            var val = $(this).val();
            var parts = val.split(/[,;]/);
            var lastPart = parts[parts.length - 1].trim();
            var suggestion = self.suggestCorrection(lastPart);
            if (suggestion && suggestion.toLowerCase() !== lastPart.toLowerCase()) {
                self.renderTypoPill(hintSelector, suggestion, inputSelector);
            } else {
                $(hintSelector).slideUp(100, function() { $(this).empty(); });
            }
        });

        $input.on('blur', function() {
            var val = $(this).val();
            if (val) {
                var parts = val.split(/[,;]/).map(function(p) { return self.cleanDeterministic(p); }).filter(Boolean);
                $(this).val(parts.join(', '));
            }
        });
    }
};

$(document).ready(function() {
    window.JeroEmailBoxEngine.setupEmailBox('#to_email', '#to_email_typo_hint');
    window.JeroEmailBoxEngine.setupEmailBox('#cc_email_panel', '#cc_email_typo_hint');

    $(document).on('click', '.apply-email-fix', function(e) {
        e.preventDefault();
        var targetSel = $(this).attr('data-target');
        var suggestion = $(this).attr('data-suggestion');
        if (targetSel && suggestion) {
            var $tgt = $(targetSel);
            var val = $tgt.val();
            var parts = val.split(/[,;]/);
            if (parts.length > 1) {
                parts[parts.length - 1] = ' ' + suggestion;
                $tgt.val(parts.join(','));
            } else {
                $tgt.val(suggestion);
            }
            $(this).closest('.email-typo-hint-container').slideUp(100, function() { $(this).empty(); });
            $tgt.trigger('input').trigger('change');
        }
    });
});

$('#ck-editor__editable_inline').html("testing");

function validateEmailInput() {
    var raw = this.value;
    var cleaned = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
    if (cleaned.split(',').length > 1 || cleaned.split(';').length > 1) {
        alert('Please enter only one email address.');
        this.value = '';
        return;
    }
    if (cleaned && window.JeroEmailBoxEngine && !window.JeroEmailBoxEngine.validateSyntax(cleaned)) {
        var suggestion = window.JeroEmailBoxEngine.suggestCorrection(cleaned);
        if (suggestion) {
            window.JeroEmailBoxEngine.renderTypoPill('#to_email_typo_hint', suggestion, '#to_email');
        }
    }
}

function addEmailToList(rel) {
    let emailInput = document.getElementById('to_email').value.trim();
    // Simple validation to check if the email is valid
	var assign					=	$('#to_team_select_'+rel).attr("data-assign");
	var title					=	$('#to_team_select_'+rel).attr("data-title");
	var email					=	$('#to_team_select_'+rel).attr("data-email");
	
	$('.to_select').removeClass("co-appr-btn--remove");
	$('.to_select').text('Add');
	$('.to_select').attr("data-assign", 0);


    if (emailInput.split(',').length > 1 || emailInput.split(';').length > 1) {

        alert('Please enter only one email address.');
        document.getElementById('to_email').value = ''; // Clear the input

			$('#to_team_select_'+rel).removeClass("co-appr-btn--remove");
			$('#to_team_select_'+rel).text('Add');
			$('#to_team_select_'+rel).attr("data-assign", 0);

        }else{

				$('#to_team_select_'+rel).addClass("co-appr-btn--remove");
				$('#to_team_select_'+rel).text('Remove');
				$('#to_team_select_'+rel).attr("data-assign", 1);

	}
}

  // Attach event listeners (only for single-select compose form, not approval flow)
  @if((int) @$mtype !== 2)
	document.getElementById('to_email').addEventListener('input', validateEmailInput);
	document.getElementById('to_email').addEventListener('change', validateEmailInput);
  @endif

	$(document).on('click', '.to_select', function() {
		var rel = $(this).attr('rel');

		@if((int) @$mtype === 2)
		// Approval flow: "Add" sends the approval email immediately (1 at a time).
		// The compose form is hidden — template/subject/body are auto-populated on load.
		var $btn         = $(this);
		var approverEmail = $('#to_team_select_' + rel).attr('data-email');
		var approverName  = $('#to_team_select_' + rel).attr('data-title') || '';
		if (!approverEmail) return;

		var resetBtn = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> <span>Add</span>';
		$btn.html('<span><i class="fas fa-spinner fa-spin me-1"></i> Sending…</span>').css({'pointer-events':'none','opacity':'0.6'});

		var editorData = '';
		try { editorData = CKEDITOR.instances.editor1.getData(); } catch(e) {
		    editorData = $('#editor1').val() || '';
		}

		$.ajax({
		    type: 'POST',
		    url: "{{ route('service_company.cvtracker.fwd_offer_send') }}",
		    data: {
		        '_token':     "{{ csrf_token() }}",
		        'template':   $('#template').val()    || '',
		        'from_email': $('#from_email').val()  || '',
		        'to_email':   approverEmail,
		        'cc_email':   '',
		        'subject':    $('#subject').val()     || '',
		        'attached_cv': 0,
		        'editor1':    editorData,
		        'arr':        [],
		        'mtypeId':    8,
		        'offerId':    $('#offerId').val(),
		        'signature':  0
		    },
		    success: function(r) {
		        $btn.html(resetBtn).css({'pointer-events':'','opacity':'1'});
		        if (r.status === 'coinCheck') {
		            Swal.fire("Failed", r.message, "warning");
		            return;
		        }
		        if (r.status === 'alreadyExists') {
		            // Status update: approver already in workflow
		            $('#co_appr_status_badge_' + rel).removeClass('co-appr-status--not-added').addClass('co-appr-status--added').html('<i class="fas fa-check-circle me-1"></i> Already Added');
		            $btn.addClass('d-none');
		            $('#to_team_remove_' + rel).removeClass('d-none');
		            Swal.fire("Already Added", r.message, "info");
		            return;
		        }
		        if (r.status === 'rateLimited') {
		            Swal.fire("Already Sent", r.message, "info");
		            return;
		        }
		        if (r.status === 'success') {
		            // Mark approval as sent so "Save & Next" just advances
		            $('#step4').data('mgmt-status', 1);

		            // Update row UI to Already Added + Remove button
		            $('#co_appr_status_badge_' + rel).removeClass('co-appr-status--not-added').addClass('co-appr-status--added').html('<i class="fas fa-check-circle me-1"></i> Already Added');
		            $btn.addClass('d-none');
		            $('#to_team_remove_' + rel).removeClass('d-none');

		            // Update the step-4 timeline in real-time
		            if (typeof co_updateStep4Timeline === 'function') {
		                co_updateStep4Timeline(approverEmail, approverName);
		            }

		            Swal.fire({
		                icon: 'success',
		                title: 'Approver Added',
		                text: 'Approver has been added to the workflow.',
		                timer: 1200,
		                showConfirmButton: false
		            });
		        } else {
		            Swal.fire("Error", r.message || "Could not send approval email.", "error");
		        }
		    },
		    error: function() {
		        $btn.html(resetBtn).css({'pointer-events':'','opacity':'1'});
		        Swal.fire("Error", "Could not send approval email. Please try again.", "error");
		    }
		});
		@else
		addEmailToList(rel);
		@endif
	});

	// Approver Removal Handler (Select Approval Drawer)
	$(document).on('click', '.to_remove', function(e) {
	    e.preventDefault();
	    var rel = $(this).attr('rel');
	    var $btn = $(this);
	    var approverEmail = $btn.attr('data-email');
	    var offerId = $btn.attr('data-offerid') || $('#offerId').val();

	    if (!approverEmail || !offerId) return;

	    var resetRemoveBtn = '<i class="fas fa-trash-alt me-1 fs-9"></i> Remove';
	    $btn.html('<span><i class="fas fa-spinner fa-spin me-1"></i> Removing…</span>').css({'pointer-events':'none','opacity':'0.6'});

	    $.ajax({
	        type: 'POST',
	        url: "{{ route('service_company.cvtracker.remove_offer_approver') }}",
	        data: {
	            '_token': "{{ csrf_token() }}",
	            'offerId': offerId,
	            'email': approverEmail
	        },
	        success: function(r) {
	            $btn.html(resetRemoveBtn).css({'pointer-events':'','opacity':'1'});
	            if (r.status === 'success') {
	                // Update row UI to Not Added + Add button
	                $('#co_appr_status_badge_' + rel).removeClass('co-appr-status--added').addClass('co-appr-status--not-added').html('<i class="far fa-circle me-1"></i> Not Added');
	                $btn.addClass('d-none');
	                $('#to_team_select_' + rel).removeClass('d-none');

	                // Update Step 4 view in real-time
	                if (typeof co_removeStep4Approver === 'function') {
	                    co_removeStep4Approver(approverEmail, r.remaining_approvers);
	                }

	                Swal.fire({
	                    icon: 'success',
	                    title: 'Approver Removed',
	                    text: 'Approver removed from workflow.',
	                    timer: 1200,
	                    showConfirmButton: false
	                });
	            } else {
	                Swal.fire("Error", r.message || "Could not remove approver.", "error");
	            }
	        },
	        error: function() {
	            $btn.html(resetRemoveBtn).css({'pointer-events':'','opacity':'1'});
	            Swal.fire("Error", "Could not remove approver. Please try again.", "error");
	        }
	    });
	});

// document.getElementById('to_select').addEventListener('click', addEmailToList);




$('#template').change(function(){

	
		var template	=	$('#template').val();
		var to_email = $('#to_email').val();

		if(to_email=='' && template > 0){
					Swal.fire("Warning","Please first enter the To Email Address", "warning");	

					$('#template').prop('selectedIndex', 0).trigger('change');
			return false;
		}

		if(template > 0)
		{
						var id        	=	$('#template').val();
						var url       	=	"{{ route('service_company.cvtracker.email_template',':id') }}";
						url         	=	url.replace(':id', id);
						var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
						var token       =	"{{ csrf_token() }}";
						var to_email = $('#to_email').val();
						var from_email = $('#from_email').val();
						var offerId		=	$('#offerId').val();
						var mailtype = "{{ @$mtype==2 ? 8 : 7 }}";
						if(to_email=='' && mailtype == 8){
							to_email = 'info@jero.co.in';
							
						}else if(to_email=='' && mailtype == 7){
							to_email = 'info@jero.co.in';
						}
						
						var selectedApplicantListing	=	[];
						var jqueryarray 				=	<?php echo json_encode($applicationData); ?>;
						jqueryarray.map((mval, mid, marr)=>{
							selectedApplicantListing.push( mval.id);	
						});
	   
						$.ajax({
										type		: 'POST',
										url			: url,
										data		: {'_token': token, '_method': 'POST', 'template' : template, 'selectedApplicantListing': selectedApplicantListing,'to_email':to_email,'mailtype':mailtype,'offerId':offerId,'from_email':from_email},
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


var _btnSubmitEl = document.getElementById("btnSubmit");
if (_btnSubmitEl) _btnSubmitEl.addEventListener("click", ()=>{
	
		

		const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
		  	 buttonsStyling: false
		})
		// swalWithBootstrapButtons.fire({
		// 								  title				: 'Are you sure?',
		// 								  text				: "You won't be able to revert this!",
		// 								  icon				: 'warning',
		// 								  showCancelButton	: true,
		// 								  confirmButtonText	: 'Yes,  Forward Resume!',
		// 								  cancelButtonText	: 'No, cancel!',
		// 								  reverseButtons	: true 
		// 							}).then((result) => {
		//   if (result.isConfirmed) {
		      
		                          $('.indicator-progress').show();
		                          $('.indicator-label').hide();
		  
						 
									var id        	=	1;
									var url       	=	"{{ route('service_company.cvtracker.fwd_offer_send',':id') }}";
									url         	=	url.replace(':id', id);
									var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
									var token       =	"{{ csrf_token() }}";
									
									
									var arr = [];
									$('.chk_list_item:checked').each(function () {
											arr.push($(this).val());
									});
									
									var template	=	$('#template').val();
									var from_email	=	$('#from_email').val();
									var to_email	=	($('#to_email').val() || '').trim();
									var cc_email	=	($('#cc_email_panel').val() || '').trim();

									if (window.JeroEmailBoxEngine) {
										if (!to_email) {
											Swal.fire("Warning", "Please enter a valid recipient email address.", "warning");
											$('#to_email').focus();
											$('.indicator-progress').hide();
											$('.indicator-label').show();
											return false;
										}
										var toParts = to_email.split(/[,;]/).map(function(s) { return window.JeroEmailBoxEngine.cleanDeterministic(s); }).filter(Boolean);
										for (var ti = 0; ti < toParts.length; ti++) {
											if (!window.JeroEmailBoxEngine.validateSyntax(toParts[ti])) {
												var sugg = window.JeroEmailBoxEngine.suggestCorrection(toParts[ti]);
												if (sugg) {
													window.JeroEmailBoxEngine.renderTypoPill('#to_email_typo_hint', sugg, '#to_email');
												}
												Swal.fire("Invalid Email", "The recipient email address " + toParts[ti] + " is invalid.", "warning");
												$('#to_email').focus();
												$('.indicator-progress').hide();
												$('.indicator-label').show();
												return false;
											}
										}
										if (cc_email) {
											var ccParts = cc_email.split(/[,;]/).map(function(s) { return window.JeroEmailBoxEngine.cleanDeterministic(s); }).filter(Boolean);
											for (var ci = 0; ci < ccParts.length; ci++) {
												if (!window.JeroEmailBoxEngine.validateSyntax(ccParts[ci])) {
													var csugg = window.JeroEmailBoxEngine.suggestCorrection(ccParts[ci]);
													if (csugg) {
														window.JeroEmailBoxEngine.renderTypoPill('#cc_email_typo_hint', csugg, '#cc_email_panel');
													}
													Swal.fire("Invalid CC Email", "The CC email address " + ccParts[ci] + " is invalid.", "warning");
													$('#cc_email_panel').focus();
													$('.indicator-progress').hide();
													$('.indicator-label').show();
													return false;
												}
											}
										}
									}
									var subject		=	$('#subject').val();
                                    var mtypeId		=	$('#mtypeId').val();
                                    var offerId		=	$('#offerId').val();
									var attached_cv	=	0;
									var signature = $('input[name="signature"]:checked').val();
									var editor1		= CKEDITOR.instances.editor1.getData(); //$('#editor1').val();

									 
						
									$.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'template' : template, 'from_email' : from_email, 'to_email' : to_email, 'cc_email' : cc_email, 'subject' : subject, 'attached_cv': attached_cv, 'editor1':editor1, 'arr':arr,'mtypeId':mtypeId,'offerId':offerId,'signature':signature},
													beforeSend	: function() {
																				// $('#main_container').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
													    $('.indicator-progress').hide();
		                                                 $('.indicator-label').show();

														 if (response.status == 'coinCheck') {
		
																Swal.fire("Failed",` ${response.message}` , "warning").then(function(){
																	$(".btn-active-color-primary").click();
																	});		
																	
																	return false;	
															}

																	if (response.status == "success") {
																		  
																		//   $('#main_container').html(response.view);
																		  swalWithBootstrapButtons.fire(
																										  'Success!',
																										  'Selected Applicant Forworded.',
																										  'success'
																										)

																										location.reload();										
																		  
																	}
														}
										});						 
						 
						 
						 
			
		//   } 
		//   else if (result.dismiss === Swal.DismissReason.cancel) {
		//                           $('.indicator-progress').hide();
		//                           $('.indicator-label').show();
		// 				swalWithBootstrapButtons.fire(
		// 												 	'Cancelled',
		// 													'Selected request is safe :)',
		// 													'error'
		// 												)
		//   }
		// })		
		
		

});


 $('.cp_select').on('click', function(e) {
   			
			
			var rel						= 	$(this).attr('rel');
			var assign					=	$('#team_select_'+rel).attr("data-assign");
			var title					=	$('#team_select_'+rel).attr("data-title");
			var email					=	$('#team_select_'+rel).attr("data-email");
			
			if(assign ==0)
			{
				$(this).addClass("co-appr-btn--remove");
				$(this).text('Remove');
				$(this).attr("data-assign", 1);
				AddEvent(title, email);
			}
			else if(assign ==1)
			{
				$(this).removeClass("co-appr-btn--remove");
				$(this).text('Add');
				$(this).attr("data-assign", 0);
				DeleteEvent(title, email);
			}
			
	});
	
	
	// Note: .to_select click is handled by the delegated $(document).on above,
	// which supports both the approval multi-select (mtype=2) and the legacy single-select flow.
	
	

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

// ── Approver picker drawer open/close ──
// Self-contained: does not rely on Metronic's KTDrawer (this HTML is injected into the
// modal via AJAX after KTDrawer.init() already ran on initial page load, so its declarative
// data-kt-drawer-toggle wiring never picks these elements up).
function coApprOpenDrawer(which) {
	var $drawer = which === 'cc' ? $('#kt_activitiesIntTeam_cc') : $('#kt_activitiesIntTeam_to');
	$drawer.addClass('co-appr-drawer--open');
	$('#co_appr_drawer_backdrop').addClass('co-appr-drawer-backdrop--open');
}
function coApprCloseDrawers() {
	$('.co-appr-drawer').removeClass('co-appr-drawer--open');
	$('#co_appr_drawer_backdrop').removeClass('co-appr-drawer-backdrop--open');
}
$(document).on('click', '[data-co-appr-open]', function(e) {
	e.preventDefault();
	coApprOpenDrawer($(this).data('co-appr-open'));
});
$(document).on('click', '#kt_activitiesIntTeam_close_cc, #kt_activitiesIntTeam_close_to, #co_appr_drawer_backdrop', function(e) {
	e.preventDefault();
	coApprCloseDrawers();
});

// ── Approver source toggle (Internal / Client) + search filter ──
// Purely presentational: it only shows/hides rows already rendered server-side from real
// data (cc_email_internal_data / cc_email_client_data). The existing Add/Remove wiring
// (cp_select / to_select, AddEvent*/DeleteEvent*) above is untouched.
$(document).on('click', '.co-appr-source-card', function() {
	var drawer = $(this).data('source-for'); // "cc" or "to"
	var source = $(this).data('source'); // "internal" or "client"
	var $scope = $(this).closest('.co-appr-picker');
	$scope.find('.co-appr-source-card[data-source-for="' + drawer + '"]').removeClass('co-appr-source-card--active');
	$(this).addClass('co-appr-source-card--active');
	$scope.find('.co-appr-row').addClass('d-none');
	$scope.find('.co-appr-row--' + source).removeClass('d-none');
	$scope.find('.co-appr-empty-row').addClass('d-none');
	if (source === 'internal') {
		$scope.find('.co-appr-empty-row--internal').each(function() {
			if ($scope.find('.co-appr-row--internal:not(.co-appr-empty-row)').length === 0) { $(this).removeClass('d-none'); }
		});
	} else {
		$scope.find('.co-appr-empty-row--client').each(function() {
			if ($scope.find('.co-appr-row--client:not(.co-appr-empty-row)').length === 0) { $(this).removeClass('d-none'); }
		});
	}
	// Re-apply the current search term to whichever source is now visible
	$scope.find('.co-appr-search-input').trigger('input');
});

$(document).on('click', '.co-appr-search-clear', function(e) {
	e.preventDefault();
	var $box = $(this).closest('.co-appr-searchbox');
	var $input = $box.find('.co-appr-search-input');
	$input.val('').trigger('input').focus();
	$(this).addClass('d-none');
});

$(document).on('input', '.co-appr-search-input', function() {
	var drawer = $(this).data('search-for'); // "cc" or "to"
	var q = $(this).val().trim().toLowerCase();
	var $scope = $(this).closest('.co-appr-picker');
	var activeSource = $scope.find('.co-appr-source-card--active').data('source') || 'internal';
	var $clearBtn = $(this).siblings('.co-appr-search-clear');
	if ($clearBtn.length) {
		$clearBtn.toggleClass('d-none', q === '');
	}
	var visibleCount = 0;
	$scope.find('.co-appr-row--' + activeSource + ':not(.co-appr-empty-row)').each(function() {
		var $row = $(this);
		var hay = ($row.data('search-name') + ' ' + $row.data('search-email') + ' ' + $row.data('search-company'));
		var matches = (q === '' || hay.indexOf(q) !== -1);
		$row.toggleClass('d-none', !matches);
		if (matches) visibleCount++;
	});
	$scope.find('.co-appr-active-count').text(visibleCount);
	$scope.find('.co-appr-empty-row--' + activeSource).toggleClass('d-none', visibleCount > 0);
});

@if((int) @$mtype === 2)
// For the approval flow (mtype=2) auto-open the approver picker immediately so
// the user does not need to click a second "+ Add Approver" button inside the form.
coApprOpenDrawer('to');

// Auto-load approval template so subject/body are ready before "Add" fires.
// The template change handler above fills #subject and CKEditor via AJAX.
@if(!empty($_approvalTpl))
$(function() {
    setTimeout(function() {
        var tplId    = '{{ @$_approvalTpl->id }}';
        var offerId  = $('#offerId').val(); 
        var fromEmail = '{{ $userdata->email }}';
        var appIds   = <?php echo json_encode(collect($applicationData ?? [])->pluck('id')->toArray()); ?>;
        var url      = "{{ route('service_company.cvtracker.email_template',':id') }}".replace(':id', tplId);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                '_token': "{{ csrf_token() }}",
                'template': tplId,
                'selectedApplicantListing': appIds,
                'to_email': fromEmail,
                'from_email': fromEmail,
                'mailtype': 8,
                'offerId': offerId
            },
            success: function(res) {
                if (res.status === 'success') {
                    $('#subject').val(res.subject || '');
                    var body = res.template_description || '';
                    try { CKEDITOR.instances.editor1.setData(body); } catch(e) {}
                    $('#editor1').val(body);
                }
            }
        });
    }, 800);
});
@endif
@endif

</script>
<script>
	
	// $.noConflict();
	
@if((int) @$mtype !== 2)
	var from_email	=	document.querySelector('#from_email');
	if (from_email) new Tagify(from_email);

	var cc_email	=	document.querySelector('#cc_email');
	if (cc_email) new Tagify(cc_email);
@endif
		 
</script>

