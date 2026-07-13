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
            cursor: pointer; background: #fff; transition: border-color .15s, box-shadow .15s;
        }
        .wallet-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .wallet-card .wallet-card-check { color: #d0d3dc; font-size: 20px; }
        .wallet-card .wallet-card-icon {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-size: 16px;
        }
        .wallet-card-operational .wallet-card-icon { background: rgba(109,40,217,.12); color: #6d28d9; }
        .wallet-card-sales .wallet-card-icon { background: rgba(16,163,74,.12); color: #10a34a; }
        .wallet-card .badge.badge-light-success { font-size: 9px; padding: 3px 8px; }
        .wallet-card-balance { font-size: 26px; font-weight: 800; color: #181c32; margin-top: 14px; line-height: 1.2; }
        .wallet-card-balance-label { font-size: 11px; color: #8a8fa3; margin-bottom: 14px; }
        .wallet-card-stats { border-top: 1px dashed #e4e6ef; padding-top: 12px; }
        .wallet-card-stat-label { font-size: 10px; color: #8a8fa3; }

        /* .package-switch.opra-tab-plan / .salse-tab-plan are pre-existing
           global rules meant for small pill buttons (solid bg + white bold
           text). Reusing those class names on the wallet cards (needed so
           the existing JS toggle still recognizes them) drags that styling
           in too, so neutralize it before applying the wallet card's own
           "active wallet" look. */
        .wallet-card.package-switch.opra-tab-plan,
        .wallet-card.package-switch.salse-tab-plan {
            background-color: #fff !important;
            color: #181c32 !important;
            font-weight: 400 !important;
        }
        .wallet-card.opra-tab-plan { border-color: #6d28d9; box-shadow: 0 0 0 1px #6d28d9; }
        .wallet-card.opra-tab-plan .wallet-card-check { color: #6d28d9; }
        .wallet-card.salse-tab-plan { border-color: #10a34a; box-shadow: 0 0 0 1px #10a34a; }
        .wallet-card.salse-tab-plan .wallet-card-check { color: #10a34a; }

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
                            <a href="javascript:;" class="btn btn-sm wallet-buy-coins-btn"
                                onclick="switchWalletCenterTab('packages'); return false;">
                                <i class="fa fa-coins me-1"></i> <span id="walletBuyCoinsLabel">Buy Coins</span>
                            </a>
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
                                                <span class="badge badge-light-success fs-9">ACTIVE</span>
                                            </div>
                                        </div>
                                        <span class="wallet-card-check"><i class="fa fa-check-circle"></i></span>
                                    </div>
                                    <div class="wallet-card-balance">{{ number_format($remain_coins) }}</div>
                                    <div class="wallet-card-balance-label">Coins Available</div>
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
                                                <span class="badge badge-light-success fs-9">ACTIVE</span>
                                            </div>
                                        </div>
                                        <span class="wallet-card-check"><i class="fa fa-check-circle"></i></span>
                                    </div>
                                    <div class="wallet-card-balance">{{ number_format($remain_coins_sales) }}</div>
                                    <div class="wallet-card-balance-label">Coins Available</div>
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
                            <li><a href="javascript:;" class="wallet-tab-link" data-tab="packages" onclick="switchWalletCenterTab('packages');return false;"><i class="fa fa-box"></i> Packages</a></li>
                            <li><a href="javascript:;" class="wallet-tab-link" data-tab="transactions" onclick="switchWalletCenterTab('transactions');return false;"><i class="fa fa-exchange-alt"></i> Transactions</a></li>
                            <li><a href="javascript:;" class="wallet-tab-link" data-tab="invoices" onclick="switchWalletCenterTab('invoices');return false;"><i class="fa fa-file-invoice"></i> Invoices</a></li>
                            <li><a href="javascript:;" class="wallet-tab-link" data-tab="billing" onclick="switchWalletCenterTab('billing');return false;"><i class="fa fa-credit-card"></i> Billing</a></li>
                            <li><a href="javascript:;" class="wallet-tab-link" data-tab="settings" onclick="switchWalletCenterTab('settings');return false;"><i class="fa fa-cog"></i> Settings</a></li>
                        </ul>
                        <!--end::Tab nav-->
                    </div>
                    <!--end::Wallet Center header-->
                    <!--begin::Card body-->
                    <div class="card-body position-relative px-0 py-0" id="kt_activitiesApplication_body">
                        <div id="wallet-pane-overview" class="wallet-tab-pane">
                        <div class="row g-5 g-xl-10 mb-5 mb-xl-10 px-5">
                            <div class="col-xl-12 col-lg-12">
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
                                        @foreach (collect($operational_package)->take(3) as $opr_preview)
                                            <div class="col-md-4">
                                                <div class="wallet-package-preview">
                                                    <div class="fw-bold fs-6 text-gray-900">{{ $opr_preview['title'] }}</div>
                                                    <div class="text-gray-500 fs-8 mb-2">{{ $opr_preview['days'] ? $opr_preview['days'] . ' Days' : 'Custom' }}</div>
                                                    <div class="fs-3 fw-bolder text-primary">
                                                        <i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"></i>
                                                        {{ $currency == 'INR' ? $opr_preview['price'] : $opr_preview['price_usd'] }}
                                                    </div>
                                                    <div class="text-gray-500 fs-8">{{ $opr_preview['coins'] }} Coins</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
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
                                        @foreach (collect($sales_package)->take(3) as $sales_preview)
                                            <div class="col-md-4">
                                                <div class="wallet-package-preview">
                                                    <div class="fw-bold fs-6 text-gray-900">{{ $sales_preview['title'] }}</div>
                                                    <div class="text-gray-500 fs-8 mb-2">{{ $sales_preview['days'] ? $sales_preview['days'] . ' Days' : 'Custom' }}</div>
                                                    <div class="fs-3 fw-bolder text-success">
                                                        <i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}"></i>
                                                        {{ $currency == 'INR' ? $sales_preview['price'] : $sales_preview['price_usd'] }}
                                                    </div>
                                                    <div class="text-gray-500 fs-8">{{ $sales_preview['coins'] }} Coins</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!--end::Overview extras - Sales-->

                                </div>
                                <!--end::wallet-pane-overview-->

                                <div id="wallet-pane-packages" class="wallet-tab-pane" style="display:none">



                                <!--begin::Packages - Operational-->
                                <div id="kt_modal_upgrade_plan" class="operational-packages">
                                    <!--begin::Heading-->
                                    <div class="mb-13 text-center">
                                        <h1 class="mb-3">Choose an Operational Package</h1>

                                        <div class="text-muted fw-semibold fs-5">
                                            If you need more info, please check <a href="{{ url('payment-terms-and-conditions') }}"
                                                class="link-primary fw-bold" target="_blank">Pricing Guidelines</a>.
                                        </div>
                                    </div>
                                    <!--end::Heading-->

                                    <!--begin::Plans-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Nav group-->

                                        <!--begin::Row-->
                                        <div class="row">
                                                <!--begin::Col-->
                                                {{-- @dd($operational_package); --}}
                                                @if ($operational_package)
                                                    @foreach ($operational_package as $opr_val)

                                                                @php
                                                                 $visibility_coins = 1; 
                                                                    if($opr_val['visibility_show']==1){
                                                                        $visibility_coins = 1;  // Yearly
                                                                    }elseif($opr_val['visibility_show']==2){
                                                                        $visibility_coins = 12; // monthaly
                                                                    }elseif($opr_val['visibility_show']==0){
                                                                        $visibility_coins = 365;  // Daily
                                                                    }

                                                                    $number = $opr_val['days'];
                                                                     $months = 12;
                                                                    if ($number >= 365) {
                                                                        $months = floor($number / 30);
                                                                        $remainingDays = $number % 30;
                                                                    } elseif ($number >= 30) {
                                                                        $months = floor($number / 30);
                                                                       
                                                                    }

                                                                    $coisn = $opr_val['coins'];
                                                                    $perMonthCoins = $coisn/$months;
                                                                    

                                                                @endphp
                                                                
                                                                    <div class="col-xl-3">
                                                                        <div class="d-flex h-100 align-items-center">
                                                                            <!--begin::Option-->
                                                                            <div class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10" style="position: relative;">
                                                                                <!--begin::Heading-->
                                                                                <div class="mb-7 text-center">
                                                                                    <!--begin::Title-->
                                                                                    <h1 class="text-gray-900 mb-5 fw-bolder">{{ $opr_val['title'] }}</h1>
                                                                                    <!--end::Title-->

                                                
                                                                                    <!--begin::Description-->
                                                                                    @if($opr_val['discount'] >0)
                                                                                        @if($opr_val['visibility_show']==1)
                                                                                        <div class="text-gray-600 fw-semibold mb-5" style="position: absolute;top: -10px;right: -10px;background: #ff6b6b;color: #fff;padding: 5px 19px;font-size: 12px;font-weight: bold;border-radius: 50px;">
                                                                                            <span class="text-white" style="font-size: 14px;">{{ $opr_val['discount'] }}% Off</span>                                             
                                                                                        </div>
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-primary mt-2 fw-bold" style="font-size: 16px;"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>  <del>{{ $currency == 'INR' ? ($opr_val['price'] / (1 - $opr_val['discount'] / 100)) : ($opr_val['price_usd'] / (1 - $opr_val['discount'] / 100)) }} </del></div>                                             
                                                                                        </div>
                                                                                        @elseif($opr_val['visibility_show']==2)
                                                                                        <div class="text-gray-600 fw-semibold mb-5" style="position: absolute;top: -10px;right: -10px;background: #ff6b6b;color: #fff;padding: 5px 19px;font-size: 12px;font-weight: bold;border-radius: 50px;">
                                                                                            <span class="text-white" style="font-size: 14px;">{{ $opr_val['discount'] }}% Off</span>                                             
                                                                                        </div>
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-primary mt-2 fw-bold" style="font-size: 16px;"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>  <del>{{ $currency == 'INR' ? ($opr_val['total_price_monthly'] / (1 - $opr_val['discount'] / 100)) : ($opr_val['total_price_usd_monthly'] / (1 - $opr_val['discount'] / 100)) }} </del></div>                                             
                                                                                        </div>

                                                                                        @elseif($opr_val['visibility_show']==0)
                                                                                        <div class="text-gray-600 fw-semibold mb-5" style="position: absolute;top: -10px;right: -10px;background: #ff6b6b;color: #fff;padding: 5px 19px;font-size: 12px;font-weight: bold;border-radius: 50px;">
                                                                                            <span class="text-white" style="font-size: 14px;">{{ $opr_val['discount'] }}% Off</span>                                             
                                                                                        </div>
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-primary mt-2 fw-bold" style="font-size: 16px;"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>  <del>{{ $currency == 'INR' ? ($opr_val['total_price_days'] / (1 - $opr_val['discount'] / 100)) : ($opr_val['total_price_usd_days'] / (1 - $opr_val['discount'] / 100)) }} </del></div>                                             
                                                                                        </div>
                                                                                        @endif
                                                                                    @else
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-primary mt-2 fw-bold">&nbsp;</div>                                             
                                                                                        </div>
                                                                                    @endif
                                                                                    <!--end::Description-->
                                                
                                                                                    <!--begin::Price-->
                                                                                    <div class="text-center">
                                                                                        <span class="mb-2 text-primary">
                                                                                            <i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>
                                                                                        </span>

                                                                                        @if($opr_val['visibility_show']==1)
                                                                                        <span class="fs-2x fw-bold text-primary" data-kt-plan-price-month="{{ $currency == 'INR' ? $opr_val['price'] : $opr_val['price_usd'] }}"
                                                                                        data-kt-plan-price-annual="{{ $currency == 'INR' ? $opr_val['price'] : $opr_val['price_usd'] }}">
                                                                                        {{ $currency == 'INR' ? $opr_val['price'] : $opr_val['price_usd'] }}
                                                                                        </span>
                                                                                        <span class="fs-7 fw-semibold opacity-50">/ 
                                                                                            <span data-kt-element="period">Year</span>
                                                                                        </span>
                                                                                        @elseif($opr_val['visibility_show']==2)
                                                                                        <span class="fs-2x fw-bold text-primary" data-kt-plan-price-month="{{ $currency == 'INR' ? $opr_val['total_price_monthly'] : $opr_val['total_price_usd_monthly'] }}"
                                                                                        data-kt-plan-price-annual="{{ $currency == 'INR' ? $opr_val['total_price_monthly'] : $opr_val['total_price_usd_monthly'] }}">
                                                                                        {{ $currency == 'INR' ? $opr_val['total_price_monthly'] : $opr_val['total_price_usd_monthly'] }}
                                                                                        </span>
                                                                                        <span class="fs-7 fw-semibold opacity-50">/ 
                                                                                            <span data-kt-element="period">Month</span>
                                                                                        </span>
                                                                                        @elseif($opr_val['visibility_show']==0)
                                                                                        <span class="fs-2x fw-bold text-primary" data-kt-plan-price-month="{{ $currency == 'INR' ? $opr_val['total_price_days'] : $opr_val['total_price_usd_days'] }}"
                                                                                        data-kt-plan-price-annual="{{ $currency == 'INR' ? $opr_val['total_price_days'] : $opr_val['total_price_usd_days'] }}">
                                                                                        {{ $currency == 'INR' ? $opr_val['total_price_days'] : $opr_val['total_price_usd_days'] }}
                                                                                        </span>
                                                                                        <span class="fs-7 fw-semibold opacity-50">/ 
                                                                                            <span data-kt-element="period">Day</span>
                                                                                        </span>
                                                                                        @endif
                                                                                       
                                                                                       
                                                                                    </div>
                                                                                    <!--end::Price-->
                                                                                </div>
                                                                                <!--end::Heading-->

                                                                                    <!--begin::Select-->
                                                                                    @php
                                                                                      $currentCoinPackageId = null;
                                                                                        if (!empty($current_coins_operational) && is_array($current_coins_operational)) {
                                                                                            $first = reset($current_coins_operational); // safe first element
                                                                                            $currentCoinPackageId = $first['coin_package_id'] ?? null;
                                                                                        }
                                                                                    @endphp
                                                                                @if($opr_val['custome_pck'] !=2)
                                                                                    <a href="javascript:;" data-id="{{ encrypt($opr_val['id']) }}" class="btn btn-lg btn-{{ $opr_val['id'] === $current_coins_operational[0]['coin_package_id'] ? 'primary' : 'dark' }} upgradeselect">
                                                                                     {{ $opr_val['id'] === $currentCoinPackageId ? 'Current Plan ' : 'Upgrade Now' }} </a>
                                                                                 @else
                                                                                 @if($opr_val['id'] === $currentCoinPackageId)
                                                                                 <a href="javascript:;" class="btn btn-lg btn-primary">
                                                                                     {{ $opr_val['id'] === $currentCoinPackageId ? 'Current Plan' : '' }} </a>

                                                                                     @else
                                                                                     <a href="javascript:;" class="btn btn-lg btn-primary" style="opacity: 0;">
                                                                                        {{ $opr_val['id'] === $currentCoinPackageId ? 'Free' : 'Free' }} </a>

                                                                                 @endif

                                                                                 @endif
                                                                                 <hr>
                                                                                
                                                                                 <!--end::Select-->

                                                
                                                                                <!--begin::Features-->
                                                                                <div class="w-100 mb-10">       

                                                                                    <div class="d-flex align-items-center mb-5">
                                                                                        <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3">
                                                                                            Coins
                                                                                        </span>
                                                                                        <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3" style="float: right;">
                                                                                        {{round($perMonthCoins)}}
                                                                                        </span>
                                                                                    </div> 
                                                                                    <div class="d-flex align-items-center mb-5">
                                                                                        <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3"> Validity
                                                                                        </span>
                                                                                        <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3" style="float: right;">
                                                                                           {{ $opr_val['days'] ? $opr_val['days'] . ' Days' : 'Custom' }}
                                                                                        </span>
                                                                                    </div> 

                                                                                
                                                                                @foreach ($opr_val['features'] as $features_val)
                                                                                    <!--begin::Item-->     
                                                                                    <div class="d-flex align-items-center mb-5">
                                                                                       @if( $features_val->status)
                                                                                        <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3">
                                                                                            {{ $features_val->title }}
                                                                                        </span>
                                                                                        <i class="fa fa-check ki-check-circle fs-4 text-success">
                                                                                            <span class="path1"></span>
                                                                                            <span class="path2"></span>
                                                                                        </i>
                                                                                        @else

                                                                                        <span class="fw-semibold fs-6 text-gray-600 flex-grow-1">
                                                                                            {{ $features_val->title }}                                             
                                                                                        </span>
                                                                                        <i class="fa fa-times ki-cross-circle fs-4">
                                                                                            <span class="path1"></span>
                                                                                            <span class="path2"></span>
                                                                                        </i>
                                                                                        @endif
                                                                                    </div>
                                                                                    <!--end::Item--> 
                                                                                @endforeach

                                                                                          
                                                                                </div>
                                                                                <!--end::Features-->         
                                                
                                                                            
                                                                            </div>
                                                                            <!--end::Option-->
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Plans-->

                                                <!--begin::Actions-->
                                                <div class="d-flex flex-center flex-row-fluid pt-12 d-none">
                                                    <button type="reset" class="btn btn-light me-3"
                                                        data-bs-dismiss="modal">
                                                        Close
                                                    </button>

                                                    <button type="submit" class="btn btn-primary"
                                                        id="oper_kt_modal_upgrade_plan_btn">

                                                        <!--begin::Indicator label-->
                                                        <span class="indicator-label">
                                                            Upgrade Plan</span>
                                                        <!--end::Indicator label-->

                                                        <!--begin::Indicator progress-->
                                                        <span class="indicator-progress">
                                                            Please wait... <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                        <!--end::Indicator progress-->
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
                                </div>
                                <!--end::Packages - Operational-->

                                <!--begin::Packages - Sales-->
                                <div id="sales_kt_modal_upgrade_plan" class="salse-packages" style="display:none">
                                    <!--begin::Heading-->
                                                <div class="mb-13 text-center">
                                                    <h1 class="mb-3">Choose a Sales Package</h1>

                                                    <div class="text-muted fw-semibold fs-5">
                                                        If you need more info, please check <a href="{{ url('payment-terms-and-conditions') }}"
                                                            class="link-primary fw-bold" target="_blank">Pricing Guidelines</a>.
                                                    </div>
                                                </div>
                                                <!--end::Heading-->

                                                <!--begin::Plans-->
                                                <div class="d-flex flex-column">
                                                    <!--begin::Nav group-->

                                                    <!--begin::Row-->
                                                    <div class="row">
                                                        <!--begin::Col-->

                                                         <!--begin::Col-->
                                                         {{-- @dd($sales_package) --}}
                                                         @if ($sales_package)
                                                         @foreach ($sales_package as $sales_val)
                                                            @php
                                                            $visibility_coins = 1; 
                                                                if($sales_val['visibility_show']==1){
                                                                    $visibility_coins = 1;  // Yearly
                                                                }elseif($sales_val['visibility_show']==2){
                                                                    $visibility_coins = 12; // monthaly
                                                                }elseif($sales_val['visibility_show']==0){
                                                                    $visibility_coins = 365;  // Daily
                                                                }
                                                                $number = $sales_val['days'];
                                                                     $months = 12;
                                                                    if ($number >= 365) {
                                                                        $months = floor($number / 30);
                                                                        $remainingDays = $number % 30;
                                                                    } elseif ($number >= 30) {
                                                                        $months = floor($number / 30);
                                                                    }

                                                                    $coisn = $sales_val['coins'];
                                                                    $perMonthCoins = $coisn/$months;

                                                            @endphp

                                                             <div class="col-xl-3">
                                                                 <div class="d-flex h-100 align-items-center">
                                                                     <!--begin::Option-->
                                                                     <div class="w-100 d-flex flex-column flex-center rounded-3 bg-light bg-opacity-75 py-15 px-10" style="position: relative;">
                                                                         <!--begin::Heading-->
                                                                         <div class="mb-7 text-center">
                                                                             <!--begin::Title-->
                                                                             <h1 class="text-gray-900 mb-5 fw-bolder">{{ $sales_val['title'] }}</h1>
                                                                             <!--end::Title-->
                                         
                                                                             <!--begin::Description-->
                                                                             @if($sales_val['discount'] >0)
                                                                                        @if($sales_val['visibility_show']==1)
                                                                                        <div class="text-gray-600 fw-semibold mb-5" style="position: absolute;top: -10px;right: -10px;background: #ff6b6b;color: #fff;padding: 5px 19px;font-size: 12px;font-weight: bold;border-radius: 50px;">
                                                                                            <span class="text-white" style="font-size: 14px;">{{ $sales_val['discount'] }}% Off</span>                                             
                                                                                        </div>
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-success mt-2 fw-bold" style="font-size: 16px;"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>  <del>{{ $currency == 'INR' ? ($sales_val['price'] / (1 - $sales_val['discount'] / 100)) : ($sales_val['price_usd'] / (1 - $sales_val['discount'] / 100)) }} </del></div>                                             
                                                                                        </div>
                                                                                        @elseif($sales_val['visibility_show']==2)
                                                                                        <div class="text-gray-600 fw-semibold mb-5" style="position: absolute;top: -10px;right: -10px;background: #ff6b6b;color: #fff;padding: 5px 19px;font-size: 12px;font-weight: bold;border-radius: 50px;">
                                                                                            <span class="text-white" style="font-size: 14px;">{{ $sales_val['discount'] }}% Off</span>                                             
                                                                                        </div>
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-success mt-2 fw-bold" style="font-size: 16px;"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>  <del>{{ $currency == 'INR' ? ($sales_val['total_price_monthly'] / (1 - $sales_val['discount'] / 100)) : ($sales_val['total_price_usd_monthly'] / (1 - $sales_val['discount'] / 100)) }} </del></div>                                             
                                                                                        </div>

                                                                                        @elseif($sales_val['visibility_show']==0)
                                                                                        <div class="text-gray-600 fw-semibold mb-5" style="position: absolute;top: -10px;right: -10px;background: #ff6b6b;color: #fff;padding: 5px 19px;font-size: 12px;font-weight: bold;border-radius: 50px;">
                                                                                            <span class="text-white" style="font-size: 14px;">{{ $sales_val['discount'] }}% Off</span>                                             
                                                                                        </div>
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-success mt-2 fw-bold" style="font-size: 16px;"><i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>  <del>{{ $currency == 'INR' ? ($sales_val['total_price_days'] / (1 - $sales_val['discount'] / 100)) : ($sales_val['total_price_usd_days'] / (1 - $sales_val['discount'] / 100)) }} </del></div>                                             
                                                                                        </div>
                                                                                        @endif
                                                                                    @else
                                                                                        <div class="text-gray-600 fw-semibold mb-5">
                                                                                            <div class="text-success mt-2 fw-bold">&nbsp;</div>                                             
                                                                                        </div>
                                                                                    @endif
                                                                             <!--end::Description-->
                                         
                                                                             <!--begin::Price-->
                                                                             <div class="text-center">
                                                                                 <span class="mb-2 text-success ">
                                                                                     <i class="fa fa-{{ $currency == 'INR' ? 'inr' : 'usd' }}" aria-hidden="true"></i>
                                                                                 </span>
                                         
                                                                                 @if($sales_val['visibility_show']==1)
                                                                                        <span class="fs-2x fw-bold text-success" data-kt-plan-price-month="{{ $currency == 'INR' ? $sales_val['price'] : $sales_val['price_usd'] }}"
                                                                                        data-kt-plan-price-annual="{{ $currency == 'INR' ? $sales_val['price'] : $sales_val['price_usd'] }}">
                                                                                        {{ $currency == 'INR' ? $sales_val['price'] : $sales_val['price_usd'] }}
                                                                                        </span>
                                                                                        <span class="fs-7 fw-semibold opacity-50">/ 
                                                                                            <span data-kt-element="period">Year</span>
                                                                                        </span>
                                                                                @elseif($sales_val['visibility_show']==2)
                                                                                        <span class="fs-2x fw-bold text-success" data-kt-plan-price-month="{{ $currency == 'INR' ? $sales_val['total_price_monthly'] : $sales_val['total_price_usd_monthly'] }}"
                                                                                        data-kt-plan-price-annual="{{ $currency == 'INR' ? $sales_val['total_price_monthly'] : $sales_val['total_price_usd_monthly'] }}">
                                                                                        {{ $currency == 'INR' ? $sales_val['total_price_monthly'] : $sales_val['total_price_usd_monthly'] }}
                                                                                        </span>
                                                                                        <span class="fs-7 fw-semibold opacity-50">/ 
                                                                                            <span data-kt-element="period">Month</span>
                                                                                        </span>
                                                                                @elseif($sales_val['visibility_show']==0)
                                                                                        <span class="fs-2x fw-bold text-success" data-kt-plan-price-month="{{ $currency == 'INR' ? $sales_val['total_price_days'] : $sales_val['total_price_usd_days'] }}"
                                                                                        data-kt-plan-price-annual="{{ $currency == 'INR' ? $sales_val['total_price_days'] : $sales_val['total_price_usd_days'] }}">
                                                                                        {{ $currency == 'INR' ? $sales_val['total_price_days'] : $sales_val['total_price_usd_days'] }}
                                                                                        </span>
                                                                                        <span class="fs-7 fw-semibold opacity-50">/ 
                                                                                            <span data-kt-element="period">Day</span>
                                                                                        </span>
                                                                                @endif
                                                                                 
                                                                             </div>
                                                                             <!--end::Price-->
                                                                         </div>
                                                                         <!--end::Heading-->

                                                                          <!--begin::Select-->
                                                                            @php
                                                                                $currentCoinPackageId = null;
                                                                                if (!empty($current_coins_sales) && is_array($current_coins_sales)) {
                                                                                    $first = reset($current_coins_sales); // safe first element
                                                                                    $currentCoinPackageId = $first['coin_package_id'] ?? null;
                                                                                }
                                                                            @endphp

                                                                          @if($sales_val['custome_pck'] !=2)
                                                                          <a href="javascript:;" data-id="{{ encrypt($sales_val['id']) }}" class="btn btn-lg btn-{{ $sales_val['id'] === $currentCoinPackageId  ? 'success' : 'dark' }}  upgradeselect">
                                                                         {{ $sales_val['id'] === $currentCoinPackageId  ? 'Current Plan' : 'Upgrade Now' }}</a>
                                                                         @else
                                                                         @if($sales_val['id'] === $currentCoinPackageId )
                                                                         <a href="javascript:;" class="btn btn-lg btn-success ">
                                                                             {{ $sales_val['id'] === $currentCoinPackageId  ? 'Current Plan' : '' }} </a>

                                                                             @else
                                                                                <a href="javascript:;" class="btn btn-lg btn-primary" style="opacity: 0;">
                                                                                {{ $opr_val['id'] === $currentCoinPackageId  ? 'Free' : 'Free' }} </a>

                                                                         @endif
                                                                        
                                                                     @endif
                                                                     <hr>
                                                                      <!--end::Select-->

                                         
                                                                         <!--begin::Features-->
                                                                         <div class="w-100 mb-10">    

                                                                            <div class="d-flex align-items-center mb-5">
                                                                             <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3">
                                                                                 Sales Coins
                                                                             </span>
                                                                             <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3" style="float: right;">
                                                                                {{round($perMonthCoins)}}
                                                                             </span>
                                                                            </div>
                                                                            
                                                                            <div class="d-flex align-items-center mb-5">
                                                                                <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3"> Validity
                                                                                </span>
                                                                                <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3" style="float: right;">
                                                                                   {{ $sales_val['days'] ? $sales_val['days'] . ' Days' : 'Custom' }}
                                                                                </span>
                                                                            </div>


                                                                         @foreach ($sales_val['features'] as $features_val)
                                                                             <!--begin::Item-->     
                                                                             <div class="d-flex align-items-center mb-5">
                                                                                @if( $features_val->status)
                                                                                 <span class="fw-semibold fs-6 text-gray-800 flex-grow-1 pe-3">
                                                                                     {{ $features_val->title }}
                                                                                 </span>
                                                                                 <i class="fa fa-check ki-check-circle fs-4 text-success">
                                                                                     <span class="path1"></span>
                                                                                     <span class="path2"></span>
                                                                                 </i>
                                                                                 @else

                                                                                 <span class="fw-semibold fs-6 text-gray-600 flex-grow-1">
                                                                                     {{ $features_val->title }}                                             
                                                                                 </span>
                                                                                 <i class="fa fa-times ki-cross-circle fs-4">
                                                                                     <span class="path1"></span>
                                                                                     <span class="path2"></span>
                                                                                 </i>
                                                                                 @endif
                                                                             </div>
                                                                             <!--end::Item--> 
                                                                         @endforeach

                                                                                   
                                                                         </div>
                                                                         <!--end::Features-->         
                                         
                                                                        
                                                                     </div>
                                                                     <!--end::Option-->
                                                                 </div>
                                                             </div>
                                                         @endforeach
                                                     @endif
                                                     <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                </div>
                                                <!--end::Plans-->

                                                <!--begin::Actions-->
                                                <div class="d-flex flex-center flex-row-fluid pt-12 d-none">
                                                    <button type="reset" class="btn btn-light me-3"
                                                        data-bs-dismiss="modal">
                                                        Close
                                                    </button>

                                                    <button type="submit" class="btn btn-primary"
                                                        id="sales_kt_modal_upgrade_plan_btn">

                                                        <!--begin::Indicator label-->
                                                        <span class="indicator-label">
                                                            Upgrade Plan</span>
                                                        <!--end::Indicator label-->

                                                        <!--begin::Indicator progress-->
                                                        <span class="indicator-progress">
                                                            Please wait... <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                        <!--end::Indicator progress-->
                                                    </button>
                                                </div>
                                                <!--end::Actions-->
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

            // Deep-link support: sidebar links to this page can append
            // #tab=packages (etc.) to open directly on that tab.
            (function() {
                var match = /tab=([a-z]+)/.exec(window.location.hash || '');
                if (match && $('#wallet-pane-' + match[1]).length) {
                    switchWalletCenterTab(match[1]);
                }
            })();


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
