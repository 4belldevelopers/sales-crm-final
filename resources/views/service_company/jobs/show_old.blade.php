<?php
use App\Helper\GeneralMethod;
use Carbon\Carbon;
// dd("jd");
?>
{{-- <style>
    .text-justify {
        text-align: justify !important;
    }

    .symbol1>img,
    .symbol1 .symbol-label1 {
        width: 32px !important;
        height: 32px !important;
    }

    .table> :not(caption)>*>* {
        padding: 0rem 0.4rem;
    }
</style> --}}
<?php
 $currency 				=	GeneralMethod::currency();
 $salaryMode 			=	GeneralMethod::salaryMode();
 $qualification  = GeneralMethod::qualification(@$job->qualification);
                                    
 $feedback_status = GeneralMethod::feedback_status();
 $available_status = GeneralMethod::available_status();
 
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


$logos = GeneralMethod::companyDetails($job->id);

$logo_path = '';

$logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
if (!empty($logos->logo_logical_path)) {
    $logo_path = url('public/' . @$logos->logo_logical_path);
}
$logo_path = GeneralMethod::companyDetails($job->id);
if(@$tabshow){
    $company_or_client = GeneralMethod::companyDetailsByClient($job->id);
    
    if($job->self_company==1){
      $description = ucfirst(@$job->company_description);
    }else{
        if($job->comId == auth()->user()->company_id){
            $description = ucfirst(@$job->client_description);
        }else{
            $description = ucfirst(@$job->company_description);
        }
      
    }
}else{
    $company_or_client = GeneralMethod::companyDetailsByClient($job->id);
    $logo_path =  @$job->company_logo;
    // $company_or_client = @$company_name;
    $description = ucfirst(@$job->company_description);
}
// dd($company_or_client);

if(@$jobfindhide==false){
      $logo_path = '';
}


$end_date	=	@$job->end_date;
$date 		=	strtotime($end_date);
$end_date   =	date('d-M-Y', $date);
			
?>
  @php
      
        $startOfMonthcomman = Carbon::now()->startOfMonth()->subMonths(5);
        $endOfMonthcomman =  Carbon::now()->endOfMonth();
        
				$endDate = Carbon::today()->endOfDay();
				$startDate = Carbon::today()->subDays(6)->startOfDay();

  @endphp

  <style>
.question-list .list-group-item {
    border: 0;
    border-bottom: 1px solid #f0f0f0;
}

.question-item:hover {
    background-color: #fafafa;
}
.question-item {
    width: 1044px;
}

.toggle-question:hover {
    background-color: #eef5ff;
    border-color: #cfe2ff;
}

.remove-question:hover {
    background-color: #fff0f0;
    border-color: #f5c2c7;
}

.question-input input {
    border-radius: 6px;
}

#kt_activitiesJob_close svg {
        color: red;
}

</style>



  @if($benchshowtab)
   @if(@$jobfindhide!=false)
