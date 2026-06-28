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

    $from_company_id = $relationget->from_company_id;
    $recommendation_id = $relationget->id;
    $to_company_id = $relationget->to_company_id;
    $final_status = $relationget->final_status;
    $from_created_at = $relationget->from_created_at;

    $row->final_status = $final_status;
    $row->staffing_company_relation_id =$recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->from_created_at = $from_created_at;

    
    $logo_path = url('img/client-default.png');
    if (!empty(@$row->website) && empty(@$row->logo)){
            $domainGet = GeneralMethod::getDomain($row->website);
            $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
            $google = GeneralMethod::getDomainIcon($googleIcon,$row->id,true);
            $logo_path  = ($google) ? $googleIcon : '';
    }
    if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
        $logo_path = @$row->logo;
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

                    $company_name = '';
                    $company_name = ucwords($row->company_name);                    

                    $ind_name = '';
                    $ind_name = (@$row->ind_name) ? @$row->ind_name : $row->main_ind_name;

                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 show-detail-direct" data-row-id="' .
                        $row->client_id .
                        '"  style="float:left"> 
                                <img src="' .
                        $logo_path .
                        '" height="100px" width="100px" class="border-radius-5"> 
                            </div>
                        <div class="d-flex flex-column"> 
                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-direct" data-row-id="' .
                        $row->client_id .
                        '"  data-toggle="tooltip1" data-html="true" title="" data-bs-original-title="' .
                        $company_name .
                        '">' .
                        $company_name .
                        '</a> 
                            <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-direct"   data-row-id="' .
                        $row->client_id .
                        '"   >' .
                        $ind_name .
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
                    ucwords(@$stateName) .
                    '</span> &nbsp;
                        <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                    ucwords($country_name) .
                    '</span>
                    <div>';
            @endphp
            </td>
            <td>
                @php

                    if ($company_id != $row->company_id) {
                        $from_company_id = $row->company_id; //from_company_id
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
                        //     // ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', 'jobs.id')
                        //     ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', 'jobs.id')
                        //     ->select(
                        //         DB::raw("count(->where('jobs.client_id', $client_id).id) as allCandidate"),
                        //         DB::raw(
                        //             "SUM(CASE WHEN ->where('jobs.client_id', $client_id).master_status_id = 6  THEN 1 ELSE 0 END) AS appJoin"),
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

                    $from_created_at = $row->from_created_at;
                    $newDate = date('d M, Y', strtotime($from_created_at));

                    $timeAgo = GeneralMethod::timeAgo($from_created_at);
                    echo '<div class="d-flex flex-column"> 
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        $newDate .
                        '</a> 
								<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        $timeAgo .
                        '</a> 
							</div>';
                @endphp
            </td>
            <td>
                @php
                    $account_company_type = '';
                    $company_type = $row->company_type;
                    if ($company_type > 0) {
                        $account_company_type = ''; //$accountCompanyType[$company_type];
                    }
                    echo '<div class="d-flex flex-column"> 
									<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.__('messages.direct_clinet_tag').'</a> 
									<span class="text-muted fw-bold text-muted d-block fs-7">' .
                        $account_company_type .
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
