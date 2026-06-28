@extends('layouts.product_company_layout')
<?php 
   use App\Helper\GeneralMethod;
?>
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
<style>
body
{
  overflow-x:hidden;
}
.border-bottom-dashed
{
  border-bottom: 1px dashed #eff2f5;
}
/*begin::Responsive-Candidate*/
.table td, .table th, .table tr
{
  width: max-content!important;
  padding: 0em;
}
/*end::Responsive-Candidate*/

@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  td:nth-child(n+2), th:nth-child(n+2)
  {
    width: revert!important;
  }
}  

</style>


<style>
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesJob
  {
    width: 1025px;
  }
}
@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesJob
  {
    width: 1200px;
  }
}


</style>
<style>
#send_email .modal-dialog
{
  margin: .3rem auto!important;
}
#fwd_resume .modal-dialog
{
  margin: .3rem auto!important;
}
/*begin::Media query*/
@media  only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0rem;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
  }
}
/*end::Media query*/

@media  only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesApplication
  {
    width: 1025px;
    /* width: 1070px; */
  }
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesApplication
  {
    width: 1200px;
  }
}



@media  only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_client_activitiesApplication
  {
    width: 1025px;
  }
}
@media  only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_client_activitiesApplication
  {
    width: 1200px;
  }
}



/*end::Media query*/
</style>
@endpush
@section('content')
<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
  <!--begin::Container-->
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <!--begin::Page title-->
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <!--begin::My Profile-->
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <span class="text-gray-800 mb-1">My Profile</span> </h1>
      </div>
      <!--end::My Profile-->
      <!--begin::Separator-->
      <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      <!--end::Separator-->
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
      <!--beign::User Details-->
      <div class="card mb-5 mb-xl-5 mx-2">
        <div class="card-body pt-5 pb-0 px-5">
          <!--begin::Details-->
          <?php 
                  $background_image					=	url('public/user-uploads/team_profile_img/avatar.png'); //'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';
                  if(!empty(@$users->image) && strlen(@$users->image)>10){
                   			$background_image			=  url('public/user-uploads/team_profile_img/'.@$users->image);  	
                   	}
                  ?>
          <?php 
                  $country_id			=	$country_name	= $state_name	=	$state	=	$city_name	=	'';	
                  $country_id			=	$users->country;	  	//@$application->country;
                  $state				=	$users->state;    		//@$application->state; users    
                  if(@$country_id > 0)
                  {
                  	   $country_name_Arr	= GeneralMethod::getCountryName($country_id);	
                   foreach($country_name_Arr as $cval){
                  $country_name		= ucwords($cval['name']);	
                   } 
                   if($country_id == 101)
                   {
                   		$state_name	= @$users->state_name;
                  $city_name	= @$users->city_name; 	
                   }
                  	   else
                   {  			
                  	if( $state > 0){
                  		$state_name_Arr		= GeneralMethod::getStateName($country_id, $state);	
                  		foreach($state_name_Arr as $sval){
                  			$state_name	= ucwords($sval['name']);	
                  		}	
                  	}
                  	$city_name	= @$users->city; 
                   }	
                  
                  } 
                  
                  ?>
          <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
              <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="<?php echo @$background_image; ?>" alt="<?php echo ucwords(@$users->name); ?>">
                <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
              </div>
            </div>
            <!--end::Pic-->
            <!--begin::Info-->
            <div class="flex-grow-1">
              <!--begin::Row-1-->
              <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                <!--begin::User Details-->
                <div class="d-flex flex-column">
                  <!--begin::User Name-->
                  <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"><?php echo ucwords(@$users->name); ?></span> <span class="text-muted fw-bolder fs-7">C-<?php echo ucwords(@$users->id); ?></span> </div>
                  <!--end::User Name-->
                  <!--begin::Designaiton-->
                  <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                    <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                        <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      </g>
                    </svg>
                    </span> <?php echo ucwords(@$users->designation); ?> </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000"/>
                      </g>
                    </svg>
                    </span> <?php echo ucwords(@$city_name); ?>, <?php echo ucwords(@$state_name); ?>, <?php echo ucwords(@$countryName); ?> </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-4 me-1">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                      </g>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Badge </a> </div>
                  <!--end::Designaiton-->
                </div>
                <!--end::User Details-->
                <!--begin::Action-->
                <div class="d-flex mt-4">
                  <!--begin::Whatsapp-->
                  <!--begin::Svg Icon-->
                  <?php 
                              if($users->status == 'active')
                              {
                              ?>
                  <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light-success fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  </span> Active </a>
                  <?php 
                              }
                              else
                              {
                              ?>
                  <a href="javascript:void(0);" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"></polygon>
                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                    </g>
                  </svg>
                  </span> Inactive </a>
                  <?php 
                              } 
                              ?>
                  <!--end::Svg Icon-->
                  <!--end::Whatsapp-->
                  <!--begin::Email-->
                  <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-primary fw-bolder me-3 fs-6">
                  <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                  <span class="svg-icon svg-icon-primary svg-icon-2x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"></rect>
                      <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
                    </g>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Email </a>
                  <!--end::Email-->
                </div>
                <!--end::Action-->
              </div>
              <!--end::Row-1-->
              <!--begin::Row-2-->
              <div class="d-flex flex-wrap flex-stack d-none">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column flex-grow-1 pe-8">
                  <div class="row w-75"> <span class=""> <b>Skills/Tags :</b> <span class="badge badge-light">HTML5</span> <span class="badge badge-light">Bootstrap5</span> <span class="badge badge-light">CSS3</span> <span class="badge badge-light">JQuery</span> <span class="badge badge-light">Javascript</span> <span class="badge badge-light">Photoshop</span> <span class="badge badge-light">Illustator</span> </span> </div>
                </div>
                <!--end::Wrapper-->
              </div>
              <!--end::Row-2-->
            </div>
            <!--end::Info-->
          </div>
          <!--end::Details-->
        </div>
      </div>
      <!--end::User Details-->
      <!--begin::Tabs-->
      <div class="card card-custom mx-2">
        <!--Begin::Tabs Header-->
        <div class="card-header card-header-tabs-line">
          <div class="card-toolbar">
            <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
              <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#requisitions"> <span class="nav-text font-weight-bold">Requisitions</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#candidate"> <span class="nav-text font-weight-bold">Candidate</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interview_tracker"> <span class="nav-text font-weight-bold">Interview Tracker</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#joinings"> <span class="nav-text font-weight-bold">Joinings</span> </a> </li>
              <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#settings"> <span class="nav-text font-weight-bold">Settings</span> </a> </li>
            </ul>
          </div>
        </div>
        <!--end::Tabs Header-->
        <!--Begin::Body-->
        <div class="card-body py-0">
          <div class="tab-content pt-5">
            <!--begin::Overview Content-->
            <div class="tab-pane active show" id="overview" role="tabpanel">
              <!--begin::Overview header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Overview</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <a href="{{route('product_company.profile.edit', [$user_id])}}" class="btn btn-primary align-self-center">Edit Profile</a>
                <!--end::Action-->
              </div>
              <!--end::Overview header-->
              <div class="card-body px-0 py-7">
                <!--begin::Full Name-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Full Name</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->name); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Full Name-->
                <!--begin::User ID-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">User ID</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">
				  <?php 
							$encoded_query_string = base64_encode(gzdeflate(@$users->id));
							//$decoded_query_string = gzinflate(base64_decode($encoded_query_string));
				  			echo $encoded_query_string; 
				  ?>
				  </span> </div>
                  <!--end::Col-->
                </div>
                <!--end::User ID-->
                <!--begin::Designation-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Designation</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->designation); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Designation-->
                <!--begin::Email-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Email</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->email; ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Email-->
                <!--begin::Phone-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Phone</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->mobile; ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Phone-->
                <!--begin::Company Name-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Company Name</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->company_name); ?> </span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Company Name-->
                <!--begin::Company Site-->
                <div class="row mb-7">
                  <!--begin::Label-->
                  <label class="col-lg-3 fw-bold text-muted fs-6">Company Site</label>
                  <!--end::Label-->
                  <!--begin::Col-->
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$users->website); ?></span> </div>
                  <!--end::Col-->
                </div>
                <!--end::Company Site-->
                <div class="row mb-7">
                  <label class="col-lg-3 fw-bold text-muted fs-6">Country</label>
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$countryName); ?></span> </div>
                </div>
                <div class="row mb-7">
                  <label class="col-lg-3 fw-bold text-muted fs-6">State</label>
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$state_name); ?></span> </div>
                </div>
                <div class="row mb-7">
                  <label class="col-lg-3 fw-bold text-muted fs-6">City</label>
                  <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords(@$city_name); ?></span> </div>
                </div>
              </div>
            </div>
            <!--end::overview Content-->
            <!--begin::Requisitions Content-->
            <div class="tab-pane" id="requisitions" role="tabpanel">
              <!--begin::Requisitions header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Requisitions</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Member</a> -->
                <!--end::Action-->
              </div>
              <!--end::Requisitions header-->
              <!--begin::Requisitions Content-->
              <div class="card-body px-0 py-7">
                <table id="load_jobs" class="table align-middle table-row-dashed fs-6 gy-5">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div></th>
                      <th class="">Requisition</th>
                      <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                      <th class="">Salary</th>
                      <th class="">Location</th>
                      <th class="">Status</th>
                      <th class="" data-toggle="tooltip" title="Date | Openings">Date</th>
                      <th class="" data-toggle="tooltip" title="Hiring Manager | Contact Person">HM | CP</th>
                      <th class="" data-toggle="tooltip" title="Staffing Partners">S.Partners</th>
                      <th class="" data-toggle="tooltip" title="Total Applicant">TOT</th>
                      <th class="" data-toggle="tooltip" title="Sourcing">SOU</th>
                      <th class="" data-toggle="tooltip" title="Screening">SCR</th>
                      <th class="" data-toggle="tooltip" title="Evaluation">EVA</th>
                      <th class="" data-toggle="tooltip" title="Negotiation">NEO</th>
                      <th class="" data-toggle="tooltip" title="Offer">OFF</th>
                      <th class="" data-toggle="tooltip" title="Joining">JOI</th>
                      <th class="" data-toggle="tooltip" title="Rejected">REJ</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                </table>
              </div>
              <!--end::Requisitions Content-->
            </div>
            <!--end::Requisitions Content-->
            <!--begin::Candidate Content-->
            <div class="tab-pane" id="candidate" role="tabpanel">
              <!--begin::Candidate header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Candidate</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Edit Candidate</a> -->
                <!--end::Action-->
              </div>
              <!--end::Candidate header-->
              <!--begin::Candidate Content-->
              <div class="card-body px-0 py-7">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="load_application">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div></th>
                      <th class="">Name</th>
                      <th class="">Contacts</th>
                      <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                      <th class="">Salary</th>
                      <th class="" data-toggle="tooltip" title="Notice Period">N.P</th>
                      <th class="">Qualifaction</th>
                      <th class="">Location</th>
                      <th class="">Source</th>
                      <th class="" data-toggle="tooltip" title="Upload Date">Date</th>
                      <th class="">Requisitions</th>
                      <th class="">Stage</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                </table>
              </div>
              <!--end::Candidate Content-->
            </div>
            <!--end::Candidate Content-->
            <!--begin::Interview Tracker Content-->
            <div class="tab-pane" id="interview_tracker" role="tabpanel">
              <!--begin::Interview Tracker header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Interview Tracker</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
                <!--end::Action-->
              </div>
              <!--end::Interview Tracker header-->
              <!--begin::Interview Tracker Content-->
              <div class="card-body px-0 py-7">
                <table id="load_interviewSchedule" class="table align-middle table-row-dashed fs-6 gy-5">
                  <thead>
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div></th>
                      <th class="">Name</th>
                      <th class="">Requisitions</th>
                      <th class="" data-toggle="tooltip" title="Interview Mode">Mode</th>
                      <th class="">Interviewers</th>
                      <th class="">Source</th>
                      <!-- <th class="" data-toggle="tooltip" title="Interview Date">Date</th> -->
                      <th class="">Interview Date</th>
                      <th class="">Feedback</th>
                      <th class="">Rating</th>
                      <th class="">Stage</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!--end::Interview Tracker Content-->
            </div>
            <!--end::Interview Tracker  Content-->
            <!--begin::Joinings Content-->
            <div class="tab-pane" id="joinings" role="tabpanel">
              <!--begin::Joinings header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Joinings</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
                <!--end::Action-->
              </div>
              <!--end::Joinings header-->
              <!--begin::Joinings Content-->
              <div class="card-body px-0 py-7">
                <table id="myTable_joining" class="table align-middle table-row-dashed fs-6 gy-5">
                  <thead>
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div></th>
                      <th class="">Name</th>
                      <th class="">Contact</th>
                      <th class="">Requisitions</th>
                      <th class="">Source</th>
                      <th class="">Offer Amount</th>
                      <th class="">Joining Date</th>
                      <th class="">Stage</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!--end::Joinings Content-->
            </div>
            <!--end::Joinings Content-->
            <!--begin::Settings Content-->
            <div class="tab-pane" id="settings" role="tabpanel">
              <form  id="setting_content" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
                  <div class="card-body px-0 pt-0 pb-0">
                    <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
                      <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
                        <div class="card-title m-0">
                          <h3 class="fw-bolder m-0">Sign-in Method</h3>
                        </div>
                      </div>
                      <div id="kt_account_signin_method" class="collapse show">
                        <div class="card-body border-top py-8 px-2">
                          <div class="d-flex flex-wrap align-items-center">
                            <div id="kt_signin_email">
                              <div class="fs-6 fw-bolder mb-1">Email Address</div>
                              <div class="fw-bold text-gray-600"><?php echo @$users->email; ?></div>
                            </div>
                          </div>
                          <div class="separator separator-dashed my-6"></div>
                          <div class="d-flex flex-wrap align-items-center mb-10">
                            <div id="kt_signin_password">
                              <div class="fs-6 fw-bolder mb-1">Password</div>
                              <div class="fw-bold text-gray-600">************</div>
                            </div>
                            <div id="kt_signin_password_button" class="ms-auto"> <a href="javascript:void(0);" class="btn btn-light btn-active-light-primary reset_password" data-toggle="modal" data-target="#reset_password">Reset Password</a> </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
                      <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
                        <div class="card-title m-0">
                          <h3 class="fw-bolder m-0">Connected Accounts</h3>
                        </div>
                      </div>
                      <div id="kt_account_connected_accounts" class="collapse show">
                        <div class="card-body border-top py-8 px-2">
                          <div class="py-2">
                            <div class="d-flex flex-stack">
                              <div class="d-flex"> <img src="{{ asset('img/images/google-icon.svg') }}" class="w-30px me-6" alt="" />
                                <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google</a>
                                  <div class="fs-6 fw-bold text-gray-400">Plan properly your workflow</div>
                                </div>
                              </div>
                              <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-switch">
                                  <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" <?php if($users->google_status == 1) { echo 'checked="checked"';} ?>  disabled="disabled">
                                  <label class="form-check-label" for="googleswitch"></label>
                                </div>
                              </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex flex-stack">
                              <div class="d-flex"> <img src="{{asset('img/images/github.svg')}}" class="w-30px me-6" alt="" />
                                <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Zoom</a>
                                  <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Repositories</div>
                                </div>
                              </div>
                              <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-switch">
                                  <input class="form-check-input w-45px h-30px" type="checkbox" id="githubswitch" <?php if($users->zoom_status == 1) { echo 'checked="checked"';} ?> disabled="disabled">
                                  <label class="form-check-label" for="githubswitch"></label>
                                </div>
                              </div>
                            </div>
                            <div class="separator separator-dashed my-5"></div>
                            <div class="d-flex flex-stack">
                              <div class="d-flex"> <img src="{{asset('img/images/slack-icon.svg')}}" class="w-30px me-6" alt="" />
                                <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Microsoft</a>
                                  <div class="fs-6 fw-bold text-gray-400">Integrate Projects Discussions</div>
                                </div>
                              </div>
                              <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-switch">
                                  <input class="form-check-input w-45px h-30px" type="checkbox" id="slackswitch" <?php if($users->microsoft_status == 1) { echo 'checked="checked"';} ?> disabled="disabled" />
                                  <label class="form-check-label" for="slackswitch"></label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <!--end begin::Settings Content-->
          </div>
        </div>
        <!--end::Body-->
      </div>
      <!--end::Tabs-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->