<div class="card-body position-relative px-0 py-0" id="kt_activities_bodynew">
    

    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        
        {{-- <h3 class="card-title fw-bolder text-dark">Job Details</h3> --}}
        <?php
                                if($job->comId==auth()->user()->company_id){
                                    // $url  = url("service_company/job-applications/create/".GeneralMethod::value_encrypt(@$job->id)."/0");
                                    $url  = url("service_company/apptracker/".GeneralMethod::value_encrypt(@$job->id)."/all");
                                }else{
                                    $url  = url("service_company/apptracker/clientapp/".GeneralMethod::value_encrypt(@$job->id)."/all");
                                    // $url  = url("service_company/job-applications/clientcreate/". GeneralMethod::value_encrypt(@$job->id) ."/0");
                                }

                                ?>
                                
                                <?php
                                if($benchshowtab){
                                    if($jobfindhide){
                                    echo '<a href="'.$url.'" type="button" class="btn btn-sm btn-light-info btn-active-primary fw-bolder me-3 fs-6">View Candidates</a>';
                                    }
                                    if($agencyView){
                                    echo '<a href="javascript:;" id="shareJobBtn" data-url="'.url('job-details/' .(@$job->id) . '/'.auth()->user()->company_id).'" type="button" class="btn btn-sm btn-light-info btn-active-primary fw-bolder me-3 fs-6">Share Job</a>';
                                    }

                                    echo '<a href="javascript:;" data-url="javascript:;" type="button" class="hm-listing btn btn-sm btn-light-info btn-active-primary fw-bolder me-3 fs-6" data-row-id="'.@$job->id.'">Add Team</a>';
                            
                                    if(@$tabshow) {  
                                    if ($viewmore){
                                        if($agencyView){
                                        echo '<a href="javascript:;" data-url="javascript:;" type="button" class="spartner-listing btn btn-sm btn-light-info btn-active-primary fw-bolder me-3 fs-6" data-row-id="'.@$job->id.'">Add Agency</a>';
                                        }
                                        }
                                    }
                                }
                                
                                ?>


        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesJob_close"> 
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                        <rect x="0" y="7" width="16" height="2" rx="1" />
                        <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
                    </g>
                    </svg>
                </span> 
            </button>
        </div>
        </div>
    @endif
 @endif

    <input type="hidden" name="hid_job_id" id="hid_job_id" value="{{ $job->id }}" />
    <!--begin::Content-->
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true"
        data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_bodynew"
        data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
        <!--beign::candidate detail-->
        <div class="card mb-5 mb-xl-5 mx-2">
            <div class="card-body pt-5 pb-0 px-5">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <!--begin: Pic-->
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                            <?php
                            if (!empty($logo_path)) {
                                echo '<img src="' . str_replace('index.php', '', @$logo_path) . '" height="100px" width="100px"  />';
                            } else {
                                echo '<div class="symbol-label fs-3 ' . @$bgTextDecore . ' border-radius-5"><span>' . ucwords(@$strVal) . '</span></div>';
                            }
                            ?>
                            <div
                                class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px">
                            </div>
                        </div>
                    </div>
                    <!--end::Pic-->
                    <!--begin::Info-->
                    <div class="flex-grow-1">
                        <!--begin::Row-1-->
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <!--begin::Candidate Details-->
                            <div class="d-flex flex-column">
                                <!--begin::Name & Candidate ID-->
                                <div class="d-flex align-items-center mb-2"> <span
                                        class="text-gray-900 fs-2 fw-bolder me-2"> 
                                        <?php echo ucwords(@$job->title); ?> </span> 
                                        <?php
                                        if (@$job->status == 'active') {
                                    echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">Hiring</a>';
                                } elseif (@$job->status == 'hold') {
                                    echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-warning btn-active-warning fw-bolder me-3 fs-6">Hold</a>';
                                } else {
                                    echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6">Closed</a>';
                                }

                                ?>
                                </div>
                                <!--end::Name & Candidate ID-->
                                <!--begin::Designaiton-->
                                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                        <!--begin::Svg Icon-->
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z"
                                                        fill="#000000" />
                                                    <rect fill="#FFFFFF" x="13" y="8" width="3" height="3"
                                                        rx="1" />
                                                    <path
                                                        d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z"
                                                        fill="#000000" opacity="0.3" />
                                                </g>
                                            </svg>
                                        </span> <?php  if(@$jobfindhide!=false){ echo ucwords(@$company_or_client);}else{ echo '-----';} ?> </a> <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <!--begin::Svg Icon-->
                                        {{-- <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3"
                                                    d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z"
                                                    fill="black"></path>
                                                <path
                                                    d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z"
                                                    fill="black"></path>
                                            </svg>
                                        </span> --}}
                                        <!--end::Svg Icon-->
                                    </a> 
                                    <a href="javascript:void(0);"
                                        class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                        <!--begin::Svg Icon-->
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                                        fill="#000000" opacity="0.3" />
                                                    <path
                                                        d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z"
                                                        fill="#000000" />
                                                    <path
                                                        d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                                        fill="#000000" />
                                                </g>
                                            </svg>
                                        </span>
                                        <?php
                                        
                                        $originalDate = @$job->created_at;
                                        $newDate = date('d M, Y', strtotime($originalDate));
                                        echo $newDate;
                                        
                                        ?>
                                    </a> </div>
                            </div>
                            <!--end::Candidate Details-->
                            <!--begin::Actions-->
                            <div class="d-flex mt-4">
                                
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Row-1-->
                        <!--begin::Row-2-->
                        <div class="d-flex flex-wrap flex-stack">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-column flex-grow-1 pe-8">
                                <div class="row w-75"> <span class="">
                                        <?php
                                        if (!empty($skillArr)) {
                                            foreach ($skillArr as $sval) {
                                                echo '<span class="badge badge-light my-1">' . ucfirst($sval->skill_name) . '</span>  ';
                                            }
                                        }
                                        ?>
                                    </span> </div>
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Row-2-->
                    </div>
                    <!--end::Info-->
                    @if(!$benchshowtab)
                        @if(!$benchshowtabclose)
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesJob_close"> 
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                                        <rect x="0" y="7" width="16" height="2" rx="1" />
                                        <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
                                    </g>
                                    </svg>
                                </span> 
                            </button>
                        </div>
                        @endif

                    @endif

                </div>

                <!--end::Details-->
            </div>
        </div>
        <!--end::candidate details-->
        <!--begin::Tabs-->
        <div class="card card-custom mx-2">
            <!--Begin::Tabs Header-->
            <div class="card-header card-header-tabs-line">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x"
                        role="tablist">
                        <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab"
                                href="#job_description"> <span class="nav-text font-weight-bold">Job
                                    Description</span> </a> </li>
                    @if(@$jobfindhide!=false)
                        @if($benchshowtab)         
                        @if(@$tabshow)    
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interviews"> <span
                                    class="nav-text font-weight-bold">Interviews</span> </a> </li>
                        @endif
                         @if(@$tabshow)           
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#team"> <span
                                    class="nav-text font-weight-bold">Team</span> </a> </li>
                         @endif  
                        @if($job->comId==auth()->user()->company_id)        
                            @if(@$tabshow)   
                                @if ($viewmore)
                                    <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab"
                                            href="#staffing_partners">{{__('messages.Agencies')}}</a> </li>
                                    <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab"
                                            href="#request">Request</a> </li>
                                @endif
                            @endif
                        @endif
                        @if($job->comId==auth()->user()->company_id)
                        <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#agency_t_c"> <span
                                        class="nav-text font-weight-bold">Agency T&C</span> </a> </li>
                        @endif
                            <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab"
                                    href="#jobGraph">Job Dashboard</a> </li>
                        @endif  
                        
                        @if($benchshowtab)
                           <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab"
                                    href="#interviewquestions">Interview Job Questions </a> </li> 
                        @else

                         @if($benchshowtabComman)
                            @if(!$benchshowtabclose)
                            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#contactPersons"> <span
                                        class="nav-text font-weight-bold">Contacts</span> </a> </li>
                            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#timeline"> <span
                                        class="nav-text font-weight-bold">Timeline</span> </a> </li>
                            @endif
                         @endif
                        @endif
                    @endif
                        
                    </ul>
                </div>
            </div>
            <!--end::Tabs Header-->
            <!--Begin::Body-->
            <div class="card-body py-0">
                <div class="tab-content pt-5">
                    <!--begin::Job Description Content-->
                    <div class="tab-pane active" id="job_description" role="tabpanel">
                        <!--begin::Job Description header-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Job Details</h3>
                            </div>
                            <!--end::Card title-->
                            @if (in_array('edit_jobs', $userPermissions))
                                <!--begin::Action-->
                                @if($benchshowtab)
                                    @if ($viewmore && $job->comId==auth()->user()->company_id)
                                        <a href="<?php echo route('service_company.jobs.edit', GeneralMethod::value_encrypt($job->id)); ?>" class="btn btn-primary align-self-center">Edit
                                            Job</a>
                                    @endif
                                @endif

                                <!--end::Action-->
                            @endif
                        </div>
                        <!--end::Job Description header-->
                        <div class="card-body px-0 py-7">

                            
                            <!--begin::Job Description Content-->
                            <div class="row">
                                <div class="col-lg-12 mb-7">
                                    
                                    <!--New design-->
                                <div class="row mb-5">
                                   <div class="col-md-3">
                                      <div class="d-flex flex-column text-dark-75">
                                         <span class="fw-bold fs-6 text-muted">Company Name</span> 
                                         <span class="fw-bold fs-6">  <?php  if(@$jobfindhide!=false){ echo ucwords(@$company_or_client);}else{ echo '-----';} ?></span>
                                      </div>
                                   </div>
                                   <div class="col-md-3">
                                      <div class="d-flex flex-column text-dark-75">
                                         <span class="fw-bold fs-6 text-muted">Position Name</span> 
                                         <span class="fw-bold fs-6"><?php echo ucwords(@$job->title); ?></span>
                                      </div>
                                   </div>
                                   <div class="col-md-3">
                                      <div class="d-flex flex-column text-dark-75">
                                         <span class="fw-bold fs-6 text-muted">Job Location</span> 
                                         <span class="fw-bold fs-6"><?php echo ucwords(@$jobCitySelected); ?></span>
                                      </div>
                                   </div>
                                   <div class="col-md-3">
                                      <div class="d-flex flex-column text-dark-75">
                                         <span class="fw-bold fs-6 text-muted">Work Mode</span> 
                                         <span class="fw-bold fs-6">{{@$work_type}}</span>
                                      </div>
                                   </div>
                                </div>
                                
                                    <div class="row mb-5">
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Position Type</span> 
                                             <span class="fw-bold fs-6"><?php echo ucwords($employment_type); ?></span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">No. of Openings</span> 
                                             <span class="fw-bold fs-6"><?php echo @$job->total_positions; ?></span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Job Department</span> 
                                             <span class="fw-bold fs-6"><?php echo @$job->cat_name; ?></span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Qualification</span> 
                                             <span class="fw-bold fs-6">{{ @$qualification->name }}</span>
                                          </div>
                                       </div>
                                    </div>
                                    
                                    <div class="row mb-5">
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Min Experience</span> 
                                             <span class="fw-bold fs-6"><?php echo @$job->min_exp; ?> Years</span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Max Experience</span> 
                                             <span class="fw-bold fs-6"><?php echo @$job->max_exp; ?> Years</span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Min Salary</span> 
                                             <span class="fw-bold fs-6"><?php echo @$currency[@$job->min_salary_currency]; ?><?php echo @$min_salary; ?>/<?php echo @$max_sal_mode; ?></span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Max Salary</span> 
                                             <span class="fw-bold fs-6"><?php echo @$currency[@$job->min_salary_currency]; ?>
                                        <?php echo @$max_salary; ?>/<?php echo @$max_sal_mode; ?></span>
                                          </div>
                                       </div>
                                    </div>   
                                     
                                    <div class="row mb-5">  
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Due Date</span> 
                                             <span class="fw-bold fs-6">{{@$end_date}}</span>
                                          </div>
                                       </div>
                                       <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Job ID</span> 
                                             <span class="fw-bold fs-6">{{ @$job->id }}<span>
                                          </div>
                                       </div>
                                       <div class="col-md-3 classHide">
                                        <div class="d-flex flex-column text-dark-75">
                                            <span class="fw-bold fs-6 text-muted">Hiring HR</span> 
                                       <?php
                                    //    echo '<pre>'; print_r($clientContactPersonListing);
                                    //     die;
                                       // ucwords(@$info->contact_person_name)
                                       if(!empty(@$clientjobAssignListCaptain)){

                                         $image_path = url('public/avatar.png');
                                            $contactHtml = '<div class="">
                                                        <div class="">';
                                                         $contactHtml .=' <span class="fw-bold fs-6">'.ucwords(@$clientjobAssignListCaptain).'<span>';
                                                    
                                            $contactHtml .= '</div></div>';
                                            echo $contactHtml;


                                       }else{
                                        if (!empty($clientContactPersonListing)) {
                                            $image_path = url('public/avatar.png');
                                            $contactHtml = '<div class="">
                                                        <div class="">';
                                            foreach ($clientContactPersonListing as $key=> $info) {
                                                $bgTextDecore				=	GeneralMethod::icoinText(@$info);	
                                                if($key > 0)	{
                                                $contactHtml .=
                                                    ' <span class="fw-bold fs-6">,'.ucwords(@$info).'<span>
                                                    ';
                                                    }else{
                                                        $contactHtml .=
                                                    ' <span class="fw-bold fs-6">'.ucwords(@$info).'<span>
                                                    ';
                                                    }
                                            }
                                            $contactHtml .= '</div></div>';
                                            echo $contactHtml;
                                        }
                                       }


                                       ?>
                                        </div>
                                       </div>

                                       <div class="col-md-3">
                                        <div class="d-flex flex-column text-dark-75">
                                           <span class="fw-bold fs-6 text-muted">Work Shift</span> 
                                           <span class="fw-bold fs-6">{{ @$job->work_shifts }}<span>
                                        </div>
                                     </div>
                                      
                                    </div>
                                    <div class="row mb-5">
                                        <div class="col-md-3">
                                          <div class="d-flex flex-column text-dark-75">
                                             <span class="fw-bold fs-6 text-muted">Gender Diversity</span> 
                                             <span class="fw-bold fs-6">{{@$job->required_columns ?? 'Rather Not Say'}}</span>
                                          </div>
                                       </div>
                                    </div>
                                    
                                    <!--<div class="d-flex flex-column mb-5">-->
                                    <!--    <span class="d-flex fw-semibold fs-7 fs-6 mb-4 badge badge-light-success" style="max-width: fit-content;">Skills</span>-->
                                    <!--    <div class="d-flex text-gray-700 fw-semibold fs-7">-->
                                            <?php
                                        // if (!empty($skillArr)) {
                                        //     foreach ($skillArr as $sval) {
                                        //         echo '<span class="border border-2 rounded me-3 p-1 px-2">' . ucfirst($sval->skill_name) . '</span>  ';
                                        //     }
                                        // }
                                        ?>
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="separator separator-solid my-7"></div>
                                     <div class="row mb-5">
                                        <h3 class="fw-bolder mb-3">Job Description</h3>
                                        <div class="card-title mb-5 fs-6">
                                            <?php echo @$job->job_description; ?>
                                        </div>
                                    </div>
                                    @if(@$jobfindhide!=false)
                                    <div class="separator separator-solid my-7"></div>
                                    <div class="row mb-5">
                                        <h3 class="fw-bolder mb-3">About Company</h3>
                                        <h6><?php echo ucwords(@$company_or_client); ?></h6>
                                        <div class="card-title mb-5 fs-6">
                                            
                                            @if(@$job->self_company==1)
                                            <?php echo $description; ?>
                                            @else
                                             <?php echo $description; ?>
                                        @endif
                                        </div>
                                    </div>
                                    @else

                                    @endif
                                    <!--End new design-->
                                    
                                </div>
                            </div>
                            <!--end::Job Description Content-->
                            
                            
                        </div>
                    </div>
                    <!--end::Job Description Content-->
                    <!--begin::Team Content-->
                    {{-- <div class="tab-pane" id="team" role="tabpanel"> --}}
                    <!--begin::Sub tabs-->
                    {{-- <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0">
                      <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#all_team">Team</a> </li>
                      <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#staffing_partners">Staffing Partners</a> </li>
                      <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#request">Request</a> </li>
                    </ul> --}}
                    <!--end::Sub tabs-->
                    <!--begin::Team Content-->
                    {{-- <div class="tab-content" id="myTabContent"> --}}
                    <!--begin::All Team-->
                    <div class="tab-pane fade show" id="team" role="tabpanel">
                        <!--begin::All team header-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Team</h3>
                            </div>
                            <!--end::Card title-->
                            @php
                            echo '<a href="javascript:;" data-url="javascript:;" type="button" class="hm-listing btn btn-primary align-self-center" data-row-id="'.@$job->id.'">Add Team</a>';
                            @endphp

                            <!--end::Action-->
                        </div>
                        <div class="card-body px-0 py-7">
                            @if (in_array('view_team', $userPermissions))
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_users">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input1"
                                                        value="1">
                                                </div>
                                            </th>
                                            <th class="min-w-150px">Name</th>
                                            <th class="min-w-150px">Contact Person</th>
                                            <th class="min-w-150px">Upload Profile</th>
                                            <th class="min-w-15px">Interview</th>
                                            <th class="">Joinings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                                if(!empty($recruiterlist)){
                                                foreach($recruiterlist as $rval){
                                                ?>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input1"
                                                        value="1">
                                                </div>
                                            </th>
                                            <th class="min-w-150px">
                                                <?php
                                                $bgTextDecore = $strVal = '';
                                                $str = trim(@$rval->name);
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
                                                
                                                $icon =
                                                    '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                                                                                                                                                                                          <a href="javascript:void(0);" data-row-id="' .
                                                    $rval->id .
                                                    '" class="show-detail-team" id="kt_activities3_toggle" >
                                                                                                                                                                                              <div class="symbol-label fs-3 ' .
                                                    @$bgTextDecore .
                                                    '  border-radius-5">' .
                                                    ucwords(@$strVal) .
                                                    '</div>
                                                                                                                                                                                          </a>
                                                                                                                                                                                      </div>';
                                                
                                                if (!empty($rval->image)) {
                                                    $imagePath = '';
                                                    $imagePath = url(@$rval->image);
                                                    $icon =
                                                        '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left">
                                                                                                                                                                                                        <a href="javascript:void(0);">
                                                                                                                                                                                                            <div class="symbol-label border-radius-5">
                                                                                                                                                                                                            <img src="' .
                                                        $imagePath .
                                                        '" alt="User Image" class="border-radius-5 w-100">
                                                                                                                                                                                                            </div>
                                                                                                                                                                                                        </a>
                                                                                                                                                                                                    </div>';
                                                }
                                                
                                                echo $icon .
                                                    '<div class="d-flex flex-column">
                                                                                                                                                                                    <a href="javascript:void(0);" data-row-id="' .
                                                    $rval->id .
                                                    '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' .
                                                    ucwords(@$rval->name) .
                                                    '">' .
                                                    ucwords(substr(@$rval->name, 0, 15)) .
                                                    '</a>
                                                                                                                                                                                    <a href="javascript:void(0);" data-row-id="' .
                                                    $rval->id .
                                                    '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">' .
                                                    ucwords(@$rval->designation) .
                                                    '</a>
                                                                                                                                                                                  </div>
                                                                                                                                                                                  ';
                                                
                                                ?>
                                            </th>
                                            <th class="min-w-150px">
                                                <?php
                                                
                                                echo '<div class="d-flex flex-column">
                                                    <a href="javascript:void(0);" data-row-id="' . $rval->id .'" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">' . @$rval->email .'</a>
                                                    <div style="position: relative;display: flex;">
                                                        <div class="iti__flag-container" data-code="'.@$rval->calling_code.'" data-row="'.$rval->id.'" id="phoneDisplayReal_'. $rval->id.'" style="display: flex;position: relative;"></div>
                                                        <div style="margin-left: 6px;margin-top: 2px;">
                                                            <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">'.ucwords(@$rval->mobile).'</a>
                                                        </div>
                                                    </div>
                                                                                                                                                                                        </div>';
                                                
                                                ?>
                                            </th>
                                            <th class="min-w-150px"><?php echo @$rval->allCandidate; ?></th>
                                            <th class="min-w-15px"><?php echo @$rval->Interview; ?></th>
                                            <th class=""><?php echo @$rval->Joined; ?></th>
                                        </tr>
                                        <?php }} ?>
                                    </tbody>
                                </table>
                            @endif

                        </div>
                    </div>

                    <!--begin::Agency Term & conditions-->
                    <div class="tab-pane fade show" id="agency_t_c" role="tabpanel">
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Terms & Condition</h3>
                            </div>
                        </div>
                        {!!@$terms!!}
                    </div>
                    <!--end::Agency Term & conditions-->

                    {{-- interviewquestions --}}
                     <!--begin::Job Graph-->
                    <div class="tab-pane fade show" id="interviewquestions" role="tabpanel">
                        
                        <div class="card-title m-0" style="display: flex;">
                            <h3 class="fw-bolder m-0">Interview Job Questions</h3> 
                            
                            @if(@$captainAccessCheck)
                                <span class="fw-bold fs-6" style="position: absolute; right: 47px;"> 
                                <a type="button" class="btn btn-primary align-self-center" id="addQuestionBtn_{{ $job->id }}" href="javascript:void(0);" style="margin-left: 4px;">+ Add Question</a>
                                </span>
                            @else
                                <span class="fw-bold fs-6" style="position: absolute; right: 47px;"> 
                                <a type="button" class="btn btn-primary align-self-center" id="addQuestionBtn_{{ $job->id }}" href="javascript:void(0);" style="margin-left: 4px;">+ Add Question</a>
                                </span>
                            @endif
                            
                        </div>
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="row pt-7 border-bottom pb-7">

                                <div class="job_interview_question_view">
                                     <!-- Question List -->
                                    <div class="card">
                                        <div class="card-body" id="questionList_{{@$job->id}}">

                                            {!! @$interview_questions->questions !!}

                                        </div>
                                    </div>

                                 </div>

                            </div>
                        </div>
                    </div>
                    <!--end::Job Graph-->
                    <!--begin::Job Graph-->
                    <div class="tab-pane fade show" id="jobGraph" role="tabpanel">
                        <div class="card-header cursor-pointer px-0 d-none">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Job Dashboard</h3>
                            </div>
                        </div>

                         <!--begin::Col-->
                        <div class="row">
                            <!--Start Total Candidate Added -->
                            <div class="col-lg-6 col-xl-cus-6 col-xl-6 col-xxl-6 mb-6 mb-xl-0">
                                <!--begin::Chart widget 3-->
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <!--begin::Header-->
                                    <div class="card-header py-5">
                                        <!--begin::Title-->
                                        <h3 class="card-title align-items-start flex-column">   
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$total_candidates}}</span>         
                                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Candidates added In</span>
                                            {{-- <span class="text-gray-500 mt-1 fw-semibold fs-6">Users from all channels</span> --}}
                                        </h3>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Card body-->
                                    <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">  
                                        <!--begin::Chart-->
                                        {{-- <div style="position: absolute; bottom: 50px; width: 222px; height: 53px; background: #ffffff; z-index: 99;"> </div> --}}
                                        <div id="kt_charts_widget_3" class="min-h-auto">

                                            <canvas id="myChart_<?php echo @$job->id; ?>"></canvas>

                                            
                
                                            {{-- <div id="chart-container" style="width: 600px; height: 420px; margin: auto;"></div> --}}
                                        </div><br>
                                        <!--end::Chart--> 
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Chart widget 3-->    
                            </div>
                            <!--End Total Candidate Added -->

                            <!--Start Candidate last 7 days -->
                            <div class="col-lg-6 col-xl-cus-6 col-xl-6 col-xxl-6 mb-6 mb-xl-0">

                                
                                <!--begin::Chart widget 3-->
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <!--begin::Header-->
                                    <div class="card-header py-5">
                                        <!--begin::Title-->
                                        <h3 class="card-title align-items-start flex-column">   
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotal}}</span>         
                                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Candidates added In 7 days</span>
                                        </h3>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Card body-->
                                    <div class="card-body d-flex justify-content-between flex-column pb-1 px-0" style="position: relative">  
                                        <!--begin::Chart-->
                                        {{-- <div style="position: absolute; bottom: 50px; width: 222px; height: 53px; background: #ffffff; z-index: 99;"> </div> --}}
                                        <div id="kt_charts_widget_3" class="min-h-auto">
                                            <canvas id="candidates_left_chart_<?php echo @$job->id; ?>"></canvas>
                                        </div><br>
                                        <!--end::Chart--> 
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Chart widget 3--> 


                            </div>
                            <!--End Candidate last 7 days -->
                        </div>

                        
                        <div class="row gx-5 gx-xl-10 mb-xl-10">
                            <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                <div class="card card-flush h-md-50 mb-xl-10">
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <div class="card-title d-flex flex-column">   
                                            <!--begin::Info--> 
                                            <div class="d-flex align-items-center">
                                                <!--begin::Amount-->
                                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaloffer}}</span>
                                                <!--end::Amount-->
                                                
                                                <!--begin::Badge-->
                                                {{-- <span class="badge badge-light-danger fs-base">                                
                                                    <i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i> 
                                                    2.2%
                                                </span>             --}}
                                                <!--end::Badge-->                
                                            </div>
                                            <!--end::Info--> 

                                            <!--begin::Subtitle-->
                                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Offers In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>             
                                            <!--end::Subtitle--> 
                                        </div>
                                        <!--end::Title-->         
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Card body-->
                                    <div class="card-body d-flex align-items-end pt-0">
                                        <!--begin::Progress-->
                                        

                                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                                            <div 
                                                id="kt_card_widget_4_chart_<?php echo @$job->id; ?>" 
                                                style="min-width: 100%; min-height: 200px">
                                                <canvas id="candidates_offer_chart_<?php echo @$job->id; ?>" height="170"></canvas>
                                            </div>
                                        </div>
                                        <!--end::Progress-->
                                    </div>
                                    <!--end::Card body-->

                                </div>                        
                            </div>
                            <div class="col-md-6 col-xl-cus-5 col-lg-6 col-xl-5 col-xxl-6 mb-2">
                                <div class="card card-flush h-md-50 mb-xl-10">
                                
                                    <!--begin::Header-->
                                    <div class="card-header pt-5">
                                        <!--begin::Title-->
                                        <div class="card-title d-flex flex-column">                
                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$candidateValuesTotaljoin}}</span>
                                            <!--end::Amount-->           

                                            <!--begin::Subtitle-->
                                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Joined In {{ date("M Y ", strtotime($startOfMonthcomman)) }} To {{ date("M Y ", strtotime($endOfMonthcomman)) }}</span>
                                            <!--end::Subtitle--> 
                                        </div>
                                        <!--end::Title-->           
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Card body-->
                                    <div class="card-body d-flex flex-column justify-content-end pe-0">
                                        <!--begin::Title-->
                                        <!--end::Title-->

                                        <!--begin::Users group-->
                                        <div class="symbol-group symbol-hover flex-nowrap">

                                            <div 
                                                id="kt_card_widget_4_chart_<?php echo @$job->id; ?>" 
                                                style="min-width: 100%; min-height: 200px">
                                                <canvas id="candidates_join_chart_<?php echo @$job->id; ?>" height="170"></canvas>
                                            </div>


                                                
                                        </div>
                                        <!--end::Users group-->
                                    </div>
                                    <!--end::Card body-->

                                </div>
                            </div>
                        </div>

                        <!--Start Interview Section-->
                        <div class="row gy-5 g-xl-10">
                            <!--begin::Col-->
                            <div class="col-xl-4 mb-xl-10">
                                
                                <!--begin::List widget 17-->
                                <div class="card card-flush h-xl-100">
                                    <!--begin::Header-->
                                    <div class="card-header pt-7">
                                        <!--begin::Title-->
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-gray-800">Upcoming Interviews</span>
                                        </h3>
                                        <!--end::Title-->
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Body-->
                                    <div class="card-body pt-0"> 
                                        
                                            <!--begin::Content-->                                  
                                            <div class="d-flex flex-stack my-5">
                                                <span class="text-gray-500 fs-7 fw-bold"></span>
                                                
                                                <span class="text-gray-500 fw-bold fs-7"></span>
                                            </div>                   
                                            <!--end::Content-->

                                            @if (count($upcomingData) > 0)
                                                @foreach ($upcomingData as $rowArr)

                                                    @php
                                                    $row = (object) $rowArr;
                                                    $schedule_date = Carbon::parse($row->schedule_date)->format('M d');
                                                    $schedule_date_explode = explode(" ",$schedule_date);
                                                    @endphp
                                        
                                                    <!--begin::Item-->
                                                    <div class="d-flex flex-stack">                 
                                                        <!--begin::Wrapper-->                                  
                                                        <div class="d-flex align-items-center me-3">
                                                            <!--begin::Icon-->                         
                                                            <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-40px h-40px mr-2 text-center" style="display: inline-grid">
                                                            <span style="font-size: 12px; font-weight: 600;">{{$schedule_date_explode[0]}}</span><span style="color: #373bed; font-size: 17px; font-weight: 900;">{{$schedule_date_explode[1]}}</span>                
                                                            </a>                 
                                                            <!--end::Icon-->

                                                            <!--begin::Section-->                                  
                                                            <div class="flex-grow-1">
                                                                <a href="javascript:;" class="text-gray-800 text-hover-primary fs-5 fw-bold lh-0">{{ucwords(mb_substr(@$row->full_name, 0, 20))}}</a>
                                                                
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">{{ucwords(mb_substr(@$row->current_designation, 0, 21))}}</span>
                                                            </div>                   
                                                            <!--end::Section-->
                                                        </div>                   
                                                        <!--end::Wrapper-->                 

                                                        <!--begin::Value-->
                                                        <span class="text-gray-800 fw-bold fs-6">
                                                            {{$row->schedule_to_time. ($row->timezone_abbreviation ? ' (' . $row->timezone_abbreviation . ')' : '')}} <br>
                                                            <span class="text-gray-500 fw-semibold d-block fs-7">
                                                            {{ucwords(mb_substr($row->mode_name, 0, 13))}} 
                                                            <span> 
                                                        </span>
                                                        <!--end::Value-->               
                                                    </div>
                                                    <!--end::Item-->

                                                    <!--begin::Separator-->
                                                    <div class="separator separator-dashed my-4"></div>
                                                    <!--end::Separator-->
                                                @endforeach
                                            @else
                                                <table border="1" width="100%" cellpadding="10">
                                                    <tbody>
                                                            <tr>
                                                                <td colspan="4" align="center">No records found</td>
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            @endif                                   
                                        
                                    </div>
                                    <!--end::Body-->
                                </div>
                                <!--end::List widget 17-->     
                            </div>
                            <!--end::Col-->      

                            <!--begin::Col-->
                            <div class="col-xl-8 mb-5 mb-xl-10">
                                
                                <!--begin::Table widget 6-->
                                <div class="card card-flush h-xl-100">
                                    <!--begin::Header-->
                                    <div class="card-header pt-7" style="position: relative">
                                        <!--begin::Title-->
                                        <h3 class="card-title align-items-start flex-column">			
                                            <span class="card-label fw-bold text-gray-800">Candidates Interviews</span>
                                        </h3>
                                        <!--end::Title-->
                                        <span class="" style="position: absolute; right: 14px; top: 26px;"> 
                                        <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdatagetmonth(this.value)">
                                                <option value="1">Last 6 Months</option>
                                                <option value="0">Last 7 Days</option>
                                            </select>
                                        </span>
                                        
                                    </div>
                                    <!--end::Header-->

                                    <!--begin::Body-->
                                    <div class="card-body">       
                                        <!--begin::Tab Content-->
                                        <div class="tab-content">
                                                <!--begin::Tap pane-->
                                                <div class="tab-pane fade active show" id="kt_stats_widget_6_tab_1_<?php echo @$job->id; ?>" role="tabpanel">
                                                    <!--begin::Table container-->

                                                    <div id="kt_card_widget_6_chart_<?php echo @$job->id; ?>" class="w-100" style="height: 200px">

                                                        <div class="inetview_recent_chart_<?php echo @$job->id; ?> d-none" id="kt_card_widget_4_chart_<?php echo @$job->id; ?>" 
                                                            style="min-width: 100%; min-height: 200px">
                                                            <canvas id="inetview_recent_chart_<?php echo @$job->id; ?>" height="300"></canvas>
                                                        </div>
                                                        
                                                        <div class="inetview_recent_chart_month_<?php echo @$job->id; ?>" id="kt_card_widget_4_chart" 
                                                                style="min-width: 100%; min-height: 200px">
                                                                <canvas id="inetview_recent_chart_month_<?php echo @$job->id; ?>" height="230"></canvas>
                                                        </div>

                                                    </div>

                                                        
                                                    <!--end::Table-->
                                                </div>
                                                <!--end::Tap pane-->
                                        </div>
                                        <!--end::Tab Content-->        
                                    </div>
                                    <!--end: Card Body-->
                                </div>
                                <!--end::Table widget 6-->   
                            </div>
                            <!--end::Col-->      
                        </div>
                        <!--End Interview Section-->

                       
                    </div>
                    <!--end::Job Graph-->


                    <!--begin::Staffing Partners-->
                    <div class="tab-pane fade show" id="staffing_partners" role="tabpanel">
                        <!--begin::Staffing Partners header-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">{{__('messages.Agencies')}}</h3>
                            </div>
                            <!--end::Card title-->
                            @php
                            if(@$tabshow) {  
                                if ($viewmore){
                                    echo '<a href="javascript:;" data-url="javascript:;" type="button" class="spartner-listing btn btn-primary align-self-center" data-row-id="'.@$job->id.'">Add Agency</a>';
                                    }
                                }
                            @endphp

                            <!--end::Action-->
                        </div>
                        <!--end::Staffing Partners header-->
                        <!--begin::Staffing Partners Content-->

                        <div class="card-body px-0 py-7">
                            @if (in_array('view_staffing_partners', $userPermissions))
                                <!--begin:Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users .form-check-input-two"
                                                        value="1" />
                                                </div>
                                            </th>
                                            <th class="">Name</th>
                                            <th class="">Contact Person</th>
                                            <th class="">Upload Profile</th>
                                            <th class="">Interview</th>
                                            <th class="">Joinings</th>
                                            <th class="">T&C</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @if ($recommendedDataList)

                                            @foreach ($recommendedDataList as $reqVal)
                                                <?php
                                                $contHtml = '';
                                                $contactQuery = GeneralMethod::team_persion($reqVal->jobid, $reqVal->id);
                                                $contHtml = '<div class="symbol-group symbol-hover" style="float:left">';
                                                if (!empty($contactQuery)) {
                                                    foreach ($contactQuery as $cont) {
                                                        $logo_path = url('public/avatar.png');
                                                        if (!empty(@$cont->image) && strlen(@$cont->image) > 10) {
                                                            $logo_path = url('public/user-uploads/team_profile_img/' . @$cont->image);
                                                        }
                                                        $contHtml .= '<div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="' . $cont->name . '"> <img alt="One" src="' . str_replace('index.php', '', $logo_path) . '"></div>';
                                                    }
                                                }
                                                $contHtml .= '</div>';
                                                ?>
                                                <?php
                                                $logo_path = url('img/client-default.png');
                                                if (!empty(@$reqVal->logo) && strlen(@$reqVal->logo) > 10) {
                                                    $logo_path = url('public/user-uploads/company-logo/' . @$reqVal->logo);
                                                }
                                                
                                                $name =
                                                    '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left"> 
                                                                                                                                                                            <a href="javascript:void(0);">
                                                                                                                                                                                          <div class="symbol-label border-radius-5"> <img src="' .
                                                    str_replace('index.php', '', $logo_path) .
                                                    '" alt="Company Logo" class="border-radius-5 w-100"> </div>
                                                                                                                                                                                        </a> 
                                                                                                                                                                          </div>
                                                                                                                                                                          <div class="d-flex flex-column"> 
                                                                                                                                                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                                                    substr(ucwords(@$reqVal->company_name), 0, 15) .
                                                    '</a>   
                                                                                                                                                                            <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">' .
                                                    substr(ucwords(@$reqVal->ind_name), 0, 15) .
                                                    '</a>
                                                                                                                                                                          </div>';
                                                ?>
                                                <tr>
                                                    <td class="">
                                                        <div
                                                            class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                data-kt-check="true"
                                                                data-kt-check-target="#kt_table_users .form-check-input-two"
                                                                value="1" />
                                                        </div>
                                                    </td>
                                                    <td class="">{!! $name !!}</td>
                                                    <td class="">{!! $contHtml !!}</td>
                                                    <td class="">{{ $reqVal->Upload }}</td>
                                                    <td class="">{{ $reqVal->Interview }}</td>
                                                    <td class="">{{ $reqVal->Joined }}</td>
                                                    <td class=""><div data-row-id="{{ $reqVal->relation_id }}" data-row-relation_id="{{$reqVal->relation_id }}" class="client-terms-accept">View</div></td>
                                                </tr>
                                            @endforeach
                                        @endif


                                    </tbody>
                                </table>
                                <!--end::Table-->
                            @endif
                        </div>
                        <!--end::Staffing Partners Content-->
                    </div>
                    <!--end::Staffing Partners-->

                    <div class="tab-pane" id="contactPersons" role="tabpanel">

                        @if(@$bench_assign_count > 0)
                            <div class="card-title m-0 border-bottom py-7">
                            
                                <div class="card-title m-0 border-bottom py-7">
                                    <h3 class="fw-bolder m-0">Contacts</h3>
                                </div>
                                <div class="row" style="margin-top: 7px;">
                                    <div class="col-lg-1"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> # </span> </div>
                                    <div class="col-lg-3"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> Name </span> </div>
                                    <div class="col-lg-3"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> Designation</span> </div>
                                    <div class="col-lg-5"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> Contact Us </span> </div>
                                </div>

                                @php $sr = 1; @endphp
                                    @foreach( $bench_assign_row as $assignVal)
                                    <div class="row" style="margin-top: 7px;">
                                        <div class="col-lg-1"> {{ $sr }}</div>
                                        <div class="col-lg-3">
                                            <div class="col-lg-12">  {{ ucwords(@$assignVal->name) }}  </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="col-lg-12">  {{ ucwords(@$assignVal->designation) }}  </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="col-lg-12">  {{ @$assignVal->email }} <br> 
                                                <div style="position: relative;display: flex;">
                                                <div class="iti__flag-container" data-code="{{ @$assignVal->calling_code ?? 'in' }}" data-row="{{ $assignVal->id }}" id="phoneDisplayReal_{{ $assignVal->id }}" style="display: flex;position: relative;"> <!-- Flag and dial code will be appended here --></div>
                                                <div style="margin-left: 6px;margin-top: 2px;">
                                                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">{{ ucwords(@$assignVal->mobile) }}</a>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>	
                                        @php $sr++;  @endphp
                                    @endforeach	
                                </div>		
                                
                        @endif


                    </div>

	    		<div class="tab-pane" id="timeline" role="tabpanel">
                    <!--begin::Timeline header-->
                    <div class="card-header cursor-pointer px-0">
                        <!--begin::Card title-->
                         <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Timeline</h3>
                        </div>
                         <!--end::Card title-->
                    </div>
                    <!--end::Timeline header-->
                    <div class="card-body px-0 py-7">
                         <div class="timeline">
                            <?php
                               if(!empty($updateTimeLine)){
                                 foreach($updateTimeLine as $tdata){
                                    $tdata = (object) $tdata;
                                    $tdata->update_at =   $tdata->updated_at;
                                    $company_or_client = GeneralMethod::companyDetailsByClient($tdata->job_id);
                                    $create_at	=	$$newDate	=	$newTime	= $prev_tiles_bg	=	$timeline_type = $current_tiles_bg =  "";
                                    $create_at	=	$tdata->created_at;
                                    $newDate 	=	date("d-M-Y", strtotime($create_at));
                                    $newTime 	=	date("H:i A", strtotime($create_at));$posted_by_image	=	$comment	=	"";	
                                    if(!empty(@$tdata->image)){	
                                        $posted_by_image =	@$tdata->image;
                                    }
                                    if(!empty(@$tdata->prev_process_name)){
                                        $prevCandStatusArr	= 	GeneralMethod::candidateStatusColorCode(@$tdata->prev_process_name);	
                                        $prev_tiles_bg	=	$prevCandStatusArr['tiles_bg'];
                                    }

                                    if(!empty(@$tdata->master_process_name)){
                                        $currentCandStatusArr			= 	GeneralMethod::candidateStatusColorCode(@$tdata->master_process_name);	
                                        $current_tiles_bg				=	$currentCandStatusArr['tiles_bg'];
                                    }
              
                                    $master_process_name	=	@$tdata->master_process_name;
                                    $prev_process_name	=	@$tdata->prev_process_name;
                                    if($tdata->event_id == 1){
                                        $prev_process_name		=	@$tdata->note;	
                                        $prev_tiles_bg		=	'sourcing-bg';	
                                    }
              
                                    if($tdata->event_id == 2){
                                        $comment	=	@$tdata->note;		
                                    }
                                    if($tdata->event_id == 5 || $tdata->event_id == 6 || $tdata->event_id == 7 || $tdata->event_id == 8){
                                        $comment	=	@$tdata->note;
                                    }
                                    
                                    $offer_status = $management_status = '----';

                                    if($tdata->candidate_status==1){
                                        $offer_status = 'Offer Released';
                                    }elseif($tdata->candidate_status==2){
                                        $offer_status = 'Offer Accepted';
                                    }elseif($tdata->candidate_status==3){
                                        $offer_status = 'Offer Rejected';
                                    }elseif($tdata->candidate_status==4){
                                        $offer_status = 'Offer Expired';
                                    }elseif($tdata->candidate_status==0){
                                        $offer_status = 'Offer Created';
                                    }

                                    if($tdata->management_status==1){
                                        $management_status = 'In Process';
                                    }elseif($tdata->management_status==2){
                                        $management_status = 'Approved';
                                    }elseif($tdata->management_status==3){
                                        $management_status = 'Not Approved';
                                    }

                                    $available_status_name = '';
                                    if ($tdata->available_status > 0) {
                                        $available_status_name = @$available_status[@$tdata->available_status]['name'];
                                    } 

                                    $feedback = ucwords(substr(@$feedback_status[@$tdata->feedback]['name'], 0, 12));

                  
                                    if($CaptainCompanyid == $tdata->event_by_company_id){
                                        $rowpostedby = $tdata->name;
                                    }else{
                                        $rowpostedby = $tdata->name;
                                        $userC = DB::table('users')->select('company_id')->where('id',$tdata->event_by_user_id)->first();
                                        if($CaptainCompanyid==$userC->company_id){
                                                $rowpostedby = $tdata->name;
                                        }else{
                                            $spokperson = DB::table('job_assign')->select('users.name','job_assign.company_id')->leftjoin('users', 'users.id', 'job_assign.spok_user_id')->where('job_assign.job_id', $tdata->job_id)->where('job_assign.company_id', $tdata->event_by_company_id)->first();
                                            if($spokperson){
                                                if(!empty($spokperson->name)){
                                                    if($CaptainCompanyid !=$spokperson->company_id){
                                                    $rowpostedby = (@$spokperson->name) ? @$spokperson->name : $tdata->name;
                                                    }else{
                                                    $rowpostedby = $tdata->name;
                                                    }
                                                }
                                            }else{
                                                $rowpostedby = $tdata->name;
                                            }
                                        }
                                    }

                                    $rowpostedby = ($rowpostedby) ? $rowpostedby : $tdata->name;
                                    $bgTextDecore	=	GeneralMethod::icoinText($rowpostedby);
                                    
                                    if(($tdata->event_id == 1) || ($tdata->event_id == 2)){?>
                                        <div class="timeline-item">
                                            <div class="timeline-line w-40px"></div>
                                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                <div class="symbol-label bg-light"> 
                                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                                                                <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                                                                <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                                                                <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                                                            </g>
                                                        </svg>
                  
                                                    </span>
                                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <rect x="0" y="0" width="24" height="24"/>
                                                                <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="timeline-content mb-10 mt-n1">
                                                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                                                    <tbody class="text-gray-600 fw-bold">
                                                        <tr>
                                                            <td class="min-w-150px">
                                                                <div class="d-flex flex-column">
                                                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}
                                                                    </a>
                                                                    <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="min-w-150px">
                                                                <div class="d-flex flex-column"> 
                                                                    <span class="badge fw-bolder fs-6 {{ @$prev_tiles_bg }} px-2 py-1">{{ ucwords(@$prev_process_name) }}</span> 
                                                                    <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->prev_process_child_name) }}</span> 
                                                                </div>
                                                            </td>
                                                            <td class="min-w-50px">
                                                                <div class="d-flex flex-column"> 
                                                                   <span class="text-dark fw-bolder fs-6"> To </span> 
                                                               </div>
                                                            </td>
                                                            <td class="min-w-150px">
                                                                <div class="d-flex flex-column"> 
                                                                    <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span> 
                                                                    <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->child_process_name) }}</span> 
                                                                </div>
                                                            </td>
                                                            <td class="min-w-120px">
                                                                <div class="d-flex flex-column"> 
                                                                    <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
                                                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span> 
                                                                </div>
                                                            </td>
                                                            <td class="min-w-50px">
                                                                <div class="d-flex flex-column">
                                                                    <div class="symbol-group symbol-hover justify-content-center">
                                                                        <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$rowpostedby) }}">
                                                                            <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$rowpostedby)}}" >
                                                                                <span class="text-dark fw-bolder fs-6">{{ substr($rowpostedby, 0, 10)}}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                @if(!empty($comment))
                                                <div class="overflow-auto pb-5">
                                                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5"> 
                                                        <div class="mb-1">
                                                            <p class="<?php //echo @$current_tiles_bg; ?>"> 
                                                                <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                                                {{ @$comment }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    <?php 
                                    }else if($tdata->event_id==3)
                                    {
                                        $interview_schedule_date	=	$newDate	=	$newTime =	$interview_mode	=	$interview_mode_child_level	= $interviewers = $panellistArr = 	"";
                                        $interview_schedule_date	=	$tdata->interview_schedule_date;
                                        $interview_to_time	=	$tdata->interview_to_time;
                                        $newDate 	=	date("d-M-Y", strtotime($interview_schedule_date));
                                        $newTime 	=	date("h:i A", strtotime(@$interview_to_time));
            
                                        if($tdata->interview_mode > 0){
                                            $interview_mode_Arr		= 	GeneralMethod::interview_mode();
                                            $interview_mode			= 	$interview_mode_Arr[@$tdata->interview_mode];				
                                        }
            
                                        $interview_mode_child_level	=	$tdata->interview_mode_child_level;	
                                        if($tdata->interview_mode==3){
                                        $interview_mode_child_level	=	@$tdata->interview_mention_link;	
                                        }
            
                                        $interviewers				=	$tdata->interviewers;
                                        if(!empty($interviewers)){
                                        $panellistArr 			=	explode(',', $interviewers);	
                                        }	
                    
                                        $average_star_rating	=	'';
                                        $average_star_rating	=	$tdata->average_star_rating;	
                                        $imagePath				=	$text = 	'';
            
                                        if($average_star_rating == 1){
                                        $imagePath	=	url('public/img/star/1.png');
                                        $text		=	'Bad';
                                        }else if($average_star_rating == 2){
                                        $imagePath	=	url('public/img/star/2.png');
                                        $text		=	'Poor';
                                        }else if($average_star_rating == 3){
                                        $imagePath	=	url('public/img/star/3.png');
                                        $text		=	'Average';
                                        }else if($average_star_rating == 4){
                                        $imagePath	=	url('public/img/star/4.png');
                                        $text		=	'Best';
                                        }else if($average_star_rating == 5){
                                        $imagePath	=	url('public/img/star/5.png');	
                                        $text		=	'Very Best';
                                        }else{
                                        $imagePath	=	url('public/img/star/5-blank.png');
                                        $text		=	'Blank';
                                        }	 		 
            
                                        ?>
                                        <div class="timeline-item">
                                            <!--begin::Timeline line-->
                                            <div class="timeline-line w-40px"></div>
                                            <!--end::Timeline line-->
                                            <!--begin::Timeline icon-->
                                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                <div class="symbol-label bg-light">
                                                    <!--begin::Svg Icon-->
                                                    <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                                <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                                <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                            </div>
                                            <!--end::Timeline icon-->
                                            <!--begin::Timeline content-->
                                            <div class="timeline-content mb-10 mt-n1">
                                                <!--begin::Table-->
                                                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                                                    <!--begin::Table body-->
                                                    <tbody class="text-gray-600 fw-bold">
                                                        <!--begin::Table row-->
                                                        <tr>
                                                            <!--begin::Requisitions=-->
                                                            <td class="min-w-150px">
                                                                <div class="d-flex flex-column">
                                                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"> {{ ucwords(@$tdata->title)}} </a>
                                                                    <span class="fw-bold text-muted d-block fs-7"> {{ ucwords(@$company_or_client) }}  </span>
                                                                </div>
                                                            </td>
                                                            <!--end::Requisitions=-->
                                                            <!--begin::Interview Mode=-->
                                                            <td class="min-w-100px">
                                                                <div class="d-flex flex-column">
                                                                    <span class="text-dark fw-bolder fs-6">{{ @$interview_mode }}</span> 
                                                                    <span class="fw-bold text-muted d-block fs-7">{{ @$interview_mode_child_level }}</span>
                                                                </div>
                                                            </td>
                                                            <!--end::Interview Mode=-->
                                                            <!--begin::Interviewers=-->
                                                            <td class="min-w-90px">
                                                                <div class="d-flex flex-column">
                                                                    <div class="symbol-group symbol-hover">
                                                                        @php 
                                                                            if(!empty($panellistArr))
                                                                            {
                                                                                foreach($panellistArr as $pval)
                                                                                {
                                                                                    $bgTextDecore = $strVal = $team_full_name	=	 '';
                                                                                    $str 			= $team_full_name =  trim(@$pval);
                                                                                    $str 			= $strVal = strtolower($str[0]);
                                                                                    $str = ord($str);
                                                                                    if ($str >= 97 && $str <= 101) {
                                                                                    $bgTextDecore = 'fs-8 bg-light-danger text-danger';
                                                                                    } 
                                                                                    else if ($str >= 102 && $str <= 106) {
                                                                                    $bgTextDecore = 'fs-8 bg-light-warning text-warning';
                                                                                    } 
                                                                                    else if ($str >= 107 && $str <= 111) {
                                                                                    $bgTextDecore = 'fs-8 bg-light-primary text-primary';
                                                                                    } 
                                                                                    else if ($str >= 112 && $str <= 116) {
                                                                                    $bgTextDecore = 'fs-8 bg-light-success text-success';
                                                                                    } 
                                                                                    else if ($str >= 117 && $str <= 121) {
                                                                                    $bgTextDecore = 'fs-8 bg-dark text-secondary';
                                                                                    } else {
                                                                                    $bgTextDecore = 'fs-8 bg-light-secondary text-dark';
                                                                                    }									
                                                                                    echo '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" data-bs-original-title="'.ucwords(@$team_full_name).'">
                                                                                    <div class="symbol-label '.@$bgTextDecore.'">'.strtoupper(@$strVal).'</div>
                                                                                    
                                                                                    </div>';	
                                                                                }
                                                                            }	
                                                                        @endphp
                                                                    </div>Interviewer
                                                                </div>
                                                            </td>
                                                            <!--end::Interviewers=-->
                                                            <!--begin::Interviewer Feedback=-->
                                                            <td class="min-w-70px">
                                                                <div class="d-flex flex-column">
                                                                    <span class="text-dark fw-bolder  fs-6">{{ ($feedback) ? $feedback : '----' }}
                                                                    </span> 
                                                                    <span class="fw-bold text-muted d-block fs-7">{{ ($available_status_name ? $available_status_name : '----') }}
                                                                    </span> 
                                                                </div>
                                                            </td>
                                                            <!--end::Interviewer Feedback-->
                                                            <!--begin::Soruce-->
                                                             <td class="min-w-50px">
                                                                <div class="d-flex flex-column">
                                                                    <div class="symbol-group symbol-hover justify-content-center">
                                                                        <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$rowpostedby) }}">
                                                                            <div class="" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="{{ ucwords(@$rowpostedby)}}" >
                                                                                <span class="text-dark fw-bolder fs-6">{{ substr($rowpostedby, 0, 10)}}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{ ucwords(@$tdata->cur_companyname) }}">
                                                                        {{ substr(ucwords(@$tdata->cur_companyname), 0, 10) }}
                                                                    </a> 
                                                                </div>
                                                            </td>
                                                            <!--end::Soruce-->
                                                            <!--begin::Image-->
                                                            <td class="min-w-90px">
                                                                <img src="{{  str_replace('index.php','',@$imagePath) }}" alt="image" style="width: 5.5rem">
                                                                <span class="fw-bold text-muted d-block fs-7">{{ @$text }}</span> 
                                                            </td>
                                                            <!--end::Image-->
                                                            <!--begin::Date-->
                                                            <td class="min-w-140px">
                                                                <div class="d-flex flex-column">
                                                                    <span class="text-dark fw-bolder fs-6"> {{ @$newDate}} </span>
                                                                    <span class="text-muted fw-bold d-block fs-7"> {{ @$newTime }} {{ (@$tdata->timezone_abbreviation) ? '('.@$tdata->timezone_abbreviation.')' :'' }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <!--end::Date-->
                                                            <!--begin::Status-->
                                                            <td class="min-w-120px">
                                                                <div class="d-flex flex-column"> 
                                                                    <span class="badge fw-bolder fs-6 {{ @$tdata->css_style }} px-2 py-1">{{ ucwords(@$tdata->status_name) }}</span> 
                                                                    <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->process_child_name) }}</span> 
                                                                </div>
                                                            </td>
                                                            <!--end::Stage-->
                                                        </tr>
                                                         <!--end::Table row-->
                                                    </tbody>
                                                    <!--end::Table body-->
                                                </table>
                                                <!--end::Table-->
                                            @php 
                                                    //interview_feedback  application_id    interview_schedules_id    
                                                    $interview_feedback 	= DB::table('interview_feedback')
                                                    ->select( 'interview_feedback.created_at', 'interview_feedback.message', 'interview_feedback.star_ratings','users.name', 'users.image')
                                                    ->join('users', 'users.id', '=', 'interview_feedback.update_by')
                                                    ->where('interview_feedback.application_id', @$tdata->application_id)
                                                    ->where('interview_feedback.interview_id', @$tdata->interview_schedules_id)
                                                    ->get(); 

                                                    $feedbackdataView ='';
                                                    $feedbackdata = DB::table('interview_skills_feedback')->where('interview_id',$tdata->interview_schedules_id)->orderByDesc('feedback_id')->count();
                                                    if($feedbackdata>0){
                                                        $feedbackdataView = '<div class="overflow-auto pb-5">
                                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                                            <span href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                                            &nbsp;Interview Feedback 
                                                            </span>  <span class="badge skill-feedback fw-bolder fs-6 interview px-2 py-1" data-row-id="' .$tdata->interview_schedules_id .'" style="cursor: pointer; margin-left: 4px; margin-top: 5px;">View</span>
                                                        </div>
                                                    </div>';
                                                    }else{
                                                    $feedbackdataView = '<div class="overflow-auto pb-5">
                                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                                            <span href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
                                                            &nbsp;Interview Feedback Pending
                                                            </span> 
                                                        </div>
                                                    </div>';
                                                    }

                                            @endphp
                                                {!! $feedbackdataView !!}
                                            @php 
                                                if(count($interview_feedback) > 0){
                                                    foreach($interview_feedback as $intfeed){
                                                        $newDate 		=	date("d-m-Y", strtotime($intfeed->created_at));	
                                                        $newTime 		=	date("H:i A", strtotime($intfeed->created_at));	
                                                        $bgTextDecore				=	GeneralMethod::icoinText(@$intfeed->name);	
                                                        $imagePath	=	'';
                                                        if(!empty($intfeed->image)){
                                                            $imagePath	=	@$intfeed->image;	
                                                            }      
                                                        $feedback_star_imagePath	=	$feedback_star_text =  '';
                                                        $feedbackstar_ratings				=	$intfeed->star_ratings;
                                                        if($feedbackstar_ratings == 1){
                                                            $feedback_star_imagePath	=	url('public/img/star/1.png');
                                                            $feedback_star_text			=	'Bad';
                                                        }else if($feedbackstar_ratings == 2){
                                                            $feedback_star_imagePath	=	url('public/img/star/2.png');
                                                            $feedback_star_text			=	'Poor';
                                                        }else if($feedbackstar_ratings == 3){
                                                            $feedback_star_imagePath	=	url('public/img/star/3.png');
                                                            $feedback_star_text			=	'Average';
                                                        }else if($feedbackstar_ratings == 4){
                                                            $feedback_star_imagePath	=	url('public/img/star/4.png');
                                                            $feedback_star_text			=	'Best';
                                                        }else if($feedbackstar_ratings == 5){
                                                            $feedback_star_imagePath	=	url('public/img/star/5.png');	
                                                            $feedback_star_text			=	'Very Best';
                                                        }else{
                                                            $feedback_star_imagePath	=	url('public/img/star/5-blank.png');
                                                            $feedback_star_text			=	'Blank';
                                                        }	
                                            @endphp
                                            @php 
                                                    } 
                                                }	
                                            @endphp
                                        </div>
                                        <!--end::Timeline content-->
                                    </div>
                            <?php 
                                    }
                                    else if($tdata->event_id == 4)
                                    {
                            
                                        $currency 				=	GeneralMethod::currency();
                                        $salaryMode 			=	GeneralMethod::salaryModeOffer();
                                        
                                        $offer_currency			=	'';
                                        if($tdata->offer_currency >= 0){
                                        $offer_currency			= $currency[(int)@$tdata->offer_currency];
                                        }
                                        $offer_mode			=	'';
                                        if($tdata->offer_mode >= 0){
                                        $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                                        }
                    
                                        $joined_time = '';
                                        if(!empty($tdata->joined_date)){
                                            $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                                        }
                                
                            ?>
                            <div class="timeline-item">
                <!--begin::Timeline line-->
                <div class="timeline-line w-40px"></div>
                <!--end::Timeline line-->
                <!--begin::Timeline icon-->
                <div class="timeline-icon symbol symbol-circle symbol-40px">
                  <div class="symbol-label bg-light">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </div>
                </div>
                <!--end::Timeline icon-->
                <!--begin::Timeline content-->
                <div class="timeline-content mb-10 mt-n1">
                  <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1269px;">
                    <thead>
                      <tr class="fw-bolder text-muted" role="row">
                        <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Created</th>
                        <th class="sorting_disabled">Offer Amount</th>
                        <th class="sorting_disabled" style="width: 244px;">Joining Date</th>
                        <th class="sorting_disabled" style="width: 79px;">Offer Status</th>
                        <th class="sorting_disabled" style="width: 79px;">Create Date</th>
                        <th class="sorting_disabled" style="width: 79px;">Stage</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr role="row" class="odd">
                                  <td class="min-w-150px">
                                      <div class="d-flex flex-column"> 
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a> 
                                        <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span> 
                                      </div>
                                  </td>

                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                       @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                     <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->yearly_final_gross) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif

                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{$joined_time }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'screening' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }}</span>
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->create_at) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->create_at)) }}(IST)</span>
                                  </div>
                                </td>
                                
                                  
                                 <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>

                                <td class="min-w-50px"><div class="d-flex flex-column">
                                  <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$rowpostedby) }}"> 
                                              
                                              <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$rowpostedby)}}" >
                                                <span class="text-dark fw-bolder fs-6">{{ substr($rowpostedby, 0, 10)}}</span>
                                                </div>
                                             

                                              </div>
                                                        </div>
                                                        <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                          <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                          <!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
                                
                                  </div>
							                  </td>


                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
            <?php }else if($tdata->event_id == 10)
          {
            
            $currency 				=	GeneralMethod::currency();
            $salaryMode 			=	GeneralMethod::salaryModeOffer();
            
            $offer_currency			=	'';
            if($tdata->offer_currency >= 0){
              $offer_currency			= $currency[(int)@$tdata->offer_currency];
            }
            $offer_mode			=	'';
            if($tdata->offer_mode >= 0){
              $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
            }
            
            $joined_time = '';
                if(!empty($tdata->joined_date)){
                   $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                 }
            
          ?>
              <div class="timeline-item">
                <!--begin::Timeline line-->
                <div class="timeline-line w-40px"></div>
                <!--end::Timeline line-->
                <!--begin::Timeline icon-->
                <div class="timeline-icon symbol symbol-circle symbol-40px">
                  <div class="symbol-label bg-light">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"/>
                        <path d="M4.85714286,1 L11.7364114,1 C12.0910962,1 12.4343066,1.12568431 12.7051108,1.35473959 L17.4686994,5.3839416 C17.8056532,5.66894833 18,6.08787823 18,6.52920201 L18,19.0833333 C18,20.8738751 17.9795521,21 16.1428571,21 L4.85714286,21 C3.02044787,21 3,20.8738751 3,19.0833333 L3,2.91666667 C3,1.12612489 3.02044787,1 4.85714286,1 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        <path d="M6.85714286,3 L14.7364114,3 C15.0910962,3 15.4343066,3.12568431 15.7051108,3.35473959 L20.4686994,7.3839416 C20.8056532,7.66894833 21,8.08787823 21,8.52920201 L21,21.0833333 C21,22.8738751 20.9795521,23 19.1428571,23 L6.85714286,23 C5.02044787,23 5,22.8738751 5,21.0833333 L5,4.91666667 C5,3.12612489 5.02044787,3 6.85714286,3 Z M8,12 C7.44771525,12 7,12.4477153 7,13 C7,13.5522847 7.44771525,14 8,14 L15,14 C15.5522847,14 16,13.5522847 16,13 C16,12.4477153 15.5522847,12 15,12 L8,12 Z M8,16 C7.44771525,16 7,16.4477153 7,17 C7,17.5522847 7.44771525,18 8,18 L11,18 C11.5522847,18 12,17.5522847 12,17 C12,16.4477153 11.5522847,16 11,16 L8,16 Z" fill="#000000" fill-rule="nonzero"/>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </div>
                </div>
                <!--end::Timeline icon-->
                <!--begin::Timeline content-->
                <div class="timeline-content mb-10 mt-n1">
                  <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer dtr-inline" id="myTable" role="grid" aria-describedby="myTable_info" style="width: 1269px;">
                    <thead>
                      <tr class="fw-bolder text-muted" role="row">
                        <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Expired</th>
                        <th class="sorting_disabled">Offer Amount</th>
                        <th class="sorting_disabled" style="width: 244px;">Joining Date</th>
                        <th class="sorting_disabled" style="width: 79px;">Offer Status</th>
                        <th class="sorting_disabled" style="width: 79px;">Expired Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr role="row" class="odd">
                                  <td class="min-w-150px">
                                      <div class="d-flex flex-column"> 
                                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a> 
                                        <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span> 
                                      </div>
                                  </td>

                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                      <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif

                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-120px">
                                    <div class="d-flex flex-column">
                                      <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{$joined_time }} </span>
                                    </div>
                                  </td>
                                  <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==4) ? 'rejected' : 'screening' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }}</span>
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->create_at) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->create_at)) }}(IST)</span>
                                  </div>
                                </td>
                                
                                  
                          

                                <td class="min-w-50px"><div class="d-flex flex-column">
                                  <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$rowpostedby) }}"> 
                                              
                                              <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$rowpostedby)}}" >
                                                <span class="text-dark fw-bolder fs-6">{{ substr($rowpostedby, 0, 10)}}</span>
                                                </div>
                                             

                                              </div>
                                                        </div>
                                                        <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                          <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                          <!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
                                
                                  </div>
							                  </td>


                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
            <?php }else if($tdata->event_id == 5) {
                 $currency 				=	GeneralMethod::currency();
                $salaryMode 			=	GeneralMethod::salaryModeOffer();
              
                $offer_currency			=	'';
                if($tdata->offer_currency >= 0){
                  $offer_currency			= $currency[(int)@$tdata->offer_currency];
                }
                $offer_mode			=	'';
                if($tdata->offer_mode >= 0){
                  $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                }
                $joined_time = '';
                if(!empty($tdata->joined_date)){
                   $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                 }
               
              ?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Offer Amount</th>
                                <th class="sorting_disabled" style="width: 79px;">Join Date</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$application->full_name) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{ucwords((@$tdata->designation_offer))}}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                     <div class="d-flex flex-column">

                                      @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                        <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$joined_time }} </span>
                                  </div>
                                </td>

                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->send_accept_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->send_accept_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>

                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
            <?php }else if($tdata->event_id == 6){ ?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Approval</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{
                                      ucwords(@$tdata->email_id_hr) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{
                                      ucwords(@$tdata->name_hr) }}</span>
                                  </div>
                                </td>
                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->send_approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->send_approved_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
           
            <?php }else if($tdata->event_id == 7){
              
                $currency 				=	GeneralMethod::currency();
                $salaryMode 			=	GeneralMethod::salaryModeOffer();
              
                $offer_currency			=	'';
                if($tdata->offer_currency >= 0){
                  $offer_currency			= $currency[(int)@$tdata->offer_currency];
                }
                $offer_mode			=	'';
                if($tdata->offer_mode >= 0){
                  $offer_mode			= $salaryMode[(int)@$tdata->offer_mode];
                }
                $joined_time = '';
                if(!empty($tdata->joined_date)){
                   $joined_time = date("H:i a", strtotime(@$tdata->joined_date));
                 }

              ?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Offer Amount</th>
                                <th class="sorting_disabled" style="width: 79px;">Join Date</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$application->full_name) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{ucwords((@$tdata->designation_offer))}}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                   <div class="d-flex flex-column">
                                    @php
                                      $offerAmont = "Confidential";
                                        $allow =  GeneralMethod::confidentialAmontShow($tdata->job_id);  
                                      @endphp
                                      @if($allow)
                                        <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->offer_amount) }} {{ @$offer_mode }}</span>
                                      @else
                                      <span class="text-dark fw-bolder fs-6">{{ $offerAmont }}</span>
                                      @endif
                                      
                                      <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                    </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$joined_time }} </span>
                                  </div>
                                </td>

                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->candidate_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$offer_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->accept_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->accept_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
         
            <?php }else if($tdata->event_id == 8){?>
                  <div class="timeline-item">
                        <div class="timeline-line w-40px"></div>
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                          <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24" />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12"
                                    y="8.8817842e-16" width="2" height="12" rx="1" />
                                  <path
                                    d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) " />
                                  <rect fill="#000000" opacity="0.3"
                                    transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) "
                                    x="10" y="12" width="2" height="12" rx="1" />
                                  <path
                                    d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z"
                                    fill="#000000" fill-rule="nonzero"
                                    transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) " />
                                </g>
                              </svg>
                            </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24" />
                                  <path
                                    d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z"
                                    fill="#000000" fill-rule="nonzero" />
                                </g>
                              </svg>
                            </span> </div>
                        </div>
                        <div class="timeline-content mb-10 mt-n1">
                          <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                            <thead>
                              <tr class="fw-bolder text-muted" role="row">
                                <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Approval</th>
                                <th class="sorting_disabled" style="width: 79px;">Status</th>
                                <th class="sorting_disabled" style="width: 79px;">Date/Time</th>
                                <th class="sorting_disabled" style="width: 79px;">Stage</th>
                              </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold">
                              <tr>
                                <td class="min-w-150px" rowspan="1" colspan="1">
                                  <div class="d-flex flex-column">
                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{
                                      ucwords(@$tdata->email_id_hr) }}</a>
                                    <span class="fw-bold text-muted d-block fs-7">{{
                                      ucwords(@$tdata->name_hr) }}</span>
                                  </div>
                                </td>
                                <td class="min-w-150px">
                                  <div class="d-flex flex-column">
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==2) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i", strtotime(@$tdata->approved_date_time)) }}(IST)</span>
                                  </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>
                              </tr>
                            </tbody>
                          </table>

                          @if(!empty($comment))
                          <div class="overflow-auto pb-5">
                            <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                              <div class="mb-1">
                                <p class="<?php //echo @$current_tiles_bg; ?>">
                                  <a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">&nbsp;Comment:</a>
                                  {{ @$comment }}
                                </p>
                              </div>
                            </div>
                          </div>
                          @endif
                        </div>
                  </div>
            <?php }else if($tdata->event_id == 9){?>
              <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column"> 
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Mail</a> 
                          <span class="fw-bold text-muted d-block fs-7">Send</span> 
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column"> 
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a> 
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span> 
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column"> 
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>    
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$rowpostedby) }}"> 
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}"> 
                  @else --}}
                 
                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$rowpostedby)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr($rowpostedby, 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
							
							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>      

            <?php }else if($tdata->event_id == 11){?>
              <div class="timeline-item">
              <div class="timeline-line w-40px"></div>
              <div class="timeline-icon symbol symbol-circle symbol-40px">
                <div class="symbol-label bg-light"> <span class="svg-icon svg-icon-2 svg-icon-gray-500 d-none">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                      <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                      <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                      <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                    </g>
                  </svg>
                  </span> <span class="svg-icon svg-icon-2 svg-icon-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M21,5.5 L21,17.5 C21,18.3284271 20.3284271,19 19.5,19 L4.5,19 C3.67157288,19 3,18.3284271 3,17.5 L3,14.5 C3,13.6715729 3.67157288,13 4.5,13 L9,13 L9,9.5 C9,8.67157288 9.67157288,8 10.5,8 L15,8 L15,5.5 C15,4.67157288 15.6715729,4 16.5,4 L19.5,4 C20.3284271,4 21,4.67157288 21,5.5 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> </div>
              </div>
              <div class="timeline-content mb-10 mt-n1">
                <table class="table align-middle table-row-dashed fs-6 gy-0 mb-5" id="">
                  <tbody class="text-gray-600 fw-bold">
                    <tr>
                      <td class="min-w-150px">
                        <div class="d-flex flex-column"> 
                          <a href="javascript:void(0);" class="text-{{($tdata->note=='Interested') ? 'success' : 'danger'}} fw-bolder text-hover-{{($tdata->note=='Interested') ? 'success' : 'danger'}} fs-6">{{@$tdata->note}}</a> 
                          <span class="fw-bold text-muted d-block fs-7">Mail</span> 
                        </div>
						         </td>
                    <td class="min-w-150px" colspan="2">
                      <div class="d-flex flex-column"> 
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Subject</a> 
                        <span class="text-muted fw-bold d-block fs-7">    {{$tdata->comment}}   </span> 
                      </div>
                    </td>
                  <td class="min-w-120px">
                      <div class="d-flex flex-column"> 
                        <span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}(IST)</span>    
                      </div>
						      </td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$rowpostedby) }}"> 
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$rowpostedby) }}" src="{{ @$posted_by_image }}"> 
                  @else --}}
                 
                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$rowpostedby)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr($rowpostedby, 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$rowpostedby) }}"> {{ substr(ucwords(@$rowpostedby), 0, 7) }} </a> -->
							
							</div>
							 </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>      
            <?php } ?>

       <?php }
      } ?>

            </div>
          </div>
		  
		  
        </div>
                      
                    <!--begin::Request-->
                    <div class="tab-pane fade show" id="request" role="tabpanel">
                        <!--begin::Request header-->
                        <div class="card-header cursor-pointer px-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Request</h3>
                            </div>
                            <!--end::Card title-->

                            <!--end::Action-->
                        </div>
                        <!--end::Request header-->
                        <!--begin::Request Content-->
                        <div class="card-body px-0 py-7 d-none">
                            <!--begin:Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="fw-bolder text-muted">
                                        <th class="w-10px pe-0">
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_table_users .form-check-input1"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="">Requisition</th>
                                        <th class="">Exp.</th>
                                        <th class="">Salary</th>
                                        <th class="">Location</th>
                                        <th class="">Status</th>
                                        <th class="">Date</th>
                                        <th class="">HM | CP</th>
                                        <th class="">S.Partners</th>
                                        <th class="">TOT</th>
                                        <th class="">SOU</th>
                                        <th class="">SCR</th>
                                        <th class="">EVA</th>
                                        <th class="">NEO</th>
                                        <th class="">OFF</th>
                                        <th class="">JOI</th>
                                        <th class="">REJ</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="text-gray-600 fw-bold">
                                    <!--begin::Table row-->
                                    <tr class="">
                                        <!--begin::Checkbox-->
                                        <td class="">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input form-check-input1" type="checkbox"
                                                    value="1" />
                                            </div>
                                        </td>
                                        <!--end::Checkbox-->
                                        <!--begin::Requisitions=-->
                                        <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                                            <div
                                                class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5">
                                                <a href="javascript:void(0);">
                                                    <div class="symbol-label border-radius-5"> <img
                                                            src="https://ayudawp.com/wp-content/uploads/2011/08/wordpress-icono.png"
                                                            alt="Company Logo" class="border-radius-5 w-100"> </div>
                                                </a>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::User details-->
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder text-hover-primary fs-6">Wordpress
                                                    Developer</a> <a href="javascript:void(0);"
                                                    class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech
                                                    Mahindra</a> </div>
                                            <!--begin::User details-->
                                        </td>
                                        <!--end::Requisitions-->
                                        <!--begin::Exp.-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6">14+</span> <span
                                                    class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div>
                                        </td>
                                        <!--end::Exp.-->
                                        <!--begin::Salary-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">18
                                                    LPA</span> <span class="fw-bold d-block fs-7">12 LPA</span> </div>
                                        </td>
                                        <!--end::Salary-->
                                        <!--begin::Location-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6">Greater Noida</span>
                                                <div class="d-flex align-items-center"> <span
                                                        class="fw-bold text-muted d-block fs-7">U.P</span>
                                                    &nbsp;|&nbsp; <span
                                                        class="fw-bold text-muted d-block fs-7">INDIA</span>
                                                    <div> </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!--end::Location-->
                                        <!--begin::Status | Requisition ID-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="fs-6 active-bg text-hover-success">Active</a> <span
                                                    class="fw-bold text-muted d-block fs-7">R1</span> </div>
                                        </td>
                                        <!--end::Status | Requisition ID-->
                                        <!--begin::Date | Openings-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <span
                                                    class="text-dark fw-bolder fs-6">05-Jul-2021</span> <span
                                                    class="text-muted fw-bold d-block fs-7">3,974</span> </div>
                                        </td>
                                        <!--end::Date | Openings-->
                                        <!--begin::Hiring Manager | Contact Person-->
                                        <td class="">
                                            <div class="d-flex flex-column">
                                                <div class="symbol-group symbol-hover">
                                                    <div class="symbol symbol-30 symbol-circle"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Promod"> <img alt="Pic"
                                                            src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg">
                                                    </div>
                                                    <div class="symbol symbol-30 symbol-circle symbol-light"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-original-title="More users list.."> <span
                                                            class="symbol-label font-weight-bold">1+</span> </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!--end::Hiring Manager | Contact Person-->
                                        <!--begin::Staffing Partners-->
                                        <td class="">
                                            <div class="d-flex flex-column">
                                                <div class="symbol-group symbol-hover">
                                                    <div class="symbol symbol-30 symbol-circle"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-original-title="Amit Kumar"> <img
                                                            alt="Pic"
                                                            src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg">
                                                    </div>
                                                    <div class="symbol symbol-30 symbol-circle"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-original-title="Pradeep"> <img
                                                            alt="Pic"
                                                            src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg">
                                                    </div>
                                                    <div class="symbol symbol-30 symbol-circle symbol-light"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-original-title="More users list.."> <span
                                                            class="symbol-label font-weight-bold">2+</span> </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!--end::Staffing Partners-->
                                        <!--begin::TOT-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">3,294</a> <a
                                                    href="javascript:void(0);"
                                                    class="text-muted fw-bold d-block fs-7 text-hover-primary">58</a>
                                            </div>
                                        </td>
                                        <!--end::TOT-->
                                        <!--begin::SOU-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                        </td>
                                        <!--end::SOU-->
                                        <!--begin::SCR-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">34</a> </div>
                                        </td>
                                        <!--end::SCR-->
                                        <!--begin::EVA-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                        </td>
                                        <!--end::EVA-->
                                        <!--begin::NEO-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">29</a> </div>
                                        </td>
                                        <!--end::NEO-->
                                        <!--begin::OFF-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">32</a> </div>
                                        </td>
                                        <!--end::OFF-->
                                        <!--begin::JOI-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div>
                                        </td>
                                        <!--end::JOI-->
                                        <!--begin::REJ-->
                                        <td class="">
                                            <div class="d-flex flex-column"> <a href="javascript:void(0);"
                                                    class="text-dark fw-bolder fs-6 text-hover-primary">384</a> </div>
                                        </td>
                                        <!--end::REJ-->
                                    </tr>
                                    <!--end::Table row-->
                                </tbody>
                                <!--end::Table body-->
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Request Content-->
                    </div>
                    <!--end::Request-->
                    {{-- </div> --}}
                    <!--end::Team Content-->
                    {{-- </div> --}}
                    <!--eng::Team Content-->
                    <!--begin::Interviews Content-->
                    <div class="tab-pane" id="interviews" role="tabpanel">
                        <!--begin::Contacts header-->
                        <div class="card-header cursor-pointer px-0 d-none">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Interview</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <div class="card-body px-0 py-7">
                            @if (in_array('view_schedule', $userPermissions))
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="interviewData">
                                    <thead>
                                        <tr class="fw-bolder text-muted">
                                            <th class="w-10px pe-0">
                                                <div
                                                    class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-kt-check="true"
                                                        data-kt-check-target="#kt_table_users form-check-input-itracker"
                                                        value="1" />
                                                </div>
                                            </th>
                                            <th class="">Name</th>
                                            <th class="">Mode</th>
                                            <th class="">Panel</th>
                                            <th class="">Source</th>
                                            <th class="">Date & Time</th>
                                            <th class="">Feedback</th>
                                            <th class="">Rating</th>
                                            <th class="">Stage</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @if ($interviewShedule)
                                            <?php
                                            $feedback_status = GeneralMethod::feedback_status();
                                            $company_id = auth()->user()->company_id;
                                            ?>
                                            @foreach ($interviewShedule as $row)
                                                <tr>
                                                    <td class="">
                                                        <div
                                                            class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                data-kt-check="true"
                                                                data-kt-check-target="#kt_table_users .form-check-input-two"
                                                                value="1" />
                                                        </div>
                                                    </td>
                                                    <td class="">
                                                        <?php
                                                        
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
                                                        
                                                        $profile_detail = 'show-application-detail';
                                                        if ($row->thirdparty_company_id == $company_id) {
                                                            $profile_detail = 'client-show-application-detail';
                                                        }
                                                        
                                                        echo '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                                                                                                                                                                                                                <a href="javascript:void(0);" data-row-id="' .
                                                            $row->wiseID .
                                                            '" class="' .
                                                            $profile_detail .
                                                            '">
                                                                                                                                                                                                                  <div data-row-id="' .
                                                            $row->wiseID .
                                                            '"  class="symbol-label ' .
                                                            $bgTextDecore .
                                                            ' border-radius-5 ' .
                                                            $profile_detail .
                                                            '">' .
                                                            ucwords($strVal) .
                                                            '</div>
                                                                                                                                                                                                                </a>
                                                                                                                                                                                                              </div>
                                                                                                                                                                                                              <div class="d-flex flex-column">
                                                                                                                                                                                                                <a href="javascript:void(0);" data-row-id="' .
                                                            $row->wiseID .
                                                            '" class="text-dark fw-bolder text-hover-primary fs-6 ' .
                                                            $profile_detail .
                                                            '" data-toggle="tooltip" title="' .
                                                            ucwords(@$row->full_name) .
                                                            '">' .
                                                            ucwords(mb_substr(@$row->full_name, 0, 15)) .
                                                            '</a>
                                                                                                                                                                                                                <a href="javascript:void(0);" data-row-id="' .
                                                            $row->wiseID .
                                                            '" class="fw-bold text-muted d-block fs-7 ' .
                                                            $profile_detail .
                                                            ' text-hover-primary" data-toggle="tooltip" title="' .
                                                            ucwords(@$row->current_designation) .
                                                            '">' .
                                                            ucwords(mb_substr(@$row->current_designation, 0, 20)) .
                                                            '</a>
                                                                                                                                                                                                              </div>';
                                                        ?>


                                                    </td>
                                                    <td class="">
                                                        <?php
                                                        
                                                        echo '<div class="d-flex flex-column">
                                                                                                                                                                                                  <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                                                            ucwords(mb_substr($row->mode_name, 0, 10)) .
                                                            '</a> 
                                                                                                                                                                                                  <span class="text-muted fw-bold d-block fs-7">' .
                                                            ucwords(mb_substr($row->mode_lavel_name, 0, 10)) .
                                                            '</span> 
                                                                                                                                                                                                </div>';
                                                        ?>

                                                    </td>
                                                    <td class="">
                                                        <?php
                                                        $contactHtml = $real_company_id = $interviewers = '';
                                                        $interviewers = $row->interviewers;
                                                        $real_company_id = $row->real_company_id;
                                                        
                                                        $contactHtml = '<div class="d-flex flex-column">
                                                                                                                                                                                                                            <div class="symbol-group symbol-hover">';
                                                        if (!empty($interviewers)) {
                                                            $interviewersArr = explode(' , ', trim($interviewers));
                                                            $interviewersData = DB::table('users')->select('users.id', 'users.name', 'users.image')->whereIn('users.name', $interviewersArr)->where('users.company_id', '=', $real_company_id)->get();
                                                            if (!empty($interviewersData)) {
                                                                foreach ($interviewersData as $intVal) {
                                                                    $image_path = '';
                                                                    $image_path = url('public/avatar.png');
                                                                    if ($intVal->image != '') {
                                                                        $image_path = url('public/user-uploads/team_profile_img/' . $intVal->image);
                                                                    }
                                                                    $contactHtml .=
                                                                        '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip1" data-html="true" title="' .
                                                                        ucwords(@$intVal->name) .
                                                                        '" >
                                                                                                                                                                                                          <img alt="Pic" src="' .
                                                                        $image_path .
                                                                        '">
                                                                                                                                                                                                          </div>';
                                                                }
                                                            }
                                                        }
                                                        $contactHtml .= '</div>
                                                                                                                                                                                                                    </div>';
                                                        
                                                        ?>
                                                        {!! $contactHtml !!}
                                                    </td>
                                                    <td class="">

                                                        <?php
                                                        $posted_by_image = '';
                                                        $posted_by_image = url('public/avatar.png');
                                                        if (!empty($row->posted_by_image)) {
                                                            $posted_by_image = url('public/user-uploads/team_profile_img/' . @$row->posted_by_image);
                                                        }
                                                        echo '<div class="d-flex flex-column">
                                                                                                                                                                                                        <div class="symbol-group symbol-hover">
                                                                                                                                                                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$row->posted_by .'">
                                                                                                                                                                            <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-toggle="tooltip" title="" data-bs-original-title="bipul">'.ucwords(mb_substr(@$row->posted_by, 0, 10)) .'</a>
                                                                                                                                                                           
                                                                                                                                                                                                          </div>
                                                                                                                                                                                                        </div>
                                                                                                                                                                                                        <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="" data-bs-original-title="' .
                                                            @$row->uc_company_name .
                                                            '">' .
                                                            mb_substr(@$row->uc_company_name, 0, 10) .
                                                            '</span>
                                                                                                                                                                                                        ';
                                                        ?>


                                                    </td>
                                                    <td class="">
                                                        <?php
                                                        echo '<div class="d-flex flex-column">
                                                                                                                                                                                                      <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">' .
                                                            date('d-m-Y', strtotime($row->schedule_date)) .
                                                            '</a>
                                                                                                                                                                                                      <span class="text-muted fw-bold d-block fs-7">' .
                                                            date('H:i a', strtotime($row->schedule_date)) .
                                                            '</span>
                                                                                                                                                                                                    </div>';
                                                        ?>


                                                    </td>
                                                    <td class="">

                                                        <?php
                                                        $available_status_name = '';
                                                        if ($row->available_status > 0) {
                                                            $available_status_name = @$available_status[@$row->available_status]['name'];
                                                        } else {
                                                            //$available_status_name	=	@$available_status[6]['name'];
                                                        }
                                                        
                                                        $feedback = ucwords(mb_substr(@$feedback_status[@$row->feedback]['name'], 0, 20));
                                                        $customHtml = 'show-feedback';
                                                        
                                                        if ($row->thirdparty_company_id == $company_id) {
                                                            $customHtml = '';
                                                            if (empty($feedback)) {
                                                                // dd("dd",$feedback);
                                                                $feedback = '<span class="badge fw-bolder fs-6 px-2 py-1 sourcing-bg"> Pending Feedback</span>';
                                                            }
                                                        } else {
                                                            if (empty($feedback)) {
                                                                // dd("fhf",$feedback);
                                                              
                                                                $feedback = '<span class="badge fw-bolder fs-6 px-2 py-1 sourcing-bg"> Pending Feedback</span>';
                                                                $available_status_name = '';
                                                            }
                                                        }
                                                        
                                                        echo '<div class="d-flex flex-column">
                                                             <span class="text-dark fw-bolder text-hover-primary fs-6">' .ucwords(mb_substr(@$available_status_name, 0, 20)) .'</span>
                                                             <a href="javascript:void(0);" data-row-id="' .$row->id .'" class="text-muted fw-bold text-muted d-block fs-7' .$customHtml .'">' .@$feedback .'</a>
                                                                                                                                                                                                  </div>';
                                                        ?>


                                                    </td>
                                                    <td class="">

                                                        <?php
                                                        
                                                        $imagePath = $text = '';
                                                        $average_star_rating = '';
                                                        $average_star_rating = $row->average_star_rating;
                                                        if ($average_star_rating == 1) {
                                                            $imagePath = url('public/img/star/1.png');
                                                            $text = 'Bad';
                                                        } elseif ($average_star_rating == 2) {
                                                            $imagePath = url('public/img/star/2.png');
                                                            $text = 'Poor';
                                                        } elseif ($average_star_rating == 3) {
                                                            $imagePath = url('public/img/star/3.png');
                                                            $text = 'Average';
                                                        } elseif ($average_star_rating == 4) {
                                                            $imagePath = url('public/img/star/4.png');
                                                            $text = 'Best';
                                                        } elseif ($average_star_rating == 5) {
                                                            $imagePath = url('public/img/star/5.png');
                                                            $text = 'Very Best';
                                                        } else {
                                                            $imagePath = url('public/img/star/5-blank.png');
                                                            $text = 'Blank';
                                                        }
                                                        echo ' <img src="' .
                                                            @$imagePath .
                                                            '" alt="image" style="width: 5.5rem">
                                                                                                                                                                                                                    <span class="text-muted fw-bold text-muted d-block fs-7">' .
                                                            @$text .
                                                            '</span>';
                                                        
                                                        ?>

                                                    </td>
                                                    <td class="">
                                                        <?php
                                                        echo '<div class="d-flex flex-column"> 
                                                                                                                                                                                                          <span class="badge fw-bolder fs-6 px-2 py-1 ' .
                                                            @$row->css_style .
                                                            '">' .
                                                            mb_substr(@$row->master_status_name, 0, 10) .
                                                            '</span>
                                                                                                                                                                                                          <span class="text-muted fw-bold text-muted d-block fs-7">' .
                                                            mb_substr(@$row->child_status_name, 0, 10) .
                                                            '</span>
                                                                                                                                                                                                      </div>';
                                                        ?>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif


                                    </tbody>


                                </table>
                        </div>
                        @endif

                    </div>
                    <!--end::Interviews Content-->
                </div>
                <!--end::Body-->
            </div>
            <!--end:Tabs-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Body-->
