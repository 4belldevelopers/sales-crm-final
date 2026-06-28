@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/clockpicker/dist/jquery-clockpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">

<style>
.content-header
{
    display: none;
}
.ace-twilight .ace_gutter
{
    background: #cacaca;
    color: #636363;    
    font-size: 15px;
    font-family: system-ui;
}
.ace_scroller
{
    background-color: #e6e1e1;    
    font-size: 15px;
    font-family: system-ui;
}
.asColorPicker-wrap:hover .asColorPicker-clear 
{
    line-height: 3;
}
.asColorPicker-wrap
{
    display: flex!important;
    justify-content: space-between!important;
    align-items: center!important;
}
.asColorPicker-trigger
{
    height: 6.4vh;
    width: 12%;
    border: none;
}
.form-control
{
    border-radius: 0px;
}
.ace-twilight .ace_print-margin
{
    width: 1px;
    background: #cacaca;
}
.ace_text-layer
{
    color: #cf6a4c;
}
.ace-twilight .ace_support
{
    color: #525050;
}
</style>

@endpush

@section('content')

    <div class="row">
        <div class="col-12">
            <!-- Card -->
            <form action="" class="ajax-form" id="createForm" method="POST">
                @csrf
                <div class="card mb-0">
                    <div class="card-body">
                        <h4 class="card-title pt-2 pb-2"><i class="fa fa-cube"></i>&nbsp;
                            @lang('modules.themeSettings.themePrimaryColor')
                        </h4>
                        <div class="row">
                            <div class="col-md-4 pt-2">
                                <div class="example">
                                    <input type="text" name="primary_color" class="gradient-colorpicker form-control" autocomplete="off" value="{{ $adminTheme->primary_color }}" />
                                </div>
                            </div>

                            <div class="col-md-12 pt-4 mb-4">
                                <h5 class="box-title">@lang('modules.themeSettings.adminPanelCustomCss')</h5>
                                <textarea name="admin_custom_css" class="my-code-area" rows="20" style="width: 100%">@if(is_null($adminTheme->admin_custom_css))/*Enter your custom css after this line*/@else {!! $adminTheme->admin_custom_css !!} @endif</textarea>
                            </div>

                            <div class="col-md-12 pt-3 mb-4">
                                <h5 class="box-title">@lang('modules.themeSettings.frontSiteCustomCss')</h5>
                                <textarea name="front_custom_css" class="my-code-area" rows="20" style="width: 100%">@if(is_null($adminTheme->front_custom_css))/*Enter your custom css after this line*/@else {!! $adminTheme->front_custom_css !!} @endif</textarea>
                            </div>

                            <div class="col-md-4 offset-md-4">
                                <div class="form-group">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify"
                                                   data-default-file="{{ $adminTheme->background_image_url }}"
                                            />
                                            <div class="col-12 text-center pt-2">
                                                <label for="exampleInputPassword1">
                                                    Upload or Change&nbsp;@lang('app.background') @lang('app.image')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-center py-4">
                                <button class="btn btn-light-success btn-sm" id="save-form" type="button">
                                    <i class="fa fa-save"></i>&nbsp;@lang('app.save')
                                </button>
                            </div>   
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
    <script src="{{ asset('assets/node_modules/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
    <script src="{{ asset('assets/node_modules/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>

    <script src="{{ asset('assets/ace/ace.js') }}"></script>
    <script src="{{ asset('assets/ace/theme-twilight.js') }}"></script>
    <script src="{{ asset('assets/ace/mode-css.js') }}"></script>
    <script src="{{ asset('assets/ace/jquery-ace.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>


    <script>

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

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

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.theme-settings.store')}}',
                container: '#createForm',
                type: "POST",
                file: true,
                redirect: true
            })
        });

    </script>
@endpush