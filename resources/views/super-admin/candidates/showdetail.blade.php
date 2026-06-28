@php 
   use App\Helper\GeneralMethod;
@endphp
<style>
.text-justify { text-align: justify!important; }
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr{	width: max-content!important;}
  .table > :not(caption) > * > *{	padding: 0.75em 0rem!important;}
  td:nth-child(n+2),
  th:nth-child(n+2){	width: revert!important;	border-bottom: 1px dashed #f5f8fa!important;}
}
</style>
<script>
   $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php 
   		
   $resume_url				=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
   $photo  					=	''; //url('public/avatar.png');
   if(!empty($application->photo)){
      $photo			=	 $application->photo;	
   }  
   $country_id			=	$country_name	= $state_name	=	$state	=	$city_name	=	'';	
   $country_id			=	@$application->country;
   $state				=	@$application->state;
   if(@$country_id > 0)
   {
   
		$country_name_Arr	= GeneralMethod::getCountryName($country_id);	
		foreach($country_name_Arr as $cval){
			$country_name	= ucwords($cval['name']);	
		}
	   
	   if($country_id == 101){
	   		$state_name	= @$application->state_name;
			$city_name	= @$application->city_name; 	
	   }else{		
         if( $state > 0){
            $state_name_Arr		= GeneralMethod::getStateName($country_id, $state);	
            foreach($state_name_Arr as $sval){
               $state_name	= ucwords($sval['name']);	
            }	
         }
         $city_name	= @$application->city;
	   }
   		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
   		$current_ctc_cur	=	$current_ectc_cur	=	"";
       if($application->condidate_type=='other'){
        $current_ctc_cur	=	$currencySymbole[@$application->current_ctc_cur];
        $current_ectc_cur	=	$currencySymbole[@$application->current_ectc_cur];
       }else{
        $current_ctc_cur	=	$currencySymbole[@$application->bill_rate_currency];
        $current_ectc_cur	=	$currencySymbole[@$application->bill_rate_currency];
       }
   		
   }			
  //  rjobid from_company_id									
@endphp
<div class="card-header px-5 py-0" id="kt_activities_header">
  <div class="d-flex align-items-center py-1">
    <h3 class="card-title fw-bolder text-dark d-none">Candidate Profile</h3>
    <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 

    @php
    $dnone = "d-none";
    if($application->master_status_id==8 && $application->rjobid > 0 && $application->from_company_id == auth()->user()->company_id){
      $dnone = "";
    }elseif($application->master_status_id !=8 && $application->rjobid > 0 && $application->from_company_id== auth()->user()->company_id){
      $dnone = "";   
    }elseif(in_array("add_schedule", $userPermissions) && $application->rjobid < 0){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->company_id== auth()->user()->company_id){
      $dnone = "";
    }elseif(in_array("add_schedule", $userPermissions) && $application->master_status_id==8 && $application->submitted_by == auth()->user()->id){
      $dnone = "";
    }

    

   @endphp
@if(in_array("add_schedule", $userPermissions)) 
	<a href="javascript:void(0);" id="app_interview_schedule" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3 {{ $dnone }}" data-toggle="modal" 
	data-target="#interview_schedule" rel="{{@$application->wiseId}}"> <span class="svg-icon svg-icon-primary svg-icon-1x">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <polygon points="0 0 24 0 24 24 0 24"></polygon>
        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
      </g>
    </svg>
    </span> Interview </a> 
@endif 
		 
	</div>
  
  <div class="card-toolbar">
    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesApplication_close"> <span class="svg-icon svg-icon-1">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
        <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
        <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
      </g>
    </svg>
    </span> </button>
  </div>
