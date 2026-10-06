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
<script>
   $( function(){
     $( "#calender" ).datepicker
     	({
			showOn				: "button",
			buttonImage			: "images/calendar.gif",
			buttonImageOnly		: true,
			buttonText			: "Select date"
       });
   });	
</script>
<style>
body{overflow-x:hidden;}
.dropzone{padding: 1.8rem 1.75rem!important; border: 2px dashed #e6e7e8!important; background:transparent!important; text-align: inherit!important;}
.border-radius-rb {border-top-right-radius: 0em!important;}
.border-radius-rt {border-bottom-right-radius: 0em!important;}
.border-radius-lt {border-bottom-left-radius: .4em!important;}
.border-radius-lb {border-top-left-radius: .4em!important;}
.font-size-icon {font-size: 1.3em;}
.dashed-border-top {border-top:1px dashed gray;}
#dob::-webkit-inner-spin-button,
#dob::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none;}
#np_id::-webkit-inner-spin-button,
#np_id::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none;}

.tagify .tagify__tag div .tagify__tag-text {
    color: #0c0c0c !important;
    font-weight: 500 !important;
    font-size: 14px !important;
}
@media only screen and (min-width: 1024px) and (max-width: 1440px){
    input::placeholder {
      font-size: 11px; /* Placeholder text size */
      color: gray; /* Optional: Change placeholder color */
  }
  input {
    font-size: 11px; /* Input text size */
  }
}

.iti {
    position: relative;
    display: block;
}
.disablesectionOn{
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            z-index: 99;
        }
        .disablesectionOnRow{
          position: relative;
        }

        /* Chrome, Safari, Edge, Opera */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .disablesectionOn.setOpacity{
            cursor: not-allowed;
            background: #8a878d;
            opacity: 0.5;
        }

/* ==========================================================================
   HIGHEST QUALIFICATION: 100% BROWSER RAM COMBOBOX & CARD OVERFLOW PROTECTION
   ========================================================================== */
.fs {
    position: relative;
}

.fs.has-active-combobox {
    z-index: 1050 !important;
}

.fs.has-active-combobox .fsc,
.fs.has-active-combobox .g4 {
    overflow: visible !important;
}

/* Bulletproof native select hiding scoped strictly to combobox container */
.fg.has-combobox > select,
.fg--has-combobox > select,
.has-combobox > select,
.candidate-field-control--has-combobox > select,
.bd-control--has-combobox > select {
    position: absolute !important;
    opacity: 0 !important;
    width: 1px !important;
    height: 1px !important;
    margin: -1px !important;
    clip: rect(0, 0, 0, 0) !important;
    overflow: hidden !important;
    z-index: -1 !important;
    pointer-events: none !important;
}

/* Hide static icons outside combobox when trigger has its own icon */
.fg.has-combobox > i.fa-graduation-cap,
.candidate-field-control--has-combobox > i.fa-graduation-cap,
.bd-control--has-combobox > i.fa-graduation-cap,
.fg.has-combobox > i.fa-flag,
.candidate-field-control--has-combobox > i.fa-flag,
.bd-control--has-combobox > i.fa-flag {
    display: none !important;
}

/* Custom Combobox Trigger Container */
.jero-custom-select {
    position: relative;
    width: 100%;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    user-select: none;
}

.jero-cs-trigger {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    min-height: 42px !important;
    height: 42px !important;
    max-height: 42px !important;
    padding: 0 14px !important;
    background: #ffffff !important;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    outline: none !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    text-align: left !important;
    box-sizing: border-box !important;
    font-family: 'Poppins', sans-serif !important;
}

.jero-cs-trigger:hover {
    border-color: #94a3b8 !important;
}

.jero-cs-trigger:focus,
.jero-custom-select.is-open .jero-cs-trigger {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.14) !important;
}

/* Validation error state */
.jero-custom-select.has-validation-error .jero-cs-trigger,
.fg.has-validation-error .jero-cs-trigger,
.candidate-field-control.has-validation-error .jero-cs-trigger,
.bd-control.has-validation-error .jero-cs-trigger {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.14) !important;
}

.jero-cs-left {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    min-width: 0 !important;
    flex: 1 1 auto !important;
}

.jero-cs-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 18px !important;
    color: #5d6681 !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}

.jero-cs-value {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #0b1545 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    line-height: 1.2 !important;
}

.jero-cs-placeholder {
    color: #8991a8 !important;
    font-weight: 400 !important;
}

.jero-cs-right {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    margin-left: 8px !important;
}

.jero-cs-badge {
    display: inline-block !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    background: #e0e7ff !important;
    color: #3730a3 !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
}

.jero-cs-arrow {
    font-size: 11px !important;
    color: #94a3b8 !important;
    transition: transform 0.2s ease !important;
}

.jero-custom-select.is-open .jero-cs-arrow {
    transform: rotate(180deg) !important;
    color: #3b82f6 !important;
}

/* Floating Dropdown Menu Panel */
.jero-cs-dropdown {
    position: absolute !important;
    left: 0 !important;
    right: 0 !important;
    top: calc(100% + 4px) !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.22), 0 10px 18px -6px rgba(15, 23, 42, 0.14) !important;
    z-index: 999999 !important;
    overflow: hidden !important;
    display: none;
    flex-direction: column !important;
    max-height: 340px !important;
    font-family: 'Poppins', sans-serif !important;
    animation: jeroCsFadeIn 0.15s ease-out;
}

.jero-custom-select.is-open .jero-cs-dropdown {
    display: flex !important;
}

.jero-custom-select.is-open.open-up .jero-cs-dropdown {
    top: auto !important;
    bottom: calc(100% + 4px) !important;
}

/* Sticky Search Box at the top */
.jero-cs-search-box {
    display: flex !important;
    align-items: center !important;
    position: sticky !important;
    top: 0 !important;
    padding: 8px 10px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    z-index: 10 !important;
    flex-shrink: 0 !important;
    gap: 8px !important;
}

.jero-cs-search-box i.jero-cs-search-icon {
    color: #64748b !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}

.jero-cs-search-input {
    width: 100% !important;
    height: 36px !important;
    min-height: 36px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    font-family: 'Poppins', sans-serif !important;
    color: #0f172a !important;
    background: #ffffff !important;
    outline: none !important;
    box-sizing: border-box !important;
}

.jero-cs-search-input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
}

.jero-cs-search-clear {
    border: none !important;
    background: transparent !important;
    color: #94a3b8 !important;
    font-size: 16px !important;
    cursor: pointer !important;
    padding: 0 4px !important;
    line-height: 1 !important;
}

.jero-cs-search-clear:hover {
    color: #475569 !important;
}

/* Scrollable Options Container */
.jero-cs-list {
    flex: 1 1 auto !important;
    overflow-y: auto !important;
    max-height: 280px !important;
    padding: 4px 0 !important;
}

/* Group Title */
.jero-cs-group-title {
    padding: 7px 12px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px !important;
    color: #1e40af !important;
    background: #f1f5f9 !important;
    border-top: 1px solid #e2e8f0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    text-transform: uppercase !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 5 !important;
}

.jero-cs-group:first-child .jero-cs-group-title {
    border-top: none !important;
}

/* Option Item */
.jero-cs-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 8px 14px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    cursor: pointer !important;
    transition: background 0.1s ease !important;
}

.jero-cs-item:hover,
.jero-cs-item.is-highlighted {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
}

.jero-cs-item.is-selected {
    background-color: #e0e7ff !important;
    color: #312e81 !important;
    font-weight: 600 !important;
}

.jero-cs-item.is-hidden {
    display: none !important;
}

.jero-cs-group.is-hidden {
    display: none !important;
}

.jero-cs-empty.is-hidden {
    display: none !important;
}

.jero-cs-item-text {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    line-height: 1.35 !important;
    word-break: break-word !important;
}

.jero-cs-item-badge {
    font-size: 10px !important;
    font-weight: 600 !important;
    padding: 2px 7px !important;
    background: #e0e7ff !important;
    color: #3730a3 !important;
    border-radius: 4px !important;
    white-space: nowrap !important;
    margin-left: 8px !important;
    flex-shrink: 0 !important;
}

.jero-cs-item.is-selected .jero-cs-item-badge {
    background: #c7d2fe !important;
    color: #1e1b4b !important;
}

.jero-cs-empty {
    padding: 20px 14px !important;
    text-align: center !important;
    color: #64748b !important;
    font-size: 13px !important;
    font-weight: 500 !important;
}

@keyframes jeroCsFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Explicit styling protection for native Gender select (42px height & 6px border-radius) */
.fg select#gender,
select#gender {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    width: 100% !important;
    min-height: 42px !important;
    height: 42px !important;
    line-height: 40px !important;
    padding: 0 34px 0 14px !important;
    background-color: #ffffff !important;
    border: 1px solid #d8deec !important;
    border-radius: 6px !important;
    color: #0b1545 !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    font-family: 'Poppins', sans-serif !important;
    outline: none !important;
    cursor: pointer !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 14px center !important;
    background-size: 11px !important;
    box-sizing: border-box !important;
}
</style>

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

<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/candidate-form.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<style>
  .toolbar {
    display: flex;
    align-items: center;
    background-color: #fff;
    padding: 0.5rem;
    position: fixed !important;
    width: 100%;
    z-index: 999 !important;
    box-shadow: 4px 3px 5px 0px #d5cece;
}
  #kt_content{
    margin-top: 89px;
  }
</style>

</head>
<body>
@php 

		  use App\Helper\GeneralMethod;
       $defaultTimeFormat = GeneralMethod::openDefaultTimeFormat($company_id);
       $country_code =  GeneralMethod::openCountryCodeFun($company_id);
       $currency_code =  GeneralMethod::openDefaultCurrency($company_id);
      // dd($currency_code);

      $phone_code = $country_code;
      $second_phone_code = $country_code;
      $url= '';
	
		  $resume_url				=	'';
		  $photo  					=	url('public/avatar.png');	
      $resumeCheck = '';
		  if($page_event == 'ADD'){
			 $pagetitle			=	'Upload Resume';
			 $resume_url			=	'';	
		  }else if($page_event == 'EDIT'){ 

        $phone_code = ($application->phone_code) ? $application->phone_code : $phone_code;
        $second_phone_code = ($application->second_phone_code) ? $application->second_phone_code : $second_phone_code;

			 $pagetitle	=	'Edit Candidate';      
			 if(!empty($application->resume)){
				$url				=	 $application->resume;
				$resume_url			=	route('download.resume', ['id' => $application->id, 'path' => rtrim(strtr(base64_encode($url), '+/', '-_'), '=')]);
        $resumeCheck = 1;
			 }else{
				$resume_url			=	'';	
			 }
			 if(!empty($application->photo)){
				//$photo			=	 url('/public/user-uploads/candidate-photos/'.$application->photo);	
				$photo			=	 $application->photo;
			 }   
		  }	
					
     

		$bgTextDecore				=	$strVal	=	'';
		$str						=	@$jobDetail->title;
    // dd($str);
		$str	= $strVal			=	strtolower($str[0]);
		$str					    =	ord($str);
		if($str >=97 && $str <=101){
			$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
		}else if($str >=102 && $str <=106){
			$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
		}else if($str >=107 && $str <=111){
			$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
		}else if($str >=112 && $str <=116){
			$bgTextDecore		=	'fs-3 bg-light-success text-success';	
		}else if($str >=117 && $str <=121){
			$bgTextDecore		=	'fs-3 bg-dark text-light';	
		}else{
			$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
		}	

		$city			=	$country_id		=	 $country_name	=	$state_name		=	$address		=	'';
		$address		=	@$jobLocation->address;
		if(@$jobLocation->country_id == 101){
		   $city			=	@$jobLocation->city_name;
		   $state_name		=	@$jobLocation->state_name;	
		}else{
		   $city		=	@$jobLocation->city;		
		}
		if(@$jobLocation->country_id > 0){
			  $country_name	=	GeneralMethod::getCountryName(@$jobLocation->country_id);	
			  foreach($country_name as $cval){
				 $country_name	=	ucwords($cval['name']);	
			  }				 
		}
		
	   $status_class		=	'';
	   if(@$jobDetail->status == 'active'){
		  $status_class	=	'bg-light-success text-success';
	   }else if(@$jobDetail->status == 'hold'){
		  $status_class	=	'bg-light-warning text-warning';
	   }else if(@$jobDetail->status == 'inactive'){
		  $status_class	=	'bg-light-danger text-danger';
	   }
		
		$created_at 		=	"";
		$created_at 		=	@$jobDetail->created_at;
		$created_at 		=	date("d M, Y", strtotime($created_at));
		
		$snp_one	=	$snp_two	= $snp_zero	= $snp_text_dummy = $snp_last_working = "";
		$snp_text_one	=	$snp_text_two	= $snp_text_zero	=	'display:none;';		
		$snp_mode = @$application->serving_notice_period_mode;
		if($snp_mode== 1){
			$snp_one		=	'checked="checked"';
			$snp_text_one	=	'display: flex;';
      $snp_text_dummy = 'Serving Notice Period';   
      $snp_last_working = $application->np_id;                                 	
		}else if($snp_mode== 2){
			$snp_two	=	'checked="checked"';
			$snp_text_one	=	'display: flex;';
      $snp_text_dummy = 'Immediate';  
      $snp_last_working = $application->last_working_date;   
		}else{
			$snp_zero		=	'checked="checked"';	
			$snp_text_zero	=	'display:block';
		}  
    $logos = GeneralMethod::oepnCompanyDetails($jobDetail->id,$company_id);

    $logo_path = '';
    
    $logo_path = url('public/user-uploads/company-logo/' . @$logos->logo);
       if (!empty($logos->logo_logical_path)) {
					$logo_path = url('public/' . @$logos->logo_logical_path);
				}
        
$logo_path = GeneralMethod::oepnCompanyDetails($jobDetail->id,$company_id);
$company_or_client = GeneralMethod::openCompanyDetailsByClient($jobDetail->id,$company_id);	
$Jobcities = GeneralMethod::openJobcities($jobDetail->id,$company_id);	
$locations  = explode('`,`',$Jobcities);
$salaryModeSelect = GeneralMethod::salaryMode();
$currencySymbole = GeneralMethod::currencySymbole();
        $min_sal_mode = strtoupper(substr($salaryModeSelect[$jobDetail->min_sal_mode], 0, 1));
				$max_sal_mode = strtoupper(substr($salaryModeSelect[$jobDetail->max_sal_mode], 0, 1));
				$min_salary_currency = $currencySymbole[@$jobDetail->min_salary_currency];
				$max_salary_currency = $currencySymbole[@$jobDetail->max_salary_currency];
        $max_salary = GeneralMethod::formatAmount(GeneralMethod::thousandsCurrencyFormat($jobDetail->max_salary));
        $salary_currency = @$jobDetail->max_salary_currency ?? 0;
        // dd($min_sal_mode,$max_sal_mode,$min_salary_currency,$max_salary_currency);

       	

@endphp 

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">


  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    {{-- @dd(@$account); --}}
      @include('sections.service_company.open-toolbar',['id' => $jobDetail->id,'company_id'=>@$company_id,'account'=>@$account])
      <div class="d-flex align-items-center py-1">
          <div class="before-event d-flex align-items-center position-relative my-1 me-3">
            <!--begin::Buttons-->
                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                  @php 
                      if($page_event == 'EDIT'){ 
                        echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary">Update Candidate</button>';
                      }else{
                        echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit Candidate</button>';	
                      }
                  @endphp
                    {{-- <a href="javascript:void(0);" class="btn btn-secondary">Cancel</a>  --}}
                  
                  </div>
                </div>
          </div>
      </div>
    <!--end::Page title-->
  </div>
  <!--end::Container-->
