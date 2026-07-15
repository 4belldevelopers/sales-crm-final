@extends('layouts.service_company_layout')
@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
    <style>
        body {
            overflow-x: hidden;
        }

        .w-md-130px {
            width: 130px !important;
        }

        .active_plan {
            width: fit-content;
        }

        .btn-group.hg-plan-btn-group {
            width: 100%;
        }

        .shared-hosting .container_hosting-package .btn-group button.btn {
            padding: 10px 15px;
        }

        .shared-hosting .container_hosting-package .btn-group button.btn {
            padding: 14px 15px;
            text-align: left;
            width: 100%;
        }

        .btn.btn-info.hg-plan-btn-info {
            box-shadow: none;
            border-color: #ddd;
            width: 100%;
            float: none;
        }

        .btn-group .selected-term {
            display: inline;
            color: #333;
        }

        .btn-group .selected-term .term-length {
            color: #666;
            font-size: 14px;
            text-transform: capitalize;
        }

        .btn-group .selected-term .WebRupee.webrupee1,
        .btn-group .selected-term span.WebRupee {
            color: #0171bc;
        }

        .hg-plan-btn-group span.WebRupee {
            font-size: 22px;
        }

        .btn-group.hg-plan-btn-group .selected-term span.term-price,
        .btn-group.hg-plan-btn-group .selected-term span.term-description {
            color: #0171bc;
        }

        .hg-plan-btn-group span.term-price {
            font-size: 22px;
            font-weight: 800;
        }

        .btn-group .selected-term .term-price {
            font-weight: 700;
        }

        .btn-group.hg-plan-btn-group .selected-term span.term-price,
        .btn-group.hg-plan-btn-group .selected-term span.term-description {
            color: #0171bc;
        }

        .btn-group .selected-term .term-price {
            font-weight: 700;
        }