</div>
<div class="card-body position-relative px-0 py-0" id="kt_activitiesApplication_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
    <div class="card mb-5 mb-xl-5 mx-2">
      <div class="card-body pt-5 pb-0 px-5">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
          <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> 
            @php
                $str = trim(@$application->full_name);
			     	$strVal = strtolower($str[0]);
            @endphp
            @if(!empty($photo))
            <img src="{{@$photo}}" alt="image">
            @else
                <div class="symbol-label  border-radius-5">{{ ucwords(@$strVal) }}</div>
            @endif
            
              <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
              <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$application->full_name) }}</span> </div>
                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                      <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                    </g>
                  </svg>
                  </span> {{ ucwords(@$application->total_exp)}} Years </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <rect fill="#000000" opacity="0.3" x="2" y="2" width="10" height="12" rx="2"/>
                      <path d="M4,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,8 C2,6.8954305 2.8954305,6 4,6 Z M18,16 C19.1045695,16 20,15.1045695 20,14 C20,12.8954305 19.1045695,12 18,12 C16.8954305,12 16,12.8954305 16,14 C16,15.1045695 16.8954305,16 18,16 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                 @if($application->condidate_type=='other')
                  {!! @$current_ctc_cur !!}&nbsp; {!! @$application->ctc !!}-{!! @$application->ectc !!}
                  @else
                  {!! @$current_ctc_cur !!}&nbsp; {!! @$application->bill_rate_amount !!}
                  @endif

                  @php 
                                 $current_ctc_mode	=	0;
                                 if($application->condidate_type=='other'){
                                  $current_ctc_mode	=	@$application->current_ctc_mode;
                                 }else{
                                  $current_ctc_mode	=	@$application->bill_rate_mode;
                                 }
                                 
                                 echo $salaryMode[$current_ctc_mode];			
                   @endphp
                  </a> 
				  
                <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2"> <span class="svg-icon svg-icon-4 me-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                          <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                        </svg>
                        </span> {{ ucwords(@$city_name) }}, {{ ucwords(@$state_name) }}, {{ ucwords(@$country_name) }} 
                </a> 
                
				  </div>
          </div>
            @if(@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id)

              @else
              
              <div class="d-flex mt-4"> 
                    <a href="https://api.whatsapp.com/send?phone=91{{@$application->phone}}" onclick="openWhatsAppPopup(event)" type="button" class="btn btn-sm btn-flex btn-light-success btn-active-primary fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-success svg-icon-2x">
                      <i class="fa fa-whatsapp" style=" font-size: 22px;"></i>
                    </span> Whatsapp 
                  </a>
                    <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-light-primary sendmaimodal btn-active-primary fw-bolder me-3 fs-6" data-toggle="modal" data-target="#send_mail"> <span class="svg-icon svg-icon-primary svg-icon-2x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"/>
                              </g>
                            </svg>
                            </span> Email 
                      </a> 
				    </div>
            @endif

            </div>
            <div class="d-flex flex-wrap flex-stack">
              <div class="d-flex flex-column flex-grow-1 pe-8">
                <div class="row w-75"> <span class="">
                @php 
                        $skills				=	'';
                        $skills				=	@$application->skills;
                        if(!empty($skills)){
                          $skillsArr		=	'';
                          $skillsArr		=	explode(',', $skills);
                          foreach($skillsArr as $sval){
                            echo '<span class="badge badge-light me-3">'.@$sval.'</span>';
                          }
                        } 
                @endphp
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="separator separator-solid my-7"></div>
        <div class="d-flex align-items-center flex-wrap pb-7">
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo2/dist/../src/media/svg/icons/Weather/Sun.svg-->
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" fill-rule="nonzero"/>
                <path d="M19.5,10.5 L21,10.5 C21.8284271,10.5 22.5,11.1715729 22.5,12 C22.5,12.8284271 21.8284271,13.5 21,13.5 L19.5,13.5 C18.6715729,13.5 18,12.8284271 18,12 C18,11.1715729 18.6715729,10.5 19.5,10.5 Z M16.0606602,5.87132034 L17.1213203,4.81066017 C17.7071068,4.22487373 18.6568542,4.22487373 19.2426407,4.81066017 C19.8284271,5.39644661 19.8284271,6.34619408 19.2426407,6.93198052 L18.1819805,7.99264069 C17.5961941,8.57842712 16.6464466,8.57842712 16.0606602,7.99264069 C15.4748737,7.40685425 15.4748737,6.45710678 16.0606602,5.87132034 Z M16.0606602,18.1819805 C15.4748737,17.5961941 15.4748737,16.6464466 16.0606602,16.0606602 C16.6464466,15.4748737 17.5961941,15.4748737 18.1819805,16.0606602 L19.2426407,17.1213203 C19.8284271,17.7071068 19.8284271,18.6568542 19.2426407,19.2426407 C18.6568542,19.8284271 17.7071068,19.8284271 17.1213203,19.2426407 L16.0606602,18.1819805 Z M3,10.5 L4.5,10.5 C5.32842712,10.5 6,11.1715729 6,12 C6,12.8284271 5.32842712,13.5 4.5,13.5 L3,13.5 C2.17157288,13.5 1.5,12.8284271 1.5,12 C1.5,11.1715729 2.17157288,10.5 3,10.5 Z M12,1.5 C12.8284271,1.5 13.5,2.17157288 13.5,3 L13.5,4.5 C13.5,5.32842712 12.8284271,6 12,6 C11.1715729,6 10.5,5.32842712 10.5,4.5 L10.5,3 C10.5,2.17157288 11.1715729,1.5 12,1.5 Z M12,18 C12.8284271,18 13.5,18.6715729 13.5,19.5 L13.5,21 C13.5,21.8284271 12.8284271,22.5 12,22.5 C11.1715729,22.5 10.5,21.8284271 10.5,21 L10.5,19.5 C10.5,18.6715729 11.1715729,18 12,18 Z M4.81066017,4.81066017 C5.39644661,4.22487373 6.34619408,4.22487373 6.93198052,4.81066017 L7.99264069,5.87132034 C8.57842712,6.45710678 8.57842712,7.40685425 7.99264069,7.99264069 C7.40685425,8.57842712 6.45710678,8.57842712 5.87132034,7.99264069 L4.81066017,6.93198052 C4.22487373,6.34619408 4.22487373,5.39644661 4.81066017,4.81066017 Z M4.81066017,19.2426407 C4.22487373,18.6568542 4.22487373,17.7071068 4.81066017,17.1213203 L5.87132034,16.0606602 C6.45710678,15.4748737 7.40685425,15.4748737 7.99264069,16.0606602 C8.57842712,16.6464466 8.57842712,17.5961941 7.99264069,18.1819805 L6.93198052,19.2426407 C6.34619408,19.8284271 5.39644661,19.8284271 4.81066017,19.2426407 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              </g>
            </svg>
            <!--end::Svg Icon-->
            </span>
            <div class="d-flex flex-column text-dark-75"> 
				<span class="fw-bolder fs-6">Designation</span> 
				<span class="fw-bolder fs-3" data-toggle="tooltip" title="{{ucwords((@$application->current_designation))}}"> {{ucwords(substr(@$application->current_designation, 0, 10))}} </span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> 
				<span class="fw-bolder fs-6">Organization</span> 
        @php if(@$application->condidate_type=='bench_condidate' ){ @endphp
          <span class="fw-bolder fs-3" data-toggle="tooltip" title="{{ucwords((@$application->own_company_name) ? @$application->own_company_name : @$application->own_company_name)}}"> {{ucwords((@$application->own_company_name) ? @$application->own_company_name : @$application->own_company_name)}} </span>
          @php  }else{ @endphp
				<span class="fw-bolder fs-3" data-toggle="tooltip" title="{{ucwords((@$application->current_company) ? @$application->current_company : @$application->company_name)}}"> {{ucwords((@$application->current_company) ? @$application->current_company : @$application->company_name)}} </span>
        @php }  @endphp
      
      </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                <path d="M18.5,8 C17.1192881,8 16,6.88071187 16,5.5 C16,4.11928813 17.1192881,3 18.5,3 C19.8807119,3 21,4.11928813 21,5.5 C21,6.88071187 19.8807119,8 18.5,8 Z M18.5,21 C17.1192881,21 16,19.8807119 16,18.5 C16,17.1192881 17.1192881,16 18.5,16 C19.8807119,16 21,17.1192881 21,18.5 C21,19.8807119 19.8807119,21 18.5,21 Z M5.5,21 C4.11928813,21 3,19.8807119 3,18.5 C3,17.1192881 4.11928813,16 5.5,16 C6.88071187,16 8,17.1192881 8,18.5 C8,19.8807119 6.88071187,21 5.5,21 Z" fill="#000000" opacity="0.3"></path>
                <path d="M5.5,8 C4.11928813,8 3,6.88071187 3,5.5 C3,4.11928813 4.11928813,3 5.5,3 C6.88071187,3 8,4.11928813 8,5.5 C8,6.88071187 6.88071187,8 5.5,8 Z M11,4 L13,4 C13.5522847,4 14,4.44771525 14,5 C14,5.55228475 13.5522847,6 13,6 L11,6 C10.4477153,6 10,5.55228475 10,5 C10,4.44771525 10.4477153,4 11,4 Z M11,18 L13,18 C13.5522847,18 14,18.4477153 14,19 C14,19.5522847 13.5522847,20 13,20 L11,20 C10.4477153,20 10,19.5522847 10,19 C10,18.4477153 10.4477153,18 11,18 Z M5,10 C5.55228475,10 6,10.4477153 6,11 L6,13 C6,13.5522847 5.55228475,14 5,14 C4.44771525,14 4,13.5522847 4,13 L4,11 C4,10.4477153 4.44771525,10 5,10 Z M19,10 C19.5522847,10 20,10.4477153 20,11 L20,13 C20,13.5522847 19.5522847,14 19,14 C18.4477153,14 18,13.5522847 18,13 L18,11 C18,10.4477153 18.4477153,10 19,10 Z" fill="#000000"></path>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Notice Period</span> <span class="fw-bolder fs-3"> 
              
              @php

              $npDate = $application->np_id;
                    if ($application->serving_notice_period_mode == 0){
                      $npDate = ($application->np_id==1) ? $application->np_id.' Day' : $application->np_id.' Days';
                    }elseif($application->serving_notice_period_mode == 1) {
                      $originalDate = $application->np_id;
                      $npDate = date("d-M-y", strtotime($originalDate));
                    }
              @endphp
              {{  $npDate  }}&nbsp; </span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
                <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Qualification</span> <span class="fw-bolder fs-3"> {{ ucwords(@$application->qualification_name) }} &nbsp;</span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M9,10 L9,19 L5,19 L5,10 L5,6 L18,6 L18,10 L9,10 Z" fill="#000000" transform="translate(11.500000, 12.500000) scale(-1, 1) translate(-11.500000, -12.500000) "/>
                <circle fill="#000000" opacity="0.3" cx="8" cy="16" r="2"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Stage</span> <span class="fw-bolder fs-3">{{ ucwords(@$application->master_status_name) }}&nbsp;</span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
