@section('content')
<!--begin::Head-->
<head>
   <title>Mily Recruit | Register</title>
   <meta name="description" content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 94,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
   <meta name="keywords" content="Metronic, bootstrap, bootstrap 5, Angular, VueJs, React, Laravel, admin themes, web design, figma, web development, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dak mode, bootstrap button, bootstrap datepicker, bootstrap timepicker, fullcalendar, datatables, flaticon" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <meta charset="utf-8" />
   <meta property="og:locale" content="en_US" />
   <meta property="og:type" content="article" />
   <meta property="og:title" content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
   <meta property="og:url" content="https://keenthemes.com/metronic" />
   <meta property="og:site_name" content="Keenthemes | Metronic" />
   <link rel="canonical" href="Https://preview.keenthemes.com/metronic8" />
   <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
   <!--begin::Fonts-->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
   <!--end::Fonts-->
   <!--begin::Global Stylesheets Bundle(used by all pages)-->
   <link href="{{ asset('demo/dist/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
   <link href="{{ asset('demo/dist/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
   <!--end::Global Stylesheets Bundle(used by all pages)-->

   <style>
   /* section-card — matches UI_UX_STANDARDS §10 (white bg, border, shadow). */
   .section-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 12px rgba(0,0,0,.03);
   }
   </style>
