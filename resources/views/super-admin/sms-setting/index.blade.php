@extends('layouts.superadmin_layout')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
<style>
body
{
    overflow-x: hidden;
}
</style>
@endpush
@section('content')
{{-- @dd("hi"); --}}
<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">@lang('app.sms.nexmoCredential')
            </h1>
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
                    <div class="card-body py-0">
                        <form id="editSmsSettings" class="ajax-form">
                            <div class="alert"></div>
                            @csrf
                            @method('PUT')
                            <div class="d-flex flex-stack">
                                <div class="me-5 fw-bold">
                                    <label class="fs-3 text-primary">Nexmo</label>
                                    <div class="fs-7 text-muted" for="nexmo_status">Nexmo Status</div>
                                </div>
                                <div class="">
                                    <input id="nexmo_status" name="nexmo_status" type="checkbox" @if($credentials->nexmo_status == 'active') checked @endif value="active" class="js-switch change-language-setting" data-color="#99d683" data-size="small" data-setting-id="{{ $credentials->id }}" onchange="toggle('#nexmo-credentials');">
                                    <span class="form-check-label fw-bold text-muted">Allowed</span>
                                </div>
                            </div>
                            <div id="nexmo-credentials" class="py-5">
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label class="fs-6 fw-bold form-label mb-2" for="nexmo_key">Nexmo Key</label>
                                            <input type="text" class="form-control form-control-solid" id="nexmo_key" name="nexmo_key" placeholder="Enter Nexmo Key" value="{{ $credentials->nexmo_key }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label class="fs-6 fw-bold form-label mb-2" for="nexmo_secret">Nexmo Secret</label>
                                            <input type="password" class="form-control form-control-solid" id="nexmo_secret" name="nexmo_secret" placeholder="Enter Naxmo Secret" value="{{ $credentials->nexmo_secret }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label class="fs-6 fw-bold form-label mb-2" for="nexmo_from">Nexmo From</label>
                                            <input type="text" class="form-control form-control-solid" id="nexmo_from" name="nexmo_from" placeholder="Enter Nexmo From" value="{{ $credentials->nexmo_from }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center py-10 mt-3">
                                <button class="btn btn-sm btn-light-primary" id="save-form" type="button">Save &amp; Update</button>
                                <button class="btn btn-sm btn-light-warning" type="reset">Reset</button>
                            </div>
                        </form>    
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
                <h4 class="card-title mb-4 text-primary">@lang('app.sms.nexmoCredential')</h4>
                <form id="editSmsSettings" class="ajax-form">
                    <div id="alert"></div>
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nexmo_status">@lang('app.sms.nexmoStatus')</label>

                        <div class="col-sm-4">
                            <div class="switchery-demo">
                                <input id="nexmo_status" name="nexmo_status" type="checkbox" @if($credentials->nexmo_status == 'active') checked @endif value="active" class="js-switch change-language-setting" data-color="#99d683" data-size="small" data-setting-id="{{ $credentials->id }}" onchange="toggle('#nexmo-credentials');">
                            </div>
                        </div>
                    </div>
                    <div id="nexmo-credentials">
                        <div class="form-group">
                            <label for="nexmo_key">@lang("app.sms.nexmoKey")</label>
                            <input type="text" name="nexmo_key" id="nexmo_key" class="form-control" value="{{ $credentials->nexmo_key }}">
                        </div>

                        <div class="form-group">
                            <label for="nexmo_secret">@lang("app.sms.nexmoSecret")</label>
                            <input type="password" name="nexmo_secret" id="nexmo_secret" class="form-control" value="{{ $credentials->nexmo_secret }}">
                        </div>

                        <div class="form-group">
                            <label for="nexmo_from">@lang("app.sms.nexmoFrom")</label>
                            <input type="text" name="nexmo_from" id="nexmo_from" class="form-control" value="{{ $credentials->nexmo_from }}">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">@lang('app.save')</button>
                        <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                    </div>    
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::previous code-->
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

    <script>
        function toggle(elementBox) {
            var elBox = $(elementBox);
            elBox.slideToggle();
        }

        $('#nexmo_status').is(':checked') ? $('#nexmo-credentials').show() : $('#nexmo-credentials').hide();

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());

        });

        // Update Mail Setting
        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('superadmin.sms-settings.update', $credentials->id)}}',
                container: '#editSmsSettings',
                type: "POST",
                redirect: true,
                messagePosition: "inline",
                data: $('#editSmsSettings').serialize(),
              })
        });

    </script>

@endpush