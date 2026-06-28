@extends('layouts.front')
<head>
    <title>Career Website - Job Listing</title>
    <style>          
    .employers-cv-area::before
    {
        background-color: #fff!important;
    }
    </style>
</head>

@section('content')
    <!--begin::Banner-->
    <div class="inner-banner">
        <div class="container">
            <div class="inner-title text-center">
                <h3>Job Listing</h3>
                <ul>
                    <li>
                        <a href="<?php echo url('careers/client22021') ?>">Home</a>
                    </li>
                    <li>Category Name</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Banner-->

    <!--begin::Search bar & Job listing-->
    <div class="job-listing-area pt-100 pb-70">
        <div class="container">
            <!--begin::Search bar-->
            <div class="job-listing-top">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="job-listing-form">
                            <form class="mb-0 py-2">
                                <div class="row align-items-center">
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" placeholder="Keywords / Job Title">
                                            <i class="ri-search-line"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" placeholder="City or Postcode">
                                            <i class="ri-map-pin-line"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group select-group">
                                            <select class="form-select form-control">
                                                <option data-display="Choose A Category">Choose A Category</option>
                                                <option value="1">Ui/Ux Designer</option>
                                                <option value="2">Web Developer</option>
                                                <option value="2">FontEnd Developer</option>
                                                <option value="2">Creative Agency</option>
                                                <option value="3">Engineer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-12 pe-0">
                                        <button type="submit" class="submit-btn border-radius-5">Find Jobs</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Search bar-->

            <!--begin::Job listing-->
            <div class="row pt-45">
                @foreach($jobs as $job)			  
                <div class="col-lg-12 portfolio-2" data-shuffle="item" data-groups="{{ @$job->location->location.','.@$job->category->name }}">
                    @php
                        $params = !module_enabled('Subdomain') ? [$job->slug] : [$job->company->career_page_link, $job->slug]
                    @endphp
                    <div class="find-jobs-card d-flex align-items-center bg-light">
                        <div class="find-jobs-img">
                            <a href="{{ route('jobs.jobDetail', $params) }}">
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
                            <h3><a href="{{ route('jobs.jobDetail', $params) }}">{{ ucwords($job->title) }}</a></h3>
                            <ul class="content-list2">                                
                                @if($job->company->show_in_frontend == 'true')
                                <li class="list-two"><i class="ri-building-4-fill"></i>Company Name</li>
                                @endif
                                <li class="list-two"><i class="ri-user-2-fill"></i>R-1001</li>
                                <li class="list-two"><i class="ri-map-pin-2-fill"></i>{{ ucwords(@$job->location->location).' | '.ucwords(@$job->location->country->country_name) }}</li>
                                <li class="list-two"><i class="ri-time-line"></i>Posted Today</li>
                            </ul>
                            <ul class="content-list3">
                                <li><i class="ri-briefcase-line"></i>Experience : <b>8-9 Years</b></li>
                                <li><i class="ri-money-euro-circle-line"></i>Salary : <b>30-35 LPA</b></li>
                                <li><i class="ri-sensor-fill"></i>Openings : <b>832</b></li>
                                <li><i class="ri-sensor-fill"></i>Category : <b>{{ ucwords(@$job->category->name) }}</b></li>
                            </ul>
                        </div>
                        <div class="find-jobs-btn">
                            <a href="javascript:void(0);" class="default-btn border-radius-5" data-toggle="modal" data-target="#apply_job">Apply Now<i class="ri-send-plane-2-line"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <!--end::Job listing-->

            <!--begin::Pagination-->
            <div class="col-lg-12 col-md-12 text-center">
                <div class="pagination-area">
                    <a href="javascript:void(0);" class="prev page-numbers">
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                    <span class="page-numbers current" aria-current="page">1</span>
                    <a href="javascript:void(0);" class="page-numbers">2</a>
                    <a href="javascript:void(0);" class="page-numbers">3</a>
                    <a href="javascript:void(0);" class="next page-numbers">
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
            <!--end::Pagination-->

            <div class="row pt-45 d-none">
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
                            <h3><a href="javascript:void(0);">Professional Copywriter for Advertising</a></h3>
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
                            <a href="javascript:void(0);" class="default-btn border-radius-5">Apply Now<i class="ri-send-plane-2-line"></i></a>
                        </div>
                    </div>
                </div>
                <!--end::Professional Copywriter for Advertising-->

                <!--begin::High-level Synthesis Design Engineer, Hardware-->
                <div class="col-lg-12">
                    <div class="find-jobs-card d-flex align-items-center">
                        <div class="find-jobs-img">
                            <a href="javascript:void(0);">
                                <img src="{{ asset('demo/career_website/images/find-jobs/find-jobs2.jpg') }}" alt="img2">
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
                            <h3><a href="javascript:void(0);">High-level Synthesis Design Engineer, Hardware</a></h3>
                            <ul class="content-list2">
                                <li class="list-two"><i class="ri-building-4-fill"></i>Tech Mahindra</li>
                                <li class="list-two"><i class="ri-user-2-fill"></i>R-1002</li>
                                <li class="list-two"><i class="ri-map-pin-2-fill"></i>Greater Noida, <span class="text-uppercase">india</span> </li>
                                <li class="list-two"><i class="ri-time-line"></i>Posted 24-Sep-2021</li>
                            </ul>
                            <ul class="content-list3">
                                <li><i class="ri-briefcase-line"></i>Experience : <b>4-7 Years</b></li>
                                <li><i class="ri-money-euro-circle-line"></i>Salary : <b>20-25 LPA</b></li>
                                <li><i class="ri-sensor-fill"></i>Openings : <b>1,332</b></li>
                                <li><i class="ri-slack-fill"></i>Department : <b>Marketing</b></li>
                            </ul>
                        </div>
                        <div class="find-jobs-btn">
                            <a href="javascript:void(0);" class="default-btn border-radius-5">Apply Now<i class="ri-send-plane-2-line"></i></a>
                        </div>
                    </div>
                </div>
                <!--end::High-level Synthesis Design Engineer, Hardware-->

                <!--begin::Bank Transfer Operations Team Leader-->
                <div class="col-lg-12">
                    <div class="find-jobs-card d-flex align-items-center">
                        <div class="find-jobs-img">
                            <a href="javascript:void(0);">
                                <img src="{{ asset('demo/career_website/images/find-jobs/find-jobs3.jpg') }}" alt="img3">
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
                            <h3><a href="javascript:void(0);">Bank Transfer Operations Team Leader</a></h3>
                            <ul class="content-list2">
                                <li class="list-two"><i class="ri-building-4-fill"></i>HCL Technologies</li>
                                <li class="list-two"><i class="ri-user-2-fill"></i>R-1003</li>
                                <li class="list-two"><i class="ri-map-pin-2-fill"></i>Delhi, <span class="text-uppercase">india</span></li>
                                <li class="list-two"><i class="ri-time-line"></i>Posted 10-April-2021</li>
                            </ul>
                            <ul class="content-list3">
                                <li><i class="ri-bricase-line"></i>Experience : <b>10-12 Years</b></li>
                                <li><i class="ri-money-euro-circle-line"></i>Salary : <b>45-70 LPA</b></li>
                                <li><i class="ri-sensor-fill"></i>Openings : <b>190</b></li>
                                <li><i class="ri-slack-fill"></i>Department : <b>Accounts & Finance</b></li>
                            </ul>                            
                        </div>
                        <div class="find-jobs-btn">
                            <a href="javascript:void(0);" class="default-btn border-radius-5">Apply Now<i class="ri-send-plane-2-line"></i></a>
                        </div>
                    </div>
                </div>
                <!--end::Bank Transfer Operations Team Leader-->

                <!--begin::Principal Devops Engineer (Senior Manager, Technology)-->
                <div class="col-lg-12">
                    <div class="find-jobs-card d-flex align-items-center">
                        <div class="find-jobs-img">
                            <a href="javascript:void(0);">
                                <img src="{{ asset('demo/career_website/images/find-jobs/find-jobs4.jpg') }}" alt="img4">
                            </a>
                        </div>
                        <div class="content">
                            <!--begin::Buttons-->
                            <ul class="content-list d-none">
                                <li class="time">full Time</li>
                                <li class="freelance">Freelance</li>
                                <li class="internship">Internship</li>
                            </ul>
                            <!--end::Buttons-->
                            <h3><a href="javascript:void(0);">Principal DevOps Engineer (Senior Manager, Technology)</a></h3>
                            <ul class="content-list2">
                                <li class="list-two"><i class="ri-building-4-fill"></i>Wipro</li>
                                <li class="list-two"><i class="ri-user-2-fill"></i>R-1004</li>
                                <li class="list-two"><i class="ri-map-pin-2-fill"></i>Pune <span class="text-uppercase">india</span></li>
                                <li class="list-two"><i class="ri-time-line"></i>Posted 7 days Ago</li>
                            </ul>
                            <ul class="content-list3">
                                <li><i class="ri-brifcase-line"></i>Experience : <b>10-12 Years</b></li>
                                <li><i class="ri-money-euro-circle-line"></i>Salary : <b>50-70 LPA</b></li>
                                <li><i class="ri-sensor-fill"></i>Openings : <b>430</b></li>
                                <li><i class="ri-slack-fill"></i>Department : <b>Recruitment</b></li>
                            </ul>
                        </div>
                        <div class="find-jobs-btn">
                            <a href="javascript:void(0);" class="default-btn border-radius-5">Apply Now<i class="ri-send-plane-2-line"></i> </a>
                        </div>
                    </div>
                </div>
                <!--end::Principal Devops Engineer (Senior Manager, Technology)-->
            </div>
        </div>
    </div>
    <!--end::Search bar & Job listing-->
@endsection