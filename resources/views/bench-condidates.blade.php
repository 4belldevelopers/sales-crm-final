<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bench Candidates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('demo/css/style-bench-candidates.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link rel="canonical" href="" />
    <link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/plugins/global/plugins.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/css/style.bundle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.0/mammoth.browser.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
         html {
                scroll-behavior: auto !important; /* disable smooth hash scrolling */
            }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesCopy {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesCopy {
                width: 1200px;
            }
        }
        @media only screen and (min-width: 1024px) and (max-width: 1440px) {
            #kt_activitiesBenchApplication {
                width: 1025px;
            }
        }

        @media only screen and (min-width: 1440px) and (max-width: 2560px) {
            #kt_activitiesBenchApplication {
                width: 1200px;
            }
        }


        .custom-pagination {
            display: flex;
            gap: 5px;
            justify-content: flex-end; /* Align pagination to the left */
            margin-top: 15px;
        }

    .custom-pagination .page-link {
        padding: 0.375rem 0.75rem;
        /* border: 1px solid #ddd; */
        cursor: pointer;
        /* background-color: #f8f9fa; */
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 0.475rem;
        height: 2.5rem;
        min-width: 2.5rem;
        font-weight: 500;
        font-size: 1.075rem;
        /* border-radius: 5px; */
    }

    .custom-pagination .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination-info {
        text-align: left; /* Align info to the left */
        margin-bottom: 10px;
        font-weight: bold;
    }

    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end; /* Align entire pagination block to the left */
        width: 100%;
    }
    .previous_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .next_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }
    .active_disabled{
        pointer-events: none; /* Disable clicking */
        opacity: 0.5; /* Make it visually look disabled */
        cursor: not-allowed; /* Change the cursor to indicate it's disabled */
    }

        
    </style>
</head>

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


