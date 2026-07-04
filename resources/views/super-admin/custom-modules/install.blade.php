@extends('layouts.superadmin_layout')

<style>
.list-group-item
{
    border-top: none!important;
    border-left: none!important;
    border-right: none!important;
    border-bottom: 1px dashed rgba(0, 0, 0, 0.125)!important;
    padding-top: 1em!important;
    padding-bottom: 1em!important;
}
</style>

@section('page-title')
    <div class="row bg-title">
        <!-- page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- page title -->
        <!-- breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('superadmin.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!--breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropzone-master/dist/dropzone.css') }}">
<style>
.file-bg
{
    height: 150px;
    overflow: hidden;
    position: relative;
}
.file-bg .overlay-file-box
{
    opacity: .9;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    text-align: center;
}
body
{
    overflow-x: hidden;
}
</style>
@endpush

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">        
        <div class="d-flex align-items-center py-1">
		        <a href="{{ route('superadmin.custom-modules.index') }}" class="btn btn-sm btn-light-dark p-1">
                <span class="svg-icon svg-icon-primary svg-icon-1x me-0">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <path d="M11.0879549,18.2771971 L17.8286578,12.3976203 C18.0367595,12.2161036 18.0583109,11.9002555 17.8767943,11.6921539 C17.8622027,11.6754252 17.8465132,11.6596867 17.8298301,11.6450431 L11.0891271,5.72838979 C10.8815919,5.54622572 10.5656782,5.56679309 10.3835141,5.7743283 C10.3034433,5.86555116 10.2592899,5.98278612 10.2592899,6.10416552 L10.2592899,17.9003957 C10.2592899,18.1765381 10.4831475,18.4003957 10.7592899,18.4003957 C10.8801329,18.4003957 10.9968872,18.3566309 11.0879549,18.2771971 Z" fill="#000000" transform="translate(14.129645, 12.002277) scale(-1, 1) translate(-14.129645, -12.002277) "/>
                            <rect fill="#000000" opacity="0.3" x="6" y="6" width="3" height="12" rx="1"/>
                        </g>
                    </svg>
                </span>   
                Back</a>
		</div>
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Custom Modules Listing
                    <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
                <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
            </h1>
        </div>
	</div>
</div>
<!--end::toolbar-->

