@extends('layouts.front')
<head>
    <title>Career Website - Job Description</title>
    <style>
    .employers-cv-area::before
    {
        background-color: #fff!important;
    }
    </style>
</head>

@section('header-text')
@endsection

@section('content')

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
                            <li class="list-two"><i class="ri-building-4-fill"></i>Company Name</li>
                            <li class="list-two"><i class="ri-user-2-fill"></i>R-1001</li>
                            <li class="list-two"><i class="ri-map-pin-2-fill"></i>Gurgaon, <span class="text-uppercase">india</span> </li>
                            <li class="list-two"><i class="ri-time-line"></i>Posted Today</li>
                        </ul>
                        <ul class="content-list3">
                            <li><i class="ri-briefcase-line"></i>Experience : <b>8-9 Years</b></li>
                            <li><i class="ri-money-euro-circle-line"></i>Salary : <b>30-35 LPA</b></li>
                            <li><i class="ri-sensor-fill"></i>Openings : <b>832</b></li>
                            <li><i class="ri-sensor-fill"></i>Category : <b>Accounts &amp; Finance</b></li>
                        </ul>
                    </div>
                    <div class="find-jobs-btn">
                        <a href="javascript:void(0);" class="default-btn border-radius-5" data-toggle="modal" data-target="#apply_job">Apply Now<i class="ri-send-plane-2-line"></i></a>
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

@endsection