<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Talentmily | Homescreen</title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="">
<!-- <link rel="stylesheet" href="{{ asset('assets_ng/career_website/css/mega_menu.min.css') }}" rel="stylesheet"> -->
<link rel="shortcut icon" href="{{ asset('assets_ng/landing/images/favicon-16x161.png') }}">
<!-- Favicon Icon -->
<link rel="stylesheet" href="{{ asset('assets_ng/landing/css/plugins.css')}}">
<!-- plugins Css -->
<link rel="stylesheet" href="{{ asset('assets_ng/landing/css/fonts-icons.css')}}">
<!-- Font Icons Css -->
<link rel="stylesheet" href="{{ asset('assets_ng/landing/css/style.css')}}">
<!-- Custom Css -->
<link rel="stylesheet" href="{{ asset('assets_ng/landing/css/helper.css')}}">
<!-- Helper Css -->
<link id="color-opt" rel="stylesheet" href="css/skins/skin-default.css">
<!-- Skins color Css -->
<link rel="stylesheet" href="{{ asset('assets_ng/landing/css/bootstrap.min.css')}}">
<!-- Bootstrap-4.1.2 -->
</head>

<body>

<!-- Start PreLoader
============================================= -->
<div id="pre-loader" class="pre-loader bg-custom">
  <div class="cube-wrapper">
    <div class="cube-folding">
      <span class="leaf1"></span>
      <span class="leaf2"></span>
      <span class="leaf3"></span>
      <span class="leaf4"></span>
    </div>
    <span class="loading" data-name="Loading">Please Wait...!</span>
    <!-- <span class="loading" data-name="Loading">Loading...!</span> -->
  </div>

  <div class="pre-loader-logo">
    <img src="{{ asset('assets_ng/landing/images/Logo_white.png')}}" height="60px">
  </div>
</div>
<!-- End PreLoader
============================================== -->

<!-- Start Header
================================================== -->
<header class="header fixed-top">
  <nav id="menu1" class="navbar navbar-expand-lg navbar-light bg-white navbar-custom">
    <div class="header-logo">
      <a class="navbar-brand d-flex align-items-center" href="{{ url('/')}}">
        <span class="fw-3 text-capitalize">
          <img src="{{ asset('assets_ng/landing/images/Logo1.png')}}">
        </span>
      </a>
    </div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <!-- <span class="navbar-toggler"><i class="fa fa-bars"></i> -->
      <span class="navbar-toggler">
        <img src="{{ asset('assets_ng/landing/images/menu.png')}}">
      </span>
    </button>

    <div class="collapse navbar-collapse header-nav" id="navbarCollapse">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link page-scroll" href="#home">Home
            <span class="sr-only">(current)</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link page-scroll" href="#why-choose">Talentmily?</a>
        </li>

        <li class="nav-item">
          <a class="nav-link page-scroll" href="#recruitment-automation">Recruitment Automation</a>
        </li>

        <li class="nav-item">
          <a class="nav-link page-scroll" href="#services">Solution</a>
        </li>

        <li class="nav-item">
          <a class="nav-link page-scroll" href="#works">How it works</a>
        </li>

        <li class="nav-item">
          <a class="nav-link page-scroll" href="#pricing">Ensure Data</a>
        </li>

        <li class="nav-item">
          <a class="nav-link page-scroll" href="#contact">Contact</a>
        </li>

        <!-- <li class="nav-item trail"><a class="btn text-center mt-lg-20 mt-0 trail-btn" href="#free_trail" data-toggle="modal"><i class="fa fa-tags" aria-hidden="true"></i>&nbsp;Free Trail</a></li> -->


        



			
@if (!Auth::guest())
		<li class="nav-item trail">
			  <a class="btn trail-btn d-block" href="{{ route('login') }}" target="blank">
				<span class="btn__text"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Dashboard</span>
			  </a>
		</li>
		<li class="nav-item login">
			<a class="btn ml-lg-10 mr-lg-10 login-btn" href="{{ route('logout') }}" title="Sign Out" onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
				</form> 
				Sign Out  
			</a> 
		</li> 
@else
        @if($headerData->show_register_in_menu)
			<li class="nav-item trail">
			  <a class="btn trail-btn d-block" href="{{ route('register') }}" target="blank">
				<span class="btn__text"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Register</span>
			  </a>
			</li>
        @endif       

        @if($headerData->show_login_in_menu)
			<li class="nav-item login">
				<a class="btn ml-lg-10 mr-lg-10 login-btn" href="{{ route('login') }}" target="blank">
					<i class="fa fa-sign-in-alt" aria-hidden="true"></i>&nbsp;@lang('app.login')
				</a>
			</li>
        @endif
@endif
				
				


        <!--  @if($headerData->
        show_register_in_menu)
        <li class="nav-item register"><a class="btn register-btn d-block" href="{{ route('register') }}" target="blank"> <span class="btn__text">@lang('app.register')</span> </a></li>
        @endif -->
      </ul>
    </div>
  </nav>
</header>
<!-- End Header
================================================== -->

<!-- Start Home
================================================== -->
<section id="home" class="d-flex align-items-center section text-white full-home" data-parallax data-speed="0.2">
  <div class="bg-overlay"></div>
  <div class="container-fluid">
    <div class="row justify-content-center align-items-center">
      <div class="col-md-4">
        <h1 class="font-30 fw-5 text-black">The AI-Powered Talent<span style="color:#ff008a;">MILY</span>
          Acquisition Platform
        </h1>
        <p class="font-20 mt-4 text-black">Better Recruiting | More Hires | Easier Workforce Management</p>
        <a   href="{{ route('register') }}" class="btn btn btn-outline-dark btn-outline-dark1 ml-2 mr-2 mt-3 mb-3 text-capitalize">
          Register for FREE<i class="fa fa-users ml-1"></i>
        </a>
        <a href="javascript:void(0);" class="btn btn btn-outline-dark btn-outline-dark1 ml-2 mr-2 mt-3 mb-3 text-capitalize" data-toggle="modal" data-target="#requestDemo">
          Request for DEMO
          <!-- <i class="fa fa-phone ml-2"></i> -->
          <i class="fa fa-laptop"></i>
        </a>
        </div>

      <div class="col-md-8 mt-4 mb-4">
        <div class="w-100">
          <img class="img-fluid d-block mx-auto banner-img" src="{{ asset('assets_ng/landing/images/gif.gif')}}" alt="Here is the best way to present your apps">
        </div>
      </div>
    </div>
  </div>

  <a href="#why-choose" class="home-scroll-down page-scroll">
  <svg viewBox="36.667 0.444 26.667 78.851">
    <circle cx="50" cy="11.555" r="2.667"></circle>
    <path d="M50,0.444c-7.364,0-13.333,5.971-13.333,13.335v22.219c0,7.365,5.97,13.335,13.333,13.335 c7.363,0,13.334-5.971,13.334-13.335V13.779C63.334,6.415,57.363,0.444,50,0.444z M61.556,35.998 c0,6.373-5.185,11.558-11.556,11.558c-6.372,0-11.556-5.185-11.556-11.558V13.779c0-6.372,5.184-11.557,11.556-11.557 c6.371,0,11.556,5.185,11.556,11.557V35.998z"></path>

    <path d="M61.816,53.149L50,64.965L38.184,53.149c-0.348-0.348-0.909-0.348-1.257,0s-0.348,0.909,0,1.257l12.444,12.444
    c0.173,0.173,0.401,0.261,0.628,0.261s0.455-0.088,0.629-0.261l12.443-12.444c0.348-0.348,0.348-0.909,0-1.257
    C62.726,52.802,62.164,52.802,61.816,53.149z"></path>

    <path d="M61.816,65.594L50,77.41L38.184,65.594c-0.348-0.348-0.909-0.348-1.257,0s-0.348,0.909,0,1.257l12.444,12.444
    c0.173,0.174,0.401,0.261,0.628,0.261s0.455-0.087,0.629-0.261l12.443-12.444c0.348-0.348,0.348-0.909,0-1.257
    C62.726,65.246,62.164,65.246,61.816,65.594z"></path>
  </svg>
