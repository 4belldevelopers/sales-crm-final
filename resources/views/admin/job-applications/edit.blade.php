@extends('layouts.app')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
@endpush
<style>
.content-header
{
   display: none;
}
.form-group label, label
{
   margin-top: .5em!important;
   font-weight: 600!important;
   text-transform: capitalize;
   font-size: 13px;
   /*padding-top: .9em;*/
   /* padding-bottom: .2em; */
}
#btnFiterSubmitSearch
{
   padding:0.5rem 0.20rem!important;
}
.content-wrapper
{
  min-height: auto!important;
}
</style>
@section('content')
@php
$gender = [
'male' => __('modules.front.male'),
'female' => __('modules.front.female'),
'others' => __('modules.front.others')
];
@endphp
<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title pt-2 pb-2"> <i class="fa fa-edit"></i>&nbsp;@lang('app.edit')&nbsp;Applicant</h4>
            <form class="ajax-form" method="POST" id="editForm">
               @csrf
               @method('PUT')
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label class="control-label">Openings</label>
                        </h5>
                        <select name="job_id" id="job_id" onchange="getQuestions(this.value)" class="select2 form-control getAdditionalDetail">
                        @foreach($jobs as $job)
                        <option value="{{ $job->id }}" @if ($application->job_id === $job->id) selected @endif>{{ ucwords($job->title).' - ' }}{{ $job->active ? 'Active' : 'Deactive' }}</option>
                        @endforeach
                        </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label>@lang('modules.front.resume')</label>
                        </h5>
                        <input class="select-file w-100 mb-2" accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume">
                     </div>
                     @if (!empty($application) && $application->resume_url) 
                     <a target="_blank" href="{{ $application->resume_url }}" class="btn btn-sm btn-light-primary px-1 py-0">
                        <!-- <i class="fa fa-file"></i>&nbsp;@lang('app.view') -->
                        <i class="fa fa-download"></i>&nbsp;@lang('app.download') 
                     </a>
                     &nbsp;| <span class="text-primary">@lang('modules.front.resumeFileType')</span> @endif 
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5 class="font-weight-bolder">
                           <label for="full_name">Full Name</label>
                        </h5>
                        <input class="form-control" type="text" value="{{ $application->full_name }}" name="full_name" placeholder="@lang('app.name')">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h5>
                        <label for="total_exp">Total Experience</label>
                     </h5>
                     <div class="form-group">
                        @php $yearText = "Year"; @endphp
                        <select class="form-control"  name="total_exp" id="total_exp" >
                           <option value=""  disabled selected >Total Exp</option>
                           <option value="0">Freshers</option>
                           @for($i=1; $i<=30; $i++)
                           @if($i > 9)
                           $yearText = "Years";
                           @endif
                           <option value="{{ $i}}" @if( $application->total_exp == $i ) selected @endif  >{{ $i}} &nbsp; {{$yearText}}</option>
                           @endfor 
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="email">Primary Email Address</label>
                        </h5>
                        <input class="form-control" type="email" value="{{ $application->email }}" name="email" id="email" placeholder="@lang('app.email')" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                        <div id="email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h5>
                        <label for="rel_exp">Relevant Experience</label>
                     </h5>
                     <div class="form-group">
                        @php $yearText = "Year"; @endphp
                        <select class="form-control"  name="rel_exp" id="rel_exp">
                           <option value="" disabled selected >Rel. Exp</option>
                           <option value="0">Freshers</option>
                           @for($i=1; $i<=30; $i++)
                           @if($i > 9)
                           $yearText = "Years";
                           @endif
                           <option value="{{ $i}}"  @if( $application->rel_exp == $i ) selected @endif  >{{ $i}} &nbsp; &nbsp; {{$yearText}}</option>
                           @endfor                    
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="secondrary_email">Secondary Email Address</label>
                        </h5>
                        <input class="form-control" type="email" name="secondrary_email" id="secondrary_email" value="{{ $application->secondrary_email }}" placeholder="@lang('app.email')" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                        <div id="secondrary_email_typo_hint" class="email-typo-hint-container mt-1" style="display: none; font-size: 11px;"></div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="qualification">Education/Qualification</label>
                        </h5>
                        <select class="form-control m-b-10"  name="qualification" id="qualification">
                           <option value="">Select Qualification</option>
                           <?php 
                              foreach($qualification as $data_value)
                              {
                                $sel = '';
                                if($application->qualification == $data_value->id){
                                  $sel = 'selected="selected"';
                                }
                              ?>
                           <option value="<?php echo $data_value->id ?>"  <?php echo @$sel; ?> ><?php echo $data_value->name ?></option>
                           <?php  } ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="phone">Primary Phone Number</label>
                        </h5>
                        <input class="form-control" type="text" name="phone" id="phone" value="{{ $application->phone }}" placeholder="@lang('app.phone')">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <?php 
                        $serving_notice_period_0 = $serving_notice_period_1 = $serving_notice_period_2 = ''; 
                        if($application->serving_notice_period_mode ==0){
                        $serving_notice_period_0 =  'checked="checked"';
                        }else if($application->serving_notice_period_mode ==1){
                        $serving_notice_period_1 =  'checked="checked"';
                        }else if($application->serving_notice_period_mode ==2){
                        $serving_notice_period_2 =  'checked="checked"';
                        }
                        ?>
                     <h5>
                        <label>
                        <input type="hidden" name="hid_np" id="hid_np" value="{{ $application->np_id }}" />
                        <input name="serving_notice_period" type="radio" value="0" class="serving_notice_period" <?php echo @$serving_notice_period_0; ?> />
                        &nbsp;Notice Period In Days
                        <input name="serving_notice_period" type="radio" value="1" class="serving_notice_period"  <?php echo @$serving_notice_period_1; ?> />
                        &nbsp;Serving notice period
                        <input name="serving_notice_period" type="radio" value="2" class="serving_notice_period" <?php echo @$serving_notice_period_2; ?> />
                        &nbsp;Immediate available
                        <input type="text" id="calender" name="calender" style="display:none">
                        </label>
                     </h5>
                     <div class="form-group">
                        <!--<input type="text" name="np_id" id="np_id" class="form-control" value="">-->
                        <input type="text" name="np_id" id="np_id" class="form-control" value="{{ $application->np_id }}" placeholder="" style="display:none ">
                        <input type="number" name="ddl_np_id" id="ddl_np_id" value="{{ $application->np_id }}" class="form-control" placeholder="" >
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="secondary_mobile">Secondary Phone Number</label>
                        </h5>
                        <input class="form-control" type="text" name="secondary_mobile" id="secondary_mobile" value="{{ $application->secondary_mobile }}" placeholder="@lang('app.phone')">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h5>
                        <label>Passport</label>
                     </h5>
                     <div class="form-group">
                        <input name="passport" type="radio" value="Y" />
                        Yes
                        <input name="passport" type="radio" value="N" checked="checked" />
                        No 
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="ctc">Current CTC</label>
                        </h5>
                        <input class="form-control" type="number" name="ctc" id="ctc" placeholder="CTC" value="{{ $application->ctc }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="ectc">@lang('modules.front.dob')</label>
                        <input class="form-control form-control-lg dob mt-1" type="text" name="dob"
                           placeholder="@lang('modules.front.dob')" autocomplete="none">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label for="ectc">Expected CTC</label>
                        </h5>
                        <input class="form-control" type="number" name="ectc" id="ectc" placeholder="ECTC" value="{{ $application->ectc }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h5>
                        <label for="ectc">Country</label>
                     </h5>
                     <div class="form-group">
                        <select class="select2 countries" name="country" id="countryId">
                           <option value="0">Select Country</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label>Current Location</label>
                        </h5>
                        <input class="form-control" type="text" name="current_location" id="current_location" placeholder="Current Location" value="{{ $application->current_location }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h5>
                        <label for="ectc">State</label>
                     </h5>
                     <div class="form-group">
                        <select class="select2 states" name="state" id="stateId">
                           <option value="0">Select State</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <h5>
                        <label>Current Company</label>
                     </h5>
                     <div class="form-group">
                        <input class="form-control" type="text" name="current_company" id="current_company" placeholder="Current Company" value="{{ $application->current_company }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <h5>
                        <label for="status">@lang('app.status')</label>
                     </h5>
                     <div class="form-group">
                        <select name="status_id" id="status_id" class="form-control">							
                           <?php 
                              foreach($interviewStatus as $status)
                              {
                                $sel  = '';
                                if($application->master_status_id == @$status->id){
                                    $sel  = 'selected="selected"';
                                }                       
                              ?>
                           <option value="<?php echo @$status->id; ?>" <?php echo $sel; ?> ><?php echo @$status->name; ?></option>
                           <?php 
                              }
                              ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <h5>
                           <label>Current Designation</label>
                        </h5>
                        <input class="form-control" type="text" name="current_designation" id="current_designation" placeholder="Current Designation" value="{{ $application->current_designation }}">
                     </div>
                  </div>
                  <div class="col-md-6"></div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <h5>
                        <label>Skills</label>
                     </h5>
                     <div class="form-group">
                        <input class="form-control" type="text" name="skills" id="skills" placeholder="Enter skills" value="{{ $application->skills }}">
                     </div>
                  </div>
                  <div class="col-md-6"></div>
               </div>
               <div class="row">
                  @if(count($jobQuestion) > 0)
                  <div class="col-md-4 pl-4 pr-4 pt-4 b-b" id="questionBoxTitle">
                     <h5>@lang('modules.front.additionalDetails')</h5>
                  </div>
                  <div class="col-md-6" id="questionBox"> </div>
                  @endif 
               </div>
               <!-- Additional details -->
               <div class="row pt-3 mb-2" id="job_additional_detail">
                  <?php 
                     //echo '<pre>'; print_r($application_add_detail); 
                     
                     if($application_add_detail_cnt > 0)
                     {
                     ?>     
                  <div class="col-12">
                     <h4 class="text-dark border-bottom p-2 bg-light-secondary"> <i class="fa fa-info-circle"></i>&nbsp;Additional Details </h4>
                  </div>
                  <div class="col-sm-12">
                     <div class="form-group">
                        <div class="row mx-0 d-flex justify-content-right">
                           <?php 
                              $i=1; 
                              $j=0;
                              foreach($application_add_detail as $aad)
                              {
                              ?>
                           <div class="boxs width-32 mr-2">   
                              <label class="detail1"><?php echo @$aad->job_detail_value; ?></label>
                              <input type="text" name="job_additional_detail[<?php echo $j; ?>]" class="form-control d-block border p-1 font-weight-400 w-100 text-dark detail1" placeholder="Enter Additional Details - <?php echo $i;?>" value="<?php echo @$aad->application_detail_value; ?>" >
                           </div>   
                           <?php 
                              $i++; $j++;
                              }
                              ?>
                        </div>               
                     </div>
                  </div>
                  <?php 
                     } 
                     ?>
               </div>
               <!-- Additional details end -->      
               <div class="row py-4">
                  <div class="col-12 text-center">
                     <button type="button" id="save-form" class="btn btn-light-primary btn-sm">
                        <i class="fa fa-save"></i>&nbsp;Save & Submit
                     </button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<?php 
   $serving_notice_period_0 = $serving_notice_period_1 = $serving_notice_period_2 = ''; 
   if($application->serving_notice_period_mode ==0){
   $serving_notice_period_0 = 'checked="checked"';
   }else if($application->serving_notice_period_mode ==1){
   $serving_notice_period_1 = 'checked="checked"';
   }else if($application->serving_notice_period_mode ==2){
   $serving_notice_period_2 = 'checked="checked"';
   }
   ?>
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script>
   $( document ).ready(function() {
   
      
      $( ".getAdditionalDetail" ).change(function() 
      {
        
                  var job_id        =   $(this).val();      
                  var url       = "{{ route('admin.job-applications.getAdditionalDetail',':job_id') }}";
                  url         = url.replace(':job_id', job_id);
            
                  $.easyAjax({
                          type  : 'GET',
                          url   : url,
                          success : function (response) {
                                            if (response.status == "success") 
                                            {
                                              $('#job_additional_detail').html(response.view);
                                            } else { 
                                              $('#job_additional_detail').hide();                           
                                            }
                                           }    
                               
                  });
          
                      
      });
   
   });
   
