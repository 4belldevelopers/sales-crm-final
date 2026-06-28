@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <style>
        body
        {
            overflow-x: hidden;
        }
    </style>
@endpush

@section('create-button')
    <a href="javascript:;" id="create-language" class="btn btn-dark btn-sm m-l-15">
        <i class="fa fa-plus-circle"></i>@lang('app.createNew')
    </a>
    <a href="{{ url('/translations') }}" target="_blank" class="btn btn-warning btn-sm m-l-15">
        <i class="fa fa-cog"></i>@lang('app.translations')
    </a>
@endsection

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Language settings</h1>
        </div>
        <div class="d-flex align-items-center py-1 position-absolute end-0 me-4">
		        <a href="{{ route('superadmin.language-settings.create') }}" class="btn btn-sm btn-light btn-active-primary me-4">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                        </svg>
                    </span>    
                    Add Language
                </a>                
		        <a href="{{ url('/translations') }}" class="btn btn-sm btn-light btn-active-primary">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M13,5 L15,5 L15,20 L13,20 L13,5 Z M5,5 L5,20 L3,20 C2.44771525,20 2,19.5522847 2,19 L2,6 C2,5.44771525 2.44771525,5 3,5 L5,5 Z M16,5 L18,5 L18,20 L16,20 L16,5 Z M20,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,19 C22,19.5522847 21.5522847,20 21,20 L20,20 L20,5 Z" fill="#000000"/>
                                <polygon fill="#000000" opacity="0.3" points="9 5 9 20 7 20 7 5"/>
                            </g>
                        </svg>
                    </span>   
                    Translations
                </a>
		</div>
        <div class="d-flex align-items-center py-1"></div>
	</div>
</div>
<!--end::toolbar-->

<!--begin::data table-->
<!--<div class="post d-flex flex-column-fluid mt-10" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8 mx-0">
            <div class="col-xl-12 mt-4 px-0">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="myTable">
                                <thead class="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-25px">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-9-check">
                                            </div>
                                        </th>
                                        <th class="min-w-150px">@lang('app.language')</th>
                                        <th class="min-w-150px">@lang('app.code')</th>
                                        <th class="min-w-100px">@lang('app.status')</th>
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
                                                <div class="symbol symbol-50px overflow-hidden me-3">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">A</div>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder fs-6">Arabic</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">ar</a>
                                        </td>
                                        <td class="">
                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </label>
                                        </td>
                                        <td>
                                            <div class="">                                            
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-toggle="modal" data-target="#edit_language">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-toggle="tooltip" data-row-id="1" data-original-title="Delete">
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
                                                <div class="symbol symbol-50px overflow-hidden me-3">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">G</div>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder fs-6">German</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">de</a>
                                        </td>
                                        <td class="">
                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </label>
                                        </td>
                                        <td>
                                            <div class="">                                            
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-toggle="tooltip" data-original-title="Edit">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-toggle="tooltip" data-row-id="2" data-original-title="Delete">
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
                                                <div class="symbol symbol-50px overflow-hidden me-3">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">E</div>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder fs-6">English</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">en</a>
                                        </td>
                                        <td class="">
                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" checked="checked">
                                            </label>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-bolder d-block fs-6">Default language.</span>
                                             <span class="text-muted fw-bolder d-block fs-6">Default language cannot be updated or deleted.</span>
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
                                                <div class="symbol symbol-50px overflow-hidden me-3">
                                                    <a href="javascript:void(0);">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">S</div>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder fs-6">Spanish</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">es</a>
                                        </td>
                                        <td class="">
                                            <label class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1">
                                            </label>
                                        </td>
                                        <td>
                                            <div class="">                                            
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-toggle="tooltip" data-original-title="Edit">
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                                <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-toggle="tooltip" data-row-id="4" data-original-title="Delete">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>-->
<!--end::data table-->