</div>
<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

      <div class="app">
       
        <!-- MAIN -->
        <main class="mc">

          
          <!-- Form -->
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="job_id" value="{{ @$jobId }}" id="job_id" />
            <input type="hidden" name="appId" value="{{ @$appId }}" id="appId" />
            <input type="hidden" name="currentPage" value="{{ @$currentPage }}" id="currentPage" />
            <input type="hidden" name="currentSlug" value="{{ @$currentSlug }}" id="currentSlug" />
            <input type="hidden" name="company_id" value="{{ @$company_id }}" id="company_id" />
            <input type="hidden" name="resumeCheck" value="1" id="resumeCheck" />
            <input type="hidden" name="resume_session_uuid" id="resume_session_uuid" value="">
            <input type="hidden" name="resume_original_name" id="resume_original_name" value="">
            <input type="hidden" name="resume_upload_method" id="resume_upload_method" value="">
            <input type="hidden" name="AddJobWiseCondidateRankRow" id="AddJobWiseCondidateRankRow" value="{{ (@$AddJobWiseCondidateRankRow) ? $AddJobWiseCondidateRankRow : '' }}">
          <div class="fc">
            <!-- Upload + Career Summary -->
            <div class="g2" style="margin-bottom:16px">
                <!-- Resume Upload -->
                <div class="fs fv-row" style="margin-bottom:0">
                    <div class="fsh">
                        <h3>Upload Resume <span style="color:red">*</span></h3>
                    </div>

                    <div class="fsc">
                        <div class="ua" id="uploadBox">

                            <div class="ui">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </div>

                            <div class="ut">Drag & Drop Resume Here</div>

                            <div class="us">or</div>

                            <button class="ub" type="button" id="chooseFileBtn">
                                Choose File
                            </button>

                            <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" style="display:none;">

                            <div class="uf">
                                Supports PDF, DOC, DOCX (Max 6 MB)
                            </div>

                            <!-- Progress bar / pill / success banner -->
                            <div class="up-wrap" id="upWrap" style="display:none">
                                <div class="up-row">
                                    <div class="up-track">
                                        <div class="up-bar" id="upBar" style="width:0%"></div>
                                    </div>
                                    <span class="up-pill" id="upPill" style="display:none">
                                        <svg class="up-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                        Uploaded
                                    </span>
                                </div>
                                <div class="up-stats" id="upStats">0% &middot; 0 KB / 0 KB</div>
                                <div class="up-banner" id="upBanner" style="display:none">
                                    <svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>
                                    <span id="upBannerText">Upload complete! Your resume has been uploaded successfully.</span>
                                </div>
                            </div>

                            <!-- Selected File -->
                            <div class="file-info" id="fileInfo" style="{{ !empty($resume_url) ? 'display:flex' : 'display:none' }}">
                                <span class="file-name" id="fileName"> </span>
                                @if(!empty($resume_url))
                                <a href="{{ $resume_url }}"
                                   target="_blank"
                                   class="download-file"
                                   style="color:#fff;margin-left:250px;display:inline-flex;align-items:center;justify-content:center;">
                                    <i class="fa fa-download" aria-hidden="true" style="color:#fff"></i>
                                </a>
                                @endif

                                <button type="button"
                                        class="remove-file"
                                        id="removeFile">
                                    Remove
                                </button>
                            </div>

                        </div>

                        <!-- After-upload Resume Information card (outside #uploadBox) -->
                        <div class="up-info" id="upInfo">
                            <div class="up-info-banner">
                                <svg class="up-info-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/>
                                </svg>
                                <span class="up-file-icon">PDF</span>
                                <div class="up-file-meta">
                                    <div class="up-file-name" id="upFileName">&nbsp;</div>
                                    <div class="up-file-sub" id="upFileSub">&nbsp;</div>
                                </div>
                            </div>
                            <div class="up-actions">
                                <button type="button" id="upReplaceBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/>
                                    </svg>
                                    Replace
                                </button>
                                <button type="button" id="upRemoveBtn" class="up-remove" title="Delete Resume">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                            <div class="up-details">
                                <div class="up-detail">
                                    <span class="up-detail-icon ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    <div><div class="up-detail-lbl">Upload Status</div><div class="up-detail-val" id="upDetailParserStatus">&nbsp;</div></div>
                                </div>
                                <div class="up-detail">
                                    <span class="up-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                                    <div><div class="up-detail-lbl">Uploaded On</div><div class="up-detail-val" id="upDetailUploadedOn">&nbsp;</div></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="fs fv-row" style="margin-bottom:0">
                  <div class="fsh" style="justify-content:space-between">
                    <h3>Career
                      Summary <span style="color:var(--r)">*</span></h3>
                  </div>
                  <div class="fsc">
                    <textarea placeholder="Enter career summary..." id="careersummary" name="careersummary" rows="10">{{ @$application->careersummary }}</textarea>
                    <div class="cc">0 / 100</div>
                    <div class="tip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="16" x2="12" y2="12" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                      </svg><span>Tip: A good summary helps the client
                        understand the candidate quickly.</span></div>
                  </div>
                </div>
            </div>
            
            <!--begin::Upload Photo-->
            <div class="form-group row fv-row mb-10 d-none">
              <div class="col-lg-4 col-xl-4 text-center">
                <!--begin::Image input-->
                <div class="image-input image-input-outline" data-kt-image-input="true">
                  <!--begin::Image preview wrapper-->
                  <div class="image-input-wrapper w-90px h-90px" style="background-image: url({!! @$photo !!})"></div>
                  <!--end::Image preview wrapper-->
                  <!--begin::Edit button-->
                  <label class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Change avatar"> <i class="bi bi-pencil-fill fs-9"></i>
                  <!--begin::Inputs-->
                  <input type="file" name="photo" id="avatar" accept=".png, .jpg, .jpeg" />
                  <input type="hidden" name="avatar_remove" />
                  <!--end::Inputs-->
                  </label>
                  <!--end::Edit button-->
                  <!--begin::Cancel button-->
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Cancel avatar"> <i class="bi bi-x fs-4"></i> </span>
                  <!--end::Cancel button-->
                  <!--begin::Remove button-->
                  <span class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-white shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Remove avatar"> <i class="bi bi-x fs-4"></i> </span>
                  <!--end::Remove button-->
                </div>
                <span class="form-text text-muted d-block">Allowed file types: png,jpg, jpeg.</span>
                <!--end::Image input-->
              </div>
              <div class="col-lg-8 col-xl-8">
                {{-- <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" > --}}
                <input type="hidden" name="uploaded_file_path" id="uploaded_file_path">
                <div class="fv-row"> <span class="form-text text-muted d-block">*Please upload the resume (Max file size 2MB). Allowed file types: PDF or Word.</span> </div>
                <!--end::Input group-->
              </div>
            </div>
            <!--end::Upload Photo & Resume/CV-->

            <!-- Basic Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Basic Details</h3>
              </div>
              <div class="fsc">
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Full Name <span class="rq">*</span></label>
                    <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" value="{{ @$application->full_name }}"
                      placeholder="Enter full name" />
                  </div>
                  <div class="fg fv-row">
                    <label>Gender</label>
                    <select name="gender" id="gender">
                      <option value="Rather Not Say" {{ (@$application->gender==='Rather Not Say') ? 'selected' : '' }}>Rather
                        Not Say</option>
                      <option value="Male" {{ (@$application->gender==='Male') ? 'selected' : '' }}>Male</option>
                      <option value="Female" {{ (@$application->gender==='Female') ? 'selected' : '' }}>Female</option>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Nationality</label>
                    @php
                      // Tenant HQ Country Mapping to Demonym (Sensible Defaulting)
                      $countryToDemonym = [
                          'in' => 'Indian',
                          'us' => 'American',
                          'gb' => 'British',
                          'uk' => 'British',
                          'ca' => 'Canadian',
                          'au' => 'Australian',
                          'ae' => 'Emirati',
                          'sg' => 'Singaporean',
                          'de' => 'German',
                          'fr' => 'French',
                          'nl' => 'Dutch',
                          'ie' => 'Irish',
                          'sa' => 'Saudi',
                      ];
                      $tenantDefaultNat = $countryToDemonym[strtolower($country_code ?? 'in')] ?? 'Indian';
                      $selectedNationality = @$application->nationality ?: ((@$page_event == 'EDIT' && !empty(@$application->id)) ? '' : $tenantDefaultNat);

                      // ⭐ Top 10 Popular Nationalities / Talent Hubs with Country Synonyms for Instant Search
                      $topPopularNationalities = [
                          'Indian'      => ['label' => '🇮🇳 Indian (India)', 'synonyms' => 'india, bharat, hindustan, in, ind'],
                          'American'    => ['label' => '🇺🇸 American (United States)', 'synonyms' => 'united states, usa, america, us, u.s., u.s.a.'],
                          'British'     => ['label' => '🇬🇧 British (United Kingdom)', 'synonyms' => 'united kingdom, uk, u.k., britain, england, scotland, wales, gb'],
                          'Canadian'    => ['label' => '🇨🇦 Canadian (Canada)', 'synonyms' => 'canada, ca, can'],
                          'Australian'  => ['label' => '🇦🇺 Australian (Australia)', 'synonyms' => 'australia, au, aus, oz'],
                          'Emirati'     => ['label' => '🇦🇪 Emirati (United Arab Emirates)', 'synonyms' => 'united arab emirates, uae, u.a.e., dubai, abu dhabi, sharjah, ae'],
                          'Singaporean' => ['label' => '🇸🇬 Singaporean (Singapore)', 'synonyms' => 'singapore, sg, sgp'],
                          'German'      => ['label' => '🇩🇪 German (Germany)', 'synonyms' => 'germany, deutschland, de, deu'],
                          'French'      => ['label' => '🇫🇷 French (France)', 'synonyms' => 'france, fr, fra'],
                          'Dutch'       => ['label' => '🇳🇱 Dutch (Netherlands)', 'synonyms' => 'netherlands, holland, nl, nld, amsterdam'],
                          'Irish'       => ['label' => '🇮🇪 Irish (Ireland)', 'synonyms' => 'ireland, eire, ie, irl, dublin'],
                          'Saudi'       => ['label' => '🇸🇦 Saudi (Saudi Arabia)', 'synonyms' => 'saudi arabia, ksa, sa, sau, riyadh'],
                          'Other'       => ['label' => '🌐 Other / Non-Disclosed', 'synonyms' => 'other, international, non disclosed, unknown'],
                      ];

                      // Complete 195+ UN Sovereign Demonyms with Country Name Synonyms
                      $allNationalities = [
                          'Afghan' => 'Afghanistan', 'Albanian' => 'Albania', 'Algerian' => 'Algeria', 'Andorran' => 'Andorra', 'Angolan' => 'Angola', 
                          'Antiguan' => 'Antigua and Barbuda', 'Argentine' => 'Argentina', 'Armenian' => 'Armenia', 'Australian' => 'Australia', 'Austrian' => 'Austria',
                          'Azerbaijani' => 'Azerbaijan', 'Bahamian' => 'Bahamas', 'Bahraini' => 'Bahrain', 'Bangladeshi' => 'Bangladesh', 'Barbadian' => 'Barbados', 
                          'Belarusian' => 'Belarus', 'Belgian' => 'Belgium', 'Belizean' => 'Belize', 'Beninese' => 'Benin', 'Bhutanese' => 'Bhutan', 
                          'Bolivian' => 'Bolivia', 'Bosnian' => 'Bosnia and Herzegovina', 'Botswanan' => 'Botswana', 'Brazilian' => 'Brazil', 'British' => 'United Kingdom, UK, Britain', 
                          'Bruneian' => 'Brunei', 'Bulgarian' => 'Bulgaria', 'Burkinabe' => 'Burkina Faso', 'Burmese' => 'Myanmar, Burma', 'Burundian' => 'Burundi', 
                          'Cambodian' => 'Cambodia', 'Cameroonian' => 'Cameroon', 'Canadian' => 'Canada', 'Cape Verdean' => 'Cape Verde', 'Central African' => 'Central African Republic', 
                          'Chadian' => 'Chad', 'Chilean' => 'Chile', 'Chinese' => 'China, PRC', 'Colombian' => 'Colombia', 'Comorian' => 'Comoros', 
                          'Congolese' => 'Congo', 'Costa Rican' => 'Costa Rica', 'Croatian' => 'Croatia', 'Cuban' => 'Cuba', 'Cypriot' => 'Cyprus', 
                          'Czech' => 'Czech Republic, Czechia', 'Danish' => 'Denmark', 'Djiboutian' => 'Djibouti', 'Dominican' => 'Dominican Republic', 'Dutch' => 'Netherlands, Holland', 
                          'East Timorese' => 'East Timor', 'Ecuadorean' => 'Ecuador', 'Egyptian' => 'Egypt', 'Emirati' => 'United Arab Emirates, UAE, Dubai', 'Equatorial Guinean' => 'Equatorial Guinea', 
                          'Eritrean' => 'Eritrea', 'Estonian' => 'Estonia', 'Ethiopian' => 'Ethiopia', 'Fijian' => 'Fiji', 'Filipino' => 'Philippines', 
                          'Finnish' => 'Finland', 'French' => 'France', 'Gabonese' => 'Gabon', 'Gambian' => 'Gambia', 'Georgian' => 'Georgia', 
                          'German' => 'Germany, Deutschland', 'Ghanaian' => 'Ghana', 'Greek' => 'Greece', 'Grenadian' => 'Grenada', 'Guatemalan' => 'Guatemala', 
                          'Guinean' => 'Guinea', 'Guyanese' => 'Guyana', 'Haitian' => 'Haiti', 'Honduran' => 'Honduras', 'Hungarian' => 'Hungary', 
                          'Icelander' => 'Iceland', 'Indian' => 'India, Bharat', 'Indonesian' => 'Indonesia', 'Iranian' => 'Iran', 'Iraqi' => 'Iraq', 
                          'Irish' => 'Ireland, Eire', 'Israeli' => 'Israel', 'Italian' => 'Italy', 'Ivorian' => 'Ivory Coast, Cote dIvoire', 'Jamaican' => 'Jamaica', 
                          'Japanese' => 'Japan', 'Jordanian' => 'Jordan', 'Kazakh' => 'Kazakhstan', 'Kenyan' => 'Kenya', 'Kuwaiti' => 'Kuwait', 
                          'Kyrgyz' => 'Kyrgyzstan', 'Laotian' => 'Laos', 'Latvian' => 'Latvia', 'Lebanese' => 'Lebanon', 'Liberian' => 'Liberia', 
                          'Libyan' => 'Libya', 'Liechtenstein citizen' => 'Liechtenstein', 'Lithuanian' => 'Lithuania', 'Luxembourgish' => 'Luxembourg', 'Macedonian' => 'North Macedonia', 
                          'Malagasy' => 'Madagascar', 'Malawian' => 'Malawi', 'Malaysian' => 'Malaysia', 'Maldivian' => 'Maldives', 'Malian' => 'Mali', 
                          'Maltese' => 'Malta', 'Marshallese' => 'Marshall Islands', 'Mauritanian' => 'Mauritania', 'Mauritian' => 'Mauritius', 'Mexican' => 'Mexico', 
                          'Micronesian' => 'Micronesia', 'Moldovan' => 'Moldova', 'Monegasque' => 'Monaco', 'Mongolian' => 'Mongolia', 'Montenegrin' => 'Montenegro', 
                          'Moroccan' => 'Morocco', 'Mozambican' => 'Mozambique', 'Namibian' => 'Namibia', 'Nauruan' => 'Nauru', 'Nepalese' => 'Nepal', 
                          'New Zealander' => 'New Zealand, NZ', 'Nicaraguan' => 'Nicaragua', 'Nigerian' => 'Nigeria', 'Nigerien' => 'Niger', 'North Korean' => 'North Korea', 
                          'Norwegian' => 'Norway', 'Omani' => 'Oman', 'Pakistani' => 'Pakistan', 'Palauan' => 'Palau', 'Palestinian' => 'Palestine', 
                          'Panamanian' => 'Panama', 'Papua New Guinean' => 'Papua New Guinea', 'Paraguayan' => 'Paraguay', 'Peruvian' => 'Peru', 'Polish' => 'Poland', 
                          'Portuguese' => 'Portugal', 'Qatari' => 'Qatar', 'Romanian' => 'Romania', 'Russian' => 'Russia', 'Rwandan' => 'Rwanda', 
                          'Saint Lucian' => 'Saint Lucia', 'Salvadoran' => 'El Salvador', 'Samoan' => 'Samoa', 'San Marinese' => 'San Marino', 'Sao Tomean' => 'Sao Tome and Principe', 
                          'Saudi' => 'Saudi Arabia, KSA', 'Senegalese' => 'Senegal', 'Serbian' => 'Serbia', 'Seychellois' => 'Seychelles', 'Sierra Leonean' => 'Sierra Leone', 
                          'Singaporean' => 'Singapore', 'Slovak' => 'Slovakia', 'Slovenian' => 'Slovenia', 'Solomon Islander' => 'Solomon Islands', 'Somali' => 'Somalia', 
                          'South African' => 'South Africa, RSA', 'South Korean' => 'South Korea, Korea', 'South Sudanese' => 'South Sudan', 'Spanish' => 'Spain', 'Sri Lankan' => 'Sri Lanka', 
                          'Sudanese' => 'Sudan', 'Surinamese' => 'Suriname', 'Swazi' => 'Eswatini, Swaziland', 'Swedish' => 'Sweden', 'Swiss' => 'Switzerland', 
                          'Syrian' => 'Syria', 'Taiwanese' => 'Taiwan', 'Tajik' => 'Tajikistan', 'Tanzanian' => 'Tanzania', 'Thai' => 'Thailand', 
                          'Togolese' => 'Togo', 'Tongan' => 'Tonga', 'Trinidadian' => 'Trinidad and Tobago', 'Tunisian' => 'Tunisia', 'Turkish' => 'Turkey, Turkiye', 
                          'Turkmen' => 'Turkmenistan', 'Tuvaluan' => 'Tuvalu', 'Ugandan' => 'Uganda', 'Ukrainian' => 'Ukraine', 'Uruguayan' => 'Uruguay', 
                          'Uzbek' => 'Uzbekistan', 'Vanuatu' => 'Vanuatu', 'Vatican citizen' => 'Vatican City', 'Venezuelan' => 'Venezuela', 'Vietnamese' => 'Vietnam', 
                          'Yemeni' => 'Yemen', 'Zambian' => 'Zambia', 'Zimbabwean' => 'Zimbabwe'
                      ];
                    @endphp
                    <select name="nationality" id="nationality">
                      <option value="">Select nationality</option>
                      <optgroup label="⭐ Top 10 Popular Nationalities">
                        @foreach($topPopularNationalities as $natKey => $natItem)
                          <option value="{{ $natKey }}" data-synonyms="{{ $natItem['synonyms'] }}" {{ ((string)$selectedNationality === (string)$natKey) ? 'selected="selected"' : '' }}>{{ $natItem['label'] }}</option>
                        @endforeach
                      </optgroup>
                      <optgroup label="🌐 All Sovereign Demonyms (A–Z)">
                        @foreach($allNationalities as $demonym => $countrySynonyms)
                          <option value="{{ $demonym }}" data-synonyms="{{ $countrySynonyms }}" {{ ((string)$selectedNationality === (string)$demonym && !isset($topPopularNationalities[$demonym])) ? 'selected="selected"' : '' }}>{{ $demonym }}</option>
                        @endforeach
                      </optgroup>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="dob" value="{{ @$application->dob }}" onclick="this.showPicker()"
                      placeholder="Select date" />
                  </div>
                </div>
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Enter Primary Email ID <span class="rq">*</span></label>
                    <input type="email" name="email" id="email" value="{{ @$application->email }}" placeholder="Enter primary email" />
                  </div>
                  <div class="fg fv-row">
                    <label>Enter Secondary Email ID</label>
                    <input type="email" name="secondrary_email" id="secondrary_email"
                      value="{{ @$application->secondrary_email }}" placeholder="Enter secondary email" />
                  </div>
                  <div class="fg fv-row">
                    <label>Enter Primary Number <span class="rq">*</span></label>
                    <div class="ig">
                      {{-- <span class="ig-p">+91 ▼</span> --}}
                      <input type="number" name="phone" id="phone" value="{{ @$application->phone }}"
                        placeholder="Enter primary number" style="padding-left: 76px !important; width: 307px;" />
                      <input type="hidden" value="{{ @$phone_code }}" id="primary_country_code" name="primary_country_code">
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Enter Secondary Number</label>
                    <div class="ig">
                      {{-- <span class="ig-p">+91 ▼</span> --}}
                      <input type="number" name="secondary_mobile" id="secondary_mobile"
                        value="{{ @$application->secondary_mobile }}" placeholder="Enter secondary number"
                        style="padding-left: 76px !important; width: 307px;" />
                      <input type="hidden" value="{{ @$second_phone_code }}" id="secondary_country_code"
                        name="secondary_country_code">
                    </div>
                  </div>
                </div>
                <div class="g4">
                  <div class="fg fv-row" style="grid-column: span 2">
                    <label>Current Location <span class="rq">*</span></label>
                        <input type="text" class="" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter current location" />
                  </div>
                  <div class="fg fv-row">
                    <label>Highest Qualification <span class="rq">*</span></label>
                    <select name="qualification" id="qualification">
                      <option value="">Select qualification</option>
                      @php
                        // Map incoming database qualification records by ID
                        $qualMap = [];
                        if (!empty($qualification)) {
                            foreach ($qualification as $qItem) {
                                $qualMap[$qItem->id] = $qItem;
                            }
                        }

                        // 7-Category Global Education Standard with Regional Prov & Global Alias Taxonomy
                        $educationCategories = [
                            "🎓 Bachelor's / Undergraduate Degrees" => [
                                13 => ['label' => 'B.Tech / B.E (Engineering & Technology)', 'badge' => 'Engineering / Tech', 'regions' => 'Indian,Commonwealth,Global', 'aliases' => 'btech,b.tech,b tech,be,b.e,bachelor of technology,bachelor of engineering,beng,b.eng,bse,bs engineering,diplome ingenieur,diplom ingenieur,software engineer,computer science engineering,cse,mechanical,electrical,civil'],
                                12 => ['label' => 'B.S. / B.Sc (Science, IT & Computing / Licence)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'bs,b.s.,bsc,b.sc,bachelor of science,bachelor science,bs cs,bsc cs,b sc,bsc it,bs it,information technology,computer science,data science,cybersecurity,cyber security,cloud computing,artificial intelligence,software development,licence,licenciatura,bac+3,grado'],
                                16 => ['label' => 'BCA (Computer Applications / Software)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'bca,b.c.a.,bachelor of computer applications,bachelor computer applications,computer programming,web development'],
                                5  => ['label' => 'BBA / BMS (Business Administration)', 'badge' => 'Business / Global', 'regions' => 'Indian,American,Canadian,British,Australian,Global', 'aliases' => 'bba,b.b.a.,bms,b.m.s.,bachelor of business administration,bachelor business,management studies,business management,bba marketing,bba finance'],
                                6  => ['label' => 'B.Com (Commerce, Accounting & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Canadian,Australian,Commonwealth,Global', 'aliases' => 'bcom,b.com,bachelor of commerce,bachelor commerce,commerce,accounting,finance,banking'],
                                3  => ['label' => 'B.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ba,b.a.,bachelor of arts,bachelor arts,humanities,economics,psychology,english,journalism,mass communication,history'],
                                7  => ['label' => 'B.Des (Design & UX/UI)', 'badge' => 'Design / Global', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bdes,b.des,bachelor of design,bachelor design,ui ux,product design,graphic design,fashion design,industrial design'],
                                18 => ['label' => 'BFA (Fine Arts & Digital Media)', 'badge' => 'Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bfa,b.f.a.,bachelor of fine arts,visual arts,animation,digital media,illustration'],
                                4  => ['label' => 'B.Arch (Architecture & Urban Planning)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'barch,b.arch,bachelor of architecture,architectural design,urban planning'],
                                11 => ['label' => 'B.Pharma / Nursing / Allied Health (Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'bpharma,b.pharma,b pharm,b.pharm,bachelor of pharmacy,pharmacy,nursing,b.sc nursing,bsc nursing,rn,registered nurse,bpt,physiotherapy,bachelor of physiotherapy,allied health,paramedical,pharm d,pharm.d'],
                                19 => ['label' => 'BHM (Hotel & Hospitality Management)', 'badge' => 'Hospitality', 'regions' => 'Indian,Global', 'aliases' => 'bhm,b.h.m.,bachelor of hotel management,hospitality,tourism,culinary arts'],
                                8  => ['label' => 'B.Ed (Education & Pedagogy)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'bed,b.ed,bachelor of education,teaching,pedagogy'],
                                9  => ['label' => 'B.El.Ed (Elementary Education)', 'badge' => 'Education', 'regions' => 'Indian', 'aliases' => 'beled,b.el.ed,bachelor of elementary education,primary teaching'],
                                10 => ['label' => 'B.P.Ed (Physical Education & Sports)', 'badge' => 'Sports Science', 'regions' => 'Indian,Global', 'aliases' => 'bped,b.p.ed,bachelor of physical education,sports science,kinesiology'],
                                21 => ['label' => 'BVSC / DVM (Veterinary Science & Medicine)', 'badge' => 'Veterinary', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bvsc,b.v.sc,bachelor of veterinary science,dvm,veterinary medicine,veterinarian'],
                                61 => ['label' => 'Other Bachelor\'s / Undergraduate Degree', 'badge' => 'Bachelor\'s / Other', 'regions' => 'Global', 'aliases' => 'other bachelor,other bachelors,other undergraduate,bachelors other,custom bachelor,unlisted bachelor'],
                            ],
                            "🎓 Master's / Postgraduate Degrees" => [
                                39 => ['label' => 'M.S. / M.Sc / M.Eng (Science, IT & Engineering / Master 2)', 'badge' => 'USA / Global / EU', 'regions' => 'American,Canadian,British,Australian,European,Indian,Global', 'aliases' => 'ms,m.s.,msc,m.sc,master of science,master science,ms cs,msc cs,m s,m sc,meng,m.eng,master of engineering,data science,ai,artificial intelligence,machine learning,deep learning,cybersecurity,cloud architecture,big data,bioinformatics,master 2,bac+5,magister'],
                                40 => ['label' => 'M.Tech / M.E (Engineering & Advanced Tech)', 'badge' => 'Engineering / Postgrad', 'regions' => 'Indian,Global', 'aliases' => 'mtech,m.tech,m tech,me,m.e,master of technology,master of engineering,meng,m.eng,postgraduate engineering,vlsi,embedded,robotics'],
                                41 => ['label' => 'MBA / PGDM (Business Administration & Strategy)', 'badge' => 'Management / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'mba,m.b.a.,pgdm,p.g.d.m.,master of business administration,master business administration,executive mba,emba,insead,wharton,hbs,iim,post graduate diploma in management,business management,strategy,consulting'],
                                42 => ['label' => 'MCA (Master of Computer Applications)', 'badge' => 'IT / Software', 'regions' => 'Indian,Global', 'aliases' => 'mca,m.c.a.,master of computer applications,master computer applications,software engineering,computer science'],
                                43 => ['label' => 'MCM (Master of Computer Management)', 'badge' => 'Management & IT', 'regions' => 'Indian', 'aliases' => 'mcm,m.c.m.,master of computer management,it management'],
                                35 => ['label' => 'M.Com (Commerce, Economics & Finance)', 'badge' => 'Commerce / Finance', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mcom,m.com,master of commerce,economics,financial management,accounting'],
                                32 => ['label' => 'M.A (Arts, Humanities & Social Sciences)', 'badge' => 'Humanities / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ma,m.a.,master of arts,humanities,sociology,psychology,political science,international relations,literature'],
                                36 => ['label' => 'M.Des (Master of Design / UX)', 'badge' => 'Design / Postgrad', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mdes,m.des,master of design,interaction design,ux design,product design'],
                                45 => ['label' => 'MFA (Fine Arts & Creative Media)', 'badge' => 'Fine Arts / Media', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mfa,m.f.a.,master of fine arts,creative media,visual arts,cinematography'],
                                33 => ['label' => 'M.Arch (Master of Architecture)', 'badge' => 'Architecture', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'march,m.arch,master of architecture,landscape architecture,urban design'],
                                38 => ['label' => 'M.Pharma / Pharm.D / MPT (Advanced Healthcare & Pharma)', 'badge' => 'Healthcare / Pharma', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => 'mpharma,m.pharma,m pharm,m.pharm,master of pharmacy,pharm d,pharm.d,doctor of pharmacy,mpt,master of physiotherapy,pharmacology,clinical research'],
                                37 => ['label' => 'M.Ed (Master of Education)', 'badge' => 'Education', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'med,m.ed,master of education,educational leadership,curriculum'],
                                47 => ['label' => 'MVSC (Master of Veterinary Science)', 'badge' => 'Veterinary Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mvsc,m.v.sc,master of veterinary science,veterinary surgery'],
                                30 => ['label' => 'Integrated PG / Dual Degree (B.Tech+M.Tech / BS+MS)', 'badge' => 'Integrated / Dual', 'regions' => 'Indian,European,American,Global', 'aliases' => 'integrated pg,dual degree,integrated mtech,integrated btech mtech,5 year integrated,bs ms,b.s. m.s.,btech mtech'],
                                62 => ['label' => 'Other Master\'s / Postgraduate Degree', 'badge' => 'Master\'s / Other', 'regions' => 'Global', 'aliases' => 'other master,other masters,other postgraduate,masters other,custom master,unlisted master,mph,msw,mphil,m.phil'],
                            ],
                            "🏛️ Doctorate & Research Degrees" => [
                                52 => ['label' => 'Ph.D. / Doctorate / Post-Doc (Doctor of Philosophy)', 'badge' => 'Doctorate / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'phd,ph.d.,ph.d,doctorate,doctor of philosophy,dphil,d.phil,postdoc,post doc,post doctoral,fellowship,research scholar,promotion,dr'],
                                64 => ['label' => 'Other Doctorate / Post-Doctoral Degree', 'badge' => 'Doctorate / Other', 'regions' => 'Global', 'aliases' => 'other doctorate,other phd,postdoc,post-doctoral,dba,d.sc,doctor of business administration,research fellow'],
                            ],
                            "📜 Associate Degrees & Diplomas" => [
                                22 => ['label' => 'Associate Degree / Polytechnic Diploma (A.A. / A.S. / Diploma)', 'badge' => 'Polytechnic / Assoc', 'regions' => 'Indian,American,Canadian,Australian,European,Global', 'aliases' => 'diploma,polytechnic,associate degree,associate of science,associate of arts,aa,a.a.,as,a.s.,aas,a.a.s.,community college,2 year degree,bts,dut,fachhochschule'],
                                48 => ['label' => 'PG Diploma (Post Graduate Diploma / Certificate)', 'badge' => 'PG Diploma', 'regions' => 'Indian,Canadian,British,Commonwealth,Global', 'aliases' => 'pg diploma,pgd,pgdiploma,post graduate diploma,postgraduate certificate,post-degree diploma,canadian pg diploma'],
                                25 => ['label' => 'HND / HNC (Higher National Diploma / Certificate)', 'badge' => 'UK / Commonwealth', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'hnd,hnc,higher national diploma,higher national certificate,vocational qualification,btec'],
                                63 => ['label' => 'Other Associate / Diploma / Vocational Degree', 'badge' => 'Diploma / Other', 'regions' => 'Global', 'aliases' => 'other diploma,other associate,vocational diploma,technical certificate,custom diploma,unlisted diploma'],
                            ],
                            "⚖️ Professional, Legal & Medical Degrees" => [
                                26 => ['label' => 'CA / CPA / CFA / ACCA (Finance & Accounting)', 'badge' => 'Finance / Global', 'regions' => 'Indian,American,Canadian,British,Australian,European,Global', 'aliases' => 'ca,cpa,cfa,acca,chartered accountant,chartered accountancy,certified public accountant,chartered financial analyst,cima,cost accountant,accounting,finance,audit'],
                                27 => ['label' => 'CS (Company Secretary / Corporate Governance)', 'badge' => 'Corporate Law', 'regions' => 'Indian,British,Commonwealth', 'aliases' => 'cs,c.s.,company secretary,company secretaryship,corporate governance,icsi'],
                                29 => ['label' => 'ICWA / CMA (Cost & Management Accounting)', 'badge' => 'Cost Accounting', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'icwa,cma,cost accountant,cost and management accountant,cma usa,icmai'],
                                23 => ['label' => 'LLB / J.D. (Law Degree / Juris Doctor)', 'badge' => 'Legal / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'llb,l.l.b.,bachelor of laws,jd,j.d.,juris doctor,law degree,bar admitted,attorney,lawyer,solicitor,advocate'],
                                31 => ['label' => 'LLM (Master of Laws / Legal Postgrad)', 'badge' => 'Legal / Postgrad', 'regions' => 'Indian,American,Canadian,British,European,Commonwealth,Global', 'aliases' => 'llm,l.l.m.,master of laws,corporate law,international law,intellectual property'],
                                24 => ['label' => 'MBBS / M.D. / D.O. (General Medicine & Surgery)', 'badge' => 'Medical / Surgery', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'mbbs,m.b.b.s.,bachelor of medicine,md,m.d.,doctor of medicine,do,d.o.,doctor of osteopathic medicine,physician'],
                                46 => ['label' => 'MD / MS (Medical Specialty / Master of Surgery)', 'badge' => 'Medical Specialty', 'regions' => 'Indian,American,Canadian,British,Commonwealth,Global', 'aliases' => 'md,m.d.,ms general surgery,master of surgery,doctor of medicine,residency,medical specialty'],
                                28 => ['label' => 'DM (Doctor of Medicine - Medical Super Specialty)', 'badge' => 'Super Specialty', 'regions' => 'Indian,Global', 'aliases' => 'dm,d.m.,super specialty,cardiology,neurology,oncology'],
                                34 => ['label' => 'M.Ch (Surgical Super Specialty)', 'badge' => 'Surgical Specialty', 'regions' => 'Indian,Global', 'aliases' => 'mch,m.ch,magister chirurgiae,neurosurgery,cardiothoracic'],
                                17 => ['label' => 'BDS / DDS (Dental Surgery & Medicine)', 'badge' => 'Dental / Global', 'regions' => 'Indian,British,American,Commonwealth,Global', 'aliases' => 'bds,b.d.s.,dds,d.d.s.,dmd,d.m.d.,dental surgery,dentist,oral surgery'],
                                44 => ['label' => 'MDS (Master of Dental Surgery)', 'badge' => 'Dental Postgrad', 'regions' => 'Indian,British,Commonwealth,Global', 'aliases' => 'mds,m.d.s.,master dental surgery,orthodontics,prosthodontics'],
                                15 => ['label' => 'BAMS (Ayurvedic Medicine & Surgery)', 'badge' => 'Ayurveda', 'regions' => 'Indian', 'aliases' => 'bums,b.a.m.s.,ayurveda,ayush'],
                                20 => ['label' => 'BHMS (Homeopathic Medicine & Surgery)', 'badge' => 'Homeopathy', 'regions' => 'Indian', 'aliases' => 'bhms,b.h.m.s.,homeopathy,ayush'],
                                14 => ['label' => 'B.U.M.S (Unani Medicine & Surgery)', 'badge' => 'Unani', 'regions' => 'Indian', 'aliases' => 'bums,b.u.m.s.,unani,ayush'],
                            ],
                            "🏫 Secondary & High School" => [
                                2  => ['label' => '12th Standard / High School Diploma / A-Levels / GED', 'badge' => 'High School', 'regions' => 'Indian,American,Canadian,British,Global', 'aliases' => '12th,12th standard,hsc,intermediate,high school,high school diploma,ged,abitur,baccalaureat,senior secondary'],
                                1  => ['label' => '10th Standard / Matriculation / Secondary School', 'badge' => 'Secondary', 'regions' => 'Indian,Global', 'aliases' => '10th,10th standard,ssc,matric,matriculation,secondary school'],
                                49 => ['label' => 'A Levels / Advanced Level (UK / International)', 'badge' => 'UK A-Levels', 'regions' => 'British,Commonwealth,European,Global', 'aliases' => 'a levels,a-levels,alevel,advanced level,gce a levels'],
                                51 => ['label' => 'GCSE / O-Levels (UK / International)', 'badge' => 'UK GCSE', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gcse,o-levels,o levels,olevel,igcse,gce o level'],
                                50 => ['label' => 'Vocational Secondary / GNVQ / Technical Certificate', 'badge' => 'Vocational', 'regions' => 'British,Commonwealth,Global', 'aliases' => 'gnvq,vocational secondary,nvq,btec level 2,technical certificate'],
                            ],
                            "🌐 Other Qualifications" => [
                                60 => ['label' => 'Others / Intensive Bootcamp / Specialized Certification', 'badge' => 'Other / Bootcamp', 'regions' => 'Global,Indian,American,Canadian,British,Australian,European', 'aliases' => 'other,others,non disclosed,bootcamp,coding bootcamp,full stack,certifications,aws certified,pmp,scrum master'],
                            ],
                        ];

                        $selectedQualId = @$application->qualification;
                        $renderedIds = [];
                      @endphp

                      @foreach($educationCategories as $categoryLabel => $catItems)
                        <optgroup label="{{ $categoryLabel }}">
                          @foreach($catItems as $qId => $itemMeta)
                            @if(isset($qualMap[$qId]))
                              @php
                                $renderedIds[] = $qId;
                                $isSelected = ((string)$selectedQualId === (string)$qId);
                              @endphp
                              <option value="{{ $qId }}" 
                                      data-regions="{{ $itemMeta['regions'] }}"
                                      data-aliases="{{ $itemMeta['aliases'] }}"
                                      data-badge="{{ $itemMeta['badge'] }}"
                                      {{ $isSelected ? 'selected="selected"' : '' }}>
                                {{ $itemMeta['label'] }}
                              </option>
                            @endif
                          @endforeach
                        </optgroup>
                      @endforeach

                      {{-- Fallback safety for any unmapped database record --}}
                      @php
                        $remainingItems = [];
                        foreach ($qualMap as $qId => $qObj) {
                            if (!in_array($qId, $renderedIds)) {
                                $remainingItems[] = $qObj;
                            }
                        }
                      @endphp
                      @if(count($remainingItems) > 0)
                        <optgroup label="Additional Qualifications">
                          @foreach($remainingItems as $remItem)
                            <option value="{{ $remItem->id }}" data-regions="Global" data-aliases="" data-badge="Other" {{ ((string)$selectedQualId === (string)$remItem->id) ? 'selected="selected"' : '' }}>
                              {{ $remItem->name }}
                            </option>
                          @endforeach
                        </optgroup>
                      @endif
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Preferred Work Mode <span class="rq">*</span></label>
                    <div class="rg">
                      <div class="ri"><input type="radio" name="wm" id="wr" {{ @$application->wm == 'remote' ? 'checked' : '' }} value="remote" />
                        <label for="wr">Remote</label>
                      </div>
                      <div class="ri">
                        <input type="radio" name="wm" id="wh" {{ (@$application->wm == 'hybrid' || empty(@$application->wm)) ? 'checked' : '' }} value="hybrid" checked />
                        <label for="wh">Hybrid</label>
                      </div>
                      <div class="ri">
                        <input type="radio" name="wm" id="wo" {{ @$application->wm == 'office' ? 'checked' : '' }} value="office" />
                        <label for="wo">Work From Office</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Professional Details -->
            <div class="fs fv-row">
              <div class="fsh"><h3>Primary Skills *</h3></div>
              <div class="fsc">
              <div id="skillTags" class="skill-tags"></div>
               <input type="text" class="" value="{{ @$application->skills }}" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
              </div>
            </div>

            <!-- Professional Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Professional Details</h3>
              </div>
              <div class="fsc">
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Current Company <span class="rq">*</span></label>
                    <input type="text" name="current_company" id="current_company" value="{{ @$application->current_company }}" placeholder="Enter current company" />
                  </div>
                  <div class="fg fv-row">
                    <label>Current Designation <span class="rq">*</span></label>
                    <input type="text" name="current_designation" id="current_designation" value="{{( @$application->current_designation) ? @$application->current_designation : @$jobDetail->title }}" placeholder="Enter current designation" />
                  </div>
                  <div class="fg fv-row">
                    <label>Total Experience <span class="rq">*</span></label>
                    <div class="ei">
                      @php
                       $value = @$application->total_exp; // Convert to string to preserve decimal
                       if(!is_null($value)){
                        $total_exp =  GeneralMethod::separateNumberParts($value);
                        $exp_years = ($total_exp[0]) ;
                        $exp_months = ($total_exp[1]); 
                      
                        $selected_exp_year = ($exp_years) ;
                        $selected_exp_month = ($exp_months); 
                       }else{
                        $selected_exp_year = null ;
                        $selected_exp_month = null; 
                       }
                      @endphp

                      <select id="total_exp_year" name="total_exp_year">
                        <option value="">Years</option>
                        <option value="0">Fresher</option> 
                        @php 
                            for ($i = 1; $i <= 60; $i++) {
                                  $selected = '';
                                  $s = 'Year';
                                  if($i > 1){
                                      $s = 'Years';
                                  }
                                  if(is_null($selected_exp_year)){
                                    $selected = '';
                                  }else{
                                    if($i==$selected_exp_year){
                                      $selected =  'selected';
                                  }
                                  }
                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                            }
                          @endphp
                      </select>
                     <select class="" arial-label="Select total_exp_month" id="total_exp_month" name="total_exp_month">
                          <option value="">Select Month</option>
                          @php 
                                       for ($i = 0; $i <= 11; $i++) {
                                              $selected = '';
                                              $s = 'Month';
                                              if($i > 1){
                                                 $s = 'Months';
                                              }
                                              if(is_null($selected_exp_month)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_exp_month){
                                                  $selected =  'selected';
                                              }
                                              }
                                              
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>
                    </div>
                  </div>
                  <div class="fg fv-row">
                    <label>Relevant Experience <span class="rq">*</span></label>
                    <div class="ei">
                        @php

                            $valuew = @$application->rel_exp;
                            // dd( $value);
                            if(!is_null($value)){
                              $total_rel_exp =  GeneralMethod::separateNumberParts($valuew);
                              // dd($total_rel_exp,$valuew);
                              $rel_exp_years = ($total_rel_exp[0]);
                              $rel_exp_months = ($total_rel_exp[1]);  

                              $selected_rel_exp_year = ($rel_exp_years);
                              $selected_rel_exp_month= ($rel_exp_months);
                              // dd($selected_rel_exp_year,$selected_rel_exp_month);
                            }else{
                              $selected_rel_exp_year = null;
                              $selected_rel_exp_month= null;
                            }
                            //  dd($total_rel_exp);
                            //  dd($selected_rel_exp_year,$selected_rel_exp_month);

                        @endphp
                      <select id="rel_exp_year" name="rel_exp_year">
                        <option value="">Years</option>
                        <option value="0">Fresher</option>   
                        @php 
                              for ($i = 1; $i <= 60; $i++) {
                                    $selected = '';
                                    $s = 'Year';
                                    if($i > 1){
                                        $s = 'Years';
                                    }
                                    
                                  if(is_null($selected_rel_exp_year)){
                                      $selected = '';
                                    }else{
                                      if($i==$selected_rel_exp_year){
                                        $selected =  'selected';
                                    }
                                    }
                                  echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                              }
                        @endphp
                      </select>
                      <select class="" arial-label="Select rel_exp_month" id="rel_exp_month" name="rel_exp_month">
                          <option value="">Select Month</option>
                          @php 
                                        for ($i = 0; $i <= 11; $i++) {
                                             $selected = '';
                                              $s = 'Month';
                                              if($i > 1){
                                                 $s = 'Months';
                                              }
                                              if(is_null($selected_rel_exp_month)){
                                                $selected = '';
                                              }else{
                                                if($i==$selected_rel_exp_month){
                                                  $selected =  'selected';
                                              }
                                              }
                                            echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                        }
                          @endphp
                        </select>
                    </div>
                  </div>
                </div>
                <div class="g4">
                  
                  <div class="fg fv-row">
                    <label>Employment Type</label>
                    <select name="employmentType" id="EmploymentType">
                      <option value="">Select employment type</option>
                      <option {{ (@$application->employmentType==='Full Time') ? 'selected' : '' }}>Full Time</option>
                      <option {{ (@$application->employmentType==='Contract') ? 'selected' : '' }}>Contract</option>
                      <option {{ (@$application->employmentType==='Part Time') ? 'selected' : '' }}>Part Time</option>
                      <option {{ (@$application->employmentType==='Freelance') ? 'selected' : '' }}>Freelance</option>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Notice Period <span class="rq">*</span></label>
                    <select name="serving_notice_period" id="serving_notice_period">
                      <option value="0" {{ ($snp_mode==0) ? 'selected' : '' }}>Select notice period</option>
                      <option value="1" {{ ($snp_mode===1) ? 'selected' : '' }}>Serving Notice Period</option>
                      <option value="2" {{ ($snp_mode===2) ? 'selected' : '' }}>Immediate</option>
                    </select>
                  </div>

                    <div class="fg fv-row">
                       <label>&nbsp;</label>
                        <div style="{{ @$snp_text_one }}" id="snp-immediate" class="" >
                            <div style="position: relative;margin-right: 6px;">
                              <input type="text" id="text_display" value="{{ $snp_text_dummy }}" class="form-control form-control-solid" placeholder="" readonly>
                            </div>
                          
                            <div style="position: relative;">
                                <input type="text" id="np_id_display" class="form-control form-control-solid" placeholder="Select last working date" name="np_id_display" value="{{ ($snp_last_working) ? date('d/M/Y',strtotime(@$snp_last_working)) : '' }}" onclick="document.getElementById('np_id').click()" readonly>
                                <input type="date" class="form-control form-control-solid np_id" name="np_id" id="np_id" style="opacity: 0; position: absolute; z-index: -1;top: 0;" onclick="this.showPicker()">
                            </div>
                        </div>
                    
                        <select class="form-select form-select-solid" arial-label="Select ddl_np_id" id="ddl_np_id" name="ddl_np_id" style="<?php echo @$snp_text_zero; ?>">
                          <option value="">Select Notice Period</option>
                          @php 
                              for ($i = 1; $i <= 200; $i++) {
                                      $s = 'Day';
                                      if($i > 1){
                                          $s = 'Days';
                                      }
                                    $selected = ($i == @$application->np_id) ? 'selected' : '';
                                  echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                              }
                          @endphp
                        </select>
                        
                        <input type="text" id="calender" name="calender" style="display:none">
                    </div> 
                    <div class="fg fv-row">
                      <label>Reason for Job Change </label>
                      <input type="text" name="reason_for_job_change" maxlength="55" id="reason_for_job_change" value="{{ @$application->reason_for_job_change }}" placeholder="Enter current company" />
                    </div>                  
                </div>
              </div>
            </div>

            <!-- Salary Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Salary Details</h3>
              </div>
              <div class="fsc">
                <div class="sg">
                  <div>
                    <div class="fg fv-row">
                      <label style="color: var(--p)">Annual Salary</label>
                      <div style="font-size: 1rem; font-weight: 600; color:var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>

                      
                      <div class="col-4 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="current_ectc_mode" id="current_ectc_mode">
                              @php 
                                          $current_ectc_mode	=	"";
                                          $current_ectc_mode	=	@$application->current_ectc_mode;
                                          foreach($salaryMode as $key=>$val){
                                            $sel	=	'';
                                            if(2==$key){
                                              $sel	=	'selected="selected"';	
                                            }
                                            echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                          }
                              @endphp
                        </select>
                      </div>
                      <div class="col-4 pl-0 d-none">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                            @php 
                                        $current_ctc_mode	=	"";
                                        $current_ctc_mode	=	@$application->current_ctc_mode;
                                        foreach($salaryMode as $key=>$val){
                                          $sel	=	'';
                                          if(2==$key){
                                            $sel	=	'selected="selected"';	
                                          }	
                                          echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                        }
                            @endphp
                        </select>
                      </div>

                      
                      <select id="current_ctc_cur" name="current_ctc_cur">
                          @php 
                              $current_ctc_cur	=	"";
                              $current_ctc_cur	=	@$application->current_ctc_cur ?? $salary_currency;//@$application->current_ctc_cur;
                              
                              foreach($currency as $key=>$val){
                                  $sel	=	'';
                                  if($current_ctc_cur===null){
                                    if($currency_code==$val){
                                      $sel	=	'selected="selected"';	
                                      }	
                                  }else{
                                    if($current_ctc_cur == $key){
                                      $sel	=	'selected="selected"';	
                                  }	
                                  }
                                                      
                                  echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                              }
                          @endphp
                      </select>
                    </div>
                    <div class="fg fv-row"><label>Current CTC (Annual) <span  style="float: right; color: #ef560a;"id="pay_rate_annual_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="pay_annual" id="pay_annual" value="{{ @$application->ctc_yearly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payrateannual"></span>
                    </div>
                    <div class="fg fv-row"><label>Expected CTC (Annual) <span style="float: right; color: #ef560a;" id="bill_rate_annual_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="bill_annual" id="bill_annual" value="{{ @$application->ectc_yearly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billrateannual"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row">
                      <label style="color: var(--p)">Monthly Salary</label>
                      <div style="font-size: 1rem; font-weight: 600; color: var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      <select name="current_ectc_cur" id="current_ectc_cur">
                          @php 
                              $current_ctc_cur	=	"";
                              $current_ctc_cur	=	@$application->current_ctc_cur ?? $salary_currency;//@$application->current_ctc_cur;
                              
                              foreach($currency as $key=>$val){
                                  $sel	=	'';
                                  if($current_ctc_cur===null){
                                    if($currency_code==$val){
                                      $sel	=	'selected="selected"';	
                                      }	
                                  }else{
                                    if($current_ctc_cur == $key){
                                      $sel	=	'selected="selected"';	
                                  }	
                                  }
                                                      
                                  echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                              }
                          @endphp
                      </select>
                    </div>
                    <div class="fg fv-row"><label>Current (Monthly)<span style="float: right; color: #ef560a;" id="pay_rate_monthly_usd"></span></label>
                      <div class="ig">
                        <input type="number" name="pay_monthly" id="pay_monthly" value="{{ @$application->ctc_monthly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted payratemonthly"></span>
                    </div>
                    <div class="fg fv-row"><label>Expected (Monthly) <span style="float: right; color: #ef560a;" id="bill_rate_monthly_usd"> </span></label>
                      <div class="ig"><input type="number" name="bill_monthly" id="bill_monthly" value="{{ @$application->ectc_monthly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted billratemonthly"></span>
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row"><label style="color: var(--p)">Hourly Salary</label>
                      <div style="font-size: 1rem; font-weight: 600; color: var(--t); margin-bottom: 4px;">Currency <span class="rq">*</span>
                      </div>
                      <select name="current_th_cur" id="current_th_cur">
                          @php 
                              $current_ctc_cur	=	"";
                              $current_ctc_cur	=	@$application->current_ctc_cur ?? $salary_currency;//@$application->current_ctc_cur;
                              
                              foreach($currency as $key=>$val){
                                  $sel	=	'';
                                  if($current_ctc_cur===null){
                                    if($currency_code==$val){
                                      $sel	=	'selected="selected"';	
                                      }	
                                  }else{
                                    if($current_ctc_cur == $key){
                                      $sel	=	'selected="selected"';	
                                  }	
                                  }
                                                      
                                  echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                              }
                          @endphp
                      </select>
                    </div>
                    <div class="fg fv-row"><label>Current (Hourly) <span style="float: right; color: #ef560a;" id="pay_rate_hourly_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="ctc" id="ctc" value="{{ @$application->ctc_hourly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted CurrentCTC"></span> 
                    </div>
                    <div class="fg fv-row"><label>Expected (Hourly) <span  style="float: right; color: #ef560a;"id="bill_rate_hourly_usd"> </span></label>
                      <div class="ig">
                        <input type="number" name="ectc" id="ectc" value="{{ @$application->ectc_hourly }}" placeholder="Enter amount" />
                        <span class="ig-a changeTextCurrency">INR</span>
                      </div>
                      <span class="form-text text-muted expectedctc"></span> 
                    </div>
                  </div>
                  <div>
                    <div class="fg fv-row">
                          <label style="color: var(--p)">Other Salary Details</label>
                        <div style="display: flex;">
                            <div class="">
                              <label>Hike Percentage</label>
                              <div class="ig">
                                <input type="number" value="{{ @$application->percentage_hike }}" name="percentage_hike" id="percentage_hike" style="text-align: center" />
                                <span class="ig-a">%</span>
                              </div>
                              <div style="font-size: 0.95rem; color: var(--t-l); margin-top: 3px;">(Calculated from Annual CTC)</div>
                            </div>
                            <div class="" style="margin-left: 10px;">
                              <div style="margin-bottom: 8px">
                                <label style="font-size: 1rem; font-weight: 600">Offer in Hand?</label>
                                <div class="rg">
                                  <div class="ri">
                                    <input type="radio" name="oh" id="ohy" value="yes" {{ @$application->oh == 'yes' ? 'checked' : '' }} />
                                    <label for="ohy">Yes</label>
                                  </div>
                                  <div class="ri">
                                    <input type="radio" name="oh" id="ohn" value="no"  {{ (@$application->wm == 'no' || empty(@$application->oh)) ? 'checked' : '' }}  />
                                    <label for="ohn">No</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="fg fv-row">
                      <label>Comments</label>
                      <textarea name="comment" id="comment" placeholder="Enter comments about salary expectations or any other details..." rows="4">{{ @$application->comment }}</textarea>
                      <div class="cc">0 / 500</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Skills Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Job Skills Details</h3>
              </div>
              <div class="fsc">
                <table class="sk">
                  <thead>
                    <tr>
                      <th>Skill <span class="rq">*</span></th>
                      <th>Experience (Years) <span class="rq">*</span></th>
                      <th>Experience (Months)</th>
                      <th>Proficiency Rating <span class="rq">*</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($jobSkill as $skill_val)
                        @if(!empty($skill_val->skill_name))

                            @php
                                  if($skill_val->skill_value){
                                    $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                                    // dd($skillValue);
                                    if(!is_null($value) && !empty($value)){
                                      $total_skill_exp =  GeneralMethod::separateNumberParts($skillValue);
                                      $skill_exp_years = ($total_skill_exp[0]);
                                      $skill_exp_months = ($total_skill_exp[1]); 

                                      $selected_skill_exp_year = $skill_exp_years;
                                      $selected_skill_exp_month = $skill_exp_months;
                                      $selected_skill_rating = $skill_val->rating ?? null;
                                    }else{
                                      $selected_skill_exp_year = null;
                                      $selected_skill_exp_month =null;
                                      $selected_skill_rating = null;
                                    }
                                    //  dd($skillValue,$skill_val->skill_value);
                                  }else{
                                    $selected_skill_exp_year = null;
                                    $selected_skill_exp_month =null;
                                    $selected_skill_rating = null;
                                  }

                            @endphp

                            <tr>
                              <td><input type="text" value="{{ strtoupper($skill_val->skill_name) }}" placeholder="Enter skill" readonly /></td>
                              <td>
                                <select class="form-select form-select-solid" arial-label="Select rel_exp_year" id="rel_exp_year" name="YearskillId[{{ $skill_val->id }}]">
                                      <option value="0">Select Year</option>
                                      @php 
                                          for ($i = 1; $i <= 60; $i++) {
                                               $selected = '';
                                                $s = 'Year';
                                                if($i > 1){
                                                    $s = 'Years';
                                                }
                                                if(is_null($selected_skill_exp_year)){
                                                $selected = '';
                                                }else{
                                                  if($i==$selected_skill_exp_year){
                                                    $selected =  'selected';
                                                }
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                    </select>
                              </td>
                              <td>
                                <select class="form-select form-select-solid" arial-label="Select rel_exp_month" id="rel_exp_month" name="MonthskillId[{{ $skill_val->id }}]">
                                      <option value="">Select Month</option>
                                      @php 
                                          for ($i = 0; $i <= 11; $i++) {
                                            $selected = '';
                                                $s = 'Month';
                                                if($i > 1){
                                                    $s = 'Months';
                                                }
                                                if(is_null($selected_skill_exp_month)){
                                                $selected = '';
                                                }else{
                                                  if($i==$selected_skill_exp_month){
                                                    $selected =  'selected';
                                                }
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                </select>
                              </td>
                              <td>
                                 <select class="form-select form-select-solid" arial-label="Select rating" id="rating" name="rating[{{ $skill_val->id }}]">
                                      <option value="0">Select rating</option>
                                      @php 
                                          for ($i = 1; $i <= 5; $i++) {
                                               $selected = '';
                                                $s = 'Star';
                                                if($i > 1){
                                                    $s = 'Stars';
                                                }
                                                if(is_null($selected_skill_rating)){
                                                $selected = '';
                                                }else{
                                                  if($i==$selected_skill_rating){
                                                    $selected =  'selected';
                                                }
                                                }
                                                echo "<option value=\"$i\" $selected>{$i} {$s}</option>";
                                          }
                                      @endphp
                                    </select>
                              </td>
                            </tr>
                        @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

             <!-- Skills Details -->
            <div class="fs">
              <div class="fsh">
                <h3>Job Questions</h3>
              </div>
              <div class="fsc">

                <!--begin::Custom Details-->
                <div class="" id="job_additional_detail">
                  @php 
                    if(!empty($job_additional_detail)){
                    
                    $customTitleId	=	1;
                    $html			=	'';
                   
                    $inc_first		=	0;
                    $inc_last		=	1;
                     $html	.=	'<div class="form-group row mb-10">';	
                    foreach($job_additional_detail as $aDetailArr){
                      if(!empty($aDetailArr->detail_value)){
                      
                      $ans_value	=	'';
                      if(!empty($application_additional_detail)){
                        foreach($application_additional_detail as $aval){
                            if($aDetailArr->id == $aval->question_id){
                              $ans_value	=	@$aval->application_detail_value;	
                            }
                        }
                      }
						
                            // if($inc_first %3 == 0) {
                             
                            // }	
                            $html		.=	'<div class="col-md-4">
                                      <label class="form-label">'.strtoupper($aDetailArr->detail_value).':</label>
                                      <input type="hidden" name="job_question[]" value="'.$aDetailArr->id.'">
                                      <input type="text" class="form-control form-control-solid" name="additional_detail[]" placeholder="Enter detail" value="'.@$ans_value.'">
                                      <span class="text-muted form-text">Please enter your details</span>
                                    </div>';
                                    
                            // if(($inc_last %3 == 0)&& $inc_last > 0){
                              
                            // }
                          }
                            $inc_first++;
                            $inc_last++;
                            $customTitleId++;				
                          }
                          $html		.='</div>';
                          echo $html;	
                          }	
                                                    
                  @endphp
                </div>
                <!--end::Custom Details-->
              </div>
            </div>


            <!-- Availability -->
            <div class="fs">
              <div class="fsh">
                <h3>Availability for Next 3 Days
                  <span style="color: var(--r)">*</span>
                </h3>
              </div>
              <div class="fsc">
                <div class="ag">
                  <div class="ai">
                            <div class="ad">Date and Time(Slot-1)</div>
                             <div class="row">
                              <div class="col-4 pl-0">
                                <input class="form-control form-control-solid" placeholder="Date" name="interviewDateOne" id="interviewDateOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('d-m-Y') : '' }}" type="text" readonly="readonly">
                              </div>
                              <div class="col-3 pl-0">
                                <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeOne" id="interviewTimeOne" value="{{ (@$application->interviewDateOne) ? \Carbon\Carbon::parse(@$application->interviewDateOne)->format('h:i A'): '' }}" type="text" readonly="readonly">
                              </div>
                              <div class="col-5 pl-0">
                                <select name="interview_mode_one" id="interview_mode_one"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_one == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="ai">
                            <div class="ad">Date and Time(Slot-2)</div>
                            <div class="row">
                              <div class="col-4 pl-0">
                                <input class="form-control form-control-solid" placeholder="Date" name="interviewDateTwo" id="interviewDateTwo" value="{{ (@$application->interviewDateTwo)  ? \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('d-m-Y'): '' }}" type="text" readonly="readonly">
                              </div>
                              <div class="col-3 pl-0">
                                <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo) ?  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" readonly="readonly">
                              </div>
                              <div class="col-5 pl-0">
                                <select name="interview_mode_two" id="interview_mode_two"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_two == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="ai">
                              <div class="ad">Date and Time(Slot-3)</div>
                              <div class="row">
                                <div class="col-4 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Date" name="interviewDateThree" id="interviewDateThree" value="{{ (@$application->interviewDateThree) ?  \Carbon\Carbon::parse(@$application->interviewDateThree)->format('d-m-Y') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-3 pl-0">
                                  <input class="form-control form-control-solid" placeholder="Time" name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree) ?  \Carbon\Carbon::parse(@$application->interviewDateTwo)->format('h:i A') : '' }}" type="text" readonly="readonly">
                                </div>
                                <div class="col-5 pl-0">
                                  <select name="interview_mode_three" id="interview_mode_three"
                                      onchange="getInterviewModeChild(this.value, {{ $jobId }});"
                                      class="form-select form-select-solid">
                                      <option value="">Mode</option>
                                      @if (!empty(@$interview_mode))
                                        @foreach (@$interview_mode as $int_mode_val)
                                            
                                            <option value="{{ @$int_mode_val->name }}" {{ @$application->interview_mode_three == $int_mode_val->name ? 'selected' : '' }}>{{ @$int_mode_val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                  </select>
                                </div>
                              </div>
                          </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="fs">
              <div class="fsh">
                <h3>Additional Information</h3>
              </div>
              <div class="fsc">
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Likedin Link (Optional)</label>
                    <input type="url" id="linkedin" name="linkedin" value="{{ @$application->linkedin }}" placeholder="Enter Likedin Link" />
                  </div>
                  <div class="fg fv-row">
                    <label>GitHub Link (Optional)</label>
                    <input type="url" value="{{ @$application->github }}" id="github" name="github" placeholder="Enter GitHub Link" />
                  </div>
                  <div class="fg fv-row">
                    <label>Behance Link (Optional)</label>
                    <input type="url" id="behance" name="behance" value="{{ @$application->behance }}" placeholder="Enter Behance Link" />
                  </div>
                  <div class="fg fv-row">
                    <label>Other Link (Optional)</label>
                    <input type="url" value="{{ @$application->otherLink }}" id="otherLink" name="otherLink" placeholder="Enter URL" />
                  </div>
                </div>
                <div class="g4">
                  <div class="fg fv-row">
                    <label>Current Visa Status (For International)</label>
                    <select name="visastatu" id="visastatu">
                      <option value="">Select visa status</option>
                      <option {{ @$application->visastatu == 'H1B' ? 'selected' : '' }}>H1B</option>
                      <option {{ @$application->visastatu == 'L1' ? 'selected' : '' }}>L1</option>
                      <option {{ @$application->visastatu == 'F1' ? 'selected' : '' }}>F1</option>
                      <option {{ @$application->visastatu == 'Green Card' ? 'selected' : '' }}>Green Card</option>
                      <option {{ @$application->visastatu == 'Citizen' ? 'selected' : '' }}>Citizen</option>
                    </select>
                  </div>
                  <div class="fg fv-row">
                    <label>Reference Name (Optional)</label>
                    <input type="text" name="referencename" value="{{ @$application->referencename}}" id="referencename" placeholder="Enter reference name" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          </form>
        </main>

      </div>    

    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content--> 

@php
    $rates = $currencyConverter['usd_based_rates'] ?? [
        'USD' => 1,
        'INR' => 83,
        'EUR' => 0.87,
        'JPY' => 159.65,
        'GBP' => 0.76,
        'AED' => 3.67
    ];
    
    $currencyKeys = $currencyConverter['getKey'] ?? [
        'INR',
        'EUR',
        'JPY',
        'GBP',
        'USD',
        'AED'
    ];

@endphp

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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@if(config('resumes.features.career_controller'))
<script src="{{ asset('js/resume-presign.js') }}"></script>
@endif
@include('service_company.job-applications.jero-client-parser')

<script>
$(document).ready(function () {

    // Authoritative Edit/Add state directly from Laravel route/view model
    var IS_EDIT_MODE = {{ $page_event === 'EDIT' ? 'true' : 'false' }};
    console.log('[PARSER] IS_EDIT_MODE authoritatively set to:', IS_EDIT_MODE);

    // ── v2 upload: career_controller flag (public presign endpoint required) ─
    // Uses a separate feature flag so it can be enabled independently from the
    // service-company browser_upload flag once a public presign route exists.
    var RESUMES_FEATURE_BROWSER_UPLOAD = @json(config('resumes.features.career_controller', false));
    var RESUME_PRESIGN_URL = @json(config('resumes.features.career_controller') ? url('/career/resume/v2/sessions') : '');
    if (window.ResumePresign && RESUMES_FEATURE_BROWSER_UPLOAD) {
        window.RESUME_PRESIGN_URL = RESUME_PRESIGN_URL;
        window.RESUMES_FEATURE_BROWSER_UPLOAD = RESUMES_FEATURE_BROWSER_UPLOAD;
        window.RESUMES_CONFIG = {
            maxPutAttempts: @json(config('resumes.network_resilience.max_put_attempts', 3)),
            putBackoffMs:   @json(config('resumes.network_resilience.put_backoff_ms', 1000)),
        };
    }

    // ── v2 state → existing progress bar UI bridge ───────────────────────────
    if (RESUMES_FEATURE_BROWSER_UPLOAD && window.ResumePresign) {
        window.ResumePresign.onStateChange(function (data) {
            if (data.state === 'uploading') {
                $('#upWrap').show();
                $('#upBar').css('width', data.progress + '%');
                $('#upStats').html(data.progress + '% &middot; Uploading to cloud&hellip;');
            } else if (data.state === 'uploaded') {
                var activeMethod = data.method || 'Method A';
                $('#resume_upload_method').val(activeMethod);
                $('#upBar').css('width', '100%');
                $('#upStats').html('100% &middot; Upload complete');
                $('#upPill').css('display', 'inline-flex');
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
                    + ' stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    + '<span><strong>Upload complete!</strong> Resume uploaded successfully using ' + activeMethod + '</span>'
                ).show();
                $('#uploaded_file_path').val('');
                $('#resume_original_name').val('');
                var ri = document.getElementById('resume'); if (ri) ri.value = ''; // prevent multipart file on submit
            } else if (data.state === 'failed') {
                if (typeof _showUploadError === 'function') {
                    _showUploadError(data.error || 'Upload failed. Please remove and try again.');
                }
            }
        });
    }

    /**
     * Intent: Clean resume state initialization for public career applications.
     * Starts fresh on every page load/refresh so candidates start with an empty resume field.
     */
    if (RESUMES_FEATURE_BROWSER_UPLOAD && window.ResumePresign) {
        window.ResumePresign.cancelUpload();
        $('#resume_session_uuid').val('');
        $('#uploaded_file_path').val('');
        $('#resume_original_name').val('');
        $('#resumeCheck').val('');
        $('#uploadBox').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        var ri = document.getElementById('resume'); if (ri) ri.value = '';
    }
    // ─────────────────────────────────────────────────────────────────────────

    const uploadBox  = document.getElementById('uploadBox');
    const resumeInput = document.getElementById('resume');

    // ─── Zero-temp upload pipeline ───────────────────────────────────────────
    // xhr.send(file) → raw bytes → Laravel php://input → streamed to R2
    // No PHP upload_tmp_dir write. No CORS needed.
    // ─────────────────────────────────────────────────────────────────────────

    let upXhr = null;

    function fmtKB(b) { return (b / 1024).toFixed(1) + ' KB'; }
    function fmtSize(b) { return b >= 1048576 ? (b/1048576).toFixed(1)+' MB' : Math.round(b/1024)+' KB'; }
    function extLabel(file) { var e=(file.name.split('.').pop()||'').toUpperCase(); return ['PDF','DOC','DOCX'].includes(e)?e:'PDF'; }

    function resetProgress() {
        if (upXhr) { upXhr.abort(); upXhr = null; }
        window.__upXhrChannelClaimed = false;
        $('#upWrap').hide();
        $('#upBar').css('width', '0%');
        $('#upStats').text('0% &middot; 0 KB / 0 KB');
        $('#upPill').hide();
        $('#upBanner').hide().removeClass('ok err').empty();
    }

    function backToDropzone() {
        if (window.JeroClientParser && typeof window.JeroClientParser.cancelActiveParse === 'function') {
            window.JeroClientParser.cancelActiveParse();
        }
        // Cancel v2 upload if in progress (aborts R2 XHR + clears session UUID)
        if (RESUMES_FEATURE_BROWSER_UPLOAD && window.ResumePresign) {
            window.ResumePresign.cancelUpload();
        }
        resetProgress();
        $('#upInfo').removeClass('show');
        $('#uploadBox').show();
        $('#fileInfo').hide();
        $('#fileName').text('');
        $('#resume').val('');
        $('#resumeCheck').val('');
        $('#resume_upload_method').val('');
        $('#uploaded_file_path').val('');
        $('#resume_original_name').val('');
        $('.download-file').addClass('d-none');
    }

    function showUploadedCard(file) {
        var ext = extLabel(file);
        $('#upInfo .up-file-icon').text(ext);
        $('#upFileName').text(file.name);
        $('#upFileSub').text(fmtSize(file.size) + ' &middot; ' + ext);
        var now = new Date();
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var tz = '';
        var tzMatch = now.toTimeString().match(/\(([A-Za-z\s]+)\)$/);
        if (tzMatch && tzMatch[1]) {
            var tzWords = tzMatch[1].trim().split(/\s+/);
            tz = tzWords.length > 1 ? tzWords.map(function (w) { return w[0]; }).join('') : tzMatch[1];
        }
        if (!tz) {
            try {
                var tzParts = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short' }).formatToParts(now);
                var tzFound = tzParts.find(function (p) { return p.type === 'timeZoneName'; });
                if (tzFound && tzFound.value) tz = tzFound.value;
            } catch (e) {}
        }
        var tzSuffix = tz ? (' ' + tz) : '';
        $('#upDetailUploadedOn').text(
            String(now.getDate()).padStart(2,'0')+' '+months[now.getMonth()]+' '+now.getFullYear()+
            ', '+String(now.getHours()).padStart(2,'0')+':'+String(now.getMinutes()).padStart(2,'0') + tzSuffix
        );
        $('#upDetailParserStatus').text('Ready ✓').css('color','');
        $('#uploadBox').hide();
        $('#fileInfo').hide();
        $('#upInfo').addClass('show');
    }

    function _showUploadError(msg) {
        $('#upBanner').removeClass('ok').addClass('err').html(
            '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            +'<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'
            +'</svg><span><strong>Upload failed:</strong> '+msg+'</span>'
        ).show();
        upXhr = null;
    }

    function uploadWithProgress(file) {
        window.__upXhrChannelClaimed = true;
        resetProgress();
        window.__upXhrChannelClaimed = true;

        $('#upWrap').show();
        $('#upStats').text('0% &middot; ' + fmtKB(0) + ' / ' + fmtKB(file.size));

        var mimeMap = { pdf:'application/pdf', doc:'application/msword', docx:'application/vnd.openxmlformats-officedocument.wordprocessingml.document' };
        var ext         = (file.name.split('.').pop()||'').toLowerCase();
        var contentType = mimeMap[ext] || file.type || 'application/octet-stream';

        upXhr = new XMLHttpRequest();
        upXhr.open('POST', '{{ route("get.uploadresume") }}', true);
        upXhr.setRequestHeader('Content-Type', contentType);
        upXhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        upXhr.setRequestHeader('X-File-Name', encodeURIComponent(file.name));
        upXhr.setRequestHeader('X-File-Size', file.size);

        upXhr.upload.onprogress = function (e) {
            if (!e.lengthComputable) return;
            var pct = Math.round(e.loaded * 100 / e.total);
            $('#upBar').css('width', pct + '%');
            $('#upStats').text(pct + '% &middot; ' + fmtKB(e.loaded) + ' / ' + fmtKB(file.size));
        };

        upXhr.onload = function () {
            var r = null;
            try { r = JSON.parse(upXhr.responseText); } catch (_) {}
            upXhr = null;

            if (this.status >= 200 && this.status < 300 && r && r.success) {
                $('#resume_upload_method').val('Method B');
                $('#upBar').css('width', '100%');
                $('#upStats').text('100% &middot; ' + fmtKB(file.size) + ' / ' + fmtKB(file.size));
                $('#upPill').css('display', 'inline-flex');
                $('#upBanner').removeClass('err').addClass('ok').html(
                    '<svg class="up-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    +'<circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 16 9"/></svg>'
                    +'<span><strong>Upload complete!</strong> Resume uploaded successfully using Method B</span>'
                ).show();
                $('#uploaded_file_path').val(r.file_path || '');
                $('#resume_original_name').val(r.original_name || file.name || '');
                $('#resumeCheck').val('1');
                showUploadedCard(file);
            } else {
                _showUploadError((r && r.message) ? r.message : 'Upload failed. Try again.');
            }
        };

        upXhr.onerror = function () {
            _showUploadError('Network error during upload. Click Remove and try again.');
        };

        upXhr.send(file); // raw bytes → php://input → R2, zero local temp
    }

    // ==========================
    // Common file handler
    // ==========================
    function processFile(file, event) {

        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        if ($.inArray(ext, ['pdf', 'doc', 'docx']) === -1) {
            Swal.fire({
                title: 'Invalid File Format',
                text: 'Only PDF, DOC, and DOCX files are allowed.',
                icon: 'warning',
                confirmButtonText: 'Ok, got it!',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            backToDropzone();
            return;
        }

        const sizeMB = file.size / (1024 * 1024);

        if (sizeMB > 5) {
            Swal.fire({
                title: 'File Too Large',
                text: 'Maximum file size allowed is 5 MB.',
                icon: 'warning',
                confirmButtonText: 'Ok, got it!',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            backToDropzone();
            return;
        }

        const sizeStr = sizeMB >= 1 ? sizeMB.toFixed(2) + ' MB' : Math.round(file.size / 1024) + ' KB';
        $('#fileName').text(file.name + ' (' + sizeStr + ')');
        $('#fileInfo').css('display', 'flex');
        $('#resumeCheck').val('1');
        $('.download-file').addClass('d-none');

        // Capture selected File immediately; decouple storage upload from parsing path
        const selectedFile = file;
        window.__upXhrChannelClaimed = true;
        window._currentActiveResumeFile = selectedFile;
        window._clientParserState = 'parsing';

        if (typeof showUploadedCard === 'function') {
            showUploadedCard(selectedFile);
        }

        $('#upDetailParserStatus').html(
            '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
            '<span>Parsing in browser&hellip;</span>'
        );

        // ── Phase 1-5: 100% Client-Side Resume Parsing via https://parser.jero.works/ ──────────
        // Zero PHP/Laravel execution, zero MySQL/ES strain, zero R2 during parsing, runs 100% in Recruiter Browser RAM
        if (window.JeroClientParser && typeof window.JeroClientParser.parseAndPopulate === 'function') {
            var reqSkills = [];
            if (typeof candidateRequiredSkills !== 'undefined' && Array.isArray(candidateRequiredSkills)) {
                reqSkills = candidateRequiredSkills.map(function (s) { return s.name; });
            }

            window.JeroClientParser.parseAndPopulate(selectedFile, {
                isEditMode: typeof IS_EDIT_MODE !== 'undefined' ? IS_EDIT_MODE : false,
                requiredSkills: reqSkills,
                onProgress: function (stage, pct) {
                    $('#upDetailParserStatus').html(
                        '<span class="spinner-border spinner-border-sm text-primary me-1" role="status" style="width: 12px; height: 12px; border-width: 2px;"></span>' +
                        '<span>' + $('<div>').text(stage).html() + ' (' + pct + '%)</span>'
                    );
                },
                onSuccess: function (result, totalMs) {
                    window._clientParserState = 'completed';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #41c08e; font-weight: 600;">✓ Parsed in ' + totalMs + 'ms</span>'
                    );
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Resume parsed in browser and fields populated!');
                    }
                },
                onError: function (err) {
                    window._clientParserState = 'error';
                    $('#upDetailParserStatus').html(
                        '<span style="color: #f59e0b; font-weight: 500;">Manual Entry</span>'
                    );
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Resume parsing unavailable. Please enter candidate details manually.');
                    }
                }
            });
        }

        // ── PIPELINE 2: Independent Background Storage Upload ──────────
        if (RESUMES_FEATURE_BROWSER_UPLOAD && window.ResumePresign) {
            // v2 path: direct browser → R2, zero PHP bytes.
            window.ResumePresign.startUpload(selectedFile).catch(function () {
                // error already surfaced via onStateChange 'failed'
            });
        } else if (typeof uploadWithProgress === 'function') {
            // v1 path: PHP multipart POST fallback
            uploadWithProgress(selectedFile);
        }

        // Reset so the legacy channel is open for the next pick.
        window.__upXhrChannelClaimed = false;
    }

    // Replace / Remove inside info card
    $(document).on('click', '#upReplaceBtn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        backToDropzone();
        resumeInput.click();
    });

    $(document).on('click', '#upRemoveBtn', function (e) {
        e.preventDefault();
        backToDropzone();
    });

    // Direct binding so stopPropagation() fires BEFORE the click reaches
    // #uploadBox — prevents resumeInput.click() from being called twice.
    document.getElementById('chooseFileBtn').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        resumeInput.click();
    });

    // Remove file strip button
    $(document).on('click', '#removeFile', function (e) {
        e.preventDefault();
        e.stopPropagation();
        backToDropzone();
    });

    // File input change
    $('#resume').on('change', function (e) {
        if (this.files.length > 0) {
            processFile(this.files[0], e);
        } else {
            $('#fileInfo').hide();
            $('#resumeCheck').val('');
            $('.download-file').addClass('d-none');
        }
    });

    // Click upload box
    $('#uploadBox').on('click', function (e) {
        if (
            $(e.target).closest('#chooseFileBtn').length ||
            $(e.target).closest('#removeFile').length ||
            $(e.target).closest('.download-file').length
        ) {
            return;
        }
        resumeInput.click();
    });

    // Paste
    document.addEventListener('paste', function (e) {
        const files = e.clipboardData?.files;
        if (files && files.length) {
            const file = files[0];
            const dt = new DataTransfer();
            dt.items.add(file);
            resumeInput.files = dt.files;
            // processFile handles upload — no synthetic change dispatch (would double-fire)
            processFile(file);
        }
    });

    // Drag & Drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadBox.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    uploadBox.addEventListener('dragenter', function () { this.classList.add('dragging'); });
    uploadBox.addEventListener('dragover',  function () { this.classList.add('dragging'); });
    uploadBox.addEventListener('dragleave', function () { this.classList.remove('dragging'); });

    uploadBox.addEventListener('drop', function (e) {
        this.classList.remove('dragging');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            const dt = new DataTransfer();
            dt.items.add(file);
            resumeInput.files = dt.files;
            // processFile handles upload — no synthetic change dispatch (would double-fire)
            processFile(file);
        }
    });

});
</script>


<script>

        // stepper
        document.querySelectorAll('.stp-s').forEach((s) => {
            s.addEventListener('click', () => {
                document
                    .querySelectorAll('.stp-s')
                    .forEach((x) => x.classList.remove('a'));
                s.classList.add('a');
            });
        });
        // char counters
        document.querySelectorAll('textarea').forEach((t) => {
            const c = t.parentElement.querySelector('.cc');
            if (c) {
                const m = c.textContent.includes('1000') ? 1000 : 500;
                t.addEventListener('input', () => {
                    c.textContent = t.value.length + ' / ' + m;
                });
            }
        });
        // star ratings
        document.querySelectorAll('.sr').forEach((r) => {
            const stars = r.querySelectorAll('.st');
            stars.forEach((s, i) => {
                s.addEventListener('click', () => {
                    stars.forEach((x, j) => {
                        if (j <= i) x.classList.remove('e');
                        else x.classList.add('e');
                    });
                });
            });
        });
        // add skill
        document.querySelector('.asb').addEventListener('click', () => {
            const tb = document.querySelector('.sk tbody');
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td><input type="text" placeholder="Enter skill"></td><td><input type="number" value="0" style="width:80px"></td><td><input type="number" value="0" style="width:80px"></td><td><select style="width:140px"><option>Currently</option><option>Within 6 Months</option><option>Within 1 Year</option><option>Within 2 Years</option><option>More than 2 Years</option></select></td><td><div class="sr"><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span><span class="st e">★</span></div></td><td><button class="ab" type="button">🗑️</button></td>';
            tb.appendChild(tr);
            tr.querySelectorAll('.sr').forEach((r) => {
                const s = r.querySelectorAll('.st');
                s.forEach((x, i) => {
                    x.addEventListener('click', () => {
                        s.forEach((y, j) => {
                            if (j <= i) y.classList.remove('e');
                            else y.classList.add('e');
                        });
                    });
                });
            });
            tr.querySelector('.ab').addEventListener('click', () => tr.remove());
        });
        // delete existing
        document.querySelectorAll('.ab').forEach((b) => {
            b.addEventListener('click', () => b.closest('tr').remove());
        });
</script>


<script>

  $(document).ready(function (){
   

        const HOURS_PER_MONTH = 160;
        const MONTHS_PER_YEAR = 12;
        const currencyRates = @json($rates);
        const currencyRateschar = @json($currencyKeys);

        let isUpdating = false;

        // Base values (USD)
        let payBase = { hourly: 0 };
        let billBase = { hourly: 0 };

        function format(val) {
            return val ? parseFloat(val).toFixed(2) : '';
        }
        
        function fromUSD(amount, currency) {
            return amount / currencyRates[currency];
        }

   
        // ================= PAY RATE =================
        $('#ctc').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let hourly = parseFloat($(this).val()) || 0;

            $('#pay_monthly').val(format(hourly * HOURS_PER_MONTH));
            $('#pay_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
            updatePayUI();
            percentage_hike();

            isUpdating = false;
        });

        $('#pay_monthly').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let monthly = parseFloat($(this).val()) || 0;

            let hourly = monthly / HOURS_PER_MONTH;

            $('#ctc').val(format(hourly));
            $('#pay_annual').val(format(monthly * MONTHS_PER_YEAR));
            updatePayUI();
            percentage_hike();

            isUpdating = false;
        });

        $('#pay_annual').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let annual = parseFloat($(this).val()) || 0;

            let monthly = annual / MONTHS_PER_YEAR;
            let hourly = monthly / HOURS_PER_MONTH;

            $('#ctc').val(format(hourly));
            $('#pay_monthly').val(format(monthly));
            updatePayUI();
            percentage_hike();

            isUpdating = false;
        });

        // ================= BILL RATE =================
        $('#ectc').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let hourly = parseFloat($(this).val()) || 0;

            $('#bill_monthly').val(format(hourly * HOURS_PER_MONTH));
            $('#bill_annual').val(format(hourly * HOURS_PER_MONTH * MONTHS_PER_YEAR));
            updateBillUI();
            percentage_hike();
            isUpdating = false;
        });

        $('#bill_monthly').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let monthly = parseFloat($(this).val()) || 0;

            let hourly = monthly / HOURS_PER_MONTH;

            $('#ectc').val(format(hourly));
            $('#bill_annual').val(format(monthly * MONTHS_PER_YEAR));
            updateBillUI();
            percentage_hike();

            isUpdating = false;
        });

        $('#bill_annual').on('input', function () {
            if (isUpdating) return;
            isUpdating = true;

            let annual = parseFloat($(this).val()) || 0;

            let monthly = annual / MONTHS_PER_YEAR;
            let hourly = monthly / HOURS_PER_MONTH;

            $('#ectc').val(format(hourly));
            $('#bill_monthly').val(format(monthly));
            updateBillUI();
            percentage_hike();

            isUpdating = false;
        });
        function updatePayUI() {
            let currency = $('#current_ctc_cur').val();
            let pay_rate_amount = $('#ctc').val();
            let currSet = currencyRateschar[currency];
            if(currency==4){ 
              $('#pay_rate_hourly_usd').text('');
              $('#pay_rate_monthly_usd').text('');
              $('#pay_rate_annual_usd').text('');
              updateCTCFields();
              return false;
            }

            let hourly = fromUSD(pay_rate_amount, currSet);
            let monthly = hourly * HOURS_PER_MONTH;
            let annual = monthly * MONTHS_PER_YEAR;
            
            $('#pay_rate_hourly_usd').text('Approx $ '+ format(hourly));
            $('#pay_rate_monthly_usd').text('Approx $ '+ format(monthly));
            $('#pay_rate_annual_usd').text('Approx $ '+ format(annual));
            updateCTCFields();
        }
        function updateBillUI() {
            let currency = $('#current_ctc_cur').val();
            let bill_rate_amount = $('#ectc').val();
            let currSet = currencyRateschar[currency];
            if(currency==4){ 
              $('#bill_rate_hourly_usd').text('');
              $('#bill_rate_monthly_usd').text('');
              $('#bill_rate_annual_usd').text('');
              updateCTCFields();
              return false;
            }

            let hourly = fromUSD(bill_rate_amount, currSet);
            let monthly = hourly * HOURS_PER_MONTH;
            let annual = monthly * MONTHS_PER_YEAR;

            $('#bill_rate_hourly_usd').text('Approx $ '+ format(hourly));
            $('#bill_rate_monthly_usd').text('Approx $ '+ format(monthly));
            $('#bill_rate_annual_usd').text('Approx $ '+ format(annual));
            updateCTCFields();
        }



        function payRateUpdate(){

          if (isUpdating) return;
            isUpdating = true;
           

            updatePayUI();

            isUpdating = false;
          
        }

        function billRateUpdate(){

          if (isUpdating) return;
            isUpdating = true;
          
            updateBillUI();

            isUpdating = false;
          
        }


        $('#current_ctc_cur').on('change', function () {
            updatePayUI();
            updateBillUI();
        });
        $('#current_ectc_cur').on('change', function () {
            updateBillUI();
            updatePayUI();
        });
        setTimeout(() => {
              billRateUpdate();
              payRateUpdate();
          }, 1000);


  });

  function fresherrbutton(cvalue)
   {
       if(cvalue == 0){
            $('.disablesection').removeClass('disablesectionOn');
       }else if(cvalue==1){
           $('.disablesection').addClass('disablesectionOn');
       }
   }

   const inputs = document.querySelectorAll("#ctc, #ectc, #phone, #secondary_mobile");

    inputs.forEach(input => {
        // Disable arrow key increment/decrement
        input.addEventListener("keydown", function (e) {
            if (e.key === "ArrowUp" || e.key === "ArrowDown") {
                e.preventDefault();
            }
        });

        // Disable scroll wheel changing the value
        input.addEventListener("wheel", function (e) {
            e.preventDefault();
        }, { passive: false });
    });


setTimeout(() => {
          
          var defaultTimeFormat = '{{$defaultTimeFormat}}';
         
           // For all time pickers
          function initTimePicker(selector, format) {
                return $(selector).flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: format,   // "h:i K" or "H:i"
                    time_24hr: format === "H:i",
                    minuteIncrement: 1,
                    allowInput: true, // allow user to delete value
                    onChange: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length > 0) {
                            let d = selectedDates[0];
                            if (d.getHours() === 0 && d.getMinutes() === 0) {
                                instance.clear(); // auto clear if 00:00
                            }
                        }
                    }
                });
            }

            // Example init
          let timeOnePicker   = initTimePicker("#interviewTimeOne",  '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeTwoPicker   = initTimePicker("#interviewTimeTwo",  '{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');
          let timeThreePicker = initTimePicker("#interviewTimeThree",'{{$defaultTimeFormat==1 ? "h:i K" : "H:i"}}');

          function parseDMY(dmy) {
              if (!dmy) return null;
              let parts = dmy.split("-");
              return new Date(parts[2], parts[1] - 1, parts[0]); // Y, M-1, D
          }

          function initInterviewDatePicker(selector, offsetDays) {
              let existingDate = $(selector).val();
              let savedDateObj = parseDMY(existingDate);

              $(selector).flatpickr({
                  dateFormat: "d-m-Y",
                  altInput: true,
                  altFormat: "d-M-Y",
                  defaultDate: savedDateObj || getFutureDate(offsetDays),
                  minDate: savedDateObj ? null : "today", // block past unless saved
                  disable: savedDateObj
                      ? [
                          function (date) {
                              let today = new Date();
                              today.setHours(0,0,0,0);
                              let sameAsSaved = date.getTime() === savedDateObj.getTime();
                              return date < today && !sameAsSaved;
                          }
                        ]
                      : [],
                  onReady: function(selectedDates, dateStr, instance) {
                      instance.altInput.style.width = "129px";
                  }
              });
          }

          function getFutureDate(days) {
              const date = new Date();
              date.setDate(date.getDate() + days);
              return date;
          }

          initInterviewDatePicker("#interviewDateOne", 0);
          initInterviewDatePicker("#interviewDateTwo", 1);
          initInterviewDatePicker("#interviewDateThree", 2);



          function clearAll() {
            timeOnePicker.clear();
            timeTwoPicker.clear();
            timeThreePicker.clear();

            dateOnePicker.clear();
            dateTwoPicker.clear();
            dateThreePicker.clear();
        }
        
          
}, 500);


    let linkedinRegex = /^(https?:\/\/)?(www\.)?linkedin\.com\/.*$/i;
    let githubRegex   = /^(https?:\/\/)?(www\.)?github\.com\/[A-Za-z0-9_-]+\/?$/i;
    let behanceRegex  = /^(https?:\/\/)?(www\.)?behance\.net\/[A-Za-z0-9_-]+\/?$/i;

  function validateLink(input, regex, messageId, platform) {
          let value = $(input).val().trim();
          if (value === "") {
              $(messageId).html(""); // Clear if empty
              return;
          }
          if (regex.test(value)) {
              $(messageId).html("✅ Valid " + platform).css("color", "green");
          } else {
              $(messageId).html("❌ Invalid " + platform).css("color", "red");
          }
      }


    $("#linkedin").on("change", function () {
        validateLink(this, linkedinRegex, "#linkedin-msg", "LinkedIn");
    });

    $("#github").on("change", function () {
        validateLink(this, githubRegex, "#github-msg", "GitHub");
    });

    $("#behance").on("change", function () {
        validateLink(this, behanceRegex, "#behance-msg", "Behance");
    });


  localStorage.setItem('currentPage', "{{ @$currentPage }}");
  
var inputloc = document.querySelector("#kt_tagify_work_location");
var cities = @json($cities);


    $(document).ready(function () {
      // Initialize intl-tel-input for Primary and Secondary phone fields
      const primaryPhoneInput = window.intlTelInput(document.querySelector("#phone"), {
        separateDialCode: true,
        initialCountry: "{{ $phone_code }}",
        preferredCountries: ["in", "us", "gb", "ca", "au"],
      });

      const secondaryPhoneInput = window.intlTelInput(document.querySelector("#secondary_mobile"), {
        separateDialCode: true,
        initialCountry: "{{ $second_phone_code }}",
        preferredCountries: ["in", "us", "gb", "ca", "au"],
      });

        // Function to update hidden country code inputs
      function updateCountryCode(inputField, hiddenField) {
        const selectedCountryData = inputField.getSelectedCountryData();
        //$(hiddenField).val(selectedCountryData.dialCode); // Set the country code in the hidden field
        const countryCode = selectedCountryData.iso2; // e.g., 'us', 'in', etc.
        $(hiddenField).val(countryCode);

      }
      // Update country code on input or country flag selection
      $("#phone").on("input change", function () {
        updateCountryCode(primaryPhoneInput, "#primary_country_code");
      });

      $("#secondary_mobile").on("input change", function () {
        updateCountryCode(secondaryPhoneInput, "#secondary_country_code");
      });
      // Update country code dynamically when the user selects a different country flag
      $(".iti__flag-container").on("click", function (event) {
        const targetInput = $(event.target).closest(".iti").find("input");

        if (targetInput.is("#phone")) {
          updateCountryCode(primaryPhoneInput, "#primary_country_code");
        } else if (targetInput.is("#secondary_mobile")) {
          updateCountryCode(secondaryPhoneInput, "#secondary_country_code");
        }
      });

      setTimeout(function () {	
        $(".iti__flag-container").click();
      },500);

    });

  
</script>

<script>

	var input2 = document.querySelector("#kt_tagify_2");
	// new Tagify(input2);

  var tagifyskill =  new Tagify(input2, {
        whitelist: [''],
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions 
            classname: "", // <- custom classname for this dropdown, so it could be targeted tagify__inline__suggestions
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
        },
        maxTags: 10,
        transformTag: function(tagData) {
        // Remove special characters, allow only letters, numbers, and spaces
        tagData.value = tagData.value.replace(/[^a-zA-Z0-9 +#\/.]/g, '');
        return tagData;
        },
        validate: function(tagData) {
            // Prevent empty tags after cleaning
            return tagData.value.trim().length > 0 ? true : false;
        }
    });

     const fetchSuggestionsSkills = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/skill-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Skill: ", data);

                if (Array.isArray(data)) {
                    tagifyskill.settings.whitelist = data; // Update whitelist
                    // tagifyskill.dropdown.show(query); // Show suggestions
                    tagifyskill.dropdown.show.call(tagifyskill);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching Skill:", err);
            }
        });
    }, 5); 

    // Listen for user input to trigger API call
    tagifyskill.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 1) return;
        fetchSuggestionsSkills(query);
    });


 
                             
    // Initialize Tagify script on the above inputs
    var tagifylocation =  new Tagify(inputloc, {
        whitelist: cities,
        enforceWhitelist: true, // Prevents users from adding custom values
        maxTags: 1,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions 
            classname: "", // <- custom classname for this dropdown, so it could be targeted tagify__inline__suggestions
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: true    // <- do not hide the suggestions dropdown once an item has been selected
        }
    });

    // Prevent creating tags on blur or enter key
    tagifylocation.on('input', function(e) {
      tagifylocation.dropdown.show(); // show the dropdown immediately
    });

    // Create tags only on select
    tagifylocation.on('dropdown:select', function(e) {
      tagifylocation.addTags([e.detail.data.value]);
    });
    tagifylocation.on('add', function(e) {
      // alert(cities);
    // Remove the tag if it wasn't selected from the dropdown
    // if (!cities.includes(e.detail.data.value)) {
    //   setTimeout(() => {
    //     tagifylocation.removeTag(e.detail.data.value);
    //   }, 200);
       
    // }
   });

   function debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

     // Debounced function to fetch suggestions
    const fetchSuggestions = debounce(function(query) {
        $.ajax({
            url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
            method: 'GET',
            success: function(data) {
                console.warn("Fetched Cities: ", data);

                if (Array.isArray(data)) {
                    tagifylocation.settings.whitelist = data; // Update whitelist
                    // tagifylocation.dropdown.show(query); // Show suggestions
                    tagifylocation.dropdown.show.call(tagifylocation);
                } else {
                    console.error("Invalid API response format: Expected array.");
                }
            },
            error: function(err) {
                console.error("Error fetching cities:", err);
            }
        });
    }, 300); // 300ms delay

    // Listen for user input to trigger API call
    tagifylocation.on('input', function(e) {
        var query = e.detail.value.trim();
        if (query.length < 2) return;
        fetchSuggestions(query);
    });

    setTimeout(() => {
      var city = "{{@$default_loc}}";
      if(city !=''){
        var cityName = city.split(" | ")[0]; // Get the first part (Ballia)
        fetchSuggestions(cityName);
      }
    }, 100);
    setTimeout(() => {
        var defaultLocation = {!! json_encode(@$default_loc) !!}; // Ensure proper Blade syntax for JavaScript
        if (defaultLocation) {
            tagifylocation.addTags([defaultLocation]); // Add the default location as a tag
            tagifylocation.dropdown.hide();
        }
    }, 2000); // 3 seconds delay


  




   $(document).on('change','#serving_notice_period',function(){
    var cvalue = $('#serving_notice_period').val();
		$('#np_id').val('');
		$('#ddl_np_id').val('');
		if(cvalue == 0){
			$('#ddl_np_id').show();
			$('#np_id').hide();		
      $('#snp-immediate').hide();		
		}else if(cvalue==1){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$('#np_id').attr("disabled", false);	
			$("#np_id").attr('type','date');
      $('#text_display').val('Serving Notice Period');
			$("#np_id").css({ '-webkit-calendar-picker-indicator':'display(none)' });	
      $("#snp-immediate").attr('style','display:flex');	
      dateManageFunc();
		}else if(cvalue ==2){
      $('#snp-immediate').show();
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			// $("#np_id").attr('type','text');
			$('#np_id').attr("disabled", false);
      $("#np_id").attr('type','date');
			$('#text_display').val('Immediate');
      $("#snp-immediate").attr('style','display:flex');	
      dateManageFunc();
		}	
    })
   
   $(document).on('change','#np_id',function(){
        var datess = $(this).val();
        if (datess) {
        var dateObj = new Date(datess);
        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        
        var formattedDate = 
            ('0' + dateObj.getDate()).slice(-2) + '/' + 
            months[dateObj.getMonth()] + '/' + 
            dateObj.getFullYear(); // Convert to DD/Mon/YYYY

        document.getElementById('np_id_display').value = formattedDate;
    }
        // document.getElementById('np_id_display').value = datess;
   });
  
         
         

  function dateManageFunc(){
    // var radios = document.getElementsByName('serving_notice_period');
    var radios = $('#serving_notice_period').val();
         var today = new Date().toISOString().split('T')[0];
          // Loop through each radio button and check which one is selected
          for (var i = 0; i < radios.length; i++) {
              if (radios==1 || radios==2) {
                if (radios == 1) {
                    // Future dates allowed
                    $('#np_id').removeAttr('max'); // Remove max restriction
                    $('#np_id').attr('min', today); // Set min to today
                    $('#np_id').attr('placeholder', 'Select your last working date (future dates allowed)'); // Set placeholder
                } else {
                    // Past dates allowed
                    $('#np_id').removeAttr('min'); // Remove min restriction
                    $('#np_id').attr('max', today); // Set max to today
                    $('#np_id').attr('placeholder', 'Select your last working date (past  dates allowed)'); // Set placeholder
                }
                  break; // Stop the loop once the selected radio button is found
              }
          }
  }
  dateManageFunc();
  




   $('body').on('change', '#current_ctc_cur', function(){
   			var	current_ctc_cur		=	$(this).val();
			document.getElementById("current_ectc_cur").selectedIndex = current_ctc_cur;
   });
   $('body').on('change', '#current_ectc_cur', function(){
   			var	current_ectc_cur	=	$(this).val();
			document.getElementById("current_ctc_cur").selectedIndex = current_ectc_cur;
   });
   $('body').on('change', '#current_ctc_mode', function(){
   			var	current_ctc_mode	=	$(this).val();
			document.getElementById("current_ectc_mode").selectedIndex = current_ctc_mode;
   });
   $('body').on('change', '#current_ectc_mode', function(){
   			var	current_ectc_mode	=	$(this).val();
			document.getElementById("current_ctc_mode").selectedIndex = current_ectc_mode;
   });
  
      setTimeout(() => {

        $('#state option:eq({{ @$state }})').prop('selected', true);
       
      }, 1000);
	
	$(document).on('change', '.change_state', function() { 
   				
				  var country				=	$('#country').val(); 
   				var state_id			=	this.value;				
   								
				if(country == 101){
					$('#city_id').show();	
					$('#city').hide();	
				}else{
					$('#city').hide();	
					$('#city_id').show();	
				}
								
				var url 			=	"{{ route('service_company.clients.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {		
																	$('#city_id').html(response.data);
															   }		
									   
				});	
   								
   	});

    function numberToIndianWords(num) {

    num = Math.floor(Number(num));

    if (!num || isNaN(num)) return '';

    const ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six',
        'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen'
    ];

    const tens = [
        '', '', 'Twenty', 'Thirty', 'Forty',
        'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    function convert(n) {

        if (n < 20) {
            return ones[n];
        }

        if (n < 100) {
            return tens[Math.floor(n / 10)] + ' ' + ones[n % 10];
        }

        if (n < 1000) {
            return ones[Math.floor(n / 100)] + ' Hundred ' + convert(n % 100);
        }

        if (n < 100000) {
            return convert(Math.floor(n / 1000)) + ' Thousand ' + convert(n % 1000);
        }

        if (n < 10000000) {
            return convert(Math.floor(n / 100000)) + ' Lakh ' + convert(n % 100000);
        }

        if (n < 1000000000) {
            return convert(Math.floor(n / 10000000)) + ' Crore ' + convert(n % 10000000);
        }

        return convert(Math.floor(n / 1000000000)) + ' Arab ' + convert(n % 1000000000);
    }

    return convert(num).replace(/\s+/g, ' ').trim() + ' Rupees';
}


    function updateCTCFields() {

              let maxLimit = 99999999999;

                // Current CTC
                var ctcVal = parseInt($("#ctc").val(), 10);
                var payMonthly = parseInt($("#pay_monthly").val(), 10);
                var payAnnual = parseInt($("#pay_annual").val(), 10);
                var ctcCurrency = $(".currency").val();

                if (Number(payAnnual) > maxLimit) {
                    Swal.fire("Warning", "Value above Arab is not allowed", "warning");
                    $('#pay_annual').val(maxLimit);
                    $(".payrateannual").text(numberToIndianWords(maxLimit));
                    return false;
                }

                if (!isNaN(payAnnual)) {
                    var ctcWords = window.numberToWords(ctcVal, ctcCurrency);
                    var payMonthlyWords = window.numberToWords(payMonthly, ctcCurrency);
                    var payAnnualWords = window.numberToWords(payAnnual, ctcCurrency);
                    
                    
                    $(".payrateannual").text(numberToIndianWords(payAnnual));
                    $(".payratemonthly").text(numberToIndianWords(payMonthly));
                    $(".CurrentCTC").text(numberToIndianWords(ctcVal));

                    // $(".CurrentCTC").text(ctcWords.charAt(0).toUpperCase() + ctcWords.slice(1));
                    // $(".payratemonthly").text(payMonthlyWords.charAt(0).toUpperCase() + payMonthlyWords.slice(1));
                    // $(".payrateannual").text(payAnnualWords.charAt(0).toUpperCase() + payAnnualWords.slice(1));
                }else{
                    $(".payrateannual").text(' ');
                    $(".payratemonthly").text(' ');
                    $(".CurrentCTC").text(' ');
                }

                // Expected CTC
                var ectcVal = parseInt($("#ectc").val(), 10);
                var billMonthly = parseInt($("#bill_monthly").val(), 10);
                var billAnnual = parseInt($("#bill_annual").val(), 10);
                var ectcCurrency = $(".currency").val();

                
                if (Number(billAnnual) > maxLimit) {
                    Swal.fire("Warning", "Value above Arab is not allowed", "warning");
                    $('#bill_annual').val(maxLimit);
                    $(".billrateannual").text(numberToIndianWords(maxLimit));
                    return false;
                }
                
                if (!isNaN(billAnnual)) {
                    var ectcWords = window.numberToWords(ectcVal, ectcCurrency);
                    var billMonthlyWords = window.numberToWords(billMonthly, ctcCurrency);
                    var billAnnualWords = window.numberToWords(billAnnual, ctcCurrency);
                    
                    
                    $(".billrateannual").text(numberToIndianWords(billAnnual));
                    $(".billratemonthly").text(numberToIndianWords(billMonthly));
                    $(".expectedctc").text(numberToIndianWords(ectcVal));

                    // $(".expectedctc").text(ectcWords.charAt(0).toUpperCase() + ectcWords.slice(1));
                    // $(".billratemonthly").text(billMonthlyWords.charAt(0).toUpperCase() + billMonthlyWords.slice(1));
                    // $(".billrateannual").text(billAnnualWords.charAt(0).toUpperCase() + billAnnualWords.slice(1));

                }else{
                    $(".billrateannual").text('');
                    $(".billratemonthly").text('');
                    $(".expectedctc").text('');
                }
    }
    setTimeout(() => {
          updateCTCFields();
    }, 500);
    $(document).on("keyup", "#ctc", updateCTCFields);
    $(document).on("keyup", "#ectc", updateCTCFields);
    $(".currency").on("change", updateCTCFields);
            
    $(document).on('keyup', '#ectc', function() { 
      percentage_hike()
     });
     $(document).on('keyup', '#ctc', function() { 
      percentage_hike()
     });

     $(document).on('keyup', '#percentage_hike', function() { 
      ectc();

     });

     $(document).on('change', '#total_exp, #rel_exp', function() { 
      total_exp_and_rel_exp();
     });


		