</a>

  <div class="home-shapes">
    <svg class="text-white" viewBox="0 0 1920 200">
      <polygon points="959.989,199.311 0,0 0,200.004 956.648,200.004 963.329,200.004 1920,200.004 1920,0">
    </svg>
  </div>
</section>
<!-- End Home
================================================== -->

<!-- Popup for Request DEMO -->
<div class="modal fade" id="requestDemo" tabindex="-1" role="dialog" aria-labelledby="requestDemoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <p class="modal-title" id="requestDemoLabel"><i class="fa fa-laptop"></i>&nbsp;Schedule a Talentmily Demo</p>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h5 class="text-pink text-center py-2">
          <i class="fa fa-phone"></i>&nbsp;Let's talk about your HR needs
        </h5>
        <form class="ajax-form" method="POST" id="" onSubmit="">
        <!-- firstName -->
         <div class="form-group mb-4">
            <input class="input1 w-100"  type="text" class="outline-0 border-0 border-bottom" id="first_name" name="first_name" placeholder="🙍‍♂️&nbsp;Enter First Name" />
         </div>
        <!-- firstName End -->

        <!-- LastName -->
         <div class="form-group mb-4">
            <input class="input1 w-100"  type="text" class="outline-0 border-0 border-bottom" id="last_name" name="last_name" placeholder="🙍‍♂️&nbsp;Enter Last Name" />
         </div>
        <!-- LattName End -->

        <!-- companyName -->
        <div class="form-group mb-4">
           <input class="input1 w-100" type="text" id="company_name" name="company_name" class="outline-0 border-0 border-bottom" placeholder="&#127970;&nbsp;Enter Your @lang('modules.accountSettings.companyName')" />
        </div>
        <!-- companyName End -->

        <!-- companyEmail -->
        <div class="form-group mb-4">
           <input class="input1 w-100" type="email" id="company_email" name="company_email" class="outline-0 border-0 border-bottom" placeholder="&#128231;&nbsp;Enter Your Company Email" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" />
           <div id="company_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
        </div>
        <!-- companyEmail End -->

        <!-- Phone Number -->
       <div class="form-group mb-4">
          <input class="input1 w-100" type="number" name="phone" placeholder="📞&nbsp;Enter Your Phone Number" class="outline-0 border-bottom border-0" autocomplete="off"/>
       </div>    
       <!-- Phone Number End -->

       <!-- Create an account Button -->
       <div class="row mb-2">
          <div class="col-sm-12">
             <button class="btn login1-btn" type="submit" id="">
                Request for demo&nbsp;<i class="fa fa-angle-double-right"></i>
              </button>
          </div>
       </div>
       <!-- Create an account Button End -->

       <!-- By clicking on -->
       <p class="text-2 text-dark mb-0 pt-1" style="font-size: .73rem;font-weight: 400;">By clicking on <span class="text-black font-weight-600">REQUEST FOR DEMO ,</span> you acknowledge having read our &nbsp;<a class="btn-link" href="{{ route('register') }}">Privacy notice</a>
       </p>
       <!-- By clicking on End -->

      </form>
      </div>
      <div class="modal-footer d-none">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!-- Popup for Request DEMO End -->

<!-- Why Choose TallentMILY?
================================================== -->

<section id="why-choose" class="section pb-0 ">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-2">
      <div class="col-lg-12">
        <h2 class="heading text-dark">Why Choose TalentMILY?</h2>
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-sm-6 col-lg-4 mt-20 mb-20">
        <div class="service-box text-center"><i class="fa fa-industry font-50"></i>
          <h5 class="mt-4 mb-3 font-weight-normal">Best Pricing in the Industry</h5>
          <p>Save +75% on license price and choose from flexible & fair pricing packages.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4 mt-20 mb-20">
        <div class="service-box text-center"><i class="fa fa-handshake font-50"></i>
          <h5 class="mt-4 mb-3 font-weight-normal">Highest Satisfaction Rate</h5>
          <p>Get data migration, training, & a dedicated account manager – free.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4 mt-20 mb-20">
        <div class="service-box text-center"><i class="fa fa-laptop font-50"></i>
          <h5 class="mt-4 mb-3 font-weight-normal">Most Advanced Platform</h5>
          <p>Increase productivity with our intuitive & beautifully designed interface.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Choose TallentMILY?
================================================== -->

