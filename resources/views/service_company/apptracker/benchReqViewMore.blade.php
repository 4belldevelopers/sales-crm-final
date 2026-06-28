@php 
		use App\Helper\GeneralMethod;
@endphp 
<style>
.text-justify{text-align: justify!important;}
.symbol1 > img,
.symbol1 .symbol-label1 { width: 32px!important; height: 32px!important; }
.table > :not(caption) > * > * {padding: 0rem 0.4rem;}
</style>
@php 
		$employment_type		=	$bgTextDecore			=	$strVal	=	'';
		$employment_type		=	$job->employment_type;
		if(!empty($employment_type)){
			$employment_type	=	$employmentType[$job->employment_type];	
		}										  
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
@endphp 

<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <input type="hidden" name="hid_job_id" id="hid_job_id" value="{{$job->id}}" />
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
    <div class="card mb-5 mb-xl-5 mx-2">
      <div class="card-body pt-5 pb-0 px-5">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
          <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">

@if(!empty($background_image))
		{!! '<img src="'.@$background_image.'" height="100px" width="100px"  />' !!}
@else
		{!! '<div class="symbol-label fs-3 '.@$bgTextDecore.' border-radius-5"><span>'.ucwords(@$strVal).'</span></div>' !!}
@endif
              <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
              <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"> {{ ucwords(@$job->title) }} </span> </a> </div>
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
				  {{ ucwords(@$job->client_name) }} 
				  </a> 
				  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                  <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                    <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                  </svg>
                  </span>
@php 

	$city				=	$country_id			=	'';		
	$city				=	@$jobLocation->city;
	if(@$jobLocation->country_id == 101){
		$city			=	@$jobLocation->city_name;		
	}
	echo $city			=	ucwords($city);
	echo ' , ';
	$country_id			=	@$jobLocation->country_id;
	if(@$country_id > 0){
		 $country_name	=	GeneralMethod::getCountryName($country_id);	
		 foreach($country_name as $cval){
			echo ucwords($cval['name']);	
		 }				 
	}
@endphp 
                  </a> 
				  <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
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
@php 
	$originalDate 	=	@$job->created_at;
	$newDate 		=	date("d M, Y", strtotime($originalDate));
@endphp	
	{{ $newDate }}
 
                  </a> </div>
              </div>
              <div class="d-flex mt-4">

@if(@$job->status == 'active')
			{!! '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">Active</a>' !!}
@elseif(@$job->status == 'hold')
			{!! '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-warning btn-active-warning fw-bolder me-3 fs-6">Hold</a>' !!}
@else
			{!! '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6">Inactive</a>' !!}	