</script>


<script>

var KTAddCandidate = function () {
    var form;
    var submitButton;
    var validator;

    
    var handleForm = function (e) {
       
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
				       	  // 'resume':
                  //   {
                  //     validators:
                  //       {
                  //           notEmpty:{message: "Resume is required"},
                  //           file: {
                  //             extension: 'pdf,doc,docx',
                  //             type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                  //             maxSize: 5242880,
                  //             message: 'Only PDF, DOC, DOCX files up to 5 MB are allowed'
                  //         }
                  //       }
                  // },
                  'full_name':{ validators:{notEmpty:{message: "Full name is required"}}},
                  'current_designation':{validators:{notEmpty:{ message: "Current designation is required"}}},
                  'phone': {validators: {notEmpty:{message: "Primary number is required"},
                            digits:{ message: "Primary number must contains only digits"},
                            stringLength:{ min: 10, max: 10, message: "Primary number must contains 10 digits only"}}},
                  'secondary_mobile':{validators:{digits:{ message: "Primary number must contains only digits"},
                            stringLength:{ min: 10, max: 10, message: "Primary number must contains 10 digits only"}}},
                  'email':{validators:{notEmpty:{message: "Primary Email is required"},
                            emailAddress:{message: 'The value is not a valid email address'}}},
                  'secondrary_email':{validators:{emailAddress:{message: 'The value is not a valid email address'}}},
                  'qualification': { validators: {notEmpty:{message: 'Qualification Name is required'} }},
                  'total_exp':{validators:{
                            // notEmpty:{message:"Total Experience is required"},                           
                        }
                  },
                  'rel_exp':
                  { validators:
                        {
                            // notEmpty:{ message:"Relevant Experience is required"}
                           
                        }
                  },
                  'ctc':
                  {validators:
                        {
                            // notEmpty:{ message: "Current CTC is required"}
                            
                        }
                  },
                  'ectc':
                    {validators:
                        {
                            // notEmpty:{message: "Exp. CTC is required"}
                            
                        }
                    },
					
                    'country':
                    {
                        validators:
                        {
                            // notEmpty:{message: "Country is required"}
                        }
                    },
					
                    'state':
                    {
                        validators:
                        {
                            // notEmpty:{message: "State is required"}
                           
                        }
                    },
					                  
                    'skills':
                    {
                        validators:
                        {
                            // notEmpty:{ message: 'Skills is required'}
                          
                        }
                    },
                    'specialties':
                    {
                        validators:
                        { notEmpty:{message: "Specialties is required"}
                           
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: '',
                        autoPlace: false,
                        message: ''
                    }),
                    message: new FormValidation.plugins.Message({
                    container: function(field, element) {
                          // Custom placement only for resume field
                          if (field === 'resume') {
                              return document.getElementById('resumeErrorBox'); 
                          }
                          return element.parentNode; // default placement
                      }
                  })
                }
            }
        );

       
        submitButton.addEventListener('click', async function (e) {
           
            e.preventDefault();
            // if (submitButton.disabled) {
            //     return; // ⛔ Prevent multiple clicks
            // }
            // submitButton.disabled = true;

            const connected = await isInternetAvailable();
            if (!connected) {
                Swal.fire({
                    title: "No Internet",
                    text: "You are currently offline or the server is unreachable.",
                    icon: "warning",
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn btn-warning"
                    }
                });
                return; // ⛔ STOP EXECUTION
            }
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                  $("#save-form").prop("disabled", true);
                  $("#save-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...`
                  );
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    setTimeout(function () {					
                        if (!navigator.onLine) {
                            Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                            $("#save-form").prop("disabled", false).html("Submit");
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            return;
                        }
					
												var formData = new FormData($('#createForm')[0]);
												// 0% Server Strain & 10K Concurrency Guard: Strip redundant binary file if already in R2
												if ($('#resume_session_uuid').val() || $('#uploaded_file_path').val()) {
												    if (formData.delete) { formData.delete('resume'); }
												}
												var submissionToken = 'sub_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11);
												formData.append('submission_token', submissionToken);
												$.ajax({
														   url 				: '{{route('get.careerStore')}}'+'?_token=' + '{{ csrf_token() }}',								   
														   type 			: 'POST',
														   data 			: formData,
														   timeout			: 30000,
														   processData		: false,  	// tell jQuery not to process the data
														   contentType		: false,  	// tell jQuery not to set contentType
														   redirect			: true,
														   success			: function (response) {
                                if(response.status=='error'){
                                  submitButton.removeAttribute('data-kt-indicator');
                                  submitButton.disabled = false;
                                  $("#save-form").html(
                                      `Submit`
                                    );
                                  Swal.fire({
                                  text				: response.message,
                                  icon: "error",
                                  buttonsStyling		: false,
                                  confirmButtonText	: "Ok, got it!",
                                  customClass			: {
                                            confirmButton: "btn btn-primary"
                                          }
                                  }).then(function (result) {
                                      if (result.isConfirmed) {
                                        $("#save-form").prop("disabled", false);
                                        return false;
                                      }
                                  });
                                  return false;
                                }
                               
                                if(response.status == 'coinCheck'){
   																	Swal.fire("Warning", response.message , "warning").then(function(){
																		
																	});	
                                   return false;
                                }
                                
                                if(response.status == 'mailcheck'){
                                  if (!navigator.onLine) {
                                      Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                      $("#save-form").prop("disabled", false).html("Submit");
                                      submitButton.disabled = false;
                                      return;
                                  }
                                   $("#save-form").html(`Submit`);
   																	Swal.fire("Warning", response.message , "warning").then(function(){
																	});	
                                   return false;
                                }

                                  submitButton.removeAttribute('data-kt-indicator');
                                  submitButton.disabled = true;
                                  Swal.fire({
                                  text				: "You have successfully Submitted",
                                  icon				: "success",
                                  buttonsStyling		: false,
                                  confirmButtonText	: "Ok, got it!",
                                  customClass			: {
                                            confirmButton: "btn btn-primary"
                                          }
                                  }).then(function (result) {
                                      if (window.CareerApplyDraftManager) {
                                          window.CareerApplyDraftManager.clearDraft();
                                      }
                                      if (result.isConfirmed) {
                                          window.open('', '_self');
                                          window.close();
                                          form.querySelector('[name="email1"]').value 		= "";
                                          form.querySelector('[name="password1"]').value 		= "";
                                      }
                                  });


                                  

                                  
                                 

																},
																error			: function (xhr, textStatus) {
                                $("#save-form").prop("disabled", false).html("Submit");
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                if (textStatus === 'timeout') {
                                    Swal.fire({ title: "Request Timed Out", text: "The server took too long to respond. Please check your internet connection and try submitting again.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                } else if (xhr.status === 429) {
                                    Swal.fire({ title: "Too Many Requests", text: "You are submitting too fast. Please wait a moment before trying again.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                } else if (xhr.status === 0 || !navigator.onLine) {
                                    Swal.fire({ title: "No Internet", text: "You are currently offline or the server is unreachable.", icon: "warning", confirmButtonText: "Ok, got it!", customClass: { confirmButton: "btn btn-warning" } });
                                } else {
                                    Swal.fire("Error", "Something went wrong. Please try again.", "error");
                                }
																}
													});
							
                  
                    }, 50);
                } 
				else 
				{
                   
                    Swal.fire({
									text				: "Sorry, looks like there are some errors detected, please try again.",
									icon				: "error",
									buttonsStyling		: false,
									confirmButtonText	: "Ok, got it!",
									customClass			: {
																confirmButton: "btn btn-primary"
														  }
                    });
                }
            });
        });
    }

    return {
				init: function () {
										form 			=	document.querySelector('#createForm');
										submitButton 	=	document.querySelector('#save-form');
										handleForm();
									}
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTAddCandidate.init();
    if (window.CareerApplyDraftManager) {
        window.CareerApplyDraftManager.init();
    }
});

