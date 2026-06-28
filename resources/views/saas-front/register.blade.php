@extends('layouts.metronic_front_app')
{{-- @dd("hi") --}}
<!--begin::Page title-->
<!--<title>Meecruit | Register</title>-->
<!--end::Page title-->
@section('content')
<!--begin::Head-->
<!--begin::Global Stylesheets Bundle(used by all pages)-->
<link href="{{ asset('demo/dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/dist/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/css/intlTelInput.css" rel="stylesheet"/>

<!--end::Global Stylesheets Bundle(used by all pages)-->
<style>
      /*Media query for smaller screen start*/
      @media only screen and (min-width: 320px) and (max-width: 768px)
      {
         .mb-sm-0
         {
            margin-bottom: 0px!important;
         }
      }
      /*Media query for smaller screen end*/

      /*Success screen popup css*/
      .swal2-popup .swal2-content, .swal2-popup .swal2-html-container
      {
         font-size: 16px;
      }
      .swal2-popup .swal2-title
      {
         font-size: 18px;
         font-weight: 600;
      }
      .swal2-styled.swal2-confirm
      {
         background-color: #009688;
         width: 100%;
         padding: 10px;
      }
      .iti--allow-dropdown{
        width: 100% !important; 
      }
      .iti__flag {
        margin-top: 2px !important;
      }


      .page-loader {
        justify-content: center;
        align-items: center;
        flex-direction: column; /* Stack the spinner and text vertically */
        height: 100vh; /* Full viewport height */
        width: 100vw; /* Full viewport width */
      }

      .spinner-border-full {
        width: 3rem; /* Adjust size as needed */
        height: 3rem;
      }

      .text-gray-800 {
        margin-top: 1rem; /* Space between spinner and text */
      }

      /*Success screen popup css end*/
   </style>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="bg-body">

  <div class="page-loader flex-column bg-dark bg-opacity-25">
    <span class="spinner-border spinner-border-full text-primary" role="status"></span>
    <span class="text-gray-800 fs-6 fw-semibold mt-5">Loading...</span>
  </div>

