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
    $targetJobId = $row->id;

    $company_id = auth()->user()->company_id;

    $relationget =  GeneralMethod::getrRcommendationRelationData($company_id,$targetJobId);
    $from_company_id = $relationget->from_company_id ?? null;
    $recommendation_id = $relationget->to_company_id ?? null;
    $final_status = $relationget->final_status ?? null;

    $row->final_status = $final_status;
    $row->recommendation_id =$recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount= $row->comId;
    $row->clients_name = $row->client_name;

    $job_assign = $row->job_assign ?? [];
    $jobAssign = collect($job_assign);

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
            if ($row->recommendation_id > 0) {
                $profile_detail_class = 'client-show-detail';
            }
            $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 30));

            $logo_path = GeneralMethod::companyDetails($row->id);
            $departmentRow = DB::table('job_categories')->where('id','=',@$row->category_id)->first();
            $department = $departmentRow->name ?? '';

            $min_sal_mode = $max_sal_mode = $min_salary_currency = $max_salary_currency = $max_salary = $min_salary = '';
            $min_sal_mode = strtoupper(mb_substr($salaryMode[$row->min_sal_mode], 0, 1));
            $max_sal_mode = strtoupper(mb_substr($salaryMode[$row->max_sal_mode], 0, 1));
            $min_salary_currency = $currencySymbole[@$row->min_salary_currency];
            $max_salary_currency = $currencySymbole[@$row->max_salary_currency];
            $max_salary = GeneralMethod::thousandsCurrencyFormat($row->max_salary);
            $min_salary = GeneralMethod::thousandsCurrencyFormat($row->min_salary);
            $max_salary = GeneralMethod::formatAmount($max_salary);
            $min_salary = GeneralMethod::formatAmount($min_salary);

            $recommendation_text = __('messages.internal_tag');
            $badge ="badge-internal";
            $svg = '<svg width="9" height="9" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5"/>
                    <path d="M8 12h8M12 8v8" stroke="currentColor" stroke-width="2.5"/>
                  </svg>';

            $status = $class = '';
            $status = ucwords($row->status);
            $class = $job_status[$row->status];

            if ($status == 'Active') {
                $status = 'Hiring';
            }
            if ($status == 'Inactive') {
                $status = 'Closed';
            }

            $ulink ='<span class="badge-hiring ' .@$class .'">' . $status .'</span>';
            $final_status = '';
            if (($row->final_status == 2 || $row->final_status == 3) && @$row->from_company_id == auth()->user()->company_id
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
            if ($row->employment_type == 1) {
                $employment_type = 'Contract';
                $badgeemploymenttype = "badge-contract";
            }
            $created_at = $row->created_at;
            $newDate = GeneralMethod::ymdTodmyNotTime($created_at);

            $skillArrFinal =[];
            $skillArr = [];
            $skilltag = '';
            if(collect(@$row->job_metronics_skills)->isNotEmpty()) {
                $skillArr = $row->job_metronics_skills;
            }
            if (!empty($skillArr)) {
                foreach ($skillArr as $sval) {
                    $sval = (object) $sval;
                    $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
                    array_push($skillArrFinal,ucfirst($sval->skill_name));
                }
            }

            $country_id = $country_name = '';
            $client_city_jobs = $row->client_city_jobs;
            $jobCitySelected = is_array($client_city_jobs) ? count($client_city_jobs) : 0;
            $firstCity = collect($client_city_jobs)->first();
            $cityName = $firstCity['city_name'] ?? null;
            $city = mb_substr(@$cityName, 0, 21);
            $countryName = $firstCity['country_name'] ?? null;
            $country_name = mb_substr(@$countryName, 0, 6);

            $total_positions = number_format($row->total_positions);
            $hiringManager = $hiringManagerdesignation = '--------';
            if(count($row->client_contact_info_jobs)>0){
                $hiringManager = @$row->client_contact_info_jobs[0]['contact_person_name'];
                $hiringManagerdesignation = @$row->client_contact_info_jobs[0]['contact_person_designation'];
            }
    @endphp

            <!-- ══ JOB ROW ══ -->
            <div class="job-row" data-job-id="{{ $row->id }}" data-status="{{ strtolower($row->status) }}" style="position: relative; min-height: 111px;">
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
                  <div class="job-logo {{ $bgTextDecore }}" style="min-height: 48px;align-self: flex-start;">
                    @if(!empty($logo_path))
                        <img src="{{ str_replace('index.php', '', @$logo_path) }}" alt="Company Logo" class="border-radius-5 w-100">
                    @else
                     {{ ucwords($strVal) }}
                    @endif
                  </div>
                  <div class="job-info">
                    <div class="job-title" data-toggle="tooltip1" title="{{ ucwords(@$row->title) }}">{{ ucwords(mb_substr(@$row->title, 0, 30)) }}</div>
                    <div class="job-dept">{{ @$department }}</div>
                    <div class="job-client">{{ $clients_name }}</div>
                  </div>
                </div>
              </div>

              <!-- Col 2: Experience + Salary + badge -->
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
                <span class="badge-type">
                  {!! $svg !!}
                  <span class="it-job-type {{ $badge }}">{{ $recommendation_text }}</span>
                    <span class="it-dot-separator"></span>
                    <a href="javascript:void(0);" class="it-job-id">{{ @$row->id }}</a>
                </span>
              </div>

              <!-- Col 3: Hiring & Location -->
              <div class="col-hl">
                {!! $ulink !!}
                <div class="loc-row">
                  <svg fill="none" viewBox="0 0 24 24">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="1.8"/>
                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                  {{ ucwords(mb_substr(@$city, 0, 10)) }}
                </div>
                <div class="mode-row">
                  <svg fill="none" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.8"/>
                    <polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                  {{ $work_type }}
                </div>
              </div>

              <!-- Col 4: Hiring Manager -->
              <div class="col-hm">
                <div class="hm-name">{{ ucwords(mb_substr(@$hiringManager, 0, 10)) }}</div>
                <div class="hm-title">{{ @$hiringManagerdesignation }}</div>
                <div class="hm-company">{{ ucwords(@$employment_type) }}</div>
              </div>

              <!-- Col 5: Team -->
              <div class="col-action">
                @php
                    $jobId = $row->id;
                    $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->unique('user_id')->count();
                    $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                        ->filter(function ($r) {
                            return $r['user_id'] == $r['spok_user_id'];
                        })->sortBy('user_name')->take(1)->values();
                    $html = '';
                    foreach ($partnersData as $pval) {
                        $pval = (object) $pval;
                        $html .='<div class="hm-name" data-row-id="' .$row->id .'">'.ucwords(mb_substr(@$pval->user_name, 0, 10)).'</div>';
                    }
                    if($partnersCount>1){
                        $partnersCount ="+".($partnersCount-1);
                    }
                    $html .='<div class="action-count">' .$partnersCount .' Members</div>';
                    echo $html;
                @endphp
              </div>

              <!-- Col 6: Agency -->
              <div class="col-action">
                @php
                    $html = '';
                    $jobId = $row->id;
                    $agencyCount = GeneralMethod::agencyCount($company_id, $jobId, 1);
                    $recommendedDataListCnt = is_countable($agencyCount) ? count($agencyCount) : 0;
                    $matchNumber = random_int(10, 20);
                    if($recommendedDataListCnt > 0){
                        $html .='<div class="hm-name">' .@$recommendedDataListCnt .' '.__('messages.Agency').'</div>';
                        $html .='<div class="action-count">' .@$matchNumber .' Matching</div>';
                    }else{
                        $html .='<div class="hm-name text-muted">No Agency</div>';
                        $html .='<div class="action-count">' .@$matchNumber .' Matching</div>';
                    }
                    echo $html;
                @endphp
              </div>

              <!-- Col 7: Pipeline -->
              <div class="col-pipeline">
                <div class="pipeline-stages">
                  <div class="stage">
                    <div class="stage-label">INW</div>
                    @php
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/all');
                        $applicationCounter = GeneralMethod::allStatusCountStaffingPartner($toCompanyId, $row->id);
                        $inreview_counter = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, 0);
                        $applicationCounter = number_format($applicationCounter);
                        $inreview_counter = number_format($inreview_counter);
                        echo '<div class="stage-badge s-inw"><a href="' . $url_path . '" class="s-inw fw-bolder fs-6">' . $inreview_counter . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">SOU</div>
                    @php
                        $master_status_id = 1;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-sou"><a href="' . $url_path . '" class="s-sou fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">SCR</div>
                    @php
                        $master_status_id = 2;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-scr"><a href="' . $url_path . '" class="s-scr fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">EVA</div>
                    @php
                        $master_status_id = 3;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-eva"><a href="' . $url_path . '" class="s-eva fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">NEG</div>
                    @php
                        $master_status_id = 4;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-neg"><a href="' . $url_path . '" class="s-neg fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">OFF</div>
                    @php
                        $master_status_id = 5;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-off"><a href="' . $url_path . '" class="s-off fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">JOI</div>
                    @php
                        $master_status_id = 6;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-joi"><a href="' . $url_path . '" class="s-joi fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                  <div class="stage">
                    <div class="stage-label">REJ</div>
                    @php
                        $master_status_id = 7;
                        $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                        $cnt = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, $master_status_id);
                        echo '<div class="stage-badge s-rej"><a href="' . $url_path . '" class="s-rej fs-6">' . @$cnt . '</a></div>';
                    @endphp
                  </div>
                </div>
                <div class="pipeline-total">
                    @php
                        $interview_counter = GeneralMethod::statusCountStaffingPartner($toCompanyId, $row->id, 3);
                    @endphp
                  Total: <strong>{{ $applicationCounter }} Applications</strong> &nbsp;|&nbsp;
                  Interview: <strong>{{ $interview_counter }}</strong>
                </div>
                <div class="add-date" style="display:flex;justify-content: flex-end;">
                    <svg fill="none" viewBox="0 0 24 24" style="width: 6%;">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    <span>&nbsp;{{ $newDate }}</span>
                </div>
              </div>

              <div class="" style="position: absolute; bottom: 5px; left: 30px;white-space: nowrap; width: 90%; overflow: hidden;">
                    <div class="job-meta-line">
                        <span class="skills-inline">
                            <b>Skills:</b> {!! $skilltag !!}
                        </span>
                    </div>
              </div>

            </div>
    @endforeach
@else
    <div style="text-align: center; color: #a1a5b7; padding: 40px 12px; font-size: 13px;">
        No data available in table
    </div>
@endif
