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

        .package-switch.salse-tab-plan { 
            background-color: rgb(17, 207, 40) !important;
            color: rgb(255, 255, 255) !important;
            font-weight: bold !important;
        }

        .package-switch.opra-tab-plan {
            background-color: rgb(45, 17, 207) !important;
            color: rgb(255, 255, 255) !important;
            font-weight: bold !important;
        }

        /* ===== Subscription & Wallet Center redesign ===== */
        .wallet-center-header * { box-sizing: border-box; }
        .wallet-center-header h1 { font-size: 16px; }
        .wallet-buy-coins-btn {
            background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe;
            font-weight: 600; font-size: 12px; padding: 8px 16px; border-radius: 8px;
        }
        .wallet-buy-coins-btn:hover { background: #ede9fe; color: #6d28d9; }

        .wallet-card {
            border: 1px solid #e4e6ef; border-radius: 14px; padding: 20px 22px;
            cursor: pointer; transition: border-color .15s, box-shadow .15s;
        }
        .wallet-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .wallet-card-operational { background: linear-gradient(180deg, #f5f3ff 0%, #fff 55%); }
        .wallet-card-sales { background: linear-gradient(180deg, #ecfdf3 0%, #fff 55%); }
        .wallet-card .wallet-card-check { color: #d0d3dc; font-size: 20px; }
        .wallet-card .wallet-card-icon {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-size: 16px;
        }
        .wallet-card-operational .wallet-card-icon { background: rgba(109,40,217,.15); color: #6d28d9; }
        .wallet-card-sales .wallet-card-icon { background: rgba(16,163,74,.15); color: #10a34a; }
        .wallet-card .badge.badge-light-success { font-size: 9px; padding: 3px 8px; }
        .wallet-card-balance { font-size: 26px; font-weight: 800; color: #181c32; margin-top: 14px; line-height: 1.2; }
        .wallet-card-balance-label { font-size: 11px; color: #8a8fa3; }
        .wallet-card-stats { border-top: 1px dashed rgba(0,0,0,.08); padding-top: 12px; margin-top: 14px; }
        .wallet-card-stat-label { font-size: 10px; color: #8a8fa3; }
        .wallet-active-pkg-row { font-size: 12px; color: #5b59e8; border-top: 1px dashed rgba(0,0,0,.08); padding-top: 8px; }
        /* Packages tab */
        .pkg-page-title { font-size: 22px; font-weight: 800; color: #1a1a2e; margin-bottom: 4px; }
        .pkg-page-sub { font-size: 13px; color: #8a8fa3; }
        .pkg-guidelines-link { color: #5b59e8; font-weight: 600; }
        .pkg-card { background: #fff; border: 1px solid #e2e2ef; border-radius: 14px; padding: 24px 20px 20px; position: relative; height: 100%; display: flex; flex-direction: column; box-shadow: 0 1px 6px rgba(0,0,0,.05); transition: box-shadow .2s, border-color .2s; }
        .pkg-card:hover { box-shadow: 0 4px 18px rgba(91,89,232,.12); border-color: #c4c2f8; }
        .pkg-card-current { border-color: #5b59e8 !important; box-shadow: 0 4px 20px rgba(91,89,232,.2) !important; }
        .pkg-card-current-sal { border-color: #10a34a !important; box-shadow: 0 4px 20px rgba(16,163,74,.2) !important; }
        .pkg-off-badge { position: absolute; top: -12px; right: 16px; background: #ff6b6b; color: #fff; font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 50px; }
        .pkg-card-name { font-size: 18px; font-weight: 800; color: #1a1a2e; margin-bottom: 2px; }
        .pkg-orig-price { font-size: 14px; color: #9ca3af; text-decoration: line-through; min-height: 20px; margin-top: 6px; }
        .pkg-price-row { display: flex; align-items: baseline; gap: 2px; margin: 4px 0 2px; }
        .pkg-currency { font-size: 16px; font-weight: 700; color: #5b59e8; }
        .pkg-price-val { font-size: 32px; font-weight: 800; color: #1a1a2e; line-height: 1; }
        .pkg-period { font-size: 12px; color: #8a8fa3; margin-left: 4px; }
        .pkg-price-custom { font-size: 22px; font-weight: 800; color: #5b59e8; margin: 8px 0 2px; }
        .pkg-coins-row { font-size: 14px; font-weight: 700; margin-bottom: 14px; }
        .pkg-btn { display: block; width: 100%; text-align: center; padding: 10px 0; border-radius: 8px; font-size: 13px; font-weight: 700; margin-top: 14px; cursor: pointer; text-decoration: none; transition: opacity .2s; }
        .pkg-btn:hover { opacity: .88; text-decoration: none; }
        .pkg-btn-upgrade { background: #1a1a2e; color: #fff; border: none; }
        .pkg-btn-upgrade-sal { background: #1a1a2e; color: #fff; border: none; }
        .pkg-btn-current { background: linear-gradient(135deg,#5b59e8,#7c3aed); color: #fff; border: none; cursor: default; }
        .pkg-btn-current-sal { background: linear-gradient(135deg,#16a34a,#10a34a); }
        .pkg-btn-contact { background: #10a34a; color: #fff; border: none; }
        .pkg-btn-contact-sal { background: #10a34a; color: #fff; border: none; }
        .pkg-divider { border-color: #f0f0f8; margin: 14px 0 10px; }
        .pkg-features { flex: 1; display: flex; flex-direction: column; gap: 0; }
        .pkg-feature-row { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #f5f5fb; font-size: 13px; color: #374151; }
        .pkg-feature-row:last-child { border-bottom: none; }
        .pkg-feature-val { font-weight: 600; color: #1a1a2e; }
        /* Current Plan card */
        .cp-card { background: #fff; border: 1px solid #e8e8f0; border-radius: 14px; padding: 20px 24px; box-shadow: 0 1px 8px rgba(0,0,0,.06); }
        .cp-card-header { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; border-bottom: 1px solid #f0f0f8; padding-bottom: 14px; }
        .cp-card-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .cp-card-icon-op { background: #ede9fe; color: #7c3aed; }
        .cp-card-icon-sal { background: #dcfce7; color: #16a34a; }
        .cp-card-title { font-size: 16px; font-weight: 700; color: #1a1a2e; }
        .cp-card-subtitle { font-size: 12px; color: #8a8fa3; }
        .cp-card-body { display: flex; flex-wrap: wrap; gap: 20px; }
        .cp-stat { display: flex; align-items: center; gap: 12px; min-width: 160px; flex: 1; }
        .cp-stat-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .cp-stat-icon-op { background: #ede9fe; color: #7c3aed; }
        .cp-stat-icon-sal { background: #dcfce7; color: #16a34a; }
        .cp-stat-label { font-size: 11px; color: #8a8fa3; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
        .cp-stat-value { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-top: 2px; }
        .cp-stat-value-highlight { color: #5b59e8; }
        .cp-stat-value-sal { color: #16a34a; }
        .cp-no-plan { font-size: 13px; color: #8a8fa3; padding: 8px 0; }

        /* .package-switch.opra-tab-plan / .salse-tab-plan are pre-existing
           global rules meant for small pill buttons (solid bg + white bold
           text). Reusing those class names on the wallet cards (needed so
           the existing JS toggle still recognizes them) drags that styling
           in too, so neutralize it before applying the wallet card's own
           tinted "active wallet" look. */
        .wallet-card.package-switch.opra-tab-plan,
        .wallet-card.package-switch.salse-tab-plan {
            color: #181c32 !important;
            font-weight: 400 !important;
        }
        .wallet-card.package-switch.opra-tab-plan { background: linear-gradient(180deg, #f5f3ff 0%, #fff 55%) !important; }
        .wallet-card.package-switch.salse-tab-plan { background: linear-gradient(180deg, #ecfdf3 0%, #fff 55%) !important; }
        .wallet-card.opra-tab-plan { border-color: #6d28d9; box-shadow: 0 0 0 1px #6d28d9; }
        .wallet-card.opra-tab-plan .wallet-card-check { color: #6d28d9; }
        .wallet-card.salse-tab-plan { border-color: #10a34a; box-shadow: 0 0 0 1px #10a34a; }
        .wallet-card.salse-tab-plan .wallet-card-check { color: #10a34a; }

        .wallet-ring {
            width: 64px; height: 64px; border-radius: 50%; position: relative;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .wallet-ring::before {
            content: ''; position: absolute; inset: 7px; background: #fff; border-radius: 50%;
        }
        .wallet-ring-label { position: relative; z-index: 1; text-align: center; line-height: 1.15; }
        .wallet-ring-pct { font-size: 12px; font-weight: 800; color: #181c32; display: block; }
        .wallet-ring-sub { font-size: 8px; color: #8a8fa3; display: block; }

        .wallet-info-banner {
            background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 10px;
            padding: 10px 16px; font-size: 12px; color: #4c1d95;
        }
        .wallet-center-header.wallet-mode-sales .wallet-info-banner {
            background: #ecfdf3; border-color: #b7ebc6; color: #14532d;
        }

        .wallet-tab-nav {
            list-style: none; display: flex; flex-wrap: wrap; gap: 4px; margin: 0;
            padding: 0; border-bottom: 1px solid #e4e6ef;
        }
        .wallet-tab-link {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 14px;
            font-size: 12px; font-weight: 600; color: #7e8299; border-bottom: 2px solid transparent;
            text-decoration: none; cursor: pointer;
        }
        .wallet-tab-link:hover { color: #4c1d95; text-decoration: none; }
        /* Named wt-active, not active — this codebase has a pre-existing
           global ".active { color: white !important; }" rule that would
           otherwise make the active tab's text invisible. */
        .wallet-tab-link.wt-active { color: #6d28d9; border-bottom-color: #6d28d9; }
        .wallet-center-header.wallet-mode-sales .wallet-tab-link.wt-active { color: #10a34a; border-bottom-color: #10a34a; }

        .wallet-tab-pane { padding: 0 5px; }

        .wallet-stat-card {
            border: 1px solid #e4e6ef; border-radius: 10px; padding: 14px 16px; background: #fafaff;
        }
        .wallet-stat-value { font-size: 18px; font-weight: 800; color: #181c32; }
        .wallet-stat-label { font-size: 11px; color: #8a8fa3; margin-top: 2px; }

        .wallet-pkg-card {
            border: 1px solid #e4e6ef; border-radius: 14px; padding: 22px 20px; height: 100%;
            position: relative; background: #fff;
        }
        .wallet-pkg-card.is-popular { border-color: #6d28d9; border-width: 2px; }
        .wallet-pkg-badge {
            position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: #6d28d9; color: #fff; font-size: 9px; font-weight: 700;
            padding: 3px 12px; border-radius: 20px; white-space: nowrap;
        }
        .wallet-pkg-icon { font-size: 20px; margin-bottom: 8px; }
        .wallet-pkg-name { font-weight: 700; font-size: 13px; color: #181c32; }
        .wallet-pkg-desc { font-size: 10px; color: #8a8fa3; margin-bottom: 10px; }
        .wallet-pkg-price { font-size: 20px; font-weight: 800; color: #181c32; }
        .wallet-pkg-price small { font-size: 11px; font-weight: 500; color: #8a8fa3; }
        .wallet-pkg-coins { font-size: 14px; font-weight: 700; margin: 10px 0 14px; }
        .wallet-pkg-features { list-style: none; padding: 0; margin: 0 0 16px; }
        .wallet-pkg-features li { font-size: 11px; color: #4a4f63; padding: 4px 0; display: flex; align-items: center; gap: 6px; }
        .wallet-pkg-features li i { color: #10a34a; font-size: 11px; }
        .wallet-pkg-btn { width: 100%; text-align: center; border-radius: 8px; padding: 9px; font-size: 12px; font-weight: 600; display: block; }
        .wallet-pkg-btn-outline { color: #6d28d9; border: 1px solid #6d28d9; background: #fff; }
        .wallet-pkg-btn-outline:hover { background: #f5f3ff; color: #6d28d9; }
        .wallet-pkg-btn-solid { color: #fff; background: #6d28d9; border: 1px solid #6d28d9; }
        .wallet-pkg-btn-solid:hover { background: #5b21b6; color: #fff; }
        .wallet-pkg-btn-success { color: #fff; background: #10a34a; border: 1px solid #10a34a; }
        .wallet-pkg-btn-success:hover { background: #0d8a3d; color: #fff; }
        .wallet-pkg-btn-current-plan { color: #fff; background: linear-gradient(135deg,#5b59e8,#7c3aed); border: none; cursor: default; }
        .wallet-pkg-btn-current-plan-sal { background: linear-gradient(135deg,#16a34a,#10a34a); }
        .is-current-plan { border-color: #5b59e8 !important; box-shadow: 0 4px 20px rgba(91,89,232,.18); }
        .is-current-plan-sal { border-color: #10a34a !important; box-shadow: 0 4px 20px rgba(16,163,74,.18); }
        .wallet-pkg-badge-current { background: linear-gradient(135deg,#5b59e8,#7c3aed) !important; }

        .wallet-feature-strip { border-top: 1px solid #e4e6ef; border-bottom: 1px solid #e4e6ef; padding: 16px 0; margin: 24px 0; }
        .wallet-feature-strip-item { display: flex; align-items: center; gap: 10px; font-size: 11px; }
        .wallet-feature-strip-item i { font-size: 18px; color: #6d28d9; }
        .wallet-feature-strip-item .t { font-weight: 700; color: #181c32; display: block; }
        .wallet-feature-strip-item .d { color: #8a8fa3; }

        .wallet-why-card, .wallet-custom-card { border-radius: 14px; padding: 22px; height: 100%; }
        .wallet-why-card { background: #f8f7ff; }
        .wallet-custom-card { background: #faf5ff; }

        .wallet-earn-banner {
            background: linear-gradient(90deg, #6d28d9, #8b5cf6); border-radius: 14px;
            color: #fff; padding: 20px 24px;
        }
        .wallet-earn-banner .fs-8 { color: rgba(255,255,255,.8); }

        .wallet-package-preview {
            border: 1px solid #e4e6ef; border-radius: 10px; padding: 16px; height: 100%;
        }

        .wallet-coming-soon {
            text-align: center; padding: 60px 10px; color: #8a8fa3;
        }
        .wallet-coming-soon i { font-size: 34px; display: block; margin-bottom: 10px; color: #c4c8d6; }

        @media (max-width: 1366px) {
            .wallet-card-balance { font-size: 22px; }
            .wallet-stat-value { font-size: 16px; }
        }
        @media (max-width: 767px) {
            .wallet-tab-nav { overflow-x: auto; flex-wrap: nowrap; }
            .wallet-tab-link { white-space: nowrap; }
        }
    </style>
@endpush

@php
    use App\Helper\GeneralMethod;

    $currency = GeneralMethod::convertINRToUSD();
   
@endphp

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
                    <span class="text-gray-600" id="">&nbsp;</span>
                </div>
                <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
            </div>
            <div class="d-flex align-items-center py-1"
                style="height: 50px; width: 50px; position: absolute; border-radius: 35px; right: 14%;">
                <a href="{{ route('service_company.subscribe.checkoutSubscribe') }}">
                    <i class="fa fa-cart-plus" style="font-size: 25px;">‌</i>
                    <span style="position: absolute; right: -22px; font-size: 17px;">Cart</span>
                    <span
                        style="text-align: center; position: absolute; top: 0px; right: 16px; background: #f9bd13; height: 22px; width: 22px; color: white; font-weight: 700; border-radius: 100%;">
                        {{ $carts_count }} <span>
                </a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">
                <!--begin::Operations/Sales-->
                <div class="card mt-2" id="kt_pricing">
                    <!--begin::Wallet Center header-->
                    <div class="wallet-center-header px-5 px-xl-9 pt-8">
                        <div class="d-flex flex-wrap flex-stack mb-6">
                            <div class="d-flex flex-column mb-3 mb-md-0">
                                <h1 class="d-flex align-items-center text-gray-900 fw-bold mb-1" style="font-size:16px;">
                                    Subscription &amp; Wallet Center
                                </h1>
                                <div class="text-gray-500 fs-8">Manage all your wallets, packages, coins and transactions in one place.</div>
                            </div>
                        </div>

                        <!--begin::Wallet cards-->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="wallet-card wallet-card-operational package-switch opra-tab-plan wallet-card-active"
                                    data-kt-plan="operational">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="wallet-card-icon"><i class="fa fa-cog"></i></span>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-6 text-gray-900">Operational Wallet</div>
                                                @if(!empty($current_coins_operational))
                                                    <span class="badge badge-light-success fs-9">ACTIVE</span>
                                                @else
                                                    <span class="badge badge-light-danger fs-9">NO ACTIVE PLAN</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="wallet-card-check"><i class="fa fa-check-circle"></i></span>
                                    </div>
                                    @if(!empty($current_coins_operational))
                                    <div class="wallet-active-pkg-row mt-2">
                                        <i class="fa fa-tag me-1"></i>
                                        <span class="fw-bold">{{ $current_coins_operational[0]['title'] ?? '—' }}</span>
                                        @if(!empty($current_coins_operational[0]['end_date']))
                                            <span class="text-muted ms-2 fs-9">· Expires {{ date('d M Y', strtotime($current_coins_operational[0]['end_date'])) }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-end justify-content-between mt-3">
                                        <div>
                                            <div class="wallet-card-balance">{{ number_format($remain_coins) }}</div>
                                            <div class="wallet-card-balance-label">Coins Available</div>
                                        </div>
                                        <div class="wallet-ring" style="background: conic-gradient(#6d28d9 {{ $op_used_pct * 3.6 }}deg, #e4e6ef 0deg);">
                                            <span class="wallet-ring-label"><span class="wallet-ring-pct">{{ $op_used_pct }}%</span><span class="wallet-ring-sub">Used</span></span>
                                        </div>
                                    </div>
                                    <div class="row text-center wallet-card-stats">
                                        <div class="col-4">
                                            <div class="fw-bold fs-6">{{ number_format($op_coins_used) }}</div>
                                            <div class="wallet-card-stat-label">Used Coins</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-6">{{ number_format($op_total_purchased) }}</div>
                                            <div class="wallet-card-stat-label">Purchased</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-6">{{ $op_days_left }}</div>
                                            <div class="wallet-card-stat-label">Days Validity</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wallet-card wallet-card-sales package-switch"
                                    data-kt-plan="sales">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="wallet-card-icon"><i class="fa fa-briefcase"></i></span>
                                            <div class="ms-3">
                                                <div class="fw-bold fs-6 text-gray-900">Sales Wallet</div>
                                                @if(!empty($current_coins_sales))
                                                    <span class="badge badge-light-success fs-9">ACTIVE</span>
                                                @else
                                                    <span class="badge badge-light-danger fs-9">NO ACTIVE PLAN</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="wallet-card-check"><i class="fa fa-check-circle"></i></span>
                                    </div>
                                    @if(!empty($current_coins_sales))
                                    <div class="wallet-active-pkg-row mt-2">
                                        <i class="fa fa-tag me-1"></i>
                                        <span class="fw-bold">{{ $current_coins_sales[0]['title'] ?? '—' }}</span>
                                        @if(!empty($current_coins_sales[0]['end_date']))
                                            <span class="text-muted ms-2 fs-9">· Expires {{ date('d M Y', strtotime($current_coins_sales[0]['end_date'])) }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-end justify-content-between mt-3">
                                        <div>
                                            <div class="wallet-card-balance">{{ number_format($remain_coins_sales) }}</div>
                                            <div class="wallet-card-balance-label">Coins Available</div>
                                        </div>
                                        <div class="wallet-ring" style="background: conic-gradient(#10a34a {{ $sal_used_pct * 3.6 }}deg, #e4e6ef 0deg);">
                                            <span class="wallet-ring-label"><span class="wallet-ring-pct">{{ $sal_used_pct }}%</span><span class="wallet-ring-sub">Used</span></span>
                                        </div>
                                    </div>
                                    <div class="row text-center wallet-card-stats">
                                        <div class="col-4">
                                            <div class="fw-bold fs-6">{{ number_format($sal_coins_used) }}</div>
                                            <div class="wallet-card-stat-label">Used Coins</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-6">{{ number_format($sal_total_purchased) }}</div>
                                            <div class="wallet-card-stat-label">Purchased</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold fs-6">{{ $sal_days_left }}</div>
                                            <div class="wallet-card-stat-label">Days Validity</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Wallet cards-->

                        <!--begin::Info banner-->
                        <div class="wallet-info-banner mb-5">
                            <i class="fa fa-shield-alt me-2"></i>
                            <span>Your wallets are secure and private. All transactions and usage are kept separate for each wallet.</span>
                        </div>
                        <!--end::Info banner-->

                        <!--begin::Tab nav-->
                        <ul class="wallet-tab-nav">
                            <li><a href="javascript:;" class="wallet-tab-link wt-active" data-tab="overview" onclick="switchWalletCenterTab('overview');return false;"><i class="fa fa-th-large"></i> Overview</a></li>
                        </ul>
                        <!--end::Tab nav-->
                    </div>
                    <!--end::Wallet Center header-->
                    <!--begin::Card body-->
                    <div class="card-body position-relative px-0 py-0" id="kt_activitiesApplication_body">
                        <div id="wallet-pane-overview" class="wallet-tab-pane">
                        <div class="row g-5 g-xl-10 mb-5 mb-xl-10 px-5">
                            <div class="col-xl-12 col-lg-12">

                                <!--begin::Current Plan card - Operational-->
                                <div class="cp-card operational-packages mb-5">
                                    <div class="cp-card-header">
                                        <span class="cp-card-icon cp-card-icon-op"><i class="fa fa-cog"></i></span>
                                        <div>
                                            <div class="cp-card-title">Current Plan</div>
                                            <div class="cp-card-subtitle">Operational Wallet</div>
                                        </div>
                                    </div>
                                    @if(!empty($current_coins_operational))
                                    <div class="cp-card-body">
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-op"><i class="fa fa-layer-group"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Plan Name</div>
                                                <div class="cp-stat-value">{{ $current_coins_operational[0]['title'] }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-op"><i class="fa fa-calendar-alt"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Expiration Date</div>
                                                <div class="cp-stat-value">{{ !empty($current_coins_operational[0]['end_date']) ? date('d M Y', strtotime($current_coins_operational[0]['end_date'])) : '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-op"><i class="fa fa-coins"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Credit Coins</div>
                                                <div class="cp-stat-value">{{ number_format($current_coins_operational[0]['actual_coins']) }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-op"><i class="fa fa-wallet"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Available Coins</div>
                                                <div class="cp-stat-value cp-stat-value-highlight">{{ number_format($current_coins_operational[0]['coins']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="cp-no-plan">
                                        <i class="fa fa-info-circle me-2"></i> No active operational plan found.
                                        <a href="javascript:;" class="ms-2 fw-bold" onclick="switchWalletCenterTab('packages');return false;">Browse Packages &rarr;</a>
                                    </div>
                                    @endif
                                </div>
                                <!--end::Current Plan card - Operational-->

                                <!--begin::Current Plan card - Sales-->
                                <div class="cp-card salse-packages mb-5" style="display:none;">
                                    <div class="cp-card-header">
                                        <span class="cp-card-icon cp-card-icon-sal"><i class="fa fa-briefcase"></i></span>
                                        <div>
                                            <div class="cp-card-title">Current Plan</div>
                                            <div class="cp-card-subtitle">Sales Wallet</div>
                                        </div>
                                    </div>
                                    @if(!empty($current_coins_sales))
                                    <div class="cp-card-body">
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-sal"><i class="fa fa-layer-group"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Plan Name</div>
                                                <div class="cp-stat-value">{{ $current_coins_sales[0]['title'] }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-sal"><i class="fa fa-calendar-alt"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Expiration Date</div>
                                                <div class="cp-stat-value">{{ !empty($current_coins_sales[0]['end_date']) ? date('d M Y', strtotime($current_coins_sales[0]['end_date'])) : '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-sal"><i class="fa fa-coins"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Credit Coins</div>
                                                <div class="cp-stat-value">{{ number_format($current_coins_sales[0]['actual_coins']) }}</div>
                                            </div>
                                        </div>
                                        <div class="cp-stat">
                                            <div class="cp-stat-icon cp-stat-icon-sal"><i class="fa fa-wallet"></i></div>
                                            <div>
                                                <div class="cp-stat-label">Available Coins</div>
                                                <div class="cp-stat-value cp-stat-value-highlight cp-stat-value-sal">{{ number_format($current_coins_sales[0]['coins']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="cp-no-plan">
                                        <i class="fa fa-info-circle me-2"></i> No active sales plan found.
                                        <a href="javascript:;" class="ms-2 fw-bold" onclick="switchWalletCenterTab('packages');return false;">Browse Packages &rarr;</a>
                                    </div>
                                    @endif
                                </div>
                                <!--end::Current Plan card - Sales-->

                                <!--begin::Overview extras - Operational-->
                                <div class="row g-4 mt-1 operational-packages">
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value">{{ number_format($op_total_purchased) }}</div>
                                            <div class="wallet-stat-label">Total Coins Purchased</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value">{{ number_format($remain_coins) }}</div>
                                            <div class="wallet-stat-label">Coins Available</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value">{{ number_format($op_coins_used) }}</div>
                                            <div class="wallet-stat-label">Coins Used</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($op_total_spend) }}</div>
                                            <div class="wallet-stat-label">Total Spend</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 operational-packages">
                                    <div class="d-flex flex-stack mb-4">
                                        <h4 class="fs-4 fw-bold text-gray-900 mb-0">Recommended Packages for Operational Wallet</h4>
                                        <a href="javascript:;" class="fs-8 fw-semibold" onclick="switchWalletCenterTab('packages');return false;">View all packages &rarr;</a>
                                    </div>
                                    <div class="row g-4">
                                        @php
                                            $opIcons = ['fa-star text-warning', 'fa-crown text-primary', 'fa-gem text-info'];
                                            $activeOpPackageId = !empty($current_coins_operational) ? ($current_coins_operational[0]['coin_package_id'] ?? null) : null;
                                        @endphp
                                        @foreach ($operational_package as $i => $opr_preview)
                                            @php $isCurrentOp = ($activeOpPackageId && $opr_preview['id'] == $activeOpPackageId); @endphp
                                            <div class="col-md-3">
                                                <div class="wallet-pkg-card {{ $isCurrentOp ? 'is-current-plan' : ($opr_preview['custome_pck'] != 2 && $i == 1 ? 'is-popular' : '') }}">
                                                    @if($isCurrentOp)
                                                        <span class="wallet-pkg-badge wallet-pkg-badge-current">CURRENT PLAN</span>
                                                    @elseif($opr_preview['custome_pck'] != 2 && $i == 1)
                                                        <span class="wallet-pkg-badge">MOST POPULAR</span>
                                                    @endif
                                                    <div class="wallet-pkg-icon">
                                                        <i class="fa {{ $opr_preview['custome_pck'] == 2 ? 'fa-building text-success' : ($opIcons[$i % 3] ?? 'fa-star text-warning') }}"></i>
                                                    </div>
                                                    <div class="wallet-pkg-name">{{ $opr_preview['title'] }}</div>
                                                    <div class="wallet-pkg-desc">{{ $opr_preview['custome_pck'] == 2 ? 'Best for Enterprises' : ($opr_preview['days'] ? $opr_preview['days'] . ' Days' : 'Custom') }}</div>
                                                    @if($opr_preview['custome_pck'] == 2)
                                                        <div class="wallet-pkg-price text-success">Custom Pricing</div>
                                                        <div class="wallet-pkg-coins text-success">Unlimited Coins</div>
                                                    @else
                                                        <div class="wallet-pkg-price">
                                                            <i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"></i>
                                                            {{ $currency == 'INR' ? $opr_preview['price'] : $opr_preview['price_usd'] }}
                                                        </div>
                                                        <div class="wallet-pkg-coins text-primary">{{ $opr_preview['coins'] }} Coins</div>
                                                    @endif
                                                    <ul class="wallet-pkg-features">
                                                        @foreach (collect($opr_preview['features'])->where('status', true)->take(3) as $feat)
                                                            <li><i class="fa fa-check"></i> {{ $feat->title }}</li>
                                                        @endforeach
                                                    </ul>
                                                    @if($opr_preview['custome_pck'] == 2)
                                                        <a href="javascript:;" class="wallet-pkg-btn wallet-pkg-btn-success" onclick="switchWalletCenterTab('packages');return false;">Contact Sales</a>
                                                    @elseif($isCurrentOp)
                                                        <a href="javascript:;" class="wallet-pkg-btn wallet-pkg-btn-current-plan mb-2">
                                                            <i class="fa fa-check-circle me-1"></i>Current Plan
                                                        </a>
                                                        <a href="javascript:;" data-id="{{ encrypt($opr_preview['id']) }}"
                                                            class="wallet-pkg-btn wallet-pkg-btn-outline upgradeselect">
                                                            <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                                                        </a>
                                                    @else
                                                        <a href="javascript:;" data-id="{{ encrypt($opr_preview['id']) }}"
                                                            class="wallet-pkg-btn upgradeselect {{ $i == 1 ? 'wallet-pkg-btn-solid' : 'wallet-pkg-btn-outline' }}">
                                                            <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="wallet-feature-strip row g-4 operational-packages">
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-bolt"></i><span><span class="t">Instant Activation</span><span class="d">Coins added instantly</span></span></div>
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-briefcase"></i><span><span class="t">Secure Payments</span><span class="d">100% secure gateway</span></span></div>
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-file-invoice"></i><span><span class="t">GST Invoices</span><span class="d">Invoice with every purchase</span></span></div>
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-headset"></i><span><span class="t">24x7 Support</span><span class="d">We're always here</span></span></div>
                                </div>

                                <div class="row g-4 mb-5 operational-packages">
                                    <div class="col-md-6">
                                        <div class="wallet-why-card">
                                            <h5 class="fw-bold mb-4">Why Choose Jero Coins?</h5>
                                            <div class="row g-3">
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-search"></i><span><span class="t">AI Resume Search</span><span class="d">Unlock premium profiles</span></span></div>
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-download"></i><span><span class="t">Resume Downloads</span><span class="d">Download unlimited resumes</span></span></div>
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-magic"></i><span><span class="t">AI Matching</span><span class="d">Advanced AI-powered matching</span></span></div>
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-tasks"></i><span><span class="t">Job Operations</span><span class="d">Post &amp; manage unlimited jobs</span></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="wallet-custom-card">
                                            <h5 class="fw-bold mb-2">Need a Custom Plan?</h5>
                                            <div class="text-gray-600 fs-8 mb-3">Our team will help you find the perfect plan for your business needs.</div>
                                            <ul class="wallet-pkg-features">
                                                <li><i class="fa fa-check"></i> Custom Coin Packages</li>
                                                <li><i class="fa fa-check"></i> Volume Discounts</li>
                                                <li><i class="fa fa-check"></i> Dedicated Support</li>
                                            </ul>
                                            <a href="javascript:;" class="btn btn-primary btn-sm" onclick="switchWalletCenterTab('packages');return false;">Contact Sales Team</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="wallet-earn-banner d-flex flex-stack operational-packages mb-5">
                                    <div>
                                        <div class="fw-bold">Earn More. Save More.</div>
                                        <div class="fs-8">Get exclusive discounts and bonus coins on higher packages.</div>
                                    </div>
                                    <a href="javascript:;" class="btn btn-light btn-sm" onclick="switchWalletCenterTab('packages');return false;">Buy Coins Now</a>
                                </div>
                                <!--end::Overview extras - Operational-->

                                <!--begin::Overview extras - Sales-->
                                <div class="row g-4 mt-1 salse-packages" style="display:none">
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value">{{ number_format($sal_total_purchased) }}</div>
                                            <div class="wallet-stat-label">Total Coins Purchased</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value">{{ number_format($remain_coins_sales) }}</div>
                                            <div class="wallet-stat-label">Coins Available</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value">{{ number_format($sal_coins_used) }}</div>
                                            <div class="wallet-stat-label">Coins Used</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="wallet-stat-card">
                                            <div class="wallet-stat-value"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"></i> {{ number_format($sal_total_spend) }}</div>
                                            <div class="wallet-stat-label">Total Spend</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 salse-packages" style="display:none">
                                    <div class="d-flex flex-stack mb-4">
                                        <h4 class="fs-4 fw-bold text-gray-900 mb-0">Recommended Packages for Sales Wallet</h4>
                                        <a href="javascript:;" class="fs-8 fw-semibold" onclick="switchWalletCenterTab('packages');return false;">View all packages &rarr;</a>
                                    </div>
                                    <div class="row g-4">
                                        @php
                                            $salIcons = ['fa-star text-warning', 'fa-crown text-success', 'fa-gem text-info'];
                                            $activeSlPackageId = !empty($current_coins_sales) ? ($current_coins_sales[0]['coin_package_id'] ?? null) : null;
                                        @endphp
                                        @foreach ($sales_package as $i => $sales_preview)
                                            @php $isCurrentSal = ($activeSlPackageId && $sales_preview['id'] == $activeSlPackageId); @endphp
                                            <div class="col-md-3">
                                                <div class="wallet-pkg-card {{ $isCurrentSal ? 'is-current-plan is-current-plan-sal' : ($sales_preview['custome_pck'] != 2 && $i == 1 ? 'is-popular' : '') }}" style="{{ !$isCurrentSal && $sales_preview['custome_pck'] != 2 && $i == 1 ? 'border-color:#10a34a;' : '' }}">
                                                    @if($isCurrentSal)
                                                        <span class="wallet-pkg-badge wallet-pkg-badge-current">CURRENT PLAN</span>
                                                    @elseif($sales_preview['custome_pck'] != 2 && $i == 1)
                                                        <span class="wallet-pkg-badge" style="background:#10a34a;">MOST POPULAR</span>
                                                    @endif
                                                    <div class="wallet-pkg-icon">
                                                        <i class="fa {{ $sales_preview['custome_pck'] == 2 ? 'fa-building text-success' : ($salIcons[$i % 3] ?? 'fa-star text-warning') }}"></i>
                                                    </div>
                                                    <div class="wallet-pkg-name">{{ $sales_preview['title'] }}</div>
                                                    <div class="wallet-pkg-desc">{{ $sales_preview['custome_pck'] == 2 ? 'Best for Large Agencies' : ($sales_preview['days'] ? $sales_preview['days'] . ' Days' : 'Custom') }}</div>
                                                    @if($sales_preview['custome_pck'] == 2)
                                                        <div class="wallet-pkg-price text-success">Custom Pricing</div>
                                                        <div class="wallet-pkg-coins text-success">Unlimited Coins</div>
                                                    @else
                                                        <div class="wallet-pkg-price">
                                                            <i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"></i>
                                                            {{ $currency == 'INR' ? $sales_preview['price'] : $sales_preview['price_usd'] }}
                                                        </div>
                                                        <div class="wallet-pkg-coins text-success">{{ $sales_preview['coins'] }} Coins</div>
                                                    @endif
                                                    <ul class="wallet-pkg-features">
                                                        @foreach (collect($sales_preview['features'])->where('status', true)->take(3) as $feat)
                                                            <li><i class="fa fa-check"></i> {{ $feat->title }}</li>
                                                        @endforeach
                                                    </ul>
                                                    @if($sales_preview['custome_pck'] == 2)
                                                        <a href="javascript:;" class="wallet-pkg-btn wallet-pkg-btn-success" onclick="switchWalletCenterTab('packages');return false;">Contact Sales</a>
                                                    @elseif($isCurrentSal)
                                                        <a href="javascript:;" class="wallet-pkg-btn wallet-pkg-btn-current-plan wallet-pkg-btn-current-plan-sal mb-2">
                                                            <i class="fa fa-check-circle me-1"></i>Current Plan
                                                        </a>
                                                        <a href="javascript:;" data-id="{{ encrypt($sales_preview['id']) }}"
                                                            class="wallet-pkg-btn upgradeselect"
                                                            style="color:#10a34a;border:1px solid #10a34a;background:#fff;">
                                                            <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                                                        </a>
                                                    @else
                                                        <a href="javascript:;" data-id="{{ encrypt($sales_preview['id']) }}"
                                                            class="wallet-pkg-btn upgradeselect {{ $i == 1 ? 'wallet-pkg-btn-success' : '' }}"
                                                            style="{{ $i != 1 ? 'color:#10a34a;border:1px solid #10a34a;background:#fff;' : '' }}">
                                                            <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="wallet-feature-strip row g-4 salse-packages" style="display:none">
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-bolt" style="color:#10a34a;"></i><span><span class="t">Instant Activation</span><span class="d">Coins added instantly</span></span></div>
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-briefcase" style="color:#10a34a;"></i><span><span class="t">Secure Payments</span><span class="d">100% secure gateway</span></span></div>
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-file-invoice" style="color:#10a34a;"></i><span><span class="t">GST Invoices</span><span class="d">Invoice with every purchase</span></span></div>
                                    <div class="col-md-3 wallet-feature-strip-item"><i class="fa fa-headset" style="color:#10a34a;"></i><span><span class="t">24x7 Support</span><span class="d">We're always here</span></span></div>
                                </div>

                                <div class="row g-4 mb-5 salse-packages" style="display:none">
                                    <div class="col-md-6">
                                        <div class="wallet-why-card" style="background:#f2fdf6;">
                                            <h5 class="fw-bold mb-4">Why Choose Sales Coins?</h5>
                                            <div class="row g-3">
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-chart-line" style="color:#10a34a;"></i><span><span class="t">Increase Visibility</span><span class="d">Get higher visibility in marketplace</span></span></div>
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-bullhorn" style="color:#10a34a;"></i><span><span class="t">Generate More Leads</span><span class="d">Unlock premium leads</span></span></div>
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-handshake" style="color:#10a34a;"></i><span><span class="t">Grow Your Agency</span><span class="d">Build trust and win clients</span></span></div>
                                                <div class="col-6 wallet-feature-strip-item"><i class="fa fa-chart-bar" style="color:#10a34a;"></i><span><span class="t">Advanced Insights</span><span class="d">Track performance and optimize</span></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="wallet-custom-card" style="background:#f2fdf6;">
                                            <h5 class="fw-bold mb-2">Need a Custom Plan?</h5>
                                            <div class="text-gray-600 fs-8 mb-3">Our team will help you find the perfect plan for your agency needs.</div>
                                            <ul class="wallet-pkg-features">
                                                <li><i class="fa fa-check"></i> Custom Coin Packages</li>
                                                <li><i class="fa fa-check"></i> Volume Discounts</li>
                                                <li><i class="fa fa-check"></i> Dedicated Support</li>
                                            </ul>
                                            <a href="javascript:;" class="btn btn-success btn-sm" onclick="switchWalletCenterTab('packages');return false;">Contact Sales Team</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="wallet-earn-banner d-flex flex-stack salse-packages mb-5" style="display:none;background:linear-gradient(90deg,#0d8a3d,#10a34a);">
                                    <div>
                                        <div class="fw-bold">Earn More. Save More.</div>
                                        <div class="fs-8">Get exclusive discounts and bonus coins on higher packages.</div>
                                    </div>
                                    <a href="javascript:;" class="btn btn-light btn-sm" onclick="switchWalletCenterTab('packages');return false;">Buy Sales Coins Now</a>
                                </div>
                                <!--end::Overview extras - Sales-->

                                </div>
                                <!--end::wallet-pane-overview-->

                                <div id="wallet-pane-packages" class="wallet-tab-pane" style="display:none">

                                <!--begin::Packages - Operational-->
                                <div id="pkg-pane-operational" class="operational-packages px-2 py-4">
                                    <div class="pkg-page-header text-center mb-8">
                                        <h2 class="pkg-page-title">Upgrade a Plan</h2>
                                        <p class="pkg-page-sub">If you need more info, please check <a href="{{ url('payment-terms-and-conditions') }}" class="pkg-guidelines-link" target="_blank">Pricing Guidelines</a>.</p>
                                    </div>
                                    <div class="row g-4">
                                        @php
                                            $opCurPkgId = null;
                                            if (!empty($current_coins_operational) && is_array($current_coins_operational)) {
                                                $opCurPkgId = reset($current_coins_operational)['coin_package_id'] ?? null;
                                            }
                                        @endphp
                                        @if ($operational_package)
                                        @foreach ($operational_package as $opr_val)
                                            @php
                                                $isOpCurrent = ($opCurPkgId && $opr_val['id'] == $opCurPkgId);
                                                $months = 12;
                                                $number = $opr_val['days'] ?? 0;
                                                if ($number >= 365)      { $months = floor($number / 30); }
                                                elseif ($number >= 30)   { $months = floor($number / 30); }
                                                $perMonthCoins = $months > 0 ? round($opr_val['coins'] / $months) : $opr_val['coins'];

                                                // Displayed price & period
                                                if ($opr_val['visibility_show'] == 1) {
                                                    $dispPrice    = $currency == 'INR' ? $opr_val['price'] : $opr_val['price_usd'];
                                                    $origPrice    = $opr_val['discount'] > 0 ? round($dispPrice / (1 - $opr_val['discount'] / 100)) : null;
                                                    $period       = 'Year';
                                                } elseif ($opr_val['visibility_show'] == 2) {
                                                    $dispPrice    = $currency == 'INR' ? $opr_val['total_price_monthly'] : $opr_val['total_price_usd_monthly'];
                                                    $origPrice    = $opr_val['discount'] > 0 ? round($dispPrice / (1 - $opr_val['discount'] / 100)) : null;
                                                    $period       = 'Month';
                                                } else {
                                                    $dispPrice    = $currency == 'INR' ? $opr_val['total_price_days'] : $opr_val['total_price_usd_days'];
                                                    $origPrice    = $opr_val['discount'] > 0 ? round($dispPrice / (1 - $opr_val['discount'] / 100)) : null;
                                                    $period       = 'Day';
                                                }
                                                $currIcon = $currency == 'INR' ? '₹' : '$';
                                            @endphp
                                            <div class="col-xl-3 col-md-6">
                                                <div class="pkg-card {{ $isOpCurrent ? 'pkg-card-current' : '' }}">
                                                    @if($opr_val['discount'] > 0)
                                                        <span class="pkg-off-badge">{{ $opr_val['discount'] }}% Off</span>
                                                    @endif
                                                    <div class="pkg-card-name">{{ $opr_val['title'] }}</div>
                                                    @if($opr_val['custome_pck'] == 2)
                                                        <div class="pkg-price-custom">Custom Pricing</div>
                                                        <div class="pkg-coins-row text-success">Unlimited Coins</div>
                                                    @else
                                                        @if($origPrice)
                                                            <div class="pkg-orig-price">{{ $currIcon }}{{ number_format($origPrice) }}</div>
                                                        @else
                                                            <div class="pkg-orig-price">&nbsp;</div>
                                                        @endif
                                                        <div class="pkg-price-row">
                                                            <span class="pkg-currency">{{ $currIcon }}</span><span class="pkg-price-val">{{ number_format($dispPrice) }}</span>
                                                            <span class="pkg-period">/ {{ $period }}</span>
                                                        </div>
                                                        <div class="pkg-coins-row text-primary">{{ number_format($opr_val['coins']) }} Coins</div>
                                                    @endif

                                                    {{-- Button --}}
                                                    @if($opr_val['custome_pck'] == 2)
                                                        @if($isOpCurrent)
                                                            <a href="javascript:;" class="pkg-btn pkg-btn-current"><i class="fa fa-check-circle me-1"></i>Current Plan</a>
                                                        @else
                                                            <a href="javascript:;" class="pkg-btn pkg-btn-contact">Contact Sales</a>
                                                        @endif
                                                    @else
                                                        @if($isOpCurrent)
                                                            <a href="javascript:;" class="pkg-btn pkg-btn-current"><i class="fa fa-check-circle me-1"></i>Current Plan</a>
                                                            <a href="javascript:;" data-id="{{ encrypt($opr_val['id']) }}" class="pkg-btn pkg-btn-upgrade upgradeselect mt-2">Upgrade Now</a>
                                                        @else
                                                            <a href="javascript:;" data-id="{{ encrypt($opr_val['id']) }}" class="pkg-btn pkg-btn-upgrade upgradeselect">Upgrade Now</a>
                                                        @endif
                                                    @endif

                                                    <hr class="pkg-divider">

                                                    {{-- Features --}}
                                                    <div class="pkg-features">
                                                        <div class="pkg-feature-row">
                                                            <span>Coins</span><span class="pkg-feature-val">{{ number_format($perMonthCoins) }}</span>
                                                        </div>
                                                        <div class="pkg-feature-row">
                                                            <span>Validity</span><span class="pkg-feature-val">{{ $opr_val['days'] ? $opr_val['days'] . ' Days' : 'Custom' }}</span>
                                                        </div>
                                                        @foreach ($opr_val['features'] as $features_val)
                                                        <div class="pkg-feature-row">
                                                            <span class="{{ $features_val->status ? '' : 'text-muted' }}">{{ $features_val->title }}</span>
                                                            @if($features_val->status)
                                                                <i class="fa fa-check text-success"></i>
                                                            @else
                                                                <i class="fa fa-times text-muted"></i>
                                                            @endif
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!--end::Packages - Operational-->

                                <!--begin::Packages - Sales-->
                                <div id="pkg-pane-sales" class="salse-packages px-2 py-4" style="display:none">
                                    <div class="pkg-page-header text-center mb-8">
                                        <h2 class="pkg-page-title">Upgrade a Plan</h2>
                                        <p class="pkg-page-sub">If you need more info, please check <a href="{{ url('payment-terms-and-conditions') }}" class="pkg-guidelines-link" target="_blank">Pricing Guidelines</a>.</p>
                                    </div>
                                    <div class="row g-4">
                                        @php
                                            $salCurPkgId = null;
                                            if (!empty($current_coins_sales) && is_array($current_coins_sales)) {
                                                $salCurPkgId = reset($current_coins_sales)['coin_package_id'] ?? null;
                                            }
                                        @endphp
                                        @if ($sales_package)
                                        @foreach ($sales_package as $sales_val)
                                            @php
                                                $isSalCurrent = ($salCurPkgId && $sales_val['id'] == $salCurPkgId);
                                                $months = 12;
                                                $number = $sales_val['days'] ?? 0;
                                                if ($number >= 365)      { $months = floor($number / 30); }
                                                elseif ($number >= 30)   { $months = floor($number / 30); }
                                                $perMonthCoins = $months > 0 ? round($sales_val['coins'] / $months) : $sales_val['coins'];

                                                if ($sales_val['visibility_show'] == 1) {
                                                    $dispPrice = $currency == 'INR' ? $sales_val['price'] : $sales_val['price_usd'];
                                                    $origPrice = $sales_val['discount'] > 0 ? round($dispPrice / (1 - $sales_val['discount'] / 100)) : null;
                                                    $period    = 'Year';
                                                } elseif ($sales_val['visibility_show'] == 2) {
                                                    $dispPrice = $currency == 'INR' ? $sales_val['total_price_monthly'] : $sales_val['total_price_usd_monthly'];
                                                    $origPrice = $sales_val['discount'] > 0 ? round($dispPrice / (1 - $sales_val['discount'] / 100)) : null;
                                                    $period    = 'Month';
                                                } else {
                                                    $dispPrice = $currency == 'INR' ? $sales_val['total_price_days'] : $sales_val['total_price_usd_days'];
                                                    $origPrice = $sales_val['discount'] > 0 ? round($dispPrice / (1 - $sales_val['discount'] / 100)) : null;
                                                    $period    = 'Day';
                                                }
                                            @endphp
                                            <div class="col-xl-3 col-md-6">
                                                <div class="pkg-card pkg-card-sales {{ $isSalCurrent ? 'pkg-card-current pkg-card-current-sal' : '' }}">
                                                    @if($sales_val['discount'] > 0)
                                                        <span class="pkg-off-badge">{{ $sales_val['discount'] }}% Off</span>
                                                    @endif
                                                    <div class="pkg-card-name">{{ $sales_val['title'] }}</div>
                                                    @if($sales_val['custome_pck'] == 2)
                                                        <div class="pkg-price-custom text-success">Custom Pricing</div>
                                                        <div class="pkg-coins-row text-success">Unlimited Coins</div>
                                                    @else
                                                        @if($origPrice)
                                                            <div class="pkg-orig-price">{{ $currIcon }}{{ number_format($origPrice) }}</div>
                                                        @else
                                                            <div class="pkg-orig-price">&nbsp;</div>
                                                        @endif
                                                        <div class="pkg-price-row">
                                                            <span class="pkg-currency text-success">{{ $currIcon }}</span><span class="pkg-price-val text-success">{{ number_format($dispPrice) }}</span>
                                                            <span class="pkg-period">/ {{ $period }}</span>
                                                        </div>
                                                        <div class="pkg-coins-row text-success">{{ number_format($sales_val['coins']) }} Coins</div>
                                                    @endif

                                                    @if($sales_val['custome_pck'] == 2)
                                                        @if($isSalCurrent)
                                                            <a href="javascript:;" class="pkg-btn pkg-btn-current pkg-btn-current-sal"><i class="fa fa-check-circle me-1"></i>Current Plan</a>
                                                        @else
                                                            <a href="javascript:;" class="pkg-btn pkg-btn-contact-sal">Contact Sales</a>
                                                        @endif
                                                    @else
                                                        @if($isSalCurrent)
                                                            <a href="javascript:;" class="pkg-btn pkg-btn-current pkg-btn-current-sal"><i class="fa fa-check-circle me-1"></i>Current Plan</a>
                                                            <a href="javascript:;" data-id="{{ encrypt($sales_val['id']) }}" class="pkg-btn pkg-btn-upgrade-sal upgradeselect mt-2">Upgrade Now</a>
                                                        @else
                                                            <a href="javascript:;" data-id="{{ encrypt($sales_val['id']) }}" class="pkg-btn pkg-btn-upgrade-sal upgradeselect">Upgrade Now</a>
                                                        @endif
                                                    @endif

                                                    <hr class="pkg-divider">

                                                    <div class="pkg-features">
                                                        <div class="pkg-feature-row">
                                                            <span>Sales Coins</span><span class="pkg-feature-val">{{ number_format($perMonthCoins) }}</span>
                                                        </div>
                                                        <div class="pkg-feature-row">
                                                            <span>Validity</span><span class="pkg-feature-val">{{ $sales_val['days'] ? $sales_val['days'] . ' Days' : 'Custom' }}</span>
                                                        </div>
                                                        @foreach ($sales_val['features'] as $features_val)
                                                        <div class="pkg-feature-row">
                                                            <span class="{{ $features_val->status ? '' : 'text-muted' }}">{{ $features_val->title }}</span>
                                                            @if($features_val->status)
                                                                <i class="fa fa-check text-success"></i>
                                                            @else
                                                                <i class="fa fa-times text-muted"></i>
                                                            @endif
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!--end::Packages - Sales-->


                           




                            <div id="{{ $current_coins_operational[0]['custome_pck'] == 2 ? '' : 'top_up_coin_packages_op' }}"
                                class="position-relative bg-light pt-2 px-0 {{ $current_coins_operational[0]['custome_pck'] == 2 ? '' : 'operational-packages' }} {{ $current_coins_operational[0]['custome_pck'] == 2 ? 'd-none' : '' }}"
                                data-kt-scroll="true" data-kt-scroll-height="auto"
                                data-kt-scroll-wrappers="#kt_activities_body"
                                data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
                                data-kt-scroll-offset="5px">
                                <div class="card mb-5 mb-xl-5 mx-2 px-0">
                                    <div class="card-body">
                                        <!--<div class="py-2 bg-light px-0"></div>-->
                                        <div class="row px-5 py-5 d-none">
                                            <div class="card-header border-0 px-0 pb-5">
                                                <!--begin::Card title-->
                                                <div class="card-title mb-2 px-5">
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-0"> <span
                                                                class="text-gray-900 fs-2 fw-bolder me-2">Coins Top-up</span></div>
                                                    </div>
                                                </div>
                                                <!--end::Card title-->
                                            </div>
                                            <div class="row">
                                                <div class="">

                                                    <div class="">
                                                        <label class="form-label required">Choose an amount</label>
                                                        <div class="row py-5 mb-3">
                                                            @if (@$top_up_coin_packages_op)
                                                                @foreach ($top_up_coin_packages_op as $key => $val)
                                                                    <div class="col-lg-2"
                                                                        style="margin-bottom: 6px;width:20%!important">
                                                                        <input type="radio"
                                                                            class="btn-check {{ $val->custome_chk ? 'topupcustom' : '' }} app_int_amount_op"
                                                                            name="app_int_amount_op"
                                                                            value="{{ encrypt($val->id) }}"
                                                                            id="app_int_amount_op{{ $val->id }}"
                                                                            {{ $key == 0 ? 'checked' : '' }}>
                                                                        <label for="app_int_amount_op{{ $val->id }}"
                                                                            class="btn btn-light-primary w-100"
                                                                            data-kt-modal-bidding="option">
                                                                            @if ($val->custome_chk == 0)
                                                                                <div class="fw-semibold"
                                                                                    style="position: relative">
                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="position: absolute; top: -24px;">
                                                                                        <span
                                                                                            class="fs-4">{{ $val->coins }}
                                                                                            Coins</span>
                                                                                    </div>
                                                                                    <div
                                                                                        style="position: relative; margin-top: 22px;">

                                                                                        <div class="fs-6 fw-bold"
                                                                                            style="float: left;">
                                                                                            <span class="mb-2"><i
                                                                                                    class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"
                                                                                                    aria-hidden="true"></i></span>{{ $currency == 'INR' ? $val->price : $val->price_usd }}
                                                                                        </div>

                                                                                        <div class="fs-6 fw-bold"
                                                                                            style="float: right;">
                                                                                            <span class="mb-2"><i
                                                                                                    class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"
                                                                                                    aria-hidden="true"></i></span>{{ $currency == 'INR' ? $val->per_coin_rate : $val->per_coin_rate_usd }}/Coins
                                                                                        </div>

                                                                                    </div>

                                                                                </div>
                                                                            @else
                                                                                <div
                                                                                    style="position: relative; margin-top: 22px;">
                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="position: absolute; top: -24px;">
                                                                                        <span class="fs-4">Custom</span>
                                                                                    </div>

                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="float: left;">
                                                                                        <span class="mb-2"></span>Contact
                                                                                    </div>

                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="float: right;">
                                                                                        <span
                                                                                            class="mb-2"></span>{{ $currency == 'INR' ? $val->per_coin_rate : $val->per_coin_rate_usd }}Coins
                                                                                    </div>

                                                                                </div>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="border-0 pb-0" style="">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <h5 class="card-title">Validity will be added on the package
                                                            </h5>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="m-0">
                                                                <a href="javascript:;"
                                                                    class="btn btn-color-primary bg-primary bg-opacity-15 top-up-cart-op bg-hover-opacity-25 fw-semibold w-100">
                                                                    Top-up
                                                                </a>
                                                            </div>
                                                            <!--<button type="button" class="btn btn-light-danger py-2">Top-up</button>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-2 bg-light px-0"></div>
                                        <!--Begin top up history-->
                                        <!--end top up history-->
                                    </div>
                                </div>
                            </div>

                            <div id="{{ $current_coins_sales[0]['custome_pck'] == 2 ? '' : 'top_up_coin_packages_sal' }}"
                                class="position-relative bg-light pt-2 px-0 {{ $current_coins_sales[0]['custome_pck'] == 2 ? '' : 'salse-packages' }} {{ $current_coins_sales[0]['custome_pck'] == 2 ? 'd-none' : '' }}"
                                data-kt-scroll="true" data-kt-scroll-height="auto"
                                data-kt-scroll-wrappers="#kt_activities_body"
                                data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
                                data-kt-scroll-offset="5px" style="display:none">
                                <div class="card mb-5 mb-xl-5 mx-2 px-0">
                                    <div class="card-body">
                                        <!--<div class="py-2 bg-light px-0"></div>-->
                                        <div class="row px-5 py-5 d-none">
                                            <div class="card-header border-0 px-0 pb-5">
                                                <!--begin::Card title-->
                                                <div class="card-title mb-2 px-5">
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-0"> <span
                                                                class="text-gray-900 fs-2 fw-bolder me-2">Coins Top-up</span></div>
                                                    </div>
                                                </div>
                                                <!--end::Card title-->
                                            </div>
                                            <div class="row">
                                                <div class="">

                                                    <div class="">
                                                        <label class="form-label required">Choose an amount</label>
                                                        <div class="row py-5 mb-3">
                                                            @if (@$top_up_coin_packages_sal)
                                                                @foreach ($top_up_coin_packages_sal as $key => $val)
                                                                    <div class="col-lg-2"
                                                                        style="margin-bottom: 6px;width:20%!important">
                                                                        <input type="radio"
                                                                            class="btn-check {{ $val->custome_chk ? 'topupcustom' : '' }} app_int_amount_sal"
                                                                            name="app_int_amount_sal"
                                                                            value="{{ encrypt($val->id) }}"
                                                                            id="app_int_amount_sal{{ $val->id }}"
                                                                            {{ $key == 0 ? 'checked' : '' }}>
                                                                        <label for="app_int_amount_sal{{ $val->id }}"
                                                                            class="btn btn-light-success w-100"
                                                                            data-kt-modal-bidding="option">
                                                                            @if ($val->custome_chk == 0)
                                                                                <div class="fw-semibold"
                                                                                    style="position: relative">
                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="position: absolute; top: -24px;">
                                                                                        <span
                                                                                            class="fs-4">{{ $val->coins }}
                                                                                            Coins</span>
                                                                                    </div>
                                                                                    <div
                                                                                        style="position: relative; margin-top: 22px;">

                                                                                        <div class="fs-6 fw-bold"
                                                                                            style="float: left;">
                                                                                            <span class="mb-2"><i
                                                                                                    class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"
                                                                                                    aria-hidden="true"></i></span>{{ $currency == 'INR' ? $val->price : $val->price_usd }}
                                                                                        </div>

                                                                                        <div class="fs-6 fw-bold"
                                                                                            style="float: right;">
                                                                                            <span class="mb-2"><i
                                                                                                    class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"
                                                                                                    aria-hidden="true"></i></span>{{ $currency == 'INR' ? $val->per_coin_rate : $val->per_coin_rate_usd }}/Coins
                                                                                        </div>

                                                                                    </div>

                                                                                </div>
                                                                            @else
                                                                                <div
                                                                                    style="position: relative; margin-top: 22px;">
                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="position: absolute; top: -24px;">
                                                                                        <span class="fs-4">Custom</span>
                                                                                    </div>

                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="float: left;">
                                                                                        <span class="mb-2"></span>Contact
                                                                                    </div>

                                                                                    <div class="fs-6 fw-bold"
                                                                                        style="float: right;">
                                                                                        <span
                                                                                            class="mb-2"></span>{{ $currency == 'INR' ? $val->per_coin_rate : $val->per_coin_rate_usd }}Coins
                                                                                    </div>

                                                                                </div>
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="border-0 pb-0" style="">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <h5 class="card-title">Validity will be added on the package
                                                            </h5>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="m-0">
                                                                <a href="javascript:;"
                                                                    class="btn btn-color-success top-up-cart-sal bg-success bg-opacity-15 bg-hover-opacity-25 fw-semibold w-100">
                                                                    Top-up
                                                                </a>
                                                            </div>
                                                            <!--<button type="button" class="btn btn-light-danger py-2">Top-up</button>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-2 bg-light px-0"></div>
                                        <!--Begin top up history-->
                                        <!--end top up history-->
                                    </div>
                                </div>
                            </div>

                                </div>
                                <!--end::wallet-pane-packages-->

                                <!--begin::wallet-pane-transactions-->
                                <div id="wallet-pane-transactions" class="wallet-tab-pane" style="display:none">
                                    <div id="walletTransactionsContent" class="text-center text-muted py-15">
                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                        Loading transactions...
                                    </div>
                                </div>
                                <!--end::wallet-pane-transactions-->

                                <!--begin::wallet-pane-invoices-->
                                <div id="wallet-pane-invoices" class="wallet-tab-pane" style="display:none">
                                    <div class="wallet-coming-soon">
                                        <i class="fa fa-file-invoice"></i>
                                        <div class="fw-bold fs-6 mt-3">Invoices</div>
                                        <div class="text-gray-500 fs-8">Coming soon.</div>
                                    </div>
                                </div>
                                <!--end::wallet-pane-invoices-->

                                <!--begin::wallet-pane-billing-->
                                <div id="wallet-pane-billing" class="wallet-tab-pane" style="display:none">
                                    <div class="wallet-coming-soon">
                                        <i class="fa fa-credit-card"></i>
                                        <div class="fw-bold fs-6 mt-3">Billing</div>
                                        <div class="text-gray-500 fs-8">Coming soon.</div>
                                    </div>
                                </div>
                                <!--end::wallet-pane-billing-->

                                <!--begin::wallet-pane-settings-->
                                <div id="wallet-pane-settings" class="wallet-tab-pane" style="display:none">
                                    <div class="wallet-coming-soon">
                                        <i class="fa fa-cog"></i>
                                        <div class="fw-bold fs-6 mt-3">Settings</div>
                                        <div class="text-gray-500 fs-8">Coming soon.</div>
                                    </div>
                                </div>
                                <!--end::wallet-pane-settings-->

                            </div>

                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Operations/Sales-->

                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
@endsection
@push('footer-script')
        <script src="https://js.stripe.com/v3/"></script>
        <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}<?php echo TM_VERSION; ?>"></script>


        <script>
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

            @if ($errors->has('error_get'))
                Swal.fire("Failed", "{{ $errors->first('error_get') }}", "error");
            @endif


            elems.forEach(function(html) {
                var switchery = new Switchery(html, {
                    size: 'medium'
                });
            });

            $('.salse-packages').hide();
            $('.package-switch').click(function() {
                let checked = $(this).data('kt-plan');
                if (checked == 'operational') {
                    $('.package-switch').removeClass('salse-tab-plan');
                    $(this).addClass('opra-tab-plan');
                    $('.salse-packages').hide();
                    $('.operational-packages').fadeIn('slow');

                } else {
                    $('.package-switch').removeClass('opra-tab-plan');
                    $(this).addClass('salse-tab-plan');
                    $('.operational-packages').hide();
                    $('.salse-packages').fadeIn('slow');
                }
                setTimeout(() => {
                    $(this).removeClass('active');
                }, 100);

            })

            // Wallet Center: cosmetic-only wallet color mode + "Buy Coins" label.
            // Added as a SEPARATE handler so the existing .package-switch click
            // logic above (which drives the actual operational/sales content
            // toggle) is never touched.
            $('.package-switch').on('click', function() {
                var plan = $(this).data('kt-plan');
                if (plan === 'sales') {
                    $('.wallet-center-header').addClass('wallet-mode-sales');
                    $('#walletBuyCoinsLabel').text('Buy Sales Coins');
                } else {
                    $('.wallet-center-header').removeClass('wallet-mode-sales');
                    $('#walletBuyCoinsLabel').text('Buy Coins');
                }
            });

            // Wallet Center: top-level tab switching (Overview / Packages /
            // Transactions / Invoices / Billing / Settings). Pure client-side
            // show/hide — all tab content is already in the DOM except
            // Transactions, which is lazy-loaded once via AJAX on first view.
            var walletTransactionsLoaded = false;

            window.switchWalletCenterTab = function(tab) {
                $('.wallet-tab-pane').hide();
                $('#wallet-pane-' + tab).show();
                $('.wallet-tab-link').removeClass('wt-active');
                $('.wallet-tab-link[data-tab="' + tab + '"]').addClass('wt-active');

                if (tab === 'transactions' && !walletTransactionsLoaded) {
                    walletTransactionsLoaded = true;
                    $.ajax({
                        url: "{{ route('service_company.transactions.subscriptionAjax') }}",
                        type: 'GET',
                        success: function(response) {
                            $('#walletTransactionsContent').html(response.html);
                        },
                        error: function() {
                            walletTransactionsLoaded = false;
                            $('#walletTransactionsContent').html(
                                '<div class="text-center text-danger py-10">Could not load transactions. Please try again.</div>'
                            );
                        }
                    });
                }
            };

            // Always activate Overview on load — only one tab exists now.
            switchWalletCenterTab('overview');


            $(document).on('click', '.topupcustom,.customsubscription,.customsubscription_sal', function(e) {
                e.preventDefault();
                // $('.app_int_amount_op').click(function(){
                var suscriptId, url;


                if ($(this).is('.customsubscription')) {
                    suscriptId = $('input[name="opr_plan"]:checked').val();
                } else if ($(this).is('.customsubscription_sal')) {
                    suscriptId = $('input[name="sales_plan"]:checked').val();
                } else if ($(this).hasClass('topupcustom')) {
                    suscriptId = $(this).val();
                }


                url = "{{ route('service_company.subscribe.customCheckoutSubscribe') }}";
                if ($(this).hasClass('topupcustom')) {
                    url += '?topup=true';
                }


                Swal.fire({
                    title: "Are you sure?",
                    text: "Would you like our sales team to give you a call and discuss the details of the custom subscription package",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "No",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.isConfirmed) {

                        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                        var token = "{{ csrf_token() }}";

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'id': suscriptId
                            },
                            beforeSend: function() {
                                $('#approve').html('<img src="' + baseurl +
                                    '" height="35px" width="100%">');
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Request Sent",
                                    text: "Your rwquest send successfully.",
                                    icon: "success"
                                });
                                $('.closemodal').click();
                            }
                        });


                    }
                });
            });

            $(document).on('click','.upgradeselect',function(e) {
                e.preventDefault();
                var suscriptId, url;
                suscriptId = $(this).data('id');
                url = "{{ route('service_company.subscribe.checkoutSubscribe') }}";

                if ($(this).hasClass('top-up-cart-op') || $(this).hasClass('top-up-cart-sal')) {
                    url += '?topup=true';
                }
                var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                var token = "{{ csrf_token() }}";
                $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'POST',
                            'id': suscriptId
                        },
                        beforeSend: function() {
                            $('#approve').html('<img src="' + baseurl + '" height="35px" width="100%">');
                        },
                        success: function(response) {
                            var url_redirect =
                                "{{ route('service_company.subscribe.checkoutSubscribeIist') }}";
                            window.location = url_redirect;
                        }
                    });
                    

                });



            $(document).on('click',
                '#oper_kt_modal_upgrade_plan_btn, #sales_kt_modal_upgrade_plan_btn, .top-up-cart-op, .top-up-cart-sal',
                function(e) {
                    e.preventDefault();
                    var $button = $(this);
                    $button.prop('disabled', true); // Disable the button after the first click

                    var suscriptId, url;

                    if ($(this).is('#oper_kt_modal_upgrade_plan_btn')) {
                        suscriptId = $('input[name="opr_plan"]:checked').val();
                    } else if ($(this).is('#sales_kt_modal_upgrade_plan_btn')) {
                        suscriptId = $('input[name="sales_plan"]:checked').val();
                    } else if ($(this).hasClass('top-up-cart-op')) {
                        suscriptId = $("input[name='app_int_amount_op']:checked").val();
                    } else if ($(this).hasClass('top-up-cart-sal')) {
                        suscriptId = $("input[name='app_int_amount_sal']:checked").val();
                    }

                    url = "{{ route('service_company.subscribe.checkoutSubscribe') }}";

                    if ($(this).hasClass('top-up-cart-op') || $(this).hasClass('top-up-cart-sal')) {
                        url += '?topup=true';
                    }



                    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

                    var token = "{{ csrf_token() }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'POST',
                            'id': suscriptId
                        },
                        beforeSend: function() {
                            $('#approve').html('<img src="' + baseurl + '" height="35px" width="100%">');
                        },
                        success: function(response) {
                            var url_redirect =
                                "{{ route('service_company.subscribe.checkoutSubscribeIist') }}";
                            window.location = url_redirect;
                        }
                    });



                    // window.location = url;
                });


            $(document).on('click', '.opdownloadinvoice', function() {

                let checked = $(this).data('cart');
                // alert(checked);
                var url = "{{ route('service_company.subscribe.receiptdownload') }}";
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        '_method': 'GET',
                        'id': checked
                    },

                    success: function(data, status, xhr) {
                        let filename = xhr.getResponseHeader('Content-Disposition').split('filename=')[1]
                            .replace(/['"]/g, '');
                        // Create a new Blob object using the data
                        let blob = new Blob([data], {
                            type: 'application/pdf'
                        });

                        // Create a link element, use it to download the file
                        let link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;
                        link.click();

                    }
                });

            });
        </script>
@endpush
