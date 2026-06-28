@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
<style>
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
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Edit Linkedin Settings
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
                    <div class="card-body">                        
                        @if($httpsContains)
                            <form id="editSettings" class="ajax-form">
                                @csrf @method('PUT')
                                    <div class="form-group">
                                        <!--begin::linkedin-credentials-->
                                        <div class="d-flex flex-stack pt-5">
                                            <div class="me-5 fw-bold">
                                                <label class="fs-3 text-primary">Linkedin</label>
                                                <div class="fs-7 text-muted">Linkedin Status</div>
                                            </div>
                                            <div class="">
                                                <input id="linkedin" name="linkedin" type="checkbox" class="js-switch change-language-setting" data-color="#99d683" data-size="small" data-setting-id="1" onchange="toggle('#paypal-credentials')" style="display: none;" data-switchery="true"></span>
                                                <span class="form-check-label fw-bold text-muted">Allowed</span>
                                            </div>
                                        </div>
                                        <div class="linkedInSettings @if($linkedInSetting->status == 'disable') hide @endif" class="py-5">
                                            <div class="row pt-5">
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="fs-6 fw-bold form-label mb-2" for="client ID">client_id</label>
                                                        <input type="text" class="form-control form-control-solid" id="client_id" name="client_id" value="{{ $linkedInSetting->client_id }}" placeholder="Enter Client ID">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="fs-6 fw-bold form-label mb-2" for="secret">Secret</label>
                                                        <input type="email" class="form-control form-control-solid" id="secret" name="secret" value="{{ $linkedInSetting->client_secret }}" placeholder="Enter Secret">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <div class="form-group">
                                                        <label class="fs-6 fw-bold form-label mb-2" for="callback_url">Callback URL</label>
                                                        <input type="tel" class="form-control form-control-solid" disabled="disabled" value="{{ $linkedInSetting->callback_url }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center py-10 mt-3">
                                                <button class="btn btn-sm btn-light-primary" id="save-form" type="button">Save &amp; Update</button>
                                                <button class="btn btn-sm btn-light-warning" type="reset">Reset</button>
                                            </div>
                                        </div>
                                        <!--end::linkedin-credentials-->
                                    </div>
                            </form>
                            @else
                            <label class="control-label color--error col-sm-8">@lang('modules.linkedInSettings.sslError')</label>
                        @endif
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
                <h4 class="card-title">@lang('app.edit') @lang('menu.linkedInSettings')</h4>
                @if($httpsContains)
                    <form id="editSettings1" class="ajax-form">
                        @csrf @method('PUT')
                        <div class="form-group">
                            <label class="control-label col-sm-8">@lang('modules.linkedInSettings.status')</label>
                            <div class="col-sm-4">
                                <div class="switchery-demo">
                                    <input type="checkbox" name="status" @if($linkedInSetting->status == 'enable') checked @endif class="js-switch change-language-setting">
                                </div>
                            </div>
                        </div>
                        <div class="linkedInSettings @if($linkedInSetting->status == 'disable') hide @endif">
                            <div class="form-group">
                                <label for="company_name">@lang('modules.linkedInSettings.client_id')</label>
                                <input type="text" class="form-control" id="client_id1" name="client_id" value="{{ $linkedInSetting->client_id }}">
                            </div>
                            <div class="form-group">
                                <label for="company_email">@lang('modules.linkedInSettings.client_secret')</label>
                                <input type="email" class="form-control" id="client_secret1" name="client_secret" value="{{ $linkedInSetting->client_secret }}">
                            </div>
                            <div class="form-group">
                                <label for="company_phone">@lang('modules.linkedInSettings.callback_url')</label>
                                <input type="tel" class="form-control" readonly id="callback_url1" name="callback_url" value="{{ $linkedInSetting->callback_url }}">
                            </div>
                            <button type="button" id="save-form1" class="btn btn-success waves-effect waves-light m-r-10">
                                @lang('app.save')
                            </button>
                            <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                        </div>
                    </form>
                    @else
                    <label class="control-label color--error col-sm-8">@lang('modules.linkedInSettings.sslError')</label>
                @endif
            </div>
        </div>
    </div>
</div>
<!--end::previous code-->

@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

    <script>

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());

        });

        $('.change-language-setting').change(function () {
                $('.linkedInSettings').toggleClass('hide');
            if (!$(this).is(':checked'))
            {
                $.ajax({
                    url: '{{route("superadmin.updateStatus", $linkedInSetting->id)}}',
                    container: '#editSettings',
                    type: "GET",
                    redirect: true,
                    file: true
                })
            }
        });

    </script>
    <script>
        $('#save-form').click(function () {
            $.ajax({
                url: '{{route("superadmin.linkedin-settings.update", $linkedInSetting->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>
@endpush