@php 
	use App\Helper\GeneralMethod;
@endphp
<style>
@media only screen and (min-width: 900px) and (max-width: 2560px){
  .table td, .table th, .table tr	{width: max-content!important;}
  .table > :not(caption) > * > *	{	padding: 0.75em 0rem!important;}
  td:nth-child(n+2), th:nth-child(n+2){width: revert!important;	border-bottom: 1px dashed #f5f8fa!important;}
}
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_client_id" id="hid_client_id" value="{{$directClientDetail->client_id}}" />
  <!--begin::Content-->
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
    <!--beign::candidate detail-->
    <div class="card mb-5 mb-xl-5 mx-2">
      <div class="card-body pt-5 pb-0 px-5">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
          <!--begin: Pic-->
          <div class="me-7 mb-4">
            <!-- <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                  <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="client_logo">
                  <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                </div>-->
@php 					  
			$bgTextDecore			=	$strVal	=	'';
			$str					=	trim(@$directClientDetail->name);
			$str	= $strVal		=	strtolower($str[0]);
			$str					=	ord($str);
			if($str >=97 && $str <=101){
				$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
			}else if($str >=102 && $str <=106){
				$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
			}else if($str >=107 && $str <=111){
				$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
			}else if($str >=112 && $str <=116){
				$bgTextDecore		=	'fs-3 bg-light-success text-success';	
			}else if($str >=117 && $str <=121){
				$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
			}else {
				$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
			}	
			
			$background_image		=	"";
			if(!empty(@$directClientDetail->logo_logical_path) && strlen(@$directClientDetail->logo_logical_path)>10){
						$background_image		=  url('public/'.@$directClientDetail->logo_logical_path); 	
			} 
			
			$logo_path				=	url('img/client-default.png');
			if(!empty(@$directClientDetail->logo) && strlen(@$directClientDetail->logo)>10){
				$logo_path			=  @$directClientDetail->logo; 	
			} 
			
			$logo_path_raw	=	'';
			if(!empty($logo_path)){
				$logo_path_raw	= '<img src="'.@$logo_path.'" height="100px" width="100px"  />';
			}else{
				$logo_path_raw	=	'<div class="symbol-label fs-3 '.@$bgTextDecore.' border-radius-5"><span>'.ucwords(@$strVal).'</span></div>'; 
			}
	
@endphp
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> 
				<a href="javascript:void(0);">{!! $logo_path_raw !!}</a>
              <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
            </div>
          </div>
          <!--end::Pic-->
          <!--begin::Info-->
          <div class="flex-grow-1">
            <!--begin::Row-1-->
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
              <!--begin::Candidate Details-->
              <div class="d-flex flex-column">
                <!--begin::Name & Candidate ID-->
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2">
                  {{ ucwords(@$directClientDetail->name) }}
                  </span> 
				  
				  <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                  <span class="svg-icon svg-icon-1 svg-icon-success">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                    <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                    <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                  </svg>
                  </span>
                  </a> 
				  </div>
                
				
               
                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> 
				<a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">  
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                      <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                      <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
                    </g>
                  </svg>
                  </span>                
                    @php 
                        $industries_name		=	'';		
                        if(trim($directClientDetail->industry) !=''){						
                          $industries_name		=	$department[@$directClientDetail->industry];
                        }
                        echo $industries_name;
                    @endphp
                  </a> 
				  
				  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                    <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                  </svg>
                  </span>
				  {{ ucwords($city_name) }}{{ ' , ' }}{{ ucwords($country_name) }}
                  </a> 
				  
				  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span>
                  Badge
				  </a> 
				  </div>
              </div>
              <!--end::Candidate Details-->
              <!--begin::Actions-->
              <div class="d-flex mt-4">
                <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">Active</a>
              </div>
              <!--end::Actions-->
            </div>
            <!--end::Row-1-->
            <!--begin::Row-2-->
            <div class="d-flex flex-wrap flex-stack">
              <!--begin::Wrapper-->
              <div class="d-flex flex-column flex-grow-1 pe-8">
                <div class="row w-75"> <span class="">
                  @php 
                        $tags	=	@$directClientDetail->tags;
                        $tags	= 	explode(',', $tags);
                        foreach($tags as $key=>$val)
                        {
                          echo '<span class="badge badge-light">'.ucwords($val).'</span>  ';	
                        }
                  @endphp
                  </span> </div>
              </div>
              <!--end::Wrapper-->
            </div>
            <!--end::Row-2-->
          </div>
          <!--end::Info-->
        </div>
        <!--begin::separator-->
        <div class="separator separator-solid my-7 d-none"></div>
        <!--end::separator-->
        <!--begin::section-->
        <div class="d-flex align-items-center flex-wrap pb-7 d-none">
          <!--begin: Item1-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
                <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
                <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Company Type</span> <span class="fw-bolder fs-3">             
                @if($directClientDetail->company_type > 0)
                  {{ $companyType[@$directClientDetail->company_type] }}
                @endif				  
              </span> </div>
          </div>
          <!--end: Item1-->
          <!--begin: Item2-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"/>
                <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Employees</span> <span class="fw-bolder fs-3">{{ number_format(@$directClientDetail->company_size) }}</span> </div>
          </div>
          <!--end: Item2-->
          <!--begin: Item3-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Active Requisition</span> <span class="fw-bolder fs-3">{{ number_format(@$active_requisition) }}</span> </div>
          </div>
          <!--end: Item3-->
          <!--begin: Item4-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M7,3 L17,3 C19.209139,3 21,4.790861 21,7 C21,9.209139 19.209139,11 17,11 L7,11 C4.790861,11 3,9.209139 3,7 C3,4.790861 4.790861,3 7,3 Z M7,9 C8.1045695,9 9,8.1045695 9,7 C9,5.8954305 8.1045695,5 7,5 C5.8954305,5 5,5.8954305 5,7 C5,8.1045695 5.8954305,9 7,9 Z" fill="#000000"/>
                <path d="M7,13 L17,13 C19.209139,13 21,14.790861 21,17 C21,19.209139 19.209139,21 17,21 L7,21 C4.790861,21 3,19.209139 3,17 C3,14.790861 4.790861,13 7,13 Z M17,19 C18.1045695,19 19,18.1045695 19,17 C19,15.8954305 18.1045695,15 17,15 C15.8954305,15 15,15.8954305 15,17 C15,18.1045695 15.8954305,19 17,19 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Founded</span> <span class="fw-bolder fs-3">
@php 
			$currentDate 	= $foundedDate		= $finalDate	=	0; 
			$currentDate	=	date('Y'); 
			$foundedDate	=	@$directClientDetail->founded;
			$finalDate		= 	$currentDate-$foundedDate;
			echo number_format(@$finalDate); 
@endphp
              Years ago</span> </div>
          </div>
          <!--end: Item4-->
          <!--begin: Item5-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M14.1654881,7.35483745 L9.61055177,10.3622525 C9.47921741,10.4489666 9.39637436,10.592455 9.38694497,10.7495509 L9.05991526,16.197949 C9.04337012,16.4735952 9.25341309,16.7104632 9.52905936,16.7270083 C9.63705011,16.7334903 9.74423017,16.7047714 9.83451193,16.6451626 L14.3894482,13.6377475 C14.5207826,13.5510334 14.6036256,13.407545 14.613055,13.2504491 L14.9400847,7.80205104 C14.9566299,7.52640477 14.7465869,7.28953682 14.4709406,7.27299168 C14.3629499,7.26650974 14.2557698,7.29522855 14.1654881,7.35483745 Z" fill="#000000"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Joined</span> <span class="fw-bolder fs-3">
@php 
			$currentDate 	=   $createDate		= $finalDate	=	0; 
			$currentDate	=	date('Y'); 
			$createDate		=	date('Y', strtotime(@$directClientDetail->to_created_at));
			$finalDate		= 	$currentDate-$createDate;
			echo number_format(@$finalDate); 
@endphp
              Years ago</span> </div>
          </div>
          <!--end: Item5-->
          <!--begin: Item-->
          <div class="d-flex align-items-center flex-lg-fill my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="symbol-group symbol-hover">

@foreach($client_contact_info as $cci)
	<div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ @$cci->contact_person_name }}">
	<img alt="Pic" src="{{ url('public/avatar.png')}}"> 
	</div>;
