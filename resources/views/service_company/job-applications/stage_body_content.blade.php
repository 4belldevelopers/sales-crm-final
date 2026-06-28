@php
    use App\Helper\GeneralMethod;
@endphp

@php

    $bgTextDecore = $strVal = $cicon = '';
    $str = trim(@$application->full_name);
    $str = $strVal = strtolower($str[0]);
    $str = ord($str);
    if ($str >= 97 && $str <= 101) {
        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
    } elseif ($str >= 102 && $str <= 106) {
        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
    } elseif ($str >= 107 && $str <= 111) {
        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
    } elseif ($str >= 112 && $str <= 116) {
        $bgTextDecore = 'fs-3 bg-light-success text-success';
    } elseif ($str >= 117 && $str <= 121) {
        $bgTextDecore = 'fs-3 bg-dark text-secondary';
    } else {
        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
    }
    $cicon =
        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
   <a href="javascript:void(0);" class="">
     <div class="symbol-label ' .
        $bgTextDecore .
        ' border-radius-5">' .
        ucwords($strVal) .
        '</div>
   </a>
    </div>';

    $bgTextDecore = $strVal = '';
    $str = trim(@$application->submitted_by_name);
    $str = $strVal = strtolower($str[0]);
    $str = ord($str);

    if ($str >= 97 && $str <= 101) {
        $bgTextDecore = 'fs-3 bg-light-danger text-danger';
    } elseif ($str >= 102 && $str <= 106) {
        $bgTextDecore = 'fs-3 bg-light-warning text-warning';
    } elseif ($str >= 107 && $str <= 111) {
        $bgTextDecore = 'fs-3 bg-light-primary text-primary';
    } elseif ($str >= 112 && $str <= 116) {
        $bgTextDecore = 'fs-3 bg-light-success text-success';
    } elseif ($str >= 117 && $str <= 121) {
        $bgTextDecore = 'fs-3 bg-dark text-secondary';
    } else {
        $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
    }

    $posted_by_image = '';
    $posted_by_image = url('public/avatar.png');
    if (!empty($application->posted_by_image)) {
        $posted_by_image = @$application->posted_by_image;
    }

    if (!empty($posted_by_image)) {
        $icon =
            '<div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' .
            @$application->submitted_by_name .
            '">
                        <img alt="Pic" src="' .
            @$posted_by_image .
            '">
                      </div>
                  </div>';
    } else {
        $icon =
            '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0" style="float:left">
      <a href="javascript:void(0);" class="">
         <div class="symbol-label ' .
            $bgTextDecore .
            ' border-radius-5 fw-bolder" style="font-size:13px!important;" data-toggle="tooltip" title="' .
            @$application->submitted_by_name .
            '">' .
            ucwords($strVal) .
            '</div>
      </a>
    </div>';
    }

    if (!empty($company->logo_logical_path)) {
        $icon =
            '<div class="symbol symbol-circle symbol-25px overflow-hidden border-radius-0">
   					<a href="javascript:void(0);">
   						<div class="symbol-label border-radius-5">
   						<img src="' .
            @$company->logo_logical_path .
            '" alt="Company Logo" class="border-radius-5 w-100">
   						</div>
   					</a>
   				</div>';
    }

@endphp