</div>


 <!--begin::Add Interview Question Modal-->
    <div class="modal modal fade" tabindex="-1" aria-hidden="true" id="add_interview_question_modal_{{@$job->id}}">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" id="add_team_member_header_{{@$job->id}}" style="padding: 5px !important;">
                    <h2 class="fw-bolder">Add Interview Question</h2>
                    <div class="btn btn-icon btn-sm" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>


                </div>
                <div class="modal-body scroll-y mx-5">
                        <div class="row client-cperson-row">
                            <div class="row client-cperson-container" style="position: relative;">
                                <span class="form-text text-muted form-label fw-bolder countContact"
                                    style="position: absolute; top: 0;"></span>
                                <div class="col-xl-12">
                                    <div class="form-group mb-0 mt-7 client-cperson-container-child fv-plugins-icon-container"
                                        id="remove">
                                        <div class="col-lg-12">
                                            <textarea id="kt_docs_tinymce_hidden_{{@$job->id}}" name="kt_docs_tinymce_hidden" class="job-editor tox-target">{!! @$interview_questions->questions !!}</textarea>
                                        </div>

                                        <input type="hidden" name="question-value" id="question-value_{{@$job->id}}"
                                            class="form-control question-value form-control-lg form-control-solid rounded me-5"
                                            value="{{ @$interview_questions->id }}" placeholder="Enter interview question">
                                        <span class="form-text text-muted">&nbsp;</span>
                                        <div class="fv-plugins-message-container"></div>
                                    </div>
                                </div>
                               
                                 <div class="text-center col-xl-12">
                                    <button class="btn btn-primary "
                                        id="saveQuestions_{{ $job->id }}"> <span class="indicator-label">Submit</span> <span
                                            class="indicator-progress">Please wait... <span
                                                class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!--end::Add Interview Question popup-->




