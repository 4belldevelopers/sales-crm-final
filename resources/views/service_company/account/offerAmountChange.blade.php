@extends('layouts.service_company_layout')
@php
    use App\Helper\GeneralMethod;
@endphp

<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">

<div class="card shadow-none rounded-0 w-100" id="application_show_right_bar">
    <style>
        .text-justify {
            text-align: justify !important;
        }

        @media only screen and (min-width: 900px) and (max-width: 2560px) {

            .table td,
            .table th,
            .table tr {
                width: max-content !important;
            }

            .table> :not(caption)>*>* {
                padding: 0.75em 0rem !important;
            }

            td:nth-child(n+2),
            th:nth-child(n+2) {
                width: revert !important;
                border-bottom: 1px dashed #f5f8fa !important;
            }
        }

        /* .swal2-html-container{
        text-align: left !important;
        } */

        /* Basic styling for the step container */
        .tab-container {
            max-width: 600px;
            margin: auto;
        }

        /* Hide all tabs by default */
        .tab-content-section {
            display: none;
        }

        /* Display the active tab */
        .tab-content-section.active {
            display: block;
        }

        /* Button styling */
        button {
            padding: 10px 20px;
            margin: 10px 5px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .prev-btn {
            background-color: #6c757d;
        }

        .prev-btn:hover {
            background-color: #5a6268;
        }

        .submit-btn {
            background-color: #28a745;
        }

        .submit-btn:hover {
            background-color: #218838;
        }

        .offer-details table {
            width: 100%;
            /* All tables will be 100% of the container width */
            border-collapse: collapse;
            /* Make the borders collapse for a cleaner look */
        }
    </style>
    <script>
        $("timeline-item:last-child").addClass("timeline-item:last-child");
    </script>

    <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
    <script>
        if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)

            CKEDITOR.tools.enableHtml5Elements(document);
        CKEDITOR.config.height = 350;
        CKEDITOR.config.width = 'auto';

        var initSample = (function() {
            var wysiwygareaAvailable = isWysiwygareaAvailable(),
                isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

            return function() {
                var editorElement = CKEDITOR.document.getById('editor1');

                if (isBBCodeBuiltIn) {
                    editorElement.setHtml(
                        'Hello world!\n\n' +
                        'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                    );
                }
                if (wysiwygareaAvailable) {
                    CKEDITOR.replace('editor1');
                } else {
                    editorElement.setAttribute('contenteditable', 'true');
                    CKEDITOR.inline('editor1');
                }
            };

            function isWysiwygareaAvailable() {
                if (CKEDITOR.revision == ('%RE' + 'V%')) {
                    return true;
                }
                return !!CKEDITOR.plugins.get('wysiwygarea');
            }

        })();


        initSample();
    </script>

@section('content')
    @php
        $currencyArr = ['INR', 'EUR', 'JPY', 'GBP', 'USD', 'AED'];
        $resume_url = '<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';
        $photo = ''; //url('public/avatar.png');
        if (!empty($application->photo)) {
            $photo = $application->photo;
        }
        $country_id = $country_name = $state_name = $state = $city_name = '';
        $country_id = @$application->country;
        $state = @$application->state;
        if (@$country_id > 0) {
            $country_name_Arr = GeneralMethod::getCountryName($country_id);
            foreach ($country_name_Arr as $cval) {
                $country_name = ucwords($cval['name']);
            }

            if ($country_id == 101) {
                $state_name = @$application->state_name;
                $city_name = @$application->city_name;
            } else {
                if ($state > 0) {
                    $state_name_Arr = GeneralMethod::getStateName($country_id, $state);
                    foreach ($state_name_Arr as $sval) {
                        $state_name = ucwords($sval['name']);
                    }
                }
                $city_name = @$application->city;
            }
            $currencySymbole = GeneralMethod::currencySymbole();
            $current_ctc_cur = $current_ectc_cur = '';
            if ($application->condidate_type == 'other') {
                $current_ctc_cur = $currencySymbole[@$application->current_ctc_cur];
                $current_ectc_cur = $currencySymbole[@$application->current_ectc_cur];
            } else {
                $current_ctc_cur = $currencySymbole[@$application->bill_rate_currency];
                $current_ectc_cur = $currencySymbole[@$application->bill_rate_currency];
            }
        }
        //  rjobid from_company_id
        $currency = '';
        $salary_ctc = '';
        $salary_mode = '';
        $designation = '';
        $joined_date = '';
        $monthly_gross = '';
        $yearly_gross = '';
        $monthly_bounus_gross = '';
        $yearly_bonus_gross = '';
        $monthly_insurence_gross = '';
        $yearly_insurence_gross = '';
        $yearly_final_gross = '';
        $monthly_final_gross = '';
        $employment_type = '';
        $view_type = '';

        if (isset($OfferJobApplication) && count($OfferJobApplication) > 0) {
            foreach ($OfferJobApplication as $offer) {
                $currency = $offer['currency'];
                $employment_type = $offer['employment_type'];
                $salary_ctc = $offer['salary_ctc'];
                $salary_mode = $offer['salary_mode'];
                $designation = $offer['designation'];
                $joined_date = $offer['joined_date'];
                $monthly_gross = $offer['monthly_gross'];
                $yearly_gross = $offer['yearly_gross'];
                $monthly_bounus_gross = $offer['monthly_bounus_gross'];
                $yearly_bonus_gross = $offer['yearly_bonus_gross'];
                $monthly_insurence_gross = $offer['monthly_insurence_gross'];
                $yearly_insurence_gross = $offer['yearly_insurence_gross'];
                $yearly_final_gross = $offer['yearly_final_gross'];
                $monthly_final_gross = $offer['monthly_final_gross'];
            }
        }

    @endphp
    <div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Add Requisition-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);" class="text-gray-800 mb-1">Salary Structure</a></h1>
      </div>
      <!--end::Add Requisition-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <!--end::Separator-->
        <span class="text-dark-800 fw-bold"  id="kt_subheader_total">
            <a href="{{ url()->previous() }}" onclick="event.preventDefault(); history.back();"> Go Back </a>
        </span>
    </div>
    <!--end::Page title-->
  </div>
  <!--end::Container-->
