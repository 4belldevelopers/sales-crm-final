@extends('layouts.app')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}<?php echo TM_VERSION; ?>">
<style>
.content-header
{
   display: none;
}
.form-group label, label
{
    margin-top: .5em!important;
    margin-bottom: 0.5em;
}
.input-group-append
{
  z-index: 0;
  height: fit-content;
}
</style>
@endpush
@section('content')
@php
$requiredColumns = [
'gender' => __('modules.front.gender'),
'dob' => __('modules.front.dob'),
'country' => __('modules.front.country')
];
$sectionVisibility = [
'profile_image' => __('modules.jobs.profileImage'),
'resume' => __('modules.jobs.resume'),
'cover_letter' => __('modules.jobs.coverLetter'),
'terms_and_conditions' => __('modules.jobs.termsAndConditions')
];
@endphp




<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <div class="modal fade bs-modal-md in" id="scheduleDetailModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-lg" id="modal-data-application">
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> 
                     </div>
                     <div class="modal-body"> Loading... </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-light-danger btn-sm" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-light-primary btn-sm">Update</button>
                     </div>
                  </div>
                  <!-- /.modal-content -->
               </div>
               <!-- /.modal-dialog -->
            </div>
            <h4 class="card-title pt-2 pb-2">
               <i class="fa fa-edit"></i>&nbsp;@lang('app.edit') Requisition</h4>
            <form class="ajax-form" method="POST" id="createForm">
               @csrf
               @method('PUT')

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="address">@lang('modules.jobs.jobTitle')</label>
                        <input type="text" class="form-control" name="title" value="{{ $job->title }}">
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="priority">Priority</label>
                        <select class="form-control select2 m-b-10" name="priority" id="priority" >
                           <?php 
                              foreach($priority as $key => $data_value)
                              {
                                $sel = '';
                                if($job->priority == $key){
                                  $sel = 'selected="selected"';   
                                }
                              ?>
                           <option value="<?php echo $key; ?>" <?php echo $sel; ?> ><?php echo $data_value; ?></option>
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
                        <label for="min_salary">Minimum Salary</label>
                        <input type="number" name="min_salary" id="min_salary" value="<?php echo $job->min_salary; ?>" class="form-control" />
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="min_exp">Minimum Experience</label>
                        <select class="form-control select2 m-b-10"   name="min_exp" id="min_exp" >
                           <option value=""  disabled selected >Select Experience</option>
                           <option value="0">Freshers</option>
                           <?php 
                              $yearText = "Year";
                              for($i=1; $i<=30; $i++)
                              {
                                if($i > 9){
                                  $yearText = "Years";
                                }
                                
                                $sel = '';
                                if($job->min_exp == $i){
                                  $sel = 'selected="selected"';   
                                }
                              
                              echo '<option value="'.$i.'" '.$sel.'>'.$i.' &nbsp; '.$yearText.'</option>';
                              }
                              ?>
                        </select>
                     </div>
                  </div>                 
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="max_salary">Maximum Salary</label>
                        <input type="number" name="max_salary" id="max_salary" value="<?php echo $job->max_salary; ?>" class="form-control" />
                     </div>
                  </div> 
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="max_exp">Maximum Experience</label>
                        <select class="form-control select2 m-b-10" name="max_exp" id="max_exp" >
                           <option value=""  disabled selected >Select Experience</option>
                           <option value="0">Freshers</option>
                           <?php 
                              $yearText = "Year";
                              for($i=1; $i<=30; $i++)
                              {
                                if($i > 9){
                                  $yearText = "Years";
                                }
                                
                                $sel = '';
                                if($job->max_exp == $i){
                                  $sel = 'selected="selected"';   
                                }
                                  
                                echo '<option value="'.$i.'" '.$sel.'>'.$i.' &nbsp; '.$yearText.'</option>';
                              }
                              ?>
                        </select>
                     </div>
                  </div>                
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="total_positions">@lang('modules.jobs.totalPositions')</label>
                        <input type="number" class="form-control" name="total_positions" id="total_positions" value="{{ $job->total_positions }}">
                     </div>
                  </div> 
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="employment_type">Employment Type</label>
                        <select class="form-control select2 m-b-10" name="employment_type" id="employment_type" >
                           <?php 
                              foreach($employmentType as $key => $data_value)
                              {
                              $sel = '';
                              if($job->employment_type == $key){
                              $sel = 'selected="selected"';   
                              }
                              ?>
                           <option value="<?php echo $key; ?>" <?php echo $sel;  ?>  ><?php echo $data_value; ?></option>
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
                        <label for="job_description">@lang('modules.jobs.jobDescription')</label>
                        <textarea class="form-control" id="job_description" name="job_description" rows="8" placeholder="Enter Job Description"  style="padding-bottom: .9em;">{!! $job->job_description !!}</textarea>
                     </div>
                  </div>       
                  <div class="col-md-6">
                    <div class="row">
                        <div class="col-12">
                           <div class="form-group">
                              <label for="status">@lang('app.status')</label>
                              <select name="status" id="status" class="form-control">
                              <option @if($job->status == 'active') selected @endif value="active">@lang('app.active')</option>
                              <option @if($job->status == 'hold') selected @endif  value="hold">Hold</option>
                              <option @if($job->status == 'inactive') selected @endif value="inactive">@lang('app.inactive')</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <label for="client_id">Client</label>
                              <select name="client_id" id="client_id" class="form-control">
                              @foreach($client as $clientarr)
                              <option @if($clientarr->id== $job->client_id) selected @endif value="{{ $clientarr->id }}">{{ ucfirst($clientarr->name) }}</option>
                              @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group">
                              <label for="qualification">Education/Qualification</label>
                              <select class="form-control select2 m-b-10"  name="qualification" id="qualification">
                                 <option value="">Select Qualification</option>
                                 <?php 
                                    foreach($qualification as $data_value)
                                    {
                                      $sel = '';
                                      if($job->qualification  == $data_value->id){
                                        $sel = 'selected="selected"';   
                                      }
                                    ?>
                                 <option value="<?php echo $data_value->id ?>" <?php echo $sel; ?> ><?php echo $data_value->name ?></option>
                                 <?php  } ?>
                              </select>
                           </div>
                        </div>
                    </div>
                  </div>          
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="job_requirement">Roles & Responsibilities</label>
                        <textarea class="form-control" id="job_requirement" name="job_requirement" rows="7" placeholder="Enter Roles & Responsibilities" style="padding-bottom: .5em;">{!! $job->job_requirement !!}</textarea>
                     </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-12">
                         <div class="form-group">
                            <label for="category_id">@lang('menu.jobCategories')</label>
                            <select name="category_id" id="category_id" class="form-control">
                            @foreach($categories as $category)
                            <option @if($category->id == $job->category_id) selected @endif  value="{{ $category->id }}">{{ ucfirst($category->name) }}</option>
                            @endforeach
                            </select>
                         </div>
                      </div>
                      <div class="col-12">
                         <div class="form-group">
                            <label>@lang('menu.skills') <a href="javascript:void(0);" onclick="addMoreSkills();">addMore</a> </label>
                            <select class="select2 m-b-10 select2-multiple" id="job_skills" style="width: 100%; " multiple="multiple"  data-placeholder="@lang('app.add') @lang('menu.skills')" name="skill_id[]">
                               <option value="">Choose Skills</option>
                               <?php  
                                  foreach($skills as $data_value)
                                  {
                                      $sel = '';
                                      $innerArr  = DB::table('job_skills')
                                                //->join('skills', 'skills.id', '=', 'job_skills.skill_id')
                                                ->select('skill_id')
                                                ->where('job_id', $job->id)
                                                //->where('job_id', $job->id)
                                                ->get();
                                                
                                      foreach($innerArr as $iArr){
                                        
                                          if($data_value->id == $iArr->skill_id)  {
                                            $sel = 'selected="selected"'; 
                                          }
                                      }           
                                  ?>
                               <option value="<?php echo $data_value->id; ?>" <?php echo @$sel ?>><?php echo ucfirst($data_value->name); ?></option>
                               <?php  
                                  }
                                  ?>
                            </select>
                         </div>
                      </div>
                      <div class="col-12">
                         <div class="form-group">
                            <label for="contact_person" class="contact_person_add_more">Contact Person <a href="javascript:void(0)" onclick="addMorePerson();">addMore</a></label>
                            <select class="select2 m-b-10 select2-multiple" id="contact_person" style="width: 100%; " multiple="multiple" data-placeholder="Add Contact Person" name="contact_person[]">
                               <?php 
                                  foreach($uArr as $urArr)
                                  {       
                                    
                                    $sel = '';
                                    $innerArr  = DB::table('client_contact_info_jobs')->select('client_contact_info_id')->where('job_id', $job->id)->get();
                                    foreach($innerArr as $iArr){
                                      
                                        if($urArr->id == $iArr->client_contact_info_id) {
                                          $sel = 'selected="selected"'; 
                                        }
                                    }
                                    
                                  ?>
                               <option value="<?php echo $urArr->id ?>" <?php echo @$sel ?> >
                                  <?php 
                                     echo ucwords($urArr->contact_person_name);
                                     ?>
                               </option>
                               <?php   
                                  }
                                  ?>
                            </select>
                         </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group">
                          <label for="location_id">@lang('menu.locations') <a href="javascript:void(0)" onclick="addMoreLocation();">addMore</a></label>
                          <select class="select2 m-b-10 select2-multiple" id="location_id" style="width: 100%; " multiple="multiple" data-placeholder="Add Location" name="location_id[]">
                            <?php 
                            $innerArr  = DB::table('client_location_jobs')->select('client_location_id')->where('job_id', $job->id)->get();
                            foreach($lArr as $clArr)
                            {
                              $sel = '';
                              
                              foreach($innerArr as $iArr){
                                
                                  if($clArr->id == $iArr->client_location_id) {
                                    $sel = 'selected="selected"'; 
                                  }
                              }
                        ?>
                            <option value="<?php echo $clArr->id ?>" <?php echo @$sel; ?> >
                            <?php 
                                echo ucwords($clArr->address). ' '.ucwords($clArr->city).' - '.ucwords($clArr->postcode).' '.ucwords($clArr->country_name);
                        ?>
                            </option>
                            <?php   } ?>
                          </select>
                        </div>
                      </div>                   
                    </div>
                  </div>
               </div>

               <div class="row">
                 <div class="col-md-6"></div>
                 <div class="col-md-6">
                   <div class="row">
                      <div class="col-12">
                         <div class="form-group">
                            <label for="start_date">@lang('app.startDate')</label>
                            <input type="text" class="form-control" id="date-start" value="{{ $job->start_date->format('Y-m-d') }}" name="start_date">
                         </div>
                      </div>
                      <div class="col-12">
                         <div class="form-group">
                            <label for="end_date">@lang('app.endDate')</label>
                            <input type="text" class="form-control" id="date-end" name="end_date" value="{{ $job->end_date->format('Y-m-d') }}">
                         </div>
                      </div>
                      <div class="col-12" id="team_status">
                         <div class="form-group">
                            <label for="team">Recruiter</label>
                            <select class="select2 m-b-10 select2-multiple select2-hidden-accessible" id="team" style="width: 100%; " multiple="" data-placeholder="Add Recruiter" name="team[]" tabindex="-1" aria-hidden="true">
                            <?php 
                               foreach($team as $teamArr)
                               {
                                 $sel = '';
                                 foreach($jobAssign as $assignArr)
                                 {
                                   if($teamArr->id == $assignArr->user_id)
                                   {
                                     $sel = 'selected="selected"'; 
                                   } 
                               
                                 }
                                 echo '<option '.$sel.'  value="'.@$teamArr->id.'">'.@$teamArr->name.'</option>';
                               
                               }
                               ?>
                            </select>
                         </div>
                      </div>
                      <div class="col-12" id="consultancy_status">
                         <div class="form-group">
                            <label for="consultancy">Consultancy</label>
                            <select class="select2 m-b-10 select2-multiple select2-hidden-accessible" id="consultancy" style="width: 100%; " multiple="" data-placeholder="Add Consultancy" name="consultancy[]" tabindex="-1" aria-hidden="true">
                            <?php 
                               foreach($consultancy as $conArr)
                               {
                               $sel = '';
                                 foreach($jobAssign as $assignArr)
                               {
                                 if($conArr->id == $assignArr->user_id)
                                 {
                                   $sel = 'selected="selected"'; 
                                 } 
                               
                               }
                                         echo '<option '.$sel.'  value="'.@$conArr->id.'">'.@$conArr->name.'</option>';
                                   
                               }
                               ?>
                            </select>
                         </div>
                      </div>                     
                   </div>
                 </div>
               </div>

               <div class="row">
                 <div class="col-md-6">     
                       @if(count($questions) > 0)
                       <h4 class="m-b-0 m-l-10 box-title">@lang('modules.front.questions')</h4>
                       @endif
                       @forelse($questions as $question)
                       <div class="form-group col-md-6">
                          <label class="">
                             <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false" style="position: relative;">
                                <input  @if(in_array($question->
                                id, $jobQuestion)) checked @endif type="checkbox" value="{{$question->id}}" name="question[]" class="flat-red"  style="position: absolute; opacity: 0;"> <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins> 
                             </div>
                             {{ ucfirst($question->question)}} @if($question->required == 'yes')(@lang('app.required'))@endif
                          </label>
                       </div>
                       @empty
                       @endforelse 
                 </div>
				 
				 
				 
                <!-- <div class="col-md-6">
                       <div id="columns">
                          <label>@lang('app.askApplicantsFor')</label>
                          <div class="form-group form-group-inline">
                             @if ($job->required_columns)
                             @foreach ($job->required_columns as $key => $value)
                             <label class="mr-4">
                                <div class="icheckbox_flat-green" aria-checked="false" aria-disabled="false" style="position: relative;">
                                   <input  @if($value) checked @endif type="checkbox" value="{{ $key }}" name="{{ $key }}" class="flat-red"  style="position: absolute; opacity: 0;">
                                   <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins> 
                                </div>
                                {{ ucfirst(__($requiredColumns[$key])) }}
                             </label>
                             @endforeach
                             @endif 
                          </div>
                       </div>
                    </div>                   
                 </div>-->
				 
				 
               </div>

				<input type="hidden" value="yes" name="resume" class="flat-red" style="position: absolute; opacity: 0;">
				
				
               



               <div class="row">                  
                  <div class="col-md-12 d-none">
                     <div class="form-group">
                        <label for="meta-title">@lang('modules.jobs.metaTitle')</label>
                        <input type="text" id="meta-title" class="form-control" name="meta_title" value="{{ $job->meta_details['title'] }}">
                     </div>
                  </div>
                  <div class="col-md-12 d-none">
                     <div class="form-group">
                        <label for="meta-description">@lang('modules.jobs.metaDescription')</label>
                        <textarea id="meta-description" class="form-control" name="meta_description" rows="3">{{ $job->meta_details['description'] }}</textarea>
                     </div>
                  </div>
                  <hr>
               </div>

               <!-- Additional Details -->
               <div class="row mt-3 mb-2 mx-0">
                  <div class="col-12">
                     <h4 class="text-dark border-bottom p-2 bg-light-secondary">
                        <i class="fa fa-info-circle"></i>&nbsp;Additional Details
                     </h4>
                  </div>
                  
			<?php 
					$cnt  			=	$additionalDetailCount ;
					$cntDiv			=	0;
					if($cnt > 0){
						$cntDiv		=	$additionalDetailCount/3 ; 
						$cntDiv		=	ceil($cntDiv);
					}	
					
					//echo $cntDiv; exit; 
					
			?>	  
				  <div class="col-sm-12 nopadding"   >
                    <div class="form-group" id="education_fields" >     
                      <!-- <label for="detail1">Contact Person Info </label> -->  
                      <div class="input-group">
                        <input type="text" name="additional_detail[]" class="form-control detail1" placeholder="Enter Additional Details" value="<?php echo @$additionalDetail[0]->detail_value ?>" >&nbsp;
                        <input type="text" name="additional_detail[]" class="form-control detail2" placeholder="Enter Additional Details" value="<?php echo @$additionalDetail[1]->detail_value ?>" >&nbsp;
                        <input type="text" name="additional_detail[]" class="form-control detail3" placeholder="Enter Additional Details" value="<?php echo @$additionalDetail[2]->detail_value ?>" >
							<div class="input-group-append">
								<button class="btn btn-light-success btn-sm px-2 py-2" type="button" id="add-more">
									<i class="fa fa-plus"></i>
								</button>
							</div> 
                      </div>

<?php 

		//echo '<pre>'; print_r(@$additionalDetail); exit;  
		$additionalDetail_counter	=	3; 
		if($cntDiv > 1){
			for($i=2; $i<=$cntDiv; $i= $i+1){
			
		//}	
?>					  
<div class="row removeclass<?php echo @$i; ?>">
  <div class="col-sm-12 nopadding">
    <div class="form-group">
      <div class="input-group">
        <input type="text" name="additional_detail[]" class="form-control detail1" placeholder="Enter Additional Details" value="<?php echo @$additionalDetail[$additionalDetail_counter]->detail_value ?>" >
        &nbsp;
        <input type="text" name="additional_detail[]" class="form-control detail2" placeholder="Enter Additional Details" value="<?php echo @$additionalDetail[$additionalDetail_counter+1]->detail_value ?>" >
        &nbsp;
        <input type="text" name="additional_detail[]" class="form-control detail3" placeholder="Enter Additional Details" value="<?php echo @$additionalDetail[$additionalDetail_counter+2]->detail_value ?>" >
        <div class="input-group-append">
          <button class="btn btn-light-danger btn-sm px-2 py-2" type="button" onClick="remove_education_fields(<?php echo @$i; ?>);">
            <i class="fa fa-minus"></i>
         </button>
        </div>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
<?php 	
				$additionalDetail_counter = $additionalDetail_counter+3;

			}
		}
?>
                    </div>
                  </div>				  
               </div>
               <!-- Additional Details end -->

               <div class="row">
                  <div class="col-12 text-center py-4">
                     <button type="button" id="save-form" class="btn btn-light-primary btn-sm">
                     <i class="fa fa-save"></i> Save & Submit</button>
                     <br>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"  type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/html5-editor/wysihtml5-0.3.0.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/html5-editor/bootstrap-wysihtml5.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>

<script>

 function addMoreSkills()
   {
   
   		var id         = 12;
   
	   if(typeof scheduleDate === "undefined") {
	   		scheduleDate   = '';
	   }
	   var url      = "{{ route('admin.skills.addMoreSkill',':id') }}";
	   url        = url.replace(':id', id);
	   $('#modelHeading').html('Schedule');
	   $.ajaxModal('#scheduleDetailModal', url);
   
   
   }
   
</script>
   
<script>

        var room = <?php echo $cntDiv+1 ?>;

        $('#add-more').click(function(){
            room++;

            var divtest = `
                <div class="row removeclass${room}">
                    <div class="col-sm-12 nopadding">
                        <div class="form-group">
                           <div class="input-group">
                              <input type="text" name="additional_detail[]" class="form-control detail1" placeholder="Enter Additional Details" >&nbsp;
                              <input type="text" name="additional_detail[]" class="form-control detail2" placeholder="Enter Additional Details" >&nbsp;
                              <input type="text" name="additional_detail[]" class="form-control detail3" placeholder="Enter Additional Details" >
                              <div class="input-group-append">
                                 <button class="btn btn-light-danger btn-sm px-2 py-2" type="button" onclick="remove_education_fields(${room});">
                                    <i class="fa fa-minus"></i>
                                 </button>
                              </div>
                           </div>
                        </div>  
                    </div>
                    <div class="clear"></div>
                </div>`;

            $('#education_fields').append(divtest);
            $(`.removeclass${room} input`).focus();
        })

        function remove_education_fields(rid) { 
            $('.removeclass' + rid).remove();    
        }
</script>


<script>
   $( document ).ready(function() {     
        
     var client_id  = $('#client_id').val();
         var value     =   $("#client_id option:selected"); 
         var text_value  = value.text();
         if(text_value == 'Self')
         {
           
           $(".contact_person_add_more").hide();
           $("#contact_person").prop("disabled", true);
           id      = client_id; 
           var url   = "{{route('admin.jobs.getContactPerson', ':id')}}";
           url     = url.replace(':id', id);
           $.easyAjax({
                   url   : url,
                   success : function (response) 
                   {
                     $('#contact_person').html(response.ccArr);
                   }
               })
         }else{
           $(".contact_person_add_more").show();
         }
      
      
      
      
        $('#client_id').change(function () 
           {
             var id      =   $(this).val();
   
             var value     =   $("#client_id option:selected"); 
             var text_value  = value.text();
             if(text_value == 'Self')
             {
               $(".contact_person_add_more").hide();
               $("#contact_person").prop("disabled", true);
             }else{
               $(".contact_person_add_more").show();
               $("#contact_person").prop("disabled", false); 
             }
             
         
             var url     = "{{route('admin.jobs.getContactPerson', ':id')}}";
             url       = url.replace(':id', id);
             $.easyAjax({
                     url: url,
                     success: function (response) 
                     {
                       $('#contact_person').html(response.ccArr);
                       $('#location_id').html(response.locArr);
                       
                       //$(".select2").select2();
                     }
                 })
           });
      
      
              
   });      
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
           //Flat red color scheme for iCheck
           $('input[type="checkbox"].flat-red').iCheck({
               checkboxClass: 'icheckbox_flat-blue',
           })
   
           // For select 2
           $(".select2").select2();
   
           $('#date-end').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
           $('#date-start').bootstrapMaterialDatePicker({ weekStart : 0, time: false }).on('change', function(e, date)
           {
               $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
           });
   
           var jobDescription = $('#job_description').wysihtml5({
               "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
               "emphasis": true, //Italics, bold, etc. Default true
               "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
               "html": true, //Button which allows you to edit the generated HTML. Default false
               "link": true, //Button to insert a link. Default true
               "image": true, //Button to insert an image. Default true,
               "color": true, //Button to change color of font
               stylesheets: ["{{ asset('assets/node_modules/html5-editor/wysiwyg-color.css') }}"], // (path_to_project/lib/css/wysiwyg-color.css)
   
           });
   
           $('#job_requirement').wysihtml5({
               "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
               "emphasis": true, //Italics, bold, etc. Default true
               "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
               "html": true, //Button which allows you to edit the generated HTML. Default false
               "link": true, //Button to insert a link. Default true
               "image": true, //Button to insert an image. Default true,
               "color": true, //Button to change color of font
               stylesheets: ["{{ asset('assets/node_modules/html5-editor/wysiwyg-color.css') }}"], // (path_to_project/lib/css/wysiwyg-color.css)
   
           });
   
   
           $('#category_id2').change(function () {
   
               var id = $(this).val();
   
               var url = "{{route('admin.job-categories.getSkills', ':id')}}";
               url = url.replace(':id', id);
   
               $.easyAjax({
                   url: url,
                   success: function (response) {
                       $('#job_skills').html(response.data);
                       $(".select2").select2();
                   }
               })
           });
   
   
           $('#save-form').click(function () {
   
               $.easyAjax({
                   url: '{{route('admin.jobs.update', $job->id)}}',
                   container: '#createForm',
                   type: "POST",
                   redirect: true,
                   data: $('#createForm').serialize()
               })
           });
   
       function addMorePerson()
             {
               
           var id = $('#client_id').val();
           if(id > 0)
           {  
             if(typeof scheduleDate === "undefined") {
                 scheduleDate = '';
             }
             var url   = "{{ route('admin.jobs.lightbox',':id') }}";
             url     = url.replace(':id', id);
             $('#modelHeading').html('Schedule');
             $.ajaxModal('#scheduleDetailModal', url);
           }
           else
           {
                 swal({
                 title       : "Please select any client",
                 type        : "warning",
                 closeOnConfirm    : false,
                 closeOnCancel   : false
                 });
   
           }
       }
       
           function addMoreLocation()
             {
       
           var id = $('#client_id').val();
           if(id > 0)
           {  
             if(typeof scheduleDate === "undefined") {
                 scheduleDate = '';
             }
             var url   = "{{ route('admin.jobs.loc_lightbox',':id') }}";
             url     = url.replace(':id', id);
             $('#modelHeading').html('Schedule');
             $.ajaxModal('#scheduleDetailModal', url);
           }
           else
           {
                 swal({
                 title       : "Please select any client",
                 type        : "warning",
                 closeOnConfirm    : false,
                 closeOnCancel   : false
                 });
   
           }
       }  
    
    
       
</script>
@endpush