<!--begin::Reset password Modal-->
<div class="modal" id="reset_password" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-650px">
    <div class="modal-content">
      <div class="modal-header" id="add_team_member_header">
        <h2 class="fw-bolder">Reset Password</h2>
        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-dismiss="modal"> <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span> </div>
      </div>
      <div class="modal-body scroll-y px-5 px-xl-8 py-5">
        <form id="mteam_member_form" class="form" action="#">
          <div class="alert alert-success alert-dismissible fade show" id="success-alert" style="display:none"> <strong>Success!</strong> Password Reset successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <div class="alert alert-danger alert-dismissible fade show" id="warning-alert" style="display:none"> <strong>Warning!</strong> Both password must me same
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          <input type="hidden" name="mteam_id" id="mteam_id" value="" />
          <div class="row mb-1">
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="currentpassword" class="form-label fs-6 fw-bolder mb-3">Name</label>
                <input type="text" class="form-control form-control-lg form-control-solid bg-light-warning" name="mteamname" id="mteamname" readonly="" >
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="currentpassword" class="form-label fs-6 fw-bolder mb-3">Email</label>
                <input type="text" class="form-control form-control-lg form-control-solid bg-light-warning" name="mteamemail" id="mteamemail" readonly="" >
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="newpassword" class="form-label fs-6 fw-bolder mb-3">New Password</label>
                <input type="password" class="form-control form-control-lg form-control-solid" name="mteampassword" id="mteampassword">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="fv-row mb-0 fv-plugins-icon-container">
                <label for="confirmpassword" class="form-label fs-6 fw-bolder mb-3">Confirm New Password</label>
                <input type="password" class="form-control form-control-lg form-control-solid" name="mteamcpassword" id="mteamcpassword">
                <div class="fv-plugins-message-container invalid-feedback"></div>
              </div>
            </div>
          </div>
          <div class="form-text mb-5">Password must be at least 8 character and contain symbols</div>
          <div class="d-flex">
            <button id="m_resetpassword_submit" type="button" class="btn btn-primary me-2 px-6">Update Password</button>
            <button id="m_resetpassword_cancel" type="button" data-dismiss="modal" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--end::Reset password Modal-->