@endif		
              </div>
            </div>
            
            <div class="d-flex flex-wrap flex-stack">
              <div class="d-flex flex-column flex-grow-1 pe-8">
                <div class="row w-75"> <span class="">
					@if(!empty($skillArr))
						@foreach($skillArr as $sval)
							{!! '<span class="badge badge-light my-1">'.ucfirst($sval->skill_name).'</span>' !!}
						@endforeach
					@endif		
                  </span> </div>
              </div>
            </div>
          </div>
        </div>

        <div class="separator separator-solid my-7"></div>
        <div class="d-flex align-items-center flex-wrap pb-7">
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Experience</span> <span class="fw-bolder fs-3">{{ @$job->min_exp }} - {{ @$job->max_exp }}  Years</span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> 
				<span class="fw-bolder fs-6">Salary</span> 
				<span class="fw-bolder fs-3"> 
					{!! @$currency[@$job->min_salary_currency] !!} {{ @$min_salary }} - {{ @$max_salary }} / {{ @$max_sal_mode }}
				</span> 
			</div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M4.875,20.75 C4.63541667,20.75 4.39583333,20.6541667 4.20416667,20.4625 L2.2875,18.5458333 C1.90416667,18.1625 1.90416667,17.5875 2.2875,17.2041667 C2.67083333,16.8208333 3.29375,16.8208333 3.62916667,17.2041667 L4.875,18.45 L8.0375,15.2875 C8.42083333,14.9041667 8.99583333,14.9041667 9.37916667,15.2875 C9.7625,15.6708333 9.7625,16.2458333 9.37916667,16.6291667 L5.54583333,20.4625 C5.35416667,20.6541667 5.11458333,20.75 4.875,20.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                <path d="M2,11.8650466 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L12.9835977,18 C12.7263047,14.0909841 9.47412135,11 5.5,11 C4.23590829,11 3.04485894,11.3127315 2,11.8650466 Z M6,7 C5.44771525,7 5,7.44771525 5,8 C5,8.55228475 5.44771525,9 6,9 L15,9 C15.5522847,9 16,8.55228475 16,8 C16,7.44771525 15.5522847,7 15,7 L6,7 Z" fill="#000000"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Openings</span> <span class="fw-bolder fs-3">{{ @$job->total_positions }}</span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M13.6855025,18.7082217 C15.9113859,17.8189707 18.682885,17.2495635 22,17 C22,16.9325178 22,13.1012863 22,5.50630526 L21.9999762,5.50630526 C21.9999762,5.23017604 21.7761292,5.00632908 21.5,5.00632908 C21.4957817,5.00632908 21.4915635,5.00638247 21.4873465,5.00648922 C18.658231,5.07811173 15.8291155,5.74261533 13,7 C13,7.04449645 13,10.79246 13,18.2438906 L12.9999854,18.2438906 C12.9999854,18.520041 13.2238496,18.7439052 13.5,18.7439052 C13.5635398,18.7439052 13.6264972,18.7317946 13.6855025,18.7082217 Z" fill="#000000"/>
                <path d="M10.3144829,18.7082217 C8.08859955,17.8189707 5.31710038,17.2495635 1.99998542,17 C1.99998542,16.9325178 1.99998542,13.1012863 1.99998542,5.50630526 L2.00000925,5.50630526 C2.00000925,5.23017604 2.22385621,5.00632908 2.49998542,5.00632908 C2.50420375,5.00632908 2.5084219,5.00638247 2.51263888,5.00648922 C5.34175439,5.07811173 8.17086991,5.74261533 10.9999854,7 C10.9999854,7.04449645 10.9999854,10.79246 10.9999854,18.2438906 L11,18.2438906 C11,18.520041 10.7761358,18.7439052 10.4999854,18.7439052 C10.4364457,18.7439052 10.3734882,18.7317946 10.3144829,18.7082217 Z" fill="#000000" opacity="0.3"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Position Type</span> <span class="fw-bolder fs-3">{{ ucwords($employment_type) }}</span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"/>
                <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 C21,16.9705627 16.9705627,21 12,21 Z M14.1654881,7.35483745 L9.61055177,10.3622525 C9.47921741,10.4489666 9.39637436,10.592455 9.38694497,10.7495509 L9.05991526,16.197949 C9.04337012,16.4735952 9.25341309,16.7104632 9.52905936,16.7270083 C9.63705011,16.7334903 9.74423017,16.7047714 9.83451193,16.6451626 L14.3894482,13.6377475 C14.5207826,13.5510334 14.6036256,13.407545 14.613055,13.2504491 L14.9400847,7.80205104 C14.9566299,7.52640477 14.7465869,7.28953682 14.4709406,7.27299168 C14.3629499,7.26650974 14.2557698,7.29522855 14.1654881,7.35483745 Z" fill="#000000"/>
              </g>
            </svg>
            </span>
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Department</span> <span class="fw-bolder fs-3">{{ @$job->cat_name }}</span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill my-1">
            <div class="symbol-group symbol-hover">
@php 
		if(!empty($clientContactPersonListing)){
			$image_path  	=	url('public/avatar.png');
			$contactHtml 	= '<div class="d-flex flex-column"><div class="symbol-group symbol-hover">';
							foreach ($clientContactPersonListing as $info) {
								$contactHtml.= '<div class="symbol symbol-30 symbol-circle" data-bs-placement="top" data-toggle="tooltip2" data-html="true" title="' . ucwords(@$info->contact_person_name) . '" >
									<img alt="Pic" src="'.$image_path.'">
								</div>';
							}
			$contactHtml.= '</div></div>';
			echo $contactHtml;
		}							
