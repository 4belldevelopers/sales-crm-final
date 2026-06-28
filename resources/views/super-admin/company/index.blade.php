@extends('layouts.superadmin_layout')

@push('head-script')

<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
  #kt_activitiesClient { width: 1025px !important; }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesClient { width: 1200px !important; }
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
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    $currency = GeneralMethod::convertINRToUSD();
    $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

@endphp
@section('create-button')
<a href="{{ route('superadmin.company.create') }}" class="btn btn-dark btn-sm m-l-15"><i class="fa fa-plus-circle"></i> @lang('app.createNew')</a>
@endsection

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Companies
                <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
			<small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
		</h1></div>
        <div class="d-flex align-items-center py-1">
		        <a href="{{ route('superadmin.company.create') }}" class="btn btn-sm btn-light btn-active-primary">
                <span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                    </svg>
                </span>    
                Add Company</a>
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
                    <div class="card-header border-0">
                        <div class="me-2 mt-4">
                            <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-3">Companies Reports</a>
                            <div class="text-muted fs-7 fw-bold">Details and updates</div>
                        </div>
                        <!-- <h3 class="card-title fw-bolder text-dark">Notifications</h3> -->
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="5" y="5" width="5" height="5" rx="1" fill="currentColor"></rect>
                                            <rect x="14" y="5" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                            <rect x="5" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                            <rect x="14" y="14" width="5" height="5" rx="1" fill="currentColor" opacity="0.3"></rect>
                                        </g>
                                    </svg>
                                </span>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px py-3" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Payments</div>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Create Invoice</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link flex-stack px-3">Create Payment 
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Specify a target name for future usage and reference" aria-label="Specify a target name for future usage and reference" data-original-title=""></i></a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3">Generate Bill</a>
                                </div>
                                <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-end">
                                    <a href="#" class="menu-link px-3">
                                        <span class="menu-title">Subscription</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Plans</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Billing</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3">Statements</a>
                                        </div>
                                        <div class="separator my-2"></div>
                                        <div class="menu-item px-3">
                                            <div class="menu-content px-3">
                                                <label class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" checked="checked" name="notifications">
                                                    <span class="form-check-label text-muted fs-6">Recuring</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-item px-3 my-1">
                                    <a href="#" class="menu-link px-3">Settings</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-7 me-3 col-md-4">
                            <span class="svg-icon svg-icon-warning me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">@lang('modules.dashboard.totalCompanies')</a>
                                <span class="text-muted fw-bold d-block">Due in 2 Days</span>
                            </div>
                            <span class="fw-bolder text-warning fs-4 py-1">{{ number_format($totalCompanies) }}</span>
                        </div>
                        <div class="d-flex align-items-center bg-light-success rounded p-5 mb-7 me-3 col-md-4">
                            <span class="svg-icon svg-icon-success me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">@lang('modules.dashboard.activeCompanies')</a>
                                <span class="text-muted fw-bold d-block">Due in 2 Days</span>
                            </div>
                            <span class="fw-bolder text-success fs-4 py-1">{{ number_format($activeCompanies) }}</span>
                        </div>
                        <div class="d-flex align-items-center bg-light-danger rounded p-5 mb-7 me-3 col-md-4">
                            <span class="svg-icon svg-icon-danger me-5">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor"></path>
                                        <path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </span>
                            <div class="flex-grow-1 me-2">
                                <a href="#" class="fw-bolder text-gray-800 text-hover-primary fs-6">@lang('modules.dashboard.inactiveCompanies')</a>
                                <span class="text-muted fw-bold d-block">Due in 5 Days</span>
                            </div>
                            <span class="fw-bolder text-danger fs-4 py-1">{{ number_format($inactiveCompanies) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 mt-4">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Our Companies</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Over {{ number_format($totalCompanies) }} company</span>
                        </h3>
                    </div>
                    <div class="card-header border-0 pt-5">
                        <div class="search-container input-group input-group-lg input-group-solid" style="width: 75%;height: 50px;">
                            <div class="input-group-prepend">
                                 <span class="input-group-text"> <i class="la la-search" style="font-size: 25px;"></i></span>
                          </div>
                            <input class="form-control form-control-solid ps-3" type="text" id="searchInput" placeholder="Search for company....">
                        </div>
                        <div class="col-lg-2 d-flex align-items-center px-2" style="width: 25%;">
                            <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="height: 50px; border: 1px solid #ffffff; margin-top: -6px;" onchange="filtersdataget()">
                                <option value="" selected="">All Companies</option>
                                <option value="active">Active</option>
                                <option value="inactive">Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
							
							<div class="loadingMessage loadingMessagecompanyListajax" style="display: none; position: absolute;">Processing...</div>
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
										<th>Jobs</th>
                                        <th>Join</th>
                                        <th>Bench</th>
                                        <th>Operational Package</th>
                                        <th>Sales Package </th>
                                        <th>SP.</th>
                                        {{-- <th>Status</th>
                                        <th>Job Req.</th>
                                        <th>Bench Req.</th> --}}
									</tr>
								</thead>
                                <tbody class="text-gray-600 fw-bold" id="companyListajax">
                                </tbody>
							</table>
                            <div id="paginationcopanylist"></div>
					
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

<div id="kt_billing_address_toggle"></div>

<div id="kt_activitiesApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_billing_address_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
    <div class="card shadow-none rounded-0 w-100" id="application_show_right_bar">


        <div class="card-header px-5 py-0" id="kt_activities_header">
            <div class="d-flex align-items-center py-1">
                <a href="javascript:void(0);" id=""
                    class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"> <span
                        class="svg-icon svg-icon-primary svg-icon-1x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                <path
                                    d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                    fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                <path
                                    d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                    fill="#000000" fill-rule="nonzero"></path>
                            </g>
                        </svg>
                    </span> Billing Address
                </a>

            </div>

            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                    id="kt_activitiesApplication_close"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                fill="#000000">
                                <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                <rect fill="#000000" opacity="0.5"
                                    transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                    x="0" y="7" width="16" height="2" rx="1" />
                            </g>
                        </svg>
                    </span> </button>
            </div>
        </div>
        <div class="card card-custom mx-2">
            <div class="card-body py-0">
                <div class="tab-content pt-5" id="kt_billing_address_body">



                    
                </div>
            </div>
        </div>


    </div>
</div>







<!--end::content-->


<!--<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="icon-badge"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">@lang('modules.dashboard.totalCompanies')</span>
                <span class="info-box-number">{{ number_format($totalCompanies) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="icon-badge"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">@lang('modules.dashboard.activeCompanies')</span>
                <span class="info-box-number">{{ number_format($activeCompanies) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="icon-badge"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('modules.dashboard.inactiveCompanies')</span>
                <span class="info-box-number">{{ number_format($inactiveCompanies) }}</span>
            </div>
        </div>
    </div>
</div>-->



@endsection
@push('footer-script')

<!--<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>-->
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
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>




	    // var table = $('#myTable2').dataTable({
            
		// 		responsive     	: true, 
      	// 		processing      : true,
		// 		ordering		: false, //disable ordering      			
      	// 		destroy       	: true,   
      	// 		stateSave     	: true,      
      	// 		"bLengthChange"   : false,   
		// 		sDom			: 'lrtip',   
		// 	searching: false,
        //     serverSide: true,
        //     ajax: '{!! route('superadmin.company.data') !!}',
        //     language: {
        //         "url": "<?php echo __("app.datatable") ?>"
        //     },
        //     "fnDrawCallback": function( oSettings ) {
        //         $("body").tooltip({
        //             selector: '[data-toggle="tooltip"]'
        //         });
        //     },
        //     columns: [
        //         { data: 'chkbox', name: 'chkbox'},
		// 	    { data: 'Name', name: 'Name' },
        //         { data: 'Location', name: 'Location' },
        //         { data: 'Connected', name: 'Connected' },
        //         { data: 'Requisitions', name: 'Requisitions' },
        //         { data: 'Joining', name: 'Joining' },
        //         { data: 'Bench', name: 'Bench' },
        //         { data: 'OperationalPack', name: 'OperationalPack' },
        //         { data: 'SalesPack', name: 'SalesPack' },
        //         { data: 'SpartnerAll', name: 'SpartnerAll' }
        //     ]
        // });

        // new $.fn.dataTable.FixedHeader( table );


        $(document).on('click', '.show-detail', function ()
        {
        
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

		$('body').on('click', '.sa-params', function(){
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

        $(document).on('click','.code_generator',function(){
                // e.preventDefault();
                var id = $(this).data('id');
                // alert(id);
                // return false;
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                var url       	=	"{{ route('superadmin.company.updateAccessCode') }}";
                var token       =	"{{ csrf_token() }}";

                                        $.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'id' : id},
													beforeSend	: function() {
																				$('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
                                                        if(response.status==="success"){
                                                            Swal.fire({
                                                                title: "Success",
                                                                html: `
                                                                    <p>This code for login</p>
                                                                    <p>Copy this code.</p>
                                                                    <input type="text" id="accessCode" value="${response.access_code}" readonly style="width: 100%; padding: 5px;">
                                                                    <button id="copyCodeBtn" style="margin-top: 10px; padding: 5px 10px;">Copy Code</button>
                                                                `,
                                                                icon: "success",
                                                                showConfirmButton: true
                                                            }).then(function(){
                                                
                                                            });	
                                                        }else{
                                                            $('#checkActive').removeClass('btn-light-success').addClass('btn-light-danger');
                                                            $('#checkActive').text("Inactive");
                                                        }
                                                        //  return false;
														}
										});	

               

        });

        $(document).on('click', '#copyCodeBtn', function() {
            var copyText = document.getElementById("accessCode");
            copyText.select();
            document.execCommand("copy");
            Swal.fire("Copied!", "The Access code has been copied to your clipboard.", "success");
        });

        $(document).on('click', '#copyText', function() {
            // Create a temporary input to hold the text to be copied
            var tempInput = document.createElement("input");
            
            // Set the input's value to the URL
            tempInput.value = "{{ route('super.login.form') }}";
            
            // Append the input to the document body
            document.body.appendChild(tempInput);
            
            // Select the input's content
            tempInput.select();
            
            // Copy the content to clipboard
            document.execCommand("copy");
            
            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Show a confirmation message
            $('#copyText').text('URL copied');
            setTimeout(() => {
                $('#copyText').text('URL Copy');
            }, 2000);
            
        });

        $(document).on('click', '.show-billing-address', function() {
            var id = $('#hid_client_id').val();
            var url       	=	"{{ route('superadmin.company.billinDetails') }}";
            $('#kt_billing_address_toggle').trigger('click');
            var token       =	"{{ csrf_token() }}";
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 

            $.ajax({
                                                type		: 'POST',
                                                url			: url,
                                                data		: {'_token': token, '_method': 'POST', 'id' : id},
                                                beforeSend	: function() {
                                                                            $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                                },
                                                success 	: function (response) {
                                                    
                                                        $('#kt_billing_address_body').html(response.view);

                                                        var country_id = response.countrie_id;
                                                        var state_id = response.state_id;
                                                        var city_id = response.city_id;
                                                        setTimeout(() => {
                                                            $('#country').val(country_id).change();
                                                        }, 200);

                                                    
                                                    //  return false;
                                                    }
                                    });	


        
                var countrie_id = $('#country').val();
                if(countrie_id==101){
                    
                    $('#gstin_text').text('GSTIN')
                }else{
                    $('#gstin_text').text('TAXIN')
                }




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
                                                        $('.checkActivetext').removeClass('text-danger').addClass('text-success');
                                                        $('.checkActivetext').text("Active");
                                                    }else{
                                                        $('.checkActivetext').removeClass('text-success').addClass('text-danger');
                                                        $('.checkActivetext').text("Suspend");
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

        $(document).on('click','.app_int_amount',function(){

            var selectedValue = $("input[name='app_int_amount']:checked").val();
            //   alert(selectedValue);
            // e.preventDefault();
            var currency_get = $('#currency_get').val();
            var gst_on_get = $('#gst_on_get').val();
            var id = $('#hid_client_id').val();
            var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
            var url       	=	"{{ route('superadmin.company.topuptypegetprice') }}";
            var token       =	"{{ csrf_token() }}";

                    $.ajax({
                                                type		: 'POST',
                                                url			: url,
                                                data		: {'_token': token, '_method': 'POST', 'id' : selectedValue,'currency_get':currency_get,'gst_on_get':gst_on_get},
                                                beforeSend	: function() {
                                                                            $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                                },
                                                success 	: function (response) {
                                                    $('.price_get').val(response.price);
                                                    $('.coins_get').val(response.coins);
                                                    $('.gsttax_text').html(response.gst+'%');
                                                    $('.subtotal_text').html(response.currency+' '+response.price);
                                                    $('.total_price_text').html(response.currency+' '+response.total_price);

                                                    $('.gsttax_hidden_value').val(response.gst);
                                                    $('.gst_amount_hidden_value').val(response.gst_amount);
                                                    $('.total_price_hidden_value').val(response.total_price);
                                                    $('#discount').val(response.discount);


                                                    $('.total_price_hidden_value').attr('readonly', true);

                                                    if(response.custome_status){
                                                        $('.price_get').removeAttr('readonly');
                                                        $('.coins_get').removeAttr('readonly');
                                                    }else{
                                                        $('.price_get').attr('readonly', true);
                                                        $('.coins_get').attr('readonly', true);
                                                    }
                                                    //  return false;
                                                    }
                    });	

        });

        $(document).on('change','#payment-mode-selector',function(e){
                var paymentMode = $(this).val();
                var paymentMethodSelector = $('#payment-method-selector');
                var paymenLink = $('#payment-method-selector').val();
                $('#paymentLinkPaymentstatus').val('');
                $('#paymentLinkpayment_orderId').val('');
                $('#paymentLinkshort_url').val('');
                $('.paymentLinkshow').hide();
                if (paymenLink === 'razorpay') {
                        $('.paymentLinkGenerate').show();
                } else { //paymentLinkGenerate
                    $('.paymentLinkGenerate').hide();
                }

                // Clear current payment method options
                paymentMethodSelector.html('<option value="">Select Payment Method</option>');

                if (paymentMode === 'Payment Link') {
                    // Show only Razorpay and PhonePe options
                    paymentMethodSelector.append('<option value="razorpay">Razorpay</option>');
                    // paymentMethodSelector.append('<option value="phonepe">PhonePe</option>');
                } else {
                    // Reset to default options
                    paymentMethodSelector.append('<option value="offline">Offline</option>');
                    paymentMethodSelector.append('<option value="online">Online</option>');
                }
        });

        $(document).on('change','#payment-method-selector',function(e){
            var paymentMode = $(this).val();
                if (paymentMode === 'razorpay') {
                        $('.paymentLinkGenerate').show();
                } else { //paymentLinkGenerate
                    $('.paymentLinkGenerate').hide();
                }
        });


        $(document).on('change','#currency_get,#topUptype',function(e){
            e.preventDefault();

            var currency_get = $('#currency_get').val();
            var topUptype  	= $('#topUptype').val();
            var url   		= "{{ route('superadmin.company.priceCurrencyWise') }}";
            
            var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
            $.ajax({
                            type  : 'GET',
                            url   : url,
                            data: {'currency_get':currency_get,'topUptype':topUptype},
                            beforeSend: function() {
                                    $('#currencyPrice').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                            success : function (response) {

                                    $('#currencyPrice').html(response);

                                    $('#discount').val('');
                                    $('#gst').val('');
                                    $('.price_get').val('');
                                    $('.coins_get').val('');
                                    $('.gst_amount_hidden_value').val('');
                                    $('.total_price_hidden_value').val('');
                            }
                    });

        });   

        $(document).on('submit','#candidate_new_form', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser
                    var form = $(this);
                    // var url = form.attr('action');
                    var formData = form.serialize();
                    $('.indicator-label-can').hide();
                    $('.indicator-progress-can').show();
                    $('#candidate_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.googleSheetCandidateData')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#candidate_new_submit').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                        }
                        // $("#candidate_new_form")[0].reset();
                        $('.indicator-label-can').show();
                        $('.indicator-progress-can').hide();
                        $('#successButtoncan').show();
                        $('#errorButtoncan').show();
                        $('#successButtoncan').text(response.done+" candidate uploaded successfully");
                        $('#errorButtoncan').text(response.pendind+" candidate pending");
                        setTimeout(() => {
                            $('#successButtoncan').hide();
                            $('#errorButtoncan').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {

                        var errors = xhr.responseJSON.errors;
                            $('.error').empty(); // Clear any previous errors

                            if (errors.googlesheetid) {
                            alert("Candidate google sheet Id id required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#name-error').html(errors.googlesheetid[0]);
                            }
                            if (errors.job_id) {
                            alert("Job is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.teams) {
                            alert("Team is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                            }
                            if (errors.current_ctc_cur) {
                            alert("Currency is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.current_ctc_mode) {
                            alert("Currency Mode is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                        }
                    });
        });

        $(document).on('submit','#candidate_new_form_job', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser
                    var form = $(this);
                    // var url = form.attr('action');
                    var formData = form.serialize();
                    $('.indicator-label-can-job').hide();
                    $('.indicator-progress-can-job').show();
                    $('#candidate_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.googleSheetCandidateDataJob')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#candidate_new_submit_job').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                        }
                        // $("#candidate_new_form")[0].reset();
                        $('.indicator-label-can-job').show();
                        $('.indicator-progress-can-job').hide();
                        $('#successButtoncanJob').show();
                        $('#errorButtoncanJob').show();
                        $('#successButtoncanJob').text(response.done+" Jobs uploaded successfully");
                        $('#errorButtoncanJob').text(response.pendind+" Jobs pending");
                        setTimeout(() => {
                            $('#successButtoncanJob').hide();
                            $('#errorButtoncanJob').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {

                        var errors = xhr.responseJSON.errors;
                            $('.error').empty(); // Clear any previous errors

                            if (errors.googlesheetid) {
                            alert("Job google sheet Id id required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#name-error').html(errors.googlesheetid[0]);
                            }
                            if (errors.job_id) {
                            alert("Job is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.teams) {
                            alert("Team is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                            }
                            if (errors.current_ctc_cur) {
                            alert("Currency is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.current_ctc_mode) {
                            alert("Currency Mode is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                        }
                    });
        });

        $(document).on('change','#clientID',function(e){
            e.preventDefault();
                var client_id 	=	$(this).val();				
                        
                if(client_id > 0)
                {
                    
                    $('#kt_activitiesRecruiter_toggle').trigger('click');   						
                    
                    var clientName    	=   $("#clientId option:selected"); 
                    var text_value 		=	clientName.text();              
        
                    var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';						
                    str					=	text_value;
                    str					=	str.substring(0, 1);
                    str	= strVal		=	str.toLowerCase();
                    var str_bg			=	str.charCodeAt(0);
                    
                    if(str_bg >=97 && str_bg <=101){
                    bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
                    }else if(str_bg >=102 && str_bg <=106){
                    bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
                    }else if(str_bg >=107 && str_bg <=111){
                    bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
                    }else if(str_bg >=112 && str_bg <=116){
                    bgTextDecore		=	'fs-3 bg-light-success text-success';	
                    }else if(str_bg >=117 && str_bg <=121){
                    bgTextDecore		=	'fs-3 bg-dark text-light';	
                    }else {
                    bgTextDecore		=	'fs-3 bg-light text-dark';	
                    }											
                    $('#light_recruiter_caption').html(strVal.toUpperCase()); 		
                    $('#light_recruiter_caption').addClass(bgTextDecore); 						
                    
                    
                    var recruiter	=	$('#recruiter').val();
                    var token 		=	"{{ csrf_token() }}";
                    
                        var id    		=	client_id;
                            var url   		=	"{{ route('superadmin.company.hiring_manager',':id') }}"; 
                            url     		=	url.replace(':id', id);  		
                            var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
                    $.ajax({
                            type  		: 'GET',
                            url   		: url,
                            data		: {'_token': token, 'hiring_manager' : client_id},
                            beforeSend	: function() {
                                    $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                            success 	: function (response) 
                            {
                                                    // Get the select element where you want to add options
                            const selectElement = $('#hiring_namager'); // Replace with your actual select element ID

                            // Clear any existing options first
                            selectElement.empty();

                            // Loop through the hiring_manager data
                            response.hiring_manager.forEach(function(manager) {
                                // Append each manager as an option in the select element
                                selectElement.append(
                                    $('<option>', {
                                        value: manager.contact_person_name, // Set the option's value
                                        text: manager.contact_person_name // Set the text displayed in the dropdown
                                    })
                                );
                            });   
                            }
                        }); 
                            
                }
                else
                {
                    alert("Please select any client after that choose our Recruiters team");
                }	
                                            
        });


        $(document).on('submit','#client_new_form', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser
                    var form = $(this);
                    // var url = form.attr('action');
                    var formData = form.serialize();
                    $('.indicator-label-client').hide();
                    $('.indicator-progress-client').show();
                    $('#client_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.googleSheetInsertdataInDatabase')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#client_new_submit').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-client').show();
                            $('.indicator-progress-client').hide();
                            return false;
                        }
                        $("#client_new_form")[0].reset();
                        $('.indicator-label-client').show();
                        $('.indicator-progress-client').hide();
                        $('#successButtonclient').show();
                        $('#errorButtonclient').show();
                        $('#successButtonclient').text(response.done+" client uploaded successfully");
                        $('#errorButtonclient').text(response.pendind+" client pending");
                        setTimeout(() => {
                            $('#successButtonclient').hide();
                            $('#errorButtonclient').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {

                        var errors = xhr.responseJSON.errors;
                            $('.error').empty(); // Clear any previous errors

                            if (errors.googlesheetid) {
                            alert("Client google sheet Id id required");
                            $('.indicator-label-client').show();
                            $('.indicator-progress-client').hide();
                            return false;
                                // $('#name-error').html(errors.googlesheetid[0]);
                            }
                            if (errors.teams) {
                            alert("Team is required");
                            $('.indicator-label-client').show();
                            $('.indicator-progress-client').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                        }
                    });
        });

        $(document).on('submit','#operational_top_up_new_form', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser

                    // var selectedValue = $("input[name='app_int_amount']:checked").val();
                    // if(selectedValue==''){
                    //     swal("Required!", response.message, "error");
                    //     return false;
                    // }
                    

                    var form = $(this);
                    // var url = form.attr('action');
                    
                    var formData = $('#operational_top_up_new_form').serialize();
                    var formData = form.serialize();
                    $('.indicator-label-top-up').hide();
                    $('.indicator-progress-top-up').show();
                    // $('#operational_top_up_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.topUpCoinsAdded')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#operational_top_up_new_submit').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-top-up').show();
                            $('.indicator-progress-top-up').hide();
                            return false;
                        }else if(response.status=='offline'){
                            Swal.fire("Success", "Payment has been successfully ", "success").then(function(){
                                $('#kt_activitiesClient_close').click();
                            });	
                            return false;
                        }else if(response.status=='razorpay'){
                            Swal.fire("Success", "Payment link has been send successfully ", "success").then(function(){
                                $('#kt_activitiesClient_close').click();
                            });	
                            return false;
                        }

                        initiateRazorpayPayment(response.orderId, response.amount,response.update_coins,response.company_id,response.event_type);

                        // $("#operational_top_up_new_form")[0].reset();
                        $('.indicator-label-top-up').show();
                        $('.indicator-progress-top-up').hide();
                        $('#successButtonoperationaltopup').show();
                        $('#errorButtonoperationaltopup').hide();
                        $('#successButtonoperationaltopup').text(" Top Up Coins uploaded successfully");
                        $('#errorButtonoperationaltopup').text(" ");
                        setTimeout(() => {
                            $('#successButtonoperationaltopup').hide();
                            $('#errorButtonoperationaltopup').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {
                            // Check if the response has validation errors
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;

                                // Clear any previous error messages
                                $('.error').empty(); 

                                // Display specific error messages based on validation errors
                                if (errors.topUptype) {
                                    alert("Top Up Type is required");
                                }
                                if (errors.price) {
                                    alert("Price is required");
                                }
                                if (errors.paymentmodeselector) {
                                    alert("Payment mode is required");
                                }
                                if (errors.paymentmethodselector) {
                                    alert("Payment method is required");
                                }
                                if (errors.currency) {
                                    alert("Currency is required");
                                }
                                if (errors.chequenumber) {
                                    alert("Cheque Number is required");
                                }
                                if (errors.chequedate) {
                                    alert("Cheque Date is required");
                                }
                                if (errors.bankname) {
                                    alert("Bank Name is required");
                                }
                                if (errors.transactionid) {
                                    alert("Transaction ID is required");
                                }
                                if (errors.bankreference) {
                                    alert("Bank Reference Number is required");
                                }

                                // Show/Hide progress indicators as needed
                                $('.indicator-label-top-up').show();
                                $('.indicator-progress-top-up').hide();
                            } else {
                                // Handle other types of errors (e.g., server errors)
                                alert("An unexpected error occurred. Please try again later.");
                            }
                        }


                    });


        });

        $(document).on('submit','#boost_keyword_form', function(e) {
            e.preventDefault();
            var form = $(this);
             var formData = $('#boost_keyword_form').serialize();
             var formData = form.serialize();
            $.ajax({
                type: "POST",
                url: '{{route('superadmin.company.boostkeywordadd')}}'+'?_token=' + '{{ csrf_token() }}',
                data: formData,
                success: function(response) {
                    Swal.fire("Success", "Keyword has been added successfully ", "success").then(function(){
                                $('#kt_activitiesClient_close').click();
                            });	
                },
                error: function(xhr, status, error) {
                   
                }
            });
        });
        

        function paymentLinkGenerate(){
            var total_price  = $('.total_price_hidden_value').val();
            var currency_get  = $('#currency_get').val();
            var company_id =  $('#company_id_topup').val();
            var packages_id = $("input[name='app_int_amount']:checked").val();

            $.ajax({
                    url: '{{ route('superadmin.custompackages.generatecustompackages', @$company_id) }}',
                    type: "GET",
                    data: {'total_price':total_price,'currency_get':currency_get,'company_id':company_id,'packages_id':packages_id,'mainType':2},
                    success: function(response) {
                        //
                        if(response.status=='success'){
                            // alert(response.short_url);
                            $('#paymentLinktextToCopy').text(response.short_url);
                            $('#paymentLinkPaymentstatus').val(response.Paymentstatus);
                            $('#paymentLinkpayment_orderId').val(response.payment_orderId);
                            $('#paymentLinkshort_url').val(response.short_url);
                            $('.paymentLinkshow').show();
                            $('#paymentLinkshow').val(response.short_url);
                        }else{
                            $('.paymentLinkshow').hide();
                            alert('Failed to create payment link');
                        }
                        
                    },
                    error: function(response) {
                        handleFails(response);
                        alert('Failed to create payment link');
                        $('.paymentLinkshow').hide();
                    }
                });

        }

        function initiateRazorpayPayment(orderId, amount,update_coins,company_id,event_type) {
            var options = {
                "key": "{{ $payment_settings->razorpay_key}}", // Razorpay API Key
                "amount": amount, // Amount in paisa
                "currency": "INR",
                "name": name,
                "description": "Test Transaction",
                "order_id": orderId, // Order ID created in backend
                "handler": function (response){

                

                    var formData = {
                        _token: "{{ csrf_token() }}", // CSRF token for security
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id, // Optional, if you want to pass it back
                        razorpay_signature: response.razorpay_signature, // Optional, if needed
                        razorpay_update_coins: update_coins, // Optional, if needed
                        razorpay_company_id: company_id, // Optional, if needed
                        razorpay_event_type: event_type, // Optional, if needed
                        razorpay_amount: amount // Optional, if needed
                    };


                    // var formData = $('#operational_top_up_new_form').serialize();
                    // formData.push({ name: '_token', value: "{{ csrf_token() }}" });
                    // formData.push({ name: 'razorpay_payment_id', value: response.razorpay_payment_id });
                    // formData.push({ name: 'razorpay_order_id', value: response.razorpay_order_id });
                    // formData.push({ name: 'razorpay_signature', value: response.razorpay_signature });
                    // console.log(response);
                    $.ajax({
                        url: "{{ route('payment.callback') }}", // Your callback route
                        method: 'POST',
                        data: formData,
                        success: function (response) {
                            if (response.status) {
                                alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
                                // Handle success (e.g., redirect or show success message)
                                // window.location.href = response.redirect_url;
                            } else {
                                // Handle failure (e.g., show error message)
                                alert('Payment failed. Please try again.');
                            }
                            },
                            error: function (error) {
                                console.log(error);
                                alert('An error occurred. Please try again.');
                            }
                    });
                            
                },
                // "prefill": {
                //     "name": name,
                //     "email": email,
                // },
                "theme": {
                    "color": "#F37254"
                }
            };

            var rzp = new Razorpay(options);
            rzp.open(); // Open the Razorpay payment window
        }

        $(document).on('keyup','.price_get,.coins_get,#gst', function(){
            // alert("dk");
            gst_calculate();
        });
        $(document).on('change','#gst_on_get', function(){
            gst_calculate();
        });
        function gst_calculate(){
            var currency_get = $('#currency_get').val(); // Get the selected currency
            var price = parseFloat($('.price_get').val()); // Get and parse the price value
            var gst_on_get = $('#gst_on_get').val(); // Get and parse the GST percentage (if needed)
            var gst = parseFloat($('#gst').val()); // Get and parse the GST percentage (if needed)

            // alert(gst);
            var discount = parseFloat($('#discount').val()); // Get and parse the price value
            var discount_amount = price * discount / 100; // 
            discount_amount_final = price;
            if(discount>0){
                var discount_amount_final = price-discount_amount;
            }


            if (currency_get === "INR" && gst_on_get==="Yes") {
                var gst_amount = discount_amount_final * gst / 100; // Calculate GST amount at 18%
                var total_amount = discount_amount_final + gst_amount; // Calculate total amount with GST
               
               
                $('.gst_amount_hidden_value').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_hidden_value').val(gst); // Show GST amount (2 decimal places)
                $('.total_price_hidden_value').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)

                $('.subtotal_text').text(discount_amount_final.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_text').text(gst); // Show GST amount (2 decimal places)
                $('.total_price_text').text(total_amount.toFixed(2)); // Show total amount (2 decimal places)

            }else if(currency_get === "INR" && gst_on_get==="No") {
               
                 var gst_amount = discount_amount_final * 0 / 100; // Calculate GST amount at 18%
                 var total_amount = discount_amount_final + gst_amount; // Calculate total amount with GST
                
                // Displaying the calculated amounts (you can update this part as needed)
                $('.gst_amount_hidden_value').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_hidden_value').val(0); // Show GST amount (2 decimal places)
                $('.total_price_hidden_value').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)
            } else {
                $('.total_price_hidden_value').val(discount_amount_final.toFixed(2)); // Show total amount (2 decimal places)
                $('.gsttax_hidden_value').val(0);
                $('.gst_amount_hidden_value').val(0);
                // If needed, handle cases for other currencies
                // alert('GST calculation is only applicable for INR currency.');
            }
        }
        $(document).on('keyup','.coins_get', function(){
               total_coins();
        });
        $(document).on('keyup','#discount', function(){
        gst_calculate();
        });

        function total_coins(){
                let value = parseFloat($('.coins_get').val());
                let percentage = parseFloat($('#discount').val());

                if (isNaN(value)) {
                    value = 0; // Default to 0 if invalid
                }
                if (isNaN(percentage)) {
                    percentage = 0; // Default to 0 if invalid
                }

                var total_coins = value; // Add the percentage amount to the original value
                if(percentage !=''){
                    let additionalAmount = (percentage / 100) * value;
                    var total_coins = value - additionalAmount; // Add the percentage amount to the original value
                }
                
            
                // $('.price_get').val(total_coins.toFixed(2)); // Set the value with two decimal points
                gst_calculate();

        }
		
</script>

<script>

        $(document).ready(function() {
                    // $.noConflict();

                    fetchCompanyAjax();
                    
                    $(document).on('click', '#paginationcopanylist .page-link', function(e) {
                        e.preventDefault();

                        const page = $(this).data('page');
                        if (!page || $(this).hasClass('disabled')) return;

                        // Disable the clicked pagination link
                        $(this).addClass('disabled');

                        // Fetch data
                        fetchCompanyAjax(page, $('#search').val()).then(() => {
                            // Re-enable the links after successful data fetch
                            $('#paginationcopanylist .page-link').removeClass('disabled');
                        }).catch(() => {
                            // Re-enable the links even if there's an error
                            $('#paginationcopanylist .page-link').removeClass('disabled');
                        });
                    });


        });

        $('#searchInput').keyup(function() {
            fetchCompanyAjax();
	   });

       function filtersdataget(){
         fetchCompanyAjax();
       }


 
        function fetchCompanyAjax(page = 1, search = '') {
            var clientid = '1';
            $('.loadingMessagecompanyListajax').css({ display: 'block' });
            $.ajax({
                url: `{!! route('superadmin.company.data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  parm : true,
                  search : $('#searchInput').val(),
                  sortingfilter : $('#sortingfilter').val()
                },
                success: function(response) {
                    $('.loadingMessagecompanyListajax').css({ display: 'none' });
                    $('#companyListajax').html(response.html);
                    $('#paginationcopanylist').html(response.pagination);
                }
            });
        }

</script>

@endpush
