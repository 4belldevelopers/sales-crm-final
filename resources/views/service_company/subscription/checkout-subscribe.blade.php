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


                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Wrapper-->
                               
                            </div>
                            <!--end::Invoice 2 content-->
                        </div>
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



                            </div>
                            <!--end::Invoice 2 sidebar-->
                        </div>
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