<script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>


<script>
    
var jobIdQuestion = "<?php echo @$job->id; ?>";
var questionList = document.getElementById(`questionList_${jobIdQuestion}`);
var addBtn = document.getElementById(`addQuestionBtn_${jobIdQuestion}`);
var saveBtn = document.getElementById(`saveQuestions_${jobIdQuestion}`);


if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

// CKEDITOR.config.height = 'auto';
CKEDITOR.config.width = 'auto';

document.querySelectorAll('.job-editor').forEach(function (editorBox) {

    CKEDITOR.replace(editorBox.id, {
        height: 400,
        toolbarGroups: [
            { name: 'clipboard', groups: ['clipboard', 'undo'] },
            { name: 'editing', groups: ['find', 'selection'] },
            { name: 'links' },
            { name: 'insert' },
            { name: 'tools' },
            { name: 'basicstyles', groups: ['basicstyles'] },
            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'] },
            { name: 'styles' },
            { name: 'colors' },
            { name: 'customFields', groups: ['customFields'] }
        ]
    });

});



document.getElementById(`saveQuestions_${jobIdQuestion}`).addEventListener('click', function () {

    let questions = [];
  
    
    document.querySelectorAll('.question-value').forEach(input => {
        if (input.value.trim() !== '') {
            questions.push(input.value.trim());
        }
    });
    var data = CKEDITOR.instances['kt_docs_tinymce_hidden_' + jobIdQuestion].getData();
    var questionsId = $('#question-value_'+jobIdQuestion).val();
    // if (questions.length === 0) {
    //     alert('Please add at least one question');
    //     return;
    // }

    showButtonLoader(saveBtn.id);

    fetch("{{ route('questions.questionsStore') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            job_id: jobIdQuestion,
            questions: data,            
            questionsId: questionsId,
            deleteChk: 'No'
        })
    })
    .then(res => res.json())
    .then(response => {
        $(`#add_interview_question_modal_${jobIdQuestion}`).modal('hide');
        $(`#questionList_${jobIdQuestion}`).html(response.html);
        //renumberQuestions(questionList);
         hideButtonLoader(saveBtn.id);
         Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
        });
    })
    .catch(error => {
        console.error(error);
        hideButtonLoader(saveBtn.id);
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Something went wrong. Please try again.',
        });
    });

});

