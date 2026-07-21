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
    $targetJobId = $row->id;
    $company_id = auth()->user()->company_id;

    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    $from_company_id = $relationget->from_company_id ?? null;
    $to_company_id = $relationget->to_company_id ?? null;
    $recommendation_id = $relationget->id ?? null;
    $final_status = $relationget->final_status ?? null;

    // $match = collect($row->recommendation_relation)->first(function ($item) use ($company_id) {
    //     return ($item['from_company_id'] ?? null) == $company_id
    //         || ($item['to_company_id'] ?? null) == $company_id;
    // });
    // $from_company_id = ($match['from_company_id'] ?? null) == $company_id ? $match['from_company_id'] : null;
    // $recommendation_id   = ($match['to_company_id'] ?? null) == $company_id ? $match['to_company_id'] : null;

    // $final_status = collect($row->recommendation_relation)->first(function ($item) use ($targetJobId) {
    //     return $item['job_id'] == $targetJobId;
    // })['final_status'] ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->recommendation_relation_id =$recommendation_id;
    $row->to_company_id = $to_company_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId;
    $row->clients_name = $row->client_name;




    $bgTextDecore = $strVal = '';
        $str = trim(@$row->title);
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
        
        $country_id = $country_name = '';
        $jobid = $row->id;
                    
        $client_city_jobs = $row->client_city_jobs;
        $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;

        $employment_type = 'Full-time';
        if ($row->employment_type == 1) {
            $employment_type = 'Contract';
        }
        
        $classAdd = "bench-match";
        $textAdd = "Bench Required";
        $textClassAdd = "bench-badge";
        // if($row->public_market_status==2){
        //     $classAdd = "market-match";
        //     $textAdd = "Marketplace";
        //     $textClassAdd = "marketplace-badge";
        // }

        $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';
                    // $sal_mode = ($row->min_sal_mode) ? $row->min_sal_mode : 0;
                    
        $mod = $sal_mode ?? 0;

        $cur = $min_salary_currency ?? 0;
        

        $min_sal_mode = $salaryMode[$mod];
        $max_sal_mode = $salaryMode[$mod];
        $salary_currency = $currencySymbole[@$cur];
        $currencymain = GeneralMethod::currency(); // Currency code
        $currency = $currencymain[$cur];
        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->max_yearly, 0);
        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->min_yearly, 0);

        $ectc = $convertedAmounEctc[$mod];
        $ctc = $convertedAmounCtc[$mod];

        $max_salary = '';
        $max_salary = GeneralMethod::thousandsCurrencyFormat($ectc);
        $min_salary = '';
        $min_salary = GeneralMethod::thousandsCurrencyFormat($ctc);

        $work_type = 'Work From Office';
        if($row->work_type=='no'){
            $work_type = "Work From Office";
        }elseif($row->work_type=='yes'){
            $work_type = "Remote";
        }elseif($row->work_type=='other'){
            $work_type = "Hybrid";
        }


    @endphp
      
                <div class="agency-card {{ $classAdd }}">
                    <div class="{{ $textClassAdd }}"> {{ $textAdd }}</div>
                    <div class="card-content">
                        
                        <div class="card-left">
                            <div class="agency-logo client-show-detail {{ $bgTextDecore }}" data-row-id="{{ $row->id }}" style="cursor: pointer;">{{ ucwords($strVal) }}</div>
                            <div class="agency-details" style="position: relative;">
                                <h3 class="agency-name client-show-detail text-dark fw-bolder text-hover-primary fs-5" data-row-id="{{ $row->id }}"><a href="javascript::void(0);" style="cursor: pointer;">{{ ucwords(mb_substr(@$row->title, 0, 30)) }}</a> <i class="ri-verified-badge-fill color-blue"></i></h3>
                                <p class="agency-location text-muted fw-bold d-block fs-6 text-hover-primary">
                                    @php

                                        if ($row->work_type == 'yes') {
                                            echo ' <i class="fas fa-map-marker-alt"></i> Remote |  '.ucwords(mb_substr(@$employment_type, 0, 10)).'';
                                        } elseif ($row->work_type == 'no' || $row->work_type =='other') {
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {

                                                $firstCity = collect($client_city_jobs)->first();
                                            
                                                $cityName = $firstCity['city_name'] ?? null;
                                                $city = mb_substr(@$cityName, 0, 21);
                                                $countryName = $firstCity['country_name'] ?? null;
                                                $country_name = mb_substr(@$countryName, 0, 6);

                                                echo '<i class="fas fa-map-marker-alt"></i>  '.ucwords(mb_substr(@$city, 0, 10)) .' | ' . ucwords(mb_substr(@$employment_type, 0, 10)) .'';
                                            } else {
                                            echo '<i class="fas fa-map-marker-alt"></i> Multiple | ' .ucwords(mb_substr(@$employment_type, 0, 10)) .'';
                                            }
                                        } elseif ($row->work_type == 'any') {
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {

                                                $firstCity = collect($client_city_jobs)->first();
                                            
                                                $cityName = $firstCity['city_name'] ?? null;
                                                $city = mb_substr(@$cityName, 0, 21);
                                                $countryName = $firstCity['country_name'] ?? null;
                                                $country_name = mb_substr(@$countryName, 0, 6);

                                                echo '<i class="fas fa-map-marker-alt"></i> '.ucwords(mb_substr(@$city, 0, 10)) .' | ' .
                                                    ucwords(mb_substr(@$employment_type, 0, 10)) .'';
                                            } else {
                                                echo '<i class="fas fa-map-marker-alt"></i>  Remote | ' . ucwords(mb_substr(@$employment_type, 0, 10)) . '';
                                            }
                                        }

                                    @endphp
                                    |  {{ $work_type }}
                                </p>
                                 @php
                                                    $skilltag = '';
                                                    $skilltagclass = '';
                                                    $totalSkill = 0;
                                                    $matchBadge = '';
                                                    if (empty($limit)) {
                                                        $skillArr = DB::table('job_metronics_skills')
                                                            ->select('*')
                                                            ->where('company_id','=',$row->comId)
                                                            ->where('job_id', $row->id)
                                                            ->get();

                                                        /* ── Match % — same computation/threshold-colors as AppTracker's
                                                           candidate Match badge and bench_explore's job Match badge,
                                                           replicated here in the requisition-card direction. Reuses
                                                           $skillArr (already fetched above) and $currentRowJob->skills
                                                           (already loaded by the controller) — zero new queries. ── */
                                                        $jobRequiredSkillNames = $skillArr->pluck('skill_name')->filter()->values()->all();
                                                        $candidateSkillArr = array_values(array_filter(array_map('trim', explode(',', $currentRowJob->skills ?? ''))));
                                                        $matchedSkillsArr = \App\Helper\GeneralMethod::matchedSkillsList($candidateSkillArr, $jobRequiredSkillNames);
                                                        $totalRequired = count($jobRequiredSkillNames);
                                                        $matchPct = $totalRequired > 0 ? round(min((count($matchedSkillsArr) / $totalRequired) * 100, 100), 1) : 0;
                                                        if ($matchPct == 0) { $matchBg = '#f1f5f9'; $matchColor = '#64748b'; }
                                                        elseif ($matchPct < 50) { $matchBg = '#fee2e2'; $matchColor = '#dc2626'; }
                                                        elseif ($matchPct <= 90) { $matchBg = '#fef3c7'; $matchColor = '#d97706'; }
                                                        else { $matchBg = '#dcfce7'; $matchColor = '#16a34a'; }
                                                        $matchBadge = '<span class="badge my-1" style="background:'.$matchBg.';color:'.$matchColor.';font-weight:600;">Match '.$matchPct.'%</span>  ';

                                                        if (!empty($skillArr)) {
                                                            foreach ($skillArr as $key => $sval) {
                                                                $isMatched = in_array($sval->skill_name, $matchedSkillsArr, true)
                                                                    || collect($matchedSkillsArr)->contains(fn($m) => strcasecmp($m, $sval->skill_name) === 0);
                                                                if($key<=3){
                                                                    $skilltag .= $isMatched
                                                                        ? '<span class="tag-badge" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;" title="Matches candidate skill">' . ucfirst($sval->skill_name) . '</span>  '
                                                                        : '<span class="tag-badge bg-gray-light color-black">' . ucfirst($sval->skill_name) . '</span>  ';
                                                                }
                                                                $totalSkill++;
                                                            }
                                                        }
                                                    } else {
                                                        $skilltagclass = 'd-none';
                                                    }
                                 @endphp

                                <div class="agency-tags" style="position: absolute;">
                                   {!! $matchBadge !!}{!! $skilltag !!}
                                    @if($totalSkill >4)
                                        <span class="tag-badge bg-gray-light color-green client-show-detail"  data-row-id="{{ $row->id }}"><a href="javascript::void(0);">+{{ $totalSkill-4 }}</a></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-middle">
                            
                            <div class="metric">
                                 <span class="text-dark fw-bolder text-hover-primary fs-5">{{ ((@$row->min_exp) ? @$row->min_exp.' Y' :'' ) }} - {{ ((@$row->max_exp) ? @$row->max_exp.' Y' :'' ) }} </span>
                                <span class="text-muted fw-bold d-block fs-6 text-hover-primary">Experience</span>
                            </div>
                            <div class="metric">
                                <span class="text-dark fw-bolder text-hover-primary fs-5">{!! $salary_currency  !!} {{ $min_salary }} - {{ $max_salary }} {{ mb_substr($min_sal_mode,0,1) }}
                                </span>
                                <span class="text-muted fw-bold d-block fs-6 text-hover-primary">Salary</span>
                            </div>
                            <div class="metric">
                                @php

                                $total_positions = '';
                                $total_positions = number_format($row->total_positions);

                                $hiringManager = '--------';
                                // if(count($row->client_contact_info_jobs)>0){
                                //     $hiringManager = $row->client_contact_info_jobs[0]['contact_person_name'];
                                // }

                                $created_at = '';
                                $created_at = $row->created_at;
                                $newDate = GeneralMethod::ymdTodmyNotTime($created_at); //date("d M, Y", strtotime($created_at));
                                echo '<span class="text-dark fw-bolder text-hover-primary fs-5"><i class="ri-briefcase-line color-gray"></i>' . $newDate.' </span>';

                               
                        $status = $active = $hold = $inactive = $class = $recommendation_id = '';
                        $status = ucwords($row->status);
                        $class = $job_status[$row->status];
                        $recommendation_id = $row->recommendation_id;

                        if ($status == 'Active') {
                            $status = 'Hiring';
                        }
                        if ($status == 'Inactive') {
                            $status = 'Closed';
                        }
                        $final_status = '';
                        if (
                            ($row->final_status == 2 || $row->final_status == 3) &&
                            @$row->to_company_id == auth()->user()->company_id
                        ) {
                            $status = 'Removed';
                            $class = 'closed-bg';
                        }

                        $ulink =
                        '<a href="javascript.void(0);" data-row-id="' .
                        @$row->id .
                        '" class="d-flex flex-column ' .
                        @$class .
                        ' status_stage text-hover-success" data-toggle="modal" data-target="#status_stage">' .
                        $status .
                        '</a>';

                            $recommendation_text = __('messages.clinet_tag');
                            if (@$row->to_company_id == auth()->user()->company_id) {
                                $ulink ='<a href="javascript:void(0);" class="fs-6 ' . @$class . ' text-hover-success">' . $status .
                                    '</a>';
                            }

                            @endphp
                            
                                <span class="text-muted fw-bold d-block fs-6 text-hover-primary" style="display: flex !important;">{!! $ulink !!}  | {{ $recommendation_text }}</span>
                            </div>

                        </div>

                        <div class="card-right">
                            <div class="favorite-icon"><i class="ri-heart-line"></i></div>
                            @php
                            $resultCount = DB::table('market_job_added_count')->where('job_id','=',$row->id)->where('status','=',0)->count();
                            $exceeded = 20;

                             $benchRequestCheck = GeneralMethod::benchRequestCheck($condidate_id,$row->id); 
                             
                             $requestsend = "checkrequestsend";
                             if($currentRowJob->status==2){
                                 $requestsend = "request-send";
                             }

                            $cnt = '';
                            if ($benchRequestCheck == null) {
                                $cnt = '<b>.</b>';
                                echo ' <div class="d-flex my-4">
                                        <a href="javascript:void(0);" data-jobId="' .$row->id .'" data-conId="' . $currentRowJob->id .'"  class="btn btn-sm btn-primary '.$requestsend.'">Send Bench Request</a>
                                    </div> ';
                            } else {
                                if ($benchRequestCheck->ac_status == 1) {
                                    echo ' <div class="d-flex my-4">
                                <a href="javascript:void(0);" class="text-dark">Added</a>
                            </div> ';
                                } else {
                                    echo ' <div class="d-flex my-4">
                                <a href="javascript:void(0);" class="btn btn-sm btn-success">Request sent</a>
                            </div> ';
                                }
                            }
                            @endphp
                            
                            <button class="btn btn-sm btn-outline client-terms-listing-find-job" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ 
                              $row->recommendation_relation_id }}"><i class="ri-chat-3-line"></i>View T&C</button>
                        </div>
                    </div>
                    <div class="card-footer" style="display: none !important;">
                        
                        <div class="footer-metric">
                            <i class="ri-group-line color-gray"></i> 
                        </div>
                    </div>
                </div>

    @endforeach
@else
    <div class="rrj-empty">
        <div class="rrj-empty-icon"><i class="ti ti-briefcase-off"></i></div>
        <div class="rrj-empty-title">No matching jobs found</div>
        <div class="rrj-empty-desc">No active client jobs match this candidate's current search and filters. Try widening the skills, location or salary range.</div>
        <button type="button" class="btn btn-sm btn-light-primary" onclick="if(typeof rrjTagifySkills!=='undefined'){$('#rrj_clear_all').trigger('click');fetchBenchRecommendedReqCandidateList();}">
            <i class="ti ti-filter-off" style="margin-right:4px;"></i>Clear Filters
        </button>
    </div>
@endif
