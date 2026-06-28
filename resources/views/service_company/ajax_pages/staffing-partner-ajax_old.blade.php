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
    $relationget =  GeneralMethod::getRelationData($row->company_id,$company_id);

    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;
    $to_created_at = $relationget->to_created_at ?? null;
    $created_at = $relationget->created_at ?? null;

    
    // $match = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
    //     return ($item['from_company_id'] ?? null) == $company_id
    //         || ($item['to_company_id'] ?? null) == $company_id;
    // });

    // $from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
    // $recommendation_id   = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

    // $final_status = collect($row->staffing_company_relation)->first(function ($item) use ($company_id) {
    //     return $item['from_company_id'] == $company_id;
    // })['final_status'] ?? null;


    // $to_created_at = collect($row->staffing_company)->first(function ($item) use ($company_id) {
    //     return $item['from_company_id'] == $company_id;
    // })['to_created_at'] ?? null;

    // $created_at = collect($row->staffing_company)->first(function ($item) use ($company_id) {
    //     return $item['from_company_id'] == $company_id;
    // })['created_at'] ?? null;


    $row->final_status = $final_status;
    $row->staffing_company_relation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->to_created_at = ($to_created_at) ? $to_created_at : $created_at;
    $td_industries = DB::table('td_industries')->where('ind_id','=',$row->industries)->first();

    $logo_path = ''; //url('img/client-default.png');


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
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                    if ($row->id > 0) {
                        echo '<div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" value="1" /></div>';
                    }
                @endphp
            </td>
            <td>
                @php
                    if ($row->id > 0) {
                        $company_name = $ind_name = '';
                        $company_name = ucwords($row->company_name);

                        if($td_industries){
                            $ind_name = $td_industries->ind_name;
                        }
                        $client_type_css = 'show-detail-direct';
                        
                        $bgTextDecore = GeneralMethod::icoinText($row->company_name);
                        if (!empty($logo_path)) {
                            $image =
                                '<div  class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> 
                    <img data-row-id="' .$row->client_id .'" src="' .
                                $logo_path .
                                '" height="100px" width="100px" class="' .
                                $client_type_css .
                                ' border-radius-5"> 
               </div>';
                        } else {
                            $image =
                                '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5 ' .
                                $client_type_css .
                                '" style="float:left"> 
                    <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' .
                                ucwords($row->company_name) .
                                '" >
                        <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                        <a href="javascript:void(0);" data-row-id="' .$row->client_id .'" class="show-detail border-radius-100" id="kt_activities3_toggle">
                            <div class="symbol-label fs-2 ' .
                                $bgTextDecore .
                                ' border-radius-100">' .
                                mb_substr(ucwords($row->company_name), 0, 1) .
                                '</div>
                        </a>		 
                        </div>
                        </div>
               </div>';
                        }

                        echo $image .
                            '		              
                  <div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' .
                            $row->client_id .
                            '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                            $client_type_css .
                            '"  data-toggle="tooltip1" data-html="true" title="' .
                            ucwords(@$row->company_name) .
                            '">' .
                            ucwords(mb_substr(@$row->company_name, 0, 20)) .
                            '</a>
                    <a href="javascript:void(0);" data-row-id="' .
                            $row->client_id .
                            '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary ' .
                            $client_type_css .
                            '">' .
                            @$ind_name .
                            '</a>
                  </div>
                  ';
                    }
                @endphp
            </td>
            <td>
                @php

                    $locationHtml = $locationHtml = $country_name = $address = '';

                    $client_id = $row->client_id;
                    $locInfo = DB::table('client_location')
                        ->select(
                            'client_location.id',
                            'client_location.city',
                            'client_location.address',
                            'client_location.postcode',
                            'client_location.country_id',
                            'city_list.city as city_name',
                            'state_list.state',
                        )
                        ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                        ->leftJoin('state_list', 'state_list.id', '=', 'client_location.state')
                        ->where('client_id', $client_id)
                        ->first();
                    $country_name = '';
                    $locInfostate = '';
                    if($locInfo){
                        if (@$locInfo->country_id > 0) {
                            $countryArr = GeneralMethod::getCountryName($locInfo->country_id);
                            if (is_array($countryArr)) {
                                if (count($countryArr) > 0) {
                                    foreach ($countryArr as $cval) {
                                        $country_name = ucwords($cval['name']);
                                    }

                                    if ($locInfo->state) {
                                        $locInfostate = $locInfo->state;
                                    }
                                }
                            }
                        }
                    }

                    $city = @$locInfo->city;
                    // if (@$locInfo->country_id == 101) {
                        $city = @$locInfo->city_name;
                    // }
                    $locationHtml = ucwords(mb_substr(@$city, 0, 10)) . '&nbsp;' . @$country_name;
                    $address = ''; //@$locInfo->address;

                    echo '<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" " data-toggle="tooltip" title="' .
                        ucwords(@$address) .
                        '">' .
                        ucwords(mb_substr($city, 0, 30)) .
                        '</a><div class="d-flex align-items-center"><span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        @$locInfostate .
                        '</span> &nbsp;|&nbsp; <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        $country_name .
                        '</span><div>';
                @endphp
            </td>
            <td>
                @php

                    $requisitionRow = DB::table('recommendation_relation')
                        ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                        ->join('add_job_wise_condidates', 'add_job_wise_condidates.job_id', '=', 'jobs.id')
                        ->select(
                            DB::raw('COUNT(add_job_wise_condidates.job_id) AS job_id_count'),
                            DB::raw('COUNT(DISTINCT add_job_wise_condidates.job_id) AS unique_job_id_count'),
                            DB::raw('COUNT(add_job_wise_condidates.job_application_id) AS job_application_id_count'),
                            DB::raw(
                                'COUNT(CASE WHEN add_job_wise_condidates.master_status_id != 8 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS all_application'),
                            DB::raw(
                                'COUNT(CASE WHEN add_job_wise_condidates.master_status_id = 6 THEN add_job_wise_condidates.job_application_id ELSE NULL END) AS join_application'),
                        )
                        ->where('recommendation_relation.from_company_id', '=', $company_id)
                        ->where('recommendation_relation.to_company_id', '=', $row->company_id)
                        ->where('add_job_wise_condidates.thirdparty_company_id', '=', $row->company_id)
                        ->where('recommendation_relation.final_status', '=', 1)
                        ->first();
                    // dd($requisitionRow);

                    echo '<div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                        $requisitionRow->all_application .
                        ' Profiles</a> <span class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                        $requisitionRow->join_application .
                        ' Joins </span> </div>';
                @endphp

            </td>
            <td>
                @php
                    if ($row->id > 0) {
                        // dd($company_id,$row->id);
                        $requisitionRow = '';
                        $requisitionRow = DB::table('recommendation_relation')
                            ->join('jobs', 'jobs.id', '=', 'recommendation_relation.job_id')
                            ->select(
                                DB::raw("SUM(CASE WHEN jobs.status = 'active'  THEN 1 ELSE 0 END) AS active"),
                                DB::raw("SUM(CASE WHEN jobs.status = 'hold'  THEN 1 ELSE 0 END) AS hold"),
                                DB::raw("SUM(CASE WHEN jobs.status = 'inactive'  THEN 1 ELSE 0 END) AS inactive"),
                            )
                            ->where('recommendation_relation.from_company_id', '=', $company_id)
                            ->where('recommendation_relation.to_company_id', '=', $row->company_id)
                            ->where('recommendation_relation.final_status', '=', 1)
                            ->first();

                        $Active = $Hold = $Closed = 0;
                        if ($requisitionRow->active > 0) {
                            $Active = $requisitionRow->active;
                        }
                        if ($requisitionRow->hold > 0) {
                            $Hold = $requisitionRow->hold;
                        }
                        if ($requisitionRow->inactive > 0) {
                            $Closed = $requisitionRow->inactive;
                        }

                        echo '<div class="d-flex flex-column">
											<span class="fw-bolder text-success fs-6">(' .
                            $Active .
                            ') Active</span>
											<!--<div class="d-flex align-items-center">
												<span class="fw-bold d-block fs-7 text-warning">(' .
                            $Hold .
                            ') Hold</span> &nbsp;|&nbsp; 
												<span class="fw-bold d-block fs-7 text-danger">(' .
                            $Closed .
                            ') Closed</span>
											<div>--> 
										</div>';
                    }
                @endphp
            </td>
            <td>
                @php

                    if ($row->id > 0) {
                        $newDate = $timeAgo = '';
                        $to_created_at = $row->to_created_at;
                        $newDate = date('d M, Y', strtotime($to_created_at));
                        $timeAgo = GeneralMethod::timeAgo($to_created_at);
                        echo '<div class="d-flex flex-column"> 
					<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                            $newDate .
                            '</a> 
					<a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">' .
                            $timeAgo .
                            '</a> 
					</div>';
                    }

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