<input type="hidden" name="userId" id="userId" value="<?php echo @$users->id; ?>" />
<?php 
   //echo '<pre>'; print_r($users->name);  
   ?>
   
   
<div id="kt_activitiesJob_toggle"></div>
<div id="kt_activitiesJob" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesJob_toggle" data-kt-drawer-close="#kt_activitiesJob_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Requisition</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesJob_close">
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <!--end::Header-->
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesJob_body"></div>
  </div>
</div>

<div class="modal fade" id="stage_sub_stage" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">	
      <div class="modal-header flex-stack">
        <h2>Stage and sub stage</h2>
        <div class="btn btn-sm btn-icon btn-active-color-primary close" data-dismiss="modal"  id="stage_sub_stage_close">  
          <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
          </span>
        </div>
      </div>
	 <div class="stage_body_content" id="stage_body_content"></div> 	  
    </div>
  </div>
</div>


<div id="kt_activitiesStaffing_toggle"></div>
<div id="kt_activitiesStaffing" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesStaffing_toggle" data-kt-drawer-close="#kt_activitiesStaffing_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Staffing Partners</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesStaffing_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activities_body"> </div>
  </div>
</div>

<div id="kt_activitiesHM_toggle"></div>
<div id="kt_activitiesHM" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'500px', 'lg': '1200px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesHM_toggle" data-kt-drawer-close="#kt_activitiesHM_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">HM | CP</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesHM_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1"></rect>
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1"></rect>
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesHM_body"> </div>
  </div>
