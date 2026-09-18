@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $job_status = GeneralMethod::job_status();

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)
    @php
    $row = (object) $rowArr;

    // $recommendation_id = collect($row->recommendation_relation)->contains('to_company_id', auth()->user()->company_id);
    $targetJobId = $row->id;

    $company_id = auth()->user()->company_id;

    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    // dd($relationget);

    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId ?? null;
    $row->clients_name = $row->client_name ?? '';
    
    $job_assign = $row->job_assign ?? [];
    $jobAssign = collect($job_assign);

    // $final_status_two = collect($row->recommendation_relation)->first(fn($item) => $item['to_company_id'] == 2)['final_status'] ?? null;

    // $final_status_two = collect($row->recommendation_relation)->first(fn($item) => $item['final_status'] == 2)['final_status'] ?? null;

    // $final_status_three = collect($row->recommendation_relation)->first(fn($item) => $item['final_status'] == 3)['final_status'] ?? null;

            $clients_name = '';
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

            $profile_detail_class = 'show-detail';
            $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 30));

            //  dd($ddudud);

            $recommendation_id = '';
            $recommendation_id = $row->recommendation_id;
            $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

            $logo_path = '';
            $clientTypeJob = 'Client';
            //$logo_path				=	url('img/client-default.png');
            if (!empty($row->logo_logical_path)) {
                $logo_path = @$row->logo_logical_path;

                if (
                    $recommendation_id > 0 &&
                    @$row->to_company_id == auth()->user()->company_id
                ) {
                    $logo_path = @$row->logo;
                    $clientTypeJob = 'Direct';
                }
            }
            

            if (@$row->self_company == 1) {
                if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                    $clientTypeJob = '';
                    $clientTypeJob = '';
                }
            }

            if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                $recommendation_text =
                    '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
                $profile_detail_class = 'client-show-detail';
                $clients_name = ucwords(mb_substr(@$row->company_name, 0, 30));

                if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
                    $logo_path = @$row->logo;
                }
                $clientTypeJob = 'Direct';
            }
            // dd($logo_path);
            $logo_path = GeneralMethod::companyDetails($row->id);
            $departmentRow = DB::table('job_categories')->where('id','=',$row->category_id)->first();
            $department = $departmentRow->name ?? '';

            
            $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary =
                '';
            // Full suffix (/Annum, /Month, /Hour) instead of a single letter — clearer at a glance.
            $min_sal_mode = ['/Annum', '/Month', '/Hour'][$row->min_sal_mode] ?? '';
            $max_sal_mode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode], 0, 1));
            $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
            $max_salary_currency = $currencySymbole[@$row->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
            $max_salary = GeneralMethod::formatAmount($max_salary);
            $min_salary = GeneralMethod::formatAmount($min_salary);

            $isSelfCompany = ((int) ($row->self_company ?? 0)) === 1;
            $recommendation_text = $isSelfCompany ? __('messages.internal_tag') : 'Internal Client';
            $badge ="badge-internal";
            // Internal Client: building icon, blue — sized/boxed to match the .exp-row /
            // .sal-row icons above it (.badge-type i/svg CSS) for clean alignment.
            $svg = '<i class="fas fa-building" style="color:#3e97ff;"></i>';
            if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                $parentCompanyName = !empty(@$row->company_name) ? @$row->company_name : (@$row->client_name ?? '');
                $recommendation_text = 'Direct Client';
                $badge ="badge-client";
                // Direct Client only: handshake icon, same green as .badge-client's own text
                // colour (#09b760) — same icon/colour used on the bench "Placed" listing for
                // this exact concept (bench-place-candidate-ajax.blade.php).
                $svg = '<i class="fas fa-handshake" style="color:#09b760;"></i>';
            }

            $status = $active = $hold = $inactive = $class = '';
            $status = ucwords($row->status);
            $class = $job_status[$row->status];
            $recommendation_id = $row->recommendation_id;

            if ($status == 'Active') {
                $status = 'Hiring';
            }
            if ($status == 'Inactive') {
                $status = 'Closed';
            }
            // Direct Client job (recommended to this agency, not owned by it) — the click
            // handler in jobs/index.blade.php reads data-direct-client and shows a message
            // instead of opening the status drawer, since only the owning company may
            // actually change the status (enforced server-side too, in
            // ServiceCompanyJobsController::newUpdateJobstatus()).
            $isDirectClientJob = $recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id;
            // Same status icons as the Job Status drawer's cards (Hiring/Hold/Closed).
            $statusIcon = $status == 'Hiring' ? 'fa-user-group' : ($status == 'Hold' ? 'fa-pause' : 'fa-box-archive');
            $ulink ='<span data-row-id="' .@$row->id .'" data-direct-client="' .($isDirectClientJob ? '1' : '0').'" class="badge-hiring ' .@$class .' status_stage"><i class="fas ' .$statusIcon .'"></i>' . $status .'</span>';
            $final_status = '';
            if (
                ($row->final_status == 2 || $row->final_status == 3) &&
                @$row->to_company_id == auth()->user()->company_id
            ) {
                $status = 'Removed';
                $class = 'closed-bg';
                $ulink ='<span class="badge-hiring ' .@$class .'">' .$status .'</span>';
            }

            $work_type = 'Office';
            if($row->work_type=='no'){
                $work_type = "Office";
            }elseif($row->work_type=='yes'){
                $work_type = "Remote";
            }elseif($row->work_type=='other'){
                $work_type = "Hybrid";
            }

            $employment_type = 'Full-time';
            $badgeemploymenttype = "badge-fulltime";
            $employmentIcon = 'fa-briefcase';
            $employmentTone = 'fulltime';
            if ($row->employment_type == 1) {
                $employment_type = 'Contract';
                $badgeemploymenttype = "badge-contract";
                $employmentIcon = 'fa-file-signature';
                $employmentTone = 'contract';
            }
            $created_at = $row->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at);
            $skillArrFinal =[];
            $skillArr = [];
            $skilltag = '';
            $skilltagclass = '';
            if(collect($row->job_metronics_skills ?? [])->isNotEmpty()) {
                $skillArr = $row->job_metronics_skills ?? [];
            }
           if (!empty($skillArr)) {
                foreach ($skillArr as $sval) {
                    $sval = (object) $sval;
                    $skilltag .=
                                    '<span class="skill-tag">' . ucfirst($sval->skill_name) . '</span>  ';
                    array_push($skillArrFinal,ucfirst($sval->skill_name));
                }
            }
           
            $country_id = $country_name = '';
            $jobid = $row->id;
            
            $client_city_jobs = $row->client_city_jobs ?? [];
            $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;
            $firstCity = collect($client_city_jobs)->first();
                           
            $cityName = $firstCity['city_name'] ?? null;
            $city = mb_substr(@$cityName, 0, 21);
            $countryName = $firstCity['country_name'] ?? null;
            $country_name = mb_substr(@$countryName, 0, 6);

            $total_positions = '';
            $total_positions = number_format($row->total_positions ?? 0);
            $hiringManager = $hiringManagerdesignation = $hiringManagerCompany = '--------';
            if(count($row->client_contact_info_jobs ?? [])>0){
                $hiringManager = @$row->client_contact_info_jobs[0]['contact_person_name'];
                $hiringManagerdesignation = @$row->client_contact_info_jobs[0]['contact_person_designation'];
                $companyId = @$row->client_contact_info_jobs[0]['company_id'];
                $hiringManagerCompany = $companyId ? (DB::table('companies')->where('id',$companyId)->first()->company_name ?? '--------') : '--------';
            }
            
            $recommendation_id = $row->recommendation_id;
            if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                $jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $row->comId)
                ->filter()
                ->map(fn ($c) => ($c))
                ->values()
                ->all();
                $clientjobAssignListCaptain = '';
                if(count($jobAssignListCaptain)>0 && $row->comId != auth()->user()->company_id){
                    $hiringManager = ucfirst($jobAssignListCaptain[0]['user_name']);
                    $companyId = $jobAssignListCaptain[0]['company_id'];
                    $spok_user_id = $jobAssignListCaptain[0]['spok_user_id'];
                    $hiringManagerCompany = DB::table('companies')->where('id',$companyId)->first()->company_name;
                    $hiringManagerdesignation = DB::table('users_profile')->where('user_id',$spok_user_id)->first()->designation;
                }
                
            }





    @endphp
    
    

            <!-- ══ ROW 1: Java Developer ══ -->
            <div class="job-row" data-job-id="JB001" data-status="active" data-location="pune" data-job-type="full-time" style="position: relative; min-height: 96px;">
              <!-- Col 1: Job Details -->
                <div class="col-job {{ $profile_detail_class }}" data-row-id="{{ $row->id }}">
                  <!-- FIX 3: logo aligned to cover title + dept + company -->
                  <div class="job-logo {{ $bgTextDecore }}" style="min-height: 48px;align-self: flex-start;">
                    @if(!empty($logo_path)) 
                        <img src="{{ str_replace('index.php', '', @$logo_path) }}" alt="Company Logo" class="border-radius-5 w-100">
                    @else
                     {{ ucwords($strVal) }}
                    @endif
                  </div>
                  <div class="job-info">
                    <div class="job-title">{{ ucwords(mb_substr(@$row->title, 0, 30)) }}</div>
                    <div class="job-dept">{{ @$department }}</div>
                    <div class="job-client">{{ $clients_name }}</div>
                  </div>
                </div>

              <!-- Col 2: Experience + Salary + FIX 4: badge -->
              <div class="col-exp">
                <div class="exp-row">
                  <svg fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                    <polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                  {{ @$row->min_exp }} - {{ @$row->max_exp }} Years
                </div>
                <div class="sal-row">
                  <span class="sal-amount">{!! $min_salary_currency !!} {{ $min_salary }} - {{ $max_salary }} {{ $min_sal_mode }}</span>
                </div>
                <!-- FIX 4: job type badge here -->
                <span class="badge-type employment-badge employment-badge-{{ $employmentTone }}">
                  <i class="fas {{ $employmentIcon }}"></i>
                  <span class="it-job-type">{{ $employment_type }}</span>
                </span>
              </div>

              <!-- Col 3: Hiring & Location — FIX 1: no bracket, clean -->
              <div class="col-hl">
                {!! $ulink !!}
                <div class="loc-row">
                  <svg fill="none" viewBox="0 0 24 24">
                    <path
                      d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"
                      stroke="currentColor"
                      stroke-width="1.8"
                    />
                    <circle
                      cx="12"
                      cy="10"
                      r="3"
                      stroke="currentColor"
                      stroke-width="1.8"
                    />
                  </svg>
                  {{ ucwords(mb_substr(@$city, 0, 10)) }}
                </div>
                <div class="mode-row">
                  <svg fill="none" viewBox="0 0 24 24">
                    <path
                      d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                      stroke="currentColor"
                      stroke-width="1.8"
                    />
                    <polyline
                      points="9,22 9,12 15,12 15,22"
                      stroke="currentColor"
                      stroke-width="1.8"
                    />
                  </svg>
                  {{ $work_type }}
                </div>
              </div>

              <!-- Col 4 -->
              <div class="col-hm">
                <div class="hm-name">{{ ucwords(mb_substr(@$hiringManager, 0, 10)) }}</div>
                <div class="hm-title">{{ @$hiringManagerdesignation }}</div>
                <div class="hm-company hm-client-type"><span class="it-job-type {{ $badge }}">{{ $recommendation_text }}</span><span class="it-dot-separator"></span><a href="javascript:void(0);" class="it-job-id">{{ @$row->id }}</a></div>
              </div>

              <!-- Col 5 -->
              <div class="col-action">
                @php
                $html = '';
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        $jobId = $row->id;
                        // dd( $jobAssign);
                       

                        $partnersCount = $jobAssign->where('company_id', $row->to_company_id)->where('job_id', $jobId)
                        ->unique('user_id')->count();

                        $partnersData = $jobAssign->where('company_id', $row->to_company_id)->where('job_id', $jobId)
                        ->filter(function ($row) {
                             return $row['user_id'] == $row['spok_user_id'];

                        })->sortBy('user_name')->take(1)->values();
                          foreach ($partnersData as $pval) {
                            $pval = (object) $pval;
                             $html .='<div class="hm-name team-listing" data-row-id="' .$row->id .'">
                               '.ucwords(mb_substr(@$pval->user_name, 0, 10)).'
                            </div>';
                          }
                          if($partnersCount>1){
                                $partnersCount ="+".$partnersCount-1;
                        }
                        $html .='<div class="action-count">' .$partnersCount .'+ Members</div>';
                        $html .= '<div class="action-more team-listing" data-row-id="' .$row->id .'">+ Add More</div>';
                    } else {
                        
                        $jobId = $row->id;

                        $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->unique('user_id')->count();

                        $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->filter(function ($row) {
                             return $row['user_id'] == $row['spok_user_id'];
                        })->sortBy('user_name')->take(1)->values();
                        $showCount = 0;
                        foreach ($partnersData as $pval) {   
                            $pval = (object) $pval;                        
                            $showCount++;
                            $html .='<div class="hm-name hm-listing" data-row-id="' .$row->id .'">
                                '.ucwords(mb_substr(@$pval->user_name, 0, 10)).'
                            </div>';
                           
                        }
                        if($partnersCount>1){
                                $partnersCount ="+".$partnersCount-1;
                        }
                        $html .='<div class="action-count">' .$partnersCount .'+ Members</div>';
                        $html .= '<div class="action-more hm-listing" data-row-id="' .$row->id .'">+ Add More</div>';
                    }
                    echo $html;
                @endphp
              </div>

              <!-- Col 6 -->
              <div class="col-action">
                @php

                    $html = '';
                    if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                        // Direct Client Job

                        $jobId = $row->id;
                        // dd( $jobAssign);

                        $company_name_agency = DB::table('companies')->where('id', $row->to_company_id)->first()->company_name;
                       

                        $partnersCount = $jobAssign->where('company_id', $row->to_company_id)->where('job_id', $jobId)->count();
                        $partnersData = $jobAssign->where('company_id', $row->to_company_id)->where('job_id', $jobId)
                        ->filter(function ($row) {
                             return $row['user_id'] == $row['spok_user_id'];
                        })->sortBy('user_name')->take(1)->values();
                        
                       $html .='<div class="hm-name" >'.ucwords(mb_substr(@$company_name_agency, 0, 30)).'</div>';
                    }
                    //	Custom Client Job
                    else {
                        $agencyCount = GeneralMethod::agencyCount(auth()->user()->company_id,$jobId,1);
                        $jobId = '';
                        $jobId = $row->id;
                        $recommendation_relation = $row->recommendation_relation ?? [];
                        $recommendationRelation = collect($recommendation_relation);
                        // dd($recommendationRelation);
                        $recommendedDataListCnt = $recommendationRelation->where('from_company_id', $company_id)->where('job_id', $jobId)->where('from_status', 1)->count();
                        $recommendedDataList = $recommendationRelation->where('from_company_id', $company_id)->where('job_id', $jobId)->where('from_status', 1)->take(1)->values();
                        $recommendedDataListCnt = count($agencyCount);
                        $matchNumber = random_int(10, 20);
                        if($recommendedDataListCnt > 0){
                            $html .='<div class="hm-name">
                                ' .@$recommendedDataListCnt .' '.__('messages.Agency').'
                                </div>';
                           $html .='<div class="action-count" >' .@$matchNumber .' Matching</div>';
                           $html .='<div data-row-id="' . $row->id .'" class="action-more spartner-listing">+ Add More</div>';
                        }else{ 
                             $html .='<button class="btn-action spartner-listing" data-row-id="' . $row->id .'">
                                <svg fill="none" viewBox="0 0 24 24">
                                    <path
                                    d="M12 5v14M5 12h14"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    /></svg
                                >Add Agency
                                </button>';
                           $html .='<div class="action-count" >' .@$matchNumber .' Matching</div>';
                        }
                    }

                    echo $html;

                @endphp
              </div>

              <!-- Col 7: Pipeline — FIX 1: no bracket -->
              <div class="col-pipeline">
                <div class="pipeline-stages">
                  <div class="stage">
                    <div class="stage-label">INW</div>
                    @php

                            $url_path = $total_application = $inreview_counter = '';
                            $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/all');

                            $job_id = $row->id;
                            
                            $applicationCounter = GeneralMethod::statusCountAll($row->id);
                            $inreview_counter = GeneralMethod::statusCountAllInRe($row->id);

                        

                            if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                                $url_path = url(
                                    '/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all',
                                );

                                $applicationCounter = GeneralMethod::statusCountThirdpartyAll($row->to_company_id,$row->id);
                                $inreview_counter = GeneralMethod::statusCountAllThirdpartyInRe($row->to_company_id,$row->id);

                                
                            }
                            $applicationCounter = number_format($applicationCounter);
                            $inreview_counter = number_format($inreview_counter);

                            echo '<div class="stage-badge s-inw">
                                <a href="' .
                                $url_path .
                                '" class="s-inw fs-6">' .
                                $inreview_counter .
                                '</a>
                            </div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">SOU</div>
                    @php
                        $job_id = '';
                        $master_status_id = 1;
                        $cnt = 0;
                        $job_id = $row->id;
                        $url_path = $total_application = $inreview_counter = '';
                        $url_path = url(
                            '/service_company/apptracker/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                        if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                            $url_path = url(
                                '/service_company/apptracker/clientapp/' .
                                    GeneralMethod::value_encrypt($job_id) .
                                    '/' .
                                    $master_status_id,
                            );
                            // $cnt = $row->thirdparty_Sourcing;
                            $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                        }
                        
                        if ($cnt == 0) {
                            echo '<div class="stage-badge s-sou"><a href="' .
                                $url_path .
                                '" class="s-sou fs-6">0</a></div>';
                        } else {
                            echo '<div class="stage-badge s-sou"><a href="' .
                                $url_path .
                                '" class="s-sou fs-6">' .
                                @$cnt .
                                '</a></div>';
                        }
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">SCR</div>
                    @php

                        $master_status_id = 2;
                        $job_id = '';
                        $job_id = $row->id;
                        $cnt = 0;
                        $url_path = '';
                        $url_path = url(
                            '/service_company/apptracker/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                        if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                            $url_path = url(
                                '/service_company/apptracker/clientapp/' .
                                    GeneralMethod::value_encrypt($job_id) .
                                    '/' .
                                    $master_status_id,
                            );
                            // $cnt = $row->thirdparty_Screening;
                            $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                        }
                        
                        if ($cnt == 0) {
                            $cnt = '<b>.</b>';
                            echo '<div class="stage-badge s-scr"><a href="' .
                                $url_path .
                                '" class="s-scr fs-6">0</a></div>';
                        } else {
                            echo '<div class="stage-badge s-scr"><a href="' .
                                $url_path .
                                '" class="s-scr fs-6">' .
                                @$cnt .
                                '</a></div>';
                        }

                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">EVA</div>
                    @php

                            $master_status_id = 3;
                            $job_id = '';
                            $job_id = $row->id;
                            $cnt = 0;
                            $url_path = '';
                            $url_path = url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($job_id) .
                                    '/' .
                                    $master_status_id,
                            );
                            $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                            if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                                $url_path = url(
                                    '/service_company/apptracker/clientapp/' .
                                        GeneralMethod::value_encrypt($job_id) .
                                        '/' .
                                        $master_status_id,
                                );
                                // $cnt = $row->thirdparty_Interview;
                                $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                            }
                            
                            if ($cnt == 0) {
                                $cnt = '<b>.</b>';
                                echo '<div class="stage-badge s-eva"><a href="' .
                                    $url_path .
                                    '" class="s-eva fs-6">0</a></div>';
                            } else {
                                echo '<div class="stage-badge s-eva"><a href="' .
                                    $url_path .
                                    '" class="s-eva fs-6">' .
                                    @$cnt .
                                    '</a></div>';
                            }

                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">NEG</div>
                    @php

                        $master_status_id = 4;
                        $job_id = '';
                        $job_id = $row->id;
                        $cnt = 0;
                        $url_path = '';
                        $url_path = url(
                            '/service_company/apptracker/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                        if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                            $url_path = url(
                                '/service_company/apptracker/clientapp/' .
                                    GeneralMethod::value_encrypt($job_id) .
                                    '/' .
                                    $master_status_id,
                            );
                            // $cnt = number_format($row->thirdparty_Negotiation);
                            $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                        }
                        
                        if ($cnt == 0) {
                            $cnt = '<b>.</b>';
                            echo '<div class="stage-badge s-neg"><a href="' .
                                $url_path .
                                '" class="s-neg fs-6">0</a></div>';
                        } else {
                            echo '<div class="stage-badge s-neg"><a href="' .
                                $url_path .
                                '" class="s-neg fs-6">' .
                                @$cnt .
                                '</a></div>';
                        }

                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">OFF</div>
                    @php
                        $master_status_id = 5;
                        $cnt = 0;
                        $job_id = '';
                        $job_id = $row->id;
                        $url_path = '';
                        $url_path = url(
                            '/service_company/apptracker/' .
                                GeneralMethod::value_encrypt($job_id) .
                                '/' .
                                $master_status_id,
                        );
                        $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                        if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                            $url_path = url(
                                '/service_company/apptracker/clientapp/' .
                                    GeneralMethod::value_encrypt($job_id) .
                                    '/' .
                                    $master_status_id,
                            );
                            // $cnt = $row->thirdparty_Offered;
                            $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                        }
                        
                        if ($cnt == 0) {
                            $cnt = '<b>.</b>';
                            echo '<div class="stage-badge s-off"><a href="' .
                                $url_path .
                                '" class="s-off fs-6">0</a></div>';
                        } else {
                            echo '<div class="stage-badge s-off"><a href="' .
                                $url_path .
                                '" class="s-off fs-6">' .
                                @$cnt .
                                '</a></div>';
                        }

                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">JOI</div>
                    @php

                        $master_status_id = 6;
                        $job_id = '';
                        $job_id = $row->id;
                        $cnt = 0;
                        $url_path = '';
                        $url_path = url(
                            '/service_company/apptracker/' .
                                GeneralMethod::value_encrypt($row->id) .
                                '/' .
                                $master_status_id,
                        );
                        $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                        if ($row->recommendation_id > 0 && @$row->to_company_id === auth()->user()->company_id) {
                            $url_path = url(
                                '/service_company/apptracker/clientapp/' .
                                    GeneralMethod::value_encrypt($job_id) .
                                    '/' .
                                    $master_status_id,
                            );
                        
                            // $cnt = $row->thirdparty_Joined;
                            $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                        }
                        
                        if ($cnt == 0) {
                            $cnt = '<b>.</b>';
                            echo '<div class="stage-badge s-joi"><a href="' .
                                $url_path .
                                '" class="s-joi fs-6">0</a></div>';
                        } else {
                            echo '<div class="stage-badge s-joi"><a href="' .
                                $url_path .
                                '" class="s-joi fs-6">' .
                                @$cnt .
                                '</a></div>';
                        }

                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">REJ</div>
                    @php
                            $master_status_id = 7;
                            $job_id = '';
                            $job_id = $row->id;
                            $cnt = 0;
                            $url_path = '';
                            $url_path = url(
                                '/service_company/apptracker/' .
                                    GeneralMethod::value_encrypt($row->id) .
                                    '/' .
                                    $master_status_id,
                            );
                            $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id); 
                            if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                                $url_path = url(
                                    '/service_company/apptracker/clientapp/' .
                                        GeneralMethod::value_encrypt($job_id) .
                                        '/' .
                                        $master_status_id,
                                );
                                // $cnt = $row->thirdparty_Rejected;
                                $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                            }
                            
                            if ($cnt == 0) {
                                $cnt = '<b>.</b>';
                                echo '<div class="stage-badge s-rej"><a href="' .
                                    $url_path .
                                    '" class="s-rej fs-6">0</a></div>';
                            } else {
                                echo '<div class="stage-badge s-rej"><a href="' .
                                    $url_path .
                                    '" class="s-rej fs-6">' .
                                    @$cnt .
                                    '</a></div>';
                            }

                    @endphp
                  </div>
                </div>
                <div class="pipeline-total">
                    @php
                        $interview_counter = GeneralMethod::statusCountInterview($row->companyidcount,$row->id);
                        if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                            $interview_counter = GeneralMethod::statusCountThirdpartyInterview(auth()->user()->company_id, $row->id);
                        }

                    @endphp
                  Total: <strong>{{ $applicationCounter }} Applications</strong> &nbsp;|&nbsp;
                  Interview: <strong>{{ $interview_counter }}</strong>
                </div>
              </div>

              <div class="job-meta-strip">
                <div class="job-meta-line">
                  <span class="job-date-tag">
                    <svg fill="none" viewBox="0 0 24 24">
                      <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                      <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/>
                    </svg>{{ $newDate }}
                  </span>
                  {!! $skilltag !!}
                </div>
              </div>

            </div>



    @endforeach
@else
    {{-- <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="17" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr> --}}
@endif
