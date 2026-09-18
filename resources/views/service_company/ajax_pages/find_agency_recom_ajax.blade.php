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
            if (!empty(@$row->website) && empty(@$row->logo)){
                    $domainGet = GeneralMethod::getDomain($row->website);
                    $googleIcon = "https://www.google.com/s2/favicons?domain={$domainGet}&sz=256";    
                    $google = GeneralMethod::getDomainIconCompany($googleIcon,$row->id,true);
                    $logo_path  = ($google) ? $googleIcon : '';
            }
            if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                $logo_path = @$row->logo;
            }
          
            $from_company = $company_id = auth()->user()->company_id;
           $client_data = $row->_client_info ?? GeneralMethod::getClientIdCompany($row->id);
           $row->client_id =  $client_data->client_id ?? null;

           $relationget =  $row->_relation_info ?? GeneralMethod::getAgencyRelationData($from_company,$row->id);

           $from_company_id = $relationget->from_company_id ?? null;
           $recommendation_id = $relationget->to_company_id ?? null;
           $to_company_id = $relationget->to_company_id ?? null;
           $final_status = $relationget->to_invitation_status ?? null;

           $matchFound = $row->_match_found ?? DB::table('staffing_company_relation')->where('from_company_id', $from_company_id)->where('to_company_id', $row->id)->where('final_status',1)->count();


                   

            $percentage = 0;
            $companies_id = '';
            $companies_id = $row->id;

            $title = $skills = $experience = $salary = $location = $jobtype = $jskillset = 0;
            $client_type_css = 'show-detail-direct';
            
            $skillname = '';
            $scnt = 0;
            $tempArr = [];

            $skilltag = '';
            if (($row->matched_skill_count) > 0) {
                foreach ($row->matched_skills as $sval) {
                    $skilltag .= '<span class="skill-tag">' . ucfirst($sval) . '</span>  ';
                }
            }
            $percentage = 0;
            $matches = count($row->matched_skills ?? []);
            $totalElements = count($row->search_skills ?? []);
            $matchPercentage = $totalElements > 0 ? ($matches / $totalElements) * 100 : 0;
            $matchPercentage = min($matchPercentage, 100);
            $percentage = round($matchPercentage, 0);

            $skillMatchSection = '';
            if ($percentage > 0 || !empty($skilltag)) {
                $skillMatchSection =
                    '<div class="skill-tags d-flex align-items-center" style="position: absolute;bottom: 5px;left: 11px;white-space: nowrap;">
                        <span class="badge" style="background-color: #e8fff3; color: #10b981; border: 1px solid #a7f3d0; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-right: 4px;">'.$percentage.'% Skill Match</span>
                   ' .$skilltag . '</div>';
            }
            if(is_iterable($row->industry) && count($row->industry)){
                $ind_name = $row->industry['industry_name'];
            }

            $str = trim(@$row->company_name);
            if (!empty($str)) {
                $str = $strVal = strtolower($str[0]);
                $str = ord($str);
                if ($str >= 97 && $str <= 101) {
                    $bgTextDecore = 'bg-light-danger text-danger';
                } elseif ($str >= 102 && $str <= 106) {
                    $bgTextDecore = 'bg-light-warning text-warning';
                } elseif ($str >= 107 && $str <= 111) {
                    $bgTextDecore = 'bg-light-primary text-primary';
                } elseif ($str >= 112 && $str <= 116) {
                    $bgTextDecore = 'bg-light-success text-success';
                } elseif ($str >= 117 && $str <= 121) {
                    $bgTextDecore = 'bg-dark text-secondary';
                } else {
                    $bgTextDecore = 'bg-light-secondary text-dark';
                }
            }

            $Sizecount = $row->_user_size_count ?? DB::table('users')->where('company_id', $row->id)->count();
            $agencyJobsCount =  $row->_agency_jobs_count ?? GeneralMethod::agencyJobsCount($row->id);
            $activeJobs = $agencyJobsCount->all_jobs ?? 0;
            $agencyCandidatesCount =  $row->_agency_candidates_count ?? GeneralMethod::agencyJobsCandidatesCount($row->id);
            $all_candidates = $agencyCandidatesCount->all_candidates ?? 0;
            $all_candidate_bench = $agencyCandidatesCount->all_candidate_bench ?? 0;

        @endphp
        <!-- ROW 1 -->
        <tr style="height: 102px; position: relative;">
            <td class="{{ $client_type_css }}"  data-row-id="{{ $row->client_id }}">
                <div class="agency-cell">
                    @if(!$logo_path)
                        <div class="agency-logo tb {{ $bgTextDecore }}">
                            <svg width="40" height="40" viewBox="0 0 22 22" fill="none">
                                <rect width="22" height="22" rx="6" fill="#0F172A"/>
                                <text x="7" y="15" font-size="9" fill="white" font-family="DM Sans,sans-serif" font-weight="700">{{ strtoupper(@$strVal) }}</text>
                            </svg>
                        </div>
                    @else  
                        <div class="agency-logo sh">
                            <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
                                <img src="{{ $logo_path }}" alt="Company Logo" class="border-radius-5 w-100">
                            </svg>
                        </div>
                    @endif   
                    <div class="agency-info">
                    <div class="a-name" style="display: flex; align-items: center; gap: 6px;">
                        <span>{{ substr(ucwords(@$row->company_name), 0, 30) }}</span>
                        @if($percentage > 0)
                            <span class="badge" style="background-color: #e8fff3; color: #10b981; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">{{ $percentage }}% Match</span>
                        @endif
                    </div>
                    <div class="a-type">{{ substr(ucwords(@$ind_name), 0, 27) }}</div>
                    {!! $skillMatchSection !!}
                    </div>
                </div>
            </td>
            <td class="match-cell">
                @php
                    if ($parent_employment_type == 1) {
                        $commtype = 1;
                    } else {
                        $commtype = 2; // full time
                    }
                    $rates = '';
                    $currency = GeneralMethod::currency();
                    $salaryMode = GeneralMethod::salaryMode();
                    $currencySymbole = GeneralMethod::currencySymbole();
                    $minimumRate = $row->_minimum_rate ?? DB::table('commericials')
                        ->join('commericial_details', 'commericial_details.commericial_id', '=', 'commericials.id')
                        ->where('commericials.commtype', '=', $commtype)
                        ->where('commericials.company_id', '=', $row->id)
                        // Intent: exclude detail indexes that are not covered by an active global experience band.
                        ->whereExists(function ($experienceBands) {
                            $experienceBands->selectRaw('1')
                                ->from('commercial_experience_bands as experience_bands')
                                ->where('experience_bands.is_active', 1)
                                ->whereColumn('commericial_details.exp', '>=', 'experience_bands.min_experience')
                                ->whereColumn('commericial_details.exp', '<=', 'experience_bands.max_experience');
                        })
                        ->min('commericial_details.rate');
                        $minimumRateGet = $minimumRate;

                    $minimumRateType = $row->_minimum_rate_type ?? DB::table('commericials')
                        ->select('commericials.*')
                        ->where('commericials.commtype', '=', $commtype)
                        ->where('commericials.company_id', '=', $row->id)
                        ->first();
                    // sal_mode /salary_currency
                    $jobSalaryCurrency = (@$minimumRateType->salary_currency) ? @$minimumRateType->salary_currency : 0;
                    $jobSalaryMode = (@$minimumRateType->sal_mode) ? @$minimumRateType->sal_mode : 0;
                    if ($minimumRateType) {
                        if ($minimumRateType->commtype == 1) {
                            $current_cur = $currencySymbole[@$jobSalaryCurrency];
                            $current_mode = $salaryMode[@$jobSalaryMode];
                            $currencymain = GeneralMethod::currency(); // Currency code
                            // dd($currency);

                            $currency = $currencymain[@$minimumRateType->salary_currency];
                            // dd($currency);
                            // $current_cur = $currencySymbole[@$minimumRateType->salary_currency];
                            // $current_mode = $salaryMode[@$minimumRateType->sal_mode];

                            $convertedAmoun = GeneralMethod::conversionRates(
                                $currency,
                                $minimumRate,
                                @$minimumRateType->sal_mode);
                            // dd($convertedAmoun[$jobSalaryMode]);
                            $exchangeRates = [
                                'INR' => 1,
                                'EUR' => 90.33, // Example exchange rate for EUR to INR
                                'JPY' => 0.68, // Example exchange rate for JPY to INR
                                'GBP' => 103.7, // Example exchange rate for GBP to INR
                                'USD' => 83.63, // Example exchange rate for USD to INR
                                'AED' => 20.38, // Example exchange rate for AED to INR
                            ];
                            // dd($jobSalaryCurrency);
                            $currencydd = $currencymain[@$jobSalaryCurrency];
                            $exchangeRateInrToEur = 1 / $exchangeRates[$currencydd];

                            // Convert INR to EUR
                            $amount = $convertedAmoun[$jobSalaryMode]; // * $exchangeRateInrToEur;

                            $currencymain = GeneralMethod::currency(); // Currency code
                            $currency = $currencymain[@$jobSalaryCurrency];
                            $econvertedAmount = $minimumRate;
                            if ($minimumRateType->salary_currency > 0) {
                                $econvertedAmount = GeneralMethod::convertToINR(
                                    $minimumRate,
                                    $minimumRateType->salary_currency );
                            }

                            $convertedAmounEctc = GeneralMethod::conversionRates(
                                $currency,
                                $econvertedAmount,
                                $minimumRateType->sal_mode);
                            $minimumRate = $convertedAmounEctc[@$jobSalaryMode];

                            $salary = GeneralMethod::thousandsCurrencyFormat($minimumRate);
                            // $rates = $current_cur.''.$salary.'/'.$current_mode;
                            echo '<div href="javascript:void(0);" class="match-pct excellent">' .
                                $current_cur .
                                $minimumRateGet .
                                '/' .
                                $current_mode .
                                '</div>
						        <span class="match-label excellent">Contract</span>
                                <div class="match-reason">Recurring Fee</div> ';
                        } else {
                            $rates = $minimumRate . '% * CTC';
                            echo '<div class="match-pct excellent">' .$rates .
                                '</div>
					            <span class="match-label excellent">Full-Time</span>
                                 <div class="match-reason">Onetime</div> ';
                        }
                    }
                @endphp
              
            </td>
            {{-- <td class="perf-cell">
            <div class="star-row">
                <span class="star">★</span>
                <span class="rating">4.7</span>
                <span class="rev">(128 reviews)</span>
            </div>
            <div class="perf-row">
                <span class="perf-label">Success Rate</span>
                <div class="bar-wrap"><div class="bar-fill green" style="width:96%"></div></div>
                <span class="perf-val">96%</span>
            </div>
            <div class="closure-row">
                <span class="closure-label">Avg. Closure Time</span>
                <span class="closure-val">12 days</span>
            </div>
            </td> --}}
            <td>
            <div class="overview-cell">
                <div class="ov-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
                {{ (($activeJobs < 5) ? 5 : $activeJobs) }} Active Jobs
                </div>
                <div class="ov-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                @if(@$app_int_outsource == 2)
                {{ (($all_candidates < 5) ? 5 : $all_candidates) }} Profiles
                @else
                {{ (($all_candidate_bench < 5) ? 5 : $all_candidate_bench) }} Bench
                @endif
                </div>
                {{-- <div class="ov-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    15+ Industries
                </div> --}}
            </div>
            </td>
            <td>
            <div class="loc-cell">

                @php
                    $cityDisplay = '';
                    $countryDisplay = '';

                    // Check if locInfo object already has city_name or city
                    if (!empty($locInfo->city_name)) {
                        $cityDisplay = $locInfo->city_name;
                    } elseif (!empty($locInfo->city)) {
                        $cityDisplay = $locInfo->city;
                    }

                    if (!empty($locInfo->country_code)) {
                        $countryDisplay = strtoupper($locInfo->country_code);
                    } elseif (!empty($locInfo->country_name)) {
                        $countryDisplay = strtoupper(substr($locInfo->country_name, 0, 2));
                    }

                    if (empty($cityDisplay) || empty($countryDisplay)) {
                        $clientLocData = DB::table('clients')
                            ->join('client_location', 'client_location.client_id', '=', 'clients.id')
                            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
                            ->leftJoin('city_list', 'city_list.city_id', '=', 'client_location.city_id')
                            ->select(
                                'client_location.city as direct_city',
                                'city_list.city as city_name',
                                'countries.country_name',
                                'countries.sortname as country_code'
                            )
                            ->where('clients.company_id', '=', $row->id)
                            ->where('clients.self_company', '=', 1)
                            ->first();

                        if ($clientLocData) {
                            $cityDisplay = !empty($clientLocData->city_name) ? $clientLocData->city_name : ($clientLocData->direct_city ?? '');
                            $countryDisplay = !empty($clientLocData->country_code) ? strtoupper($clientLocData->country_code) : (!empty($clientLocData->country_name) ? strtoupper(substr($clientLocData->country_name, 0, 2)) : 'IN');
                        }
                    }
                @endphp

                <div class="loc-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                @if(!empty($cityDisplay))
                    {{ ucwords(substr($cityDisplay, 0, 15)) }}@if(!empty($countryDisplay)), {{ $countryDisplay }}@endif
                @elseif(!empty($countryDisplay))
                    {{ $countryDisplay }}
                @else
                    Multiple Locations
                @endif
                </div>
                <div class="loc-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                {{ $Sizecount }} Recruiters
                </div>
            </div>
            </td>
            <td>
            <div class="last-active">{{ date('d M y', strtotime(@$row->last_login)) }}</div>
            <div class="last-active-time">{{ date('h:i A', strtotime(@$row->last_login)) }}</div>
            <div class="status-dot"><span class="dot active"></span><span style="color:#16A34A;font-weight:600">Active</span></div>
            </td>
            <td>
                @php
                    $from_company = $company_id = auth()->user()->company_id;
                    $id = '';
                    $id = $row->id;
                    $to_company_id = 0;
                    if (is_iterable($row->staffing_company_relation) && count($row->staffing_company_relation) > 0) {
                        $to_company_id =  $row->staffing_company_relation[0]['to_company_id'];
                    }

                    // $matchFound = collect($row->staffing_company_relation)->contains('to_company_id', $id);
                     $matchFound = DB::table('staffing_company_relation')->where('from_company_id', $company_id)->where('to_company_id', $row->id)->where('final_status',1)->count();
                        
                

                    $html = '';
                    $recommendateCounter = DB::table('recommendation_relation')
                    ->select('recommendation_relation.id')
                    ->where('recommendation_relation.from_company_id', $from_company)
                    ->where('recommendation_relation.to_company_id', $id)
                    ->where('recommendation_relation.job_id', $jobid)
                    //->where('recommendation_relation.final_status', '=', 1)
                    ->count();

                        if ($recommendateCounter == 1) {
                            $recommendateRow = DB::table('recommendation_relation')
                            ->select('recommendation_relation.id', 'recommendation_relation.from_company_id', 'recommendation_relation.from_status', 'recommendation_relation.to_status', 'recommendation_relation.final_status')
                            ->where('recommendation_relation.from_company_id', $from_company)
                            ->where('recommendation_relation.to_company_id', $id)
                            ->where('recommendation_relation.job_id', $jobid)
                            //->where('recommendation_relation.final_status', '=', 1)
                            ->first();
                           
                            // if($recommendateRow){
                                $final_status = $recommendateRow ? $recommendateRow->final_status : null;
                            // }


                            if (($final_status == 0) || ($final_status == '')) {
                                $html = '<div class="d-flex flex-column "> <a href="javascript:void(0);" class="btn btn-sm btn-success w-130">Request Sent</a> </div>';
                            } else if ($final_status == -1) {
                                $html = '<a href="javascript:void(0);" data-row-id="' . $id . '" data-matchcount="" data-row-jobid="' . @$jobid . '" data-row-ename="ignore" class="btn btn-sm text-hover-white btn-primary w-130 resend_job_recommanded_request" style="width: 135px !important;">Send Requirement</a>
                                ';
                            } else if ($final_status == 2) {
                                $html = '<a href="javascript:void(0);" data-row-id="' . $id . '" data-matchcount="" data-row-jobid="' . @$jobid . '" data-row-ename="remove" class="btn btn-sm text-hover-white btn-primary w-130 resend_job_recommanded_request" style="width: 135px !important;">Send Requirement</a>
                                ';
                            } else {
                                $html = '<div class="d-flex flex-column"> 
                                <a href="javascript:void(0);" data-row-tabname="Registered" data-row-jobid="' . $jobid . '" data-matchcount="" data-row-fromcompany="' . $company_id . '" data-row-tocompany="' . $to_company_id. '" class="btn btn-sm btn-danger w-130 remove_job">
                                Remove
                                </a>
                                </div>';
                            }
                        }else{
                            if ($matchFound){
                                     //###############   REGISTER NOT ASSIGN 
									$html = '<div class="d-flex flex-column"> 
                                    <a href="javascript:void(0);" id="sp_list_' . $id . '" data-boosterCom="" data-boost-ids="" data-row-jobid="' . $jobid . '"  data-matchcount="" data-row-fromcompany="' . $from_company . '" data-row-tocompany="' . $id . '" class="btn btn-sm text-hover-white btn-primary w-130  w-130 send_only_job_invitation" style="width: 135px !important;">Send Requirement </a> 
                                    
                                    </div>';
							} else{    
                                 //########################################## 	UNREGISTER   
                                    $html = '<div class="flex-column">
                                    <a href="javascript:void(0);" id="sp_list_' . $id . '" data-boosterCom="" data-boost-ids="" data-row-id="' . $id . '" data-matchcount="" data-row-jobid="' . $jobid . '" class="btn btn-sm text-hover-white btn-primary w-130 send_job_recommanded_request" style="width: 135px !important;"> Send Requirement</a> 
                                    
                                    </div>';
                           }
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