/**
 * Intent: Client-side draft autosave & restore engine using sessionStorage.
 * Tenancy: Scoped strictly by company_id and job_id to prevent cross-tenant or cross-job contamination.
 */
window.CareerApplyDraftManager = {
    draftKey: 'career_apply_draft_{{ $company_id }}_{{ $jobId ?? (@$jobDetail->id ?? "general") }}',
    timer: null,

    init: function() {
        var self = this;
        this.restoreDraft();

        // Listen for input changes on createForm (debounced 400ms)
        $('#createForm').on('input change', 'input:not([type=file]):not([type=hidden][name=_token]):not([name=password]):not([name=password1]), select, textarea', function() {
            clearTimeout(self.timer);
            self.timer = setTimeout(function() {
                self.saveDraft();
            }, 400);
        });
    },

    saveDraft: function() {
        var form = document.getElementById('createForm');
        if (!form) return;
        var draft = {};
        var elements = form.querySelectorAll('input:not([type=file]):not([type=hidden][name=_token]):not([name=password]):not([name=password1]), select, textarea');
        elements.forEach(function(el) {
            if (el.name && el.value) {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    if (el.checked) draft[el.name] = el.value;
                } else {
                    draft[el.name] = el.value;
                }
            }
        });
        try {
            sessionStorage.setItem(this.draftKey, JSON.stringify(draft));
        } catch(e) {}
    },

    restoreDraft: function() {
        try {
            var raw = sessionStorage.getItem(this.draftKey);
            if (!raw) return;
            var draft = JSON.parse(raw);
            if (!draft || typeof draft !== 'object') return;

            var form = document.getElementById('createForm');
            if (!form) return;

            Object.keys(draft).forEach(function(name) {
                var el = form.querySelector('[name="' + name + '"]');
                if (el && !el.value) {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = (el.value === draft[name]);
                    } else {
                        el.value = draft[name];
                        $(el).trigger('change');
                    }
                }
            });
        } catch(e) {}
    },

    clearDraft: function() {
        try {
            sessionStorage.removeItem(this.draftKey);
        } catch(e) {}
    }
};