<!-- Recruitment Automation
================================================== -->
<section id="recruitment-automation" class="section bg-white">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <!-- <h2 class="heading text-dark mb-2">Recruitment Automation</h2> -->
        <h2 class="heading text-dark mb-2">Recruitment Automation</h2>
      </div>
      <div class="col-lg-8">
        <p class="font-18 mb-0">Recruit smarter with the most advanced technology for successful hiring.</p>
        <p class="font-15 text-muted mt-1">
          <i class="fa fa-quote-left text-muted fa-sm" aria-hidden="true"></i>&nbsp;
          Complete Solutions, One Integral Platform -&nbsp;<i class="fa fa-quote-right text-muted fa-sm" aria-hidden="true"></i>
        </p>
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-lg-12">
        <nav>
          <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="ai-applicant-ranking-tab" data-toggle="tab" href="#ai-applicant-ranking" role="tab" aria-controls="ai-applicant-ranking" aria-selected="true">AI Applicant Ranking</a>
            <a class="nav-item nav-link" id="two-way-email-tab" data-toggle="tab" href="#two-way-email" role="tab" aria-controls="two-way-email" aria-selected="false">Two-Way Email Sync</a>
            <a class="nav-item nav-link" id="business-intelligence-tab" data-toggle="tab" href="#business-intelligence" role="tab" aria-controls="business-intelligence" aria-selected="false">Business Intelligence</a>
            <a class="nav-item nav-link" id="background-checks-tab" data-toggle="tab" href="#background-checks" role="tab" aria-controls="background-checks" aria-selected="false">Background Checks</a>
            <a class="nav-item nav-link" id="background-checks-tab" data-toggle="tab" href="#managed-harvesting" role="tab" aria-controls="managed-harvesting" aria-selected="false">Managed Harvesting</a>
            <a class="nav-item nav-link" id="paperless-onboarding-tab" data-toggle="tab" href="#paperless-onboarding" role="tab" aria-controls="paperless-onboarding" aria-selected="false">Paperless Onboarding</a>
          </div>
        </nav>

        <div class="tab-content pt-35" id="nav-tabContent">
          <div class="tab-pane fade show active" id="ai-applicant-ranking" role="tabpanel" aria-labelledby="ai-applicant-ranking-tab">
            <div class="row justify-content-center align-items-center">
              <div class="col-lg-8">
                <div class="w-100">
                  <img class="img-fluid d-block mx-auto" src="{{ asset('assets_ng/landing/images/img-1-1.png')}}" alt="TallentMILY" data-tilt>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="media mt-30"> <span class="mr-3 h4 text-custom"><i class="fa fa-envelope"></i></span>
                  <div class="media-body">
                    <h6 class="text-black mb-1">Parallax effects</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the</p>
                  </div>
                </div>

                <div class="media mt-30"> <span class="mr-3 h4 text-custom"><i class="fa fa-tasks"></i></span>
                  <div class="media-body">
                    <h6 class="text-black mb-1">Slider</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the</p>
                  </div>
                </div>

                <div class="media mt-30"> <span class="mr-3 h4 text-custom"><i class="fa fa-arrows-alt"></i></span>
                  <div class="media-body">
                    <h6 class="text-black mb-1">Working Contact Form</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="two-way-email" role="tabpanel" aria-labelledby="two-way-email-tab">
            <div class="row">
              <div class="col-lg-3">
                <div class="w-100">
                  <img class="img-fluid d-block mx-auto" src="{{ asset('assets_ng/landing/images/img-3.png')}}" alt="Roy Henry"> </div>
              </div>

              <div class="col-lg-9">
                <p>Roy is the main person behind Brave. He is the multitalented manager and a former UX designer who is a true mine of creative ideas for everyone at our team.The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33</p>
                <p>Roy is the main person behind Brave. He is the multitalented manager and a former UX designer who is a true mine of creative ideas for everyone at our team.</p>
                <p>The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33.The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33.The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33</p>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="business-intelligence" role="tabpanel" aria-labelledby="business-intelligence-tab">
            <div class="row">
              <div class="col-lg-12">
                <p>In the digital age, we are marketing ourselves on the web.  That is why we aim to make it as easy and affordable for everyone to take charge of their web presence.  Words like tweet and yelp should be part of your marketing campaign, and not part of your stress.</p>
                <blockquote class="font-25 fw-3 font-italic">We strive to create a memorable web image for everyone.</blockquote>
                <p>We establish a relationship with all of our clients, and we feel it is our responsibility to help them grow and harness the opportunity of their presence on the Internet.</p>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="background-checks" role="tabpanel" aria-labelledby="background-checks-tab">
            <div class="row">
              <div class="col-lg-12">
                <p>Background Checks In the digital age, we are marketing ourselves on the web.  That is why we aim to make it as easy and affordable for everyone to take charge of their web presence.  Words like tweet and yelp should be part of your marketing campaign, and not part of your stress.</p>
                <blockquote class="font-25 fw-3 font-italic">We strive to create a memorable web image for everyone.</blockquote>
                <p>We establish a relationship with all of our clients, and we feel it is our responsibility to help them grow and harness the opportunity of their presence on the Internet.</p>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="managed-harvesting" role="tabpanel" aria-labelledby="managed-harvesting-tab">
            <div class="row">
              <div class="col-lg-12">
                <p>Managed Harvesting In the digital age, we are marketing ourselves on the web.  That is why we aim to make it as easy and affordable for everyone to take charge of their web presence.  Words like tweet and yelp should be part of your marketing campaign, and not part of your stress.</p>
                <blockquote class="font-25 fw-3 font-italic">We strive to create a memorable web image for everyone.</blockquote>
                <p>We establish a relationship with all of our clients, and we feel it is our responsibility to help them grow and harness the opportunity of their presence on the Internet.</p>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="paperless-onboarding" role="tabpanel" aria-labelledby="paperless-onboarding-tab">
            <div class="row">
              <div class="col-lg-12">
                <p>Paperless Onboarding In the digital age, we are marketing ourselves on the web.  That is why we aim to make it as easy and affordable for everyone to take charge of their web presence.  Words like tweet and yelp should be part of your marketing campaign, and not part of your stress.</p>
                <blockquote class="font-25 fw-3 font-italic">We strive to create a memorable web image for everyone.</blockquote>
                <p>We establish a relationship with all of our clients, and we feel it is our responsibility to help them grow and harness the opportunity of their presence on the Internet.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Recruitment Automation
================================================== -->

<!-- Proven Talentmily Acquisition Solutions
================================================== -->
<section id="services" class="section bg-custom text-white">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <!-- <h2 class="fw-3 mb-4">Proven TalentMILY Acquisition Solutions</h2> -->
        <h2 class="heading text-white mb-4">Proven TalentMILY Acquisition Solutions</h2>
      </div>
      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-right border-bottom-smaller">
        <div class="service-box text-center" data-tilt> <i class="fa fa-share-alt-square font-50"></i>
          <h5 class="mt-4 mb-3">Social Media Integration</h5>
          <p>Share jobs on social media from career site.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-right border-bottom-smaller">
        <div class="service-box text-center" data-tilt> <i class="fa fa-file-excel font-50"></i>
          <h5 class="mt-4 mb-3">Export to excel</h5>
          <p>Select the information that you wish to export (Excel or CSV).</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-right border-bottom-smaller">
        <div class="service-box text-center" data-tilt> <i class="fa fa-indent font-50"></i>
          <h5 class="mt-4 mb-3">Quick Filters</h5>
          <p>Add quick filters to narrow your talent pool searches.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-bottom-smaller">
        <div class="service-box text-center" data-tilt>
          <i class="fa fa-star"></i>
          <i class="fa fa-star font-50"></i>
          <i class="fa fa-star"></i>
          <h5 class="mt-4 mb-3">Applicant Rating</h5>
          <p>Quickly evaluate candidates with rating system.</p>
        </div>
      </div>
    </div>

    <hr class="border-bottom mb-0 mt-0">
    <div class="row justify-content-center align-items-center">
      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-right border-bottom-smaller">
        <div class="service-box text-center" data-tilt> <i class="fa fa-comments font-50"></i>
          <h5 class="mt-4 mb-3">Questionnaires</h5>
          <p>Create custom questionnaires for job application form.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-right border-bottom-smaller">
        <div class="service-box text-center" data-tilt> <i class="fa fa-address-card font-50"></i>
          <h5 class="mt-4 mb-3">Flexible hiring roles</h5>
          <p>Assign flexible hiring roles to your team.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-20 mb-20 border-right border-bottom-smaller">
        <div class="service-box text-center" data-tilt><i class="fa fa-cog font-50"></i>
          <h5 class="mt-4 mb-3">Theme Settings</h5>
          <p>Change logo and color of the app according to your branding.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-20 mb-20">
        <div class="service-box text-center" data-tilt><i class="fa fa-language font-50"></i>
          <h5 class="mt-4 mb-3">Multiple Languages</h5>
          <p>Recruit is available in multiple languages.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Proven Talentmily Acquisition Solutions
