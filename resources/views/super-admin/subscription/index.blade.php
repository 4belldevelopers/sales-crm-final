@extends('layouts.superadmin_layout')

@push('head-script')

<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">

@endpush

<style>
    body
    {
        overflow-x: hidden;
    }

    <style>
.border-radius-5 {border-radius: 5px!important; }
.ml-2 {margin-left: 2em;}
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr {width: max-content!important; }
  .table > :not(caption) > * > * { padding: 0.75em 0rem!important; }
  td:nth-child(n+2),
  th:nth-child(n+2) { width: revert!important; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesClient { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClient { width: 1200px; }
}
@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesDirectClient { width: 1025px; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesDirectClient {width: 1200px;}
}
</style>

</style>

@section('create-button')
<a href="{{ route('superadmin.company.create') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> @lang('app.createNew')</a>
@endsection

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
		
            <div class="nav-group nav-group-outline mx-auto mb-1" data-kt-buttons="true" style="display: flex;">
                <button class="btn btn-color-gray-600 btn-active btn-active-secondary px-6 py-3 me-2 pck_select active" data-kt-plan="1">
                    Operational Package
                </button>
                <button class="btn btn-color-gray-600 btn-active btn-active-secondary pck_select px-6 py-3" data-kt-plan="2">
                    Sales Package
                </button>
            </div>
        
        </div>
        <div class="d-flex align-items-center py-1">
		       
		</div>
	</div>
</div>
<!--end::toolbar-->

<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-5" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12">
                <div class="card card-xl-stretch mb-xl-8">                            
                    <div class="card-header border-0 justify-content-space-around">
                      

                        



                           <input type="hidden" name="mstatus_id" id="mstatus_id" value="all" />
                           <input type="hidden" name="pck_id" id="pck_id" value="1" />
                        {{-- <div class="d-flex justify-content-space-around align-items-center custm-bg">  --}}
                                <a class="d-block text-white f-17 font-weight-bolder application_sttus_nav application_sttus_nav" href="Javascript:void(0);" rel="all">
                                    <div class="total_jobs mr-2">
                                    <div class=" bg-total-active text-center squre_box" role="alert" id="bg_all"> <span class="total-content-view">
                                        <i class="flaticon2-group text-white fa-sm" aria-hidden="true"></i> <span id="totalcnt">{{ $counts->all_count }}</span> </span> </div>
                                    <h6 class="text-center color-all font-weight-600">&nbsp;All</h6>
                                    </div>
                                    </a> 
                                    <a class="d-block text-white font-weight-bolder f-20 application_sttus_nav" href="Javascript:void(0);" rel="1">
                                    <div class="sourcing mr-2">
                                    <div class="bg text-center circle_boxes SourcingCounter alert bg-gray text-center circle_boxes" role="alert" id="bg_1">{{ $counts->free_count }}</div>
                                    <h6 class="tx text-center color-all font-weight-600 text-center color-all" id="tx_1">Free</h6>
                                    </div>
                                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="2">
                                    <div class="screening mr-2">
                                    <div class="bg text-center circle_boxes alert bg-gray text-center circle_boxes" role="alert" id="bg_2">{{ $counts->paid_count }}</div>
                                    <h6 class="tx text-center color-all font-weight-600 text-center color-all" id="tx_2">Paid</h6>
                                    </div>
                                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="3">
                                    <div class="negitation mr-2">
                                    <div class="bg text-center circle_boxes alert bg-gray text-center circle_boxes" role="alert" id="bg_3">{{ $counts->expiry_in_30_days_count }}</div>
                                    <h6 class="tx text-center color-all font-weight-600 text-center color-all" id="tx_3">Expiry in 30 Days</h6>
                                    </div>
                                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="4">
                                    <div class="negitation mr-2">
                                    <div class="bg text-center circle_boxes alert bg-gray text-center circle_boxes" role="alert" id="bg_4">{{ $counts->less_coins_count }}</div>
                                    <h6 class="tx text-center color-all font-weight-600 text-center color-all" id="tx_4">Less Coins (100)</h6>
                                    </div>
                                    </a> <a class="d-block text-white font-weight-bolder f-20  application_sttus_nav" href="Javascript:void(0);" rel="5">
                                    <div class="offer mr-2">
                                    <div class="bg text-center circle_boxes alert bg-gray text-center circle_boxes" role="alert" id="bg_5">{{ $counts->last_active_7_days_count }}</div>
                                    <h6 class="tx text-center color-all font-weight-600 text-center color-all" id="tx_5">Last Active(7+ Days)</h6>
                                    </div>
                                    </a>  
                        {{-- </div> --}}


                    </div>
                    {{-- <div class="card-body pt-0 d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-7 me-1 col-md-2" rel="all">
                           
                            <span class="svg-icon svg-icon-warning me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">All</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light-success rounded p-5 mb-7 me-1 col-md-2" rel="1">
                           
                            <span class="svg-icon svg-icon-success me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">Free</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light-danger rounded p-5 mb-7 me-1 col-md-2" rel="2">
                           
                            <span class="svg-icon svg-icon-danger me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">Paid</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-7 me-1 col-md-2" rel="3">
                           
                            <span class="svg-icon svg-icon-warning me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">Expiry in 30 Days</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light-success rounded p-5 mb-7 me-1 col-md-2" rel="4">
                           
                            <span class="svg-icon svg-icon-success me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">Less Coins (100)</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light-danger rounded p-5 mb-7 me-1 col-md-2" rel="5">
                           
                            <span class="svg-icon svg-icon-danger me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">Not Active</a>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 mt-4">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <div class="loadingMessage loadingMessagesubscriptionList" style="display: none; position: absolute;">Processing...</div>			
							<table id="myTable2" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
								<thead>
									<tr>
										<th> <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
										</th>										
										<th>Company Name</th>
										<th>Location</th>
										<th>Date</th>
										<th>Operational Package</th>
                                        <th>Op. Coins</th>
                                        <th>Sales</th>
                                        <th>Sales Coins</th>
                                        <th>Last Active</th>
									</tr>
								</thead>
                                <tbody class="text-gray-600 fw-bold" id="subscriptionList">
                                </tbody>
							</table>
                            <div id="paginationsubscription"></div>
					
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Company Details</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesClient_close">
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
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
    <!--begin::Body-->
  </div>
</div>

<div id="kt_activitiesWallet_toggle"></div>
<div id="kt_activitiesWallet" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesWallet_toggle" data-kt-drawer-close="#kt_activitiesWallet_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Edit Company Wallet</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesWallet_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesWallet_body"></div>
  </div>
</div>





@endsection
@push('footer-script')

<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
     
	//   var table = $('#myTable2').dataTable({
            
	// 			responsive     	: true, 
    //   			processing      : true,
	// 			ordering		: false, //disable ordering      			
    //   			destroy       	: true,   
    //   			stateSave     	: true,      
    //   			"bLengthChange"   : false,   
	// 			sDom			: 'lrtip',   
				
	// 		searching: false,
    //         serverSide: true,
            
    //         ajax: {
    //                 url: '{!! route('superadmin.company.subscriptiondata') !!}',
    //                 type: 'GET',
    //                 data: function (d) {
    //                     d.mstatus_id=$('#mstatus_id').val(), d.pck_id=$('#pck_id').val()
    //                 }
    //             },
    //         language: {
    //             "url": "<?php echo __("app.datatable") ?>"
    //         },
    //         "fnDrawCallback": function( oSettings ) {
    //             $("body").tooltip({
    //                 selector: '[data-toggle="tooltip"]'
    //             });
    //         },
    //         columns: [
    //             { data: 'chkbox', name: 'chkbox'},
	// 		    { data: 'Name', name: 'Name' },
    //             { data: 'Location', name: 'Location' },
    //             { data: 'Connected', name: 'Connected' },
    //             { data: 'OperationalPack', name: 'OperationalPack' },
    //             { data: 'OpCoins', name: 'OpCoins' },
    //             { data: 'SalesPack', name: 'SalesPack' },
    //             { data: 'SalesCoins', name: 'SalesCoins' },
    //             { data: 'LastActive', name: 'LastActive' }
    //         ]
    //     });

        // new $.fn.dataTable.FixedHeader( table );


        $(document).on('click', '.show-detail', function (){
       
		$('#kt_activitiesClient_toggle').trigger('click');	
		var id    		= $(this).data('row-id');
		var url   		= "{{ route('superadmin.company.viewmore',':id') }}";
		url     		= url.replace(':id', id);
		
		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
		$.ajax({
						type  : 'GET',
						url   : url,
						 beforeSend: function() {
                                  $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success : function (response) {
								$('#kt_activitiesClient_body').html(response.view);
						}
	  			});
	});

    $(document).on('click', '.show-detail-direct', function ()
	{
		$('#kt_activitiesDirectClient_toggle').trigger('click');	
		var id    		= $(this).data('row-id');
		var url   		= "{{ route('superadmin.company.viewmore',':id') }}";
		url     		= url.replace(':id', id);
		
		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
		$.ajax({
						type  : 'GET',
						url   : url,
						 beforeSend: function() {
                                  $('#kt_activitiesDirectClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success : function (response) {
								$('#kt_activitiesDirectClient_body').html(response.view);
						}
	  			});
	}); 

    $(document).on('change', '.app_int_list', function (){
        // Get the value of the selected radio button
        var selectedValue = $("input[name='app_int_list']:checked").val();

        alert(selectedValue);
        
        // Perform any action with the selected value
        console.log("Selected Value: " + selectedValue);
    });
    $(document).on('click', '.pck_select', function (){
         var pck_id  = $(this).attr('data-kt-plan');
         $('#pck_id').val(pck_id);
        //  $('#myTable2').DataTable().draw();
            fetchSubscription();

         setTimeout(() => {
            var url   		= "{{ route('superadmin.company.countaccountviewmore',':id') }}";
		    url     		= url.replace(':id', pck_id);
		
		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
		$.ajax({
						type  : 'GET',
						url   : url,
						success : function (response) {
                            if (response.counts) {
                            var allCount = response.counts.all_count;
                            var freeCount = response.counts.free_count;
                            var paidCount = response.counts.paid_count;
                            var expiryIn30DaysCount = response.counts.expiry_in_30_days_count;
                            var lessCoinsCount = response.counts.less_coins_count;
                            var lastActive7DaysCount = response.counts.last_active_7_days_count;


                            $('#totalcnt').text(allCount);
                            $('#bg_1').text(freeCount);
                            $('#bg_2').text(paidCount);
                            $('#bg_3').text(expiryIn30DaysCount);
                            $('#bg_4').text(lessCoinsCount);
                            $('#bg_5').text(lastActive7DaysCount);
                            }

								
						}
	  			});
            
         }, 2000);

    });


    $(document).on('click', '.application_sttus_nav', function (){
          
          $(".bg").removeClass('bg-Asourcing').addClass('bg-gray');
          $(".bg").removeClass('bg-Ascreening').addClass('bg-gray');
          $(".bg").removeClass('bg-Ainterview').addClass('bg-gray');
          $(".bg").removeClass('bg-Anegotation').addClass('bg-gray');
          $(".bg").removeClass('bg-Aoffer').addClass('bg-gray');
          $(".bg").removeClass('bg-Ajoiner').addClass('bg-gray');
          $(".bg").removeClass('bg-Arejected').addClass('bg-gray');
          
          $("#tx_1").removeClass('text-Asourcing').addClass('color-all');
          $("#tx_2").removeClass('text-Ascreening').addClass('color-all');
          $("#tx_3").removeClass('text-Ainterview').addClass('color-all');
          $("#tx_4").removeClass('text-Anegotation').addClass('color-all');
          $("#tx_5").removeClass('text-Aoffer').addClass('color-all');
          $("#tx_6").removeClass('text-Ajoiner').addClass('color-all');
          $("#tx_7").removeClass('text-Arejected').addClass('color-all');
          
          $('#bg_all').removeClass('bg-total-active').addClass('bg-gray');
          
          var page  = $(this).attr('rel');
   
        if(page == 1)
        {
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Asourcing'); 
            $('#tx_'+page).removeClass('color-all').addClass('text-Asourcing');
            $('.nav_status').html('Sourcing');  
            $('.nav_status').css('background','#efefef');
            $('.nav_status').css('color','#000000');
        }else if(page==2){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ascreening');  
            $('#tx_'+page).removeClass('color-all').addClass('text-Ascreening');  
            $('.nav_status').html('Screening'); 
            $('.nav_status').css('background','#fff9ed');
            $('.nav_status').css('color','#ffab00');
        }else if(page==3){  
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ainterview');  
            $('#tx_'+page).removeClass('color-all').addClass('text-Ainterview');  
            $('.nav_status').html('Interview'); 
            $('.nav_status').css('background','#f0f8ff');
            $('.nav_status').css('color','#2896ff');
        }else if(page==4){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Anegotation'); 
            $('#tx_'+page).removeClass('color-all').addClass('text-Anegotation');
            $('.nav_status').html('Negotiation');
            $('.nav_status').css('background','#ffe6f2');
            $('.nav_status').css('color','#ff4098');    
        }else if(page==5){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Aoffer');
            $('#tx_'+page).removeClass('color-all').addClass('text-Aoffer');  
            $('.nav_status').html('Offered'); 
            $('.nav_status').css('background','#d4ffde');
            $('.nav_status').css('color','#06c732');  
        }else if(page==6){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Ajoiner');
            $('#tx_'+page).removeClass('color-all').addClass('text-Ajoiner'); 
            $('.nav_status').html('Joined');  
            $('.nav_status').css('background','#f7deff');
            $('.nav_status').css('color','#c000ff');
        }else if(page==7){
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-Arejected');
            $('#tx_'+page).removeClass('color-all').addClass('text-Arejected'); 
            $('.nav_status').html('Rejected');  
            $('.nav_status').css('background','#ffe2e5');
            $('.nav_status').css('color','#ff0202');
        }else if(page=='all'){
            $('.nav_status').html('All Candidate'); 
            $('.nav_status').css('background','linear-gradient(to left, #01e074, #0581d9)');
            $('.nav_status').css('color','#FFFFFF');
            $('#bg_'+page).removeClass('bg-gray').addClass('bg-total-active');   
        }
  
      $('#mstatus_id').val(page);
      var url   		=	"{{url('/super-admin/subscription/')}}";
      url     		=	url+'/'+page;
      window.history.pushState('page2', 'Title', url);
    //   $('#myTable2').DataTable().draw();
    fetchSubscription();
   
})




       $(document).on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('superadmin.company.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.ajax({
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


        $(document).on('click','.checkActive',function(){
                // e.preventDefault();
                var id = $('#hid_client_id').val();
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                var url       	=	"{{ route('superadmin.company.checkActive') }}";
                var token       =	"{{ csrf_token() }}";

                $.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'id' : id},
													beforeSend	: function() {
																				$('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
                                                        if(response.pack_status==="active"){
                                                            $('#checkActive').removeClass('btn-light-danger').addClass('btn-light-success');
                                                            $('#checkActive').text("Active");
                                                        }else{
                                                            $('#checkActive').removeClass('btn-light-success').addClass('btn-light-danger');
                                                            $('#checkActive').text("Inactive");
                                                        }
                                                        //  return false;
														}
										});	

               

          });

          $(document).on('click','.checkStatusOperational',function(){
            //    e.preventDefault();
                var id = $('#hid_client_id').val();
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                var url       	=	"{{ route('superadmin.company.checkStatusOperational') }}";
                var token       =	"{{ csrf_token() }}";

                $.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'id' : id},
													beforeSend	: function() {
																				$('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {

                                                        $('.checkStatusOperational_text').text(response.pack_status);


														}
						    });	

               

          });

          $(document).on('click','.checkStatusSales',function(){
            // e.preventDefault();
              var id = $('#hid_client_id').val();
              var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
              var url       	=	"{{ route('superadmin.company.checkStatusSales') }}";
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
                                    
                                    $('.checkStatusSales_text').text(response.pack_status);
                                                                                                    
                                }
                          }
                  });	

          });


        


          $(document).ready(function() {
           

           fetchSubscription();

           $(document).on('click', '#paginationsubscription .page-link', function(e) {
               e.preventDefault();

               const page = $(this).data('page');
               if (!page || $(this).hasClass('disabled')) return;

               // Disable the clicked pagination link
               $(this).addClass('disabled');

               // Fetch data
               fetchSubscription(page, $('#search').val()).then(() => {
                   // Re-enable the links after successful data fetch
                   $('#paginationsubscription .page-link').removeClass('disabled');
               }).catch(() => {
                   // Re-enable the links even if there's an error
                   $('#paginationsubscription .page-link').removeClass('disabled');
               });
           });


       });
       
       function fetchSubscription(page = 1, search = '') {
           var clientid = '1';
           $('.loadingMessagesubscriptionList').css({
               display: 'block'
           });
           $.ajax({
               url: `{!! route('superadmin.company.allSubscriptionDataAjax') !!}?page=${page}`,
               type: 'GET',
               data: {
                   pck_id: $('#pck_id').val(),
                   mstatus_id: $('#mstatus_id').val()
               },
               success: function(response) {
                   $('.loadingMessagesubscriptionList').css({
                       display: 'none'
                   });
                   $('#subscriptionList').html(response.html);
                   $('#paginationsubscription').html(response.pagination);
               }
           });
       }

		
</script>

@endpush
