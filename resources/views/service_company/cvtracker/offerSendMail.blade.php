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

/* Self-contained sliding drawer — driven entirely by our own JS/CSS classes below, not
   Metronic's KTDrawer plugin (which never re-scans HTML injected into the modal via AJAX,
   so its data-kt-drawer-toggle wiring silently did nothing — that was the "drawer won't
   open" bug). */
.co-appr-drawer {
    position: fixed; top: 0; right: -420px; width: 400px; max-width: 92vw; height: 100vh;
    background: #fff; box-shadow: -6px 0 24px rgba(24,28,50,.18); z-index: 1200;
    transition: right .25s ease; overflow-y: auto;
}
.co-appr-drawer.co-appr-drawer--open { right: 0; }
.co-appr-drawer-backdrop {
    display: none; position: fixed; inset: 0; background: rgba(24,28,50,.35); z-index: 1199;
}
.co-appr-drawer-backdrop.co-appr-drawer-backdrop--open { display: block; }
</style>
<div class="co-appr-drawer-backdrop" id="co_appr_drawer_backdrop"></div>
<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>


<form id="form1">
  <!--begin::Modal body-->
  <div class="modal-body scroll-y py-0 px-lg-7">
    <div data-kt-element="options">
      <div class="py-5">
        <!--begin::Template-->
       
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">From</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="from_email" id="from_email"  value="{{ $userdata->email }}" readonly="" data-role="from_email" >
            <span class="form-text text-muted">Please enter email</span> </div>
        </div>
        <!--end::From email-->
        <!--begin::To email-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">To</label>
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
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">CC</label>
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
			<label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Signature</label>
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
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Template</label>
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
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Subject</label>
          <div class="col-lg-10 col-xl-10">
            <input class="form-control form-control-solid" name="subject" id="subject" placeholder="Enter subject" value="" >
            <span class="form-text text-muted">Please enter subject</span> </div>
        </div>
        <!--end::Subject-->
        <!--begin::Body-->
        <div class="form-group row mb-4">
          <label class="col-xl-2 col-lg-2 col-form-label text-dark fw-bolder fs-5">Body</label>
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
  <div class="modal-footer flex-center">
    <div class="div1">
      <!--begin::Cancel-->
      <button type="close" id="" class="btn btn-light-dark me-3" data-dismiss="modal">Cancel</button>
      <!--begin::Send-->
      <button type="button" class="btn btn-light-primary" name="btnSubmit" id="btnSubmit" value="btnSubmit"> <span class="indicator-label">Send</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span> </button>
      <!--end::Send-->
    </div>
    <!--<span style="position: absolute;right: 2%;"></span>-->
    <div class="div2 position-absolute end-0 me-5 d-none"> </div>
  </div>
  <!--end::Modal footer-->
  <!--</form>-->
  </div>
</form>




<div id="kt_activitiesIntTeam_cc" class="bg-body co-appr-drawer" data-kt-drawer-name="activities">
  <div class="card-body position-relative px-0 py-0" id="kt__cc_body">
  <div id="kt_cc_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__cc_body" data-kt-scroll-dependencies="#kt__cc_header, #kt__cc_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div class="card-header px-5 py-0" id="kt__cc_header">
          <div class="d-flex align-items-center py-1">
            <div class=" d-flex align-items-center"> 
              <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light__cc_name">Select Approver</a> </div>
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
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_cc">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="min-w-200px">Approver</th>
              <th class="min-w-200px">Contact</th>
              <th class="min-w-200px">Company</th>
              <th class="min-w-100px">Action</th>
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
                        <td><div class="d-flex flex-column"> <a id="team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="cp_select btn btn-sm btn-light-primary w-130"
                            rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
                        <!--end::Action-->
                    </tr>
                @endforeach
            @endforeach
            <tr class="co-appr-row co-appr-row--internal co-appr-empty-row {{ !empty($cc_email_internal_data) ? 'd-none' : '' }}"><td colspan="4" class="co-appr-empty">No internal approvers found.</td></tr>
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



<div id="kt_activitiesIntTeam_to" class="bg-body co-appr-drawer" data-kt-drawer-name="activities">
  <div class="card-body position-relative px-0 py-0" id="kt__to_body">
  <div id="kt_to_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt__to_body" data-kt-scroll-dependencies="#kt__to_header, #kt__to_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::Team card-->
    <div class="card card-custom mx-2">
      <!--begin::Team header-->
      <div class="card-header px-5 py-0" id="kt_to_header">
          <div class="d-flex align-items-center py-1">
              <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" id="light_to_name">Select Approver</a> </div>
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
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="Iteam_Table_to">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="min-w-200px">Approver</th>
              <th class="min-w-200px">Contact</th>
              <th class="min-w-200px">Company</th>
              <th class="min-w-100px">Action</th>
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
                        <td><div class="d-flex flex-column"> <a id="to_team_select_{{ @$teamVal->id }}" href="javascript:void(0);" class="to_select btn btn-sm btn-light-primary w-130"
                            rel="{{ @$teamVal->id }}" data-assign="0" data-title="{{ @$teamVal->name }}"  data-email="{{ @$teamVal->email }}"  >Add</a> </div></td>
                        <!--end::Action-->
                    </tr>
                @endforeach
            @endforeach
            <tr class="co-appr-row co-appr-row--internal co-appr-empty-row {{ !empty($cc_email_internal_data) ? 'd-none' : '' }}"><td colspan="4" class="co-appr-empty">No internal approvers found.</td></tr>
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
	
	$('.to_select').addClass("btn-light-primary");
	$('.to_select').removeClass("btn-light-danger");
	$('.to_select').text('Add');
	$('.to_select').attr("data-assign", 0);


    if (emailInput.split(',').length > 1 || emailInput.split(';').length > 1) {
		
        alert('Please enter only one email address.');
        document.getElementById('to_email').value = ''; // Clear the input
		
			$('#to_team_select_'+rel).addClass("btn-light-primary");
			$('#to_team_select_'+rel).removeClass("btn-light-danger");
			$('#to_team_select_'+rel).text('Add');
			$('#to_team_select_'+rel).attr("data-assign", 0);	
			
        }else{
		
				$('#to_team_select_'+rel).addClass("btn-light-danger");
				$('#to_team_select_'+rel).removeClass("btn-light-primary");
				$('#to_team_select_'+rel).text('Remove');
				$('#to_team_select_'+rel).attr("data-assign", 1);
			
	}
}

  // Attach event listeners
	document.getElementById('to_email').addEventListener('input', validateEmailInput);
	document.getElementById('to_email').addEventListener('change', validateEmailInput);

	$(document).on('click','.to_select',function(){

		    var rel						= 	$(this).attr('rel');
			
		  addEmailToList(rel);
		// document.getElementById('to_team_select_'+id).addEventListener('change', addEmailToList);
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


document.getElementById("btnSubmit").addEventListener("click", ()=>{
	
		

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
				if (typeof coApprCloseDrawers === 'function') { coApprCloseDrawers(); }
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

</script>
<script>
	
	// $.noConflict();
	
	var from_email	=	document.querySelector('#from_email');
	new Tagify(from_email);
	
// 	var to_email	=	document.querySelector('#to_email');
// 	new Tagify(to_email);
    
	var cc_email	=	document.querySelector('#cc_email');
	new Tagify(cc_email);
		 
</script>

