<?php 
	use App\Helper\GeneralMethod;
?>
<style>
@media only screen and (min-width: 900px) and (max-width: 2560px)
{
  .table td, .table th, .table tr
  {
    width: max-content!important;
  }
  .table > :not(caption) > * > *
  {
    padding: 0.75em 0rem!important;
  }
  td:nth-child(n+2),
  th:nth-child(n+2)
  {
    width: revert!important;
    border-bottom: 1px dashed #f5f8fa!important;
  }
}
</style>

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">

<input type="hidden" name="hid_client_id" id="hid_client_id" value="{{$client->id}}" />
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
            <?php 					  
					$bgTextDecore			=	$strVal	=	'';
					$str					=	trim(@$client->name);
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
					if(!empty(@$client->logo_logical_path) && strlen(@$client->logo_logical_path)>10){
   								$background_image		=  url('public/'.@$client->logo_logical_path); 	
   					}  
				
				?>
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> 
				<a href="javascript:void(0);">
					<?php 
							if(!empty($background_image)){
								echo '<img src="'.@$background_image.'" height="100px" width="100px"  />';
							}else{
								echo	'<div class="symbol-label fs-3 '.@$bgTextDecore.' border-radius-5"><span>'.ucwords(@$strVal).'</span></div>'; 
							}
					?>             		
               </a>
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
                  <?php 
								echo ucwords(@$client->name); 
						?>
                  </span> <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Verified">
                  <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                  <span class="svg-icon svg-icon-1 svg-icon-success">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                    <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="#00A3FF"></path>
                    <path class="permanent" d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  </a> </div>
                <!--end::Name & Candidate ID-->
                <!--begin::Designaiton-->
                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                  <!--begin::Svg Icon-->
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
                  <!--end::Svg Icon-->
                  <?php 
					  			$industries_name		=	'';		
								if(trim($client->industry) !=''){						
					  				$industries_name		=	$department[@$client->industry];
								}
								echo $industries_name;
					  ?>
                  </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                    <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  <?php 				  				
								echo ucwords($city_name);	
								echo ' , ';
								echo ucwords($country_name);								
				  ?>
                  </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  Badge</a> </div>
                <!--end::Designaiton-->
              </div>
              <!--end::Candidate Details-->
              <!--begin::Actions-->
              <div class="d-flex mt-4">
                <!--begin::Active-->
                <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">Active</a>
                <!--end::Active-->
                <!--begin::Inactive-->
                <!-- <a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6">Inactive</a>-->
                <!--end::Inactive-->
              </div>
              <!--end::Actions-->
            </div>
            <!--end::Row-1-->
            <!--begin::Row-2-->
            <div class="d-flex flex-wrap flex-stack">
              <!--begin::Wrapper-->
              <div class="d-flex flex-column flex-grow-1 pe-8">
                <div class="row w-75"> <span class="">
                  <?php 
					  			$tags	=	@$client->tags;
								$tags	= 	explode(',', $tags);
								foreach($tags as $key=>$val)
								{
									echo '<span class="badge badge-light">'.ucwords($val).'</span>  ';	
								}
					  ?>
                  </span> </div>
              </div>
              <!--end::Wrapper-->
            </div>
            <!--end::Row-2-->
          </div>
          <!--end::Info-->
        </div>
        <!--begin::separator-->
        <div class="separator separator-solid my-7"></div>
        <!--end::separator-->
        <!--begin::section-->
        <div class="d-flex align-items-center flex-wrap pb-7">
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
              <?php 
			  					//echo @$client->company_type;
								if($client->company_type > 0)
								{
									echo $companyType[@$client->company_type]; 	 
								}	
						?>
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
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Employees</span> <span class="fw-bolder fs-3"><?php echo number_format(@$client->company_sizes); ?></span> </div>
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
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Active Requisition</span> <span class="fw-bolder fs-3"><?php echo number_format(@$active_requisition); ?></span> </div>
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
              <?php 
							$currentDate 	= $foundedDate		= $finalDate	=	0; 
							$currentDate	=	date('Y'); 
							$foundedDate	=	@$client->founded;
							$finalDate		= 	$currentDate-$foundedDate;
							echo number_format(@$finalDate); 
					?>
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
              <?php 
							$currentDate 	=   $createDate		= $finalDate	=	0; 
							$currentDate	=	date('Y'); 
							$createDate		=	date('Y', strtotime(@$client->created_at));
							$finalDate		= 	$currentDate-$createDate;
							echo number_format(@$finalDate); 
					?>
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
<?php 
		foreach($client_contact_info as $cci){
			echo '<div class="symbol symbol1 symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="'.@$cci->contact_person_name.'">
			<img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg">
			</div>';
		}
