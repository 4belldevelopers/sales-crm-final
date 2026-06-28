<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .custmbg {
            background-color: #f5f4f4 !important;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-dark {
            color: #000;
        }

        .fs-6 { font-size: 14px; }
        .fs-7 { font-size: 12px; }
        .fs-5 { font-size: 16px; }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .bg-light-danger {
            background-color: #f8d7da;
        }

        .bg-light-warning {
            background-color: #fff3cd;
        }

        .bg-light-primary {
            background-color: #cfe2ff;
        }

        .bg-light-success {
            background-color: #d1e7dd;
        }

        .bg-dark {
            background-color: #343a40;
        }

        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-primary { color: #0d6efd; }
        .text-success { color: #198754; }
        .text-secondary { color: #6c757d; }

        h3, h5 {
            margin: 0 0 6px 0;
        }

        .symbol-label {
            width: 25px;
            height: 25px;
            text-align: center;
            border-radius: 50%;
            line-height: 25px;
        }
       

        /* Disable JS */
        script {
            display: none;
        }
        .watermark {
            position: fixed;
            top: 40%;
            left: 45%;
            font-size: 100px;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: 99999999;
            text-align: center;
            pointer-events: none;
        }
    </style>

    </head>
<body>
    <!-- Conditional Watermark -->
    @php
    $status_text = 'JEROWORKS';
	$status_color = 'color: rgba(11, 224, 64, 0.07)';
    @endphp
    <div class="watermark" style="{{@$status_color}}">
       {{@$status_text}}
    </div>

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
                        <tr>
                            <th colspan="6">
                              <h2> Interview Feedback</h2>
                              <div class="d-flex flex-column"> 
                                    Interview Date <span class="text-muted fw-bold d-block fs-7">{{ @$schedule_date }} {{ @$schedule_time }}</span> 
                                    <p>&nbsp;</p>
                                </div>
                            </th>
                        </tr>
                        <tbody class="text-gray-600">
                            <tr style="background: #e3b35b;">
                                <td><span class="text-dark text-hover-primary fw-bolder fs-6">Name</span></td>
                                <td><span class="text-dark text-hover-primary fw-bolder fs-6">Job</span></td>
                                <td><span class="text-dark text-hover-primary fw-bolder fs-6">Mode</span></td>
                                <td><span class="text-dark text-hover-primary fw-bolder fs-6">Interviewer</span></td>
                                <td><span class="text-dark text-hover-primary fw-bolder fs-6">Source By</span></td>
                                <td><span class="text-dark text-hover-primary fw-bolder fs-6">Stage</span></td>
                            </tr>
                        <!--begin::Table row-->
                        <tr>
                            <!--begin::Name=-->
                            <td>
                                    <div class="d-flex flex-column"> 
                                    <span class="text-dark text-hover-primary fw-bolder fs-6"> {{ ucwords(@$interviewSchedule->full_name) }} </span> <br>
                                    <span class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ @$interviewSchedule->current_designation }}</span> 
                                </div>
                            </td>
                            <!--end::Name=-->
                            <!--begin::Requisitions=-->
                            <td>
                                    <div class="d-flex flex-column"> 
                                    <span class="text-dark text-hover-primary fw-bolder fs-6">{{ ucwords(@$interviewSchedule->job_title) }}</span> <br>
                                    <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ ucwords(@$interviewSchedule->client_name) }}</span> 
                                </div>
                            </td>
                            <!--end::Requisitions=-->
                            <!--begin::Interview Mode=-->
                            <td>
                                    <div class="d-flex flex-column"> 
                                    <span class="text-dark text-hover-primary fw-bolder fs-6">{{ ucwords(@$interviewSchedule->mode_name) }}</span> <br>
                                    <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ ucwords(@$interviewSchedule->mode_lavel_name) }}</span> 
                                </div>
                            </td>
                            <!--end::Interview Mode=-->
                            <!--begin::Interviewers=-->
                            <td><div class="d-flex flex-column">
                                <div class="symbol-group symbol-hover avatar-group">
                    @php 
                        $panellistArr	=	'';
                        $panellistArr	=	@$interviewSchedule->interviewers;  
                        if(!empty($panellistArr))
                    {
                        $panellistArr 	=	explode(',', $panellistArr);
                        foreach($panellistArr as $key=> $pval)
                        {
                            $bgTextDecore = $strVal = $team_full_name	=	 '';
                            $str 			= $team_full_name =  trim(@$pval);
                            $str 			= $strVal = strtolower($str[0]);
                            $str = ord($str);
                            if ($str >= 97 && $str <= 101) {
                                $bgTextDecore = 'fs-8 text-danger';
                            } 
                            else if ($str >= 102 && $str <= 106) {
                                $bgTextDecore = 'fs-8 text-warning';
                            } 
                            else if ($str >= 107 && $str <= 111) {
                                $bgTextDecore = 'fs-8 text-primary';
                            } 
                            else if ($str >= 112 && $str <= 116) {
                                $bgTextDecore = 'fs-8 text-success';
                            } 
                            else if ($str >= 117 && $str <= 121) {
                                $bgTextDecore = 'fs-8 text-secondary';
                            } else {
                                $bgTextDecore = 'fs-8 text-dark';
                            }	
                            
                                $contactHtml = $real_company_id = $interviewers = '';
                            $interviewers = $interviewSchedule->interviewers;
                            $real_company_id = $interviewSchedule->real_company_id;
                           $com = '';
                            if($key>0){
                                $com = ",";
                            }
            
                            $contactHtml = '<div class="avatar">
                                                            <div class="">';
                        
                                            
                                        $contactHtml .= '<div class="" data-toggle="tooltip1" data-placement="top" data-bs-original-title="'.ucwords(@$pval).'">
                                                  <div class=" text-muted text-hover-primary fw-bold d-block fs-7 '.@$bgTextDecore.'">'.$com.' '.(@$pval).'</div>
                            </div>';
                            
            
                                    
                                
                            
                            $contactHtml .= '</div>
                                            </div>';
                            echo $contactHtml;
                            
                            
                                
                        }
                    }							
                            @endphp
                                                </div>
                                            </div> 
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
                                            <span class="text-dark text-hover-primary fw-bolder fs-6">{{($interviewSchedule->upload_by_name) }}</span><br>
                                            <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ @$interviewSchedule->company_name }}</span>
                                        </div>
                                                </div>
                                    </div>
                                </div>
                                {{-- @endif --}}
                                            
                                    </div>
                                </div>
                            </div></td>
                            <!--end::Source=-->
                            <!--begin::Stage-->
                            <td>
                                    <div class="d-flex flex-column"> 
                                    <span class="text-dark text-hover-primary fw-bolder fs-6 {{ @$interviewSchedule->css_style }} px-2 py-1">{{ ucwords(@$interviewSchedule->master_status_name) }}</span> <br>
                                    <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ ucwords(@$interviewSchedule->child_status_name) }}</span> 
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
         
               @foreach($feedbackMapped as $feedbackId => $feedbacks)
               <div class="card mb-5 mb-xl-5 mx-2">
                <div class="card-body py-0">                                       
                  <div class="row py-5 mb-3">
                     <table class="table table-bordered">
                         <thead>
                                <tr style="background: #efefef;">
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px!important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Candidate Status</span></td>
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Interview status</span></td>
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Contact</span></td>
                                <td style="width: 25%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-dark text-hover-primary fw-bolder fs-6">Feedback Date</span></td>
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
                                <td style="width: 30%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px!important;"><span class="text-gray-600 text-hover-primary fw-bolder fs-6">SKILLS</span></td>
                                <td style="width: 20%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-gray-600 text-hover-primary fw-bolder fs-6">RATINGS</span></td>
                                <td style="width: 50%!important;border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"><span class="text-gray-600 text-hover-primary fw-bolder fs-6">REMARKS</span></td>
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
                            <span class="text-dark text-hover-primary fw-bolder fs-6"> {{ $feedback['skills'] }} </span><br>
                            <span class="text-muted text-hover-primary fw-bold d-block fs-7"> {{ $feedback['experience'] }}</span>
                            </td>
                            <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                            <img src="{{ str_replace('index.php','',@$imagePath)}}" alt="image" style="width: 5.5rem">
                                <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ @$text }}
                            </span>
                            </td>
                            <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                             <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ $feedback['remarks'] }}</span>
                            </td>
                        </tr>
                       @endforeach
  
                       <tr>
                        <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;"> <span class="text-dark text-hover-primary fw-bolder fs-6">Final Comment </span> </td>
                         <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">
                            <img src="{{ str_replace('index.php','',@$imagePathMain)}}" alt="image" style="width: 5.5rem">
                                <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ @$textMain }}
                            </span>
                        </td>

                        <td style="border-bottom: 1px solid #f3efef !important; border-top: 1px solid #f3efef !important; border-left: 1px solid #f3efef !important; border-right: 1px solid #f3efef !important;padding: 6px !important;">  <span class="text-muted text-hover-primary fw-bold d-block fs-7">{{ $feedbacks['message'] }}</span></td>
                      </tr>

                    

  
                    </tbody>
                </table> 
                </div>
              </div>
          </div>
                  @endforeach
         @endif

  </body>
</html>
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
                                        }
                    }
              });	
              
        }

  };

  setTimeout(() => {
       substatuts();
  }, 200);
		
  </script>
  
  
  