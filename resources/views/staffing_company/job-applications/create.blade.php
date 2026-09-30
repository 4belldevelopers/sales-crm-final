@extends('layouts.staffing_company_layout')
@push('head-script')
<script>
   $( function()
   {
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
body
{
   overflow-x:hidden;
}
.dropzone
{
   padding: 1.8rem 1.75rem!important;
   border: 2px dashed #e6e7e8!important;
   background:transparent!important;
   text-align: inherit!important;
}
.border-radius-rb
{
   border-top-right-radius: 0em!important;
}
.border-radius-rt
{
   border-bottom-right-radius: 0em!important;
}
.border-radius-lt
{
   border-bottom-left-radius: .4em!important;
}
.border-radius-lb
{
   border-top-left-radius: .4em!important;
}
.font-size-icon
{
   font-size: 1.3em;
}
.dashed-border-top
{
   border-top:1px dashed gray;
}
.br-currency-select2-dropdown {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06) !important;
    overflow: hidden;
    z-index: 1060;
}
.br-currency-select2-dropdown .select2-search--dropdown {
    padding: 8px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 13px;
    outline: none;
    font-family: 'Poppins', sans-serif;
    background-color: #ffffff;
    width: 100%;
}
.br-currency-select2-dropdown .select2-search--dropdown .select2-search__field:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
.br-currency-select2-dropdown .select2-results__options {
    max-height: 260px;
}
.br-currency-select2-dropdown .select2-results__option {
    padding: 7px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    line-height: 1.4;
}
.br-currency-select2-dropdown .select2-results__option--highlighted[aria-selected] {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
}
.br-currency-select2-dropdown .select2-results__option[aria-selected="true"] {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<?php 
		use App\Helper\GeneralMethod;
?>
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <?php 
      //echo '<pre>'; print_r($documents); exit;  
      $resume_url			=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
      $photo				=	'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';	
      if($page_event == 'ADD')
      {
         $pagetitle			=	'Add Candidate';
         $resume_url			=	'';	
      }
      else if($page_event == 'EDIT')
      { 
         $pagetitle	=	'Edit Candidate';      
         if(!empty($documents->hashname))
         {
            $url				=	 url('/public/user-uploads/documents/'.$documents->documentable_id.'/'.$documents->hashname);
            $resume_url			=	'<a href="'.$url.'" target="_blank"><i class="fa fa-download" aria-hidden="true" style="color:#003300"></i></a>';	
         }
         else
         {
            $resume_url			=	'';	
         }
         if(!empty($application->photo))
         {
            $photo			=	 url('/public/user-uploads/candidate-photos/'.$application->photo);	
         }   
      }	
   ?>
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::Requisitions=-->
      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
        <?php 
               //echo '<pre>'; print_r(@$jobDetail); exit; 					
               
               $bgTextDecore			=	$strVal	=	'';
               $str					=	trim(@$jobDetail->title);
               $str	= $strVal		=	strtolower($str[0]);
               $str					=	ord($str);
               if($str >=97 && $str <=101)
               {
                  $bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
               }
               else if($str >=102 && $str <=106)
               {
                  $bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
               }
               else if($str >=107 && $str <=111)
               {
                  $bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
               }
               else if($str >=112 && $str <=116)
               {
                  $bgTextDecore		=	'fs-3 bg-light-success text-success';	
               }
               else if($str >=117 && $str <=121)
               {
                  $bgTextDecore		=	'fs-3 bg-dark text-light';	
               }
               else
               {
                  $bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
               }		
               ?>
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
          <div class="symbol-label fs-3 <?php echo @$bgTextDecore; ?> text-dark border-radius-5"><?php echo ucwords(@$strVal); ?></div>
          </a> </div>
        <!--end::Avatar-->
        <!--begin::User details-->
        <div class="d-flex flex-column me-5"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo ucwords(substr(@$jobDetail->title, 0, 15)); ?></a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary"><?php echo ucwords(substr(@$jobDetail->client_name, 0, 15)); ?></a> </div>
        <!--begin::User details-->
      </td>
      <!--end::Requisitions-->
      <!--begin::Experience & Salary-->
      <td><div class="d-flex flex-column me-5"> <span class="text-dark fw-bolder fs-6"><?php echo @$jobDetail->min_exp; ?>-<?php echo @$jobDetail->max_exp; ?> Yrs</span> <span class="text-muted fw-bold d-block fs-7"><?php echo @$jobDetail->min_salary; ?>-<?php echo @$jobDetail->max_salary; ?></span> </div></td>
      <!--end::Experience & Salary-->
      <!--begin::Location-->
      <td><?php 

            //echo '<pre>'; print_r(@$jobLocation); exit; 

            $city			=	'';
            $country_id		=	'';
            $country_name	=	'';
            $state_name		=	'';
            $address		=	'';
            $address		=	@$jobLocation->address;
            if(@$jobLocation->country_id == 101){
               $city			=	@$jobLocation->city_name;
               $state_name		=	@$jobLocation->state_name;	
            }
            else{
               $city		=	@$jobLocation->city;		
            }

            if(@$jobLocation->country_id > 0){
                  $country_name	=	GeneralMethod::getCountryName(@$jobLocation->country_id);	
                  foreach($country_name as $cval){
                     $country_name	=	ucwords($cval['name']);	
                  }				 
            }
                                 
            ?>
        <div class="d-flex flex-column me-5"> <span class="text-dark fw-bolder fs-6"><?php echo ucwords($address); ?></span> <span class="text-muted fw-bold d-block fs-7"><?php echo ucwords($city); ?> | <?php echo ucwords(@$country_name); ?></span> </div></td>
      <!--end::Location-->
      <!--begin::Status & Req. ID-->
      <td class="d-none"><?php 
               $status_class	=	'';
               if(@$jobDetail->status == 'active')
               {
                  $status_class	=	'bg-light-success text-success';
               }
               else if(@$jobDetail->status == 'hold')
               {
                  $status_class	=	'bg-light-warning text-warning';
               }
               else if(@$jobDetail->status == 'inactive')
               {
                  $status_class	=	'bg-light-danger text-danger';
               }
               ?>
        <div class="d-flex flex-column me-5"> <span class="badge fw-bolder fs-6 <?php echo @$status_class; ?>  px-2 py-1"><?php echo ucwords(@$jobDetail->status); ?></span> <span class="text-muted fw-bold d-block fs-7">R-<?php echo @$jobDetail->id; ?></span> </div></td>
      <!--end::Status & Req. ID-->
      <!--begin::Interview Date & No. of Openings-->
      <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">
          <?php 
                  $created_at 		=	"";
                  $created_at 		=	@$jobDetail->created_at;
                  $created_at 		=	date("d M, Y", strtotime($created_at));
                  echo @$created_at;
                  ?>
          </span> <span class="text-muted fw-bold d-block fs-7">540</span> </div></td>
      <!--end::Interview Date & No. of Openings-->
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
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
          <!--begin::Form-->
          <h3 class="py-5"><?php echo @$pagetitle; ?></h3>
          <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="job_id" value="<?php echo @$jobId; ?>" id="job_id" />
            <input type="hidden" name="appId" value="<?php echo @$appId; ?>" id="appId" />
            <!--<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">-->
            <div class="mb-5">
              <div class="flex-column">
                <!--begin::Upload Photo & Resume/CV-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4 col-xl-4 text-center">
                    <!--begin::Image input-->
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                      <!--begin::Image preview wrapper-->
                      <div class="image-input-wrapper w-90px h-90px" style="background-image: url(<?php echo @$photo; ?>)"></div>
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
                    <input class="select-file w-100 mb-2 dropzone bg-light " accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume" id="resume">
                    <div class="fv-row"> <span class="form-text text-muted d-block">Allowed file types: Pdf or Word. &nbsp;<?php echo @$resume_url;	 ?></span> </div>
                    <!--end::Input group-->
                  </div>
                </div>
                <!--end::Upload Photo & Resume/CV-->
                <!--begin::Fullname | Designation | Current Company-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Full Name:</label>
                    <input type="text" class="form-control form-control-solid" name="full_name" id="full_name" placeholder="Enter full name" value="<?php echo @$application->full_name; ?>">
                    <span class="form-text text-muted">Please enter your full name</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Designation:</label>
                    <input type="text" class="form-control form-control-solid" name="current_designation" id="current_designation" placeholder="Enter designation" value="<?php echo @$application->current_designation; ?>">
                    <span class="form-text text-muted">Please enter your designation</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Current Company:</label>
                    <input type="text" class="form-control form-control-solid" name="current_company" id="current_company" placeholder="Enter current company" value="<?php echo @$application->current_company ?>">
                    <span class="form-text text-muted">Please enter your current company</span> </div>
                </div>
                <!--end::Fullname | Designation | Current Company-->
                <!--begin::Primary Number | Secondary Number | Date of Birth-->
                <div class="form-group row fv-row mb-10 fv-plugins-icon-container has-danger">
                  <div class="col-lg-4">
                    <label class="form-label required">Primary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="phone" id="phone" placeholder="Enter primary number" value="<?php echo @$application->phone; ?>">
                    <span class="form-text text-muted">Please enter primary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Secondary Number:</label>
                    <input type="number" class="form-control form-control-solid" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary number" value="<?php echo @$application->secondary_mobile; ?>">
                    <span class="form-text text-muted">Please enter secondary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Date of Birth:</label>
                    <input type="date" class="form-control form-control-solid"  name="dob" id="dob" placeholder="Enter dob" value="<?php echo @$application->dob; ?>">
                    <span class="form-text text-muted">Please enter your dob</span> </div>
                </div>
                <!--end::Primary Number | Secondary Number | Date of Birth-->
                <!--begin::Primary Email | Secondary Email | Qualifaction-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Primary Email:</label>
                    <input type="email" class="form-control form-control-solid" name="email" id="email" placeholder="Enter primary number" value="<?php echo @$application->email; ?>">
                    <span class="form-text text-muted">Please enter primary email</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label">Secondary Email:</label>
                    <input type="email" class="form-control form-control-solid" name="secondrary_email" id="secondrary_email" placeholder="Enter secondary number" value="<?php echo @$application->secondrary_email; ?>">
                    <span class="form-text text-muted">Please enter secondary number</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Qualification:</label>
                    <select class="form-select form-select-solid"  name="qualification" id="qualification" aria-label="Select qualification">
                      <option value="" >Select Qualification</option>
                      <?php 
                                    foreach($qualification as $data_value)
                                    {
                                       $sel	=	'';
                                       if(@$application->qualification == $data_value->id ){
                                          $sel	=	'selected="selected"';
                                       }
                                       echo '<option value="'.@$data_value->id.'"  '.@$sel.'>'.@$data_value->name.'</option>';
                                    }	
                                    ?>
                    </select>
                    <span class="form-text text-muted">Please select your qualification</span> </div>
                  <div class="col-lg-4 d-none">
                    <label class="form-label">Minimum Salary</label>
                    <div class="row">
                      <div class="col-3">
                        <select class="form-select form-select-solid" arial-label="Select Currency">
                          <?php 
                                       foreach($currency as $key=>$val)
                                       {
                                          echo '<option value="'.$key.'">'.$val.'</option>';	
                                       }
                                    ?>
                        </select>
                      </div>
                      <div class="col-5">
                        <input type="number" class="form-control form-control-solid" name="min_salary" placeholder="Enter minimum" value="">
                      </div>
                      <div class="col-4">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary">
                          <?php 
                                       foreach($salaryMode as $key=>$val)
                                       {
                                          echo '<option value="'.$key.'">'.$val.'</option>';	
                                       }
                                    ?>
                        </select>
                      </div>
                    </div>
                    <span class="form-text text-muted">Please enter your minimum salary</span> </div>
                </div>
                <!-- end::Primary Email | Secondary Email | Qualifaction -->
                <!--begin::Total Experience | Relevant Experience | Notice Period-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Total Experience:</label>
                    <input type="number" class="form-control form-control-solid" name="total_exp" id="total_exp" placeholder="Enter total experience" value="<?php echo @$application->total_exp; ?>">
                    <span class="form-text text-muted">Please enter total experience</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Relevant Experience:</label>
                    <input type="number" class="form-control form-control-solid" name="rel_exp" id="rel_exp" placeholder="Enter relevant experience" value="<?php echo @$application->rel_exp; ?>">
                    <span class="form-text text-muted">Please enter relevant experience</span> </div>
                  <div class="col-lg-4">
                    <?php //echo 'NP='.@$application->np_id; ?>
                    <?php  
                                 $snp_one	=	$snp_two	= $snp_zero	= "";
                                 $snp_text_one	=	$snp_text_two	= $snp_text_zero	=	'display:none;';		
                                 $snp_mode = @$application->serving_notice_period_mode;
                                 if($snp_mode== 1)
                                 {
                                 	$snp_one		=	'checked="checked"';
                                 	$snp_text_one	=	'display:block';                                 	
                                 }
                                 else if($snp_mode== 2)
                                 {
                                 	$snp_two	=	'checked="checked"';
                                 	$snp_text_one	=	'display:block';
                                 }
                                 else
                                 {
                                 	$snp_zero		=	'checked="checked"';	
                                 	$snp_text_zero	=	'display:block';
                                 }                                 
                                 ?>
                    <label class="form-label fs-7">
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input  class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="0" checked onclick="rbutton(this.value);" <?php echo @$snp_zero; ?> >
                      Notice period </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="1" onclick="rbutton(this.value);" <?php echo @$snp_one; ?> >
                      Serving Notice Period </label>
                    </div>
                    <div class="d-inline-flex">
                      <label class="radio-inline cursor-pointer">
                      <input class="form-check-input h-15px w-15px serving_notice_period mt-0 me-1 cursor-pointer" type="radio" name="serving_notice_period" value="2" onclick="rbutton(this.value);" <?php echo @$snp_two; ?> >
                      Immediate </label>
                    </div>
                    </label>
                    <input type="text" name="np_id" id="np_id" class="form-control form-control-solid np_id" placeholder="" style="<?php echo @$snp_text_one; ?>"  value="<?php echo @$application->np_id; ?>">
                    <input type="number" name="ddl_np_id" id="ddl_np_id" class="form-control form-control-solid" style="<?php echo @$snp_text_zero; ?>" placeholder="Enter Notice Period" value="<?php echo @$application->np_id; ?>" >
                    <input type="text" id="calender" name="calender" style="display:none">
                    <span class="form-text text-muted">Please enter noitce period</span> </div>
                </div>
                <!--end::Total Experience | Relevant Experience | Notice Period-->
                <!--begin::Current CTC | Expected CTC | Percentage-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <label class="form-label required">Current CTC</label>
                    <div class="row">
                      <div class="col-4 pe-0">
                        <select class="form-select form-select-solid select2-candidate-currency" arial-label="Select Currency" id="current_ctc_cur" name="current_ctc_cur" data-control="select2" data-placeholder="Search currency...">
                          <?php 
                                       $current_ctc_cur	=	"";
                                       $current_ctc_cur	=	@$application->current_ctc_cur;
                                       foreach($currency as $key=>$val)
                                       {
                                           $sel	=	'';
                                           if($current_ctc_cur == $key){
                                               $sel	=	'selected="selected"';	
                                           }
                                           $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                           $short = !empty($cMeta['symbol']) ? $cMeta['code'] . ' (' . $cMeta['symbol'] . ')' : $cMeta['code'];
                                           echo '<option value="' . $key . '" data-code="' . e($val) . '" data-short="' . e($short) . '" data-search="' . e($cMeta['keywords']) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';	
                                       }
                                    ?>
                        </select>
                      </div>
                      <div class="col-4 px-2">
                        <input type="number" class="form-control form-control-solid" name="ctc" id="ctc" placeholder="Enter current CTC" value="<?php echo @$application->ctc; ?>">
                      </div>
                      <div class="col-4 pl-0">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" id="current_ctc_mode" name="current_ctc_mode">
                          <?php 
                                       $current_ctc_mode	=	"";
                                       $current_ctc_mode	=	@$application->current_ctc_mode;
                                       foreach($salaryMode as $key=>$val)
                                       {
                                          $sel	=	'';
                                          if($current_ctc_mode == $key){
                                             $sel	=	'selected="selected"';	
                                          }	
                                          echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                       }
                                    ?>
                        </select>
                      </div>
                    </div>
                    <span class="form-text text-muted">Please enter your current ctc</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Expected CTC</label>
                    <?php 
                                 //echo '<pre>'; print_r( @$application); 
                                 ?>
                    <div class="row">
                      <div class="col-4 pe-0">
                        <select class="form-select form-select-solid select2-candidate-currency" arial-label="Select Currency" name="current_ectc_cur" id="current_ectc_cur" data-control="select2" data-placeholder="Search currency...">
                          <?php 
                                       $current_ectc_cur	=	"";
                                       $current_ectc_cur	=	@$application->current_ectc_cur;
                                       foreach($currency as $key=>$val)
                                       {
                                          $sel	=	'';
                                          if($current_ectc_cur == $key){
                                             $sel	=	'selected="selected"';	
                                          }
                                          $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($val);
                                          $short = !empty($cMeta['symbol']) ? $cMeta['code'] . ' (' . $cMeta['symbol'] . ')' : $cMeta['code'];
                                          echo '<option value="' . $key . '" data-code="' . e($val) . '" data-short="' . e($short) . '" data-search="' . e($cMeta['keywords']) . '" ' . $sel . '>' . e($cMeta['label']) . '</option>';	
                                       }
                                    ?>
                        </select>
                      </div>
                      <div class="col-4 px-2">
                        <input type="number" class="form-control form-control-solid" name="ectc" id="ectc" placeholder="Enter expected CTC" value="<?php echo @$application->ectc; ?>" >
                      </div>
                      <div class="col-4 pl-0">
                        <select class="form-select form-select-solid" arial-label="Select Annual Salary" name="current_ectc_mode" id="current_ectc_mode">
                          <?php 
                                       $current_ectc_mode	=	"";
                                       $current_ectc_mode	=	@$application->current_ectc_mode;
                                       foreach($salaryMode as $key=>$val)
                                       {
                                          $sel	=	'';
                                          if($current_ectc_mode == $key){
                                             $sel	=	'selected="selected"';	
                                          }
                                       echo '<option value="'.$key.'"  '.$sel.'>'.$val.'</option>';	
                                       }
                                    ?>
                        </select>
                      </div>
                      <span class="form-text text-muted">Please enter your expected ctc</span> </div>
                  </div>
                  <div class="col-lg-4">
                    <label class="form-label required">Percentage</label>
                    <div class="row">
                      <div class="col-4 pe-0">
                        <input type="number" class="form-control form-control-solid" name="percentage_hike" id="percentage_hike" placeholder="Enter hike" value="<?php echo @$application->percentage_hike; ?>">
                        <span class="form-text text-muted">Hike in percentage</span> </div>
                      <div class="col-8">
                        <input type="text" class="form-control form-control-solid" name="comment" id="comment" placeholder="Enter comment" value="<?php echo @$application->comment; ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <!--end::Current CTC | Expected CTC | Percentage-->
                <!--begin::Country | State | City | Pincode-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-4">
                    <?php
                                 $country			=	'';
                                 $country			=	@$application->country;

                                 $display_city		=	'';
                                 $display_city_id	= 'style="display:none"';		
                                 if($country == 101){
                                    $display_city_id	=	'';
                                    $display_city		=	'style="display:none"';	
                                       
                                 }
                                 else{
                                    $display_city_id	= 'style="display:none"';			
                                 }
                                                            
                                 ?>
                    <label class="form-label required">Country</label>
                    <select class="form-select form-select-solid" arial-label="Select Currency" name="country" id="country" >
                      <option value="">Select Country</option>
                      <?php 
                                    foreach($countries as $countryVal){

                                       $sel			=	'';
                                       if($country != ''){
                                          if($countryVal['id'] == $country ){
                                             $sel	=	'selected="selected"';	
                                          }	
                                       }
                                       else
                                       {
                                          //if($countryVal['id'] == 101 ){
                                          //	$sel	=	'selected="selected"';	
                                          //}
                                       }																				
                                       echo '<option value="'.@$countryVal['id'].'"  '.@$sel.' >'.@$countryVal['name'].'</option>';	
                                    }
                                    ?>
                    </select>
                    <span class="form-text text-muted">Please enter your Country</span> </div>
                  <div class="col-lg-4">
                    <label class="form-label required">State</label>
                    <!--<select class="select2 states form-select form-select-solid" name="state" id="stateId">
                                 <option value="0">Select State</option>
                                 </select> -->
                    <select class="form-select form-select-solid change_state" arial-label="Select Currency" name="state" id="state">
                      <option value="">Select State</option>
                      <?php 
                                    $sel		=	'';
                                    $state		=	'';
                                    $state		=	@$application->state;
                                    if(!empty($statelist))
                                    {
                                          foreach($statelist as $key=>$sVal){
                                             $sel	=	'';
                                             if($state == @$sVal['id'] ){
                                                $sel	=	'selected="selected"';	
                                             }
                                             echo '<option value="'.@$sVal['id'].'"  '.@$sel.' >'.@$sVal['name'].'</option>';	
                                          }
                                    }
                                 ?>
                    </select>
                    <span class="form-text text-muted">Please enter your State</span> </div>
                  <div class="col-lg-4">
                    <div class="row">
                      <div class="col-6 pe-0">
                        <label class="form-label required">City</label>
                        <input type="text" class="form-control form-control-solid" name="city" id="city" placeholder="Enter city" value="<?php echo @$application->city; ?>"  <?php echo @$display_city; ?> >
                        <select class="form-select form-control form-control-lg form-control-solid" name="city_id" id="city_id" <?php echo @$display_city_id; ?> >
                          <option value="">Select City</option>
                          <?php 
                                          $sel		=	'';
                                          //$state		=	'';
                                          //$state		=	@$application->state;
                                          if(!empty($cityListArr)){
                                             foreach($cityListArr as $key=>$sVal){
                                                $sel	=	'';
                                                if($application->city_id == @$sVal['id'] ){
                                                   $sel	=	'selected="selected"';	
                                                }
                                                echo '<option value="'.@$sVal['id'].'"  '.@$sel.' >'.@$sVal['name'].'</option>';	
                                             }
                                          }
                                       ?>
                        </select>
                        <span class="form-text text-muted">Please enter your city</span> </div>
                      <div class="col-6">
                        <label class="form-label required">Pincode</label>
                        <input type="number" class="form-control form-control-solid" name="pincode" id="pincode" placeholder="Enter pincode" value="<?php echo @$application->pincode; ?>">
                        <span class="form-text text-muted">Please enter your pincode</span> </div>
                    </div>
                  </div>
                </div>
                <!--end::Country | State | City | Pincode-->
                <!--begin::Skills-->
                <div class="form-group row fv-row mb-10">
                  <div class="col-lg-12">
                    <script>
                                 // The DOM element you wish to replace with Tagify
                                 var input = document.querySelector('input[name=skills]'); 
                                 // initialize Tagify on the above input node reference
                                 new Tagify(input)
                                       function isNumberKey(evt)
                                       {
                                          var charCode = (evt.which) ? evt.which : evt.keyCode
                                          if (charCode > 31 && (charCode < 48 || charCode > 57))
                                             return false;
                                          return true;
                                       }
                              </script>
                    <label class="form-label required">Skills</label>
                    <!-- <input class="form-control mb-3" value="tag1, tag2, tag3" id="kt_tagify_4"/> -->
                    <!-- <input class="form-control form-control-sm form-control-solid js-tagify value_JSON" value="<?php echo @$application->
                    skills; ?>" id="kt_tagify_1111" name="skills" /> -->
                    <input class="form-control form-control-sm form-control-solid js-tagify value_JSON" value="<?php echo @$application->skills; ?>" placeholder="Enter skills" id="kt_tagify_2" name="skills" />
                    <span class="form-text text-muted">Please enter your skills</span> </div>
                </div>
                <!--end::Skills-->
                <!--begin::Custom Details-->
                <div class="dashed-border-top" id="job_additional_detail">
                  <?php 
                              if(!empty($job_additional_detail)){
                              
                              	$customTitleId	=	1;
                              	$html		=	'';
                              	$html		=	'<h3 class="py-5">Custom Details</h3>';
                              	$inc_first	=	0;
                              	$inc_last	=	1;
                              	foreach($job_additional_detail as $aDetailArr)
                              	{
                              		
                              		$ans_value	=	'';
                              		if(!empty($application_additional_detail)){
                              			foreach($application_additional_detail as $aval){
                              					if($aDetailArr->id == $aval->question_id){
                              						$ans_value	=	@$aval->application_detail_value;	
                              					}
                              			}
                              		}
                              		
                              		
                              		if($inc_first %3 == 0) 
                              		{
                              			$html	.=	'<div class="form-group row fv-row mb-10">';	
                              		}	
                              		$html		.=	'<div class="col-md-4">
                              						   <label class="form-label">'.strtoupper($aDetailArr->detail_value).':</label>
                              						   <input type="hidden" name="job_question[]" value="'.$aDetailArr->id.'">
                              						   <input type="text" class="form-control form-control-solid" name="additional_detail[]" placeholder="Enter detail" value="'.@$ans_value.'">
                              						   <span class="text-muted form-text">Please enter your details</span>
                              						</div>';
                              						 
                              		if(($inc_last %3 == 0)&& $inc_last > 0){
                              			$html		.='</div>';
                              		}
                              		$inc_first++;
                              		$inc_last++;
                              		$customTitleId++;				
                              	}
                              	echo $html;	
                              }	
                              
                              //echo '<pre>'; print_r($statelist); exit; 				
                              ?>
                </div>
                <!--end::Custom Details-->
                <!--begin::Buttons-->
                <div class="form-row row fv-row">
                  <div class="col-12 text-center py-10">
                    <?php 
                                 if($page_event == 'EDIT')
                                 { 
                                 	echo '<button type="button" name="update" id="update-form" value="update" class="btn btn-primary">Update</button>';
                                 }
                                 else{
                                 	echo '<button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit</button>';	
                                 }
                                 ?>
                    <a href="javascript:void(0);" class="btn btn-secondary">Cancel</a> </div>
                </div>
                <!--end::Buttons-->
              </div>
            </div>
          </form>
          <!--end::Form-->
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<!--<div class="mb-10">
    <label class="form-label">Default input style</label>
    <input class="form-control" value="tag1, tag2, tag3" id="kt_tagify_1"/>
</div>

<div class="mb-0">
    <label class="form-label">Solid background style</label>
    <input class="form-control form-control-solid" value="tag1, tag2, tag3" id="kt_tagify_2"/>
</div>-->
@endsection


@push('footer-script')
<script>

// The DOM elements you wish to replace with Tagify
var input1 = document.querySelector("#kt_tagify_1");
var input2 = document.querySelector("#kt_tagify_2");

// Initialize Tagify components on the above inputs
new Tagify(input1);
new Tagify(input2);


</script>
<
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/3.14.3/tagify.css">
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('front/assets/js/location.js') }}"></script>
<!-- <script src="{{ asset('js/jquery.validate.js') }}"></script> -->
<!--begin::Validation-->
<?php if($page_event == 'EDIT'){ ?>
<script src="{{ asset('demo/v7/js/edit-applicant.js') }}"></script>
<?php 	}else{	?>
<script src="{{ asset('demo/v7/js/add-applicant.js') }}"></script>
<?php }	?>
<!--end::Validation-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
   new Tagify(document.querySelector('#skills'),{
       tagTextProp	: "name",
       duplicates	: true,
       whitelist	: [{"value":"123","name":"Bahamas"}]
   });