================================================== -->

<!-- Start How It Works?
================================================== -->
<section class="section bg-white" id="works">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <h2 class="heading text-dark mb-4">How It Works?</h2>
        <!-- <h2 class="text-black fw-3 mb-4">How It Works?</h2> -->
      </div>
      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>
    <div class="row justify-content-center align-items-center">
      <div class="col-sm-6 col-lg-4 mt-20 mb-20">
        <div class="how-box text-center">
          <span class="position-relative d-inline-block">
            <img class="img-fluid d-block mx-auto rounded-circle" src="{{ asset('assets_ng/landing/images/how/01.jpg')}}" alt="DISCOVERY">
            <span class="how-number bg-white text-custom">1</span>
          </span>
          <h5 class="text-custom mt-4 mb-3">Discovery</h5>
          <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-4 mt-20 mb-20">
        <div class="how-box text-center">
          <span class="position-relative d-inline-block">
            <img class="img-fluid d-block mx-auto rounded-circle" src="{{ asset('assets_ng/landing/images/how/02.jpg')}}" alt="PLANNING">
            <span class="how-number bg-white text-custom">2</span>
          </span>
          <h5 class="text-custom mt-4 mb-3">Planning</h5>
          <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4 mt-20 mb-20">
        <div class="how-box text-center">
          <span class="position-relative d-inline-block">
            <img class="img-fluid d-block mx-auto rounded-circle" src="{{ asset('assets_ng/landing/images/how/03.jpg')}}" alt="EXECUTION">
            <span class="how-number bg-white text-custom">3</span>
          </span>
          <h5 class="text-custom mt-4 mb-3">Execution</h5>
          <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End How It Works?
================================================== -->

<!-- Start Video Presentation
================================================== -->
<section id="video_section" class="section text-white pt-100 pb-60" data-parallax data-speed="0.2" data-src="{{ asset('assets_ng/landing/images/banner/img-7.jpg')}}">
  <div class="bg-overlay bg-overlay1"></div>
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <h2 class="fw-3 mb-4 d-flex align-items-center justify-content-center">What Our Customer Said&nbsp;&nbsp;-&nbsp;Video Presentation&nbsp;</h2>
        <!-- <h4 class="fw-3 mb-4">Video Presentation</h4> -->
      </div>
      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>

    <div class="row mt-60">
      <div class="col-lg-12 text-center">
        <a class="play-btn d-inline-block popup-youtube" href="https://www.youtube.com/watch?v=M-LRcmFAg0w" target="blank">
          <span class="play-btn-icon">
            <span class="play-btn-shield bg-white font-20 rounded-circle d-flex justify-content-center align-items-center text-center"> <i class="icofont icofont-ui-play text-custom"></i></span>
            <span class="play-btn-circle play-btn-circle-1"></span>
            <span class="play-btn-circle play-btn-circle-2"></span>
            <span class="play-btn-circle play-btn-circle-3"></span>
          </span>
        </a>
      </div>
    </div>
  </div>
</section>
<!-- End Video Presentation
================================================== -->

<!-- Start Do you need a Consultation?
================================================== -->
<section class="pt-15 pb-15 bg-custom text-white">
  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-8">
        <!-- <h2 class="fw-3">Do you need a Consultation?</h2> -->
        <h2 class="heading text-white mb-4">Do you need a Consultation?</h2>
        <p>Feel free to contact us if you have any questions on how you can use Headland.</p>
      </div>
      <div class="col-lg-4 text-lg-right"> <a href="#contact" class="btn btn-outline-light mt-4 mb-4 page-scroll">Contact us</a> </div>
    </div>
  </div>
</section>
<!-- End Do you need a Consultation?
================================================== -->

<!-- Talentmily by the Numbers
================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <!-- <h2 class="text-black fw-3 mb-4">TalentMILY by the Numbers</h2> -->
        <h2 class="heading text-dark mb-4">TalentMILY by the Numbers</h2>
      </div>
      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-lg-5">
        <ul class="row list-unstyled pl-0 mb-0 ul-framework">
          <li class="col-sm-6 col-lg-6 text-center li-framework pt-30 pb-30">
            <i class="fa fa-film font-50 text-custom"></i>
            <span class="d-block h3 fw-3 text-black mt-15 mb-0">
              <span class="counter-value">200</span>+
            </span>
            <p>Recruiting & Enterprise Firms</p>
          </li>

          <li class="col-sm-6 col-lg-6 text-center li-framework pt-30 pb-30">
            <i class="fa fa-globe font-50 text-custom"></i>
            <span class="d-block h3 fw-3 text-black mt-15 mb-0">
              <span class="counter-value">600</span>+
            </span>
            <p>Recruiters Worldwide</p>
          </li>

          <li class="col-sm-6 col-lg-6 text-center li-framework pt-30 pb-30">
            <i class="fa fa-users font-50 text-custom"></i>
            <span class="d-block h3 fw-3 text-black mt-15 mb-0">
              <span class="counter-value">150</span>
            </span>
            <p>Global Employees</p>
          </li>
          <li class="col-sm-6 col-lg-6 text-center li-framework pt-30 pb-30">
            <i class="fa fa-comments font-50 text-custom"></i>
            <span class="d-block h3 fw-3 text-black mt-15 mb-0">
              <span class="counter-value">1200</span>
            </span>
            <p>Customer Reviews</p>
          </li>
        </ul>
      </div>

      <div class="col-lg-6 offset-lg-1 mt-3 mb-3">
        <div class="w-100">
          <img class="img-fluid d-block mx-auto" src="{{ asset('assets_ng/landing/images/img-4.png')}}" alt="We are digital creative Landing Page"> </div>
      </div>
    </div>
  </div>
</section>
<!-- Talentmily by the Numbers
================================================== -->

