@php
    use App\Helper\GeneralMethod;
@endphp
<style>
    .text-justify {
        text-align: justify !important;
    }
    .iti__flag {
    margin-top: 4px !important;
    }
    #kt_activitiesBenchApplication_close svg {
        color: red;
    }
</style>
<script>
    $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php

    $resume_url = '<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';
    $photo = url('public/avatar.png');
    if (!empty($application->photo)) {
        $photo = url('/public/user-uploads/bench_photos/' . $application->photo);
    }
    $country_id = $country_name = $state_name = $state = $city_name = $posted_by_company = '';
    $country_id = @$benchApplication->country;
    $state = @$benchApplication->state;
    // if (@$country_id > 0) {
        $country_name_Arr = GeneralMethod::getCountryName($country_id);
        foreach ($country_name_Arr as $cval) {
            $country_name = ucwords($cval['name']);
        }

        if ($country_id == 101) {
            $state_name = @$benchApplication->state_name;
            $city_name = @$benchApplication->city_name;
        } else {
            if ($state > 0) {
                $state_name_Arr = GeneralMethod::getStateName($country_id, $state);
                foreach ($state_name_Arr as $sval) {
                    $state_name = ucwords($sval['name']);
                }
            }
            $city_name = @$benchApplication->city;
        }
        $currencySymbole = GeneralMethod::currencySymbole();
        $current_ctc_cur = $current_ectc_cur = '';
        $current_ctc_cur = $currencySymbole[(@$benchApplication->pay_rate_currency) ? @$benchApplication->pay_rate_currency : 0];
        $current_ectc_cur = $currencySymbole[(@$benchApplication->bill_rate_currency) ? @$benchApplication->bill_rate_currency : 0];
    // }
    $posted_by_company = @$benchApplication->company_name;
@endphp

{{-- <div class="card-header px-5 py-0" id="kt_activities_header">
    <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark d-none">Candidate Profile</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
    </div>
</div> --}}

