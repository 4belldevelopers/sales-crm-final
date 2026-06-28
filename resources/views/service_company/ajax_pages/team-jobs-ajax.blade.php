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
    $row->recommendation_id = $recommendation_id;
    $row->to_company_id = $recommendation_id;
    $row->from_company_id = $from_company_id;
    $row->companyidcount = $row->comId;
    $row->clients_name = $row->client_name;

    $job_assign = $row->job_assign;
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
    $clients_name = ucwords(mb_substr(@$row->clients_name, 0, 30));

    $recommendation_id = $row->recommendation_id;
    $recommendation_text = '&nbsp;<i class="bi bi-house" style="color:#00CC00"></i>';

    $logo_path = '';
    $clientTypeJob = 'Client';
    if (!empty($row->logo_logical_path)) {
        $logo_path = @$row->logo_logical_path;
        if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
            $logo_path = @$row->logo;
            $clientTypeJob = 'Direct';
        }
    }

    if (@$row->self_company == 1) {
        if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
            $logo_path = @$row->logo;
            $clientTypeJob = '';
        }
    }

    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
        $recommendation_text = '&nbsp;<i style="color: #FF0000; font-weight:bold" class="fa fa-users" aria-hidden="true"></i>';
        $profile_detail_class = 'client-show-detail';
        $clients_name = ucwords(mb_substr(@$row->company_name, 0, 30));
        if (!empty(@$row->logo) && strlen(@$row->logo) > 10) {
            $logo_path = @$row->logo;
        }
        $clientTypeJob = 'Direct';
    }

    $logo_path = GeneralMethod::companyDetails($row->id);
    $departmentRow = DB::table('job_categories')->where('id','=',$row->category_id)->first();
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

    $recommendation_text = __('messages.internal_tag').' '.$clientTypeJob;
    $badge = "badge-internal";
    $svg = '<svg width="9" height="9" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5"/>
            <path d="M8 12h8M12 8v8" stroke="currentColor" stroke-width="2.5"/>
          </svg>';
    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
        $recommendation_text = $clientTypeJob.' '.__('messages.clinet_tag');
        $badge = "badge-client";
        $svg = '<svg width="9" height="9" fill="none" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2.5"></path>
            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2.5"></circle>
          </svg>';
    }

    $status = $active = $hold = $inactive = $class = '';
    $status = ucwords($row->status);
    $class = $job_status[$row->status];
    $recommendation_id = $row->recommendation_id;

    if ($status == 'Active') { $status = 'Hiring'; }
    if ($status == 'Inactive') { $status = 'Closed'; }
    $ulink = '<span data-row-id="' .@$row->id .'" class="badge-hiring ' .@$class .' status_stage" data-toggle="modal" data-target="#status_stage">' . $status .'</span>';
    if (($row->final_status == 2 || $row->final_status == 3) && @$row->to_company_id == auth()->user()->company_id) {
        $status = 'Removed';
        $class = 'closed-bg';
        $ulink = '<span class="badge-hiring ' .@$class .'">' .$status .'</span>';
    }

    $work_type = 'Office';
    if($row->work_type=='no'){ $work_type = "Office"; }
    elseif($row->work_type=='yes'){ $work_type = "Remote"; }
    elseif($row->work_type=='other'){ $work_type = "Hybrid"; }

    $employment_type = 'Full-time';
    $badgeemploymenttype = "badge-fulltime";
    if ($row->employment_type == 1) {
        $employment_type = 'Contract';
        $badgeemploymenttype = "badge-contract";
    }
    $created_at = $row->created_at;
    $newDate = GeneralMethod::ymdTodmyNotTime($created_at);

    $skillArrFinal = [];
    $skillArr = [];
    $skilltag = '';
    if(collect($row->job_metronics_skills)->isNotEmpty()) {
        $skillArr = $row->job_metronics_skills;
    }
    if (!empty($skillArr)) {
        foreach ($skillArr as $sval) {
            $sval = (object) $sval;
            $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
            array_push($skillArrFinal, ucfirst($sval->skill_name));
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
    $hiringManager = $hiringManagerdesignation = $hiringManagerCompany = '--------';
    if(count($row->client_contact_info_jobs)>0){
        $hiringManager = @$row->client_contact_info_jobs[0]['contact_person_name'];
        $hiringManagerdesignation = @$row->client_contact_info_jobs[0]['contact_person_designation'];
        $companyId = @$row->client_contact_info_jobs[0]['company_id'];
        $hiringManagerCompany = optional(DB::table('companies')->where('id',$companyId)->first())->company_name;
    }

    $recommendation_id = $row->recommendation_id;
    if ($recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
        $jobAssignListCaptain = $jobAssign->where('spok_user_id', '>', 0)->where('company_id', $row->comId)
            ->filter()->map(fn ($c) => ($c))->values()->all();
        if(count($jobAssignListCaptain)>0 && $row->comId != auth()->user()->company_id){
            $hiringManager = ucfirst($jobAssignListCaptain[0]['user_name']);
            $companyId = $jobAssignListCaptain[0]['company_id'];
            $spok_user_id = $jobAssignListCaptain[0]['spok_user_id'];
            $hiringManagerCompany = optional(DB::table('companies')->where('id',$companyId)->first())->company_name;
            $hiringManagerdesignation = optional(DB::table('users_profile')->where('user_id',$spok_user_id)->first())->designation;
        }
    }
    @endphp

    <!-- ══ JOB ROW ══ -->
    <div class="job-row" data-job-id="{{ $row->id }}" style="position: relative; min-height: 111px; cursor: pointer;">
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
            <div class="job-title">{{ ucwords(mb_substr(@$row->title, 0, 30)) }}</div>
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
        $html = '';
            if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                $jobId = $row->id;
                $partnersCount = $jobAssign->where('company_id', $row->to_company_id)->where('job_id', $jobId)->unique('user_id')->count();
                $partnersData = $jobAssign->where('company_id', $row->to_company_id)->where('job_id', $jobId)
                    ->filter(function ($r) { return $r['user_id'] == $r['spok_user_id']; })->sortBy('user_name')->take(1)->values();
                foreach ($partnersData as $pval) {
                    $pval = (object) $pval;
                    $html .= '<div class="hm-name team-listing" data-row-id="' .$row->id .'">'.ucwords(mb_substr(@$pval->user_name, 0, 10)).'</div>';
                }
                if($partnersCount>1){ $partnersCount = "+".$partnersCount-1; }
                $html .= '<div class="action-count">' .$partnersCount .' Members</div>';
                $html .= '<div class="action-more team-listing" data-row-id="' .$row->id .'">+ Add More</div>';
            } else {
                $jobId = $row->id;
                $partnersCount = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)->unique('user_id')->count();
                $partnersData = $jobAssign->where('company_id', $company_id)->where('job_id', $jobId)
                    ->filter(function ($r) { return $r['user_id'] == $r['spok_user_id']; })->sortBy('user_name')->take(1)->values();
                foreach ($partnersData as $pval) {
                    $pval = (object) $pval;
                    $html .= '<div class="hm-name hm-listing" data-row-id="' .$row->id .'">'.ucwords(mb_substr(@$pval->user_name, 0, 10)).'</div>';
                }
                if($partnersCount>1){ $partnersCount = "+".$partnersCount-1; }
                $html .= '<div class="action-count">' .$partnersCount .' Members</div>';
                $html .= '<div class="action-more hm-listing" data-row-id="' .$row->id .'">+ Add More</div>';
            }
            echo $html;
        @endphp
      </div>

      <!-- Col 6: Agency -->
      <div class="col-action">
        @php
            $html = '';
            if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                $jobId = $row->id;
                $company_name_agency = optional(DB::table('companies')->where('id', $row->to_company_id)->first())->company_name;
                $html .= '<div class="hm-name">'.ucwords(mb_substr(@$company_name_agency, 0, 30)).'</div>';
            } else {
                $jobId = $row->id;
                $agencyCount = GeneralMethod::agencyCount(auth()->user()->company_id,$jobId,1);
                $recommendation_relation = $row->recommendation_relation;
                $recommendationRelation = collect($recommendation_relation);
                $recommendedDataListCnt = count($agencyCount);
                $matchNumber = random_int(10, 20);
                if($recommendedDataListCnt > 0){
                    $html .= '<div class="hm-name">' .@$recommendedDataListCnt .' '.__('messages.Agency').'</div>';
                    $html .= '<div class="action-count">' .@$matchNumber .' Matching</div>';
                    $html .= '<div data-row-id="' . $row->id .'" class="action-more spartner-listing">+ Add More</div>';
                }else{
                    $html .= '<button class="btn-action spartner-listing" data-row-id="' . $row->id .'">
                        <svg fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Add Agency
                        </button>';
                    $html .= '<div class="action-count">' .@$matchNumber .' Matching</div>';
                }
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
                $applicationCounter = GeneralMethod::statusCountAll($row->id);
                $inreview_counter = GeneralMethod::statusCountAllInRe($row->id);
                if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $url_path = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/all');
                    $applicationCounter = GeneralMethod::statusCountThirdpartyAll($row->to_company_id,$row->id);
                    $inreview_counter = GeneralMethod::statusCountAllThirdpartyInRe($row->to_company_id,$row->id);
                }
                $applicationCounter = number_format($applicationCounter);
                $inreview_counter = number_format($inreview_counter);
                echo '<div class="stage-badge s-inw"><a href="' .$url_path .'" class="s-inw fw-bolder fs-6">' .$inreview_counter .'</a></div>';
            @endphp
          </div>
          @php
            $stages = [
                ['SOU', 1, 's-sou'],
                ['SCR', 2, 's-scr'],
                ['EVA', 3, 's-eva'],
                ['NEG', 4, 's-neg'],
                ['OFF', 5, 's-off'],
                ['JOI', 6, 's-joi'],
                ['REJ', 7, 's-rej'],
            ];
          @endphp
          @foreach ($stages as [$lbl, $master_status_id, $scls])
          <div class="stage">
            <div class="stage-label">{{ $lbl }}</div>
            @php
                $url_path = url('/service_company/apptracker/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                $cnt = GeneralMethod::statusCount($row->companyidcount, $row->id, $master_status_id);
                if ($row->recommendation_id > 0 && @$row->to_company_id == auth()->user()->company_id) {
                    $url_path = url('/service_company/apptracker/clientapp/' . GeneralMethod::value_encrypt($row->id) . '/' . $master_status_id);
                    $cnt = GeneralMethod::statusCountThirdparty(auth()->user()->company_id, $row->id, $master_status_id);
                }
                $cntShow = ($cnt == 0) ? '0' : $cnt;
                echo '<div class="stage-badge '.$scls.'"><a href="' .$url_path .'" class="'.$scls.' fs-6">' .$cntShow .'</a></div>';
            @endphp
          </div>
          @endforeach
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
        <div class="add-date" style="display:flex;justify-content: flex-end;">
            <svg fill="none" viewBox="0 0 24 24">
              <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
              <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.8"/>
            </svg>
            <span>&nbsp;{{ $newDate }}</span>
        </div>
      </div>

      <div style="position: absolute; bottom: 5px; left: 30px; white-space: nowrap; width: 90%; overflow: hidden;">
        <div class="job-meta-line">
          <span class="skills-inline"><b>Skills:</b> {!! $skilltag !!}</span>
        </div>
      </div>
    </div>
    @endforeach
@else
    <div class="job-empty">No data available</div>
@endif