<!-- We Ensure the Security of Your Data
================================================== -->
<section id="pricing" class="section bg-white">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <!-- <h2 class="text-black fw-3 mb-4">We Ensure the Security of Your Data</h2> -->
        <h2 class="heading text-dark mb-4">We Ensure the Security of Your Data</h2>
      </div>
      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-sm-6 col-lg-4 mt-15 mb-15">
        <div class="bg-white mt-3 price-box active pt-30 position-relative pink-border">
          <!-- VIEW OUR RECOMMENDED SECURITY BEST PRACTICES -->
          <span class="price-box-lable bg-custom">Securely manage your data</span>
          <span class="price-border d-block bg-custom"></span>
          <div class="pricing-name text-center">
            <h5 class="mb-0 text-black">SOC2 Compliance</h5>
          </div>

          <div class="plan-price bg-custom text-white text-center mt-4 pt-3 pb-3"> <i class="fa fa-envelope font-55"></i></div>
          <div class="price-features text-justify mt-1">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4 mt-15 mb-15">
        <div class="bg-white mt-3 price-box active pt-30 position-relative pink-border">
          <!-- VIEW OUR RECOMMENDED SECURITY BEST PRACTICES -->
          <span class="price-box-lable bg-custom">In another form</span>
          <span class="price-border d-block bg-custom"></span>
          <div class="pricing-name text-center">
            <h5 class="mb-0 text-black">Data Encryption</h5>
          </div>
          <div class="plan-price bg-custom text-white text-center mt-4 pt-3 pb-3"> <i class="fa fa-database font-55"></i></div>
          <div class="price-features text-justify mt-1">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining
            essentially unchanged. It was popularised in the 1960s.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-4 mt-15 mb-15">
        <div class="bg-white mt-3 price-box active pt-30 position-relative pink-border">
          <!-- VIEW OUR RECOMMENDED SECURITY BEST PRACTICES -->
          <span class="price-box-lable bg-custom">Three-prong Approach to security</span>
          <span class="price-border d-block bg-custom"></span>
          <div class="pricing-name text-center">
            <h5 class="mb-0 text-black">3-Prong Security</h5>
          </div>
          <div class="plan-price bg-custom text-white text-center mt-4 pt-3 pb-3"> <i class="fa fa-lock font-55"></i></div>
          <div class="price-features text-justify mt-1">
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End Our Pricing
================================================== -->

<!-- Start Contact us
================================================== -->
<section id="contact" class="section text-white" data-parallax data-speed="0.2" data-src="{{ asset('assets_ng/landing/images/banner/img-10.jpg')}}">
  <div class="bg-overlay bg-overlay1"></div>
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <!-- <h2 class="fw-3 mb-4">Contact us</h2> -->
        <h2 class="heading text-white mb-4">Contact us</h2>
      </div>

      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-sm-6 col-lg-3 mt-15 mb-15">
        <div class="text-center">
          <i class="fa fa-street-view font-45"></i>
          <h4 class="fw-3 mt-50 mb-20">OUR LOCATION</h4>
          <p>
            <a class="text-white" href="javascript:void(0);">
            F-314, F Block, Sector 63, Noida- 201301, Uttar Pradesh, India</a>
          </p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-15 mb-15">
        <div class="text-center"> <i class="fa fa-envelope-open font-45"></i>
          <h4 class="fw-3 mt-50 mb-20">DROP US A LINE</h4>
          <p>
            <a class="text-white" href="mailto:info@4bell.com">info@4bell.com</a><br>
            <a class="text-white" href="mailto:info@ats.com">info@ats.com</a>
          </p>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3 mt-15 mb-15">
        <div class="text-center"> <i class="fa fa-phone font-45"></i>
          <h4 class="fw-3 mt-50 mb-20">CALL US</h4>
          <p>
            <a class="text-white" href="tel:+91-9616890080">+91-9616890080</a><br>
            <a class="text-white" href="tel:+91-7217860087">+91-7217860087</a>
          </p>
        </div>

      </div>

      <div class="col-sm-6 col-lg-3 mt-15 mb-15">
        <div class="text-center"> <i class="fa fa-comments font-45"></i>
          <h4 class="fw-3 mt-50 mb-20">LIVE CHAT</h4>
          <p> <a class="text-white" href="javascript:void(0);">We are here to help you <br>
            24/7 Click here</a> </p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End Contact us
================================================== -->

<!-- Start Get in touch today
================================================== -->
<section id="Get-In-Touch" class="section bg-white">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-5">
      <div class="col-lg-12">
        <!-- <h2 class="text-black fw-3 mb-4">Get in touch today</h2> -->
        <h2 class="heading text-dark mb-4">Get in touch today</h2>
      </div>

      <div class="col-lg-6">
        <p class="font-15">It is a long established fact that a reader will be of a page when established fact looking at its layout.</p>
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-lg-4 mt-3 mb-3">
        <div class="w-100">
          <img class="img-fluid d-block mx-auto" src="{{ asset('assets_ng/landing/images/img-5.png')}}" alt="Get in touch today">
        </div>
      </div>

      <div class="col-lg-8">
        <form class="form needs-validation" method="post" action="javascript:void(0);">
          <div class="row">
            <div class="col-lg-12">
              <div class="messages"></div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <div class="position-relative">
                  <input class="form-control" type="text" name="name" placeholder="Your Name*" required="required" data-error="Your Name is required.">
                  <!-- <label><i class="fa fa-user"></i></label> -->
                </div>
                <div class="help-block with-errors text-danger"></div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <div class="position-relative">
                  <input class="form-control" type="email" id="contact_email" name="email" placeholder="Your Email*" required="required" data-error="Your Email is required." maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false">
                  <div id="contact_email_typo_hint" class="email-typo-hint" style="display: none; margin-top: 5px; font-size: 11.5px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 4px 8px;"></div>
                  <!-- <label><i class="fa fa-envelope-open"></i></label> -->
                </div>
                <div class="help-block with-errors text-danger"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <div class="position-relative">
                  <input class="form-control" type="number" name="phone" placeholder="Your Phone Number*" required="required" data-error="Your Number is required.">
                  <!-- <label><i class="fa fa-phone"></i></label> -->
                </div>
                <div class="help-block with-errors text-danger"></div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <div class="position-relative">
                  <input class="form-control form-control1" type="text" name="subject" placeholder="Your Subject*" required="required" data-error="Your Subject is required.">
                  <!-- <label><i class="fa fa-edit"></i></label> -->
                </div>
                <div class="help-block with-errors text-danger"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="form-group">
                <div class="position-relative">
                  <textarea class="form-control" name="message" placeholder="Your message*" rows="6"></textarea>
                  <!-- <label><i class="fa fa-clipboard"></i></label> -->
                </div>
                <div class="help-block with-errors text-danger"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <button class="btn btn-light-success btn-custom" type="submit">Send message&nbsp;<i class="far fa-paper-plane"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
<!-- End Get in touch today
================================================== -->

<!-- Start Footer
================================================== -->