</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_body" class="bg-body">
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
                  <a href="javascript:void(0);" class="mb-10 mb-lg-20">
                  <img alt="Logo" src="https://preview.keenthemes.com/metronic8/demo1/assets/media/logos/logo-1.svg" class="h-40px" />
                  </a>
                  <!--end::Logo-->
                  <!--begin::Nav-->
                  <div class="stepper-nav">
                     <!--begin::Registration Details-->
                     <div class="stepper-item current" data-kt-stepper-element="nav">
                        <div class="stepper-line w-40px"></div>
                        <div class="stepper-icon w-40px h-40px">
                           <i class="stepper-check fas fa-check"></i>
                           <span class="stepper-number">1</span>
                        </div>
                        <div class="stepper-label">
                           <h3 class="stepper-title">Registration Details</h3>
                           <div class="stepper-desc fw-bold">Setup Your Registration Details</div>
                        </div>
                     </div>
                     <!--end::Registration Details-->
                     <!--begin::Company Details-->
                     <div class="stepper-item" data-kt-stepper-element="nav">
                        <div class="stepper-line w-40px"></div>
                        <div class="stepper-icon w-40px h-40px">
                           <i class="stepper-check fas fa-check"></i>
                           <span class="stepper-number">2</span>
                        </div>
                        <div class="stepper-label">
                           <h3 class="stepper-title">Company Details</h3>
                           <div class="stepper-desc fw-bold">Setup Your Account Details</div>
                        </div>
                     </div>
                     <!--end::Company Details-->
                     <!--begin::Account Details-->
                     <div class="stepper-item" data-kt-stepper-element="nav">
                        <div class="stepper-line w-40px"></div>
                        <div class="stepper-icon w-40px h-40px">
                           <i class="stepper-check fas fa-check"></i>
                           <span class="stepper-number">3</span>
                        </div>
                        <div class="stepper-label">
                           <h3 class="stepper-title">Account Details</h3>
                           <div class="stepper-desc fw-bold">Your Company Related Details</div>
                        </div>
                     </div>
                     <!--end::Account Details-->
                     <!--begin::Subscription-->
                     <div class="stepper-item" data-kt-stepper-element="nav">
                        <div class="stepper-line w-40px"></div>
                        <div class="stepper-icon w-40px h-40px">
                           <i class="stepper-check fas fa-check"></i>
                           <span class="stepper-number">4</span>
                        </div>
                        <div class="stepper-label">
                           <h3 class="stepper-title">Subscription</h3>
                           <div class="stepper-desc fw-bold">Set Your Subscription</div>
                        </div>
                     </div>
                     <!--end::Subscription-->
                     <!--begin::Completed-->
                     <div class="stepper-item" data-kt-stepper-element="nav">
                        <div class="stepper-line w-40px"></div>
                        <div class="stepper-icon w-40px h-40px">
                           <i class="stepper-check fas fa-check"></i>
                           <span class="stepper-number">5</span>
                        </div>
                        <div class="stepper-label">
                           <h3 class="stepper-title">Completed</h3>
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
               <div class="w-lg-900px p-5 p-lg-5 mx-auto section-card">
                  <!--begin::Form-->
                  <form class="my-auto pb-5" novalidate="novalidate" id="kt_create_account_form" method="post" name="kt_create_account_form">
                     <!--begin::Registration Details-->
                     <div class="current" data-kt-stepper-element="content">
                        <div class="w-100">
                           <div class="pb-10 pb-lg-15">
                              <h2 class="fw-bolder d-flex align-items-center text-dark">Registration Details</h2>
                              <div class="text-muted fw-bold fs-6">If you need more info, please check out
                                 <a href="#" class="link-primary fw-bolder">Help Page</a>.
                              </div>
                           </div>
                           <div class="fv-row mb-10">
                              <label class="form-label fw-bolder text-dark fs-6 required">Email</label>
                              <input class="form-control form-control-solid" type="email" placeholder="" name="email" autocomplete="off" />
                           </div>
                           <div class="mb-7 fv-row" data-kt-password-meter="true">
                              <div class="mb-1">
                                 <label class="form-label fw-bolder text-dark fs-6 required">Password</label>
                                 <div class="position-relative mb-3">
                                    <input class="form-control form-control-solid" type="password" placeholder="" name="password" id="password" autocomplete="off" minlength="8" />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                 </div>
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
                           <!--begin::Input group-->
                           <div class="mb-7 fv-row" data-kt-password-meter="true">
                              <!--begin::Wrapper-->
                              <div class="mb-1">
                                 <!--begin::Label-->
                                 <label class="form-label fw-bolder text-dark fs-6 required">Confirm Password</label>
                                 <!--end::Label-->
                                 <!--begin::Input wrapper-->
                                 <div class="position-relative mb-3">
                                    <input class="form-control form-control-solid" type="password" placeholder="" name="confirm_password" id="confirm_password" autocomplete="off" minlength="8" />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                 </div>
                                 <!--end::Input wrapper-->
                              </div>
                              <!--end::Wrapper-->
                           </div>
                           <!--end::Input group=-->
                        </div>
                     </div>
                     <!--end::Registration Details-->
                     <!--begin::Company Details-->
                     <div class="" data-kt-stepper-element="content">
                        <div class="w-100">
                           <div class="pb-10 pb-lg-15">
                              <h2 class="fw-bolder text-dark">Company Details</h2>
                              <div class="text-muted fw-bold fs-6">If you need more info, please check out
                                 <a href="#" class="link-primary fw-bolder">Help Page</a>.
                              </div>
                           </div>
                           <div class="mb-10 fv-row">
                              <label class="form-label mb-3 required">Company Name</label>
                              <input type="text" class="form-control form-control-lg form-control-solid" name="company_name" placeholder="Enter company name" value="" maxlength="191" minlength="2" />
                           </div>
                           <div class="mb-10 fv-row">
                              <label class="d-flex align-items-center form-label mb-3 required">Specify Team Size
                              <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Provide your team size to help us setup your subscription"></i></label>
                              <div class="row mb-2" data-kt-buttons="true">
                                 <div class="col">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                                    <input type="radio" class="btn-check" name="team_size" value="0" />
                                    <span class="fw-bolder fs-3">1-50</span>
                                    </label>
                                 </div>
                                 <div class="col">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                                    <input type="radio" class="btn-check" name="team_size" value="1" />
                                    <span class="fw-bolder fs-3">51-200</span>
                                    </label>
                                 </div>
                                 <div class="col">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                                    <input type="radio" class="btn-check" name="team_size" value="2" />
                                    <span class="fw-bolder fs-3">201-1000</span>
                                    </label>
                                 </div>
                                 <div class="col">
                                    <label class="btn btn-outline btn-outline-dashed btn-outline-default w-100 p-4">
                                    <input type="radio" class="btn-check" name="team_size" value="3" />
                                    <span class="fw-bolder fs-3">1000+</span>
                                    </label>
                                 </div>
                              </div>
                              <div class="form-text">Customers will see this shortened version of your statement descriptor</div>
                           </div>
                           <div class="mb-0 fv-row">
                              <label class="d-flex align-items-center form-label mb-5 required">Select Company Type</label>
                              <div class="mb-0">
                                 <label class="d-flex flex-stack mb-5 cursor-pointer">
                                    <span class="d-flex align-items-center me-2">
                                       <span class="symbol symbol-50px me-6">
                                          <span class="symbol-label">
                                             <!--begin::Svg Icon-->
                                             <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                   <path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="black" />
                                                   <path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="black" />
                                                </svg>
                                             </span>
                                             <!--end::Svg Icon-->
                                          </span>
                                       </span>
                                       <span class="d-flex flex-column">
                                       <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Staffing Partners</span>
                                       <span class="fs-6 fw-bold text-muted">Recruitment Consultant, Staffing Companies, Man-Power Companies.</span>
                                       </span>
                                    </span>
                                    <span class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="company_type" value="1" />
                                    </span>
                                 </label>
                                 <label class="d-flex flex-stack mb-5 cursor-pointer">
                                    <span class="d-flex align-items-center me-2">
                                       <span class="symbol symbol-50px me-6">
                                          <span class="symbol-label">
                                             <!--begin::Svg Icon-->
                                             <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                   <path d="M13 5.91517C15.8 6.41517 18 8.81519 18 11.8152C18 12.5152 17.9 13.2152 17.6 13.9152L20.1 15.3152C20.6 15.6152 21.4 15.4152 21.6 14.8152C21.9 13.9152 22.1 12.9152 22.1 11.8152C22.1 7.01519 18.8 3.11521 14.3 2.01521C13.7 1.91521 13.1 2.31521 13.1 3.01521V5.91517H13Z" fill="black" />
                                                   <path opacity="0.3" d="M19.1 17.0152C19.7 17.3152 19.8 18.1152 19.3 18.5152C17.5 20.5152 14.9 21.7152 12 21.7152C9.1 21.7152 6.50001 20.5152 4.70001 18.5152C4.30001 18.0152 4.39999 17.3152 4.89999 17.0152L7.39999 15.6152C8.49999 16.9152 10.2 17.8152 12 17.8152C13.8 17.8152 15.5 17.0152 16.6 15.6152L19.1 17.0152ZM6.39999 13.9151C6.19999 13.2151 6 12.5152 6 11.8152C6 8.81517 8.2 6.41515 11 5.91515V3.01519C11 2.41519 10.4 1.91519 9.79999 2.01519C5.29999 3.01519 2 7.01517 2 11.8152C2 12.8152 2.2 13.8152 2.5 14.8152C2.7 15.4152 3.4 15.7152 4 15.3152L6.39999 13.9151Z" fill="black" />
                                                </svg>
                                             </span>
                                             <!--end::Svg Icon-->
                                          </span>
                                       </span>
                                       <span class="d-flex flex-column">
                                       <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Consulting Companies</span>
                                       <span class="fs-6 fw-bold text-muted">IT Companies, Trainings Companies, Service Based Companies.</span>
                                       </span>
                                    </span>
                                    <span class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="company_type" value="2" />
                                    </span>
                                 </label>
                                 <label class="d-flex flex-stack mb-0 cursor-pointer">
                                    <span class="d-flex align-items-center me-2">
                                       <span class="symbol symbol-50px me-6">
                                          <span class="symbol-label">
                                             <!--begin::Svg Icon-->
                                             <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                   <path d="M13 10.9128V3.01281C13 2.41281 13.5 1.91281 14.1 2.01281C16.1 2.21281 17.9 3.11284 19.3 4.61284C20.7 6.01284 21.6 7.91285 21.9 9.81285C22 10.4129 21.5 10.9128 20.9 10.9128H13Z" fill="black" />
                                                   <path opacity="0.3" d="M13 12.9128V20.8129C13 21.4129 13.5 21.9129 14.1 21.8129C16.1 21.6129 17.9 20.7128 19.3 19.2128C20.7 17.8128 21.6 15.9128 21.9 14.0128C22 13.4128 21.5 12.9128 20.9 12.9128H13Z" fill="black" />
                                                   <path opacity="0.3" d="M11 19.8129C11 20.4129 10.5 20.9129 9.89999 20.8129C5.49999 20.2129 2 16.5128 2 11.9128C2 7.31283 5.39999 3.51281 9.89999 3.01281C10.5 2.91281 11 3.41281 11 4.01281V19.8129Z" fill="black" />
                                                </svg>
                                             </span>
                                             <!--end::Svg Icon-->
                                          </span>
                                       </span>
                                       <span class="d-flex flex-column">
                                       <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Other Companies</span>
                                       <span class="fs-6 fw-bold text-muted">Product Based and similar Companies.</span>
                                       </span>
                                    </span>
                                    <span class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="radio" name="company_type" value="3" />
                                    </span>
                                 </label>
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
                              <div class="text-muted fw-bold fs-6">If you need more info, please check out
                                 <a href="#" class="link-primary fw-bolder">Help Page</a>.
                              </div>
                           </div>
                           <div class="fv-row mb-10">
                              <label class="form-label required">Website</label>
                              <input name="website" type="url" class="form-control form-control-lg form-control-solid" placeholder="https://example.com" value="" maxlength="255" />
                           </div>
                           <div class="fv-row mb-0">
                              <label class="fs-6 fw-bold form-label required">Contact Number</label>
                              <input name="contact_number" type="tel" inputmode="numeric" maxlength="15" class="form-control form-control-lg form-control-solid" placeholder="Enter contact number" value="" />
                           </div>								
                           <div class="fv-row mb-0 d-none">
                              <label class="form-label required">Specialties</label>
                              <input name="specialties" class="form-control form-control-lg form-control-solid" placeholder="Enter specialties" value="IT Services, Recruitment" id="kt_tagify_2" />
                           </div>
                        </div>
                     </div>
                     <!--end::Account Details-->
                     <!--begin::Subscription-->
                     <div class="" data-kt-stepper-element="content">
                        <!--begin::New Design-->
                        <div class="w-100">
                           <div class="row">
                              <div class="col-lg-6">
                                 <h2 class="fw-bolder text-dark required">Upgrade a Plan</h2>
                                 <div class="text-muted fw-bold fs-6">If you need more info, please check out
                                    <a href="javascript:void(0);" class="text-primary fw-bolder">Help Page</a>.
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div class="nav-group nav-group-outline mb-5" data-kt-buttons="true" style="float:right">
                                    <a href="javascript:void(0);" class="btn btn-color-gray-400 btn-active btn-active-secondary px-6 py-3 me-2 active" data-kt-plan="month">Monthly</a>
                                    <a href="javascript:void(0);" class="btn btn-color-gray-400 btn-active btn-active-secondary px-6 py-3" data-kt-plan="annual">Annual</a>
                                 </div>
                              </div>
                           </div>
                           <div class="d-flex flex-column">
                              <div class="nav-group nav-group-outline mx-auto mb-5 d-none" data-kt-buttons="true" style="float: right">
                                 <a href="javascript:void(0);" class="btn btn-color-gray-400 btn-active btn-active-secondary px-6 py-3 me-2 active" data-kt-plan="month">Monthly</a>
                                 <a href="javascript:void(0);" class="btn btn-color-gray-400 btn-active btn-active-secondary px-6 py-3" data-kt-plan="annual">Annual</a>
                              </div>                              
                              <div class="row fv-row mt-10">
                                 <div class="col-lg-6 mb-10 mb-lg-0">
                                    <div class="nav flex-column">
                                       <!--begin::Startup-->
                                       <div class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_startup">
                                          <div class="d-flex align-items-center me-2">
                                             <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                                                <input class="form-check-input" type="radio" name="upgrade_plan" value="startup" />
                                             </div>
                                             <div class="flex-grow-1">
                                                <h2 class="d-flex align-items-center fs-2 fw-bolder flex-wrap">Startup</h2>
                                                <div class="fw-bold opacity-50">Best for startups</div>
                                             </div>
                                          </div>
                                          <div class="ms-2">
                                             <span class="mb-2">$</span>
                                             <span class="fs-3x fw-bolder" data-kt-plan-price-month="39" data-kt-plan-price-annual="399">39</span>
                                             <span class="fs-7 opacity-50">/
                                             <span data-kt-element="period">Mon</span></span>
                                          </div>
                                       </div>
                                       <!--end::Startup-->
                                       <!--begin::Advanced-->
                                       <div class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_advanced">
                                          <div class="d-flex align-items-center me-2">
                                             <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                                                <input class="form-check-input" type="radio" name="upgrade_plan" value="advanced" />
                                             </div>
                                             <div class="flex-grow-1">
                                                <h2 class="d-flex align-items-center fs-2 fw-bolder flex-wrap">Advanced</h2>
                                                <div class="fw-bold opacity-50">Best for 100+ team</div>
                                             </div>
                                          </div>
                                          <div class="ms-2">
                                             <span class="mb-2">$</span>
                                             <span class="fs-3x fw-bolder" data-kt-plan-price-month="339" data-kt-plan-price-annual="3399">339</span>
                                             <span class="fs-7 opacity-50">/
                                             <span data-kt-element="period">Mon</span></span>
                                          </div>
                                       </div>
                                       <!--end::Advanced-->
                                       <!--begin::Enterprise-->
                                       <div class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_enterprise">
                                          <div class="d-flex align-items-center me-2">
                                             <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                                                <input class="form-check-input" type="radio" name="upgrade_plan" value="enterprise" />
                                             </div>
                                             <div class="flex-grow-1">
                                                <h2 class="d-flex align-items-center fs-2 fw-bolder flex-wrap">Enterprise
                                                <span class="badge badge-light-success ms-2 fs-7">Popular</span></h2>
                                                <div class="fw-bold opacity-50">Best for 1000+ team</div>
                                             </div>
                                          </div>
                                          <div class="ms-2">
                                             <span class="mb-2">$</span>
                                             <span class="fs-3x fw-bolder" data-kt-plan-price-month="999" data-kt-plan-price-annual="9999">999</span>
                                             <span class="fs-7 opacity-50">/
                                             <span data-kt-element="period">Mon</span></span>
                                          </div>
                                       </div>
                                       <!--end::Enterprise-->
                                       <!--begin::Custom-->
                                       <div class="nav-link btn btn-outline btn-outline-dashed btn-color-dark d-flex flex-stack text-start p-6">
                                          <div class="d-flex align-items-center me-2">
                                             <div class="form-check form-check-custom form-check-solid form-check-success me-6">
                                                <input class="form-check-input" type="radio" name="upgrade_plan" value="custom" />
                                             </div>
                                             <div class="flex-grow-1">
                                                <h2 class="d-flex align-items-center fs-2 fw-bolder flex-wrap">Custom</h2>
                                                <div class="fw-bold opacity-50">Requet a custom license</div>
                                             </div>
                                          </div>
                                          <div class="ms-5">
                                             <a href="#" class="btn btn-sm btn-primary">Contact Us</a>
                                          </div>
                                       </div>
                                       <!--end::Custom-->
                                    </div>
                                 </div>
                                 <div class="col-lg-6">
                                    <div class="tab-content rounded h-100 bg-light p-10">
                                       <!--begin::Startup Content-->
                                       <div class="tab-pane fade show active" id="kt_upgrade_plan_startup">
                                          <div class="pb-5">
                                             <h2 class="fw-bolder text-dark">What’s in Startup Plan?</h2>
                                             <div class="text-muted fw-bold">Optimal for 10+ team size and new startup</div>
                                          </div>
                                          <div class="pt-1">
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Up to 10 Active Users</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Up to 30 Project Integrations</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Analytics Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-muted flex-grow-1">Finance Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Error-circle.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-muted flex-grow-1">Accounting Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Error-circle.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-muted flex-grow-1">Network Platform</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Error-circle.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center">
                                                <span class="fw-bold fs-5 text-muted flex-grow-1">Unlimited Cloud Space</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Error-circle.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000" />
                                                   </svg>
                                                </span>
                                             </div>
                                          </div>
                                       </div>
                                       <!--end::Startup Content-->
                                       <!--begin::Advanced Content-->
                                       <div class="tab-pane fade" id="kt_upgrade_plan_advanced">
                                          <div class="pb-5">
                                             <h2 class="fw-bolder text-dark">What’s in Advanced Plan?</h2>
                                             <div class="text-muted fw-bold">Optimal for 100+ team size and grown company</div>
                                          </div>
                                          <div class="pt-1">
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Up to 10 Active Users</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Up to 30 Project Integrations</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Analytics Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Finance Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Accounting Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-muted flex-grow-1">Network Platform</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Error-circle.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center">
                                                <span class="fw-bold fs-5 text-muted flex-grow-1">Unlimited Cloud Space</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Error-circle.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M12.0355339,10.6213203 L14.863961,7.79289322 C15.2544853,7.40236893 15.8876503,7.40236893 16.2781746,7.79289322 C16.6686989,8.18341751 16.6686989,8.81658249 16.2781746,9.20710678 L13.4497475,12.0355339 L16.2781746,14.863961 C16.6686989,15.2544853 16.6686989,15.8876503 16.2781746,16.2781746 C15.8876503,16.6686989 15.2544853,16.6686989 14.863961,16.2781746 L12.0355339,13.4497475 L9.20710678,16.2781746 C8.81658249,16.6686989 8.18341751,16.6686989 7.79289322,16.2781746 C7.40236893,15.8876503 7.40236893,15.2544853 7.79289322,14.863961 L10.6213203,12.0355339 L7.79289322,9.20710678 C7.40236893,8.81658249 7.40236893,8.18341751 7.79289322,7.79289322 C8.18341751,7.40236893 8.81658249,7.40236893 9.20710678,7.79289322 L12.0355339,10.6213203 Z" fill="#000000" />
                                                   </svg>
                                                </span>
                                             </div>
                                          </div>
                                       </div>
                                       <!--end::Advanced Content-->
                                       <!--begin::Enterprise Content-->
                                       <div class="tab-pane fade" id="kt_upgrade_plan_enterprise">
                                          <div class="pb-5">
                                             <h2 class="fw-bolder text-dark">What’s in Enterprise Plan?</h2>
                                             <div class="text-muted fw-bold">Optimal for 1000+ team and enterpise</div>
                                          </div>
                                          <div class="pt-1">
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Up to 10 Active Users</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Up to 30 Project Integrations</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Analytics Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Finance Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Accounting Module</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center mb-7">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Network Platform</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                             <div class="d-flex align-items-center">
                                                <span class="fw-bold fs-5 text-gray-700 flex-grow-1">Unlimited Cloud Space</span>
                                                <!--begin::Svg Icon | path: icons/duotone/Code/Done-circle.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-success">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                      <path d="M16.7689447,7.81768175 C17.1457787,7.41393107 17.7785676,7.39211077 18.1823183,7.76894473 C18.5860689,8.1457787 18.6078892,8.77856757 18.2310553,9.18231825 L11.2310553,16.6823183 C10.8654446,17.0740439 10.2560456,17.107974 9.84920863,16.7592566 L6.34920863,13.7592566 C5.92988278,13.3998345 5.88132125,12.7685345 6.2407434,12.3492086 C6.60016555,11.9298828 7.23146553,11.8813212 7.65079137,12.2407434 L10.4229928,14.616916 L16.7689447,7.81768175 Z" fill="#000000" fill-rule="nonzero" />
                                                   </svg>
                                                </span>
                                             </div>
                                          </div>
                                       </div>
                                       <!--end::Enterprise Content-->
                                    </div>
                                 </div>
                              </div>
                              <!--end::New Design-->

                              <!--begin::Previous Design-->
                              <div class="mb-0 fv-row d-none">
                                 <div class="pb-10 lg-pb-10">                                       
                                    <h2 class="fw-bolder text-dark">Choose a Plan</h2>
                                    <div class="text-muted fw-bold fs-6">If you need more info, please check out
                                       <a href="javascript:void(0);" class="text-primary fw-bolder">Help Page</a>.
                                    </div>
                                 </div>
                                 <label class="d-flex align-items-center form-label mb-5 required">Select Subscription</label>
                                 <div class="mb-0">
                                    <label class="d-flex flex-stack mb-5 cursor-pointer">
                                       <span class="d-flex align-items-center me-2">
                                          <span class="symbol symbol-50px me-6">
                                             <span class="symbol-label">
                                                <!--begin::Svg Icon-->
                                                <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <polygon points="0 0 24 0 24 24 0 24"/>
                                                         <path d="M12,4.25932872 C12.1488635,4.25921584 12.3000368,4.29247316 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 L12,4.25932872 Z" fill="#000000" opacity="0.3"/>
                                                         <path d="M12,4.25932872 L12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.277344,4.464261 11.6315987,4.25960807 12,4.25932872 Z" fill="#000000"/>
                                                      </g>
                                                   </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                             </span>
                                          </span>
                                          <span class="d-flex flex-column">
                                          <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Free</span>
                                          <span class="fs-6 fw-bold text-muted">One login with limited access.</span>
                                          </span>
                                       </span>
                                       <span class="form-check form-check-custom form-check-solid">
                                       <input class="form-check-input" type="radio" name="subscription" value="1" />
                                       </span>
                                    </label>
                                    <label class="d-flex flex-stack mb-5 cursor-pointer">
                                       <span class="d-flex align-items-center me-2">
                                          <span class="symbol symbol-50px me-6">
                                             <span class="symbol-label">
                                                <!--begin::Svg Icon-->
                                                <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <rect x="0" y="0" width="24" height="24"/>
                                                         <rect fill="#000000" x="2" y="5" width="19" height="4" rx="1"/>
                                                         <rect fill="#000000" opacity="0.3" x="2" y="11" width="19" height="10" rx="1"/>
                                                      </g>
                                                   </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                             </span>
                                          </span>
                                          <span class="d-flex flex-column">
                                          <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Pro</span>
                                          <span class="fs-6 fw-bold text-muted"></span>
                                          </span>
                                       </span>
                                       <span class="form-check form-check-custom form-check-solid">
                                       <input class="form-check-input" type="radio" name="subscription" value="2" />
                                       </span>
                                    </label>
                                    <label class="d-flex flex-stack mb-5 cursor-pointer">
                                       <span class="d-flex align-items-center me-2">
                                          <span class="symbol symbol-50px me-6">
                                             <span class="symbol-label">
                                                <!--begin::Svg Icon-->
                                                <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <rect x="0" y="0" width="24" height="24"/>
                                                         <path d="M3,4 L20,4 C20.5522847,4 21,4.44771525 21,5 L21,7 C21,7.55228475 20.5522847,8 20,8 L3,8 C2.44771525,8 2,7.55228475 2,7 L2,5 C2,4.44771525 2.44771525,4 3,4 Z M3,10 L13,10 C13.5522847,10 14,10.4477153 14,11 L14,19 C14,19.5522847 13.5522847,20 13,20 L3,20 C2.44771525,20 2,19.5522847 2,19 L2,11 C2,10.4477153 2.44771525,10 3,10 Z" fill="#000000"/>
                                                         <rect fill="#000000" opacity="0.3" x="16" y="10" width="5" height="10" rx="1"/>
                                                      </g>
                                                   </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                             </span>
                                          </span>
                                          <span class="d-flex flex-column">
                                          <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Plus</span>
                                          <span class="fs-6 fw-bold text-muted">Use images to enhance time travel rivers</span>
                                          </span>
                                       </span>
                                       <span class="form-check form-check-custom form-check-solid">
                                       <input class="form-check-input" type="radio" name="subscription" value="3" />
                                       </span>
                                    </label>
                                    <label class="d-flex flex-stack mb-0 cursor-pointer">
                                       <span class="d-flex align-items-center me-2">
                                          <span class="symbol symbol-50px me-6">
                                             <span class="symbol-label">
                                                <!--begin::Svg Icon-->
                                                <span class="svg-icon svg-icon-1 svg-icon-gray-600">
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                         <rect x="0" y="0" width="24" height="24"/>
                                                         <path d="M3,4 L20,4 C20.5522847,4 21,4.44771525 21,5 L21,7 C21,7.55228475 20.5522847,8 20,8 L3,8 C2.44771525,8 2,7.55228475 2,7 L2,5 C2,4.44771525 2.44771525,4 3,4 Z M10,10 L20,10 C20.5522847,10 21,10.4477153 21,11 L21,13 C21,13.5522847 20.5522847,14 20,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L20,16 C20.5522847,16 21,16.4477153 21,17 L21,19 C21,19.5522847 20.5522847,20 20,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000"/>
                                                         <rect fill="#000000" opacity="0.3" x="2" y="10" width="5" height="10" rx="1"/>
                                                      </g>
                                                   </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                             </span>
                                          </span>
                                          <span class="d-flex flex-column">
                                          <span class="fw-bolder text-gray-800 text-hover-primary fs-5">Premium</span>
                                          <span class="fs-6 fw-bold text-muted">Use images to enhance time travel rivers</span>
                                          </span>
                                       </span>
                                       <span class="form-check form-check-custom form-check-solid">
                                       <input class="form-check-input" type="radio" name="subscription" value="3" />
                                       </span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="d-flex flex-center flex-row-fluid pt-7">
                              <!-- <button type="button" class="btn btn-light me-3">Cancel</button> -->
                              <button type="button" class="btn btn-success">Upgrade Plan</button>
                           </div>
                           <!--end::Previous Design-->
                        </div>
                     </div>
                     <!--end::Subscription-->
                     <!--begin::Completed-->
                     <div class="" data-kt-stepper-element="content">
                        <div class="w-100">
                           <div class="pb-8 pb-lg-10">
                              <h2 class="fw-bolder text-dark">You Are Done!</h2>
                              <div class="text-muted fw-bold fs-6">If you need more info, please
                                 <a href="{{ route('login') }}" class="link-primary fw-bolder">Sign In</a>.
                              </div>
                           </div>
                           <div class="mb-0">
                              <div class="fs-6 text-gray-600 mb-5">Writing headlines for blog posts is as much an art as it is a science and probably warrants its own post, but for all advise is with what works for your great &amp; amazing audience.</div>
                              <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                 <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                       <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" />
                                       <rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" />
                                       <rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" />
                                    </svg>
                                 </span>
                                 <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-bold">
                                       <h4 class="text-gray-900 fw-bolder">We need your attention!</h4>
                                       <div class="fs-6 text-gray-700">To start using great tools, please, please
                                          <a href="#" class="fw-bolder">Create Team Platform</a>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <!--end::Completed-->
                     <!--begin::Actions-->
                     <div class="d-flex flex-stack pt-5">
                        <div class="mr-2">
                           <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-4 me-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1" fill="black" />
                                    <path d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z" fill="black" />
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->Previous
                           </button>
                        </div>
                        <div>                           
                           <!-- <button type="button" class="btn btn-success">Upgrade Plan</button> -->
                        </div>
                        <div>
                           <a href="{{ route('login') }}" type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="submit">
                              <span class="indicator-label">
                                 Submit
                                 <!--begin::Svg Icon-->
                                 <span class="svg-icon svg-icon-4 ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                       <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
                                       <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
                                    </svg>
                                 </span>
                                 <!--end::Svg Icon-->
                              </span>
                              <span class="indicator-progress">Please wait...
                              <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                           </a>
                           <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next">
                              Continue
                              <!--begin::Svg Icon-->
                              <span class="svg-icon svg-icon-4 ms-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
                                    <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
                                 </svg>
                              </span>
                              <!--end::Svg Icon-->
                           </button>
                        </div>
                     </div>
                     <!--end::Actions-->
                     {{-- Cloudflare Turnstile — Sprint 2 task 2.4. Widget auto-injects cf-turnstile-response into form on solve. --}}
                     @if(config('services.turnstile.site_key'))
                     <div class="cf-turnstile mt-3 d-flex justify-content-center"
                          data-sitekey="{{ config('services.turnstile.site_key') }}"
                          data-theme="light"
                          data-appearance="interaction-only">
                     </div>
                     @endif
                     @csrf
                     {{-- Honeypot: invisible to humans; bots fill it. Server silently 200s on non-empty. (Sprint 2 task 2.5) --}}
                     <input type="text" name="website_url" id="website_url_hp" value=""
                            style="display:none;position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">
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
                  <a href="javascript:void(0);" class="text-muted text-hover-primary px-2" target="_blank">About</a>
                  <a href="javascript:void(0);" class="text-muted text-hover-primary px-2" target="_blank">Support</a>
                  <a href="javascript:void(0);" class="text-muted text-hover-primary px-2" target="_blank">Purchase</a>
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
<!--begin::Javascript-->
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('demo/dist/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('demo/dist/assets/js/scripts.bundle.js') }}"></script>
<!--end::Global Javascript Bundle-->
@if(config('services.turnstile.site_key'))
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif

<!--begin::Tagify-->
<!-- <script src="{{ asset('demo/dist/assets/js/custom/documentation/forms/tagify.js') }}"></script> -->
<!-- <script src="assets/js/custom/documentation/forms/tagify.js"></script> -->
<!--end::Tagify-->

<!--begin::Upgrade plan-->
<!-- <script src="{{ asset('demo/js/custom/modals/create-account.js') }}"></script> -->
<!-- <script src="{{ asset('demo/js/custom/modals/upgrade-plan.js') }}"></script> -->
<!--end::Upgrade plan-->

<!--begin::On click change-->
<script>
   $(document).ready(function() {
   "use strict";
      var KTModalUpgradePlan = (function () {
         var t,
            n,
            a,
            e = function (n) {
                  [].slice.call(t.querySelectorAll("[data-kt-plan-price-month]")).map(function (t) {
                     var a = t.getAttribute("data-kt-plan-price-month"),
                        e = t.getAttribute("data-kt-plan-price-annual");
                     "month" === n ? (t.innerHTML = a) : "annual" === n && (t.innerHTML = e);
                  });
            };
         return {
            init: function () {
                  (t = document.querySelector("#upgrade-plan")) &&
                     ((n = t.querySelector('[data-kt-plan="month"]')),
                        (a = t.querySelector('[data-kt-plan="annual"]')),
                        n.addEventListener("click", function (t) {
                              e("month");
                        }),
                        a.addEventListener("click", function (t) {
                              e("annual");
                        }),
                        KTUtil.on(t, '[data-bs-toggle="tab"]', "click", function (t) {
                              this.querySelector('[type="radio"]').checked = !0;
                        }));
            },
         };
      })();
      KTUtil.onDOMContentLoaded(function () {
         KTModalUpgradePlan.init();
      });
   });
</script>
<!--end::On click change-->
      
<!--begin::Validaation-->
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
                     (i = document.querySelector("#kt_create_account_stepper")) &&
                     ((o = i.querySelector("#kt_create_account_form")),
                        (r = i.querySelector('[data-kt-stepper-action="submit"]')),
                        (s = new KTStepper(i)).on("kt.stepper.next", function (e) {
                              console.log("stepper.next");
                              var t = a[e.getCurrentStepIndex() - 1];
                              t
                                 ? t.validate().then(function (t) {
                                    console.log("validated!"),
                                          "Valid" == t
                                             ? (e.goNext(), KTUtil.scrollTop())
                                             : Swal.fire({
                                                text: "Sorry, looks like there are some errors detected, please try again.",
                                                icon: "error",
                                                buttonsStyling: !1,
                                                confirmButtonText: "Ok, got it!",
                                                customClass: { confirmButton: "btn btn-light" },
                                             }).then(function () {
                                                KTUtil.scrollTop();
                                             });
                                 })
                                 : (e.goNext(), KTUtil.scrollTop());
                        }),
                        s.on("kt.stepper.previous", function (e) {
                              console.log("stepper.previous"), e.goPrevious(), KTUtil.scrollTop();
                        }),
                     
                        a.push(
                              FormValidation.formValidation(o, {
                                 fields:
                                 {
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
                                 },
                                 stringLength: 
                                 {
                                    min: 8,
                                    max: 25,
                                    message: 'Use 8 or more characters with a mix of letters, numbers & symbols.'
                                 }
         
                              }
                           },	
                           confirm_password:
                           {
                                 validators:
                              {
                                 notEmpty:
                                 {
                                    message: "Please enter confirm password"
                                 },
                                 stringLength: 
                                 {
                                    min: 8,
                                    max: 25,
                                    message: 'The password and its confirm are not the same'
                                 }												
                              }
                              },
                                 },
                                 plugins: {
                                    trigger: new FormValidation.plugins.Trigger(),
                                    bootstrap: new FormValidation.plugins.Bootstrap5({
                                          rowSelector: '.fv-row',
                                          eleInvalidClass: '',
                                          eleValidClass: ''
                                    })
                                 }
                              })
                        ),
                        a.push(
                              FormValidation.formValidation(o, {
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
                                    team_size: 
                                    { 
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Team size is required" 
                                             } 
                                          } 
                                    },
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
                                 },
                                 plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                              })
                        ),
                        a.push(
                              FormValidation.formValidation(o, {
                                 fields:
                                 {
                                    upgrade_plan: 
                                    {
                                          validators: 
                                          { 
                                             notEmpty: 
                                             { 
                                                message: "Plan is required" 
                                             } 
                                          } 
                                    }
                                 },
                                 plugins: { trigger: new FormValidation.plugins.Trigger(), bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: ".fv-row", eleInvalidClass: "", eleValidClass: "" }) },
                              })
                        ),
                        r.addEventListener("click", function (t) {
                              t.preventDefault(),
                                 (r.disabled = !0),
                                 r.setAttribute("data-kt-indicator", "on"),
                                 setTimeout(function () {
                                          
      
                     $.ajax({
                     url: "{{route('company-register')}}",
                     container: '#kt_create_account_form',
                     type: "POST",
                     data: $('#kt_create_account_form').serialize(),
                     success: function(response) {
                        $('#kt_create_account_form').trigger("reset");
                        // swal("Sent!", "Your account has been created successfully.\n Check your email to activate your account.", "success");
                     }
                     // ,
                     // error: function (response) {
                     //     swal("Error!", "Your need to fill all the form fields!", "error");
                     // }
                  }),
      
                                          
                  r.removeAttribute("data-kt-indicator"),
                     (r.disabled = !1),
                     Swal.fire(
                        {
                           text: "Form has been successfully submitted!",
                           icon: "success",
                           buttonsStyling: !1,
                           confirmButtonText: "Ok, got it!",
                           customClass:
                           {
                              confirmButton: "btn btn-primary"
                           }
                        }).then(function (t)
                        {
                           t.isConfirmed && e && e.hide();
                        });

                     }, 2e3);
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