@php 
				$posted_by_image			=	$posted_by			=	'';
				$posted_by					=	@$application->posted_by;
				if(!empty($application->posted_by_image)){	
					$posted_by_image		=@$application->posted_by_image;
				}			
        $bgTextDecore				=	GeneralMethod::icoinText(@$posted_by);		  
@endphp
            <div class="symbol-group symbol-hover">
              <div class="symbol symbol1 symbol-30 symbol-circle" data-toggle="tooltip" title="" data-original-title="{{@$posted_by}}"> 
               @if(!empty($posted_by_image))
                <img alt="Pic" src="{{@$posted_by_image}}">
                @else
                <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="{{ ucwords(@$posted_by)}}" >
                  <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                  <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                    <div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($posted_by, 0, 1)}}</div>
                  </a>		 
                  </div>
                  </div>
                @endif

               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
	

	
    <div class="card card-custom mx-2">
      <div class="card-header card-header-tabs-line">
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
            <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab" href="#personal_details"><span class="nav-text font-weight-bold">Personal Detail & Notes</span></a> </li>
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#timeline"><span class="nav-text font-weight-bold">Timeline</span></a> </li>
            {{-- <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#offer_tracker"><span class="nav-text font-weight-bold">Offer Tracker</span></a> </li> --}}
          </ul>
        </div>
      </div>
	  
      <div class="card-body py-0">
      
	    <div class="tab-content pt-5">
          		
				<div class="tab-pane active" id="personal_details" role="tabpanel">
          @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other')
            		<div class="card-header cursor-pointer px-0">
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0"&nbsp;</h3>
                  </div>
                  @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='other') 
                  <a href="{{route('service_company.job-applications.edit', [ GeneralMethod::value_encrypt($jobWiseCanId)])}}" class="btn btn-primary align-self-center {{ $dnone }}"> Edit Profile </a> 
                  @endif 
                </div>
          @endif 

          {{-- @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='bench_condidate')
            		<div class="card-header cursor-pointer px-0">
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">&nbsp;</h3>
                  </div>
                  @if(in_array("edit_job_applications", $userPermissions) && $application->condidate_type=='bench_condidate') 
                  <a href="{{route('service_company.bench-candidates.edit', [$jobWiseCanId])}}" class="btn btn-primary align-self-center {{ $dnone }}"> Edit Profile </a> 
                  @endif 

                </div>
          @endif  --}}


              @php
              $dnone = "d-none";
              if($application->master_status_id==8 && $application->rjobid > 0 && $application->from_company_id != auth()->user()->company_id){
                $dnone = "";
              }elseif($application->master_status_id !=8 && $application->rjobid > 0 && $application->from_company_id== auth()->user()->company_id){
                $dnone = "";   
              }elseif(in_array("edit_job_applications", $userPermissions) && $application->rjobid < 0){
                $dnone = "";
              }elseif(in_array("edit_job_applications", $userPermissions) && $application->company_id== auth()->user()->company_id){
                $dnone = "";
              }
          
             @endphp

               
			
            		<div class="card-body px-0 py-7">
            
			    <div class="row">
                <!--begin::Left Section-->
                <div class="col-lg-6">
                  <!--begin::Name-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Name</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$application->full_name) }}</span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Name-->
                  <!--begin::Primary Number-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Primary Number</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ (@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id) ? 'xxx-xxx-xxxx' :  @$application->phone }}</span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Primary Number-->
                  <!--begin::Primary Email-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Primary Email</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{  (@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id) ? 'xxx-xxx-xxxx' : @$application->email }}</span></div>
                    <!--end::Col-->
                  </div>
                  <!--end::Primary Email-->
                  <!--begin::Company-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Company</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ (@$application->current_company) ? ucwords(@$application->current_company) : @$application->own_company_name }}</span></div>
                    <!--end::Col-->
                  </div>
                  <!--end::Company-->
                  <!--begin::Desigination-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Desigination</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$application->current_designation) }}</span></div>
                    <!--end::Col-->
                  </div>
                  <!--end::Desigination-->
                  <!--begin::Total Experience-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Total Experience</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ @$application->total_exp }} Years</span></div>
                    <!--end::Col-->
                  </div>
                  <!--end::Total Experience-->
                  <!--begin::Relevant Experience-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6" data-toggle="tooltip" title="Relevant Experience">Rel. Experience</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6"> {{ @$application->rel_exp }} Years</span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Relevant Experience-->
                  <!--begin::Current Salary-->
                  <div class="row">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">{{ ($application->condidate_type=='other') ? 'Current Salary' : 'Pay Rate' }}</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                      <div class="row">
                        <!--begin::Col-->
                        <div class="col-lg-12"> <span class="fw-bolder text-gray-800 fs-6">
                          @if($application->condidate_type=='other')
                          {!! @$current_ctc_cur !!} {!! @$application->ctc !!}
                        @php 
                                      $current_ctc_mode	=	"";
                                      $current_ctc_mode	=	@$application->current_ctc_mode;
                                      echo $salaryMode[$current_ctc_mode];			
                        @endphp
                        @elseif((@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id))
                         XXX-XXX-XXXX
                         @else
                         {!! @$current_ctc_cur !!} {!! @$application->pay_rate_amount !!}
                         @php 
                                       $current_ctc_mode	=	"";
                                       $current_ctc_mode	=	@$application->pay_rate_mode;
                                       echo $salaryMode[$current_ctc_mode];			
                         @endphp

                        @endif
                        
                          </span> </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-3"> <span class="fw-bolder text-gray-800 fs-6"> &nbsp; </span> </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7"> <span class="fw-bolder text-gray-800 fs-6">&nbsp;</span>
                          <!-- Monthly | Annualy | Hourly -->
                        </div>
                        <!--end::Col-->
                      </div>
                    </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Current Salary-->
                  <!--begin::Expected Salary-->
                  <div class="row">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">{{ ($application->condidate_type=='other') ? 'Expected Salary' : 'Bill Rate' }} </label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                      <div class="row">
                        <!--begin::Col-->
                        <div class="col-lg-12"> <span class="fw-bolder text-gray-800 fs-6">
                          @if($application->condidate_type=='other')
                          {!! @$current_ectc_cur !!} {!! @$application->ectc !!}
                                  @php 
                                                $current_ectc_mode	=	"";
                                                $current_ectc_mode	=	@$application->current_ctc_mode;
                                                echo $salaryMode[$current_ectc_mode];			
                                  @endphp

                          @else
                          {!! @$current_ectc_cur !!} {!! @$application->bill_rate_amount !!}
                          @php 
                                        $current_ectc_mode	=	"";
                                        $current_ectc_mode	=	@$application->bill_rate_mode;
                                        echo $salaryMode[$current_ectc_mode];			
                          @endphp

                          @endif
                          </span> </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-3"> <span class="fw-bolder text-gray-800 fs-6"> &nbsp; </span> </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7"> <span class="fw-bolder text-gray-800 fs-6"> &nbsp; </span>
                          <!-- Monthly | Annualy | Hourly -->
                        </div>
                        <!--end::Col-->
                      </div>
                    </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Expected Salary-->
                  <!--begin::Percentage-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Percentage</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                      <div class="row">
                        <!--begin::Col-->
                        <div class="col-lg-12"> <span class="fw-bolder text-gray-800 fs-6">{{ @$application->percentage_hike }}%&nbsp;{{ @$application->comment }}</span> </div>
                        <!--end::Col-->
                      </div>
                    </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Percentage-->
                  <!--begin::Offer Amount-->
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Offer Amount</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-md-12"> <span class="fw-bolder text-gray-800 fs-6">{{ @$application->offer_amount }}</span> </div>
                      </div>
                    </div>
                  </div>
                  <!--end::Offer amount-->
                  <!--begin::Resume-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Resume</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-md-12"> <span class="fw-bolder text-gray-800 fs-6">
          @if(!empty(@$application->hashname))
          <a type="button" class="btn btn-light-primary btn-sm py-1" href="{{  @$application->hashname }}" target="_blank" download> Download&nbsp;&nbsp;<i class="fa fa-download"></i></a>
          @endif
                  </span> </div>
                      </div>
                    </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Resume-->
                </div>
                <!--end::Left Section-->
                <!--begin::Right Section-->
                <div class="col-lg-6">
                  <!--begin::Notice Period-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Notice Period</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">
             @php
              $np = $application->np_id;
                      if (empty($np)) {
                        $np = 'N/A';
                      }
                      if ($application->serving_notice_period_mode == 0) {
                        $np = $application->np_id." Days";
                        if ($application->np_id==1) {
                          $np = $application->np_id . '  Day';
                        }
                      } elseif($application->serving_notice_period_mode == 1){
                        $np = $application->np_id;
                        if($np !='Immediate'){
                          $np =  GeneralMethod::ymdTodmyNotTime($application->np_id);
                        }
                      }

              echo @$np;	
             @endphp
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Notice Period-->
                  <!--begin::Secondary Number-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Secondary Number</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{  (@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id) ? 'xxx-xxx-xxxx' : @$application->secondary_mobile }} </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Secondary Number-->
                  <!--begin::Secondary Email-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Secondary Email</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{  (@$application->condidate_type=='bench_condidate' && @$application->other_company !=auth()->user()->company_id) ? 'xxx-xxx-xxxx' : @$application->secondrary_email }} </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Secondary Email-->
                  <!--begin::Location-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Location</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8">
                      <div class="row">
                        <!--begin::Country-->
                        <div class="col-lg-12"> 
							<span class="fw-bolder text-gray-800 fs-6"> 
								{{ @$city_name }}&nbsp; {{ ucwords(@$state_name) }}&nbsp; {{ ucwords(@$country_name) }}&nbsp;
							</span> 
						</div>
                      </div>
                    </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Location-->
                  <!--begin::Qualification-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Qualification</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6"> {{ ucwords(@$application->qualification_name) }} </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Qualification-->
                  <!--begin::Date of Birth-->
                  <div class="row mb-7 d-none">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Date of Birth</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">
          @php 
                      $originalDate 	= @$application->dob;
                      $newDate 		= date("d-M-Y", strtotime($originalDate)); 
                      echo @$newDate;
          @endphp
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Date of Birth-->
                  <!--begin::Candidate ID<-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Candidate ID</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> C-{{ @$application->id }} </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Candidate ID<-->
                  <!--begin::Reffered By-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Reffered By</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">{{ @$posted_by }}</span></div>
                    <!--end::Col-->
                  </div>
                  <!--end::Reffered By-->
                  <!--begin::Hiring HR-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Hiring HR</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">
          @php 
                      if(!empty($client_contuser_arr)){
                        foreach($client_contuser_arr as $cpn){
                          echo @$cpn->contact_person_name .'  ';
                        }	
                      }
          @endphp 
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Hiring HR-->
                  <!--begin::Applied Date-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Applied Date</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">
          @php 
                      $originalDate 	= @$application->created_at;
                      $newDate 		= date("d-M-Y H:i:s", strtotime($originalDate)); 
                      echo @$newDate;
          @endphp
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Applied Date-->
                  <!--begin::Joining Date-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Joining Date</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">{{ @$application->joined_date ? date("d-m-Y H:i:s", strtotime(@$application->joined_date)) : '' }}</span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Joining Date-->
                  <!--begin::Stage-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Stage</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ucwords(@$application->master_status_name)}} </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Stage-->
                </div>
                <!--end::Right Section-->
              </div>
			  
             
			 
              <?php 
              if($benchStatusCnt > 0){
              ?>			 
						  <div class="card-title m-0 border-bottom py-7">
							<h3 class="fw-bolder m-0">Relationship Manager</h3>
						  </div>
			 
						  <div class="row">
									
											@foreach( $bench_assign_row as $assignVal)
													  
														<div class="col-lg-4">
																<label class="col-lg-4 fw-bold text-muted fs-6">Name</label>
																<div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ ucwords(@$assignVal->name) }} </span> </div>
														</div>
														 <div class="col-lg-4">
																<label class="col-lg-4 fw-bold text-muted fs-6">Email</label>
																<div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ @$assignVal->email }} </span> </div>
														</div>
														 <div class="col-lg-4">
																<label class="col-lg-4 fw-bold text-muted fs-6">Contact No</label>
																<div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ ucwords(@$assignVal->mobile) }} </span> </div>
														</div>
										
										 @endforeach			
							 </div>	




 			   <div class="card-title m-0 border-bottom py-7">
                <h3 class="fw-bolder m-0">Custom Details</h3>
              </div>
              <div class="row pt-7 border-bottom pb-7">
          @php 
            if(!empty($bench_additional_detail)) {
            
                $customTitleId	=	1;
                $html			=	'';
                //$html			=	'<h3 class="py-5">Custom Details</h3>';
                $inc_first		=	0;
                $inc_last		=	1;
                
                foreach($bench_additional_detail as $aDetailArr){
                
                  $custom_keys			=	'';
                  $custom_keys_value		=	'';
                  
                  $custom_keys			=	$aDetailArr->custom_keys;
                  $custom_keys_value		=	$aDetailArr->custom_keys_value;
                          
                  $html					.=	'<div class="col-lg-6">';	
                  $html					.=	'<div class="row mb-7">
                  <label class="col-lg-4 fw-bold text-muted fs-6">'.strtoupper($custom_keys).'</label>
                  <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">'.@$custom_keys_value.'</span> </div>
                  </div>';
                  $html					.=	'</div>';
                  $inc_first++;
                  $inc_last++;
                  $customTitleId++;				
                }
                
                echo $html;	
            
            }	
              
          @endphp
              </div>
			  





          <?php 
          }else {
          ?>			 			 
			 
          @if($application->condidate_type !='other')
            <div class="card-title m-0 border-bottom py-7">
                    <h3 class="fw-bolder m-0">Additional Details</h3>
                  </div>
                  <div class="row pt-7 border-bottom pb-7">
                      @php 
                      
                        if(!empty($bench_additional_detail)){
                          $customTitleId	=	1;
                          $html			=	'';
                          $inc_first		=	0;
                          $inc_last		=	1;

                          foreach($bench_additional_detail as $aDetailArr)
                          {
                            if(!empty($aDetailArr->custom_keys) && !empty($aDetailArr->custom_keys_value)){
                              $html		.=	'<div class="col-lg-6">';	
                            $html		.=	'<div class="row mb-7">
                            <label class="col-lg-4 fw-bold text-muted fs-6">'.strtoupper($aDetailArr->custom_keys).'</label>
                            <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">'.@$aDetailArr->custom_keys_value.'</span> </div>
                            </div>';
                            $html		.='</div>';
                            }
                         

                            $inc_first++;
                            $inc_last++;
                            $customTitleId++;	
                          } 
                          echo $html;	

                        }		
                  @endphp
              </div>
              @endif
			  
          <?php } ?>		


              @if(@$bench_assign_count > 0)
                 <div class="card-title m-0 border-bottom py-7">
                  
                      <div class="card-title m-0 border-bottom py-7">
                        <h3 class="fw-bolder m-0">Relationship Manager</h3>
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
                                  <div class="col-lg-12">  {{ @$assignVal->email }} <br> {{ ucwords(@$assignVal->mobile) }}</div>
                              </div>
                            </div>	
                            @php $sr++;  @endphp
                        @endforeach	

                      </div>		
                 	
              @endif	
              @if($application->condidate_type=='other')
              <div class="card-title m-0 border-bottom py-7">
                  <h3 class="fw-bolder m-0">Job Questions</h3>
               </div>
              <div class="row pt-7 border-bottom pb-7">
                @php 
                if(!empty($job_additional_detail))
                {
               
                
                 $customTitleId	=	1;
                 $html			=	'';
                 //$html			=	'<h3 class="py-5">Custom Details</h3>';
                 $inc_first		=	0;
                 $inc_last		=	1;
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
           
           
                           
                   $html		.=	'<div class="col-lg-6">';	
                   $html		.=	'<div class="row mb-7">
                   <label class="col-lg-4 fw-bold text-muted fs-6">'.strtoupper($aDetailArr->detail_value).'</label>
                   <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">'.@$ans_value.'</span> </div>
                   </div>';
                   $html		.='</div>';
                   
                   $inc_first++;
                   $inc_last++;
                   $customTitleId++;				
                 }
                 echo $html;	
               }	
               @endphp

              </div>
              @endif
              <div class="row pt-7">
                <form class="form">
                  <div class="form-group">
                    <textarea class="form-control form-control-lg form-control-solid" name="note" id="note_text" rows="3" placeholder="Type notes" style="margin-top: 0px; margin-bottom: 0px; height: 88px;"></textarea>
                  </div>
                  <div class="row pt-4">
                    <div class="col"> <a href="javascript:void(0);" id="add-note" class="btn btn-sm btn-light-primary fw-bold">Add notes</a> <a href="javascript:void(0);" id="cancel-note" class="btn btn-sm btn-secondary fw-bold">Cancel</a> </div>
                  </div>
                </form>
                <div class="separator separator-dashed my-10"></div>
                <!--begin::Timeline-->
                <div class="timeline timeline-3">
                  <div class="timeline-items">
                    <div id="applicant-notes" class="col-sm-12">
                      <ul class="list-unstyled">
                          <?php 
                            $cnt	=	0;
                            foreach($notesArr as $key => $notes )
                            {
                              $date		=	$notes->created_at;
                              $noteDate 	= date("d-M-Y", strtotime($date));
                              $noteTitme 	= date("h:i A", strtotime($date));
                              $posted_by_image			=	'';
                              if(!empty($notes->posted_by_image)){	
                                $posted_by_image			=	@$notes->posted_by_image;
                              }  
                              $bgTextDecore				=	GeneralMethod::icoinText(@$notes->name);	                                     
                          ?>
                        <li class="media" id="note-{{ $notes->id }}">
                          <div class="timeline-item">
                            <?php if($cnt%3==0){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-danger svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <rect x="0" y="0" width="24" height="24"></rect>
                                  <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"></path>
                                  <path d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z" fill="#000000"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php }else if($cnt%3==1){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-warning svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                  <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                  <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                </g>
                              </svg>
                              </span> </div>
                            <?php  }else if($cnt%3==2){ ?>
                            <div class="timeline-media"> <span class="svg-icon svg-icon-primary svg-icon-2x">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                  <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"></path>
                                  <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"></rect>
                                </g>
                              </svg>
                              </span> </div>
                            <?php } ?>
                            <div class="timeline-content">
                              <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="me-2 fs-6"> <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder me-2">New Note Added</a> <span class="text-muted me-2"><?php echo @$noteTitme; ?> | <?php echo @$noteDate; ?></span>
                                  <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" title="{{ ucwords($notes->name) }}"> 
                                    @if(!empty($posted_by_image))
                                    <img src="<?php echo @$posted_by_image; ?>" alt="img">
                                    @else
                                    <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$notes->name)}}" >
                                      <div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
                                      <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                                        <div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($notes->name, 0, 1)}}</div>
                                      </a>		 
                                      </div>
                                      </div>
                                    @endif

                                   </div>
                                </div>
                                <div class="dropdown ml-2" data-toggle="tooltip" title="" data-placement="left" data-original-title="New Project"> <a href="javascript:void(0);" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <a href="javascript:void(0);" class="edit-note" data-note-id="{{ $notes->id }}"><i class="fa fa-edit ml-2"></i></a> <a href="javascript:;" class="delete-note" data-note-id="{{ $notes->id }}"><i class="fa fa-trash ml-1 text-danger"></i></a> </div>
                              </div>
                              <p class="p-0 fs-6 comment-para note-text">{{ ucfirst($notes->note_text) }}</p>
                              <div class="note-textarea"></div>
                            </div>
                          </div>
                        </li>
                     <?php 	$cnt++;	}	?>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--eng::Timeline-->
              </div>
            </div>
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
              $company_or_client = GeneralMethod::companyDetailsByClient($tdata->job_id);
              
              $create_at	=	$$newDate	=	$newTime	= $prev_tiles_bg	=	$timeline_type = $current_tiles_bg =  "";
              $create_at	=	$tdata->create_at;
                  
              $newDate 							=	date("d-M-Y", strtotime($create_at));
              $newTime 							=	date("H:i A", strtotime($create_at));
              
              $posted_by_image					=	$comment							=	"";	
              if(!empty(@$tdata->image)){	
                $posted_by_image				=	@$tdata->image;
              }
              
              if(!empty(@$tdata->prev_process_name)){
                $prevCandStatusArr				= 	GeneralMethod::candidateStatusColorCode(@$tdata->prev_process_name);	
                $prev_tiles_bg					=	$prevCandStatusArr['tiles_bg'];
              }
              
              if(!empty(@$tdata->master_process_name)){
                $currentCandStatusArr			= 	GeneralMethod::candidateStatusColorCode(@$tdata->master_process_name);	
                $current_tiles_bg				=	$currentCandStatusArr['tiles_bg'];
                
              }
              
              $master_process_name				=	@$tdata->master_process_name;
              $prev_process_name					=	@$tdata->prev_process_name;
              if($tdata->event_id == 1){
                $prev_process_name				=	@$tdata->note;	
                $prev_tiles_bg					=	'sourcing-bg';	
              }
              
              if($tdata->event_id == 2){
                $comment						=	@$tdata->note;		
              }
              if($tdata->event_id == 5 || $tdata->event_id == 6 || $tdata->event_id == 7 || $tdata->event_id == 8){
                $comment						=	@$tdata->note;		

                // dd($comment);
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
						$management_status = 'Approved';
					}elseif($tdata->management_status==2){
						$management_status = 'Not Approved';
					}elseif($tdata->management_status==3){
						$management_status = 'In Process';
					}





              $feedback_status = GeneralMethod::feedback_status();
              $available_status = GeneralMethod::available_status();

              $available_status_name = '';
                  if ($tdata->available_status > 0) {
                    $available_status_name = @$available_status[@$tdata->available_status]['name'];
                  } 

                  $feedback = ucwords(substr(@$feedback_status[@$tdata->feedback]['name'], 0, 12));

                  
          $bgTextDecore				=	GeneralMethod::icoinText($tdata->name);				 
          if(($tdata->event_id == 1) || ($tdata->event_id == 2))
          {		
          ?>
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
              <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">{{ ucwords(@$tdata->title) }}</a> 
              <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$company_or_client) }}</span> 
            </div>
						</td>
                        <td class="min-w-150px">
							<div class="d-flex flex-column"> 
								<span class="badge fw-bolder fs-6 {{ @$prev_tiles_bg }} px-2 py-1">{{ ucwords(@$prev_process_name) }}</span> 
								<span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->prev_process_child_name) }}</span> 
							</div>
						</td>
                        <td class="min-w-50px"><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6"> To </span> </div></td>
                        <td class="min-w-150px">
							<div class="d-flex flex-column"> 
								<span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span> 
								<span class="text-muted fw-bold d-block fs-7">{{ ucwords(@$tdata->child_process_name) }}</span> 
							</div>
						</td>
                        <td class="min-w-120px">
							<div class="d-flex flex-column"> 
								<span class="text-dark fw-bolder fs-6">{{  GeneralMethod::ymdTodmyNotTime(@$newDate) }}</span> 
								<span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}</span> 
							</div>
						</td>
            <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
								  <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}"> 
                  {{-- @if(!empty($posted_by_image))
									 <img alt="{{ ucwords(@$tdata->name) }}" src="{{ @$posted_by_image }}"> 
                  @else --}}
                 
                  <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                    <span class="text-dark fw-bolder fs-6">{{ substr($tdata->name, 0, 10)}}</span>
                    </div>
                  {{-- @endif --}}

								  </div>
                            </div>
                            <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
              <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
							<!--data-toggle="tooltip" title="{{ ucwords(@$tdata->name) }}"> {{ substr(ucwords(@$tdata->name), 0, 7) }} </a> -->
							
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
          } 
          else if($tdata->event_id==3)
          {
	

            $interview_schedule_date	=	$newDate	=	$newTime =	$interview_mode	=	$interview_mode_child_level	= $interviewers = $panellistArr = 	"";
            $interview_schedule_date	=	$tdata->interview_schedule_date;
            $newDate 					=	date("d-M-Y", strtotime($interview_schedule_date));
            $newTime 					=	date("H:i A", strtotime($interview_schedule_date));
            
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
                        <td class="min-w-90px"><div class="d-flex flex-column">
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
                            </div>
                            Interviewer
                          </div></td>
                        <!--end::Interviewers=-->
                        <!--begin::Interviewer Feedback=-->
                        <td class="min-w-70px"><div class="d-flex flex-column"> <span class="text-dark fw-bolder  fs-6">{{ ($feedback) ? $feedback : '----' }}</span> <span class="fw-bold text-muted d-block fs-7">{{ ($available_status_name ? $available_status_name : '----') }}</span> </div></td>
                        <!--end::Interviewer Feedback-->
                        <!--begin::Soruce-->
                        <td class="min-w-50px"><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover justify-content-center">
                              <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}"> 
                                {{-- @if(!empty($posted_by_image))
							  	              <img alt="{{ ucwords(@$tdata->name) }}" src="{{ @$posted_by_image }}"> 
                                @else --}}
                              <div class="" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                <span class="text-dark fw-bolder fs-6">{{ substr($tdata->name, 0, 10)}}</span>
                                </div>
                              {{-- @endif --}}
							  </div>
                            </div>
                            <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{ ucwords(@$tdata->cur_companyname) }}">
								{{ substr(ucwords(@$tdata->cur_companyname), 0, 10) }}
							</a> 
							</div>
						</td>
                        <!--end::Soruce-->
                        <!--begin::Image-->
                        <td class="min-w-90px"><img src="{{ @$imagePath }}" alt="image" style="width: 5.5rem"> <span class="fw-bold text-muted d-block fs-7">{{ @$text }}</span> </td>
                        <!--end::Image-->
                        <!--begin::Date-->
                        <td class="min-w-140px">
						<div class="d-flex flex-column"> 
						<span class="text-dark fw-bolder fs-6"> {{ @$newDate}} </span> 
						<span class="text-muted fw-bold d-block fs-7"> {{ @$newTime }} </span> 
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
                                 ->select(
                                          'interview_feedback.created_at', 'interview_feedback.message', 'interview_feedback.star_ratings',
                                          'users.name', 'users.image' 
                                       )									
                                 ->join('users', 'users.id', '=', 'interview_feedback.update_by')	
                                 ->where('interview_feedback.application_id', @$tdata->application_id)
                                 ->where('interview_feedback.interview_id', @$tdata->interview_schedules_id)
                                 ->get(); 
            if(!empty($interview_feedback)){
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
                  <div class="overflow-auto pb-5">
                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                      <div class="mb-1"> 
					  	<a href="#" class="fs-5 fw-bold text-gray-800 text-hover-primary mb-2">
							<img src="{{ @$feedback_star_imagePath }}" alt="{{ @$feedback_star_text }}"  />&nbsp;Interview Feedback:
						</a>
                        <p> {{ $intfeed->message }} </p>
                        <div class="d-flex align-items-center mt-1 fs-6">
                          <div class="text-muted me-2 fs-7">Added at {{ @$newTime }} | {{ @$newDate }} by</div>
                          <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-boundary="window" data-placement="top" title="{{ @$intfeed->name }}"> 
                          @if(!empty($imagePath))
						  	             <img src="{{ @$imagePath }}" alt="{{ @$intfeed->name }}"> 
                             @else
                             <div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip" data-html="true" title="{{ ucwords(@$intfeed->name)}}" >
							<div class="symbol symbol-circle symbol-30px overflow-hidden me-2 border-radius-100" style="float:left">
							<a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
								<div class="symbol-label fs-8 fs-8 {{ $bgTextDecore}} border-radius-100">{{ substr($intfeed->name, 0, 1)}}</div>
							</a>		 
							</div>
							</div>
                             @endif
						  </div>
                        </div>
                      </div>
                    </div>
                  </div>
          <?php 
              }
            }	
          ?>
                </div>
                <!--end::Timeline content-->
              </div>
          <?php 
          }
          else if($tdata->event_id == 4)
          {
            
            $currency 				=	GeneralMethod::currency();
            $salaryMode 			=	GeneralMethod::salaryMode();
            
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
                                      <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->yearly_final_gross) }} {{ @$offer_mode }}</span>
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
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==1) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->create_at) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->create_at)) }}</span>
                                  </div>
                                </td>
                                
                                  
                                 <td>
                                    <div class="d-flex flex-column">  <span class="badge fw-bolder fs-6 {{ @$current_tiles_bg }} px-2 py-1">{{ ucwords(@$master_process_name) }}</span>  </div>
                                 </td>

                                <td class="min-w-50px"><div class="d-flex flex-column">
                                  <div class="symbol-group symbol-hover justify-content-center">
                                              <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="{{ ucwords(@$tdata->name) }}"> 
                                              
                                              <div class="" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="{{ ucwords(@$tdata->name)}}" >
                                                <span class="text-dark fw-bolder fs-6">{{ substr($tdata->name, 0, 10)}}</span>
                                                </div>
                                             

                                              </div>
                                                        </div>
                                                        <span class="text-muted fw-bold d-block fs-7" style="text-align: center;">{{ ucwords(@$tdata->cur_companyname) }}</span>
                                          <!--                     <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" -->
                                          <!--data-toggle="tooltip" title="{{ ucwords(@$tdata->name) }}"> {{ substr(ucwords(@$tdata->name), 0, 7) }} </a> -->
                                
                                  </div>
							                  </td>


                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
            <?php }else if($tdata->event_id == 5) {
                 $currency 				=	GeneralMethod::currency();
                $salaryMode 			=	GeneralMethod::salaryMode();
              
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
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->accept_date_time)) }}</span>
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
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==1) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->approved_date_time)) }}</span>
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
                $salaryMode 			=	GeneralMethod::salaryMode();
              
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
                                    <span class="text-dark fw-bolder fs-6">{{ @$offer_currency }} {{ (@$tdata->yearly_final_gross) }} {{ @$offer_mode }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{( @$tdata->employment_type==1) ? 'Contract' : 'Full-Time' }} </span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->joined_date) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ @joined_time }} </span>
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
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->accept_date_time)) }}</span>
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
                                    <span class="badge fw-bolder fs-6 {{ (@$tdata->management_status==1) ? 'joined' : 'offer' }}-bg px-2 py-1">{{ ucwords(@$management_status)
                                      }}</span>
                                  </div>
                                </td>
                                <td class="min-w-120px">
                                  <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ GeneralMethod::ymdTodmyNotTime(@$tdata->approved_date_time) }}</span>
                                    <span class="text-muted fw-bold text-muted d-block fs-7">{{ date("H:i:s", strtotime(@$tdata->approved_date_time)) }}</span>
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
          <?php } ?>

       <?php }
      } ?>

            </div>
          </div>
		  
		  
        </div>
		
        <div class="tab-pane" id="offer_tracker" role="tabpanel">
          <!--begin::Offer tracker header-->
          <div class="card-header cursor-pointer px-0">
            <!--begin::Card title-->
            <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Offer Tracker</h3>
            </div>
            <!--end::Card title-->
          </div>
          <!--end::Offer tracker header-->
          <!--begin::Content-->
          <div class="card-body px-0 py-7">
            <div class="row">Offer Tracker - Content</div>
          </div>
          <!--end::Content-->
        </div>
		
      	 </div>
	  
	  </div>
    </div>
  </div>
