<!DOCTYPE html>
<html lang="en">
<head>
<base href="../../../">
<title>Setup New Password</title>
<meta charset="utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="" />
<meta property="og:url" content="" />
<meta property="og:site_name" content="" />
<link rel="canonical" href="" />
<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
<link href="{{ asset('demo/v8/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/v8/style.bundle.css')}}" rel="stylesheet" type="text/css" />
</head>
<body data-kt-name="metronic" id="kt_body" class="app-blank app-blank">
<script>if ( document.documentElement ) { const defaultThemeMode = "system"; const name = document.body.getAttribute("data-kt-name"); let themeMode = localStorage.getItem("kt_" + ( name !== null ? name + "_" : "" ) + "theme_mode_value"); if ( themeMode === null ) { if ( defaultThemeMode === "system" ) { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } else { themeMode = defaultThemeMode; } } document.documentElement.setAttribute("data-theme", themeMode); }</script>
<div class="d-flex flex-column flex-root" id="kt_app_root">
  <div class="d-flex flex-column flex-lg-row flex-column-fluid">
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
      <div class="d-flex flex-center flex-column flex-lg-row-fluid">
        <div class="w-lg-500px p-10">
        <!--  <form class="form w-100" novalidate="novalidate" id="kt_new_password_form" data-kt-redirect-url="../../demo1/dist/authentication/layouts/corporate/sign-in.html" action="#">-->


           
 <!--<form class="form w-100" novalidate="novalidate" id="kt_new_password_form" method="POST" action="{{ route('password.request') }}">-->
 <form class="form w-100" novalidate="novalidate" id="kt_new_password_form" method="POST" action="{{ route('ResetPasswordStore') }}">
 
@if($errors->any())
	<h4 style="color:#FF0000; text-align:center">{{$errors->first()}}</h4>
@endif

        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
		<input type="hidden" name="encEmail" value="<?php echo base64_encode($email); ?>" >
		
		<!--<input id="email" type="input" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" >-->
				   
				   
		    <div class="text-center mb-10">
              <h1 class="text-dark fw-bolder mb-3">Setup New Password</h1>
              <div class="text-gray-500 fw-semibold fs-6">Have you already reset the password ? <a href="{{ route('login') }}" class="link-primary fw-bold">Sign in</a></div>
            </div>
            <div class="fv-row mb-8" data-kt-password-meter="true">
              <div class="mb-1">
                <div class="position-relative mb-3">
                  <!--<input class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off" />-->
                  <input id="password" type="password" class="form-control bg-transparent{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" autocomplete="off" required>
					@if ($errors->has('password'))
						<span class="invalid-feedback">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
						  
				  <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility"> <i class="bi bi-eye-slash fs-2"></i> <i class="bi bi-eye fs-2 d-none"></i> </span> </div>
                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                  <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                </div>
              </div>
              <div class="text-muted">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</div>
            </div>
            <div class="fv-row mb-8">
              <!--<input type="password" placeholder="Repeat Password" name="confirm-password" autocomplete="off" class="form-control bg-transparent" />-->
			  <input id="password-confirm" type="password" class="form-control bg-transparent" name="password_confirmation" placeholder="Repeat Password" autocomplete="off"  required>
			  
            </div>
            <div class="fv-row mb-8">
              <label class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="toc" value="1" />
              <span class="form-check-label fw-semibold text-gray-700 fs-6 ms-1">I Agree &amp; <a href="{{ route('termsAndConditions') }}" class="ms-1 link-primary">Terms and conditions</a>.</span> </label>
            </div>
            <div class="d-grid mb-10">
				  <button type="button" id="kt_new_password_submit" class="btn btn-primary"> 
						<span class="indicator-label">Submit</span> 
						<span class="indicator-progress">Please wait... 
							<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
						</span> 
				  </button>
            </div>
			
			
			
          </form>
        </div>
      </div>
      <div class="d-flex flex-center flex-wrap px-5">
        <div class="d-flex fw-semibold text-primary fs-base"> 
			<a href="{{ route('termsAndConditions') }}" class="px-5" >Terms</a> 
			<a href="{{ route('termsAndConditions') }}" class="px-5" >Plans</a> 
			<a href="{{ route('termsAndConditions') }}" class="px-5" >Contact Us</a>  
		</div>
      </div>
    </div>
    <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-image: url({{ asset('demo/v8/auth-bg.png')}})">
      <div class="d-flex flex-column flex-center py-15 px-5 px-md-15 w-100"> 
	  <a href="#" class="mb-12"> <img alt="Logo" src="{{ asset('img/logo-transparent-white.svg') }}" class="h-75px" /> </a> 
	  <img class="mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20" src="{{ asset('demo/v8/auth-screens.png')}}" alt="" />
        <h1 class="text-white fs-2qx fw-bolder text-center mb-7">Fast, Efficient and Productive</h1>
        
      </div>
    </div>
  </div>
