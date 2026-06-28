@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $salaryMode = GeneralMethod::salaryMode();
    $currencySymbole = GeneralMethod::currencySymbole();
    $feedback_status = GeneralMethod::feedback_status();
    $currency = GeneralMethod::currency();
    $job_status_arr = GeneralMethod::job_status();
    $symbolsValue = GeneralMethod::currencyConverter()['getKey'] ?? [];
    $symbolsValue = !empty($symbolsValue) ? $symbolsValue : [
        0	=>	"INR",
        1	=>	"EUR",
        2	=>	"JPY",
        3	=>	"GBP",
        4	=>	"USD",
        5	=>	"AED"
     ];

@endphp
@if (count($data) > 0)
    @foreach ($data as $rowArr)

    @php
       $row = (object) $rowArr;

       $skillExperiences = DB::table('bench_candidate_skill_experience')
        ->where('candidate_id', $row->id)
        ->get();
        $skillExperiencesCount = DB::table('bench_candidate_skill_experience')
        ->where('candidate_id', $row->id)
        ->count();


       $bgTextDecore = $strVal = '';
        $str = trim(@$row->full_name);
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

        $profile_sidebar_css = 'pbench_show_application_detail';

        $skilltag = '';
        $skilltagclass = '';
        $skillArr = explode(',', $row->skills);

        $percentage =  GeneralMethod::skill_percentage($jobid,$skillArr);
        $viewMore = 0;
        if(!empty($skillExperiencesCount)){
            foreach ($skillExperiences as $key=> $exp) {
                if($key < 3){
                    $skilltag .= '<div class="skill-item">
                            <span class="skill-name">' . ucfirst($exp->skill_key) . '</span>
                            <div class="stars">
                                <svg class="star_skill '.(@$exp->rating >= 1 ? '' : 'empty').'" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill '.(@$exp->rating >= 2 ? '' : 'empty').'" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill '.(@$exp->rating >= 3 ? '' : 'empty').'" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill '.(@$exp->rating >= 4 ? '' : 'empty').'" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <svg class="star_skill '.(@$exp->rating >= 5 ? '' : 'empty').'" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                        </div>';
                }else{
                    $viewMore++;
                }
            }
        }


        $total_exp = $row->total_exp;          
        $experience = trim((string) @$row->total_exp);
        // Keep exact value like 3.0, 5.6, 9.10 — remove only ".00"
        if (strpos($experience, '.') !== false) {
            $parts = explode('.', $experience);
            if (isset($parts[1])) {
                // If decimals are exactly "00", remove them
                if ($parts[1] === '00') {
                    $formattedExperience = $parts[0];
                } else {
                    $formattedExperience = $experience; // keep as-is like 3.0, 5.6, 9.10
                }
            } else {
                $formattedExperience = $experience;
            }
        } else {
            $formattedExperience = $experience;
        }
        $rel_experience = trim((string) @$row->rel_exp);
        // Keep exact value like 3.0, 5.6, 9.10 — remove only ".00"
        if (strpos($rel_experience, '.') !== false) {
            $parts = explode('.', $rel_experience);
            if (isset($parts[1])) {
                // If decimals are exactly "00", remove them
                if ($parts[1] === '00') {
                    $rel_formattedExperience = $parts[0];
                } else {
                    $rel_formattedExperience = $rel_experience; // keep as-is like 3.0, 5.6, 9.10
                }
            } else {
                $rel_formattedExperience = $rel_experience;
            }
        } else {
            $rel_formattedExperience = $rel_experience;
        }

        $npArr = ['Notice period', 'Available from', 'Immediate'];
        $npDate = $row->np_id;
        $modeNp = $row->serving_notice_period_mode;
        if ($row->serving_notice_period_mode == 0) {
            $npDate = $row->np_id . ' Days';
            if ($npDate == 1) {
                $npDate = $row->np_id . ' Day';
            }
        } elseif ($row->serving_notice_period_mode == 1) {
            $originalDate = $row->np_id;
            $npDate = '' . date('d-M-y', strtotime($originalDate));
                $today = date('Y-m-d'); 
                if(date('Y-m-d', strtotime($row->np_id)) < $today){
                    $npDate = "";
                    $modeNp = 2;
                }
        } else {
            $npDate = '';
        }

        $country = '';
        $row_country_name = '';

        if (empty($row->country)) {
            $row_country_name = '----';
        } else {
            if (!empty(@$row->country)) {
                if (is_numeric(@$row->country)) {
                    $country_name_Arr = GeneralMethod::getCountryName(@$row->country);
                    foreach ($country_name_Arr as $cval) {
                        $row_country_name = ucwords($cval['name']);
                    }
                }
            }
        }

        $state = '';
        $row_state_name = '';
        if (empty($row->state)) {
            $row_state_name = '----';
        } else {
            if (@$row->country == 101) {
                $row_state = DB::table('state_list')
                    ->select('*')
                    ->where('id', @$row->state)
                    ->first();
                $row_state_name = @$row_state->state;
            } else {
                $state = @$row->state;
                if (is_numeric(@$row->country) && is_numeric($state)) {
                    if ($state > 0) {
                        $state_name_Arr = GeneralMethod::getStateName(@$row->country, $state);
                        foreach ($state_name_Arr as $sval) {
                            $row_state_name = ucwords($sval['name']);
                        }
                    }
                }
            }
        }

        $city = '';
        $row_city_name = '';

        if (@$row->country == 101) {
            $row_city = DB::table('city_list')
                ->select('*')
                ->where('city_id', @$row->city_id)
                ->first();
            $row_city_name = @$row_city->city;
        } else {
            $row_city_name = @$row->city;
        }
        $row_state_name = @$row->state_name;
        $row_city_name = @$row->city_name;
        $row_country_name = $row->country_name;

        $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                        '';
        $pay_rate_currency = $currencySymbole[@$min_salary_currency];
        $bill_rate_currency = $currencySymbole[@$min_salary_currency];

        $currencymain = GeneralMethod::currency(); // Currency code
        $currency = $currencymain[@$min_salary_currency];
        $convertedAmounEctc = GeneralMethod::conversionRates($currency, $row->ectc_yearly, 0);
        $convertedAmounCtc = GeneralMethod::conversionRates($currency, $row->ctc_yearly, 0);
        $bill_rate_amount = $convertedAmounEctc[$sal_mode];
        $pay_rate_amount = $convertedAmounCtc[$sal_mode];

        $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($pay_rate_amount);
        $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($bill_rate_amount);

        $pay_rate_mode = '';
        $pay_rate_mode = mb_substr($salaryMode[@$sal_mode], 0, 1);
        //$current_ctc_mode		= mb_substr($salaryMode[0], 0, 1); min_salary_currency sal_mode

        $bill_rate_mode = '';
        $bill_rate_mode = mb_substr($salaryMode[@$sal_mode], 0, 1);

        $company_id 			=	auth()->user()->company_id;
        $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);
            
        $currency_code = $symbolsValue[$min_salary_currency] ?? 'INR';
        
        $payFormatViewSalary = GeneralMethod::formatViewSalary($row->pay_rate_amount,$currencyBenchshowMode->currency_code,$currencyBenchshowMode->show_mode);
        $billFormatViewSalary = GeneralMethod::formatViewSalary($row->bill_rate_amount,$currency_code,$sal_mode);
        
        $pay_rate_currency = $currencySymbole[$payFormatViewSalary->currency];
        $pay_rate_mode = $salaryMode[$payFormatViewSalary->mode];
        $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency];
        $bill_rate_mode = $salaryMode[$sal_mode];

        //$current_ectc_mode		= mb_substr($salaryMode[0], 0, 1);
        $dnone = '';
        if ($row->condidate_type == 'bench_condidate') {
            $dnone = 'd-none';
        }
        $outsourcing_type = $row->outsourcing_type == 1 ? 'Fulltime' : 'Contract';
        $comission_type = $row->comission_type == 1 ? 'Free' : 'Paid ';

        $companyDetailsGet = GeneralMethod::companyDetailsGet($row->company_id);




    @endphp

       <tr>
            <td>
                <div class="candidate-cell {{  $profile_sidebar_css }}" data-row-id="{{ @$row->id  }}">
                    <div class="candidate-avatar {{ @$bgTextDecore }}">{{ strtoupper(@$strVal) }}S</div>
                    <div class="candidate-info">
                        <div class="candidate-name">{{ ucwords(mb_substr(@$row->full_name, 0, 22)) }}</div>
                        <div class="candidate-role">{{ ucwords(mb_substr(@$row->current_designation, 0, 22)) }}</div>
                    </div>
                </div>
            </td>
            <td>
                <div class="experience-main">{{ $formattedExperience }} yrs</div>
                <div class="experience-sub">{{ $rel_formattedExperience }} yrs </div>
            </td>
            <td>
                <div class="skills-list">
                    {!! $skilltag !!}
                    @if($viewMore)
                     <span class="more-skills {{  $profile_sidebar_css }}" data-row-id="{{ @$row->id  }}">+{{ $viewMore }} more</span>
                    @endif
                </div>
            </td>
            <td>
                <div class="availability-badge avail-immediate">
                    <span class="avail-dot"></span>
                    {{ $npArr[$modeNp] }}
                </div>
                @if($npDate)
                    <div class="verified-text">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ $npDate }} 
                    </div>
                @endif
            </td>
            <td>
                <div class="submissions-list">
                    <div class="submission-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>{{ucwords(mb_substr($row_city_name, 0, 10))}}, {{ ucfirst(mb_substr($row_state_name, 0, 5)) }}, {{ strtoupper(mb_substr($row_country_name, 0, 2)) }}
                    </div>
                    <div class="submission-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Onsite/Remote/Hybrid
                    </div>
                    <div class="submission-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        {{ @$row->qualification_name }}
                    </div>
                </div>
            </td>
            <td>
                @php
                        $billRates = GeneralMethod::formatAmount( $billFormatViewSalary->amount);

                @endphp
                <div class="salary-amount">{!! @$bill_rate_currency  !!} {{  $billRates }} /{{ mb_substr($bill_rate_mode,0,1)  }}</div>
                <div class="submission-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> {{ $outsourcing_type }}
                </div>
                <div class="submission-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    {{ $comission_type }}
                </div>
            </td>
            <td style="text-align: center;">
                @php
                    $recCounter = 0;
                    $recCounter = DB::table('rec_candidate_assign')
                        ->select('id')
                        ->where('source_from_id', @$row->id)
                        ->whereIn('source_from_type', [1, 2])
                        ->where('source_jobid', @$jobid)
                        ->count();
                        
                        $existingCandidatesQuery = DB::table('add_job_wise_condidates')
                            ->select('job_application_id')
                            ->where('add_job_wise_condidates.job_id', '=', $jobid)
                            ->where('add_job_wise_condidates.job_application_id', '=', $row->id)->count();

                    if($row->status==2){
                        if ($row->countforadd >= 3) {
                            $html = '<a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a>';
                        } else {
                        $html ='<a href="javascript:void(0);" class="btn btn-sm btn-primary openSideBar" data-row-source_from_type="2"  data-row-id="' .@$row->id . '" data-row-jobid="' . @$jobid .'">Add to job</a>';
                        }
                    }else{
                        $html = '<a href="javascript:void(0);" class="btn btn-sm btn-danger fs-6">Expired</a>';
                    }
                    echo '<div class="d-flex flex-column" style="margin-bottom: 8px;"> '.$html.' </div>';
                @endphp
                
                <a href="javascript:;" class="view-link {{ $profile_sidebar_css }}" data-row-id="{{ $row->id }}">View Profile</a>

                {{-- <button class="btn btn-primary btn-dropdown">
                    Add to Job
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button> --}}
            </td>
        </tr>

        
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="9" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