</div>

  
@endsection
@push('footer-script')
<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
   $('.dropify').dropify({
       messages: {
           default: '@lang("app.dragDrop")',
           replace: '@lang("app.dragDropReplace")',
           remove: '@lang("app.remove")',
           error: '@lang('app.largeFile')'
       }
   });
   
   $('body').on('click', '#change-mobile', function () {
       $.easyAjax({
           url: "{{ route('changeMobile') }}",
           container: '#verify-mobile',
           type: "GET",
           success: function (response) {
               $('#verify-mobile').html(response.view);
               $('.selectpicker').selectpicker({
                   style: 'btn-info',
                   size: 4
               });
           }
       })
   });
   
   $('#save-form').click(function () {
       $.easyAjax({
           url: '{{route('product_company.profile.update', $user->id)}}',
           container: '#editSettings',
           type: "POST",
           redirect: true,
           file: true
       })
   });
</script>
<script>
   var x = '';
   
   function clearLocalStorage() {
       localStorage.removeItem('otp_expiry');
       localStorage.removeItem('otp_attempts');
   }
   
   function checkSessionAndRemove() {
       $.easyAjax({
           url: '{{ route('removeSession') }}',
           type: 'GET',
           data: {'sessions': ['verify:request_id']}
       })
   }
   
   function startCounter(deadline) {
       x = setInterval(function() {
           var now = new Date().getTime();
           var t = deadline - now;
   
           var days = Math.floor(t / (1000 * 60 * 60 * 24));
           var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
           var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
           var seconds = Math.floor((t % (1000 * 60)) / 1000);
   
           $('#demo').html('Time Left: '+minutes + ":" + seconds);
           $('.attempts_left').html(`${localStorage.getItem('otp_attempts')} attempts left`);
   
           if (t <= 0) {
               clearInterval(x);
               clearLocalStorage();
               checkSessionAndRemove();
               location.href = '{{ route('product_company.profile.index') }}'
           }
       }, 1000);
   }
   
   if (localStorage.getItem('otp_expiry') !== null) {
       let localExpiryTime = localStorage.getItem('otp_expiry');
       let now = new Date().getTime();
   
       if (localExpiryTime - now < 0) {
           clearLocalStorage();
           checkSessionAndRemove();
       }
       else {
           $('#otp').focus().select();
           startCounter(localStorage.getItem('otp_expiry'));
       }
   }
   
   function sendOTPRequest() {
       $.easyAjax({
           url: '{{ route('sendOtpCode.account') }}',
           type: 'POST',
           container: '#request-otp-form',
           messagePosition: 'inline',
           data: $('#request-otp-form').serialize(),
           success: function (response) {
               if (response.status == 'success') {
                   localStorage.setItem('otp_attempts', 3);
   
                   $('#verify-mobile').html(response.view);
                   $('.attempts_left').html(`3 attempts left`);
   
                   let html = `<div class="alert alert-info mb-0" role="alert">
                       @lang('messages.info.verifyAlert')
                       <a href="{{ route('product_company.profile.index') }}" class="btn btn-warning">
                           @lang('menu.profile')
                       </a>
                   </div>`;
   
                   $('#verify-mobile-info').html(html);
                   $('#otp').focus();
   
                   var now = new Date().getTime();
                   var deadline = new Date(now + parseInt('{{ config('nexmo.settings.pin_expiry') }}')*1000).getTime();
   
                   localStorage.setItem('otp_expiry', deadline);
                   // intialize countdown
                   startCounter(deadline);
               }
               if (response.status == 'fail') {
                   $('#mobile').focus();
               }
           }
       });
   }
   
   function sendVerifyRequest() {
       $.easyAjax({
           url: '{{ route('verifyOtpCode.account') }}',
           type: 'POST',
           container: '#verify-otp-form',
           messagePosition: 'inline',
           data: $('#verify-otp-form').serialize(),
           success: function (response) {
               if (response.status == 'success') {
                   clearLocalStorage();
   
                   $('#verify-mobile').html(response.view);
                   $('#verify-mobile-info').html('');
   
                   // select2 reinitialize
                   $('.selectpicker').selectpicker({
                       style: 'btn-info',
                       size: 4
                   });
               }
               if (response.status == 'fail') {
                   // show number of attempts left
                   let currentAttempts = localStorage.getItem('otp_attempts');
   
                   if (currentAttempts == 1) {
                       clearLocalStorage();
                   }
                   else {
                       currentAttempts -= 1;
   
                       $('.attempts_left').html(`${currentAttempts} attempts left`);
                       $('#otp').focus().select();
                       localStorage.setItem('otp_attempts', currentAttempts);
                   }
   
                   if (Object.keys(response.data).length > 0) {
                       $('#verify-mobile').html(response.data.view);
   
                       // select2 reinitialize
                       $('.selectpicker').selectpicker({
                           style: 'btn-info',
                           size: 4
                       });
   
                       clearInterval(x);
                   }
               }
           }
       });
   }
   
   $('body').on('submit', '#request-otp-form', function (e) {
       e.preventDefault();
       sendOTPRequest();
   })
   
   $('body').on('click', '#request-otp', function () {
       sendOTPRequest();
   })
   
   $('body').on('submit', '#verify-otp-form', function (e) {
       e.preventDefault();
       sendVerifyRequest();
   })
   
   $('body').on('click', '#verify-otp', function() {
       sendVerifyRequest();
   })