function re_test(text){
  var jobId = "{{ @$jobId }}";
  $('.page-loader').css('display', 'flex');
  var page_event_check = "{{$page_event}}";
  $.ajax({
                    type		: 'POST',
                    url			: "{{ route('get.extract-resume-job-applications',':id') }}",
                    data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'resumeText' : text,'jobId':jobId},
                    beforeSend	: function() {
                    },
                    success 	: function (response) {
                      $('.page-loader').css('display', 'none');
                      
                    if(response.linkedin !=''){
                      $('#linkedin').val(response.linkedin);
                    }
                    if(response.github !=''){
                       $('#github').val(response.github);
                    }
                    if(response.behance !=''){
                       $('#behance').val(response.behance);
                    }

                      if(page_event_check != 'EDIT'){
                      $('#full_name').val(response.name);
                      $('#phone').val(response.number);
                      $('#email').val(response.email);
                      $('#dob').val(response.dob);
                      $('#current_company').val(response.currentjob);
                      $('#qualification').val(response.qualification).change();
                      if (response.careerSummary) {
                          $('#careersummary').val(response.careerSummary);
                      }

                      tagifyskill.removeAllTags(); // Clear existing tags first
                      tagifyskill.addTags(response.skills); // Add new tags
                      tagifylocation.removeAllTags(); // Clear existing tags first
                      tagifylocation.addTags(response.country); // Add new tags


                      var resumeText = $('#uploaded_file_path').val();
                      $.ajax({
                        type		: 'POST',
                        url			: "{{ route('service_company.file.unset') }}",
                        data		: {'_token': '{{ csrf_token() }}','_method': 'POST', 'file_path' : resumeText},
                        success 	: function (response) {

                        }

                      });
                      


                      var countryName = response.country;
                      var stateName = response.state;
                      var cityName = response.city;
                    
                    var countrydropdown = document.getElementById("country");
                    for (var i = 0; i < countrydropdown.options.length; i++) {
                     
                        if (countrydropdown.options[i].text.trim() === countryName) {
                            // Set the option as selected
                            countrydropdown.selectedIndex = i;
                            var event = new Event('change');
                            countrydropdown.dispatchEvent(event);
                            break; // Exit the loop since we found the country
                        }
                    }
                   
                  setTimeout(() => {
                    var statedropdown = document.getElementById("state");
                    for (var ik = 0; ik < statedropdown.options.length; ik++) {
                        if (statedropdown.options[ik].text.trim() === stateName) {
                            statedropdown.selectedIndex = ik;
                            $("#state").change();
                            break; // Exit the loop since we found the state
                        }
                    }   
                  }, 1000);

                  setTimeout(() => {
                    $('city').val(cityName);
                    var city_iddropdown = document.getElementById("city_id");
                    for (var ik = 0; ik < city_iddropdown.options.length; ik++) {
                        if (city_iddropdown.options[ik].text.trim() === cityName) {
                            //Set the option as selected Uttar Pradesh Uttar Pradesh
                            
                            city_iddropdown.selectedIndex = ik;
                            var event = new Event('change');
                            city_iddropdown.dispatchEvent(event);
                            break; 
                        }
                    }   
                  }, 2000);

                }

                  

                  // $.each(response.skills, function(index, skill) {
                  //     console.log(skill); // Output each skill
                      
                  //     $('.value_JSON').prepend('<tag title="'+skill+'" contenteditable="false" spellcheck="false" tabindex="-1" class="tagify__tag " value="'+skill+'"><x title="" class="tagify__tag__removeBtn" role="button" aria-label="remove tag"></x><div><span class="tagify__tag-text">'+skill+'</span></div></tag>');
                  //   });

                      }
               });	

}