@endforeach

            </div>
          </div>
          <!--end: Item-->
        </div>
        <!--end::sections-->
        <!--end::Details-->
      </div>
    </div>
    <!--end::candidate details-->
    <!--begin::Tabs-->
    <div class="card card-custom mx-2">
      <!--Begin::Tabs Header-->
      <div class="card-header card-header-tabs-line">
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
            <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab" href="#overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
          </ul>
        </div>
      </div>
      <!--end::Tabs Header-->
      <!--Begin::Body-->
      <div class="card-body py-0">
        <div class="tab-content pt-5">
          <!--begin::Overview Content-->
          <div class="tab-pane active" id="overview" role="tabpanel">
            <!--begin::Overview header-->
            <div class="card-header cursor-pointer px-0">
              <!--begin::Card title-->
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Description</h3>
              </div>
              </div>
            <!--end::Overview header-->
            <div class="card-body px-0 py-7">
              <!--begin::Overview Content-->
              <div class="row">
                <!--begin::Col-Desc-->
                <div class="col-lg-12 mb-7">
                    <?php 
                        $description	=	'';
                        $description	=	@$directClientDetail->description;
                        echo $description;
                    ?>
                </div>
                <div class="col-lg-12">
                  <!--end::Col-Desc-->
                  <!--begin::Row-1-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Website</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <a href="javascript:void(0);" class="fw-bold fs-6 text-primary"><?php echo @$directClientDetail->website; ?></a> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-1-->
                  <!--begin::Row-2-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted"> <?php echo $industries_name; ?> </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-2-->
                  <!--begin::Row-3-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted"> <?php echo number_format(@$directClientDetail->company_size); ?>+ employees, </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-3-->
                  <!--begin::Row-4-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Headquarters</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
