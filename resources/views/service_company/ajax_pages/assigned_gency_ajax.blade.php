@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
        @php
          $row = (object) $rowArr;
          $logo_path = '';
            if (!empty(@$row->logo_to) && strlen(@$row->logo_to) > 10) {
                $logo_path = @$row->logo_to;
            }
        @endphp
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    $str = trim(@$row->company_name_to);
                     $getClientIdCompany = GeneralMethod::getClientIdCompany($row->to_company_id);
                    $ind_name = $getClientIdCompany->main_ind_name ?? '';
                    $client_type_css = 'show-detail-direct';

                    if (!empty($str)) {
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
                    }
                    if(!$logo_path){
                        echo '<div data-row-id="' .$row->id .'" class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 ' . $client_type_css .'" style="float:left">
                            <a href="javascript:void(0);" data-row-id="1210" class="show-application-detail">
                            <div class="symbol-label ' .
                                    @$bgTextDecore .
                                    ' border-radius-5">' .
                                    strtoupper(@$strVal) .
                                    '</div>
                            </a>
                            </div>';
                    }else{
                         echo ' <div data-row-id="' .$row->id .'" class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5 ' . $client_type_css .'" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
									<div class="symbol-label border-radius-5"> <img src="' .
                        $logo_path .
                        '" alt="Company Logo" class="border-radius-5 w-100"> </div>
								</a> 
							</div>';
                    }

                    echo '<div data-row-id="' .$row->id .'" class="d-flex flex-column ' . $client_type_css .'" style=" float:left;"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        substr(ucwords(@$row->company_name_to), 0, 30) .
                        '</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        substr(ucwords(@$ind_name), 0, 27) .
                        '</a>
							</div>
							';



                @endphp
            </td>
            <td>
                @php
                  $contHtml = '';
					$contactQuery = DB::table('users')
						->join('job_assign', 'job_assign.user_id', 'users.id')
						->select('users.name', 'users.image')
						->where('users.company_id', $row->to_company_id)
						->where('job_assign.company_id', $row->to_company_id)
						->where('job_assign.job_id', $row->job_id)
						->skip(0)->take(1)
						->get();

                        $contactQueryCount = DB::table('users')
						->join('job_assign', 'job_assign.user_id', 'users.id')
						->select('users.name', 'users.image')
						->where('users.company_id', $row->to_company_id)
						->where('job_assign.company_id', $row->to_company_id)
						->where('job_assign.job_id', $row->job_id)
						->count();
                        if($contactQueryCount > 0){
                                 $contactQueryCount = $contactQueryCount - 1;
                        }

						// dd(count($contactQuery));
					if (count($contactQuery)>0) {
						foreach ($contactQuery as $cont) {
							$bgTextDecore				=	GeneralMethod::icoinText(@$cont->name);
							$logo_path = '';
							
								$contHtml .=
								'<div class="d-flex flex-column">
                                     <div data-row-id="253" class="symbol-group symbol-hover client-hm-listing">
                                        <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="'.ucwords(@$cont->name).'">
                            <div class="overflow-hidden me-2">
                            <a href="javascript:void(0);" data-row-id="11" class="fw-bold text-muted d-block text-hover-primary fs76" id="kt_activities3_toggle">
                                <div class="fw-bold text-muted d-block fs-7 text-hover-primary">'.ucwords(@$cont->name).'</div>
                            </a>		 
                            </div>
                            </div>';
                             if($contactQueryCount){
                                $contHtml .='<div class="symbol symbol-30 symbol-circle symbol-light" data-bs-placement="top" data-toggle="tooltip2" title="More Comtact list.." style="margin-left: 0px;">
                                    <span class="symbol-label font-weight-bold">'.$contactQueryCount.'+</span>
                                </div>';
                             }
                            
                            $contHtml .=
								'</div>
                            </div>';
							
							
						}
					}else{
                        
                         $contHtml = 0;
                    }
					$contHtml .= '</div>';
					// dd($contHtml);
				   echo $contHtml;

                @endphp
            </td>
            <td>
                @php
                   $agencyCandidatesCount = GeneralMethod::agencyCandidatesCount($row->to_company_id,$row->job_id);
                   $profiles = $agencyCandidatesCount->Upload;
                   $interview_schedules = $agencyCandidatesCount->Interview;
                   $joined = $agencyCandidatesCount->Joined;
                 
                //    $profiles = DB::table('add_job_wise_condidates')->where('job_id','=',$row->job_id)->where('thirdparty_company_id','=',$row->to_company_id)->whereNotIn('master_status_id', [8, 7])->count();
                   echo  $profiles;
                @endphp
            </td>
            <td>
              @php
                //    $interview_schedules = DB::table('interview_schedules')->where('interview_job_id','=',$row->job_id)->where('company_id','=',$row->to_company_id)->whereNotNull('available_status')->count();
                   echo  $interview_schedules;
                @endphp
            </td>
            <td>
              @php
                //    $joined = DB::table('add_job_wise_condidates')->where('job_id','=',$row->job_id)->where('thirdparty_company_id','=',$row->to_company_id)->where('master_status_id','=',6)->count();
                   echo  $joined;
                @endphp
            </td>
            <td>
                @php
                $jobid = $row->job_id;
				echo '<div class="flex-column"> 
                <a href="javascript:void(0);" data-row-tabname="Assigned"  data-row-ename="remove"  data-row-jobid="' . $jobid . '" data-row-fromcompany="' . $row->from_company_id . '" data-row-tocompany="' . $row->to_company_id . '" class="btn btn-sm btn-light-danger w-130 remove_job">
                    Remove
                </a> 
                </div>';
                @endphp
            </td>
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="9" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
