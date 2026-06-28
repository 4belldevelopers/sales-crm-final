@extends('layouts.service_company_layout')
@push('head-script')
    <link href="{{ asset('demo/v7/css/wizard-1.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/prismjs.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <script>
        $(document).ready(function() {
            $('#client_id').select2();
        });
    </script>
    <style>
        .form-label {
            margin-bottom: 0.5rem !important;
            font-size: 1.05rem !important;
            font-weight: 500 !important;
            color: #3f4254 !important;
        }

        body {
            overflow-x: hidden;
        }

        .tox .tox-toolbar__group {
            padding: 0px !important;
        }

        .tox .tox-toolbar-overlord {
            display: inline-flex;
        }

        .tox .tox-tbtn--select {
            margin: 0px !important;
            padding: 0px !important;
            width: 127px !important;
        }

        .tox:not(.tox-tinymce-inline) .tox-editor-header {
            border-bottom: 1px solid lightgray;
        }

        .tox .tox-dialog--width-lg {
            height: 650px;
            max-width: 920px !important;
        }

        .last-input-border-radius {
            border-radius: 5px 0px 0px 5px !important;
        }

        .option {
            cursor: pointer;
        }

        .select2-container--default .select2-selection--multiple {
            background-color: #f5f8fa !important;
            border: 1px solid #f5f8fa !important;
            height: 44px;
            line-height: 44px;
            display: flex !important;
            align-items: center;
            justify-content: left;
            border-radius: 5px !important;
            overflow-y: scroll;
        }

        .select2-container--bootstrap5 .select2-selection {
            height: 6vh !important;
            display: flex !important;
            align-items: center;
            box-shadow: none !important;
            height: auto;
            outline: 0 !important;
            background-color: #f5f8fa;
            border-color: #f5f8fa;
            color: #5e6278;
            transition: color .2s ease, background-color .2s ease;
        }

        .select2-container--bootstrap5 .select2-dropdown .select2-results__options {
            max-height: 200px;
            overflow-y: overlay !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            border-right: 1px solid #e7e8e8 !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
            margin-right: 0.2rem !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            background: #e7e8e8 !important;
        }

        .select2-container--default .select2-selection--multiple {
            padding: 0px 5px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:before {
            content: "view more" !important;
            font-size: 1rem !important;
        }

        #end_date::-webkit-inner-spin-button,
        #end_date::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesRecruiter {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesRecruiter {
                width: 1200px;
            }
        }

        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_add_address {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_add_address {
                width: 1200px;
            }
        }

        .tagify .tagify__tag div .tagify__tag-text {
            color: #0c0c0c !important;
            font-weight: 500 !important;
            font-size: 14px !important;
        }

        .custom-dropdown-class {
            background-color: #fff;
            /* Background color for dropdown */
            border: 1px solid #ccc;
            /* Border to distinguish the dropdown */
            z-index: 1000;
            /* Ensure dropdown appears above other elements */
            max-height: 300px;
            /* Limit the height of the dropdown */
            overflow-y: auto;
            /* Add scroll if the list exceeds the max height */
        }

        .custom-dropdown-class .tagify__dropdown__item {
            padding: 10px;
            /* Padding for each item in the dropdown */
            cursor: pointer;
            /* Change cursor to pointer for better UX */
            border-bottom: 1px solid #eee;
            /* Add a subtle divider between items */
        }

        .custom-dropdown-class .tagify__dropdown__item--active {
            background-color: #f0f0f0;
            /* Highlight active item */
        }

        .custom-dropdown-class .tagify__dropdown__item:hover {
            background-color: #e0e0e0;
            /* Highlight item on hover */
        }

        #suggestions {
            top: 45px;
            position: absolute;
            /* Position the suggestions list relative to the input */
            z-index: 1000;
            /* Make sure it appears above other elements */
            background-color: white;
            /* White background for the dropdown */
            border: 1px solid #ccc;
            /* Light gray border */
            border-radius: 4px;
            /* Rounded corners */
            max-height: 200px;
            /* Limit height for scrolling */
            width: 100%;
            overflow-y: auto;
            /* Add scrollbar if content exceeds max height */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for depth */
        }

        #suggestions-list {
            list-style-type: none;
            /* Remove default list styling */
            padding: 0;
            /* Remove padding */
            margin: 0;
            /* Remove margin */
        }

        #suggestions-list li {
            padding: 10px 15px;
            /* Padding for list items */
            cursor: pointer;
            /* Pointer cursor on hover */
            transition: background-color 0.3s;
            /* Smooth background change */
        }

        #suggestions-list li:hover {
            background-color: #f0f0f0;
            /* Light gray background on hover */
        }

        #title {
            position: relative;
            /* Make the input position relative for proper dropdown placement */
            z-index: 10;
            /* Ensure input is above other elements */
        }
    </style>
