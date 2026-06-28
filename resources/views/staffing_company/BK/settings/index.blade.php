@extends('layouts.demo_admin_app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<style>
.content-header{display: none;}
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title pt-2 pb-2">
                        <i class="fa fa-building"></i>&nbsp;Business Setting
                    </h4>
                    <form id="editSettings" class="ajax-form">
                        @csrf
                        @method('PUT')

                        <div class="row pt-2">
                            <div class="col-md-4 offset-md-4">
                                <div class="form-group">
                                     <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="logo" class="dropify"
                                                data-default-file="{{ $global->logo_url }}"
                                            />
                                            <div class="col-12 text-center">
                                                <label for="exampleInputPassword1">
                                                    Upload&nbsp;@lang('modules.accountSettings.companyLogo')
                                                </label>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $global->company_name }}" placeholder="Enter Company Name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company_name">@lang('modules.accountSettings.companyUrlLink')</label>
                                    <input type="text" class="form-control" id="company_slug" name="company_slug" value="{{ $global->career_page_link }}" placeholder="Enter Company URL">
                                    <input type="hidden" id="slug" value="{{ $global->career_page_link }}" name="slug" placeholder="Enter Company URL">
                                    <span class="text-success">@lang('modules.accountSettings.companyUrlLinkWillBe') </span> : <span class="font-weight-bold " style="border: none" id="slugValue">{{ $global->career_page_link }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">    
                                <div class="form-group">
                                    <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email" value="{{ $global->company_email }}" placeholder="Enter Company Email">
                                </div>
                            </div>    
                        </div>

                        <div class="row pt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                                    <input type="number" class="form-control" id="company_phone" name="company_phone" value="{{ $global->company_phone }}" placeholder="Enter Company Phone">
                                </div>
                            </div>    

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                                    <input type="text" class="form-control" id="website" name="website" value="{{ $global->website }}" placeholder="Enter Company Website">
                                </div>
                            </div>    

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('modules.accountSettings.jobOpeningHeading')</label>
                                    <input type="text" class="form-control" id="job_opening_title" name="job_opening_title" value="{{ $global->job_opening_title }}" placeholder="Enter Career Page Heading">
                                </div>
                            </div>    
                        </div>   

                        <div class="row pt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">@lang('modules.accountSettings.defaultTimezone')</label>
                                    <select name="timezone" id="timezone" class="form-control select2 custom-select">
                                        @foreach($timezones as $tz)
                                            <option @if($global->timezone == $tz) selected @endif>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address" class="mb-1">@lang('modules.accountSettings.changeLanguage')</label>

                                    <select class="form-control" name="locale">
                                        @foreach($languageSettings as $language)
                                            <option value="{{ $language->language_code }}" @if($global->locale == $language->language_code) selected @endif  data-content='<span class="flag-icon flag-icon-{{ $language->language_code }}"></span> {{ $language->language_name }}'>{{ $language->language_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>    
                        </div>    

                        <hr>

                        <div class="row">
                            @if(module_enabled('Subdomain'))
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('modules.frontCms.loginBackroundImage'); ?></label>
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="file" id="input-file-now" name="login_background" class="dropify" data-default-file="{{ $global->login_background_image_url }}"
                                            />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                                    <textarea class="form-control" id="address" rows="2" name="address">{{ $global->address }}</textarea>
                                </div>
                            </div>    
                        </div>    

                        <div class="row pt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="address">@lang('modules.accountSettings.jobOpeningText')</label>
                                    <textarea class="form-control" id="job_opening_text" rows="3" name="job_opening_text">{{ $global->job_opening_text }}</textarea>
                                </div>
                            </div>    
                        </div>    

                        <div class="row py-4">
                            <div class="col-12 text-center">
                                <button type="button" id="save-form" class="btn btn-light-success btn-sm">
                                    @lang('app.save')
                                </button>
                                <button type="reset" class="btn btn-light-danger btn-sm">
                                    @lang('app.reset')
                                </button>
                            </div>    
                        </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="//cdn.jsdelivr.net/npm/speakingurl@14.0.1/speakingurl.min.js" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/slugify/src/slugify.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>

    <script>
//        jQuery(function($) {
            $.slugify("Ätschi Bätschi"); // "aetschi-baetschi"
            $('#slug').slugify('#company_slug'); // Type as you slug

//            $("#slug-target").slugify("#slug-source", {
//                separator: '_' // If you want to change separator from hyphen (-) to underscore (_).
//            });
//        });

        $('#company_slug').on('keyup change', function() {
            if ($('#slug').val() !== '' && $('#slug').val() !== undefined) {
                $('#slugValue').html($('#slug').val());
            } else {

            }
        });
        // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });



        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.settings.update', $global->id)}}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>


@endpush