setTimeout(() => {
        // alert("k");
        $('.iti__flag-container').each(function(index) {
            var cnt = $(this).attr('data-row');
            const flagContainer = document.getElementById(`phoneDisplayReal_${cnt}`);
            var calling_code = $(this).attr('data-code');
            
            const flagDiv = document.createElement("div");
            flagDiv.className = `iti__flag iti__${calling_code}`; // Ensure calling_code is in lowercase

            const dialCode = countryDialCodes[calling_code] || ""; // Fetch the dial code dynamically
            const dialCodeSpan = document.createElement("span");
            dialCodeSpan.textContent = dialCode;

            // Append the flag and dial code
            flagContainer.appendChild(flagDiv);
            flagContainer.appendChild(dialCodeSpan);


        });
}, 2000);

// Add new question
addBtn.addEventListener('click', () => {
    var captainAccessCheck = Number('{{@$captainAccessCheck}}');
    if (captainAccessCheck !== 1) {
       Swal.fire({
            icon: 'info',
            title: 'Access Restricted',
            text: '{{@$alertmessage}}',
        });
        return;
    }
   $(`#add_interview_question_modal_${jobIdQuestion}`).modal('show');
});

function showButtonLoader(btnId) {
    var btn = document.getElementById(btnId);
    btn.setAttribute('data-kt-indicator', 'on');
    btn.disabled = true;
}