</script>
<script>
   $.noConflict();
   
   var input1 = document.querySelector("#kt_tagify_1");
   //var input2 = document.querySelector("#kt_tagify_2");
   
   // Initialize Tagify components on the above inputs   
   //new Tagify(input1);
   new Tagify(input2);
</script>
<!--end::Old Tagify-->
<script>
   const fetchCountryState 	=	"{{ route('jobs.fetchCountryState') }}";
   const csrfToken 			=	"{{ csrf_token() }}";
   const selectCountry 		=	"@lang('modules.front.selectCountry')";
   const selectState 		=	"@lang('modules.front.selectState')";
   const selectCity 		=	"@lang('modules.front.selectCity')";
   const pleaseWait 		=	"@lang('modules.front.pleaseWait')";
   
   let country = "";
   let state = "";
</script>
<script>
   function rbutton(cvalue)
   {
		//alert(cvalue); 
		$('#np_id').val('');
		$('#ddl_np_id').val('');
		if(cvalue == 0){
			$('#ddl_np_id').show();
			$('#np_id').hide();		
		}else if(cvalue==1){
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$('#np_id').attr("disabled", false);	
			
					 $("#np_id").attr('type','date');
					
		}else if(cvalue ==2){
			$('#ddl_np_id').hide();
			$('#np_id').show();		
			$("#np_id").attr('type','text');
			$('#np_id').attr("disabled", true);
			$('#np_id').val('Immediate');
		}	
   }
   
   
   $( document ).ready(function() {
   	
   $( ".getAdditionalDetail" ).change(function() 
   {	
   
   					var job_id       	=   $(this).val();			
   					var url 			=	"{{ route('staffing_company.job-applications.getAdditionalDetail',':job_id') }}";
   					url 				=	url.replace(':job_id', job_id);
   		
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {
   																		if (response.status == "success") 
   																		{
   																			$('#job_additional_detail').html(response.view);
   																		} else { 
   																			$('#job_additional_detail').hide();                           
   																		}
   																   }		
   										   
   					});
     
   							
   }).change();
   
   
   $('#country').change(function(){
   		
   					//alert("call");					
   				var cid				=	$(this).val() ;

   			
				if(cid == 101){
					$('#city_id').show();	
					$('#city').hide();	
				}else{
					$('#city').show();	
					$('#city_id').hide();	
				}
				
   					var url 			=	"{{ route('staffing_company.job-applications.statelist',':cid') }}";
   					url 				=	url.replace(':cid', cid);
   					$.ajax({
   									type	: 'GET',
   									url		: url,
   									success	: function (response) {																		
   																		$('#state').html(response.data);
   																		
   																   }		
   										   
   					});
   		});
    		
   });
   
   var datepicker = $('.dob').datepicker({
         autoclose: true,
         format: 'yyyy-mm-dd',
         endDate: (new Date()).toDateString(),
     });
     
   $('.select2').select2({
         width: '100%'
     });
    
   
   var val = $('#job_id').val(); // get Current Selected Job
   if (val != '' && typeof(val) !== 'undefined') {
     getQuestions(val); // get Questions by question on page load            
   }
   
   function getQuestions(id) {
         var url = "{{ route('staffing_company.job-applications.question',':id') }}";
         url = url.replace(':id', id);
   
         $.ajax({
             type: 'GET',
             url: url,
             success: function (response) {
                 if (response.status == "success") {
                     if (response.count > 0) { // Question Found for selected job
                         $('#questionBox').show();
                         $('#questionBoxTitle').show();
                         $('#questionBox').html(response.view);
                     } else { // Question Not Found for selected job
                         $('#questionBox').hide();
                         $('#questionBoxTitle').hide();
                     }
                     $('#show-columns').html(response.requiredColumnsView);
                     $('#show-sections').html(response.requiredSectionsView);
                     if(response.requiredColumnsView !== '') {
                         var datepicker = $('.dob').datepicker({
                             autoclose: true,
                             format: 'yyyy-mm-dd',
                             endDate: (new Date()).toDateString(),
                         });
                         
                         $('.select2').select2({
                             width: '100%'
                         });
   
                         var loc = new locationInfo()
                         loc.getCountries()
                     }
                 }
             }
         });
     }
   
   function handleFails(response) {
   
         if (typeof response.responseJSON.errors != "undefined") {
             var keys = Object.keys(response.responseJSON.errors);
             console.log(keys);
             $('#createForm').find(".has-error").find(".help-block").remove();
             $('#createForm').find(".has-error").removeClass("has-error");
   
             for (var i = 0; i < keys.length; i++) {
                 // Escape dot that comes with error in array fields
                 var key = keys[i].replace(".", '\\.');
                 var formarray = keys[i];
   
                 // If the response has form array
                 if (formarray.indexOf('.') > 0) {
                     var array = formarray.split('.');
                     response.responseJSON.errors[keys[i]] = response.responseJSON.errors[keys[i]];
                     key = array[0] + '[' + array[1] + ']';
                 }
   
                 var ele = $('#createForm').find("[name='" + key + "']");
   
                 var grp = ele.closest(".form-group");
                 $(grp).find(".help-block").remove();
   
                 //check if wysihtml5 editor exist
                 var wys = $(grp).find(".wysihtml5-toolbar").length;
   
                 if (wys > 0) {
                     var helpBlockContainer = $(grp);
                 } else {
                     var helpBlockContainer = $(grp).find("div:first");
                 }
                 if ($(ele).is(':radio')) {
                     helpBlockContainer = $(grp);
                 }
   
                 if (helpBlockContainer.length == 0) {
                     helpBlockContainer = $(grp);
                 }
   
                 helpBlockContainer.append('<div class="help-block">' + response.responseJSON.errors[keys[i]] + '</div>');
                 $(grp).addClass("has-error");
             }
   
             if (keys.length > 0) {
                 var element = $("[name='" + keys[0] + "']");
                 if (element.length > 0) {
                     $("html, body").animate({scrollTop: element.offset().top - 150}, 200);
                 }
             }
         }
     }
   
   $( document ).ready(function() {
   
   
   /*$(".serving_notice_period").click(function(){
       var selected = $(".serving_notice_period:checked");
       if(!selected.val()){
           alert('No color selected!')
       }
       else{
           var selectedValue = selected.val();
           var selectedName = selected.siblings().text();
           alert("Color: " + selectedName + "\r\nValue: " + selectedValue );
       }
   });*/
   
   
   
   $("#calender").datepicker({
   
   	//alert("call me");
    onSelect: function(dateText, inst) {
    		
   		 $('#np_id').show(); 
    		$('#np_id').val(dateText);
       }
   	
   });
   
   $('#datepicker').focus(function(){
   	$('#datepicker').datepicker('show');
   });
   	
   
   
   	
   });
   
