<!DOCTYPE html>
<html lang="en">
<head>
    <title>Career Website - Homepage</title>
    <link rel="icon" type="image/png" href="{{ asset('demo/career_website/images/favicon.png') }}">
    <meta charset="UTF-8">
    <meta property="og:type" content="website" />
    <meta property="og:image:width"content="600" />
    <meta property="og:image:height" content="600" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta itemprop="name" content="{{ !empty($metaTitle) ? $metaTitle : '' }}">
    <meta property="og:url" content="{{ !empty($pageUrl) ? $pageUrl : '' }}" />
    <meta itemprop="image" content="{{ !empty($metaImage) ? $metaImage : '' }}">
    <meta property="og:image" content="{{ !empty($metaImage) ? $metaImage : '' }}" />
    <meta property="og:title" content="{{ !empty($metaTitle) ? $metaTitle : '' }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ !empty($metaDescription) ? $metaDescription : '' }}">
    <meta itemprop="description" content="{{ !empty($metaDescription) ? $metaDescription : '' }}">
    <meta property="og:description" content="{{ !empty($metaDescription) ? $metaDescription : '' }}" />
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/fonts/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/metismenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/odometer.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/meanmenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/responsive.css') }}">

    <!--begin::Form validation css-->
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/swal.css') }}">
    <!--end::Form validation css-->

</head>

