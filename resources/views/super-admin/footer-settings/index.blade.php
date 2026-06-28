@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <style>
    body
    {
        overflow-x: hidden;
    }
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active
    {
        color: #5e6278;
        background-color: #f5f8fa;
        border-color: #e4e6ef #e4e6ef #fff;
        color: black!important;
        border-bottom: 1px solid #f5f8fa;
    }
    hr
    {
        margin: 1rem 0;
        color: inherit;
        background-color: #898e91;
        border: 0;
        opacity: 0.25;
    }
    </style>
@endpush

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">@lang('modules.footerSettings.footerSettings')
                <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
			    <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
		    </h1>
        </div>
        <div class="d-flex align-items-center py-1">
		</div>
	</div>
</div>
<!--end::toolbar-->

<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-10" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8 mx-0">
            <div class="col-xl-12 mt-4 px-0">
                <div class="card">
                    <div class="card-body">
                        <form action="" class="ajax-form" id="createForm" method="POST">
                            @csrf
                            {{-- <div class="form-group">
                                <select name="language" class="form-control selectpicker" id="language_switcher" onchange="changeForm();">
                                    @forelse ($activeLanguages as $language)
                                    <option value="{{ $language->id }}" data-content=' <span class="flag-icon  @if($language->language_code == 'en') flag-icon-us @else  flag-icon-{{ $language->language_code }} @endif"></span> {{ ucfirst($language->language_code) }}'>{{ ucfirst($language->language_code) }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div> --}}
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @forelse ($activeLanguages as $language)
                                    <li class="nav-item">
                                        <a class="nav-link @if($language->language_code == 'en') active @endif" id="{{$language->language_code}}-tab" data-toggle="tab" data-language-id="{{$language->id}}" href="#{{$language->language_code}}" role="tab" aria-controls="{{$language->language_code}}" aria-selected="true">
                                            <span class="flag-icon  @if($language->language_code == 'en') flag-icon-us @else  flag-icon-{{ $language->language_code }} @endif"></span> {{ ucfirst($language->language_name) }}
                                        </a>
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                            <div id="copyright-section" class="py-10 fs-6 fw-bold form-label mb-2">
                                @include('super-admin.footer-settings.copyright-input', $footerSettings)
                            </div>
                            <div class="form-group">
                                <h5 class="box-title fs-6 fw-bold form-label mb-2">@lang('modules.footerSettings.socialLinks')</h5>
                                <span class="text-danger">@lang('modules.footerSettings.socialLinksNote')</span><br><br>
                                <div class="row">
                                    @forelse($footerSettings->social_links as $link)
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="{{ $link['name'] }}">
                                                    @lang('modules.footerSettings.'.$link['name'])
                                                </label>
                                                <input class="form-control form-control-solid" id="{{ $link['name'] }}" name="social_links[{{ $link['name'] }}]" type="url" value="{{ $link['link'] }}" placeholder="@lang('modules.footerSettings.enter'.ucfirst($link['name']).'Link')">
                                            </div>
                                        </div>
                                    @empty                                
                                    @endforelse
                                </div>
                            </div>
                            <div class="text-center py-10 mt-3">
                                <button class="btn btn-sm btn-light-success" id="save-form" type="button">Save & Update</button>
                            </div>
                        </form>
                        <hr>
                        <div id="footer-menu-setting">

                        <!--begin::toolbar-->
                            <div id="" class="container-fluid d-flex flex-stack pl-0 pt-5">
                                <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Footer Menu Setting</h1>
                                </div>
                                <div class="d-flex align-items-center py-1 position-absolute end-0 me-4">
                                        <a href="javascript:void(0);" onclick="javascript:createNewFooterMenuSetting();" class="btn btn-sm btn-light btn-active-primary me-4">
                                            <span class="svg-icon svg-icon-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                                </svg>
                                            </span>    
                                            Add New
                                        </a>
                                </div>
                            </div>
                        <!--end::toolbar-->

                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="myTable">
                                <thead class="">
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-25px">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-9-check">
                                            </div>
                                        </th>
                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-100px">Description</th>
                                        <th class="min-w-150px">Language</th>
                                        <th class="min-w-100px">Action</th>
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
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">H</div>
                                                    </a>
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bolder fs-6">Help</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">Help</a>
                                        </td>
                                        <td class="">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder d-block fs-6">ar</a>
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
                                </tbody>
                            </table>

                            <div class="table-responsive mt-3">
                                <table id="myTable" class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 ">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>@lang('app.name')</th>
                                            <th>@lang('app.description')</th>
                                            <th>@lang('app.language')</th>
                                            <th>@lang('app.action')</th>
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
</div>

<!--begin::add footer settings menu-->
<div class="modal fade show" id="kt_modal_new_address" tabindex="-1" aria-modal="true" role="dialog">
			<div class="modal-dialog modal-dialog-centered mw-650px">
				<div class="modal-content">
					<form class="form fv-plugins-bootstrap5 fv-plugins-framework" action="#" id="kt_modal_new_address_form">
						<div class="modal-header" id="kt_modal_new_address_header">
							<h2>Add Footer Menu</h2>
							<div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
								<span class="svg-icon svg-icon-1">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor"></rect>
										<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor"></rect>
									</svg>
								</span>
							</div>
						</div>
						<div class="modal-body py-10 px-lg-17">
							<div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px" style="max-height: 457px;">
								<div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
									<span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
											<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"></rect>
											<rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor"></rect>
											<rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"></rect>
										</svg>
									</span>
									<div class="d-flex flex-stack flex-grow-1">
										<div class="fw-bold">
											<h4 class="text-gray-900 fw-bolder">Warning</h4>
											<div class="fs-6 text-gray-700">Updating address may affter to your 
											<a href="#">Tax Location</a></div>
										</div>
									</div>
								</div>
								<div class="row mb-5">
									<div class="col-md-6 fv-row fv-plugins-icon-container">
										<label class="required fs-5 fw-bold mb-2">First name</label>
										<input type="text" class="form-control form-control-solid" placeholder="" name="first-name">
									<div class="fv-plugins-message-container invalid-feedback"></div></div>
									<div class="col-md-6 fv-row fv-plugins-icon-container">
										<label class="required fs-5 fw-bold mb-2">Last name</label>
										<input type="text" class="form-control form-control-solid" placeholder="" name="last-name">
									<div class="fv-plugins-message-container invalid-feedback"></div></div>
								</div>
								<div class="d-flex flex-column mb-5 fv-row fv-plugins-icon-container">
									<label class="d-flex align-items-center fs-5 fw-bold mb-2">
										<span class="required">Country</span>
										<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Your payment statements may very based on selected country" aria-label="Your payment statements may very based on selected country"></i>
									</label>
									<select name="country" data-control="select2" data-dropdown-parent="#kt_modal_new_address" data-placeholder="Select a Country..." class="form-select form-select-solid select2-hidden-accessible" tabindex="-1" aria-hidden="true" data-select2-id="select2-data-345-nzve">
										<option value="" data-select2-id="select2-data-347-bvsu">Select a Country...</option>
										<option value="AF">Afghanistan</option>
										<option value="AX">Aland Islands</option>
										<option value="AL">Albania</option>
										<option value="DZ">Algeria</option>
										<option value="AS">American Samoa</option>
									</select>
                                    <span class="select2 select2-container select2-container--bootstrap5" dir="ltr" data-select2-id="select2-data-346-89da" style="width: 100%;">
                                    <span class="selection">
                                        <span class="select2-selection select2-selection--single form-select form-select-solid" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-country-7t-container" aria-controls="select2-country-7t-container">
                                            <span class="select2-selection__rendered" id="select2-country-7t-container" role="textbox" aria-readonly="true" title="Select a Country...">
                                                <span class="select2-selection__placeholder">Select a Country...</span>
                                                </span>
                                                <span class="select2-selection__arrow" role="presentation">
                                                    <b role="presentation"></b>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="dropdown-wrapper" aria-hidden="true"></span></span>
								<div class="fv-plugins-message-container invalid-feedback"></div></div>
								<div class="d-flex flex-column mb-5 fv-row fv-plugins-icon-container">
									<label class="required fs-5 fw-bold mb-2">Address Line 1</label>
									<input class="form-control form-control-solid" placeholder="" name="address1">
								<div class="fv-plugins-message-container invalid-feedback"></div></div>
								<div class="d-flex flex-column mb-5 fv-row fv-plugins-icon-container">
									<label class="required fs-5 fw-bold mb-2">Address Line 2</label>
									<input class="form-control form-control-solid" placeholder="" name="address2">
								<div class="fv-plugins-message-container invalid-feedback"></div></div>
								<div class="d-flex flex-column mb-5 fv-row fv-plugins-icon-container">
									<label class="fs-5 fw-bold mb-2">Town</label>
									<input class="form-control form-control-solid" placeholder="" name="city">
								<div class="fv-plugins-message-container invalid-feedback"></div></div>
								<div class="row g-9 mb-5">
									<div class="col-md-6 fv-row fv-plugins-icon-container">
										<label class="fs-5 fw-bold mb-2">State / Province</label>
										<input class="form-control form-control-solid" placeholder="" name="state">
									<div class="fv-plugins-message-container invalid-feedback"></div></div>
									<div class="col-md-6 fv-row fv-plugins-icon-container">
										<label class="fs-5 fw-bold mb-2">Post Code</label>
										<input class="form-control form-control-solid" placeholder="" name="postcode">
									<div class="fv-plugins-message-container invalid-feedback"></div></div>
								</div>
								<div class="fv-row mb-5">
									<div class="d-flex flex-stack">
										<div class="me-5">
											<label class="fs-5 fw-bold">Use as a billing adderess?</label>
											<div class="fs-7 fw-bold text-muted">If you need more info, please check budget planning</div>
										</div>
										<label class="form-check form-switch form-check-custom form-check-solid">
											<input class="form-check-input" name="billing" type="checkbox" value="1" checked="checked">
											<span class="form-check-label fw-bold text-muted">Yes</span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer flex-center">
							<button type="reset" id="kt_modal_new_address_cancel" class="btn btn-light me-3">Discard</button>
							<button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary">
								<span class="indicator-label">Submit</span>
								<span class="indicator-progress">Please wait... 
								<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
							</button>
						</div>
					<div>                        
                    </div>
                </form>
				</div>
			</div>
		</div>
<!--end::add footer settinga menu-->

<br><br><br>

<!--begin::previous code-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="" class="ajax-form" id="createForm" method="POST">
                        @csrf
                        <h4 class="box-title">@lang('modules.footerSettings.footerSettings')</h4>
                        <hr>
                        {{-- <div class="form-group">
                            <select name="language" class="form-control selectpicker" id="language_switcher" onchange="changeForm();">
                                @forelse ($activeLanguages as $language)
                                <option value="{{ $language->id }}" data-content=' <span class="flag-icon  @if($language->language_code == 'en') flag-icon-us @else  flag-icon-{{ $language->language_code }} @endif"></span> {{ ucfirst($language->language_code) }}'>{{ ucfirst($language->language_code) }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div> --}}
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @forelse ($activeLanguages as $language)
                                <li class="nav-item">
                                    <a class="nav-link @if($language->language_code == 'en') active @endif" id="{{$language->language_code}}-tab" data-toggle="tab" data-language-id="{{$language->id}}" href="#{{$language->language_code}}" role="tab" aria-controls="{{$language->language_code}}" aria-selected="true">
                                        <span class="flag-icon  @if($language->language_code == 'en') flag-icon-us @else  flag-icon-{{ $language->language_code }} @endif"></span> {{ ucfirst($language->language_name) }}
                                    </a>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                        <div id="copyright-section">
                            @include('super-admin.footer-settings.copyright-input', $footerSettings)
                        </div>
                        <div class="form-group">
                          <h5 class="box-title  fs-6 fw-bold form-label mb-2">@lang('modules.footerSettings.socialLinks')</h5>
                            <span class="text-danger">@lang('modules.footerSettings.socialLinksNote')</span><br><br>
                            <div class="row">
                                @forelse($footerSettings->social_links as $link)
                                    <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <label for="{{ $link['name'] }}">
                                                @lang('modules.footerSettings.'.$link['name'])
                                            </label>
                                            <input class="form-control" id="{{ $link['name'] }}" name="social_links[{{ $link['name'] }}]" type="url" value="{{ $link['link'] }}" placeholder="@lang('modules.footerSettings.enter'.ucfirst($link['name']).'Link')">
                                        </div>
                                    </div>
                                @empty                                
                                @endforelse
                            </div>
                        </div>
                        <div class="text-center">                            
                            <button class="btn btn-sm btn-light-success" id="save-form" type="button">@lang('app.save')</button>
                        </div>
                    </form>
                    <hr>
                    <div id="footer-menu-setting">
                        <h4 class="box-title">@lang('modules.footerSettings.footerMenuSetting')</h4>
                        <button class="btn btn-sm btn-primary" onclick="javascript:createNewFooterMenuSetting();">@lang('app.createNew')</button>
                        <div class="table-responsive mt-3">
                            <table id="myTable" class="table table-bordered table-striped ">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('app.name')</th>
                                        <th>@lang('app.description')</th>
                                        <th>@lang('app.language')</th>
                                        <th>@lang('app.action')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--end::previous code-->

@endsection

@push('footer-script')
    <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script>
        function createNewFooterMenuSetting() {
            let url = "{{ route('superadmin.footer-settings.create') }}"

            $.ajaxModal('#application-lg-modal', url);
        }

        function updateNewFooterMenuSetting(id) {
            let url = "{{ route('superadmin.footer-settings.edit', ':id') }}";
            url = url.replace(':id', id);

            $.ajaxModal('#application-lg-modal', url);
        }

        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            changeForm(e.target);
        })

        function changeForm(target) {
            $.ajax({
                url: "{{ route('superadmin.footer-settings.index') }}",
                container: '#copyright-section',
                data: {
                    language_settings_id: $(target).data('language-id')
                },
                type: 'GET',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#copyright-section').html(response.view);
                    }
                }
            })
        }

        var table = $('#myTable').dataTable({
            responsive: true,
            // processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.footer-settings.data') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            columns: [
                { data: 'DT_Row_Index'},
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'language', name: 'language' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });

        new $.fn.dataTable.FixedHeader( table );

        $('#save-form').click(function () {
            $.ajax({
                url: '{{route('superadmin.footer-settings.store')}}',
                type: "POST",
                container: '#createForm',
                data: $('#createForm').serialize()+'&language='+$('#myTab a.active').data('language-id'),
            })
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

                    var url = "{{ route('superadmin.footer-settings.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                // swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush