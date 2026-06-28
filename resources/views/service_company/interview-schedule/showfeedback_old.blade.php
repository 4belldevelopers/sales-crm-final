<style>
    .custmbg {
    background-color: #f5f4f4 !important;
}
</style>
<div class="card mb-5 mb-xl-5 mx-2">
  <div class="card-body pt-5 pb-0 px-5">
    <form class="ajax-form" method="POST" id="ajax-form" name="ajax-form" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="interview_id" id="interview_id" value="{{ @$interviewSchedule->interview_schedules_id }}" />
      <input type="hidden" name="application_id" id="application_id" value="{{ @$interviewSchedule->id }}" />
        @php 	  	
              $schedule_date 		=	$schedule_time 		= "";
              $schedule 			=	@$interviewSchedule->schedule_date;
              $schedule_date 		=	date("d M, Y", strtotime($schedule));			
              $schedule_time 		=	date("H:i A", strtotime($schedule));
        @endphp
      <div id="load_interview_feedback">
      <div class="modal-body scroll-y py-0 px-lg-7">
        <div data-kt-element="options">
          <div class="pb-5">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="">
              <!--begin::Table body-->
              <tbody class="text-gray-600 fw-bold">
                <!--begin::Table row-->
                <tr>
                        <!--begin::Name=-->
                        <td>
                            <div class="d-flex flex-column"> 
                              <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6"> {{ ucwords(@$interviewSchedule->full_name) }} </a> 
                            <a href="javascript:void(0);" class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ @$interviewSchedule->current_designation }}</a> 
                          </div>
                        </td>
                        <!--end::Name=-->
                        <!--begin::Requisitions=-->
                        <td>
                            <div class="d-flex flex-column"> 
                            <span class="text-dark fw-bolder fs-6">{{ ucwords(@$interviewSchedule->job_title) }}</span> 
                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$interviewSchedule->client_name) }}</span> 
                          </div>
                        </td>
                        <!--end::Requisitions=-->
                        <!--begin::Interview Mode=-->
                        <td>
                            <div class="d-flex flex-column"> 
                            <span class="text-dark fw-bolder fs-6">{{ ucwords(@$interviewSchedule->mode_name) }}</span> 
                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$interviewSchedule->mode_lavel_name) }}</span> 
                          </div>
                        </td>
                        <!--end::Interview Mode=-->
                        <!--begin::Interviewers=-->
                        <td>
                            <div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover">
                            @php 
                              $panellistArr	=	'';
                              $panellistArr	=	@$interviewSchedule->interviewers;  
                                if(!empty($panellistArr))
                                {
                                    $panellistArr 	=	explode(',', $panellistArr);
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
                                        
                                        $contactHtml = $real_company_id = $interviewers = '';
                                        $interviewers = $interviewSchedule->interviewers;
                                        $real_company_id = $interviewSchedule->real_company_id;

                                        $contactHtml = '<div class="d-flex flex-column">
                                          <div class="symbol-group symbol-hover">';
                                        $contactHtml .= '<div class="symbol symbol-circle symbol-25px" data-toggle="tooltip1" data-placement="top" data-bs-original-title="'.ucwords(@$pval).'">
                                          <div class="symbol-label '.@$bgTextDecore.'">'.strtoupper(@$strVal).'</div>
                                          </div>';
                                                
                                        
                                        $contactHtml .= '</div>
                                                          </div>';
                                        echo $contactHtml;

                                    }
                                }							
                            @endphp
                            </div>
                          </div> <span class="text-muted fw-bold d-block fs-7">Interviewer</span>
                        </td>
                          @php 
                              $posted_by_image			=	'';	
                              if(!empty($interviewSchedule->image)){	
                                $posted_by_image		=	url('public/user-uploads/team_profile_img/'.@$interviewSchedule->image);
                              }		
                          @endphp 
                        <td>
                            <div>
                              <div class="d-flex flex-column">
                                  <div class="symbol-group symbol-hover">
                                    <div class="" data-toggle="tooltip" data-placement="top" title="{{ @$interviewSchedule->upload_by_name }}"> 
                                        {{-- @if($posted_by_image)
                                        
                                        <img alt="Pic" src="{{ @$posted_by_image }}"> 
                                        
                                        @else --}}
                                        <div class="d-flex flex-column">
                                          <div class="symbol-group symbol-hover">
                                              <div class="" data-toggle="tooltip1" data-placement="top" data-bs-original-title="{{ @$interviewSchedule->upload_by_name }}">
                                                  @php
                                                  $strUpload  = trim(@$interviewSchedule->upload_by_name)
                                                  @endphp
                                                  <span class="text-dark fw-bolder fs-6">{{($interviewSchedule->upload_by_name) }}</span>
                                                  <span class="text-muted fw-bold d-block fs-7">{{ @$interviewSchedule->company_name }}</span>
                                                </div>
                                          </div>
                                        </div>
                                    </div>
                                    {{-- @endif --}}
                              
                                  </div>
                                  </div>
                              </div>
                            </div>
                        </td>
                        <!--end::Source=-->
                        <!--begin::Interview Date=-->
                        <td>
                              <div class="d-flex flex-column"> 
                              <span class="text-dark fw-bolder fs-6">{{ @$schedule_date }}</span> 
                              <span class="text-muted fw-bold d-block fs-7">{{ @$schedule_time }}</span> 
                            </div>
                        </td>
                        <!--end::Interview Date=-->
                        <!--begin::Stage-->
                        <td>
                            <div class="d-flex flex-column"> 
                            <span class="badge fw-bolder fs-6 {{ @$interviewSchedule->css_style }} px-2 py-1">{{ ucwords(@$interviewSchedule->master_status_name) }}</span> 
                            <span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$interviewSchedule->child_status_name) }}</span> 
                          </div>
                        </td>
                        <!--end::Stage-->
                </tr>
                <!--end::Table row-->
              </tbody>
              <!--end::Table body-->
            </table>
            <!--end::Table-->
            <!--begin::Candidate status-->
            <label class="text-dark fw-bolder fs-5">Candidate Status</label>
            <div class="row py-5 mb-3">
                
              @foreach ($available_status as $c_status)
            
                      @php
                            $row = (object) $c_status;
                            $sel	=	'';
                            if(@$interviewSchedule->available_status == $row->id ){
                              $sel	=	'checked="checked"';	
                            }
                      @endphp		
                      
                  
                        <div class="col-lg-2">
                          <input type="radio" class="btn-check app_int_status" name="app_int_status" value="{{$row->id}}" id="app_int_status{{$row->id}}"  {{@$sel}} >
                                <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_status{{$row->id}}"> 
                                    <span class="d-block fw-bold text-start"> 
                                      <span class="text-dark-800 fw-bolder d-block fs-6"> {{ ucwords($row->name) }} </span> 
                                    </span> 
                                </label>
                        </div>
                        
             @endforeach
        
            </div>
            <!--end::Candidate status-->

            <!--begin::Feedback status-->

            <div class="sub_status_div">

            </div>
            {{-- <label class="text-dark fw-bolder fs-5">Feedback Status</label>
            <div class="row py-5 mb-3">
            
                @for($j=0; $j<=5; $j++)
                  
                    @php
                          $sel	=	'';
                          if(@$interviewSchedule->feedback == $j ){
                            $sel	=	'checked="checked"';	
                          }
                    @endphp 
                    
                          <div class="col-lg-2">
                            <input type="radio" class="btn-check" name="app_int_feedback_status" id="app_int_feedback_status_{{$j}}" value="{{$j}}"  {{@$sel}} >
                            <label class="btn btn-outline btn-outline-dashed btn-outline-default p-3 d-flex align-items-center justify-content-center" for="app_int_feedback_status_{{$j}}"> 
                        <span class="d-block fw-bold text-start"> <span class="text-dark-800 fw-bolder d-block fs-5"> {{ ucwords($feedback_status[$j]['name']) }} </span> </span> 
                      </label>
                          </div>
                @endfor

            </div> --}}


        


      
          <?php
            //  $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);

              // dd($jobSkill);
          ?>


            <!-- Start Skills Assessment -->
            <div class="row skillAssessment {{(@$interviewSchedule->feedback==5  || @$interviewSchedule->feedback=='' || @$interviewSchedule->available_status !=1) ? 'd-none' : ''}}">
              <div class="py-5 align-items-center justify-content-between">   
                <div class="row py-5 mb-3">
                  @if($jobSkill)

                  <div class="row">


                    <div class="col-md-4">
                      <label class="text-dark fw-bolder fs-5">Skills</label>
                    </div>
                    <div class="col-md-2">
                      <label class="text-dark fw-bolder fs-5">Rating</label>
                    </div>
                  
                    <div class="col-md-6">
                      <label class="text-dark fw-bolder fs-5">Remarks</label>
                    </div>
                  
                </div>

                  @foreach($jobSkill as $skill_val)
                        @php
                          $skillValue = str_replace(['Years','Year',' '],'',$skill_val->skill_value);
                          $experience = floatval(@$skillValue); // Ensure it's a valid float
                        // If experience is a whole number, convert it to an integer; otherwise, keep the decimal
                        $formattedExperience = ($experience == intval($experience)) ? intval($experience) : $experience;
                        $expir = $formattedExperience . ' Years Exprience';
                        @endphp

                      <!-- React Fundamentals -->
                      <div class="row">
                          <div class="col-md-4" style="margin-top: 10px;">
                            <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6"> {{ ucfirst($skill_val->skill_name) }} </a>
                            <a href="javascript:void(0);" class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ $expir }}</a>
                            <input type="hidden" name="skills_{{ $skill_val->id }}" value="{{ ucfirst($skill_val->skill_name) }}" />
                            <input type="hidden" name="experience_{{ $skill_val->id }}" value="{{ $expir }}" />
                            <input type="hidden" name="skillsId_{{ $skill_val->id }}" value="{{ $skill_val->id }}" />
                            
                          </div>
                          <div class="col-md-2">
                            <div style="display: flex; gap: 5px;margin-top: 10px;">
                              <div class="rating">

                                <!--begin::Star 0-->
                                  <label class="rating-label" for="kt_rating_2_input0_{{ $skill_val->id }}"> <span class="svg-icon svg-icon-1">Reset
                                  </span> </label>
                                  <input class="rating-input" name="rating_{{ $skill_val->id }}" value="0" type="radio" checked="checked" id="kt_rating_2_input0_{{ $skill_val->id }}"/>
                                <!--end::Star 0-->
                                              
                                <!--begin::Star 1-->
                                <label class="rating-label" for="kt_rating_2_input1_{{ $skill_val->id }}"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                                  </g>
                                </svg>
                                </span> </label>
                                <input class="rating-input" name="rating_{{ $skill_val->id }}" value="1" type="radio" id="kt_rating_2_input1_{{ $skill_val->id }}"/>
                                <!--end::Star 1-->
                                <!--begin::Star 2-->
                                <label class="rating-label" for="kt_rating_2_input2_{{ $skill_val->id }}"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                                  </g>
                                </svg>
                                </span> </label>
                                <input class="rating-input" name="rating_{{ $skill_val->id }}" value="2" type="radio" id="kt_rating_2_input2_{{ $skill_val->id }}"/>
                                <!--end::Star 2-->
                                <!--begin::Star 3-->
                                <label class="rating-label" for="kt_rating_2_input3_{{ $skill_val->id }}"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                                  </g>
                                </svg>
                                </span> </label>
                                <input class="rating-input" name="rating_{{ $skill_val->id }}" value="3" type="radio" id="kt_rating_2_input3_{{ $skill_val->id }}"/>
                                <!--end::Star 3-->
                                <!--begin::Star 4-->
                                <label class="rating-label" for="kt_rating_2_input4_{{ $skill_val->id }}"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                                  </g>
                                </svg>
                                </span> </label>
                                <input class="rating-input" name="rating_{{ $skill_val->id }}" value="4" type="radio" id="kt_rating_2_input4_{{ $skill_val->id }}"/>
                                <!--end::Star 4-->
                                <!--begin::Star 5-->
                                <label class="rating-label" for="kt_rating_2_input5_{{ $skill_val->id }}"> <span class="svg-icon svg-icon-1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                                  </g>
                                </svg>
                                </span> </label>
                                <input class="rating-input" name="rating_{{ $skill_val->id }}" value="5" type="radio" id="kt_rating_2_input5_{{ $skill_val->id }}"/>
                                <!--end::Star 5-->
                              </div>
                          </div>
                          </div>
                        
                          <div class="col-md-6">
                            <input type="text" name="remark_{{ $skill_val->id }}" placeholder="Add remarks" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                          </div>
                        
                      </div>

                      @endforeach

                  @endif

                <div class="row">
                      <div class="col-md-4" style="margin-top: 10px;">
                        <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6"> Communication Skills </a>
                        <input type="hidden" name="skills_com" value="Communication Skills" />
                        <input type="hidden" name="experience_com" value="" />
                      </div>
                      <div class="col-md-2">
                        <div style="display: flex; gap: 5px;margin-top: 10px;">
                          <div class="rating">

                            <!--begin::Star 0-->
                              <label class="rating-label" for="kt_rating_2_input_com_0"> <span class="svg-icon svg-icon-1">Reset
                              </span> </label>
                              <input class="rating-input" name="rating_com" value="0" type="radio" checked="checked" id="kt_rating_2_input_com_0"/>
                            <!--end::Star 0-->
                                      
                            <!--begin::Star 1-->
                            <label class="rating-label" for="kt_rating_2_input_com_1"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                              </g>
                            </svg>
                            </span> </label>
                            <input class="rating-input" name="rating_com" value="1" type="radio" id="kt_rating_2_input_com_1"/>
                            <!--end::Star 1-->
                            <!--begin::Star 2-->
                            <label class="rating-label" for="kt_rating_2_input_com_2"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                              </g>
                            </svg>
                            </span> </label>
                            <input class="rating-input" name="rating_com" value="2" type="radio" id="kt_rating_2_input_com_2"/>
                            <!--end::Star 2-->
                            <!--begin::Star 3-->
                            <label class="rating-label" for="kt_rating_2_input_com_3"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                              </g>
                            </svg>
                            </span> </label>
                            <input class="rating-input" name="rating_com" value="3" type="radio" id="kt_rating_2_input_com_3"/>
                            <!--end::Star 3-->
                            <!--begin::Star 4-->
                            <label class="rating-label" for="kt_rating_2_input_com_4"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                              </g>
                            </svg>
                            </span> </label>
                            <input class="rating-input" name="rating_com" value="4" type="radio" id="kt_rating_2_input_com_4"/>
                            <!--end::Star 4-->
                            <!--begin::Star 5-->
                            <label class="rating-label" for="kt_rating_2_input_com_5"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24"/>
                                <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                              </g>
                            </svg>
                            </span> </label>
                            <input class="rating-input" name="rating_com" value="5" type="radio" id="kt_rating_2_input_com_5"/>
                            <!--end::Star 5-->
                          </div>
                      </div>
                      </div>
                    
                      <div class="col-md-6">
                        <input type="text" placeholder="Add remarks" name="remark_com" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                      </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                    <div class="col-md-4" style="margin-top: 10px;">
                      <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6"> Confidence Level</a> 
                      <input type="hidden" name="skills_conl" value="Confidence Level" />
                      <input type="hidden" name="experience_conl" value="" />
                    </div>
                    <div class="col-md-2">
                      <div style="display: flex; gap: 5px;margin-top: 10px;">
                        <div class="rating">
                          <!--begin::Star 0-->
                            <label class="rating-label" for="kt_rating_2_input_conl_0"> <span class="svg-icon svg-icon-1">Reset
                            </span> </label>
                            <input class="rating-input" name="rating_conl" value="0" type="radio" checked="checked" id="kt_rating_2_input_conl_0"/>
                          <!--end::Star 0-->
                                
                          <!--begin::Star 1-->
                          <label class="rating-label" for="kt_rating_2_input_conl_1"> <span class="svg-icon svg-icon-1">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <polygon points="0 0 24 0 24 24 0 24"/>
                              <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                            </g>
                          </svg>
                          </span> </label>
                          <input class="rating-input" name="rating_conl" value="1" type="radio" id="kt_rating_2_input_conl_1"/>
                          <!--end::Star 1-->
                          <!--begin::Star 2-->
                          <label class="rating-label" for="kt_rating_2_input_conl_2"> <span class="svg-icon svg-icon-1">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <polygon points="0 0 24 0 24 24 0 24"/>
                              <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                            </g>
                          </svg>
                          </span> </label>
                          <input class="rating-input" name="rating_conl" value="2" type="radio" id="kt_rating_2_input_conl_2"/>
                          <!--end::Star 2-->
                          <!--begin::Star 3-->
                          <label class="rating-label" for="kt_rating_2_input_conl_3"> <span class="svg-icon svg-icon-1">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <polygon points="0 0 24 0 24 24 0 24"/>
                              <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                            </g>
                          </svg>
                          </span> </label>
                          <input class="rating-input" name="rating_conl" value="3" type="radio" id="kt_rating_2_input_conl_3"/>
                          <!--end::Star 3-->
                          <!--begin::Star 4-->
                          <label class="rating-label" for="kt_rating_2_input_conl_4"> <span class="svg-icon svg-icon-1">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <polygon points="0 0 24 0 24 24 0 24"/>
                              <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                            </g>
                          </svg>
                          </span> </label>
                          <input class="rating-input" name="rating_conl" value="4" type="radio" id="kt_rating_2_input_conl_4"/>
                          <!--end::Star 4-->
                          <!--begin::Star 5-->
                          <label class="rating-label" for="kt_rating_2_input_conl_5"> <span class="svg-icon svg-icon-1">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <polygon points="0 0 24 0 24 24 0 24"/>
                              <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                            </g>
                          </svg>
                          </span> </label>
                          <input class="rating-input" name="rating_conl" value="5" type="radio" id="kt_rating_2_input_conl_5"/>
                          <!--end::Star 5-->
                        </div>
                    </div>
                    </div>
                  
                    <div class="col-md-6">
                      <input type="text" placeholder="Add remarks" name="remark_conl" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                    </div>
                </div>
                <div class="row" style="margin-top: 12px;">
                  <div class="col-md-4" style="margin-top: 10px;">
                    <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6"> Cultural Fit </a>
                    <input type="hidden" name="skills_culf" value="Cultural Fit" />
                    <input type="hidden" name="experience_culf" value="" />
                  </div>
                  <div class="col-md-2">
                    <div style="display: flex; gap: 5px;margin-top: 10px;">
                      <div class="rating">
                        <!--begin::Star 0-->
                        <label class="rating-label" for="kt_rating_2_input_culf_0"> <span class="svg-icon svg-icon-1">Reset
                          </span> </label>
                          <input class="rating-input" name="rating_culf" value="0" type="radio" checked="checked" id="kt_rating_2_input_culf_0"/>
                          <!--end::Star 1-->
          
                        <!--begin::Star 1-->
                        <label class="rating-label" for="kt_rating_2_input_culf_1"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                          </g>
                        </svg>
                        </span> </label>
                        <input class="rating-input" name="rating_culf" value="1" type="radio" id="kt_rating_2_input_culf_1"/>
                        <!--end::Star 1-->
                        <!--begin::Star 2-->
                        <label class="rating-label" for="kt_rating_2_input_culf_2"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                          </g>
                        </svg>
                        </span> </label>
                        <input class="rating-input" name="rating_culf" value="2" type="radio" id="kt_rating_2_input_culf_2"/>
                        <!--end::Star 2-->
                        <!--begin::Star 3-->
                        <label class="rating-label" for="kt_rating_2_input_culf_3"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                          </g>
                        </svg>
                        </span> </label>
                        <input class="rating-input" name="rating_culf" value="3" type="radio" id="kt_rating_2_input_culf_3"/>
                        <!--end::Star 3-->
                        <!--begin::Star 4-->
                        <label class="rating-label" for="kt_rating_2_input_culf_4"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                          </g>
                        </svg>
                        </span> </label>
                        <input class="rating-input" name="rating_culf" value="4" type="radio" id="kt_rating_2_input_culf_4"/>
                        <!--end::Star 4-->
                        <!--begin::Star 5-->
                        <label class="rating-label" for="kt_rating_2_input_culf_5"> <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                          </g>
                        </svg>
                        </span> </label>
                        <input class="rating-input" name="rating_culf" value="5" type="radio" id="kt_rating_2_input_culf_5"/>
                        <!--end::Star 5-->
                      </div>
                  </div>
                  </div>
                
                  <div class="col-md-6">
                    <input type="text" placeholder="Add remarks" name="remark_culf" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                  </div>
                </div>
                


                </div>
              </div>
            </div>
        <!-- End Skills Assessment -->
            

              <script>
                  // Add interactivity for star ratings if needed
                  document.querySelectorAll('.fa-star').forEach(star => {
                      star.addEventListener('click', function() {
                          const parent = this.parentElement;
                          const stars = parent.children;
                          const clickedIndex = Array.from(stars).indexOf(this);
                          
                          for (let i = 0; i < stars.length; i++) {
                              if (i <= clickedIndex) {
                                  stars[i].classList.remove('far');
                                  stars[i].classList.add('fas');
                                  stars[i].style.color = '#ffd700';
                              } else {
                                  stars[i].classList.remove('fas');
                                  stars[i].classList.add('far');
                                  stars[i].style.color = '#ccc';
                              }
                          }
                      });
                  });
              </script>

            <!--end::Feedback status-->
            <!--begin::Comment & rating-->
            <div class="row">
              <div class="py-5 d-flex align-items-center justify-content-between">
                <label class="text-dark fw-bolder fs-5">Final Comment</label>
                <div class="rating">
                  <!--begin::Star 1-->
                  <label class="rating-label" for="kt_rating_2_input_0"> <span class="svg-icon svg-icon-1">
                  RESET
                    </span> </label>
                    <input class="rating-input" name="rating" value="0" type="radio" checked="checked" id="kt_rating_2_input_0"/>
                    <!--end::Star 1-->

                  <!--begin::Star 1-->
                  <label class="rating-label" for="kt_rating_2_input_1"> <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span> </label>
                  <input class="rating-input" name="rating" value="1" type="radio" id="kt_rating_2_input_1"/>
                  <!--end::Star 1-->
                  <!--begin::Star 2-->
                  <label class="rating-label" for="kt_rating_2_input_2"> <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span> </label>
                  <input class="rating-input" name="rating" value="2" type="radio" id="kt_rating_2_input_2"/>
                  <!--end::Star 2-->
                  <!--begin::Star 3-->
                  <label class="rating-label" for="kt_rating_2_input_3"> <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span> </label>
                  <input class="rating-input" name="rating" value="3" type="radio" id="kt_rating_2_input_3"/>
                  <!--end::Star 3-->
                  <!--begin::Star 4-->
                  <label class="rating-label" for="kt_rating_2_input_4"> <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span> </label>
                  <input class="rating-input" name="rating" value="4" type="radio" id="kt_rating_2_input_4"/>
                  <!--end::Star 4-->
                  <!--begin::Star 5-->
                  <label class="rating-label" for="kt_rating_2_input_5"> <span class="svg-icon svg-icon-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span> </label>
                  <input class="rating-input" name="rating" value="5" type="radio" id="kt_rating_2_input_5"/>
                  <!--end::Star 5-->
                </div>
              </div>
              <textarea name="feedback_comment" id="feedback_comment" class="form-control form-control-solid" rows="5" placeholder="Write comment here..."></textarea>
            </div>
            <!--end::Comment & rating-->
          </div>
        </div>
        <div class="modal-footer flex-center">
          <div class="div1">
            <!--begin::Cancel-->
            <button type="button" id="" class="btn btn-secondary me-3" data-bs-dismiss="modal">Cancel</button>
            <!--end::Cancel-->
            <!--begin::Submit-->
            <button type="button" id="submit-frm-btn" class="btn btn-primary"> <span class="indicator-label">Submit</span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </button>
            <!--end::Submit-->


        
            
          </div>
          <div class="div2" style="position: absolute;right: 2%;">
            <!--begin::Rescheduled-->
            <a href="javascript:void(0);" id="app_interview_schedule_reschedule" class="btn btn-secondary" data-toggle="modal" data-target="#interview_schedule" rel="{{@$interviewSchedule->id}}" data-bs-dismiss="modal"> Rescheduled </a>
            <!--end::Rescheduled-->
          </div>
        </div>

      </div>
    </form>
  </div>
</div>
</div>

@if(empty($feedbackMapped))
             <p>No data available</p>
         @else
          <div class="card mb-5 mb-xl-5 mx-2">
                <div class="card-body py-0">
                  @if(count($feedbackMapped))
                    <div class="row urlchange">
                      <div class="py-5 d-flex align-items-center justify-content-between" style="position: relative;">
                        <label class="text-dark fw-bolder fs-5">Feedback Timeline</label>
                        <span style="position: absolute;left: 166px;display: flex;top: 4px;">
                          <a href="{{ route('service_company.interview-schedule.skillshowfeedbackdownload',[$interviewSchedule->interview_schedules_id]) }}" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2" target="_blank" style="display: flex">
                                            Download &nbsp;<i class="fa fa-download text-muted fw-bold d-block fs-6"></i>
                                </a>
                        </span>

                         
                      </div>  
                    </div>
                    @endif
         
               @foreach($feedbackMapped as $feedbackId => $feedbacks)
              
                  <div class="row py-5 mb-3">
                      <table class="table table-bordered">
                         <thead>
                                <tr style="background: #efefef;">
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px!important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Candidate Status</span></td>
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Interview status</span></td>
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Contact</span></td>
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important; position: relative;">
                                  <span class="text-dark text-hover-primary fw-bolder fs-6">Feedback Date</span>
                                  <span style="position: absolute;right: 15px;top: 7px;">
                                      <a href="{{route('service_company.interview-schedule.skillshowfeedbackdownload',[$interviewSchedule->interview_schedules_id,$feedbackId])  }}" class="fs-5 fw-bold text-dark text-hover-primary mb-2" target="_blank" style="display: flex;">
                                          Download &nbsp;
                                          <i class="fa fa-download text-muted fw-bold d-block fs-6"></i>
                                      </a>
                                  </span>
                                
                                </td>
                            </tr>
                         </thead>
                        <tbody>
                             <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                                 @foreach ($available_status as $c_status)
                                       @php
                                         $row = (object) $c_status;
                                                    $sel	=	'';
                                                    if($feedbacks['candidateStatus'] == $row->id ){
                                                        $sel	=	'checked="checked"';	

                                        @endphp	
                                         <span class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ ucwords($row->name) }}</span>
                                        @php

                                                    }
                                        @endphp		


                                @endforeach
                            
                            </td>
                             <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                                 @foreach ($feedback_status as $if_status)
                                 @php
                                    $row = (object) $if_status;
                                                $sel	=	'';
                                                if($feedbacks['interviewStatus'] == $row->id ){
                                                    $sel	=	'checked="checked"';	

                                @endphp
                                      <span class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ ucwords($row->name) }}</span>
                                 @php

                                        }
                                @endphp	


                                @endforeach
                             </td>
                              <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                              <span class="text-muted text-hover-primary fw-bold d-block fs-7"> {{$feedbacks['by_name']}} <br>{{$feedbacks['designation']}}</span>
                              </td>
                            <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">

                                <span class="text-muted text-hover-primary fw-bold d-block fs-7"> {{date("d M Y h:i A", strtotime($feedbacks['created_at']))}}</span>
                                
                            </td>

                        </tbody>

                     </table>

                   <table class="table table-bordered">
                  <thead>
                      <tr style="">
                        <th style="width: 30%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px!important;"><span class="text-gray-600 text-hover-primary fw-bolder fs-5">SKILLS</span></th>
                        <th style="width: 20%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-gray-600 text-hover-primary fw-bolder fs-5">RATINGS</span></th>
                        <th style="width: 50%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-gray-600 text-hover-primary fw-bolder fs-5">REMARKS</span>
                        </th>
                      </tr>
                  </thead>
                  <tbody>

                    @php
                       $star_rating = $feedbacks['star_ratings'];
                        if ($star_rating == 1) {
                            $imagePathMain = url('public/img/star/1.png');
                            $textMain = 'Bad';
                        } elseif ($star_rating == 2) {
                            $imagePathMain = url('public/img/star/2.png');
                            $textMain = 'Poor';
                        } elseif ($star_rating == 3) {
                            $imagePathMain = url('public/img/star/3.png');
                            $textMain = 'Average';
                        } elseif ($star_rating == 4) {
                            $imagePathMain = url('public/img/star/4.png');
                            $textMain = 'Best';
                        } elseif ($star_rating == 5) {
                            $imagePathMain = url('public/img/star/5.png');
                            $textMain = 'Very Best';
                        } else {
                            $imagePathMain = url('public/img/star/5-blank.png');
                            $textMain = 'Blank';
                        }
                    @endphp
                                       
                       @foreach($feedbacks['feedbackdata'] as $feedback)
                       @php
                       $average_star_rating = $feedback['ratings'];
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
                        @endphp
                        <tr>
                            <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                            <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6"> {{ $feedback['skills'] }} </a>
                            <a href="javascript:void(0);" class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ $feedback['experience'] }}</a>
                            </td>
                            <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                            <img src="{{ str_replace('index.php','',@$imagePath)}}" alt="image" style="width: 5.5rem">
                                <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$text }}
                            </span>
                            </td>
                            <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                            {{ $feedback['remarks'] }}
                            </td>
                        </tr>
                       @endforeach
  
                       <tr>
                        <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"> <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder fs-6">Comment </a> </td>
                        <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                            <img src="{{ str_replace('index.php','',@$imagePathMain)}}" alt="image" style="width: 5.5rem">
                                <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ @$textMain }}
                            </span>
                        </td>

                        <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"> {{ $feedbacks['message'] }}</td>
                      </tr>

                      

  
                    </tbody>
                </table> 
                </div>
             
               @endforeach
                </div>
          </div>
     @endif


<script>


function substatuts(){
        var selectedValue = $("input[name='app_int_status']:checked").val();
        if (selectedValue) {
          var baseurl     				=	"{{ asset('assets/images/Loading2.gif') }}"; 
          var feedback = "{{@$interviewSchedule->feedback}}";
          var url 						=	"{{ route('service_company.interview-schedule.sub_status_tab',':id') }}";
          url 							=	url.replace(':id', selectedValue);
              $.ajax({
                    type		: 'GET',
                    url			: url,
                    beforeSend	: function() {
                                                    $('#modal-content-body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                          },
                
                    data		: {'id':selectedValue,'feedback':feedback},
                    success		: function (response) {
                                        if (response.status == "success") {
                                          $('.sub_status_div').html(response.view);		
                                           setTimeout(() => {
                                                skillRating();
                                            }, 500);					
                                        }
                    }
              });	
              
        }

  };

  setTimeout(() => {
       substatuts();
  }, 200);
 $(document).off('click', "input[name='app_int_status']:checked").on('click', "input[name='app_int_status']:checked", function(){
      substatuts();
 })


 $(document).on('click',"input[name='app_int_feedback_status']:checked",function(){
   skillRating();
 });


function skillRating(){
    var app_int_feedback_status = $("input[name='app_int_feedback_status']:checked").val();
    var app_int_status = $("input[name='app_int_status']:checked").val();
    if (typeof app_int_feedback_status === 'undefined') {
        $(".skillAssessment").addClass('d-none')
    }else{
    if(app_int_feedback_status==5 || app_int_feedback_status=='' || app_int_status !=1){
         $(".skillAssessment").addClass('d-none')
    }else{
       $(".skillAssessment").removeClass('d-none')
    }
  }
      
 }



$('#submit-frm-btn').click(async function(e){

   const connectedCandidateShowCustomSave= await isInternetAvailableSave();
              if (!connectedCandidateShowCustomSave) {
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
    					
				var  app_int_status				=	$('input[name="app_int_status"]:checked').val(); 
				var  app_int_feedback_status	=	$('input[name="app_int_feedback_status"]:checked').val();  
				//var  rating2					=	$('input[name="rating2"]:checked').val();   
				var feedback_comment			=	$('#feedback_comment').val();		
        var form = $('#ajax-form'); // Select the form
        var formData = form.serialize(); // Serialize form data
        					
				var id    						=	$('#interview_id').val();	
				var application_id    			=	$('#application_id').val();								
				var rating						=	$("input[name='rating']:checked").val();							
				var url 						=	"{{ route('service_company.interview-schedule.updateInterview',':id') }}";
                url 							=	url.replace(':id', id);
                var token 						=	"{{ csrf_token() }}";
				var baseurl     				=	"{{ asset('assets/images/Loading2.gif') }}"; 
        // if(feedback_comment==''){
        //    Swal.fire("Required", "Comment is required", "warning")
        //    return false;
        //  }

         $("#submit-frm-btn").prop("disabled", true);
          $("#submit-frm-btn").html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
          );
                $.ajax({
                    type		: 'POST',
                    url			: url,
                    beforeSend	: function() {
                                                    $('#modal-content-body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                          },
                
                    data		: formData,//{'_token': token, '_method': 'POST', 'id':id, 'app_int_status':app_int_status, 'app_int_feedback_status':app_int_feedback_status, 'feedback_comment':feedback_comment, 'application_id':application_id, 'rating':rating },
                    success		: function (response) {
                                        if (response.status == "success") {
                                          var page = $('#pagination .active_disabled').data('page');
                                          fetchCandidates(page);
                                            Swal.fire("Success", "Interview Feedback submit successfully!", "success").then(function(){
                                              $(".data-bs-dismiss").click();
                                            });								
                                        }
                    }
                });	

});	

        async function isInternetAvailableSave() {
      
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
</script>