</script>
<script>
   var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
       url: "https://keenthemes.com/scripts/void.php", // Set the url for your upload script location
       paramName: "file", // The name that will be used to transfer the file
       maxFiles: 1,
       maxFilesize: 1, // MB
       addRemoveLinks: true,
       accept: function(file, done) {
           if (file.name == "wow.jpg") {
               done("Naha, you don't.");
           } else {
               done();
           }
       }
   });
   
</script>
<?php 
   if($page_event == 'EDIT'){ 
   ?>
<script>
   $('#update-form').click(function () {
      
   							
   		  var formData 					=	new FormData($('#createForm')[0]);					
          $.ajax({
              		  url				: '{{route('staffing_company.job-applications.update', @$application->id)}}'+'?_token=' + '{{ csrf_token() }}',	
   					  type				: "POST",
   					  data 				: formData,
   					  processData		: false,  	// tell jQuery not to process the data
   					  contentType		: false,  	// tell jQuery not to set contentType
   					  //container		: '#createForm',
   					 
   					  redirect			: true,
   					  file				: true,
   					  //data			: $('#createForm').serialize(),
   					  success			: function (response) {
   					  							window.location = response.url;
   					  						},
              			error			: function (response) {
                  										handleFails(response);
              										}
          		})
      });
</script>
<?php 
   }
