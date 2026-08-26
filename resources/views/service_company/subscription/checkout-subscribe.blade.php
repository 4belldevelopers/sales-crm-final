@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        /* ===== SECURE CHECKOUT ===== */
        /* ===== PROFESSIONAL PAYMENT PROCESSING OVERLAY & MODAL ===== */
        .loader-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 999999;
            justify-content: center;
            align-items: center;
        }

        .loader-modal-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 36px 30px;
            width: 90%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            animation: loaderModalFadeIn 0.3s ease-out forwards;
        }

        @keyframes loaderModalFadeIn {
            from { opacity: 0; transform: scale(0.92) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .loader-spinner-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .loader-spinner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #6366f1;
            border-right: 4px solid #818cf8;
            border-radius: 50%;
            animation: co-spin 0.9s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
        }

        .loader-icon-center {
            font-size: 26px;
            color: #6366f1;
            animation: pulseIcon 1.8s ease-in-out infinite;
        }

        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.12); opacity: 1; color: #4f46e5; }
        }

        .loader-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px 0;
            font-family: 'Poppins', sans-serif;
            letter-spacing: -0.3px;
        }

        .loader-subtitle {
            font-size: 13px;
            line-height: 1.6;
            color: #64748b;
            margin: 0 0 20px 0;
            font-family: 'Poppins', sans-serif;
        }

        .loader-dots {
            display: flex;
            gap: 6px;
            margin-bottom: 24px;
        }

        .loader-dots span {
            width: 8px;
            height: 8px;
            background-color: #6366f1;
            border-radius: 50%;
            display: inline-block;
            animation: dotsBounce 1.4s infinite ease-in-out both;
        }

        .loader-dots span:nth-child(1) { animation-delay: -0.32s; }
        .loader-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes dotsBounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
            40% { transform: scale(1); opacity: 1; }
        }

        .loader-footer {
            width: 100%;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
            font-weight: 600;
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        @keyframes co-spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }

        /* Page header */
        .co-header h1 { font-size: 26px; font-weight: 800; color: #1a1a2e; margin: 0 0 4px; }
        .co-header h1 i { color: #5b59e8; font-size: 22px; }
        .co-secure-badge {
            background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
            font-size: 12px; font-weight: 700; padding: 8px 16px; border-radius: 10px;
            display: flex; align-items: flex-start; gap: 6px;
        }
        .co-secure-badge i { margin-top: 2px; }
        .co-secure-badge-sub { font-size: 11px; font-weight: 400; color: #6b7280; display: block; margin-top: 1px; }

        /* Cards */
        .co-card {
            background: #fff; border: 1px solid #e8e8f0; border-radius: 14px;
            padding: 20px 22px; margin-bottom: 18px;
            box-shadow: 0 1px 8px rgba(0,0,0,.06);
        }
        .co-card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .co-icon-circle {
            width: 36px; height: 36px; border-radius: 10px; background: #ede9fe; color: #7c3aed;
            display: inline-flex; align-items: center; justify-content: center;
            margin-right: 10px; font-size: 14px; flex-shrink: 0;
        }

        /* Billing */
        .co-gst-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 20px; padding: 3px 10px;
            font-size: 11px; font-weight: 700; color: #15803d;
        }
        .co-btn-outline-primary {
            font-size: 12px; font-weight: 600; color: #5b59e8; border: 1.5px solid #5b59e8;
            background: transparent; border-radius: 8px; padding: 7px 16px; cursor: pointer;
            transition: all .18s; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .co-btn-outline-primary:hover { background: #5b59e8; color: #fff; }

        /* Order item */
        .co-badge-type { background: #f5f3ff; color: #6d28d9; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-block; }
        .co-order-icon {
            width: 56px; height: 56px; border-radius: 14px; background: #ede9fe; color: #7c3aed;
            display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
        }
        .co-feature-item { font-size: 12px; color: #374151; padding: 3px 0; }
        .co-feature-item i { color: #22c55e; margin-right: 6px; }
        .co-stat-block { border-top: 1px solid #f0f0f8; padding-top: 12px; margin-top: 12px; display: flex; gap: 28px; }
        .co-stat-value { font-size: 17px; font-weight: 800; color: #1a1a2e; }
        .co-stat-label { font-size: 10px; color: #9a9cb0; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
        .co-trash-btn {
            width: 34px; height: 34px; background: #fff5f5; border: 1.5px solid #fecaca;
            border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: background .15s; flex-shrink: 0;
        }
        .co-trash-btn:hover { background: #fecaca; }
        .co-trash-btn i { color: #ef4444; font-size: 13px; pointer-events: none; }

        /* Coupon */
        .co-coupon-wrap { display: flex; gap: 14px; align-items: stretch; }
        .co-coupon-input-side { flex: 1; }
        .co-coupon-box { background: #f8f9fc; border-radius: 10px; padding: 14px 16px; height: 100%; }
        .co-coupon-applied {
            min-width: 190px; background: #f0fdf4; border: 1.5px solid #bbf7d0;
            border-radius: 10px; padding: 14px 16px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .co-coupon-applied-name { font-size: 15px; font-weight: 800; color: #15803d; display: flex; align-items: center; gap: 6px; }
        .co-coupon-applied-desc { font-size: 12px; font-weight: 600; color: #15803d; margin-top: 3px; }
        @media (max-width: 600px) { .co-coupon-wrap { flex-direction: column; } .co-coupon-applied { min-width: unset; } }

        /* Summary */
        .co-summary-row { display: flex; justify-content: space-between; font-size: 13px; padding: 9px 0; border-bottom: 1px solid #f5f5fa; }
        .co-summary-row:last-child { border-bottom: none; }
        .co-summary-total { font-size: 22px; font-weight: 900; color: #5b59e8; }
        .co-save-banner {
            background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 10px;
            padding: 10px 14px; font-size: 12px; color: #14532d; display: flex; justify-content: space-between; align-items: center;
        }
        .co-pay-btn {
            width: 100%; background: linear-gradient(135deg,#5b59e8,#7c3aed) !important;
            border: none; padding: 14px; font-weight: 700; border-radius: 12px; font-size: 14px;
            color: #fff; box-shadow: 0 4px 16px rgba(91,89,232,.3); transition: opacity .18s;
        }
        .co-pay-btn:hover { opacity: .9; color: #fff; }

        /* Need Help */
        .co-help-card {
            background: #fff; border: 1px solid #e8e8f0; border-radius: 14px;
            padding: 18px 20px; box-shadow: 0 1px 8px rgba(0,0,0,.06);
        }
        .co-btn-contact {
            width: 100%; border: 1.5px solid #5b59e8; color: #5b59e8; background: transparent;
            border-radius: 10px; padding: 9px; font-size: 13px; font-weight: 700; cursor: pointer;
            transition: all .18s; margin-bottom: 14px;
        }
        .co-btn-contact:hover { background: #5b59e8; color: #fff; }
        .co-help-channels { display: flex; justify-content: space-around; }
        .co-help-channel { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #6b7280; font-weight: 600; }
        .co-help-channel i { color: #5b59e8; font-size: 12px; }

        /* Feature strip */
        .co-feature-strip { padding-top: 14px; }
        .co-feature-strip-item { text-align: center; font-size: 11px; }
        .co-feature-strip-item i { font-size: 20px; color: #7c3aed; display: block; margin-bottom: 6px; }
        .co-feature-strip-item .fw-bold { font-size: 12px; font-weight: 700; color: #5b59e8; }
        .co-feature-strip-item .text-gray-500 { font-size: 10px; }

        /* Empty cart */
        .co-empty-state { text-align: center; padding: 50px 30px; }
        .co-empty-icon-wrap { position: relative; display: inline-block; margin-bottom: 20px; }
        .co-empty-icon-wrap > i {
            font-size: 46px; color: #7c3aed; background: #ede9fe; border-radius: 50%;
            width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;
        }
        .co-empty-icon-badge {
            position: absolute; top: 4px; right: 2px; width: 24px; height: 24px; border-radius: 50%;
            background: #ef4444; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px;
        }
        .co-empty-divider { display: flex; align-items: center; color: #9a9cb0; font-size: 11px; margin: 14px 0; }
        .co-empty-divider::before, .co-empty-divider::after { content: ''; flex: 1; border-bottom: 1px solid #e8e8f0; }
        .co-empty-divider span { padding: 0 12px; }
        .co-summary-empty-icon {
            width: 80px; height: 80px; border-radius: 50%; background: #f5f3ff; color: #c4b5fd;
            display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 14px;
        }

        /* Form */
        .form-group { margin-bottom: 14px; }
        .form-group label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px; display: block; }
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

@section('content')
<div id="loaderOverlay" class="loader-overlay" style="display: none;">
    <div class="loader-modal-card">
        <div class="loader-spinner-wrapper">
            <div class="loader-spinner"></div>
            <div class="loader-icon-center">
                <i class="fa fa-shield-alt"></i>
            </div>
        </div>
        <h3 class="loader-title">Processing Payment</h3>
        <p class="loader-subtitle">Please wait while we securely process your transaction.<br><span class="text-danger font-weight-semibold">Do not refresh or leave this page.</span></p>
        <div class="loader-dots">
            <span></span><span></span><span></span>
        </div>
        <div class="loader-footer">
            <i class="fa fa-lock me-1"></i> 256-bit SSL Encrypted Transaction
        </div>
    </div>
</div>
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-8" id="kt_content">
        <div class="container-xxl" id="kt_content_container">

            <div class="d-flex flex-wrap flex-stack mb-6">
                <div class="co-header d-flex flex-column">
                    <h1 class="d-flex align-items-center mb-1">
                        <i class="fa fa-shield-alt me-2"></i> Secure Checkout
                    </h1>
                    <div class="text-gray-500 fs-7">Review your order and complete your payment securely.</div>
                </div>
                <div class="d-none d-sm-flex flex-column align-items-end">
                    <span class="co-secure-badge">
                        <i class="fa fa-lock"></i>
                        <span>
                            100% Secure Checkout
                            <span class="co-secure-badge-sub">Your payment details are safe with us</span>
                        </span>
                    </span>
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
                            <a href="javascript:;" class="co-btn-outline-primary show-billing-address">
                                <i class="fa fa-pen-to-square"></i> Edit Billing
                            </a>
                        </div>
                        <div class="fs-7 text-gray-700">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                <span class="fw-bold fs-5 text-gray-900">{{ @$company_name }}</span>
                                @if(@$gstin)
                                    <span class="co-gst-badge"><i class="fa fa-circle-check"></i> GST Verified</span>
                                @endif
                            </div>
                            <div class="fw-bold text-gray-700 mb-1"><span id="usernamebill">{{ @$user_name }}</span></div>
                            <div class="text-gray-600"><span id="billing_address">{{ @$locArr }}</span></div>
                            @if(@$gstin)
                                <div class="text-gray-500 mt-1">GSTIN: <span id="billing_gst">{{ @$gstin }}</span></div>
                            @else
                                <span id="billing_gst" style="display:none;"></span>
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
                                    <span class="co-card-title">Your Order</span>
                                    <span class="ms-2" style="background:#f5f3ff;color:#7c3aed;font-size:11px;font-weight:700;border-radius:20px;padding:2px 10px;">(1 item)</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('service_company.subscribe.newindex') }}#tab=packages" class="co-btn-outline-primary">Edit Order</a>
                                    <button type="button" class="co-trash-btn" title="Remove">
                                        <i class="fa fa-trash" data-trash="{{ encrypt(@$carts->id) }}"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <span class="co-order-icon"><i class="fa fa-coins"></i></span>
                                <div class="flex-grow-1">
                                    <span class="co-badge-type mb-2">{{ ucfirst(@$carts->event_type) }} Coins</span>
                                    <div class="fw-bold fs-4 text-gray-900 mt-2 mb-2">{{ @$carts->product_name }}</div>
                                    <div class="co-feature-item"><i class="fa fa-check-circle"></i>{{ number_format(@$carts->credit) }} {{ ucfirst(@$carts->event_type) }} Coins</div>
                                    <div class="co-feature-item"><i class="fa fa-check-circle"></i>Validity: {{ @$carts->duration }} Days</div>
                                    <div class="co-feature-item"><i class="fa fa-check-circle"></i>AI Resume Search &amp; Unlock</div>
                                    <div class="co-feature-item"><i class="fa fa-check-circle"></i>Priority Email &amp; Chat Support</div>
                                    <div class="co-stat-block">
                                        <div class="me-4">
                                            <div class="co-stat-value">{{ number_format(@$carts->credit) }}</div>
                                            <div class="co-stat-label">Coins</div>
                                        </div>
                                        <div class="me-4">
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
                        </div>
                        <!--end::Your Order-->

                        <!--begin::Coupon-->
                        <div class="co-card">
                            <div class="d-flex align-items-center mb-4">
                                <span class="co-icon-circle"><i class="fa fa-ticket-alt"></i></span>
                                <div>
                                    <span class="co-card-title">Have Coupon?</span>
                                    <div class="text-gray-500 fs-8">Apply a coupon code to get discount</div>
                                </div>
                            </div>

                            <div class="co-coupon-wrap">
                                {{-- Input side (always visible) --}}
                                <div class="co-coupon-input-side">
                                    <div class="co-coupon-box">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="text" id="couponCodeInput" class="form-control form-control-solid"
                                                placeholder="Enter coupon code"
                                                value="{{ @$carts->coupon_code }}">
                                            <button type="button" id="applyCouponBtn" class="btn btn-outline-secondary" style="white-space:nowrap;font-weight:700;">Apply</button>
                                        </div>
                                        <div id="couponError" class="text-danger fs-8 mt-2" style="display:none;"></div>
                                    </div>
                                </div>

                                {{-- Applied state (visible when coupon applied) --}}
                                <div id="couponAppliedState" class="co-coupon-applied" style="{{ @$carts->coupon_code ? '' : 'display:none;' }}">
                                    <div class="co-coupon-applied-name">
                                        <i class="fa fa-circle-check"></i>
                                        <span id="couponAppliedCode">{{ @$carts->coupon_code }}</span>
                                        <a href="javascript:;" id="removeCouponBtn" class="ms-auto" style="color:#9a9cb0;font-size:16px;line-height:1;" title="Remove">&times;</a>
                                    </div>
                                    <div class="co-coupon-applied-desc">
                                        <span id="couponAppliedPct">{{ @$carts->coupon_code ? rtrim(rtrim(number_format(@$carts->discount, 2), '0'), '.') : '' }}</span>% Discount Applied
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            </div>
                        </div>
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
                            <div>
                                <div class="co-card-title">Need Help?</div>
                                <div class="text-gray-500 fs-8">Our support team is here to assist you</div>
                            </div>
                        </div>
                        <button type="button" class="co-btn-contact">Contact Support</button>
                        <div class="co-help-channels">
                            <div class="co-help-channel"><i class="fa fa-comment"></i> Live Chat</div>
                            <div class="co-help-channel"><i class="fa fa-phone"></i> Call Us</div>
                            <div class="co-help-channel"><i class="fa fa-envelope"></i> Email Us</div>
                        </div>
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
        $(document).on('click', '.co-trash-btn', function() {
            let checked = $(this).find('i').data('trash');
            var url = "{{ route('service_company.subscribe.deletecheckout') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: { '_method': 'GET', 'id': checked },
                success: function(response) { window.location.reload(true); }
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
