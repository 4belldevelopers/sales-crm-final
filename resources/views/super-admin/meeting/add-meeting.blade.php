@extends('layouts.superadmin_layout')

<link rel="stylesheet" href="{{ asset('demo/plugins/custom/jkanban/jkanban.bundle.css') }}">
@php
    use App\Helper\GeneralMethod;
@endphp

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
                                    <form class="ajax-form" method="POST" id="importForm" name="importForm"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                        <input type="hidden" name="tablename" value="{{ $tablename }}">
                                        <div class="col-lg-12">

                                            <div class="row">
                                                <table class="table-row-dashed fs-6 gy-5 no-footer" id=""
                                                    role="grid" aria-describedby=""
                                                    style="width: 1270px;">
                                                    <thead>
                                                        <tr class="fw-bolder text-muted other_section {{ $section_other }}"
                                                            role="row">
                                                            <th class="sorting_disabled"
                                                                style="width:62% !important">{{ ($tablename=='google_meeting_lists') ? 'Google' : 'Teams' }} Meeting Link (Import csv/xlsx) <a href="{{url('/img/importDemo.xlsx')}}">Download Sample<i class="fa fa-download"></i></a></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                      
                                                                <tr class="other_section other_section_row row-template"
                                                                    role="row">
                                                                    <td class="" style="width:85% !important">
                                                                       <input &nbsp="" type="file"
                                                                            class="form-control form-control-solid meeting_link_import"
                                                                            name="meeting_link_import"
                                                                            id="meeting_link_import_1"
                                                                            data-id="1"
                                                                            data-offerid="1"
                                                                            value=""
                                                                            placeholder="Enter Meeting Link">
                                                                    </td>
                                                                    <td class="" style="width:25% !important">
                                                                         <div class="col-lg-3  d-flex justify-content-center">
                                                                            <input type="submit" name="save"
                                                                                value="{{ ($tablename=='google_meeting_lists') ? 'Google' : 'Teams' }} Links Import" id="save-form-import"
                                                                                class="btn btn-primary"
                                                                                style="">
                                                                        </div>

                                                                    </td>
                                                                    
                                                                </tr>
                                                    </tbody>

                                                </table>
                                            </div>
                                           
                                            <div class="row justify-content-center">
                                               <!-- Progress Bar -->
                                               <div class="col-lg-4">
                                                <div class="progress" style="height: 25px; display:none;">
                                                    <div class="progress-bar" role="progressbar" style="width: 0%">
                                                        0%
                                                    </div>
                                                </div>

                                                <br>
                                                <div style="text-align: center;" id="responseMsg"></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                </div>

                                <h4>Manual Entry</h4>
                                <div class="tab-content pt-5">
                                    <form class="ajax-form" method="POST" id="createForm" name="createForm"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row fv-row mb-10 fv-plugins-icon-container">
                                        <input type="hidden" name="tablename" value="{{ $tablename }}">
                                        <div class="col-lg-12">

                                            <div class="row">
                                                <table class="table-row-dashed fs-6 gy-5 no-footer" id="pointList"
                                                    role="grid" aria-describedby="myTable_info"
                                                    style="width: 1270px;">
                                                    <thead>
                                                        <tr class="fw-bolder text-muted other_section {{ $section_other }}"
                                                            role="row">
                                                            <th class="sorting_disabled" style="width: 25%!important">Account holder email
                                                            </th>
                                                            <th class="sorting_disabled"
                                                                style="width:62% !important">{{ ($tablename=='google_meeting_lists') ? 'Google' : 'Teams' }} Meeting Link</th>
                                                            <th class="sorting_disabled">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                      
                                                                <tr class="other_section other_section_row row-template"
                                                                    role="row">
                                                                    <td class="" style="width: 25%!important">
                                                                        <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid holder_emails"
                                                                            name="holder_email[]"
                                                                            id="holder_email_1"
                                                                            data-id="1"
                                                                            data-offerid="1"
                                                                            value=""
                                                                            placeholder="Enter account holder email">
                                                                    </td>
                                                                    <td class="" style="width:62% !important">
                                                                       <input &nbsp="" type="text"
                                                                            class="form-control form-control-solid meeting_link"
                                                                            name="meeting_link[]"
                                                                            id="meeting_link_1"
                                                                            data-id="1"
                                                                            data-offerid="1"
                                                                            value=""
                                                                            placeholder="Enter Meeting Link">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button"
                                                                            class="btn btn-light-danger font-weight-bolder remove_points me-0"
                                                                            {{ 0 > 1 ? '' : 'disabled' }}>
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
                                                    </tbody>

                                                </table>
                                            </div>
                                           
                                            <div class="row justify-content-center">
                                                <div class="col-lg-3  d-flex justify-content-center">
                                                    <input type="submit" name="save"
                                                        value="Submit" id="save-form"
                                                        class="btn btn-primary"
                                                        style="width: 100%;height: 40px;margin-top: 10px;">
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
            <script>

               
                $('#importForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    $('.progress').show();
                    $('.progress-bar').css('width', '0%').text('0%');
                    $('#responseMsg').html('Uploading...');

                    $.ajax({
                        url: "/import-meetings",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,

                        xhr: function() {
                            let xhr = new window.XMLHttpRequest();

                            xhr.upload.addEventListener("progress", function(e) {
                                if (e.lengthComputable) {
                                    let percent = Math.round((e.loaded / e.total) * 100);

                                    $('.progress-bar')
                                        .css('width', percent + '%')
                                        .text(percent + '%');
                                }
                            });

                            return xhr;
                        },

                        success: function(res) {
                            $('.progress-bar')
                                .css('width', '100%')
                                .text('Upload Complete');

                            $('#responseMsg').html(
                                '<span style="color:green;">' + res.message + '</span>'
                            );
                             window.location.href =  res.url;
                        },

                        error: function() {
                            $('#responseMsg').html(
                                '<span style="color:red;">Upload failed</span>'
                            );
                        }
                    });
                });

                document.addEventListener("DOMContentLoaded", function() {
                    // Initially show the first step
                    document.getElementById("step1").classList.add("active");
                });


                $(document).ready(function() {

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
                        });
                    });

                    $(document).off('click', '.remove_points').on('click', '.remove_points', function() {
                        const removeRow = $(this).closest('.remove_row');
                        removeRow.remove();

                    });

                    $('#createForm').on('submit', function(e) {
                        e.preventDefault();
                        //  var formData = new FormData($('#createForm')[0]);	
                        let formData = new FormData(this);
                        $.ajax({
                            url: '{{ route('superadmin.company.addMeetingLists') }}' +
                                '?_token=' + '{{ csrf_token() }}',
                            type: 'POST',
                            data: formData,
                            processData: false, // tell jQuery not to process the data
                            contentType: false, // tell jQuery not to set contentType
                            redirect: true,
                            success: function(response) {

                                if (response.status) {
                                    Swal.fire("Success", response.message , "success");	
                                         window.location.href =  response.url;
                                } else {

                                    Swal.fire({
                                        icon: response.type,
                                        title: 'Error',
                                        text: response.message
                                    });

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


                });
            </script>
        @endpush