<!--begin::add language popup-->
<div class="modal fade show" id="add_language" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Language</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <form id="add_language_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="javascript:void(0);">
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="required">Language Name</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Enter Language Name" name="language_name">
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <label class="required fs-6 fw-bold form-label mb-2">Language Code</label>
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-solid" placeholder="Enter Language Code" name="language_code">
                        </div>
                    </div>
                    <div class="row mb-10">
                        <div class="fv-row">
                            <label class="required fs-6 fw-bold form-label mb-2">Language Status</label>
                            <select class="form-select form-select-solid" aria-label="Select example">
                                    <option value="">Select status</option>
                                    <option value="1">Enabled</option>
                                    <option value="2">Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center align-items-center">
                <div class="text-center pt-5">
                    <button type="submit" id="add_language_submit" class="btn btn-primary">
                        <span class="indicator-label">Submit</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <button type="reset" id="add_language_cancel" class="btn btn-light me-3">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::add language popup-->

<!--begin::edit language popup-->
<div class="modal fade show" id="edit_language" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Language</h2>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" data-bs-target="#edit_langauge">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <form id="edit_language_form" class="form fv-plugins-bootstrap5 fv-plugins-framework" action="javascript:void(0);">
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
                            <span class="required">Language Name</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" placeholder="Enter Language Name" name="language_name" value="English">
                    </div>
                    <div class="d-flex flex-column mb-7 fv-row fv-plugins-icon-container">
                        <label class="required fs-6 fw-bold form-label mb-2">Language Code</label>
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-solid" placeholder="Enter Language Code" name="language_code" value="ar">
                        </div>
                    </div>
                    <div class="row mb-10">
                        <div class="fv-row">
                            <label class="required fs-6 fw-bold form-label mb-2">Language Status</label>
                            <select class="form-select form-select-solid" aria-label="Select example">
                                    <option value="">Select status</option>
                                    <option value="1">Enabled</option>
                                    <option value="2" selected="selected">Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center align-items-center">
                <div class="text-center pt-5">
                    <button type="submit" id="edit_language_submit" class="btn btn-primary">
                        <span class="indicator-label">Save</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <button type="reset" id="edit_language_cancel" class="btn btn-light me-3">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::edit language popup-->

<div class="modal fade bs-modal-lg" id="application-md-modal" tabindex="-1" role="dialog">
</div>


<!--begin::previous table-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="langTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>@lang('app.name')</th>
                                <th>@lang('app.code')</th>
                                <th>@lang('app.status')</th>
                                <th>@lang('app.action')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--end::previous table-->
@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

    <script>
        var langTable = $('#langTable').dataTable({
            responsive: true,
            // processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.language-settings.index') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                // Switchery
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                $('.js-switch').each(function () {
                    new Switchery($(this)[0], $(this).data());
                });

                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            order: [[0, 'ASC']],
            columns: [
                { data: 'serial_no',name: 'serial_no'},
                { data: 'language_name', name: 'language_name' },
                { data: 'language_code', name: 'language_code' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action' }
            ]
        });
        new $.fn.dataTable.FixedHeader( langTable );


        $('body').on('click', '.edit-language', function () {
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.language-settings.edit', ':id')}}';
            url = url.replace(':id', id);

            $('#modelHeading').html('@lang('app.edit') @lang('menu.language')');
            $.ajaxModal('#application-md-modal', url);
        });

        $('body').on('click', '#create-language', function () {
            var url = '{{ route('superadmin.language-settings.create') }}';

            $('#modelHeading').html('@lang('app.createNew') @lang('menu.language')');
            $.ajaxModal('#application-md-modal', url);
        });

        $('body').on('click', '.delete-language-row', function(){
            var id = $(this).data('row-id');

            Swal.fire({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('superadmin.language-settings.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                // $.unblockUI();
                               Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });
                                langTable._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('change', '.change-language-setting', function () {
            var id = $(this).data('lang-id');

            if ($(this).is(':checked'))
                var status = 'enabled';
            else
                var status = 'disabled';

            var url = '{{route('superadmin.language-settings.changeStatus', ':id')}}';
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: {'id': id, 'status': status, '_method': 'PUT', '_token': '{{ csrf_token() }}'},
                success: function (response) {
                    if (response.status == 'success') {
                        langTable._fnDraw();
                    }
                }
            })
        });

    </script>
@endpush