<div class="card-body position-relative px-0 py-0" id="kt_activitiesBenchApplication_body">
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <div class="card mb-5 mb-xl-5 mx-2">
            <div class="card-body pt-5 pb-0 px-5">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img
                                src="{{ @$photo }}" alt="image">
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px">
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2"> <span
                                        class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$benchApplication->full_name) }}</span>

                                        <div class="social-links" style="display:flex; gap:15px;">
                                            <!-- LinkedIn -->
                                                @if(@$benchApplication->linkedin)
                                                <a href="{{@$benchApplication->linkedin}}" target="_blank" title="LinkedIn">
                                                    <i class="fab fa-linkedin" style="font-size:23px; color:#0A66C2;"></i>
                                                </a>
                                                @endif
                                                <!-- GitHub -->
                                                @if(@$benchApplication->github)
                                                <a href="{{@$benchApplication->github}}" target="_blank" title="GitHub">
                                                    <i class="fab fa-github" style="font-size:23px; color:#171515;"></i>
                                                </a>
                                                @endif
                                                <!-- Behance -->
                                                @if(@$benchApplication->behance)
                                                <a href="{{@$benchApplication->behance}}" target="_blank" title="Behance">
                                                    <i class="fab fa-behance" style="font-size:23px; color:#1769FF;"></i>
                                                </a>
                                                @endif
                                                <!-- Other Link -->
                                                @if(@$benchApplication->otherLink)
                                                <a href="{{@$benchApplication->otherLink}}" target="_blank" title="Other">
                                                    <i class="fa fa-link" style="font-size:23px; color:#6c757d;"></i>
                                                </a>
                                                @endif
                                        </div>

                                </div>
                                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z"
                                                        fill="#000000" />
                                                    <path
                                                        d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z"
                                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                </g>
                                            </svg>
                                        </span> {{ ucwords(@$benchApplication->total_exp) }} Years </a>
                                    <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <rect fill="#000000" opacity="0.3" x="2" y="2" width="10"
                                                        height="12" rx="2" />
                                                    <path
                                                        d="M4,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,8 C2,6.8954305 2.8954305,6 4,6 Z M18,16 C19.1045695,16 20,15.1045695 20,14 C20,12.8954305 19.1045695,12 18,12 C16.8954305,12 16,12.8954305 16,14 C16,15.1045695 16.8954305,16 18,16 Z"
                                                        fill="#000000" />
                                                </g>
                                            </svg>
                                        </span>
                                        {!! @$current_ctc_cur !!} &nbsp; {{ @$benchApplication->pay_rate_amount }} -
                                        {{ @$benchApplication->bill_rate_amount }}
                                        @php
                                            $pay_rate_mode = '';
                                            $pay_rate_mode = (@$benchApplication->pay_rate_mode) ? @$benchApplication->pay_rate_mode : 0;
                                            echo $salaryMode[$pay_rate_mode];
                                        @endphp
                                    </a>
                                    <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary mb-2"> <span
                                            class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z"
                                                    fill="black"></path>
                                                <path
                                                    d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z"
                                                    fill="black"></path>
                                            </svg>
                                        </span> {{ ucwords(@$city_name) }}, {{ ucwords(@$state_name) }},
                                        {{ ucwords(@$country_name) }}
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex mt-0">
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5"
                                        id="kt_activitiesBenchApplication_close"> <span class="svg-icon svg-icon-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                height="24px" viewBox="0 0 24 24" version="1.1">
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

                            
                        </div>
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="row w-75"> <span class="">
                                        @php
                                            $skills = '';
                                            $skills = @$benchApplication->skills;
                                        @endphp
                                        @if (!empty($skills))
                                            @php
                                                $skillsArr = '';
                                                $skillsArr = explode(',', $skills);
                                            @endphp
                                            @foreach ($skillsArr as $sval)
                                                {!! '<span class="badge badge-light me-3">' . @$sval . '</span>' !!}
                                            @endforeach
                                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="card card-custom mx-2">
            <div class="card-header card-header-tabs-line">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x"
                        role="tablist">
                        <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab"
                                href="#personal_details"><span class="nav-text font-weight-bold">Personal Detail &
                                    Notes</span></a> </li>
                    </ul>
                </div>
            </div>
            <div class="card-body py-0">

                <div class="tab-content pt-5">
                    <div class="tab-pane active" id="personal_details" role="tabpanel">
                        <div class="card-header cursor-pointer px-0">
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Personal Details</h3>
                            </div>
                        </div>
                        <div class="card-body px-0 py-7">

                            <div class="row mb-5">
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Name</span>
                                        <span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$benchApplication->full_name) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Desigination</span>
                                        <span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$benchApplication->current_designation) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Company</span>
                                        <span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$benchApplication->company_name) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Notice Period</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            @php

                                                $np = $benchApplication->np_id;
                                                if (empty($np)) {
                                                    $np = 'N/A';
                                                }
                                                if ($benchApplication->serving_notice_period_mode == 0) {
                                                    $np = $benchApplication->np_id . ' Days';
                                                    if ($benchApplication->np_id == 1) {
                                                        $np = $benchApplication->np_id . '  Day';
                                                    }
                                                } elseif ($benchApplication->serving_notice_period_mode == 1) {
                                                    $np = $benchApplication->np_id;
                                                    if ($np != 'Immediate') {
                                                        $np =
                                                            ' Available from ' .
                                                            GeneralMethod::ymdTodmyNotTime(
                                                                $benchApplication->np_id);
                                                    }
                                                }
                                            @endphp

                                        {{ $np }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">

                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Total Experience</span>
                                        @php
                                            $experience = trim((string) @$benchApplication->total_exp);
                                                // Keep exact value like 3.0, 5.6, 9.10 — remove only ".00"
                                                if (strpos($experience, '.') !== false) {
                                                    $parts = explode('.', $experience);
                                                    if (isset($parts[1])) {
                                                        // If decimals are exactly "00", remove them
                                                        if ($parts[1] === '00') {
                                                            $formattedExperience = $parts[0];
                                                        } else {
                                                            $formattedExperience = $experience; // keep as-is like 3.0, 5.6, 9.10
                                                        }
                                                    } else {
                                                        $formattedExperience = $experience;
                                                    }
                                                } else {
                                                    $formattedExperience = $experience;
                                                }
              
                                        @endphp
                                        <span class="fw-bolder text-gray-800 fs-6">{{ @$formattedExperience }} Years
                                            </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Rel. Experience</span>
                                        @php
									
										$rel_experience = trim((string) @$benchApplication->rel_exp);
										// Keep exact value like 3.0, 5.6, 9.10 — remove only ".00"
										if (strpos($rel_experience, '.') !== false) {
											$parts = explode('.', $rel_experience);
											if (isset($parts[1])) {
												// If decimals are exactly "00", remove them
												if ($parts[1] === '00') {
													$rel_formattedExperience = $parts[0];
												} else {
													$rel_formattedExperience = $rel_experience; // keep as-is like 3.0, 5.6, 9.10
												}
											} else {
												$rel_formattedExperience = $rel_experience;
											}
										} else {
											$rel_formattedExperience = $rel_experience;
										}
                                        
                                        
                                        @endphp
                                        <span class="fw-bolder text-gray-800 fs-6">{{ @$rel_formattedExperience }} Years </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Qualification</span>
                                        <span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$benchApplication->qualification_name) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Date of Birth</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            @php
                                                $newDate = '';
                                                $originalDate = @$benchApplication->dob;
                                                if(!empty($originalDate) && $originalDate !=''){
                                                    $newDate = date('d-M-Y', strtotime($originalDate));
                                                }
                                               
                                            @endphp
                                            {{ @$newDate }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if(@$benchApplication->status_type =='DIRECT')
                               <div class="row mb-5">
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Current Salary</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            {!! $current_ctc_cur !!}{{ @$benchApplication->ctc }}
                                            @php
                                                $current_ctc_mode = '';
                                                $current_ctc_mode = (@$benchApplication->current_ctc_mode) ? @$benchApplication->current_ctc_mode : 0;
                                                echo $salaryMode[$current_ctc_mode];
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Expected Salary</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            {!! $current_ectc_cur !!}
                                            {{ @$benchApplication->ectc }}
                                            @php
                                                $current_ctc_mode = '';
                                                $current_ctc_mode = (@$benchApplication->current_ctc_mode) ? @$benchApplication->current_ctc_mode : 0;
                                                echo $salaryMode[$current_ctc_mode];
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                            @else
                            <div class="row mb-5">
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Pay Rate</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            XXX-XXX-XXXX
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Bill Rate</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            {!! $current_ectc_cur !!}
                                            {{ @$benchApplication->bill_rate_amount }}
                                            @php
                                                $bill_rate_mode = '';
                                                $bill_rate_mode = (@$benchApplication->bill_rate_mode) ? @$benchApplication->bill_rate_mode : 0;
                                                echo $salaryMode[$bill_rate_mode];
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Comission type</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            @php
                                                $comission_type = $comission_type_name = '';
                                                $comission_type = @$benchApplication->comission_type;
                                                if ($comission_type == 1) {
                                                    $comission_type_name = 'Free';
                                                } elseif ($comission_type == 2) {
                                                    $comission_type_name = 'Paid';
                                                }
                                                echo @$comission_type_name;
                                            @endphp
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Employee ID</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            {{ @$benchApplication->employee_number }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Primary Number</span>
                                        <div style="display: flex;">
                                                <div id="firstPhoneDisplaybench" class="fw-bolder text-gray-800 fs-6" style="display: flex;"></div>
                                            <span class="fw-bolder text-gray-800 fs-6" style="margin-left: 4px;">xxx-xxx-xxxx</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Primary Email</span>
                                        <span class="fw-bolder text-gray-800 fs-6">xxx-xxx-xxxx</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Location</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            {{ @$city_name }}
                                            &nbsp; {{ ucwords(@$state_name) }}&nbsp;
                                            {{ ucwords(@$country_name) }} 
                                        </span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Added Date</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            @php
                                                $originalDate = @$benchApplication->created_at;
                                                $newDate = date('d-M-Y', strtotime($originalDate));
                                            @endphp
                                           {{ @$newDate }}
                                        </span>
                                    </div>
                                </div> 

                            </div>

                            <div class="row mb-5">
                                                   
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Inhouse Talent Status</span>
                                        <span class="fw-bolder text-gray-800 fs-6"> {{ ucwords(@$benchApplication->master_status_name) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Added By</span>
                                        <span class="fw-bolder text-gray-800 fs-6">{{ @$posted_by }}</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Last Active Date</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            @php
                                                    $updated_at = @$benchApplication->updated_at;
                                                    $updated_at = date('d-M-Y', strtotime($updated_at));
                                            @endphp
                                            {{ ucwords(@$updated_at) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Resumew</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                           
                                            @if (!empty(@$benchApplication->resume))
                                                @php
                                                    $resumePath =
                                                        public_path('user-uploads/bench_resume/') .
                                                        @$benchApplication->resume;
                                                    $resume_status = 0;
                                                    if (file_exists($resumePath)) {
                                                        $resume_status = 1;
                                                    }
                                                     $url = @$benchApplication->resume;

                                                     $info = pathinfo($url);
                                                @endphp
                                                <a type="button"
                                                    class="btn btn-light-primary btn-sm py-1"
                                                    href="{{ route('download.resume', ['id' => $benchApplication->id,'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]) }}"
                                                    target="_blank" download>
                                                    Download&nbsp;&nbsp;<i class="fa fa-download"></i>
                                                </a>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- <div class="col-md-3">
                                    <div class="d-flex flex-column text-dark-75">
                                        <span class="fw-bold fs-6 text-muted">Hiring HR</span>
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            @if (!empty($bench_assign))
                                                @foreach ($bench_assign as $cpn)
                                                    {!! @$cpn->name . ' &nbsp;&nbsp; ' !!}
                                                @endforeach
                                            @endif
                                        </span>
                                    </div>
                                </div> --}}
                            </div>

                           @if(@$benchApplication->status_type !='DIRECT')
                            <div class="card-title m-0 border-bottom py-7">
                                <h3 class="fw-bolder m-0">Job Skills</h3>
                            </div>
                            <div class="row pt-7 border-bottom pb-7">
                                @php 
                    
                                    if(!empty($skillExperiencesCount)){
                                    $customTitleId	=	1;
                                    $html			=	'';
                                    $inc_first		=	0;
                                    $inc_last		=	1;

                                    foreach($skillExperiences as $exp)
                                    {
                                @endphp
                                        <div class="col-md-3 mb-6">
                                            <div class="fw-semibold text-muted text-uppercase">
                                                {{ strtoupper($exp->skill_key) }} EXP.
                                            </div>

                                            <div class="fw-bold text-gray-800">
                                                {{ ($exp->year ?? '0') }}.{{ ($exp->month ?? '0') }} Year
                                            </div>
                                        </div>
                                        
                                @php 
                                    } 

                                    }		
                                @endphp
                            </div>
                            @endif

                            @if(@$bench_assign_count > 0)
                 <div class="card-title m-0 border-bottom py-7">
                  
                      <div class="card-title m-0 border-bottom py-7">
                        <h3 class="fw-bolder m-0">Relationship Manager</h3>
                      </div>
                      <div class="row" style="margin-top: 7px;">
                        <div class="col-lg-1"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> # </span> </div>
                        <div class="col-lg-3"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> Name </span> </div>
                        <div class="col-lg-3"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> Designation</span> </div>
                        <div class="col-lg-5"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> Contact Us </span> </div>
                      </div>

                      @php $sr = 1; @endphp
                        @foreach( $bench_assign_row as $assignVal)
                        <div class="row" style="margin-top: 7px;">
                            <div class="col-lg-1"> {{ $sr }}</div>
                              <div class="col-lg-3">
                                  <div class="col-lg-12">  {{ ucwords(@$assignVal->name) }}  </div>
                              </div>
                              <div class="col-lg-3">
                                <div class="col-lg-12">  {{ ucwords(@$assignVal->designation) }}  </div>
                            </div>
                              <div class="col-lg-5">
                                  <div class="col-lg-12">  {{ @$assignVal->email }} <br> 
                                    <div style="position: relative;display: flex;">
                                      <div class="iti__flag-container" data-code="{{ @$assignVal->calling_code ?? 'in' }}" data-row="{{ $assignVal->id }}" id="phoneDisplayReal_{{ $assignVal->id }}" style="display: flex;position: relative;"> <!-- Flag and dial code will be appended here --></div>
                                      <div style="margin-left: 6px;margin-top: 2px;">
                                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">{{ ucwords(@$assignVal->mobile) }}</a>
                                      </div>
                                    </div>
                                  </div>
                              </div>
                            </div>	
                            @php $sr++;  @endphp
                        @endforeach	
                      </div>		
                 	
              @endif



                        </div>
                    </div>

            
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var input = document.querySelector('input[name=tags]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input)

   var input_show = document.querySelector('input[name=tags_show]'); 
   // initialize Tagify on the above input node reference
   new Tagify(input_show)

    $( document ).ready(function() {
 
      
       const firstSavedCountryCode = "{{ @$benchApplication->phone_code }}"; // Example country code (lowercase)
      
       const firstPhoneDisplaybenchDiv = document.getElementById("firstPhoneDisplaybench");
 
         const flagDiv = document.createElement("div");
         flagDiv.className = `iti__flag iti__${firstSavedCountryCode}`;
 
         // Get the dial code for the selected country code
         const dialCode = countryDialCodes[firstSavedCountryCode] || ""; // Default to empty string if not found
 
         // Create a span to display the dial code
         const dialCodeSpan = document.createElement("span");
         dialCodeSpan.textContent = dialCode;
 
         // Append the flag and the dial code to the display div
         firstPhoneDisplaybenchDiv.appendChild(flagDiv);
         firstPhoneDisplaybenchDiv.appendChild(dialCodeSpan);


        setTimeout(() => {
            // alert("k");
            $('.iti__flag-container').each(function(index) {
                var cnt = $(this).attr('data-row');
                const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
                var calling_code = $(this).attr('data-code');
                
                const flagDiv = document.createElement("div");
                flagDiv.className = `iti__flag iti__${calling_code}`; // Ensure calling_code is in lowercase

                const dialCode = countryDialCodes[calling_code] || ""; // Fetch the dial code dynamically
                const dialCodeSpan = document.createElement("span");
                dialCodeSpan.textContent = dialCode;

                // Append the flag and dial code
                flagContainer.appendChild(flagDiv);
                flagContainer.appendChild(dialCodeSpan);


            });
        }, 2000);


        });
</script> 
