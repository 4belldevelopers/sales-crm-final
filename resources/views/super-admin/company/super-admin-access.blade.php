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
        
	</div>
</div>
<!--end::toolbar-->

<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-5" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8">
           
        </div>
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 mt-4">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">&nbsp;</span>
                            <span class="text-muted mt-1 fw-bold fs-7">&nbsp;</span>
                        </h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 d-none" id="myTable">
                                <thead class="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-25px">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-9-check">
                                            </div>
                                        </th>
                                        <th class="min-w-200px">@lang('menu.companies')</th>
                                        <th class="min-w-150px">@lang('menu.packages')</th>
                                        <th class="min-w-150px">@lang('app.status')</th>
                                        <th class="min-w-100px">@lang('app.action')</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="">
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="https://bot.4bell.com/super-admin/company/1" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Technology</a>
                                                    <span class="text-muted fw-bold text-muted d-block fs-7">4belltechnology@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Professional</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">Annually</span>
                                        </td>
                                        <td class="">
                                            <label class="badge text-success text-dark-800 fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>
                                        </td>
                                        <td>
                                            <div class="">
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <img src="https://www.hcltech.com/sites/default/files/2021-05/guideline_based_vectors-square.png?itok=KEUChzF3" alt="">
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="https://bot.4bell.com/super-admin/company/2" class="text-dark fw-bolder text-hover-primary fs-6">HCL Technologies</a>
                                                    <span class="text-muted fw-bold text-muted d-block fs-7">hcltechnologies@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Trail Package</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">Monthly</span>
                                        </td>
                                        <td class="">
                                            <label class="badge text-danger text-dark-800 fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>
                                        </td>
                                        <td>
                                            <div class="">
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javscript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javscript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <img src="data:image/png;base64,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" alt="">
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="https://bot.4bell.com/super-admin/company/3" class="text-dark fw-bolder text-hover-primary fs-6">Adobe</a>
                                                    <span class="text-muted fw-bold text-muted d-block fs-7">adobe@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Premium Package</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">Yearly</span>
                                        </td>
                                        <td class="">
                                            <label class="badge text-success text-dark-800 fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>
                                        </td>
                                        <td>
                                            <div class="">
                                                <a href="javscript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <img src="https://indiaobservers.com/wp-content/uploads/2020/09/Flipkart.jpg" alt="">
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="https://bot.4bell.com/super-admin/company/4" class="text-dark fw-bolder text-hover-primary fs-6">Flipkart</a>
                                                    <span class="text-muted fw-bold text-muted d-block fs-7">flipkart@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Trail Package</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">7 days</span>
                                        </td>
                                        <td class="">                                                    
                                            <label class="badge text-danger text-dark-800 fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>
                                        </td>
                                        <td>
                                            <div class="">
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <img src="https://play-lh.googleusercontent.com/p6xjj89UgPwnGeTtslDnubuGIFgEF-q_ks8Pq0kx4HvQmX6noAs-16Fk4YN1dS9frwg0" alt="">
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="https://bot.4bell.com/super-admin/company/5" class="text-dark fw-bolder text-hover-primary fs-6">Tech Mahindra</a>
                                                    <span class="text-muted fw-bold text-muted d-block fs-7">techmahindra@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Plus Package</a>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">Annually</span>
                                        </td>
                                        <td class="">                                                    
                                            <label class="badge text-success text-dark-800 fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>
                                        </td>
                                        <td>
                                            <div class="">
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"></path>
                                                            <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
							
							
							<table id="myTable2" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
								<thead>
									<tr>
										<th> <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input widget-9-check" type="checkbox" value="1">
                                            </div>
										</th>										
										<th>Company Name</th>
										<th>Username</th>
										<th>Code Generator</th>
                                        <th>Status</th>
									</tr>
								</thead>
							</table>
					
					
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
    

	  var table = $('#myTable2').dataTable({
            
				responsive     	: true, 
      			processing      : true,
				ordering		: false, //disable ordering      			
      			destroy       	: true,   
      			stateSave     	: true,      
      			"bLengthChange"   : false,   
				sDom			: 'lrtip',   
				
			//responsive: true,
            // processing: true,
			searching: false,
            serverSide: true,
            ajax: '{!! route('superadmin.company.accessData') !!}',
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'chkbox', name: 'chkbox'},
			    { data: 'Name', name: 'Name' },
                { data: 'Username', name: 'Username' },
                { data: 'Code', name: 'Code' },
                { data: 'Status', name: 'Status' }
            ]
        });

        new $.fn.dataTable.FixedHeader( table );


        table.on('click', '.show-detail', function ()
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

    table.on('click', '.show-detail-direct', function ()
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

                    // Clear current payment method options
                    paymentMethodSelector.html('<option value="">Select Payment Method</option>');

                    if (paymentMode === 'Payment Link') {
                        // Show only Razorpay and PhonePe options
                        paymentMethodSelector.append('<option value="razorpay">Razorpay</option>');
                        paymentMethodSelector.append('<option value="phonepe">PhonePe</option>');
                    } else {
                        // Reset to default options
                        paymentMethodSelector.append('<option value="offline">Offline</option>');
                        paymentMethodSelector.append('<option value="online">Online</option>');
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



        $(document).on('keyup','.price_get,.coins_get', function(){
            gst_calculate();
        });
        $(document).on('change','#gst_on_get', function(){
            gst_calculate();
        });
        function gst_calculate(){
            var currency_get = $('#currency_get').val(); // Get the selected currency
            var price = parseFloat($('.price_get').val()); // Get and parse the price value
            var gst_on_get = $('#gst_on_get').val(); // Get and parse the GST percentage (if needed)

            if (currency_get === "INR" && gst_on_get==="Yes") {
                var gst_amount = price * 18 / 100; // Calculate GST amount at 18%
                var total_amount = price + gst_amount; // Calculate total amount with GST
               
               
                $('.gst_amount_hidden_value').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_hidden_value').val(18); // Show GST amount (2 decimal places)
                $('.total_price_hidden_value').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)

                $('.subtotal_text').text(price.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_text').text(18); // Show GST amount (2 decimal places)
                $('.total_price_text').text(total_amount.toFixed(2)); // Show total amount (2 decimal places)

            }else if(currency_get === "INR" && gst_on_get==="No") {
               
                 var gst_amount = price * 0 / 100; // Calculate GST amount at 18%
                 var total_amount = price + gst_amount; // Calculate total amount with GST
                
                // Displaying the calculated amounts (you can update this part as needed)
                $('.gst_amount_hidden_value').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_hidden_value').val(0); // Show GST amount (2 decimal places)
                $('.total_price_hidden_value').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)
            } else {
               
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
        total_coins();
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
                
            
                $('.price_get').val(total_coins.toFixed(2)); // Set the value with two decimal points
                gst_calculate();

        }


		
</script>



<script>


   /* var table = $('#myTable').dataTable({
            
											responsive		: true,
            // processing: true,
            								serverSide		: true,
            								ajax			: '{!! route('superadmin.company.data') !!}',
            								language		: languageOptions(),
											"fnDrawCallback": function( oSettings ) {
												$("body").tooltip({
													selector: '[data-toggle="tooltip"]'
												});
											},
            columns: [
                { data: 'DT_Row_Index'},
                { data: 'logo', name: 'logo' },
                { data: 'company_name', name: 'company_name' },
                    @if(module_enabled('Subdomain'))
                { data: 'sub_domain', name: 'sub_domain' },
                    @else
                { data: 'company_email', name: 'company_email' },
                    @endif
                { data: 'package_id', name: 'package_id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });


        new $.fn.dataTable.FixedHeader( table );
*/
       /* $('body').on('click', '.sa-params', function(){
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
        });*/

   /* @if(module_enabled('Subdomain'))
    $('body').on('click', '.domain-params', function(){
        var company_id = $(this).data('company-id');
        var company_url = $(this).data('company-url');
        swal({
            title: "Are you sure?",
            text: `You want to notify company admins about company Login URL <br> Company URL: <a href="${company_url}"><b>${company_url}</b></a>`,
            html:true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Notify it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: "{{route('notify.domain')}}",
                    data: {'_token': token, 'company_id': company_id},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });
    @endif*/




   /* var table = $('#myTable').dataTable({
            responsive: true,
            // processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.company.data') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'DT_Row_Index'},
                { data: 'logo', name: 'logo' },
                { data: 'company_name', name: 'company_name' },
                    @if(module_enabled('Subdomain'))
                { data: 'sub_domain', name: 'sub_domain' },
                    @else
                { data: 'company_email', name: 'company_email' },
                    @endif
                { data: 'package_id', name: 'package_id' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });


        new $.fn.dataTable.FixedHeader( table );*/

        /*$('body').on('click', '.sa-params', function(){
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
        });*/

   /* @if(module_enabled('Subdomain'))
    $('body').on('click', '.domain-params', function(){
        var company_id = $(this).data('company-id');
        var company_url = $(this).data('company-url');
        swal({
            title: "Are you sure?",
            text: `You want to notify company admins about company Login URL <br> Company URL: <a href="${company_url}"><b>${company_url}</b></a>`,
            html:true,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Notify it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var token = "{{ csrf_token() }}";

                $.ajax({
                    type: 'POST',
                    url: "{{route('notify.domain')}}",
                    data: {'_token': token, 'company_id': company_id},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });
    @endif
*/
</script>

@endpush
