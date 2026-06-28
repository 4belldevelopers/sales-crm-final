@extends('layouts.service_company_layout')
@push('head-script')
<style>
body{overflow-x:hidden;}
</style>
@endpush
@section('content')
@php 
		use App\Helper\GeneralMethod;
@endphp 
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-5 pb-0" id="kt_content">
  
    <div class="row">
        <div class="post d-flex flex-column-fluid mt-5" id="kt_post">
            <div id="kt_content_container" class="container-fluid">


                <div class="row gy-5 g-xl-8">
                    <div class="col-xl-12">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder fs-3 mb-1">Meetings</span>
                                    <span class="text-muted mt-1 fw-bold fs-7">{{ @$totalMeetingSchedules }}
                                        meetings</span>
                                </h3>
                            </div>
                            <div class="card-body py-3">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                        <thead>
                                            <tr class="fw-bolder text-muted">
                                                <th class="w-25px">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            data-kt-check="true" data-kt-check-target=".widget-9-check">
                                                    </div>
                                                </th>
                                                <th class="min-w-200px">Meet Mode</th>
                                                <th class="min-w-200px">Company</th>
                                                <th class="min-w-150px">Company Panel</th>
                                                <th class="min-w-150px">Our Panel</th>
                                                <th class="min-w-150px">Date</th>
                                                <th class="min-w-100px text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (@$MeetingSchedule)
                                                @foreach (@$MeetingSchedule as $val_meeting)
                                                    @php
                                                    // dd(@$val_meeting);
                                                        $interview_schedule_date = $newDate = $newTime = $interview_mode = $interview_mode_child_level = $interviewers = $panellistArr = $panellistArrour_team = $feedback = $available_status_name =
                                                            '';

                                                        $interviewers = $val_meeting->company_team;
                                                        if (!empty($interviewers)) {
                                                            $panellistArr = explode(',', $interviewers);
                                                        }

                                                        $interviewersour_team = $val_meeting->our_team;
                                                        if (!empty($interviewersour_team)) {
                                                            $panellistArrour_team = explode(',', $interviewersour_team);
                                                        }

                                                        $interview_schedule_date = $val_meeting->schedule_date;
                                                        $newDate = date('d-M-Y', strtotime($interview_schedule_date));
                                                        $newTime = date('H:i A', strtotime($interview_schedule_date));

                                                        if ($val_meeting->interview_mode > 0) {
                                                            $interview_mode_Arr = GeneralMethod::interview_mode();
                                                            $interview_mode =
                                                                $interview_mode_Arr[@$val_meeting->interview_mode];
                                                        }
                                                        // $interview_mode	=	$val_meeting->mode_name;

                                                        $interview_mode_child_level = $val_meeting->sub_mode_name;
                                                        if ($val_meeting->interview_mode == 3) {
                                                            $interview_mode_child_level = substr(
                                                                @$val_meeting->url_address,
                                                                0,
                                                                15,
                                                            );
                                                        }
                                                        $average_star_rating = '';
                                                        $imagePath = $text = '';


                                                        $fromCompany_image = $comment = '';
                                                        if (!empty(@$val_meeting->fromCompany->logo)) {
                                                            $fromCompany_image = url(
                                                                'public/user-uploads/company-logo/' .
                                                                @$val_meeting->fromCompany->logo,
                                                            );
                                                        }

                                                        $imagePath = url('public/img/star/5-blank.png');
                                                        $text = 'Blank';

                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <div
                                                                class="form-check form-check-sm form-check-custom form-check-solid">
                                                                <input class="form-check-input widget-9-check"
                                                                    type="checkbox" value="1">
                                                            </div>
                                                        </td>
                                                        <td class="min-w-100px">
                                                            <div class="d-flex flex-column">
                                                                <span
                                                                    class="text-dark fw-bolder fs-6">{{ @$interview_mode }}</span>
                                                                <span
                                                                    class="fw-bold text-muted d-block fs-7">{{ @$interview_mode_child_level }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="min-w-50px">
                                                            <div class="d-flex flex-column">
                                                                <div class="symbol-group symbol-hover">
                                                                    <div class="symbol symbol-30 symbol-circle"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="One">
                                                                        <img alt="{{ ucwords(@$val_meeting->fromCompany->company_name) }}"
                                                                            src="{{ str_replace('index.php', '', @$fromCompany_image) }}">
                                                                    </div>
                                                                </div>
                                                                <a href="javascript:void(0);"
                                                                    class="text-muted fw-bold d-block fs-7 text-hover-primary"
                                                                    data-toggle="tooltip"
                                                                    title="{{ ucwords(@$val_meeting->fromCompany->company_name) }}">
                                                                    {{ substr(ucwords(@$val_meeting->fromCompany->company_name), 0, 10) }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <div class="symbol-group symbol-hover">
                                                                    @php
                                                                        if (!empty($panellistArr)) {
                                                                            foreach ($panellistArr as $pval) {
                                                                                $bgTextDecore = $strVal = $team_full_name =
                                                                                    '';
                                                                                $str = $team_full_name = trim(@$pval);
                                                                                $str = $strVal = strtolower($str[0]);
                                                                                $str = ord($str);
                                                                                if ($str >= 97 && $str <= 101) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-danger text-danger';
                                                                                } elseif ($str >= 102 && $str <= 106) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-warning text-warning';
                                                                                } elseif ($str >= 107 && $str <= 111) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-primary text-primary';
                                                                                } elseif ($str >= 112 && $str <= 116) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-success text-success';
                                                                                } elseif ($str >= 117 && $str <= 121) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-dark text-secondary';
                                                                                } else {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-secondary text-dark';
                                                                                }
                                                                                echo '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" data-bs-original-title="' .
                                                                                    ucwords(@$team_full_name) .
                                                                                    '">
                                                                      <div class="symbol-label ' .
                                                                                    @$bgTextDecore .
                                                                                    '">' .
                                                                                    strtoupper(@$strVal) .
                                                                                    '</div>
                                                                      </div>';
                                                                            }
                                                                        }
                                                                    @endphp
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <div class="symbol-group symbol-hover">
                                                                    @php
                                                                        if (!empty($panellistArrour_team)) {
                                                                            foreach ($panellistArrour_team as $pval) {
                                                                                $bgTextDecore = $strVal = $team_full_name =
                                                                                    '';
                                                                                $str = $team_full_name = trim(@$pval);
                                                                                $str = $strVal = strtolower($str[0]);
                                                                                $str = ord($str);
                                                                                if ($str >= 97 && $str <= 101) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-danger text-danger';
                                                                                } elseif ($str >= 102 && $str <= 106) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-warning text-warning';
                                                                                } elseif ($str >= 107 && $str <= 111) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-primary text-primary';
                                                                                } elseif ($str >= 112 && $str <= 116) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-success text-success';
                                                                                } elseif ($str >= 117 && $str <= 121) {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-dark text-secondary';
                                                                                } else {
                                                                                    $bgTextDecore =
                                                                                        'fs-8 bg-light-secondary text-dark';
                                                                                }
                                                                                echo '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" data-bs-original-title="' .
                                                                                    ucwords(@$team_full_name) .
                                                                                    '">
                                                                      <div class="symbol-label ' .
                                                                                    @$bgTextDecore .
                                                                                    '">' .
                                                                                    strtoupper(@$strVal) .
                                                                                    '</div>
                                                                      </div>';
                                                                            }
                                                                        }
                                                                    @endphp
                                                                </div>
                                                            </div>
                                                        </td>
                                                        
                                                        <td class="min-w-140px">
                                                            <div class="d-flex flex-column">
                                                                <span class="text-dark fw-bolder fs-6"> {{ @$newDate }}
                                                                </span>
                                                                <span class="text-muted fw-bold d-block fs-7">
                                                                    {{ @$newTime }} </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-end flex-shrink-0">
                                                                <a href="javascript:;"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 modal_show" id="{{ $val_meeting->id }}">
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none">
                                                                            <path opacity="0.3"
                                                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                fill="currentColor"></path>
                                                                            <path
                                                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                fill="currentColor"></path>
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </a>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{ $MeetingSchedule->links() }}
                            <br>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Content-->
@endsection
@push('footer-script')
<script src="{{ asset('assets/plugins/jQueryUI/jquery-ui.min.js') }}{{ TM_VERSION }}"></script>


@endpush 