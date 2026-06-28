<?php 
		use App\Helper\GeneralMethod;
?>
<style>
.text-justify
{
text-align: justify!important;
}
.symbol1 > img,
.symbol1 .symbol-label1
{
width: 32px!important;
height: 32px!important;
}
/*Table column width*/
.table > :not(caption) > * > *
{
padding: 0rem 0.4rem;
}
/* end */
</style>
<?php 

	
	
	$employment_type		=	'';
	$employment_type		=	$job->employment_type;
	if(!empty($employment_type)){
		$employment_type	=	$employmentType[$job->employment_type];	
	}					
						  
	$bgTextDecore			=	$strVal	=	'';
	$str					=	trim(@$job->client_name);
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
	if(!empty(@$job->logo_logical_path) && strlen(@$job->logo_logical_path)>10){
		$background_image	=  url('public/'.@$job->logo_logical_path); 	
	} 
	
	
	
	
	$currency 				=	GeneralMethod::currencySymbole();  
	$max_salary 			=	$min_salary 			=	$max_sal_mode		=	$min_sal_mode	=	'';		
	$max_salary 			=	GeneralMethod::thousandsCurrencyFormat($job->max_salary); 	 
	$min_salary 			=	GeneralMethod::thousandsCurrencyFormat($job->min_salary); 	
	if(!empty($job->min_sal_mode)){
		$min_sal_mode		=	strtoupper(substr($salaryMode[$job->min_sal_mode], 0, 1));
	}
	if(!empty($job->max_sal_mode)){
		$max_sal_mode		=	strtoupper(substr($salaryMode[$job->max_sal_mode], 0, 1));
	}
					 								
?>
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="{{$job->id}}" />
  <!--begin::Content-->
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
    <!--beign::candidate detail-->
    <div class="card mb-5 mb-xl-5 mx-2">
      <div class="card-body pt-5 pb-0 px-5">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
          <!--begin: Pic-->
          <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
              <?php 
							if(!empty($background_image)){
								echo '<img src="'.@$background_image.'" height="100px" width="100px"  />';
							}else{
								echo	'<div class="symbol-label fs-3 '.@$bgTextDecore.' border-radius-5"><span>'.ucwords(@$strVal).'</span></div>'; 
							}
				?>
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
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"> <?php echo ucwords(@$job->title); ?> </span> </a> </div>
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
                  </span> <?php echo ucwords(@$job->client_name); ?> </a> 

<a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                    <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
<?php 

	$city				=	'';	
	//$city				=	@$jobLocation->city;
	//echo $city		= 	ucwords($city);
	
	$city				=	@$jobLocation->city;
	if(@$jobLocation->country_id == 101){
		$city			=	@$jobLocation->city_name;		
	}
	echo $city			=	ucwords($city);
	echo ' , ';
	$country_id			=	'';	
	$country_id			=	@$jobLocation->country_id;
	if(@$country_id > 0){
		 $country_name	=	GeneralMethod::getCountryName($country_id);	
		 foreach($country_name as $cval){
			echo ucwords($cval['name']);	
		 }				 
	}
								
?>
</a> 

				  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"/>
                      <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                      <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                      <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                    </g>
                  </svg>
                  </span>
                  <?php 

	$originalDate 	= @$job->created_at;
	$newDate 		= date("d M, Y", strtotime($originalDate));
	echo $newDate;

?>
                  </a> </div>
              </div>
              <!--end::Candidate Details-->
              <!--begin::Actions-->
              <div class="d-flex mt-4">
<?php 
		if(@$job->status == 'active'){	
			echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">Active</a>';
		}else if(@$job->status == 'hold'){
			echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-warning btn-active-warning fw-bolder me-3 fs-6">Hold</a>';
		}else{
			echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6">Inactive</a>';	
		}	