</script>

<script>
   $(document).ready(function() {
   
   	//$("#success-alert").hide();
   	$('body').on('click', '.reset_password', function(){
   
   							
   						$('#mteampassword').val('');
   						$('#mteamcpassword').val('');
   								
   						var  id					=	$('#userId').val();
   						$('#mteam_id').val(id);
   						var url   				= "{{ route('product_company.team.info',':id') }}";
   						url     				= url.replace(':id', id);
   		
   						var token 				= "{{ csrf_token() }}";							
   						var baseurl    			= "{{ asset('assets/images/Loading2.gif') }}"; 
   						$.ajax({
   									type		: 'GET',
   									url			: url,
   									beforeSend: function() {
   											  
   														},
   									//data		: {'_token': token, 'userId':userId},
   									success		: function (response) {
   																		if (response.status == "success") {
   																												$('#mteamname').val(response.team.name);
   																												$('#mteamemail').val(response.team.email);	
   																																																										
   																											
   																										  }
   																 }
   					});
   										
   				
   	});
   	
   	$('body').on('click', '#m_resetpassword_submit', function(){
   														
   							   
   								var mteam_id				=	$('#mteam_id').val();
   								var mteampassword			=	$('#mteampassword').val();
   								var mteamcpassword			=	$('#mteamcpassword').val();
   								
   								if(mteampassword != mteamcpassword)
   								{
   										$('#mteampassword').val('');	
   										$('#mteamcpassword').val('');
   										$("#warning-alert").fadeTo(2000, 500).slideUp(500, function() {
   											$("#warning-alert").slideUp(500);
   										});	
   										return false;
   								}
   								var url 					= "{{ route('product_company.team.resetpassword') }}";
   								var token 					= "{{ csrf_token() }}";
   								
   								var baseurl    				= "{{ asset('assets/images/Loading2.gif') }}"; 
   												
   								$.ajax({
   											type		: 'POST',
   											url			: url,
   											beforeSend: function() {
   														 // $('#add_interview_sub_stage_form').hide();
   														 // $('#add_interview_sub_stage_form_loader').show();
   														 // $('#add_interview_sub_stage_form_loader').html('<img src="'+baseurl+'" height="60%" width="100%">');
   												},
   											data		: {'_token': token, 'mteam_id':mteam_id, 'mteampassword':mteampassword, 'mteamcpassword':mteamcpassword },
   											success		: function (response) {
   																					if (response.status == "success") {
   																															$('#mteampassword').val('');	
   																															$('#mteamcpassword').val('');
   																															$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
   																																$("#success-alert").slideUp(500);
   																															});
   																															 
   																															 
   																														
   																													  }
   																			 }
   								});
   												
   							  
   					
   						
   										
   				
   	});
   	
   });	
   	