?>
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
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#contacts"> <span class="nav-text font-weight-bold">Contacts</span> </a> </li>
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#requisitions"> <span class="nav-text font-weight-bold">Requisitions</span> </a> </li>
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
                <h3 class="fw-bolder m-0">Client</h3>
              </div>
              <!--end::Card title-->
			  
		
@if(in_array("edit_clients", $userPermissions))			  
<!--begin::Action-->
<a href="<?php echo route('admin.clients.edit', $client->id); ?>" class="btn btn-primary align-self-center">Edit Client</a>
<!--end::Action-->
@endif


            </div>
            <!--end::Overview header-->
            <div class="card-body px-0 py-7">
              <!--begin::Overview Content-->
              <div class="row">
                <!--begin::Col-Desc-->
                <div class="col-lg-12 mb-7">
                  <?php 
						   			$description	=	'';
						   			$description	=	@$client->description;
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
                    <div class="col-lg-9"> <a href="javascript:void(0);" class="fw-bold fs-6 text-primary"><?php echo @$client->website; ?></a> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-1-->
                  <!--begin::Row-2-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Industry</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted">
                      <?php echo $industries_name; ?>
                      </span> </div>
                    <!--end::Col-->
                  </div>
                  <!--end::Row-2-->
                  <!--begin::Row-3-->
                  <div class="row mb-7">
                    <!--begin::Label-->
                    <label class="col-lg-3 fw-bolder text-gray-800 fs-6">Company Size</label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9"> 
						<span class="fw-bold fs-6 text-muted">
							<?php echo @$client->company_sizes; ?>+ employees, 
						</span> 
					</div>
					
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
									if($client->company_type > 0)
									{
										echo $companyType[@$client->company_type]; 	
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
                    <div class="col-lg-9"> <span class="fw-bold fs-6 text-muted"><?php echo @$client->founded; ?></span> </div>
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
								$tags	=	@$client->tags;
								$tags	= 	explode(',', $tags);
								$cnt=0;
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
		  
          <!--begin::Contacts Content-->
          <div class="tab-pane" id="contacts" role="tabpanel">
            <!--begin::Contacts header-->
            <div class="card-header cursor-pointer px-0">
              <!--begin::Card title-->
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Contact List</h3>
              </div>
              <!--end::Card title-->
              <!--begin::Action-->
              <!--<a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Contact</a>-->
              <!--end::Action-->
            </div>
            <!--end::Contacts header-->
            <!--begin::Contacts Content-->
            <div class="card-body px-0 py-7">
              <!--begin::Table-->
              <table class="table align-middle table-row-dashed fs-6 gy-5" id="contactList">
                <!--begin::Table head-->
                <thead>
                  <!--begin::Table row-->
                  <tr class="fw-bolder text-muted">
                    <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input2" value="1" />
                      </div></th>
                    <th class="min-w-150px">Name</th>
                    <th class="min-w-150px">Role</th>
                    <th class="min-w-150px">Contacts</th>
                    <th class="min-w-15px">Status</th>
                  </tr>
                  <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="text-gray-600 fw-bold">
                  <!--begin::Table row-->
                  <tr>
                    <!--begin::Checkbox-->
                    <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input form-check-input2" type="checkbox" value="1" />
                      </div></td>
                    <!--end::Checkbox-->
                    <!--begin::Name=-->
                    <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                        <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">P</div>
                        </a> </div>
                      <!--end::Avatar-->
                      <!--begin::User details-->
                      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Priyanka</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">Recruiter</a> </div>
                      <!--begin::User details-->
                    </td>
                    <!--end::Name-->
                    <!--begin::Role-->
                    <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Hiring Manager</span> </div></td>
                    <!--end::Role-->
                    <!--begin::Contacts-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">+91 8989898998</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">priyanka@test.com</a> </div></td>
                    <!--end::Contacts-->
                    <!--begin::Status-->
                    <td class=""><div class="form-check form-switch form-switch-sm form-check-custom form-check-solid d-none">
                        <input class="form-check-input" type="checkbox" value="" name="staus" checked="checked">
                      </div>
                      <span class="bg-light-danger text-danger fw-bolder fs-6 px-1">Inactive</span> </td>
                    <!--end::Status-->
                  </tr>
                  <!--end::Table row-->
                  <!--begin::Table row-->
                  <tr>
                    <!--begin::Checkbox-->
                    <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input form-check-input2" type="checkbox" value="1" />
                      </div></td>
                    <!--end::Checkbox-->
                    <!--begin::Name=-->
                    <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                        <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">A</div>
                        </a> </div>
                      <!--end::Avatar-->
                      <!--begin::User details-->
                      <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Anjali</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">Developer</a> </div>
                      <!--begin::User details-->
                    </td>
                    <!--end::Name-->
                    <!--begin::Role-->
                    <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Interviewer</span> </div></td>
                    <!--end::Role-->
                    <!--begin::Contacts-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">+91 8549898944</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">anjali@test.com</a> </div></td>
                    <!--end::Contacts-->
                    <!--begin::Status-->
                    <td class=""><div class="form-check form-switch form-switch-sm form-check-custom form-check-solid d-none">
                        <input class="form-check-input" type="checkbox" value="" name="staus">
                      </div>
                      <span class="bg-light-success text-success fw-bolder fs-6">Active</span> </td>
                    <!--end::Status-->
                  </tr>
                  <!--end::Table row-->
                </tbody>
                <!--end::Table body-->
              </table>
              <!--end::Table-->
            </div>
            <!--end::Contacts Content-->
          </div>
          <!--end::Contacts Content-->
		  
          <!--begin::Requisitions Content-->
          <div class="tab-pane" id="requisitions" role="tabpanel">
            <!--begin::Sub tabs-->
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0">
              <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#all-requisitions">All Req.</a> </li>
              <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#client-requisitions">HCL Req.</a> </li>
              <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#custom-requisitions">Custom Req.</a> </li>
              <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#jobs">Jobs</a> </li>
            </ul>
            <!--end::Sub tabs-->
            <!--begin::Requisitions Content-->
            <div class="tab-content" id="myTabContent">
              <!--begin::All Req.-->
              <div class="tab-pane fade show active" id="all-requisitions" role="tabpanel">
                <!--begin::All Req. header-->
                <div class="card-header cursor-pointer px-0">
                  <!--begin::Card title-->
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">All Requisition</h3>
                  </div>
                  <!--end::Card title-->
                  <!--begin::Actions-->
                  <div class="d-flex align-items-center py-1">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1 me-3">
                      <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                      <span class="svg-icon svg-icon-1 position-absolute ms-6">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      <input type="text" id="inputSearch" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search..." />
                    </div>
                    <!--end::Search-->
                    <div class="card-toolbar">
                      <!--begin::Toolbar-->
                      <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <!--begin::Filter-->
                        <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                        <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                          <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                        </svg>
                        </span>
                        <!--end::Svg Icon-->
                        Filter</button>
                        <!--begin::Menu 1-->
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                          <!--begin::Header-->
                          <div class="px-7 py-5">
                            <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                          </div>
                          <!--end::Header-->
                          <!--begin::Separator-->
                          <div class="separator border-gray-200"></div>
                          <!--end::Separator-->
                          <!--begin::Content-->
                          <div class="px-7 py-5" data-kt-user-table-filter="form">
                            <!--begin::Input group-->
                            <div class="mb-10">
                              <label class="form-label fs-6 fw-bold">Role:</label>
                              <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
                                <option></option>
                                <option value="Administrator">Administrator</option>
                                <option value="Analyst">Analyst</option>
                                <option value="Developer">Developer</option>
                                <option value="Support">Support</option>
                                <option value="Trial">Trial</option>
                              </select>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-10">
                              <label class="form-label fs-6 fw-bold">Two Step Verification:</label>
                              <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
                                <option></option>
                                <option value="Enabled">Enabled</option>
                              </select>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                              <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                              <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                            </div>
                            <!--end::Actions-->
                          </div>
                          <!--end::Content-->
                        </div>
                        <!--end::Menu 1-->
                        <!--end::Filter-->

@if(in_array("add_jobs", $userPermissions))
<!--begin::Add Requisitions-->
<a href="<?php echo url('admin/jobs/create'); ?>">
<button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
<span class="svg-icon svg-icon-primary svg-icon-1x">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
	<polygon points="0 0 24 0 24 24 0 24"/>
	<path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
	<path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
  </g>
</svg>
</span> Add Requisitions</button>
</a>
<!--end::Add Requisitions-->
@endif                    
					
					  </div>
                      <!--end::Toolbar-->
                    </div>
                  </div>
                  <!--end::Actions-->
                </div>
                <!--end::All Req. header-->
                <!--begin::All Req. Content-->
                <div class="card-body px-0 py-7">
				
				
				
				
		
@if(in_array("view_jobs", $userPermissions))	
<table class="table align-middle table-row-dashed fs-6 gy-5" id="allreq">
		  <thead>
					<tr class="fw-bolder text-muted">
						  <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
							  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input1" value="1" />
							</div></th>
						  <th class="">Requisition</th>
						  <th class="">Exp.</th>
						  <th class="">Salary</th>
						  <th class="">Location</th>
						  <th class="">Status</th>
						  <th class="">Date</th>
						  <th class="">HM | CP</th>
						  <th class="">S.Partners</th>
						  <th class="">TOT</th>
						  <th class="">SOU</th>
						  <th class="">SCR</th>
						  <th class="">EVA</th>
						  <th class="">NEO</th>
						  <th class="">OFF</th>
						  <th class="">JOI</th>
						  <th class="">REJ</th>
					</tr>
		  </thead>
</table>							
@endif								
                  
                </div>
                <!--end::All Req. Content-->
              </div>
              <!--end::All Req.-->
              <!--end::Client Req.-->
              <div class="tab-pane fade" id="client-requisitions" role="tabpanel">
                <!--begin::Client Req. header-->
                <div class="card-header cursor-pointer px-0">
                  <!--begin::Card title-->
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">HCL Requisitions</h3>
                  </div>
                  <!--end::Card title-->
                </div>
                <!--end::Client Req. header-->
                <!--begin::Client Req. Content-->
                <div class="card-body px-0 py-7">
                <!--begin:Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input2" value="1" />
                        </div></th>
                      <th class="">Requisition</th>
                      <th class="">Exp.</th>
                      <th class="">Salary</th>
                      <th class="">Location</th>
                      <th class="">Status</th>
                      <th class="">Date</th>
                      <th class="">HM | CP</th>
                      <th class="">S.Partners</th>
                      <th class="">TOT</th>
                      <th class="">SOU</th>
                      <th class="">SCR</th>
                      <th class="">EVA</th>
                      <th class="">NEO</th>
                      <th class="">OFF</th>
                      <th class="">JOI</th>
                      <th class="">REJ</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                  <!--begin::Table body-->
                  <tbody class="text-gray-600 fw-bold">
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input2" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">U</div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">22+</span> <span class="text-muted fw-bold text-muted d-block fs-7">18+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">50 LPA</span> <span class="fw-bold d-block fs-7">30 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Gurgaon</span>
                          <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">Haryana</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                            <div> </div>
                          </div>
                        </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 hold-bg text-hover-warning">Hold</a> <span class="fw-bold text-muted d-block fs-7">R2</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span class="text-muted fw-bold d-block fs-7">774</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Pawan"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div>
                  </div>
                  
                  </td>
                  
                  <!--end::Hiring Manager | Contact Person-->
                  <!--begin::Staffing Partners-->
                  <td class=""><div class="d-flex flex-column">
                        <div class="symbol-group symbol-hover">
                          <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                          <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Abinesh"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg"> </div>
                          <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                        </div>
                      </div></td>
                    <!--end::Staffing Partners-->
                    <!--begin::TOT-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a> </div></td>
                    <!--end::TOT-->
                    <!--begin::SOU-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                    <!--end::SOU-->
                    <!--begin::SCR-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">23</a> </div></td>
                    <!--end::SCR-->
                    <!--begin::EVA-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                    <!--end::EVA-->
                    <!--begin::NEO-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">24</a> </div></td>
                    <!--end::NEO-->
                    <!--begin::OFF-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">83</a> </div></td>
                    <!--end::OFF-->
                    <!--begin::JOI-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">49</a> </div></td>
                    <!--end::JOI-->
                    <!--begin::REJ-->
                    <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                    <!--end::REJ-->
                  </tr>
                  <!--end::Table row-->
                  </tbody>
                  
                  <!--end::Table body-->
                </table>
                <!--end::Table-->
              </div>
              <!--end::Client Req. Content-->
            </div>
            <!--end::Client Req.-->
            <!--begin::Custom Req.-->
            <div class="tab-pane fade" id="custom-requisitions" role="tabpanel">
              <!--begin::Custom Req. header-->
              <div class="card-header cursor-pointer px-0">
                <!--begin::Card title-->
                <div class="card-title m-0">
                  <h3 class="fw-bolder m-0">Custom Requisitions</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center py-1">
                  <div class="card-toolbar">
                    <!--begin::Toolbar-->
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                      <!--begin::Add Requisitions-->
                      <a href="<?php echo url('admin/jobs/create'); ?>">
                      <button type="button" class="btn btn-sm btn-flex btn-primary btn-active-primary fw-bolder me-3">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-primary svg-icon-1x">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <polygon points="0 0 24 0 24 24 0 24"/>
                          <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                          <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                        </g>
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      Add Requisitions </button>
                      </a>
                      <!--end::Add Requisitions-->
                    </div>
                    <!--end::Toolbar-->
                  </div>
                </div>
                <!--end::Actions-->
              </div>
              <!--end::Custom Req. header-->
              <!--begin::Custom Req. Content-->
              <div class="card-body px-0 py-7">
                <!--begin:Table-->
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                  <!--begin::Table head-->
                  <thead>
                    <!--begin::Table row-->
                    <tr class="fw-bolder text-muted">
                      <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                          <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input3" value="1" />
                        </div></th>
                      <th class="">Requisition</th>
                      <th class="">Exp.</th>
                      <th class="">Salary</th>
                      <th class="">Location</th>
                      <th class="">Status</th>
                      <th class="">Date</th>
                      <th class="">HM | CP</th>
                      <th class="">S.Partners</th>
                      <th class="">TOT</th>
                      <th class="">SOU</th>
                      <th class="">SCR</th>
                      <th class="">EVA</th>
                      <th class="">NEO</th>
                      <th class="">OFF</th>
                      <th class="">JOI</th>
                      <th class="">REJ</th>
                    </tr>
                    <!--end::Table row-->
                  </thead>
                  <!--end::Table head-->
                  <!--begin::Table body-->
                  <tbody class="text-gray-600 fw-bold">
                    <!--begin::Table row-->
                    <tr class="">
                      <!--begin::Checkbox-->
                      <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="form-check-input form-check-input3" type="checkbox" value="1" />
                        </div></td>
                      <!--end::Checkbox-->
                      <!--begin::Requisitions=-->
                      <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                          <div class="symbol-label border-radius-5"> <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="Company Logo" class="border-radius-5 w-100"> </div>
                          </a> </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Frontend Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl Technologies</a> </div>
                        <!--begin::User details-->
                      </td>
                      <!--end::Requisitions-->
                      <!--begin::Exp.-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30+</span> <span class="text-muted fw-bold text-muted d-block fs-7">27+</span> </div></td>
                      <!--end::Exp.-->
                      <!--begin::Salary-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">70 LPA</span> <span class="fw-bold d-block fs-7">67 LPA</span> </div></td>
                      <!--end::Salary-->
                      <!--begin::Location-->
                      <td class=""><div class="d-flex flex-column">
                        <span class="text-dark fw-bolder fs-6">Noida</span>
                        <div class="d-flex align-items-center">
                        <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                        <div> </div></td>
                      <!--end::Location-->
                      <!--begin::Status | Requisition ID-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 closed-bg text-hover-danger">Closed</a> <span class="fw-bold text-muted d-block fs-7">R3-Custom</span> </div></td>
                      <!--end::Status | Requisition ID-->
                      <!--begin::Date | Openings-->
                      <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">33</span> </div></td>
                      <!--end::Date | Openings-->
                      <!--begin::Hiring Manager | Contact Person-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Priyanka"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Hiring Manager | Contact Person-->
                      <!--begin::Staffing Partners-->
                      <td class=""><div class="d-flex flex-column">
                          <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                            <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                          </div>
                        </div></td>
                      <!--end::Staffing Partners-->
                      <!--begin::TOT-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a> </div></td>
                      <!--end::TOT-->
                      <!--begin::SOU-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">345</a> </div></td>
                      <!--end::SOU-->
                      <!--begin::SCR-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::SCR-->
                      <!--begin::EVA-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">94</a> </div></td>
                      <!--end::EVA-->
                      <!--begin::NEO-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">03</a> </div></td>
                      <!--end::NEO-->
                      <!--begin::OFF-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                      <!--end::OFF-->
                      <!--begin::JOI-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">45</a> </div></td>
                      <!--end::JOI-->
                      <!--begin::REJ-->
                      <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">540</a> </div></td>
                      <!--end::REJ-->
                    </tr>
                    <!--end::Table row-->
                  </tbody>
                  <!--end::Table body-->
                </table>
                <!--end::Table-->
              </div>
              <!--end::Custom Req. Content-->
            </div>
            <!--end::Custom Req.-->
            <div class="tab-pane fade" id="jobs" role="tabpanel">Jobs - Content</div>
          </div>
          <!--end::Requisitions Content-->
        </div>
        <!--end::Requisitions Content-->
      </div>
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
									
		ajax: {'url' : '{!! route('admin.clients.contactdata') !!}?clientId='+<?php echo @$client->id; ?>,
				"data": function ( d )
					{
						d.search = $('#inputSearch').val()
					}
				},
			
        language: {
            "url": "<?php echo __("app.datatable") ?>"
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
           	"pageLength": 10,
   			colReorder		: true,
      		ajax			: {'url' : '{!! route('admin.jobs.data') !!}',
								"data"			: function ( d )
												{
													d.search 		=	$('#inputSearch').val(),
													d.client_id 	=	$('#hid_client_id').val()
												}
   					},
   
   					language:
   					{
   					  "url": "<?php echo __("app.datatable") ?>"
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

