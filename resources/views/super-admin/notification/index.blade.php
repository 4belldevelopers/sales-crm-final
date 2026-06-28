@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->
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
    </style>
@endpush

@section('content')
    <!--begin::toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">E-mail Notification <span
                        class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small
                        class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
            </div>
            <a href="{{ route('superadmin.company.notificationupdate', 0) }}" type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3" id="AddCandidate"> <span class="svg-icon svg-icon-primary svg-icon-1x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                    <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                    <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                  </g>
                </svg>
                </span>Add Email</a>
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





                        <table>
                            <tbody>
                                {{-- <tr>
						<td>
								<h4 class="card-title" style="text-align:left">Package</h4>
						</td>
						<td>
								<a href="javascript:void(0);" id="editDefaultPackage"> 
									&nbsp;<i class="bi bi-pencil-fill" style="color:#5DB8F9"></i>Edit
								</a>
						</td>
					</tr> --}}
                            </tbody>
                        </table>
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4"
                                id="currentPackage">
                                <thead class="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="">Sr.No.</th>
                                        <th class="min-w-150px">Purpose</th>
                                        <th class="min-w-100px">Subject</th>
                                        <th class="min-w-100px">Permission</th>
                                        <th class="min-w-100px">Notification Type</th>
                                        <th class="min-w-50px">Status</th>
                                        <th class="min-w-150px">From E-mail</th>
                                        <th class="min-w-150px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="defaultPackageBody">
                                    @php
                                     $notificationTemplates = [
                                        "1" => "Recruitment Notification",
                                        "2" => "Sales Notification",
                                        "3" => "Mass Mail Template",
                                        "4" => "Candidate Send Email Template",
                                        "5" => "Client Send Email Template",
                                        "6" => "Offer Accept",
                                        "7" => "Offer Reject",
                                        "8" => "Offer Approved",
                                        "9" => "Offer Not Approved",
                                        "10" => "Offer Approved Notification HR"
                                    ];

                                    $sr = 1;
                                    @endphp
                                    @if($th_email_template)
                                        @foreach ($th_email_template as $val_tem)
                                        
                                        
                                            <tr>
                                                <td><a href="javascript:void(0);"
                                                        class="text-dark fw-bolder text-hover-primary d-block fs-6">{{$sr}}</a>
                                                </td>
                                                <td><a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6">{{$val_tem->purpose}}</a>
                                                     <?php
                                                    echo @$val_tem->status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>' : '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';
                                                    ?>({{ $val_tem->id }})
                                                </td>           
                                                <td><a href="javascript:void(0);"
                                                        class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$val_tem->subject; ?></a>
                                                       
                                                </td>
                                                <td>
                                                    @php
                                                     $mail_permissions = DB::table('mail_permissions')->where('notification_id','=', $val_tem->id)->pluck('role_name')->toArray();

                                                    @endphp
                                                    <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6" style="    word-break: break-word;">
                                                    {{  implode(",",$mail_permissions) }}
                                                    </a>
                                                   
                                                </td>
                                                <td>
                                                   
                                                    <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary d-block fs-6" >
                                                    {{ $notificationTemplates[$val_tem->mailType] }}({{ $val_tem->mailType }})
                                                    </a>
                                                   
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                        <input id="working_status_{{  $val_tem->id }}" class="form-check-input chkStatus" data-status="{{ ($val_tem->status) ? 0 : 1 }}" type="checkbox" value="{{  $val_tem->id }}" name="working_status" {{ ( $val_tem->status) ? 'checked' : '' }} onclick="WorkingStatus(this, $val_tem->id);"></div>
                                                </td>
                                                <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$val_tem->from_email_id; ?></a>
                                                </td>
                                                <td class="">
                                                    <a href="{{ route('superadmin.company.notificationupdate', $val_tem->id) }}" class="" data-toggle="tooltip" data-original-title="@lang('app.edit')">
                                                        <i class="fa fa-edit text-primary fs-4" aria-hidden="true">Edit/View</i>
                                                </td>
                                            </tr>
                                            @php
                                            $sr++;
                                            @endphp
                                        @endforeach
                                    @endif



                                </tbody>
                            </table>
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
            
            
           $(document).on("click",".chkStatus",function(){
                 var id = $(this).val();
                 var chkStatus = $(this).attr('data-status');
                 var url 				=	"{{ route('superadmin.company.SingleNotificationUpdate') }}";
                 var token 				=	"{{ csrf_token() }}";
                 $.ajax({
                    type		: 'POST',
                    url			: url,
                    beforeSend	: function(){ },
                    data		: {'_token': token, 'chkStatus':chkStatus, 'id': id },
                    success		: function (response){																																						
                                        if (response.status == "success"){  															  
                                            alert("Status Changed");
                                            location.reload();
                                        } 
                    }
                });
           })


        function WorkingStatus(currentValue, currentId){

            alert(currentId);
            
        }

    });
    </script>
@endpush