</div>

    <div class="card-body position-relative px-0 py-0" id="kt_offercreate_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
            data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
            data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
            <div class="card mb-5 mb-xl-5 mx-2">
                <div class="card-body pt-5 pb-0 px-5">

                    <div clas <div class="card card-custom mx-2">

                        <div class="card-body py-0">


                            <!-- Step 1 -->
                            <div class="tab-content-section pt-5 active" id="step1">
                                <div class="tab-content pt-5">
                                    <form class="ajax-form" method="POST" id="createForm" name="createForm"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                            <input type="hidden" name="offer_id" value="{{ $offer_id }}">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-4 pe-0">
                                                        <label class="form-label required">Select Currency:</label>
                                                        <select class="form-select form-select-solid"
                                                            arial-label="Select Currency" id="current_ctc_cur"
                                                            name="current_ctc_cur">
                                                            <option value="0"
                                                                {{ $currency == 0 ? 'selected' : '' }}>INR</option>
                                                            <option value="1"
                                                                {{ $currency == 1 ? 'selected' : '' }}>EUR</option>
                                                            <option value="2"
                                                                {{ $currency == 2 ? 'selected' : '' }}>JPY</option>
                                                            <option value="3"
                                                                {{ $currency == 3 ? 'selected' : '' }}>GBP</option>
                                                            <option value="4"
                                                                {{ $currency == 4 ? 'selected' : '' }}>USD</option>
                                                            <option value="5"
                                                                {{ $currency == 5 ? 'selected' : '' }}>AED</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-4 px-2">
                                                        <label class="form-label required">Enter Amount:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            name="ctc" id="ctc"
                                                            placeholder="Enter current CTC"
                                                            value="{{ @$salary_ctc }}">
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                        </div>
                                                    </div>
                                                    <div class="col-4 pl-0">
                                                        <label class="form-label required">Select Salary Format:</label>
                                                        <select class="form-select form-select-solid"
                                                            arial-label="Select Annual Salary" id="current_ctc_mode"
                                                            name="current_ctc_mode">
                                                            <option value="2" {{ $salary_mode == 2 ? 'selected' : '' }}>Hourly</option>
                                                            <option value="1" {{ $salary_mode == 1 ? 'selected' : '' }}>Monthly</option>
                                                            <option value="3" {{ $salary_mode == 3 ? 'selected' : '' }}>Annual</option>
                                                            <option value="0" {{ empty($salary_mode) || $salary_mode == 0 ? 'selected' : '' }}>Monthly/Annual</option>
                                                            <option value="4" {{ $salary_mode == 4 ? 'selected' : '' }}>Hourly/Annual</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-4 pe-0">
                                                        <label class="form-label required">Enter Offer Designation:</label>
                                                        <input type="text" class="form-control form-control-solid"
                                                            name="current_designation" id="current_designation"
                                                            placeholder="Enter designation"
                                                            value="{{ @$designation }}">
                                                    </div>
                                                    <div class="col-4 px-2">
                                                        <label class="form-label">Select Date & Time for Joining:</label>
                                                        <input type="datetime-local"
                                                            class="form-control form-control-solid" name="dob"
                                                            id="dob" placeholder="Enter dob"
                                                            value="{{ @$joined_date }}" onclick="this.showPicker()">
                                                    </div>
                                                    <div class="col-4 pe-0">
                                                        <label class="form-label required">Select Job Type</label>
                                                        <select class="form-select form-select-solid"
                                                            arial-label="Select Job Type" id="employment_type"
                                                            name="employment_type">
                                                            <option value="0"
                                                                {{ $employment_type == 0 ? 'selected' : '' }}>Full-time
                                                            </option>
                                                            <option value="1"
                                                                {{ $employment_type == 1 ? 'selected' : '' }}>Contract
                                                            </option>
                                                            <option value="3"
                                                            {{ $employment_type == 3 ? 'selected' : '' }}>Part Time
                                                        </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">

                                                <div class="row">
                                                    <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList"
                                                        role="grid" aria-describedby="myTable_info"
                                                        style="width: 1270px;">
                                                        <thead>
                                                            <tr class="fw-bolder text-muted other_section {{ $section_other }}"
                                                                role="row">
                                                                <th class="sorting_disabled" style="width: 25%!important">Components
                                                                </th>
                                                                <th class="sorting_disabled"
                                                                    style="width:10% !important">View</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 7% !important;">Percentage</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 3% !important;">&nbsp;</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 10% !important;">Condition</th>
                                                                <th class="sorting_disabled change_text_first"
                                                                    style="width: 17% !important;">Monthly</th>
                                                                <th class="sorting_disabled"
                                                                    style="width:15% !important">Annually</th>
                                                                <th class="sorting_disabled">Action</th>
                                                            </tr>
                                                            <tr class="fw-bolder text-muted hourly_section {{ $section_hourly }}"
                                                                role="row">
                                                                <th class="sorting_disabled"
                                                                    style="width: 47% !important;">Components</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 12% !important;">Percentage</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 3% !important;">&nbsp;</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 10% !important;">Condition</th>
                                                                <th class="sorting_disabled change_text"
                                                                    style="width:15% !important">Hourly</th>
                                                                <th class="sorting_disabled" style="width: 78px;">
                                                                    Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>


                                                            @if (isset($OfferJobApplication) && count($OfferJobApplication) > 0)
                                                                @foreach ($OfferJobApplication as $key=> $offer)
                                                                    @foreach ($offer['Basic_offer_points'] as $key => $basic)
                                                                        <tr class="other_section other_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_other }}"
                                                                            role="row">
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_points"
                                                                                    name="offer_point[]"
                                                                                    id="offer_point_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_poins }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class="">
                                                                                <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $basic->id }}" data-id="{{ $basic->id }}"
                                                                                data-offerid="{{ $basic->id }}"
                                                                                style="width: 100px;display: initial; ">
                                                                                <option value="0" {{ $basic->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                <option value="1" {{ $basic->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                <option value="2" {{ $basic->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                </select>
                                                                            </td>

                                                                            <td class="">

                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid option_percentage"
                                                                                    name="option_percentage[]"
                                                                                    id="option_percentage_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_percentage }}"
                                                                                    placeholder="%">


                                                                            </td>
                                                                            <td class="" style="position: relative;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                                                            <td class="">
                                                                                <select &nbsp=""
                                                                                    class="form-select form-select-solid option_salarymode"
                                                                                    arial-label="Select Annual Salary"
                                                                                    name="option_salarymode[]"
                                                                                    id="option_salarymode_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    style="width: 100px;display: initial; ">
                                                                                    <option value="0"
                                                                                        {{ $basic->basic_condition_s == 0 ? 'selected' : '' }}>
                                                                                        Amount </option>
                                                                                        @if($key>0)
                                                                                        <option value="1"
                                                                                        {{ $basic->basic_condition_s == 1 ? 'selected' : '' }}>
                                                                                        Basic</option>
                                                                                        @endif
                                                                                </select>
                                                                            </td>
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_monthly"
                                                                                    name="offer_monthly[]"
                                                                                    id="offer_monthly_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_monthly }}"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_yearly {{ $key > 0 ? 'other_salary_get' : 'basic_salary_get' }}"
                                                                                    name="offer_yearly[]"
                                                                                    id="offer_yearly_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_yearly }}"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td style="float:right">
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points"
                                                                                    {{ $key > 0 ? '' : 'disabled' }}>
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon-2x">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px"
                                                                                            height="24px"
                                                                                            viewBox="0 0 24 24"
                                                                                            version="1.1">
                                                                                            <g stroke="none"
                                                                                                stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000"
                                                                                                    x="4" y="11"
                                                                                                    width="16"
                                                                                                    height="2"
                                                                                                    rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span> Delete </button>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="hourly_section hourly_section_row row-template {{ $key > 0 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                                                            role="row">
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_point_hourly"
                                                                                    name="offer_point_hourly[]"
                                                                                    id="offer_point_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_poins }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class="">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid option_percentage_hourly"
                                                                                    name="option_percentage_hourly[]"
                                                                                    id="option_percentage_hourly{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_percentage }}"
                                                                                    placeholder="%">

                                                                                {{-- <select &nbsp=""
                                                                                    class="form-select form-select-solid option_percentage_hourly"
                                                                                    arial-label="Select Percentage"
                                                                                    name="option_percentage_hourly[]" id="option_percentage_hourly{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}" data-offerid="{{ $basic->id }}"
                                                                                    style="width: 100px;display: initial; ">
                                                                                    <option value="">Select</option>
                                                                                    @php

                                                                                    for($i=0; $i<100; $i++){
                                                                                        @endphp 
                                                                                        <option value="{{ $i }}" {{ ($basic->basic_percentage==$i) ? 'selected'  : '' }}>{{ ($i==0) ? 'Custom' : $i.'%' }}</option>
                                                                                        @php    
                                                                                    }
                                                                                    @endphp 
                                                                                                                                            
                                                                                </select> --}}
                                                                            </td>
                                                                            <td class="">
                                                                                <select &nbsp=""
                                                                                    class="form-select form-select-solid option_salarymode_hourly"
                                                                                    arial-label="Select Annual Salary"
                                                                                    name="option_salarymode_hourly[]"
                                                                                    id="option_salarymode_hourly{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    style="width: 100px;display: initial; ">
                                                                                    <option value="0"
                                                                                        {{ $basic->basic_condition_s == 0 ? 'selected' : '' }}>
                                                                                        Amount </option>
                                                                                    <option value="1"
                                                                                        {{ $basic->basic_condition_s == 1 ? 'selected' : '' }}>
                                                                                        Basic</option>
                                                                                </select>
                                                                            </td>
                                                                            <td class="hourly_section">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_hourly {{ $key > 0 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                                                                    name="offer_hourly[]"
                                                                                    id="offer_hourly_{{ $basic->id }}"
                                                                                    data-id="{{ $basic->id }}"
                                                                                    data-offerid="{{ $basic->id }}"
                                                                                    value="{{ $basic->basic_yearly }}"
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td style="float:right">
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points"
                                                                                    {{ $key > 0 ? '' : 'disabled' }}>
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon-2x">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px"
                                                                                            height="24px"
                                                                                            viewBox="0 0 24 24"
                                                                                            version="1.1">
                                                                                            <g stroke="none"
                                                                                                stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000"
                                                                                                    x="4" y="11"
                                                                                                    width="16"
                                                                                                    height="2"
                                                                                                    rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span> Delete </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            @else
                                                                @if ($offer_points)
                                                                    @foreach ($offer_points as $key => $value)
                                                                        <tr class="other_section other_section_row row-template {{ $key > 1 ? 'remove_row' : '' }} {{ $section_other }}"
                                                                            role="row">
                                                                            <td class="" style="width: 25%!important">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_points"
                                                                                    name="offer_point[]"
                                                                                    id="offer_point_{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="{{ $value->points }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class="" style="width:10% !important">
                                                                                <select &nbsp="" class="form-select form-select-solid basic_view" arial-label="Select  View" name="basic_view[]" id="basic_view_{{ $value->id }}" data-id="{{ $value->id }}"
                                                                                data-offerid="{{ $value->id }}"
                                                                                style="width: 100%;display: initial; ">
                                                                                <option value="0" {{ $value->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                <option value="1" {{ $value->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                <option value="2" {{ $value->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                </select>
                                                                            </td>

                                                                            <td class="" style="width:7% !important">

                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid option_percentage"
                                                                                    name="option_percentage[]"
                                                                                    id="option_percentage_{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="{{ $value->percentage }}"
                                                                                    placeholder="%">
                                                                            </td>
                                                                            <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> </td>
                                                                            <td class="" style="width:10% !important">
                                                                                <select &nbsp=""
                                                                                    class="form-select form-select-solid option_salarymode"
                                                                                    arial-label="Select Annual Salary"
                                                                                    name="option_salarymode[]"
                                                                                    id="option_salarymode_{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0"
                                                                                        selected="selected">Amount
                                                                                    </option>
                                                                                     @if($key>0)
                                                                                       <option value="1">Basic</option>
                                                                                     @endif
                                                                                </select>
                                                                            </td>
                                                                            <td class="" style="width:17% !important">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_monthly"
                                                                                    name="offer_monthly[]"
                                                                                    id="offer_monthly_{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="" placeholder="0.00">
                                                                            </td>
                                                                            <td class="" style="width:15% !important">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_yearly {{ $key > 1 ? 'other_salary_get' : 'basic_salary_get' }}"
                                                                                    name="offer_yearly[]"
                                                                                    id="offer_yearly_{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="" placeholder="0.00">
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0"
                                                                                    {{ $key > 0 ? '' : 'disabled' }}>
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px"
                                                                                            height="24px"
                                                                                            viewBox="0 0 24 24"
                                                                                            version="1.1">
                                                                                            <g stroke="none"
                                                                                                stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000"
                                                                                                    x="4" y="11"
                                                                                                    width="16"
                                                                                                    height="2"
                                                                                                    rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span>
                                                                                    
                                                                                </button>

                                                                                <button type="button" id="addRowBtn" class="btn btn-primary addRowBtn me-0" style="">
                                                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                                <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"></rect>
                                                                                                <rect fill="#000000" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span>
                                                                                    </button>

                                                                            </td>
                                                                            
                                                                        </tr>
                                                                     @endforeach
                                                                @endif
                                                                @if ($offer_point_hourly)
                                                                    @foreach ($offer_point_hourly as $key=> $value)
                                                                        <tr class="hourly_section hourly_section_row row-template {{ $key > 1 ? 'remove_row' : '' }} {{ $section_hourly }}"
                                                                            role="row">
                                                                            <td class=""
                                                                                style="width: 47% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_point_hourly"
                                                                                    name="offer_point_hourly[]"
                                                                                    id="offer_point_hourly{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="{{ $value->points }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class=""
                                                                                style="width: 12% !important;">

                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid option_percentage_hourly"
                                                                                    name="option_percentage_hourly[]"
                                                                                    id="option_percentage_hourly{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="{{ $value->percentage }}"
                                                                                    placeholder="%">
                                                                            </td>
                                                                            <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                            </td>
                                                                            <td class=""
                                                                                style="width: 10% !important;">
                                                                                <select &nbsp=""
                                                                                    class="form-select form-select-solid option_salarymode_hourly"
                                                                                    arial-label="Select Annual Salary"
                                                                                    name="option_salarymode_hourly[]"
                                                                                    id="option_salarymode_hourly{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0"
                                                                                        selected="selected">Amount
                                                                                    </option>
                                                                                    @if($key>0)
                                                                                    <option value="1">Basic
                                                                                    </option>
                                                                                    @endif
                                                                                </select>
                                                                            </td>
                                                                            <td class=""
                                                                                style="width: 15% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid offer_hourly {{ $key > 1 ? 'other_salary_get_hourly' : 'basic_salary_get_hourly' }}"
                                                                                    name="offer_hourly[]"
                                                                                    id="offer_hourly{{ $value->id }}"
                                                                                    data-id="{{ $value->id }}"
                                                                                    data-offerid="{{ $value->id }}"
                                                                                    value="" placeholder="0.00">
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0"
                                                                                    {{ $key > 0 ? '' : 'disabled' }}>
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px"
                                                                                            height="24px"
                                                                                            viewBox="0 0 24 24"
                                                                                            version="1.1">
                                                                                            <g stroke="none"
                                                                                                stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000"
                                                                                                    x="4" y="11"
                                                                                                    width="16"
                                                                                                    height="2"
                                                                                                    rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span>
                                                                                </button>
                                                                                <button type="button" id=""
                                                                                    class="btn btn-primary addRowBtn me-0" style="">
                                                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px" height="24px"
                                                                                            viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000" x="4" y="11"
                                                                                                    width="16" height="2"
                                                                                                    rx="1"></rect>
                                                                                                <rect fill="#000000" 
                                                                                                    transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                    x="4" y="11" width="16"
                                                                                                    height="2" rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span>
                                                                                </button>

                                                                            </td>
                                                                            
                                                                        </tr>
                                                                    @endforeach
                                                                @endif

                                                            @endif


                                                        </tbody>


                                                        <tfoot class="other_section {{ $section_other }}">
                                                            <tr>
                                                                <th colspan="8" class="sorting_disabled"
                                                                    style="width: 102px;">&nbsp; </th>
                                                                
                                                            </tr>
                                                            <tr>
                                                                <th colspan="2" class="sorting_disabled">
                                                                    <input type="text" class="form-control form-control-solid" value="Total Gross Amount" readonly style="cursor: not-allowed;background: #ffeb0066;">
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalpercentage"
                                                                        name="totalpercentage"
                                                                        value=""
                                                                        id="totalpercentage" style="background: #ffeb0066;">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled">
                                                                    <input &nbsp="" type="text"
                                                                    class="form-control form-control-solid show_currency"
                                                                    name="show_currency"
                                                                    value="" readonly style="cursor: not-allowed;background: #ffeb0066;"></th>
                                                                <th class="sorting_disabled">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalMonthly"
                                                                        name="totalMonthly"
                                                                        value="{{ @$monthly_gross }}"
                                                                        id="totalMonthly" style="background: #ffeb0066;">
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalYearly"
                                                                        name="totalYearly"
                                                                        value="{{ @$yearly_gross }}"
                                                                        id="totalYearly" style="background: #ffeb0066;">
                                                                </th>
                                                                <th class="sorting_disabled">&nbsp;</th>
                                                            </tr>
                                                        </tfoot>

                                                        <tfoot class="hourly_section {{ $section_hourly }}">
                                                            <tr>
                                                                <th colspan="5" class="sorting_disabled"
                                                                    style="width: 102px;">&nbsp; </th>
                                                            </tr>
                                                            <tr>
                                                                <th class="sorting_disabled">
                                                                    <input type="text" class="form-control form-control-solid" value="Total Gross Amount" readonly style="cursor: not-allowed;background: #ffeb0066;">
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalpercentagehouly"
                                                                        name="totalpercentagehouly" value="" id="totalpercentagehouly" style="background: #ffeb0066;">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled">
                                                                    <input &nbsp="" type="text"
                                                                    class="form-control form-control-solid show_currency"
                                                                    name="show_currency"
                                                                    value="" readonly style="cursor: not-allowed;background: #ffeb0066;"></th>
                                                                <th class="sorting_disabled" colspan="2">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalhourlyhourly"
                                                                        name="totalhourlyhourly"
                                                                        value="{{ @$yearly_gross }}"
                                                                        id="totalhourlyhourly" style="width: 88%;background: #ffeb0066;">
                                                                </th>
                                                            </tr>
                                                        </tfoot>




                                                    </table>
                                                </div>

                                                <div class="row">
                                                    <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList"
                                                        role="grid" aria-describedby="bonusList_info"
                                                        style="width: 1270px;">
                                                        <thead>
                                                            <tr class="other_section other_section_row row-template {{ $section_other }}">
                                                                <th colspan="7" class="sorting_disabled"
                                                                    style="width: 102px;">Other Component</th>
                                                                <th class="sorting_disabled"
                                                                    style="width: 102px;position: relative;">
                                                                    <button type="button" id="addRowBtnbonus"
                                                                    class="btn btn-primary addRowBtnbonus me-0" style="position: absolute; top: -25px; right: 16px;">
                                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                            width="24px" height="24px"
                                                                            viewBox="0 0 24 24" version="1.1">
                                                                            <g stroke="none" stroke-width="1"
                                                                                fill="none"
                                                                                fill-rule="evenodd">
                                                                                <rect fill="#000000" x="4" y="11"
                                                                                    width="16" height="2"
                                                                                    rx="1"></rect>
                                                                                <rect fill="#000000"
                                                                                    transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                    x="4" y="11" width="16"
                                                                                    height="2" rx="1">
                                                                                </rect>
                                                                            </g>
                                                                        </svg>
                                                                    </span>
                                                                </button>
                                                                </th>
                                                            </tr>
                                                            <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}">
                                                                <th colspan="6" class="sorting_disabled other_text"
                                                                    style="width: 102px;">Other Component</th>

                                                                    <th class="sorting_disabled"
                                                                    style="width: 102px;position: relative;">
                                                                    <button type="button" id=""
                                                                        class="btn btn-primary me-0 addRowBtnbonus" style="position: absolute; top: -25px; right: 16px;">
                                                                            <span class="svg-icon svg-icon svg-icon me-0">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                    width="24px" height="24px"
                                                                                    viewBox="0 0 24 24" version="1.1">
                                                                                    <g stroke="none" stroke-width="1"
                                                                                        fill="none"
                                                                                        fill-rule="evenodd">
                                                                                        <rect fill="#000000" x="4" y="11"
                                                                                            width="16" height="2"
                                                                                            rx="1"></rect>
                                                                                        <rect fill="#000000" 
                                                                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                            x="4" y="11" width="16"
                                                                                            height="2" rx="1">
                                                                                        </rect>
                                                                                    </g>
                                                                                </svg>
                                                                            </span>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                        </thead>


                                                        <tbody>
                                                            @if(isset($offer_point_other) && count($offer_point_other) > 0)
                                                                    @foreach ($offer_point_other as $offer)
                                                                        <tr class="other_section other_section_row row-template remove_row {{ $section_other }}"
                                                                            role="row">
                                                                            <td class="" style="width: 25% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_points"
                                                                                    name="bonus_points[]"
                                                                                    id="bonus_points_{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value="{{ $offer->points }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class="" style="width: 10% !important;">
                                                                                <select &nbsp="" class="form-select form-select-solid bonus_view" arial-label="Select  View" name="bonus_view[]" id="bonus_view_{{ $offer->id }}" data-id="{{ $offer->id }}"
                                                                                data-offerid="{{ $offer->id }}"
                                                                                style="width: 100%;display: initial; ">
                                                                                <option value="0" {{ @$offer->view_type == 0 ? 'selected' : '' }}> Annually </option>
                                                                                <option value="1" {{ @$offer->view_type == 1 ? 'selected' : '' }}> Monthly</option>
                                                                                <option value="2" {{ @$offer->view_type == 2 ? 'selected' : '' }}> Both</option>
                                                                                </select>
                                                                            </td>


                                                                            <td class="" style="width: 7% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid bonus_option_percentage"
                                                                                    name="bonus_option_percentage[]"
                                                                                    id="bonus_option_percentage{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value="{{ $offer->percentage }}"
                                                                                    placeholder="%">

                                                                            </td>
                                                                            <td class="" style="position: relative; width: 3% !important;">
                                                                                 <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                            </td>
                                                                            <td class="" style="width: 10% !important;">
                                                                                <select &nbsp=""
                                                                                    class="form-select form-select-solid bonus_option_salarymode"
                                                                                    arial-label="Select Annual Salary"
                                                                                    name="bonus_option_salarymode[]"
                                                                                    id="bonus_option_salarymode_{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    style="width: 100%;display: initial; ">
                                                                                    <option value="0"
                                                                                        {{ $offer->condition_check == 0 ? 'selected' : '' }}>
                                                                                        Amount </option>
                                                                                    <option value="1"
                                                                                        {{ $offer->condition_check == 1 ? 'selected' : '' }}>
                                                                                        Basic</option>
                                                                                </select>
                                                                            </td>
                                                                            <td class="" style="width: 17% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_offer_monthly"
                                                                                    name="bonus_offer_monthly[]"
                                                                                    id="bonus_offer_monthly_{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value=""
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td class="" style="width: 15% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_offer_yearly"
                                                                                    name="bonus_offer_yearly[]"
                                                                                    id="bonus_offer_yearly{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value=""
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0">
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px"
                                                                                            height="24px"
                                                                                            viewBox="0 0 24 24"
                                                                                            version="1.1">
                                                                                            <g stroke="none"
                                                                                                stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000"
                                                                                                    x="4" y="11"
                                                                                                    width="16"
                                                                                                    height="2"
                                                                                                    rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span></button>
                                                                                    <button type="button" id="addRowBtnbonus"
                                                                                    class="btn btn-primary addRowBtnbonus me-0" style="">
                                                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px" height="24px"
                                                                                            viewBox="0 0 24 24" version="1.1">
                                                                                            <g stroke="none" stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000" x="4" y="11"
                                                                                                    width="16" height="2"
                                                                                                    rx="1"></rect>
                                                                                                <rect fill="#000000"
                                                                                                    transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                    x="4" y="11" width="16"
                                                                                                    height="2" rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                            @endif
                                                            @if(isset($offer_point_other_hourly) && count($offer_point_other_hourly) > 0)
                                                                    @foreach ($offer_point_other_hourly as $offer)
                                                                        <tr class="hourly_section hourly_section_row row-template remove_row {{ $section_hourly }}"
                                                                            role="row">
                                                                            <td class=""
                                                                                style="width: 47% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_point_hourly"
                                                                                    name="bonus_point_hourly[]"
                                                                                    id="bonus_points_hourly{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value="{{ $offer->points }}"
                                                                                    placeholder="Enter Point">
                                                                            </td>
                                                                            <td class=""
                                                                                style="width: 12% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-select-solid bonus_option_percentage_hourly"
                                                                                    name="bonus_option_percentage_hourly[]"
                                                                                    id="bonus_option_percentage_hourly{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value="{{ $offer->percentage }}"
                                                                                    placeholder="%">
                                                                            </td>
                                                                            <td class="" style="position: relative; width: 3% !important;"> <i class="fa fa-close" style="position: absolute; top: 17px; left: 11px;"></i> 
                                                                            </td>
                                                                            <td class=""
                                                                                style="width: 10% !important;">
                                                                                <select &nbsp=""
                                                                                    class="form-select form-select-solid bonus_option_salarymode_hourly"
                                                                                    arial-label="Select Annual Salary"
                                                                                    name="bonus_option_salarymode_hourly[]"
                                                                                    id="bonus_option_salarymode_hourly{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    style="width: 100px;display: initial; ">
                                                                                    <option value="0"
                                                                                        {{ $offer->condition_check == 0 ? 'selected' : '' }}>
                                                                                        CTC </option>
                                                                                    <option value="1"
                                                                                        {{ $offer->condition_check == 1 ? 'selected' : '' }}>
                                                                                        Basic</option>
                                                                                </select>
                                                                            </td>
                                                                            <td class=""
                                                                                style="width: 15% !important;">
                                                                                <input &nbsp="" type="text"
                                                                                    class="form-control form-control-solid bonus_offer_hourly"
                                                                                    name="bonus_offer_hourly[]"
                                                                                    id="bonus_offer_hourly{{ $offer->id }}"
                                                                                    data-id="{{ $offer->id }}"
                                                                                    data-offerid="{{ $offer->id }}"
                                                                                    value=""
                                                                                    placeholder="0.00">
                                                                            </td>
                                                                            <td>
                                                                                <button type="button"
                                                                                    class="btn btn-light-danger font-weight-bolder remove_points me-0">
                                                                                    <span
                                                                                        class="svg-icon svg-icon svg-icon me-0">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                            width="24px"
                                                                                            height="24px"
                                                                                            viewBox="0 0 24 24"
                                                                                            version="1.1">
                                                                                            <g stroke="none"
                                                                                                stroke-width="1"
                                                                                                fill="none"
                                                                                                fill-rule="evenodd">
                                                                                                <rect fill="#000000"
                                                                                                    x="4" y="11"
                                                                                                    width="16"
                                                                                                    height="2"
                                                                                                    rx="1">
                                                                                                </rect>
                                                                                            </g>
                                                                                        </svg>
                                                                                    </span>
                                                                                </button>
                                                                                <button type="button" id=""
                                                                                    class="btn btn-primary me-0 addRowBtnbonus" style="">
                                                                                        <span class="svg-icon svg-icon svg-icon me-0">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                                                width="24px" height="24px"
                                                                                                viewBox="0 0 24 24" version="1.1">
                                                                                                <g stroke="none" stroke-width="1"
                                                                                                    fill="none"
                                                                                                    fill-rule="evenodd">
                                                                                                    <rect fill="#000000" x="4" y="11"
                                                                                                        width="16" height="2"
                                                                                                        rx="1"></rect>
                                                                                                    <rect fill="#000000" 
                                                                                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                                                        x="4" y="11" width="16"
                                                                                                        height="2" rx="1">
                                                                                                    </rect>
                                                                                                </g>
                                                                                            </svg>
                                                                                        </span>
                                                                                </button>

                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                            @endif

                                                        </tbody>
                                                        <tfoot class="other_section {{ $section_other }}">
                                                           
                                                            <tr>
                                                                <th colspan="2" class="sorting_disabled">
                                                                   
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalpercentagebonus"
                                                                        name="totalpercentagebonus"
                                                                        value=""
                                                                        id="totalpercentagebonus">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled">&nbsp;</th>
                                                                <th class="sorting_disabled" style="width: 78px;">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalMonthlybonus"
                                                                        name="totalMonthlybonus"
                                                                        id="totalMonthlybonus"
                                                                        value="{{ @$monthly_bounus_gross }}">
                                                                </th>
                                                                <th class="sorting_disabled">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalYearlybonus"
                                                                        name="totalYearlybonus"
                                                                        value="{{ @$yearly_bonus_gross }}"
                                                                        id="totalYearlybonus">
                                                                </th>
                                                                <th class="sorting_disabled">&nbsp;</th>
                                                            </tr>
                                                        </tfoot>

                                                        <tfoot class="hourly_section {{ $section_hourly }}">
                                                            <tr>
                                                                <th colspan="6" class="sorting_disabled"
                                                                    style="width: 102px;">&nbsp; </th>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="4" class="sorting_disabled"
                                                                    style="width: 86% !important;">
                                                                    <input type="hidden"
                                                                        class="form-control form-control-solid totalhourlypercentagebonus"
                                                                        name="totalhourlypercentagebonus"
                                                                        value=""
                                                                        id="totalhourlypercentagebonus">
                                                                </th>
                                                                <th class="sorting_disabled" colspan="1">
                                                                    <input &nbsp="" type="hidden"
                                                                        class="form-control form-control-solid totalhourlyybonus"
                                                                        name="totalhourlyybonus"
                                                                        value="{{ @$yearly_bonus_gross }}"
                                                                        id="totalhourlyybonus">
                                                                </th>
                                                            </tr>
                                                        </tfoot>

                                                    </table>
                                                </div>

                                                <div class="row">
                                                    <table class="table-row-dashed fs-6 gy-5 no-footer"
                                                        id="final_pay" role="grid"
                                                        aria-describedby="final_pay_info" style="width: 1270px;">


                                                        <tfoot class="other_section_final other_section {{ $section_other }}">

                                                            <tr>
                                                                <th colspan="2" class="sorting_disabled" style="width: 35%;">
                                                                    <input type="text" class="form-control form-control-solid" value="Final Salary (Cost To Company)" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled" style="width: 7% !important;">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalpercentagefinal"
                                                                        name="totalpercentagefinal"
                                                                        value=""
                                                                        id="totalpercentagefinal" style="background: #7de96f40;">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled" style="width: 13% !important;">
                                                                    <input &nbsp="" type="text"
                                                                    class="form-control form-control-solid show_currency"
                                                                    name="show_currency"
                                                                    value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled"  style="width: 17% !important;">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid finalpaytotalMonthly"
                                                                        name="finalpaytotalMonthly"
                                                                        id="finalpaytotalMonthly"
                                                                        value="{{ @$yearly_final_gross }}" style="background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled"  style="width: 15% !important;">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid finalpaytotalYearly"
                                                                        name="finalpaytotalYearly"
                                                                        id="finalpaytotalYearly"
                                                                        value="{{ @$monthly_final_gross }}" style="background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled">&nbsp;</th>
                                                            </tr>


                                                        </tfoot>
                                                        <tfoot class="hourly_section_final hourly_section {{ $section_hourly }}">

                                                            <tr>
                                                                <th class="sorting_disabled" style="width: 47%;">
                                                                    <input type="text" class="form-control form-control-solid" value="Final Salary (Cost To Company)" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled" style="width: 12% !important;">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid totalpercentagefinalhourly"
                                                                        name="totalpercentagefinalhourly"
                                                                        value=""
                                                                        id="totalpercentagefinalhourly" style="background: #7de96f40;">
                                                                </th>
                                                                <th colspan="2" class="sorting_disabled" style="width: 13% !important;">
                                                                    <input &nbsp="" type="text"
                                                                    class="form-control form-control-solid show_currency"
                                                                    name="show_currency"
                                                                    value="" readonly style="cursor: not-allowed;background: #7de96f40;">
                                                                </th>
                                                                <th class="sorting_disabled" colspan="1">
                                                                    <input &nbsp="" type="text"
                                                                        class="form-control form-control-solid finalpaytotalhourly"
                                                                        name="finalpaytotalhourly"
                                                                        id="finalpaytotalhourly"
                                                                        value="{{ @$yearly_final_gross }}" style="width:88%;background: #7de96f40;">
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>

                                                <div id="text_for_hourly_annually" class="d-none"><br>
                                                    <p><b>Note:</b>*The per-hour salary is calculated based on 8 hours per day, 5 days per week, and 2,080 hours per year. The annual salary is subject to adjustments based on the actual number of hours worked</p> </div>
                                                <hr>
                                                <div class="row justify-content-center">
                                                    <div class="col-lg-3  d-flex justify-content-center">
                                                        <input type="submit" name="save"
                                                            value="Save" id="save-form"
                                                            class="btn btn-primary"
                                                            style="width: 100%;height: 40px;margin-top: 10px;">
                                                    </div>
                                                    <div class="col-lg-3  d-flex justify-content-center section_change_value">
                                                        <button class="next-btn btn btn-warning" style="width: 100%; height: 41px; margin-top: 9px;"><a href="{{ route('service_company.account.offerAmountChange',[encrypt(1)]) }}" class="align-self-center" style="color: #fff;">Set to default</a></button>
                                                     </div>

                                                    
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        @endsection
        @push('footer-script')
            <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
            <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
            <script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
            <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
            <script src="{{ asset('demo/js/salarybreak.js') }}"></script>
            <script>
               
                document.addEventListener("DOMContentLoaded", function() {
                    // Initially show the first step
                    document.getElementById("step1").classList.add("active");
                });

                // Function to move to the next step
                function nextStep(event, currentStep) {
                    event.preventDefault(); // Prevent form submission
                    if (currentStep == 2) {
                        var id = "{{ $offer_id }}";
                        var url = "{{ route('service_company.cvtracker.previewOfferLettetr', ':id') }}";
                        url = url.replace(':id', id);
                        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                        var token = "{{ csrf_token() }}";
                        var offerId = "{{ $offer_id }}";
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'offerId': offerId
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    // $('#titleSet').text(response.title)
                                    $('#preview_offer_body_content').html(response.view);
                                    document.getElementById("step" + currentStep).classList.remove("active");
                                    document.getElementById("step" + (currentStep + 1)).classList.add("active");
                                } else if (response.status == "error") {
                                    Swal.fire("Warning", "Offer letter not ready", "warning");
                                }
                            }
                        });

                    } else {
                        document.getElementById("step" + currentStep).classList.remove("active");
                        document.getElementById("step" + (currentStep + 1)).classList.add("active");
                    }


                }

                // Function to move to the previous step
                function prevStep(event, currentStep) {
                    event.preventDefault();
                    document.getElementById("step" + currentStep).classList.remove("active");
                    document.getElementById("step" + (currentStep - 1)).classList.add("active");
                }





                $(document).ready(function() {

                    $(document).on('click', '#setToDefaultCompanyTerms', function() {

                        var data = new FormData();
                        var kt_docs_tinymce_hidden = 'setDefault';
                        data.append("description", kt_docs_tinymce_hidden);
                        var form_data = $('#termConditioncreateForm').serializeArray();
                        $.each(form_data, function(key, input) {
                            data.append(input.name, input.value);
                        });
                        CKEDITOR.replace('kt_docs_tinymce_hidden');
                        data.append("description", kt_docs_tinymce_hidden);
                        data.append("descriptiontype", "offer_terms_admin");

                        $.ajax({
                            url: '{{ route('service_company.account.update', auth()->user()->id) }}',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: "POST",
                            processData: false,
                            contentType: false,
                            data: data,
                            redirect: true,
                            success: function(data) {
                                if (data.status == 'success') {
                                    var editor1 = CKEDITOR.instances.editor1.setData(data.terms);
                                }
                            }
                        });
                    });

                    function calculateRowDetails(row) {

                        const option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.basic_view').val()) || 0;

                        if (basic_view == 0) {
                            row.find('.offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary
                        } else if (basic_view == 1) {
                            row.find('.offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''});
                        }

                       

                        var currentSalary = 0;
                        if (option_salarymode == 1) {
                            currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.option_percentage').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }

                        if (basic_view == 2) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.offer_monthly').val(0.00);
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        }

                    }

                    function calculateRowDetailshourly(row) {

                        var option_salarymode = parseFloat(row.find('.option_salarymode_hourly').val()) || 0;

                        var currentSalary = 0;

                        if (option_salarymode == 1) {
                            currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        // alert("currentSalary");
                        // alert("k");
                        const percentage = parseFloat(row.find('.option_percentage_hourly').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;


                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "2") { // Hourly
                            hourly = calculatedAmount; // Hourly wage based on input percentage
                            // alert(hourly);
                            monthly = hourly * 8 * 5 * 4; // Calculate monthly based on 8 hours a day, 5 days a week, 4 weeks a month
                            yearly = hourly * 8 * 5 * 52; // Calculate yearly based on 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "1") { // Hourly
                            yearly = calculatedAmount;
                            hourly = yearly / 12;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly * 12;
                        }

                        row.find('.offer_hourly').val(hourly.toFixed(2));
                    }


                    $(document).on('change', '.offer_hourly', function() {
                        const row = $(this).closest(
                            '.hourly_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.option_salarymode_hourly').val()) || 0;
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;
                        row.find('.option_percentage_hourly').val(percentage.toFixed(2));
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        finalPayupdateTotalAmountshourly();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.bonus_offer_hourly', function() {
                        const row = $(this).closest(
                            '.hourly_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.bonus_option_salarymode_hourly').val()) || 0;
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;
                        row.find('.bonus_option_percentage_hourly').val(percentage.toFixed(2));
                        updateTotalAmountsbonushourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        finalPayupdateTotalAmountshourly();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.insurence_offer_hourly', function() {
                        // return false;
                        const row = $(this).closest(
                            '.hourly_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.insurence_option_salarymode_hourly').val()) ||
                            0;
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get_hourly').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;
                        row.find('.insurence_option_percentage_hourly').val(percentage.toFixed(2));
                        updateTotalAmountsinsurencehourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });


                    $(document).on('change', '.offer_yearly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;

                        const calculatedAmount = (baseSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }
                        if (basic_view == 2) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.offer_monthly').val(0.00);
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.offer_yearly').val(yearly.toFixed(2));
                        row.find('.option_percentage').val(percentage.toFixed(2));
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.offer_monthly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const monthlyValue = parseFloat($(this).val()) || 0; // Get the monthly value
                        var option_salarymode = parseFloat(row.find('.option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.basic_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;

                        // Determine the base salary from either basic salary or CTC
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0; // Basic Salary
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0; // CTC
                        }

                        // Calculate the yearly based on the monthly value
                        let yearly = monthlyValue * 12; // Convert monthly to yearly
                        let monthly =  yearly / 12;
                        // Calculate percentage based on base salary
                        let percentage = (yearly / baseSalary) * 100;

                        // Update the yearly and percentage values in the row
                        if (basic_view == 2) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.offer_monthly').val(monthly.toFixed(2));
                            row.find('.offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.offer_monthly').val(0.00);
                            row.find('.offer_yearly').val(yearly.toFixed(2));
                        }
                        row.find('.option_percentage').val(percentage.toFixed(2)); // Update percentage
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.bonus_offer_yearly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;

                        const calculatedAmount = (baseSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;
                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }
                        // row.find('.bonus_offer_monthly').val(monthly.toFixed(2));

                        if (basic_view == 2) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').val(0.00);
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        }

                        // row.find('.offer_yearly').val(yearly.toFixed(2));
                        row.find('.bonus_option_percentage').val(percentage.toFixed(2));
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts()
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.bonus_offer_monthly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const monthlyValue = parseFloat($(this).val()) || 0; // Get the monthly value
                        var option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;

                        // Determine the base salary from either basic salary or CTC
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0; // Basic Salary
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0; // CTC
                        }

                        // Calculate the yearly based on the monthly value
                        let yearly = monthlyValue * 12; // Convert monthly to yearly
                        let monthly =  yearly / 12;

                        // Calculate percentage based on base salary
                        let percentage = (yearly / baseSalary) * 100;

                        // Update the yearly and percentage values in the row
                        if (basic_view == 2) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').val(0.00);
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.bonus_offer_yearly').val(yearly.toFixed(2)); // Update yearly amount
                        row.find('.bonus_option_percentage').val(percentage.toFixed(2)); // Update percentage
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.insurence_offer_yearly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const changeValue = parseFloat($(this).val()) || 0; // Get the percentage value
                        var option_salarymode = parseFloat(row.find('.insurence_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.insurence_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0;
                        }
                        let percentage = (changeValue / baseSalary) * 100;

                        const calculatedAmount = (baseSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;
                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }
                        if (basic_view == 2) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.insurence_offer_monthly').val(0.00);
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                        // row.find('.offer_yearly').val(yearly.toFixed(2));
                        row.find('.insurence_option_percentage').val(percentage.toFixed(2));
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts()
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.insurence_offer_monthly', function() {
                        const row = $(this).closest(
                            '.other_section_row'); // Get the closest row to the changed percentage
                        const monthlyValue = parseFloat($(this).val()) || 0; // Get the monthly value
                        var option_salarymode = parseFloat(row.find('.insurence_option_salarymode').val()) || 0;
                        const basic_view = parseFloat(row.find('.insurence_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        var baseSalary = 0;

                        // Determine the base salary from either basic salary or CTC
                        if (option_salarymode == 1) {
                            baseSalary = parseFloat($('.basic_salary_get').val()) || 0; // Basic Salary
                        } else {
                            baseSalary = parseFloat($('#ctc').val()) || 0; // CTC
                        }

                        // Calculate the yearly based on the monthly value
                        let yearly = monthlyValue * 12; // Convert monthly to yearly

                        // Calculate percentage based on base salary
                        let percentage = (yearly / baseSalary) * 100;

                        // Update the yearly and percentage values in the row
                        if (basic_view == 2) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.insurence_offer_monthly').val(0.00);
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        }
                        // row.find('.insurence_offer_yearly').val(yearly.toFixed(2)); // Update yearly amount
                        row.find('.insurence_option_percentage').val(percentage.toFixed(2)); // Update percentage
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });








                    // Event listener for input changes in CTC
                    $(document).on('keyup', '#ctc', function() {
                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 2 || current_ctc_mode == 1 || current_ctc_mode == 3) {
                            $('#pointList tbody .hourly_section_row').each(function() {
                                calculateRowDetailshourly($(this));
                                updateTotalAmountshourly();
                                updateTotalpercentagehourly();
                                updateTotalpercentagebonushourly();
                                finalPayupdateTotalAmountshourly();
                                totalpercentagefinalhourly();

                            });
                            $('#bonusList tbody .hourly_section_row').each(function() {
                                calculateRowDetailsbonushourly($(this));
                                updateTotalAmountsbonushourly();
                                updateTotalpercentagehourly();
                                updateTotalpercentagebonushourly();
                                finalPayupdateTotalAmountshourly();
                                totalpercentagefinalhourly();

                            });
                            $('#insurenceList tbody .hourly_section_row').each(function() {
                                calculateRowDetailsinsurencehourly($(this));
                                updateTotalAmountsinsurencehourly();
                                finalPayupdateTotalAmountshourly();
                                updateTotalpercentagehourly();
                                updateTotalpercentagebonushourly();
                                totalpercentagefinalhourly();

                            });

                        } else {
                            $('#pointList tbody .other_section_row').each(function() {
                                calculateRowDetails($(this));
                                updateTotalAmounts();
                                updateTotalpercentage();
                                finalPayupdateTotalAmounts();
                                totalpercentagefinal();
                            });
                            $('#bonusList tbody .other_section_row').each(function() {
                                calculateRowDetailsbonus($(this));
                                updateTotalAmountsbonus();
                                updateTotalpercentagebonus();
                                finalPayupdateTotalAmounts();
                                totalpercentagefinal();
                            });
                            $('#insurenceList tbody .other_section_row').each(function() {
                                calculateRowDetailsinsurence($(this));
                                updateTotalAmountsinsurence();
                                finalPayupdateTotalAmounts();
                                totalpercentagefinal();
                            });
                        }

                    });

                    // Event listener for percentage change in the table rows
                    $(document).on('change', '.option_percentage', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetails(row);
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.basic_view', function() {
                        const row = $(this).closest('tr');
                        const basic_view = $(this).val();
                        if (basic_view == 0) {
                            row.find('.offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary
                        } else if (basic_view == 1) {
                            row.find('.offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});  
                            row.find('.offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''});
                        }

                        calculateRowDetails(row);
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.bonus_view', function() {
                        const row = $(this).closest('tr');
                        const basic_view = $(this).val();
                        if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary                            
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});  
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); 
                        }

                        calculateRowDetailsbonus(row);
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.insurence_view', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetailsinsurence(row);
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });


                    $(document).on('change', '.option_salarymode', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetails(row);
                        updateTotalAmounts();
                        updateTotalpercentage();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.option_percentage_hourly', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetailshourly(row);
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.option_salarymode_hourly', function() {
                        const row = $(this).closest('tr');
                        calculateRowDetailshourly(row);
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        totalpercentagefinalhourly();
                    });


                    function updateTotalAmounts() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        $('#pointList tbody .other_section_row').each(function() {
                            const monthly = parseFloat($(this).find('.offer_monthly').val()) || 0;
                            const yearly = parseFloat($(this).find('.offer_yearly').val()) || 0;
                            totalMonthly += monthly;
                            totalYearly += yearly;
                        });

                        $('#totalMonthly').val(totalMonthly.toFixed(2));
                        $('#totalYearly').val(totalYearly.toFixed(2));
                    }

                    function updateTotalpercentage() {
                        let totalPercentage = 0;

                        $('#pointList tbody .other_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.option_percentage').val()) || 0;
                            totalPercentage += percentage;
                        });

                        $('#totalpercentage').val(totalPercentage.toFixed(2));
                    }

                    function updateTotalpercentagebonus() {

                        let totalPercentage = 0;

                        $('#bonusList tbody .other_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.bonus_option_percentage').val()) || 0;
                            totalPercentage += percentage;
                        });

                        $('#totalpercentagebonus').val(totalPercentage.toFixed(2));

                    }

                    function updateTotalpercentagehourly() {
                        let totalPercentage = 0;

                        $('#pointList tbody .hourly_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.option_percentage_hourly').val()) || 0;
                            totalPercentage += percentage;
                        });
                        //    alert(totalPercentage);
                        $('#totalpercentagehouly').val(totalPercentage.toFixed(2));
                    }

                    function updateTotalpercentagebonushourly() {

                        let totalPercentage = 0;

                        $('#bonusList tbody .hourly_section_row').each(function() {
                            const percentage = parseFloat($(this).find('.bonus_option_percentage_hourly').val()) || 0;
                            totalPercentage += percentage;
                            // alert(percentage);
                        });

                        $('#totalhourlypercentagebonus').val(totalPercentage.toFixed(2));

                    }



                    function updateTotalAmountshourly() {
                        let totaloffer_hourly = 0;

                        $('#pointList tbody .hourly_section_row').each(function() {
                            const offer_hourly = parseFloat($(this).find('.offer_hourly').val()) || 0;
                            totaloffer_hourly += offer_hourly;
                        });

                        // alert(totaloffer_hourly);
                        $('#totalhourlyhourly').val(totaloffer_hourly.toFixed(2));
                    }

                    function finalPayupdateTotalAmounts() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        // Assuming 'monthly' and 'yearly' are defined somewhere in your code
                        const monthly = parseFloat($('#monthlyInput').val()) || 0; // Define how to get 'monthly'
                        const yearly = parseFloat($('#yearlyInput').val()) || 0; // Define how to get 'yearly'

                        const Monthly = parseFloat($('.totalMonthly').val()) || 0;
                        const Yearly = parseFloat($('.totalYearly').val()) || 0;
                        const MonthlyBonus = parseFloat($('.totalMonthlybonus').val()) || 0;
                        const YearlyBonus = parseFloat($('.totalYearlybonus').val()) || 0;
                        const MonthlyInsurance = parseFloat($('.totalMonthlyinsurence').val()) || 0;
                        const YearlyInsurance = parseFloat($('.totalYearlyinsurence').val()) || 0;

                        totalMonthly = monthly + Monthly + MonthlyBonus + MonthlyInsurance;
                        totalYearly = yearly + Yearly + YearlyBonus + YearlyInsurance;

                        $('#finalpaytotalMonthly').val(totalMonthly.toFixed(2));
                        $('#finalpaytotalYearly').val(totalYearly.toFixed(2));
                    }

                    function totalpercentagefinal() {
                        let totalpercentagefinal = 0;

                        // Assuming 'monthly' and 'yearly' are defined somewhere in your code
                        const totalpercentage = parseFloat($('#totalpercentage').val()) || 0; // Define how to get 'monthly'
                        const totalpercentagebonus = parseFloat($('#totalpercentagebonus').val()) || 0; // Define how to get 

                        totalpercentagefinal = totalpercentage + totalpercentagebonus;

                        $('#totalpercentagefinal').val(totalpercentagefinal.toFixed(2));
                    }

                    function totalpercentagefinalhourly() {
                        let totalpercentagefinal = 0;
                    
                        // Assuming 'monthly' and 'yearly' are defined somewhere in your code
                        var totalpercentage = parseFloat($('#totalpercentagehouly').val()) || 0; // Define how to get 'monthly'
                        var totalpercentagebonus = parseFloat($('#totalhourlypercentagebonus').val()) || 0; // Define how to get 
                         //alert(totalpercentage);
                        totalpercentagefinal = totalpercentage + totalpercentagebonus;
                        // alert(totalpercentagefinal+"-"+totalpercentage+"-"+totalpercentagebonus);

                        $('#totalpercentagefinalhourly').val(totalpercentagefinal.toFixed(2));
                    }



                    function finalPayupdateTotalAmountshourly() {
                        let totalhourly = 0;

                        const totalhourlyhourly = parseFloat($('.totalhourlyhourly').val()) || 0;
                        const totalhourlyybonus = parseFloat($('.totalhourlyybonus').val()) || 0;
                        const totalYearlyinsurencehourly = parseFloat($('.totalYearlyinsurencehourly').val()) || 0;

                        totalhourly = totalhourlyhourly + totalhourlyybonus + totalYearlyinsurencehourly;

                        $('#finalpaytotalhourly').val(totalhourly.toFixed(2));
                    }

                    function finalRow(){
                        const rowCountOther = $('#bonusList tbody .other_section_row').length;
                        const rowCountHourly = $('#bonusList tbody .hourly_section_row').length;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode
                        if((rowCountOther == 0) && (addSubtract==4 || addSubtract==0)){
                            $('.other_section_final').addClass('d-none');
                            // $('.other_text').text('');
                        }else if((rowCountOther > 0) && (addSubtract==4 || addSubtract==0)){
                            $('.other_section_final').removeClass('d-none');
                            // $('.other_text').text('Other Component');
                        }
                        
                        if((rowCountHourly == 0) && (addSubtract==1 || addSubtract==2 || addSubtract==3)){ 
                            $('.hourly_section_final').addClass('d-none');
                            // $('.other_text').text('');
                        }else if((rowCountHourly > 0) && (addSubtract==1 || addSubtract==2 || addSubtract==3)){
                            $('.hourly_section_final').removeClass('d-none');
                            // $('.other_text').text('Other Component');
                        }
                    }


                    $(document).off('click', '.addRowBtn').on('click', '.addRowBtn', function() {
                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3) {
                            var newRow = $('.hourly_section_row').first().clone(); // Clone the first template row{
                        } else {
                            var newRow = $('.other_section_row').first().clone(); // Clone the first template row{
                        }
                        newRow.find('input').val(''); // Clear input values
                        newRow.find('select').val('0'); // Reset selects to default values
                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        $('#pointList tbody').append(newRow); // Append the new row to the table body

                        // Attach event listeners for new row
                        newRow.find('.option_percentage').change(function() {
                            calculateRowDetails(newRow);
                            updateTotalAmounts(); // Update totals when percentage changes
                            updateTotalpercentage();
                        });
                    });

                    $(document).off('click', '.remove_points').on('click', '.remove_points', function() {
                        const removeRow = $(this).closest('.remove_row');
                        removeRow.remove();
                        finalRow();
                        // Attach event listeners for new row
                        updateTotalAmounts(); // Update totals when percentage changes
                        updateTotalpercentage();
                        updateTotalAmountsinsurence();
                        updateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        updateTotalAmountsbonushourly();
                        updateTotalAmountsinsurencehourly();
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                        finalPayupdateTotalAmountshourly();
                        finalPayupdateTotalAmounts();

                    });

                    $(document).off('click', '.addRowBtnbonus').on('click', '.addRowBtnbonus', function() {
                        
                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3) {
                            var newRow = $('.hourly_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_point_hourly').addClass('bonus_point_hourly').removeClass(
                                'offer_point_hourly');
                            newRow.find('.bonus_point_hourly').each(function(index) {
                                var bonus_point_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_point_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_point_hourly_' +
                                    bonus_point_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_point_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_point_hourlyIndex); // Update 'data-offerid' attribute
                            });


                            newRow.find('.option_percentage_hourly').addClass('bonus_option_percentage_hourly')
                                .removeClass('option_percentage_hourly');
                            newRow.find('.bonus_option_percentage_hourly').each(function(index) {
                                var bonus_option_percentage_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_percentage_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_percentage_hourly_' +
                                    bonus_option_percentage_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_percentage_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_percentage_hourlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode_hourly').addClass('bonus_option_salarymode_hourly')
                                .removeClass('option_salarymode_hourly');
                            newRow.find('.bonus_option_salarymode_hourly').each(function(index) {
                                var bonus_option_salarymode_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_salarymode_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_salarymode_hourly_' +
                                    bonus_option_salarymode_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_salarymode_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_salarymode_hourlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_hourly').addClass('bonus_offer_hourly').removeClass('offer_hourly');
                            newRow.find('.bonus_offer_hourly').each(function(index) {
                                var bonus_offer_hourlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_offer_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_offer_hourly_' +
                                    bonus_offer_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_offer_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_offer_hourlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');
                            
                            newRow.find('.bonus_offer_hourly').removeClass('basic_salary_get_hourly');

                        } else {
                            var newRow = $('.other_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_points').addClass('bonus_points').removeClass('offer_points');
                            newRow.find('.bonus_points').each(function(index) {
                                var bonus_pointsIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_points[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_points_' +
                                    bonus_pointsIndex); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id', bonus_pointsIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_pointsIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_monthly').addClass('bonus_offer_monthly').removeClass(
                                'offer_monthly');
                            newRow.find('.bonus_offer_monthly').each(function(index) {
                                var bonus_offer_monthlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_offer_monthly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_offer_monthly_' +
                                    bonus_offer_monthlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_offer_monthlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_offer_monthlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.basic_view').addClass('bonus_view').removeClass(
                                'basic_view');
                            newRow.find('.bonus_view').each(function(index) {
                                var bonus_viewIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_view[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_view_' +
                                    bonus_viewIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_viewIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_viewIndex); // Update 'data-offerid' attribute
                            });


                            newRow.find('.option_percentage').addClass('bonus_option_percentage').removeClass(
                                'option_percentage');
                            newRow.find('.bonus_option_percentage').each(function(index) {
                                var bonus_option_percentageIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_percentage[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_percentage_' +
                                    bonus_option_percentageIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_percentageIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_percentageIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode').addClass('bonus_option_salarymode').removeClass(
                                'option_salarymode');
                            newRow.find('.bonus_option_salarymode').each(function(index) {
                                var bonus_option_salarymodeIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'bonus_option_salarymode[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_option_salarymode_' +
                                    bonus_option_salarymodeIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_option_salarymodeIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_option_salarymodeIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_yearly').addClass('bonus_offer_yearly').removeClass('offer_yearly');
                            newRow.find('.bonus_offer_yearly').each(function(index) {
                                var bonus_offer_yearlyIndex = $('#bonusList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'bonus_offer_yearly[]'); // Change 'name' attribute
                                $(this).attr('id', 'bonus_offer_yearly_' +
                                    bonus_offer_yearlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    bonus_offer_yearlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    bonus_offer_yearlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');

                            newRow.find('.bonus_offer_yearly').removeClass('basic_salary_get');

                        }



                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        $('#bonusList tbody').append(newRow); // Append the new row to the table body
                        finalRow();
                        // Attach event listeners for new row
                    });

                    $(document).off('click', '.addRowBtninsurence').on('click', '.addRowBtninsurence', function() {

                        const insurence_offer_count = $('#insurenceList tbody tr').length

                        var current_ctc_mode = $('#current_ctc_mode').val();
                        if (current_ctc_mode == 1 || current_ctc_mode == 2 || current_ctc_mode == 3) {
                            var newRow = $('.hourly_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_point_hourly').addClass('insurence_point_hourly').removeClass(
                                'offer_point_hourly');
                            newRow.find('.insurence_point_hourly').each(function(index) {
                                const insurence_point_hourlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_point_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_point_hourly_' +
                                    insurence_point_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_point_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_point_hourlyIndex); // Update 'data-offerid' attribute
                            });


                            newRow.find('.option_percentage_hourly').addClass('insurence_option_percentage_hourly')
                                .removeClass('option_percentage_hourly');
                            newRow.find('.insurence_option_percentage_hourly').each(function(index) {
                                const option_percentage_hourlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_percentage_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_percentage_hourly_' +
                                    option_percentage_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    option_percentage_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    option_percentage_hourlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode_hourly').addClass('insurence_option_salarymode_hourly')
                                .removeClass('option_salarymode_hourly');
                            newRow.find('.insurence_option_salarymode_hourly').each(function(index) {
                                const insurence_option_salarymode_hourlyIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_salarymode_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_salarymode_hourly_' +
                                    insurence_option_salarymode_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_option_salarymode_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_option_salarymode_hourlyIndex
                                ); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_hourly').addClass('insurence_offer_hourly').removeClass(
                                'offer_hourly');
                            newRow.find('.insurence_offer_hourly').each(function(index) {
                                const insurence_offer_hourlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_offer_hourly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_offer_hourly_' +
                                    insurence_offer_hourlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_offer_hourlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_offer_hourlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.insurence_offer_hourly').removeClass('basic_salary_get_hourly');

                        } else {
                            var newRow = $('.other_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values
                            newRow.find('select').val('0'); // Reset selects to default values
                            newRow.find('.offer_points').addClass('insurence_points').removeClass('offer_points');
                            newRow.find('.insurence_points').each(function(index) {
                                const insurence_pointsIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_points[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_points_' +
                                    insurence_pointsIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_pointsIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_pointsIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.basic_view').addClass('insurence_view').removeClass(
                                'basic_view');
                            newRow.find('.insurence_view').each(function(index) {
                                const insurence_viewIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_view[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_view_' +
                                    insurence_viewIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_viewIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_viewIndex); // Update 'data-offerid' attribute
                            });




                            newRow.find('.offer_monthly').addClass('insurence_offer_monthly').removeClass(
                                'offer_monthly');
                            newRow.find('.insurence_offer_monthly').each(function(index) {
                                const insurence_offer_monthlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_offer_monthly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_offer_monthly_' +
                                    insurence_offer_monthlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_offer_monthlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_offer_monthlyIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_percentage').addClass('insurence_option_percentage').removeClass(
                                'option_percentage');
                            newRow.find('.insurence_option_percentage').each(function(index) {
                                const insurence_option_percentageIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_percentage[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_percentage_' +
                                    insurence_option_percentageIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_option_percentageIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_option_percentageIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.option_salarymode').addClass('insurence_option_salarymode').removeClass(
                                'option_salarymode');
                            newRow.find('.insurence_option_salarymode').each(function(index) {
                                const insurence_option_salarymodeIndex = $('#insurenceList tbody tr')
                                    .length + 1; // Get a unique index for each new row
                                $(this).attr('name',
                                    'insurence_option_salarymode[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_option_salarymode_' +
                                    insurence_option_salarymodeIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_option_salarymodeIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_option_salarymodeIndex); // Update 'data-offerid' attribute
                            });

                            newRow.find('.offer_yearly').addClass('insurence_offer_yearly').removeClass(
                                'offer_yearly');
                            newRow.find('.insurence_offer_yearly').each(function(index) {
                                const insurence_offer_yearlyIndex = $('#insurenceList tbody tr').length +
                                    1; // Get a unique index for each new row
                                $(this).attr('name', 'insurence_offer_yearly[]'); // Change 'name' attribute
                                $(this).attr('id', 'insurence_offer_yearly_' +
                                    insurence_offer_yearlyIndex
                                ); // Update 'id' attribute with a new unique number
                                $(this).attr('data-id',
                                    insurence_offer_yearlyIndex); // Update 'data-id' attribute
                                $(this).attr('data-offerid',
                                    insurence_offer_yearlyIndex); // Update 'data-offerid' attribute
                            });
                            newRow.find('.insurence_offer_yearly').removeClass('basic_salary_get');
                        }


                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        $('#insurenceList tbody').append(newRow); // Append the new row to the table body

                        // Attach event listeners for new row
                    });



                    function calculateRowDetailsbonus(row) {
                        const bonus_option_salarymode = parseFloat(row.find('.bonus_option_salarymode').val()) || 0;
                        var currentSalary = 0;
                        if (bonus_option_salarymode == 1) {
                            currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.bonus_option_percentage').val()) || 0;
                        const basic_view = parseFloat(row.find('.bonus_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});   // Remove readonly from yearly if necessary                            
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_yearly').attr('readonly', true).css({ 'cursor': 'not-allowed', 'background': '#dddddd'});  // Add readonly
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''}); // Remove readonly from monthly if necessary
                        }else{
                            row.find('.bonus_offer_yearly').removeAttr('readonly').css({'cursor': '','background': ''});
                            row.find('.bonus_offer_monthly').removeAttr('readonly').css({'cursor': '','background': ''});
                        }


                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }

                        if (basic_view == 2) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.bonus_offer_monthly').val(monthly.toFixed(2));
                            row.find('.bonus_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.bonus_offer_monthly').val(0.00);
                            row.find('.bonus_offer_yearly').val(yearly.toFixed(2));
                        }
                    }

                    function calculateRowDetailsbonushourly(row) {
                        // alert("k");
                        const bonus_option_salarymode_hourly = parseFloat(row.find('.bonus_option_salarymode_hourly')
                            .val()) || 0;
                        var currentSalary = 0;
                        if (bonus_option_salarymode_hourly == 1) {
                            currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.bonus_option_percentage_hourly').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "2") { // Hourly
                            hourly = calculatedAmount; // Hourly wage based on input percentage
                            // alert(hourly);
                            monthly = hourly * 8 * 5 * 4; // Calculate monthly based on 8 hours a day, 5 days a week, 4 weeks a month
                            yearly = hourly * 8 * 5 * 52; // Calculate yearly based on 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "1") { // Hourly
                            yearly = calculatedAmount;
                            hourly = yearly / 12;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly * 12;
                        }

                        row.find('.bonus_offer_hourly').val(hourly.toFixed(2));
                    }

                   
                    


                    // Event listener for percentage change in the table rows
                    $(document).on('change', '.bonus_option_percentage', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsbonus(row);
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.bonus_option_salarymode', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsbonus(row);
                        updateTotalAmountsbonus();
                        updateTotalpercentagebonus();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.bonus_option_percentage_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsbonushourly(row);
                        updateTotalAmountsbonushourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                        
                    });
                    $(document).on('change', '.bonus_option_salarymode_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsbonushourly(row);
                        updateTotalAmountsbonushourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });


                    function updateTotalAmountsbonus() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        $('#bonusList tbody .other_section_row').each(function() {
                            const monthly = parseFloat($(this).find('.bonus_offer_monthly').val()) || 0;
                            const yearly = parseFloat($(this).find('.bonus_offer_yearly').val()) || 0;
                            totalMonthly += monthly;
                            totalYearly += yearly;
                        });

                        $('#totalMonthlybonus').val(totalMonthly.toFixed(2));
                        $('#totalYearlybonus').val(totalYearly.toFixed(2));
                    }

                    function updateTotalAmountsbonushourly() {
                        let totalhourly = 0;

                        $('#bonusList tbody .hourly_section_row').each(function() {
                            const hourly = parseFloat($(this).find('.bonus_offer_hourly').val()) || 0;
                            totalhourly += hourly;
                        });

                        $('#totalhourlyybonus').val(totalhourly.toFixed(2));
                    }


                    function calculateRowDetailsinsurence(row) {
                        const insurence_option_salarymode = parseFloat(row.find('.insurence_option_salarymode').val()) || 0;
                        var currentSalary = 0;
                        if (insurence_option_salarymode == 1) {
                            currentSalary = parseFloat($('.basic_salary_get').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.insurence_option_percentage').val()) || 0;
                        const basic_view = parseFloat(row.find('.insurence_view').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                        } else if (addSubtract === "1") { // Monthly
                            monthly = calculatedAmount;
                            yearly = monthly * 12;
                        } else if (addSubtract === "4") { // Monthly
                            yearly = calculatedAmount;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52   let hourly = 0;
                            monthly = hourly;
                        }

                        if (basic_view == 2) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        } else if (basic_view == 1) {
                            row.find('.insurence_offer_monthly').val(monthly.toFixed(2));
                            row.find('.insurence_offer_yearly').val(0.00);
                        } else if (basic_view == 0) {
                            row.find('.insurence_offer_monthly').val(0.00);
                            row.find('.insurence_offer_yearly').val(yearly.toFixed(2));
                        }
                    }

                    function calculateRowDetailsinsurencehourly(row) {
                        const insurence_option_salarymode = parseFloat(row.find('.insurence_option_salarymode_hourly')
                            .val()) || 0;
                        var currentSalary = 0;
                        if (insurence_option_salarymode == 1) {
                            currentSalary = parseFloat($('.other_salary_get_hourly').val()) || 0;
                        } else {
                            currentSalary = parseFloat($('#ctc').val()) || 0;
                        }

                        const percentage = parseFloat(row.find('.insurence_option_percentage_hourly').val()) || 0;
                        const addSubtract = $('#current_ctc_mode').val(); // Add/Subtract mode

                        // Calculate the amount based on the selected percentage
                        const calculatedAmount = (currentSalary * (percentage / 100));
                        let monthly = 0;
                        let yearly = 0;
                        let hourly = 0;

                        if (addSubtract === "0") { // Annual
                            yearly = calculatedAmount;
                            monthly = yearly / 12;
                            hourly = yearly / (8 * 5 * 52); // Assuming 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "2") { // Hourly
                            hourly = calculatedAmount; // Hourly wage based on input percentage
                            // alert(hourly);
                            monthly = hourly * 8 * 5 * 4; // Calculate monthly based on 8 hours a day, 5 days a week, 4 weeks a month
                            yearly = hourly * 8 * 5 * 52; // Calculate yearly based on 8 hours a day, 5 days a week, 52 weeks a year
                        } else if (addSubtract === "1") { // Hourly
                            yearly = calculatedAmount;
                            hourly = yearly / 12;
                        } else if (addSubtract === "3") { // Hourly
                            monthly = calculatedAmount;
                            hourly = monthly * 12;
                        }
                        row.find('.insurence_offer_hourly').val(hourly.toFixed(2));

                    }

                    // Event listener for percentage change in the table rows
                    $(document).on('change', '.insurence_option_percentage', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsinsurence(row);
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });
                    $(document).on('change', '.insurence_option_salarymode', function() {
                        const row = $(this).closest('.other_section_row');
                        calculateRowDetailsinsurence(row);
                        updateTotalAmountsinsurence();
                        finalPayupdateTotalAmounts();
                        totalpercentagefinal();
                        totalpercentagefinalhourly();
                    });

                    $(document).on('change', '.insurence_option_percentage_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsinsurencehourly(row);
                        updateTotalAmountsinsurencehourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });
                    $(document).on('change', '.insurence_option_salarymode_hourly', function() {
                        const row = $(this).closest('.hourly_section_row');
                        calculateRowDetailsinsurencehourly(row);
                        updateTotalAmountsinsurencehourly();
                        finalPayupdateTotalAmountshourly();
                        updateTotalpercentagehourly();
                        updateTotalpercentagebonushourly();
                    });

                    function updateTotalAmountsinsurencehourly() {
                        let totalhourly = 0;

                        $('#insurenceList tbody .hourly_section_row').each(function() {
                            const hourly = parseFloat($(this).find('.insurence_offer_hourly').val()) || 0;
                            totalhourly += hourly;
                        });

                        $('#totalYearlyinsurencehourly').val(totalhourly.toFixed(2));
                    }

                    function updateTotalAmountsinsurence() {
                        let totalMonthly = 0;
                        let totalYearly = 0;

                        $('#insurenceList tbody .other_section_row').each(function() {
                            const monthly = parseFloat($(this).find('.insurence_offer_monthly').val()) || 0;
                            const yearly = parseFloat($(this).find('.insurence_offer_yearly').val()) || 0;
                            totalMonthly += monthly;
                            totalYearly += yearly;
                        });

                        $('#totalMonthlyinsurence').val(totalMonthly.toFixed(2));
                        $('#totalYearlyinsurence').val(totalYearly.toFixed(2));
                    }

                    $(document).on('change', '#current_ctc_mode', function() {
                        var current_ctc_mode = $(this).val();
                        finalRow();
                         $('.basic_view').each(function() {
                            const select = $(this);  // Get the current select element in the loop
                            const monthlyOption = select.find('option[value="1"]');  // Find the option with value 1 (Monthly)

                            // Assuming current_ctc_mode is dynamic or can vary per select element
                            // Change the option text based on current_ctc_mode
                            if (current_ctc_mode == 4) {
                                monthlyOption.text("Hourly");
                                $('#text_for_hourly_annually').removeClass('d-none');
                            } else {
                                monthlyOption.text("Monthly");                                
                                $('#text_for_hourly_annually').addClass('d-none');
                            }
                         });

                         $('.bonus_view').each(function() {
                            const select = $(this);  // Get the current select element in the loop
                            const monthlyOption = select.find('option[value="1"]');  // Find the option with value 1 (Monthly)

                            // Assuming current_ctc_mode is dynamic or can vary per select element
                            // Change the option text based on current_ctc_mode
                            if (current_ctc_mode == 4) {
                                monthlyOption.text("Hourly");
                            } else {
                                monthlyOption.text("Monthly");
                            }
                         });

                        

                      
                        if (current_ctc_mode == 0 || current_ctc_mode == 4) {
                            var change_text = '';
                            if(current_ctc_mode==1){
                                 change_text = 'Monthly';
                            }else if(current_ctc_mode==4){
                                 change_text = 'Hourly';
                            }else if(current_ctc_mode==3){
                                 change_text = 'Annual';
                            }
                            $('.change_text_first').text(change_text);
                            $('.other_section').removeClass('d-none');
                            $('.hourly_section').addClass('d-none');
                        } else {
                            var change_text = '';
                            if(current_ctc_mode==1){
                                 change_text = 'Monthly';
                            }else if(current_ctc_mode==2){
                                 change_text = 'Hourly';
                            }else if(current_ctc_mode==3){
                                 change_text = 'Annual';
                            }
                            $('.change_text').text(change_text);
                            $('.hourly_section').removeClass('d-none');
                            $('.other_section').addClass('d-none');
                        }
                    });

                    setTimeout(() => {
                        changeCurrency();
                    }, 500);

                    $(document).on('change', '#current_ctc_cur', function() {
                        changeCurrency();
                    });

                    function changeCurrency(){
                        const val_currency = $('#current_ctc_cur').val();   
                        if(val_currency==0){
                            $('.show_currency').val('INR');
                        }else if(val_currency==1){
                            $('.show_currency').val('EUR');
                        }else if(val_currency==2){
                            $('.show_currency').val('JPY');
                        }else if(val_currency==3){
                            $('.show_currency').val('GBP');
                        }else if(val_currency==4){
                            $('.show_currency').val('USD');
                        }else if(val_currency==5){
                           $('.show_currency').val('AED');
                        }
                    }
                    


                    $('#createForm').on('submit', function(e) {
                        e.preventDefault();
                        //  var formData = new FormData($('#createForm')[0]);	
                        let formData = new FormData(this);
                        $.ajax({
                            url: '{{ route('service_company.account.companysavecreateoffer') }}' +
                                '?_token=' + '{{ csrf_token() }}',
                            type: 'POST',
                            data: formData,
                            processData: false, // tell jQuery not to process the data
                            contentType: false, // tell jQuery not to set contentType
                            redirect: true,
                            success: function(response) {

                                if (response.status == 'success') {
                                    Swal.fire("Success", response.message , "success");	
                                    return false;
                                } else if (response.status == 'errors') {
                                    Swal.fire("Warning", response.message, "warning");
                                } else {
                                    Swal.fire("Warning", response.message, "warning");
                                }

                            },
                            error: function(response) {
                                // displayErrors(response.responseJSON.errors);
                                // Handle validation errors and display in SweetAlert
                                let errors = response.responseJSON.errors;
                                let errorMessages = '';
                                let counter = 1;

                                $.each(errors, function(fieldName, errorMessage) {
                                    // Add error messages with red color using inline CSS
                                    errorMessages +=
                                        `<span style="color:red;">${counter}. ${errorMessage[0]}</span><br>`;
                                    counter++;
                                });
                                // Show error messages in SweetAlert
                                Swal.fire({
                                    title: "Validation Errors",
                                    html: errorMessages, // Display the errors with HTML content
                                    icon: "warning",
                                    customClass: {
                                        popup: 'text-left' // Align the text to the left
                                    }
                                });
                            }
                        });
                    });




                    $('body').on('click', '#cancel-note', function() {
                        $('#note_text').val('');
                    });

                    $('#add-note').click(function() {
                        var url = "http://localhost/MEECRUITUAT/index.php/service_company/applicant-note";
                        var id = 1028;
                        var note = $('#note_text').val();
                        var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                'id': id,
                                'note': note
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    $('#applicant-notes').html(response.view);
                                    $('#note_text').val('');
                                }
                            }
                        });
                    });

                    $('body').on('click', '.delete-note', function() {
                        let noteId = $(this).data('note-id');


                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                                confirmButton: 'btn btn-success',
                                cancelButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        })

                        swalWithBootstrapButtons.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'No, cancel!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {



                                var url =
                                    "http://localhost/MEECRUITUAT/index.php/service_company/applicant-note/:id";
                                url = url.replace(':id', noteId);
                                var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        '_token': token,
                                        '_method': 'DELETE'
                                    },
                                    success: function(response) {
                                        if (response.status == 'success') {
                                            $('#applicant-notes').html(response.view);

                                            swalWithBootstrapButtons.fire(
                                                'Deleted!',
                                                'Your text note has been deleted.',
                                                'success'
                                            )

                                        }
                                    }
                                });



                            } else if (
                                result.dismiss === Swal.DismissReason.cancel
                            ) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    'Your text note is safe :)',
                                    'error'
                                )
                            }
                        })


                    });

                    $('body').on('click', '.edit-note', function() {

                        $(this).hide();
                        let noteId = $(this).data('note-id');
                        $('body').find('#note-' + noteId + ' .note-text').hide();

                        let noteText = $('body').find('#note-' + noteId + ' .note-text').html();
                        let textArea = '<textarea id="edit-note-text-' + noteId +
                            '" class="form-control" row="4">' + noteText +
                            '</textarea><a class="update-note btn btn-sm btn-light-success py-1 mt-2" data-note-id="' +
                            noteId + '" href="javascript:void(0);">Save</a>';
                        $('body').find('#note-' + noteId + ' .note-textarea').html(textArea);

                    });


                    $('body').on('click', '.update-note', function() {

                        let noteId = $(this).data('note-id');
                        var url = "http://localhost/MEECRUITUAT/index.php/service_company/applicant-note/:id";
                        url = url.replace(':id', noteId);
                        var note = $('#edit-note-text-' + noteId).val();
                        var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                'noteId': noteId,
                                'note': note,
                                '_method': 'PUT'
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    $('#applicant-notes').html(response.view);
                                }
                            }
                        });


                    });

                    $('body').on('click', '.resume_delete', function() {


                        var application_id = $(this).attr('rel');
                        var token = 'Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK';


                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                                confirmButton: 'btn btn-success',
                                cancelButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                        })

                        swalWithBootstrapButtons.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'No, cancel!',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {


                                var url =
                                    "http://localhost/MEECRUITUAT/index.php/service_company/job-applications/resume_delete";
                                var token = "Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK";
                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        '_token': token,
                                        '_method': 'POST',
                                        'application_id': application_id
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {

                                            swalWithBootstrapButtons.fire(
                                                'Deleted!',
                                                'Your resume has been deleted.',
                                                'success'
                                            )


                                        }
                                    }
                                });

                            } else if (
                                result.dismiss === Swal.DismissReason.cancel
                            ) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    'Your text note is safe :)',
                                    'error'
                                )
                            }
                        })

                    });


                    $('.sendmaimodal').click(function() {


                        $('#application_send_mail_body_content').html('');
                        $('#application_mass_mail_body_content').html('');
                        $('#application_fwd_resume_body_content').html('');
                        var id = 29;
                        var sid = 1028;
                        var arr = [sid];
                        var jobIdArr = [id];
                        $('.chk_list_item:checked').each(function() {

                            var jobId = $(this).attr('data-jobId');
                            arr.push($(this).val());
                            jobIdArr.push(jobId);
                        });

                        console.log(jobIdArr);

                        var id = jobIdArr[0];
                        var url = "http://localhost/MEECRUITUAT/index.php/service_company/send-mail/send-mail/:id";
                        url = url.replace(':id', id);
                        var baseurl = "http://localhost/MEECRUITUAT/assets/images/Loading2.gif";
                        var token = "Q6b9KeXqR9IISJVKyeiYpqmiv7tFzEPrEHdsqroK";


                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': token,
                                '_method': 'POST',
                                'arr': arr,
                                'jobIdArr': jobIdArr
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status == "success") {
                                    $('#application_send_mail_body_content').html(response.view);

                                    $('#kt_activities_scroll').removeAttr('style');
                                }
                            }
                        });

                    });


                });
            </script>
        @endpush
