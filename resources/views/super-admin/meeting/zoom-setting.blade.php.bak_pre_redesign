@extends('layouts.superadmin_layout')
@php 
   use App\Helper\GeneralMethod;
@endphp
@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->
    <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet"
        type="text/css" />
    <style>
        body {
            overflow-x: hidden;
        }

        .company-logo {
            max-height: 50px;
            max-width: 120px;
        }

        .company-logo-div {
            border-radius: 2px;
            padding: 2px 2px 2px 2px;
            margin-bottom: 2px;
        }

        .symbol.symbol-circle,
        .symbol.symbol-circle .symbol-label,
        .symbol.symbol-circle>img {
            border-radius: 10%;
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesDefaultPackage {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesDefaultPackage {
                width: 1200px;
            }
        }

        @media  only screen and (min-width: 1024px) and (max-width: 1440px){#kt_activitiesBenchApplication{width: 1025px;}}
        @media  only screen and (min-width: 1440px) and (max-width: 2560px){#kt_activitiesBenchApplication{width: 1200px;}}

    </style>
@endpush

@section('content')
    <!--begin::toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Zoom Setting<span
                        class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small
                        class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
            </div>
          
            
            {{-- <div class="d-flex align-items-center py-1">
                
                 <span class="svg-icon svg-icon-primary svg-icon-1x"> Access token generator</span>&nbsp;&nbsp;&nbsp;
                <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                     
                   <input class="form-check-input chkStatus" type="checkbox" value="{{ @$zoom_setting->status }}" name="staus" {{ (@$zoom_setting->status) ? 'checked' : '' }}>
                </div> &nbsp;&nbsp;&nbsp; --}}

            {{-- </div> --}}
          
        </div>
    </div>


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

                        <form id="editSettings" class="ajax-form">
                            @csrf
    
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title_package">Title</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                        value="{{ (@$zoom_setting->title) ? $zoom_setting->title : '' }}">
                                    </div>
                                </div>
    
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="redirect_uri_label">Account ID</label>
                                        <input type="text" class="form-control" id="redirect_uri" name="redirect_uri"
                                            value="{{ (@$zoom_setting->redirect_uri) ? $zoom_setting->redirect_uri : '' }}">
                                    </div>
                                </div>
    
    
                            </div>
                            <label for="">&nbsp;</label>
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id_package">client ID</label>
                                        <input type="text" class="form-control" id="client_id" name="client_id"
                                        value="{{ (@$zoom_setting->client_id) ? $zoom_setting->client_id : '' }}">
                                    </div>
                                </div>
    
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_secret_label">Client secret ID</label>
                                        <input type="text" class="form-control" id="client_secret" name="client_secret"
                                            value="{{ (@$zoom_setting->client_secret) ? $zoom_setting->client_secret : '' }}">
                                    </div>
                                </div>

                            </div>

                            
                            <div class="row">                        
                              
                                <label class="form-label" style="padding-top:10px;">&nbsp;</label>
                                <input type="hidden" class="form-control" value="{{ (@$zoom_setting->id) ? $zoom_setting->id : '' }}" id="token_id" name="token_id">
                            </div>
    

    
                            <div class="form-row row fv-row">
                                <div class="col-12 text-center py-10">
    
                                <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                    @lang('app.save')
                                </button>
                                {{-- <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button> --}}
    
                            </div>
                            {{-- <label class="form-label" style="padding-top:10px;"><strong> Access token</strong></label>
                            <label class="form-label" style="padding-top:10px; opacity: 0.6;">{{ (@$zoom_setting->zoom_access_token) ? $zoom_setting->zoom_access_token : '' }}</label> --}}
                        </div>
                        </form>

                    </div>
                    
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->

    </div>
    <!--end::Content-->



    <!--begin::Mass Mail - Modal-->
<div class="modal fade" id="disapprove" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header flex-stack">
          <h2>Bench Candidate Reject</h2>
          <div class="btn btn-sm btn-icon btn-active-color-primary" data-dismiss="modal">
            <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
            </svg>
            </span>
          </div>
        </div>
        <div class="card-body position-relative px-0 py-0" id="application_reject_mail_body_content"></div>
      </div>
    </div>
  </div>
  <!--end::Mass Mail - Modal-->



    <div id="kt_activitiesBenchApplication_toggle"></div>
    <div id="kt_activitiesBenchApplication" class="bg-body" data-kt-drawer="true"
        data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
        data-kt-drawer-width="" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle"
        data-kt-drawer-close="#kt_activitiesBenchApplication_close">
        <div class="card shadow-none rounded-0 w-100" id="bench_application_show_right_bar"></div>
    </div>


@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>


    <script>

      

        


        $(document).ready(function() {

            const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
		  	         buttonsStyling: false
		    });

            $('.chkStatus').click(function(){
                var id = $(this).val();
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                var url       	=	"{{ route('superadmin.company.auto-approval') }}";
                var token       =	"{{ csrf_token() }}";

                	                Swal.fire({
											title: 'Permission',
											text: 'Allow to Zoom Meeting?',
											icon: 'question',
											showCancelButton: true, // Show the cancel button
											confirmButtonText: 'Allow',
											cancelButtonText: 'No, cancel',
											reverseButtons: true // Optional: to reverse the order of the buttons
										   }).then((result) => {
											if (result.isConfirmed) {
												$.ajax({
												url: '{{ route('redirectToZoom') }}',
												method: 'GET',
												success: function(response) {
													var width = 600;
                                                    var height = 400;
                                                    var left = (screen.width - width) / 2;
                                                    var top = (screen.height - height) / 2;

                                                    var newWindow = window.open(
                                                        response.authUrl,
                                                        'popupWindow',
                                                        'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',scrollbars=no,resizable=no'
                                                    );
													var selectElement = document.getElementById('interview_mode_lavels');
														selectElement.selectedIndex = 0; // Selects the first option
													setTimeout(function() {
														newWindow.close();
														var selectElement = document.getElementById('interview_mode_lavels');
														selectElement.selectedIndex = 0; // Selects the first option
													}, 5000);

													$('#responseContainer').html('<p>' + response.message + '</p>');
												},
												error: function(xhr) {
													// Handle errors
													$('#responseContainer').html('<p>An error occurred: ' + xhr.statusText + '</p>');
												}
											});

											} else if (result.isDismissed) {
												// Handle the case when the user cancels
												console.log('User cancelled the Google Meeting request.');
											}
									});


                                       

               

            });
         
            
            
            



               


            



            $('#save-form').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = new FormData($('#editSettings')[0]);
            $.ajax({
                url: '{{ route('superadmin.company.settingZoomStore') }}',
                type: "POST",
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                //container		: '#createForm',
                redirect: true,
                file: true,
                //data			: $('#createForm').serialize(),
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success') {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function(result) {
                            // window.location = response.url;
                        });
                    } else {
                        Swal.fire({
                            text: "Sorry, looks like there are some errors, please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                },
                error: function(response) {

                    if (response.responseJSON && response.responseJSON.errors) {
                            let errorMessages = Object.values(response.responseJSON.errors).flat().join('<br>');

                            Swal.fire({
                                html: `<p>Sorry, looks like there are some validation:</p><p style="color: red;text-align: left;">${errorMessages}</p>`,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                    } else {
                        Swal.fire({
                            text: "Sorry, looks like there was an issue. Please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                }
            })


});


          
            });
    </script>
@endpush
