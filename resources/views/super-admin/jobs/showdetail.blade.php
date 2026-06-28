@php 
   use App\Helper\GeneralMethod;
@endphp
<style>
.text-justify {text-align: justify!important;}
</style>
<script>
   $("timeline-item:last-child").addClass("timeline-item:last-child");
</script>
@php 
		  
   $resume_url			=	'<i class="fa fa-download" aria-hidden="true" style="color:#003300"></i>';	
   $photo  				=	url('public/avatar.png');
   if(!empty($application->photo)){
      $photo			=	 url('/public/user-uploads/bench_photos/'.$application->photo);	
   }   
   $country_id			=	$country_name	= $state_name	=	$state	=	$city_name	=	$posted_by_company	=	'';	
   $country_id			=	@$benchApplication->country;
   $state				=	@$benchApplication->state;
   if(@$country_id > 0)
   {
			$country_name_Arr	= GeneralMethod::getCountryName($country_id);	
			foreach($country_name_Arr as $cval){
				$country_name	= ucwords($cval['name']);	
			}
		   
			if($country_id == 101){
				$state_name	= @$benchApplication->state_name;
					$city_name	= @$benchApplication->city_name; 	
				}else{		
				if( $state > 0){
									$state_name_Arr		= GeneralMethod::getStateName($country_id, $state);	
									foreach($state_name_Arr as $sval){
										$state_name	= ucwords($sval['name']);	
				}	
			}
         $city_name	= @$benchApplication->city;
	   }
   		$currencySymbole 	=	GeneralMethod::currencySymbole(); 
   		$current_ctc_cur	=	$current_ectc_cur	=	"";
   		$current_ctc_cur	=	$currencySymbole[@$benchApplication->pay_rate_currency];
	   	$current_ectc_cur	=	$currencySymbole[@$benchApplication->bill_rate_currency];
   }	
   	$posted_by_company		=	@$benchApplication->company_name; 												
@endphp 

<div class="card-header px-5 py-0" id="kt_activities_header">
	<div class="d-flex align-items-center py-1">
		<h3 class="card-title fw-bolder text-dark d-none">Candidate Profile</h3>
		<span class="h-20px border-gray-200 border-start mx-4 d-none"></span> 
	</div>
  	<div class="card-toolbar">
		<button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesBenchApplication_close"> <span class="svg-icon svg-icon-1">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
		  <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
			<rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
			<rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
		  </g>
		</svg>
		</span> </button>
  	</div>
</div>

