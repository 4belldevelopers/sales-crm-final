@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/datepicker3.css') }}">
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script> -->
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
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2 pb-2">
          <i class="fa fa-plus-square"></i>&nbsp;Add Applicant
        </h4>               
        @if(count($jobs) == 0)
        <div class="alert alert-danger alert-dismissible fade show">
          <h4 class="alert-heading"><i class="fa fa-warning"></i> Warning!</h4>
          <p>You do not have any job created. You need to create the job first to add the job
            application.
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-info btn-sm m-l-15"style="text-decoration:none;">
              <i class="fa fa-plus-circle"></i>@lang('app.createNew') @lang('menu.jobs')
            </a>
          </p>
        </div>
        @else
        <form class="ajax-form" method="POST" id="createForm">
          @csrf
          <div class="row">
            <div class="col-md-6">              
              <div class="form-group">
                <h5 class="font-weight-bolder">
                  <label class="control-label">Openings</label>
                </h5>  
                <select name="job_id" id="job_id" onchange="getQuestions(this.value)" class="form-control getAdditionalDetail">
				<?php 
						if(!empty($jobs))
						{
							foreach($jobs as $jd)
							{
							
								$id   = $title =  $min_salary = $max_salary = $salary = $created_at = $client_name  = $location_city  = '';
								
								$id   = $jd->id; 
								$title  = $jd->title; 
								
								$min_salary     = $jd->min_salary;
								$max_salary     = $jd->max_salary;
								$salary       = 'Salary('.$min_salary.'-'.$max_salary.')';
								
								if(($min_salary == 0) && ($max_salary == 0) )
								{
								$salary       = 'Salary(Not Disclosed)';  
								}
								
								//$created_at     = $jd->created_at;
								$client_name    = $jd->client_name;
								$location_city    = $jd->location_city;
								
										$created_at 	= '';
										$created_at 	= $jd->created_at;
										$created_at 		= date('d-M-Y: H:I:s', strtotime($created_at)); 
		
								$sel	=	'';
								if($jobID > 0){
									if($jobID == $jd->id){
										$sel	=	'selected="selected"';
									}									
								}
		
								?>
								<option value="<?php echo $jd->id ?>"  <?php echo @$sel; ?> >
								<?php echo $id; ?> &nbsp;|&nbsp;
								<?php echo $title; ?> &nbsp;|&nbsp;
								<?php echo $client_name; ?> &nbsp;|&nbsp;
								<?php echo $location_city; ?> &nbsp;|&nbsp;
								<?php echo $salary; ?> &nbsp;|&nbsp;
								<?php echo $created_at; ?>                      
								</option>
				<?php 
							}
						} 
				?>
                </select>
              </div>
            </div>
            <div class="col-md-6">              
              <!-- <div id="show-sections">
                @include('admin.job-applications.required-sections', ['section_visibility' => $jobs[0]->section_visibility])
              </div> -->
              <div class="form-group">
                 <h5><label class="required">@lang('modules.front.resume')</label></h5>
                 <input class="select-file w-100 mb-2" accept=".png,.jpg,.jpeg,.pdf,.doc,.docx,.xls,.xlsx,.rtf" type="file" name="resume">
              </div>
              @if (!empty($application) && $application->resume_url)
                <a target="_blank" href="{{ $application->resume_url }}" class="btn btn-sm btn-light-primary px-1 py-0">
                  <i class="fa fa-file"></i>&nbsp;@lang('app.view')
                </a> &nbsp;|
                <span class="text-primary">@lang('modules.front.resumeFileType')</span>
                <div class="help-block with-errors"></div>
              @endif 
            </div>
          </div>

          <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6"></div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5 class="font-weight-bolder">
                <label for="full_name" class="required">Full Name</label>
              </h5>
              <div class="form-group">
                <input class="form-control" type="text" name="full_name" id="full_name" placeholder="Enter name">
              </div>
            </div>
            <div class="col-md-6">
              <h5>
                <label for="total_exp">Total Experience</label>
              </h5>
              <div class="form-group"> @php $yearText = "Year"; @endphp
                <select class="form-control"  name="total_exp" id="total_exp" >
                  <option value=""  disabled selected >Select Experience</option>
                  <option value="0">Freshers</option>                    
                    @for($i=1; $i<=30; $i++)
                    @if($i > 9)
                      $yearText = "Years";
                    @endif                   
                  <option value="{{ $i}}">{{ $i}} &nbsp; {{$yearText}}</option>
                @endfor           
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
                <h5>
                  <label for="email" class="required">Primary Email Address</label>
                </h5>
                <div class="form-group">
                  <input class="form-control" type="email" name="email" id="email" placeholder="Enter primary email address">
                </div>             
            </div>
            <div class="col-md-6">
              <h5>
                <label for="rel_exp">Relevant Experience</label>
              </h5>
              <div class="form-group"> @php $yearText = "Year"; @endphp
                <select class="form-control"  name="rel_exp" id="rel_exp">
                  <option value="" disabled selected >Select Experience</option>
                  <option value="0">Freshers</option>                    
                  @for($i=1; $i<=30; $i++)
                  @if($i > 9)
                    $yearText = "Years";
                  @endif
                  <option value="{{ $i}}">{{ $i}} &nbsp; &nbsp; {{$yearText}}</option>
                @endfor            
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5>
                <label for="secondrary_email">Secondary Email Address</label>
              </h5>
              <div class="form-group">
                <input class="form-control" type="email" name="secondrary_email" id="secondrary_email" placeholder="Enter secondary email address">
              </div>              
            </div>
            <div class="col-md-6">
              <h5>
                <label for="qualification">Education/Qualification</label>
              </h5>
              <div class="form-group">
                <select class="form-control m-b-10"  name="qualification" id="qualification">
                  <option value="">Select Qualification</option>
                  <?php 
                foreach($qualification as $data_value)
                {
                ?>
                  <option value="<?php echo $data_value->id ?>"><?php echo $data_value->name ?></option>
                  <?php  } ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5><label for="phone" class="required">Primary Phone Number</label></h5>
              <div class="form-group">
                <input class="form-control" type="tel" name="phone" id="phone" placeholder="Enter primary phone number">
              </div>
            </div>
            <div class="col-md-6">
              <h5><label for="ddl_np_id" class="required">
                  <input name="serving_notice_period" type="radio"class="serving_notice_period" checked="checked" />
                  &nbsp;Notice Period In Days
                  <input name="serving_notice_period" type="radio"class="serving_notice_period" />
                  &nbsp;Serving notice period
                  <input name="serving_notice_period" type="radio"class="serving_notice_period"/>
                  &nbsp;Immediate available
                  <!--<input name="serving_notice_period" id="serving_notice_period" type="checkbox" value="1"  class="serving_notice_period"/>-->
                  <input type="text" id="calender" name="calender" style="display:none"></label>  
              </h5>
              <div class="form-group">
                <input type="text" name="np_id" id="np_id" class="form-control" placeholder="" style="display:none ">
                <input class="form-control" type="number" name="ddl_np_id" id="ddl_np_id"  placeholder="Enter Notice Period"> 
                  <div class="help-block with-errors"></div>
              </div>  
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5><label for="secondary_mobile">Secondary Phone Number</label></h5>
              <div class="form-group">
                <input class="form-control" type="tel" name="secondary_mobile" id="secondary_mobile" placeholder="Enter secondary phone number">
              </div>
            </div>
            <!-- <div class="col-md-6">
              <h5><label>Passport</label></h5>
              <div class="form-group">
                <input name="passport" type="radio" value="Y" />
                Yes
                <input name="passport" type="radio" value="N" checked="checked" />
                No </div>
            </div> -->
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
              <h5>
                <label for="ctc">Current CTC</label>
              </h5>
              <div class="form-group">
                <input class="form-control" type="number" name="ctc" id="ctc" placeholder="Enter current ctc eg :-2000">
              </div>
            </div>
            <div class="col-md-6">
                <h5><label for="ectc">Country</label></h5>
                <div class="form-group">
                    <select class="select2 countries" name="country" id="countryId">
                        <option value="0">Select Country</option>
                    </select>
                </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5>
                <label for="ectc">Expected CTC</label>
              </h5>
              <div class="form-group">
                <input class="form-control" type="number" name="ectc" id="ectc" placeholder="Enter expected ctc eg:-3000">
              </div>
            </div>
            <div class="col-md-6">
              <h5><label for="ectc">State</label></h5>
              <div class="form-group">
                  <select class="select2 states" name="state" id="stateId">
                      <option value="0">Select State</option>
                  </select>
              </div>              
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5><label>City</label></h5>
              <div class="form-group mt-1">
                  <input class="form-control" type="text" name="city" id="cityId" placeholder="@lang('modules.front.selectCity')" value="{{ !empty($application) ? $application->cover_letter : '' }}">
              </div>
            </div>
            <div class="col-md-6"></div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5><label>Current Company</label></h5>
              <div class="form-group">
                <input class="form-control" type="text" name="current_company" placeholder="Enter current company">
              </div>
            </div>
            <div class="col-md-6"></div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5><label>Current Designation</label></h5>
              <div class="form-group">
                <input class="form-control" type="text" name="current_designation" placeholder="Enter current designation">
              </div>
            </div>
            <div class="col-md-6"></div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5><label>Skills</label></h5>
              <div class="form-group">
                <input class="form-control" type="text" name="skills" id="skills" placeholder="Enter skills">
              </div>
            </div>
            <div class="col-md-6"></div>
          </div>

         <!-- Additional Details -->
         <div class="row pt-3 mb-2" id="job_additional_detail">
                               
         </div>
         <!-- Additional Details End -->

         <div class="row">
               <div class="col-md-6" id="questionBoxTitle">
                  <h5>@lang('modules.front.additionalDetails')</h5>
               </div>
               <div class="col-md-6"> </div>
         </div>

         <div class="row">
               <div class="col-md-6" id="questionBox"></div>
               <div class="col-md-6"></div>
         </div>
         
          <div class="row py-4">
            <div class="col-12 text-center">
              <button type="button" id="save-form" class="btn btn-light-primary btn-sm">
                <i class="fa fa-save"></i>&nbsp;Save & Submit
              </button>
            </div>
          </div>
        </form>
        @endif </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script>
        const fetchCountryState = "{{ route('jobs.fetchCountryState') }}";
        const csrfToken = "{{ csrf_token() }}";
        const selectCountry = "@lang('modules.front.selectCountry')";
        const selectState = "@lang('modules.front.selectState')";
        const selectCity = "@lang('modules.front.selectCity')";
        const pleaseWait = "@lang('modules.front.pleaseWait')";

        let country = "";
        let state = "";
    </script>