?>
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
		if(!empty($skillArr)){
			foreach($skillArr as $sval){
				echo '<span class="badge badge-light my-1">'.ucfirst($sval->skill_name).'</span>  ';
			}
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
          <!--begin: Experience-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Experience</span> <span class="fw-bolder fs-3"><?php echo @$job->min_exp; ?>-<?php echo @$job->max_exp; ?> Years</span> </div>
          </div>
          <!--end: Experience-->
          <!--begin: Salary-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            
			<div class="d-flex flex-column text-dark-75"> 
				<span class="fw-bolder fs-6">Salary</span> 
				<span class="fw-bolder fs-3">
					<?php echo @$currency[@$job->min_salary_currency]; ?><?php echo @$min_salary; ?>-
					<?php echo @$max_salary; ?>/<?php echo @$max_sal_mode; ?>
				</span> 
			</div>
			
          </div>
          <!--end: Salary-->
          <!--begin: Openings-->
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
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Openings</span> <span class="fw-bolder fs-3"><?php echo @$job->total_positions; ?></span> </div>
          </div>
          <!--end: Openings-->
          <!--begin: Position Type-->
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <!--begin::Svg Icon-->
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M13.6855025,18.7082217 C15.9113859,17.8189707 18.682885,17.2495635 22,17 C22,16.9325178 22,13.1012863 22,5.50630526 L21.9999762,5.50630526 C21.9999762,5.23017604 21.7761292,5.00632908 21.5,5.00632908 C21.4957817,5.00632908 21.4915635,5.00638247 21.4873465,5.00648922 C18.658231,5.07811173 15.8291155,5.74261533 13,7 C13,7.04449645 13,10.79246 13,18.2438906 L12.9999854,18.2438906 C12.9999854,18.520041 13.2238496,18.7439052 13.5,18.7439052 C13.5635398,18.7439052 13.6264972,18.7317946 13.6855025,18.7082217 Z" fill="#000000"/>
                <path d="M10.3144829,18.7082217 C8.08859955,17.8189707 5.31710038,17.2495635 1.99998542,17 C1.99998542,16.9325178 1.99998542,13.1012863 1.99998542,5.50630526 L2.00000925,5.50630526 C2.00000925,5.23017604 2.22385621,5.00632908 2.49998542,5.00632908 C2.50420375,5.00632908 2.5084219,5.00638247 2.51263888,5.00648922 C5.34175439,5.07811173 8.17086991,5.74261533 10.9999854,7 C10.9999854,7.04449645 10.9999854,10.79246 10.9999854,18.2438906 L11,18.2438906 C11,18.520041 10.7761358,18.7439052 10.4999854,18.7439052 C10.4364457,18.7439052 10.3734882,18.7317946 10.3144829,18.7082217 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <!--end::Svg Icon-->
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Position Type</span> <span class="fw-bolder fs-3"><?php echo ucwords($employment_type); ?></span> </div>
          </div>
          <!--end: Position Type-->
          <!--begin: Department-->
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
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Department</span> <span class="fw-bolder fs-3"><?php echo @$job->cat_name; ?></span> </div>
          </div>
          <!--end: Department-->
          <!--begin:Team Member-->
          <div class="d-flex align-items-center flex-lg-fill my-1">
            <!--begin::Svg Icon-->
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
            <div class="symbol-group symbol-hover">
<?php 

//echo '<pre>'; print_r($assignTeam); 
if(!empty($assignTeam)){
	foreach($assignTeam as $atVal){

		$imagePath		=	"";
		$imagePath		=	"https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg";
		if(!empty(@$atVal->image)){
			$imagePath	=	url('public/user-uploads/team_profile_img/'.@$atVal->image);	
		}		
	
		echo '<div class="symbol symbol1 symbol-30 symbol-circle" data-toggle="tooltip" title="'.@$atVal->name.'"> <img alt="Pic" src="'.@$imagePath.'"> </div>';
	}
}		

if($assignTeamCount>=5){
	echo '<div class="symbol symbol1 symbol-30 symbol-circle symbol-light" data-toggle="tooltip" title="All Team Member List...!"><span class="symbol-label symbol-label1 font-weight-bold">5+</span></div>';
}

?>
            </div>
          </div>
          <!--end: Team Member-->
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
            <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab" href="#job_description"> <span class="nav-text font-weight-bold">Job Description</span> </a> </li>
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#team"> <span class="nav-text font-weight-bold">Team</span> </a> </li>
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interviews"> <span class="nav-text font-weight-bold">Interviews</span> </a> </li>
          </ul>
        </div>
      </div>
      <!--end::Tabs Header-->
      <!--Begin::Body-->
      <div class="card-body py-0">
        <div class="tab-content pt-5">
          <!--begin::Job Description Content-->
          <div class="tab-pane active" id="job_description" role="tabpanel">
            <!--begin::Job Description header-->
            <div class="card-header cursor-pointer px-0">
              <!--begin::Card title-->
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Job Description</h3>
              </div>
              <!--end::Card title-->
@if(in_array("edit_jobs", $userPermissions))			  
<!--begin::Action-->
<a href="<?php echo route('admin.jobs.edit', $job->id); ?>" class="btn btn-primary align-self-center">Edit Requisition</a>
<!--end::Action-->
@endif

            </div>
            <!--end::Job Description header-->
            <div class="card-body px-0 py-7">
              <!--begin::Job Description Content-->
              <div class="row">
                <div class="col-lg-12 mb-7">
                  <h4 class="" style="margin: 10px 0px;">Position - <?php echo ucwords(@$job->title); ?> </h4>
                  <h5 class="" style="margin: 10px 0px;">Experience- <?php echo @$job->min_exp; ?>-<?php echo @$job->max_exp; ?> Years</h5>
                  <p> <?php echo @$job->job_description; ?> </p>
                  <h5 class="" style="margin: 10px 0px;">About <?php echo ucwords(@$job->client_name); ?></h5>
                  <p class="fw-bold fs-6 text-justify"> <?php echo ucfirst(@$job->client_description); ?> </p>
                </div>
              </div>
              <!--end::Job Description Content-->
            </div>
          </div>
          <!--end::Job Description Content-->
          <!--begin::Team Content-->
          <div class="tab-pane" id="team" role="tabpanel">
            <!--begin::Sub tabs-->
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 border-bottom-0">
              <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#all_team">Team</a> </li>
              <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#staffing_partners">Staffing Partners</a> </li>
              <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#request">Request</a> </li>
            </ul>
            <!--end::Sub tabs-->
            <!--begin::Team Content-->
            <div class="tab-content" id="myTabContent">
              <!--begin::All Team-->
              <div class="tab-pane fade show active" id="all_team" role="tabpanel">
                <!--begin::All team header-->
                <div class="card-header cursor-pointer px-0">
                  <!--begin::Card title-->
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">Team</h3>
                  </div>
                  <!--end::Card title-->
                  <!--begin::Action-->
                  <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-primary svg-icon-1x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> Add/Edit Team Member</a>
                  <!--end::Svg Icon-->
                  <!--end::Action-->
                </div>
                <div class="card-body px-0 py-7">
               
@if(in_array("view_team", $userPermissions))				   
			   
			      <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_users">
                    <thead>
                      <tr class="fw-bolder text-muted">
                        <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input1" value="1">
                          </div></th>
                        <th class="min-w-150px">Name</th>
                        <th class="min-w-150px">Contact Person</th>
                        <th class="min-w-150px">Upload Profile</th>
                        <th class="min-w-15px">Interview</th>
                        <th class="">Joinings</th>
                      </tr>
                    </thead>
					<tbody>
<?php 
if(!empty($recruiterlist)){
	foreach($recruiterlist as $rval){
?>					
                      <tr class="fw-bolder text-muted">
                        <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input1" value="1">
                          </div></th>
                        <th class="min-w-150px">
<?php 
			$bgTextDecore 		= $strVal = '';
            $str = trim(@$rval->name);
            $str = $strVal = strtolower($str[0]);
            $str = ord($str);
            if ($str >= 97 && $str <= 101) {
                $bgTextDecore = 'fs-3 bg-light-danger text-danger';
            } else if ($str >= 102 && $str <= 106) {
                $bgTextDecore = 'fs-3 bg-light-warning text-warning';
            } else if ($str >= 107 && $str <= 111) {
                $bgTextDecore = 'fs-3 bg-light-primary text-primary';
            } else if ($str >= 112 && $str <= 116) {
                $bgTextDecore = 'fs-3 bg-light-success text-success';
            } else if ($str >= 117 && $str <= 121) {
                $bgTextDecore = 'fs-3 bg-dark text-secondary';
            } else {
                $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
            }
           
		    $icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left" >
                   				 <a href="javascript:void(0);" data-row-id="' . $rval->id . '" class="show-detail-team" id="kt_activities3_toggle" >
                      				<div class="symbol-label fs-3 ' . @$bgTextDecore . '  border-radius-5">' . ucwords(@$strVal) . '</div>
                    			</a>
                  		</div>';
				  
				  if(!empty($rval->image)){
				  		$imagePath	=	'';
				  		$imagePath	=	url('public/user-uploads/team_profile_img/'.@$rval->image);
				  		$icon	= '<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5" style="float:left">
                                        <a href="javascript:void(0);">
                                            <div class="symbol-label border-radius-5">
                                            <img src="'.$imagePath.'" alt="User Image" class="border-radius-5 w-100">
                                            </div>
                                        </a>
                                    </div>';
				  }			
										  
            echo  $icon.'<div class="d-flex flex-column">
                    <a href="javascript:void(0);" data-row-id="' . $rval->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$rval->name) . '">' . ucwords(substr(@$rval->name, 0, 15)) . '</a>
                    <a href="javascript:void(0);" data-row-id="' . $rval->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.ucwords($rval->display_name).'</a>
                  </div>
                  ';
				  
?>						
						
						</th>
                        <th class="min-w-150px">
<?php 

echo '<div class="d-flex flex-column">  
<a href="javascript:void(0);" data-row-id="' . $rval->id . '" class="text-dark fw-bolder text-hover-primary fs-6 show-detail-team"  data-toggle="tooltip1" data-html="true" title="' . ucwords(@$rval->mobile) . '">' . ucwords(substr(@$rval->mobile, 0, 15)) . '</a>
<a href="javascript:void(0);" data-row-id="' . $rval->id . '" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary show-detail-team">'.@$rval->email.'</a>
</div>';
				  
?>
						</th>
                        <th class="min-w-150px"><?php echo @$rval->allCandidate; ?></th> 
                        <th class="min-w-15px"><?php echo @$rval->Interview; ?></th>
                        <th class=""><?php echo @$rval->Joined; ?></th>
                      </tr>
<?php 
	}
}	
?>					  
                    </tbody>
					
					
                  </table>

@endif
				  
<?php 
		
		//echo '<pre>'; print_r($recruiterlist);
?>				  
<script>

$(document).ready(function() {
	
	$('#inputSearch').keyup(function() {
		//alert("call");
		table1._fnDraw(); 
	});

 	var table1 = $('#table_users').dataTable({	
	
			responsive  	: true,
			processing  	: true,
			serverSide  	: true,
			destroy   		: true,
			ordering		: false, //disable ordering			
			"bLengthChange" : false, //thought this line could hide the LengthMenu
			sDom			: 'lrtip',
			oLanguage 		: {sLengthMenu: "_MENU_", },
			ajax: {'url' : '{!! route('admin.team.assigndata') !!}?job_id='+$('#hid_job_id').val(),
			"data": function ( d )
				{
					//d.search = $('#inputSearch').val()
					d.search = $('#inputSearch').val()
				}
		},	
        language: languageOptions(),
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        "columnDefs": [
						{ "width": "3%", "targets": 0 },
						{ "width": "27%", "targets": 1 },
						{ "width": "40%", "targets": 2 },
						{ "width": "22%", "targets": 3 },
						{ "width": "8%", "targets": 4 }						
					  ], 			  
        columns: [
					{ data: 'chkbox', name: 'chkbox', searchable : false, orderable : false},
					{ data: 'Name', name: 'Name', orderable : false, searchable : true },
					{ data: 'ContactPerson', name: 'ContactPerson', orderable : false, searchable : true },
					{ data: 'UploadProfile', name: 'UploadProfile', orderable : false, searchable : false },
					{ data: 'Interview', name: 'Interview', orderable : false, searchable : false},
					{ data: 'Joinings', name: 'Joinings', orderable : false, searchable : false},
					
        		]
    });
    new $.fn.dataTable.FixedHeader( table1 ); 
	
	
});	
</script>
                </div>
              </div>
              <!--begin::Staffing Partners-->
              <div class="tab-pane fade show" id="staffing_partners" role="tabpanel">
                <!--begin::Staffing Partners header-->
                <div class="card-header cursor-pointer px-0">
                  <!--begin::Card title-->
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">Staffing Partners</h3>
                  </div>
                  <!--end::Card title-->
                  <!--begin::Action-->
                  <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-primary svg-icon-1x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> Add Staffing Partners</a>
                  <!--end::Svg Icon-->
                  <!--end::Action-->
                </div>
                <!--end::Staffing Partners header-->
                <!--begin::Staffing Partners Content-->
                <div class="card-body px-0 py-7">
               
@if(in_array("view_staffing_partners", $userPermissions))			  
				  <!--begin:Table-->
                  <table class="table align-middle table-row-dashed fs-6 gy-5" >
                    <thead>
                      <tr class="fw-bolder text-muted">
                        <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-two" value="1" />
                          </div></th>
                        <th class="">Name</th>
                        <th class="">Contact Person</th>
                        <th class="">Upload Profile</th>
                        <th class="">Interview</th>
                        <th class="">Joinings</th>
                      </tr>
                    </thead>
                  </table>
                  <!--end::Table-->
@endif			   
			   
			   
			      
                </div>
                <!--end::Staffing Partners Content-->
              </div>
              <!--end::Staffing Partners-->
              <!--begin::Request-->
              <div class="tab-pane fade show" id="request" role="tabpanel">
                <!--begin::Request header-->
                <div class="card-header cursor-pointer px-0">
                  <!--begin::Card title-->
                  <div class="card-title m-0">
                    <h3 class="fw-bolder m-0">Request</h3>
                  </div>
                  <!--end::Card title-->
                  <!--begin::Action-->
                  <a href="javascript:void(0);" class="btn btn-primary align-self-center d-none">
                  <!--begin::Svg Icon-->
                  <span class="svg-icon svg-icon-primary svg-icon-1x">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <polygon points="0 0 24 0 24 24 0 24"/>
                      <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                      <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                    </g>
                  </svg>
                  </span> Add</a>
                  <!--end::Svg Icon-->
                  <!--end::Action-->
                </div>
                <!--end::Request header-->
                <!--begin::Request Content-->
                <div class="card-body px-0 py-7 d-none">
                  <!--begin:Table-->
                  <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <!--begin::Table head-->
                    <thead>
                      <!--begin::Table row-->
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
                      <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="text-gray-600 fw-bold">
                      <!--begin::Table row-->
                      <tr class="">
                        <!--begin::Checkbox-->
                        <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input form-check-input1" type="checkbox" value="1" />
                          </div></td>
                        <!--end::Checkbox-->
                        <!--begin::Requisitions=-->
                        <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                            <div class="symbol-label border-radius-5"> <img src="https://ayudawp.com/wp-content/uploads/2011/08/wordpress-icono.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                            </a> </div>
                          <!--end::Avatar-->
                          <!--begin::User details-->
                          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Wordpress Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech Mahindra</a> </div>
                          <!--begin::User details-->
                        </td>
                        <!--end::Requisitions-->
                        <!--begin::Exp.-->
                        <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">14+</span> <span class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div></td>
                        <!--end::Exp.-->
                        <!--begin::Salary-->
                        <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">18 LPA</span> <span class="fw-bold d-block fs-7">12 LPA</span> </div></td>
                        <!--end::Salary-->
                        <!--begin::Location-->
                        <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Greater Noida</span>
                            <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                              <div> </div>
                            </div>
                          </div></td>
                        <!--end::Location-->
                        <!--begin::Status | Requisition ID-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R1</span> </div></td>
                        <!--end::Status | Requisition ID-->
                        <!--begin::Date | Openings-->
                        <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-Jul-2021</span> <span class="text-muted fw-bold d-block fs-7">3,974</span> </div></td>
                        <!--end::Date | Openings-->
                        <!--begin::Hiring Manager | Contact Person-->
                        <td class=""><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover">
                              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Promod"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                              <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                            </div>
                          </div></td>
                        <!--end::Hiring Manager | Contact Person-->
                        <!--begin::Staffing Partners-->
                        <td class=""><div class="d-flex flex-column">
                            <div class="symbol-group symbol-hover">
                              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Amit Kumar"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg"> </div>
                              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                              <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                            </div>
                          </div></td>
                        <!--end::Staffing Partners-->
                        <!--begin::TOT-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">3,294</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">58</a> </div></td>
                        <!--end::TOT-->
                        <!--begin::SOU-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                        <!--end::SOU-->
                        <!--begin::SCR-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">34</a> </div></td>
                        <!--end::SCR-->
                        <!--begin::EVA-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                        <!--end::EVA-->
                        <!--begin::NEO-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">29</a> </div></td>
                        <!--end::NEO-->
                        <!--begin::OFF-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">32</a> </div></td>
                        <!--end::OFF-->
                        <!--begin::JOI-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                        <!--end::JOI-->
                        <!--begin::REJ-->
                        <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">384</a> </div></td>
                        <!--end::REJ-->
                      </tr>
                      <!--end::Table row-->
                    </tbody>
                    <!--end::Table body-->
                  </table>
                  <!--end::Table-->
                </div>
                <!--end::Request Content-->
              </div>
              <!--end::Request-->
            </div>
            <!--end::Team Content-->
          </div>
          <!--eng::Team Content-->
          <!--begin::Interviews Content-->
          <div class="tab-pane" id="interviews" role="tabpanel">
            <!--begin::Contacts header-->
            <div class="card-header cursor-pointer px-0">
              <!--begin::Card title-->
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Interview</h3>
              </div>
              <!--end::Card title-->
            </div>
           
            <div class="card-body px-0 py-7">
@if(in_array("view_schedule", $userPermissions))	             
              <table class="table align-middle table-row-dashed fs-6 gy-5" id="interviewData">
                <thead>
                  <tr class="fw-bolder text-muted">
                    <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users form-check-input-itracker" value="1" />
                      </div></th>
                    <th class="">Name</th>
                    <th class="">Requisitions</th>
                    <th class="">Mode</th>
                    <th class="">Interviewers</th>
                    <th class="">Source</th>
                    <th class="">Date</th>
                    <th class="">Feedback</th>
                    <th class="">Rating</th>
                    <th class="">Stage</th>
                  </tr>
                </thead>
              </table>
             
            </div>
@endif           
			
<script>    
    
        loadTable();       
        var table;
        function loadTable(){
		
            var hid_job_id 		= $('#hid_job_id').val();         
            table   = $('#interviewData').dataTable({
            
														responsive  	: true,
														processing  	: true,
														serverSide  	: true,
														destroy   		: true,
														ordering		: false, 	//disable ordering														
														"bLengthChange" : false, 	//thought this line could hide the LengthMenu														
														sDom			: 'lrtip',
														oLanguage 		: {
																			sLengthMenu: "_MENU_",
																		  },
														ajax    		: '{!! route('admin.interview-schedule.interviewData') !!}?hid_job_id=' + hid_job_id,

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
		 
</script>
			
						
          </div>
          <!--end::Interviews Content-->
        </div>
        <!--end::Body-->
      </div>
      <!--end:Tabs-->
    </div>
    <!--end::Content-->
  </div>
  <!--end::Body-->
</div>