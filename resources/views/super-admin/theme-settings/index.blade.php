@extends('layouts.superadmin_layout')

<style>
    body
    {
        overflow-x: hidden;
    }
    .asColorPicker-wrap
    {
        position: relative;
        display: flex!important;
        align-items: center!important;
    }
    .form-control
    {
        display: block;
        width: fit-content!important;
        border-radius: 0.475rem 0px 0px 0.475rem!important;
    }
    .asColorPicker-clear
    {
        display: none;
        position: absolute;
        top: 10!important;
        left: 180px;
        color: #777;
        text-decoration: none;
    }
    .asColorPicker-trigger
    {
        cursor: pointer;
        width: 25px;
        height: 42px!important;
        border-radius: 0px 5px 5px 0px;
    }
</style>

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/clockpicker/dist/jquery-clockpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/jquery-asColorPicker-master/css/asColorPicker.css') }}">
@endpush

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Theme settings</h1>
        </div>
        <div class="d-flex align-items-center py-1"></div>
	</div>
</div>
<!--end::toolbar-->


<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-10" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8 mx-0">
            <div class="col-xl-12 mt-4 px-0">
                <form action="" class="ajax-form" id="createForm" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="example">
                                        <h5 class="box-title fw-normal">@lang('modules.themeSettings.themePrimaryColor')</h5>
                                        <input type="text" name="primary_color" class="gradient-colorpicker form-control" autocomplete="off" value="{{ $adminTheme->primary_color }}" />
                                    </div>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <h5 class="box-title fw-normal">@lang('modules.themeSettings.adminPanelCustomCss')</h5>
                                    <textarea name="admin_custom_css" class="my-code-area" rows="20" style="width: 100%">
                                        @if(is_null($adminTheme->admin_custom_css))/*Enter your custom css after this line*/
                                            @else {!! $adminTheme->admin_custom_css !!}
                                        @endif
                                    </textarea>
                                </div>
                                <div class="col-md-12">
                                    <h5 class="box-title fw-normal">@lang('modules.themeSettings.frontSiteCustomCss')</h5>
                                    <textarea name="front_custom_css" class="my-code-area" rows="20" style="width: 100%">
                                        @if(is_null($adminTheme->front_custom_css))/*Enter your custom css after this line*/
                                            @else {!! $adminTheme->front_custom_css !!}
                                        @endif
                                    </textarea>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-sm btn-light-primary" id="save-form" type="button">@lang('app.save')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<!--end::content-->

@push('footer-script')
    <script src="{{ asset('assets/node_modules/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
    <script src="{{ asset('assets/node_modules/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
    <script src="{{ asset('assets/node_modules/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

    <script src="{{ asset('assets/ace/ace.js') }}"></script>
    <script src="{{ asset('assets/ace/theme-twilight.js') }}"></script>
    <script src="{{ asset('assets/ace/mode-css.js') }}"></script>
    <script src="{{ asset('assets/ace/jquery-ace.min.js') }}"></script>


    <script>
        $('.my-code-area').ace({ theme: 'twilight', lang: 'css' })

        // Colorpicker
        $(".colorpicker").asColorPicker();
        $(".complex-colorpicker").asColorPicker({
            mode: 'complex'
        });
        $(".gradient-colorpicker").asColorPicker(
            // {
            //     mode: 'gradient'
            // }
        );

// createForm
 $(document).on('click', '#save-form', function (e) {
            e.preventDefault();
            var url = "{{route('superadmin.theme-settings.store')}}";
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

            var form = $('#createForm')[0];
            var formData = new FormData(form);

            // Laravel requires these
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            // formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: "POST", 
                data: formData,
       			processData			: false,
       			contentType			: false,
                beforeSend: function() {
                    $('#kt_activitiesDefaultPackage_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                     Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    });
                }
            });
        });

        // $('#save-form').click(function () {
        //     $.easyAjax({
        //         url: '',
        //         container: '#createForm',
        //         data: $('#createForm').serialize(),
        //         type: "POST",
        //         redirect: true
        //     })
        // });

    </script>
@endpush