</script> 
<script>
   const fetchCountryState = "{{ route('jobs.fetchCountryState') }}";
   const csrfToken = "{{ csrf_token() }}";
   const selectCountry = "@lang('modules.front.selectCountry')";
   const selectState = "@lang('modules.front.selectState')";
   const selectCity = "@lang('modules.front.selectCity')";
   const pleaseWait = "@lang('modules.front.pleaseWait')";
   
   let country = "{{ $application->country }}";
   let state = "{{ $application->state }}";
</script>
<script src="{{ asset('front/assets/js/location.js') }}"></script>
<script>
   $( document ).ready(function() {
   
   var npbtn = {{$application->serving_notice_period_mode}};
   if(npbtn == 2)
   {
   $('#np_id').show();
   $('#ddl_np_id').hide();
   
   $('#np_id').val('Immediate');
   $('#np_id').attr("disabled", true);
   }
   else if(npbtn == 1)
   {
   $('#np_id').show();
   $('#ddl_np_id').hide();
   
   $("#calender").show().focus().hide();
   $('#np_id').attr("disabled", true);
   
   }
   else
   {
   $('#np_id').hide();
   $('#ddl_np_id').show();
   
   $('#np_id').val('');
   $('#np_id').attr("disabled", false);
   
   }
   
   $('.serving_notice_period').click(function() {
   
   if ($(".serving_notice_period").is(":checked")) {
   
   $('#np_id').val('');
   var selectedOption = this.value;
   
   if(selectedOption == 2)
   {
   $('#np_id').show();
   $('#ddl_np_id').hide();
   
   $('#np_id').val('Immediate');
   $('#np_id').attr("disabled", true);
   }
   else if(selectedOption == 1)
   {
   $('#np_id').show();
   $('#ddl_np_id').hide();
   
   $("#calender").show().focus().hide();
   $('#np_id').attr("disabled", true);
   
   }
   else
   {
   $('#np_id').hide();
   $('#ddl_np_id').show();
   
   $('#np_id').val('');
   $('#np_id').attr("disabled", false);
   
   }
   
   }
   });
   
   
   $("#calender").datepicker({
   onSelect: function(dateText, inst) {
   $('#np_id').val(dateText);
   //alert(dateText);
   }
   
   });
   
   $('#datepicker').focus(function(){
   $('#datepicker').datepicker('show');
   });
   
   
   // console.log( "ready!" );
   $('#serving_notice_period').click(function(){
   if ($("#serving_notice_period").is(":checked")) {
   //$("#calender").show();
   //$("#calender").datepicker("show");
   $("#calender").show().focus().hide();
   //alert("check");
   
   }else{
   $("#calender").hide();
   //$('#np_id').val(''); 
   }
   
   });
   
   });
   
   
   $("#createForm").validate({
   
   onfocusout: function(e) {
      this.element(e);
   },
   
   rules: {
      
      
      full_name   : "required",
      qualification : "required",
      total_exp   : "required",
      rel_exp     : "required",
      email: {
              required: true,
              email: true
            },
      phone: {
              required: true,
              number: true,
                  minlength:10,
                  maxlength:10
              
            },
      
      secondary_mobile: {
              
              number: true,
                  minlength:10,
                  maxlength:10
              
            },      
      ctc: "required",
      ectc: "required"                  
      
    },
   messages: {
      
      full_name   : "Please enter Fullname",
      qualification : "Please select Education/Qualification",
      total_exp   : "Please select Total Experience",
      rel_exp     : "Please select Relevant Experience",
      email: {
              required: "please enter email address",
              email: "please enter valid email address"
            },
      phone: {
              required: "please enter phone number"
            },
      secondary_mobile: {
              required: "please enter phone number"
            },
      
      ctc: {
              required: "please enter Current CTC"
            },
      ectc: {
              required: "please enter Expected CTC"
            }           
                                  
   
     }
   
   });
   
   function getMasterChild(id)
   {
   
    var id        = id;
    var url       = "{{ route('admin.cvtracker.getchild',':id') }}";
               url        = url.replace(':id', id);
   
    var baseurl     = "{{ asset('assets/images/AjaxLoader.gif') }}"; 
               var token      = "{{ csrf_token() }}";
    
     $.easyAjax({
                          type    : 'POST',
                          url     : url,
                          data    : {'_token': token, '_method': 'POST', 'id' : id},
              beforeSend  : function() {
                            //$('.clientStatusLoader').html('<img src="'+baseurl+'" height="30px" width="30px">');
              },
                          success   : function (response) {
              
                  if(response.counter == 0){
                    $('.child').hide();
                    $('#childStatus').hide();
                  }else{
                    $('.child').show();
                    $('#childStatus').show();
                    $('#childStatus').html(response.data);
                  }
                  
              },
              complete  : function (data) {
               }
               });
    
   }
   
