@extends('layouts.front')

<head>
    <style>
    </style>
</head>

@section('content')
<!--begin::Banner-area-three-->
<div class="banner-area-three">
    <div class="container">
        <div class="banner-content-three">
            <h1 class="wow fadeInUp" data-wow-delay="000ms" data-wow-duration="1000ms">Searching For A Job? <br>Find the Perfect Job Near You</h1>
            <div class="banner-form-three wow fadeInUp" data-wow-delay="100ms" data-wow-duration="1000ms">
                <form>
                    <div class="row">
                        <div class="col-lg-8 col-md-8">
                            <div class="form-group border-0">
                                <input class="form-control" type="text" placeholder="Keywords / Job Title">
                                <i class="ri-search-line"></i>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 d-none">
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="City or Postcode">
                                <i class="ri-map-pin-line"></i>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <button type="submit" class="submit-btn border-radius-5">
                                Find Jobs
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="popular-searches-tag wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1000ms">
                <span class="title">Popular Searches : </span>
                <a href="javascript:void(0);">UI/UX Designer</a>,
                <a href="javascript:void(0);">Developer</a>,
                <a href="javascript:void(0);">Web</a>,
                <a href="javascript:void(0);">iOS</a>,
                <a href="javascript:void(0);">PHP</a>,
                <a href="javascript:void(0);">Senior</a>,
                <a href="javascript:void(0);">Engineer</a>
            </div>
            <div class="banner-three-other">
                <div class="other-content1 wow fadeInUp" data-wow-delay="300ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/user2.jpg') }}" alt="Images">
                </div>
                <div class="other-content2 wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/home-three-icon2.png') }}" alt="Images">
                </div>
                <div class="other-content3 wow fadeInDown" data-wow-delay="500ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/user1.jpg') }}" alt="Images">
                </div>
                <div class="other-content4 wow fadeInDown" data-wow-delay="600ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/home-three-shape1.jpg') }}" alt="Images">
                </div>
                <div class="other-content5 wow fadeInUp" data-wow-delay="700ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/home-three-icon4.png') }}" alt="Images">
                </div>
                <div class="other-content6 wow fadeInDown" data-wow-delay="800ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/home-three-icon1.png') }}" alt="Images">
                </div>
                <div class="other-content7 wow fadeInDown" data-wow-delay="900ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/home-three-shape2.jpg') }}" alt="Images">
                </div>
                <div class="other-content8 wow fadeInDown" data-wow-delay="1000ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/user4.jpg') }}" alt="Images">
                </div>
                <div class="other-content9 wow fadeInDown" data-wow-delay="1100ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/user3.jpg') }}" alt="Images">
                    <div class="other-into-content">
                        <img src="{{ asset('demo/career_website/images/home-three/home-three-shape1.jpg') }}" alt="Images">
                    </div>
                </div>
                <div class="other-content10 wow fadeInDown" data-wow-delay="1200ms" data-wow-duration="1000ms">
                    <img src="{{ asset('demo/career_website/images/home-three/home-three-icon3.png') }}" alt="Images">
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Banner-area-three-->

