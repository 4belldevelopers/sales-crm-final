@extends('layouts.metronic_front_app')
@php 
   use App\Helper\GeneralMethod;
@endphp
<!--begin::Page title-->
<title>Jero - Login</title>
<!--end::Page title-->


@section('content')
<?php 



if(session('status')){
   $resetForm	=	'';
   $loginForm	=	'display:none';
}
else{
 	$resetForm	=	'display:none';
    $loginForm	=	'';	
}

/*if (@$errors->has('email'))
{
	$resetForm	=	'display:none';
    $loginForm	=	'';	
}
else
{
	$resetForm	=	'';
    $loginForm	=	'display:none';	
} 
*/

?>
<!--begin::Left section-->
<div class="d-flex flex-column flex-lg-row-auto w-xl-600px positon-xl-relative" style="background-color: #F2C98A">
  <!--begin::Wrapper-->
  <div class="d-flex flex-column position-xl-fixed top-0 bottom-0 w-xl-600px scroll-y">
    <!--begin::Content-->
    <div class="d-flex flex-row-fluid flex-column text-center p-10 pt-lg-20">
      <!--begin::Logo-->
      <a href="{{ url('/')}}" class="py-9">
        <img alt="Logo" src="{{ asset('img/Icon-meecruit.png')}}" class="h-90px">
      </a>
      <!--end::Logo-->
      <!--begin::Title-->
      <h1 class="fw-bolder fs-2qx pb-3 pb-md-3" style="color:#986923;">Welcome to JERO</h1>
      <!--end::Title-->
      <!--begin::Description-->
      <p class="fw-bold fs-2" style="color:#986923;">Discover Amazing tools for Recruitment</p>
      <!--end::Description-->
    </div>
    <!--end::Content-->
    <!--begin::Illustration-->
    <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px" style="background-image: url({{ asset('metronic_assets/media/sketchy-1.png') }})"></div>
    <!--end::Illustration-->
  </div>
  <!--end::Wrapper-->
