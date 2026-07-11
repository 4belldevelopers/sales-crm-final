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
                    <!--begin::Nav group-->
                    <div class="d-flex flex-column pt-10 pb-0">
                        <div class="mb-8 text-center">
                            <h1 class="text-gray-900 fs-2hx fw-normal mb-2">Welcome back,
                                <span class=" fs-2hx fw-bolder me-2">{{ $username }}</span>
                            </h1>
                        </div>
                        <div class="nav-group nav-group-outline mx-auto mb-15" data-kt-buttons="true">
                            <button
                                class="btn btn-color-gray-600 btn-active btn-active-secondary px-6 py-3 me-2 package-switch opra-tab-plan"
                                data-kt-plan="operational">
                                Operational Package
                            </button>

                            <button class="btn btn-color-gray-600 btn-active btn-active-secondary px-6 py-3 package-switch"
                                data-kt-plan="sales">
                                Sales Package
                            </button>
                        </div>
                    </div>
                    <!--end::Nav group-->
                    <!--begin::Card body-->
                    <div class="card-body position-relative px-0 py-0" id="kt_activitiesApplication_body">
                        <div class="row g-5 g-xl-10 mb-5 mb-xl-10 px-5">
                            <div class="col-xl-12 col-lg-12">
                                <!--<div class="card h-lg-100" style="background: linear-gradient(112.14deg, #FF8A00 0%, #E96922 100%)">-->
                                <div class="card h-lg-100 operational-packages"
                                    style="background: dark;box-shadow: 0px 0px 3px 0px #cfcfcf;">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-sm-9 pe-0 mb-5 mb-sm-0">
                                                <div
                                                    class="d-flex justify-content-between h-100 flex-column pt-xl-5 pb-xl-2 ps-xl-7">
                                                    <div class="mb-7">
                                                        <div class="mb-6">
                                                            <h4 class="fs-2 fw-semibold text-dark mb-0">Current Plan</h4>
                                                        </div>

                                                        <div class="d-flex align-items-center flex-wrap d-grid gap-2 ">
                                                            <div class="d-flex align-items-center me-5 me-xl-13">
                                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                                    <span class="symbol-label"
                                                                        style="background: rgb(22 19 241 / 31%);">
                                                                        <span class="svg-icon svg-icon-danger svg-icon-1x">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="20px" height="20px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="20"
                                                                                        height="20" />
                                                                                    <path
                                                                                        d="M8,17 C8.55228475,17 9,17.4477153 9,18 L9,21 C9,21.5522847 8.55228475,22 8,22 L3,22 C2.44771525,22 2,21.5522847 2,21 L2,18 C2,17.4477153 2.44771525,17 3,17 L3,16.5 C3,15.1192881 4.11928813,14 5.5,14 C6.88071187,14 8,15.1192881 8,16.5 L8,17 Z M5.5,15 C4.67157288,15 4,15.6715729 4,16.5 L4,17 L7,17 L7,16.5 C7,15.6715729 6.32842712,15 5.5,15 Z"
                                                                                        opacity="0.3" style="fill: blue !important;" />
                                                                                    <path
                                                                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                                                                        style="fill: blue !important;" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="m-0">
                                                                    <a href="#"
                                                                        class="text-dark text-opacity-75 fs-4">Current
                                                                        Plan</a>
                                                                    <span
                                                                        class="fw-bold text-dark fs-4 d-block">{{ $current_coins_operational ? $current_coins_operational[0]['title'] : '' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center me-5 me-xl-13">
                                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                                    <span class="symbol-label"
                                                                        style="background: rgb(22 19 241 / 31%);">
                                                                        <span class="svg-icon svg-icon-danger svg-icon-1x">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="20px" height="20px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="20"
                                                                                        height="20" />
                                                                                    <path
                                                                                        d="M8,17 C8.55228475,17 9,17.4477153 9,18 L9,21 C9,21.5522847 8.55228475,22 8,22 L3,22 C2.44771525,22 2,21.5522847 2,21 L2,18 C2,17.4477153 2.44771525,17 3,17 L3,16.5 C3,15.1192881 4.11928813,14 5.5,14 C6.88071187,14 8,15.1192881 8,16.5 L8,17 Z M5.5,15 C4.67157288,15 4,15.6715729 4,16.5 L4,17 L7,17 L7,16.5 C7,15.6715729 6.32842712,15 5.5,15 Z"
                                                                                        opacity="0.3" style="fill: blue !important;" />
                                                                                    <path
                                                                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                                                                        style="fill: blue !important;" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="m-0">
                                                                    <a href="#"
                                                                        class="text-dark text-opacity-75 fs-4">Expiration
                                                                        Date</a>
                                                                    <span
                                                                        class="fw-bold text-dark fs-4 d-block">{{ $current_coins_operational ? date('d M Y ', strtotime($current_coins_operational[0]['end_date'])) : '' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center me-5 me-xl-13">
                                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                                    <span class="symbol-label"
                                                                        style="background: rgb(22 19 241 / 31%);">
                                                                        <span class="svg-icon svg-icon-danger svg-icon-1x">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="20px" height="20px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="20"
                                                                                        height="20" />
                                                                                    <path
                                                                                        d="M8,17 C8.55228475,17 9,17.4477153 9,18 L9,21 C9,21.5522847 8.55228475,22 8,22 L3,22 C2.44771525,22 2,21.5522847 2,21 L2,18 C2,17.4477153 2.44771525,17 3,17 L3,16.5 C3,15.1192881 4.11928813,14 5.5,14 C6.88071187,14 8,15.1192881 8,16.5 L8,17 Z M5.5,15 C4.67157288,15 4,15.6715729 4,16.5 L4,17 L7,17 L7,16.5 C7,15.6715729 6.32842712,15 5.5,15 Z"
                                                                                        opacity="0.3" style="fill: blue !important;" />
                                                                                    <path
                                                                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                                                                        style="fill: blue !important;" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="m-0">
                                                                    <a href="#"
                                                                        class="text-dark text-opacity-75 fs-4">Credit
                                                                        Coins</a>
                                                                    <span
                                                                        class="fw-bold text-dark fs-4 d-block">{{ $current_coins_operational ? $current_coins_operational[0]['actual_coins'] : '' }}</span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="m-0">
                                                        <a href="#"
                                                            class="btn btn-color-primary bg-primary bg-opacity-15 bg-hover-opacity-25 fw-semibold"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#kt_modal_upgrade_plan">
                                                            Upgrade Plan
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3" style="height: 200px;width: 200px; display: flex;justify-content: center;align-items: center;text-align: center;position: relative;">
                                                 <canvas id="gaugeChart" height="50" width="50" style="display: block; box-sizing: border-box; height: 50px; width: 50px;"></canvas> 
                                                  <div style="position: absolute; top: 105px; text-align: center;">
                                                        <text x="150" y="145" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-family="inherit" font-weight="700" fill="#99a1b7" class="apexcharts-text apexcharts-datalabel-label" style="font-family: inherit;">Available Coins</text><br>
                                                                        <text x="150" y="126" text-anchor="middle" dominant-baseline="auto" font-size="24px" font-family="inherit" font-weight="600" fill="#071437" class="apexcharts-text apexcharts-datalabel-value" style="font-family: inherit;">{{ $current_coins_operational ? $current_coins_operational[0]['coins'] : '' }}</text>
                                                    </div>

                                                <div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card h-lg-100 salse-packages"
                                    style="background: dark;box-shadow: 0px 0px 3px 0px #cfcfcf; display:none">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-sm-9 pe-0 mb-5 mb-sm-0">
                                                <div
                                                    class="d-flex justify-content-between h-100 flex-column pt-xl-5 pb-xl-2 ps-xl-7">
                                                    <div class="mb-7">
                                                        <div class="mb-6">
                                                            <h4 class="fs-2 fw-semibold text-dark mb-0">Current Plan</h4>
                                                        </div>

                                                        <div class="d-flex align-items-center flex-wrap d-grid gap-2 ">
                                                            <div class="d-flex align-items-center me-5 me-xl-13">
                                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                                    <span class="symbol-label"
                                                                        style="background: rgb(22 247 18 / 35%);">
                                                                        <span class="svg-icon svg-icon-danger svg-icon-1x">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="20px" height="20px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="20"
                                                                                        height="20" />
                                                                                    <path
                                                                                        d="M8,17 C8.55228475,17 9,17.4477153 9,18 L9,21 C9,21.5522847 8.55228475,22 8,22 L3,22 C2.44771525,22 2,21.5522847 2,21 L2,18 C2,17.4477153 2.44771525,17 3,17 L3,16.5 C3,15.1192881 4.11928813,14 5.5,14 C6.88071187,14 8,15.1192881 8,16.5 L8,17 Z M5.5,15 C4.67157288,15 4,15.6715729 4,16.5 L4,17 L7,17 L7,16.5 C7,15.6715729 6.32842712,15 5.5,15 Z"
                                                                                        opacity="0.3" style="fill: green !important;" />
                                                                                    <path
                                                                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                                                                        style="fill: green !important;" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="m-0">
                                                                    <a href="#"
                                                                        class="text-dark text-opacity-75 fs-4">Current
                                                                        Plan</a>
                                                                    <span
                                                                        class="fw-bold text-dark fs-4 d-block">{{ $current_coins_sales ? $current_coins_sales[0]['title'] : '' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center me-5 me-xl-13">
                                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                                    <span class="symbol-label"
                                                                        style="background: rgb(22 247 18 / 35%);">
                                                                        <span class="svg-icon svg-icon-danger svg-icon-1x">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="20px" height="20px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="20"
                                                                                        height="20" />
                                                                                    <path
                                                                                        d="M8,17 C8.55228475,17 9,17.4477153 9,18 L9,21 C9,21.5522847 8.55228475,22 8,22 L3,22 C2.44771525,22 2,21.5522847 2,21 L2,18 C2,17.4477153 2.44771525,17 3,17 L3,16.5 C3,15.1192881 4.11928813,14 5.5,14 C6.88071187,14 8,15.1192881 8,16.5 L8,17 Z M5.5,15 C4.67157288,15 4,15.6715729 4,16.5 L4,17 L7,17 L7,16.5 C7,15.6715729 6.32842712,15 5.5,15 Z"
                                                                                         opacity="0.3" style="fill: green !important;" />
                                                                                    <path
                                                                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                                                                        style="fill: green !important;" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="m-0">
                                                                    <a href="#"
                                                                        class="text-dark text-opacity-75 fs-4">Expiration
                                                                        Date</a>
                                                                    <span
                                                                        class="fw-bold text-dark fs-4 d-block">{{ $current_coins_sales ? date('d M Y ', strtotime($current_coins_sales[0]['end_date'])) : '' }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center me-5 me-xl-13">
                                                                <div class="symbol symbol-40px symbol-circle me-3">
                                                                    <span class="symbol-label"
                                                                        style="background: rgb(22 247 18 / 35%);">
                                                                        <span class="svg-icon svg-icon-danger svg-icon-1x">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                width="20px" height="20px"
                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1"
                                                                                    fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="20"
                                                                                        height="20" />
                                                                                    <path
                                                                                        d="M8,17 C8.55228475,17 9,17.4477153 9,18 L9,21 C9,21.5522847 8.55228475,22 8,22 L3,22 C2.44771525,22 2,21.5522847 2,21 L2,18 C2,17.4477153 2.44771525,17 3,17 L3,16.5 C3,15.1192881 4.11928813,14 5.5,14 C6.88071187,14 8,15.1192881 8,16.5 L8,17 Z M5.5,15 C4.67157288,15 4,15.6715729 4,16.5 L4,17 L7,17 L7,16.5 C7,15.6715729 6.32842712,15 5.5,15 Z"
                                                                                         opacity="0.3" style="fill: green !important;"/>
                                                                                    <path
                                                                                        d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z"
                                                                                        style="fill: green !important;" />
                                                                                </g>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <div class="m-0">
                                                                    <a href="#"
                                                                        class="text-dark text-opacity-75 fs-4">Credit
                                                                        Coins</a>
                                                                    <span
                                                                        class="fw-bold text-dark fs-4 d-block">{{ $current_coins_sales ? $current_coins_sales[0]['actual_coins'] : '' }}</span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="m-0">
                                                        <a href="#"
                                                            class="btn btn-color-success bg-success bg-opacity-15 bg-hover-opacity-25 fw-semibold"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#sales_kt_modal_upgrade_plan">
                                                            Upgrade Plan
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3" style="height: 200px;width: 200px; display: flex; justify-content: center; align-items: center; text-align: center;position: relative;">

                                                <canvas id="gaugeChartSales" height="50" width="50" style="display: block; box-sizing: border-box; height: 50px; width: 50px;"></canvas> 

                                                 <div style="position: absolute; top: 105px; text-align: center;">
                                                        <text x="150" y="145" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-family="inherit" font-weight="700" fill="#99a1b7" class="apexcharts-text apexcharts-datalabel-label" style="font-family: inherit;">Available Coins</text><br>
                                                                        <text x="150" y="126" text-anchor="middle" dominant-baseline="auto" font-size="24px" font-family="inherit" font-weight="600" fill="#071437" class="apexcharts-text apexcharts-datalabel-value" style="font-family: inherit;">{{ $current_coins_sales ? $current_coins_sales[0]['coins'] : '' }}</text>
                                                    </div>
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>





                                <!--begin::Modal - Upgrade plan-->
                                <div class="modal fade" id="kt_modal_upgrade_plan" tabindex="-1" aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-xl">
                                        <!--begin::Modal content-->
                                        <div class="modal-content rounded">
                                            <!--begin::Modal header-->
                                            <div class="modal-header justify-content-end border-0 pb-0">
                                                <!--begin::Close-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary closemodal"
                                                    data-bs-dismiss="modal">
                                                    <i class="fa fa-times ki-cross fs-1"><span class="path1"></span><span
                                                            class="path2"></span></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->

                                            <!--begin::Modal body-->
                                            <div class="modal-body pt-0 pb-15 px-5 px-xl-20">
                                                <!--begin::Heading-->
                                                <div class="mb-13 text-center">
                                                    <h1 class="mb-3">Upgrade a Plan</h1>

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
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - Upgrade plan--><!--begin::Modal - Create App-->

                                <!--begin::Modal - sales_kt_modal_upgrade_plan Upgrade plan-->
                                <div class="modal fade" id="sales_kt_modal_upgrade_plan" tabindex="-1"
                                    aria-hidden="true">
                                    <!--begin::Modal dialog-->
                                    <div class="modal-dialog modal-xl">
                                        <!--begin::Modal content-->
                                        <div class="modal-content rounded">
                                            <!--begin::Modal header-->
                                            <div class="modal-header justify-content-end border-0 pb-0">
                                                <!--begin::Close-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary closemodal"
                                                    data-bs-dismiss="modal">
                                                    <i class="fa fa-times ki-cross fs-1"><span class="path1"></span><span
                                                            class="path2"></span></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->

                                            <!--begin::Modal body-->
                                            <div class="modal-body pt-0 pb-15 px-5 px-xl-20">
                                                <!--begin::Heading-->
                                                <div class="mb-13 text-center">
                                                    <h1 class="mb-3">Upgrade a Plan</h1>

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
                                            <!--end::Modal body-->
                                        </div>
                                        <!--end::Modal content-->
                                    </div>
                                    <!--end::Modal dialog-->
                                </div>
                                <!--end::Modal - sales_kt_modal_upgrade_plan Upgrade plan--><!--begin::Modal - Create App-->

                            </div>


                           




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
                                        <div class="row px-5 py-5">
                                            <a href="{{ route('service_company.transactions.subscription') }}"
                                                class="btn btn-sm btn-color-primary bg-primary bg-opacity-15 bg-hover-opacity-25 fw-semibold">
                                                View Transaction History
                                            </a>
                                        </div>
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
                                        <div class="row px-5 py-5">
                                            <a href="{{ route('service_company.transactions.subscription') }}"
                                                class="btn btn-sm btn-color-success bg-success bg-opacity-15 bg-hover-opacity-25 fw-semibold">
                                                View Transaction History
                                            </a>
                                        </div>
                                        <!--end top up history-->
                                    </div>
                                </div>
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
        const vctx = document.getElementById('gaugeChart').getContext('2d');

        new Chart(vctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: ["{{$actual_count}}", "{{$remain_coins}}"], // 75% filled
                    backgroundColor: ['#122ef5', '#E2E8F0'], // blue + light gray
                    borderWidth: 0
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '85%',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                }
            }
        });
        
    </script>
     <script>
        const vctxs = document.getElementById('gaugeChartSales').getContext('2d');

        new Chart(vctxs, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: ["{{$actual_count_sales}}", "{{$remain_coins_sales}}"], // 75% filled
                    backgroundColor: ['#24ef21', '#E2E8F0'], // blue + light gray
                    borderWidth: 0
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '85%',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                }
            }
        });
        
    </script>

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