function hideButtonLoader(btnId) {
    var btn = document.getElementById(btnId);
    btn.removeAttribute('data-kt-indicator');
    btn.disabled = false;
}


// addBtn.addEventListener('click', () => {
//     var li = document.createElement('li');
//     li.className = 'list-group-item question-item';

//     li.innerHTML = `<div class="d-flex align-items-start justify-content-between gap-3"> <!-- Question Text --> <div class="flex-grow-1"> <div class="fw-semibold text-dark question-text"><span class="question-number" style="font-weight: 900;"></span> New Question </div> </div> <!-- Actions --> <div class="d-flex gap-2"> <button class="btn btn-sm btn-light border toggle-question" title="Edit Question"> ✏️ </button> <button class="btn btn-sm btn-light border text-danger remove-question" title="Delete Question"> 🗑️ </button> </div> </div> <!-- Edit Input --> <div class="question-input mt-3"> <input type="text" class="form-control form-control-sm question-value" value="" placeholder="Enter interview question"> </div>`;

//     questionList.appendChild(li);
//     renumberQuestions(questionList);
// });



// Event delegation
questionList.addEventListener('click', function (e) {

    // Edit toggle
    if (e.target.classList.contains('toggle-question')) {
        var item = e.target.closest('.question-item');
        var inputBox = item.querySelector('.question-input');

        inputBox.classList.toggle('d-none');
        e.target.textContent = inputBox.classList.contains('d-none') ? 'Edit' : 'Close';
    }

    // Delete question
    if (e.target.classList.contains('remove-question')) {

        var captainAccessCheck = Number('{{@$captainAccessCheck}}');
        if(captainAccessCheck !== 1) {
            Swal.fire({
                    icon: 'info',
                    title: 'Access Restricted',
                    text: '{{@$alertmessage}}',
                });
            return;
        }


        Swal.fire({
            title: 'Are you sure?',
            text: 'This question will be permanently deleted',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                
                e.target.closest('.question-item').remove();
                var btn = e.target;
                var id = btn.dataset.id; // ✅ correct way

                // Remove from UI
              fetch("{{ route('questions.questionsStore') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            job_id: jobIdQuestion,          
                            questionsId: id,
                            questions: id,
                            deleteChk: 'Yes'
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        $(`#add_interview_question_modal_${jobIdQuestion}`).modal('hide');
                        renumberQuestions(questionList);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                        });
                    });
            }
        });

        renumberQuestions(questionList);
    }
});

