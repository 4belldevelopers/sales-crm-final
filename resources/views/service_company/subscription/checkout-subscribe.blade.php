@extends('layouts.service_company_layout')
@push('head-script')
    <style>
        /* ===== SECURE CHECKOUT PAGE ===== */

        /* Page header */
        .sc-page-header {
            display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px;
        }
        .sc-page-title { font-size: 26px; font-weight: 800; color: #1a1a2e; margin: 0 0 4px; display: flex; align-items: center; gap: 10px; }
        .sc-page-title i { color: #5b59e8; font-size: 22px; }
        .sc-page-sub { font-size: 13px; color: #9a9cb0; margin: 0; }
        .sc-secure-badge {
            display: flex; align-items: flex-start; gap: 8px; background: #f0fdf4;
            border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 18px;
            text-align: right;
        }
        .sc-secure-badge i { color: #22c55e; font-size: 16px; margin-top: 1px; }
        .sc-secure-badge-title { font-size: 13px; font-weight: 700; color: #15803d; }
        .sc-secure-badge-sub { font-size: 11px; color: #6b7280; margin-top: 1px; }

        /* Two-column layout */
        .sc-layout { display: grid; grid-template-columns: 1fr 360px; gap: 22px; align-items: start; }
        @media (max-width: 1024px) { .sc-layout { grid-template-columns: 1fr 320px; } }
        @media (max-width: 768px) { .sc-layout { grid-template-columns: 1fr; } }

        /* Cards */
        .sc-card {
            background: #fff; border-radius: 14px; border: 1px solid #e8e8f0;
            box-shadow: 0 1px 8px rgba(0,0,0,.06); margin-bottom: 16px; overflow: hidden;
        }
        .sc-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 22px; border-bottom: 1px solid #f0f0f8;
        }
        .sc-card-header-left { display: flex; align-items: center; gap: 12px; }
        .sc-card-icon {
            width: 38px; height: 38px; border-radius: 10px; background: #ede9fe;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .sc-card-icon i { color: #7c3aed; font-size: 15px; }
        .sc-card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .sc-card-body { padding: 20px 22px; }

        /* Edit button */
        .sc-btn-outline {
            font-size: 12px; font-weight: 600; color: #5b59e8; border: 1.5px solid #5b59e8;
            background: transparent; border-radius: 8px; padding: 7px 16px; cursor: pointer;
            transition: all .18s; white-space: nowrap;
        }
        .sc-btn-outline:hover { background: #5b59e8; color: #fff; }
        .sc-btn-danger-icon {
            width: 34px; height: 34px; background: #fff5f5; border: 1.5px solid #fecaca;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .18s;
        }
        .sc-btn-danger-icon:hover { background: #fecaca; }
        .sc-btn-danger-icon i { color: #ef4444; font-size: 13px; }

        /* Billing card content */
        .bill-company-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; }
        .bill-company-name { font-size: 15px; font-weight: 800; color: #1a1a2e; }
        .gst-badge {
            display: inline-flex; align-items: center; gap: 4px; background: #f0fdf4;
            border: 1px solid #bbf7d0; border-radius: 20px; padding: 3px 10px;
            font-size: 11px; font-weight: 700; color: #15803d;
        }
        .gst-badge i { font-size: 11px; }
        .bill-person { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 6px; }
        .bill-address { font-size: 13px; color: #6b7280; line-height: 1.7; }
        .bill-gstin { font-size: 12px; color: #6b7280; margin-top: 6px; }

        /* Order item */
        .order-item-row { display: flex; align-items: flex-start; gap: 16px; }
        .order-item-icon {
            width: 52px; height: 52px; border-radius: 14px; background: #ede9fe;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;
        }
        .order-item-icon i { color: #7c3aed; font-size: 22px; }
        .order-item-body { flex: 1; }
        .order-item-tag {
            display: inline-block; background: #f5f3ff; color: #7c3aed;
            font-size: 11px; font-weight: 700; border-radius: 20px; padding: 2px 12px; margin-bottom: 6px;
        }
        .order-item-name { font-size: 18px; font-weight: 800; color: #1a1a2e; margin-bottom: 8px; }
        .order-item-features { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
        .order-item-feature { font-size: 12px; color: #374151; display: flex; align-items: center; gap: 6px; }
        .order-item-feature i { color: #22c55e; font-size: 11px; }
        .order-item-stats { display: flex; gap: 32px; padding-top: 14px; border-top: 1px solid #f0f0f8; }
        .order-stat { display: flex; flex-direction: column; gap: 2px; }
        .order-stat-val { font-size: 16px; font-weight: 800; color: #1a1a2e; }
        .order-stat-label { font-size: 10px; font-weight: 600; color: #9a9cb0; text-transform: uppercase; letter-spacing: .4px; }

        /* Coupon section */
        .coupon-wrap { display: flex; align-items: stretch; gap: 16px; }
        .coupon-input-side { flex: 1; }
        .coupon-input-label { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; display: flex; align-items: center; gap: 6px; }
        .coupon-input-label i { color: #7c3aed; }
        .coupon-input-sub { font-size: 11px; color: #9a9cb0; margin-bottom: 10px; }
        .coupon-input-row { display: flex; gap: 8px; }
        .coupon-input {
            flex: 1; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 9px 14px;
            font-size: 13px; outline: none; transition: border-color .18s; background: #fafafa;
        }
        .coupon-input:focus { border-color: #5b59e8; background: #fff; }
        .btn-apply {
            background: #fff; color: #1a1a2e; border: 1.5px solid #e5e7eb; border-radius: 8px;
            padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s;
        }
        .btn-apply:hover { border-color: #5b59e8; color: #5b59e8; }
        .coupon-applied-side {
            min-width: 200px; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 10px;
            padding: 12px 16px; display: flex; flex-direction: column; justify-content: center;
        }
        .coupon-applied-name { font-size: 15px; font-weight: 800; color: #15803d; margin-bottom: 2px; display: flex; align-items: center; gap: 8px; }
        .coupon-applied-name i { color: #22c55e; font-size: 14px; }
        .coupon-applied-name .coupon-remove { margin-left: auto; background: none; border: none; color: #9a9cb0; cursor: pointer; font-size: 13px; padding: 0; }
        .coupon-applied-name .coupon-remove:hover { color: #ef4444; }
        .coupon-applied-desc { font-size: 12px; font-weight: 600; color: #15803d; }
        @media (max-width: 600px) { .coupon-wrap { flex-direction: column; } .coupon-applied-side { min-width: unset; } }

        /* ===== RIGHT SIDEBAR ===== */
        .sc-summary-title { font-size: 16px; font-weight: 800; color: #1a1a2e; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .sc-summary-icon { width: 36px; height: 36px; border-radius: 10px; background: #ede9fe; display: flex; align-items: center; justify-content: center; }
        .sc-summary-icon i { color: #7c3aed; font-size: 14px; }

        .summary-lines { margin-bottom: 0; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; }
        .summary-row + .summary-row { border-top: 1px solid #f5f5fa; }
        .summary-row-label { font-size: 13px; color: #6b7280; font-weight: 500; }
        .summary-row-val { font-size: 13px; color: #1a1a2e; font-weight: 600; }
        .summary-row-val.green { color: #22c55e; font-weight: 700; }
        .summary-divider { border: none; border-top: 1.5px solid #e5e7eb; margin: 8px 0 14px; }
        .summary-total-row { display: flex; justify-content: space-between; align-items: center; }
        .summary-total-label { font-size: 15px; font-weight: 800; color: #1a1a2e; }
        .summary-total-val { font-size: 22px; font-weight: 900; color: #5b59e8; }

        .you-save-bar {
            background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 10px;
            padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; margin: 16px 0;
        }
        .you-save-label { font-size: 12px; font-weight: 700; color: #15803d; }
        .you-save-val { font-size: 12px; font-weight: 800; color: #15803d; }

        .btn-proceed {
            width: 100%; background: linear-gradient(135deg, #5b59e8 0%, #7c3aed 100%);
            color: #fff; border: none; border-radius: 12px; padding: 15px 20px;
            font-size: 14px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity .18s; box-shadow: 0 4px 16px rgba(91,89,232,.35);
        }
        .btn-proceed:hover { opacity: .92; }
        .btn-proceed-note { text-align: center; font-size: 11px; color: #9a9cb0; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 5px; }

        /* Need Help sidebar box */
        .need-help-card { background: #fff; border-radius: 14px; border: 1px solid #e8e8f0; box-shadow: 0 1px 8px rgba(0,0,0,.06); padding: 18px 20px; }
        .need-help-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .need-help-icon { width: 38px; height: 38px; border-radius: 10px; background: #ede9fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .need-help-icon i { color: #7c3aed; font-size: 14px; }
        .need-help-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .need-help-sub { font-size: 12px; color: #9a9cb0; margin-top: 1px; }
        .btn-contact-support {
            width: 100%; border: 1.5px solid #5b59e8; color: #5b59e8; background: transparent;
            border-radius: 10px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
            transition: all .18s; margin-bottom: 14px;
        }
        .btn-contact-support:hover { background: #5b59e8; color: #fff; }
        .help-channels { display: flex; justify-content: space-around; }
        .help-channel { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #6b7280; font-weight: 600; }
        .help-channel i { color: #5b59e8; font-size: 13px; }

        /* Trust badges */
        .trust-badges { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
        .trust-badge { display: flex; align-items: center; gap: 10px; background: #fff; border: 1px solid #e8e8f0; border-radius: 12px; padding: 12px 16px; flex: 1; min-width: 140px; }
        .trust-badge-icon { width: 34px; height: 34px; border-radius: 8px; background: #ede9fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .trust-badge-icon i { color: #7c3aed; font-size: 13px; }
        .trust-badge-text strong { font-size: 12px; font-weight: 700; color: #5b59e8; display: block; }
        .trust-badge-text span { font-size: 10px; color: #9a9cb0; }

        /* Empty cart */
        .empty-cart-wrap { text-align: center; padding: 52px 30px; }
        .empty-cart-icon { width: 90px; height: 90px; border-radius: 50%; background: #ede9fe; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; position: relative; }
        .empty-cart-icon i { font-size: 36px; color: #7c3aed; }
        .empty-cart-badge { position: absolute; top: 8px; right: 6px; width: 22px; height: 22px; border-radius: 50%; background: #ef4444; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .empty-cart-title { font-size: 20px; font-weight: 800; color: #1a1a2e; margin-bottom: 8px; }
        .empty-cart-sub { font-size: 13px; color: #9a9cb0; margin-bottom: 22px; line-height: 1.7; }
        .btn-explore { background: #5b59e8; color: #fff; border: none; border-radius: 10px; padding: 11px 26px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .btn-explore:hover { opacity: .88; }
        .btn-view-cart { background: transparent; color: #5b59e8; border: 1.5px solid #5b59e8; border-radius: 10px; padding: 11px 26px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .18s; }
        .btn-view-cart:hover { background: #5b59e8; color: #fff; }
        .coins-upsell { border: 1.5px solid #e8e8f0; border-radius: 12px; padding: 14px 16px; margin-top: 20px; display: flex; align-items: flex-start; gap: 12px; text-align: left; }
        .coins-upsell-icon { width: 38px; height: 38px; border-radius: 10px; background: #ede9fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .coins-upsell-icon i { color: #7c3aed; font-size: 15px; }
        .coins-upsell-title { font-size: 13px; font-weight: 700; color: #5b59e8; margin-bottom: 2px; }
        .coins-upsell-sub { font-size: 12px; color: #6b7280; }
        .coins-upsell-link { font-size: 12px; color: #5b59e8; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px; }
        .empty-summary-feature { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; margin-bottom: 10px; }
        .empty-summary-feature i { color: #22c55e; }

        /* Loader */
        .loader-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,.88); z-index: 9999; justify-content: center; align-items: center; flex-direction: column; }
        .loader { border: 5px solid #f0f0f0; border-top: 5px solid #5b59e8; border-radius: 50%; width: 50px; height: 50px; animation: ld-spin 1s linear infinite; }
        .loader-text { margin-top: 14px; font-size: 14px; color: #374151; font-weight: 600; }
        @keyframes ld-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Billing drawer form */
        .form-group { margin-bottom: 14px; }
        .form-group label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px; display: block; }
    </style>
@endpush

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    $currency = GeneralMethod::convertINRToUSD();
    $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();

    $durationType = 0;
    $durationTypeText = "/Year";
    if (@$carts) {
        $number = $carts->duration;
        $months = 12;
        if ($number >= 365) {
            $years = floor($number / 365);
            $remainingDays = $number % 365;
            $durationType = "$years Year" . ($remainingDays > 0 ? ", $remainingDays day(s)" : "");
            $durationTypeText = "/Year";
            $months = floor($number / 30);
        } elseif ($number >= 30) {
            $months = floor($number / 30);
            $remainingDays = $number % 30;
            $durationType = "$months Month" . ($remainingDays > 0 ? ", $remainingDays day(s)" : "");
            $durationTypeText = "/Month";
        } elseif ($number >= 7) {
            $weeks = floor($number / 7);
            $remainingDays = $number % 7;
            $durationType = "$weeks Week" . ($remainingDays > 0 ? ", $remainingDays day(s)" : "");
            $durationTypeText = "/Week";
        } else {
            $durationType = "$number Days";
            $durationTypeText = "/Day";
        }

        $actualPrice = @$carts->price;
        $perMonthPrice = 0;
        $perMonthPrice = $actualPrice / $months;
        if (@$carts->discount) {
            $actualPrice = $carts->price / (1 - $carts->discount / 100);
            $perMonthPrice = $actualPrice / $months;
        }
    }
@endphp

<div id="loaderOverlay" class="loader-overlay">
    <div class="loader"></div>
    <div class="loader-text">Processing payment...</div>
</div>

@section('content')
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
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-8" id="kt_content">
        <div class="container-xxl" id="kt_content_container">

            {{-- Page header --}}
            <div class="sc-page-header">
                <div>
                    <h1 class="sc-page-title">
                        <i class="fa-solid fa-shield-halved"></i> Secure Checkout
                    </h1>
                    <p class="sc-page-sub">Review your order and complete your payment securely.</p>
                </div>
                <div class="sc-secure-badge d-none d-sm-flex">
                    <i class="fa-solid fa-lock"></i>
                    <div>
                        <div class="sc-secure-badge-title">100% Secure Checkout</div>
                        <div class="sc-secure-badge-sub">Your payment details are safe with us</div>
                    </div>
                </div>
            </div>

            @if(@$carts)

            {{-- ===== TWO-COLUMN LAYOUT ===== --}}
            <div class="sc-layout">

                {{-- LEFT COLUMN --}}
                <div>

                    {{-- Billing Information --}}
                    <div class="sc-card">
                        <div class="sc-card-header">
                            <div class="sc-card-header-left">
                                <div class="sc-card-icon"><i class="fa-solid fa-building"></i></div>
                                <span class="sc-card-title">Billing Information</span>
                            </div>
                            <button class="sc-btn-outline show-billing-address">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Edit Billing
                            </button>
                        </div>
                        <div class="sc-card-body">
                            <div class="bill-company-row">
                                <span class="bill-company-name">{{ @$company_name }}</span>
                                @if(@$gstin)
                                <span class="gst-badge"><i class="fa-solid fa-circle-check"></i> GST Verified</span>
                                @endif
                            </div>
                            <div class="bill-person" id="usernamebill">{{ @$user_name }}</div>
                            <div class="bill-address" id="billing_address">{{ @$locArr }}</div>
                            <div class="bill-gstin{{ @$gstin ? '' : ' d-none' }}" id="billing_gst">{{ @$gstin ? 'GSTIN: ' . @$gstin : '' }}</div>
                            <div class="bill-gstin{{ @$pan_no ? '' : ' d-none' }}" id="billing_pan">{{ @$pan_no ? 'PAN: ' . @$pan_no : '' }}</div>
                        </div>
                    </div>

                    {{-- Your Order --}}
                    <div class="sc-card">
                        <div class="sc-card-header">
                            <div class="sc-card-header-left">
                                <div class="sc-card-icon"><i class="fa-solid fa-tag"></i></div>
                                <div>
                                    <span class="sc-card-title">Your Order</span>
                                    <span style="background:#f5f3ff;color:#7c3aed;font-size:11px;font-weight:700;border-radius:20px;padding:2px 10px;margin-left:8px;">(1 item)</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="sc-btn-outline" onclick="window.location='{{ route('service_company.subscribe.newindex') }}'">
                                    Edit Order
                                </button>
                                <button class="sc-btn-danger-icon fa-trash" data-trash="{{ encrypt(@$carts->id) }}" title="Remove">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="sc-card-body">
                            <div class="order-item-row">
                                <div class="order-item-icon">
                                    <i class="fa-solid fa-coins"></i>
                                </div>
                                <div class="order-item-body">
                                    <span class="order-item-tag">Operational Coins</span>
                                    <div class="order-item-name">{{ @$carts->product_name }}</div>
                                    <div class="order-item-features">
                                        <div class="order-item-feature"><i class="fa-solid fa-circle-check"></i> {{ @$carts->credit }} Operational Coins</div>
                                        <div class="order-item-feature"><i class="fa-solid fa-circle-check"></i> Validity: {{ $durationType }}</div>
                                        <div class="order-item-feature"><i class="fa-solid fa-circle-check"></i> AI Resume Search &amp; Unlock</div>
                                        <div class="order-item-feature"><i class="fa-solid fa-circle-check"></i> Priority Email &amp; Chat Support</div>
                                    </div>
                                    <div class="order-item-stats">
                                        <div class="order-stat">
                                            <span class="order-stat-val">{{ @$carts->credit }}</span>
                                            <span class="order-stat-label">Coins</span>
                                        </div>
                                        <div class="order-stat">
                                            <span class="order-stat-val">{{ $durationType }}</span>
                                            <span class="order-stat-label">Validity</span>
                                        </div>
                                        <div class="order-stat">
                                            @php
                                                $displayPrice = @$carts->price;
                                                if (@$carts->discount) {
                                                    $displayPrice = $carts->price / (1 - $carts->discount / 100);
                                                }
                                                $currSym = @$carts->currency == 'INR' ? '₹' : '$';
                                            @endphp
                                            <span class="order-stat-val">{{ $currSym }}{{ number_format($displayPrice, 2) }}</span>
                                            <span class="order-stat-label">Price</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Coupon --}}
                    <div class="sc-card">
                        <div class="sc-card-body">
                            <div class="coupon-wrap">
                                <div class="coupon-input-side">
                                    <div class="coupon-input-label"><i class="fa-solid fa-tag"></i> Have Coupon?</div>
                                    <div class="coupon-input-sub">Apply a coupon code to get discount</div>
                                    <div class="coupon-input-row">
                                        <input type="text" class="coupon-input" placeholder="Enter coupon code" id="coupon_code_input">
                                        <button class="btn-apply" type="button">Apply</button>
                                    </div>
                                </div>

                                @if(@$carts->discount)
                                <div class="coupon-applied-side">
                                    <div class="coupon-applied-name">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Coupon Applied
                                        <button class="coupon-remove" title="Remove coupon"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="coupon-applied-desc">{{ @$carts->discount }}% Discount Applied</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Trust Badges --}}
                    <div class="trust-badges">
                        <div class="trust-badge">
                            <div class="trust-badge-icon"><i class="fa-solid fa-file-invoice"></i></div>
                            <div class="trust-badge-text"><strong>GST Invoice</strong><span>Automatic invoice with GST</span></div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge-icon"><i class="fa-solid fa-bolt"></i></div>
                            <div class="trust-badge-text"><strong>Instant Activation</strong><span>Coins will be added instantly</span></div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <div class="trust-badge-text"><strong>Secure Payment</strong><span>Razorpay secured checkout</span></div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge-icon"><i class="fa-solid fa-rotate-left"></i></div>
                            <div class="trust-badge-text"><strong>Money Back</strong><span>7-day refund policy</span></div>
                        </div>
                        <div class="trust-badge">
                            <div class="trust-badge-icon"><i class="fa-solid fa-headset"></i></div>
                            <div class="trust-badge-text"><strong>24x7 Support</strong><span>We're here to help you</span></div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN (sidebar) --}}
                <div>
                    {{-- Order Summary --}}
                    <div class="sc-card">
                        <div class="sc-card-body">
                            @php
                                $origPrice2  = @$carts->price;
                                if (@$carts->discount) {
                                    $origPrice2 = $carts->price / (1 - $carts->discount / 100);
                                }
                                $discountAmt = $origPrice2 - @$carts->price;
                                $gstAmt      = (@$carts->gst) ? (@$carts->price * @$carts->gst / 100) : 0;
                            @endphp

                            <div class="sc-summary-title">
                                <div class="sc-summary-icon"><i class="fa-solid fa-receipt"></i></div>
                                Order Summary
                            </div>

                            <div class="summary-lines">
                                <div class="summary-row">
                                    <span class="summary-row-label">Subtotal ({{ @$carts->product_name }})</span>
                                    <span class="summary-row-val">{{ $currSym }}{{ number_format($origPrice2, 2) }}</span>
                                </div>
                                @if(@$carts->discount)
                                <div class="summary-row">
                                    <span class="summary-row-label">Discount ({{ @$carts->discount }}%)</span>
                                    <span class="summary-row-val green">-{{ $currSym }}{{ number_format($discountAmt, 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-row-label">Price After Discount</span>
                                    <span class="summary-row-val">{{ $currSym }}{{ number_format(@$carts->price, 2) }}</span>
                                </div>
                                @endif
                                <div class="summary-row">
                                    <span class="summary-row-label">{{ @$carts->currency == 'INR' ? 'GST' : 'TAX' }} ({{ @$carts->gst ? @$carts->gst . '%' : '0%' }})</span>
                                    <span class="summary-row-val">{{ $currSym }}{{ number_format($gstAmt, 2) }}</span>
                                </div>
                            </div>

                            <hr class="summary-divider">

                            <div class="summary-total-row">
                                <span class="summary-total-label">Total Amount</span>
                                <span class="summary-total-val">{{ $currSym }}{{ @$carts->total_price }}</span>
                            </div>

                            @if(@$carts->discount)
                            <div class="you-save-bar">
                                <span class="you-save-label">You Save</span>
                                <span class="you-save-val">{{ $currSym }}{{ number_format($discountAmt, 2) }} ({{ @$carts->discount }}%) 🎉</span>
                            </div>
                            @else
                            <div style="height:16px;"></div>
                            @endif

                            <button class="btn-proceed {{ $carts_count ? 'cart_add' : 'cert_warning' }}"
                                    data-cart="{{ encrypt(@$carts->id) }}">
                                <i class="fa-solid fa-lock"></i> Proceed to Secure Payment
                            </button>
                            <div class="btn-proceed-note">
                                <i class="fa-solid fa-rotate" style="font-size:10px;"></i>
                                You will be redirected to Razorpay
                            </div>
                        </div>
                    </div>

                    {{-- Need Help --}}
                    <div class="need-help-card">
                        <div class="need-help-header">
                            <div class="need-help-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <div class="need-help-title">Need Help?</div>
                                <div class="need-help-sub">Our support team is here to assist you</div>
                            </div>
                        </div>
                        <button class="btn-contact-support">Contact Support</button>
                        <div class="help-channels">
                            <div class="help-channel"><i class="fa-solid fa-comment"></i> Live Chat</div>
                            <div class="help-channel"><i class="fa-solid fa-phone"></i> Call Us</div>
                            <div class="help-channel"><i class="fa-solid fa-envelope"></i> Email Us</div>
                        </div>
                    </div>
                </div>

            </div>

            @else

            {{-- ===== EMPTY CART ===== --}}
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="sc-card">
                        <div class="sc-card-body">
                            <div class="empty-cart-wrap">
                                <div class="empty-cart-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <div class="empty-cart-badge">!</div>
                                </div>
                                <div class="empty-cart-title">Your cart is empty</div>
                                <div class="empty-cart-sub">
                                    Looks like you haven't selected any plan or item yet.<br>
                                    Choose a subscription plan or add items to your cart to continue.
                                </div>
                                <div class="d-flex justify-content-center gap-3">
                                    <button class="btn-explore" onclick="window.location='{{ route('service_company.subscribe.newindex') }}'">Explore Plans</button>
                                    <button class="btn-view-cart" onclick="window.location='{{ route('service_company.subscribe.newindex') }}'">View My Cart</button>
                                </div>
                                <div class="d-flex align-items-center gap-3 my-4">
                                    <hr style="flex:1;border:none;border-top:1px solid #e8e8f0;margin:0;">
                                    <span style="font-size:12px;color:#9a9cb0;">or</span>
                                    <hr style="flex:1;border:none;border-top:1px solid #e8e8f0;margin:0;">
                                </div>
                                <div class="coins-upsell">
                                    <div class="coins-upsell-icon"><i class="fa-solid fa-coins"></i></div>
                                    <div>
                                        <div class="coins-upsell-title">Need more coins?</div>
                                        <div class="coins-upsell-sub">You can purchase additional coins or upgrade your plan anytime.</div>
                                        <a href="{{ route('service_company.subscribe.newindex') }}" class="coins-upsell-link">Buy Coins <i class="fa-solid fa-chevron-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sc-card">
                        <div class="sc-card-body">
                            <div class="sc-summary-title">
                                <div class="sc-summary-icon"><i class="fa-solid fa-receipt"></i></div>
                                Order Summary
                            </div>
                            <div class="text-center py-4">
                                <div style="width:70px;height:70px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                                    <i class="fa-solid fa-receipt" style="font-size:28px;color:#c4b5fd;"></i>
                                </div>
                                <div style="font-size:14px;font-weight:700;color:#1a1a2e;margin-bottom:4px;">No items selected</div>
                                <div style="font-size:12px;color:#9a9cb0;line-height:1.7;margin-bottom:16px;">Your order summary will appear here once you add items to your cart.</div>
                                <div class="text-start">
                                    <div class="empty-summary-feature"><i class="fa-solid fa-circle-check"></i> No hidden charges</div>
                                    <div class="empty-summary-feature"><i class="fa-solid fa-circle-check"></i> GST invoice included</div>
                                    <div class="empty-summary-feature"><i class="fa-solid fa-circle-check"></i> Instant activation</div>
                                    <div class="empty-summary-feature"><i class="fa-solid fa-circle-check"></i> 24x7 support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endif

        </div>
    </div>
    <!--end::Content-->

    {{-- Billing Details Drawer --}}
    <div id="kt_billing_address_toggle"></div>
    <div id="kt_activitiesApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_billing_address_toggle"
        data-kt-drawer-close="#kt_activitiesApplication_close">
        <div class="card shadow-none rounded-0 w-100" id="application_show_right_bar">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3">
                        <span class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                </g>
                            </svg>
                        </span> Billing Details
                    </a>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesApplication_close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            <div class="card card-custom mx-2">
                <div class="card-body py-0">
                    <div class="tab-content pt-5">
                        <form id="show-billing-form">
                            @csrf
                            <input class="form-control form-control-solid" value="{{ $company_id }}" type="hidden" id="company_id" name="company_id">
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
                                        <input class="form-control form-control-solid" value="{{ $firstName }}" type="text" id="first_name" name="first_name" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name</label>
                                        <input class="form-control form-control-solid" value="{{ $lastName }}" type="text" id="last_name" name="last_name" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input class="form-control form-control-solid" value="{{ $phone }}" type="tel" id="phone" name="phone" placeholder="Phone Number">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="building">Building / Society</label>
                                        <input class="form-control form-control-solid" value="{{ $building }}" type="text" id="building" name="building" placeholder="Building / Society">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="street">Street Name / Landmark</label>
                                        <input class="form-control form-control-solid" value="{{ @$locArr }}" type="text" id="street" name="street" placeholder="Street Name / Landmark">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <select class="form-control form-control-solid" id="state" name="state_id"></select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <select class="form-control form-control-solid" id="city" name="city"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input class="form-control form-control-solid" value="{{ $postal_code }}" type="text" id="postal_code" name="postal_code" placeholder="Postal Code">
                            </div>
                            <div class="form-group">
                                <label for="organization">Organization</label>
                                <input class="form-control form-control-solid" type="text" value="{{ @$company_name }}" id="organization" name="organization" placeholder="Organization" readonly>
                            </div>
                            <div class="form-group">
                                <label for="gstin" id="gstin_text">GSTIN</label>
                                <input class="form-control form-control-solid" value="{{ $gstin }}" type="text" id="gstin" name="gstin" placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="pan_no" id="pan_no_text">PAN No.</label>
                                <input class="form-control form-control-solid" value="{{ $pan_no }}" type="text" id="pan_no" name="pan_no" placeholder="">
                            </div>
                            <div class="text-center mt-4 mb-4">
                                <button type="submit" id="submitbtn" class="btn btn-primary w-100">
                                    <span class="indicator-label">Save Billing Details</span>
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

@endsection

@push('footer-script')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        var country_id = "{{ $countrie_id }}";
        var state_id   = "{{ $state_id }}";
        var city_id    = "{{ $city_id }}";

        setTimeout(() => { $('#country').val(country_id).change(); }, 200);

        $('#country').change(function() {
            const country       = $(this).val();
            const stateDropdown = $('#state');
            const cityDropdown  = $('#city');
            $('#gstin_text').text($('#country').val() == 101 ? 'GSTIN' : 'TAXIN');
            stateDropdown.empty().append('<option value="">Select State</option>').prop('disabled', true);
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);
            if (country) {
                $.ajax({
                    url: '{{ route('get.states') }}', method: 'GET', data: { country: country },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(state) {
                                stateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                            });
                            stateDropdown.prop('disabled', false);
                        }
                        $('#state').val(state_id).change();
                    },
                    error: function() { alert('Failed to fetch states. Please try again.'); }
                });
            }
        });

        $('#state').change(function() {
            const state      = $(this).val();
            const cityDropdown = $('#city');
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);
            if (state) {
                $.ajax({
                    url: '{{ route('get.cities') }}', method: 'GET', data: { state: state },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(city) {
                                cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                            });
                            cityDropdown.prop('disabled', false);
                        }
                        $('#city').val(city_id).change();
                    },
                    error: function() { alert('Failed to fetch cities. Please try again.'); }
                });
            }
        });

        $(document).on('click', '.show-billing-address', function() {
            $('#kt_billing_address_toggle').trigger('click');
            $('#gstin_text').text($('#country').val() == 101 ? 'GSTIN' : 'TAXIN');
        });

        $(document).on('submit', '#show-billing-form', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '{{ route('form.submit') }}', method: 'POST', data: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire("Success", "Billing Address updated successfully", "success").then(function() {
                        $('#usernamebill').text(response.usernamebill);
                        $('#billing_address').text(response.street);
                        $('#billing_gst').text(response.billing_gst);
                        $('#billing_pan').text(response.billing_pan);
                        $('#kt_activitiesApplication_close').click();
                    });
                },
                error: function(response) { console.log(response); alert('Form submission failed'); }
            });
        });

        // Remove cart item
        $(document).on('click', '.fa-trash', function() {
            let checked = $(this).data('trash');
            $.ajax({
                type: 'GET',
                url: "{{ route('service_company.subscribe.deletecheckout') }}",
                data: { '_method': 'GET', 'id': checked },
                success: function() { window.location.reload(true); }
            });
        });

        // Empty cart warning
        $(document).on('click', '.cert_warning', function() {
            Swal.fire("Warning", "Cart is empty", "warning");
        });

        // Proceed to Razorpay
        $(document).on('click', '.cart_add', function() {
            let checked = $(this).data('cart');
            document.getElementById('loaderOverlay').style.display = 'flex';
            $.ajax({
                type: 'GET',
                url: "{{ route('service_company.subscribe.cartcheckout') }}",
                data: { '_method': 'GET', 'id': checked },
                success: function(response) {
                    initiateRazorpayPayment(
                        response.orderId, response.amount, response.update_coins,
                        response.company_id, response.event_type, response.currency,
                        response.start_date, response.end_date, response.coin_order_id,
                        response.subscription_type, response.cart_id
                    );
                }
            });
        });

        function initiateRazorpayPayment(orderId, amount, update_coins, company_id, event_type, currency,
            start_date, end_date, coin_order_id, subscription_type, cart_id) {

            var options = {
                "key": "{{ $payment_settings->razorpay_key }}",
                "amount": amount,
                "currency": currency,
                "name": name,
                "description": "Test Transaction",
                "order_id": orderId,
                "handler": function(response) {
                    var formData = {
                        _token: "{{ csrf_token() }}",
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature,
                        razorpay_update_coins: update_coins,
                        razorpay_company_id: company_id,
                        razorpay_event_type: event_type,
                        razorpay_amount: amount,
                        razorpay_currency: currency,
                        razorpay_start_date: start_date,
                        razorpay_end_date: end_date,
                        razorpay_coin_order_id: coin_order_id,
                        razorpay_subscription_type: subscription_type,
                        razorpay_cart_id: cart_id
                    };
                    $.ajax({
                        url: "{{ route('payment.callback') }}",
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status = 'captured') {
                                Swal.fire("Success", "Payment successfull", "success").then(function() {
                                    document.getElementById('loaderOverlay').style.display = 'none';
                                    window.location = '{{ route('service_company.subscribe.newindex') }}';
                                });
                            } else if (response.status = 'authorized') {
                                Swal.fire("Failed", "Payment has been authorized but not yet captured.", "error").then(function() { window.location.reload(true); });
                            } else if (response.status = 'failed') {
                                Swal.fire("Failed", "Payment failed. Please try again.", "error").then(function() { window.location.reload(true); });
                            } else if (response.status = 'refunded') {
                                Swal.fire("Success", "Payment has been refunded.", "success").then(function() { window.location.reload(true); });
                            } else if (response.status = 'partially_refunded') {
                                Swal.fire("success", "Payment has been partially refunded.", "success").then(function() { window.location.reload(true); });
                            } else if (response.status = 'created') {
                                Swal.fire("Failed", "Payment failed. Please try again.", "error").then(function() { window.location.reload(true); });
                            } else {
                                Swal.fire("Failed", "An error occurred. Please try again", "error").then(function() { window.location.reload(true); });
                            }
                        },
                        error: function(error) {
                            console.log(error);
                            Swal.fire("Failed", "An error occurred. Please try again", "error");
                            setTimeout(() => { window.location.reload(true); }, 500);
                        }
                    });
                },
                "modal": {
                    "ondismiss": function() {
                        Swal.fire("Cancelled", "Payment process was cancelled", "info").then(function() {
                            document.getElementById('loaderOverlay').style.display = 'none';
                        });
                    }
                },
                "theme": { "color": "#5b59e8" }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        }
    </script>
@endpush