<<<<<<< HEAD

        .btn-group.hg-plan-btn-group .selected-term span.term-price,
        .btn-group.hg-plan-btn-group .selected-term span.term-description {
            color: #0171bc;
        }

        .selected-term .testsdfd {
            margin-left: 3%;
            margin-right: 12%;
            line-height: 17px;
            float: none;
        }

        .hg-percentage-discount {
            color: #f15922;
            font-size: 14px;
            font-weight: 500;
        }

        span.hg-actual-price,
        .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee,
        .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee,
        .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
            color: #767676;
        }

        span.hg-actual-price {
            display: block;
            font-size: 14px;
            font-weight: 500;
        }

        span.hg-actual-price,
        .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee,
        .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee,
        .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
            color: #767676;
        }

        .hg-plan-btn-group .hg-actual-price span.WebRupee {
            font-size: 14px;
        }

        .WebRupee {
            font-family: 'WebRupee' !important;
        }

        span.hg-actual-price {
            display: block;
            font-size: 14px;
            font-weight: 500;
        }

        span.hg-actual-price,
        .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee,
        .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee,
        .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
            color: #767676;
        }

        .package-term {
            font-size: 16px;
        }

        .dropdown-menu li {
            padding: .8em 1em;
            cursor: pointer;
            border-bottom: 1px dotted lightgray;
            display: block;
        }

        .dropdown-menu li:last-child {
            border-bottom: none;
        }

        .active {
            color: white !important;
        }


        /* Main container styling */
        .container {
            padding: 20px;
            text-align: center;
        }

        /* Loader styles */
        .loader-overlay {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            /* White overlay */
            z-index: 1000;
            /* Ensure it stays on top */
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 6px solid #f3f3f3;
            /* Light grey */
            border-top: 6px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        /* Loader animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Text below loader */
        .loader-text {
            margin-top: 10px;
            font-size: 18px;
            color: #333;
        }
        .no-time-message {
            display: flex;
            justify-content: center; /* Centers horizontally */
            align-items: center;    /* Centers vertically */
            width: 100%;
            text-align: center;     /* Aligns text to center */
            font-size: 1.5rem;      /* Adjust font size as needed */
            color: #555;            /* Adjust color as needed */
        }

        /* ===== Secure Checkout redesign ===== */
        .co-header h1 { font-size: 16px; }
        .co-secure-badge {
            background: #ecfdf3; color: #10a34a; border: 1px solid #b7ebc6;
            font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 8px;
        }
        .co-card { border: 1px solid #e4e6ef; border-radius: 12px; padding: 20px 22px; margin-bottom: 20px; }
        .co-card-title { font-size: 14px; font-weight: 700; color: #181c32; }
        .co-icon-circle {
            width: 36px; height: 36px; border-radius: 50%; background: #f5f3ff; color: #6d28d9;
            display: inline-flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 15px;
        }
        .co-badge-type { background: #f5f3ff; color: #6d28d9; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
        .co-order-icon {
            width: 56px; height: 56px; border-radius: 12px; background: #f5f3ff; color: #6d28d9;
            display: flex; align-items: center; justify-content: center; font-size: 22px;
        }
        .co-feature-item { font-size: 12px; color: #4a4f63; padding: 3px 0; }
        .co-feature-item i { color: #10a34a; margin-right: 6px; }
        .co-stat-value { font-size: 16px; font-weight: 800; color: #181c32; }
        .co-stat-label { font-size: 10px; color: #8a8fa3; }
        .co-coupon-box { background: #f8f9fc; border-radius: 10px; padding: 16px; }
        .co-coupon-applied { background: #ecfdf3; border: 1px solid #b7ebc6; border-radius: 10px; padding: 12px 16px; }
        .co-feature-strip { border-top: 1px solid #e4e6ef; padding-top: 16px; margin-top: 8px; }
        .co-feature-strip-item { text-align: center; font-size: 11px; }
        .co-feature-strip-item i { font-size: 20px; color: #6d28d9; display: block; margin-bottom: 6px; }
        .co-summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px; }
        .co-summary-total { font-size: 20px; font-weight: 800; color: #4c1d95; }
        .co-save-banner { background: #ecfdf3; border: 1px solid #b7ebc6; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #14532d; }
        .co-pay-btn {
            width: 100%; background: #4c1d95 !important; border: none; padding: 12px; font-weight: 700; border-radius: 10px;
        }
        .co-help-card { border: 1px solid #e4e6ef; border-radius: 12px; padding: 18px; }
        .co-empty-state { text-align: center; padding: 50px 30px; width: 100%; }
        .co-empty-icon-wrap { position: relative; display: inline-block; margin-bottom: 20px; }
        .co-empty-icon-wrap > i {
            font-size: 46px; color: #6d28d9; background: #f5f3ff; border-radius: 50%;
            width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;
        }
        .co-empty-icon-badge {
            position: absolute; top: -2px; right: -2px; width: 26px; height: 26px; border-radius: 50%;
            background: #6d28d9; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px;
        }
        .co-empty-divider { display: flex; align-items: center; text-align: center; color: #8a8fa3; font-size: 11px; margin: 10px 0; }
        .co-empty-divider::before, .co-empty-divider::after { content: ''; flex: 1; border-bottom: 1px solid #e4e6ef; }
        .co-empty-divider span { padding: 0 12px; }
        .co-summary-empty-icon {
            width: 90px; height: 90px; border-radius: 50%; background: #f5f3ff; color: #d6d1e8;
            display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 16px;
        }
        .co-feature-strip-item span { display: block; }
=======

        .btn-group.hg-plan-btn-group .selected-term span.term-price,
        .btn-group.hg-plan-btn-group .selected-term span.term-description {
            color: #0171bc;
        }

        .selected-term .testsdfd {
            margin-left: 3%;
            margin-right: 12%;
            line-height: 17px;
            float: none;
        }

        .hg-percentage-discount {
            color: #f15922;
            font-size: 14px;
            font-weight: 500;
        }

        span.hg-actual-price,
        .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee,
        .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee,
        .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
            color: #767676;
        }

        span.hg-actual-price {
            display: block;
            font-size: 14px;
            font-weight: 500;
        }

        span.hg-actual-price,
        .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee,
        .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee,
        .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
            color: #767676;
        }

        .hg-plan-btn-group .hg-actual-price span.WebRupee {
            font-size: 14px;
        }

        .WebRupee {
            font-family: 'WebRupee' !important;
        }

        span.hg-actual-price {
            display: block;
            font-size: 14px;
            font-weight: 500;
        }

        span.hg-actual-price,
        .plan-featured .hg-plan-btn-group .hg-actual-price del span.WebRupee,
        .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee,
        .plan-featured .hg-plan-btn-group .selected-term .testsdfd .hg-actual-price del span.WebRupee {
            color: #767676;
        }

        .package-term {
            font-size: 16px;
        }

        .dropdown-menu li {
            padding: .8em 1em;
            cursor: pointer;
            border-bottom: 1px dotted lightgray;
            display: block;
        }

        .dropdown-menu li:last-child {
            border-bottom: none;
        }

        .active {
            color: white !important;
        }


        /* Main container styling */
        .container {
            padding: 20px;
            text-align: center;
        }

        /* Loader styles */
        .loader-overlay {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            /* White overlay */
            z-index: 1000;
            /* Ensure it stays on top */
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 6px solid #f3f3f3;
            /* Light grey */
            border-top: 6px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        /* Loader animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Text below loader */
        .loader-text {
            margin-top: 10px;
            font-size: 18px;
            color: #333;
        }
        .no-time-message {
            display: flex;
            justify-content: center; /* Centers horizontally */
            align-items: center;    /* Centers vertically */
            width: 100%;
            text-align: center;     /* Aligns text to center */
            font-size: 1.5rem;      /* Adjust font size as needed */
            color: #555;            /* Adjust color as needed */
        }

>>>>>>> parent of e32f1d9c (Subscriptions)
    </style>
@endpush

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    $currency = GeneralMethod::convertINRToUSD();
    $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

    // dd($payment_settings);
    $durationType = 0;
    $durationTypeText = "/Year";
    if (@$carts){
        $number = $carts->duration;
        // dd($number);
        $months = 12;
        if ($number >= 365) {
            $years = floor($number / 365);
            $remainingDays = $number % 365;
           $durationType =  "$years Year" . ($remainingDays > 0 ? ", $remainingDays day(s)" : "");
           $durationTypeText = "/Year";
           $months = floor($number / 30);

        } elseif ($number >= 30) {
            $months = floor($number / 30);
            $remainingDays = $number % 30;
           $durationType =  "$months Month" . ($remainingDays > 0 ? ", $remainingDays day(s)" : "");
           $durationTypeText = "/Month";
        } elseif ($number >= 7) {
            $weeks = floor($number / 7);
            $remainingDays = $number % 7;
           $durationType =  "$weeks Week" . ($remainingDays > 0 ? ", $remainingDays day(s)" : "");
           $durationTypeText = "/Week";
        } else {
           $durationType =  "$number Days";
           $durationTypeText = "/Day";
        }

        
        $actualPrice = @$carts->price;
        $perMonthPrice  = 0;
        $perMonthPrice = $actualPrice/$months;
        if (@$carts->discount) {
            $actualPrice = $carts->price / (1 - $carts->discount / 100);
            $perMonthPrice = $actualPrice/$months;
        }
                                                          


    }

@endphp

<div id="loaderOverlay" class="loader-overlay">
    <div class="loader"></div>
    <div class="loader-text">Processing payment...</div>
</div>


@section('content')
<<<<<<< HEAD
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
        <div class="container-xxl" id="kt_content_container">

            <div class="d-flex flex-wrap flex-stack mb-6">
                <div class="co-header d-flex flex-column">
                    <h1 class="d-flex align-items-center text-gray-900 fw-bold mb-1">
                        <i class="fa fa-shield-alt text-primary me-2"></i> Secure Checkout
                    </h1>
                    <div class="text-gray-500 fs-8">Review your order and complete your payment securely.</div>
                </div>
                <div class="d-flex flex-column align-items-end">
                    <span class="co-secure-badge"><i class="fa fa-lock me-1"></i> 100% Secure Checkout</span>
                    <span class="text-gray-500 fs-9 mt-1">Your payment details are safe with us</span>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">

                    <!--begin::Billing Information-->
                    <div class="co-card">
                        <div class="d-flex flex-stack mb-4">
                            <div class="d-flex align-items-center">
                                <span class="co-icon-circle"><i class="fa fa-building"></i></span>
                                <span class="co-card-title">Billing Information</span>
                            </div>
                            <a href="javascript:;" class="btn btn-sm btn-light-primary show-billing-address">Edit Billing</a>
                        </div>
                        <div class="fs-7 text-gray-700">
                            <div class="fw-bold fs-6 mb-1">{{ @$company_name }}</div>
                            <div><span id="usernamebill">{{ @$user_name }}</span></div>
                            <div><span id="billing_address">{{ @$locArr }}</span></div>
                            @if(@$gstin)
                                <div class="mt-1">GSTIN: <span id="billing_gst">{{ @$gstin }}</span></div>
                            @else
                                <div><span id="billing_gst" style="display:none;"></span></div>
                            @endif
                            <span id="billing_pan" style="display:none;">{{ @$pan_no }}</span>
                        </div>
                    </div>
                    <!--end::Billing Information-->

                    @if (@$carts)
                        <!--begin::Your Order-->
                        <div class="co-card">
                            <div class="d-flex flex-stack mb-4">
                                <div class="d-flex align-items-center">
                                    <span class="co-icon-circle"><i class="fa fa-tag"></i></span>
                                    <span class="co-card-title">Your Order (1 item)</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <a href="{{ route('service_company.subscribe.newindex') }}#tab=packages" class="btn btn-sm btn-light-primary">Edit Order</a>
                                    <a href="javascript:;" class="text-danger" data-toggle="tooltip" title="Remove">
                                        <i class="fa fa-trash fa-lg" aria-hidden="true"
                                            data-trash="{{ encrypt(@$carts->id) }}"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <span class="co-order-icon me-4"><i class="fa fa-coins"></i></span>
                                <div class="flex-grow-1">
                                    <span class="co-badge-type mb-2 d-inline-block">{{ ucfirst(@$carts->event_type) }} Coins</span>
                                    <div class="fw-bold fs-4 text-gray-900 mb-2">{{ @$carts->product_name }}</div>
                                    <div class="co-feature-item"><i class="fa fa-check-circle"></i>{{ number_format(@$carts->credit) }} {{ ucfirst(@$carts->event_type) }} Coins</div>
                                    <div class="co-feature-item"><i class="fa fa-check-circle"></i>Validity: {{ @$carts->duration }} Days</div>
                                </div>
                                <div class="d-flex text-center gap-6">
                                    <div>
                                        <div class="co-stat-value">{{ number_format(@$carts->credit) }}</div>
                                        <div class="co-stat-label">Coins</div>
                                    </div>
                                    <div>
                                        <div class="co-stat-value">{{ @$durationType }}</div>
                                        <div class="co-stat-label">Validity</div>
                                    </div>
                                    <div>
                                        <div class="co-stat-value"><i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($actualPrice, 2) }}</div>
                                        <div class="co-stat-label">Price</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Your Order-->

                        <!--begin::Coupon-->
                        <div class="co-card">
                            <div class="d-flex align-items-center mb-4">
                                <span class="co-icon-circle"><i class="fa fa-ticket-alt"></i></span>
                                <span class="co-card-title">Have Coupon?</span>
                            </div>

                            <div id="couponInputState" class="co-coupon-box" style="{{ @$carts->coupon_code ? 'display:none;' : '' }}">
                                <label class="fs-8 text-gray-600 mb-2 d-block">Apply a coupon code to get discount</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="text" id="couponCodeInput" class="form-control form-control-solid"
                                        placeholder="Enter coupon code">
                                    <button type="button" id="applyCouponBtn" class="btn btn-primary">Apply</button>
                                </div>
                                <div id="couponError" class="text-danger fs-8 mt-2" style="display:none;"></div>
                            </div>

                            <div id="couponAppliedState" class="co-coupon-applied d-flex align-items-center justify-content-between"
                                style="{{ @$carts->coupon_code ? '' : 'display:none;' }}">
                                <div>
                                    <div class="fw-bold text-success" id="couponAppliedCode">{{ @$carts->coupon_code }}</div>
                                    <div class="fs-8 text-success">
                                        <span id="couponAppliedPct">{{ @$carts->coupon_code ? rtrim(rtrim(number_format(@$carts->discount, 2), '0'), '.') : '' }}</span>% Discount Applied
=======
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <span class="text-gray-800 mb-0 pt-5">Subscription Package</span>
                    </h5>
                    <span class="text-gray-600" id="">&nbsp;</span>
                </div>
                <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
            </div>
            <div class="d-flex align-items-center py-1 d-none ">
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
        <div class=" container-xxl " id="kt_content_container">
            <!--begin::Invoice 2 main-->
            <div class="card">
                <!--begin::Body-->
                <div class="card-body p-lg-20">
                    <!--begin::Layout-->
                    <div class="d-flex flex-column flex-xl-row">
                        <!--begin::Content-->
                        @if (@$carts)
                        <div class="flex-lg-row-fluid me-xl-18 mb-10 mb-xl-0">
                            <!--begin::Invoice 2 content-->
                            <div class="mt-n1">

                               
                                <!--begin::Wrapper-->
                                <div class="m-0">



                                    <!--begin::Row-->
                                    <div class="row g-5 mb-12">
                                        <!--end::Col-->
                                        <div class="col-sm-12">
                                            <!--end::Label-->

                                            <!--end::Text-->

                                            <!--end::Text-->

                                            <!--end::Description-->



                                            <!--end::Description-->
                                            <div class="fw-semibold fs-7 text-gray-600">

                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <div class="fw-bold fs-6 text-gray-800">Billing Details</div>
                                                        <p><span id="usernamebill">{{ @$user_name }}</span><p>
                                                        <p>{{ @$company_name }}<p>
                                                        <p><span id="billing_address">{{ @$locArr }}</span> <p>
                                                        <p><span id="billing_gst">{{ @$gstin }}</span> <p>
                                                        <p><span id="billing_pan">{{ @$pan_no }}</span> <p>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <a href="javascript:;" class="btn btn-primary show-billing-address"
                                                            id="" style="float: right;">Edit Details</a>
                                                    </div>

                                                </div>

                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Col-->

                                        <!--end::Col-->
                                        <div class="col-sm-6">
                                            <!--end::Label-->


                                            <!--end::Description-->
                                            <div class="fw-semibold fs-7 text-gray-600">

                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->


                                    <!--begin::Content-->
                                    <div class="flex-grow-1">
                                        <!--begin::Table-->
                                        <div class="table-responsive border-bottom mb-9">
                                            <table class="table mb-3">
                                                <thead>
                                                    <tr class="border-bottom fs-6 fw-bold text-muted">
                                                        <th class="min-w-50px pb-2">Product</th>
                                                        <th class="min-w-50px pb-2">Duration</th>
                                                        <th class="min-w-50px pb-2">Coins</th>
                                                        <th class="min-w-50px pb-2">Price</th>
                                                        <th class="min-w-50px pb-2">Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @if (@$carts)
                                                        <tr class="fw-bold text-gray-700 fs-5">
                                                            <td class="d-flex align-items-center pt-6">
                                                                 {{ @$carts ? @$carts->product_name : '' }}
                                                            </td>

                                                            <td class="pt-6">
                                                                {{ @$carts->subscription_type == 'yes' ? @$durationType : @$durationType }} at <i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"
                                                                    aria-hidden="true"></i> {{ number_format($perMonthPrice,2) }}/Month
                                                            </td>
                                                            <td class="pt-6 fw-bolder">
                                                                {{ @$carts ? $carts->credit : '' }}</td>
                                                            <td class="pt-6">
                                                                <i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"
                                                                    aria-hidden="true"></i>
                                                            @php
                                                                    $actualPrice = @$carts->price;
                                                                    if (@$carts->discount) {
                                                                        $actualPrice = $carts->price / (1 - $carts->discount / 100);
                                                                    }
                                                            @endphp

                                                                {{ @$carts ?  number_format($actualPrice,2) : '' }}</td>
                                                            <td class="pt-6"><i class="fa fa-trash" aria-hidden="true"
                                                                    data-trash="{{ encrypt(@$carts->id) }}" style="cursor: pointer;"></i></td>
                                                        </tr>
                                                    @else
                                                        <tr class="border-bottom fs-6 fw-bold text-muted"
                                                            style="text-align: center; background: #ff000012;">
                                                            <td colspan="5" class="min-w-175px pb-2">No Product</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--end::Table-->


>>>>>>> parent of e32f1d9c (Subscriptions)
                                    </div>
                                    <!--end::Content-->
                                </div>
<<<<<<< HEAD
                                <a href="javascript:;" id="removeCouponBtn" class="text-danger fs-3">&times;</a>
=======
                                <!--end::Wrapper-->
                               
>>>>>>> parent of e32f1d9c (Subscriptions)
                            </div>
                            <!--end::Invoice 2 content-->
                        </div>
<<<<<<< HEAD
                        <!--end::Coupon-->
                    @else
                        <!--begin::Empty cart state-->
                        <div class="co-card co-empty-state" style="background:#fff;">
                            <div class="co-empty-icon-wrap">
                                <i class="fa fa-cart-shopping"></i>
                                <span class="co-empty-icon-badge"><i class="fa fa-exclamation"></i></span>
                            </div>
                            <div class="fw-bold fs-2 text-gray-800 mb-2">Your cart is empty</div>
                            <div class="text-gray-500 fs-7">Looks like you haven't selected any plan or item yet.</div>
                            <div class="text-gray-500 fs-7 mb-6">Choose a subscription plan or add items to your cart to continue.</div>

                            <div class="d-flex justify-content-center align-items-center gap-3 mb-6">
                                <a href="{{ route('service_company.subscribe.newindex') }}#tab=packages" class="btn btn-primary">Explore Plans</a>
                                <a href="{{ route('service_company.subscribe.checkoutSubscribeIist') }}" class="btn btn-outline btn-outline-primary">View My Cart</a>
                            </div>

                            <div class="co-empty-divider"><span>or</span></div>

                            <div class="d-flex align-items-center justify-content-center mt-6">
                                <span class="co-icon-circle"><i class="fa fa-coins"></i></span>
                                <div class="text-start">
                                    <div class="fw-bold text-gray-800">Need more coins?</div>
                                    <div class="text-gray-500 fs-8">You can purchase additional coins or upgrade your plan anytime.</div>
                                    <a href="{{ route('service_company.subscribe.newindex') }}#tab=packages" class="fw-bold">Buy Coins <i class="fa fa-chevron-right fs-9"></i></a>
                                </div>
=======
                        <!--end::Content-->

                        <!--begin::Sidebar-->
                        <div class="m-0">
                            <!--begin::Invoice 2 sidebar-->
                            <div
                                class="d-print-none border border-dashed border-gray-300 card-rounded h-lg-100 min-w-md-350px p-9 bg-lighten">

                                <!--begin::Title-->
                                <h6 class="mb-8 fw-bolder text-gray-600 text-hover-primary">PAYMENT DETAILS</h6>
                                <!--end::Title-->

                                <!--begin::Container-->
                                <div class="mb-6">
                                    <!--begin::Section-->
                                    <div class="mw-300px">

                                        <div class="d-flex justify-content-between mb-3">
                                            <!--begin::Label-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">Subtotal:</div>
                                            <!--end::Label-->

                                            <!--begin::Value-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">

                                                @if (@$carts)
                                                    <i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"
                                                        aria-hidden="true"></i>
                                                @endif
                                                @php
                                                    $actualPrice = $carts->price;
                                                    if ($carts->discount) {
                                                        $actualPrice = $carts->price / (1 - $carts->discount / 100);
                                                    }
                                                @endphp

                                                {{  number_format($actualPrice,2) }}
                                            </div>
                                            <!--end::Value-->
                                        </div>

                                        @if(@$carts->discount)
                                         <!--begin::Item-->
                                         <div class="d-flex justify-content-between mb-3">
                                            <!--begin::Label-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">Discounts :</div>
                                            <!--end::Label-->

                                            <!--begin::Value-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">
                                                {{ @$carts->discount }}%
                                            </div>
                                            <!--end::Value-->
                                        </div>
                                        
                                        <!--begin::Item-->
                                        <div class="d-flex justify-content-between mb-3">
                                            <!--begin::Label-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">Price Offer Discount:</div>
                                            <!--end::Label-->

                                            <!--begin::Value-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">
                                                
                                                @if (@$carts)
                                                    <i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"
                                                        aria-hidden="true"></i>
                                                @endif

                                                {{ @$carts->price }}
                                            </div>
                                            <!--end::Value-->
                                        </div>

                                        <!--end::Item-->
                                        @endif

                                        <!--begin::Item-->
                                        <div class="d-flex justify-content-between mb-3">
                                            <!--begin::Label-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">{{ @$carts->currency == 'INR' ? 'GST' : 'TAX' }}</div>
                                            <!--end::Label-->

                                            <!--begin::Value-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">
                                                {{ @$carts->gst ? @$carts->gst . '%' : '0.00' }}</div>
                                            <!--end::Value-->
                                        </div>
                                        <!--end::Item-->

                                        <!--begin::Item-->


                                        <!--begin::Item-->
                                        <div class="d-flex justify-content-between">
                                            <!--begin::Label-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">Total</div>
                                            <!--end::Label-->

                                            <!--begin::Value-->
                                            <div class="text-end fw-bold fs-6 text-gray-800">
                                                @if (@$carts)
                                                    <i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"
                                                        aria-hidden="true"></i>
                                                @endif
                                                {{ @$carts->total_price }}
                                            </div>
                                            <!--end::Value-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Section-->



                                </div>

                                <!--end::Container-->


                                <div class="mb-6">

                                    <a href="#"
                                        class="btn btn-sm btn-success {{ $carts_count ? 'cart_add' : 'cert_warning' }}"
                                        style="    background-color: #be4783 !important; width: 100% !important;"
                                        data-cart="{{ encrypt(@$carts->id) }}">Pay
                                        Now</a>
                                    <!--end::Title-->
                                </div>



>>>>>>> parent of e32f1d9c (Subscriptions)
                            </div>
                            <!--end::Invoice 2 sidebar-->
                        </div>
<<<<<<< HEAD
                        <!--end::Empty cart state-->
                    @endif
                </div>

                <div class="col-xl-4">
                    @if (@$carts)
                        <!--begin::Order Summary-->
                        <div class="co-card">
                            <div class="d-flex align-items-center mb-4">
                                <span class="co-icon-circle"><i class="fa fa-bookmark"></i></span>
                                <span class="co-card-title">Order Summary</span>
                            </div>

                            <div class="co-summary-row">
                                <span class="text-gray-600">Subtotal ({{ @$carts->product_name }})</span>
                                <span class="fw-bold"><i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($actualPrice, 2) }}</span>
                            </div>

                            @if(@$carts->discount)
                                <div class="co-summary-row">
                                    <span class="text-gray-600">Discount ({{ rtrim(rtrim(number_format($carts->discount, 2), '0'), '.') }}%)</span>
                                    <span class="fw-bold text-success">-<i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($actualPrice - $carts->price, 2) }}</span>
                                </div>
                                <div class="co-summary-row">
                                    <span class="text-gray-600">Price After Discount</span>
                                    <span class="fw-bold"><i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($carts->price, 2) }}</span>
                                </div>
                            @endif

                            <div class="co-summary-row">
                                <span class="text-gray-600">{{ @$carts->currency == 'INR' ? 'GST' : 'TAX' }} ({{ @$carts->gst ? rtrim(rtrim(number_format($carts->gst, 2), '0'), '.') : 0 }}%)</span>
                                <span class="fw-bold"><i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format(@$carts->gst_amount, 2) }}</span>
                            </div>

                            <hr>

                            <div class="co-summary-row align-items-center">
                                <span class="fw-bold fs-6">Total Amount</span>
                                <span class="co-summary-total"><i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format(@$carts->total_price, 2) }}</span>
                            </div>

                            @if(@$carts->discount)
                                <div class="co-save-banner d-flex justify-content-between align-items-center mb-4">
                                    <span>You Save</span>
                                    <span class="fw-bold"><i class="fa fa-{{ @$carts->currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($actualPrice - $carts->price, 2) }} ({{ rtrim(rtrim(number_format($carts->discount, 2), '0'), '.') }}%)</span>
                                </div>
                            @endif

                            <a href="#" class="btn btn-sm btn-success co-pay-btn {{ $carts_count ? 'cart_add' : 'cert_warning' }}"
                                data-cart="{{ encrypt(@$carts->id) }}">
                                <i class="fa fa-lock me-1"></i> Proceed to Secure Payment
                            </a>
                            <div class="text-center text-gray-500 fs-9 mt-2">You will be redirected to Razorpay</div>
                        </div>
                        <!--end::Order Summary-->
                    @else
                        <!--begin::Order Summary empty-->
                        <div class="co-card text-center" style="background:#fff;">
                            <div class="d-flex align-items-center mb-4">
                                <span class="co-icon-circle"><i class="fa fa-bookmark"></i></span>
                                <span class="co-card-title">Order Summary</span>
                            </div>
                            <div class="co-summary-empty-icon"><i class="fa fa-file-alt"></i></div>
                            <div class="fw-bold fs-5 text-gray-800 mb-2">No items selected</div>
                            <div class="text-gray-500 fs-8 mb-6">Your order summary will appear here once you add items to your cart.</div>
                            <div class="text-start">
                                <div class="co-feature-item"><i class="fa fa-check-circle"></i>No hidden charges</div>
                                <div class="co-feature-item"><i class="fa fa-check-circle"></i>GST invoice included</div>
                                <div class="co-feature-item"><i class="fa fa-check-circle"></i>Instant activation</div>
                                <div class="co-feature-item"><i class="fa fa-check-circle"></i>24x7 support</div>
                            </div>
                        </div>
                        <!--end::Order Summary empty-->
                    @endif

                    <!--begin::Need Help-->
                    <div class="co-help-card">
                        <div class="d-flex align-items-center mb-3">
                            <span class="co-icon-circle"><i class="fa fa-shield-alt"></i></span>
                            <span class="co-card-title">Need Help?</span>
                        </div>
                        <div class="text-gray-600 fs-8 mb-3">Have a question about your order or billing? Our team is here to help.</div>
                        <a href="mailto:support@jero.works" class="btn btn-sm btn-light-primary w-100">
                            <i class="fa fa-envelope me-1"></i> Email Us
                        </a>
                    </div>
                    <!--end::Need Help-->
                </div>
            </div>

            <!--begin::Feature strip-->
            <div class="co-card" style="background:#fff;">
                <div class="row co-feature-strip">
                    <div class="col co-feature-strip-item"><i class="fa fa-file-invoice"></i><span class="fw-bold text-gray-800">GST Invoice</span><div class="text-gray-500">Automatic invoice with GST</div></div>
                    <div class="col co-feature-strip-item"><i class="fa fa-bolt"></i><span class="fw-bold text-gray-800">Instant Activation</span><div class="text-gray-500">Coins will be added instantly</div></div>
                    <div class="col co-feature-strip-item"><i class="fa fa-lock"></i><span class="fw-bold text-gray-800">Secure Payment</span><div class="text-gray-500">Razorpay secured checkout</div></div>
                    <div class="col co-feature-strip-item"><i class="fa fa-undo"></i><span class="fw-bold text-gray-800">Money Back</span><div class="text-gray-500">7-day refund policy</div></div>
                    <div class="col co-feature-strip-item"><i class="fa fa-headset"></i><span class="fw-bold text-gray-800">24x7 Support</span><div class="text-gray-500">We're here to help you</div></div>
                </div>
            </div>
            <!--end::Feature strip-->
=======
                        <!--end::Sidebar-->
                        @else

                        <div class="no-time-message">
                            <p>No data available</p>
                        </div>

                        @endif

                    </div>
                    <!--end::Layout-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Invoice 2 main-->
>>>>>>> parent of e32f1d9c (Subscriptions)
        </div>
    </div>



    <div id="kt_billing_address_toggle"></div>
    <div id="kt_activitiesApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_billing_address_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
        <div class="card shadow-none rounded-0 w-100" id="application_show_right_bar">


            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <a href="javascript:void(0);" id=""
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"> <span
                            class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <path
                                        d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path
                                        d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero"></path>
                                </g>
                            </svg>
                        </span> Billing Details
                    </a>

                </div>

                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesApplication_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card card-custom mx-2">
                <div class="card-body py-0">
                    <div class="tab-content pt-5">
                        <form id="show-billing-form">
                            @csrf
                            <input class="form-control form-control-solid" value="{{ $company_id }}" type="hidden"
                                        id="company_id" name="company_id">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="country">Country / Region</label>
                                            <select class="form-control form-control-solid" id="country" name="country">

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
                                            <input class="form-control form-control-solid" value="{{ $firstName }}"
                                                type="text" id="first_name" name="first_name" placeholder="First Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input class="form-control form-control-solid" value="{{ $lastName }}" type="text"
                                                id="last_name" name="last_name" placeholder="Last Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input class="form-control form-control-solid" value="{{ $phone }}" type="tel" id="phone"
                                                name="phone" placeholder="Phone Number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="building">Building / Society</label>
                                            <input class="form-control form-control-solid" value="{{ $building }}" type="text" id="building"
                                                name="building" placeholder="Building / Society">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="street">Street Name / Landmark</label>
                                            <input class="form-control form-control-solid" value="{{ @$locArr }}"
                                                type="text" id="street" name="street"
                                                placeholder="Street Name / Landmark">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <select class="form-control form-control-solid" id="state" name="state_id">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <select class="form-control form-control-solid" id="city" name="city">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input class="form-control form-control-solid" value="{{ $postal_code }}" type="text" id="postal_code"
                                        name="postal_code" placeholder="Postal Code">
                                </div>
                                <div class="form-group">
                                    <label for="organization">Organization</label>
                                    <input class="form-control form-control-solid" type="text" value="{{ @$company_name }}"
                                        id="organization" name="organization" placeholder="Organization" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="gstin" id="gstin_text">GSTIN</label>
                                    <input class="form-control form-control-solid" value="{{ $gstin }}" type="text" id="gstin" name="gstin"
                                        placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="pan_no" id="pan_no_text">PAN No.</label>
                                    <input class="form-control form-control-solid" value="{{ $pan_no }}" type="text" id="pan_no" name="pan_no"
                                        placeholder="">
                                </div>
                        <!-- Buttons -->
                            <div class="text-center mt-4">
                                <button type="submit" id="submitbtn" class="btn btn-light-primary">
                                    <span class="indicator-label">Save</span>
                                    <span class="indicator-progress">
                                        Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                    </form>


                    </div>
                </div>
            </div>


        </div>
    </div>







    <!--end::Content-->
@endsection
@push('footer-script')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>

        
        var country_id = "{{ $countrie_id }}";
        var state_id = "{{ $state_id }}";
        var city_id = "{{ $city_id }}";
        setTimeout(() => {
            $('#country').val(country_id).change();
        }, 200);


        $('#country').change(function() {
            const country = $(this).val();
            const stateDropdown = $('#state');
            const cityDropdown = $('#city');

            var countrie_id = $('#country').val();
            if(countrie_id==101){
                
                $('#gstin_text').text('GSTIN')
            }else{
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
            if(countrie_id==101){
                
                $('#gstin_text').text('GSTIN')
            }else{
                $('#gstin_text').text('TAXIN')
            }




        });

        $(document).on('submit', '#show-billing-form', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '{{ route('form.submit') }}', // Route to handle form submission
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token header
                },
                success: function (response) {
                    // Handle success
                    Swal.fire("Success", "Billing Address updated successfully ", "success").then(
                                        function() {
                                            $('#usernamebill').text(response.usernamebill);
                                            $('#billing_address').text(response.street);
                                            $('#billing_gst').text(response.billing_gst);
                                            $('#billing_pan').text(response.billing_pan);
                                            $('#kt_activitiesApplication_close').click();
                                        });
                },
                error: function (response) {
                    // Handle error
                    console.log(response);
                    alert('Form submission failed');
                }
            });
        });






        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function(html) {
            var switchery = new Switchery(html, {
                size: 'medium'
            });
        });

        $('.salse-packages').hide();
        $('.fa-trash').click(function() {
            let checked = $(this).data('trash');
            // alert(checked);
            var url = "{{ route('service_company.subscribe.deletecheckout') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    '_method': 'GET',
                    'id': checked
                },

                success: function(response) {
                    window.location.reload(true);
                }
            });


        });

        $('.cert_warning').click(function() {
            Swal.fire("Warning", "Cart is empty", "warning");
        });

        $('.cart_add').click(function() {
            let checked = $(this).data('cart');
            // alert(checked);
            var url = "{{ route('service_company.subscribe.cartcheckout') }}";
            document.getElementById('loaderOverlay').style.display = 'flex';
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    '_method': 'GET',
                    'id': checked
                },

                success: function(response) {

                    initiateRazorpayPayment(response.orderId, response.amount, response.update_coins,
                        response.company_id, response.event_type, response.currency, response
                        .start_date, response.end_date, response.coin_order_id, response
                        .subscription_type, response.cart_id);
                        
                }
            });


        });

        // initiateRazorpayPayment(response.orderId, response.amount,response.update_coins,response.company_id,response.event_type);

        function initiateRazorpayPayment(orderId, amount, update_coins, company_id, event_type, currency, start_date,
            end_date, coin_order_id, subscription_type, cart_id) {

            var options = {
                "key": "{{ $payment_settings->razorpay_key }}", // Razorpay API Key
                "amount": amount, // Amount in paisa
                "currency": currency,
                "name": name,
                "description": "Test Transaction",
                "order_id": orderId, // Order ID created in backend
                "handler": function(response) {


                    var formData = {
                        _token: "{{ csrf_token() }}", // CSRF token for security
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id, // Optional, if you want to pass it back
                        razorpay_signature: response.razorpay_signature, // Optional, if needed
                        razorpay_update_coins: update_coins, // Optional, if needed
                        razorpay_company_id: company_id, // Optional, if needed
                        razorpay_event_type: event_type, // Optional, if needed
                        razorpay_amount: amount, // Optional, if needed
                        razorpay_currency: currency, //
                        razorpay_start_date: start_date, //
                        razorpay_end_date: end_date, //
                        razorpay_coin_order_id: coin_order_id, //
                        razorpay_subscription_type: subscription_type, //
                        razorpay_cart_id: cart_id //
                    };


                    // var formData = $('#operational_top_up_new_form').serialize();
                    // formData.push({ name: '_token', value: "{{ csrf_token() }}" });
                    // formData.push({ name: 'razorpay_payment_id', value: response.razorpay_payment_id });
                    // formData.push({ name: 'razorpay_order_id', value: response.razorpay_order_id });
                    // formData.push({ name: 'razorpay_signature', value: response.razorpay_signature });
                    // console.log(response);
                    $.ajax({
                        url: "{{ route('payment.callback') }}", // Your callback route
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status = 'captured') {
                                // document.getElementById('loaderOverlay').style.display = 'none';
                                Swal.fire("Success", "Payment successfull ", "success").then(
                                    function() {
                                        document.getElementById('loaderOverlay').style.display = 'none';
                                        window.location = '{{ route('service_company.subscribe.newindex') }}'
                                        // window.location.reload(true);
                                    });

                            } else if (response.status = 'authorized') {
                                Swal.fire("Failed", "Payment has been authorized but not yet captured.",
                                    "error").then(
                                    function() {
                                        window.location.reload(true);
                                    });
                            } else if (response.status = 'failed') {
                                Swal.fire("Failed", "Payment failed. Please try again.", "error").then(
                                    function() {
                                        window.location.reload(true);
                                    });
                            } else if (response.status = 'refunded') {
                                Swal.fire("Success", "Payment has been refunded.", "success").then(
                                    function() {
                                        window.location.reload(true);
                                    });
                            } else if (response.status = 'partially_refunded') {
                                Swal.fire("success", "Payment has been partially refunded.", "success").then(
                                    function() {
                                        window.location.reload(true);
                                    });
                            } else if (response.status = 'created') {
                                Swal.fire("Failed", "Payment failed. Please try again.", "error").then(
                                    function() {
                                        window.location.reload(true);
                                    });
                            } else {
                                Swal.fire("Failed", "An error occurred. Please try again", "error").then(
                                    function() {
                                        window.location.reload(true);
                                    });
                            }
                           

                        },
                        error: function(error) {
                            console.log(error);
                            Swal.fire("Failed", "An error occurred. Please try again", "error");
                            setTimeout(() => {
                                window.location.reload(true);
                            }, 500);
                        }
                    });

                },
                "modal": {
                    "ondismiss": function() {
                        console.log("Payment modal closed by user.");
                        Swal.fire("Cancelled", "Payment process was cancelled", "info").then(
                            function() {
                                document.getElementById('loaderOverlay').style.display = 'none';
                            });
                    }
                },
                // "prefill": {
                //     "name": name,
                //     "email": email,
                // },
                "theme": {
                    "color": "#F37254"
                }

            };

            var rzp = new Razorpay(options);
            rzp.open(); // Open the Razorpay payment window
        }
    </script>
@endpush

@push('footer-script')
    <script>
        $(document).on('click', '#applyCouponBtn', function() {
            var code = $('#couponCodeInput').val().trim();
            $('#couponError').hide().text('');
            if (!code) {
                $('#couponError').text('Please enter a coupon code.').show();
                return;
            }
            $.ajax({
                type: 'POST',
                url: "{{ route('service_company.subscribe.applyCoupon') }}",
                data: { '_token': "{{ csrf_token() }}", 'code': code },
                success: function() {
                    window.location.reload();
                },
                error: function(xhr) {
                    var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not apply this coupon.';
                    $('#couponError').text(message).show();
                }
            });
        });

        $(document).on('click', '#removeCouponBtn', function() {
            $.ajax({
                type: 'POST',
                url: "{{ route('service_company.subscribe.removeCoupon') }}",
                data: { '_token': "{{ csrf_token() }}" },
                success: function() {
                    window.location.reload();
                }
            });
        });
    </script>
@endpush