?>
<script>
   $(document).ready(function(){
   
   		$('#save-form').click(function () {
   	
   	
   				//alert("call"); 
   				
   				 /* $.ajax({
   								  url			: '{{route('staffing_company.job-applications.store')}}',
   								  container		: '#createForm',
   								  type			: "POST",
   								  redirect		: true,
   								  file			: true,
   								  data			: $('#createForm').serialize(),
   								  success		: function (response) {
   														//window.location = response.url;
   												},
   								  error			: function (response) {
   																			handleFails(response);
   												  }
   						})*/
   						
   						
   						//var formData = new FormData();
   						var formData = new FormData($('#createForm')[0]);							
   						$.ajax({
   								   url 				: '{{route('staffing_company.job-applications.store')}}'+'?_token=' + '{{ csrf_token() }}',								   
   								   type 			: 'POST',
   								   data 			: formData,
   								   processData		: false,  	// tell jQuery not to process the data
   								   contentType		: false,  	// tell jQuery not to set contentType
   								   redirect			: true,
   								    success			: function (response) {
   														//alert(response); 
   														window.location = response.url;
   													},
   								  	error			: function (response) {
   																			//handleFails(response);
   												  	}
   							});
   
   
     });
     
});
   
</script>
<script>
	$(document).on('change', '.change_state', function() { 
   				
				var country				=	$('#country').val(); 
   				var state_id			=	this.value;				
   								
				if(country == 101){
					$('#city_id').show();	
					$('#city').hide();	
				}else{
					$('#city').show();	
					$('#city_id').hide();	
				}
								
				var url 			=	"{{ route('staffing_company.clients.citylist',':state_id') }}";
				url 				=	url.replace(':state_id', state_id);
				$.ajax({
								type	: 'GET',
								url		: url,
								success	: function (response) {		
																	$('#city_id').html(response.data);
															   }		
									   
				});	
   								
   	});
		
   function initStaffingCandidateCurrencySelect2() {
       if (window.jQuery && $.fn.select2) {
           $('.select2-candidate-currency').each(function() {
               var $el = $(this);
               if ($el.hasClass('select2-hidden-accessible')) {
                   $el.select2('destroy');
               }
               $el.select2({
                   width: '100%',
                   dropdownParent: $(document.body),
                   dropdownCssClass: 'br-currency-select2-dropdown',
                   placeholder: 'Search currency...',
                   minimumResultsForSearch: 0,
                   templateSelection: function(data) {
                       if (!data.id) return data.text;
                       var $opt = $(data.element);
                       var short = $opt.data('short') || $opt.data('code');
                       return short ? short : data.text;
                   },
                   matcher: function (params, data) {
                       if ($.trim(params.term) === '') {
                           return data;
                       }
                       if (typeof data.text === 'undefined') {
                           return null;
                       }
                       var term = params.term.toLowerCase();
                       var text = data.text.toLowerCase();
                       var $opt = $(data.element);
                       var code = ($opt.data('code') || '').toString().toLowerCase();
                       var searchKw = ($opt.data('search') || '').toString().toLowerCase();

                       if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || searchKw.indexOf(term) > -1) {
                           return data;
                       }
                       return null;
                   }
               });
           });
       }
   }

   $(document).ready(function() {
       initStaffingCandidateCurrencySelect2();
   });

   $('body').on('change', '#current_ctc_cur', function(){
       var val = $(this).val();
       if ($('#current_ectc_cur').val() !== val) {
           $('#current_ectc_cur').val(val).trigger('change.select2');
       }
   });
   $('body').on('change', '#current_ectc_cur', function(){
       var val = $(this).val();
       if ($('#current_ctc_cur').val() !== val) {
           $('#current_ctc_cur').val(val).trigger('change.select2');
       }
   });
   $('body').on('change', '#current_ctc_mode', function(){
   			var	current_ctc_mode	=	$(this).val();
			document.getElementById("current_ectc_mode").selectedIndex = current_ctc_mode;
   });
   $('body').on('change', '#current_ectc_mode', function(){
   			var	current_ectc_mode	=	$(this).val();
			document.getElementById("current_ctc_mode").selectedIndex = current_ectc_mode;
   });
   	
</script>
@endpush