// Sync input value to text
questionList.addEventListener('input', function (e) {
    if (e.target.classList.contains('question-value')) {
        var item = e.target.closest('.question-item');
        item.querySelector('.question-text').textContent = e.target.value || 'New Question';
        renumberQuestions(questionList);
    }
});

function renumberQuestions(questionList) {
    var items = questionList.querySelectorAll('.question-item');

    items.forEach((item, index) => {
        var textDiv = item.querySelector('.question-text');
        let numberSpan = textDiv.querySelector('.question-number');

        if (!numberSpan) {
            numberSpan = document.createElement('span');
            numberSpan.className = 'question-number';
            textDiv.prepend(numberSpan);
        }

        numberSpan.textContent = `Q.${index + 1} `;
    });
}

setTimeout(() => {
    renumberQuestions(questionList);
}, 500);

</script>



        <script>

             var myChartId = "<?php echo @$job->id; ?>";

             var showDataLabels = {
                    id: 'showDataLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.font = 'bold 12px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.fillText(value, bar.x, bar.y - 1.5);
                                ctx.restore();
                            });
                        });
                    }
            };


            var showDataLabelsvertical = {
                id: 'showDataLabelsvertical',
                afterDatasetsDraw(chart) {
                    var { ctx } = chart;
                    chart.data.datasets.forEach((dataset, i) => {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach((bar, index) => {
                            var value = dataset.data[index];
                            ctx.save();
                            ctx.fillStyle = 'black';
                            ctx.font = 'bold 12px sans-serif';
                            ctx.textAlign = 'left';  // align left for horizontal bars
                            ctx.textBaseline = 'middle';

                            // place text at the end of bar (right side)
                            ctx.fillText(value, bar.x + 1, bar.y);
                            ctx.restore();
                        });
                    });
                }
            };

            function loadCountryChart() {
                var ctx = document.getElementById(`myChart_${myChartId}`).getContext('2d');
                var countryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['In Review','Sourcing', 'Screening', 'Evaluation', 'Negotiation', 'Offer','Joined','Rejected'],
                    datasets: [{
                        label: 'Users',
                        data: ["{{$inreview_count}}", "{{$sourcing_count}}", "{{$screening_count}}", "{{$evaluation_count}}", "{{$negotiation_count}}", "{{$offer_count}}", "{{$joined_count}}","{{$Rejected_count}}"],
                        backgroundColor: [
                            '#bdbd26', // inreview_count
                            '#000000', // sourcing_count
                            '#c000ff', // screening_count
                            '#2896ff', // evaluation_count
                            '#ff4098', // negotiation_count
                            '#ffab00', // offer_count
                            '#06c732',
                            '#ff0202' // joined_count // joined_count
                        ],
                        borderRadius: 10,
                        barThickness: 30
                    }]
                },
                options: {
                    indexAxis: 'y', // makes it horizontal
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.x.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value <= 10) return value;
                                    else if (value % 100 === 0) return value.toLocaleString();
                                    else return '';
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                },
                plugins: [showDataLabelsvertical] // ✅ custom labels plugin
               });
            } 

            setTimeout(() => {
                  loadCountryChart();
            }, 300); 


        var candidates_left_chart = document.getElementById(`candidates_left_chart_${myChartId}`).getContext('2d');

        var allJobGraphBar = new Chart(candidates_left_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Total Candidates',
                    data: {!! json_encode($candidateValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3', // Blue 
                        '#2196f3'  // Blue 
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // show integer values only
                        }
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });


            
            var candidates_offer_chart = document.getElementById(`candidates_offer_chart_${myChartId}`).getContext('2d');

            var allofferGraphBar = new Chart(candidates_offer_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommoffer) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommoffer) !!},
                        backgroundColor: [
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00', 
                            '#ffab00' 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            
            var candidates_join_chart = document.getElementById(`candidates_join_chart_${myChartId}`).getContext('2d');

            var alljoinGraphBar = new Chart(candidates_join_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsCommjoin) !!},
                    datasets: [{
                        label: 'Total Candidates',
                        data: {!! json_encode($candidateValuesCommjoin) !!},
                        backgroundColor: [
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732',  
                            '#06c732'  
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart_month = document.getElementById(`inetview_recent_chart_month_${myChartId}`).getContext('2d');

            var netview_recentGraphBarMonth = new Chart(inetview_recent_chart_month, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabelsMonth) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValuesMonth) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });

            var inetview_recent_chart = document.getElementById(`inetview_recent_chart_${myChartId}`).getContext('2d');

            var netview_recentGraphBar = new Chart(inetview_recent_chart, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($interviewlabels) !!},
                    datasets: [{
                        label: 'Total Interviews',
                        data: {!! json_encode($interviewValues) !!},
                        backgroundColor: [
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3', // Blue 
                            '#2196f3'  // Blue 
                        ],
                        borderRadius: 5,
                        borderSkipped: false,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // show integer values only
                            }
                        }
                    }
                },
                plugins: [showDataLabels] // ✅ custom labels plugin
            });


            function filtersdatagetmonth(valSelected){
                if(valSelected==1){
                      $(`.inetview_recent_chart_month_${myChartId}`).removeClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).addClass('d-none');
                }else{
                      $(`.inetview_recent_chart_month_${myChartId}`).addClass('d-none');
                      $(`.inetview_recent_chart_${myChartId}`).removeClass('d-none');
                }

            }






            
    </script>
    