<body>
    <!--begin::Preloader-->
    <div class="preloader">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
    <!--end::Preloader-->

    <!--begin::Navbar-->
    <div class="navbar-area">
        <div class="mobile-responsive-nav">
            <div class="container-fluid">
                <div class="mobile-responsive-menu">
                    <div class="logo">                        
                        <!-- {{ !module_enabled('Subdomain') ? route('jobs.jobOpenings') : route('jobs.jobOpenings',$company->career_page_link) }} -->
                        <a href="<?php echo url('careers/client22021') ?>">
                            <img src="{{ asset('demo/career_website/images/logo.png') }}" alt="logo">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="desktop-nav desktop-nav-one nav-area">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-md navbar-light">
                    <!-- {{ !module_enabled('Subdomain') ? route('jobs.jobOpenings') : route('jobs.jobOpenings',$company->career_page_link) }} -->
                    <a class="navbar-brand" href="<?php echo url('careers/client22021') ?>">
                        <img src="{{ asset('demo/career_website/images/logo.png') }}" alt="Logo">
                    </a>
                    <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                        <ul class="navbar-nav d-none">
                            <li class="nav-item">
                                <a href="javascript:void(0);" class="nav-link active">
                                    Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Jobs
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">Job Listing</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">Post A Job</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">Job Details</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Employers
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Employers Listing
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Employers Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Employers Dashboard
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Candidates
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Candidates Listing
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Candidates Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Candidates Dashboard
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Blog
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Blog Grid
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Blog Left Sidebar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Blog Right Sidebar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Blog Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Categories
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Tags
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Pages
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            About Us
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Contact Us
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Testimonials
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            Freelancer
                                            <i class="ri-arrow-down-s-line"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class="nav-item">
                                                <a href="javascript:void(0);" class="nav-link">
                                                    Freelancer
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);" class="nav-link">
                                                    Freelancer Details
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Invoice
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            Company
                                            <i class="ri-arrow-down-s-line"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class="nav-item">
                                                <a href="javascript:void(0);" class="nav-link">
                                                    Company Listing
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);" class="nav-link">
                                                    Company Details
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0);" class="nav-link">
                                                    Search Job
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Pricing Plan
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            FAQ
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Log In / Register
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Forgot Password
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Terms & Conditions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Privacy Policy
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            404 Page
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="javascript:void(0);" class="nav-link">
                                            Coming Soon
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <div class="others-options d-flex align-items-center">
                            <div class="optional-item d-none">
                                <a href="javascript:void(0);" class="default-btn border-radius-5">Login / Register <i class="ri-user-3-line"></i></a>
                            </div>
                            <div class="optional-item d-none">
                                <a href="javascript:void(0);" class="default-btn two border-radius-5">Login / Register <i class="ri-user-3-line"></i></a>
                            </div>
                            <div class="optional-item d-none">
                                <a href="javascript:void(0);" class="default-btn border-radius-5">Post A Job <i class="ri-briefcase-2-line"></i></a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="side-nav-responsive d-none">
            <div class="container-max">
                <div class="dot-menu">
                    <div class="circle-inner">
                        <div class="circle circle-one"></div>
                        <div class="circle circle-two"></div>
                        <div class="circle circle-three"></div>
                    </div>
                </div>
                <div class="container">
                    <div class="side-nav-inner">
                        <div class="side-nav justify-content-center align-items-center">
                            <div class="side-nav-item">
                                <a href="javascript:void(0);" class="default-btn two">
                                    Login / Register <i class="ri-user-3-line"></i>
                                </a>
                            </div>
                            <div class="side-nav-item">
                                <a href="javascript:void(0);" class="default-btn">
                                    Post A Job <i class="ri-briefcase-2-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Navbar-->

    <!-- Main container -->
    <main class="main-content">
        @yield('content')
    </main>
    <!-- END Main container -->
    
    <!--begin::Apply_job - Popup-->
    <div class="modal fade show" id="apply_job" tabindex="-1" role="dialog" aria-labelledby="">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form id="applicantForm" novalidate="true">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Applicant Form</h5>
                        <a href="javascript:void(0);" class="text-dark" data-dismiss="modal">
                            <span aria-hidden="true">X</span>
                        </a>
                    </div>
                    <div class="modal-body pb-0">
                        <div class="post-job-form shadow-none py-0 px-0 mb-0">
                            <div class="contact-form">
                                <h3 class="d-none">Applicant Information</h3>
                                    <div class="row">
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0 choose-img">
                                                <label for="upload_cv" class="font-weight-500 required">Upload CV</label>
                                                <!-- accept="document/*" -->
                                                <input type="file" id="resume" name="upload_cv" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,text/plain, application/pdf, image/*">
                                                <small class="d-block">Maximum file size: 1 MB</small>
                                                <!-- <p class="mt-1">Maximum file size: 1 MB</p> -->
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="full_name" class="font-weight-500 required">Full Name</label>
                                                <input type="text" class="form-control" name="full_name" placeholder="Enter Full Name">
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="contact_number" class="font-weight-500 required">Contact Number</label>
                                                <input type="number" class="form-control" name="contact_number" placeholder="Enter Contact Number">
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="email" class="font-weight-500 required">Email</label>
                                                <input type="email" class="form-control" name="email" placeholder="Enter Email">
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="designation" class="font-weight-500 required">Designation</label>
                                                <input type="text" class="form-control" name="designation" placeholder="Enter Designation">
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="experience" class="font-weight-500 required">Experience</label>
                                                <input type="number" class="form-control" name="experience" placeholder="Enter Experience">
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="current_ctc" class="font-weight-500 required">Current CTC</label>
                                                <input type="number" class="form-control" name="current_ctc" placeholder="Enter Current CTC">
                                            </div>
                                        </div>
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0">
                                                <label for="notice_period" class="font-weight-500 required">Notice Period</label>
                                                <!-- w-100 p-2 rounded bg-light border form-control-custom-new is-invalid -->
                                                <select class="form-control" name="notice_period">
                                                    <option value="">Select Notice Period</option>
                                                    <option value="1">Immediate</option>
                                                    <option value="2">30 Days</option>
                                                    <option value="3">15 Days</option>
                                                    <option value="4">7 Days</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer others-options d-flex align-items-center justify-content-center">      
                        <div class="others-options d-flex align-items-center">
                            <div class="optional-item">
                                <button type="button" class="default-btn two border-radius-5 btn btn-sm" data-dismiss="modal">Cancel</button>
                            </div>
                            <div class="optional-item">
                                <button id="submit-form" type="button" class="default-btn border-radius-5 btn btn-sm">Submit</button>
                            </div>
                        </div>                                     
                    </div>
                </div>
            </form>    
        </div>
    </div>
    <!--end::Apply_job - Popup-->

    <!--begin::Form validation-->
    
    <!--begin::Global Javascript Bundle-->
    <script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('demo/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->

    <script src="{{ asset('demo/career_website/js/apply_validation.js') }}"></script>
    <!--end::Form validation-->

    <!--begin::Let Employers Find You-->
    <div class="employers-cv-area">
        <div class="container">
            <div class="employers-cv-bg">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="employers-cv-content">
                            <h2>Let Employers Find You</h2>
                            <div class="bar"></div>
                            <p>Advertise your jobs to millions of monthly users and search 15.8 million CV in our database.</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="employers-cv-btn">
                            <a href="mailto:contact@zoben.com">Upload Your CV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Let Employers Find You-->

    <!--begin::Footer Section-->
    <footer class="footer-area">
        <div class="container">
            <div class="footer-top pt-50 pb-0">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-md-6">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <a href="javascript:void(0);">
                                    <img src="{{ asset('demo/career_website/images/logo.png') }}" alt="Footer Logo">
                                </a>
                            </div>
                            <ul class="footer-contact-list">
                                <li>
                                    <i class="ri-map-pin-line"></i>
                                    <div class="content">
                                        <a href="javascript:void(0);" target="_blank">
                                            4Bell Technology, F-314, 1st Floor, Sector-63, Noida, <span class="text-uppercase">india</span>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <i class="ri-mail-line"></i>
                                    <div class="content">
                                        <a href="mailto:support@meecruit.com">
                                            support@meecruit.com
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <i class="ri-phone-line"></i>
                                    <div class="content">
                                        <a href="tel:+919999999999">
                                            +91 9999999999
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-md-6">
                        <div class="footer-widget">
                            <h3>For Employer</h3>
                            <ul class="footer-list">
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Browse Candidates
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Employer Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Job Packages
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Jobs Featured
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Post A Job
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-md-4">
                        <div class="footer-widget ps-5">
                            <h3>For Candidates</h3>
                            <ul class="footer-list">
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Browse Jobs
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Submit Resume
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Candidates Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Browse Categories
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Candidate Listing
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-md-4">
                        <div class="footer-widget">
                            <h3>Company</h3>
                            <ul class="footer-list">
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        About Us
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Contact Us
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Terms &amp; Conditions
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Privacy Policy
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Candidate Listing
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-md-4">
                        <div class="footer-widget">
                            <h3>Quick Links</h3>
                            <ul class="footer-list">
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Jobs Listing
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Employer Listing
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Post New Job
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        All Employers
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" target="_blank">
                                        Featured Jobs
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-7">
                        <div class="copy-right-text">
                            <p>                                
                                &copy; {{ \Carbon\Carbon::today()->year }} <a href="javascript:void(0);" target="_blank">Company name</a> | All Rights Reserved @lang('app.by')
                                <a href="javascript:void(0);" target="_blank">Meecruit</a>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-5">
                        <div class="copy-right-social-link">
                            <ul class="social-link">
                                <li>
                                    <a href="https://www.facebook.com/" target="_blank">
                                        <i class="ri-facebook-fill"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/" target="_blank">
                                        <i class="ri-instagram-fill"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/" target="_blank">
                                        <i class="ri-twitter-fill"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/" target="_blank">
                                        <i class="ri-linkedin-fill"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--end::Footer Section-->

    <!--begin::Script section-->
    <script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="{{ asset('demo/career_website/js/jquery.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/odometer.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/meanmenu.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/dropzone.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/sticky-sidebar.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/tweenMax.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/wow.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/form-validator.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/contact-form-script.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/custom.js') }}"></script>
    <!--end::Script section-->

    <!--begin::Scripts -->
    <script src="{{ asset('front/assets/js/core.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('front/assets/js/thesaas.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('front/assets/js/script.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}<?php echo TM_VERSION; ?>"></script>
    @stack('footer-script')
    <!--end::Scripts-->

</body>
</html>