@extends('layouts.service_company_layout')
@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

@endphp

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
    </style>
@endpush

<?php

$pagetype = request('pagetype');
$operationaltab = 'active';
$salestab = '';
if($pagetype){
    if($pagetype=='operationaltab'){
        $operationaltab = 'active';
        $salestab = '';
    }elseif($pagetype=='salestab'){
        $salestab = 'active';
        $operationaltab = '';
    }
}





?>

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack px-5">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <div class="d-flex flex-column">
                    <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                        <span class="text-gray-800 mb-1">Coins Activity</span>
                    </h5>
                    <span class="text-gray-600" id=""></span>
                </div>
                
            </div>

            <div class="d-flex flex-column">
                <h5 class="d-flex align-items-center text-dark fw-bolder my-1">
                  <a href="{{ url('service_company/subscribe') }}" class="text-gray-800 mb-1">Click to View Coin Usage Table: Predefined Deductions by Activity </a>
                </h5>
           </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-2 pb-0" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">
                <!--begin::card-->
                <div class="card bg-light">
                    <!--begin::card body-->
                    <div class="card-body pt-3 px-0 pb-4">

                        <div class="card shadow-none">

                            <!--begin::Table-->
                            <div class="card-body">

                                <div class="card card-custom mx-2">
                                    <div class="card-header card-header-tabs-line">
                                        <div class="card-toolbar">
                                          <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
                                            <li class="nav-item me-3"> <a class="nav-link {{$operationaltab}}" href="{{url('/service_company/transection-log?pagetype=operationaltab&page=1')}}"> <span class="nav-text font-weight-bold">Operational Coins Activity</span> </a> </li>
                                            <li class="nav-item me-3"> <a class="nav-link {{$salestab}}" href="{{url('/service_company/transection-log?pagetype=salestab&page=1')}}"> <span class="nav-text font-weight-bold">Sales Coins Activity</span> </a> </li>
                                            </ul>
                                        </div>
                                      </div>
                                      

                                      <div class="card-body py-0">
                                        <div class="tab-content">
                                            <div class="tab-pane {{$operationaltab}}" id="salestab" role="tabpanel">
                                                <div class="card-body px-0 py-7">
                                                    <div class="timeline">
                                                        @if(@$coin_transection_logs)

                                                        @foreach($coin_transection_logs as $transection_log)
                                                                <div class="timeline-item">
                                                                    
                                                                    <!--begin::Timeline line-->
                                                                    <div class="timeline-line w-40px"></div>
                                                                    <!--end::Timeline line-->
                                                                    <!--begin::Timeline icon-->
                                                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                                        <div class="symbol-label bg-light">
                                                                            <!--begin::Svg Icon-->
                                                                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                                        fill-rule="evenodd">
                                                                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                                                        <path
                                                                                            d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                            fill="#000000" fill-rule="nonzero" opacity="0.3">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                            fill="#000000" fill-rule="nonzero"></path>
                                                                                    </g>
                                                                                </svg>
                                                                            </span>
                                                                            <!--end::Svg Icon-->
                                                                        </div>
                                                                    </div>
                                                                    <!--end::Timeline icon-->
                                                                    <!--begin::Timeline content-->
                                                                    <div class="timeline-content mb-10 mt-n1">

                                                                        <!--begin::Table-->
                                                                        <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5"
                                                                            id="">
                                                                            <!--begin::Table body-->
                                                                            <tbody class="text-gray-600 fw-bold">
                                                                                <!--begin::Table row-->
                                                                                <tr>
                                                                                    <!--begin::Date-->
                                                                                    <td class="min-w-110px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder fs-6">
                                                                                                {{ Carbon::parse($transection_log->created_at) ->timezone(config('app.timezone')) ->format('d M, Y')  }}
                                                                                                
                                                                                            </span>
                                                                                            <span class="text-muted fw-bold d-block fs-7"> 
                                                                                                {{ Carbon::parse($transection_log->created_at) ->timezone(config('app.timezone')) ->format('h:i A')  }}
                                                    
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    <!--end::Date-->

                                                                                    <td class="min-w-285px" style="width: 285px;">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder fs-6">
                                                                                                {{ $transection_log->event }}
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->event_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>

                                                                                    <!--begin::Status-->
                                                                                    <td class="min-w-150px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <div class="symbol-group symbol-hover">
                                                                                                <div class="symbol symbol-30 symbol-circle"
                                                                                                    data-toggle="tooltip" data-placement="top"
                                                                                                    title="{{ ($transection_log->event_by !=1) ? $transection_log->name  : 'Free' }}" style="    text-align: center;">
                                                                                                    <div class="text-dark fw-bolder fs-6"
                                                                                                        data-bs-placement="top"
                                                                                                        data-toggle="tooltip2" data-html="true"
                                                                                                        title="{{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}">
                                                                                                        {{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}
                                                                                                    </div>
                                                                                                    <span class="text-muted fw-bold d-block fs-7">{{ ($transection_log->event_by !=1) ? $transection_log->company_name : 'Default' }} </span>
                                                                                                </div>
                                                                                                
                                                                                            </div>
                                                                                            
                                                                                            <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                                                                            <!--data-toggle="tooltip" title="User Company2">
                                                                                            User Co </a> -->

                                                                                        </div>
                                                                                    </td>
                                                                                    <!--end::Stage-->

                                                                                
                                                                                    <!--begin::Requisitions=-->
                                                                                    @if($transection_log->settlement_coins <= 0)
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder  fs-6"> 
                                                                                                @if($transection_log->transection_type==='credit')
                                                                                                {{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                                                @else
                                                                                                {{ $transection_log->coins }} Coins
                                                                                                @endif
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    @else
                                                                                    <!--end::Requisitions=-->
                                                                                    <!--begin::Interview Mode=-->
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="fw-bold text-muted d-block fs-7">Settlement</span>
                                                                                            <span class="text-dark fw-bolder  fs-6">
                                                                                                {{ $transection_log->settlement_coins }} Coins
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    @endif
                                                                                    <!--end::Interview Mode=-->
                                                                                    <!--begin::Interviewer Feedback=-->
                                                                                    <td class="min-w-70px">
                                                                                        <div class="d-flex flex-column"> 
                                                                                            <span class="text-dark fw-bolder  fs-6">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                                            <span
                                                                                                class="fw-bold text-muted d-block fs-7">
                                                                                                Balance
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    <!--end::Interviewer Feedback-->
                                                                                </tr>
                                                                                <!--end::Table row-->
                                                                            </tbody>
                                                                            <!--end::Table body-->
                                                                        </table>
                                                                        <!--end::Table-->
                                                                    </div>
                                                                    <!--end::Timeline content-->
                                                                </div>
                                                        @endforeach

                                                        @else
                                                        <div class="text-center">
                                                            <p>No transaction logs available.</p>
                                                        </div>
                                                        @endif
                                                        <x-custom-pagination :paginator="$coin_transection_logs" :extra="['pagetype' => 'operationaltab']" />

                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane {{$salestab}}" id="operationaltab" role="tabpanel">
                                                <div class="card-body px-0 py-7">
                                                    <div class="timeline">
                                                        @if(@$coin_transection_logs_sales)

                                                        @foreach($coin_transection_logs_sales as $transection_log)
                                                                <div class="timeline-item">
                                                                    
                                                                    <!--begin::Timeline line-->
                                                                    <div class="timeline-line w-40px"></div>
                                                                    <!--end::Timeline line-->
                                                                    <!--begin::Timeline icon-->
                                                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                                        <div class="symbol-label bg-light">
                                                                            <!--begin::Svg Icon-->
                                                                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                                        fill-rule="evenodd">
                                                                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                                                        <path
                                                                                            d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                            fill="#000000" fill-rule="nonzero" opacity="0.3">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                                            fill="#000000" fill-rule="nonzero"></path>
                                                                                    </g>
                                                                                </svg>
                                                                            </span>
                                                                            <!--end::Svg Icon-->
                                                                        </div>
                                                                    </div>
                                                                    <!--end::Timeline icon-->
                                                                    <!--begin::Timeline content-->
                                                                    <div class="timeline-content mb-10 mt-n1">

                                                                        <!--begin::Table-->
                                                                        <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5"
                                                                            id="">
                                                                            <!--begin::Table body-->
                                                                            <tbody class="text-gray-600 fw-bold">
                                                                                <!--begin::Table row-->
                                                                                <tr>
                                                                                    <!--begin::Date-->
                                                                                    <td class="min-w-110px">
                                                                                        <div class="d-flex flex-column">
                                                                                            
                                                                                            <span class="text-dark fw-bolder fs-6">
                                                                                                {{ Carbon::parse($transection_log->created_at) ->timezone(config('app.timezone')) ->format('d M, Y')  }}
                                                                                                
                                                                                            </span>
                                                                                            <span class="text-muted fw-bold d-block fs-7"> 
                                                                                                {{ Carbon::parse($transection_log->created_at) ->timezone(config('app.timezone')) ->format('h:i A')  }}
                                                    
                                                                                            </span>
                                                                                            
                                                                                        </div>
                                                                                    </td>
                                                                                    <!--end::Date-->

                                                                                    <td class="min-w-285px"  style="width: 285px;">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder fs-6">
                                                                                                {{ $transection_log->event }}
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->event_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>

                                                                                    <!--begin::Status-->
                                                                                    <td class="min-w-150px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <div class="symbol-group symbol-hover">
                                                                                                <div class="symbol symbol-30 symbol-circle"
                                                                                                    data-toggle="tooltip" data-placement="top"
                                                                                                    title="{{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}" style="    text-align: center;">
                                                                                                    <div class="symbol symbol-30 symbol-circle"
                                                                                                        data-bs-placement="top"
                                                                                                        data-toggle="tooltip2" data-html="true"
                                                                                                        title="{{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}">
                                                                                                        <div class="text-dark fw-bolder fs-6"
                                                                                                        data-bs-placement="top"
                                                                                                        data-toggle="tooltip2" data-html="true"
                                                                                                        title="{{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}">
                                                                                                        {{ ($transection_log->event_by !=1) ? $transection_log->name : 'Free' }}
                                                                                                    </div>
                                                                                                    </div>
                                                                                                    <span class="text-muted fw-bold d-block fs-7">{{ ($transection_log->company_name) ? $transection_log->company_name : 'Default' }} </span>
                                                                                                </div>
                                                                                                
                                                                                            </div>
                                                                                            
                                                                                            <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                                                                            <!--data-toggle="tooltip" title="User Company2">
                                                                                            User Co </a> -->

                                                                                        </div>
                                                                                    </td>
                                                                                    <!--end::Stage-->

                                                                                
                                                                                    <!--begin::Requisitions=-->
                                                                                    @if($transection_log->settlement_coins <= 0)
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="text-dark fw-bolder  fs-6"> 
                                                                                                @if($transection_log->transection_type==='credit')
                                                                                                {{ ($transection_log->actual_coins) ? $transection_log->actual_coins : $transection_log->coins }} Coins
                                                                                                @else
                                                                                                {{ $transection_log->coins }} Coins
                                                                                                @endif
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    @else
                                                                                    <!--end::Requisitions=-->
                                                                                    <!--begin::Interview Mode=-->
                                                                                    <td class="min-w-120px">
                                                                                        <div class="d-flex flex-column">
                                                                                            <span class="fw-bold text-muted d-block fs-7">Settlement</span>
                                                                                            <span class="text-dark fw-bolder  fs-6">
                                                                                                {{ $transection_log->settlement_coins }} Coins
                                                                                            </span>
                                                                                            <span class="fw-bold text-muted d-block fs-7"> 
                                                                                                {{ ucwords($transection_log->transection_type) }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    @endif
                                                                                    <!--end::Interview Mode=-->
                                                                                    <!--begin::Interviewer Feedback=-->
                                                                                    <td class="min-w-70px">
                                                                                        <div class="d-flex flex-column"> 
                                                                                            <span class="text-dark fw-bolder  fs-6">{{ ($transection_log->remain_coins) ? $transection_log->remain_coins : $transection_log->coins }} Coins</span>
                                                                                            <span
                                                                                                class="fw-bold text-muted d-block fs-7">
                                                                                                Balance
                                                                                            </span>
                                                                                        </div>
                                                                                    </td>
                                                                                    <!--end::Interviewer Feedback-->
                                                                                </tr>
                                                                                <!--end::Table row-->
                                                                            </tbody>
                                                                            <!--end::Table body-->
                                                                        </table>
                                                                        <!--end::Table-->
                                                                    </div>
                                                                    <!--end::Timeline content-->
                                                                </div>
                                                        @endforeach

                                                        @else
                                                        <div class="text-center">
                                                            <p>No transaction logs available.</p>
                                                        </div>
                                                        @endif
                                                    
                                                        <x-custom-pagination :paginator="$coin_transection_logs_sales" :extra="['pagetype' => 'salestab']" />
                                                    </div>
                                                </div>
                                            </div>

                                            </div>
                                        </div>          


                            </div>

                            </div>
                            <!--end::Table-->
                        </div>



                        <!--begin::Logins -->
                        <div class="card shadow-none d-none">
                            <!--begin::Header-->
                            <div class="card-header px-7">
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Logins</h3>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-primary align-self-center">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path
                                                    d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z"
                                                    fill="#000000" opacity="0.3" />
                                                <path
                                                    d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span>
                                    See Pricing
                                </a>
                            </div>
                            <!--end::Header-->
                            <!--begin::Table-->
                            <div class="card-body">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="logins_list">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-2">#</th>
                                            <th class="">Package Name</th>
                                            <th class="">Number of users</th>
                                            <th class="">Price</th>
                                            <th class="">Select the duration</th>
                                            <th class="">Number of CV limit</th>
                                            <th class="">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        <tr class="odd">
                                            <td class="invisible">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" class="">
                                                        <div
                                                            class="symbol-label fs-3 bg-dark-light text-light border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->
                                                            <span class="svg-icon svg-icon-dark-800 svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L8,21 C5.790861,21 4,19.209139 4,17 C4,14.790861 5.790861,13 8,13 L20,13 C21.1045695,13 22,13.8954305 22,15 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M15.5421357,5.69999981 L18.3705628,8.52842693 C19.1516114,9.30947552 19.1516114,10.5758055 18.3705628,11.3568541 L9.88528147,19.8421354 C8.3231843,21.4042326 5.79052439,21.4042326 4.22842722,19.8421354 C2.66633005,18.2800383 2.66633005,15.7473784 4.22842722,14.1852812 L12.7137086,5.69999981 C13.4947572,4.91895123 14.7610871,4.91895123 15.5421357,5.69999981 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M5,3 L9,3 C10.1045695,3 11,3.8954305 11,5 L11,17 C11,19.209139 9.209139,21 7,21 C4.790861,21 3,19.209139 3,17 L3,5 C3,3.8954305 3.8954305,3 5,3 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder text-hover-primary fs-6">Free</a>
                                                    </div>
                                                    <span class="badge badge-light-success active_plan">Active Plan</span>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">1</td>
                                            <td class="text-dark fs-6 fw-bolder">Free</td>
                                            <td class="text-dark fs-6 fw-bolder">Free Forever</td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 4em!important;">
                                                30/
                                                Month</td>
                                            <td></td>
                                        </tr>
                                        <tr class="even">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="logins_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                                            <span class="svg-icon svg-icon-success svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Basic</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Basic
                                                        service</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="position-relative w-md-130px" data-kt-dialer="true"
                                                    data-kt-dialer-min="1" data-kt-dialer-max="50000"
                                                    data-kt-dialer-step="1" data-kt-dialer-prefix=""
                                                    data-kt-dialer-decimals="">
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0"
                                                        data-kt-dialer-control="decrease">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="5" fill="black">
                                                                </rect>
                                                                <rect x="6.0104" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="black">
                                                                </rect>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                    <input type="number"
                                                        class="form-control form-control-solid border-0 ps-12"
                                                        style=" padding: 0.5rem;padding-left: 4em!important;"
                                                        data-kt-dialer-control="input" placeholder="Enter users"
                                                        name="users" value="30">
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0"
                                                        data-kt-dialer-control="increase">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="5" fill="black">
                                                                </rect>
                                                                <rect x="10.8891" y="17.8033" width="12"
                                                                    height="2" rx="1"
                                                                    transform="rotate(-90 10.8891 17.8033)"
                                                                    fill="black"></rect>
                                                                <rect x="6.01041" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="black">
                                                                </rect>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group hg-plan-btn-group">
                                                    <button type="button"
                                                        class="btn btn-secondary  hg-plan-btn-secondary dropdown-toggle block-level shared d-flex align-items-center justify-content-between"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        <div class="selected-term" data-term-length="3">
                                                            <span class="term-length">3 Months</span>
                                                        </div>
                                                        <span class="glyphicon glyphicon-chevron-down pull-right"></span>
                                                    </button>
                                                    <ul class="dropdown-menu block-level w-100" role="menu">
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="1">
                                                                <span class="term-length">1 Month</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="3">
                                                                <span class="term-length">3 Months</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="6">
                                                                <span class="term-length">6 Months</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="12">
                                                                <span class="term-length">1 Year</span>
                                                                <div class="testsdfd" style="display:inline-block;">
                                                                    <span
                                                                        class="hg-percentage-discount one-year-percentage">SAVE
                                                                        20%</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="24">
                                                                <span class="term-length">2 Years</span>
                                                                <div class="testsdfd" style="display:inline-block;">
                                                                    <span
                                                                        class="hg-percentage-discount two-year-percentage">SAVE
                                                                        30%</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="36">
                                                                <span class="term-length">3 Years</span>
                                                                <div class="testsdfd" style="display:inline-block;">
                                                                    <span
                                                                        class="hg-percentage-discount three-year-percentage">SAVE
                                                                        30%</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="package-term d-flex align-items-center justify-content-between"
                                                                data-term-length="60">
                                                                <span class="term-length">5 Years</span>
                                                                <div class="testsdfd" style="display:inline-block;">
                                                                    <span
                                                                        class="hg-percentage-discount five-year-percentage">SAVE
                                                                        50%</span>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em!important;">12
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr class="odd">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="logins_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                                            <span class="svg-icon svg-icon-warning svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Premium</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Premium
                                                        service</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="position-relative w-md-130px" data-kt-dialer="true"
                                                    data-kt-dialer-min="1" data-kt-dialer-max="50000"
                                                    data-kt-dialer-step="1" data-kt-dialer-prefix=""
                                                    data-kt-dialer-decimals="">
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0"
                                                        data-kt-dialer-control="decrease">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="5" fill="black">
                                                                </rect>
                                                                <rect x="6.0104" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="black">
                                                                </rect>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                    <input type="number"
                                                        class="form-control form-control-solid border-0 ps-12"
                                                        style=" padding: 0.5rem;padding-left: 4em!important;""
                                                        data-kt-dialer-control="input" placeholder="Enter users"
                                                        name="users" value="120">
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0"
                                                        data-kt-dialer-control="increase">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="5" fill="black">
                                                                </rect>
                                                                <rect x="10.8891" y="17.8033" width="12"
                                                                    height="2" rx="1"
                                                                    transform="rotate(-90 10.8891 17.8033)"
                                                                    fill="black"></rect>
                                                                <rect x="6.01041" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="black">
                                                                </rect>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">6,999 INR</a>
                                                        <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder">10 days left</td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em!important;">30
                                            </td>
                                            <td>
                                                <span class="text-dark fs-6 fw-bolder">Added</span>
                                            </td>
                                        </tr>
                                        <tr class="even">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="logins_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-primary text-primary"></i> -->
                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Pro</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail">Pro
                                                        service</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="position-relative w-md-130px" data-kt-dialer="true"
                                                    data-kt-dialer-min="1" data-kt-dialer-max="50000"
                                                    data-kt-dialer-step="1" data-kt-dialer-prefix=""
                                                    data-kt-dialer-decimals="">
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0"
                                                        data-kt-dialer-control="decrease">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="5" fill="black">
                                                                </rect>
                                                                <rect x="6.0104" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="black">
                                                                </rect>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                    <input type="number"
                                                        class="form-control form-control-solid border-0 ps-12"
                                                        style=" padding: 0.5rem;padding-left: 4em!important;""
                                                        data-kt-dialer-control="input" placeholder="Enter users"
                                                        name="users" value="45">
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0"
                                                        data-kt-dialer-control="increase">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="5" fill="black">
                                                                </rect>
                                                                <rect x="10.8891" y="17.8033" width="12"
                                                                    height="2" rx="1"
                                                                    transform="rotate(-90 10.8891 17.8033)"
                                                                    fill="black"></rect>
                                                                <rect x="6.01041" y="10.9247" width="12"
                                                                    height="2" rx="1" fill="black">
                                                                </rect>
                                                            </svg>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">8,999 INR</a>
                                                        <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder">60 days left</td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em!important;">
                                                120
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add to
                                                    cart</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Logins-->

                        <!--begin::Emails-->
                        <div class="card shadow-none mt-2 d-none">
                            <!--begin::Header-->
                            <div class="card-header px-7">
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Emails</h3>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-primary align-self-center">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http;//www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path
                                                    d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z"
                                                    fill="#000000" opacity="0.3" />
                                                <path
                                                    d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span>
                                    See Pricing
                                </a>
                            </div>
                            <!--end::Header-->
                            <!--begin::Table-->
                            <div class="card-body">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="email_list">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-2">#</th>
                                            <th class="">Package Name</th>
                                            <th class="">Number of emails</th>
                                            <th class="">Price</th>
                                            <th class="">Duration</th>
                                            <th class="">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        <tr class="odd">
                                            <td class="invisible">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input">
                                                </div>
                                            </td>
                                            <td class="align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" class="">
                                                        <div
                                                            class="symbol-label fs-3 bg-dark-light text-light border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->
                                                            <span class="svg-icon svg-icon-dark-800 svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L8,21 C5.790861,21 4,19.209139 4,17 C4,14.790861 5.790861,13 8,13 L20,13 C21.1045695,13 22,13.8954305 22,15 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M15.5421357,5.69999981 L18.3705628,8.52842693 C19.1516114,9.30947552 19.1516114,10.5758055 18.3705628,11.3568541 L9.88528147,19.8421354 C8.3231843,21.4042326 5.79052439,21.4042326 4.22842722,19.8421354 C2.66633005,18.2800383 2.66633005,15.7473784 4.22842722,14.1852812 L12.7137086,5.69999981 C13.4947572,4.91895123 14.7610871,4.91895123 15.5421357,5.69999981 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M5,3 L9,3 C10.1045695,3 11,3.8954305 11,5 L11,17 C11,19.209139 9.209139,21 7,21 C4.790861,21 3,19.209139 3,17 L3,5 C3,3.8954305 3.8954305,3 5,3 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder text-hover-primary fs-6">Free</a>
                                                    </div>
                                                    <span class="badge badge-light-success active_plan">Active Plan</span>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">1</td>
                                            <td class="text-dark fs-6 fw-bolder">Free</td>
                                            <td class="text-dark fs-6 fw-bolder">Free Forever</td>
                                            <td></td>
                                        </tr>
                                        <tr class="even">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="email_plan">
                                                </div>
                                            </td>
                                            <td class="align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                                            <span class="svg-icon svg-icon-success svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Basic</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Basic
                                                        service</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">5</td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder">
                                                <span style="padding-left:1.6em;">5</span>
                                            </td>
                                            <td>
                                                <span class="text-dark fs-6 fw-bolder">Added</span>
                                            </td>
                                        </tr>
                                        <tr class="odd">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="email_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                                            <span class="svg-icon svg-icon-warning svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Premium</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Premium
                                                        service</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">56</td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">6,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder">
                                                <span style="padding-left:1.6em;">6</span>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add to
                                                    cart</a>
                                            </td>
                                        </tr>
                                        <tr class="even">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="email_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-primary text-primary"></i> -->
                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Pro</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail">Pro
                                                        service</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">32</td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">8,999 INR</a>
                                                        <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder">
                                                <span style="padding-left:1.6em;">2</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Emails-->

                        <!--begin::Resume access-->
                        <div class="card shadow-none mt-2 d-none">
                            <!--begin::Header-->
                            <div class="card-header px-7">
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Resume access</h3>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-primary align-self-center">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http;//www.w3.org/1999/xlink" width="24px" height="24px"
                                            viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path
                                                    d="M5.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L5.5,11 C4.67157288,11 4,10.3284271 4,9.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M11,6 C10.4477153,6 10,6.44771525 10,7 C10,7.55228475 10.4477153,8 11,8 L13,8 C13.5522847,8 14,7.55228475 14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 Z"
                                                    fill="#000000" opacity="0.3" />
                                                <path
                                                    d="M5.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M11,15 C10.4477153,15 10,15.4477153 10,16 C10,16.5522847 10.4477153,17 11,17 L13,17 C13.5522847,17 14,16.5522847 14,16 C14,15.4477153 13.5522847,15 13,15 L11,15 Z"
                                                    fill="#000000" />
                                            </g>
                                        </svg>
                                    </span>
                                    See Pricing
                                </a>
                            </div>
                            <!--end::Header-->
                            <!--begin::Table-->
                            <div class="card-body">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="resume_list">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-2">#</th>
                                            <th class="">Package Name</th>
                                            <th class="">Number of resume access</th>
                                            <th class="">Price</th>
                                            <th class="">Duration</th>
                                            <th class="">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        <tr class="odd">
                                            <td class="invisible">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" class="">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-danger text-danger"></i> -->
                                                            <span class="svg-icon svg-icon-dark-800 svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M22,15 L22,19 C22,20.1045695 21.1045695,21 20,21 L8,21 C5.790861,21 4,19.209139 4,17 C4,14.790861 5.790861,13 8,13 L20,13 C21.1045695,13 22,13.8954305 22,15 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M15.5421357,5.69999981 L18.3705628,8.52842693 C19.1516114,9.30947552 19.1516114,10.5758055 18.3705628,11.3568541 L9.88528147,19.8421354 C8.3231843,21.4042326 5.79052439,21.4042326 4.22842722,19.8421354 C2.66633005,18.2800383 2.66633005,15.7473784 4.22842722,14.1852812 L12.7137086,5.69999981 C13.4947572,4.91895123 14.7610871,4.91895123 15.5421357,5.69999981 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M5,3 L9,3 C10.1045695,3 11,3.8954305 11,5 L11,17 C11,19.209139 9.209139,21 7,21 C4.790861,21 3,19.209139 3,17 L3,5 C3,3.8954305 3.8954305,3 5,3 Z M7,19 C8.1045695,19 9,18.1045695 9,17 C9,15.8954305 8.1045695,15 7,15 C5.8954305,15 5,15.8954305 5,17 C5,18.1045695 5.8954305,19 7,19 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);"
                                                            class="text-dark fw-bolder text-hover-primary fs-6">Free</a>
                                                    </div>
                                                    <span class="badge badge-light-success active_plan">Active Plan</span>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">1</td>
                                            <td class="text-dark fs-6 fw-bolder">Free</td>
                                            <td class="text-dark fs-6 fw-bolder">Free Forever</td>
                                            <td></td>
                                        </tr>
                                        <tr class="even">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="resume_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-success text-success border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-success text-success"></i> -->
                                                            <span class="svg-icon svg-icon-success svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Basic</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Basic
                                                        service</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">45</td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">4,999 INR</span>
                                                    <span class="text-muted fw-bold d-block fs-7">Per month</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark fs-6 fw-bolder"
                                                    style="padding-left:.5em;">5</span>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add to
                                                    cart</a>
                                            </td>
                                        </tr>
                                        <tr class="odd">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="resume_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-warning text-warning"></i> -->
                                                            <span class="svg-icon svg-icon-warning svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Premium</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-application-detail">Premium
                                                        service</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">25</td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">6,999 INR</a>
                                                        <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder">
                                                <span style="padding:left:.5em;">3</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr class="even">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" value="1"
                                                        name="resume_plan">
                                                </div>
                                            </td>
                                            <td class=" align-items-center">
                                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                                    style="float:left">
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="show-application-detail">
                                                        <div
                                                            class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">
                                                            <!-- <i class="fab fa-buffer fs-3 bg-light-primary text-primary"></i> -->
                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                                        <path
                                                                            d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                        <path
                                                                            d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="">
                                                        <a href="javascript:void(0);" data-row-id="806"
                                                            class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail">Pro</a>
                                                    </div>
                                                    <a href="javascript:void(0);" data-row-id="806"
                                                        class="text-muted fw-bold d-block fs-7 text-hover-primary show-application-detail">Pro
                                                        service</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: 5em;">20</td>
                                            <td class="">
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6">8,999 INR</a>
                                                        <span class="text-muted fw-bold d-block fs-7">Per month</a>
                                                </div>
                                            </td>
                                            <td class="text-dark fs-6 fw-bolder" style="padding-left: .5em!important;">2
                                            </td>
                                            <td>
                                                <span class="text-dark fs-6 fw-bolder">Added</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                        </div>
                        <!--end::Resume access-->

                        <!--begin::Bench activation-->
                        <!--end::Bench activation-->
                    </div>
                    <!--end::card body-->
                </div>
                <!--end::card-->
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

        elems.forEach(function(html) {
            var switchery = new Switchery(html, {
                size: 'medium'
            });
        });

        $('#package-switch').change(function() {
            let checked = $(this).is(":checked");
            if (checked) {
                $('.monthly-packages').hide();
                $('.annual-packages').fadeIn('slow');
            } else {
                $('.annual-packages').hide();
                $('.monthly-packages').fadeIn('slow');
            }
        })
    </script>
@endpush
