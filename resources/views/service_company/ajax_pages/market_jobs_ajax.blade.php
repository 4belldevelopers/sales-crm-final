@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();
    // dd(count($data));
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

        // $row->final_status   = ($match['to_company_id'] ?? null) == $company_id ? $match['final_status'] : null;

        // $final_status = collect($row->recommendation_relation)->first(function ($item) use ($targetJobId) {
        //     return $item['job_id'] == $targetJobId;
        // })['final_status'] ?? null;

        $row->final_status = $final_status;
        $row->recommendation_id =$recommendation_id;
        $row->recommendation_relation_id  =$recommendation_id;
        $row->from_company_id = $from_company_id;
        $row->to_company_id = $to_company_id;
        $row->companyidcount= $row->comId;
        $row->clients_name = $row->client_name;

        $bgTextDecore = $strVal =  $leftbar = '';
        $str = trim(@$row->title);
        $str = $strVal = strtolower($str[0]);
        $str = ord($str);
        if ($str >= 97 && $str <= 101) {
            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
            $leftbar = 'danger';
        } elseif ($str >= 102 && $str <= 106) {
            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
            $leftbar = 'warning';
        } elseif ($str >= 107 && $str <= 111) {
            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
            $leftbar = 'primary';
        } elseif ($str >= 112 && $str <= 116) {
            $bgTextDecore = 'fs-3 bg-light-success text-success';
            $leftbar = 'success';
        } elseif ($str >= 117 && $str <= 121) {
            $bgTextDecore = 'fs-3 bg-dark text-secondary';
            $leftbar = 'secondary';
        } else {
            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
            $leftbar = 'secondary';
        }
        
        $country_id = $country_name = '';
        $jobid = $row->id;
                    
        $client_city_jobs = $row->client_city_jobs;
        $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;

        // dd($client_city_jobs);

        $employment_type = 'Full-time';
        if ($row->employment_type == 1) {
            $employment_type = 'Contract';
        }
        
        $classAdd = "bench-match";
        $textAdd = "Bench Required";
        $textClassAdd = "bench-badge";
        if($row->public_market_status==2){
            $classAdd = "market-match";
            $textAdd = "Marketplace";
            $textClassAdd = "marketplace-badge";
        }

        $min_sal_mode = $max_sal_mode = $max_salary_currency = $max_salary = $min_salary = '';
                    // $sal_mode = ($row->min_sal_mode) ? $row->min_sal_mode : 0;
                    
        $mod = $sal_mode_set ?? $row->max_sal_mode;

        $cur = $salary_currency_set ?? $row->max_salary_currency;
        

        $min_sal_mode = $salaryMode[$mod];
        $max_sal_mode = $salaryMode[$mod];
        $salary_currency = $currencySymbole[@$cur];
        $currencymain = GeneralMethod::currency(); // Currency code
        $currency = $currencymain[$cur];
        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->max_yearly, $mod);
        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->min_yearly, $mod);

        $ectc = $convertedAmounEctc[$mod];
        $ctc = $convertedAmounCtc[$mod];

        $max_salary = '';
        $max_salary = GeneralMethod::formatK($ectc);
        $min_salary = '';
        $min_salary = GeneralMethod::formatK($ctc);

        $work_type = 'Work From Office';
        if($row->work_type=='no'){
            $work_type = "Work From Office";
        }elseif($row->work_type=='yes'){
            $work_type = "Remote";
        }elseif($row->work_type=='other'){
            $work_type = "Hybrid";
        }
        $companyDetailsGet = GeneralMethod::companyDetailsGet($row->comId);
        $agencyJobsCandidatesCount = GeneralMethod::agencyJobsCandidatesCountJobWise($row->comId,$row->id);
        $agencyJobsInterviewCountJobWise = GeneralMethod::agencyJobsInterviewCountJobWise($row->id);
        $job_categories = DB::table('job_categories')
        ->select('job_categories.name')
        ->where('job_categories.id','=',$row->category_id)
        ->first();

        $candidateCount = GeneralMethod::benchCandidateCountSkillBase($row->id);
        
        @endphp

                
          
                    <!-- Job 1 -->
                    <div class="job-card">
                        <div class="job-main-info">
                            <div class="job-header-row">
                                <div class="job-icon {{ $bgTextDecore }}">{{ ucwords($strVal) }}</div>
                                <div class="job-details">
                                    <h2>{{ ucwords(mb_substr(@$row->title, 0, 25)) }}</h2>
                                    <div class="job-dept">Department: {{ ucwords(mb_substr(@$job_categories->name, 0, 35)) }}</div>
                                    {{-- <div class="job-badges">
                                        <span class="badge badge-red"><i class="fa-solid fa-fire"></i> Urgent Hiring</span>
                                    </div> --}}
                                </div>
                                {{-- <div class="openings-count">
                                    <span>8</span> Openings
                                </div> --}}
                            </div>
                            @php
                                    $skilltag = '';
                                    $skilltagclass = '';
                                    $totalSkill = 0;
                                    if (empty($limit)) {
                                        $skillArr = DB::table('job_metronics_skills')
                                            ->select('*')
                                            ->where('company_id','=',$row->comId)
                                            ->where('job_id', $row->id)
                                            ->get();
                                        if (!empty($skillArr)) {
                                            foreach ($skillArr as $key => $sval) {
                                                if($key<=5){
                                                        $skilltag .= '<span class="skill-tag">' . ucfirst($sval->skill_name) . '</span>';
                                                }else{
                                                  $totalSkill++;
                                                }
                                            }
                                        }
                                    } else {
                                        $skilltagclass = 'd-none';
                                    }
                            @endphp

                            <div class="skills-row">
                                {!! $skilltag !!}
                                @if($totalSkill)
                                    <span class="skill-tag skill-more client-show-detail" data-row-id="{{ $row->id }}">+{{ $totalSkill }}</span>
                                @endif
                            </div>
                            <div class="bench-match-box open-bench-talents" data-row-id="{{ $row->id }}" data-mod="{{ $mod }}" data-cur="{{ $cur }}" role="button" tabindex="0" title="View matching bench talents">
                                <i class="fa-solid fa-user-plus bench-icon"></i>
                                <div class="bench-text">
                                    <h4>Your Bench Match</h4>
                                    <p>You have {{ $candidateCount }} matching candidates</p>
                                </div>
                                <i class="fa-solid fa-chevron-right" style="margin-left: auto; color: #5E6C84;"></i>
                            </div>
                        </div>
                        
                        <div class="job-meta-info">
                            <div class="meta-row">
                                <i class="fa-regular fa-location-dot"></i>
                                <div class="meta-content">
                                    <p>Location</p>
                                    @php
                                        if ($row->work_type == 'yes') {
                                            echo '<span>Remote </span>';
                                        } elseif ($row->work_type == 'no' || $row->work_type =='other') {
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {

                                                $firstCity = collect($client_city_jobs)->first();

                                                $state_list = DB::table('city_list')
                                                ->select('state_list.state')
                                                ->join('state_list','state_list.id','=','city_list.state_id')
                                                ->where('city_list.city_id','=',$firstCity['city_id'])
                                                ->first();
                                                
                                            
                                                $cityName = $firstCity['city_name'] ?? null;
                                                $city = mb_substr(@$cityName, 0, 21);
                                                $countryName = $firstCity['country_name'] ?? null;
                                                $country_name = mb_substr(@$countryName, 0, 6);
                                                $stateName = @$state_list->state ?? null;
                                                $state_name = mb_substr(@$stateName, 0, 15);

                                                echo '<span>'.ucwords(mb_substr(@$city, 0, 10)) .',' .$country_name.'</span>';
                                            } else {
                                            echo '<span>Multiple Locations</span>';
                                            }
                                        } elseif ($row->work_type == 'any') {
                                            if ($jobCitySelected == 1 || $jobCitySelected == 0) {
                                                $firstCity = collect($client_city_jobs)->first();
                                                $state_list = DB::table('city_list')
                                                ->select('state_list.state')
                                                ->join('state_list','state_list.id','=','city_list.state_id')
                                                ->where('city_list.city_id','=',$firstCity['city_id'])
                                                ->first();
                                            
                                                $cityName = $firstCity['city_name'] ?? null;
                                                $city = mb_substr(@$cityName, 0, 21);
                                                $countryName = $firstCity['country_name'] ?? null;
                                                $country_name = mb_substr(@$countryName, 0, 6);
                                                $stateName = @$state_list->state ?? null;
                                                $state_name = mb_substr(@$stateName, 0, 15);

                                                echo '<span>'.ucwords(mb_substr(@$city, 0, 10)) .','.$country_name.'</span>';
                                            } else {
                                                echo '<span>Remote</span>';
                                            }
                                        }
                                    @endphp
                                </div>
                            </div>
                            <div class="meta-row">
                                <i class="fa-solid fa-laptop-house"></i>
                                <div class="meta-content">
                                    <p>Work Mode</p>
                                    <span>{{ $work_type }}</span>
                                </div>
                            </div>
                            <div class="meta-row">
                                <i class="fa-regular fa-clock"></i>
                                <div class="meta-content">
                                    <p>Experience</p>
                                    <span>{{ ((@$row->min_exp) ? @$row->min_exp.' Y' :'' ) }} - {{ ((@$row->max_exp) ? @$row->max_exp.' Y' :'' ) }}</span>
                                </div>
                            </div>
                            <div class="meta-row">
                                <i class="fa-regular fa-clock"></i>
                                <div class="meta-content">
                                    <p>Openings</p>
                                    <span>{{ @$row->total_positions }}</span>
                                </div>
                            </div>
                        </div>
                        @php
                            $ind_name = '';
                            if(is_iterable($companyDetailsGet->industry) && count($companyDetailsGet->industry)){
                                    $ind_name = $companyDetailsGet->industry['industry_name'];
                            }
                        @endphp

                        <div class="job-meta-info-2">
                            <div class="meta-row">
                                <i class="fa-solid fa-industry"></i>
                                <div class="meta-content">
                                    <p>Industry</p>
                                    <span>{{ $ind_name }}</span>
                                </div>
                            </div>
                            <div class="meta-row">
                                <i class="fa-regular fa-building"></i>
                                <div class="meta-content">
                                    <p>Company Size</p>
                                    @if(@$companyDetailsGet->company_size_from >= 10000)
                                        <span class=""> {{ @$companyDetailsGet->company_size_from }}+ </span>
                                        @else
                                        <span class=""> {{ @$companyDetailsGet->company_size_from }} - {{ @$companyDetailsGet->company_size }} </span>
                                        Employees 
                                    @endif
                                </div>
                            </div>
                            @php
                                $daysAgo = '';
                                $created_at = $row->created_at;
                                $newDate = date('d M, Y', strtotime($created_at));
                                $daysAgo = GeneralMethod::timeAgo($created_at);
                            @endphp

                            <div class="meta-row">
                                <i class="fa-regular fa-calendar"></i>
                                <div class="meta-content">
                                    <p>Posted</p>
                                    <span>{{ $newDate }} ({{ $daysAgo }})</span>
                                </div>
                            </div>
                            <div class="meta-row">
                                <i class="fa-regular fa-calendar"></i>
                                <div class="meta-content">
                                    <p>Salary</p>
                                    <span>{!! $salary_currency  !!}  {{ $min_salary }} - {{ $max_salary }} / {{ mb_substr($min_sal_mode,0,1) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="job-actions">
                            {{-- <div>
                                <div class="salary-range">₹ 22 L - 28 L</div>
                                <div class="salary-period">Per Annum</div>
                            </div> --}}
                             @php
                            $resultCount = DB::table('market_job_added_count')->where('job_id','=',$row->id)->where('status','=',0)->count();
                            $exceeded = 20;

                            if($row->public_market_status==2){
                                if(($resultCount>=$exceeded ||  $row->public_market_status==6) && ($row->to_company_id!=$company_id)){
                                    echo '<button data-row-status="" data-row-id="' .
                                            $row->id .
                                            '" data-row-recommendation_relation_id="' .
                                            $row->recommendation_relation_id .
                                            '" class="btn-submit btn btn-sm btn-danger">
                                        Vendor FullFilled
                                    </button> ';
                                }elseif(($row->public_market_status==2) && ($row->recommendation_relation_id=='' || $final_status==2 ||$final_status==0)){
                                 echo '<button class="btn-submit btn btn-sm btn-warning request_approval" data-row-status="accept" data-row-id="' . $row->id .'" data-row-recommendation_relation_id="' . $row->recommendation_relation_id .'">Apply As Agency</button>';

                                }elseif($row->public_market_status==3){
                                    echo '<button data-row-status="accept" data-row-id="' .
                                            $row->id .
                                            '" data-row-recommendation_relation_id="' .
                                            $row->recommendation_relation_id .
                                            '" class="btn-submit btn btn-sm btn-danger">
                                        Reject
                                    </button>';
                                }elseif($row->public_market_status==1){
                                    echo '<button data-row-status="accept" data-row-id="' .
                                            $row->id .
                                            '" data-row-recommendation_relation_id="' .
                                            $row->recommendation_relation_id .
                                            '" class="btn-submit btn btn-sm btn-warning">
                                        Under Review
                                    </button>';
                                }elseif($row->public_market_status==4){
                                    echo '<button data-row-status="accept" data-row-id="' .
                                            $row->id .
                                            '" data-row-recommendation_relation_id="' .
                                            $row->recommendation_relation_id .
                                            '" class="btn-submit btn btn-sm btn-danger">
                                        Vendor Limit
                                    </button>';
                                }elseif($row->to_company_id==$company_id){
                                    $url_path = url(
                                                '/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all',
                                            );
                                    echo '<div class="d-flex my-4"> 
                                    <a href="'.$url_path.'" class="btn-submit btn btn-sm btn-info">
                                        View Job
                                    </a> 
                                    </div>';
                                }


                            }else{
                                 echo '<button data-row-id="'.$row->id.'" data-mod="'.$mod.'" data-cur="'.$cur.'" class="btn btn-primary btn-submit open-bench-talents">Send Bench Request</button>';
                            }
                            @endphp

                            {{-- <button class="btn-submit">Submit Bench Profiles <i class="fa-solid fa-chevron-down"></i></button> --}}
                            <button class="btn-view market-show-detail" data-row-id="{{ $row->id }}">View Requirement</button>
                            <button class="btn-view client-terms-listing-find-job" data-row-id="{{ $row->id }}" data-row-recommendation_relation_id="{{ 
                              $row->recommendation_relation_id }}">View T&C</button>
                            <div class="save-job-link">{{ $agencyJobsCandidatesCount->offer_candidates }} Offered</div> 
                            <div class="save-job-link">{{$agencyJobsInterviewCountJobWise->all_interview}} Interview</div>
                        </div>
                    </div>

        
    @endforeach
@else
    No data available in table
@endif
