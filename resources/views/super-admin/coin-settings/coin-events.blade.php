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
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Events
                <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
                <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
            </div>
            <div class="d-flex align-items-center py-1">
                    <a href="{{ route('superadmin.company.addEditcoinFeatutes') }}" class="btn btn-sm btn-light btn-active-primary">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                    </span>    
                    Add Event</a>
            </div>
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

                        <div id="benchListingTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div id="benchListingTable_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="benchListingTable" role="grid" aria-describedby="benchListingTable_info" style="width: 1293px;">
                            <thead>
                            <tr class="fw-bolder text-muted" role="row">
                            
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 34px;">Sr.no</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Type</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Title</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Default Rate</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Deduction type</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Minimum required</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 137px;">Description</th>
                                <th style="width: 150px">Working Status</th>
                                <th style="width: 150px">@lang('app.action')</th>
                            </tr>
                        </thead>
                    @if ($coinEvents)
                    <tbody>
                        @foreach ($coinEvents as $key=> $val)
                            <tr >
                                <td>
                                   <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ $key+1 }}</a>
                                      </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ ucfirst($val->event_type) }}</a>
                                         <span class="text-muted fw-bold d-block fs-7">ID-{{ $val->id }}</span>
                                       </div>
                                 </td>

                                 <td>
                                    <div class="d-flex flex-column">
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ $val->title }}</a>
                                         <span class="text-muted fw-bold d-block fs-7"></span>
                                       </div>
                                 </td>

                                 <td>
                                    <div class="d-flex flex-column">
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ ($val->default_rate > 0) ? $val->default_rate : 'Free' }}</a>
                                         <span class="text-muted fw-bold d-block fs-7"></span>
                                       </div>
                                 </td>

                                 <td>
                                    <div class="d-flex flex-column">
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ ($val->default_rate > 0) ? $val->deduction_type : 'Free' }}</a>
                                         <span class="text-muted fw-bold d-block fs-7"></span>
                                       </div>
                                 </td>

                                 <td>
                                    <div class="d-flex flex-column">
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ ($val->default_rate > 0) ? $val->minimum_required :'Free' }}</a>
                                         <span class="text-muted fw-bold d-block fs-7"></span>
                                       </div>
                                 </td>

                                 <td>
                                    <div class="d-flex flex-column">
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ $val->description }}</a>
                                         <span class="text-muted fw-bold d-block fs-7"></span>
                                       </div>
                                 </td>
                                 <td>
                                    <div class="d-flex flex-column">
                                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                           
                                          <input class="form-check-input chkStatus" type="checkbox" value="{{ @$val->id }}" name="staus" {{ (@$val->status) ? 'checked' : '' }}>
                                       </div> 
                                         <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">{{ ($val->status) ? 'Active' : 'Inactive' }}</a>
                                         <span class="text-muted fw-bold d-block fs-7"></span>
                                       </div>
                                 </td>
                                 
                                 <td class="border-bottom-0" style="margin: 20px 0 0 0;width: 100;">
                                    <a href="{{ route('superadmin.company.addEditcoinFeatutes', encrypt($val->id)) }}" class="" data-toggle="tooltip" data-original-title="@lang('app.edit')">
                                    <i class="fa fa-edit text-primary fs-4" aria-hidden="true"></i>
                                </a>
                               </td>
                               
                                
                            </tr>
                           
                        @endforeach
                    </tbody>
                    @endif
                
                </table>

                <x-custom-pagination :paginator="$coinEvents" :extra="['pagetype' => 'salestab']" />
                {{-- {{ $coinEvents->links() }} --}}
        </div>








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

        $('body').on('click', '#editDefaultPackage', function() {

            $('#kt_activitiesDefaultPackage_toggle').trigger('click');
            var url = "{{ route('superadmin.defaultpackages.editDefaultPackage') }}";
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function() {
                    $('#kt_activitiesDefaultPackage_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                    setTimeout(
                        () => {
                            if (response.status == "success") {
                                $('#kt_activitiesDefaultPackage_body').html(response.view);
                            }
                        }, 500);

                }
            });
        });


        


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
                var url       	=	"{{ route('superadmin.company.coinEventStatus') }}";
                var token       =	"{{ csrf_token() }}";

                $.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'id' : id},
													beforeSend	: function() {
																				$('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
																	if (response.status == "success") {
																		  
																		//   $('#main_container').html(response.view);
                                                                    swalWithBootstrapButtons.fire(
                                                                                                    'Success!',
                                                                                                    response.message,
                                                                                                    'success'
                                                                                                )
                                                                                        setTimeout(() => {
                                                                                            location.reload();  
                                                                                        }, 2000);
                                                                                        							
																	}
														}
										});	

               

            });
         
            $('.clickbtn').click(function(){
                var datatype = $(this).attr('data-type');
                swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You won't be able to approved this!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes,  Do approved',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true 
									}).then((result) => {
                                            if (result.isConfirmed) {
                                                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                                                var token       =	"{{ csrf_token() }}";
                                                var candidate_id = $(".chk_list_item:checked").map(function() {
                                                    return this.value;
                                                    }).get();
                                                var url       	=	"{{ route('superadmin.company.candidate-approval') }}";

                                                $.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'candidate_id' : candidate_id,'datatype':datatype},
													beforeSend	: function() {
																				$('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
																	if (response.status == "success") {
																		  
																		//   $('#main_container').html(response.view);
                                                                    swalWithBootstrapButtons.fire(
                                                                                                    'Success!',
                                                                                                    response.message,
                                                                                                    'success'
                                                                                                )
                                                                                        setTimeout(() => {
                                                                                            location.reload();  
                                                                                        }, 2000);
                                                                                        							
																	}
														}
										});	

                                            }else if (result.dismiss === Swal.DismissReason.cancel) {
                                                    swalWithBootstrapButtons.fire(
                                                                                'Cancelled',
                                                                                'Selected request is safe :)',
                                                                                'error'
                                                                            )
                                            }

                                    });
            })
            
            



               


            $('.chk_list').click(function(){ 
                setTimeout(() => {
				if($(".chk_list").prop('checked') == true){
					$('.chk_list_item').attr('checked','checked');
                    $('.approve').removeClass("d-none");
                    $('.disapprove').removeClass("d-none");
				}else{
					$('.chk_list_item').removeAttr('checked');	
                    $('.approve').addClass("d-none");
                    $('.disapprove').addClass("d-none");	
				}
                }, 500);   
		    });



            $('.chk_list_item').click(function(){
    
                var i		=	0;	
                var arr 		=	[];
                $('.chk_list_item:checked').each(function () {
                    arr.push($(this).val());
                });	
                    setTimeout(() => {
                    if(arr.length > 0){
                            $('.approve').removeClass("d-none");
                            $('.disapprove').removeClass("d-none");	
                        }else{
                            $('.approve').addClass("d-none");
                            $('.disapprove').addClass("d-none");
                        }
                    }, 500);   
                
            });


            $('.bench_show_application_detail').click(function() {
            $('#kt_activitiesBenchApplication_toggle').trigger('click');
                var id = $(this).attr('data-id');
                var url = "{{ route('superadmin.company.showdetails-bench-candidates', ':id') }}";
                url = url.replace(':id', id);
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    beforeSend: function() {
                        $('#bench_application_show_right_bar').html('<img src="' + baseurl +
                            '" height="35px" width="100%">');
                    },
                    success: function(response) {
                        $('#bench_application_show_right_bar').html(response.view);
                    }
                });
            });




            $('.reject_mail').click(function(e){
  
                e.preventDefault();		
                $('#application_reject_mail_body_content').html('');
                var arr 		=	[];
                var jobIdArr = [];
                $('.chk_list_item:checked').each(function () {
                    
                    var jobId = $(this).attr('data-jobId');
                    arr.push($(this).val());
                    jobIdArr.push(jobId);
                });

                console.log(jobIdArr);

                var id        	=	jobIdArr[0];
                var url       	=	"{{ route('superadmin.company.reject_mail',':id') }}";
                url         	=	url.replace(':id', id);
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                var token       =	"{{ csrf_token() }}";

 
                $.ajax({
                        type		: 'POST',
                        url			: url,
                        data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr},
                        cache		: false,
                        success 	: function (response) {
                                            if (response.status == "success") {
                                                $('#application_reject_mail_body_content').html(response.view);
                                            }
                                }
                    });
        
            });


          
            });
    </script>
@endpush
