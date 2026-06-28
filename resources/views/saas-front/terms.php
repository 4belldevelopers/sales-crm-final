@extends('layouts.saas-front')
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Talentmily | Terms</title>
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
            <i class="fa fa-sign-in-alt" aria-hidden="true"></i>&nbsp;@lang('app.login')</a>
          </li>
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


<!-- Terms
================================================== -->
<section id="terms" class="section pb-0 ">
  <div class="container">
    <div class="row justify-content-center align-items-center text-center mb-2">
      <div class="col-lg-12">
        <h2 class="heading text-dark">Terms</h2>
      </div>
    </div>
  </div>
</section>
<!-- Terms End
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
     var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html) {
    var switchery = new Switchery(html, { size: 'medium' });
    });
    $('#save-form').click(function (e) {
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

