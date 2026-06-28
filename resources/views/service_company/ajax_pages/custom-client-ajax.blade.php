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
    
    $targetJobId = $row->id;
    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;

    $match = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
        return ($item['from_company_id'] ?? null) == $company_id
            || ($item['to_company_id'] ?? null) == $company_id;
    });
    $from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
    $recommendation_id   = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

    $final_status = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
        return $item['to_company_id'] == $company_id;
    })['final_status'] ?? null;

    $from_created_at = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
        return $item['to_company_id'] == $company_id;
    })['from_created_at'] ?? null;

    $row->final_status = $final_status;
    $row->staffing_company_relation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->from_created_at = $from_created_at;


    $background_image = '';
    

    if (!empty(@$row->website) && empty(@$row->logo_logical_path)){
            $domainGet = GeneralMethod::getDomain($row->website);
            $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
            $google = GeneralMethod::getDomainIcon($googleIcon,$row->id,false);
            $background_image  = ($google) ? $googleIcon : '';
    }
    if (!empty(@$row->logo_logical_path) && strlen(@$row->logo_logical_path) > 10) {
        $background_image = @$row->logo_logical_path;
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

                    $image =
                        '<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="show-detail border-radius-5" id="kt_activities3_toggle" >
												<div class="symbol-label fs-3 ' .
                        @$bgTextDecore .
                        '  border-radius-5">' .
                        ucwords(@$strVal) .
                        '</div>
											</a>';
                    if (!empty(@$background_image) && strlen(@$background_image) > 10) {
                        $image =
                            '<img   data-row-id="' .
                            $row->id .
                            '"    src="' .
                            @$background_image .
                            '" height="100px" width="100px" class="show-detail border-radius-5"  />';
                    }

                    $departmentArr = '------';
                    if ($row->industry >= 0) {
                        $departmentArr = $department[@$row->industry];
                    }

                    echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
										' .
                        $image .
                        '		 
										</div>                 
								<div class="d-flex flex-column">
									<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail"  data-toggle="tooltip1" data-html="true" title="' .
                        ucwords(@$row->name) .
                        '">' .
                        ucwords(mb_substr(@$row->name, 0, 15)) .
                        '</a>
									<a href="javascript:void(0);" data-row-id="' .
                        $row->id .
                        '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail">' .
                        @$departmentArr .
                        '</a>
								</div>
								';
                @endphp
            </td>
            <td>
                @php
                    $client_id = $row->id;
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
                        ucwords($city ).
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
                    $client_id = '';
                    $client_id = $row->id;
                    $applicationCounter = DB::table('jobs')
                        // ->join('job_applications', 'job_applications.job_id', '=', 'jobs.id')
                        ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', 'jobs.id')
                        ->select(
                            DB::raw('count(add_job_wise_condidates.id) as allCandidate'),
                            DB::raw('SUM(CASE WHEN add_job_wise_condidates.master_status_id = 6  THEN 1 ELSE 0 END) AS appJoin'),
                        )
                        ->where('jobs.client_id', $client_id)
                        ->first();

                    echo '<div class="d-flex flex-column">
							<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        number_format(@$applicationCounter->allCandidate) .
                        ' Profiles</a>                    
							<span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        number_format(@$applicationCounter->appJoin) .
                        ' Joins</span>
						</div>';

                @endphp
            </td>
            <td>
                @php
                    $client_id = '';
                    $client_id = $row->id;
                    $jobCounter = DB::table('jobs')
                        ->select(
                            DB::raw("SUM(CASE WHEN jobs.status = 'active' THEN 1 ELSE 0 END) AS active"),
                            DB::raw("SUM(CASE WHEN jobs.status = 'inactive' THEN 1 ELSE 0 END) AS inactive"),
                            DB::raw("SUM(CASE WHEN jobs.status = 'hold' THEN 1 ELSE 0 END) AS hold"),
                        )
                        ->where('client_id', $client_id)
                        ->first();

                    $active_counter = $inactive_counter = $hold_counter = 0;

                    $active_counter = $jobCounter->active;
                    if (empty($active_counter)) {
                        $active_counter = 0;
                    }

                    $inactive_counter = $jobCounter->inactive;
                    if (empty($inactive_counter)) {
                        $inactive_counter = 0;
                    }

                    $hold_counter = $jobCounter->hold;
                    if (empty($hold_counter)) {
                        $hold_counter = 0;
                    }
                    echo '<div class="d-flex flex-column">
                      <span class="fw-bolder text-success fs-6">(' .
                        @$active_counter .
                        ') Active</span>
                      <div class="d-flex align-items-center d-none">
                        <span class="fw-bold d-block fs-7 text-warning">' .
                        @$hold_counter .
                        ' Hold</span>
                          &nbsp;|&nbsp;
                        <span class="fw-bold d-block fs-7 text-danger">' .
                        @$inactive_counter .
                        ' Closed</span>
                      <div>
                    </div>
                </div></div>';

                @endphp
            </td>
            <td>
                @php

                    $daysAgo = $created_at='';
                    $from_created_at = $row->created_at;
                    $created_at = date('d M, Y', strtotime($from_created_at));
                    $daysAgo = GeneralMethod::timeAgo($created_at);
                    // if($row->created_at!= '0000-00-00 00:00:00' &&!empty($row->created_at)){
                    //     $created_at = $row->created_at->format('d M, Y');
                    //     $daysAgo = GeneralMethod::timeAgo($row->created_at);
                    //   }
                    

                    echo '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        @$created_at .
                        '</a>
								<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$daysAgo .
                        '</a>
							  </div>';
                @endphp
            </td>
            <td>
                @php
                    echo '<div class="d-flex flex-column">
								<a href="javascript:void(0);" class="text-dark fw-bolder fs-6">'.__('messages.internal_client_tag').'</a>
								<span class="text-muted fw-bold text-muted d-block fs-7"></span>
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