<footer class="section bg-black text-white">
  <div class="container footer-shadow">
    <div class="row justify-content-center text-center mb-30">
      <div class="col-lg-12">
        <!-- <h2 class="fw-3">
          <span class="text-capitalize fw-3">Tallent
            <span class="fw-9 text-custom text-uppercase mily-text-shadow">MILY</span>
          </span>
        </h2> -->
        <h2 class="fw-3">
          <a class="navbar-brand d-flex align-items-center justify-content-center" href="javascript:void(0);">
            <span class="fw-3 text-capitalize">
              <img src="{{ asset('assets_ng/landing/images/Logo_white.png')}}" height="80px">
            </span>
          </a>
        </h2>
      </div>

      <div class="col-lg-6">
        <p>It seems that only fragments of the original text remain in the Lorem Ipsum texts used today. One may speculate</p>
      </div>
    </div>
    <div class="row text-center">
      <div class="col-lg-12">
        <p>2021© TalentMILY&nbsp;&nbsp;|&nbsp;&nbsp;Design & Developed By
          <a class="text-white" href="http://www.4bell.com">4Bell Technology</a>
        </p>
      </div>
    </div>

    <div class="row text-center mt-3">
      <div class="col-lg-12">
        <ul class="list-inline social-link social-light mb-0">
          <li class="list-inline-item"><a href="javascript:void(0);" class="social-hover fab fa-facebook-f"></a></li>
          <li class="list-inline-item"><a href="javascript:void(0);" class="social-hover fab fa-whatsapp"></a></li>
          <li class="list-inline-item"><a href="javascript:void(0);" class="social-hover fab fa-instagram"></a></li>
          <li class="list-inline-item"><a href="javascript:void(0);" class="social-hover fab fa-linkedin-in"></a></li>
          <li class="list-inline-item"><a href="javascript:void(0);" class="social-hover fab fa-youtube"></a></li>
          <li class="list-inline-item"><a href="javascript:void(0);" class="social-hover fab fa-google-plus-g"></a></li>
        </ul>
      </div>
    </div>
  </div>
</footer>
<!-- End Footer
================================================== -->

<!-- Modal Form -->
<!-- free_trail -->
<div class="modal fade" id="free_trail">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content form-wrapper">
      <div class="close-box" data-dismiss="modal"> <i class="fa fa-times"></i> </div>
      <div class="row height-90vh">
        <div class="col-md-5 bg-pink1">
          <h3 class="text-white pt-5 get-full">Get a full featured FREE TRAIL</h3>
          <p class="text-white padding-left1 w-75 text-justify pt-2">Get a free trial and you'll have access to the latest AI-powered acquisition platform.</p>
          <!-- <img class="img-style" src="<?php echo asset('assets_ng/img/3.png');?>" alt='img'> -->
          <img src="{{ asset('assets_ng/landing/images/Free-Trail.png')}}" alt="Free Trail image"> </div>
        <div class="col-md-7 bg-white">
          <div class="container-fluid mt-4">
            <form action="{{route('login')}}" method="post" id="LoginForm">
              <div class="form-group text-left">
                <!-- <h4 class="font-weight-normal">Registration Form</h4>
                <h6 class="font-weight-normal">Enter your credentials</h6> -->
              </div>
              <div class="form-group">
                <label class="mb-0" for="company_name" class="font-weight-700">
                <i class="fa fa-building"></i>&nbsp;Company Name
                </label>
                <input type="text" id="company_name" class="form-control input1" placeholder="Enter Your Company Name" required>
              </div>
              <div class="form-group">
                <label class="mb-0" for="full_name" class="font-weight-700">
                <i class="fa fa-user"></i>&nbsp;Full Name
                </label>
                <input type="text" id="full_name" class="form-control input1" placeholder="Enter Your Name" required>
              </div>
              <div class="form-group">
                <label class="mb-0" for="l_email" class="font-weight-700">
                <i class="fa fa-envelope"></i>&nbsp;Email
                </label>
                <input type="email" id="l_email" class="form-control mb-1 input1" placeholder="Enter Your Email" required>
              </div>
              <div class="form-group">
                <label class="mb-0" for="phone_number" class="font-weight-700">
                <i class="fa fa-phone"></i>&nbsp;Phone Number
                </label>
                <input type="number" id="phone_number" class="form-control mb-1 input1" placeholder="Enter Your Number" required minlength="4" maxlength="8" size="10">
              </div>
              <div class="form-group">
                <label class="mb-0 pb-3" for="designation" class="font-weight-700">
                <i class="fa fa-tag"></i>&nbsp;Designation
                </label>
                <select class="dropdown form-control" id="Designation">
                  <option>Select Your Designation</option>
                  <option>HR</option>
                  <option>Director</option>
                  <option>CEO</option>
                  <option>Others</option>
                </select>
              </div>

              <div class="form-group pt-2">
                <button class="btn btn-info form-control">
                <a href="javascript:void(0);">Try It FREE&nbsp;<i class="fa fa-arrow-right"></i></a>
                </button>
              </div>
              <!-- <div class="form-group text-center">
                  <a href="javascript:void(0)" class="font-weight-600">or Get a Guided Demo&nbsp;<i class="fa fa-play fa-sm"></i> </a>
              </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="login">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content form-wrapper">
      <div class="close-box" data-dismiss="modal"> <i class="fa fa-times"></i></div>
      <div class="row height-90vh">
        <div class="col-md-5 bg-pink1">
          <h3 class="text-white pt-5 font-weight-500">Welcome Back!</h3>
          <p class="text-white w-75 text-justify pt-2">Recruitment Management Software (RMS) for recruiters that makes recruitment unstoppable.</p>
          <!-- <img class="img-style" src="<?php echo asset('assets_ng/img/1.png');?>" alt='img'> -->
          <img src="{{ asset('assets_ng/landing/images/1.png')}}" alt="Login Form" class="img-style"> </div>
        <div class="col-md-7 bg-white">
          <div class="container-fluid mt-4">
            <form action="{{route('login')}}" method="post" id="LoginForm">
              <div class="form-group text-left">
                <h4 class="font-weight-normal">Login Form</h4>
                <h6 class="font-weight-normal">Enter your credentials</h6>
              </div>
              <div class="form-group">
                <label class="mb-0" for="l_email"><i class="fa fa-envelope"></i>&nbsp;Email</label>
                <input type="email" id="l_email" class="form-control mb-1 input1" placeholder="Ex: example@gmail.com" required="required" autocomplete="false">
              </div>
              <div class="form-group">
                <label class="mb-0" for="l_password"><i class="fa fa-key"></i>&nbsp;Password</label>
                <input type="password" id="l_password" class="form-control mb-1 input1" placeholder="********" required="required" autocomplete="false">
              </div>
              <div class="remember_section d-flex justify-content-between">
                <div class="form-check">
                  <!-- <input type="checkbox" class="form-check-input" id="exampleCheck1"> -->
                  <label class="main">
                  <input type="checkbox">
                  <span class="geekmark"></span></label>
                  <label class="form-check-label" for="exampleCheck1">Remember me</label>
                </div>
                <div class="">
                  <a href="#forgot" data-toggle="modal" class="font-weight-normal">Forgot Password?</a>
                </div>
              </div>
              <div class="form-group pt-4">
                <button class="btn btn-info form-control">
                  <a href="javascript:void(0);">Login&nbsp;<i class="fa fa-sign-in-alt"></i></a>
                </button>
              </div>
              <div class="form-group text-center pt-2 social-login">
                <h6 class="or">OR</h6>
              </div>
              <div class="form-group text-center">
                <a href="javascript:void(0)" class="font-weight-normal">New in ATS? Get a Free Trail</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Forgot Modal -->

