@extends('layouts.demo_admin_app')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
@endpush
<style>
body
{
  overflow-x:hidden;
}

/*begin::Media query*/
@media  only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesSP
  {
    width: 1025px;
  }
}

@media  only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesSP
  {
    width: 1200px;
  }
}
/*Media query end*/
</style>
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Staffing Partners-->
      <div class="d-flex flex-column">    
        <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
          <a href="javascript:void(0);" class="text-primary">Staffing Partners</a>
        </h5>
        <span class="text-gray-600">1,715</span>
      </div>
      <!--end::Staffing Partners-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
       <!--begin::Suggested Vendor-->
       <div class="d-flex flex-column">    
          <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
            <a href="https://rm.vibhanshujaiswal.com/admin/consultancy/suggestedPartners/2" class="text-gray-800 text-hover-primary">Suggested Partners</a>
          </h5>
        <span class="text-gray-600">430</span>
      </div>
      <!--end::Suggested Vendor-->      
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
       <!--begin::Request-->
       <div class="d-flex flex-column">    
          <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
            <a href="https://rm.vibhanshujaiswal.com/admin/consultancy/requestPartners/2" class="text-gray-800 text-hover-primary">Request</a>
          </h5>
        <span class="text-gray-600">240</span>
      </div>
      <!--end::Request-->
    </div>
    <!--end::Page title-->
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">        
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1 me-3">
          <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
          <span class="svg-icon svg-icon-1 position-absolute ms-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
              <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
            </svg>
          </span>
          <!--end::Svg Icon-->
          <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
        </div>
        <!--end::Search-->
                
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
              <!--begin::Filter-->
              <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
              <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
              <span class="svg-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                </svg>
              </span>
              <!--end::Svg Icon-->Filter</button>
              <!--begin::Menu 1-->
              <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                <!--begin::Header-->
                <div class="px-7 py-5">
                  <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                </div>
                <!--end::Header-->
                <!--begin::Separator-->
                <div class="separator border-gray-200"></div>
                <!--end::Separator-->
                <!--begin::Content-->
                <div class="px-7 py-5" data-kt-user-table-filter="form">
                  <!--begin::Input group-->
                  <div class="mb-10">
                    <label class="form-label fs-6 fw-bold">Role:</label>
                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
                      <option></option>
                      <option value="Administrator">Administrator</option>
                      <option value="Analyst">Analyst</option>
                      <option value="Developer">Developer</option>
                      <option value="Support">Support</option>
                      <option value="Trial">Trial</option>
                    </select>
                  </div>
                  <!--end::Input group-->
                  <!--begin::Input group-->
                  <div class="mb-10">
                    <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                    <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
                      <option></option>
                      <option value="Enabled">Enabled</option>
                    </select>
                  </div>
                  <!--end::Input group-->
                  <!--begin::Actions-->
                  <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                    <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                  </div>
                  <!--end::Actions-->
                </div>
                <!--end::Content-->
              </div>
              <!--end::Menu 1-->
              <!--end::Filter-->
             
@if(in_array("add_staffing_partners", $userPermissions))					
	 <!--begin::Add  Staffing Partners<-->
			      <a href="javascript:void(0);" type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#add_staffing_partners">
              <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
              <span class="svg-icon svg-icon-primary svg-icon-1x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                  </g>
                </svg>
              </span>
              <!--end::Svg Icon-->Add Staffing Partners
			      </a>
@endif				 
			 
				  
				  
				  
              <!--end::Add  Staffing Partners<-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Group actions-->
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
              <div class="fw-bolder me-5">
              <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
              <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
            </div>
          </div>
      </div>
      <!--end::Actions-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          
@if(in_array("view_staffing_partners", $userPermissions))			  
		  <!--begin::Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5" id="myTable">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0">
                  <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div>
                </th>
                <th class="">Name</th>
                <th class="">Location</th>
                <th class="">Joining</th>
                <th class="">Requisitions</th>
                <th class="">Connected On</th>
                <th class="">Contact Person</th>
              </tr>
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
          </table>
          <!--end::Table-->
@endif		  
		  
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->