@endpush
@section('content')
    <?php
     use App\Helper\GeneralMethod;
     $currency_code =  GeneralMethod::defaultCurrency();
     $country_code =  GeneralMethod::countryCodeFun();
     
    $pagetitle = $end_date = $jobtags = $recruiterData = $recruiterDataId = $cpersonData = $carriew_website_chk = '';
    $job_id = 0;
    if ($page_event == 'ADD') {
        $pagetitle = 'Add Job';
        $rec_inc = 0;
        foreach ($recruiterlist as $recData) {
            if ($rec_inc == 0) {
                $recruiterData = $recData->name;
                $recruiterDataId = $recData->id;
            } else {
                $recruiterData = $recruiterData . ' , ' . $recData->name;
                $recruiterDataId = $recruiterDataId . ' , ' . $recData->id;
            }
            $rec_inc++;
        }
    } elseif ($page_event == 'EDIT') {
        $pagetitle = 'Edit Job';
        $end_date = @$job->end_date;
        $date = strtotime($end_date);
        $new_date = date('Y-m-d', $date);
    
        $startTimeStamp = strtotime($job->start_date);
        $endTimeStamp = strtotime($job->end_date);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 86400;
    
        $numberDays = intval($numberDays);
    
        $cnt = 0;
        foreach ($job_metronics_skills as $jobSkills) {
            if ($cnt == 0) {
                $jobtags = $jobSkills->skill_name;
            } else {
                $jobtags = $jobtags . ' , ' . $jobSkills->skill_name;
            }
            $cnt++;
        }
        $job_id = @$job->id;
    
        $rec_inc = 0;
        foreach ($recruiterlist as $recData) {
            if ($rec_inc == 0) {
                $recruiterData = $recData->name;
                $recruiterDataId = $recData->id;
            } else {
                $recruiterData = $recruiterData . ' , ' . $recData->name;
                $recruiterDataId = $recruiterDataId . ' , ' . $recData->id;
            }
            $rec_inc++;
        }
    
        $cp_inc = 0;
        foreach ($contactPersonList as $cpData) {
            if ($cp_inc == 0) {
                $cpersonData = $cpData->contact_person_name;
            } else {
                $cpersonData = $cpersonData . ' , ' . $cpData->contact_person_name;
            }
            $cp_inc++;
        }
    
        if (@$job->carrier_website == 1) {
            $carriew_website_chk = 'checked="checked"';
        }
    }
   
    ?>
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Add Requisition-->
                <div class="d-flex flex-column">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><a href="javascript:void(0);"
                            class="text-gray-800 mb-1"><?php echo @$pagetitle; ?></a></h1>
                </div>
                <!--end::Add Requisition-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Job details and submit</span>
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid pt-4 pb-0" id="kt_content">
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid px-3">
                <!--begin::Card-->
                <div class="card card-custom gutter-b">
                    <!--begin::Body-->
                    <div class="card-body p-0">
                        <!--begin::Wizard-->
                        <div class="wizard wizard-1" id="kt_contact_add" data-wizard-state="step-first"
                            data-wizard-clickable="true">
                            <!--begin::Wizard Nav-->
                            <div class="wizard-nav border-bottom">
                                <div class="wizard-steps p-8 p-lg-10">
                                    <!--begin::Basic Details-->
                                    <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                                        <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                                                <!--begin::Svg Icon-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z"
                                                            fill="#000000" fill-rule="nonzero"
                                                            transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) " />
                                                        <path
                                                            d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z"
                                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <h3 class="wizard-title">1. Job Details</h3>
                                        </div>
                                        <span class="svg-icon svg-icon-xl wizard-arrow">
                                            <!--begin::Svg Icon-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                    <rect fill="#000000" opacity="0.3"
                                                        transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)"
                                                        x="11" y="5" width="2" height="14" rx="1" />
                                                    <path
                                                        d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z"
                                                        fill="#000000" fill-rule="nonzero"
                                                        transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Basic Details-->
                                    <!--begin::Job Description-->
                                    <div class="wizard-step" data-wizard-type="step">
                                        <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                                                <!--begin::Svg Icon-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z"
                                                            fill="#000000" opacity="0.3" />
                                                        <path
                                                            d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z"
                                                            fill="#000000" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <h3 class="wizard-title">2. Job Description</h3>
                                        </div>
                                        <span class="svg-icon svg-icon-xl wizard-arrow">
                                            <!--begin::Svg Icon-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                    <rect fill="#000000" opacity="0.3"
                                                        transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)"
                                                        x="11" y="5" width="2" height="14" rx="1" />
                                                    <path
                                                        d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z"
                                                        fill="#000000" fill-rule="nonzero"
                                                        transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Job Description-->
                                    <!--begin::Custom Details-->
                                    <div class="wizard-step" data-wizard-type="step">
                                        <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                                                <!--begin::Svg Icon-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M13,18.9450712 L13,20 L14,20 C15.1045695,20 16,20.8954305 16,22 L8,22 C8,20.8954305 8.8954305,20 10,20 L11,20 L11,18.9448245 C9.02872877,18.7261967 7.20827378,17.866394 5.79372555,16.5182701 L4.73856106,17.6741866 C4.36621808,18.0820826 3.73370941,18.110904 3.32581341,17.7385611 C2.9179174,17.3662181 2.88909597,16.7337094 3.26143894,16.3258134 L5.04940685,14.367122 C5.46150313,13.9156769 6.17860937,13.9363085 6.56406875,14.4106998 C7.88623094,16.037907 9.86320756,17 12,17 C15.8659932,17 19,13.8659932 19,10 C19,7.73468744 17.9175842,5.65198725 16.1214335,4.34123851 C15.6753081,4.01567657 15.5775721,3.39010038 15.903134,2.94397499 C16.228696,2.49784959 16.8542722,2.4001136 17.3003976,2.72567554 C19.6071362,4.40902808 21,7.08906798 21,10 C21,14.6325537 17.4999505,18.4476269 13,18.9450712 Z"
                                                            fill="#000000" fill-rule="nonzero" />
                                                        <circle fill="#000000" opacity="0.3" cx="12"
                                                            cy="10" r="6" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <h3 class="wizard-title">3. Job Questions</h3>
                                        </div>
                                        <span class="svg-icon svg-icon-xl wizard-arrow">
                                            <!--begin::Svg Icon-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                    <rect fill="#000000" opacity="0.3"
                                                        transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)"
                                                        x="11" y="5" width="2" height="14" rx="1" />
                                                    <path
                                                        d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z"
                                                        fill="#000000" fill-rule="nonzero"
                                                        transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Custom Details-->
                                    <!--begin::Review and Submit-->
                                    <div class="wizard-step" data-wizard-type="step">
                                        <div class="wizard-label"> <span class="svg-icon svg-icon-4x wizard-icon">
                                                <!--begin::Svg Icon-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z"
                                                            fill="#000000" />
                                                        <circle fill="#000000" opacity="0.3" cx="18.5"
                                                            cy="5.5" r="2.5" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <h3 class="wizard-title">4. Review and Submit</h3>
                                        </div>
                                        <span class="svg-icon svg-icon-xl wizard-arrow last">
                                            <!--begin::Svg Icon-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                    <rect fill="#000000" opacity="0.3"
                                                        transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)"
                                                        x="11" y="5" width="2" height="14" rx="1" />
                                                    <path
                                                        d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z"
                                                        fill="#000000" fill-rule="nonzero"
                                                        transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Review and Submit-->
                                </div>
                            </div>
                            <!--end::Wizard Nav-->
                            <!--begin::Wizard Body-->
                            <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
                                <div class="col-xl-12 col-xxl-12">
                                    <!--begin::Form Wizard Form-->
                                    <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);"
                                        method="post" id="kt_contact_add_form" name="kt_contact_add_form">
                                        <meta name="csrf-token" content="{{ csrf_token() }}" />
                                        <!--begin::Basic Details-->
                                        <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                            <h3 class="mb-10 font-weight-bold text-dark">Job Details</h3>
                                            <div class="flex-column">
                                                <!--begin::Client | Locations | Work Location for candidate-->
                                                <div class="form-group row fv-row mb-10">
                                                    <?php 
                                                    if($global_company_type  == 3)
                                                    {
                                                    ?>
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Company</label>
                                                        <input type="text"
                                                            class="form-control form-control-lg form-control-solid"
                                                            value="<?php echo @$client[0]->name; ?>" readonly>
                                                        <input type="hidden"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="client_id" id="client_id" value="<?php echo @$client[0]->id; ?>">
                                                    </div>
                                                    <?php }else{ ?>
                                                    <div class="col-lg-6">
                                                        <label class="form-labe required">Client:</label>
                                                        <select
                                                            class="form-select form-select-lg form-select-solid client_id"
                                                            data-control="select2" data-placeholder="Select an option"
                                                            data-allow-clear="true" id="client_id" name="client_id">
                                                            @if (@$direct_add)
                                                                <option value="">Select Client</option>
                                                            @endif
                                                            <optgroup label="Internal">
                                                                <?php
                                                                foreach ($client as $clientarr) {
                                                                    if($clientarr->self_company){
                                                                    $client_id = $sel = '';
                                                                    $client_id = @$job->client_id;
                                                                    if (@$currentClient > 0) {
                                                                        $client_id = $currentClient;
                                                                    }
                                                                    if ($client_id == $clientarr->id) {
                                                                        $sel = 'selected="selected"';
                                                                    }
                                                                    echo '<option value="' . @$clientarr->id . '"   ' . $sel . '  >' . ucfirst($clientarr->name) . '</option>';
                                                                }
                                                                }
                                                                ?>
                                                            </optgroup>
                                                            <optgroup label="Clients">
                                                                <?php
                                                                foreach ($client as $clientarr) {
                                                                    if(!$clientarr->self_company){
                                                                    $client_id = $sel = '';
                                                                    $client_id = @$job->client_id;
                                                                    if (@$currentClient > 0) {
                                                                        $client_id = $currentClient;
                                                                    }
                                                                    if ($client_id == $clientarr->id) {
                                                                        $sel = 'selected="selected"';
                                                                    }
                                                                    echo '<option value="' . @$clientarr->id . '"   ' . $sel . '  >' . ucfirst($clientarr->name) . '</option>';
                                                                }
                                                                }
                                                                ?>
                                                            </optgroup>

                                                        </select>
                                                        <span class="form-text text-muted">*Please select the
                                                            client with openings</span>
                                                    </div>
                                                    <?php } ?>

                                                    <div class="col-lg-6" style="position: relative;">
                                                        <label class="form-label">Select the Address:</label>
                                                        <select class="form-select form-select-solid" id="address_id"
                                                            name="address_id">
                                                            <option value="" selected="selected">Select the Address
                                                            </option>

                                                        </select>
                                                        <span class="form-text text-muted">Please select the address of
                                                            your company/client</span>
                                                        <div class="address_show"
                                                            style="position: absolute; right: 13px; top: 32px;display: none;">
                                                            <a href="javascript:;"
                                                                class="btn btn-primary btn-sm add_address">Add Address</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">

                                                        {{-- <input type="hidden" class="form-control form-control-solid" value="{{ $default_loc }}" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter your Location..."/> kt_select2_3-- }}


                          {{-- <label class="form-label required">Work City for candidate:</label> --}}


                                                        {{-- <select class="form-control select2" id="kt_select2_2" name="kt_select2_2[]" multiple="multiple">
                            <optgroup label="Select Work Location for candidate">
                               <?php
                               
                               //  foreach($jobCity as $jobCityval){
                               //   $sel = '';
                               //   if(in_array($jobCityval->city_id,$jobCitySelected)){
                               //     $sel =	"selected";
                               //   }
                               //  echo '<option value="'.$jobCityval->city_id.'" '.$sel.'>'.ucfirst($jobCityval->city).'</option>';
                               //   }
                               ?>

                            </optgroup>
                          </select> --}}
                                                        {{-- <span class="form-text text-muted">Please select work location for candidate</span>  --}}
                                                    </div>
                                                </div>
                                                <!--end::Client | Locations | Work Location for candidate-->


                                                <!--begin::Job Title | No.of Openings | Job Department-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Enter Job
                                                            Title/Designation:</label>
                                                        <div class="input-group input-group-lg input-group-solid">
                                                            <div class="input-group-prepend"> <span
                                                                    class="input-group-text"> <i class="la la-user"></i>
                                                                </span>
                                                            </div>
                                                            <input type="text"
                                                                class="form-control form-control-lg form-control-solid"
                                                                name="title" id="title"
                                                                value="<?php echo @$job->title; ?>" placeholder="Enter job title"
                                                                onkeyup="getSuggestions(this.value)">
                                                            <div id="suggestions" style="display: none;">
                                                                <ul id="suggestions-list"></ul>
                                                            </div>
                                                        </div>
                                                        <span class="form-text text-muted">*Please enter the job title or
                                                            designation of the openings.</span>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Job Department:</label>
                                                        <select class="form-select form-select-solid" name="category_id"
                                                            id="category_id">
                                                            <option value="">Select Job Department</option>
                                                            <?php
                                                            foreach ($categories as $category) {
                                                                $category_id = $sel = '';
                                                                $category_id = @$job->category_id;
                                                                if ($category_id == @$category->id) {
                                                                    $sel = 'selected="selected"';
                                                                }
                                                                echo '<option value="' . @$category->id . '"  ' . $sel . '>' . ucfirst($category->name) . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="form-text text-muted">*Please select the department
                                                            for this job.</span>
                                                    </div>
                                                </div>
                                                <!--end::Job Title | Total Openings | Job Department-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-12">

                                                        <div class="d-flex flex-column mb-3 fv-row">
                                                            <label class="form-label">Enter the Job/Work Location of the
                                                                Candidate (Select Maximum 15 cities):</label>
                                                            <input class="form-control form-control-solid"
                                                                value="" id="kt_tagify_work_location"
                                                                name="kt_tagify_work_location"
                                                                placeholder="Enter your Location..." />
                                                            <span class="form-text text-muted">*Enter the location of the
                                                                job.</span>
                                                        </div>

                                                        {{-- <select class="form-control city_data" id="city_data" name="kt_select2_2[]" multiple="multiple">
                              <optgroup label="Select city">
                                
                              </optgroup>
                            </select>  --}}
                                                    </div>
                                                </div>

                                                <!--begin::Position Type | Work from home | Show client name to vendors-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Position Type:</label>
                                                        <select class="form-select form-select-solid"
                                                            aria-label="Select position type" name="employment_type"
                                                            id="employment_type">
                                                            <option value="">Select Position Type</option>
                                                            <?php
                                                            foreach ($employmentType as $key => $data_value) {
                                                                $employment_type = $sel = '';
                                                                $employment_type = @$job->employment_type;
                                                                if ($employment_type == $key) {
                                                                    $sel = 'selected="selected"';
                                                                }
                                                                echo '<option value="' . $key . '"   ' . @$sel . '>' . $data_value . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="form-text text-muted">Please select your position
                                                            type</span>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Work Mode</label>
                                                        <select name="work_from_home" id="work_from_home"
                                                            class="form-select form-select-solid"
                                                            aria-label="Select work from home">
                                                            <option value="no" <?php if (@$job->work_type == 'no') {
                                                                echo 'selected';
                                                            } ?>>Work From Office</option>
                                                            <option value="yes" <?php if (@$job->work_type == 'yes') {
                                                                echo 'selected';
                                                            } ?>>Remote</option>
                                                             <option value="other" <?php if (@$job->work_type == 'other') {
                                                                echo 'selected';
                                                            } ?>>Hybrid</option>
                                                        </select>
                                                        <span class="form-text text-muted">*Please confirm if this is a
                                                            remote opportunity.</span>
                                                    </div>
                                                    <input type="hidden"
                                                        class="form-control form-control-lg form-control-solid"
                                                        name="show_client_name" id="show_client_name" value="no">
                                                    <div class="col-lg-4 d-none">
                                                        <label class="form-label required">Show client name to
                                                            vendors:</label>
                                                        <select name="show_client_name" id="show_client_name"
                                                            class="form-select form-select-solid"
                                                            aria-label="Select client name to vendors">
                                                            <option value="no">Select Show client name to vendors
                                                            </option>
                                                            <option value="no" <?php if (@$job->show_vendor == 'no') {
                                                                echo 'selected';
                                                            } ?>>No</option>
                                                            <!-- default value -->
                                                            <option value="yes" <?php if (@$job->show_vendor == 'yes') {
                                                                echo 'selected';
                                                            } ?>>Yes</option>
                                                        </select>
                                                        <span class="form-text text-muted">Please select show client name
                                                            to vendors</span>
                                                    </div>
                                                </div>
                                                <!--end::Position Type | Work from home | Show client name to vendors-->
                                                <!--begin::Minimum Experience | Maximum Experience | Qualifaction-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Minimum Experience:</label>
                                                        <select class="form-select form-select-solid" name="min_exp"
                                                            id="min_exp">
                                                            <option value="" disabled selected>Select Experience
                                                            </option>
                                                            <option value="0">Freshers</option>
                                                            <?php
                                                            $yearText = 'Year';
                                                            for ($i = 1; $i <= 30; $i++) {
                                                                if ($i > 9) {
                                                                    $yearText = 'Years';
                                                                }
                                                                $min_exp = $sel = '';
                                                                $min_exp = @$job->min_exp;
                                                                if ($min_exp == @$i) {
                                                                    $sel = 'selected="selected"';
                                                                }
                                                                echo '<option value="' . $i . '"   ' . $sel . '>' . $i . ' &nbsp; ' . $yearText . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="form-text text-muted">Please enter minimum experience
                                                            for this job</span>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Maximum Experience:</label>
                                                        <select class="form-select form-select-solid" name="max_exp"
                                                            id="max_exp">
                                                            <option value="" disabled selected>Select Experience
                                                            </option>
                                                            <option value="0">Freshers</option>
                                                            <?php
                                                            $yearText = 'Year';
                                                            for ($i = 1; $i <= 30; $i++) {
                                                                if ($i > 9) {
                                                                    $yearText = 'Years';
                                                                }
                                                                $max_exp = $sel = '';
                                                                $max_exp = @$job->max_exp;
                                                                if ($max_exp == @$i) {
                                                                    $sel = 'selected="selected"';
                                                                }
                                                                echo '<option value="' . $i . '"  ' . $sel . '>' . $i . ' &nbsp; ' . $yearText . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <span class="form-text text-muted">Please enter minimum experience
                                                            for this job</span>
                                                    </div>
                                                </div>
                                                <!--end::Minimum Experience | Maximum Experience | Qualifaction-->
                                                <!--begin::Salary Range | Due Date-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-8 d-none">
                                                        <label class="form-label required">Salary Range</label>
                                                        <div class="row">
                                                            <div class="">
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency" name="salary_currency"
                                                                    id="salary_currency">
                                                                    <?php
                                                                    foreach ($currency as $key => $val) {
                                                                        $min_salary_currency = $sel = '';
                                                                        $min_salary_currency = @$job->min_salary_currency;
                                                                        if ($min_salary_currency == @$key) {
                                                                            $sel = 'selected="selected"';
                                                                        }
                                                                        echo '<option value="' . $key . '"  ' . $sel . '>' . $val . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="">
                                                                <input type="number"
                                                                    class="form-control form-control-solid"
                                                                    name="min_salary" id="min_salary" placeholder="From"
                                                                    value="<?php echo @$job->min_salary; ?>">
                                                            </div>
                                                            <div class="">
                                                                <input type="number"
                                                                    class="form-control form-control-solid" name="exp_ctc"
                                                                    id="exp_ctc" placeholder="To" value="">
                                                            </div>
                                                            <div class="">
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Annual Salary"
                                                                    name="salary_time_mode" id="salary_time_mode">
                                                                    <?php
                                                                    foreach ($salaryMode as $key => $val) {
                                                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <span class="form-text text-muted">Please enter your salary
                                                            range</span>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Minimum Salary</label>
                                                        <div class="row">
                                                            <div class="col-3 px-1">
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency"
                                                                    name="min_salary_currency" id="min_salary_currency">
                                                                    <?php
                                                                    foreach ($currency as $key => $val) {
                                                                        $min_salary_currency = $sel = '';
                                                                        $min_salary_currency = @$job->min_salary_currency;
                                                                        if($min_salary_currency===null){
                                                                            if ($currency_code==$val) {
                                                                                $sel = 'selected="selected"';
                                                                            }
                                                                        }else{
                                                                            if ($min_salary_currency == @$key) {
                                                                                $sel = 'selected="selected"';
                                                                            }
                                                                        }
                                                                        echo '<option value="' . $key . '"  ' . @$sel . '>' . $val . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-5 px-0">
                                                                <input type="number"
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    name="minimum_salary" id="minimum_salary"
                                                                    placeholder="Enter min salary"
                                                                    value="<?php echo @$job->min_salary; ?>">
                                                            </div>
                                                            <div class="col-4 px-1">
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Annual Salary" name="min_sal_mode"
                                                                    id="min_sal_mode">
                                                                    <?php
                                                                    foreach ($salaryMode as $key => $val) {
                                                                        $min_sal_mode = $sel = '';
                                                                        $min_sal_mode = @$job->min_sal_mode;
                                                                        if ($min_sal_mode == @$key) {
                                                                            $sel = 'selected="selected"';
                                                                        }
                                                                        echo '<option value="' . $key . '"   ' . $sel . '>' . $val . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <span class="form-text text-muted minimum_salary_word">*Please
                                                            enter the minimum salary you would like to offer for this job
                                                            position.</span>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Maximum Salary</label>
                                                        <div class="row">
                                                            <div class="col-3 px-1">
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Currency"
                                                                    name="max_salary_currency" id="max_salary_currency">
                                                                    <?php
                                                                    foreach ($currency as $key => $val) {
                                                                        $max_salary_currency = $sel = '';
                                                                        $max_salary_currency = @$job->max_salary_currency;
                                                                        if($max_salary_currency===null){
                                                                            if ($currency_code == $val) {
                                                                                $sel = 'selected="selected"';
                                                                            }
                                                                        }else{
                                                                            if ($max_salary_currency == @$key) {
                                                                                $sel = 'selected="selected"';
                                                                            }
                                                                        }
                                                                        echo '<option value="' . $key . '"  ' . @$sel . '>' . $val . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-5 px-0">
                                                                <input type="number"
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    name="maximum_salary" id="maximum_salary"
                                                                    placeholder="Enter max salary"
                                                                    value="<?php echo @$job->max_salary; ?>">
                                                            </div>
                                                            <div class="col-4 px-1">
                                                                <select class="form-select form-select-solid"
                                                                    arial-label="Select Annual Salary" name="max_sal_mode"
                                                                    id="max_sal_mode">
                                                                    <?php
                                                                    foreach ($salaryMode as $key => $val) {
                                                                        $max_sal_mode = $sel = '';
                                                                        $max_sal_mode = @$job->max_sal_mode;
                                                                        if ($max_sal_mode == @$key) {
                                                                            $sel = 'selected="selected"';
                                                                        }
                                                                        echo '<option value="' . $key . '"   ' . $sel . '>' . $val . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <span class="form-text text-muted maximum_salary_word">*Please
                                                            enter the maximum salary you would like to offer for this job
                                                            position.</span>
                                                    </div>

                                                </div>
                                                <!--end::Start Date | Due Date | Status-->

                                                <div class="form-group row fv-row mb-10">                                                    
                                                    <div class="col-lg-4">
                                                        <label class="form-label">Gender Diversity</label>
                                                        <select class="form-select form-select-solid" arial-label="Select Gender Diversity" name="required_columns" id="required_columns">
                                                            <option value="Rather Not Say" {{ (@$job->required_columns==='Rather Not Say') ? 'selected' : '' }}>Rather Not Say</option> 
                                                            <option value="Male" {{ (@$job->required_columns==='Male') ? 'selected' : '' }}>Male</option>
                                                            <option value="Female" {{ (@$job->required_columns==='Female') ? 'selected' : '' }}>Female</option>
                                                        </select>
                                                        <span class="form-text text-muted">Please select gender</span> 
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <label class="form-label required">Number of Openings:</label>
                                                        <div class="input-group input-group-lg input-group-solid">
                                                            <div class="input-group-prepend"> <span
                                                                    class="input-group-text"> <i
                                                                        class="la la-briefcase"></i> </span> </div>
                                                            <input type="number"
                                                                class="form-control form-control-lg form-control-solid"
                                                                name="total_positions" id="total_positions"
                                                                value="<?php echo @$job->total_positions; ?>"
                                                                placeholder="Enter number of openings">
                                                        </div>
                                                        <span class="form-text text-muted">Please enter your number of
                                                            openings</span>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <label class="form-label">Work Shifts:</label>
                                                        <select class="form-select form-select-solid"
                                                        arial-label="Select Work shifts" name="work_shifts" id="work_shifts">
                                                        <option value="Day Shift" @if (@$job->work_shifts == 'Day Shift') selected @endif>Day Shift</option>
                                                        <option value="Night Shift" @if (@$job->work_shifts == 'Night Shift') selected @endif>Night Shift</option>
                                                        <option value="Rotational shift" @if (@$job->work_shifts == 'Rotational shift') selected @endif>Rotational shift </option>
                                                        <option value="UK Timezone" @if (@$job->work_shifts == 'UK Timezone') selected  @endif> UK Timezone</option>
                                                        <option value="US Timezone" @if (@$job->work_shifts == 'US Timezone') selected  @endif> US Timezone</option>
                                                    </select>
                                                    </div>
                                                   
                                                </div>
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-6">
                                                        <label class="form-label">Qualification (Please select up to 5
                                                            options):</label>
                                                        {{-- <select class="form-select form-select-lg form-select-solid"  name="qualification" id="qualification"> --}}
                                                        <?php
                                                        // foreach($qualification as $data_value){
                                                        //   $qualification		=	$sel	=	'';
                                                        //   $qualification		=	@$job->qualification;
                                                        //   if($qualification == @$data_value->id ){
                                                        //     $sel		=	'selected="selected"' ;
                                                        //   }
                                                        //   echo '<option value="'.@$data_value->id.'"  '.$sel.'>'.@$data_value->name.'</option>';
                                                        // }
                                                        ?>
                                                        {{-- </select> --}}


                                                        <input class="form-control form-control-solid"
                                                            value="{{ @$default_qualification }}" id="qualification"
                                                            name="qualification" placeholder="Enter your Location..." />


                                                        <span class="form-text text-muted">*Please select the
                                                            qualifications required for this job.</span>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        @php
                                                            $selected = '';
                                                            if (@$numberDays == '') {
                                                                $selected = 'selected';
                                                            }

                                                        @endphp
                                                        <label class="form-label required">Job automatically closed
                                                            in:</label>
                                                        <select class="form-select form-select-solid"
                                                            arial-label="Select Due Days" name="end_date" id="end_date">
                                                            <option value="15"
                                                                @if (@$numberDays == 15) selected @endif>15 Days
                                                            </option>
                                                            <option value="30"
                                                                @if (@$numberDays == 30) selected @endif>30 Days
                                                            </option>
                                                            <option value="45"
                                                                @if (@$numberDays == 45) selected @endif>45 Days
                                                            </option>
                                                            <option value="60"
                                                                @if (@$numberDays == 60) selected @else {{ $selected }} @endif>
                                                                60 Days</option>
                                                        </select>

                                                        <!--<input type="number" class="form-control form-control-solid"  name="end_date" id="end_date" placeholder="Enter due date" value="<?php echo @$new_date; ?>">-->
                                                        <span class="form-text text-muted">*Please enter the duration after
                                                            which the job will be automatically closed.</span>
                                                    </div>

                                                    
                                                    


                                                </div>

                                                <!--begin::Point of contact from client | Recruiter-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-6" style="position: relative;">
                                                        <label class="form-label required">Select the Hiring Manager/HR for
                                                            this job position from the {{ ($global_company_type==3) ? 'company' : 'client' }}.</label>
                                                        <input class="form-control form-control-solid hiring_namager"
                                                            name="hiring_namager" id="hiring_namager"
                                                            value="<?php echo @$cpersonData; ?>" readonly="" />
                                                        <span class="form-text text-muted">Please select your Point of
                                                            contact from client</span>
                                                            <div class=""
                                                            style="position: absolute; right: 13px; top: 29px;">
                                                            <a href="javascript:;"
                                                                class="btn btn-primary btn-sm hr_manager_add">View List</a>
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-6" style="position: relative;">
                                                        <label class="form-label required">Select your team who will work
                                                            on this job position.</label>
                                                        <input class="form-control form-control-solid" name="recruiter"
                                                            id="recruiter" value="<?php echo strtolower(@$recruiterData); ?>" readonly="" />
                                                             <input type="hidden" class="form-control form-control-solid" name="recruiter_id" id="recruiter_id" value="<?php echo strtolower(@$recruiterDataId); ?>" readonly="" />
                                                        <span class="form-text text-muted">Please select your Assign
                                                            internal team/recruiter for this requisition</span>
                                                            <div class=""
                                                            style="position: absolute; right: 13px; top: 29px;">
                                                            <a href="javascript:;"
                                                                class="btn btn-primary btn-sm team_manager_add">View List</a>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 d-none">
                                                        <label class="form-label">Country allow:</label>
                                                        <select class="form-control select2" id="kt_select2_4"
                                                            name="kt_select2_4[]" multiple="multiple">
                                                            <optgroup label="Select Country for Vendor">
                                                                <?php
                                                                
                                                                foreach ($countries as $val) {
                                                                    $sel = '';
                                                                    if (in_array($val->id, $valSelected)) {
                                                                        $sel = 'selected';
                                                                    } elseif ($val->id == $country) {
                                                                        $sel = 'selected';
                                                                    }
                                                                    echo '<option value="' . $val->id . '" ' . $sel . '>' . ucfirst($val->country_name) . '</option>';
                                                                }
                                                                
                                                                ?>

                                                            </optgroup>
                                                        </select>
                                                        <span class="form-text text-muted">Please select your
                                                            country</span>
                                                    </div>

                                                </div>


                                                <!--end::Point of contact from client | Recruiter-->
                                            </div>
                                        </div>

                                        <!--end::Basic Details-->
                                        <!--begin::Job Description-->
                                        <div class="pb-5" data-wizard-type="step-content">
                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="form-group row">
                                                        <div class="col-lg-9 col-xl-6">
                                                            <h3 class="font-weight-bold text-dark required">Enter Job
                                                                Description</h3>
                                                        </div>
                                                    </div>
                                                    <!--begin::Job Description-->
                                                    <div class="form-group row fv-row mb-10">
                                                        <div class="col-lg-12">
                                                            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" class="tox-target"><?php echo @$job->job_description; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <!--end::Job Description-->
                                                    <!--begin::Skills-->
                                                    <div class="form-group row fv-row mb-10">
                                                        <div class="col-lg-12">
                                                            <label class="form-label">Skills:</label>
                                                            <input class="form-control form-control-solid"
                                                                value="<?php echo @$jobtags; ?>" id="tags"
                                                                name="tags" />
                                                            <span class="form-text text-muted">Please select your
                                                                skills</span>
                                                        </div>
                                                    </div>
                                                    <!--end::Skills-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Job Description-->
                                        <!--begin::Custom Details-->
                                        <div class="pb-5" data-wizard-type="step-content">
                                            <h3 class="mb-10 font-weight-bold text-dark">Enter Job Questions(Optional)</h3>
                                            <div class="form-group">
                                                <div class="col-sm-12 px-0">
                                                    <div class="form-group mb-0" id="addmore"> </div>
                                                    <?php 
                      $customTitleId = 0;
                      // dd($additionalDetail);
                      if($page_event == 'EDIT' && count($additionalDetail) > 0)
                        {
                          $html		=	'';
                          $inc_first	=	0;
                          $inc_last	=	1;
                          // dd($additionalDetail);
                           $html	.=	'<div class="row custom-detail-container">';
                          foreach($additionalDetail as $aDetailArr)
                          {
                            // if($inc_first %3 == 0){
                             
                               $html	.=	'<div class="form-group col-md-4 mb-0 mt-7 client-cperson-container-child" id="remove">
                              <div class="input-group">';	
                            // }	
                            $html		.=	'<input type="text" name="custom_title[]" id="custom_title_'.$customTitleId.'" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter job questions (Optional)" value="'.$aDetailArr->detail_value.'">';
                        
                            // if(($inc_last %3 == 0)&& $inc_last > 0){
                              $html		.='</div></div>';
                            // }
                        $inc_first++;
                        $inc_last++;
                        $customTitleId++;	
                        }
                         $html	.=	'</div>';
                        echo $html;
                        $customTitleId--;
                      }
                      else 
                      {
                      ?>
                                                    <div class="row custom-detail-container">
                                                        <div class="form-group mb-0 mt-7 client-cperson-container-child"
                                                            id="remove">
                                                            <div class="input-group">
                                                                <input type="text" name="custom_title[]"
                                                                    id="custom_title_<?php echo $customTitleId; ?>"
                                                                    class="form-control form-control-lg form-control-solid rounded me-5"
                                                                    placeholder="Enter job questions (Optional)">
                                                                <input type="text" name="custom_title[]"
                                                                    id="custom_title_<?php echo $customTitleId; ?>"
                                                                    class="form-control form-control-lg form-control-solid rounded me-5"
                                                                    placeholder="Enter job questions (Optional)">
                                                                <input type="text" name="custom_title[]"
                                                                    id="custom_title_<?php echo $customTitleId; ?>"
                                                                    class="form-control form-control-lg form-control-solid rounded me-5"
                                                                    placeholder="Enter job questions (Optional)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <input type="hidden" name="customTitleId" id="customTitleId"
                                                        value="<?php echo @$customTitleId; ?>" />
                                                    <div class="d-flex justify-content-center py-5 my-5">
                                                        <button type="button"
                                                            class="btn btn-light-success font-weight-bolder text-uppercase me-5 add_custom_title d-flex">
                                                            <!--begin::Svg Icon-->
                                                            <span class="svg-icon svg-icon svg-icon me-0">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect fill="#000000" x="4" y="11" width="16"
                                                                            height="2" rx="1"></rect>
                                                                        <rect fill="#000000" opacity="0.3"
                                                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                            x="4" y="11" width="16" height="2"
                                                                            rx="1"></rect>
                                                                    </g>
                                                                </svg>
                                                            </span> <span> Add more </span> </button>
                                                        <button type="button"
                                                            class="btn btn-light-danger font-weight-bolder text-uppercase remove_cperson d-flex">
                                                            <!-- <i class="fa fa-plus"></i> -->
                                                            <!--begin::Svg Icon-->
                                                            <span class="svg-icon svg-icon svg-icon-2x">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect fill="#000000" x="4" y="11" width="16"
                                                                            height="2" rx="1"></rect>
                                                                    </g>
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                            <span> Remove </span> </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pb-15" data-wizard-type="step-content">
                                            <h4 class="mb-10 font-weight-bold">Review your Details and Submit</h4>
                                            <h6 class="font-weight-bold mb-3">Jobs Details:</h6>
                                            <div class="row pt-7">
                                                <div class="col-lg-6">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Ciient</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_client_name"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Job Title</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_jobtitle"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Minimum
                                                            Experience</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_min_exp"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Maximum
                                                            Experience</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_max_exp"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Minimum
                                                            Salary</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_min_sal"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Maximum
                                                            Salary</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_max_sal"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label
                                                            class="col-lg-4 fw-bold text-muted fs-6">Qualification</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_qualification"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Contact
                                                            Person</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_contact_person"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Recruiter</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_recruiter"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6 ">Job
                                                            Locations</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold fs-6 text-gray-800 fs-6 span_location"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Position
                                                            Type</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold fs-6 text-gray-800 fs-6 span_job_type"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Total
                                                            Openings</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold fs-6 text-gray-800 fs-6 span_openings"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Job
                                                            Department</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bolder fs-6 text-gray-800 fs-6 span_department"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted fs-6">Due Date</label>
                                                        <div class="col-lg-8"> <span
                                                                class="fw-bold fs-6 text-gray-800 fs-6 span_due_date"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="separator separator-dashed mt-5 mb-10"></div>
                                            <h6 class="font-weight-bold mb-3">Job Description:</h6>
                                            <div class="row pt-7 span_description"></div>
                                            <div class="row pt-7">
                                                <div class="col-lg-12">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-2 fw-bold text-muted fs-6">Skills</label>
                                                        <div class="col-lg-10"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_skills"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row pt-7">
                                                <div class="col-lg-12">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-2 fw-bold text-muted fs-6">Job
                                                            Questions</label>
                                                        <div class="col-lg-10"> <span
                                                                class="fw-bold text-gray-800 fs-6 span_custom_question_title"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="separator separator-dashed mt-5 mb-10 d-none"></div>
                                            <div class="col-lg-9 fv-row d-none">
                                                <div class="d-flex align-items-center mt-3">
                                                    <label
                                                        class="form-check form-check-inline form-check-solid me-5 cursor-pointer">
                                                        <input class="form-check-input" name="carrier_website"
                                                            type="checkbox" <?php echo @$carriew_website_chk; ?> value="1">
                                                        <span class="fw-bold ps-2 fs-6">Post this Job on Career
                                                            Site.</span> </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center border-top pt-10">
                                            <div class="mr-2">
                                                <button type="button"
                                                    class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4"
                                                    data-wizard-type="action-prev">Back</button>
                                            </div>
                                            <div>
                                                <?php
                                                if ($page_event == 'EDIT') {
                                                    echo '<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 updatefrm" data-wizard-type="action-submit1" style="display:none;">Update</button>';
                                                } else {
                                                    echo '<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4 submitfrm" data-wizard-type="action-submit1" style="display:none;">Submit</button>';
                                                }
                                                ?>
                                                <button type="button"
                                                    class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4"
                                                    data-wizard-type="action-next">Continue</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="kt_activitiesClient_toggle"></div>
    <div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle"
        data-kt-drawer-close="#kt_activitiesClient_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                            style="float:left"> <a href="javascript:void(0);" class="l">
                                <div class="symbol-label border-radius-5" id="light_client_caption">&nbsp;</div>
                            </a> 
                        </div>
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_client_name">&nbsp;</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4">(Contacts Person/Hiring Manager)</span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesClient_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
        </div>
    </div>

    <div id="kt_add_address_toggle"></div>
    <div id="kt_add_address" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_add_address_toggle"
        data-kt-drawer-close="#kt_add_address_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                            style="float:left"> <a href="javascript:void(0);" class="l">
                                <div class="symbol-label border-radius-5" id="light_address_caption">&nbsp;</div>
                            </a> </div>
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_address">&nbsp;</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_add_address_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_add_address_body">

                <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
                    <div class="col-xl-12 col-xxl-12">
                        <!--begin::Form Wizard Form-->
                        <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);" method="post"
                            id="frmupdateAddress" name="frmupdateAddress" enctype="multipart/form-data">
                            <meta name="csrf-token" content="{{ csrf_token() }}" />
                            <div class="pb-5" data-wizard-type="step-content">
                                <div class="row client-address-container">
                                    <div class="row client-address-container-child" id="clientAddr_0">
                                        <div class="form-group">
                                            <label class="form-label required fw-bolder">Address: 1</label>
                                            <input type="text" class="form-control form-control-lg form-control-solid"
                                                name="address[]" placeholder="Enter address" value=""
                                                id="address_0">
                                            <span class="form-text text-muted">Please enter your Address</span>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group fv-plugins-icon-container">
                                                <label class="form-label required fw-bolder">Select City:</label>
                                                <input class="form-control form-control-solid" value=""
                                                    id="kt_tagify_work_location_one" name="kt_tagify_work_location_one[]"
                                                    placeholder="Enter your Location..." />
                                                <span class="form-text text-muted">Please select your city</span>
                                                <div class="fv-plugins-message-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="addressLoader"> </div>
                                <input type="hidden" name="clientId" id="clientId" value="0" />
                                <div class="d-flex justify-content-center py-5 my-5">

                                    <button type="button"
                                        class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4 frmupdateAddress"
                                        data-wizard-type="action-next">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Address Details-->




            </div>
        </div>
    </div>


    <div id="kt_activitiesRecruiter_toggle"></div>
    <div id="kt_activitiesRecruiter" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesRecruiter_toggle"
        data-kt-drawer-close="#kt_activitiesRecruiter_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <div class=" d-flex align-items-center">
                        @php
                            $logo_path = @$companylogo;
                            // dd($logo_path );
                        @endphp
                        @if (!empty($companylogo))
                            <div data-row-id="' . $row->id . '"
                                class="symbol symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5">
                                <a href="javascript:void(0);">
                                    <div class="symbol-label border-radius-5">
                                        <img src="{{ @$logo_path }}" alt="Company Logo" class="border-radius-5 w-100">
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"
                                style="float:left"> <a href="javascript:void(0);" class="l">
                                    <div class="symbol-label border-radius-5" id="light_recruiter_caption">&nbsp;</div>
                                </a>
                            </div>
                        @endif

                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"
                                id="light_recruiter_name_n">{{ @$company_name }}</a> </div>
                    </div>
                    <span class="h-20px border-gray-200 border-start mx-4">(Team Member)</span>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                        id="kt_activitiesRecruiter_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="currentColor">
                                    <rect x="0" y="7" width="16" height="2" rx="1" />
                                    <rect opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="kt_activitiesRecruiter_body"> &nbsp; </div>
        </div>
    </div>


    <!--begin::Add Contact Person Modal-->
    <div class="modal fade" id="add_team_member" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header" id="add_team_member_header">
                    <h2 class="fw-bolder">Add Contact Person</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-danger add_team_member_close" data-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="currentColor">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" />
                            </svg>
                        </span>
                    </div>


                </div>
                <div class="modal-body scroll-y mx-5">
                    <form id="add_team_member_form" name="add_team_member_form" class="form" action="#"
                        method="post">
                        <meta name="csrf-token" content="{{ csrf_token() }}" />
                        @csrf
                        @method('POST')
                        <div class="row client-cperson-row">
                            <div class="row client-cperson-container" style="position: relative;">
                                <span class="form-text text-muted form-label fw-bolder countContact"
                                    style="position: absolute; top: 0;"></span>
                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="text" name="cp_name" id="cp_name_0"
                                            class="form-control form-control-lg form-control-solid rounded me-5"
                                            value="" placeholder="Enter person name">
                                        <span class="form-text text-muted">Please enter contact person name</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="email" name="cp_email" id="cp_email_0"
                                            class="form-control form-control-lg form-control-solid rounded me-5"
                                            value="" placeholder="Enter email">
                                        <span class="form-text text-muted">Please enter contact email</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="number" name="cp_number" maxlength="10" id="cp_number_0"
                                            class="form-control form-control-lg form-control-solid rounded me-5"
                                            value="" placeholder="Enter contact number">
                                            <input type="hidden" value="{{ @$country_code }}" id="phone_country_code" name="phone_country_code">
                                        <span class="form-text text-muted">Please enter contact number</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <input type="text" name="cp_desigination" id="cp_desigination_0"
                                            class="form-control form-control-lg form-control-solid rounded" value=""
                                            placeholder="Enter desigination" style="">
                                        <span class="form-text text-muted">Please enter desigination</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>

                                <div class="text-center col-xl-12">
                                    <button type="reset" class="btn btn-light me-3" data-dismiss="modal">Close</button>
                                    <button class="btn btn-primary "
                                        id="hiring_contact_person_add"> <span class="indicator-label">Submit</span> <span
                                            class="indicator-progress">Please wait... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Add  Contact Person popup-->
@endsection



@push('footer-script')
    <script>
    $('#cp_number_0').on('input', function () {
        this.value = this.value.slice(0, 10);
    });

  $(document).on("change", 'input[name="cp_email"]', function () {

      let input  = $(this);                 // current input
      let email  = input.val();

      if (email !== '') {

          $('.page-loader').css('display', 'flex');

          $.ajax({
              url: "{{ route('valid-email-chk') }}",
              type: "POST",
              data: {
                  _token: "{{ csrf_token() }}",
                  email: email
              },

              success: function (response) {

                  $('.page-loader').hide();

                  if (response.status === 'fail') {

                      input.val('');

                      Swal.fire({
                          text: "Sorry, Email [" + email + "] is already registered, Please use another email.",
                          icon: "error",
                          confirmButtonText: "Ok, got it!",
                          customClass: { confirmButton: "btn btn-light" }
                      });

                  } else if (response.status === 'emailchecker') {

                      input.val('');

                      Swal.fire({
                          text: response.message,
                          icon: "error",
                          confirmButtonText: "Ok, got it!",
                          customClass: { confirmButton: "btn btn-light" }
                      });

                  } else {

                      // ✅ valid email
                      $('.verification_email').val(email);
                  }
              },

              error: function () {
                  $('.page-loader').hide();

                  Swal.fire({
                      text: "Server error, please try again",
                      icon: "error"
                  });
              }
          });
      }
  });


$(document).ready(function () {
    // Initialize intl-tel-input for Primary and Secondary phone fields
    const primaryPhoneInput = window.intlTelInput(document.querySelector("#cp_number_0"), {
      separateDialCode: true,
      initialCountry: "{{ $country_code }}",
      preferredCountries: ["in", "us", "gb", "ca", "au"],
    });


      // Function to update hidden country code inputs
    function updateCountryCode(inputField, hiddenField) {
      const selectedCountryData = inputField.getSelectedCountryData();
      //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
      const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
      $(hiddenField).val(countryCode);

    }
    // Update country code on input or country flag selection
    $("#cp_number_0").on("input change", function () {
      updateCountryCode(primaryPhoneInput, "#phone_country_code");
    });

   
    // Update country code dynamically when the user selects a different country flag
    $(".iti__flag-container").on("click", function (event) {
      const targetInput = $(event.target).closest(".iti").find("input");

      if (targetInput.is("#cp_number_0")) {
        updateCountryCode(primaryPhoneInput, "#phone_country_code");
      } 
      
    });

    setTimeout(function () {	
      $(".iti__flag-container").click();
    },500);

  });


        // tinymce.init({
        //     selector: '#kt_docs_tinymce_basic'
        // });
    </script>
    <!--Job Description Script End-->
    <!--Tinymce Editor-->
    <script src="{{ asset('demo/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
    <script src="{{ asset('demo/dist/assets/js/custom/documentation/editors/tinymce/hidden.js') }}"></script>
    <script>
        function opensidebar() {
            $('#kt_activitiesClient_toggle').trigger('click');
        }
    </script>
    <!--Tinymce Editor End-->
    <!--begin::Page Scripts-->
    <script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/v7/js/add-requisition.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>

    <script>
        function getSuggestions(query) {
            if (query.length > 2) { // Start suggesting after 3 characters
                $.ajax({
                    url: "{{ url('service_company/') }}" + '/get-job-suggestions', // Your backend endpoint
                    type: 'GET',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        let suggestions = data; // Use data directly if it's already parsed as JSON
                        $('#suggestions-list').empty(); // Clear previous suggestions

                        if (suggestions.length > 0) {
                            $('#suggestions').show(); // Show suggestion box

                            suggestions.forEach(function(suggestion) {
                                $('#suggestions-list').append('<li onclick="selectSuggestion(\'' +
                                    suggestion + '\')">' + suggestion + '</li>');
                            });

                        } else {
                            $('#suggestions').hide(); // Hide suggestion box if no suggestions
                        }
                    }
                });
            } else {
                $('#suggestions').hide();
            }
        }

        // Function to position the suggestions list


        // Hide suggestions when clicking outside
        $(document).click(function(event) {
            const target = $(event.target);
            if (!target.closest('#suggestions').length && !target.is('#title')) {
                $('#suggestions').hide(); // Hide suggestions if clicked outside
            }
        });

        $(document).on('change', '#title', function() {
            changeautofillfield();
        });



        function selectSuggestion(suggestion) {
            $('#title').val(suggestion);
            $('#suggestions').hide();
            changeautofillfield();
        }




        $(document).ready(function() {
            $('.address_show').hide();

            // Toggle the dropdown on button click
            $('#kt_header_user_menu_toggle').click(function(event) {
                event.stopPropagation(); // Prevent the click event from bubbling up to the body
                var $dropdown = $('.menu-sub-dropdown');
                if ($dropdown.hasClass('menu-sub-dropdown-hidden')) {
                    $dropdown.removeClass('menu-sub-dropdown-hidden');
                    $dropdown.attr('style',
                        'z-index: 105; position: fixed; inset: 0px auto auto 0px; margin: 0px; transform: translate(1278px, 65px);display: inline-block;'
                    );
                    $dropdown.css('display', 'inline-block');
                } else {
                    $dropdown.addClass('menu-sub-dropdown-hidden');
                    $dropdown.css('display', 'none');
                }
            });

            // Hide the dropdown when clicking outside of it
            $('body').click(function(event) {
                var $dropdown = $('.menu-sub-dropdown');
                if (!$dropdown.hasClass('menu-sub-dropdown-hidden')) {
                    $dropdown.addClass('menu-sub-dropdown-hidden');
                    $dropdown.css('display', 'none');
                }
            });



            var today = new Date().toISOString().split('T')[0];
            $('#end_date').attr('min', today);


            $.noConflict();

            // alert("k");
            $("#hiring_contact_person_add").on("click", function(e) {
                e.preventDefault(); // Prevent the default form submission
                   // $(document).on('click','#hiring_contact_person_add', function() {
                var client_id = $('#client_id').val();
                var cp_name_0 = $('#cp_name_0').val();
                var cp_email_0 = $('#cp_email_0').val();
                var cp_number_0 = $('#cp_number_0').val();
                var cp_desigination_0 = $('#cp_desigination_0').val();
                var phone_country_code = $('#phone_country_code').val();


                if (client_id > 0 && cp_name_0 !='' && cp_email_0 !='' && cp_desigination_0 !='') {

                    $('.indicator-label').hide();
                    $('#hiring_contact_person_add').attr('disabled',true);
                    $('.indicator-progress').show();
                   
                    var token = "{{ csrf_token() }}";
                    var id = client_id;
                    var url = "{{ route('service_company.jobs.hiring_contact_person', ':id') }}";
                    url = url.replace(':id', id);
                    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    
                    $.ajax({
                        //type  : 'GET',
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            'cp_name_0': cp_name_0,
                            'cp_email_0': cp_email_0,
                            'cp_number_0': cp_number_0,
                            'cp_desigination_0': cp_desigination_0,
                            'phone_country_code': phone_country_code,
                            'id': id
                        },
                        success: function(response) {

                            if(response.status == 'warning'){
                                
                                $('#hiring_contact_person_add').attr('disabled',false);
                                $('.indicator-label').show();
                                $('.indicator-progress').hide();
                                $("input[name=cp_email]").val('');
                                Swal.fire({
                                            text				: response.message,
                                            icon				: "error",
                                            buttonsStyling		: !1,
                                            confirmButtonText	: "Ok, got it!",
                                            customClass			: { confirmButton: "btn btn-light" },
                                }); 

                                return false;

                            }

                            
                          $('#Iteam_Table').prepend(response.view);
                           
                          var hiring_namager = $('#hiring_namager').val();
                           if(hiring_namager==''){
                                $('#hiring_namager').val(response.contact_person_name);
                            }else{
                                $('#hiring_namager').val(hiring_namager+','+response.contact_person_name);
                            }
                             $('#add_team_member_form')[0].reset();
                             $('#hiring_contact_person_add').attr('disabled',false);
                             $('.indicator-label').show();
                             $('.indicator-progress').hide();
                             $(".add_team_member_close").click();
                             phoneDisplay();
                          
                          //$('.btn-active-icon-primary').click();
                        }
                    });


                } else {
                    Swal.fire({
                                text				: "Please Enter the Conract Person Details after Submit",
                                icon				: "error",
                                buttonsStyling		: !1,
                                confirmButtonText	: "Ok, got it!",
                                customClass			: { confirmButton: "btn btn-light" },
                    }); 
                }

            }); 


            $('.hr_manager_add').on('click', function(){
                $("#hiring_namager").click();
            });

            $("#hiring_namager").on("click", function() {
              //  alert("dj");
                var client_id = $('#client_id').val();

                if (client_id > 0) {

                    $('#kt_activitiesClient_toggle').trigger('click');
                    var clientName = $("#client_id option:selected");
                    var text_value = clientName.text();
                    $('#light_client_name').html(text_value);


                    var strVal = '';
                    var bgTextDecore = '';
                    var str = '';
                    str = text_value;
                    str = str.substring(0, 1);
                    str = strVal = str.toLowerCase();
                    var str_bg = str.charCodeAt(0);

                    if (str_bg >= 97 && str_bg <= 101) {
                        bgTextDecore = 'fs-3 bg-light-danger text-danger';
                    } else if (str_bg >= 102 && str_bg <= 106) {
                        bgTextDecore = 'fs-3 bg-light-warning text-warning';
                    } else if (str_bg >= 107 && str_bg <= 111) {
                        bgTextDecore = 'fs-3 bg-light-primary text-primary';
                    } else if (str_bg >= 112 && str_bg <= 116) {
                        bgTextDecore = 'fs-3 bg-light-success text-success';
                    } else if (str_bg >= 117 && str_bg <= 121) {
                        bgTextDecore = 'fs-3 bg-dark text-light';
                    } else {
                        bgTextDecore = 'fs-3 bg-light text-dark';
                    }
                    $('#light_client_caption').html(strVal.toUpperCase());
                    $('#light_client_caption').addClass(bgTextDecore);



                    var hiring_namager = $('#hiring_namager').val();
                    var token = "{{ csrf_token() }}";
                    var id = client_id;
                    var url = "{{ route('service_company.jobs.hiring_manager', ':id') }}";
                    url = url.replace(':id', id);
                    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    $.ajax({
                        //type  : 'GET',
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            'hiring_namager': hiring_namager
                        },
                        beforeSend: function() {
                            $('#kt_activitiesClient_body').html('<img src="' + baseurl +
                                '" height="35px" width="100%">');
                        },
                        success: function(response) {
                            $('#kt_activitiesClient_body').html(response.view);
                            if(response.logo){
                                $('#light_client_caption').removeClass('d-none');
                             $('#light_client_caption').html('<img src="' + response.logo +
                                '" height="35px" width="100%">');
                            }else{
                                $('#light_client_caption').addClass('d-none');
                            }
                        }
                    });


                } else {
                    alert(
                        "Please select any client after that choose our Hiring manager or Contact person"
                        );
                }

            });

            $(".add_address").on("click", function() {

                var clientId = $("#client_id").val();
                if (clientId != '') {
                    $('#kt_add_address_toggle').trigger('click');
                    var clientName = $("#client_id option:selected");

                    var text_value = clientName.text();
                    $('#light_address').html(text_value);
                    $('#clientId').val(clientId);
                    var strVal = '';
                    var bgTextDecore = '';
                    var str = '';
                    str = text_value;
                    str = str.substring(0, 1);
                    str = strVal = str.toLowerCase();
                    var str_bg = str.charCodeAt(0);

                    if (str_bg >= 97 && str_bg <= 101) {
                        bgTextDecore = 'fs-3 bg-light-danger text-danger';
                    } else if (str_bg >= 102 && str_bg <= 106) {
                        bgTextDecore = 'fs-3 bg-light-warning text-warning';
                    } else if (str_bg >= 107 && str_bg <= 111) {
                        bgTextDecore = 'fs-3 bg-light-primary text-primary';
                    } else if (str_bg >= 112 && str_bg <= 116) {
                        bgTextDecore = 'fs-3 bg-light-success text-success';
                    } else if (str_bg >= 117 && str_bg <= 121) {
                        bgTextDecore = 'fs-3 bg-dark text-light';
                    } else {
                        bgTextDecore = 'fs-3 bg-light text-dark';
                    }
                    $('#light_address_caption').html(strVal.toUpperCase());
                    $('#light_address_caption').addClass(bgTextDecore);


                } else {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Please Select client',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                }
            });

            $(document).on('click', '.frmupdateAddress', function() {

                var clientId = $("#client_id").val();
                var data = new FormData();
                var form_data = $('#frmupdateAddress').serializeArray();
                $.each(form_data, function(key, input) {
                    data.append(input.name, input.value);
                });

                $.ajax({

                    url: '{{ route('service_company.account.addressupdate') }}',
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
                            $('#kt_add_address_close').click();
                            $("#client_id").val(clientId).change();
                        }

                    }

                });



            });


            $('.remove_address').click(function() {
                $('.client-address-container').children().last().remove();
            });


            $('.team_manager_add').on('click', function(){
                $("#recruiter").click();
            });
            $("#recruiter").on("click", function() {

                var client_id = $('#client_id').val();

                if (client_id > 0) {

                    $('#kt_activitiesRecruiter_toggle').trigger('click');

                    var clientName = $("#client_id option:selected");
                    var text_value = clientName.text();
                    $('#light_recruiter_name').html(text_value);


                    var strVal = '';
                    var bgTextDecore = '';
                    var str = '';
                    str = text_value;
                    str = str.substring(0, 1);
                    str = strVal = str.toLowerCase();
                    var str_bg = str.charCodeAt(0);

                    if (str_bg >= 97 && str_bg <= 101) {
                        bgTextDecore = 'fs-3 bg-light-danger text-danger';
                    } else if (str_bg >= 102 && str_bg <= 106) {
                        bgTextDecore = 'fs-3 bg-light-warning text-warning';
                    } else if (str_bg >= 107 && str_bg <= 111) {
                        bgTextDecore = 'fs-3 bg-light-primary text-primary';
                    } else if (str_bg >= 112 && str_bg <= 116) {
                        bgTextDecore = 'fs-3 bg-light-success text-success';
                    } else if (str_bg >= 117 && str_bg <= 121) {
                        bgTextDecore = 'fs-3 bg-dark text-light';
                    } else {
                        bgTextDecore = 'fs-3 bg-light text-dark';
                    }
                    $('#light_recruiter_caption').html(strVal.toUpperCase());
                    $('#light_recruiter_caption').addClass(bgTextDecore);


                    var recruiter = $('#recruiter').val();
                    var recruiter_id	=	$('#recruiter_id').val();
                    var token = "{{ csrf_token() }}";

                    var id = client_id;
                    var url = "{{ route('service_company.jobs.recruiter', ':id') }}";
                    url = url.replace(':id', id);
                    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {
                            '_token': token,
                            'recruiter': recruiter,
                            'recruiter_id': recruiter_id
                        },
                        beforeSend: function() {
                            $('#kt_activitiesRecruiter_body').html('<img src="' + baseurl +
                                '" height="35px" width="100%">');
                        },
                        success: function(response) {
                            $('#kt_activitiesRecruiter_body').html(response.view);
                        }
                    });

                } else {
                    alert("Please select any client after that choose our Recruiters team");
                }

            });



        });


        var input = document.querySelector('input[name=tags]');

        // Initialize Tagify script on the above inputs
        var tagifyskill = new Tagify(input, {
            whitelist: [''],
            maxTags: 10,
            transformTag: function(tagData) {
            // Remove special characters, allow only letters, numbers, and spaces
            tagData.value = tagData.value.replace(/[^a-zA-Z0-9 +#\/.]/g, '');
            return tagData;
            },
            validate: function(tagData) {
                // Prevent empty tags after cleaning
                return tagData.value.trim().length > 0 ? true : false;
            }
        });
        const fetchSuggestionsSkills = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Skill: ", data);

                if (Array.isArray(data)) {
                    tagifyskill.settings.whitelist = data; // Update whitelist
                    // tagifyskill.dropdown.show(query); // Show suggestions
                    tagifyskill.dropdown.show.call(tagifyskill);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching Skill:", err);
            }
        });
    }, 5); 

    // Listen for user input to trigger API call
    tagifyskill.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 1) return;
        fetchSuggestionsSkills(query);
    });


        var inputloc = document.querySelector("#kt_tagify_work_location");


        var cities = @json($cities);

        // Initialize Tagify script on the above inputs
        var tagifylocation = new Tagify(inputloc, {
            whitelist: cities,
            enforceWhitelist: true, // Prevents users from adding custom values
            maxTags: 15,
            dropdown: {
                maxItems: 20, // <- mixumum allowed rendered suggestions
                classname: "", // <-- tagify__inline__suggestions custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
            }
        });

        // Prevent creating tags on blur or enter key
        tagifylocation.on('input', function(e) {
            tagify.dropdown.show(); // show the dropdown immediately
        });

        // Create tags only on select
        tagifylocation.on('dropdown:select', function(e) {
            tagify.addTags([e.detail.data.value]);
        });
        tagifylocation.on('add', function(e) {
            // alert(cities);
            // Remove the tag if it wasn't selected from the dropdown
            // if (!cities.includes(e.detail.data.value)) {
            //     tagifylocation.removeTag(e.detail.data.value);
            // }
        });

        function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }
        // Debounced function to fetch suggestions
        const fetchSuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                        tagifylocation.settings.whitelist = data; // Update whitelist
                        // tagifylocation.dropdown.show(query); // Show suggestions
                        tagifylocation.dropdown.show.call(tagifylocation);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay

         // Listen for user input to trigger API call
    tagifylocation.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions(query);
    });

    setTimeout(() => {
      var city = "{{@$default_loc}}";
      if(city !=''){
        var cityArray = city.split(","); // Split multiple locations
        var cityNames = cityArray.map(entry => entry.split(" | ")[0]); // Extract city names
        
        // Call function with city names
        fetchSuggestions(cityNames.join(", ")); 
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = {!! json_encode(@$default_loc) !!}; // Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
            tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
            tagifylocation.dropdown.hide();
        }
    }, 3000); // 3 seconds delay



        var inputElement = document.querySelector("#kt_tagify_work_location_one");


        var tagifyInstance = new Tagify(inputElement, {
            whitelist: [],
            enforceWhitelist: true, // Prevents users from adding custom values
            maxTags: 1,
            dropdown: {
                maxItems: 20, // maximum allowed rendered suggestions
                classname: "custom-dropdown-class", // custom classname for this dropdown
                enabled: 1, // show suggestions on focus
                closeOnSelect: true // do not hide the suggestions dropdown once an item has been selected
            }
        });

        // Prevent creating tags on blur or enter key
        tagifyInstance.on('input', function(e) {
            tagifyInstance.dropdown.show(); // show the dropdown immediately
        });

        // Create tags only on select
        tagifyInstance.on('dropdown:select', function(e) {
            tagifyInstance.addTags([e.detail.data.value]);
        });

        tagifyInstance.on('add', function(e) {
            // Remove the tag if it wasn't selected from the dropdown
            // if (!cities.includes(e.detail.data.value)) {
            //     tagifyInstance.removeTag(e.detail.data.value);
            // }
        });

        function debounceone(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }
        // Debounced function to fetch suggestions
        const fetchSuggestionsone = debounceone(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                        tagifyInstance.settings.whitelist = data; // Update whitelist
                        // tagifylocation.dropdown.show(query); // Show suggestions
                        tagifyInstance.dropdown.show.call(tagifyInstance);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 200); // 300ms delay

         // Listen for user input to trigger API call
         tagifyInstance.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestionsone(query);
    });








        var inputlocqualification = document.querySelector("#qualification");


        var qualification = @json($qualificationTag);

        // Initialize Tagify script on the above inputs
        var tagifyqualification = new Tagify(inputlocqualification, {
            whitelist: qualification,
            maxTags: 10,
            dropdown: {
                maxItems: 200, // <- mixumum allowed rendered suggestions
                classname: "", // <- custom classname for this dropdown, so it could be targeted
                enabled: 0, // <- show suggestions on focus
                closeOnSelect: true // <- do not hide the suggestions dropdown once an item has been selected
            }
        });

        // Prevent creating tags on blur or enter key
        tagifyqualification.on('input', function(e) {
            tagify.dropdown.show(); // show the dropdown immediately
        });

        // Create tags only on select
        tagifyqualification.on('dropdown:select', function(e) {
            tagify.addTags([e.detail.data.value]);
        });
        tagifyqualification.on('add', function(e) {
            // alert(qualification);
            // Remove the tag if it wasn't selected from the dropdown
            if (!qualification.includes(e.detail.data.value)) {
                setTimeout(() => {
                    tagifyqualification.removeTag(e.detail.data.value);
                }, 200);

            }
        });




        // var hiring_namager = document.querySelector('input[name=hiring_namager]'); 
        // new Tagify(hiring_namager)

        var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        var teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
            'nineteen'
        ];
        var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];


        $('.add_custom_title').click(function() {

            var customTitleId = $('#customTitleId').val();
            customTitleId++;

            $('#customTitleId').val(customTitleId);

            $('.custom-detail-container').last().append(
                '<div class="form-group col-md-4 mb-0 mt-7 client-cperson-container-child" id="remove"><div class="input-group"><input type="text" name="custom_title[]" id="custom_title_' +
                customTitleId +
                '" class="form-control form-control-lg form-control-solid rounded me-5" placeholder="Enter job questions (Optional)"></div></div>'
            );
        });

        $('.remove_cperson').click(function() {
            $('.custom-detail-container').children().last().remove();
        });

        $(document).ready(function() {

            $(document).on("change", "#maximum_salary", function() {
                salary_check()
            });
            $(document).on("change", "#minimum_salary", function() {
                salary_check()
            });

            $(document).on("change", "#min_exp", function() {
                experience_check()
            });
            $(document).on("change", "#max_exp", function() {
                experience_check()
            });

            $(document).on('change', '#address_id', function() {

                var locationId = $(this).val();
                // $('#kt_select2_2').html(''); 
                console.log("state" + $(this).val());
                var url = "{{ route('service_company.jobs.getlocationCity', ':id') }}?locationId=" +
                    locationId;
                url = url.replace(':id', locationId);
                $.ajax({
                    url: url,
                    success: function(response) {
                        console.log(response.locArr);
                        //	$('#kt_select2_2').html(response.locArr);     // Location
                    }
                });

            })



            $('#client_id').change(function() {
                // alert("dfj");
                var id = $(this).val();
                var value = $("#client_id option:selected");
                var text_value = value.text();
                var jobLocation = "{{ implode(',', $jobLocation) }}";
                // alert(jobLocation);

                $('#address_id').html('<option   value="" >Select the address</option>'); // Location

                var url =
                    "{{ route('service_company.jobs.getContactPerson', ':id') }}?job_id={{ $job_id }}&&jobLocation={{ implode(',', $jobLocation) }}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    success: function(response) {
                        if (response.address_show) {
                            $('.address_show').show();
                        } else {
                            $('.address_show').hide();
                        }
                        $('#address_id').html(response.locArr); // Location
                    }
                })
            }).change();



            $(document).on('keyup', '#minimum_salary', function() {

                var number = parseInt($('#minimum_salary').val());
                var result = convertToWords(number);
                $('.minimum_salary_word').text(capitalizeFirstLetter(result));

            });
            $(document).on('keyup', '#maximum_salary', function() {
                var number = parseInt($('#maximum_salary').val());
                var result = convertToWords(number);
                $('.maximum_salary_word').text(capitalizeFirstLetter(result));

            });





        });

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function convertToWords(num) {
            if (num < 10) {
                return ones[num];
            } else if (num < 20) {
                return teens[num - 10];
            } else if (num < 100) {
                return tens[Math.floor(num / 10)] + ' ' + ones[num % 10];
            } else if (num < 1000) {
                return ones[Math.floor(num / 100)] + ' hundred ' + convertToWords(num % 100);
            } else if (num < 100000) {
                return convertToWords(Math.floor(num / 1000)) + ' thousand ' + convertToWords(num % 1000);
            } else if (num < 10000000) {
                return convertToWords(Math.floor(num / 100000)) + ' lakh ' + convertToWords(num % 100000);
            } else if (num < 1000000000) {
                return convertToWords(Math.floor(num / 10000000)) + ' crore ' + convertToWords(num % 10000000);
            }
            return "Number out of range";
        }

        function salary_check() {
            var minimum_salary = parseInt($("#minimum_salary").val());
            var maximum_salary = parseInt($("#maximum_salary").val());
            if (minimum_salary != '') {
                if (minimum_salary > maximum_salary) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Enter the maximum salary greater than the minimum salary',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#maximum_salary").val('');
                }
            }
        }


        function experience_check() {
            var min_exp = parseInt($("#min_exp").val());
            var max_exp = parseInt($("#max_exp").val());
            if (min_exp != '') {
                if (min_exp > max_exp) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Select the maximum experience greater than the minimum experience',
                        icon: 'warning',
                        allowOutsideClick: false,
                    });
                    $("#max_exp").val('');
                }
            }
        }

        $(document).on('click', '.btn', function() {

            var state = $('#kt_contact_add').attr("data-wizard-state");
            var addressset = $('#address_id option:selected').text();
            var cityset = $('#kt_select2_2 option:selected').text();
            if (addressset == '') {
                Swal.fire("Required", "Address is required", "warning");
                return false;
                $('.submitfrm').hide();
                $('.updatefrm').hide();
            }
            // if(cityset==''){
            // Swal.fire("Required", "Work City is required", "warning");
            //  return false;
            //  $('.submitfrm').hide();	
            //  $('.updatefrm').hide();	
            // }


            if (state == 'last') {
                $('.span_client_name').html('');
                $('.span_location').html('');
                $('.span_jobtitle').html('');
                $('.span_job_type').html('');
                $('.span_openings').html('');

                var global_company_type = {{ $global_company_type }};
                if (global_company_type == 3) {
                    var client_id = '{{ @$client[0]->name }}';
                    $('.span_client_name').html(client_id);
                } else {
                    var client_id = $('#client_id option:selected').text();
                    $('.span_client_name').html(client_id);
                }

                // var address_id		=	$('#address_id option:selected').text(); 

                var tagsjoblocation = $('#kt_tagify_work_location').val();
                arrjoblocation = $.parseJSON(tagsjoblocation);
                // console.log("djdj",arrjoblocation);
                var cntjoblocation = 0;
                var tags_htmljoblocation = '';
                $.each(arrjoblocation, function(key, value) {
                    if (cntjoblocation == 0) {
                        tags_htmljoblocation = value.value;
                    } else {
                        tags_htmljoblocation = tags_htmljoblocation + ' , ' + value.value;
                    }
                    cntjoblocation++;
                });

                $('.span_location').html(tags_htmljoblocation);

                var employment_type = $('#employment_type option:selected').text();
                $('.span_job_type').html(employment_type);

                var title = $('#title').val();
                $('.span_jobtitle').html(title);

                var total_positions = $('#total_positions').val();
                $('.span_openings').html(total_positions);

                var category_id = $('#category_id option:selected').text();
                $('.span_department').html(category_id);

                var min_exp = $('#min_exp option:selected').text();
                $('.span_min_exp').html(min_exp);

                var min_exp = $('#max_exp option:selected').text();
                $('.span_max_exp').html(min_exp);

                // var qualification		=	$('#qualification option:selected').text();   
                // $('.span_qualification').html(qualification);	

                var tagsqualification = $('#qualification').val();
                arrqualification = $.parseJSON(tagsqualification);
                var cntqualification = 0;
                var tags_htmlqualification = '';
                $.each(arrqualification, function(key, value) {
                    if (cntqualification == 0) {
                        tags_htmlqualification = value.value;
                    } else {
                        tags_htmlqualification = tags_htmlqualification + ' , ' + value.value;
                    }
                    cntqualification++;
                });
                $('.span_qualification').html(tags_htmlqualification);



                var min_salary_currency = $('#min_salary_currency option:selected').text();
                var minimum_salary = $('#minimum_salary').val();
                var min_sal_mode = $('#min_sal_mode option:selected').text();
                var minSal = min_salary_currency + ' ' + minimum_salary + ' ' + min_sal_mode;
                $('.span_min_sal').html(minSal);

                var max_salary_currency = $('#max_salary_currency option:selected').text();
                var maximum_salary = $('#maximum_salary').val();
                var max_sal_mode = $('#max_sal_mode option:selected').text();
                var maxSal = max_salary_currency + ' ' + maximum_salary + ' ' + max_sal_mode;
                $('.span_max_sal').html(maxSal);

                var end_date = $('#end_date').val();
                $('.span_due_date').html(end_date);

                var hiring_namager = $('#hiring_namager').val();
                $('.span_contact_person').html(hiring_namager);

                var recruiter = $('#recruiter').val();
                $('.span_recruiter').html(recruiter);

                var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();
                $('.span_description').html(kt_docs_tinymce_hidden);

                var tags = $('#tags').val();
                arr = $.parseJSON(tags);
                var cnt = 0;
                var tags_html = '';
                $.each(arr, function(key, value) {
                    if (cnt == 0) {
                        tags_html = value.value;
                    } else {
                        tags_html = tags_html + ' , ' + value.value;
                    }
                    cnt++;
                });
                $('.span_skills').html(tags_html);





                var arr = $('input[name="custom_title[]"]').map(function() {
                    return this.value;
                }).get();

                $('.span_custom_question_title').html(arr.join(', '));




                $('.submitfrm').show();
                $('.updatefrm').show();

            } else {
                $('.submitfrm').hide();
                $('.updatefrm').hide();
            }

        });

        $(document).on('click', '.submitfrm', function() {


            var data = new FormData();
            var form_data = $('#kt_contact_add_form').serializeArray();
            $.each(form_data, function(key, input) {
                data.append(input.name, input.value);
            });

            var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();
            data.append('detail_description', kt_docs_tinymce_hidden);

            $(this).prop("disabled", true);
            $(this).html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
            );

            $.ajax({
                url: '{{ route('service_company.jobs.store') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                processData: false,
                contentType: false,
                data: data,
                redirect: true,
                success: function(data) {
                    //alert(data.status); 
                    if (data.status == 'success') {
                        setTimeout(() => {
                            window.location = '{{ route('service_company.jobs.index') }}';
                        }, 500);
                        //{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/service_company\/clients"}	
                    }

                }
                //error: function (e) {
                //error
                //}
            });

        });


        //updatefrm

        $('body').on('change', '#min_salary_currency', function() {
            var min_salary_currency = $(this).val();
            document.getElementById("max_salary_currency").selectedIndex = min_salary_currency;
        });
        $('body').on('change', '#max_salary_currency', function() {
            var max_salary_currency = $(this).val();
            document.getElementById("min_salary_currency").selectedIndex = max_salary_currency;
        });
        $('body').on('change', '#min_sal_mode', function() {
            var min_sal_mode = $(this).val();
            document.getElementById("max_sal_mode").selectedIndex = min_sal_mode;
        });
        $('body').on('change', '#max_sal_mode', function() {
            var max_sal_mode = $(this).val();
            document.getElementById("min_sal_mode").selectedIndex = max_sal_mode;
        });
    </script>
    <?php 
   if($page_event == 'EDIT')
   {
?>
    <script>
        $(document).on('click', '.updatefrm', function() {

            var data = new FormData();
            var form_data = $('#kt_contact_add_form').serializeArray();
            $.each(form_data, function(key, input) {
                data.append(input.name, input.value);
            });

            var kt_docs_tinymce_hidden = tinymce.get("kt_docs_tinymce_hidden").getContent();
            data.append('detail_description', kt_docs_tinymce_hidden);

            $(this).prop("disabled", true);
            $(this).html(
                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
            );

            $.ajax({
                url: '{{ route('service_company.jobs.update', $job->id) }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                processData: false,
                contentType: false,
                data: data,
                redirect: true,
                success: function(data) {
                    //alert(data.status); 
                    if (data.status == 'success') {
                        setTimeout(() => {
                            window.location = '{{ route('service_company.jobs.index') }}';
                        }, 500);
                        
                        //{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/service_company\/clients"}	
                    }

                }
                //error: function (e) {
                //error
                //}
            });



        });
    </script>


    // <script>
    //     tinymce.init({
    //         selector: 'textarea',
    //         branding: false,
    //         menubar: false,
    //         setup: function(editor) {
    //             editor.on('keyup', function() {
    //                 fv.revalidateField('comment');
    //             });
    //         },
    //     });
    // </script>
    <!--end::TinyMCE Editor-->
    <?php 
   }
   ?>

    <script>
        function changeautofillfield() {
            var data = new FormData();
            var kt_docs_tinymce_hidden = $('#title').val();
            data.append("title", kt_docs_tinymce_hidden);

            $.ajax({
                url: '{{ route('service_company.jobs.jdDetails') }}',
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
                        tinymce.get("kt_docs_tinymce_hidden").setContent(data.jd);
                        $("#min_exp").val(data.minimum_exp).change();
                        $("#max_exp").val(data.maxmum_exp).change();

                        tagifyskill.removeAllTags(); // Clear existing tags first
                        tagifyskill.addTags(data.skills.split(',')); // Add new tags

                    }
                }
            });
        }
    </script>
@endpush
