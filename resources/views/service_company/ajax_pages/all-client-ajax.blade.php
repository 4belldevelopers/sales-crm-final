@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

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
    $relationget =  GeneralMethod::getRelationData($company_id, $row->company_id);
    // dd( $relationget->from_company_id);
    // $relations = collect($row->staffing_company_relation)->flatten(1);
    // $match = $relations->first(function ($item) use ($company_id) {
    //     return ($item['from_company_id'] ?? null) == $company_id
    //         || ($item['to_company_id'] ?? null) == $company_id;
    // });
    
    $from_company_id = $relationget->from_company_id ?? null;
    // $from_company_id = ($match['from_company_id'] ?? null) == $company_id 
    //     ? $match['from_company_id'] 
    //     : null;
    $recommendation_id = $relationget->id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    // $recommendation_id = ($match['to_company_id'] ?? null) == $company_id 
    //     ? $match['to_company_id'] 
    //     : null;

    $final_status = $relationget->final_status ?? null;
    // $final_status = $relations->first(function ($item) use ($company_id) {
    //     return ($item['to_company_id'] ?? null) == $company_id;
    // })['final_status'] ?? null;

    $from_created_at = $relationget->from_created_at ?? null;
    // $from_created_at = $relations->first(function ($item) use ($company_id) {
    //     return ($item['to_company_id'] ?? null) == $company_id;
    // })['from_created_at'] ?? null;

    $row->final_status = $final_status;
    $row->staffing_company_relation_id =$recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->from_created_at = $from_created_at;

    $background_image = '';
    
    if ($company_id != $row->company_id) {
            if (!empty(@$row->website) && empty(@$row->logo)){
                 $domainGet = GeneralMethod::getDomain($row->website);
                 $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
                 $google = GeneralMethod::getDomainIcon($googleIcon,$row->id,true);
                 $background_image  = ($google) ? $googleIcon : '';
            }
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $background_image = @$row->logo;
            }

        }else{
            if (!empty(@$row->website) && empty(@$row->logo_logical_path)){
                 $domainGet = GeneralMethod::getDomain($row->website);
                 $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
                 $google = GeneralMethod::getDomainIcon($googleIcon,$row->id,false);
                 $background_image  = ($google) ? $googleIcon : '';
            }
            if (!empty(@$row->logo_logical_path) && strlen(@$row->logo_logical_path) > 10) {
                $background_image = @$row->logo_logical_path;
            }

        }

    @endphp



        <tr style="">
            <td>
                @php
                    echo '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1">
                  </div>';

                @endphp
            </td>
            <td>
                @php
                    $image = '';
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

                    $client_type_css = 'show-detail';

                    if ($company_id == $row->clients_company_id) {
                        $image =
                            '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="' .
                            $row->client_id .
                            '" class="' .
                            $client_type_css .
                            ' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 ' .
                            @$bgTextDecore .
                            '  border-radius-5">' .
                            ucwords(@$strVal) .
                            '</div>
														</a>
													</div>';

                        
                        if (!empty($background_image) && strlen($background_image) > 10) {
                            $image =
                                '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 " style="float:left" >
														<img src="' .
                                @$background_image .
                                '" height="100px" width="100px" data-row-id="' .
                                $row->client_id .
                                '" class="' .
                                $client_type_css .
                                ' border-radius-5   tttt"  />
													</div>';
                        }
                    } elseif ($row->staffing_company_relation_id > 0) {
                        $client_type_css = 'show-detail-direct';
                        $logo_path = ''; //url('img/client-default.png');
                        if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                            $logo_path = @$row->logo;
                        }
                        if (!empty($logo_path)) {
                            $image =
                                '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 ' .
                                $client_type_css .
                                '" style="float:left"> 
												<img src="' .
                                $logo_path .
                                '" height="100px" width="100px" class="' .
                                $client_type_css .
                                ' border-radius-5"> 
										   </div>';
                        } else {
                            $image =
                                '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
														<a href="javascript:void(0);" data-row-id="' .
                                $row->client_id .
                                '" class="' .
                                $client_type_css .
                                ' border-radius-5  ssss" id="kt_activities3_toggle" >
															<div class="symbol-label fs-3 ' .
                                @$bgTextDecore .
                                '  border-radius-5">' .
                                ucwords(@$strVal) .
                                '</div>
														</a>
													</div>';
                        }
                    }

                    // $departmentArr = '------';
                    // if (@$row->ind_name) {
                       $departmentArr = (@$row->ind_name) ? @$row->ind_name : $row->main_ind_name;
                    // }

                    $client_Or_CompanyName = $row->name;

                    if ($company_id != $row->company_id) {
                        $client_Or_CompanyName = $row->company_name;
                    }

                    echo $image .
                        '		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->client_id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                        $client_type_css .
                        '"  data-toggle="tooltip1" data-html="true" title="' .
                        ucwords(@$client_Or_CompanyName) .
                        '">' .
                        ucwords(mb_substr(@$client_Or_CompanyName, 0, 20)) .
                        '</a>
                    <a href="javascript:void(0);" data-row-id="' .
                        $row->client_id .
                        '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary ' .
                        $client_type_css .
                        '">' .
                        @$departmentArr .
                        '</a>
                  </div>';
                @endphp
            </td>
            <td>
                @php
                    $client_id = $row->client_id;
                    $country_name = '';
                    $stateName = '';
                    $city = '';
                    if(count($row->client_location)>0){
                            
                        $country_name = ' | '.($row->client_location[0]['country_name']);
                        $stateName = ($row->client_location[0]['state_name']);
                        $city = ($row->client_location[0]['city_name']);
                        
                    }

                    // $address			=	@$locInfo->address;

                    echo '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        ucwords($city) .
                        '</a>
                        <div class="d-flex align-items-center">
                            <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        ucwords(@$stateName ).
                        '</span> &nbsp;
                            <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        ucwords($country_name ).
                        '</span>
                        <div>';
                @endphp
            </td>
            <td>
                @php

                    $currentCompanyId = $company_id;
                    $clientCompanyId = $row->from_company_id;

                    //  dd($from_company_id,$to_company_id);

                    if ($company_id != $row->company_id) {
                        $from_company_id = $row->from_company_id;
                        $to_company_id = $row->to_company_id;
                        $client_id = '';
                        $client_id = $row->client_id;

                        // $applicationCounter = DB::table('recommendation_relation')
                        //     ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                        //     ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', '=', 'jobs.id')
                        //     ->select(
                        //         DB::raw('COUNT(add_job_wise_condidates.job_id) AS job_id_count'),
                        //         DB::raw('COUNT(DISTINCT add_job_wise_condidates.job_id) AS unique_job_id_count'),
                        //         DB::raw(
                        //             'COUNT(add_job_wise_condidates.job_application_id) AS job_application_id_count'),
                        //         DB::raw(
                        //             'COUNT(CASE WHEN add_job_wise_condidates.master_status_id != 8 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS allCandidate'),
                        //         DB::raw(
                        //             'COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS appJoin'),
                        //     )
                        //     ->where('recommendation_relation.from_company_id', '=', $from_company_id)
                        //     ->where('recommendation_relation.to_company_id', '=', $to_company_id)
                        //     ->where('add_job_wise_condidates.thirdparty_company_id', '=', $to_company_id)
                        //     ->where('recommendation_relation.final_status', '=', 1)
                        //     ->first();

                            $ClientCandidatesCount = GeneralMethod::DirectClientCandidatesCount($from_company_id,$to_company_id);
                            $allCandidate = ($ClientCandidatesCount->all_candidates);
                            $appJoin = ($ClientCandidatesCount->Joined);


                    } else {
                        $client_id = '';
                        $client_id = $row->client_id;

                        // $applicationCounter = DB::table('jobs')
                        //     // ->join('job_applications', 'job_applications.job_id', '=', 'jobs.id')
                        //     ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', 'jobs.id')
                        //     ->select(
                        //         DB::raw('count(add_job_wise_condidates.id) as allCandidate'),
                        //         DB::raw(
                        //             'SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6  THEN 1 ELSE 0 END) AS appJoin'),
                        //     )
                        //     ->where('jobs.client_id', $client_id)
                        //     ->first();

                            $ClientCandidatesCount = GeneralMethod::InternalClientCandidatesCount($client_id);
                            $allCandidate = ($ClientCandidatesCount->all_candidates);
                            $appJoin = ($ClientCandidatesCount->Joined);

                    }

                    echo '<div class="d-flex flex-column">
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        number_format(@$allCandidate) .
                        ' Profiles</a>                    
					<span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        number_format(@$appJoin) .
                        ' Joins</span>
				</div>';

                @endphp
            </td>
            <td>
                @php

                    $client_id = $clientCompanyId = '';
                    $active_counter = $inactive_counter = $hold_counter = 0;
                    $currentCompanyId = $company_id;
                    $clientCompanyId = $row->from_company_id;

                    if ($company_id == $row->clients_company_id) {
                        // $jobCounter = DB::table('jobs')
                        //     ->select(
                        //         DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"),
                        //         DB::raw("SUM(CASE WHEN jobs.status = 'inactive' THEN 1 ELSE 0 END) AS inactive"),
                        //         DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS hold"),
                        //     )
                        //     ->where('jobs.company_id', $company_id)
                        //     ->where('jobs.client_id', $row->client_id)
                        //     ->first();
                             $jobsCount = GeneralMethod::InternalClientJobsCount($company_id,$row->client_id);
                           $active_counter = ($jobsCount->all_jobs);

                    } else {
                        $from_company_id = $row->from_company_id;
                        $to_company_id = $row->to_company_id;
                        // $jobCounter = DB::table('recommendation_relation')
                        //     ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                        //     ->select(
                        //         DB::raw("SUM(CASE WHEN jobs.status = 'active'  THEN 1 ELSE 0 END) AS active"),
                        //         DB::raw("SUM(CASE WHEN jobs.status = 'hold'  THEN 1 ELSE 0 END) AS hold"),
                        //         DB::raw("SUM(CASE WHEN jobs.status = 'inactive'  THEN 1 ELSE 0 END) AS inactive"),
                        //     )
                        //     ->where('recommendation_relation.from_company_id', '=', $from_company_id)
                        //     ->where('recommendation_relation.to_company_id', '=', $to_company_id)
                        //     ->where('recommendation_relation.final_status', '=', 1)
                        //     ->first();

                            // dd( $jobCounter,$company_id, $row->company_id,$relationget);
                            $jobsCount = GeneralMethod::DirectClientJobsCount($from_company_id,$to_company_id);
                           $active_counter = ($jobsCount->all_jobs);
                    }
                    


                    // $active_counter = $jobCounter->active ? $jobCounter->active : 0;
                    $inactive_counter = 0;
                    $hold_counter = 0;

                    echo '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">(' .
                        $active_counter .
                        ') Active</span>
                      <div class="d-flex align-items-center d-none">
                        <span class="fw-bold d-block fs-7 text-warning">' .
                        $hold_counter .
                        ' Hold</span>
                          &nbsp;|&nbsp;
                        <span class="fw-bold d-block fs-7 text-danger">' .
                        $inactive_counter .
                        ' Closed</span>
                      <div>
                    </div>
                </div></div>';
                @endphp
            </td>
            <td>
                @php

                    $daysAgo = '';

                    if ($row->staffing_company_relation_id > 0 && $row->to_company_id == auth()->user()->company_id) {
                        $created_at = $row->from_created_at;
                        $newDate = date('d M, Y', strtotime($created_at));
                        $daysAgo = GeneralMethod::timeAgo($row->from_created_at);
                    } else {
                        $created_at = $row->created_at;
                        $newDate = date('d M, Y', strtotime($created_at));
                        $daysAgo = GeneralMethod::timeAgo($row->created_at);
                    }
                    echo '<div class="d-flex flex-column">
                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$newDate .
                        '</a>
                    <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$daysAgo .
                        '</a>
                  </div>';
                @endphp

            </td>
            <td>
                @php
                    $client_type =  __('messages.internal_client_tag');
                    $company_name = '------------';

                    if ($company_id != $row->company_id) {
                        $client_type = __('messages.direct_clinet_tag');
                    }

                    $company_type = @$row->company_type;
                    if ($company_type >= 1) {
                        $company_name = ''; //$accountCompanyType[$company_type];
                    }

                    echo '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                        $client_type .
                        '</a>
						<span class="text-muted fw-bold text-muted d-block fs-7">' .
                        $company_name .
                        '</span>
					</div>';
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
