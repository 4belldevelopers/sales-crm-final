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
        $row->id = $row->job_id;
        $client_loc = DB::table('client_city_jobs')
                                    ->leftJoin('city_list', 'city_list.city_id', '=', 'client_city_jobs.city_id')
                                    ->leftJoin('state_list', 'state_list.id', '=', 'city_list.state_id')
                                    ->leftJoin('countries', 'countries.id', '=', 'state_list.country_id')
                                    ->select('city_list.city as city_name', 'countries.country_name')
                                    ->where('client_city_jobs.job_id', $row->id)
                                    ->first();
        $city = mb_substr(@$client_loc->city_name, 0, 21);
        $country_name = mb_substr(@$client_loc->country_name, 0, 6);


        $profile_detail_class = "client-show-detail";
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
        $logo_path = ''; //url('img/client-default.png');
        if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
            $logo_path = @$row->logo;
        }
        $skillArrFinal =[];
        $skillArr = [];
        $skilltag = '';
        $skilltagclass = '';
        if (empty($limit)) {
            $skillArr = $row->skillArr;
            if (!empty($skillArr)) {
                foreach ($skillArr as $sval) {
                    $skilltag .=
                        '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                    array_push($skillArrFinal,ucfirst($sval));
                }
            }
        } else {
            $skilltagclass = 'd-none';
        }
            
        $departmentRow = DB::table('job_categories')->where('id','=',@$row->category_id)->first();
        $department = $departmentRow->name ?? '';

        $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = '';
        // $min_sal_mode = $salaryMode[$row->min_sal_mode];
        // $max_sal_mode = $salaryMode[$row->max_sal_mode];
        
        $min_sal_mode = strtoupper(mb_substr($salaryMode[$row->min_sal_mode], 0, 1));
        $max_sal_mode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode], 0, 1));

        $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
        $max_salary_currency = $currencySymbole[@$row->max_salary_currency];

        $max_salary = '';
        $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
        $min_salary = '';
        $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
        $max_salary = GeneralMethod::formatAmount($max_salary);
        $min_salary = GeneralMethod::formatAmount($min_salary);

        $recommendation_text = __('messages.clinet_tag');
        $badge ="badge-client";
        // Direct Client only: handshake icon, same green as .badge-client's own text
        // colour (#09b760) — same icon/colour used on the bench "Placed" listing for
        // this exact concept (bench-place-candidate-ajax.blade.php).
        $svg = '<i class="fas fa-handshake" style="color:#09b760;font-size:9px;"></i>';

        $status = $active = $hold = $inactive = $class = $recommendation_id = '';
        $status = ucwords($row->status);
        $class = $job_status[$row->status];
        $recommendation_id = $row->recommendation_relation_id;

        if ($status == 'Active') {
            $status = 'Hiring';
        }
        if ($status == 'Inactive') {
            $status = 'Closed';
        }
        // Every job on this tab is recommended to this agency, not owned by it — the click
        // handler in jobs/index.blade.php reads data-direct-client and shows a message
        // instead of opening the status drawer (enforced server-side too, in
        // ServiceCompanyJobsController::newUpdateJobstatus()).
        $isDirectClientJob = $recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id;
        $ulink ='<span data-row-id="' .@$row->id .'" data-direct-client="' .($isDirectClientJob ? '1' : '0').'" class="badge-hiring ' .@$class .' status_stage">' . $status .'</span>';
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
        if(@$row->work_type=='no'){
            $work_type = "Office";
        }elseif(@$row->work_type=='yes'){
            $work_type = "Remote";
        }elseif(@$row->work_type=='other'){
            $work_type = "Hybrid";
        }
        $employment_type = 'Full-time';
        if ($row->employment_type == 1) {
            $employment_type = 'Contract';
        }

        $hiringManager = $hiringManagerdesignation ='--------';
                    
        // dd($row); from_company_id job_assign
        $captain = DB::table('job_assign')
        ->select('users.name','users_profile.designation')
        ->join('users','users.id','=','job_assign.spok_user_id')
        ->leftJoin('users_profile','users_profile.user_id','=','users.id')
        ->where('job_assign.company_id','=',$row->from_company_id)
        ->where('job_assign.job_id','=',$row->id)
        ->where('job_assign.spok_user_id','!=','')
        ->first();
        if($captain){
            $hiringManager = $captain->name ?? null;
            $hiringManagerdesignation = $captain->designation ?? null;
        }



    @endphp

    <!-- ══ ROW 1: Java Developer ══ -->
            <div class="job-row" data-job-id="JB001" data-status="active" data-location="pune" data-job-type="full-time" style="position: relative; min-height: 111px;">

                <!-- Col 1: Job Details -->
              <div style="display: flex; gap: 8px; align-items: flex-start">
                <div class="drag-col">
                  <div class="drag-handle">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24">
                      <circle cx="9" cy="5" r="1.5" fill="currentColor" />
                      <circle cx="15" cy="5" r="1.5" fill="currentColor" />
                      <circle cx="9" cy="12" r="1.5" fill="currentColor" />
                      <circle cx="15" cy="12" r="1.5" fill="currentColor" />
                      <circle cx="9" cy="19" r="1.5" fill="currentColor" />
                      <circle cx="15" cy="19" r="1.5" fill="currentColor" />
                    </svg>
                  </div>
                </div>
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
                    <div class="job-client">{{ ucwords(mb_substr(@$row->company_name, 0, 15)) }}</div>
                  </div>
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
                  {!! $min_salary_currency !!} {{ $min_salary }} - {{ $max_salary }} {{ $min_sal_mode }}
                </div>
                <!-- FIX 4: job type badge here -->
                <span class="badge-type {{ $badge }}">
                  {!! $svg !!}
                  {{ $recommendation_text }}
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
              <div class="col-hm client-hm-listing" data-row-id="{{ $row->id  }}">
                <div class="hm-name">{{ ucwords(mb_substr(@$hiringManager, 0, 10)) }}</div>
                <div class="hm-title">{{ @$hiringManagerdesignation }}</div>
                <div class="hm-company">{{ ucwords(@$employment_type) }}</div>
              </div>

              <!-- Col 5 -->
              <div class="col-hm">
                @php

                    $total_positions = '';
                    $total_positions = number_format($row->total_positions);
                    $created_at = $row->created_at;
                    $newDate = GeneralMethod::ymdTodmyNotTime($created_at); 
                    
                @endphp
                <div class="add-date" style="display:flex;">
                    <svg fill="none" viewBox="0 0 24 24" style="width: 10%;">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/>
                    </svg> 
                    <span>&nbsp;{{ $newDate }}</span>
                </div>
                <div class="hm-title">{{$total_positions }} Openings</div>
              </div>
              <!-- Col 5 -->
              <div class="col-hm">
                @php

                    $html =
                        '<div data-row-id="' .
                        $row->id .
                        '" data-row-recommendation_relation_id="' .
                        $row->recommendation_relation_id .
                        '" class="client-terms-listing"><a href="javascript:;" class="btn btn-sm btn-light">View</a></div>';
                    echo $html;
                @endphp
              </div>
              <!-- Col 5 -->
              <div class="col-action">
                @php
                    if($row->final_status==1){
                      echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-success">
                            Accepted
                        </a> 
                        </div>';
                    }elseif($row->final_status==-1){
                    echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                            Rejected
                        </a> 
                        </div>';
                    }elseif($row->final_status==2){
                      echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                            Removed
                        </a> 
                        </div>';
                    }elseif($row->final_status==3){
                    echo '<div class="d-flex my-4">  
                        <a href="javascript:void(0);" data-row-status="accept" class="btn btn-sm btn-danger">
                            Expired
                        </a> 
                        </div>';
                    }else{
                    echo '<div class="d-flex my-4"> 
                        <a href="javascript:void(0);" data-row-status="ignore" data-row-id="' .
                                $row->id .
                                '"  data-row-recommendation_relation_id="' .
                                $row->recommendation_relation_id .
                                '" class="btn btn-sm btn-light me-2 request_reject">
                            Reject
                        </a> 
                        <a href="javascript:void(0);" data-row-status="accept" data-row-id="' .
                                $row->id .
                                '" data-row-recommendation_relation_id="' .
                                $row->recommendation_relation_id .
                                '" class="btn btn-sm btn-primary request_approval">
                            Accept
                        </a> 
                        </div>';
                    }
                    
                @endphp
              </div>

              

              <div class="" style="position: absolute; bottom: 5px; left: 30px;white-space: nowrap; width: 90%; overflow: hidden;">
                <!-- FIX 2: all on one line -->
                    <div class="job-meta-line">
                        <span class="skills-inline" style="">
                            <b>Skills:</b> {!! $skilltag !!}
                        </span>

                    </div>
              </div>


            </div>
            
    @endforeach
@else
    {{-- <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="10" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr> --}}
@endif