<div class="card-body position-relative px-0 py-0" id="kt_activitiesBenchApplication_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
    <div class="card mb-5 mb-xl-5 mx-2">
      <div class="card-body pt-5 pb-0 px-5">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
          <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="{{ @$photo }}" alt="image">
              <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
              <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2">{{ ucwords(@$benchApplication->full_name) }}</span> </div>
                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                      <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                    </g>
                  </svg>
                  </span> {{ ucwords(@$benchApplication->total_exp) }} Years </a> 
				  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">                 
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <rect fill="#000000" opacity="0.3" x="2" y="2" width="10" height="12" rx="2"/>
                      <path d="M4,6 L20,6 C21.1045695,6 22,6.8954305 22,8 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,8 C2,6.8954305 2.8954305,6 4,6 Z M18,16 C19.1045695,16 20,15.1045695 20,14 C20,12.8954305 19.1045695,12 18,12 C16.8954305,12 16,12.8954305 16,14 C16,15.1045695 16.8954305,16 18,16 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span>
                 {!! @$current_ctc_cur !!} &nbsp; {{ @$benchApplication->pay_rate_amount }} - {{ @$benchApplication->bill_rate_amount }}
                  @php 
                                 $pay_rate_mode		=	"";
                                 $pay_rate_mode		=	@$benchApplication->pay_rate_mode;
                                 echo $salaryMode[$pay_rate_mode];			
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
              <div class="d-flex mt-4"> 
			  <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-light-success btn-active-primary fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-success svg-icon-2x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <polygon fill="#000000" opacity="0.3" points="5 15 3 21.5 9.5 19.5"/>
                    <path d="M13,2 C18.5228475,2 23,6.4771525 23,12 C23,17.5228475 18.5228475,22 13,22 C7.4771525,22 3,17.5228475 3,12 C3,6.4771525 7.4771525,2 13,2 Z M7.16794971,13.5547002 C8.67758127,15.8191475 10.6456687,17 13,17 C15.3543313,17 17.3224187,15.8191475 18.8320503,13.5547002 C19.1384028,13.0951715 19.0142289,12.4743022 18.5547002,12.1679497 C18.0951715,11.8615972 17.4743022,11.9857711 17.1679497,12.4452998 C16.0109146,14.1808525 14.6456687,15 13,15 C11.3543313,15 9.9890854,14.1808525 8.83205029,12.4452998 C8.52569784,11.9857711 7.90482849,11.8615972 7.4452998,12.1679497 C6.98577112,12.4743022 6.86159725,13.0951715 7.16794971,13.5547002 Z" fill="#000000"/>
                  </g>
                </svg>
                </span> Whatsapp </a> 
				<a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-light-primary btn-active-primary fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-primary svg-icon-2x">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"/>
                  </g>
                </svg>
                </span> Email </a> </div>
            </div>
            <div class="d-flex flex-wrap flex-stack">
              <div class="d-flex flex-column flex-grow-1 pe-8">
                <div class="row w-75"> <span class="">
					@php
						$skills				=	'';
						$skills				=	@$benchApplication->skills;
					@endphp	
					@if(!empty($skills))
						@php 
							$skillsArr		=	'';
							$skillsArr		=	explode(',', $skills);
						@endphp
						@foreach($skillsArr as $sval)
							{!! '<span class="badge badge-light me-3">'.@$sval.'</span>' !!}
						@endforeach
					@endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="separator separator-solid my-7"></div>
        <div class="d-flex align-items-center flex-wrap pb-7">
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" fill-rule="nonzero"/>
                <path d="M19.5,10.5 L21,10.5 C21.8284271,10.5 22.5,11.1715729 22.5,12 C22.5,12.8284271 21.8284271,13.5 21,13.5 L19.5,13.5 C18.6715729,13.5 18,12.8284271 18,12 C18,11.1715729 18.6715729,10.5 19.5,10.5 Z M16.0606602,5.87132034 L17.1213203,4.81066017 C17.7071068,4.22487373 18.6568542,4.22487373 19.2426407,4.81066017 C19.8284271,5.39644661 19.8284271,6.34619408 19.2426407,6.93198052 L18.1819805,7.99264069 C17.5961941,8.57842712 16.6464466,8.57842712 16.0606602,7.99264069 C15.4748737,7.40685425 15.4748737,6.45710678 16.0606602,5.87132034 Z M16.0606602,18.1819805 C15.4748737,17.5961941 15.4748737,16.6464466 16.0606602,16.0606602 C16.6464466,15.4748737 17.5961941,15.4748737 18.1819805,16.0606602 L19.2426407,17.1213203 C19.8284271,17.7071068 19.8284271,18.6568542 19.2426407,19.2426407 C18.6568542,19.8284271 17.7071068,19.8284271 17.1213203,19.2426407 L16.0606602,18.1819805 Z M3,10.5 L4.5,10.5 C5.32842712,10.5 6,11.1715729 6,12 C6,12.8284271 5.32842712,13.5 4.5,13.5 L3,13.5 C2.17157288,13.5 1.5,12.8284271 1.5,12 C1.5,11.1715729 2.17157288,10.5 3,10.5 Z M12,1.5 C12.8284271,1.5 13.5,2.17157288 13.5,3 L13.5,4.5 C13.5,5.32842712 12.8284271,6 12,6 C11.1715729,6 10.5,5.32842712 10.5,4.5 L10.5,3 C10.5,2.17157288 11.1715729,1.5 12,1.5 Z M12,18 C12.8284271,18 13.5,18.6715729 13.5,19.5 L13.5,21 C13.5,21.8284271 12.8284271,22.5 12,22.5 C11.1715729,22.5 10.5,21.8284271 10.5,21 L10.5,19.5 C10.5,18.6715729 11.1715729,18 12,18 Z M4.81066017,4.81066017 C5.39644661,4.22487373 6.34619408,4.22487373 6.93198052,4.81066017 L7.99264069,5.87132034 C8.57842712,6.45710678 8.57842712,7.40685425 7.99264069,7.99264069 C7.40685425,8.57842712 6.45710678,8.57842712 5.87132034,7.99264069 L4.81066017,6.93198052 C4.22487373,6.34619408 4.22487373,5.39644661 4.81066017,4.81066017 Z M4.81066017,19.2426407 C4.22487373,18.6568542 4.22487373,17.7071068 4.81066017,17.1213203 L5.87132034,16.0606602 C6.45710678,15.4748737 7.40685425,15.4748737 7.99264069,16.0606602 C8.57842712,16.6464466 8.57842712,17.5961941 7.99264069,18.1819805 L6.93198052,19.2426407 C6.34619408,19.8284271 5.39644661,19.8284271 4.81066017,19.2426407 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> 
			<span class="fw-bolder fs-6">Designation</span> 
			<span class="fw-bolder fs-3" data-toggle="tooltip" title="{{ ucwords((@$benchApplication->current_designation)) }}"> {{ ucwords(substr(@$benchApplication->current_designation, 0, 10)) }} </span> </div>
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
				<span class="fw-bolder fs-6">Origination</span> 
				<span class="fw-bolder fs-3" data-toggle="tooltip" title="{{ ucwords((@$benchApplication->company_name)) }}"> {{ ucwords(substr(@$benchApplication->company_name, 0, 10)) }} </span> 
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

            $npDate = $benchApplication->np_id;
                    if ($benchApplication->serving_notice_period_mode == 0){
                      $npDate = ($benchApplication->np_id==1) ? $benchApplication->np_id.' Day' : $benchApplication->np_id.' Days';
                    }elseif($benchApplication->serving_notice_period_mode == 1) {
                      $originalDate = $benchApplication->np_id;
                      $npDate = date("d-M-y", strtotime($originalDate));
                    }


              @endphp
              
              {{ $npDate }} &nbsp; </span> </div>
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
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Qualification</span> <span class="fw-bolder fs-3"> {{ ucwords(@$benchApplication->qualification_name) }} &nbsp;</span> </div>
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
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Status</span> <span class="fw-bolder fs-3">{{ ucwords(@$benchApplication->master_status_name) }}&nbsp;</span> </div>
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
                     $posted_by					=	@$benchApplication->company_name;
					 $posted_by_image			=	url('img/default.png');
                     if(!empty($application->posted_by_image)){	
                     	$posted_by_image		=	url('public/user-uploads/team_profile_img/'.@$benchApplication->posted_by_image);
                     }	       				  
            @endphp
            <div class="symbol-group symbol-hover">
              	<div class="symbol symbol1 symbol-30 symbol-circle" data-toggle="tooltip" title="" data-original-title="{{ @$posted_by }}"> 
			  		<img alt="Pic" src="{!! @$posted_by_image !!}"> 
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
            {{-- <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#timeline"><span class="nav-text font-weight-bold">Timeline</span></a> </li>
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#offer_tracker"><span class="nav-text font-weight-bold">Offer Tracker</span></a> </li> --}}
          </ul>
        </div>
      </div>
      <div class="card-body py-0">
       
	    <div class="tab-content pt-5">
          <div class="tab-pane active" id="personal_details" role="tabpanel">
            <div class="card-header cursor-pointer px-0">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Personal Details</h3>
              </div>
              
			  </div>
            <div class="card-body px-0 py-7">
              <div class="row">
                <div class="col-lg-6">
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Name</label>
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$benchApplication->full_name) }}</span> </div>
                  </div>
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Primary Number</label>
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ @$benchApplication->phone }}</span> </div>
                  </div>
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Primary Email</label>
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ @$benchApplication->email }}</span></div>
                  </div>
                 
                  <div class="row mb-7">                    
                    <label class="col-lg-4 fw-bold text-muted fs-6">Company</label>                    
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ ucwords((@$benchApplication->company_name)) }}</span></div>
                  </div>
                 
                  <div class="row mb-7">                    
                    <label class="col-lg-4 fw-bold text-muted fs-6">Desigination</label>                  
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ ucwords(@$benchApplication->current_designation) }}</span></div>
                  </div>
                 
                  <div class="row mb-7">                   
                    <label class="col-lg-4 fw-bold text-muted fs-6">Total Experience</label>                   
                    <div class="col-lg-8"><span class="fw-bolder text-gray-800 fs-6">{{ @$benchApplication->total_exp }} Years</span></div>                    
                  </div>
				                    
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6" data-toggle="tooltip" title="Relevant Experience">Rel. Experience</label>
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6"> {{ @$benchApplication->rel_exp }} Years</span> </div>
                  </div>
				  
                  <div class="row">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Pay Rate</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-lg-12"> <span class="fw-bolder text-gray-800 fs-6">{!! $current_ctc_cur !!}{{ @$benchApplication->pay_rate_amount }}
                          @php 
                                             $pay_rate_mode	=	"";
                                             $pay_rate_mode	=	@$benchApplication->pay_rate_mode;
                                             echo $salaryMode[$pay_rate_mode];			
                           @endphp 
                          </span> </div>
                        <div class="col-lg-3"> <span class="fw-bolder text-gray-800 fs-6"> &nbsp; </span> </div>
                        <div class="col-lg-7"> <span class="fw-bolder text-gray-800 fs-6">&nbsp;</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Bill Rate</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-lg-12"> <span class="fw-bolder text-gray-800 fs-6">{!! $current_ectc_cur !!} {{ @$benchApplication->bill_rate_amount }}
							@php 
									$bill_rate_mode	=	"";
									$bill_rate_mode	=	@$benchApplication->bill_rate_mode;
									echo $salaryMode[$bill_rate_mode];			
							@endphp 
                          </span> </div>
                        <div class="col-lg-3"> <span class="fw-bolder text-gray-800 fs-6"> &nbsp; </span> </div>
                        <div class="col-lg-7"> <span class="fw-bolder text-gray-800 fs-6"> &nbsp; </span>
                        </div>
                      </div>
                    </div>
                  </div>
                
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Comission type</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-lg-12"> 
							<span class="fw-bolder text-gray-800 fs-6">
								@php 
											$comission_type 	=	$comission_type_name	= 	'';
											$comission_type 	=	@$benchApplication->comission_type;
											if($comission_type == 1){
												$comission_type_name	= 	'Free';	
											}else if($comission_type == 2){
												$comission_type_name	= 	'Paid';	
											}
											echo @$comission_type_name; 
								@endphp
							</span> 
						</div>
                      </div>
                    </div>
                  </div>
                 
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Offer Amount</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-md-12"> <span class="fw-bolder text-gray-800 fs-6">----------</span> </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Resume</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-md-12"> <span class="fw-bolder text-gray-800 fs-6">						
							@if(!empty(@$benchApplication->resume))
								@php
										$resumePath			=	public_path('user-uploads/bench_resume/').@$benchApplication->resume;
										$resume_status		=	0;
										if (file_exists($resumePath)){
											$resume_status		=	1;
										}
								@endphp 
								<a type="button" class="btn btn-light-primary btn-sm py-1" href="{{ @$benchApplication->resume }}" target="_blank" download> 
									Download&nbsp;&nbsp;<i class="fa fa-download"></i>
								</a>
							@endif	
                          </span> </div>
                      </div>
                    </div>
                  </div>
                </div>
              
                <div class="col-lg-6">
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Notice Period</label>
                    <div class="col-lg-8"> 
						<span class="fw-bolder fs-6 text-gray-800 fs-6">						
							@php 
										$np				=	'';
							@endphp 						
							@if((@$benchApplication->serving_notice_period_mode == 0) || (@$benchApplication->serving_notice_period_mode == 1) )
								@php
										$np				=	@$benchApplication->np_id;
										$np 			=	'Available from '.date("dM Y", strtotime($np)); 
								@endphp
							@else
								@php
									$np					=	@$benchApplication->np_id;
								@endphp
							@endif		
							{{ $np }}				
                      </span> 
					</div>
                  </div>

                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Secondary Number</label>
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ @$benchApplication->secondary_mobile }} </span> </div>
                  </div>
                  
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Employee Number</label>
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ @$benchApplication->employee_number }} </span> </div>
                  </div>
				  
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Location</label>
                    <div class="col-lg-8">
                      <div class="row">
                        <div class="col-lg-12"> <span class="fw-bolder text-gray-800 fs-6">{{ @$city_name }} &nbsp; {{ ucwords(@$state_name) }}&nbsp; {{ ucwords(@$country_name) }}&nbsp; {{ $benchApplication->pincode }} </span> </div>
                      </div>
                    </div>
                  </div>
                 
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Qualification</label> 
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6"> {{ ucwords(@$benchApplication->qualification_name) }} </span> </div>
                  </div>
                
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Date of Birth</label>
                    <div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">
                      @php 
                                    $originalDate 	= @$benchApplication->dob;
                                    $newDate 		= date("d-M-Y", strtotime($originalDate)); 
                       @endphp 
					   {{ @$newDate }}                       
                      </span> </div>
                  </div>
                  
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Candidate ID</label>                   
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> C-{{ @$benchApplication->id }} </span> </div>
                  </div>
                  
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Reffered By</label>
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">{{ @$posted_by }}</span></div>
                  </div>

                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Hiring HR</label>
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">
						@if(!empty($bench_assign))
							@foreach($bench_assign as $cpn)
								{!! @$cpn->name .' &nbsp;&nbsp; ' !!}
							@endforeach	
						@endif
                      </span> </div> 
                  </div>
                 
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Added Date</label>
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">
                      @php 
                                    $originalDate 	= @$benchApplication->created_at;
                                    $newDate 		= date("d-M-Y", strtotime($originalDate)); 
                      @endphp
					  {{ @$newDate }}              
                      </span> </div>
                  </div>
                 
				  
                  <div class="row mb-7">                   
                    <label class="col-lg-4 fw-bold text-muted fs-6">Last Active Date</label>
                    <div class="col-lg-8"> 
						<span class="fw-bolder text-gray-800 fs-6">
						@php
									$updated_at			=	@$benchApplication->updated_at;
									$updated_at 		=	date("d-M-Y", strtotime($updated_at)); 
						@endphp					
						{{ ucwords(@$updated_at) }}
						</span> 
					</div>
                  </div>
				  
                  <div class="row mb-7">
                    <label class="col-lg-4 fw-bold text-muted fs-6">Status</label>
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6"> {{ ucwords(@$benchApplication->master_status_name) }} </span> </div>   
                  </div>
                </div>
                
              </div>
              <div class="card-title m-0 border-bottom py-7">
                <h3 class="fw-bolder m-0">Custom Details</h3>
              </div>
              <div class="row pt-7 border-bottom pb-7">
@php 
		   if(!empty($job_additional_detail)){
			
				$customTitleId	=	1;
				$html		=	'';
				$inc_first	=	0;
				$inc_last	=	1;
				foreach($job_additional_detail as $aDetailArr){
					
					if($inc_first %2 == 0) {
						$html	.=	'<div class="col-lg-6">';	
					}	
					$html		.=	'<div class="row mb-7">
													<label class="col-lg-4 fw-bold text-muted fs-6">'.strtoupper($aDetailArr->custom_keys).'</label> 
													<div class="col-lg-8"> <span class="fw-bolder text-gray-800 fs-6">'.strtoupper($aDetailArr->custom_keys_value).'</span> </div>
											</div>';
									 
					if(($inc_last %2 == 0)&& $inc_last > 0){
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
			                
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
