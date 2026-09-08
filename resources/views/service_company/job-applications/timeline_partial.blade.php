<?php
use App\Helper\GeneralMethod;

if(empty($updateTimeLine)){
    echo '<div class="text-center p-5 text-muted">No timeline activity recorded yet.</div>';
} else {
    foreach($updateTimeLine as $tdata){
        $tdata = (object) $tdata;
        $tdata->update_at = $tdata->updated_at;
                        $company_or_client = isset($companyOrClientMap[$tdata->job_id]) ? $companyOrClientMap[$tdata->job_id] : (!empty($tdata->cur_companyname) ? $tdata->cur_companyname : 'N/A');
                        $create_at	=	$newDate	=	$newTime	= $prev_tiles_bg	=	$timeline_type = $current_tiles_bg =  "";
                        $create_at	=	$tdata->created_at;
                        $newDate 							=	date("d-M-Y", strtotime($create_at));
                        $newTime 							=	date("H:i A", strtotime($create_at));
                        $posted_by_image					=	$comment							=	"";
                        if(!empty(@$tdata->image)){
                          $posted_by_image				=	@$tdata->image;
                        }
                        if(!empty(@$tdata->prev_process_name)){
                          $prevCandStatusArr				= 	GeneralMethod::candidateStatusColorCode(@$tdata->prev_process_name);$prev_tiles_bg					=	$prevCandStatusArr['tiles_bg'];
                        }
                        if(!empty(@$tdata->master_process_name)){
                          $currentCandStatusArr			= 	GeneralMethod::candidateStatusColorCode(@$tdata->master_process_name);
                          $current_tiles_bg				=	$currentCandStatusArr['tiles_bg'];
                        }
                        $master_process_name				=	@$tdata->master_process_name;
                        $prev_process_name					=	@$tdata->prev_process_name;
                        if($tdata->event_id == 1){
                          $prev_process_name				=	@$tdata->note;
                          $prev_tiles_bg					=	'sourcing-bg';
                        }
                        if($tdata->event_id == 2){
                          $comment						=	@$tdata->note;
                        }
                        if($tdata->event_id == 5 || $tdata->event_id == 6 || $tdata->event_id == 7 || $tdata->event_id == 8){
                          $comment						=	@$tdata->note;
                        }
                        $offer_status = $management_status = '----';
                        $downloadOffer = '';
                        $candStatus = @$tdata->candidate_status;
                        if($candStatus == 1){

                          $offer_status = 'Offer Released';
                          $allow = isset($confidentialAllowedJobsMap[$tdata->job_id]) ? $confidentialAllowedJobsMap[$tdata->job_id] : (!empty($tdata->job_id) ? GeneralMethod::confidentialAmontShow($tdata->job_id) : false);
                          if($allow){
                             $downloadOffer = '<i  data-jobId="'.@$application->jobId.'" data-candidate="'.@$application->candidate_id.'" data-mtype="3" data-offerId="'.@$application->offer_id.'" class="fa fa-download download_offer"  data-wizard-type="action-next" style="width: 100%;cursor: pointer;"></i>';
                          }else{
                            $offer_job_applications = isset($offerJobApplicationsMap[@$application->offer_id]) ? $offerJobApplicationsMap[@$application->offer_id] : (!empty(@$application->offer_id) ? DB::table('offer_job_applications')->where('id','=',@$application->offer_id)->first() : null);
                            if($offer_job_applications && $offer_job_applications->submitted_by != auth()->user()->id){
                                $downloadOffer = '<i  data-jobId="'.@$application->jobId.'" data-candidate="'.@$application->candidate_id.'" data-mtype="3" data-offerId="'.@$application->offer_id.'" class="fa fa-download ConfidentialClass"  data-wizard-type="action-next" style="width: 100%;cursor: pointer;"></i>';
                            }else{
                             $downloadOffer = '<i  data-jobId="'.@$application->jobId.'" data-candidate="'.@$application->candidate_id.'" data-mtype="3" data-offerId="'.@$application->offer_id.'" class="fa fa-download download_offer"  data-wizard-type="action-next" style="width: 100%;cursor: pointer;"></i>';
                            }

                          }

                        }elseif($candStatus == 2){
                          $offer_status = 'Offer Accepted';
                        }elseif($candStatus == 3){
                          $offer_status = 'Offer Rejected';
                        }elseif($candStatus == 4){
                          $offer_status = 'Offer Expired';
                        }elseif(isset($tdata->candidate_status) && $candStatus == 0){
                          $offer_status = 'Offer Created';
                        }
                        $mgmtStatus = @$tdata->management_status;
                        if($mgmtStatus == 1){
                          $management_status = 'In Process';
                        }elseif($mgmtStatus == 2){
                          $management_status = 'Approved';
                        }elseif($mgmtStatus == 3){
                          $management_status = 'Not Approved';
                        }
                        $feedback_status = $feedback_status ?? GeneralMethod::feedback_status();
                        $available_status = $available_status ?? GeneralMethod::available_status();
                        $available_status_name = '';
                        if (!empty(@$tdata->available_status)) {
                          $available_status_name = @$available_status[@$tdata->available_status]['name'];
                        }
                        $feedback = ucwords(substr(@$feedback_status[@$tdata->feedback]['name'], 0, 12));
                        if($CaptainCompanyid == $tdata->event_by_company_id){
                                $rowpostedby = $tdata->name;
                        }else{
                            $rowpostedby = $tdata->name;
                            $userCCompanyId = isset($userCompanyMap[$tdata->event_by_user_id]) ? $userCompanyMap[$tdata->event_by_user_id] : null;
                            if(!$userCCompanyId){
                                $userC = DB::table('users')->select('company_id')->where('id',$tdata->event_by_user_id)->first();
                                $userCCompanyId = @$userC->company_id;
                            }
                            if($CaptainCompanyid==$userCCompanyId){
                                    $rowpostedby = $tdata->name;
                              }else{
                                $spokKey = $tdata->job_id . '_' . $tdata->event_by_company_id;
                                $spokperson = isset($spokpersonMap[$spokKey]) ? $spokpersonMap[$spokKey] : DB::table('job_assign')->select('users.name','job_assign.company_id')->leftjoin('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $tdata->job_id)->where('job_assign.company_id', $tdata->event_by_company_id)->first();
                                if($spokperson){
                                    if(!empty($spokperson->name)){
                                      if($CaptainCompanyid !=$spokperson->company_id){
                                        $rowpostedby = (@$spokperson->name) ? @$spokperson->name : $tdata->name;
                                      }else{
                                        $rowpostedby = $tdata->name;
                                      }
                                  }
                                }else{
                                  $rowpostedby = $tdata->name;
                                }
                              }
                        }

                        $rowpostedby = ($rowpostedby) ? $rowpostedby : $tdata->name;
                        $bgTextDecore				=	GeneralMethod::icoinText($rowpostedby);

                        if(($tdata->event_id == 1) || ($tdata->event_id == 2))
                        {
                        ?>
                              <div class="timeline-item">
                                <div class="timeline-line w-40px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-40px">
                                  <div class="symbol-label bg-light">
                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                          <polygon points="0 0 24 0 24 24 0 24"/>
                                          <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                                          <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                                          <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                                          <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                                        </g>
                                      </svg>
                                    </span>
                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                          <rect x="0" y="0" width="24" height="24"/>
                                          <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                      </svg>
                                    </span>
                                  </div>
                                </div>
                                <div class="timeline-content mb-10 mt-n1">
                                  <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                                    <tbody class="text-gray-600 fw-bold">
                                      <tr>
                                        <td class="min-w-150px">
                                          <div class="d-flex flex-column">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a>
                                            <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span>
                                          </div>
                                        </td>
                                        <td class="min-w-150px">
                                          <div class="d-flex flex-column">
                                            <span class="badge fw-bolder fs-6 {{ @$prev_tiles_bg }} px-2 py-1">{{ ucwords(@$prev_process_name) }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->prev_process_child_name) }}</span>
                                          </div>
                                        </td>
                                        <td class="min-w-50px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6"> To </span>
                                          </div>
                                        </td>
                                        <td class="min-w-150px">
                                          <div class="d-flex flex-column">
                                            <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->child_process_name) }}</span>
                                          </div>
                                        </td>
                                        <td class="min-w-120px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>
                                          </div>
                                        </td>
                                        <td class="min-w-50px">
                                          <div class="d-flex flex-column">
                                            <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                                                 <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                                  <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                                </div>
                                              </div>
                                            </div>
                                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  @if(!empty($comment))
                                    <div class="overflow-auto pb-5">
                                      <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                        <div class="mb-1">
                                          <p class="<?php //echo @$current_tiles_bg; ?>">
                                            <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                            {{ @$comment }}
                                          </p>
                                        </div>
                                      </div>
                                    </div>
                                  @endif
                                </div>
                              </div>
                        <?php
                        }else if($tdata->event_id==3){
                            $interview_schedule_date	=	$newDate	=	$newTime =	$interview_mode	=	$interview_mode_child_level	= $interviewers = $panellistArr = 	"";
                            $interview_schedule_date	=	$tdata->interview_schedule_date;
                            $interview_to_time	=	$tdata->interview_to_time;
                            $newDate 					=	date("d-M-Y", strtotime($interview_schedule_date));
                            $newTime 					=	date("h:i A", strtotime(@$interview_to_time));
                            if($tdata->interview_mode > 0){
                              $interview_mode_Arr		= 	GeneralMethod::interview_mode();
                              $interview_mode			= 	$interview_mode_Arr[@$tdata->interview_mode];
                            }
                            $interview_mode_child_level	=	$tdata->interview_mode_child_level;
                            if($tdata->interview_mode==3){
                              $interview_mode_child_level	=	@$tdata->interview_mention_link;
                            }
                            $interviewers				=	$tdata->interviewers;
                            if(!empty($interviewers)){
                              $panellistArr 			=	explode(',', $interviewers);
                            }
                            $average_star_rating	=	'';
                            $average_star_rating	=	$tdata->average_star_rating;
                            $imagePath				=	$text = 	'';
                            if($average_star_rating == 1){
                              $imagePath	=	url('public/img/star/1.png');
                              $text		=	'Bad';
                            }else if($average_star_rating == 2){
                              $imagePath	=	url('public/img/star/2.png');
                              $text		=	'Poor';
                            }else if($average_star_rating == 3){
                              $imagePath	=	url('public/img/star/3.png');
                              $text		=	'Average';
                            }else if($average_star_rating == 4){
                              $imagePath	=	url('public/img/star/4.png');
                              $text		=	'Best';
                            }else if($average_star_rating == 5){
                              $imagePath	=	url('public/img/star/5.png');
                              $text		=	'Very Best';
                            }else{
                              $imagePath	=	url('public/img/star/5-blank.png');
                              $text		=	'Blank';
                            }
                        ?>
                            <div class="timeline-item">
                              <!--begin::Timeline line-->
                              <div class="timeline-line w-40px"></div>
                              <!--end::Timeline line-->
                              <!--begin::Timeline icon-->
                              <div class="timeline-icon symbol symbol-circle symbol-40px">
                                <div class="symbol-label bg-light">
                                  <!--begin::Svg Icon-->
                                  <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
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
                                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                                  <!--begin::Table body-->
                                  <tbody class="text-gray-600 fw-bold">
                                    <!--begin::Table row-->
                                    <tr>
                                      <!--begin::Requisitions=-->
                                      <td class="min-w-150px">
                                        <div class="d-flex flex-column">
                                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"> {{ ucwords(@$tdata->title)}} </a>
                                          <span class="fw-bold text-muted d-block fs-7"> {{ ucwords(@$company_or_client) }}  </span>
                                        </div>
                                        <!--end::Requisitions=-->
                                        <!--begin::Interview Mode=-->
                                        <td class="min-w-100px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ @$interview_mode }}</span>
                                            <span class="fw-bold text-muted d-block fs-7">{{ @$interview_mode_child_level }}</span>
                                          </div>
                                        </td>
                                        <!--end::Interview Mode=-->
                                        <!--begin::Interviewers=-->
                                        <td class="min-w-90px">
                                          <div class="d-flex flex-column">
                                            <div class="symbol-group symbol-hover">
                                               @php
                                                  if(!empty($panellistArr)){
                                                    foreach($panellistArr as $pval)
                                                    {
                                                      $bgTextDecore = $strVal = $team_full_name	=	 '';
                                                      $str 			= $team_full_name =  trim(@$pval);
                                                      $str 			= $strVal = strtolower($str[0]);
                                                      $str = ord($str);
                                                      if ($str >= 97 && $str <= 101) {
                                                        $bgTextDecore = 'fs-8 bg-light-danger text-danger';
                                                      }
                                                      else if ($str >= 102 && $str <= 106) {
                                                        $bgTextDecore = 'fs-8 bg-light-warning text-warning';
                                                      }
                                                      else if ($str >= 107 && $str <= 111) {
                                                        $bgTextDecore = 'fs-8 bg-light-primary text-primary';
                                                      }
                                                      else if ($str >= 112 && $str <= 116) {
                                                        $bgTextDecore = 'fs-8 bg-light-success text-success';
                                                      }
                                                      else if ($str >= 117 && $str <= 121) {
                                                        $bgTextDecore = 'fs-8 bg-dark text-secondary';
                                                      } else {
                                                        $bgTextDecore = 'fs-8 bg-light-secondary text-dark';
                                                      }
                                                      echo '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" data-bs-original-title="'.ucwords(@$team_full_name).'">
                                                        <div class="symbol-label '.@$bgTextDecore.'">'.strtoupper(@$strVal).'</div>
                                                        </div>';
                                                    }
                                                  }
                                                @endphp
                                            </div>Interviewer
                                          </div>
                                        </td>
                                        <!--end::Interviewers=-->
                                        <!--begin::Interviewer Feedback=-->
                                        <td class="min-w-70px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder  fs-6">{{ ($feedback) ? $feedback : '----' }}</span> <span class="fw-bold text-muted d-block fs-7">{{ ($available_status_name ? $available_status_name : '----') }}</span>
                                          </div>
                                        </td>
                                        <!--end::Interviewer Feedback-->
                                        <!--begin::Date-->
                                        <td class="min-w-140px">
                                          <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6"> {{ @$newDate}} </span>
                                            <span class="text-muted fw-bold d-block fs-7"> {{ @$newTime }} {{ (@$tdata->timezone_abbreviation) ? '('.@$tdata->timezone_abbreviation.')' :'' }}
                                            </span>
                                          </div>
                                        </td>
                                        <!--end::Date-->
                                        <!--begin::Image-->
                                        <td class="min-w-90px">
                                          <img src="{{  str_replace('index.php','',@$imagePath) }}" alt="image" style="width: 5.5rem"> <span class="fw-bold text-muted d-block fs-7">{{ @$text }}</span>
                                        </td>
                                        <!--end::Image-->
                                        <!--begin::Soruce-->
                                        <td class="min-w-50px">
                                          <div class="d-flex flex-column">
                                            <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name)}}">
                                                <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}">
                                                  <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                                </div>
                                              </div>
                                            </div>
                                            <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="{{ ucwords(@$tdata->cur_companyname) }}" style="text-align: center;">{{ substr(ucwords(@$tdata->cur_companyname), 0, 20) }}</span>
                                          </div>
                                        </td>
                                        <!--end::Soruce-->
                                        <!--begin::Status-->
                                        <td class="min-w-120px">
                                          <div class="d-flex flex-column">
                                            <span class="badge fw-bolder fs-6 {{ @$tdata->css_style }} px-2 py-1">{{ ucwords(@$tdata->status_name) }}</span>
                                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->process_child_name) }}</span>
                                          </div>
                                        </td>
                                        <!--end::Stage-->
                                    </tr>
                                      <!--end::Table row-->
                                  </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                          @php
                                $interview_feedback = isset($interviewFeedbackMap[$tdata->interview_schedules_id])
                                    ? (is_array($interviewFeedbackMap[$tdata->interview_schedules_id]) ? array_map(fn($f) => (object)$f, $interviewFeedbackMap[$tdata->interview_schedules_id]) : $interviewFeedbackMap[$tdata->interview_schedules_id])
                                    : DB::table('interview_feedback')
                                        ->select('interview_feedback.created_at', 'interview_feedback.message', 'interview_feedback.star_ratings','users.name', 'users.image')
                                        ->join('users', 'users.id', '=', 'interview_feedback.update_by')
                                        ->where('interview_feedback.application_id', @$tdata->application_id)
                                        ->where('interview_feedback.interview_id', @$tdata->interview_schedules_id)
                                        ->get();
                                $feedbackdataView ='';
                                $feedbackdata = isset($interviewSkillsFeedbackCountMap[$tdata->interview_schedules_id])
                                    ? $interviewSkillsFeedbackCountMap[$tdata->interview_schedules_id]
                                    : DB::table('interview_skills_feedback')->where('interview_id',$tdata->interview_schedules_id)->orderByDesc('feedback_id')->count();
                                if($feedbackdata>0){
                                    $feedbackdataView = '<div class="overflow-auto pb-5">
                                      <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                        <span href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                          &nbsp;Interview Feedback
                                          </span>  <span class="badge skill-feedback fw-bolder fs-6 interview px-2 py-1" data-row-id="' .$tdata->interview_schedules_id .'" style="cursor: pointer; margin-left: 4px; margin-top: 5px;">View</span>
                                      </div>
                                  </div>';
                                }else{
                                  $feedbackdataView = '<div class="overflow-auto pb-5">
                                      <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                        <span href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                          &nbsp;Interview Feedback Pending
                                          </span>
                                      </div>
                                  </div>';
                                }
                        @endphp
                                {!! $feedbackdataView !!}
                        @php
                                if(count($interview_feedback) > 0){
                                  foreach($interview_feedback as $intfeed){
                                    $newDate 		=	date("d-m-Y", strtotime($intfeed->created_at));
                                    $newTime 		=	date("H:i A", strtotime($intfeed->created_at));
                                    $bgTextDecore				=	GeneralMethod::icoinText(@$intfeed->name);
                                    $imagePath	=	'';
                                    if(!empty($intfeed->image)){
                                      $imagePath	=	@$intfeed->image;
                                    }
                                    $feedback_star_imagePath	=	$feedback_star_text =  '';
                                    $feedbackstar_ratings				=	$intfeed->star_ratings;
                                    if($feedbackstar_ratings == 1){
                                      $feedback_star_imagePath	=	url('public/img/star/1.png');
                                      $feedback_star_text			=	'Bad';
                                    }else if($feedbackstar_ratings == 2){
                                      $feedback_star_imagePath	=	url('public/img/star/2.png');
                                      $feedback_star_text			=	'Poor';
                                    }else if($feedbackstar_ratings == 3){
                                      $feedback_star_imagePath	=	url('public/img/star/3.png');
                                      $feedback_star_text			=	'Average';
                                    }else if($feedbackstar_ratings == 4){
                                      $feedback_star_imagePath	=	url('public/img/star/4.png');
                                      $feedback_star_text			=	'Best';
                                    }else if($feedbackstar_ratings == 5){
                                      $feedback_star_imagePath	=	url('public/img/star/5.png');
                                      $feedback_star_text			=	'Very Best';
                                    }else{
                                      $feedback_star_imagePath	=	url('public/img/star/5-blank.png');
                                      $feedback_star_text			=	'Blank';
                                    }
                                  }
                                }
                        @endphp
                        </div>
                          <!--end::Timeline content-->
                        </div>
                        <?php
                        }else if($tdata->event_id == 4){
                          // Offer Section
                          $currency 				=	GeneralMethod::currency();
                          $salaryMode 			=	GeneralMethod::salaryModeOffer();
                          $offer_currency			=	'';
                          if($tdata->offer_currency >= 0){
                            $offer_currency			= $currency[(int)@$tdata->offer_currency];
                          }
                          $offer_mode			=	'';
                          if($tdata->offer_mode >= 0){
                             $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                          }
                          $joined_time = '';
                          if(!empty($tdata->joined_date)){
                            $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                          }
                        ?>
                        <div class="timeline-item">
                          <!--begin::Timeline line-->
                          <div class="timeline-line w-40px"></div>
                          <!--end::Timeline line-->
                          <!--begin::Timeline icon-->
                          <div class="timeline-icon symbol symbol-circle symbol-40px">
                            <div class="symbol-label bg-light">
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                  </g>
                                </svg>
                              </span>
                              <!--end::Svg Icon-->
                            </div>
                          </div>
                          <!--end::Timeline icon-->
                          <!--begin::Timeline content-->
                          <div class="timeline-content mb-10 mt-n1">
                            <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1269px;">
                              <thead>
                                <tr class="fw-bolder text-muted" role="row">
                                  <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Created</th>
                                  <th class="sorting_disabled">Offer Amount</th>
                                  <th class="sorting_disabled" style="width: 244px;">Joining Date</th>
                                  <th class="sorting_disabled" style="width: 79px;">Offer Status</th>
                                  <th class="sorting_disabled" style="width: 79px;">Create Date</th>
                                  <th class="sorting_disabled" style="width: 79px;">Stage</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr role="row" class="odd">
                                  <td class="min-w-150px">
                                      <div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a>
                                        <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span>
                                      </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                       @php
                                        $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);
                                      @endphp
                                      @if($allow)
                                     <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->yearly_final_gross) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif

                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{$joined_time }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-150px">
                                    <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'screening' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                    </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->created_at) }}</span>
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->created_at)) }}(IST)</span>
                                    </div>
                                  </td>
                                  <td>
                                      <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                  </td>
                                  <td class="min-w-50px">
                                    <div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover justify-content-center">
                                        <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                                          <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                            <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                          </div>
                                        </div>
                                      </div>
                                      <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                    </div>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                <?php
                  }else if($tdata->event_id == 10){
                    $currency 				=	GeneralMethod::currency();
                    $salaryMode 			=	GeneralMethod::salaryModeOffer();
                    $offer_currency			=	'';
                    if($tdata->offer_currency >= 0){
                      $offer_currency			= $currency[(int)@$tdata->offer_currency];
                    }
                    $offer_mode			=	'';
                    if($tdata->offer_mode >= 0){
                      $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                    }

                  $joined_time = '';
                  if(!empty($tdata->joined_date)){
                    $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                  }
                ?>
                    <div class="timeline-item">
                      <!--begin::Timeline line-->
                      <div class="timeline-line w-40px"></div>
                      <!--end::Timeline line-->
                      <!--begin::Timeline icon-->
                      <div class="timeline-icon symbol symbol-circle symbol-40px">
                        <div class="symbol-label bg-light">
                          <!--begin::Svg Icon-->
                          <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                              </g>
                            </svg>
                          </span>
                          <!--end::Svg Icon-->
                        </div>
                      </div>
                      <!--end::Timeline icon-->
                      <!--begin::Timeline content-->
                      <div class="timeline-content mb-10 mt-n1">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1269px;">
                          <thead>
                            <tr class="fw-bolder text-muted" role="row">
                              <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Expired</th>
                              <th class="sorting_disabled">Offer Amount</th>
                              <th class="sorting_disabled" style="width: 244px;">Joining Date</th>
                              <th class="sorting_disabled" style="width: 79px;">Offer Status</th>
                              <th class="sorting_disabled" style="width: 79px;">Expired Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr role="row" class="odd">
                              <td class="min-w-150px">
                                <div class="d-flex flex-column">
                                  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a>
                                  <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span>
                                </div>
                              </td>
                              <td class="min-w-120px">
                                <div class="d-flex flex-column">
                                    @php
                                      $offerAmont = "Confidential";
                                      $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);
                                    @endphp
                                    @if($allow)
                                    <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                    @else
                                    <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                    @endif
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                </div>
                              </td>
                              <td class="min-w-120px">
                                <div class="d-flex flex-column">
                                  <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                  <span class="text-muted fw-bold text-muted d-block fs-7">{{$joined_time }} </span>
                                </div>
                              </td>
                              <td class="min-w-150px">
                                <div class="d-flex flex-column">
                                  <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==4) ? 'rejected' : 'screening' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                </div>
                              </td>
                              <td class="min-w-120px">
                                <div class="d-flex flex-column">
                                  <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->created_at) }}</span>
                                  <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->created_at)) }}(IST)</span>
                                </div>
                              </td>
                              <td class="min-w-50px"><div class="d-flex flex-column">
                                  <div class="symbol-group symbol-hover justify-content-center">
                                      <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                                        <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                          <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                                        </div>
                                      </div>
                                    </div>
                                    <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                  </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                <?php
                }else if($tdata->event_id == 5){
                    $currency 				=	GeneralMethod::currency();
                    $salaryMode 			=	GeneralMethod::salaryModeOffer();
                    $offer_currency			=	'';
                    if($tdata->offer_currency >= 0){
                      $offer_currency			= $currency[(int)@$tdata->offer_currency];
                    }
                    $offer_mode			=	'';
                    if($tdata->offer_mode >= 0){
                      $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                    }
                    $joined_time = '';
                    if(!empty($tdata->joined_date)){
                      $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                    }
                ?>
                    <div class="timeline-item">
                      <div class="timeline-line w-40px"></div>
                      <div class="timeline-icon symbol symbol-circle symbol-40px">
                        <div class="symbol-label bg-light">
                          <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" iewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1" />
                                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1" />
                                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span>
                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span>
                        </div>
                      </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Offer Amount</th>
                                <th class="sorting_disabled" style="width: 79px;">Join Date</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$application->full_name) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{ucwords((@$tdata->designation_offer))}}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                     <div class="d-flex flex-column">

                                      @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);
                                      @endphp
                                      @if($allow)
                                        <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$joined_time }} </span>
                                  </div>
                                </td>

                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->send_accept_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->send_accept_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>

                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
                <?php
                   }else if($tdata->event_id == 6){
                ?>
                      <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1" />
                                  <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span>
                            <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span>
                          </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Approval</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{
                                      ucwords(@$tdata->email_id_hr) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{
                                      ucwords(@$tdata->name_hr) }}</span>
                                  </div>
                                </td>
                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->send_approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->send_approved_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>

            <?php }else if($tdata->event_id == 7){

                $currency 				=	GeneralMethod::currency();
                $salaryMode 			=	GeneralMethod::salaryModeOffer();

                $offer_currency			=	'';
                if($tdata->offer_currency >= 0){
                  $offer_currency			= $currency[(int)@$tdata->offer_currency];
                }
                $offer_mode			=	'';
                if($tdata->offer_mode >= 0){
                  $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                }
                $joined_time = '';
                if(!empty($tdata->joined_date)){
                   $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                 }

              ?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Offer Amount</th>
                                <th class="sorting_disabled" style="width: 79px;">Join Date</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$application->full_name) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{ucwords((@$tdata->designation_offer))}}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                   <div class="d-flex flex-column">
                                    @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);
                                      @endphp
                                      @if($allow)
                                        <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif

                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$joined_time }} </span>
                                  </div>
                                </td>

                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }} {!! $downloadOffer !!}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->accept_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->accept_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>

            <?php }elseif($tdata->event_id == 45){ ?>

               <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Whatsapp</a>
                          <span class="fw-bold text-muted d-block fs-7">Send</span>
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a>
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span>
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}">
                  @else --}}

                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->

							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>

           <?php }else if($tdata->event_id == 8){?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Approval</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{
                                      ucwords(@$tdata->email_id_hr) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{
                                      ucwords(@$tdata->name_hr) }}</span>
                                  </div>
                                </td>
                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->approved_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
            <?php }else if($tdata->event_id == 9){?>
              <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Mail</a>
                          <span class="fw-bold text-muted d-block fs-7">Send</span>
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a>
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span>
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}">
                  @else --}}

                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->

							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>

            <?php }else if($tdata->event_id == 11){?>
              <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column">
                          <a href="javascript:void(0);" class="text-{{($tdata->note=='Interested') ? 'success' : 'danger'}} fw-bolder text-hover-{{($tdata->note=='Interested') ? 'success' : 'danger'}} fs-6">{{@$tdata->note}}</a>
                          <span class="fw-bold text-muted d-block fs-7">Mail</span>
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a>
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span>
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}">
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}">
                  @else --}}

                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr(ucwords($tdata->name), 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->

							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>
            <?php } ?>

        <?php }
    } ?>