<script src="{{ asset('front/assets/js/location.js') }}"></script>
<script>

$( document ).ready(function() {

 	var jobid = <?php echo $jobID; ?>;
	if(jobid > 0)
	{
		getQuestions(jobid);
	}
	
	
		$( ".getAdditionalDetail" ).change(function() 
		{
			
								var job_id       	=   $(this).val();			
								var url 			=	"{{ route('admin.job-applications.getAdditionalDetail',':job_id') }}";
								url 				=	url.replace(':job_id', job_id);
					
								$.easyAjax({
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

});
 




        var datepicker = $('.dob').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            endDate: (new Date()).toDateString(),
        });
        
        $('.select2').select2({
            width: '100%'
        });
		
		
		
		

        $('#save-form').click(function () {

            $.easyAjax({
                url: '{{route('admin.job-applications.store')}}',
                container: '#createForm',
                type: "POST",
                redirect: true,
                file: true,
                data: $('#createForm').serialize(),
                error: function (response) {
                    handleFails(response);
                }
            })
        });






        var val = $('#job_id').val(); // get Current Selected Job
        if (val != '' && typeof(val) !== 'undefined') {
            getQuestions(val); // get Questions by question on page load            
        }

        // get Questions on change Job
        function getQuestions(id) {
            var url = "{{ route('admin.job-applications.question',':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
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

    </script>
@endpush