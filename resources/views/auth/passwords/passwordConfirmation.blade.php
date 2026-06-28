<!DOCTYPE html>
<html lang="en">
	
	<head><base href="../../">
		<title></title>
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
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
				
		<link href="{{ asset('demo/v8/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('demo/v8/style.bundle.css')}}" rel="stylesheet" type="text/css" />

	</head>
	
	<body data-kt-name="metronic" id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
		<!--begin::Theme mode setup on page load-->
		<script>if ( document.documentElement ) { const defaultThemeMode = "system"; const name = document.body.getAttribute("data-kt-name"); let themeMode = localStorage.getItem("kt_" + ( name !== null ? name + "_" : "" ) + "theme_mode_value"); if ( themeMode === null ) { if ( defaultThemeMode === "system" ) { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } else { themeMode = defaultThemeMode; } } document.documentElement.setAttribute("data-theme", themeMode); }</script>
		
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			
			<style>body { background-image: url('{{ asset('demo/v8/bg6.jpg')}}'); } [data-theme="dark"] body { background-image: url('{{ asset('demo/v8/bg6-dark.jpg')}}'); }</style>
			
			<div class="d-flex flex-column flex-center flex-column-fluid">
				
				<div class="d-flex flex-column flex-center text-center p-10">
					
					<div class="card card-flush w-lg-650px py-5">
						<div class="card-body py-15 py-lg-20">
							
							<div class="mb-14">
								<a href="../../demo1/dist/index.html" class="">
									<img alt="Logo" src="{{ asset('img/Icon-meecruit.svg')}}" class="h-90px" />
								</a>
							</div>
							
							<h1 class="fw-bolder text-gray-900 mb-5">Your password has been successfully changed</h1>
							
							<div class="fs-6 fw-semibold text-gray-500 mb-10 d-none">This is your opportunity to get creative
							<a href="#" class="link-primary fw-semibold">{{Session::get('set_email')}}</a>
							<br />that gives readers an idea</div>
							
							<div class="mb-11">
								<a href="{{route('login')}}" class="btn btn-sm btn-primary">Sign in</a>
							</div>
							
							<div class="mb-0">
								<img src="{{asset('demo/v8/ok.png')}}" class="mw-100 mh-300px theme-light-show" alt="" />
								<img src="{{asset('demo/v8/ok-dark.png')}}" class="mw-100 mh-300px theme-dark-show" alt="" />
							</div>
							
						</div>
					</div>
					
				</div>
				
			</div>
			
		</div>
		
		
		
		<script>var hostUrl = "assets/";</script>
		<script src="{{ asset('demo/v8/plugins.bundle.js')}}"></script>
		<script src="{{ asset('demo/v8/scripts.bundle.js')}}"></script>

	</body>
	
</html>