<form class="form fv-plugins-bootstrap fv-plugins-framework" id="status_frm" name="status_frm" method="post"
    enctype="multipart/form-data">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="application_id" id="application_id" value="{{ @$application->id }}" />
    <input type="hidden" name="job_wise_condidates_id" id="job_wise_condidates_id"
        value="{{ @$application->job_wise_condidates_id }}" />
    <div class="modal-body scroll-y py-0 px-lg-7">
        <div data-kt-element="options">
            <div class="pb-5">
                <!--begin::Table-->
                <table class="table align-middle table-row-dashed fs-6 mt-5" id="">
                    <!--begin::Table head-->
                    <thead>
                        <!--begin::Table row-->
                        <tr class="fw-bolder text-muted" role="row">
                            <th class="">Name</th>
                            {{-- <th class="">Contacts</th> --}}
                            <th class="">Exp.</th>
                            <th class="">Salary</th>
                            <th class="">Notice Period</th>
                            <th class="">Qualification</th>
                            <th class="">Location</th>
                            <th class="">Source</th>
                            <th class="">Jobs</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="text-gray-600 fw-bold">
                        <!--begin::Table row-->
                        <tr class="">
                            <!--begin::Name=-->
                            <td class="d-flex align-items-center border-bottom-0">{!! @$cicon !!}
                                <!--begin:: Avatar -->
                                <!--<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                           <div class="symbol-label border-radius-5"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="amit kumar image" class="w-100"> </div>
                           </a> </div>-->
                                <!--end::Avatar-->
                                <!--begin::User details-->
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"
                                        data-toggle="tooltip" title="{{ @$application->full_name }}">
                                        {{ ucwords(substr(@$application->full_name, 0, 10)) }} </a>
                                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip"
                                        title="{{ @$application->current_designation }}">{{ ucwords(substr(@$application->current_designation, 0, 10)) }}</span>
                                </div>
                                <!--begin::User details-->
                            </td>
                            <!--end::Name=-->
                            <!--begin::Contacts-->
                            {{-- <td class="">
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0);"
                                        class="text-dark fw-bolder text-hover-primary fs-6">{{ @$application->phone }}</a>
                                    <a href="javascript:void(0);"
                                        class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip"
                                        title="{{ @$application->email }}">{{ substr(@$application->email, 0, 10) }}</a>
                                </div>
                            </td> --}}
                            <!--end::Contacts-->
                            <!--begin::Experience-->
                            <td class="">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ @$application->total_exp }}+</span>
                                    <span class="text-muted fw-bold d-block fs-7">{{ @$application->rel_exp }}+</span>
                                </div>
                            </td>
                            <!--end::Experience-->
                            <!--begin::Salary-->
                            <td class="">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{!! @$expected_ctc !!}</span>
                                    @if (@$application->condidate_type == 'other')
                                        <span class="text-muted fw-bold d-block fs-7">{!! @$current_ctc !!}</span>
                                    @endif
                                </div>
                            </td>
                            <!--end::Salary-->
                            <!--begin::Notice Period-->
                            <td class="">
                                @php
                                    $np = $application->np_id;
                                    if (empty($np)) {
                                        $np = 'N/A';
                                    }
                                    if ($application->serving_notice_period_mode == 0) {
                                        $np = $application->np_id . ' Days';
                                        if ($application->np_id == 1) {
                                            $np = $application->np_id . '  Day';
                                        }
                                    } elseif ($application->serving_notice_period_mode == 1) {
                                        $np = $application->np_id;
                                        if ($np != 'Immediate') {
                                            $np = GeneralMethod::ymdTodmyNotTime($application->np_id); //date("d-M-y", strtotime($application->np_id));
                                        }
                                    }
                                @endphp
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ @$np }}</span>
                                    <span
                                        class="text-muted fw-bold d-block fs-7">{{ substr(@$application->current_company, 0, 16) }}</span>
                                </div>
                            </td>
                            <!--end::Notice Period-->
                            <!--begin::Qualifaction-->
                            <td class="">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ ucwords(@$qualification->name) }}</span>
                                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip"
                                        title="">{{ @$application->current_designation }}</span>
                                </div>
                            </td>
                            <!--end::Qualifaction-->
                            <!--begin::Location-->
                            <td class="">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ @$row_city_name }}</span>
                                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip"
                                        title="{{ @$row_state_name }} | {{ strtoupper(@$row_country_name) }}">
                                        {{ substr(@$row_state_name, 0, 6) }} |
                                        {{ strtoupper(substr(@$row_country_name, 0, 2)) }}
                                    </span>
                                </div>
                            </td>
                            <!--end::Location-->
                            <!--begin::Source-->
                            <td class="">
                                <div class="d-flex flex-column">
                                  <span class="text-dark fw-bolder fs-6">{{ @$application->submitted_by_name }}</span>
                                    <span
                                        class="text-muted fw-bold d-block fs-7">{{ ucwords(@$company->company_name) }}</span>
                                </div>
                            </td>
                            <!--end::Source-->
                            <!--begin::Requisitions-->
                            <td class="">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip"
                                        title="{{ @$application->title }}">{{ substr(ucwords(@$application->title), 0, 10) }}</span>
                                    @php
                                        $company_or_client = GeneralMethod::companyDetailsByClient(
                                            @$application->job_id,
                                        );
                                    @endphp
                                    <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip"
                                        title="{{ @$company_or_client }}">{{ substr(ucwords(@$company_or_client), 0, 10) }}</span>
                                </div>
                            </td>
                            <!--end::Requisitions-->
                        </tr>
                        <!--end::Table row-->
                    </tbody>
                    <!--end::Table body-->
                </table>
                <!--end::Table-->
                <!--begin::Stages-->
                <label class="text-dark mb-0 mt-3 fw-bolder fs-5">Stages </label>
                <div class="row py-5 w-100 d-inline-block justify-content-between align-items-center">
                    @if (!empty($master_status))
                        @foreach ($master_status as $mstatus)
                            @php
                                $master_status_css = $chk = $text_name = '';
                                if ($mstatus->id == @$application->master_status_id) {
                                    $master_status_css = @$mstatus->css_style;
                                    $chk = 'checked="checked"';
                                }
                                $text_name = @$mstatus->text_name;
                            @endphp
                            <div class="{{ @$mstatus->divname }} d-inline-block w-12 px-2 master_status_div"
                                data-master-status-id="{{ @$mstatus->id }}">
                                <input type="radio" class="btn-check master_status" name="master_status"
                                    value="{{ @$mstatus->id }}" {{ @$chk }} id="{{ @$text_name }}">
                                <label
                                    class="lbl_common_master btn-{{ @$text_name }}  btn  btn-outline btn-outline-dashed btn-outline-default d-flex align-items-center justify-content-center"
                                    for="{{ @$text_name }}" id="lbl_master_status_{{ @$mstatus->id }}"> <span
                                        class="d-block fw-bold text-start"> <span
                                            class="text-dark-800 fw-bolder d-block fs-5">{{ @$mstatus->name }}</span>
                                    </span> </label>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!--end::Stages-->
                <!--begin::Sub Stages-->
                <?php 
        if(!empty($candidate_status_Arr)){	
					?>
                <div id="main_substage">
                    <label
                        class="text-dark mb-0 mt-3 fw-bolder fs-5 {{ count($candidate_status_Arr) == 0 ? '' : '' }} subStageSection">Sub
                        Stages</label>
                    <div class="row py-5 w-100 d-inline-block justify-content-between align-items-center substage"
                        id="substage">

                        @if (!empty($candidate_status_Arr))

                            @php $cnt	=	1; @endphp
                            @foreach ($candidate_status_Arr as $cantData)
                                @php
                                    $master_status_css = $chk = '';
                                    if ($cantData->id == @$application->child_status_id) {
                                        $chk = 'checked="checked"';
                                    }
                                @endphp

                                <div class="div{{ $cnt }} d-inline-block ws-25 px-2">
                                    <input type="radio" class="btn-check" name="sub_stages"
                                        value="{{ $cantData->id }}" id="screening{{ $cnt }}"
                                        {{ @$chk }}>
                                    <label
                                        class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center"
                                        for="screening{{ $cnt }}"> <span class="d-block fw-bold text-start">
                                            <span
                                                class="text-dark-800 fw-bolder d-block fs-5">{{ $cantData->name }}</span>
                                        </span> </label>
                                </div>

                                @php $cnt++; @endphp
                            @endforeach
                        @endif
                    </div>
                </div>

                <?php 
		}	
        ?>
                <!--end::Sub Stages-->
                <!--begin::Comment-->
                <div class="row" id="comment_div" style="display:none">
                    <div class="py-5">
                        <label class="text-dark fw-bolder fs-5">Comment</label>
                    </div>
                    <textarea id="comment" name="comment" class="form-control form-control-solid" rows="5"
                        placeholder="Write comment here..."></textarea>
                </div>
                <!--end::Comment-->
            </div>
        </div>
    </div>
    <div class="modal-footer flex-center">
        <div class="div1">
            <!--begin::Cancel-->
            <button type="button" id="" class="btn btn-secondary me-3"
                data-dismiss="modal">Cancel</button>
            <!--end::Cancel-->

            <!--begin::Submit-->
            <button type="button" id="custom_stage_submit" class="btn btn-primary">
                <span class="indicator-label">Submit</span>
                <span class="indicator-progress">
                    Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
            <!--end::Submit-->

        </div>
        <!-- style=""position: absolute;right: 2%; -->
        <div class="div2 position-absolute end-0 me-5 d-none">
            <!--begin::Rescheduled-->
            <button type="button" id="" class="btn btn-secondary">Rescheduled</button>
            <!--end::Rescheduled-->
        </div>
    </div>
</form>
<script>
    //  $(document).ready(function() {
    function autoClickCheckedCheckboxes() {
        const checkboxes = document.querySelectorAll('input[name="master_status"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.click();
            }
        });
    }
    // Call the function when the page loads
    autoClickCheckedCheckboxes();
    // });
</script>