<!--begin::Browse Jobs By Specialisms-->
<div class="browse-jobs-area-two pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">                
            <h2>Browse Jobs By Specialisms</h2>
            <div class="bar m-auto"></div>
            <p class="m-auto">144 jobs live - 20 added today.</p>
        </div>
        <div class="row pt-45" data-provide="shuffle">
            <!--begin::Menus-->
            <div class="job-filters mt-5 mb-5" data-shuffle="filter" style="display:contents">
                <!-- <button class="btn btn-success active px-2 py-1"data-shuffle="button">All</button> -->
                @foreach($locations as $location)
                    <div class="col-lg-3 col-6 pe-3">
                        <div class="browse-jobs-item">
                            <i class="ri-calculator-fill jobs-card-bg"></i>
                            <h3>
                                <a href="javascript:void(0);" data-shuffle="button" data-group="{{ $location->location }}">{{ ucwords($location->location) }}</a>
                            </h3>
                            <p>( 22 open positions )</p>
                        </div>
                    </div>
                @endforeach

                <!--begin::Bg and color codes-->
                <?php 
                if(!empty($categories))
                {
                    foreach($categories as $category)
                    {             
                        $bgTextDecore = $strVal = '';
                        $str = trim(@$category->name);
                        $str = $strVal = strtolower($str[0]);
                        $str = ord($str);
                        if ($str >= 97 && $str <= 99)
                        {
                            $bgTextDecore = 'fs-3 bg-light-danger text-danger';
                        }
                        else if ($str >= 100 && $str <= 104)
                        {
                            $bgTextDecore = 'fs-3 bg-light-warning text-warning';
                        }
                        else if ($str >= 105 && $str <= 107)
                        {
                            $bgTextDecore = 'fs-3 bg-light-primary text-primary';
                        }
                        else if ($str >= 108 && $str <= 110)
                        {
                            $bgTextDecore = 'fs-3 bg-light-success text-success';
                        }
                        else if ($str >= 111 && $str <= 113)
                        {
                            $bgTextDecore = 'fs-3 bg-dark text-secondary';
                        }
                        else
                        {
                            $bgTextDecore = 'fs-3 bg-light-secondary text-dark';
                        }
                ?>		
                <!--end::Bg and color codes-->			
                                
                    <div class="col-lg-3 col-6 pe-3">
                        <div class="browse-jobs-item">
                            <i class="ri-centos-fill jobs-card-bg  <?php echo @$bgTextDecore; ?>"></i>
                            <h3>									
                                <a href="<?php echo url('careers/'.$slug.'/job-listing'); ?>" data-shuffle="button" data-group="{{ $category->name }}">{{ ucwords($category->name) }}</a>
                            </h3>
                            <p>( 22 open positions )</p>
                        </div>
                    </div> 
                <?php 
                    }
                }			
                ?>
                <div class="col-lg-12 text-center">
                    <div class="browse-btn">
                        <a href="javascript:void(0);" data-shuffle="button"> Browse All Categories <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>

            </div>
            <!--end::Menus-->

        </div>
    </div>
</div>
<!--end::Browse Jobs By Specialisms-->

<!--begin::Find Your Dream Jobs-->
<div class="job-post-area pt-100 pb-5">
    <div class="container">
        <div class="section-title text-center">
            <h2>Find Your Dream Job</h2>
            <div class="bar m-auto"></div>
            <p class="m-auto">144 jobs live - 20 added today.</p>
        </div>

        <!--begin::Job list-->
        <div class="row pt-45 gap-y mb-5" data-shuffle="list"">
            @foreach($jobs as $job)
            <!--begin::Professional Copywriter for Advertising-->
            <div class="col-lg-12 portfolio-2" data-shuffle="item" data-groups="{{ @$job->location->location.','.@$job->category->name }}">
                    @php
                        $params = !module_enabled('Subdomain') ? [$job->slug] : [$job->company->career_page_link, $job->slug]
                    @endphp                        
                <div class="find-jobs-card d-flex align-items-center">
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
                            <!-- <li class="list-two"><i class="ri-map-pin-2-fill"></i>Gurgaon, <span class="text-uppercase">india</span> </li> -->
                            <li class="list-two"><i class="ri-time-line"></i>Posted Today</li>
                        </ul>
                        <ul class="content-list3">
                            <li><i class="ri-briefcase-line"></i>Experience : <b>8-9 Years</b></li>
                            <li><i class="ri-money-euro-circle-line"></i>Salary : <b>30-35 LPA</b></li>
                            <li><i class="ri-sensor-fill"></i>Openings : <b>832</b></li>
                            <li><i class="ri sensor-fill"></i>Category: <b>{{ ucwords(@$job->category->name) }}</b></li>
                        </ul>
                    </div>
                    <div class="find-jobs-btn">
                        <a href="javascript:void(0);" class="default-btn border-radius-5" data-toggle="modal" data-target="#apply_job">Apply Now<i class="ri-send-plane-2-line"></i></a>
                    </div>
                </div>
            </div>
            <!--end::Professional Copywriter for Advertising-->
            @endforeach
            <!--begin::Action-->
            <div class="col-lg-12 text-center">
                <div class="browse-btn">
                    <a href="javascript:void(0);"> Load More Job Opportunity <i class="ri-arrow-right-s-line"></i></a>
                </div>
            </div>
            <!--end::Action-->    
        </div>
        <!--end::Job list-->

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

            <!--begin::Action-->
            <div class="col-lg-12 text-center">
                <div class="browse-btn">
                    <a href="javascript:void(0);"> Load More Job Opportunity <i class="ri-arrow-right-s-line"></i></a>
                </div>
            </div>
            <!--end::Action-->
        </div>
    </div>
</div>
<!--end::Find Your Dream Jobs-->
@endsection