</script>

<script>

 	// JOIN DATA  
   var jointable;
   jointableLoad('load');
   function jointableLoad()
   {
      var userId		=	$('#userId').val();
      table 			=	$('#myTable_joining').dataTable(
															  {
																	responsive  	: true,
																	processing  	: true,
																	serverSide  	: true,
																	destroy   		: true,
																	ordering		: false, //disable ordering
																	
																		"bLengthChange" : false, //thought this line could hide the LengthMenu
																	  sDom			: 'lrtip',
																	  oLanguage 		: {
																	sLengthMenu: "_MENU_",
																	},
    						
																  ajax: '{!! route('product_company.team.joiningdata') !!}?userId='+userId,
																  language:
																  {
																	  "url": "<?php echo __("app.datatable") ?>"
																  },
																  "fnDrawCallback": function (oSettings)
																  {
																	  $("body").tooltip(
																	  {
																		  selector: '[data-toggle="tooltip"]'
																	  });
																  },        
															  columns:
															  [
																{data: 'chkbox', name:'chkbox', width: '5%', orderable: false, searchable: false},
																{data: 'Name', name:'Name', width: '20%', orderable: false, searchable: false},
																{data: 'Contact', name:'Contact', width: '15%', orderable: false, searchable: false},
																{data: 'Requisitions', name:'Requisitions', width: '15%', orderable: false, searchable: false},
																{data: 'Source', name:'Source', width: '10%', orderable: false, searchable: false},
																{data: 'Offer', name:'Offer', width: '10%', orderable: false, searchable: false},
																{data: 'Joining', name:'Joining', width: '10%', orderable: false, searchable: false},
																{data: 'Stage', name:'Stage', width: '10%', orderable: false, searchable: false}   
															  ]
      												});
          			new $.fn.dataTable.FixedHeader(jointable);
   }	
   