<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-10" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8 mx-4">
            <div class="col-xl-12 mt-4 px-0">
                <div class="card">
                    <div class="card-body">
                        <!--begin::Form-->
                        <form>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3 col-sm-12 text-lg-right bg-light-danger text-danger w-100 mb-10">You will have to login again to see the changes.</label>
                                    <h3 class="card-title align-items-start flex-column px-0">
                                        <span class="card-label fw-bolder fs-3 mb-1 d-block">Step-1</span>
                                        <span class="text-muted mt-1 fw-bold fs-7">Upload files</span>
                                    </h3>
                                    <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                                        <div class="dropzone dropzone-default dropzone-primary fallback" id="file-upload-dropzone">
                                            <div class="dropzone-msg needsclick">
                                                <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3><br>
                                                <span class="dropzone-msg-desc">Upload up to 10 files</span>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12 m-t-20" id="install-process"></div>
                                    </div>

                                    <!--begin::table-->
                                    <div class="card card-xl-stretch mb-5 mb-xl-8 px-0 shadow-none">
                                        <div class="card-header border-0 px-0">
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bolder fs-3 mb-1">Step-2</span>
                                                <span class="text-muted mt-1 fw-bold fs-7">Module zip file</span>
                                            </h3>
                                        </div>
                                        <div class="card-body p-0">                                                                                          
                                            <div class="col-md-12">
                                                <ul class="list-group" id="">
                                                    <li class="list-group-item">
                                                        <div class="row d-flex align-items-center">
                                                            <div class="col-md-4">
                                                                <span class="text-muted fw-bold fs-6">Name</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <span class="text-muted fw-bold fs-6">Description</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <span class="text-muted fw-bold fs-6">Action</span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <ul class="list-group" id="files-list">
                                                    @foreach (\Illuminate\Support\Facades\File::files($updateFilePath) as $key=>$filename)
                                                        @if (\Illuminate\Support\Facades\File::basename($filename) != "modules_statuses.json" &&
                                                        strpos(\Illuminate\Support\Facades\File::basename($filename) , 'auto') ===
                                                        false)
                                                            <li class="list-group-item" id="file-{{ $key+1 }}">
                                                                <div class="row d-flex align-items-center">
                                                                    <div class="col-md-4">
                                                                        <span class="text-dark-800 fw-bold fs-6">
                                                                            {{ \Illuminate\Support\Facades\File::basename($filename) }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <span class="text-dark-800 fw-bold fs-6">
                                                                            Uploaded On: {{ \Carbon\Carbon::parse(\Illuminate\Support\Facades\File::lastModified($filename))->format('jS F, Y g:i:s a')}}
                                                                        </span>
                                                                    </div>
                                                                    <div class="col-md-4 text-right">
                                                                        <button type="button" class="btn btn-light-success btn-sm install-files" data-file-no="{{ $key+1 }}" data-file-path="{{ $filename }}">@lang('modules.update.install')</button>
                                                                        <button type="button" class="btn btn-light-danger btn-sm delete-files" data-file-no="{{ $key+1 }}" data-file-path="{{ $filename }}">@lang('app.delete')</button>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::table-->
                                </div>
                            </div>
                        </form>
                        <!--end::Form-->                        
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>
<!--end::content-->
<br>
<!--begin::previous code-->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('menu.customModules')</h4>
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ route('superadmin.custom-modules.index') }}" class="btn btn-primary btn-sm btn-outline"><i class="fa fa-arrow-left"></i>@lang('app.back')</a>
                        <div class="white-box">
                            <p class="text-danger m-b-10 font-13">
                                @lang("messages.loginAgain")
                            </p>
                            <div class="row">
                                <div class="col-md-12 m-t-20">
                                    <h4 class="box-title text-info">Step 1</h4>
                                    <form action="{{ route('superadmin.update-application.store') }}" class="dropzone" id="file-upload-dropzone1">
                                        {{ csrf_field() }}
                                        <div class="fallback">
                                            <input name="file" type="file" multiple/>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12 m-t-20" id="install-process"></div>
                                <div class="col-md-12 m-t-20">
                                    <h4 class="box-title text-info">Step 2</h4>
                                    <h4 class="box-title">@lang('modules.update.moduleFile')</h4>
                                </div>
                                <div class="col-md-12">
                                    <ul class="list-group" id="files-list">
                                        @foreach (\Illuminate\Support\Facades\File::files($updateFilePath) as $key=>$filename)
                                            @if (\Illuminate\Support\Facades\File::basename($filename) != "modules_statuses.json" &&
                                            strpos(\Illuminate\Support\Facades\File::basename($filename) , 'auto') ===
                                            false)
                                                <li class="list-group-item" id="file-{{ $key+1 }}">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            {{ \Illuminate\Support\Facades\File::basename($filename) }}
                                                        </div>
                                                        <div class="col-md-4">
                                                            Uploaded On:
                                                            {{ \Carbon\Carbon::parse(\Illuminate\Support\Facades\File::lastModified($filename))->format('jS F, Y g:i:s a')}}
                                                        </div>
                                                        <div class="col-md-4 text-right">
                                                            <button type="button" class="btn btn-success btn-sm install-files text-white" data-file-no="{{ $key+1 }}" data-file-path="{{ $filename }}">@lang('modules.update.install') <i class="fa fa-refresh"></i></button>
                                                            <button type="button" class="btn btn-danger btn-sm delete-files text-white" data-file-no="{{ $key+1 }}" data-file-path="{{ $filename }}">@lang('app.delete')<i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::previous code-->
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/dropzone-master/dist/dropzone.js') }}"></script>

    <!--begin::previous design scripts-->    
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/popper/popper.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/fastclick/fastclick.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/js/bootstrap-select.min.js'></script>
    <script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}<?php echo TM_VERSION; ?>"></script>
    {{--sticky note script--}}
    <script src="{{ asset('js/cbpFWTabs.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/icheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/icheck/icheck.init.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}<?php echo TM_VERSION; ?>"></script>
    <!--end::previous design scripts-->


    <script type="text/javascript">
        Dropzone.options.fileUploadDropzone = {
            paramName: "file",
            dictDefaultMessage: "Upload or drop the downloaded file here",
            accept: function (file, done)
            {
                done();
            },
            init: function () {
                this.on("success", function (file, response)
                {
                    var viewName = $('#view').val();
                    if (viewName == 'list') {
                        $('#files-list-panel ul.list-group').html(response.html);
                    } else {
                        $('#thumbnail').empty();
                        $(response.html).hide().appendTo("#thumbnail").fadeIn(500);
                    }
                    window.location.reload();
                })
            }
        };
        var updateAreaDiv = $('#update-area');
        var refreshPercent = 0;
        var checkInstall = true;
        function checkIfFileExtracted() {
            $.ajax({
                type: 'GET',
                url: '{!! route("admin.updateVersion.checkIfFileExtracted") !!}',
                success: function (response) {
                    checkInstall = false;
                    $('#download-progress').append("<br><i><span class='text-success'>Installed successfully. Reload page to see the changes.</span>.</i>");
                    window.location.reload();
                }
            });
        }
        $('.install-files').click(function ()
        {
            $('#install-process').html('<div class="alert alert-success fs-6">Installing...Please wait (This may take few minutes.)</div>');
            let filePath = $(this).data('file-path');
            $.ajax({
                type: 'POST',
                url: '{!! route("superadmin.custom-modules.store") !!}',
                data: {"_token": "{{ csrf_token() }}", filePath: filePath},
                success: function (response) {
                    if (response.status === 'success') {
                        $('#download-progress').append("<br><i><span class='text-success'>Installed successfully. Reload page to see the changes.</span>.</i>");
                        window.location.reload();
                    }
                    $('#install-process').html('');
                }
            });
        });

        $('.delete-files').click(function ()
        {
            let filePath = $(this).data('file-path');
            let fileNumber = $(this).data('file-no');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm)
            {
                if (isConfirm)
                {
                    $.ajax({
                        type: 'POST',
                        url: '{!! route("superadmin.update-application.deleteFile") !!}',
                        data: {"_token": "{{ csrf_token() }}", filePath: filePath},
                        success: function (response) {
                            $('#file-' + fileNumber).remove();
                        }
                    });
                }
            });
        });
    </script>
@endpush
