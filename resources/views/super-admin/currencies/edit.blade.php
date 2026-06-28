@extends('layouts.superadmin_layout')
<style>
body{overflow-x: hidden;}
</style>
@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Edit Currency</h1>
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Edit currency details and save</span>
    </div>
  </div>
</div>
<!--end::toolbar-->

<!--begin::content-->
<div class="post d-flex flex-column-fluid mt-5 mb-5" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="editSettings" class="ajax-form">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-4">
                                <label for="company_name" class="form-label fw-bold text-dark fs-6 mb-0 required">@lang('modules.currency.currencyName')</label>
                                <input type="text" class="form-control form-control-lg form-control-solid" id="currency_name" name="currency_name" value="{{ $currency->currency_name }}">
                                <span class="form-text text-muted mb-3">Please enter currency name</span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="company_email" class="form-label fw-bold text-dark fs-6 mb-0 required">@lang('modules.currency.currencySymbol')</label>
                                <input type="email" class="form-control form-control-lg form-control-solid" id="currency_symbol" name="currency_symbol" value="{{ $currency->currency_symbol }}">
                                <span class="form-text text-muted mb-3">Please enter currency symbol</span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="company_phone" class="form-label fw-bold text-dark fs-6 mb-0 required">@lang('modules.currency.currencyCode')</label>
                                <input type="tel" class="form-control form-control-lg form-control-solid" id="currency_code" name="currency_code" value="{{ $currency->currency_code }}">
                                <span class="form-text text-muted mb-3">Please enter currency code</span>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="company_phone" class="form-label fw-bold text-dark fs-6 mb-0 required">INR Based Rates</label>
                                <input type="tel" class="form-control form-control-lg form-control-solid" id="inr_based_rates" name="inr_based_rates" value="{{ $currency->inr_based_rates }}">
                                <span class="form-text text-muted mb-3">Please enter inr based rates</span>
                            </div>
                            <div class="form-group mb-4">
                                <label for="company_phone" class="form-label fw-bold text-dark fs-6 mb-0 required">USD Based Rates</label>
                                <input type="tel" class="form-control form-control-lg form-control-solid" id="usd_based_rates" name="usd_based_rates" value="{{ $currency->usd_based_rates }}">
                                <span class="form-text text-muted mb-3">Please enter usd based rates</span>
                            </div>
                            <div class="d-flex justify-content-center pt-10">
                                <div class="mr-2">
                                    <button type="button" id="save-form" class="btn btn-sm btn-light-success fw-bold text-uppercase">@lang('app.save')</button>
                                    <button type="reset" class="btn btn-sm btn-light-warning fw-bold text-uppercase">@lang('app.reset')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::content-->

<!--begin::previous code-->
<!--<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('app.edit')</h4>
                <form id="editSettings1" class="ajax-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="company_name">@lang('modules.currency.currencyName')</label>
                        <input type="text" class="form-control" id="currency_name" name="currency_name" value="{{ $currency->currency_name }}">
                    </div>
                    <div class="form-group">
                        <label for="company_email">@lang('modules.currency.currencySymbol')</label>
                        <input type="email" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ $currency->currency_symbol }}">
                    </div>
                    <div class="form-group">
                        <label for="company_phone">@lang('modules.currency.currencyCode')</label>
                        <input type="tel" class="form-control" id="currency_code" name="currency_code" value="{{ $currency->currency_code }}">
                    </div>
                    <button type="button" id="save-form1" class="btn btn-success waves-effect waves-light m-r-10">@lang('app.save')</button>
                    <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                </form>
            </div>
        </div>
    </div>
</div>-->
<!--end::previous code-->
@endsection

@push('footer-script')
<script>

        $(document).on('click', '#save-form', function (e) {
            e.preventDefault();
            var url = "{{route('superadmin.currency-settings.update', $currency->id)}}";
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

            var form = $('#editSettings')[0];
            var formData = new FormData(form);

            // Laravel requires these
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('_method', 'PUT');

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
</script>
@endpush