<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="{{ asset('demo/career_website/css/swal.css') }}">

   <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <!-- <link href="https://bot.4bell.com/demo/plugins/global/plugins.bundle.css?ver=1553620716" rel="stylesheet" type="text/css" /> -->
    <!-- <link href="https://bot.4bell.com/demo/css/style.bundle.css?ver=1553620716" rel="stylesheet" type="text/css" /> -->
    <!--end::Global Stylesheets Bundle-->

    <title>Career Website - Job Detail</title>
    <link rel="icon" type="image/png" href="{{ asset('demo/career_website/images/favicon.png') }}">

    <style>
    .employers-cv-area::before
    {
        background-color: #fff;
    }
    .find-jobs-card .find-jobs-btn
    {
        position: absolute;
        top: auto;
        right: 30px;
        -webkit-transition: var(--transition);
        transition: var(--transition);
    }
    .post-job-form .contact-form
    {
        height: 500px;
        overflow-y: scroll;
        overflow-x: hidden;
    }
    .contact-form .form-group
    {
        padding-right: 15px;
    }
    .required:after
    {
        content: "*";
        position: relative;
        font-size: inherit;
        color: #f1416c;
        padding-left: 0.25rem;
        font-weight: 700;
    }
    </style>

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
                        <a href="javascript:void(0);">
                            <img src="{{ asset('demo/career_website/images/logo.png') }}" alt="logo">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="desktop-nav desktop-nav-one nav-area">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-md navbar-light ">
                    <a class="navbar-brand" href="javascript:void(0);">
                        <img src="{{ asset('demo/career_website/images/logo.png') }}" alt="Logo">
                    </a>
                    <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                        <ul class="navbar-nav invisible">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Home
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="index.html" class="nav-link">
                                            Home One
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="index-2.html" class="nav-link">
                                            Home Two
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="index-3.html" class="nav-link">
                                            Home Three
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link active">
                                    Jobs
                                    <i class="ri-arrow-down-s-line"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item">
                                        <a href="job-listing.html" class="nav-link">Job Listing</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="post-job.html" class="nav-link">Post A Job</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="job-details.html" class="nav-link active">Job Details</a>
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
                                        <a href="employers-listing.html" class="nav-link">
                                            Employers Listing
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="employers-details.html" class="nav-link">
                                            Employers Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard.html" class="nav-link">
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
                                        <a href="candidates-listing.html" class="nav-link">
                                            Candidates Listing
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="candidates-details.html" class="nav-link">
                                            Candidates Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="candidates-dashboard.html" class="nav-link">
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
                                        <a href="blog-1.html" class="nav-link">
                                            Blog Grid
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="blog-2.html" class="nav-link">
                                            Blog Left Sidebar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="blog-3.html" class="nav-link">
                                            Blog Right Sidebar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="blog-details.html" class="nav-link">
                                            Blog Details
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="categories.html" class="nav-link">
                                            Categories
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tags.html" class="nav-link">
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
                                        <a href="about.html" class="nav-link">
                                            About Us
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="contact.html" class="nav-link">
                                            Contact Us
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="testimonials.html" class="nav-link">
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
                                                <a href="freelancer.html" class="nav-link">
                                                    Freelancer
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="freelancer-details.html" class="nav-link">
                                                    Freelancer Details
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="dashboard-invoice.html" class="nav-link">
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
                                                <a href="company.html" class="nav-link">
                                                    Company Listing
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="company-details.html" class="nav-link">
                                                    Company Details
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="search-job.html" class="nav-link">
                                                    Search Job
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="pricing.html" class="nav-link">
                                            Pricing Plan
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="faq.html" class="nav-link">
                                            FAQ
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="log-in-register.html" class="nav-link">
                                            Log In / Register
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="forgot-password.html" class="nav-link">
                                            Forgot Password
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="terms-condition.html" class="nav-link">
                                            Terms & Conditions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="privacy-policy.html" class="nav-link">
                                            Privacy Policy
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="404.html" class="nav-link">
                                            404 Page
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="coming-soon.html" class="nav-link">
                                            Coming Soon
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <div class="others-options d-flex align-items-center">
                            <div class="optional-item d-none">
                                <a href="post-job.html" class="default-btn border-radius-5">Login / Register <i class="ri-user-3-line"></i></a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="side-nav-responsive">
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
                        <div class="side-nav justify-content-center align-items-center d-none">
                            <div class="side-nav-item d-none">
                                <a href="post-job.html" class="default-btn">
                                    Login / Register <i class="ri-user-3-line"></i>
                                </a>
                            </div>
                            <div class="side-nav-item d-none">
                                <a href="log-in-register.html" class="default-btn two">
                                    Login / Register <i class="ri-user-3-line"></i>
                                </a>
                            </div>
                            <div class="side-nav-item d-none">
                                <a href="post-job.html" class="default-btn">
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

    <!--begin::Banner-->
    <div class="inner-banner">
        <div class="container">
            <div class="inner-title text-center">
                <h3>Category Name</h3>
                <ul>
                    <li>
                        <a href="<?php echo url('careers/client22021/job-listing')?>">Job Listing</a>
                    </li>
                    <li>Requisition Name</li>
                </ul>
            </div>
        </div>
    </div>
    <!--begin::Banner-->

    <!--begin::Job Description Content-->
    <div class="job-details-area pt-50 pb-30">
        <div class="container">
            <div class="row">                
                <!--begin::Professional Copywriter for Advertising-->
                <div class="col-lg-12">
                    <div class="find-jobs-card d-flex align-items-center">
                        <div class="find-jobs-img">
                            <a href="javascript:void(0);">
                                <img src="{{ asset('demo/career_website/images/find-jobs/find-jobs1.jpg') }}" alt="img1">
                            </a>
                        </div>
                        <div class="content">
                            <!--begin::Buttons-->
                            <ul class="content-list d-none">
                                <li class="time">Full Time</li>
                                <li class="freelance">Freelance</li>
                                <li class="internship">Internship</li>
                            </ul>
                            <!--end::Buttons-->
                            <h3><a href="<?php echo url('careers/client22021/job-details')?>">Professional Copywriter for Advertising</a></h3>
                            <ul class="content-list2">
                                <li class="list-two"><i class="ri-building-4-fill"></i>Ontech Group</li>
                                <li class="list-two"><i class="ri-user-2-fill"></i>R-1001</li>
                                <li class="list-two"><i class="ri-map-pin-2-fill"></i>Gurgaon, <span class="text-uppercase">india</span> </li>
                                <li class="list-two"><i class="ri-time-line"></i>Posted Today</li>
                            </ul>
                            <ul class="content-list3">
                                <li><i class="ri-briefcase-line"></i>Experience : <b>8-9 Years</b></li>
                                <li><i class="ri-money-euro-circle-line"></i>Salary : <b>30-35 LPA</b></li>
                                <li><i class="ri-sensor-fill"></i>Openings : <b>832</b></li>
                                <li><i class="ri-slack-fill"></i>Department : <b>IT</b></li>
                            </ul>
                        </div>
                        <div class="find-jobs-btn">
                            <a href="javascript:void(0);" class="default-btn border-radius-5" data-toggle="modal" data-target="#apply_now_popup">Apply Now<i class="ri-send-plane-2-line"></i></a>
                        </div>
                    </div>
                </div>
                <!--end::Professional Copywriter for Advertising-->

                <!--begin::Sidebar-->
                <div class="col-lg-4">
                    <div class="job-details-widget-side pr-20">
                        <!--begin::Job Overview-->
                        <div class="job-details-widget">
                            <h3 class="title">Job Overview</h3>
                            <div class="bar"></div>
                            <ul class="job-details-category">
                                <li>Published On: <span>Vacancy</span></li>
                                <li>Vacancy: <span>01</span></li>
                                <li>Job Type: <span>Part Time</span></li>
                                <li>Experience: <span>3 Year(s)</span></li>
                                <li>Job Location: <span>New York, USA</span></li>
                                <li>Category: <span>Finance</span></li>
                                <li>Gender: <span>Both</span></li>
                                <li>Application Due: <span>05.05.2021</span></li>
                            </ul>
                            <div class="job-details-social">
                                <span>Share Post:</span>
                                <ul class="social-icon">
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
                        <!--end::Job Overview-->
                        <!--begin::Job Skills-->
                        <div class="job-details-widget">
                            <h3 class="title">Job Skills</h3>
                            <div class="bar"></div>
                            <ul class="job-details-widget-tag">
                                <li><a href="tags.html" target="_blank">Prototyping</a></li>
                                <li><a href="tags.html" target="_blank">New Layout</a></li>
                                <li><a href="tags.html" target="_blank">Agile</a></li>
                                <li><a href="tags.html" target="_blank">Resumes</a></li>
                                <li><a href="tags.html" target="_blank">Skills</a></li>
                                <li><a href="tags.html" target="_blank">Career</a></li>
                            </ul>
                        </div>
                        <!--end::Job Skills-->
                        <!--begin::Company Overview-->
                        <div class="job-details-widget">
                            <h3 class="title">Company Overview</h3>
                            <div class="bar"></div>
                            <ul class="job-details-category">
                                <li>Categories: <span>Application</span></li>
                                <li>Established: <span>1986</span></li>
                                <li>Employees: <span>3,475</span></li>
                                <li>Location: <span>Noida</span></li>
                                <li>Phone Number: <span>+91 9999999999</span></li>
                                <li>Email: <span>info@milyrecruit.com</span></li>
                            </ul>
                            <div class="job-details-social">
                                <span>Socials:</span>
                                <ul class="social-icon">
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
                            <div class="job-details-web-btn">
                                <a href="javascript:void(0);" target="_blank">www.4belltechnology.com</a>
                            </div>
                        </div>
                        <!--end::Company Overview-->
                    </div>
                </div>
                <!--end::Sidebar-->

                <!--begin::Content right sidebar-->
                <div class="col-lg-8">
                    <div class="job-details-content">
                        <div class="content mb-30">
                            <h3>Job Description</h3>
                            <div class="bar"></div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat enim a erat sit vulputate elementum
                                orci. Risus nec viverra ornare venenatis proin ac varius tristique ut. Vitae egestas tellus amet nulla
                                cursus.ands Pellentesque placerat maecenas egestas ullamcorper sed nunc. Vitae egestas tellus amet nulla
                                something loss Pellentesque placerat maecenas egestas ullamcorper sed nunc.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat enim a erat sit vulputate elementum
                                orci. Risus nec viverra ornare venenatis proin ac varius tristique ut. Vitae egestas tellus amet nulla
                                cursus in that. Pellentesque placerat maecenas egestas ullamcorper sed sarinto.</p>
                        </div>
                        <div class="job-details-list">
                            <h3>Key Responsibilities</h3>
                            <div class="bar"></div>
                            <ul>
                                <li>Be involved in every step of the product design cycle from discovery to developer handoff and user
                                    acceptance testing.</li>
                                <li>Work with BAs, product managers and tech teams to lead the Product Design</li>
                                <li>Style guides, high fidelity prototypes, micro interaction specifications and pattern libraries.</li>
                                <li>Maintain quality of the design process and ensure that when designs are translated into code they accurately
                                    reflect the design specifications</li>
                                <li>Present your work to the wider business at Show &amp; Tell sessions.</li>
                                <li>Consetetur sadipscing elitr, sed diam nonumy eirmod tempor</li>
                            </ul>
                        </div>
                        <div class="job-details-list">
                            <h3>Skill &amp; Experience</h3>
                            <div class="bar"></div>
                            <ul>
                                <li>You have at least 3 years’ experience working as a Product Designer.</li>
                                <li>You have experience using Sketch and InVision or Framer</li>
                                <li>You have some previous experience working in an agile environment – Think two-week sprints.</li>
                                <li>You are familiar using Jira and Confluence in your workflow</li>
                                <li>Present your work to the wider business at Show &amp; Tell sessions.</li>
                                <li>Consetetur sadipscing elitr, sed diam nonumy eirmod tempor</li>
                            </ul>
                        </div>
                        <div class="article-social-icon">
                            <ul class="social-icon">
                                <li class="title">Share Post</li>
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
                <!--end::Content right sidebar-->
            </div>
        </div>
    </div>
    <!--end::Job Description Content-->

    <!--begin::Apply for job - Popup-->
    <div class="modal fade show" id="apply_now_popup" tabindex="-1" role="dialog" aria-labelledby="">
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
                                <div class="bar d-none"></div>
                                    <div class="row">
                                        <div class="fv-row col-lg-12 pb-3">
                                            <div class="form-group has-error mb-0 choose-img">
                                                <label for="upload_cv" class="font-weight-500 required">Upload CV</label>
                                                <input type="file" id="resume" name="upload_cv" accept="document/*">
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
    <!--end::Apply for job - Popup-->

    <!--begin::Global Javascript Bundle-->
    <script src="{{ asset('demo/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('demo/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->

    <!--begin::Form validation-->
    <script>
    $( document ).ready(function()
    {
        var KTAddCandidate = function () {
            // Elements
            var form;
            var submitButton;
            var validator;

            // Handle form
            var handleForm = function (e) {
                // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
                validator = FormValidation.formValidation(
                    form,
                    {
                        fields: {
                            'upload_cv':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Upload CV is required"
                                    }
                                }
                            },
                            'full_name':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Full name is required"
                                    }
                                }
                            },
                            'contact_number':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Contact number is required"
                                    },
                                    digits:
                                    {
                                        message: "Contact number must contains only digits"
                                    },
                                    stringLength:
                                    {
                                        min: 10,
                                        max: 10,
                                        message: "Contact number must contains 10 digits only"
                                    }
                                }
                            },
                            'email':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Email is required"
                                    },                                    
                                    emailAddress:
                                    {
                                        message: 'The value is not a valid email address'
                                    }
                                }
                            },
                            'designation':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Designation is required"
                                    }
                                }
                            },
                            'experience':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Experience is required"
                                    },
                                    digits:
                                    {
                                        message: "Experience contains only digits"
                                    },
                                    stringLength:
                                    {
                                        min: 1,
                                        max: 3,
                                        message: "experience contains only digits"
                                    }
                                }
                            },
                            'current_ctc':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Current CTC is required"
                                    }    
                                }
                            },
                            'notice_period':
                            {
                                validators:
                                {
                                    notEmpty:
                                    {
                                        message: "Notice period is required"
                                    }
                                }
                            }
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: '.fv-row'
                            })
                        }
                    }
                );

                // Handle form submit
                submitButton.addEventListener('click', function (e) {
                    // Prevent button default action
                    e.preventDefault();

                    // Validate form
                    validator.validate().then(function (status) {
                        if (status == 'Valid') {
                            // Show loading indication
                            submitButton.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click 
                            submitButton.disabled = true;


                            // Simulate ajax request
                            setTimeout(function () {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                // Enable button
                                submitButton.disabled = true;

                                // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                                Swal.fire({
                                    text: "You have successfully Submitted",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function(isConfirm)
                                {        
                                    if(isConfirm)
                                    { 
                                        form.submit();
                                    } 
                                
                                // .then(function (result)
                                // {
                                //     if (result.isConfirmed)
                                //     {
                                //         form.querySelector('[name="email"]').value = "";
                                //     }
                                // }

                                });
                            }, 2000);
                        } else {
                            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Sorry, looks like there are some errors detected, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                });
            }

            // Public functions
            return {
                // Initialization
                init: function () {
                    form 			=	document.querySelector('#applicantForm');
                    submitButton 	=	document.querySelector('#submit-form');

                    handleForm();
                }
            };
        }();

        // On document ready
        KTUtil.onDOMContentLoaded(function () {
            KTAddCandidate.init();
        });
    });
    </script>
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
                            <a href="javascript:void(0);">Upload Your CV</a>
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
            <div class="footer-top pt-100 pb-70">
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
                                            4Bell Technology, F-314, 1st Floor, Sector-63, Noida, <span
                                                class="text-uppercase">india</span>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <i class="ri-mail-line"></i>
                                    <div class="content">
                                        <a href="mailto:info@zoben.com">
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
                                © 2021 Meecruit | All Rights Reserved by
                                <a href="javascript:void(0);" target="_blank">client22021-999</a>
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
              
    <!--begin::Script for modal-->
    <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js'></script>
    <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js'></script>
    <!--end::Script for modal-->

    <!-- <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->
    <script src="{{ asset('demo/career_website/js/jquery.min.js') }}"></script>
    <!-- <script src="{{ asset('demo/career_website/js/bootstrap.bundle.min.js') }}"></script> -->
    <script src="{{ asset('demo/career_website/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- <script src="{{ asset('demo/career_website/js/odometer.min.js') }}"></script> -->
    <script src="{{ asset('demo/career_website/js/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/meanmenu.min.js') }}"></script>
    <!-- <script src="{{ asset('demo/career_website/js/metismenu.min.js') }}"></script> -->
    <!-- <script src="{{ asset('demo/career_website/js/simplebar.min.js') }}"></script> -->
    <!-- <script src="{{ asset('demo/career_website/js/dropzone.min.js') }}"></script> -->
    <!-- <script src="{{ asset('demo/career_website/js/sticky-sidebar.min.js') }}"></script> -->
    <!-- <script src="{{ asset('demo/career_website/js/tweenMax.min.js') }}"></script> -->
    <script src="{{ asset('demo/career_website/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/wow.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/form-validator.min.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/contact-form-script.js') }}"></script>
    <script src="{{ asset('demo/career_website/js/custom.js') }}"></script>
</body>
</html>