</script>

<script>

	//	REQUISITION DATA
   	var userId		=	$('#userId').val();  
   	var load_jobs 	=	$('#load_jobs').dataTable({   
   		responsive  	: true,
   		processing  	: true,
   		serverSide  	: true,
   		destroy   		: true,
   		ordering		: false, //disable ordering   			
   		"bLengthChange" : false, //thought this line could hide the LengthMenu
   		sDom			: 'lrtip',
   		oLanguage 		: {sLengthMenu: "_MENU_",},   
     		ajax: {'url' : '{!! route('product_company.team.jobdata') !!}?userId='+userId,
   				  "data": function ( d )
   				  {
   					d.search = $('#inputSearch').val()
   				  }
   				},   
   		language:{"url": "<?php echo __("app.datatable") ?>"},	  
   		"drawCallback": function( settings ) {   				
   					$('[data-toggle="tooltip1"]').tooltip();
   					$('[data-toggle="tooltip2"]').tooltip();
   					$('[data-toggle="tooltip3"]').tooltip();
   					$('[data-toggle="tooltip4"]').tooltip();		
   			},  
   		"columnDefs":
   					  [ 
   								{ "width": "50px", "targets": 0, "width": "3%", "class":""},
   								{  "width": "100px", "targets": 1, "class":"d-flex align-items-center w-100 border-bottom-dashed" },																		
   								{  "width": "50px"  , "targets": 2},		
   								{  "width": "50px", "targets": 3 },		
   								{  "width": "200px", "targets": 4 }, 
   								{  "width": "50px" ,"targets": 5 },
   								{  "width": "250px" , "targets": 6 },              
   								{  "width": "100px" , "targets": 7 },
   
   								{ "width": "5px", "targets": 8 },									
   								{ "width": "5px", "targets": 9 },	
   								{ "width": "5px", "targets": 10 }, 
   								{ "width": "5px", "targets": 11 },   								
   								{ "width": "5px", "targets": 12 },             
   								{ "width": "5px", "targets": 13 },
   								{ "width": "5px", "targets": 14 },   
   								{ "width": "5px", "targets": 15 }, 
   								{ "width": "5px", "targets": 16 }
   						  ], 
   			columns:
   			[
   			  { data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   				{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   				{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   				{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   				{ data: 'Location', name: 'Location' , orderable: false, searchable: false},   
   				{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   				{ data: 'Date', name: 'Date' , width: '90px', orderable: false, searchable: false},
   				{ data: 'HM', name: 'HM' , width: '50px', orderable: false, searchable: false},   				
   				{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},   				
   				{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
   				{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
   				{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},   				
   				{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
   				{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
   				{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
   				{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
   				{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}
   				        
   			  	   
   			]
   			
   			});    
   	new $.fn.dataTable.FixedHeader( load_jobs );   

	$('body').on('click', '.show-job-detail', function ()
   	{
   	
		$('#kt_activitiesJob_toggle').trigger('click');		
		var id    		= $(this).data('row-id');
		var url   		= "{{ route('product_company.jobs.viewmore',':id') }}";
		url     		= url.replace(':id', id);
		var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
   											$('#kt_activitiesJob_body').html(response.view);
   										}
   								}, 500);
     		  					
   						}
   	  			});
   	}); 
		   		   		   
</script>


<script>

	//	INTERVIEW DATA
 	load_interviewSchedule();
   	var table;
   	function load_interviewSchedule(){
         
   	var userId		=	$('#userId').val();
   
           table   = $('#load_interviewSchedule').dataTable({
           
   		responsive  : true,
   		processing  : true,
   		serverSide  : true,
   		destroy   : true,
   		ordering: false, //disable ordering
   		"bLengthChange" : false, //thought this line could hide the LengthMenu
   		sDom: 'lrtip',
   		oLanguage : {sLengthMenu: "_MENU_",},
               		ajax    : '{!! route('product_company.team.intdata') !!}?userId=' + userId, 
              
               		"fnDrawCallback": function( settings, start, end, max, total, pre ) {
   	var total_record	=	this.fnSettings().fnRecordsTotal();
   	$('#total_record').html(total_record);					
   	$("body").tooltip({
                       selector: '[data-toggle="tooltip"]'
                   });
               },
       
     
           "columnDefs": [
   			{"targets": 0 },
   			{"targets": 1, "class":"align-items-center" }, 
   			{"targets": 2 },
   			{ "targets": 3 },
   			{ "targets": 4 },
   			{ "targets": 5 },            
   			{ "targets": 6 },
   			{ "targets": 7 },
   			{ "targets": 8 },
   			{ "targets": 9 }					
   		  ],     
       
     columns: [
     { data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},  
     { data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
     { data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
     { data: 'InterviewMode', name: 'InterviewMode',width: '10%', searchable : false, orderable: false},
     { data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
     { data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
     { data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
     { data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
     { data: 'Rating', name: 'Rating',width: '10%', searchable : false, orderable: false},		  
     { data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
     ],
              
           });
           new $.fn.dataTable.FixedHeader( table );
   
       }
	$('body').on('click', '.custom-show-application-detail', function ()
         {
         	$('#kt_activitiesApplication_toggle').trigger('click');
         	var id    		= $(this).data('row-id');
         	var url 		= "{{ route('product_company.job-applications.show',':id') }}";
         	url     		= url.replace(':id', id);		   
         	var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
         	$.ajax({
         					type  : 'GET',
         					url   : url,
         					beforeSend: function() {
                                          $('#application_show_right_bar').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            	},
         					success : function (response) 
         					{
         						$('#application_show_right_bar').html(response.view);	
         					}
           			});
         }); 
		 		       
</script>

<script>

	//	APPLICATION DATA
  	var userId			=	$('#userId').val();
  	var table 		 	=	$('#load_application').dataTable({
     								
   													responsive     		: true, 
   													processing      	: true,
   													ordering			: false, //disable ordering
   													
   													destroy       		: true,   
   													stateSave     		: true,
   													
   													"bLengthChange"   	: false,   
   													sDom				: 'lrtip',       
   
         ajax: '{!! route('product_company.team.appdata') !!}?userId='+userId,             
                  language: {
                      "url": "<?php echo __("app.datatable") ?>"
                  },
          
                  "fnDrawCallback": function (oSettings) {
                      $("body").tooltip({
                          selector: '[data-toggle="tooltip"]'
                      });
                  },         
         
         "initComplete":function( settings, json){ 
         
           $('.clientStatusLoader').html('');
           $('.jobLoader').html('');
           $('.sourceLoader').html('');
           
           $('.search_np_idLoader').html('');
           $('.search_passportLoader').html('');
           $('.statusLoader').html('');            
           
         }, 				  
           columns: [
              
   		{data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
   		{data: 'Name', name: 'Name',  orderable: false, searchable: false},						
   		{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false},						
   		{data: 'Exp', name: 'Exp', searchable : false, orderable : false},
   		{data: 'Salary', name: 'Salary',  searchable : false, orderable : false},   
   		{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
   		{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},   					
   		{data: 'Location', name: 'Location', orderable : false},  												 
   		{data: 'Source', name: 'Source', orderable: false},
   		{data: 'UploadDate', name: 'UploadDate', orderable: false},   		
   		{data: 'Requisitions', name: 'Requisitions', orderable: false},
   		{data: 'Stage', name: 'Stage', orderable: false}
   	]         
     
            }); 				
  	new $.fn.dataTable.FixedHeader( table );	
					   
</script>

<script>

		
$('body').on('click', '.stage_sub_stage', function ()   
{

	
       
               var id       = $(this).data('row-id');			   
               var url    	= "{{ route('product_company.job-applications.stage_body_content',':id') }}";
               url      	= url.replace(':id', id);
               $.ajax({
                        	type  : 'GET',
                        	url   : url,
                        	success : function (response) {
                                            if (response.status == "success") 
                                  			{
                                                  $('#stage_body_content').html(response.view);
                                            }
                                        }
               });
        
        
        
           

}); 

		   	
			
</script>





<script>

load_jobs.on('click', '.hm-listing', function ()
   	{
   	
   		$('#kt_activitiesHM_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.hmlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activitiesHM_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
   											$('#kt_activitiesHM_body').html(response.view);
   										}
   								}, 500);
     		  					
   						}
   	  			});
   	});

load_jobs.on('click', '.spartner-listing', function ()
   	{
   	
   		$('#kt_activitiesStaffing_toggle').trigger('click');		
   		var id    		= $(this).data('row-id');
   		var url   		= "{{ route('product_company.jobs.spartnerlisting',':id') }}";
   		url     		= url.replace(':id', id);
   		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
   		$.ajax({
   						type  : 'GET',
   						url   : url,
   						 beforeSend: function() {
                                     $('#kt_activities_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                       	},
   						success : function (response) 
   						{
   								setTimeout(
   								()=>
   								{
   										if (response.status == "success")
   										{
   											$('#kt_activities_body').html(response.view);
   										}
   								}, 500);
     		  					
   						}
   	  			});
   	}); 
		
</script>
@endpush