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

/* ── Approver recipient picker (Internal Company Approvers / Client Company Approvers) ──
   Scoped to .co-appr-* so it doesn't affect the rest of this shared email-composer modal.
   Reuses the exact same underlying data + Add/Remove wiring — this only changes presentation. */
.co-appr-picker, .co-appr-picker * { font-family: 'Poppins', sans-serif; }
.co-appr-picker { font-size: 12px; }
.co-appr-source-row { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
.co-appr-source-card {
    flex: 1 1 220px; display: flex; align-items: center; gap: 10px; cursor: pointer;
    border: 1px solid #e4e6ef; border-radius: 10px; padding: 10px 12px; background: #fff;
}
.co-appr-source-card input { margin: 0 6px 0 0; }
.co-appr-source-card--active { border-color: #1b84ff; background: #f3f8ff; }
.co-appr-source-ico { width: 30px; height: 30px; border-radius: 8px; background: #eef3ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.co-appr-source-ico svg { width: 16px; height: 16px; }
.co-appr-source-title { font-size: 12px; font-weight: 700; color: #181c32; }
.co-appr-source-sub { font-size: 10px; color: #7e8299; margin-top: 1px; }
.co-appr-search { margin-bottom: 10px; }
.co-appr-search input { height: 34px; font-size: 12px; }
.co-appr-empty { font-size: 11px; color: #a1a5b7; text-align: center; padding: 14px 4px; }
/* Action buttons in the approver/CC picker tables — explicit styling so they always render
   in the AJAX-injected context regardless of Metronic CSS loading order. */
.co-appr-add-btn {
    display: inline-block; padding: 4px 14px; font-size: 11.5px; font-weight: 600;
    color: #1b84ff; background: #e9f3ff; border: 1px solid #b3d5ff;
    border-radius: 6px; cursor: pointer; text-decoration: none; white-space: nowrap;
    transition: background .15s, color .15s;
}
.co-appr-add-btn:hover { background: #1b84ff; color: #fff; }
.co-appr-add-btn.co-appr-btn--remove { color: #f1416c; background: #fff0f3; border-color: #f8b4c4; }
.co-appr-add-btn.co-appr-btn--remove:hover { background: #f1416c; color: #fff; }

/* Self-contained sliding drawer — driven entirely by our own JS/CSS classes below, not
   Metronic's KTDrawer plugin (which never re-scans HTML injected into the modal via AJAX,
   so its data-kt-drawer-toggle wiring silently did nothing — that was the "drawer won't
   open" bug).
   position:absolute (not fixed) anchored to #application_fwd_offer_body_content, which the
   modal markup already gives `position-relative`. Bootstrap's modal applies a CSS transform
   to .modal-dialog for its open/close animation, and any transformed ancestor becomes the
   containing block for position:fixed descendants — so a fixed drawer here renders relative
   to the (small, mid-page) modal box instead of the viewport and ends up invisible/off-screen.
   Anchoring to the already-positioned modal body sidesteps that entirely. */
.co-appr-drawer {
    position: absolute !important; top: 0 !important; bottom: 0 !important; right: -100% !important; left: auto !important;
    width: 100% !important; height: 100% !important; background: #fff !important;
    box-shadow: -6px 0 24px rgba(24,28,50,.18); z-index: 2147483000 !important;
    transition: right .25s ease; overflow-y: auto; display: block !important;
}
.co-appr-drawer.co-appr-drawer--open { right: 0 !important; }
.co-appr-drawer-backdrop {
    display: none; position: absolute !important; inset: 0; background: rgba(24,28,50,.35);
    z-index: 2147482999 !important;
}
.co-appr-drawer-backdrop.co-appr-drawer-backdrop--open { display: block !important; }

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
      <input type="hidden" id="from_email" name="from_email" value="{{ $userdata->email }}">
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
            <input class="form-control form-control-solid" name="from_email" id="from_email"  value="{{ $userdata->email }}" readonly="" data-role="from_email" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <!--end::From email-->
        <!--begin::To email-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bold"><span class="co-so-licon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.2"/><path d="M5 20a7 7 0 0 1 14 0"/></svg></span>To</label>
          <div class="col-lg-10 col-xl-10" style="position: relative;">
            <input type="hidden" id="mtypeId" name="mtype" value="{{ $mtype }}">
            <input type="hidden" id="offerId" name="offerId" value="{{ $offerId }}">
            <input class="form-control form-control-solid" name="to_email" id="to_email"  value="{{ (@$mtype==1) ? @$toEmailId : '' }}" >
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

            <input class="form-control form-control-solid" name="cc_email_panel" id="cc_email_panel"  value="">
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
  <div class="card-body position-relative px-0 py-0" id="kt__cc_body">
  <div id="kt_cc_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__cc_body" data-kt-scroll-dependencies="#kt__cc_header, #kt__cc_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div id="kt__cc_header" style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid #e4e6ef;background:#fff;min-height:48px;">
          <span class="fw-bold text-dark" style="font-size:13px;">Select Approver (CC)</span>
          <button type="button" id="kt_activitiesIntTeam_close_cc" class="btn btn-sm btn-icon btn-active-light-danger" style="width:30px;height:30px;flex-shrink:0;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
      </div>
      <!--end::Team header-->
      <!--begin::Team Content-->
      <div class="card-body py-7 co-appr-picker">
        {{-- Choose Approver Source: internal team always available; client team only shown
             when this job actually has a client (i.e. it's not an internal-only job). --}}
        <div class="co-appr-source-row">
            <label class="co-appr-source-card co-appr-source-card--active" data-source-for="cc" data-source="internal">
                <input type="radio" name="apprSourceCc" value="internal" checked>
                <span class="co-appr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/></svg></span>
                <div>
                    <div class="co-appr-source-title">Internal Company Approvers</div>
                    <div class="co-appr-source-sub">{{ count($cc_email_internal_data ?? []) }} available</div>
                </div>
            </label>
            @if(!empty($cc_email_client_data))
            <label class="co-appr-source-card" data-source-for="cc" data-source="client">
                <input type="radio" name="apprSourceCc" value="client">
                <span class="co-appr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                <div>
                    <div class="co-appr-source-title">Client Company Approvers</div>
                    <div class="co-appr-source-sub">{{ count($cc_email_client_data) }} available</div>
                </div>
            </label>
            @endif
        </div>
        <div class="co-appr-search">
            <input type="text" class="form-control form-control-solid co-appr-search-input" data-search-for="cc" placeholder="Search by name, email or company...">
        </div>
        <!--begin::Table-->
        <div style="overflow-x:auto;">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_cc">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th style="min-width:140px;">Approver</th>
              <th style="min-width:160px;">Contact</th>
              <th style="min-width:120px;">Company</th>
              <th style="min-width:70px;width:70px;">Action</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold">
            @foreach ([['data' => $cc_email_internal_data ?? [], 'source' => 'internal'], ['data' => $cc_email_client_data ?? [], 'source' => 'client']] as $group)
                @foreach ($group['data'] as $teamVal)
                    @php
                        $bgTextDecore = $strVal = '';
                        $str = trim(@$teamVal->name);
                        $str = $strVal = strtolower($str[0] ?? 'c');
                        $str = ord($str);
                        if ($str >= 97 && $str <= 101) { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                        else if ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                        else if ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                        else if ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
                        else if ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
                        else { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }
                    @endphp
                    <tr class="co-appr-row co-appr-row--{{ $group['source'] }} {{ $group['source'] === 'client' ? 'd-none' : '' }}"
                        data-search-name="{{ strtolower((string) @$teamVal->name) }}" data-search-email="{{ strtolower((string) @$teamVal->email) }}" data-search-company="{{ strtolower((string) @$teamVal->company_name) }}">
                        <!--begin::Name-->
                        <td><div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                                <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                                </a> </div>
                            <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 20)) }}</span> <span class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 25)) }}</span> </div>
                        </div></td>
                        <!--end::Name-->
                        <td><div class="d-flex flex-column">{{ @$teamVal->email }}</div></td>
                        <td><div class="d-flex flex-column">{{ @$teamVal->company_name }}</div></td>
                        <!--begin::Action-->
                        <td><div class="d-flex flex-column"> <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select co-appr-add-btn"
                            rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
                        <!--end::Action-->
                    </tr>
                @endforeach
            @endforeach
            <tr class="co-appr-row co-appr-row--internal co-appr-empty-row {{ !empty($cc_email_internal_data) ? 'd-none' : '' }}"><td colspan="4" class="co-appr-empty">No internal approvers found.</td></tr>
          </tbody>
          <!--end::Table body-->
        </table>
        </div>
        <!--end::Table-->
      </div>
      <!--end::Team Content-->
    </div>
    <!--end::Team card-->
  </div>
</div>
</div>



<div id="kt_activitiesIntTeam_to" class="co-appr-drawer">
  <div class="card-body position-relative px-0 py-0" id="kt__to_body">
  <div id="kt_to_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__to_body" data-kt-scroll-dependencies="#kt__to_header, #kt__to_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div id="kt_to_header" style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid #e4e6ef;background:#fff;min-height:48px;">
          <span class="fw-bold text-dark" style="font-size:13px;">Select Approver</span>
          <button type="button" id="kt_activitiesIntTeam_close_to" class="btn btn-sm btn-icon btn-active-light-danger" style="width:30px;height:30px;flex-shrink:0;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
      </div>
      <!--end::Team header-->
      <!--begin::Team Content-->
      <div class="card-body py-7 co-appr-picker">
        <div class="co-appr-source-row">
            <label class="co-appr-source-card co-appr-source-card--active" data-source-for="to" data-source="internal">
                <input type="radio" name="apprSourceTo" value="internal" checked>
                <span class="co-appr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-6h4v6"/></svg></span>
                <div>
                    <div class="co-appr-source-title">Internal Company Approvers</div>
                    <div class="co-appr-source-sub">{{ count($cc_email_internal_data ?? []) }} available</div>
                </div>
            </label>
            @if(!empty($cc_email_client_data))
            <label class="co-appr-source-card" data-source-for="to" data-source="client">
                <input type="radio" name="apprSourceTo" value="client">
                <span class="co-appr-source-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                <div>
                    <div class="co-appr-source-title">Client Company Approvers</div>
                    <div class="co-appr-source-sub">{{ count($cc_email_client_data) }} available</div>
                </div>
            </label>
            @endif
        </div>
        <div class="co-appr-search">
            <input type="text" class="form-control form-control-solid co-appr-search-input" data-search-for="to" placeholder="Search by name, email or company...">
        </div>
        <!--begin::Table-->
        <div style="overflow-x:auto;">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_to">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th style="min-width:140px;">Approver</th>
              <th style="min-width:160px;">Contact</th>
              <th style="min-width:120px;">Company</th>
              <th style="min-width:70px;width:70px;">Action</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold">
            @foreach ([['data' => $cc_email_internal_data ?? [], 'source' => 'internal'], ['data' => $cc_email_client_data ?? [], 'source' => 'client']] as $group)
                @foreach ($group['data'] as $teamVal)
                    @php
                        $bgTextDecore = $strVal = '';
                        $str = trim(@$teamVal->name);
                        $str = $strVal = strtolower($str[0] ?? 'c');
                        $str = ord($str);
                        if ($str >= 97 && $str <= 101) { $bgTextDecore = 'fs-3 bg-light-danger text-danger'; }
                        else if ($str >= 102 && $str <= 106) { $bgTextDecore = 'fs-3 bg-light-warning text-warning'; }
                        else if ($str >= 107 && $str <= 111) { $bgTextDecore = 'fs-3 bg-light-primary text-primary'; }
                        else if ($str >= 112 && $str <= 116) { $bgTextDecore = 'fs-3 bg-light-success text-success'; }
                        else if ($str >= 117 && $str <= 121) { $bgTextDecore = 'fs-3 bg-dark text-secondary'; }
                        else { $bgTextDecore = 'fs-3 bg-light-secondary text-dark'; }
                    @endphp
                    <tr class="co-appr-row co-appr-row--{{ $group['source'] }} {{ $group['source'] === 'client' ? 'd-none' : '' }}"
                        data-search-name="{{ strtolower((string) @$teamVal->name) }}" data-search-email="{{ strtolower((string) @$teamVal->email) }}" data-search-company="{{ strtolower((string) @$teamVal->company_name) }}">
                        <!--begin::Name-->
                        <td><div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);" class="">
                                <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                                </a> </div>
                            <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">{{ ucwords(substr(@$teamVal->name, 0, 20)) }}</span> <span class="text-muted fw-bold d-block fs-7">{{ ucwords(substr(@$teamVal->display_name, 0, 25)) }}</span> </div>
                        </div></td>
                        <!--end::Name-->
                        <td><div class="d-flex flex-column">{{ @$teamVal->email }}</div></td>
                        <td><div class="d-flex flex-column">{{ @$teamVal->company_name }}</div></td>
                        <!--begin::Action-->
                        <td><div class="d-flex flex-column"> <a id="to_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="to_select co-appr-add-btn"
                            rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
                        <!--end::Action-->
                    </tr>
                @endforeach
            @endforeach
            <tr class="co-appr-row co-appr-row--internal co-appr-empty-row {{ !empty($cc_email_internal_data) ? 'd-none' : '' }}"><td colspan="4" class="co-appr-empty">No internal approvers found.</td></tr>
          </tbody>
          <!--end::Table body-->
        </table>
        </div>
        <!--end::Table-->
      </div>
      <!--end::Team Content-->
    </div>
    <!--end::Team card-->
  </div>
</div>
</div>






<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>

<script>

$('#ck-editor__editable_inline').html("testing");



function validateEmailInput() {
    let emailInput = this.value.trim();
    
    // Check if there's more than one email
    if (emailInput.split(',').length > 1 || emailInput.split(';').length > 1) {
        alert('Please enter only one email address.');
        this.value = ''; // Clear the input
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

		$btn.text('Sending…').css({'pointer-events':'none','opacity':'0.6'});

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
		        if (r.status === 'coinCheck') {
		            $btn.text('Add').css({'pointer-events':'','opacity':'1'});
		            Swal.fire("Failed", r.message, "warning");
		            return;
		        }
		        if (r.status === 'rateLimited') {
		            $btn.text('Add').css({'pointer-events':'','opacity':'1'});
		            Swal.fire("Already Sent", r.message, "info");
		            return;
		        }
		        if (r.status === 'success') {
		            // Mark approval as sent so "Save & Next" just advances
		            $('#step4').data('mgmt-status', 1);
		            $btn.text('Sent ✓').addClass('co-appr-btn--remove').css({'pointer-events':'none','opacity':'0.7'});
		            // Update the step-4 timeline
		            if (typeof co_updateStep4Timeline === 'function') {
		                co_updateStep4Timeline(approverEmail, approverName);
		            }
		            // Close inner pickers
		            coApprCloseDrawers();
		            // Close outer approval drawer (direct DOM — bypasses any reference issues)
		            $('#kt_fwd_offer_drawer').removeClass('drawer-on');
		            $('#kt_fwd_offer_overlay').removeClass('active');
		            $('body').css('overflow', '');
		        } else {
		            $btn.text('Add').css({'pointer-events':'','opacity':'1'});
		            Swal.fire("Error", r.message || "Could not send approval email.", "error");
		        }
		    },
		    error: function() {
		        $btn.text('Add').css({'pointer-events':'','opacity':'1'});
		        Swal.fire("Error", "Could not send approval email. Please try again.", "error");
		    }
		});
		@else
		addEmailToList(rel);
		@endif
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
									var to_email	=	$('#to_email').val();
									var cc_email	=	$('#cc_email_panel').val();
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
		$scope.find('.co-appr-row--internal.co-appr-empty-row').each(function() {
			if ($scope.find('.co-appr-row--internal:not(.co-appr-empty-row)').length === 0) { $(this).removeClass('d-none'); }
		});
	}
	// Re-apply the current search term to whichever source is now visible
	$scope.find('.co-appr-search-input').trigger('input');
});

$(document).on('input', '.co-appr-search-input', function() {
	var drawer = $(this).data('search-for'); // "cc" or "to"
	var q = $(this).val().trim().toLowerCase();
	var $scope = $(this).closest('.co-appr-picker');
	var activeSource = $scope.find('.co-appr-source-card--active').data('source') || 'internal';
	$scope.find('.co-appr-row--' + activeSource + ':not(.co-appr-empty-row)').each(function() {
		var $row = $(this);
		var hay = ($row.data('search-name') + ' ' + $row.data('search-email') + ' ' + $row.data('search-company'));
		$row.toggleClass('d-none', q !== '' && hay.indexOf(q) === -1);
	});
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