</div>
<script>var hostUrl = "assets/";</script>
<script src="{{ asset('demo/v8/plugins.bundle.js')}}"></script>
<script src="{{ asset('demo/v8/scripts.bundle.js')}}"></script>
<!--<script src="{{ asset('demo/v8/new-password.js')}}"></script>-->

<!--callback: {-->
<!--								message: "Please enter valid password",-->
<!--								callback: function(t) {-->
<!--									if (t.value.length > 0) return a()-->
<!--								}-->
<!--							}-->

<script>

"use strict";
var KTAuthNewPassword = function() {
	var t, e, r, o, a = function() {
		return 100 === o.getScore()
	};
	return {
		init: function() {
			t = document.querySelector("#kt_new_password_form"), e = document.querySelector("#kt_new_password_submit"), o = KTPasswordMeter.getInstance(t.querySelector('[data-kt-password-meter="true"]')), r = FormValidation.formValidation(t, {
				fields: {
					password: {
						validators: {
							notEmpty: {
								message: "The password is required"
							},
					
						}
					},
					"password_confirmation": {
						validators: {
							notEmpty: {
								message: "The password confirmation is required"
							},
							identical: {
								compare: function() {
									return t.querySelector('[name="password"]').value
								},
								message: "The password and its confirm are not the same"
							}
						}
					},
					toc: {
						validators: {
							notEmpty: {
								message: "You must accept the terms and conditions"
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger({
						event: {
							password: !1
						}
					}),
					bootstrap: new FormValidation.plugins.Bootstrap5({
						rowSelector: ".fv-row",
						eleInvalidClass: "",
						eleValidClass: ""
					})
				}
			}), e.addEventListener("click", (function(a) {
				a.preventDefault(), r.revalidateField("password"), r.validate().then((function(r) {
					"Valid" == r ? (e.setAttribute("data-kt-indicator", "on"), e.disabled = !0, setTimeout((function() {
						
						
						/*e.removeAttribute("data-kt-indicator"), e.disabled = !1, Swal.fire({
							text: "You have successfully reset your password!",
							icon: "success",
							buttonsStyling: !1,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						}).then((function(e) {
							if (e.isConfirmed) {
								t.querySelector('[name="password"]').value = "", t.querySelector('[name="confirm-password"]').value = "", o.reset();
								var r = t.getAttribute("data-kt-redirect-url");
								r && (location.href = r)
							}
						}))*/
						
						$('#kt_new_password_form').submit();
						
						
						
					}), 1500)) : Swal.fire({
						text: "Sorry, looks like there are some errors detected, please try again.",
						icon: "error",
						buttonsStyling: !1,
						confirmButtonText: "Ok, got it!",
						customClass: {
							confirmButton: "btn btn-primary"
						}
					})
				}))
			})), t.querySelector('input[name="password"]').addEventListener("input", (function() {
				this.value.length > 0 && r.updateFieldStatus("password", "NotValidated")
			}))
		}
	}
}();
KTUtil.onDOMContentLoaded((function() {
	KTAuthNewPassword.init()
}));


</script>


</body>
</html>
