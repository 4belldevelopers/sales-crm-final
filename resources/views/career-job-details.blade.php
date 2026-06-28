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
    $job_status = GeneralMethod::job_status();


    $qualification  = GeneralMethod::qualification(@$job->qualification);
 
    $employment_type = '';
    $employment_type = $job->employment_type;
    //if(!empty($employment_type)){
    $employment_type = $employmentType[$job->employment_type];
    //}
    $bgTextDecore = $strVal = '';
    $str = trim(@$job->client_name);
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

    $background_image = '';
    if (!empty(@$job->logo_logical_path) && strlen(@$job->logo_logical_path) > 10) {
        $background_image = url('public/' . @$job->logo_logical_path);
    }

    $currency = GeneralMethod::currencySymbole();
    $max_salary = $min_salary = $max_sal_mode = $min_sal_mode = '';
    $max_salary = GeneralMethod::thousandsCurrencyFormat($job->max_salary);
    $min_salary = GeneralMethod::thousandsCurrencyFormat($job->min_salary);

    $min_sal_mode = strtoupper(substr($salaryMode[$job->min_sal_mode], 0, 1));
    $max_sal_mode = strtoupper(substr($salaryMode[$job->max_sal_mode], 0, 1));
    $work_type = 'Work From Office';
    if($job->work_type=='no'){
        $work_type = "Work From Office";
    }elseif($job->work_type=='yes'){
        $work_type = "Remote";
    }elseif($job->work_type=='other'){
        $work_type = "Hybrid";
    }


    $logos = GeneralMethod::openCompanyDetails($job->id,$company_id);

    $logo_path = '';

    $logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
    if (!empty($logos->logo_logical_path)) {
        $logo_path = url('public/' . @$logos->logo_logical_path);
    }
    $logo_path = GeneralMethod::openCompanyDetails($job->id,$company_id);
   
    $company_or_client = GeneralMethod::openCompanyDetailsByClient($job->id,$company_id);
    if($job->self_company==1){
        $logo_path =  @$job->company_logo;
        $company_or_client = @$company_name;
            $description = ucfirst(@$job->company_description);
        $description = ucfirst(@$job->company_description);
    }else{
        if($job->comId == $company_id){
            $description = ucfirst(@$job->client_description);
        }else{
            $description = ucfirst(@$job->company_description);
        }
    
    }
    $logo_path =  $account->logo;
    $company_or_client =$account->company_name;
  
        
  


    $end_date	=	@$job->end_date;
    $date 		=	strtotime($end_date);
    $end_date   =	date('d-M-Y', $date);
			
@endphp


<body>

    <main>

        


        <div class="profile-card-main">
            <div class="job-header-container">
                <div class="job-header">
                    <h1><?php echo ucwords(@$company_or_client); ?></h1>
                    <div class="job-meta">
                         <span>{{@$industries_name}}</span>
                    </div>
                </div>
            </div>

            <div class="company-info-container-main">
               <div class="company-info-container">
                    <div class="company-info">
                        @if (!empty($logo_path)) 
                        <img src="{{ @$logo_path }}" alt="<?php echo ucwords(@$company_or_client); ?>" class="company-logo">
                        @endif
                        <div class="company-details">
                            <div class="job-overview">
                            <h2>{{ ucwords(@$job->title)}}(R-{{@$job->id}})</h2>
                        </div>
                            
                            <div class="job-overview" style="display: ruby;">
                                <div class="overview-item">
                                    <?php echo @$currency[@$job->min_salary_currency]; ?>
                                    <div>
                                        {{-- <p><strong>Offered Salary</strong></p> --}}
                                        <p><?php echo @$min_salary; ?>-<?php echo @$max_salary; ?>/<?php echo @$max_sal_mode; ?></p>
                                    </div>
                                </div>
                                <div class="overview-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div>
                                        {{-- <p><strong>Qualification</strong></p> --}}
                                        <p>{{ @$qualification->name }}</p>
                                    </div>
                                </div>
                                <div class="overview-item">
                                    <i class="fas fa-venus-mars"></i>
                                    <div>
                                        {{-- <p><strong>Industry</strong></p> --}}
                                        <p><?php echo @$job->cat_name; ?></p>
                                    </div>
                                </div>
                                 <div class="overview-item">
                                    <i class="fas fa-briefcase"></i>
                                    <div>
                                        {{-- <p><strong>Industry</strong></p> --}}
                                        <p><?php echo ucwords($employment_type); ?></p>
                                    </div>
                                </div>
                                 <div class="overview-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        {{-- <p><strong>Industry</strong></p> --}}
                                        <p><?php echo ucwords(@$jobCitySelected); ?></p>
                                    </div>
                                </div>
                                 <div class="overview-item">
                                    <i class="far fa-calendar-alt"></i>
                                    <div>
                                        {{-- <p><strong>Industry</strong></p> --}}
                                        <p></i> {{@$end_date}}</p>
                                    </div>
                                </div>
                            </div>


                        </div>
                        
                        
                    </div>
                    <button class="apply-btn"><a href="{{ url('job-apply/' .(@$job->id) . '/'.$company_id) }}" target="_blank" style="color:#fff">Apply for job</a></button>
                </div>
                <div class="skills-list" style="margin-bottom: 7px;">
                        <?php
                            if (!empty($skillArr)) {
                                foreach ($skillArr as $sval) {
                                    echo '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
                                }
                            }
                        ?>
                </div>
   
            </div>
                
                <div class="job-content-container">

                    <div class="job-description">
                        <h2>Job Description</h2>
                        <?php echo @$job->job_description; ?>
                   
                       
                    </div>
                </div>

            
             
            
    </div>
    </main>


    {{-- <footer>
        <p>&copy; 2025 Your Company. All rights reserved.</p>
    </footer> --}}

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



</script>

</body>

</html>