<div class="modal fade" id="forgot">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content form-wrapper">
      <div class="close-box" data-dismiss="modal"> <i class="fa fa-times"></i> </div>
      <div class="row height-90vh">
        <div class="col-md-5 bg-pink1">
          <h3 class="text-white pt-5 font-weight-normal">Reset Password!</h3>
          <p class="text-white padding-left1 w-75 text-justify pt-2">If you've lost your password or wish reset it, use the link below to get started.</p>
          <!-- <img class="img-style" src="<?php echo asset('assets_ng/img/2.png');?>" alt='img'> -->
          <img src="{{ asset('assets_ng/landing/images/2.png')}}" alt="Login Form" class="img-style"> </div>
        <div class="col-md-7 bg-white">
          <div class="container-fluid mt-4">
            <form action="{{route('login')}}" method="post" id="LoginForm">
              <div class="form-group text-left">
                <h4 class="font-weight-normal">Forgot Password</h4>
                <h6 class="font-weight-normal">Enter your email and instructions will be sent to you!</h6>
              </div>
              <div class="form-group">
                <label class="mb-0" for="l_email"><i class="fa fa-envelope"></i>&nbsp;Email</label>
                <input type="email" id="l_email" class="form-control mb-1 input1" placeholder="Ex: example@gmail.com" required>
              </div>
              <div class="form-group pt-4">
                <button class="btn btn-info form-control">
                <a href="javascript:void(0);">Reset Password&nbsp;<i class="fa fa-retweet"></i></a>
                </button>
              </div>

              <div class="form-group text-center">
                <!-- <i class="fa fa-chevron-left fa-sm"></i> -->
                <a href="javascript:void(0);" class="font-weight-normal text-decoration-none">
                  <i class="fa fa-arrow-left fa-sm"></i>&nbsp;Login here</a>
                </div>
              <div class="form-group pt-4">
                <button class="btn btn-info1 form-control">
                  <a href="javascript:void(0);">
                    <i class="fa fa-globe"></i> Visit Front Website
                  </a>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Register Modal -->

<!-- <div class="modal fade" id="register">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content form-wrapper">
        <div class="close-box" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </div>
        <div class="row height-90vh">
            <div class="col-md-5 bg-pink1">
                <h3 class="text-white pt-5">Create an account</h3>
                <p class="text-white padding-left1 w-75 text-justify pt-2">Your can use any email address as the user name for your new ATS Account.</p>
                    <img src="{{ asset('assets_ng/landing/images/3.png')}}" alt="Register image">
            </div>
            <div class="col-md-7 bg-white">
                <div class="container-fluid mt-4">
                  <form action="{{route('login')}}" method="post" id="LoginForm">
                    <div class="form-group text-left">
                    </div>
                   <div class="form-group">
                    <label for="company_name"><i class="fa fa-building"></i>&nbsp;Company Name</label>
                    <input type="text" id="company_name" class="form-control input1" placeholder="Ex: 4Bell Technology" required>
                   </div>
                   <div class="form-group">
                    <label for="career_page_link"><i class="fa fa-link"></i>&nbsp;Website link</label>
                    <input type="text" id="career_page_link" class="form-control input1" placeholder="Ex: www.4bell.com" required>
                   </div>
                    <div class="form-group">
                      <label for="l_email"><i class="fa fa-envelope"></i>&nbsp;Email</label>
                      <input type="email" id="l_email" class="form-control mb-1 input1" placeholder="Ex: example@gmail.com" required>
                    </div>
                    <div class="form-row">
                      <div class="form-group col">
                      <label for="password_password"><i class="fa fa-key"></i>&nbsp;Password</label>
                      <input type="password" id="password_password" class="form-control mb-1 input1" placeholder="*******************" required>
                      </div>
                      <div class="form-group col">
                      <label for="confirm_password"><i class="fa fa-key"></i>&nbsp;Confirm Password</label>
                      <input type="password" id="confirm_password" class="form-control mb-1 input1" placeholder="*******************" required>
                      </div>
                    </div> 
                    <div class="remember_section d-flex justify-content-between">
                        <div class="form-check">
                            <label class="main">
                              <input type="checkbox"><span class="geekmark"></span>
                              </label>
                            <label class="form-check-label termsuse" for="exampleCheck1">By continuing, you agree to ATS's
                              <a href="javascript:void(0)">Terms of Use</a> and 
                              <a href="javascript:void(0)">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <div class="form-group pt-2">
                      <button class="btn btn-info form-control"><a href="javascript:void(0);">Register&nbsp;<i class="fa fa-user-plus"></i></a></button>
                    </div>
                    <div class="form-group text-center">
                      <a href="javascript:void(0)" class="font-weight-normal">Already Registered? Login</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
      </div>
    </div>
</div> -->
<!-- Modal Form End -->

<!-- Start Back to Top
================================================== -->
<a href="javascript:void(0);" class="back-to-top bg-pink text-white" id="back-to-top"><span class="fa fa-arrow-up"></span></a>
<!-- End Back to Top
================================================== -->

