@extends('layouts.superadmin_layout')
@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endpush
@section('content')

    <!--begin::toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">@lang('app.edit') @lang('menu.settings')</h1>
            </div>
        </div>
    </div>
    <!--end::toolbar-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="editSettings" class="ajax-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                        value="{{ $global->company_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email"
                                        value="{{ $global->company_email }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                                    <input type="tel" class="form-control" id="company_phone" name="company_phone"
                                        value="{{ $global->company_phone }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                                    <input type="text" class="form-control" id="website" name="website"
                                        value="{{ $global->website }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">@lang('modules.accountSettings.companyLogo')</label>
                            <div class="card">
                                <div class="card-body">
                                    <input type="file" id="input-file-now" name="logo" class="dropify"
                                        data-default-file="{{ str_replace('index.php', '', $global->logo_url) }}" />
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <label for="exampleInputPassword1" style="width: 100%">@lang('modules.accountSettings.numberDeleteAccountAutomatically')</label>
                            <div class="input-group-prepend">
                                <select name="hoursDays" class="form-control">
                                    <option @if ($global->delete_account_hour_day == 'hour') selected @endif value="hour">
                                        @lang('app.hours')</option>
                                    <option @if ($global->delete_account_hour_day == 'day') selected @endif value="day">
                                        @lang('app.days')</option>
                                </select>
                            </div>
                            <input type="number" min="0" class="form-control"
                                value="{{ $global->delete_account_in }}" name="delete_account_in">
                        </div>
                        <div class="form-group">
                            <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                            <textarea class="form-control" id="address" rows="5" name="address">{{ $global->address }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="address">@lang('app.currency')</label>
                                    <select name="currency_id" id="currency_id" class="form-control">
                                        @foreach ($currencies as $item)
                                            <option @if ($item->id == $global->currency_id) selected @endif
                                                value="{{ $item->id }}">
                                                {{ $item->currency_code . ' (' . $item->currency_symbol . ')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="address">@lang('modules.accountSettings.changeLanguage')</label>
                                    <select class="form-control" name="locale">
                                        @foreach ($languageSettings as $language)
                                            <option value="{{ $language->language_code }}"
                                                @if ($global->locale == $language->language_code) selected @endif
                                                data-content='<span class="flag-icon flag-icon-{{ $language->language_code }}"></span> {{ $language->language_name }}'>
                                                {{ $language->language_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="google_recaptcha_key">Google Recaptcha Key</label>
                                    <input type="text" class="form-control" id="google_recaptcha_key"
                                        name="google_recaptcha_key" value="{{ $global->google_recaptcha_key }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">@lang('modules.accountSettings.updateEnableDisable')
                                <a href="javascript:void(0)"><i class="fa fa-info-circle" data-toggle="tooltip"
                                        data-placement="right" data-original-title="@lang('modules.accountSettings.updateEnableDisableTest')"></i></a>
                            </label>
                            <div class="col-sm-4">
                                <div class="switchery-demo">
                                    <input id="nexmo_status" name="system_update" type="checkbox"
                                        @if ($global->system_update == 1) checked @endif value="on"
                                        class="js-switch change-language-setting" data-color="#99d683"
                                        data-size="small" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="country">Country / Region</label>
                                    <select class="form-control" id="country" name="country">

                                        @if ($countries)
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input class="form-control" value="{{ $sold_by_address->first_name }}"
                                        type="text" id="first_name" name="first_name" placeholder="First Name">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input class="form-control" value="{{ $sold_by_address->last_name }}" type="text"
                                        id="last_name" name="last_name" placeholder="Last Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="building">Building / Society</label>
                                    <input class="form-control" value="{{ $sold_by_address->building }}" type="text"
                                        id="building" name="building" placeholder="Building / Society">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <select class="form-control" id="state" name="state_id">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <select class="form-control" id="city" name="city">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input class="form-control" value="{{ $sold_by_address->postal_code }}"
                                        type="text" id="postal_code" name="postal_code" placeholder="Postal Code">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="gstin" id="gstin_text">GSTIN</label>
                                    <input class="form-control" value="{{ $sold_by_address->gstin }}" type="text"
                                        id="gstin" name="gstin" placeholder="">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="pan_no" id="pan_no_text">PAN No.</label>
                                    <input class="form-control" value="{{ $sold_by_address->pan_no }}" type="text"
                                        id="pan_no" name="pan_no" placeholder="">
                                </div>
                            </div>
                        </div>


                        <div class="row justify-content-center">
                            <div class="col-12 form-group d-flex justify-content-center">
                                <p>&nbsp;</p>
                            </div>
                            <div class="col-4 form-group d-flex justify-content-center">
                              
                                <button type="button" id="save-form"
                                    class="btn btn-success waves-effect waves-light m-r-10">@lang('app.save')</button>
                                {{-- <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button> --}}
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
    <script>
        $('[data-toggle="tooltip"]').tooltip()
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());

        });
        // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang('app.dragDrop')',
                replace: '@lang('app.dragDropReplace')',
                remove: '@lang('app.remove')',
                error: '@lang('app.largeFile')'
            }
        });

        $('#save-form').click(function() {
            var formData = new FormData($('#editSettings')[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.ajax({
                url: '{{ route('superadmin.global-settings.updatePr', $global->id) }}',
                type: "POST",
                container: '#editSettings',
                messagePosition: "inline",
                data: formData, // Use FormData to include file inputs
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Ensure the request is sent as multipart/form-data
                success: function(response) {
                    if (response.status == 'error') {
                        $('#alert').prepend(
                            '<div class="alert alert-danger">{{ __('messages.smtpError') }}</div>')
                    } else {
                        $('#alert').show();
                        alert('Updated Successfully');
                    }
                }
            })

            // alert(ddd);
            // $.easyAjax({
            //     url: '{{ route('superadmin.global-settings.update', $global->id) }}',
            //     container: '#editSettings',
            //     type: "POST",
            //     redirect: true,
            //     file: true
            // })
        });


        var country_id = "{{ @$countrie_id }}";
        var state_id = "{{ @$state_id }}";
        var city_id = "{{ @$city_id }}";
        setTimeout(() => {
            $('#country').val(country_id).change();
        }, 200);


        $('#country').change(function() {
            const country = $(this).val();
            const stateDropdown = $('#state');
            const cityDropdown = $('#city');

            var countrie_id = $('#country').val();
            if (countrie_id == 101) {

                $('#gstin_text').text('GSTIN')
            } else {
                $('#gstin_text').text('TAXIN')
            }


            // Reset State and City Dropdowns
            stateDropdown.empty().append('<option value="">Select State</option>').prop('disabled', true);
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (country) {
                // Fetch States via AJAX
                $.ajax({
                    url: '{{ route('get.states') }}',
                    method: 'GET',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(state) {
                                stateDropdown.append(
                                    `<option value="${state.id}">${state.name}</option>`);
                            });
                            stateDropdown.prop('disabled', false);
                        }
                        $('#state').val(state_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch states. Please try again.');
                    }
                });
            }
        });

        // Handle State Change
        $('#state').change(function() {
            const state = $(this).val();
            const cityDropdown = $('#city');

            // Reset City Dropdown
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (state) {
                // Fetch Cities via AJAX
                $.ajax({
                    url: '{{ route('get.cities') }}',
                    method: 'GET',
                    data: {
                        state: state
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(city) {
                                cityDropdown.append(
                                    `<option value="${city.id}">${city.name}</option>`);
                            });
                            cityDropdown.prop('disabled', false);
                        }
                        $('#city').val(city_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch cities. Please try again.');
                    }
                });
            }
        });


        $(document).on('click', '.show-billing-address', function() {

            //alert("call");	
            $('#kt_billing_address_toggle').trigger('click');
            var countrie_id = $('#country').val();
            if (countrie_id == 101) {

                $('#gstin_text').text('GSTIN')
            } else {
                $('#gstin_text').text('TAXIN')
            }

        });
    </script>
@endpush