<!--begin::Add staffing partners - popup-->
<div class="modal fade show" id="add_staffing_partners" tabindex="-1" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header" id="add_staffing_partners_header">
        <h2 class="fw-bolder">Invite Staffing partner or Recruitment consultant</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
            </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
      </div>
      <!--end::Modal header-->
      <!--begin::Modal body-->
      <div class="modal-body scroll-y mx-5 my-0">
        <!--begin::Form-->
        <form id="add_staffing_partners_form" class="fv-row form fv-plugins-bootstrap5 fv-plugins-framework" action="#">
              <div class="d-flex flex-column scroll-y me-n7 pe-7" id="add_staffing_partners_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#add_staffing_partners_header" data-kt-scroll-wrappers="#add_staffing_partners_scroll" data-kt-scroll-offset="300px" style="max-height: 10px;">
                <div class="fv-row mb-7 fv-plugins-icon-container">
                  <label class="fw-bold fs-6 mb-2">Invite with email</label>
                  <input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Enter one or more email addresses" value="">
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
              </div>
              <div class="text-center pt-5">
                <button type="button" class="btn btn-light me-3" data-kt-users-modal-action="close" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-kt-users-modal-action="submit">
                  <span class="indicator-label">Invite</span>
                  <span class="indicator-progress">Please wait... 
                  <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
              </div>
        </form>
        <!--end::Form-->
      </div>
      <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
  </div>
</div>
<!--end::Add staffing partners - popup-->

<div id="kt_activitiesSP_toggle"></div>
<div id="kt_activitiesSP" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesSP_toggle" data-kt-drawer-close="#kt_activitiesSP_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">      
      <div class="d-flex align-items-center py-1">    
        <h3 class="card-title fw-bolder text-dark">Staffing Partners</h3> 
        <!--begin::seprator-->
          <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>  
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesSP_close">
          <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
              <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
              <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <!--end::Header-->
	<div class="card-body position-relative px-0 py-0" id="kt_activitiesSP_body">
		&nbsp;
	</div>
    <!--begin::Body-->
    
</div>
</div>


@endsection

@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>

<!--<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>-->

<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>




<!--begin::Validaation-->
<script>
   $(document).ready(function()
   {         
// Class definition
var KTUsersAddUser = function () {
    // Shared variables
    const element = document.getElementById('add_staffing_partners');
    const form = element.querySelector('#add_staffing_partners_form');
    const modal = new bootstrap.Modal(element);

    // Init add schedule modal
    var initAddUser = () => {

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        var validator = FormValidation.formValidation(
            form,
            {
              fields:
              {
                user_email:
                {
                  validators:
                  {
                    notEmpty:
                    {
                      message: "Email address is required"
                    },
                    emailAddress:
                    {
                      message: "The value is not a valid email address"
                    }
                  }
                }
              },

              plugins:
              {
                  trigger: new FormValidation.plugins.Trigger(),
                  bootstrap: new FormValidation.plugins.Bootstrap5({
                      rowSelector: '.fv-row',
                      eleInvalidClass: '',
                      eleValidClass: ''
                  })
              }
            }
        );

        // Submit button handler
        const submitButton = element.querySelector('[data-kt-users-modal-action="submit"]');
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');

                    if (status == 'Valid') {
                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click 
                        submitButton.disabled = true;

                        // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        setTimeout(function () {
                            // Remove loading indication
                            submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton.disabled = false;

                            // Show popup confirmation 
                            Swal.fire({
                                text: "Invitation has been sent!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            })
                            // .then(function (t)
                            // {
                            //    t.isConfirmed && e && e.hide();
                            // });
                            .then(function()
                            {
                              window.location = "";
                            });

                            //form.submit(); // Submit form
                        }, 2e3);
                    } else {
                        // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
                            text: "Sorry, looks like there are some errors detected, please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            }
        });

        // Cancel button handler
        const cancelButton = element.querySelector('[data-kt-users-modal-action="cancel"]');
        cancelButton.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: "Are you sure you would like to cancel?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, cancel it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    form.reset(); // Reset form			
                    modal.hide();	
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been cancelled!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        }
                    });
                }
            });
        });

        // Close button handler
        const closeButton = element.querySelector('[data-kt-users-modal-action="close"]');
        closeButton.addEventListener('click', e => {
            e.preventDefault();

            Swal.fire({
                text: "Are you sure you would like to cancel?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, cancel it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    form.reset(); // Reset form			
                    modal.hide();	
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been cancelled!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        }
                    });
                }
            });
        });
    }

    return {
        // Public functions
        init: function () {
            initAddUser();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTUsersAddUser.init();
});         
   });         
