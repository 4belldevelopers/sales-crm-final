
@extends('layouts.metronic_front_app')
<!--begin::Page title-->
<title>Meecruit - Login</title>
<!--end::Page title-->


@section('content')
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-37564768-1');
</script>
<!--end::Google tag-->        
        <script>
            // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
            if (window.top != window.self) {
                window.top.location.replace(window.self.location.href);
            }
        </script>
    </head>
    <!--end::Head-->

    <!--begin::Body-->
    <body  id="kt_body"  class="app-blank bgi-size-cover bgi-position-center bgi-no-repeat" >
        <!--begin::Theme mode setup on page load-->
<script>
	var defaultThemeMode = "light";
	var themeMode;

	if ( document.documentElement ) {
		if ( document.documentElement.hasAttribute("data-bs-theme-mode")) {
			themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
		} else {
			if ( localStorage.getItem("data-bs-theme") !== null ) {
				themeMode = localStorage.getItem("data-bs-theme");
			} else {
				themeMode = defaultThemeMode;
			}			
		}

		if (themeMode === "system") {
			themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
		}

		document.documentElement.setAttribute("data-bs-theme", themeMode);
	}            
</script>
<!--end::Theme mode setup on page load-->
                    <!--Begin::Google Tag Manager (noscript) -->
<!--End::Google Tag Manager (noscript) -->
        
        <!--begin::Root-->
<div class="d-flex flex-column flex-root" id="kt_app_root">
    <!--begin::Page bg image-->
<!--end::Page bg image-->


<!--begin::Authentication - Signup Welcome Message -->
<div class="d-flex flex-column flex-center flex-column-fluid">    
    <!--begin::Content-->
    <div class="d-flex flex-column flex-center text-center p-10">        
        <!--begin::Wrapper-->
        <div class="card card-flush w-lg-650px py-5">
            <div class="card-body py-15 py-lg-20">
                
    <!--begin::Title-->
    <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">
        Oops!
    </h1>
    <!--end::Title--> 
    
    <!--begin::Text-->
    <div class="fw-semibold fs-6 text-gray-500 mb-7">
        We can't find that page.
    </div>
    <!--end::Text--> 

    <!--begin::Illustration-->
    <div class="mb-3">
        <img src="{{ asset('404-error.png')}}" class="mw-100 mh-300px theme-light-show" alt=""/>
    </div>
    <!--end::Illustration-->
    
    <!--begin::Link-->
    <div class="mb-0">
        <a href="{{ url('/')}}/login" class="btn btn-sm btn-primary">Return Home</a>
    </div>    
    <!--end::Link-->

            </div>
        </div>
        <!--end::Wrapper-->        
    </div>
    <!--end::Content-->    
</div>
<!--end::Authentication - Signup Welcome Message-->
                         </div>
<!--end::Root-->
        