document.addEventListener('DOMContentLoaded', function () {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', today);
});

          
    function percentage_hike(){
     
        var currentCTC = parseFloat($('#pay_annual').val());
        var expectedCTC = parseFloat($('#bill_annual').val());
        
        var percentageIncrease = ((expectedCTC - currentCTC) / currentCTC) * 100;
        if (currentCTC > 0) {
        $('#percentage_hike').val(percentageIncrease.toFixed(0));
        }else{
          $('#percentage_hike').val('');
        }
    }

    function ectc(){
        var currentCTC = parseFloat($('#pay_annual').val());
        var percentage_hike = $('#percentage_hike').val();
        var amount = currentCTC*percentage_hike/100;
        $('#bill_annual').val(amount+currentCTC);
        
    }
    
    function total_exp_and_rel_exp(){
        var total_exp = parseFloat($('#total_exp').val());
        var rel_exp = parseFloat($('#rel_exp').val());
        if(rel_exp>total_exp){
       $('#rel_exp_error').text('Relevant Experience can not be higher then Total Experience');
       setTimeout(() => {
        $('#rel_exp').val('');
       }, 1000);
         
    }else{
      $('#rel_exp_error').text('');
    }
             
    }

    function normalizeResumeText(text) {
        return text
            .replace(/[^\x00-\x7F]/g, " ")   // remove broken unicode
            .replace(/\s+/g, " ")
            .replace(/\|/g, " ")
            .trim();
    }


    function handleFileInputChange(event) {
        // Deprecated: Resume parsing is executed 100% client-side in processFile() via JeroClientParser.
        // Zero duplicate uploads to /uploadresume, zero server parse requests.
        return;
    }

    // Attach event listener to file input
    // handleFileInputChange called via processFile with __upXhrChannelClaimed guard — not bound directly
    // document.getElementById('resume').addEventListener('change', handleFileInputChange);

    