<?php 				  				
		echo ucwords($city_name);	
		echo ' , ';
		echo ucwords($country_name);								
?>
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-4-->
                  <!--begin::Row-5-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Type</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
<?php 
		if($directClientDetail->company_type > 0)
		{
			echo $companyType[@$directClientDetail->company_type]; 	
		}
		
?>
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-5-->
                  <!--begin::Row-6-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Founded</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> 
						<span class="fw-bold fs-6 text-muted">
<?php 
		$currentDate 	= $foundedDate		= $finalDate	=	0; 
		$currentDate	=	date('Y'); 
		$foundedDate	=	@$directClientDetail->founded;
		$finalDate		= 	$currentDate-$foundedDate;
		echo number_format(@$finalDate); 
?> Years ago
						</span> 
					</div> 
                    <!--end::Col-->
                  </div>
                  <!--end::Row-6-->
                  <!--begin::Row-5-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Specialties</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
<?php 
		$tags	=	@$directClientDetail->tags;
		$tags	= 	explode(',', $tags);
		$cnt	=	0;
			foreach($tags as $key=>$val)
			{
				if($cnt >0){
					echo ", ";
				}
				echo '<span class="">'.ucwords($val).'</span> ';
				$cnt++;	
		}
?>
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-5-->
                </div>
                <!--begin::Right Section-->
                <div class="col-lg-6 d-none">
                  <!--begin::Row-1-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-4 fw-bold text-muted fs-6">Notice Period</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-8"> <span class="fw-bolder fs-6 text-gray-800 fs-6">15 Days</span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-1-->
                </div>
                <!--end::Right Section-->
              </div>
              <!--end::Overview Content-->
            </div>
          </div>
          <!--end::Overview Content-->
         
    </div>
    <!--end::Body-->
  </div>
  <!--end:Tabs-->
