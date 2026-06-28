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
                <div class="d-flex flex-column">
                  <h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="javascript:void(0);" class="text-primary-800">{{ $status }}</a> </h5>
                  <span class="text-gray-600" id="total_record">{{ $total }}</span>
                </div>
              </div>

            @if(@$showAction)
            
            <div class="d-flex align-items-center py-1">
                @if(@$reject)
                 <span class="svg-icon svg-icon-primary svg-icon-1x"> Auto Approved </span>&nbsp;&nbsp;&nbsp;
                <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                     @php
                       $bench_status = DB::table('global_settings')->select('bench_status','id')->first();
                     @endphp
                   <input class="form-check-input chkStatus" type="checkbox" value="{{ @$bench_status->id }}" name="staus" {{ (@$bench_status->bench_status) ? 'checked' : '' }}>
                </div> &nbsp;&nbsp;&nbsp;
                @endif

            <a href="javascript:void(0);" type="button" data-type="approve" class="btn btn-sm btn-flex btn-light-primary clickbtn approve fw-bolder me-3 d-none" data-toggle="modal" data-target="#approve"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3"/>
                    <path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000"/>
                  </g>
                </svg>
                </span> Approve 
            </a>
            @if(@$reject)
                <a href="javascript:void(0);" type="button" data-type="disapprove" class="btn btn-sm btn-flex btn-light-danger reject_mail disapprove fw-bolder me-3 d-none" data-toggle="modal" data-target="#disapprove"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3"/>
                        <path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000"/>
                    </g>
                    </svg>
                    </span> Reject 
                </a>
            @endif

            </div>
            @endif
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

                        <div id="benchListingTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div id="benchListingTable_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                            <table class="table align-middle table-row-dashed fs-6 gy-5 no-footer dataTable" id="benchListingTable" role="grid" aria-describedby="benchListingTable_info" style="width: 1293px;"><thead>
                            <tr class="fw-bolder text-muted" role="row">
                                <th class="w-10px pe-0 sorting_disabled" rowspan="1" colspan="1" style="width: 40px;">
                                  <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input datatable_chk chk_list" type="checkbox" data-kt-check="true" data-kt-check-target="#myTable .form-check-input" value="1">
                                  </div>
                                </th>
                                <th class="align-items-center sorting_disabled" rowspan="1" colspan="1" style="width: 171px;">Name

                                </th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 34px;">Exp</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 137px;">Salary</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 120px;">Qualifaction</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 109px;">Availability</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 83px;">Location</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 63px;">Status</th>
                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 124px;">Upload Date</th>
                            </tr>
                        </thead>
                       
                      <tbody class="text-gray-600 fw-bold" id="BenchCandidatesListAjax">
                       
                    </tbody>
                
                </table>
             <div id="paginationBenchCandidates"></div>
                {{-- {{ $newCandidates->links() }}                --}}
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
                var url       	=	"{{ route('superadmin.company.auto-approval') }}";
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
            
                

          $(document).on('click','.chk_list',function(){
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


    $(document).on('click','.chk_list_item',function(){
    
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


            $(document).on('click','.bench_show_application_detail',function(){
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



             $(document).ready(function() {
            // $.noConflict();

            fetchallBenchCandidatesAjax();
            
            $(document).on('click', '#paginationBenchCandidates .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchallBenchCandidatesAjax(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#paginationBenchCandidates .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#paginationBenchCandidates .page-link').removeClass('disabled');
                });
            });


    });

 

    function fetchallBenchCandidatesAjax(page = 1, search = '') {
        var clientid = '1';
        $('#benchListingTable_processing').css({ display: 'block' });
        $.ajax({
            url: `{!! route('superadmin.company.ajax-new-bench-candidates') !!}?page=${page}`,
            type: 'GET',
            data: {
              search:$('#inputSearch').val(),
              custom_search:$('#kt_tagify_2').val(),
              custom_status:$('#status').val(),
              status:'{{$statusType}}'
            },
            success: function(response) {
                  
                $('#benchListingTable_processing').css({ display: 'none' });
                $('#BenchCandidatesListAjax').html(response.html);
                $('#total_record').html(response.total);
                $('#paginationBenchCandidates').html(response.pagination);
            }
        });
    }




    </script>
@endpush
