@extends('layouts.superadmin_layout')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
<style>
.box-display
{
    display: none;
}
body
{
    overflow-x: hidden;
}
.switchery-small
{
    border-radius: 20px;
    height: 23px;
    width: 36px;
}
.switchery-small>small
{
    height: 20px;
    width: 20px;
    margin-top: 1px;
}
</style>
@endpush
@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
	<div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Payment Settings
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
                        <form id="editPaymentSettings" class="ajax-form" method="POST">
                            <div id="alert"></div>
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <!--begin::paypal-credentials-->
                                <div class="d-flex flex-stack pt-5">
                                    <div class="me-5 fw-bold">
                                        <label class="fs-3 text-primary">PayPal</label>
                                        <div class="fs-7 text-muted">PayPal Status</div>
                                    </div>
                                    <div class="">
                                        <input id="paypal_status" name="paypal_status" type="checkbox" @if($credentials->paypal_status == 'active') checked @endif class="js-switch change-language-setting" data-color="#99d683" data-size="small" data-setting-id="{{ $credentials->id }}" onchange="toggle('#paypal-credentials')">
                                        <span class="form-check-label fw-bold text-muted">Allowed</span>
                                    </div>
                                </div>
                                <div id="paypal-credentials" class="py-5">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2"  for="mail_username">Client ID</label>
                                                <input class="form-control form-control-solid" id="paypal_client_id" name="paypal_client_id" value="{{ $credentials->paypal_client_id }}" placeholder="Enter Client ID">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="twitter">Secret</label>
                                                <input class="form-control form-control-solid" id="paypal_secret" name="paypal_secret" value="{{ $credentials->paypal_secret }}" placeholder="Enter Secret">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="instagram">Webhook</label>
                                                <input class="form-control form-control-solid" disabled="disabled" value="{{ route('verify-billing-ipn') }}">
                                                <!-- <p class="text-bold">{{ route('verify-billing-ipn') }}</p> -->
                                                <p class="text-info">(@lang('messages.addPaypalWebhookUrl'))</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="dribbble">Select environment</label>
                                                <select class="form-select form-select-solid" name="paypal_mode" id="paypal_mode" data-control="select2" data-placeholder="Select an option" data-hide-search="true">
                                                    <option value="">Select environment</option>
                                                    <option value="sandbox" @if($credentials->paypal_mode == 'sandbox') selected @endif>Sandbox</option>
                                                    <option value="live" @if($credentials->paypal_mode == 'live') selected @endif>Live</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::paypal-credentials-->
                                <hr>
                                <!--begin::Stripe-credentials-->
                                <div class="d-flex flex-stack pt-5">
                                    <div class="me-5 fw-bold">
                                        <label class="fs-3 text-primary">Stripe</label>
                                        <div class="fs-7 text-muted">@lang('app.stripe') @lang('app.status')</div>
                                    </div>
                                    <div class="">
                                        <input id="stripe_status" name="stripe_status" type="checkbox" @if($credentials->stripe_status == 'active') checked @endif class="js-switch change-language-setting" data-color="#99d683" data-size="small" data-setting-id="{{ $credentials->id }}" onchange="toggle('#stripe-credentials');">
                                        <span class="form-check-label fw-bold text-muted">Allowed</span>
                                    </div>
                                </div>
                                <div id="stripe-credentials" class="py-5">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2"  for="api_key">Publishable Key</label>
                                                <input class="form-control form-control-solid"  id="api_key" name="api_key" value="{{ $credentials->api_key }}" placeholder="Enter Client ID">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="api_secret">Secret Key</label>
                                                <input class="form-control form-control-solid" id="api_secret" name="api_secret" value="{{ $credentials->api_secret }}" placeholder="Enter Secret">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="webhook_key">@lang('app.webhook')</label>
                                                <input class="form-control form-control-solid" id="webhook_key" name="webhook_key" value="{{ $credentials->webhook_key }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="mail_from_name">@lang('app.webhook') @lang('app.url')</label>
                                                <input class="form-control form-control-solid" disabled="disabled" value="{{ route('save_webhook') }}">
                                                <!-- <p class="text-bold">{{ route('verify-billing-ipn') }}</p> -->
                                                <p class="text-info">(@lang('messages.addStripeWebhookUrl'))</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Stripe-credentials-->
                                <hr>
                                <!--begin::Razorpay -credentials-->
                                <div class="d-flex flex-stack pt-5">
                                    <div class="me-5 fw-bold">
                                        <label class="fs-3 text-primary">Razorpay</label>
                                        <div class="fs-7 text-muted">@lang('app.razorpay') @lang('app.status')</div>
                                    </div>
                                    <div class="">
                                        <input  id="razorpay_status" name="razorpay_status" type="checkbox" @if($credentials->razorpay_status == 'active') checked @endif class="js-switch change-language-setting" data-color="#99d683" data-size="small" data-setting-id="{{ $credentials->id }}" onchange="toggle('#razorpay-credentials');">
                                        <span class="form-check-label fw-bold text-muted">Allowed</span>
                                    </div>
                                </div>
                                <div id="razorpay-credentials" class="py-5"  @if($credentials->razorpay_status != 'active') class="box-display" @endif>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2"  for="razorpay_key">Razorpay Key</label>
                                                <input class="form-control form-control-solid"  name="razorpay_key" id="razorpay_key" value="{{ $credentials->razorpay_key }}" placeholder="Enter Razorpay ID">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="razorpay_secret_key">Razorpay Secret Key</label>
                                                <input class="form-control form-control-solid"  name="razorpay_secret" id="razorpay_secret" value="{{ $credentials->razorpay_secret }}" placeholder="Enter Razorpay Secret Key">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2" for="razor_webhook_key">Razorpay Webhook Secret Key</label>
                                                <input class="form-control form-control-solid"  name="razorpay_webhook_secret" id="razorpay_webhook_secret" value="{{ $credentials->razorpay_webhook_secret }}" placeholder="Enter Razor webhook Key">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group">
                                                <label class="fs-6 fw-bold form-label mb-2"  for="mail_from_name">@lang('app.webhook') @lang('app.url')</label>
                                                <input class="form-control form-control-solid" disabled="disabled" value="{{ route('save_razorpay-webhook') }}">
                                                <!-- <p class="text-bold">{{ route('verify-billing-ipn') }}</p> -->
                                                <p class="text-info">(@lang('messages.addRazorpayWebhookUrl'))</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Razorpay-credentials-->
                            </div>
                            <div class="text-center py-10 mt-3">
                                <button class="btn btn-sm btn-light-primary" id="save-form" type="button">Save &amp; Update</button>
                                {{-- <button class="btn btn-sm btn-light-warning" type="reset">@lang('app.reset')</button> --}}
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


@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

    <script>
        function toggle(elementBox) {
            var elBox = $(elementBox);
            elBox.slideToggle();
        }

        $('#paypal_status').is(':checked') ? $('#paypal-credentials').show() : $('#paypal-credentials').hide();
        $('#stripe_status').is(':checked') ? $('#stripe-credentials').show() : $('#stripe-credentials').hide();

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());

        });

        // Update Mail Setting
        $('#save-form').click(function () {
            $.ajax({
                url: '{{route('superadmin.payment-settings.update', $credentials->id)}}',
                container: '#editPaymentSettings',
                type: "POST",
                redirect: true,
                messagePosition: "inline",
                data: $('#editPaymentSettings').serialize(),
                success: function (response) {
                    // Display success message
                    Swal.fire({
                        title: "Success!",
                        text: "Payment settings updated successfully.",
                        icon: "success",
                        confirmButtonText: "OK"
                    });

                    // Optionally, refresh the page or update content dynamically
                    // location.reload();
                },
                error: function (xhr) {
                    // Handle errors
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
              })
        });
    </script>

@endpush