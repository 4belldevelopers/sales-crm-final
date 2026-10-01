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
     
    $pagetitle = $end_date = $jobtags = $recruiterData = $cpersonData = $carriew_website_chk = '';
    $job_id = 0;
    if ($page_event == 'ADD') {
        $pagetitle = 'Add Campaign';
        $rec_inc = 0;
        foreach ($recruiterlist as $recData) {
            if ($rec_inc == 0) {
                $recruiterData = $recData->name;
            } else {
                $recruiterData = $recruiterData . ' , ' . $recData->name;
            }
            $rec_inc++;
        }
    } elseif ($page_event == 'EDIT') {
        $pagetitle = 'Edit Campaign';
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
            } else {
                $recruiterData = $recruiterData . ' , ' . $recData->name;
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
                <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Campaign details and submit</span>
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
                           
                            <!--begin::Wizard Body-->
                            <div class="row justify-content-center py-10 px-8 py-lg-10 px-lg-10">
                                <div class="col-xl-12 col-xxl-12">
                                    <div class="loadingMessage" style="display: none; position: absolute;">Processing...</div>
                                    <!--begin::Form Wizard Form-->
                                    <form class="form mx-auto" novalidate="novalidate" action="javascript:void(0);"
                                        method="post" id="{{$page_event == 'ADD' ? 'boost_keyword_store' : 'boost_keyword_update'}}" name="{{$page_event == 'ADD' ? 'boost_keyword_store' : 'boost_keyword_update'}}">
                                        <meta name="csrf-token" content="{{ csrf_token() }}" />
                                        <!--begin::Basic Details-->
                                        <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                                            <h3 class="mb-10 font-weight-bold text-dark">Campaign Details</h3>
                                            <div class="flex-column">
                                                <!--begin::Client | Locations | Work Location for candidate-->
                                                <div class="form-group row fv-row mb-10">
                                                   
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Campaign Name</label>
                                                        <input type="text"
                                                            class="form-control form-control-lg form-control-solid"
                                                            value="Campaign 1" name="campaign_name">
                                                        <input type="hidden"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="keyword_compant_id" id="keyword_compant_id" value="{{$company_id}}">
                                                    </div>
                                                   
                                                    <div class="col-lg-6">
                                                        <label class="form-label required">Keyword</label>
                                                        <div class="input-group input-group-lg input-group-solid">
                                                            
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
                                                </div>
                                                <!--end::Client | Locations | Work Location for candidate-->


                                                <!--begin::Job Title | No.of Openings | Job Department-->
                                                <div class="form-group row fv-row mb-10">
                                                    <div class="col-lg-6">
                                                       
                                                            <label class="form-label">Enter the Campaign Location of the
                                                                Candidate (Select Maximum 1 cities):</label>
                                                            <input class="form-control form-control-solid"
                                                                value="{{ $default_loc }}" id="kt_tagify_work_location"
                                                                name="kt_tagify_work_location"
                                                                placeholder="Enter your Location..." />
                                                            <span class="form-text text-muted">*Enter the location of the
                                                                job.</span>
                                                    </div>

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
                                                </div>
                                            
                                                
                                            </div>
                                        </div>

                                        <!--end::Basic Details-->
                                      
                                       <!--begin::Buttons-->
                <div class="form-row row fv-row">
                    <div class="col-12 text-center py-10">
                            @php 
                                if($page_event == 'EDIT'){ 
                                    echo '<button type="submit" name="update" id="update-form" value="update" class="btn btn-primary">Update</button>';
                                }else{
                                    echo '<button type="submit" name="save" id="save-form" value="save" class="btn btn-primary">Submit</button>';	
                                }
                            @endphp
                    </div>
                  </div>
                  <!--end::Buttons-->
                                       
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 

@endsection



@push('footer-script')
    <script>




        tinymce.init({
            selector: '#kt_docs_tinymce_basic'
        });
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


        var inputloc = document.querySelector("#kt_tagify_work_location");


        var cities = @json($cities);

        // Initialize Tagify script on the above inputs
        var tagifylocation = new Tagify(inputloc, {
            whitelist: cities,
            maxTags: 1,
            dropdown: {
                maxItems: 450, // <- mixumum allowed rendered suggestions
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
            if (!cities.includes(e.detail.data.value)) {
                tagifylocation.removeTag(e.detail.data.value);
            }
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
                        window.location = '{{ route('service_company.jobs.index') }}'
                        //{"status":"success","message":"menu.clients created successfully.","action":"redirect","url":"https:\/\/rm.vibhanshujaiswal.com\/service_company\/clients"}	
                    }

                }
                //error: function (e) {
                //error
                //}
            });

        });
    </script>

    <?php 
     }else{
   ?>
    <script>
      $(document).on('submit','#boost_keyword_store', function(e) {
            e.preventDefault();
            var form = $(this);
            $('.page-loader').css('display', 'flex');
             var formData = $('#boost_keyword_store').serialize();
             var formData = form.serialize();
             var urlRedirect = "{{ route('service_company.account.index') }}?ads=show";
            $.ajax({
                type: "POST",
                url: '{{route('service_company.account.boostkeywordadd')}}'+'?_token=' + '{{ csrf_token() }}',
                data: formData,
                success: function(response) {
                    if(response.status == 'success'){                        
                        Swal.fire("Success", response.message, "success").then(() => {
                            window.location = urlRedirect;
                        });
                        $('#boost_keyword_store')[0].reset();
                    }else if(response.status == 'faild'){
                       
                        Swal.fire("Oops...", response.message, "warning").then(() => {
                            window.location = urlRedirect;
                        });
                    }else{
                        Swal.fire("Error", "Something went wrong!", "error"); 
                    }
                    $('.page-loader').css('display', 'none');
                    	
                },
                error: function(xhr, status, error) {
                    Swal.fire("Error", "Something went wrong!", "error");
                    $('.page-loader').css('display', 'none');
                }
            });
        });

    </script>

<?php 
   }
?>
@endpush