$(document).ready(function() {
    $.noConflict();  
        var optionFormat = function(item) {
            if ( !item.id ) {
                return item.text;
            }

            var span = document.createElement('span');
            var imgUrl = item.element.getAttribute('data-kt-select2-user');
            var template = '';

            template += '';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        }

        $('#city_id').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

        $('#country').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });

        $('#country').change(function(){  
   				var cid				=	$(this).val() ;
          var cityId				=	"{{ (@$application->city_id) ? @$application->city_id : 0 }}"; 
            if(cid == 101){
              $('#city_id').show();	
              $('#city').hide();	
            }else{
              $('#city').hide();	
              $('#city_id').show();	
            }
              //route('service_company.job-applications.statelist',':cid') 
              var url 			=	"{{ route('countrycitylist',':cid') }}";
              url 				=	url.replace(':cid', cid);
              $.ajax({
                      type	: 'GET',
                      url		: url,
                      success	: function (response) {																		
                                        $('#city_id').html(response.data);
                                        setTimeout(() => {
                                        if(cityId	>=1){
                                          $('#city_id').val(cityId).change();
                                        }
                                      
                                      }, 500);
                                      }		
              });
        });
        $('#country').trigger('change');
			
   
   });



async function isInternetAvailable() {
      
            try {
                await fetch("https://www.gstatic.com/generate_204", {
                    method: "GET",
                    cache: "no-cache",
                    mode: "no-cors"
                });
                return true;
            } catch (error) {
                return false;
            }
        }