</div>
<!--end::Left section-->
<!--begin::Right section-->
<div class="d-flex flex-column flex-lg-row-fluid py-10">
  <!--begin::Content-->
  <div class="d-flex flex-center flex-column flex-column-fluid">
    <div class="w-lg-500px p-10 p-lg-15 mx-auto"> 
      <!--begin::Form-->
        <!--<begin::Login Form>-->
        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form1" action="{{ route('login') }}" method="post" style="<?php echo $loginForm; ?>">
          @csrf
          <!--begin::Heading-->
          <div class="text-center mb-10">
           
            <div class="alert alert-{{ @$class }} alert-dismissible fade show" role="alert">
              <strong> {{@$class }}</strong>  {!! @$messsage !!}
            </div>
            <h1 class="text-dark mb-3">Sign In to JERO</h1>
            
          </div>
          <!--begin::Heading-->
          <!--begin::Input group-->
          <div class="fv-row mb-10">
            <label class="form-label fs-6 fw-bolder text-dark">Email</label>
            <input class="form-control form-control-lg form-control-solid" type="text" name="email" autocomplete="off" />
              @if ($errors->has('email'))
                <span class="invalid-feedback">{{ $errors->first('email') }}</span>
              @endif
            <!--end::Input-->
          </div>
		  		
          <!--end::Input group-->
          <!--begin::Input group-->
          <div class="fv-row mb-10">
            <div class="d-flex flex-stack mb-2">
              <label class="form-label fw-bolder text-dark fs-6 mb-0">Code</label>
            </div>
            <input value="{{ GeneralMethod::value_encrypt('SUPERCODE') }}" type="hidden" name="super_code" autocomplete="off" />
            <input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" />
              @if ($errors->has('password'))
                <span class="invalid-feedback">
                  <strong>{{ $errors->first('password') }}</strong>
              </span>
              @endif
          </div>
        @if (session('accountnotverify'))
          <div class="alert alert-danger">
            {{ session('accountnotverify') }}
          </div>
		   	@endif
          <!--end::Input group-->
          @if ($errors->has('device_message'))
              <div class="alert alert-danger">
                  <ul>
                      <li>{{ $errors->first('device_message') }}</li>
                  </ul>
              </div>
          @endif
          <!--begin::Submit button-->
          <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
            <span class="indicator-label">Login</span>
            <span class="indicator-progress">Please wait...
              <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
          </button>
          <!--end::Submit button-->
          <!--begin::Actions-->
          <div class="text-center" style="display:none;">
            <!--begin::Separator-->
            <div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div>
            <!--end::Separator-->
            <!--begin::Google link-->
            <a href="javascript:void(0);" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
              <img alt="Logo" src="{{ asset('metronic_assets/media/svg/brand-logos/google-icon.svg') }}" class="h-20px me-3">Continue with Google
            </a>
            <!--end::Google link-->
            <!--begin::Google link-->
            <a href="javascript:void(0);" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
              <img alt="Logo" src="{{ asset('metronic_assets/media/svg/brand-logos/facebook-4.svg') }}" class="h-20px me-3">Continue with Facebook
            </a>
            <!--end::Google link-->
            <!--begin::Google link-->
            <a href="javascript:void(0);" class="btn btn-flex flex-center btn-light btn-lg w-100">
              <img alt="Logo" src="{{ asset('metronic_assets/media/svg/brand-logos/apple-black.svg') }}" class="h-20px me-3">Continue with Apple
            </a>
            <!--end::Google link-->
          </div>
          <!--end::Actions-->
        </form>
        <!--end::Login Form-->

        <!--begin::Forgot Password Form-->
        <form class="form w-100" novalidate="novalidate" id="recoverform" action="{{ route('password.emailsend') }}" method="post" style="<?php echo $resetForm; ?>">	
          {{ csrf_field() }}
          <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Forgot Password ?</h1>
            <div class="text-gray-400 fw-bold fs-4">Enter your email to reset your password.</div>
          </div>
		  
			@if (session('status'))
				<div class="alert alert-success">
					{{ session('status') }}
				</div>
			@endif
				@if (session('accountnotverify'))
				<div class="alert alert-danger">
					{{ session('accountnotverify') }}
				</div>
			@endif
					
          <div class="fv-row mb-10 {{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="form-label fs-6 fw-bolder text-dark">Email</label>
            <input type="email" name="email" id="email" class="form-control form-control-lg form-control-solid {{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Enter your email" value="{{ old('email') }}" required="required" autocomplete="off">
			
			@if ($errors->has('email'))
				<span class="invalid-feedback">	<strong>{{ $errors->first('email') }}	</strong>	</span>
			@endif
								
          </div>
          <!--begin::Action-->
          <div class="d-flex flex-wrap justify-content-center pb-lg-0">
              <button type="submit" id="reset_password" class="btn btn-lg btn-primary fw-bolder me-4 login-btn">
                <span class="indicator-label">Reset Password</span>
                <span class="indicator-progress">Please wait... 
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
              </button>
              <a href="{{ route('login') }}" class="btn btn-lg btn-light-primary fw-bolder">Cancel</a>
            </div>
          <!--end::Action-->
          
        </form>
        <!--end::Forgot Password Form-->
      <!--end::Form-->	  
    </div>
  </div>
  <!--end::Content-->

  <!--begin::Footer-->
  <div class="d-flex flex-center flex-wrap fs-6 p-5 pb-0">
    <div class="d-flex flex-center fw-bold fs-6">
      <a href="javascript:void(0);" class="text-muted text-hover-primary px-2">About</a>
      <a href="javascript:void(0);" class="text-muted text-hover-primary px-2">Support</a>
      <a href="javascript:void(0);" class="text-muted text-hover-primary px-2">Purchase</a>
    </div>
  </div>
  <!--end::Footer-->
</div>
<!--end::Right section-->


@endsection