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
        //   dd(count($row->industry));
        @endphp
        <tr style="height: 102px !important; position: relative;">
            <td>
               @php
                    $logo_path = '';//str_replace('index.php', '', url('img/client-default.png'));
                    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                        $logo_path = @$row->logo;
                    }

                    $percentage = 0;
                    $companies_id = '';
                    $companies_id = $row->id;

                    $title = $skills = $experience = $salary = $location = $jobtype = $jskillset = 0;

                   
                    $skillname = '';
                    $scnt = 0;
                    $tempArr = [];

                    $skilltag = '';
                    if (($row->matched_skill_count) > 0) {
                        foreach ($row->matched_skills as $sval) {
                            $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                        }
                    }
                    $percentage=0;
                    $matches = $row->matched_skill_count;
                    $totalElements = count($currentJobSkill);
                    // dd($totalElements);
                    $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
                    $matchPercentage = min($matchPercentage, 100);
                    $percentage = round($matchPercentage, 1);
                    

                   // number_format($row->skill_match_percentage, 0); //number_format($row->match_percentage, 0, '.', ''); //$salary + $title + $jobtype + $experience + $skills + $location;
                    $skillMatchSection = '';
                    if ($percentage > 0) {
                        $skillMatchSection =
                            '<div class="" style="position: absolute;bottom: 5px;left: 11px;white-space: nowrap;"><span>' .
                            $percentage .
                            '%' .
                            $skilltag .
                            '</span></div>';
                    }
                    if(is_iterable($row->industry) && count($row->industry)){
                        $ind_name = $row->industry['industry_name'];
                    }
					$str = trim(@$row->company_name);
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
                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                            <a href="javascript:void(0);" data-row-id="1210" class="show-application-detail">
                            <div class="symbol-label ' .
                                    @$bgTextDecore .
                                    ' border-radius-5">' .
                                    strtoupper(@$strVal) .
                                    '</div>
                            </a>
                            </div>';
                    }else{
                         echo ' <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style=" float:left;margin-left: 10px !important;"> 
								<a href="javascript:void(0);">
									<div class="symbol-label border-radius-5"> <img src="' .
                        $logo_path .
                        '" alt="Company Logo" class="border-radius-5 w-100"> </div>
								</a> 
							</div>';
                    }

					echo '<div class="d-flex flex-column" style=" float:left;"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        substr(ucwords(@$row->company_name), 0, 18) .
                        '</a>   
								<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        substr(ucwords(@$ind_name), 0, 22) .
                        '</a>
							</div>
							' .
                        $skillMatchSection .
                        '';

                @endphp
            </td>
            <td>
               @php
                    $companies_id = $row->id;
                   
                        // dd($companies_id,$alldatalist,$currentJobSkill);

                    $Profiles = 0; //0;
                    $benchCount = collect($row->candidates)->where('profile_type', 'bench_condidate')->count();
                    $otherCount = collect($row->candidates)->where('profile_type', 'other')->count();
                    $activeJobs = collect($row->jobs)->where('status', 'active')->count();
                    $titleffc = 'Profiles';
                    $SimilarReq = count($row->jobs);//0;
                    if ($hiringtype === '1') {
                        if ($benchCount > 0) {
                            $Profiles = $benchCount;
                            $SimilarReq = $activeJobs;
                            $titleffc = 'Bench';
                        } else {
                            $Profiles = $otherCount;
                            $SimilarReq = $activeJobs;
                            $titleffc = 'Profiles';
                        }
                    } elseif ($hiringtype === '0') {
                        $Profiles = $otherCount;
                        $SimilarReq = $activeJobs;
                        $titleffc = 'Profiles';
                    } else {
                        $Profiles = $benchCount + $otherCount;
                        $SimilarReq = $activeJobs;
                    }
                    echo ' <div class="d-flex flex-column"><span class="text-dark fw-bolder fs-6">' .
                        (($SimilarReq <5) ? 5: $SimilarReq) .
                        ' Jobs </span>
                    <span class="text-muted fw-bold text-muted d-block fs-7">' .
                        (($Profiles < 5) ? 5 : $Profiles) . ' ' . $titleffc .
                        ' </span> </div>';
                @endphp

            </td>
            <td>
                @php
                 if($jobMinExp>0){
					$jobMinExp = $jobMinExp-1;
				 }
				 if($jobMaxExp){
				   $jobMaxExp = $jobMaxExp+1;
				 }
				if($hiringtype==1){
                    $commtype = 1;
				}else{
					$commtype = 2; // full time
				}
				$rates  = '';
				$currency = GeneralMethod::currency();
				$salaryMode = GeneralMethod::salaryMode();
				$currencySymbole = GeneralMethod::currencySymbole();
				$minimumRate   =  DB::table('commericials')
					->join('commericial_details','commericial_details.commericial_id','=','commericials.id')
					->where('commericials.commtype','=', $commtype)
					->where('commericials.company_id','=',$row->id)
					->whereBetween('commericial_details.exp', [$jobMinExp, $jobMaxExp])
					->min('commericial_details.rate');

					$minimumRateType   =  DB::table('commericials')
					->select('commericials.*')
					->where('commericials.commtype','=',$commtype)
					->where('commericials.company_id','=',$row->id)
					->first();
                    // sal_mode /salary_currency

					if($minimumRateType){
						if($minimumRateType->commtype==1){
							$current_cur = $currencySymbole[@$minimumRateType->salary_currency];
							$current_mode = mb_substr($salaryMode[@$minimumRateType->sal_mode], 0, 1);


							$currencymain = GeneralMethod::currency();  // Currency code
							$currency  = $currencymain[@$jobSalaryCurrency];
							$econvertedAmount = $minimumRate;
						   if($minimumRateType->salary_currency > 0){
							  $econvertedAmount = GeneralMethod::convertToINR($minimumRate,$minimumRateType->salary_currency);
							}
							
							$convertedAmounEctc = GeneralMethod::conversionRates($currency,$econvertedAmount,$minimumRateType->sal_mode);
							$minimumRate = $convertedAmounEctc[@$jobSalaryMode];
							
							$current_cur = $currencySymbole[@$jobSalaryCurrency];
							$current_mode = $salaryMode[@$jobSalaryMode];

							$salary = GeneralMethod::thousandsCurrencyFormat($minimumRate);
							$rates = $current_cur.''.$salary.'/'.$current_mode;
							echo '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' . $current_cur . @$salary . '/' .substr($current_mode,0,1) . '</a>
								<span class="text-muted fw-bold text-muted d-block fs-7">Contract</span> </div>';
						}else{
							$rates = $minimumRate.'%';
							echo '<div class="d-flex flex-column">'.$rates.'</div>
							<span class="text-muted fw-bold text-muted d-block fs-7">full time</span> ';
						}
					}
                @endphp
            </td>
            <td>
                @php
                 $Sizecount = DB::table('users')->where('company_id', $row->id)->count();
			    	$regStatusCss = 'dark-text';
				// if (!empty($row->from_company_id)) {
				// 	if (($row->from_company_id == $company_id) && ($row->to_company_id == $row->id) && ($row->final_status == 1)) {
						$regStatus = 'Registered';

						$regStatusCss = 'text-success';
				// 	}
				// }
				
		       	echo '<div class="d-flex flex-column"> <span class="fw-bolder fs-6 '.$regStatusCss.'">'.$regStatus.'</span>
		       	<span class="text-muted fw-bold text-muted d-block fs-7">'.$Sizecount.' Member</span></div>';
                @endphp
            </td>
            <td>
                @php
                 $clients = DB::table('clients')->where('company_id','=',$row->id)->where('self_company','=',1)->first();
				$locInfo = '';
				if($clients){
					$locInfo = DB::table('client_location')
				   ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
				   ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
				   ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
				   ->select(
					   'client_location.id',
					   'client_location.city',
					   'client_location.address',
					   'client_location.postcode',
					   'client_location.country_id',
					   'city_list.city as city_name',
					   'state_list.state as state_name',
					   'countries.country_name'
				   )
				   ->where('client_id', $clients->id)
				   ->first(); 
				}
			  
		   
			   if (!empty($locInfo)) {
				   echo ' <div class="d-flex flex-column">
					   <span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="' . ucwords(@$locInfo->city_name) . '" >' . ucwords(mb_substr(@$locInfo->city_name, 0, 10)) . '</span>
					   <div class="d-flex align-items-center">
							 <span class="fw-bold text-muted d-block fs-7">' . ucwords(mb_substr(@$locInfo->state_name, 0, 10)) . '</span>
							   &nbsp;|&nbsp;
							 <span class="fw-bold text-muted d-block fs-7">' . strtoupper(mb_substr(@$locInfo->country_name, 0, 2)) . '</span>
						   <div>
						 </div>
					   </div>
					 </div>';
			   } 
                @endphp
            </td>
            <td>
                @php
                 echo ' <div class="d-flex flex-column">
					<span class="text-dark fw-bolder fs-6" data-toggle="tooltip4" title="" >' . date('d M y',strtotime(@$row->last_login)) . '</span>
					<div class="d-flex align-items-center">
						  <span class="fw-bold text-muted d-block fs-7">' . date('h:i A',strtotime(@$row->last_login)) . '</span>
						<div>
					  </div>
					</div>
				  </div>';
                @endphp
            </td>
            <td>
                @php
                 $from_company = $company_id = auth()->user()->company_id;
				$id = '';
				 $id = $row->id;
				// $id = $row->to_company_id;

				$html = '';
				$recommendateCounter = DB::table('recommendation_relation')
					->select('recommendation_relation.id')
					->where('recommendation_relation.from_company_id', $from_company)
					->where('recommendation_relation.to_company_id', $id)
					->where('recommendation_relation.job_id', $jobId)
					//->where('recommendation_relation.final_status', '=', 1)
					->count();

				if ($recommendateCounter == 1) {

					$recommendateRow = DB::table('recommendation_relation')
						->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
						->where('recommendation_relation.from_company_id', $from_company)
						->where('recommendation_relation.to_company_id', $id)
						->where('recommendation_relation.job_id', $jobId)
						//->where('recommendation_relation.final_status', '=', 1)
						->first();


					if (($recommendateRow->final_status == 0) || ($recommendateRow->final_status == '')) {
						$html = '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-success w-130">Request Sent</a> </div>';
					} else if ($recommendateRow->final_status == -1) {
						$html = '<a href="javascript:void(0);" data-row-id="' . $id . '"  data-matchcount="0" data-row-jobid="' . @$jobId . '" data-row-ename="ignore" class="btn btn-sm btn-light-primary w-130 resend_job_recommanded_request" style="width: 135px !important;">Send Requirement</a>';
					} else if ($recommendateRow->final_status == 2) {
						$html = '<a href="javascript:void(0);" data-row-id="' . $id . '"  data-matchcount="0" data-row-jobid="' . @$jobId . '" data-row-ename="remove" class="btn btn-sm btn-light-primary w-130 resend_job_recommanded_request" style="width: 135px !important;">Send Requirement</a>';
					} else {
						$html = '<div class="d-flex flex-column"> 
						<a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="' . $jobId . '" data-row-fromcompany="' . $company_id . '" data-row-tocompany="' . $row->id . '" class="btn btn-sm btn-light-danger w-130 remove_job">
						Remove
						</a>
						</div>';
					}

				} else {
					$html = '<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" id="sp_list_'.$id.'" data-row-jobid="' . $jobId . '" data-row-fromcompany="' . $from_company . '" data-row-tocompany="' . $id . '" class="btn btn-sm btn-light-primary w-130  send_only_job_invitation" style="width: 135px !important;">
					Send Requirement
					</a> 
					</div>';

				}


				echo $html;
                @endphp
            </td>

        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="7" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