/**
 * ==========================================================================
 * HIGHEST QUALIFICATION: 100% BROWSER RAM COMBOBOX ENGINE (CAREER APPLY BLADE)
 * ==========================================================================
 * - UNESCO ISCED-2011 7-Category Education Hierarchy
 * - 0% Server Strain: Instant < 1ms search & filtering directly in User RAM
 * - Adaptive Regional Demonym Curating with Global Search Unlock
 * - 100% Two-Way Synchronization with native <select id="qualification"> for form submission & resume parser
 * - Rule 17 Page Isolation & 15.6" screen responsiveness
 */
(function($) {
    'use strict';

    // 1. Regional Bucket Resolver for Adaptive Idle Curation
    function getRegionalBucket(nationality) {
        var nat = (nationality || '').trim();
        if (!nat || nat === 'Indian') return ['Indian'];
        if (nat === 'American') return ['American'];
        if (nat === 'Canadian') return ['Canadian', 'American'];
        if (nat === 'British') return ['British', 'Commonwealth'];
        if (nat === 'Australian') return ['Australian', 'Commonwealth', 'British'];
        if (['German', 'French', 'Dutch', 'Irish', 'Italian', 'Spanish', 'Swedish', 'Swiss', 'Austrian', 'Belgian', 'Polish'].indexOf(nat) !== -1) {
            return ['European'];
        }
        if (['Emirati', 'Saudi', 'Qatari', 'Kuwaiti', 'Bahraini', 'Omani'].indexOf(nat) !== -1) {
            return ['Indian', 'British', 'American'];
        }
        return ['all']; // 'Other' or unlisted demonyms show all degrees cleanly
    }

    // 2. Token-Based, Accent-Insensitive and Punctuation-Insensitive String Cleaner
    function jeroCleanStr(str) {
        if (!str) return '';
        return str.toString()
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    // 3. Close All Comboboxes Helper
    function closeAllComboboxes() {
        $('.jero-custom-select').removeClass('is-open open-up');
        $('.jero-cs-trigger').attr('aria-expanded', 'false');
        $('.fs, .candidate-profile-card, .bd-card').removeClass('has-active-combobox');
    }

    // 4. Build Custom Combobox DOM from native <select id="nationality">
    function buildNationalityCombobox() {
        var $select = $('#nationality');
        if (!$select.length) return;
        $('#jero-cs-nationality').remove();

        var placeholder = 'Select nationality';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text().trim();
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-nationality">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-regular fa-flag"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search nationality or country (e.g. India, USA, UK, Canada)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-circle-question"></i> No nationality found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text().trim();
                    var synonyms = $opt.attr('data-synonyms') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-synonyms="' + synonyms + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            } else if ($node.is('option') && $node.val()) {
                var val = $node.val();
                var text = $node.text().trim();
                var synonyms = $node.attr('data-synonyms') || '';
                var isSelected = (val === currentVal && !selectedItemDone);
                if (isSelected) selectedItemDone = true;
                var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-synonyms="' + synonyms + '">' +
                    '<span class="jero-cs-item-text">' + text + '</span>' +
                '</div>');
                $list.append($item);
            }
        });

        $select.closest('.fg, .candidate-field-control, .bd-control').addClass('candidate-field-control--has-combobox bd-control--has-combobox fg--has-combobox has-combobox');
        $select.after($cs);
    }

    // 5. Build Custom Combobox DOM from native <select id="qualification">
    function buildQualificationCombobox() {
        var $select = $('#qualification');
        if (!$select.length) return;
        $('#jero-cs-qualification').remove();

        var placeholder = 'Select qualification';
        var currentVal = ($select.val() || '').trim();
        var currentLabel = placeholder;
        var currentBadge = '';

        if (currentVal) {
            var $selOpt = $select.find('option:selected').first();
            if ($selOpt.length && $selOpt.val()) {
                currentLabel = $selOpt.text().trim();
                currentBadge = $selOpt.attr('data-badge') || '';
            }
        }

        var html = '<div class="jero-custom-select" id="jero-cs-qualification">' +
            '<button type="button" class="jero-cs-trigger" aria-haspopup="listbox" aria-expanded="false">' +
                '<div class="jero-cs-left">' +
                    '<span class="jero-cs-icon"><i class="fa-solid fa-graduation-cap"></i></span>' +
                    '<span class="jero-cs-value ' + (!currentVal ? 'jero-cs-placeholder' : '') + '">' + currentLabel + '</span>' +
                '</div>' +
                '<div class="jero-cs-right">' +
                    '<span class="jero-cs-badge" style="' + (currentBadge ? '' : 'display:none;') + '">' + currentBadge + '</span>' +
                    '<span class="jero-cs-arrow"><i class="fa-solid fa-chevron-down"></i></span>' +
                '</div>' +
            '</button>' +
            '<div class="jero-cs-dropdown">' +
                '<div class="jero-cs-search-box">' +
                    '<i class="fa-solid fa-magnifying-glass jero-cs-search-icon"></i>' +
                    '<input type="text" class="jero-cs-search-input" placeholder="Search degree or acronym (e.g. B.Tech, MS, HND, CA, MBA)..." spellcheck="false" autocomplete="off">' +
                    '<button type="button" class="jero-cs-search-clear" style="display:none;" title="Clear search">&times;</button>' +
                '</div>' +
                '<div class="jero-cs-list"></div>' +
                '<div class="jero-cs-empty" style="display:none;"><i class="fa-solid fa-graduation-cap"></i> No matching degrees found</div>' +
            '</div>' +
        '</div>';

        var $cs = $(html);
        var $list = $cs.find('.jero-cs-list');
        var selectedItemDone = false;

        $select.children().each(function() {
            var $node = $(this);
            if ($node.is('optgroup')) {
                var groupLabel = $node.attr('label') || '';
                var $group = $('<div class="jero-cs-group"><div class="jero-cs-group-title">' + groupLabel + '</div></div>');
                $node.find('option').each(function() {
                    var $opt = $(this);
                    var val = $opt.val();
                    if (!val) return;
                    var text = $opt.text().trim();
                    var regions = $opt.attr('data-regions') || 'Global';
                    var aliases = $opt.attr('data-aliases') || '';
                    var badge = $opt.attr('data-badge') || '';
                    var isSelected = (val === currentVal && !selectedItemDone);
                    if (isSelected) selectedItemDone = true;
                    var badgeHtml = badge ? '<span class="jero-cs-item-badge">' + badge + '</span>' : '';
                    var $item = $('<div class="jero-cs-item ' + (isSelected ? 'is-selected' : '') + '" data-val="' + val + '" data-regions="' + regions + '" data-aliases="' + aliases + '" data-badge="' + badge + '">' +
                        '<span class="jero-cs-item-text">' + text + '</span>' +
                        badgeHtml +
                    '</div>');
                    $group.append($item);
                });
                $list.append($group);
            }
        });

        $select.closest('.fg, .candidate-field-control, .bd-control').addClass('candidate-field-control--has-combobox bd-control--has-combobox fg--has-combobox has-combobox');
        $select.after($cs);
    }

    // 6. Filter Nationality items by country synonyms or demonym text
    function filterNationalityItems() {
        var $cs = $('#jero-cs-nationality');
        if (!$cs.length) return;
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var text = $item.find('.jero-cs-item-text').text() || '';
                var synonyms = $item.attr('data-synonyms') || '';
                var val = $item.attr('data-val') || '';

                var isMatch = false;
                if (!cleanQ) {
                    isMatch = true;
                } else {
                    var cleanText = jeroCleanStr(text);
                    var cleanSynonyms = jeroCleanStr(synonyms);
                    var cleanVal = jeroCleanStr(val);
                    var combined = cleanText + ' ' + cleanSynonyms + ' ' + cleanVal;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanSynonyms.indexOf(cleanQ) !== -1 || cleanVal.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) {
                            isMatch = true;
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden').show();
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden').hide();
                }
            });

            if (groupVisible > 0) {
                $group.removeClass('is-hidden').show();
            } else {
                $group.addClass('is-hidden').hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').removeClass('is-hidden').show();
        } else {
            $cs.find('.jero-cs-empty').addClass('is-hidden').hide();
        }
    }

    // 7. Intelligent Filtering for Highest Qualification (Idle Regional Curation vs Global Search Unlock)
    function filterQualificationItems() {
        var $cs = $('#jero-cs-qualification');
        if (!$cs.length) return;
        var selectedNat = ($('#nationality').val() || '').trim();
        var selectedQual = ($('#qualification').val() || '').trim();
        var bucket = getRegionalBucket(selectedNat);
        var rawQuery = $cs.find('.jero-cs-search-input').val() || '';
        var cleanQ = jeroCleanStr(rawQuery);
        var compactQ = cleanQ.replace(/\s+/g, '');
        var queryWords = cleanQ.split(' ').filter(Boolean);
        var totalVisible = 0;

        $cs.find('.jero-cs-group').each(function() {
            var $group = $(this);
            var groupVisible = 0;

            $group.find('.jero-cs-item').each(function() {
                var $item = $(this);
                var val = String($item.attr('data-val') || '');
                var text = $item.find('.jero-cs-item-text').text() || '';
                var aliases = $item.attr('data-aliases') || '';
                var badge = $item.attr('data-badge') || '';
                var regions = ($item.attr('data-regions') || 'Global').split(',').map(function(r) { return r.trim(); });

                var isMatch = false;

                if (cleanQ.length > 0) {
                    // Global Search Mode: Unlocks all degrees worldwide regardless of nationality
                    var cleanText = jeroCleanStr(text);
                    var cleanAliases = jeroCleanStr(aliases);
                    var cleanBadge = jeroCleanStr(badge);
                    var combined = cleanText + ' ' + cleanAliases + ' ' + cleanBadge;
                    var combinedCompact = combined.replace(/\s+/g, '');

                    if (cleanText.indexOf(cleanQ) !== -1 || cleanAliases.indexOf(cleanQ) !== -1 || cleanBadge.indexOf(cleanQ) !== -1) {
                        isMatch = true;
                    } else if (combinedCompact.indexOf(compactQ) !== -1) {
                        isMatch = true;
                    } else if (queryWords.length > 0) {
                        var allWordsMatch = true;
                        for (var w = 0; w < queryWords.length; w++) {
                            if (combined.indexOf(queryWords[w]) === -1 && combinedCompact.indexOf(queryWords[w]) === -1) {
                                allWordsMatch = false;
                                break;
                            }
                        }
                        if (allWordsMatch) isMatch = true;
                    }
                } else {
                    // Idle Mode: Regionally curates degrees based on selected nationality
                    if (val === selectedQual) {
                        isMatch = true;
                    } else if (bucket.indexOf('all') !== -1 || regions.indexOf('Global') !== -1) {
                        isMatch = true;
                    } else {
                        for (var k = 0; k < bucket.length; k++) {
                            if (regions.indexOf(bucket[k]) !== -1) {
                                isMatch = true;
                                break;
                            }
                        }
                    }
                }

                if (isMatch) {
                    $item.removeClass('is-hidden').show();
                    groupVisible++;
                    totalVisible++;
                } else {
                    $item.addClass('is-hidden').hide();
                }
            });

            if (groupVisible > 0) {
                $group.removeClass('is-hidden').show();
            } else {
                $group.addClass('is-hidden').hide();
            }
        });

        if (totalVisible === 0) {
            $cs.find('.jero-cs-empty').removeClass('is-hidden').show();
        } else {
            $cs.find('.jero-cs-empty').addClass('is-hidden').hide();
        }
    }

    // 8. Event Listeners & Two-Way Synchronization

    // Open/Toggle Combobox
    $(document).on('click', '.jero-cs-trigger', function(e) {
        e.stopPropagation();
        var $combobox = $(this).closest('.jero-custom-select');
        var wasOpen = $combobox.hasClass('is-open');

        closeAllComboboxes();

        if (!wasOpen) {
            var rect = this.getBoundingClientRect();
            var spaceBelow = window.innerHeight - rect.bottom;
            if (spaceBelow < 340 && rect.top > 340) {
                $combobox.addClass('open-up');
            } else {
                $combobox.removeClass('open-up');
            }

            $combobox.addClass('is-open');
            $(this).attr('aria-expanded', 'true');
            $combobox.closest('.fs, .candidate-profile-card, .bd-card').addClass('has-active-combobox');

            var $search = $combobox.find('.jero-cs-search-input');
            $search.val('');
            $combobox.find('.jero-cs-search-clear').hide();

            if ($combobox.attr('id') === 'jero-cs-nationality') {
                filterNationalityItems();
            } else if ($combobox.attr('id') === 'jero-cs-qualification') {
                filterQualificationItems();
            }

            setTimeout(function() {
                $search.focus().select();
            }, 60);
        }
    });

    // Prevent clicks inside dropdown from bubbling and closing
    $(document).on('click', '.jero-cs-dropdown', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', '.jero-cs-search-box', function(e) {
        e.stopPropagation();
        $(this).find('.jero-cs-search-input').focus();
    });

    // Search Input Typing Handler
    $(document).on('input keyup change', '.jero-cs-search-input', function(e) {
        if (e.which === 27 || e.which === 13 || e.which === 38 || e.which === 40) return;
        var $input = $(this);
        var val = $input.val();
        var $clearBtn = $input.siblings('.jero-cs-search-clear');
        if (val) {
            $clearBtn.show();
        } else {
            $clearBtn.hide();
        }

        var $combobox = $input.closest('.jero-custom-select');
        if ($combobox.attr('id') === 'jero-cs-nationality') {
            filterNationalityItems();
        } else if ($combobox.attr('id') === 'jero-cs-qualification') {
            filterQualificationItems();
        }
    });

    // Clear Search Input Button
    $(document).on('click', '.jero-cs-search-clear', function(e) {
        e.stopPropagation();
        var $input = $(this).siblings('.jero-cs-search-input');
        $input.val('').focus();
        $(this).hide();
        var $combobox = $(this).closest('.jero-custom-select');
        if ($combobox.attr('id') === 'jero-cs-nationality') {
            filterNationalityItems();
        } else if ($combobox.attr('id') === 'jero-cs-qualification') {
            filterQualificationItems();
        }
    });

    // Keyboard Navigation: Escape, Arrows, Enter
    $(document).on('keydown', '.jero-custom-select.is-open', function(e) {
        var $combobox = $(this);
        var $visibleItems = $combobox.find('.jero-cs-item:not(.is-hidden):visible');

        if (e.which === 27) { // Escape
            closeAllComboboxes();
            $combobox.find('.jero-cs-trigger').focus();
            e.preventDefault();
        } else if (e.which === 40) { // Arrow Down
            var $curr = $visibleItems.filter('.is-highlighted');
            var idx = $visibleItems.index($curr);
            $visibleItems.removeClass('is-highlighted');
            if (idx === -1 || idx >= $visibleItems.length - 1) {
                var $target = $visibleItems.first();
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            } else {
                var $target = $visibleItems.eq(idx + 1);
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            }
            e.preventDefault();
        } else if (e.which === 38) { // Arrow Up
            var $curr = $visibleItems.filter('.is-highlighted');
            var idx = $visibleItems.index($curr);
            $visibleItems.removeClass('is-highlighted');
            if (idx <= 0) {
                var $target = $visibleItems.last();
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            } else {
                var $target = $visibleItems.eq(idx - 1);
                $target.addClass('is-highlighted');
                if ($target.length) $target[0].scrollIntoView({ block: 'nearest' });
            }
            e.preventDefault();
        } else if (e.which === 13) { // Enter
            var $target = $visibleItems.filter('.is-highlighted');
            if (!$target.length) {
                $target = $visibleItems.first();
            }
            if ($target.length) {
                $target.trigger('click');
            }
            e.preventDefault();
        }
    });

    // Select Nationality Item
    $(document).on('click', '#jero-cs-nationality .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val') || '';
        var text = $item.find('.jero-cs-item-text').text().trim();

        var $cs = $('#jero-cs-nationality');
        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        $cs.removeClass('has-validation-error');
        $cs.closest('.fg, .candidate-field-control, .bd-control').removeClass('has-validation-error');
        $('[data-field-error="nationality"]').remove();
        closeAllComboboxes();

        var $nativeNat = $('#nationality');
        if ($nativeNat.val() !== val) {
            $nativeNat.val(val).trigger('change');
        }

        filterQualificationItems();

        if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
            window.benchFormValidator.revalidateField('nationality');
            window.benchFormValidator.revalidateField('qualification');
        }
    });

    // Select Qualification Item
    $(document).on('click', '#jero-cs-qualification .jero-cs-item', function(e) {
        e.stopPropagation();
        var $item = $(this);
        var val = $item.attr('data-val') || '';
        var text = $item.find('.jero-cs-item-text').text().trim();
        var badge = $item.attr('data-badge') || '';
        var $cs = $('#jero-cs-qualification');

        $cs.find('.jero-cs-item').removeClass('is-selected');
        $item.addClass('is-selected');
        $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(text);
        if (badge) {
            $cs.find('.jero-cs-badge').show().text(badge);
        } else {
            $cs.find('.jero-cs-badge').hide().text('');
        }
        $cs.removeClass('has-validation-error');
        $cs.closest('.fg, .candidate-field-control, .bd-control').removeClass('has-validation-error');
        $('[data-field-error="qualification"]').remove();
        closeAllComboboxes();

        var $native = $('#qualification');
        if ($native.val() !== val) {
            $native.val(val).trigger('change');
            if (window.benchFormValidator && typeof window.benchFormValidator.revalidateField === 'function') {
                window.benchFormValidator.revalidateField('qualification');
            }
        }
    });

    // Global Click Outside Handler
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.jero-custom-select').length) {
            closeAllComboboxes();
        }
    });

    // Native <select id="nationality"> External Change Listener (Resume Parser or programmatic update)
    $(document).on('change', '#nationality', function() {
        var val = ($(this).val() || '').trim();
        var $cs = $('#jero-cs-nationality');
        if ($cs.length) {
            var $item = $cs.find('.jero-cs-item[data-val="' + val + '"]').first();
            $cs.find('.jero-cs-item').removeClass('is-selected');
            if ($item.length) {
                $item.addClass('is-selected');
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text().trim());
            } else if (!val) {
                $cs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select nationality');
            } else {
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text(val);
            }
        }
        filterQualificationItems();
    });

    // Native <select id="qualification"> External Change Listener (Resume Parser or programmatic update)
    $(document).on('change', '#qualification', function() {
        var val = ($(this).val() || '').trim();
        var $cs = $('#jero-cs-qualification');
        if ($cs.length) {
            var $item = $cs.find('.jero-cs-item[data-val="' + val + '"]').first();
            $cs.find('.jero-cs-item').removeClass('is-selected');
            if ($item.length) {
                $item.addClass('is-selected');
                $cs.find('.jero-cs-value').removeClass('jero-cs-placeholder').text($item.find('.jero-cs-item-text').text().trim());
                var badge = $item.attr('data-badge') || '';
                if (badge) {
                    $cs.find('.jero-cs-badge').show().text(badge);
                } else {
                    $cs.find('.jero-cs-badge').hide().text('');
                }
            } else if (!val) {
                $cs.find('.jero-cs-value').addClass('jero-cs-placeholder').text('Select qualification');
                $cs.find('.jero-cs-badge').hide().text('');
            }
        }
    });

    // Mount on DOM Ready
    $(document).ready(function() {
        buildNationalityCombobox();
        buildQualificationCombobox();
        filterQualificationItems();
    });

})(jQuery);

       


</script>