<!--begin::Main-->
<div class="d-flex flex-column flex-root">
  <!--begin::Authentication - Multi-steps-->
  <div class="d-flex flex-column flex-lg-row flex-column-fluid stepper stepper-pills stepper-column" id="kt_create_account_stepper">
    <!--begin::Aside-->
    <div class="d-flex flex-column flex-lg-row-auto w-xl-500px bg-lighten shadow-sm">
      <!--begin::Wrapper-->
      <div class="d-flex flex-column position-xl-fixed top-0 bottom-0 w-xl-500px scroll-y">
        <!--begin::Header-->
        <div class="d-flex flex-row-fluid flex-column flex-center p-10 pt-lg-20">
          <!--begin::Logo-->
          <a href="https://jero.works/" class="mb-10 mb-lg-10"> <img alt="Logo" src="{{ asset('img/logo-transparent1.svg') }}" class="h-70px" /> </a>
          <!--end::Logo-->
          <!--begin::Nav-->
          <div class="stepper-nav">
            <!--begin::Registration Details-->
            <div class="stepper-item current" data-kt-stepper-element="nav">
              <div class="stepper-line w-40px"></div>
              <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">1</span> </div>
              <div class="stepper-label">
                <h3 class="stepper-title">Registration Details</h3>
                <div class="stepper-desc fw-bold">Setup Your Registration Details</div>
              </div>
            </div>
            <!--end::Registration Details-->
            <!--begin::Company Details-->
            <div class="stepper-item" data-kt-stepper-element="nav">
              <div class="stepper-line w-40px"></div>
              <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">2</span> </div>
              <div class="stepper-label">
                <h3 class="stepper-title">Company Details</h3>
                <div class="stepper-desc fw-bold">Setup Your Account Details</div>
              </div>
            </div>
            <!--end::Company Details-->
            <!--begin::Account Details-->
            <div class="stepper-item" data-kt-stepper-element="nav">
              <div class="stepper-line w-40px"></div>
              <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">3</span> </div>
              <div class="stepper-label">
                <h3 class="stepper-title">Account Details</h3>
                <div class="stepper-desc fw-bold">Your Company Related Details</div>
              </div>
            </div>
            <!--end::Account Details-->
            <!--begin::Completed-->
            <div class="stepper-item" data-kt-stepper-element="nav">
              <div class="stepper-line w-40px"></div>
              <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">4</span> </div>
              <div class="stepper-label">
                <h3 class="stepper-title">Preview & Submit</h3>
                <div class="stepper-desc fw-bold">Woah, we are here</div>
              </div>
            </div>
            <!--end::Completed-->
          </div>
          <!--end::Nav-->
        </div>
        <!--end::Header-->
        <!--begin::Illustration-->
        <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-150px min-h-lg-300px" style="background-image: url(assets/media/illustrations/sketchy-1/16.png"></div>
        <!--end::Illustration-->
      </div>
      <!--end::Wrapper-->
    </div>
    <!--begin::Aside-->
    <!--begin::Body-->
    <div class="d-flex flex-column flex-lg-row-fluid py-10" id="upgrade-plan">
      <!--begin::Content-->
      <div class="d-flex flex-center flex-column flex-column-fluid">
        <!--begin::Wrapper-->
        <div class="w-lg-700px p-10 p-lg-15 mx-auto">
          <!--begin::Form-->
          <form class="my-auto pb-5" novalidate="novalidate" id="kt_create_account_form" method="post" name="kt_create_account_form">
           
          

             
          

          
		    <!--begin::Registration Details-->
            <div class="current" data-kt-stepper-element="content">
              <div class="w-100">
                <div class="pb-10 pb-lg-15">
                  <h2 class="fw-bolder d-flex align-items-center text-dark">Registration Details</h2>
                  <div class="text-muted fw-bold fs-6">If you need more info, please check out <a href="https://jero.works/book-a-demo" target="_blank" class="link-primary fw-bolder">Help Page</a>. </div>
                </div>

                
                <div class="mb-10 fv-row">
                  <label class="form-label mb-3 required">Enter Company Name</label>
                  <input type="text" class="form-control form-control-lg form-control-solid" name="company_name" placeholder="Enter company name" value="" />
                </div>

                <div class="fv-row mb-10">
                  <label class="form-label fw-bolder text-dark fs-6 required">Enter Your Name</label>
                  <input class="form-control form-control-solid" type="text" placeholder=""
                      name="name" autocomplete="off" />
                </div>

                <div class="fv-row mb-10">
                  <label class="form-label fw-bolder text-dark fs-6 required">Enter Your Email</label>
                  <input class="form-control form-control-solid" type="email" placeholder="" name="email" autocomplete="off" />
                </div>
                <div class="mb-7 fv-row" data-kt-password-meter="true">
                  <div class="mb-1">
                    <label class="form-label fw-bolder text-dark fs-6 required">Password</label>
                    <div class="position-relative mb-3">
                      <input class="form-control form-control-solid" type="password" placeholder="" name="password" id="password" autocomplete="off" />
                      <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility"> <i class="bi bi-eye-slash fs-2"></i> <i class="bi bi-eye fs-2 d-none"></i> </span> </div>
                    <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                      <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                      <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                      <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                      <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                    </div>
                  </div>
                  <div class="text-muted d-none">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</div>
                </div>
                <!--end::Input group=-->
                <!--begin::Confirm password-->
                <div class="mb-7 fv-row" data-kt-password-meter="true">
                  <!--begin::Wrapper-->
                  <div class="mb-1">
                    <!--begin::Label-->
                    <label class="form-label fw-bolder text-dark fs-6 required">Confirm Password</label>
                    <!--end::Label-->
                    <!--begin::Input wrapper-->
                    <div class="position-relative mb-3">
                      <input class="form-control form-control-solid" type="password" placeholder="" name="password_confirmation" id="password_confirmation" autocomplete="off" />
                      <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility"> <i class="bi bi-eye-slash fs-2"></i> <i class="bi bi-eye fs-2 d-none"></i> </span> </div>
                    <!--end::Input wrapper-->
                  </div>
                  <!--end::Wrapper-->
                </div>
                <!--end::Confirm password=-->
                <!--begin::Terms & conditions check-->
                <div class="fv-row mb-10 fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
                  <label class="form-check form-check-custom form-check-solid form-check-inline">
                  <input class="form-check-input" type="checkbox" checked="checked" name="toc" value="1">
                  <span class="form-check-label fw-bold text-gray-700 fs-6">I Agree <a href="{{ route('termsAndConditions') }}" target="_blank" class="ms-1 link-primary">Terms and conditions</a>. </span> </label>
                  <div class="fv-plugins-message-container invalid-feedback"></div>
                </div>
                <!--end::Terms & confitions check-->
              </div>
            </div>
            <!--end::Registration Details-->
			
            <!--begin::Company Details-->
            <div class="" data-kt-stepper-element="content">
              <div class="w-100">
                <div class="pb-10 pb-lg-15">
                  <h2 class="fw-bolder text-dark">Company Details</h2>
                  <div class="text-muted fw-bold fs-6">If you need more info, please check out <a href="https://jero.works/book-a-demo" target="_blank" class="link-primary fw-bolder">Help Page</a>. </div>
                </div>
                
                {{-- <div class="mb-10 fv-row">
                  <label class="d-flex align-items-center form-label mb-3 required">Specify Team Size <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Provide your team size to help us setup your subscription"></i></label>
                  <div class="row mb-2" data-kt-buttons="true">
                    <div class="col">
                      <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                      <input type="radio" class="btn-check" name="team_size" value="0" />
                      <span class="fw-bolder fs-3">1-50</span> </label>
                    </div>
                    <div class="col">
                      <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                      <input type="radio" class="btn-check" name="team_size" value="1" />
                      <span class="fw-bolder fs-3">51-200</span> </label>
                    </div>
                    <div class="col">
                      <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                      <input type="radio" class="btn-check" name="team_size" value="2" />
                      <span class="fw-bolder fs-3">201-1000</span> </label>
                    </div>
                    <div class="col">
                      <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                      <input type="radio" class="btn-check" name="team_size" value="3" />
                      <span class="fw-bolder fs-3">1000+</span> </label>
                    </div>
                  </div>
                  <div class="form-text">Customers will see this shortened version of your statement descriptor</div>
                </div> --}}
                <input type="hidden" name="team_size" id="team_size" value="">
                <div class="mb-0 fv-row">
                  <label class="d-flex align-items-center form-label mb-5 required">Select Company Type</label>
                  <div class="mb-0">
                    <label class="d-flex flex-stack mb-5 cursor-pointer"> <span class="d-flex align-items-center me-2"> <span class="symbol symbol-50px me-6"> <span class="symbol-label">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M13 10.9128V3.01281C13 2.41281 13.5 1.91281 14.1 2.01281C16.1 2.21281 17.9 3.11284 19.3 4.61284C20.7 6.01284 21.6 7.91285 21.9 9.81285C22 10.4129 21.5 10.9128 20.9 10.9128H13Z" fill="black" />
                        <path opacity="0.3" d="M13 12.9128V20.8129C13 21.4129 13.5 21.9129 14.1 21.8129C16.1 21.6129 17.9 20.7128 19.3 19.2128C20.7 17.8128 21.6 15.9128 21.9 14.0128C22 13.4128 21.5 12.9128 20.9 12.9128H13Z" fill="black" />
                        <path opacity="0.3" d="M11 19.8129C11 20.4129 10.5 20.9129 9.89999 20.8129C5.49999 20.2129 2 16.5128 2 11.9128C2 7.31283 5.39999 3.51281 9.89999 3.01281C10.5 2.91281 11 3.41281 11 4.01281V19.8129Z" fill="black" />
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      </span> </span> <span class="d-flex flex-column"> <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Corporate & Enterprise Companies</span> <span class="fs-6 fw-bold text-muted">Select if you are a product or manufacturing company.</span> </span> </span> <span class="form-check form-check-custom form-check-solid">
                      <input class="form-check-input" type="radio" name="company_type" value="3" />
                      </span> </label>

                   
                    <label class="d-flex flex-stack mb-5 cursor-pointer"> <span class="d-flex align-items-center me-2"> <span class="symbol symbol-50px me-6"> <span class="symbol-label">
                    <!--begin::Svg Icon-->
                    <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <path d="M13 5.91517C15.8 6.41517 18 8.81519 18 11.8152C18 12.5152 17.9 13.2152 17.6 13.9152L20.1 15.3152C20.6 15.6152 21.4 15.4152 21.6 14.8152C21.9 13.9152 22.1 12.9152 22.1 11.8152C22.1 7.01519 18.8 3.11521 14.3 2.01521C13.7 1.91521 13.1 2.31521 13.1 3.01521V5.91517H13Z" fill="black" />
                      <path opacity="0.3" d="M19.1 17.0152C19.7 17.3152 19.8 18.1152 19.3 18.5152C17.5 20.5152 14.9 21.7152 12 21.7152C9.1 21.7152 6.50001 20.5152 4.70001 18.5152C4.30001 18.0152 4.39999 17.3152 4.89999 17.0152L7.39999 15.6152C8.49999 16.9152 10.2 17.8152 12 17.8152C13.8 17.8152 15.5 17.0152 16.6 15.6152L19.1 17.0152ZM6.39999 13.9151C6.19999 13.2151 6 12.5152 6 11.8152C6 8.81517 8.2 6.41515 11 5.91515V3.01519C11 2.41519 10.4 1.91519 9.79999 2.01519C5.29999 3.01519 2 7.01517 2 11.8152C2 12.8152 2.2 13.8152 2.5 14.8152C2.7 15.4152 3.4 15.7152 4 15.3152L6.39999 13.9151Z" fill="black" />
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    </span> </span> <span class="d-flex flex-column"> <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Consulting or Service Companies</span> <span class="fs-6 fw-bold text-muted">Select if you Offer consulting services to your clients.</span> </span> </span> <span class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="radio" name="company_type" value="2" />
                    </span> </label>
                    <label class="d-flex flex-stack mb-5 cursor-pointer"> <span class="d-flex align-items-center me-2"> <span class="symbol symbol-50px me-6"> <span class="symbol-label">
                      <!--begin::Svg Icon-->
                      <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="black" />
                        <path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="black" />
                      </svg>
                      </span>
                      <!--end::Svg Icon-->
                      </span> </span> <span class="d-flex flex-column"> <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Staffing Companies</span> <span class="fs-6 fw-bold text-muted">Select if you Offers staffing or recruitment services to your clients .</span> </span> </span> <span class="form-check form-check-custom form-check-solid">
                      <input class="form-check-input" type="radio" name="company_type" value="1" />
                      </span> </label>

                  </div>
                </div>
              </div>
            </div>
            <!--end::Company Details-->
			
            <!--begin::Account Details-->
            <div class="" data-kt-stepper-element="content">
              <div class="w-100">
                <div class="pb-10 pb-lg-12">
                  <h2 class="fw-bolder text-dark">Account Details</h2>
                  <div class="text-muted fw-bold fs-6">If you need more info, please check out <a href="https://jero.works/book-a-demo" target="_blank" class="link-primary fw-bolder">Help Page</a>. </div>
                </div>
                <div class="fv-row mb-10">
                  <label class="form-label required">Website</label>
                  <input name="website" type="website" class="form-control form-control-lg form-control-solid" placeholder="Enter website URL" value="" />
                </div>
                <div class="fv-row mb-10 smsForm">
                  <label class="fs-6 fw-bold form-label required">Contact Number</label>
                  <input name="contact_number" id="contact_number" type="number" class="form-control form-control-lg form-control-solid" placeholder="Enter contact number" value="" pattern="[7-9]{1}[0-9]{9}" required />
                  <input name="contact_code" id="contact_code" type="hidden" class="form-control form-control-lg form-control-solid" />
                </div>
                <div class="fv-row mb-0 smsForm">
                  <label class="fs-6 fw-bold form-label required">Select City:</label>
                  <input class="form-control form-control-lg form-control-solid" value="" id="kt_tagify_work_location" name="kt_tagify_work_location" placeholder="Enter your Location..."/>
               </div>
              </div>
            </div>
            <!--end::Account Details-->
			
            <!--begin::Completed-->
            <div class="" data-kt-stepper-element="content">
              <div class="w-100">
                <div class="pb-10 pb-lg-10">
                  <h2 class="fw-bolder d-flex align-items-center text-dark">Registratation Preview!</h2>
                  <div class="text-muted fw-bold fs-6">please check out all info before submit.</div>
                </div>
                <div class="mb-0">
				
                  <div class="fs-6 text-gray-600 mb-5">
				  
                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Company Name</label>
                      </div>
                      <div class="col-md-9 col-sm-12"> <span id="preview_company_name">&nbsp;</span> </div>
                    </div>
                    
                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Name</label>
                      </div>
                      <div class="col-md-3 col-sm-12"> <span id="preview_name">&nbsp;</span> </div>
                    </div>

                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Email</label>
                      </div>
                      <div class="col-md-3 col-sm-12"> <span id="preview_email">&nbsp;</span> </div>
                    </div>
					
                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Password</label>
                      </div>
                      <div class="col-md-9 col-sm-12"> <span id="preview_email">********</span> </div>
                    </div>
					
                    {{-- <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Specify Team Size</label>
                      </div>
                      <div class="col-md-9 col-sm-12"> <span id="preview_team_size" class="w-100">&nbsp;</span> </div>
                    </div> --}}
					
                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Company Type</label>
                      </div>
                      <div class="col-md-9 col-sm-12"> <span id="preview_company_type">&nbsp;</span> </div>
                    </div>
					
                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Website</label>
                      </div>
                      <div class="col-md-9 col-sm-12"> <span id="preview_website">&nbsp;</span> </div>
                    </div>
					
                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">Contact Number</label>
                      </div>
                      <div class="col-md-9 col-sm-12" style="display: flex;"> 
                        <div class="iti__flag-container" id="phoneDisplay" style="display: flex;position: relative;"> <!-- Flag and dial code will be appended here --> </div>
                        <span id="preview_contact_number" style="margin-left:4px">&nbsp;</span> </div>
                    </div>

                    <div class="row mb-5">
                      <div class="col-md-3 col-sm-12">
                        <label class="form-label fw-bolder text-dark fs-6">City</label>
                      </div>
                      <div class="col-md-9 col-sm-12"> <span id="preview_city">&nbsp;</span> </div>
                    </div>
					
                  </div>
                </div>
                <!-- <div class="pb-8 pb-lg-10">
                              <h2 class="fw-bolder text-dark">Registratation Preview!</h2>
                              <div class="text-muted fw-bold fs-6 d-none">If you need more info, please
                                 <a href="{{ route('login') }}" class="link-primary fw-bolder">Sign In</a>.
                              </div>  
                           </div>-->
                <div class="mb-0">
                  <!--<div class="fs-6 text-gray-600 mb-5">
							  		We have sent you a verifaction link on email 
									<a href="javascript:void(0);" class="link-primary fw-bolder verification_email">&nbsp;</a> 
									please click on the link and complete the registration process.
                              </div>-->
                  <!-- <div class="fs-6 text-gray-600 mb-5">Did't receive an email? <a href="javascript:void(0);" class="link-primary fw-bolder">Resend</a></div>-->
                </div>
              </div>
            </div>
            <!--end::Completed-->
			
			
            <div class="d-flex flex-stack pt-5">
              <div class="mr-2">
                <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous"> <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1" fill="black" />
                  <path d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z" fill="black" />
                </svg>
                </span> Previous </button>
              </div>
              <div> </div>
              <div> <a href="{{ route('login') }}" type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="submit"> <span class="indicator-label"> Submit <span class="svg-icon svg-icon-4 ms-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
                  <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
                </svg>
                </span> </span> <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span> </a>
                <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next"> Continue <span class="svg-icon svg-icon-4 ms-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
                  <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
                </svg>
                </span> </button>
              </div>
            </div>
            @csrf
          </form>
          <!--end::Form-->
        </div>
        <!--end::Wrapper-->
      </div>
      <!--end::Content-->
      <!--begin::Footer-->
      <div class="d-flex flex-center flex-wrap fs-6 p-5 pb-0">
        <!--begin::Links-->
        <div class="d-flex flex-center fw-bold fs-6"> 
          <a href="https://jero.works/about-us" class="text-muted text-hover-primary px-2" target="_blank">About</a> 
          <a href="https://jero.works/support-policy" class="text-muted text-hover-primary px-2" target="_blank">Support Policy</a>
           <a href="https://ats.jero.works/payment-terms-and-conditions" class="text-muted text-hover-primary px-2" target="_blank">Purchase</a> 
          </div>
        <!--end::Links-->
      </div>
      <!--end::Footer-->
    </div>
    <!--end::Body-->
  </div>
  <!--end::Authentication - Multi-steps-->
</div>
<!--end::Main-->
</body>
<!--end::Body-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('demo/dist/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('demo/dist/assets/js/scripts.bundle.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.17/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="{{ asset('front/assets/js/countryDialCodes.js') }}"></script>

<script>


let inputId = `#kt_tagify_work_location`;
        let inputElement = document.querySelector(inputId);

        if (inputElement) {
            let tagifyInstance = new Tagify(inputElement, {
                whitelist: [],
                enforceWhitelist: true, // Prevents users from adding custom values
                maxTags: 1,
                dropdown: {
                    maxItems: 20,           // maximum allowed rendered suggestions
                    classname: "", // custom classname for this dropdown
                    enabled: 0,             // show suggestions on focus
                    closeOnSelect: true    // do not hide the suggestions dropdown once an item has been selected
                }
            });

            // Prevent creating tags on blur or enter key
            tagifyInstance.on('input', function(e) {
                tagifyInstance.dropdown.show(); // show the dropdown immediately
            });

            // Create tags only on select
            tagifyInstance.on('dropdown:select', function(e) {
                tagifyInstance.addTags([e.detail.data.value]);
            });

            function debounce(func, delay) {     
            let timer;
                return function (...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(this, args), delay);
                };
            }

            const fetchSuggestions = debounce(function(query) {
            $.ajax({
                url: "{{ url('/') }}" + "/city-job-suggestions?q=" + query,
                method: 'GET',
                success: function(data) {
                    console.warn("Fetched Cities: ", data);

                    if (Array.isArray(data)) {
                      tagifyInstance.settings.whitelist = data; 
                      // tagifyInstance.dropdown.show(query); 
                      tagifyInstance.dropdown.show.call(tagifyInstance);
                    } else {
                        console.error("Invalid API response format: Expected array.");
                    }
                },
                error: function(err) {
                    console.error("Error fetching cities:", err);
                }
            });
        }, 50); // 300ms delay
        tagifyInstance.on('input', function(e) {
            var query = e.detail.value.trim();
            if (query.length < 1) return;
            fetchSuggestions(query);
            
        });


        }




  const phoneInputField = document.querySelector("#contact_number");
  const phoneInput = window.intlTelInput(phoneInputField, { 
        separateDialCode: true,
        dropdown: true,
        initialCountry: "in",
        preferredCountries: ["in", "us", "gb", "ca", "au"]
      });
      window.addEventListener('load', function() {
        // Get the forms we want to add validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
      $(document).ready(function() {
        $('.smsForm .iti__flag-container').click(function() { 
          var countryCode = $('.smsForm .iti__selected-flag').attr('title');
          var countryCode = countryCode.replace(/[^0-9]/g,'');
          $('#contact_number').val("");
          $('#contact_number').val("+"+countryCode+" "+ $('#contact_number').val());
        });
        
      });

      $(".iti__flag-container").on("click", function (event) {
        var iti__active  			=	$(".iti__active").attr('data-country-code'); 
         // alert(iti__active);
         $('#contact_code').val(iti__active);
      
      });
  
</script>

<!--end::Global Javascript Bundle-->
<script>
   $(document).ready(function()
   {  
   
	
		
       
      var KTCreateAccount = (function () {
         var e,
            t,
            i,
            o,
            r,
            s,
            a = [];
         return {
            init: function () {
                  	(t = document.querySelector("#kt_modal_create_account")) && (e = new bootstrap.Modal(t)),
					(i = document.querySelector("#kt_create_account_stepper")) && ( 
					(o = i.querySelector("#kt_create_account_form")), 
					(r = i.querySelector('[data-kt-stepper-action="submit"]')),
					(s = new KTStepper(i)).on("kt.stepper.next", function (e) {
												  console.log("stepper.next");
												  var t = a[e.getCurrentStepIndex() - 1];
												  
												 
												  
var email  			=	$("input[name=email]").val(); 
var name  			=	$("input[name=name]").val(); 
var company_name  	=	$("input[name=company_name]").val(); 
var team_size  		=	$("input[name='team_size']:checked").val();
if(team_size == 0){
	team_size	= '1-50';	
}else if(team_size == 1){
	team_size		=	'51-200';	
}else if(team_size == 2){
	team_size		=	'201-1000';	
}else{ 
	team_size		=	'1000+';	
}
var company_type  	=	$("input[name='company_type']:checked").val();
if(company_type == 1){
	company_type	=	'Staffing Partners - Recruitment Consultant, Staffing Companies, Man-Power Companies.';
}else if(company_type == 2){
	company_type	=	'Consulting Companies - IT Companies, Trainings Companies, Service Based Companies.';
}else if(company_type == 3){
	company_type	=	'Other Companies - Product Based and similar Companies.';
}
var website  		=	$("input[name=website]").val();
var contact_number  =	$("input[name=contact_number]").val(); 
var kt_tagify_work_location		=	$('#kt_tagify_work_location').val();
var city = kt_tagify_work_location
                    .replace('[{"value":"', '')  // Remove the starting part
                    .replace('"}]', ''); 
var iti__active  			=	$(".iti__active").attr('data-country-code'); 
// alert(iti__active);
$('#contact_code').val(iti__active);

$('#preview_email').html(email);
$('#preview_name').html(name);
$('#preview_company_name').html(company_name);
$('#preview_team_size').html(team_size);	
$('#preview_company_type').html(company_type);	
$('#preview_website').html(website);
$('#preview_contact_number').html(contact_number);
$('#preview_city').html(city);

const flagContainer = document.getElementById(`phoneDisplay`);
                if (flagContainer) {
                  $('#phoneDisplay').html('');
                    const flagDiv = document.createElement("div");
                    flagDiv.className = `iti__flag iti__${iti__active}`; // Ensure calling_code is in lowercase

                    const dialCode = countryDialCodes[iti__active] || ""; // Fetch the dial code dynamically
                    const dialCodeSpan = document.createElement("span");
                    dialCodeSpan.textContent = dialCode;

                    // Append the flag and dial code
                    flagContainer.appendChild(flagDiv);
                    flagContainer.appendChild(dialCodeSpan);
                }


												  
												  console.log(t);
												  
												  t ? t.validate().then(function (t) {
														console.log("validated!"),
															  "Valid" == t ? (e.goNext(), KTUtil.scrollTop())
																 : Swal.fire({
																				text				: "Sorry, looks like there are some errors detected, please try again.",
																				icon				: "error",
																				buttonsStyling		: !1,
																				confirmButtonText	: "Ok, got it!",
																				customClass			: { confirmButton: "btn btn-light" },
																 }).then(function () {
																	KTUtil.scrollTop();
																 }); }) : (e.goNext(), KTUtil.scrollTop());}),
																 
																 
															 
						
						
                        s.on("kt.stepper.previous", function (e) {
							
                              console.log("stepper.previous"), e.goPrevious(), KTUtil.scrollTop();
                        }),
                     
                        a.push(
                              FormValidation.formValidation(o,
                              {
                                 fields:
                                 {
                                  company_name:
                                    {
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Company name is required" 
                                             } 
                                          } 
                                    },
                                  name: {
                                            validators: {
                                                notEmpty: {
                                                    message: "Name is required"
                                                }
                                            }
                                        },
                                    email:
                                    {
                                          validators:
                                          {
                                             notEmpty:
                                             {
                                                message: "Email is required"
                                             },
                                             emailAddress:
                                             {
                                                message: "The value is not a valid email address"
                                             }
                                          }
                                    },                              
                                    password:
                                    {
                                       validators:
                                       {
                                          notEmpty:
                                          {
                                             message: "Please enter password"
                                          }
                  
                                       }
                                    },	
                                    password_confirmation:
                                    {
                                          validators:
                                          {
                                             notEmpty:
                                             {
                                                message: "Please enter confirm password"
                                             },
                                             identical: {
                                                 compare: function() {
                                                     return $('[name="password"]').val();
                                                     },
                                                     message: "The password and its confirm are not the same"
                                                     }
																																		
                                          }
                                    },
                                    toc: 
                                    { 
                                       validators: 
                                       { 
                                          notEmpty: 
                                          { 
                                             message: "You must accept the terms and conditions" 
                                          } 
                                       } 
                                    },
                                 },
                                 plugins:
                                 {
                                    trigger: new FormValidation.plugins.Trigger(),
                                    bootstrap: new FormValidation.plugins.Bootstrap5({
                                    													rowSelector		: '.fv-row',
                                    													eleInvalidClass	: '',
                                    													eleValidClass	: ''
                                    												 })
                                 }
                              })
                        ),
                        a.push(
                              FormValidation.formValidation(o, {
                                 fields:
                                 {
                                    // team_size: 
                                    // { 
                                    //       validators: 
                                    //       { 
                                    //          notEmpty: 
                                    //          { 
                                    //             message: "Team size is required" 
                                    //          } 
                                    //       } 
                                    // },
                                    company_type: 
                                    { 
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Company type is required" 
                                             } 
                                          } 
                                    },
                                 },
                                 plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                              })
                        ),
                        a.push(
                              FormValidation.formValidation(o, {
                                 fields:
                                 {
                                    website: 
                                    { 
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Website URL is required" 
                                             } 
                                          } 
                                    },
                                    specialties: 
                                    { 
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Specialties is required" 
                                             } 
                                          } 
                                    },
                                    contact_number: 
                                    { 
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Contact Number is required" 
                                             },
                                             digits: 
                                             { 
                                                message: "Contact Number must contain only digits" 
                                             }, 
                                             stringLength: 
                                             { 
                                                min: 10, 
                                                max: 10, 
                                                message: "Contact Number must contain 10 digits only" 
                                             } 
                                          } 
                                    },
                                    kt_tagify_work_location: 
                                    { 
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "City is required" 
                                             } 
                                          } 
                                    },
                                 },
                                 plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                              })
                        ),
                       
                      
						  r.addEventListener("click", function (t) {
                              t.preventDefault(),
                                 (r.disabled = !0),
                                 r.setAttribute("data-kt-indicator", "on"),
                                //  setTimeout(function () {
                                          
                                  $(this).prop("disabled", true);
                                  r.setAttribute("disabled", true);
                                  $(this).html(
                                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span class="indicator-label"> Submit <span class="svg-icon svg-icon-4 ms-2">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black"></rect>
                                    <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black"></path>
                                  </svg>
                                  </span> </span><span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>`
                                                    );
                                  $('.indicator-label').hide();
                                  $('.indicator-progress').show();
                              $.ajax({
                                    url			: "{{route('company-register')}}",
                                    container	: '#kt_create_account_form',
                                    type		: "POST",
                                    data		: $('#kt_create_account_form').serialize(),
                                    success: function(response) 
                                    {
                                       $('.indicator-label').hide();
                                       $('.indicator-progress').show();
                                          //alert(response.status); 
                                          // $('#kt_create_account_form').trigger("reset");
                                          if(response.status=='success')
                                          {	
                                            $('#kt_create_account_form').trigger("reset");
                                            Swal.fire("Sent!", "Your account has been created successfully.\n Check your email to activate your account.", "success");
                                            
                                            $('#upgrade-plan').html(response.view);
                                          }
                                          else
                                          {
                                            
                                            $('.indicator-label').show();
                                            $('.indicator-progress').hide();
                                            Swal.fire("Error!", "Your need to fill all the form fields!", "error");
                                          }

                                          

                                    },
                                    error: function(xhr, status, error) {
                                            $('.indicator-label').show();
                                            $('.indicator-progress').hide();
                                            Swal.fire("Error!", "Your need to fill all the form fields!", "error");
                                    }
                              }),
      
                                          
                  r.removeAttribute("data-kt-indicator"),
                     (r.disabled = !1)
					 
                    //  }, 2e3);
                        }),
						
						
						
						  $(o.querySelector('[name="email"]')).on("change", function () {            
							  		var email  = $("input[name=email]").val();
										if(email != '')
										{
                      $('.page-loader').css('display', 'flex');
												$.ajax({
														url: "{{route('company-register-chk')}}",
														container: '#kt_create_account_form',
														type: "POST",
														data: $('#kt_create_account_form').serialize(),
														success: function(response) {
                              $('.page-loader').css('display', 'none');
                                                if(response.status == 'fail')
                                                {
                                                  //$('#kt_create_account_form').trigger("reset");
                                                
                                                  $("input[name=email]").val('');
                                                  //new FormValidation.plugins.Trigger();
                                                
                                                
                                                  "Valid" == t ? (e.goNext(), KTUtil.scrollTop())
                                                  : Swal.fire({
                                                            text				: "Sorry, Email ["+email+"]  is already registered, Please use another email.",
                                                            icon				: "error",
                                                            buttonsStyling		: !1,
                                                            confirmButtonText	: "Ok, got it!",
                                                            customClass			: { confirmButton: "btn btn-light" },
                                                  }).then(function () {
                                                    KTUtil.scrollTop();
                                                  }); 
                            
                                                } else if(response.status == 'emailchecker'){
                                                   $("input[name=email]").val('');
                                                    //new FormValidation.plugins.Trigger();
                                                  
                                                  
                                                    "Valid" == t ? (e.goNext(), KTUtil.scrollTop())
                                                    : Swal.fire({
                                                              text				: response.message,
                                                              icon				: "error",
                                                              buttonsStyling		: !1,
                                                              confirmButtonText	: "Ok, got it!",
                                                              customClass			: { confirmButton: "btn btn-light" },
                                                    }).then(function () {
                                                      KTUtil.scrollTop();
                                                    }); 

                                                } else {
                                                  $('.verification_email').val(email);
                                                }
												 
												 
																					}
													});
																				
                                    		
                    }  	
											   
              }),

              $(o.querySelector('[name="contact_number"]')).on("change", function () {            
							  		var contact_number  = $("input[name=contact_number]").val();
										if(contact_number != '')
										{
                      $('.page-loader').css('display', 'flex');
												$.ajax({
														url: "{{route('contact-company-register-chk')}}",
														container: '#kt_create_account_form',
														type: "POST",
														data: $('#kt_create_account_form').serialize(),
														success: function(response) {
                              $('.page-loader').css('display', 'none');
                                                if(response.status == 'fail')
                                                {
                                                  //$('#kt_create_account_form').trigger("reset");
                                                
                                                  $("input[name=contact_number]").val('');
                                                  //new FormValidation.plugins.Trigger();
                                                
                                                
                                                  "Valid" == t ? (e.goNext(), KTUtil.scrollTop())
                                                  : Swal.fire({
                                                            text				: "Sorry, Contact Number ["+contact_number+"]  is already registered, Please use another Contact Number.",
                                                            icon				: "error",
                                                            buttonsStyling		: !1,
                                                            confirmButtonText	: "Ok, got it!",
                                                            customClass			: { confirmButton: "btn btn-light" },
                                                  }).then(function () {
                                                    KTUtil.scrollTop();
                                                  }); 
                            
                                                }
                                                else
                                                {
                                                  $('#contact_number').val(contact_number);
                                                }
																					}
													});             		
                    }  	
											   
              }),

              $(o.querySelector('[name="website"]')).on("change", function () {            
							  		var website  = $("input[name=website]").val();
                    
										if(website != '')
										{
                      $('.page-loader').css('display', 'flex');
												$.ajax({
														url: "{{route('domain-company-register-chk')}}",
														container: '#kt_create_account_form',
														type: "POST",
														data: $('#kt_create_account_form').serialize(),
														success: function(response) {
                              $('.page-loader').css('display', 'none');
                                                if(response.status == 'fail')
                                                {
                                                  //$('#kt_create_account_form').trigger("reset");
                                                
                                                  $("input[name=website]").val('');
                                                  //new FormValidation.plugins.Trigger();
                                                
                                                
                                                  "Valid" == t ? (e.goNext(), KTUtil.scrollTop())
                                                  : Swal.fire({
                                                            title:'Account Already Exists',
                                                            html				: 'An account is already registered on <strong>Jero.Works</strong> using <strong><a href="javascript:void();">'+response.company_email+'</a></strong> <br><br>If you’ve forgotten your password, please use the <strong>“Forgot Password”</strong> option to log in.<br><br>If this email doesn’t belong to your organization, contact us at <a href="mailto:support@jero.works"> support@jero.works</a>.</strong><br><br><strong></strong> Please avoid creating multiple accounts for the same organization. Duplicate accounts may lead to suspension.',
                                                            icon				: "warning",
                                                            width: '800px', // Large width
                                                            buttonsStyling		: !1,
                                                            confirmButtonText	: "Ok, got it!",
                                                            customClass			: { confirmButton: "btn btn-light" },
                                                  }).then(function () {
                                                    KTUtil.scrollTop();
                                                  }); 
                            
                                                }
                                                else
                                                {
                                                  $('[name="website"]').val(website);
                                                }
																					}
													});             		
                    }  	
											   
              }),
						
              $(o.querySelector('[name="card_expiry_month"]')).on("change", function () {
                    a[3].revalidateField("card_expiry_month");
              }),
              $(o.querySelector('[name="card_expiry_year"]')).on("change", function () {
                    a[3].revalidateField("card_expiry_year");
              }),
              $(o.querySelector('[name="business_type"]')).on("change", function () {
                    a[2].revalidateField("business_type");
              }));
            },
         };
      })();
      KTUtil.onDOMContentLoaded(function () {
         KTCreateAccount.init();
      });         
   });         
</script>
<!--end::Validation-->
@endsection