@push('footer-script')
    <script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script>
        loadTable();
        var table;

        function loadTable() {

            var hid_job_id = $('#hid_job_id').val();
            table = $('#interviewData').dataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                destroy: true,
                ordering: false, //disable ordering														
                "bLengthChange": false, //thought this line could hide the LengthMenu														
                sDom: 'lrtip',
                oLanguage: {
                    sLengthMenu: "_MENU_",
                },
                ajax: '{!! route('service_company.interview-schedule.interviewData') !!}?hid_job_id=' + hid_job_id,

                "fnDrawCallback": function(settings, start, end, max, total, pre) {

                    var total_record = this.fnSettings().fnRecordsTotal();
                    $('#total_record').html(total_record);
                    $("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },

                "columnDefs": [{
                        "targets": 0
                    },
                    {
                        "targets": 1,
                        "class": "align-items-center"
                    },
                    {
                        "targets": 2
                    },
                    {
                        "targets": 3
                    },
                    {
                        "targets": 4
                    },
                    {
                        "targets": 5
                    },
                    {
                        "targets": 6
                    },
                    {
                        "targets": 7
                    },
                    {
                        "targets": 8
                    },
                    {
                        "targets": 9
                    }
                ],
                columns: [{
                        data: 'chkbox',
                        name: 'chkbox',
                        width: '1%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Name',
                        name: 'Name',
                        width: '15%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Requisitions',
                        name: 'Requisitions',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'InterviewMode',
                        name: 'InterviewMode',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Interviewers',
                        name: 'Interviewers',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Source',
                        name: 'Source',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'InterviewDate',
                        name: 'InterviewDate',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Feedback',
                        name: 'Feedback',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Rating',
                        name: 'Rating',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'Stage',
                        name: 'Stage',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    }
                ],

            });
            new $.fn.dataTable.FixedHeader(table);

        }
    </script>
@endpush