<script>
// =========================================================================
// JeroEmailBoxEngine: 100% Client-First (Zero Server Strain) Email Engine
// RFC 5321 (254 char), Deterministic Sanitizer, 3-State Typo Detection
// =========================================================================
window.JeroEmailBoxEngine = window.JeroEmailBoxEngine || (function () {
    var CONSUMER_DOMAIN_TYPOS = {
        'gmai.com': 'gmail.com', 'gamil.com': 'gmail.com', 'gmial.com': 'gmail.com',
        'gmaill.com': 'gmail.com', 'gmaik.com': 'gmail.com', 'gmeil.com': 'gmail.com',
        'gmaul.com': 'gmail.com', 'gmal.com': 'gmail.com', 'gmaili.com': 'gmail.com',
        'yaho.com': 'yahoo.com', 'yahooo.com': 'yahoo.com', 'ymail.com': 'yahoo.com',
        'outlok.com': 'outlook.com', 'outloo.com': 'outlook.com',
        'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com',
        'iclou.com': 'icloud.com', 'iclod.com': 'icloud.com',
        'redifmail.com': 'rediffmail.com', 'rediffmial.com': 'rediffmail.com'
    };

    var CONSUMER_TLD_TYPOS = {
        'con': 'com', 'cmo': 'com', 'ocm': 'com', 'cpm': 'com',
        'comm': 'com', 'coom': 'com', 'ent': 'net', 'og': 'org', 'ogr': 'org'
    };

    return {
        cleanDeterministic: function (raw) {
            if (!raw) return '';
            var str = String(raw).trim();
            str = str.replace(/[\u00A0\u1680\u180E\u2000-\u200B\u202F\u205F\u3000\uFEFF]/g, '');
            str = str.replace(/^['"`<]+|['"`>]+$/g, '');
            str = str.replace(/^mailto:\s*/i, '');
            var match = str.match(/<([^>]+)>/);
            if (match && match[1]) {
                str = match[1];
            }
            str = str.replace(/[.,;:]+$/, '');
            return str.toLowerCase().replace(/\s+/g, '');
        },

        getSuggestion: function (email) {
            if (!email || email.indexOf('@') === -1) return null;
            var parts = email.split('@');
            if (parts.length !== 2) return null;

            var local = parts[0];
            var domain = parts[1];

            if (domain.indexOf(',') !== -1) {
                var commaFixed = domain.replace(/,([a-zA-Z]{2,})$/, '.$1');
                return {
                    suggested: local + '@' + commaFixed,
                    message: 'Did you mean <strong>' + local + '@' + commaFixed + '</strong>?'
                };
            }

            if (CONSUMER_DOMAIN_TYPOS[domain]) {
                var fixedEmail = local + '@' + CONSUMER_DOMAIN_TYPOS[domain];
                return {
                    suggested: fixedEmail,
                    message: 'Did you mean <strong>' + fixedEmail + '</strong>?'
                };
            }

            var dParts = domain.split('.');
            if (dParts.length >= 2) {
                var root = dParts[0];
                var tld = dParts[dParts.length - 1];
                if (CONSUMER_TLD_TYPOS[tld] && (root === 'gmail' || root === 'yahoo' || root === 'outlook' || root === 'hotmail')) {
                    dParts[dParts.length - 1] = CONSUMER_TLD_TYPOS[tld];
                    var fixed = local + '@' + dParts.join('.');
                    return {
                        suggested: fixed,
                        message: 'Did you mean <strong>' + fixed + '</strong>?'
                    };
                }
            }

            return null;
        },

        validateSyntax: function (email) {
            if (!email || email.trim() === '') {
                return { isValid: false, error: 'Email address is required.' };
            }

            if (email.length > 254) {
                return { isValid: false, error: 'Email exceeds maximum allowed length of 254 characters.' };
            }

            if (/\s/.test(email)) {
                return { isValid: false, error: 'Email address cannot contain spaces.' };
            }

            if (email.indexOf(',') !== -1) {
                if (email.indexOf('@') !== -1 && email.split('@')[1].indexOf(',') !== -1) {
                    return { isValid: false, error: 'Domain name contains a comma instead of a period (e.g. gmail,com).' };
                }
                return { isValid: false, error: 'Email address cannot contain commas.' };
            }

            var parts = email.split('@');
            if (parts.length < 2) {
                return { isValid: false, error: 'Email is missing the "@" symbol.' };
            }
            if (parts.length > 2) {
                return { isValid: false, error: 'Email contains more than one "@" symbol.' };
            }

            var local = parts[0];
            var domain = parts[1];

            if (local.length === 0) {
                return { isValid: false, error: 'Email username cannot be empty.' };
            }
            if (local.length > 64) {
                return { isValid: false, error: 'Email username cannot exceed 64 characters.' };
            }

            if (local.indexOf('..') !== -1 || domain.indexOf('..') !== -1) {
                return { isValid: false, error: 'Email cannot contain consecutive dots ("..").' };
            }

            if (local.startsWith('.') || local.endsWith('.')) {
                return { isValid: false, error: 'Email username cannot start or end with a dot.' };
            }
            if (domain.startsWith('.') || domain.endsWith('.')) {
                return { isValid: false, error: 'Domain name cannot start or end with a dot.' };
            }

            if (domain.indexOf('_') !== -1) {
                return { isValid: false, error: 'Domain name cannot contain underscores (use hyphens instead).' };
            }

            if (!/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/.test(domain)) {
                return { isValid: false, error: 'Domain name format is invalid (letters, numbers, and hyphens only).' };
            }

            var tld = domain.split('.').pop();
            if (!tld || tld.length < 2 || /^\d+$/.test(tld)) {
                return { isValid: false, error: 'Domain must end with a valid extension (e.g. .com, .org).' };
            }

            return { isValid: true, error: null };
        }
    };
})();

     var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html) {
    var switchery = new Switchery(html, { size: 'medium' });
    });

    // Paste & Blur Handlers for Email Inputs
    $(document).on('paste', '#company_email, #contact_email', function () {
        var self = this;
        setTimeout(function () {
            var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic($(self).val()) : $(self).val().trim();
            if (clean !== $(self).val()) {
                $(self).val(clean).trigger('input');
            }
        }, 10);
    });

    $(document).on('blur', '#company_email, #contact_email', function () {
        var raw = $(this).val();
        var clean = window.JeroEmailBoxEngine ? window.JeroEmailBoxEngine.cleanDeterministic(raw) : raw.trim();
        if (clean !== raw) {
            $(this).val(clean);
        }
        var targetId = $(this).attr('id');
        var $hint = $('#' + targetId + '_typo_hint');
        if (!clean) {
            $hint.hide().empty();
            return;
        }
        if (window.JeroEmailBoxEngine) {
            var syntax = window.JeroEmailBoxEngine.validateSyntax(clean);
            if (!syntax.isValid) {
                $hint.html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                return;
            }
            var suggestion = window.JeroEmailBoxEngine.getSuggestion(clean);
            if (suggestion) {
                $hint.html('<i class="fa fa-lightbulb-o text-warning"></i> ' + suggestion.message + ' <a href="javascript:void(0);" class="apply-email-fix font-weight-bold text-primary ml-1" data-target="#' + targetId + '" data-fixed="' + suggestion.suggested + '">Apply</a>').show();
            } else {
                $hint.hide().empty();
            }
        }
    });

    $(document).on('click', '.apply-email-fix', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        var fixed = $(this).data('fixed');
        if (target && fixed) {
            $(target).val(fixed).trigger('input').trigger('change');
            $(this).closest('.email-typo-hint').hide().empty();
        }
    });

    $('#save-form').click(function (e) {
        var contactEmail = $('#contact_email').val();
        if (contactEmail && window.JeroEmailBoxEngine) {
            var syntax = window.JeroEmailBoxEngine.validateSyntax(contactEmail.trim());
            if (!syntax.isValid) {
                $('#contact_email_typo_hint').html('<i class="fa fa-exclamation-circle text-danger"></i> ' + syntax.error).show();
                swal("Invalid Email", syntax.error, "error");
                return false;
            }
        }
        @if(!is_null($global->google_recaptcha_key))
            if(grecaptcha.getResponse().length == 0){
                e.preventDefault();
                alert('Please click the reCAPTCHA checkbox');
                return false;
            }
        @endif

        $.easyAjax({
            url: '{{route('contact')}}',
            container: '#createForm',
            type: "POST",
            data: $('#createForm').serialize(),
            success: function(response) {
                $('#createForm').trigger("reset");
                swal("Sent!", "We will contact you soon!", "success");
        },
        
        error: function (response) {
          swal("Error!", "Your need to fill all the form fields!", "error");
          }
        })
    });
</script>
<!-- <script src="{{ asset('saas-front/js/jquery-3.1.1.min.js') }}"></script> -->
<script src="{{ asset('assets_ng/landing/js/plugins.js')}}"></script>

<!-- Plugins JS -->
<script src="{{ asset('assets_ng/landing/js/app.js')}}"></script>

<!-- Custom JS -->
</body>
</html>