</script>
<script>
   var datepicker = $('.dob').datepicker({
       autoclose: true,
       format: 'yyyy-mm-dd',
       endDate: (new Date()).toDateString(),
   });
   
   @if ($application->dob)
       datepicker.datepicker('setDate', new Date('{{ $application->dob }}'))
   @endif
   
   $('.select2').select2({
       width: '100%'
   });
   
   // JeroEmailBoxEngine - 100% Client-Side RFC 5321 Validation & Typo Detection
   window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || {
       domainTypos: {
           'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com', 'gmaill.com': 'gmail.com',
           'gmai.com': 'gmail.com', 'gmaii.com': 'gmail.com', 'gemail.com': 'gmail.com',
           'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com', 'hotmil.com': 'hotmail.com',
           'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'yhaoo.com': 'yahoo.com',
           'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
           'iclou.com': 'icloud.com', 'icoud.com': 'icloud.com'
       },
       tldTypos: {
           'con': 'com', 'cmo': 'com', 'ocm': 'com', 'comm': 'com', 'coom': 'com',
           'ney': 'net', 'nte': 'net', 'nett': 'net',
           'og': 'org', 'orgg': 'org', 'ogr': 'org',
           'in': 'in', 'inn': 'in', 'iin': 'in',
           'co': 'co', 'cc': 'cc'
       },
       cleanDeterministic: function(val) {
           if (!val) return '';
           var s = String(val).replace(/[\u200B-\u200D\uFEFF\u00A0\u1680\u180E\u2000-\u200A\u202F\u205F\u3000]/g, ' ').trim();
           s = s.replace(/^mailto:\s*/i, '');
           s = s.replace(/^["'<(\[]+|[>"')\]]+$/g, '');
           s = s.replace(/[,;]+$/, '');
           return s.trim();
       },
       validateSyntax: function(email) {
           if (!email) return false;
           var cleaned = this.cleanDeterministic(email);
           if (cleaned.length < 5 || cleaned.length > 254) return false;
           var atIdx = cleaned.indexOf('@');
           if (atIdx <= 0 || atIdx !== cleaned.lastIndexOf('@')) return false;
           var local = cleaned.slice(0, atIdx);
           var domain = cleaned.slice(atIdx + 1);
           if (local.length > 64 || domain.length > 253) return false;
           var localRegex = /^[a-zA-Z0-9!#$%&'*+\-/=?^_`{|}~]+(?:\.[a-zA-Z0-9!#$%&'*+\-/=?^_`{|}~]+)*$/;
           if (!localRegex.test(local)) return false;
           if (domain.indexOf('.') === -1) return false;
           var parts = domain.split('.');
           if (parts.length < 2) return false;
           var domainPartRegex = /^[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/;
           for (var i = 0; i < parts.length; i++) {
               if (!parts[i] || !domainPartRegex.test(parts[i])) return false;
           }
           var tld = parts[parts.length - 1];
           return tld.length >= 2 && !/^\d+$/.test(tld);
       },
       suggestCorrection: function(email) {
           if (!email) return null;
           var cleaned = this.cleanDeterministic(email);
           var atIdx = cleaned.indexOf('@');
           if (atIdx <= 0 || atIdx !== cleaned.lastIndexOf('@')) return null;
           var local = cleaned.slice(0, atIdx);
           var domain = cleaned.slice(atIdx + 1).toLowerCase();
           if (!domain) return null;
           if (this.domainTypos[domain]) {
               return local + '@' + this.domainTypos[domain];
           }
           var lastDot = domain.lastIndexOf('.');
           if (lastDot > 0) {
               var mainDomain = domain.slice(0, lastDot);
               var ext = domain.slice(lastDot + 1);
               if (this.tldTypos[ext]) {
                   return local + '@' + mainDomain + '.' + this.tldTypos[ext];
               }
           }
           return null;
       },
       renderTypoPill: function(containerSelector, suggestion, inputSelector) {
           var $container = $(containerSelector);
           if (!$container.length) return;
           $container.html(
               '<div class="d-inline-flex align-items-center py-1 px-2 rounded bg-light-warning text-warning fs-8 border border-warning border-dashed">' +
                   '<i class="fa fa-info-circle text-warning mr-1 fs-8"></i>' +
                   '<span>Did you mean <strong>' + $('<div>').text(suggestion).html() + '</strong>?</span>' +
                   '<a href="javascript:void(0);" class="btn btn-xs btn-primary ml-2 px-2 py-0 apply-email-fix" data-suggestion="' + $('<div>').text(suggestion).html() + '" data-target="' + inputSelector + '">Apply</a>' +
               '</div>'
           ).slideDown(150);
       },
       setupEmailBox: function(inputSelector, hintSelector) {
           var self = this;
           var $input = $(inputSelector);
           if (!$input.length) return;

           $input.on('paste', function(e) {
               var pasted = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text') : null;
               if (pasted) {
                   e.preventDefault();
                   var cleaned = self.cleanDeterministic(pasted);
                   $input.val(cleaned).trigger('input').trigger('change');
               }
           });

           $input.on('input', function() {
               var val = $(this).val();
               var cleaned = self.cleanDeterministic(val);
               var suggestion = self.suggestCorrection(cleaned);
               if (suggestion && suggestion.toLowerCase() !== cleaned.toLowerCase()) {
                   self.renderTypoPill(hintSelector, suggestion, inputSelector);
               } else {
                   $(hintSelector).slideUp(100, function() { $(this).empty(); });
               }
           });

           $input.on('blur', function() {
               var val = $(this).val();
               if (val) {
                   $(this).val(self.cleanDeterministic(val));
               }
           });
       }
   };

   window.JeroEmailBoxEngine.setupEmailBox('#email', '#email_typo_hint');
   window.JeroEmailBoxEngine.setupEmailBox('#secondrary_email', '#secondrary_email_typo_hint');

   $(document).off('click', '.apply-email-fix').on('click', '.apply-email-fix', function(e) {
       e.preventDefault();
       var suggestion = $(this).attr('data-suggestion');
       var target = $(this).attr('data-target');
       if (suggestion && target) {
           $(target).val(suggestion).trigger('input').trigger('change');
           $(this).closest('.email-typo-hint-container').slideUp(100, function() { $(this).empty(); });
       }
   });

   $('#save-form').click(function () {
       var primaryEmail = $('#email').val();
       if (window.JeroEmailBoxEngine) {
           var cleanedPrimary = window.JeroEmailBoxEngine.cleanDeterministic(primaryEmail);
           $('#email').val(cleanedPrimary);
           if (!cleanedPrimary) {
               if (typeof toastr !== 'undefined') {
                   toastr.error('Please enter primary email address');
               } else {
                   alert('Please enter primary email address');
               }
               $('#email').focus();
               return false;
           }
           if (!window.JeroEmailBoxEngine.validateSyntax(cleanedPrimary)) {
               if (typeof toastr !== 'undefined') {
                   toastr.error('Please enter a valid primary email address');
               } else {
                   alert('Please enter a valid primary email address');
               }
               $('#email').focus();
               return false;
           }
           var secondaryEmail = $('#secondrary_email').val();
           if (secondaryEmail) {
               var cleanedSecondary = window.JeroEmailBoxEngine.cleanDeterministic(secondaryEmail);
               $('#secondrary_email').val(cleanedSecondary);
               if (!window.JeroEmailBoxEngine.validateSyntax(cleanedSecondary)) {
                   if (typeof toastr !== 'undefined') {
                       toastr.error('Please enter a valid secondary email address');
                   } else {
                       alert('Please enter a valid secondary email address');
                   }
                   $('#secondrary_email').focus();
                   return false;
               }
           }
       }

       $.easyAjax({
           url: '{{route('admin.job-applications.update', $application->id)}}',
           container: '#editForm',
           type: "POST",
           redirect: true,
           file:true,
           error: function (response) {
               handleFails(response);
           }
       })
   });
   
   var val = $('#job_id').val(); // get Current Selected Job
   if (val != '' && typeof val !== 'undefined') {
       getQuestions(val); // get Questions by question on page load
   }
   
   // get Questions on change Job
   function getQuestions(id) {
       var url = "{{ route('admin.job-applications.question', [':id', $application->id]) }}";
       url = url.replace(':id', id);
   
       $.easyAjax({
           type: 'GET',
           url: url,
           container: '#editForm',
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
                       if (response.application.dob !== null) {
                           $('.dob').datepicker('setDate', new Date(response.application.dob));
                       }
                       
                       $('.select2').select2({
                           width: '100%'
                       });
   
                       country = response.application.country;
                       state = response.application.state;
   
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
           $('#editForm').find(".has-error").find(".help-block").remove();
           $('#editForm').find(".has-error").removeClass("has-error");
   
           for (var i = 0; i < keys.length; i++) {
               // Escape dot that comes with error in array fields
               var key = keys[i].replace(".", '\\.');
               var formarray = keys[i];
   
               // If the response has form array
               if(formarray.indexOf('.') >0){
                   var array = formarray.split('.');
                   response.responseJSON.errors[keys[i]] = response.responseJSON.errors[keys[i]];
                   key = array[0]+'['+array[1]+']';
               }
   
               var ele = $('#editForm').find("[name='" + key + "']");
   
               var grp = ele.closest(".form-group");
               $(grp).find(".help-block").remove();
   
               //check if wysihtml5 editor exist
               var wys = $(grp).find(".wysihtml5-toolbar").length;
   
               if(wys > 0){
                   var helpBlockContainer = $(grp);
               }
               else{
                   var helpBlockContainer = $(grp).find("div:first");
               }
               if($(ele).is(':radio')){
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
</script>
@endpush