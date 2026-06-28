@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

    $reportMonth = Carbon::now()->startOfMonth()->subMonth();
    $rankstartOfMonthForReport = $reportMonth->copy()->startOfMonth()->toDateTimeString();      // 2025-06-01
    $rankendOfMonthForReport = $reportMonth->copy()->endOfMonth()->toDateTimeString(); 
    $rankstartOfMonthForReport = Carbon::parse($rankstartOfMonthForReport)->toIso8601String();
	$rankendOfMonthForReport = Carbon::parse($rankendOfMonthForReport)->toIso8601String();
    $isInternal = true;

    

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;
    // dd($row->company_id, $row->id, $rankstartOfMonthForReport, $rankendOfMonthForReport, $isInternal = true);
    $teamRanks = (object) GeneralMethod::getTeamRankData($row->company_id, $row->id, $rankstartOfMonthForReport, $rankendOfMonthForReport, $isInternal = true);

    // dd(@$teamRanks->rankMonth);

    @endphp
        <tr style="height: 102px; position: relative;">
            
            <td class="d-flex">
                <div class="mt-4">
                @php
                    if ($row->root_user == 1) {
                        echo '<div class="form-check form-check-sm form-check-solid d-flex flex-column me-2"><input class="form-check-input form-check-input1"  type="checkbox" disabled></div>';
                    } else {
                        $status_disabled = "disabled";
                        $status = $row->status;
                    if ($status == 'active') {
                        $status_disabled = '';
                    }
                        echo '<div class="form-check form-check-sm form-check-custom form-check-solid d-flex flex-column me-2"><input class="form-check-input form-check-input1 chk_list_item" name="chk_list_item[]" type="checkbox" value="' . $row->id . '" '.$status_disabled.'></div>';
                    }
                @endphp
                </div>

                @php
                    $bgTextDecore = $strVal = '';
                    $str = trim(@$row->name);
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
                    $icon =
                        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 show-detail-team" data-row-id="' .
                        $row->id .
                        '" style="float:left" >
													 <a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="show-detail-team" id="kt_activities3_toggle" >
														<div class="symbol-label fs-3 ' .
                        @$bgTextDecore .
                        '  border-radius-5">' .
                        ucwords(@$strVal) .
                        '</div>
													</a>
											</div>';
                    if (!empty($row->image)) {
                        $imagePath = '';
                        $imagePath = @$row->image;
                        $icon =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 show-detail-team" data-row-id="' .
                            $row->id .
                            '" style="float:left">
														<a href="javascript:void(0);">
															<div class="symbol-label border-radius-5">
															<img src="' .
                            $imagePath .
                            '" alt="User Image" class="border-radius-5 w-100">
															</div>
														</a>
													</div>';
                    }
                    $departmentArr = ucwords(@$row->designation);
                    echo '<div>'.$icon .
                        '<div class="d-flex flex-column" data-row-id="' .
                        $row->id .
                        '">
										<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' .
                        ucwords(@$row->name) .
                        '">' .
                        ucwords(mb_substr(@$row->name, 0, 30)) .
                        '</a>
										<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$departmentArr .
                        '</a>
									  </div>
									  </div>';
                @endphp
            </td>

            <td>
                @php
                   
                    echo ' <div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">#'.@$teamRanks->rank.' <span class="text-muted fw-bold fs-7">('.@$teamRanks->rankMonth.')</span></span>
					<span class="badge badge-light-success text-muted fw-bold d-block fs-7 teamViewScore" data-profileUpload="'.@$teamRanks->profileUpload.'" data-interviewScheduled="'.@$teamRanks->interviewScheduled.'" data-interviewNoShow="'.@$teamRanks->interviewNoShow.'" data-offerReleased="'.@$teamRanks->offerReleased.'" data-candidateJoined="'.@$teamRanks->candidateJoined.'" data-teamCaptainInterviewScheduled="'.@$teamRanks->teamCaptainInterviewScheduled.'" data-teamCaptainOfferReleased="'.@$teamRanks->teamCaptainOfferReleased.'" data-teamCaptainCandidateJoined="'.@$teamRanks->teamCaptainCandidateJoined.'"  data-sourcing="'.@$teamRanks->sourcing.'"  data-screening="'.@$teamRanks->screening.'"  data-evaluation="'.@$teamRanks->evaluation.'"  data-negotiation="'.@$teamRanks->negotiation.'"  data-backout="'.@$teamRanks->backout.'" data-backoutteam="'.@$teamRanks->backoutTeam.'" data-rankMonth="'.@$teamRanks->rankMonth.'" data-score="'.@$teamRanks->score.'" data-rank="'.@$teamRanks->rank.'" style="width: fit-content;cursor: pointer;">View Score</span>
						</div>';
                @endphp
            </td>


            <td>
                @php
                    $activeJobs = DB::table('job_assign')
                        ->join('jobs', 'jobs.id', '=', 'job_assign.job_id')
                        ->where('jobs.status', '=', 'active')
                        ->where('job_assign.user_id', $row->id)
                        ->count();

                    $departmentArr = '';
                    echo '<div class="d-flex flex-column">
										<span class="fw-bolder text-success text-muted fw-bold d-block fs-7 fs-6">' .
                        GeneralMethod::teamActiveJobsCount($row->id).
                        ' Active</span>
									  </div>
									  ';

                @endphp
            </td>
            <td>
                @php
                    $disabled = $row->root_user == 1 ? '' : 'update-user-role';
                    $update =
                        $row->root_user == 1
                            ? ''
                            : '<span class="svg-icon svg-icon-primary cursor-pointer update-role">
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24"/>
													<path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
													<rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
												</g>
												</svg>
											</span>';
                    echo '<div class="d-flex flex-column ' .
                        $disabled .
                        '" data-row-id="' .
                        $row->id .
                        '">
										<span class="text-dark fw-bolder fs-6">' .
                        ucwords($row->display_name) .
                        '
										 
											' .
                        $update .
                        '
										</span>
									</div>';
                @endphp

            </td>
            <td>
                @php
                    $chk = $disabled = '';
                    $status = $row->status;
                    $status_text = 'Inactive';
                    $status_text_color = "text-danger";
                    if ($status == 'active') {
                        $chk = 'checked="checked"';
                        $status_text = 'Active';
                        $status_text_color = "text-success";
                    }
                    $disabled = $row->root_user == 1 ? 'disabled="disabled"' : '';
                    if (!empty($row->password)) {
                        echo '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
													<input class="form-check-input chkStatus" type="checkbox" value="active" name="staus" ' .
                            @$chk .
                            ' onclick="handleClick(this, ' .
                            $row->id .
                            ');" ' .
                            $disabled .
                            '>
												</div>
                                                <span class="fw-bold d-block fs-7 '.$status_text_color.' status_text">'.$status_text.'</span>';
                    } else {
                        echo '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
													<a href="javascript:;" onclick="reSendhandleClick(this, ' .
                            $row->id .
                            ');">Resend Mail <i class="fa fa-repeat" aria-hidden="true"></i></a>
												</div>
                                                <span class="text-danger status_text fw-bold d-block fs-7">Inactive</span>';
                    }
                @endphp
            </td>
            {{-- <td>
                @php
                    $chk = $disabled = '';
                    $working_status = $row->working_status;
                    if ($working_status == 1) {
                        $chk = 'checked="checked"';
                    }
                    $disabled = $row->root_user == 1 ? 'disabled="disabled"' : '';
                    if (!empty($row->password)) {
                        echo '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
												<input id="working_status_' .
                            $row->id .
                            '" class="form-check-input chkStatus" type="checkbox" value="1" name="working_status" ' .
                            @$chk .
                            ' onclick="WorkingStatus(this, ' .
                            $row->id .
                            ');"   ' .
                            @$disabled .
                            '  ></div>';
                    } else {
                        echo '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid" style="position: relative;">
												<input id="working_status_' .
                            $row->id .
                            '" class="form-check-input chkStatus" type="checkbox" value="1" name="working_status" ' .
                            @$chk .
                            '  ' .
                            @$disabled .
                            '  >
												<div style="position: absolute; top: 0; width: 100%; height: 100%;"> </div>
												</div>
												';
                    }
                @endphp
            </td> --}}
            <td>
                <div class="d-flex flex-column">
                    @php
                        $whatsappConnectCheck =  GeneralMethod::whatsappConnectCheck($row->id);
                    @endphp
                    
                        <a href="javascript:;" class="text-success fw-bolder d-block fs-7 sendwhatsapp_connected_{{ $row->id }} {{ ($whatsappConnectCheck) ? '' : 'd-none' }}" style="background: #50cd890d; width: 107px; padding: 4px; border-radius: 7px;"><i class="fab fa-whatsapp text-success"></i> <span id="f_phone_user{{  $row->id  }}">{{ $whatsappConnectCheck }}</span>  <br>
                        </a>
                        <a href="javascript:;" data-userid="{{ $row->id }}" class="text-danger fw-bold d-block fs-7 sendwhatsapp_connected_{{ $row->id }} {{ ($whatsappConnectCheck) ? '' : 'd-none' }} deleteBtn" style="background: #f1416c0a; width: 107px; padding: 4px; border-radius: 7px;margin-top: 1px;"><i class="fa fa-power-off text-danger"></i> Disconnect</a>
                   
                        
                        <a href="javascript:;" id="" data-userid="{{ $row->id }}" class="text-white fw-bold d-block fs-7 sendwhatsapp_panel sendwhatsapp_connect_{{ $row->id }} sendwhatsapp_text_{{ $row->id }} {{ ($whatsappConnectCheck) ? 'd-none' : '' }}" style="background: #009ef7; width: 115px; padding: 4px; border-radius: 7px;"><i class="fab fa-whatsapp text-white" style=""></i> <span id="">Click to Connect</span></a>

                        
                   

                </div>
            </td>
            <td>
                @php
                    $created_at = $row->created_at;
                    $strtotime = strtotime($created_at);
                    $newDate = date('d-M-Y', $strtotime);
                    $newDateTime = date('h:i A', $strtotime);
                    $temp = '';
                    echo ' <div class="d-flex flex-column">
												   <span class="text-dark fw-bolder fs-6">' .
                        @$newDate .
                        '</span>
												   <span class="text-muted fw-bold d-block fs-7">' .
                        @$newDateTime .
                        '</span>
												</div>';
                @endphp
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="6" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
