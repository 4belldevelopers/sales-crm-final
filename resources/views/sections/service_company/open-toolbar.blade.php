<div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0" style="position: relative;">
                @php
                    use App\Helper\GeneralMethod;
                    
					$company_id = (int) ((@$company_id) ? $company_id : (auth()->user()->company_id ?? 0));
                    $logos = GeneralMethod::openCompanyDetails($id,$company_id);
                    $lock_unlock = GeneralMethod::openChecksubscription($company_id);
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
                            $statusClass = 'joined-bg';
                    }elseif(@$job->status==='inactive'){
                            $statusClass = 'closed-bg';
                    }elseif(@$job->status==='hold'){
                            $statusClass = 'hold-bg';
                    }

                    $logo_path = '';
                    // dd($account);


                    if (!empty($account->logo)) {
                        $logo_path = $account->logo;
                    }
                    $company_or_client = GeneralMethod::openCompanyDetailsByClient($id,$company_id);



                    $Jobcities = GeneralMethod::openJobcities($id,$company_id);
                    $firstJobcities = GeneralMethod::openFirstJobcities($id,$company_id);	
                    $locations  = explode('`,`',$firstJobcities['display']);
                    $salaryModeSelect = GeneralMethod::salaryMode();
                    $currencySymbole = GeneralMethod::currencySymbole();
                    $min_sal_mode = strtoupper(substr($salaryModeSelect[$job->min_sal_mode], 0, 1));
                    $max_sal_mode = strtoupper(substr($salaryModeSelect[$job->max_sal_mode], 0, 1));
                    $min_salary_currency = $currencySymbole[@$job->min_salary_currency];
                    $max_salary_currency = $currencySymbole[@$job->max_salary_currency];
                    $max_salary = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($job->max_salary));

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
                    $viewerCompId = (int) ($company_id ?? 0);

                    $isDirectClientJob = ($viewerCompId > 0 && $jobCompId > 0 && $viewerCompId !== $jobCompId);
                    if ($isDirectClientJob) {
                        $tagsType = 'Direct Client';
                    } elseif ($selfCompVal === 1) {
                        $tagsType = __('messages.internal_tag');
                    } else {
                        $tagsType = 'Internal Client';
                    }

                    /* Intent: Always display Parent/Main Company Name under job title on open-toolbar */
                    $parent_company_name = ! empty($account->company_name) ? $account->company_name : '';
                    if (empty($parent_company_name)) {
                        $targetCompanyId = ($viewerCompId > 0) ? $viewerCompId : ($jobCompId > 0 ? $jobCompId : 0);
                        if ($targetCompanyId > 0) {
                            $parent_company_name = \DB::table('companies')->where('id', $targetCompanyId)->value('company_name');
                        }
                    }
                    $toolbar_display_company = ! empty($parent_company_name) ? $parent_company_name : $company_or_client;

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
                            class="text-dark fw-bolder text-hover-primary fs-6">{{ strtoupper(substr(@$jobtitle, 0, 27)) }}</a>
                        <a href="javascript:void(0);"
                            class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">{{ ucwords(substr(@$toolbar_display_company, 0, 50)) }}</a>
                    </div>
                    <!--begin::User details-->
                    <div class="d-flex flex-column ml-2 show-detail-open" data-id="{{$id}}">
                        <a href="javascript:void(0);">
                        <span class="badge {{ @$statusClass }} fw-bolder fs-6  px-2 py-1">{{ @$statusName }}</span>
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ $tagsType }} • R{{ @$id }}</span>
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
                    <span class="text-muted fw-bold d-block fs-7">{!! $max_salary_currency !!} {{ @$max_salary }}/{{ $max_sal_mode }}</span> 
                </div>
               <span class="h-20px border-gray-200 border-start mx-4"></span>
                 <div class="d-flex flex-column me-5"> 
                    <span class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="{{$firstJobcities['tooltip']}}">
						{{ ucwords(substr($locations[0], 0, 20)) }} 
						{{ ucwords($firstJobcities['countPlus']) }}
					</span> 
                    <span class="text-muted fw-bold d-block fs-7">
						{{ ucwords(substr($locations[1], 0, 20)) }}
					</span> 
                </div>
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <div class="d-flex flex-column">
                     <span class="text-dark fw-bolder fs-6">
                        {{ @$created_at }}
                    </span> <span class="text-muted fw-bold d-block fs-7">{{ (@$job->total_positions) ? @$job->total_positions.' Opening' : '' }} </span> 
                <!--end::Interview Date & No. of Openings-->
                </div>
                
                @php
                    $rc_path = url('/service_company/apptracker/similar_candidates/' . @$id);
                @endphp

                <!-- <div class="d-flex flex-column">
				<h5 class="d-flex align-items-center text-dark fw-bolder my-1"> <a href="{{ $rc_path }}" class="text-gray-800 text-hover-primary mb-1">Recommend Candidates</a> </h5>
				<span class="text-gray-600">0</span> </div> -->
                <!--end::Recommend Candidates-->

</div>