@endphp 
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card card-custom mx-2">
      <div class="card-header card-header-tabs-line">
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
            <li class="nav-item me-3"> <a class="nav-link active" data-toggle="tab" href="#job_description"> <span class="nav-text font-weight-bold">Job Description</span> </a> </li>
            <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#interviews"> <span class="nav-text font-weight-bold">Timeline</span> </a> </li>
          </ul>
        </div>
      </div>
      <div class="card-body py-0">
        <div class="tab-content pt-5">
          <div class="tab-pane active" id="job_description" role="tabpanel">
            <div class="card-header cursor-pointer px-0">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Job Description</h3>
              </div>
              </div>
            <div class="card-body px-0 py-7">
              <div class="row">
                <div class="col-lg-12 mb-7">
                  <h4 class="" style="margin: 10px 0px;">Position - {{ ucwords(@$job->title) }} </h4>
                  <h5 class="" style="margin: 10px 0px;">Experience- {{ @$job->min_exp }} - {{ @$job->max_exp }} Years</h5>
                  <p> {!! @$job->job_description !!} </p>
                  <h5 class="" style="margin: 10px 0px;">About {!! ucwords(@$job->client_name) !!} </h5>
                  <p class="fw-bold fs-6 text-justify"> {!! ucfirst(@$job->client_description) !!} </p>
                </div>
              </div>
            </div>
          </div>
         
			  <div class="tab-pane" id="interviews" role="tabpanel">
				<div class="card-header cursor-pointer px-0">
				  <div class="card-title m-0">
					<h3 class="fw-bolder m-0">Timeline</h3>
				  </div>
				</div>
				<div class="card-body px-0 py-7">
				<div class="timeline">

          <?php
 
 
          if(!empty($updateTimeLine)){
            foreach($updateTimeLine as $tdata){

              $feedback_status = GeneralMethod::feedback_status();
              $available_status = GeneralMethod::available_status();

              $available_status_name = '';
              if ($tdata->available_status > 0) {
                $available_status_name = @$available_status[@$tdata->available_status]['name'];
              } 

              $feedback = ucwords(substr(@$feedback_status[@$tdata->feedback]['name'], 0, 12));


              
              $create_at	=	$$newDate	=	$newTime	= $prev_tiles_bg	=	$timeline_type = $current_tiles_bg =  "";
              $create_at	=	$tdata->create_at;
                  
              $newDate 							=	date("d-M-Y", strtotime($create_at));
              $newTime 							=	date("H:i A", strtotime($create_at));
              
              $posted_by_image					=	$comment							=	"";	
              if(!empty(@$tdata->image)){	
                $posted_by_image				=	url('public/user-uploads/team_profile_img/'.@$tdata->image);
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
                          <span class="fw-bold text-muted d-block fs-7">{{ ucwords(@$tdata->client_name) }}</span> 
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
                          <span class="text-dark fw-bolder fs-6">{{ @$newDate }}</span> 
                          <span class="text-muted fw-bold text-muted d-block fs-7">{{ @$newTime }}</span> 
                        </div>
                      </td>
                                  <td class="min-w-50px"><div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover">
                            <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="One"> 
                            <img alt="{{ ucwords(@$tdata->name) }}" src="{{ @$posted_by_image }}"> 
                            </div>
                                      </div>
                                      <a href="javascript:void(0);" class="text-muted fw-bold text-hover-primary d-block fs-7" 
                        data-toggle="tooltip" title="{{ ucwords(@$tdata->name) }}"> {{ substr(ucwords(@$tdata->name), 0, 7) }} </a> </div></td>
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
                          <span class="fw-bold text-muted d-block fs-7"> {{ ucwords(@$tdata->client_name) }} </span>
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
                                    </div></td>
                                  <!--end::Interviewers=-->
                                  <!--begin::Interviewer Feedback=-->
                                  <td class="min-w-70px"><div class="d-flex flex-column"> <span class="text-dark fw-bolder  fs-6">{{ ($feedback) ? $feedback : '----' }}</span> <span class="fw-bold text-muted d-block fs-7">{{ ($available_status_name ? $available_status_name : '----') }}</span> </div></td>
                                  <!--end::Interviewer Feedback-->
                                  <!--begin::Soruce-->
                                  <td class="min-w-50px"><div class="d-flex flex-column">
                                      <div class="symbol-group symbol-hover">
                                        <div class="symbol symbol-30 symbol-circle"  data-toggle="tooltip" data-placement="top" title="One"> 
                            <img alt="{{ ucwords(@$tdata->name) }}" src="{{ @$posted_by_image }}"> 
                          </div>
                                      </div>
                                      <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary" data-toggle="tooltip" title="{{ ucwords(@$tdata->name) }}">
                          {{ substr(ucwords(@$tdata->name), 0, 10) }}
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
                            
                            $imagePath	=	'';
                            $imagePath	=	url('public/user-uploads/team_profile_img/'.@$intfeed->image);	
                                      
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
                          <img src="{{ @$imagePath }}" alt="{{ @$intfeed->name }}"> 
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
                                  <th class="sorting_disabled" style="width: 344px;" rowspan="1" colspan="1">Offer Amount</th>
                                  <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 244px;">Joining Date</th>
                                  <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 79px;">Stage</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr role="row" class="odd">
                                  <td><div class="" style="">
          <input  type="text" class="form-control form-control-solid offer_amount" minlength="1" maxlength="10"  value="{{ @$offer_currency }}" style="width: 100px;display: initial; " readonly="">
          <input  type="text" class="form-control form-control-solid offer_amount" minlength="1" maxlength="10"  value="{{ @$tdata->offer_amount }}" style="width: 100px;display: initial; " readonly="">
          <input  type="text" class="form-control form-control-solid offer_amount" minlength="1" maxlength="10"  value="{{ @$offer_mode }}" style="width: 100px;display: initial; " readonly="">
                                    </div></td>
                                  <td><div class="d-flex align-items-center justify-content-stretch">
                                      <input type="text" id="joining_date_1428" value="{{ @$tdata->joined_date }}"  class="me-2 form-control form-control-solid" style="width:185px; " readonly="">
                                    </div></td>
                                  <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 offer-bg px-2 py-1" data-toggle="tooltip" title="Offered">Offered</span> </div></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
          <?php } }} ?>

            </div>
          </div>
          </div>
        </div>
      </div>  
    </div> 
  </div>  
</div>