</div>

<script>
   $( document ).ready(function() {
   
   		$('body').on('click', '#cancel-note', function(){
   			$('#note_text').val(''); 
   		});
   
   		$('#add-note').click(function () {
   				var url 	= "{{ route('service_company.applicant-note.store') }}";
   				var id 		= {{$application->id}};
   				var note 	= $('#note_text').val();
   				var token 	= '{{ csrf_token() }}';
   				$.ajax({ 
   								type	: 'POST',
   								url		: url,
   								data	: {'_token':token, 'id':id, 'note': note},
   								success	: function (response) {
   																if(response.status == 'success') {
   																	$('#applicant-notes').html(response.view);
   																	$('#note_text').val('');
   																}
   								}
   				});
   		});
   
   		$('body').on('click', '.delete-note', function(){
   				let noteId = $(this).data('note-id');
   				
   								
   const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })
   
   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.isConfirmed) {
     
     
      
   						var url 		= "{{ route('service_company.applicant-note.destroy', ':id') }}";
   						url 			= url.replace(':id', noteId);
   						var token 		= '{{ csrf_token() }}';
   		
   						$.ajax({
   							type: 'POST',
   							url: url,
   							data: {'_token':token, '_method': 'DELETE'},
   							success: function (response) {
   								if(response.status == 'success') {
   									$('#applicant-notes').html(response.view);
   									
   									swalWithBootstrapButtons.fire(
   										'Deleted!',
   										'Your text note has been deleted.',
   										'success'
   									)
   	
   								}
   							}
   						});
   						
   	
   	
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
		   swalWithBootstrapButtons.fire(
			 'Cancelled',
			 'Your text note is safe :)',
			 'error'
		   )
     }
   })
   
   
});	
   		
   		$('body').on('click', '.edit-note', function() {
   	
         														$(this).hide();
         														let noteId = $(this).data('note-id');
         														$('body').find('#note-'+noteId+' .note-text').hide();
   
         														let noteText = $('body').find('#note-'+noteId+' .note-text').html();
         let textArea = '<textarea id="edit-note-text-'+noteId+'" class="form-control" row="4">'+noteText+'</textarea><a class="update-note btn btn-sm btn-light-success py-1 mt-2" data-note-id="'+noteId+'" href="javascript:void(0);">@lang("app.save")</a>';
         $('body').find('#note-'+noteId+' .note-textarea').html(textArea);
     
   });
   
   
   $('body').on('click', '.update-note', function () {
   	
         let noteId 	= $(this).data('note-id');
         var url 		= "{{ route('service_company.applicant-note.update', ':id') }}";
         url 			= url.replace(':id', noteId);
         var note 		= $('#edit-note-text-'+noteId).val();
         var token 		= '{{ csrf_token() }}';   
         $.ajax({
						  type		: 'POST',
						  url		: url,
						  data		: {'_token':token, 'noteId':noteId, 'note': note, '_method': 'PUT'},
						  success	: function (response) {
							  if(response.status == 'success') {
								  $('#applicant-notes').html(response.view);
							  }
						  }
         		});
     
   
   });
   
   $('body').on('click', '.resume_delete', function(){
   
   	
   			var application_id  		=	$(this).attr('rel');
     		var token       			=	'{{ csrf_token() }}';
   				 				
   				
		   const swalWithBootstrapButtons = Swal.mixin({
			 customClass: {
			   confirmButton: 'btn btn-success',
			   cancelButton: 'btn btn-danger'
			 },
			 buttonsStyling: false
		   })
   
   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.isConfirmed) {
     
     
      						var url     			=	"{{ route('service_company.job-applications.resume_delete') }}";
                     		var token     			=	"{{ csrf_token() }}";
                     		$.ajax({
   									  type  		: 'POST',
   									  url   		: url,
   									  data  		: {'_token': token, '_method': 'POST', 'application_id':application_id },
   									  success 		: function (response) {
   										  if (response.status == "success") {
   											
   											swalWithBootstrapButtons.fire(
   																			'Deleted!',
   																			'Your resume has been deleted.',
   																			'success'
   																		)
   									
   									
   										  } 
               						 }
             					});
   							
     } else if (
       				result.dismiss === Swal.DismissReason.cancel 
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Your text note is safe :)',
         'error'
       )
     }
   })

  });
  
  
 $('.sendmaimodal').click(function(){
  
		
  $('#application_send_mail_body_content').html('');
  $('#application_mass_mail_body_content').html('');
  $('#application_fwd_resume_body_content').html('');
  var id        	=	{{ @$application->jobId }};
  var sid        	=	{{ @$application->id }};
  var arr 		=	[sid];
  var jobIdArr = [id];
  $('.chk_list_item:checked').each(function () {
      
      var jobId = $(this).attr('data-jobId');
      arr.push($(this).val());
      jobIdArr.push(jobId);
  });

  console.log(jobIdArr);

  var id        	=	jobIdArr[0];
  var url       	=	"{{ route('service_company.cvtracker.send_mail',':id') }}";
  url         	=	url.replace(':id', id);
  var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
  var token       =	"{{ csrf_token() }}";

 
  $.ajax({
          type		: 'POST',
          url			: url,
          data		: {'_token': token, '_method': 'POST', 'arr' : arr,'jobIdArr':jobIdArr},
          cache		: false,
          success 	: function (response) {
                              if (response.status == "success") {
                                  $('#application_send_mail_body_content').html(response.view);
                                  
                                  $('#kt_activities_scroll').removeAttr('style');
                              }
                  }
    });
        
});


   });
</script> 