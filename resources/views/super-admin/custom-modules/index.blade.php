@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
<style>
body
{
    overflow-x: hidden;
}
/*begin::data table css*/
.list-group-item
{
    color: #181c32;
    background-color: #fff;
    border-bottom: 1px dashed #e4e6ef;
    border-top: none;
    border-right: none;
    border-left: none;
    font-size: 13px;
    font-weight: lighter;
    color: #a1a5b7;
}
/*end::data table css*/
</style>
@endpush 

@section('create-button')
<a href="{{ route('superadmin.custom-modules.create') }}" class="btn btn-dark btn-sm m-l-15">
    <i class="fa fa-plus-circle"></i> @lang('app.install') / @lang('app.update') @lang('app.module')
</a>
@endsection

@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
		<div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
			<h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Custom Modules
                    <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span>
                <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small>
            </h1>
        </div>
        <div class="d-flex align-items-center py-1">
		        <a href="{{ route('superadmin.custom-modules.create') }}" class="btn btn-sm btn-light btn-active-primary">
                <span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                    </svg>
                </span>    
                @lang('app.install') / @lang('app.update') @lang('app.module')</a>
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
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-group m-t-20" id="files-list">
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <span class="fw-bold">@lang('app.name')</span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <span class="fw-bold">Envato Purchase code</span>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <span class="fw-bold">@lang('app.currentVersion')</span>
                                            </div>
                                            {{-- <div class="col-md-2 text-right">--}}
                                            {{-- <strong>@lang('app.latestVersion')</strong>--}}
                                            {{-- </div>--}}
                                                    <div class="col-md-3 text-right">
                                                        <span class="fw-bold">@lang('app.status')</span>
                                                    </div>
                                            {{-- <div class="col-md-2 text-right">--}}
                                            {{-- <span class="fw-bold">@lang('app.action')</span>--}}
                                            {{-- </div>--}}
                                        </div>
                                    </li>
                                    @php
                                        $count = 0;
                                    @endphp
                                    @forelse ($allModules as $key=>$module)
                                        <li class="list-group-item" id="file-{{ $count++ }}">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    {{ $key }}
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    @if(in_array($module, $recruitPlugins))
                                                        @if (config(strtolower($module).'.setting'))
                                                            @php
                                                                $settingInstance = config(strtolower($module).'.setting');
                                                                $fetchSetting = $settingInstance::first();
                                                            @endphp
                                                            @if (config(strtolower($module).'.verification_required'))
                                                            {!! $fetchSetting->purchase_code ?? '<a href="javascript:;" class="btn btn-success btn-sm btn-outline verify-module" data-module="'. strtolower($module).'" >'.__('app.verifyEnvato').'</a>' !!}
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="col-md-3 text-right">
                                                    @if (config(strtolower($module).'.setting'))
                                                        <label class="badge badge-info">{{ File::get($module->getPath() . '/version.txt') }}</label>
                                                    @endif
                                                </div>
                                                {{-- <div class="col-md-2 text-right">--}}
                                                {{-- @if (config(strtolower($module).'.setting'))--}}
                                                {{-- <label class="label label-info">{{ File::get($module->getPath() . '/version.txt') }}</label>--}}
                                                {{-- @endif--}}
                                                {{-- </div>--}}
                                                <div class="col-md-3 text-right">
                                                    <div class="switchery-demo">
                                                        <input type="checkbox" @if(in_array($module, $recruitPlugins)) checked @endif class="js-switch change-module-setting" data-size="small" data-module-name="{{ $module }}" />
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-2 text-right">--}}
                                                    {{-- @if (config(strtolower($module).'.setting'))
                                                    <a href="" class="btn btn-success btn-sm btn-outline" data-file-no="{{ $module }}" >@lang('app.download') @lang('app.update') <i class="fa fa-download"></i></a>
                                                    @endif --}}
                                                {{-- </div>--}}
                                            </div>
                                        </li>
                                    @empty
                                        <div class="text-center pt-10 pb-2 border-bottom">
                                            <div class="empty-space" style="height: 110px;">
                                                <div class="empty-space-inner">
                                                    <div class="icon" style="font-size:30px"><i class="icon-layers"></i>
                                                    </div>
                                                    <div class="title mb-3">@lang('messages.noModules')
                                                    </div>
                                                    <div class="subtitle mt-10">
                                                        <a href="{{ route('superadmin.custom-modules.create') }}" class="btn btn-light-success btn-sm"><i class="fa fa-refresh"></i> @lang('app.install') / @lang('app.update') @lang('app.module')</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </ul>
                            </div>
                            @include('vendor.froiden-envato.update.plugins')
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>
<!--end::content-->
<br>
<!--begin::previous code-->
<div class="row d-none">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('menu.customModules')</h4>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="list-group m-t-20" id="files-list">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>@lang('app.name')</strong>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <strong>Envato Purchase code</strong>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <strong>@lang('app.currentVersion')</strong>
                                    </div>
                                    {{-- <div class="col-md-2 text-right">--}}
                                    {{-- <strong>@lang('app.latestVersion')</strong>--}}
                                    {{-- </div>--}}
                                            <div class="col-md-3 text-right">
                                                <strong>@lang('app.status')</strong>
                                            </div>
                                    {{-- <div class="col-md-2 text-right">--}}
                                    {{-- <strong>@lang('app.action')</strong>--}}
                                    {{-- </div>--}}
                                </div>
                            </li>
                            @php
                                $count = 0;
                            @endphp
                            @forelse ($allModules as $key=>$module)
                                <li class="list-group-item" id="file-{{ $count++ }}">
                                    <div class="row">
                                        <div class="col-md-2">
                                            {{ $key }}
                                        </div>
                                        <div class="col-md-4 text-right">
                                            @if(in_array($module, $recruitPlugins))
                                                @if (config(strtolower($module).'.setting'))
                                                    @php
                                                        $settingInstance = config(strtolower($module).'.setting');
                                                        $fetchSetting = $settingInstance::first();
                                                    @endphp
                                                    @if (config(strtolower($module).'.verification_required'))
                                                    {!! $fetchSetting->purchase_code ?? '<a href="javascript:;" class="btn btn-success btn-sm btn-outline verify-module" data-module="'. strtolower($module).'" >'.__('app.verifyEnvato').'</a>' !!}
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-right">
                                            @if (config(strtolower($module).'.setting'))
                                                <label class="badge badge-info">{{ File::get($module->getPath() . '/version.txt') }}</label>
                                            @endif
                                        </div>
                                        {{-- <div class="col-md-2 text-right">--}}
                                        {{-- @if (config(strtolower($module).'.setting'))--}}
                                        {{-- <label class="label label-info">{{ File::get($module->getPath() . '/version.txt') }}</label>--}}
                                        {{-- @endif--}}
                                        {{-- </div>--}}
                                        <div class="col-md-3 text-right">
                                            <div class="switchery-demo">
                                                <input type="checkbox" @if(in_array($module, $recruitPlugins)) checked @endif class="js-switch change-module-setting" data-size="small" data-module-name="{{ $module }}" />
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-2 text-right">--}}
                                            {{-- @if (config(strtolower($module).'.setting'))
                                            <a href="" class="btn btn-success btn-sm btn-outline" data-file-no="{{ $module }}" >@lang('app.download') @lang('app.update') <i class="fa fa-download"></i></a>
                                            @endif --}}
                                        {{-- </div>--}}
                                    </div>
                                </li>
                            @empty
                                <div class="text-center">
                                    <div class="empty-space" style="height: 200px;">
                                        <div class="empty-space-inner">
                                            <div class="icon" style="font-size:30px"><i class="icon-layers"></i>
                                            </div>
                                            <div class="title mb-3">@lang('messages.noModules')
                                            </div>
                                            <div class="subtitle">
                                                <a href="{{ route('superadmin.custom-modules.create') }}" class="btn btn-success btn-sm btn-outline"><i class="fa fa-refresh"></i> @lang('app.install') / @lang('app.update') @lang('app.module')</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </ul>
                    </div>
                    @include('vendor.froiden-envato.update.plugins')
                </div>
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
    $('[data-toggle="tooltip"]').tooltip()
    // Switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function () {
        new Switchery($(this)[0], $(this).data());
    });
    $('.change-module-setting').change(function () {
        var module = $(this).data('module-name');
        if($(this).is(':checked'))
            var moduleStatus = 'active';
        else
            var moduleStatus = 'inactive';
        var url = '{{route('superadmin.custom-modules.update', ':module')}}';
        url = url.replace(':module', module);
        $.easyAjax({
            url: url,
            type: "POST",
            data: { 'id': module, 'status': moduleStatus, '_method': 'PUT', '_token': '{{ csrf_token() }}' }
        })
    });
    $('.verify-module').click(function () {
        var module = $(this).data('module');
        var url = '{{route('superadmin.custom-modules.show', ':module')}}';
        url = url.replace(':module', module);
        $('#modelHeading').html('Verify your purchase');
        $.ajaxModal('#application-lg-modal', url);
    })
</script>
<script>
    function validateCode() {
        $.easyAjax({
            type: 'POST',
            url: "{{ route('superadmin.custom-modules.verify-purchase') }}",
            data: $("#verify-form").serialize(),
            container: "#verify-form",
            messagePosition: 'inline'
        });
        return false;
    }
</script>
@endpush