</div>
<!--end::Content-->
</div>
<!--end::Body-->
<!--begin::Footer-->
<div class="card-footer py-5 text-center d-none" id="kt_activities_footer"> <a href="../../demo1/dist/pages/profile/activity.html" class="btn btn-bg-body text-primary">View All Activities
  <!--begin::Svg Icon | path: icons/duotone/Navigation/Right-2.svg-->
  <span class="svg-icon svg-icon-3 svg-icon-primary">
  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
      <polygon points="0 0 24 0 24 24 0 24" />
      <rect fill="#000000" opacity="0.5" transform="translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000)" x="7.5" y="7.5" width="2" height="9" rx="1" />
      <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
    </g>
  </svg>
  </span>
  <!--end::Svg Icon-->
  </a> </div>
<!--end::Footer-->
</div>
<script>
		
  	var table = $('#contactList').dataTable({		
	   		responsive  	: true,
			processing  	: true,
			serverSide  	: true,
			destroy   		: true,
			ordering		: false, 							//disable ordering			
			"bLengthChange" : false, 							//thought this line could hide the LengthMenu
			sDom			: 'lrtip',
			oLanguage 		: {
									sLengthMenu: "_MENU_",
							  },
									
		ajax: {'url' : '{!! route('service_company.clients.contactdata') !!}?clientId='+<?php echo @$client->id; ?>,
				"data": function ( d )
					{
						d.search = $('#inputSearch').val()
					}
				},       
	   "drawCallback": function( settings ) {
				
						$('[data-toggle="tooltip1"]').tooltip();
						$('[data-toggle="tooltip2"]').tooltip();
				},									  
        columns: [
					{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
					{ data: 'Name', name: 'Name', searchable : false, orderable : false},
					{ data: 'Role', name: 'Role', searchable : false, orderable : false},
					{ data: 'Contacts', name: 'Contacts', searchable : false, orderable : false},
					{ data: 'Status', name: 'Status', searchable : false, orderable : false}
        ]
    });
    new $.fn.dataTable.FixedHeader( table );
	
</script>
<script>

   var table = $('#allreq').dataTable({
     
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
           	"pageLength": 25,
   			colReorder		: true,
      		ajax			: {'url' : '{!! route('service_company.jobs.data') !!}',
								"data"			: function ( d )
												{
													d.search 		=	$('#inputSearch').val(),
													d.client_id 	=	$('#hid_client_id').val()
												}
   					},
    
   				"drawCallback": function( settings ) {
   				
   						$('[data-toggle="tooltip1"]').tooltip();
   						$('[data-toggle="tooltip2"]').tooltip();
   						$('[data-toggle="tooltip3"]').tooltip();
   						$('[data-toggle="tooltip4"]').tooltip();
   						
   				},  			
   
   				"columnDefs":
   						  [ 
   									{ "width": "50px", "targets": 0},
   									{  "width": "100px", "targets": 1, "class":"d-flex align-items-center border-0" },																				
   									{  "width": "50px"  , "targets": 2},		
   									{  "width": "50px", "targets": 3 },		
   									{  "width": "200px", "targets": 4 }, 
   									{  "width": "50px" ,"targets": 5 },
   									{  "width": "200px" , "targets": 6 },              
   									{  "width": "150px" , "targets": 7 },	
   									{  "width": "150px", "targets": 8 },	
   										
   									{ "width": "20px", "targets": 9 },	
   									{ "width": "20px", "targets": 10 }, 
   									{ "width": "20px", "targets": 11 },    
   									
   									{ "width": "20px", "targets": 12 },             
   									{ "width": "20px", "targets": 13 },
   									{ "width": "20px", "targets": 14 },   
   									{ "width": "20px", "targets": 15 }, 
   									{ "width": "20px", "targets": 16 }
   							  ], 
   				columns:
   				[
   				  	{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   					{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   					{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   					{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   					{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
   					
   					{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   					{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   					{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
   					
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
   new $.fn.dataTable.FixedHeader( table );
	
</script>
