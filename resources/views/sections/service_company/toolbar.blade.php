<div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0" style="position: relative;">
                @php
                    use App\Helper\GeneralMethod;
					$company_id = auth()->user()->company_id;
                    $logos = GeneralMethod::companyDetails($id);
                    $lock_unlock = GeneralMethod::checksubscription();
                    $jobIdEncrypt = GeneralMethod::value_encrypt($id);
					$job = $jobDetail ?? $job ?? null;
					// dd($job,$company_id);
                    $jobtitle = @$job->title;
                    $bgTextDecore = $strVal = '';
				
                    $str = trim(@$jobtitle);
                    $str = $strVal = strtolower($str[0] ?? '');
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

                    $statusName = ucwords(substr((string) @$job->status, 0, 10));
                    $statusClass = 'joined-bg';
                    $final_status = @$job->final_status;
                    if ($final_status == 2) {
                        $statusName = 'REMOVED';
                        $statusClass = 'closed-bg';
                    }elseif(@$job->status==='active'){
                            $statusName = 'Hiring';
                            $statusClass = 'joined-bg';
                    }elseif(@$job->status==='inactive'){
                            $statusName = 'Closed';
                            $statusClass = 'closed-bg';
                    }elseif(@$job->status==='hold'){
                            $statusName = 'Hold';
                            $statusClass = 'hold-bg';
                    }


                    // Phase 3 Optimization: Avoid calling GeneralMethod::companyDetails($id) twice
                    // and remove unused $Jobcities = GeneralMethod::Jobcities($id) (~194ms 4-table MySQL JOIN).
                    $logo_path = is_string($logos) ? $logos : '';
                    $company_or_client = GeneralMethod::companyDetailsByClient($id);

                    $firstJobcities = GeneralMethod::firstJobcities($id);	
                    $locations  = explode('`,`',$firstJobcities['display']);
                    $salaryModeSelect = GeneralMethod::salaryMode();
                    $currencySymbole = GeneralMethod::currencySymbole();
                    // Salary budget shown as an abbreviated range (e.g. "50K-100K/Annual")
                    // — same K abbreviation GeneralMethod::formatAmount() uses on the main
                    // Jobs list page, but with the full mode word instead of a single letter.
                    $min_salary_abbr = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat(@$job->min_salary));
                    $max_salary_abbr = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat(@$job->max_salary));
                    $salaryModeWord = $salaryModeSelect[@$job->max_sal_mode] ?? $salaryModeSelect[0];
                    $salaryRangeCurrency = $currencySymbole[@$job->max_salary_currency] ?? $currencySymbole[0];

                    $created_at 		=	"";
                    $created_at 		=	@$job->created_at;
                    $created_at 		=	date("d M, Y", strtotime($created_at));
                    $jobClientInfo = \DB::table('jobs')
                        ->leftJoin('clients', 'clients.id', '=', 'jobs.client_id')
                        ->select('jobs.company_id', 'clients.self_company', 'clients.name as client_name')
                        ->where('jobs.id', $id)
                        ->first();

                    $jobCompId = (int) ($jobClientInfo->company_id ?? @$job->company_id ?? 0);
                    $selfCompVal = (int) ($jobClientInfo->self_company ?? @$job->self_company ?? 0);

                    $isDirectClientJob = ($company_id > 0 && $jobCompId > 0 && $company_id !== $jobCompId);
                    if ($isDirectClientJob) {
                        $tagsType = 'Direct Client';
                    } elseif ($selfCompVal === 1) {
                        $tagsType = __('messages.internal_tag');
                    } else {
                        $tagsType = 'Internal Client';
                    }

                    if ($tagsType === 'Internal Client') {
                        $internalClientName = ! empty($jobClientInfo->client_name) ? $jobClientInfo->client_name : (@$job->client_name ?? '');
                        if (! empty($internalClientName)) {
                            $company_or_client = $internalClientName;
                        }
                    }
					

                @endphp
                <!--begin::Requisitions=-->
                

                    <?php if (! empty($logo_path)) { ?>
                    <div data-row-id="' . $row->id . '"
                        class="symbol show-detail-open symbol-circle symbol-40px overflow-hidden me-2  ' . $profile_detail_class . ' border-radius-5" data-id="{{$id}}">
                        <a href="javascript:void(0);">
                            <div class="symbol-label border-radius-5">
                                <img src="{{ str_replace('index.php', '', @$logo_path) }}" alt="Company Logo"
                                    class="border-radius-5 w-100">
                            </div>
                        </a>
                    </div>
                    <?php	} else { ?>
                    <div class="symbol show-detail-open symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" data-id="{{$id}}"> <a
                            href="javascript:void(0);">
                            <div class="symbol-label {{ @$bgTextDecore }} border-radius-5">{{ strtoupper(@$strVal) }}</div>
                        </a>
                    </div>

                    <?php } ?>


                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column show-detail-open" data-id="{{$id}}">
                        <a href="javascript:void(0);"
                            class="text-dark fw-bolder text-hover-primary fs-6">{{ strtoupper(substr(@$jobtitle, 0, 20)) }}</a>
                        <a href="javascript:void(0);"
                            class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ ucwords(substr(@$company_or_client, 0, 50)) }}</a>
                    </div>
                    <!--begin::User details-->
                    <div class="d-flex flex-column ml-2 show-detail-open" data-id="{{$id}}">
                        <a href="javascript:void(0);">
                        <span class="badge {{ @$statusClass }} fw-bolder fs-6  px-2 py-1">{{ @$statusName }}</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ $tagsType }} • {{ @$id }}</span>
                        </a>
                    </div>
                    {{-- <div class="nac"></div>
                  <div class="nactop"></div> --}}
               
                <!--end::Requisitions-->
                <!--begin::Requisition Status-->
                
             
                
                <!--end::Requisition Status-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->

                <div class="d-flex flex-column me-5"> 
                    <span class="text-dark fw-bolder fs-6">{{ @$job->min_exp }}-{{ @$job->max_exp }} Yrs</span> 
                    <span class="text-muted fw-bold d-block fs-7">{!! $salaryRangeCurrency !!} {{ $min_salary_abbr }}-{{ $max_salary_abbr }}/{{ $salaryModeWord }}</span> 
                </div>
                @if(empty($hideLocationDate))
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                 <div class="d-flex flex-column me-5"> 
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{$firstJobcities['tooltip']}}">
						{{ ucwords(substr($locations[0], 0, 20)) }} 
						{{ ucwords($firstJobcities['countPlus']) }}
					</span> 
                    <span class="text-muted fw-bold d-block fs-7">
						{{ ucwords(substr($locations[1] ?? '', 0, 20)) }}
					</span> 
                </div>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <div class="d-flex flex-column">
                     <span class="text-dark fw-bolder fs-6">
                        {{ @$created_at }}
                    </span> <span class="text-muted fw-bold d-block fs-7">{{ (@$job->total_positions) ? @$job->total_positions.' Opening' : '' }} </span> 
                <!--end::Interview Date & No. of Openings-->
                </div>
                @endif

                @if(!empty($showStage))
                @php
                    $headerStageId = (int)($stageId ?? 8);
                    $headerStageMetaMap = [
                        8 => ['name' => 'In Review',   'key' => 'inreview',   'color' => '#92400e', 'bg' => '#fef3c7', 'border' => '#fde68a', 'dot' => '#d97706'],
                        1 => ['name' => 'Sourcing',    'key' => 'sourcing',   'color' => '#334155', 'bg' => '#f1f5f9', 'border' => '#cbd5e1', 'dot' => '#64748b'],
                        2 => ['name' => 'Screening',   'key' => 'screening',  'color' => '#6b21a8', 'bg' => '#f3e8ff', 'border' => '#e9d5ff', 'dot' => '#a855f7'],
                        3 => ['name' => 'Evaluation',  'key' => 'evaluation', 'color' => '#1e40af', 'bg' => '#dbeafe', 'border' => '#bfdbfe', 'dot' => '#3b82f6'],
                        4 => ['name' => 'Negotiation', 'key' => 'negotiation', 'color' => '#9d174d', 'bg' => '#fce7f3', 'border' => '#fbcfe8', 'dot' => '#ec4899'],
                        5 => ['name' => 'Offered',     'key' => 'offer',       'color' => '#9a3412', 'bg' => '#ffedd5', 'border' => '#fed7aa', 'dot' => '#f97316'],
                        6 => ['name' => 'Joined',      'key' => 'joined',     'color' => '#166534', 'bg' => '#dcfce7', 'border' => '#bbf7d0', 'dot' => '#22c55e'],
                        7 => ['name' => 'Rejected',    'key' => 'rejected',   'color' => '#991b1b', 'bg' => '#fee2e2', 'border' => '#fecaca', 'dot' => '#ef4444'],
                        9 => ['name' => 'Rejected',    'key' => 'rejected',   'color' => '#991b1b', 'bg' => '#fee2e2', 'border' => '#fecaca', 'dot' => '#ef4444'],
                    ];
                    $currentStageInfo = $headerStageMetaMap[$headerStageId] ?? $headerStageMetaMap[8];
                @endphp
                <span class="candidate-header-stage-sep" style="display: inline-block !important; width: 1px !important; height: 36px !important; min-width: 1px !important; background: #e8dfc5 !important; margin: 0 14px 0 10px !important; flex-shrink: 0 !important;"></span>
                <div class="candidate-header-stage-col" data-stage-id="{{ $headerStageId }}" style="display: inline-flex !important; flex-direction: row !important; align-items: center !important; gap: 9px !important; min-height: 42px !important; padding: 0 !important; margin: 0 10px 0 0 !important; flex-shrink: 0 !important;">
                    <div class="candidate-header-stage-icon" style="width: 30px !important; height: 30px !important; min-width: 30px !important; max-width: 30px !important; border-radius: 7px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; background: {{ $currentStageInfo['bg'] }} !important; border: 1px solid {{ $currentStageInfo['border'] }} !important; box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important; flex-shrink: 0 !important;">
                        <i class="fa-solid fa-flag" aria-hidden="true" style="color: {{ $currentStageInfo['color'] }} !important; font-size: 11px !important; line-height: 1 !important; display: inline-block !important;"></i>
                    </div>
                    <div class="candidate-header-stage-content" style="display: inline-flex !important; flex-direction: column !important; justify-content: center !important; gap: 2px !important; padding: 0 !important; margin: 0 !important;">
                        <span class="candidate-header-stage-lbl" style="display: block !important; color: #7882a0 !important; font-size: 9px !important; font-weight: 700 !important; line-height: 1.2 !important; text-transform: uppercase !important; letter-spacing: 0.6px !important; margin: 0 !important; padding: 0 !important; user-select: none !important;">STAGE</span>
                        <div class="d-flex align-items-center" style="margin: 0 !important; padding: 0 !important; line-height: 1 !important;">
                            <span class="candidate-header-stage-pill candidate-header-stage-pill--{{ $currentStageInfo['key'] }}"
                                  style="display: inline-flex !important; align-items: center !important; gap: 5px !important; padding: 2.5px 9px !important; border-radius: 12px !important; font-size: 10.5px !important; font-weight: 700 !important; line-height: 1.2 !important; border: 1px solid {{ $currentStageInfo['border'] }} !important; background: {{ $currentStageInfo['bg'] }} !important; color: {{ $currentStageInfo['color'] }} !important; white-space: nowrap !important; user-select: none !important; box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;"
                                  title="Current Pipeline Stage: {{ $currentStageInfo['name'] }}">
                                <span class="candidate-header-stage-dot" style="width: 6px !important; height: 6px !important; border-radius: 50% !important; background: {{ $currentStageInfo['dot'] }} !important; display: inline-block !important; flex-shrink: 0 !important;"></span>
                                <span class="candidate-header-stage-name" style="display: inline-block !important; white-space: nowrap !important; color: {{ $currentStageInfo['color'] }} !important;">{{ $currentStageInfo['name'] }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                @endif
                
                @php
                    $rc_path = url('/service_company/apptracker/similar_candidates/' . @$id);
                @endphp

                <!-- <div class="d-flex flex-column">
				<h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ $rc_path }}" class="text-gray-800 text-hover-primary mb-1">Recommend Candidates</a> </h5>
				<span class="text-gray-600">0</span> </div> -->
                <!--end::Recommend Candidates-->

</div>