<body>
    {{-- <header>
        <div class="logo">
            <a href="#">Your Logo</a>
        </div>
        <nav>
            <ul>
                <li><a href="#">Find Talent</a></li>
                <li><a href="#">Find Agency</a></li>
                <li><a href="#">Find Client</a></li>
                <li><a href="#">ATS</a></li>
                <li><a href="#">Pricing</a></li>
            </ul>
        </nav>
    </header> --}}

    <main>
        <div class="profile-card">
            <div class="profile-left">
                <div class="profile-header">

                    @php $logo_path				= ''; @endphp
                    @if(!empty(@$account->logo) && strlen(@$account->logo)>10)
                            @php //$logo_path		=  url('public/user-uploads/company-logo/'.@$account->logo); @endphp
                                @php $logo_path		=  @$account->logo; 
                        
                                @endphp 
                    @endif
                    @php
                        $bgTextDecore = $strVal = '';
                        $str = trim($account->company_name);
                        $str = $strVal = strtolower($str[0]);
                        // dd( $salary_mode[$contractStatus->sal_mode],$currencySymbole[$contractStatus->salary_currency]);

                    @endphp
                    {{-- <span class="verified-badge">✔</span> --}}
                    @if (!empty($logo_path)) 
                    <img src="{{ @$logo_path }}" alt="image" class="profile-pic">
                    @else
                    <div class="symbol-label fs-3 {{ @$bgTextDecore}} border-radius-5"><span>{{ ucwords(@$strVal)}}</span></div>
                    @endif
                    <div class="profile-info">
                        <h2 class="profile-name">{{ ucwords(@$account->company_name)}}</h2>
                        <p class="profile-rate">{{@$industries_name}} </p>
                    </div>
                </div>
                {{-- <div class="profile-stats">
                    <div class="stat">
                        <span class="star-icon">★</span> 5.0 (3 reviews)
                    </div>
                    <div class="stat">
                        <span class="eye-icon">👁</span> 4,715 views
                    </div>
                </div> --}}
                <div class="profile-details-list">
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span class="detail-label">Location</span>
                        <span class="detail-value">{{ ucwords($company_city_name) }} {{ ' , ' }} {{ ucwords($company_country_name) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">👤</span>
                        <span class="detail-label">Founded</span>
                        <span class="detail-value">{{ @$account->founded }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🌐</span>
                        <span class="detail-label">Website</span>
                        <span class="detail-value">{{ @$account->website }}</span>
                    </div>
                    @if(@$fullTimeStatus->status==1)
                        <div class="detail-item">
                            <span class="detail-icon">✍</span>
                            <span class="detail-label">FullTime Rate</span>
                            <span class="detail-value">{{@$fullTimeStatusRate}}% / Hire</span>
                        </div>
                    @endif
                     @if(@$contractStatus->status==1)
                    <div class="detail-item">
                        <span class="detail-icon">✍</span>
                        <span class="detail-label">Contract Rate</span>
                        <span class="detail-value">{!!$currencySymbole[(@$contractStatus->salary_currency) ? $contractStatus->salary_currency : 0]!!} {{@$contractStatusRate}} /{{$salary_mode[(@$contractStatus->sal_mode) ? @$contractStatus->sal_mode : 0]}}</span>
                    </div>
                    @endif

                </div>
                 @php
                          $recommendateRow = DB::table('recommendation_relation')
                            ->select(
                                'recommendation_relation.id',
                                'recommendation_relation.from_company_id',
                                'recommendation_relation.from_status',
                                'recommendation_relation.to_status',
                                'recommendation_relation.final_status',
                            )->where(function ($query) use ($company_id, $to_company_id) {
                                $query->where(function ($q) use ($company_id, $to_company_id) {
                                    $q->where('recommendation_relation.from_company_id', $company_id)
                                    ->where('recommendation_relation.to_company_id', $to_company_id);
                                })
                                ->orWhere(function ($q) use ($company_id, $to_company_id) {
                                    $q->where('recommendation_relation.from_company_id', $to_company_id)
                                    ->where('recommendation_relation.to_company_id', $company_id);
                                });
                            })
                            ->first();
                 @endphp
           
                <div class="profile-actions">
                    @if($recommendateRow)
                     @if($recommendateRow->final_status == 0 || $recommendateRow->final_status == '')
                               <button class="book-meeting-btn send_job_recommanded_request text-primary" data-row-event="-1" id="sp_list_" data-row-id="{{ $to_company_id}}" style="width:280px">Cancel Request</button>
                    @else
                         <button class="book-meeting-btn btn-secondary" id="sp_list_0" data-row-fromcompany="{{$company_id}}" data-row-tocompany="{{$to_company_id}}" style="width:280px">Connected</button>                               
                        
                    @endif
                    @else
                      @if($to_company_id != $company_id)
                        <button class="book-meeting-btn send_job_recommanded_request"  data-row-jobid="" data-row-event="1" id="sp_list_{{$to_company_id}}" data-boosterCom="0" data-boost-ids="" data-row-id="{{$to_company_id}}" style="width:280px">Send Request</button>
                      @endif
                    @endif
                </div>
          

            </div>
            <div class="profile-right">
                <div class="profile-about">
                    <h3>About</h3>
                    {!! @$account->description !!}
                </div>
                <div class="profile-skills">
                    <h3>Skills</h3>
                    <div class="skills-list">
                         @if(!empty($client->tags))
                            @php $tags	=	explode(',', $client->tags); @endphp 
                            @foreach($tags as $key=>$val) 
                                {!! '<span class="skill-tag">'.ucwords(@$val).'</span>' !!}
                            @endforeach
                        @endif	
                    </div>
                </div>
            </div>
        </div>
       
        <div class="candidate-section" id="candidate-section">
            <div class="candidate-list">
                 <form method="GET" action="">
                <div class="search-and-filter">
                  
                    <input type="text" name="cus_search" value="{{$searchTerms}}" class="search-input" placeholder="Search by keyword..." 
                        value="{{ request('keyword') }}">
                    
                    <select class="filter-select" name="sortingfilter" id="sortingfilter" onchange="this.form.submit()">
                        <option value="Show All" {{ $valCheck == 'Show All' ? 'selected' : '' }}>Sort</option>
                        <option value="1" {{ $valCheck == '1' ? 'selected' : '' }}>Newest to Oldest (Upload Date)</option>
                        <option value="2" {{ $valCheck == '2' ? 'selected' : '' }}>Oldest to Newest (Upload Date)</option>
                        <option value="9" {{ $valCheck == '9' ? 'selected' : '' }}>Notice Period (Immediate)</option>
                        <option value="5" {{ $valCheck == '5' ? 'selected' : '' }}>Low to High (Notice Period)</option>
                        <option value="6" {{ $valCheck == '6' ? 'selected' : '' }}>High to Low (Notice Period)</option>
                        <option value="7" {{ $valCheck == '7' ? 'selected' : '' }}>High to Low (Experience)</option>
                        <option value="8" {{ $valCheck == '8' ? 'selected' : '' }}>Low to High (Experience)</option>
                    </select>

                    <button type="submit" class="search-button">Search</button>
                
                </div>
                </form>
                <h2>Bench Candidate List ({{$total}})</h2>
            
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-custom-class">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th>Name</th>
                            <th>Exp</th>
                            <th>Billing(INR)</th>
                            <th>Billing(USD)</th>
                            <th>Education</th>
                            <th>Availability</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($data) > 0)
                            @foreach ($data as $rowArr)
                                    @php
                                    $row = (object) $rowArr;
                                    //dd($row);
                                    $jobs = @$row->jobs;
                                    $masterStatusIds = array_column($jobs, 'master_status_id');

                                    // Count how many times each value appears
                                    $counts = array_count_values($masterStatusIds);
                                    $countForZero = $counts[0] ?? 0;
                                    $row->Sourcing = $counts[1] ?? 0;
                                    $row->Screening = $counts[2] ?? 0;
                                    $row->Interview = $counts[3] ?? 0;
                                    $row->Negotiation = $counts[4] ?? 0;
                                    $row->Offered = $counts[5] ?? 0;
                                    $row->Joined = $counts[6] ?? 0;
                                    $row->Rejected = $counts[7] ?? 0;
                                    $row->InReview = $counts[8] ?? 0;
                                

                                    @endphp

                                <tr style="height: 102px; position: relative;">              
                                    <td>
                                        @php
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
                                            $profile_sidebar_css = 'bench_show_application_detail';

                                            $skilltag = '';
                                            $skilltagclass = '';
                                            $skillArr = explode(',', $row->skills);
                                            if (empty($limit)) {
                                                if (count($skillArr) > 0) {
                                                    foreach ($skillArr as $sval) {
                                                        $skilltag .= '<span class="badge badge-light my-1">' . ucfirst($sval) . '</span>  ';
                                                    }
                                                }
                                            } else {
                                                $skilltagclass = 'd-none';
                                            }
                                            $viewCount = DB::table('bench_count_view_download')
                                                ->where('type', '=', 1)
                                                ->where('status', '=', 0)
                                                ->where('candidate_id', '=', $row->id)
                                                ->count();
                                            $downloadCount = DB::table('bench_count_view_download')
                                                ->where('type', '=', 2)
                                                ->where('status', '=', 0)
                                                ->where('candidate_id', '=', $row->id)
                                                ->count();
                                                $logo_path = url('public/award.png');
                                                $skillArr = DB::table('benchboostkeywordcandidates')->where('benchboostkeywordcandidates.candidate_id', @$row->id)->pluck('skill_name')->toArray();
                                                $award = '';
                                                if(@$row->ads_switch=='Yes'){
                                                    $award = '<sup><img src="' .
                                                    str_replace('index.php', '', @$logo_path) .
                                                    '" alt="Company Logo" class="border-radius-5"></sup>';
                                                }

                                            echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                            <a href="javascript:void(0);"   data-row-id="' .
                                                @$row->id .
                                                '" class="' .
                                                $profile_sidebar_css .
                                                '">
                                                <div class="symbol-label ' .
                                                @$bgTextDecore .
                                                ' border-radius-5">' .
                                                strtoupper(@$strVal) .
                                                '</div>
                                            </a>
                                        </div>                  
                                        <div class="d-flex flex-column">
                                        <a href="javascript:void(0);"  data-row-id="' .
                                                @$row->id .
                                                '"  class="text-dark fw-bolder text-hover-primary fs-6 ' .
                                                $profile_sidebar_css .
                                                ' " data-toggle="tooltip" title="' .
                                                ucwords(@$row->full_name) .
                                                '">' .
                                                ucwords(mb_substr(@$row->full_name, 0, 22)) .
                                                ''.$award.'</a>
                                        <a href="javascript:void(0);"  data-row-id="' .
                                                @$row->id .
                                                '"  class="text-muted fw-bold d-block fs-7 text-hover-primary ' .
                                                $profile_sidebar_css .
                                                ' " data-toggle="tooltip" title="' .
                                                ucwords(@$row->current_designation) .
                                                '">' .
                                                ucwords(mb_substr(@$row->current_designation, 0, 22)) .
                                                '</a>
                                        </div>
                                        <div class="' .
                                                $skilltagclass .
                                                '" style="position: absolute; bottom: 0;">
                                                            <span class="badge badge-light-success my-1">Skills:</span>
                                                        
                                                        ' .
                                                $skilltag .
                                                '
                                                        
                                                            </div>

                                        ';
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            $total_exp = $row->total_exp;
                                            if($total_exp == '0.'){
                                            $total_exp = str_replace('.','',$total_exp);
                                            }
                                            $rel_exp = $row->rel_exp;
                                            if($total_exp == '0.'){
                                            $rel_exp = str_replace('.','',$rel_exp);
                                            }
                                            if($total_exp==0 && $rel_exp==0){
                                                $rel_exp = ''; 
                                            }
                                            $experience = floatval(@$row->total_exp); // Ensure it's a valid float
                                            // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                                            $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;

                                            $rel_experience = floatval(@$row->rel_exp); // Ensure it's a valid float
                                            // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                                            $rel_formattedExperience = ($rel_experience == intval($rel_experience)) ? intval($rel_experience) : $rel_experience;

                                            // echo $formattedExperience . ' Y';

                                                echo '<div class="d-flex flex-column">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .$formattedExperience . ' Y' . 
                                                '</a>
                                                <span class="text-muted fw-bold d-block fs-7">' . $rel_formattedExperience . ' Y'. 
                                                '</span>
                                            </div>';
                                        @endphp

                                    </td>
                                    <td>
                                        @php
                                            $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                                                '';
                                            $pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
                                            $bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
                                            $pay_rate_mode = $salaryMode[@$row->pay_rate_mode];
                                            $bill_rate_mode = $salaryMode[@$row->bill_rate_mode];
                                            
                                            $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
                                            $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);

                                            $billFormatViewMonth = GeneralMethod::formatViewSalary($row->ectc_yearly,"INR",1);
                                            $bill_rate_modeMo = $salaryMode[$billFormatViewMonth->mode];

                                            echo '<div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                                                $bill_rate_currency .
                                                @$bill_rate_amount .
                                                '/' .
                                                $bill_rate_mode .
                                                '</a>
                                                <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" >' .
                                                $bill_rate_currency .
                                                @$billFormatViewMonth->amount  .
                                                '/' .
                                                mb_substr($bill_rate_modeMo,0,1) .
                                                '</span>
                                            
                                        </div>';
                                        @endphp
                                    </td>
                                    
                                    <td>
                                        @php
                                            $pay_rate_currency = $bill_rate_currency = $pay_rate_amount = $bill_rate_amount = $pay_rate_mode = $bill_rate_mode =
                                                '';
                                            $pay_rate_currency = $currencySymbole[@$row->pay_rate_currency];
                                            $bill_rate_currency = $currencySymbole[@$row->bill_rate_currency];
                                            $pay_rate_mode = $salaryMode[@$row->pay_rate_mode];
                                            $bill_rate_mode = $salaryMode[@$row->bill_rate_mode];
                                            $pay_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->pay_rate_amount);
                                            $bill_rate_amount = GeneralMethod::thousandsCurrencyFormat($row->bill_rate_amount);
                                            
                                            $currencyBenchshowMode = GeneralMethod::getCompanyCurrencyBenchshowMode($company_id);

                                            $billFormatViewSalary = GeneralMethod::formatViewSalary($row->ectc_yearly,'USD',$currencyBenchshowMode->show_mode);
                                            $billFormatViewMonth = GeneralMethod::formatViewSalary($row->ectc_yearly,'USD',1);
                                            $bill_rate_currency = $currencySymbole[$billFormatViewSalary->currency];
                                            $bill_rate_mode = $salaryMode[$billFormatViewSalary->mode];
                                            $bill_rate_modeMo = $salaryMode[$billFormatViewMonth->mode];

                                            echo '<div class="d-flex flex-column">
                                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                                                $bill_rate_currency .
                                                @$billFormatViewSalary->amount  .
                                                '/' .
                                                mb_substr($bill_rate_mode,0,1) .
                                                '</a>
                                                <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" >' .
                                                $bill_rate_currency .
                                                @$billFormatViewMonth->amount  .
                                                '/' .
                                                mb_substr($bill_rate_modeMo,0,1) .
                                                '</span> 
                                            
                                        </div>';
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                        $created_at = '';
                                        $created_at = GeneralMethod::ymdTodmyNotTime($row->created_at);
                                            echo ' <div class="d-flex flex-column">
                                                <a href="javascript:void(0);" class="text-dark fw-bolder fs-6" data-toggle="tooltip" title="' .
                                                @$row->qualification_name .
                                                '" style="text-transform: uppercase;!important">' .
                                                @$row->qualification_name .
                                                '</a>
                                                <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' .
                                                 $created_at .
                                                '">' .
                                                $created_at.
                                                '</span> 
                                                </div>';
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            $npArr = ['Notice period', 'Available from', 'Immediate'];
                                            $npDate = $row->np_id;
                                            if ($row->serving_notice_period_mode == 0) {
                                                $npDate = $row->np_id == 1 ? $row->np_id . ' Day' : $row->np_id . ' Days';
                                            } elseif ($row->serving_notice_period_mode == 1) {
                                                $originalDate = $row->np_id;
                                                $npDate = date('d-M-y', strtotime($originalDate));
                                                $today = date('Y-m-d'); 
                                                //if(date('Y-m-d', strtotime($row->np_id)) < $today){
                                               //     $npDate = "Immediate";
                                               // }
                                            } elseif ($row->serving_notice_period_mode == 2) {
                                                $originalDate = $row->np_id;
                                                $npDate = date('d-M-y', strtotime($originalDate));
                                                $today = date('Y-m-d'); 
                                                if(date('Y-m-d', strtotime($row->np_id)) < $today){
                                                    $npDate = "";
                                                }
                                            } else {
                                                $npDate = '';
                                            }
                                            echo '<div class="d-flex flex-column">
                                            <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">' .
                                                $npArr[$row->serving_notice_period_mode] .
                                                '</a> 
                                            <span class="text-muted fw-bold text-muted d-block fs-7" data-toggle="tooltip" title="' .
                                                $row->np_id .
                                                '">' .
                                                $npDate .
                                                '</span>
                                            </div>';
                                        @endphp
                                    </td>
                                    <td>
                                        @php

                                            $country = $state = $row_state_name = $row_country_name = $city = $row_city_name = '';
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

                                            if (empty($row->state)) {
                                                $row_state_name = '----';
                                            } else {
                                                $row_state = DB::table('state_list')
                                                    ->select('*')
                                                    ->where('id', @$row->state)
                                                    ->first();
                                                $row_state_name = @$row_state->state;
                                            }

                                            $row_city = DB::table('city_list')
                                                ->select('*')
                                                ->where('city_id', @$row->city_id)
                                                ->first();
                                            $row_city_name = @$row_city->city;
                                            $row_city_name = @$row->city_name;
                                            $row_state_name = @$row->state_name;
                                            $row_country_name = @$row->country_name;

                                            echo '<div class="d-flex flex-column">
                                                <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="' .
                                                ucwords($row_city_name) .
                                                '">' .
                                                ucwords(mb_substr($row_city_name, 0, 20)) .
                                                '</a>
                                                <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="' .
                                                ucwords($row_state_name) .
                                                ' | ' .
                                                strtoupper($row_country_name) .
                                                '">' .
                                                ucfirst(mb_substr($row_state_name, 0, 5)) .
                                                ' | ' .
                                                strtoupper(mb_substr($row_country_name, 0, 2)) .
                                                '</span>
                                            </div>';
                                        @endphp
                                    </td>
                                    <td>
                                        @php

                                            $subS = '';
                                            if ($row->master_status_name == 'Bench') {
                                                $subS = 'Live';
                                                if ($row->superadminstatus == 0) {
                                                    $subS = 'Pending';
                                                }
                                            }
                                             $result = GeneralMethod::bench_status_subline($row->status);
					                         $subS = ($result->name) ?? '';
                                            echo '<div class="d-flex flex-column">
                                            <a href="javascript:void(0);" data-row-id="' .
                                                $row->id .
                                                '" class="fw-bold fs-6  stage_sub_stage ' .
                                                $row->css_style .
                                                '" data-toggle="modal" data-target="#stage_sub_stage" >
                                                ' .
                                                $row->master_status_name .
                                                '</a>
                                            <span class="fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="10 Days to Expire">' .
                                                $subS .
                                                '</span>
                                            </div>';
                                        @endphp
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column"> 
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary openSideBar" data-row-source_from_type="2" data-row-id="{{$row->id}}" data-row-jobid="">Add to job</a> 
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                        <tr style="height: 102px; position: relative; background: #fffcfc7a;">

                            <td colspan="8" style="text-align: center; color: black;">
                                No data available in table
                            </td>

                        </tr>
                    @endif

                    </tbody>
                </table>
                <?php
                    $totalPages =$last_page;
                    $currentPage =$current_page;
                ?>

                <div class="pagination-wrapper">
                    <div class="custom-pagination">
                    <!-- Previous Button -->
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="page-link">Previous</a>
                    <?php else: ?>
                        <span class="page-link disabled">Previous</span>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <a href="?page=<?= $i ?>" class="page-link active"><?= $i ?></a>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>" class="page-link"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Next Button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="page-link">Next</a>
                    <?php else: ?>
                        <span class="page-link disabled">Next</span>
                    <?php endif; ?>
                </div>
            </div>

            </div>
        </div>
    </main>

    
    <div id="kt_activitiesCopy_toggle"></div>
    <div id="kt_activitiesCopy" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width=""
        data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesCopy_toggle"
        data-kt-drawer-close=".kt_activitiesCopy_close">
        <div class="card shadow-none rounded-0 w-100">
            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <h3 class="card-title fw-bolder text-dark">Select the job checkbox, then click "Save" to add the candidate</h3>
                    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
                </div>
                <div class="card-toolbar">

                  
                    <div class="">
                        <button type="button" id="submit-frm-btn" class="btn btn-primary copy_current_selected_applicant d-none"> 
                            <span class="indicator-label">Save to Jobs</span> 
                            <span class="indicator-progress">
                                Please wait...
                            </span> 
                        </button>
                    </div>


                    <button type="button"
                        class="btn btn-sm btn-icon btn-active-light-primary me-n5 kt_activitiesCopy_close"
                        id="kt_activitiesCopy_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card-body position-relative px-0 py-0" id="application_copy_body_content"></div>
        </div>
    </div>
        
    <div id="kt_activitiesBenchApplication_toggle"></div>
    <div id="kt_activitiesBenchApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesBenchApplication" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesBenchApplication_toggle" data-kt-drawer-close="#kt_activitiesBenchApplication_close">
    <div class="card shadow-none rounded-0 w-100" id="bench_application_show_right_bar" ></div>
    </div>


    <footer>
        <p>&copy; 2025 Your Company. All rights reserved.</p>
    </footer>

    <script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/scripts.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/widgets.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('demo/js/custom/apps/chat/chat.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}{{ TM_VERSION }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/intlTelInput.min.js"></script>
<script src="{{ asset('front/assets/js/number-to-words.js') }}"></script>
<script src="{{ asset('front/assets/js/countryDialCodes.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>

  function showToastr(type, message, title) {
    switch(type) {
        case 'success':
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.success(message, title);
            break;

        case 'error':
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.error(message, title);
            break;

        case 'info':
            toastr.options.positionClass = 'toast-top-left';
            toastr.info(message, title);
            break;

        case 'warning':
            toastr.options.positionClass = 'toast-bottom-left';
            toastr.warning(message, title);
            break;

        default:
            toastr.options.positionClass = 'toast-top-center';
            toastr.info("Unknown status", "Notice");
    }
}


        $(document).on('click', '.openSideBar', function() {
            $('#kt_activitiesCopy_toggle').trigger('click');
            var id = 1;
            var url = "{{ route('service_company.cvtracker.talent-add', ':id') }}";
            url = url.replace(':id', id);
            var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
            var token = "{{ csrf_token() }}";
            var idid = $(this).data('row-id');
            var currentUrl = window.location.href;
            // alert(idid);
            var arr = [idid];
           
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': token,
                    '_method': 'POST',
                    'arr': arr
                },
                beforeSend: function() {
                    //$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
                },
                success: function(response) {
                    if (response.status == "block") {
                        Swal.fire("Warning", 'Bench candidate cannot be copy ', "warning").then(
                            function() {
                                $("#kt_activitiesCopy_close").click();
                            });
                        return false;
                    }

                    if (response.status == "success") {
                        $('#application_copy_body_content').html(response.view);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                       Swal.fire({
                            title: "Session Expired",
                            text: "Please login again.",
                            icon: "error",
                            allowOutsideClick: true, // ⛔ disable outside click
                            allowEscapeKey: false,    // ⛔ disable Esc key
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ url('login') }}?url=" + encodeURIComponent(currentUrl);
                            }
                            $('#kt_activitiesCopy_close').click();
                        });

                    } else {
                        Swal.fire("Error", "Something went wrong. Try again.", "error");
                    }
                }
            });
        });

        $(document).on('click','.bench_show_application_detail',function(){
			//  table.on('click', '.bench_show_application_detail', function (){
                        var company_id = "{{ $company_id }}";
						$('#kt_activitiesBenchApplication_toggle').trigger('click');
						var id    		=	$(this).data('row-id');
						var url 		=	"{{ route('get.benchCandidateDetails',':id') }}";
						url     		=	url.replace(':id', id);		
						var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
						$.ajax({
										type  		:	'GET',
										url   		:	url,
                                        data : {
                                            company_id : company_id   // ✅ PASS HERE
                                        },
										beforeSend	:	function() {
													  				$('#bench_application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
														},
										success 	:	function (response) {
																			$('#bench_application_show_right_bar').html(response.view);
														}
								});
		});

        $(document).on('change', '.chk_copy_item', function () {
           const isChecked = $(this).is(':checked');
           const checkedCount = $('.chk_copy_item:checked').length;

            // Show individual toast message only for this one checkbox
           if (isChecked) {
                showToastr('success', 'Job selected successfully!', 'Selected');
            } else {
                showToastr('error', 'Job removed successfully!', 'Removed');
            }

            // Show/hide the bulk action button based on overall selection
            if (checkedCount > 0) {
                $('.copy_current_selected_applicant').removeClass("d-none");
            } else {
                $('.copy_current_selected_applicant').addClass("d-none");
            }
        });

    $('body').off('click', '.send_job_recommanded_request').on('click', '.send_job_recommanded_request', function(event) {
        event.preventDefault();

        var $this = $(this);
        // Disable the button to prevent multiple clicks 

        var from_company_id = $this.data('row-id');
        var current_jobid = $this.data('row-jobid');
        var event_id = $this.data('row-event');
        var matchcount = $this.data('matchcount');
        var id = from_company_id;
        var kt_tagify_location = $('#kt_tagify_location').val();
        var page  = $('.active_disabled ').data('page');
        var boostercom = $(this).data('boostercom');
        var boostids = '';  

        var currentUrl = window.location.href;
        if(id==0){
            Swal.fire({
                        title: "Session Expired",
                        text: "Please login again.",
                        icon: "error",
                        allowOutsideClick: true, // ⛔ disable outside click
                        allowEscapeKey: false,    // ⛔ disable Esc key
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ url('login') }}?url=" + encodeURIComponent(currentUrl);
                        }
                        $('#kt_activitiesCopy_close').click();
            });
            return false;
        }

        
        $this.prop('disabled', true);

        if(boostercom==1){
            var boostids =  $(this).data('boost-ids');
        }


        var url = "{{ route('service_company.agency.recommanded_request', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            type: 'GET',
            url: url,
            data: {
                'from_company_id': from_company_id,
                'event_id': event_id,
                'current_jobid': current_jobid,
                'boostercom': boostercom,
                'boostids': boostids,
                'matchcount': matchcount,
                'kt_tagify_location': kt_tagify_location
            },
            success: function(response) {
                if (response.status === "success") {
                    if(event_id==1){
                        $('#sp_list_' + id).removeClass('btn-light-primary send_job_recommanded_request send_only_job_invitation')
                                      .addClass('btn-light-danger')
                                      .text('Cancel Request');
                    }else{
                        $('#sp_list_' + id).removeClass('btn-light-primary send_job_recommanded_request send_only_job_invitation')
                                      .addClass('btn-light-success')
                                      .text('Request Sent');
                    }

                    Swal.fire("Success", response.message, "success");
                    location.reload();
                }else if (response.status === "warning") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "info") {
                    Swal.fire("Warning", response.message, "warning");
                }else if (response.status === "closeJob") {
                    Swal.fire("Warning", response.message, "warning");
                }
                // Re-enable the button after processing
                $this.prop('disabled', false);
            },
            error: function() {
                // Re-enable the button if there's an error
                $this.prop('disabled', false);
            }
        });
    });
        

    
        document.addEventListener('DOMContentLoaded', function() {
            const searchButton = document.querySelector('.search-button');
            const candidateSection = document.getElementById('candidate-section');
            const filterSelect = document.querySelector('.filter-select');
            const searchInputt= document.querySelector('.search-input');

            // Scroll on search button click
            searchButton.addEventListener('click', function() {
                candidateSection.scrollIntoView({ behavior: 'smooth' });
            });

            // Scroll on page load if a filter is selected
            if (filterSelect.value !== '') {
                candidateSection.scrollIntoView({ behavior: 'smooth' });
            }
            if (searchInputt.value !== '') {
                candidateSection.scrollIntoView({ behavior: 'smooth' });
            }
        });

        

    </script>

</body>

</html>