</script>
<!--end::Validation-->

<script>

$('#column_visibility_control_btn')
.click(function()
{		
    var id 				= 12;
    if(typeof scheduleDate === "undefined") {
        scheduleDate 	= '';
    }
    var url 			= "{{ route('admin.consultancy.load_visibility_control',':id') }}";
    url 				= url.replace(':id', id);
    $('#modelHeading').html('Schedule');
    $.ajaxModal('#scheduleDetailModal', url);
});	
</script>

<script>

$(document).ready(function() {

	
	$('#inputSearch').keyup(function() {
		//alert("call");
		table._fnDraw(); 
	});
});


        var table = $('#myTable').dataTable({
                    
			responsive  	: true,
			processing  	: true,
			serverSide  	: true,
			destroy   		: true,
			ordering		: false, 		//	disable ordering			
			"bLengthChange" : false, 		//	thought this line could hide the LengthMenu
			sDom			: 'lrtip',
			oLanguage 		: { sLengthMenu: "_MENU_", },	
			ajax			: {'url' : '{!! route('admin.consultancy.data') !!}',
									"data"	: function ( d ){d.search = $('#inputSearch').val()}
							  },				
            language		: {"url": "<?php echo __("app.datatable") ?>"},
            "fnDrawCallback": function( oSettings ) {
                			$("body").tooltip({	selector: '[data-toggle="tooltip"]' });
            },
            columns: [
    						{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
							{ data: 'Name', name: 'Name', searchable : false, orderable : false},
							{ data: 'Location', name: 'Location', searchable : false, orderable : false},
							{ data: 'Joining', name: 'Joining', searchable : false, orderable : false},
							{ data: 'Requisitions', name: 'Requisitions', searchable : false, orderable : false},
							{ data: 'ConnectedOn', name: 'ConnectedOn', searchable : false, orderable : false},
							{ data: 'ContactPerson', name: 'ContactPerson', searchable : false, orderable : false}    					
            		]
        });
        new $.fn.dataTable.FixedHeader( table );


		table.on('click', '.show-detail', function ()
		{	
			$('#kt_activitiesSP_toggle').trigger('click');			
			var id    		=	$(this).data('row-id');
			var url   		=	"{{ route('admin.consultancy.show',':id') }}";
			url     		=	url.replace(':id', id);			
			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type  		: 'GET',
							url   		: url,
							 beforeSend	: function() {
									  $('#kt_activitiesSP_body').html('<img src="'+baseurl+'" height="50px" width="100%">');
							},
							success : function (response) 
							{									
											if (response.status == "success")
											{
												$('#kt_activitiesSP_body').html(response.view);
											}	
							}
					});
		}); 
	
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title				: "@lang('errors.areYouSure')",
                text				: "@lang('errors.deleteWarning')",
                type				: "warning",
                showCancelButton	: true,
                confirmButtonColor	: "#DD6B55",
                confirmButtonText	: "@lang('app.delete')",
                cancelButtonText	: "@lang('app.cancel')",
                closeOnConfirm		: true,
                closeOnCancel		: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.consultancy.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
function exportExcel()
{
	var table 		        		= $('#myTable').dataTable();
	var length 		        		= table.fnGetData().length; 	
	var oSettings         			= table.fnSettings();
	var start             			= oSettings._iDisplayStart;					
	var url 			        	= '{{ route('admin.consultancy.export', [':start' , ':length']) }}';
	url 					        = url.replace(':start', start);
	url 					        = url.replace(':length', length);
	window.location.href  			= url;
}
</script>



@endpush