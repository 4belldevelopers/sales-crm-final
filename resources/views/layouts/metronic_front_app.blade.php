<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="shortcut icon" href="{{ asset('favicon/ms-icon-144x144.png') }}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
		<link rel="shortcut icon" type="image/x-icon"  href="{{ asset('img/favicon.svg')}}">
		<title>
			<?php 
					/*if($pageTitle != ''){
						echo $pageTitle;
					}else{
						echo 'Meecruit';	
					}*/
			?>
			JERO
		</title>	  
		<link href="{{ asset('metronic_assets/plugins/global/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css">
		<link href="{{ asset('metronic_assets/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css">
	</head>
	<body id="kt_body" class="bg-body">
		<div class="d-flex flex-column flex-root">
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				@yield('content')
			</div>
		</div>		
		<script src="{{ asset('metronic_assets/plugins/global/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
		<script src="{{ asset('metronic_assets/js/scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>

		<!--begin::Forgot Validation-->
		<!--<script src="{{ asset('demo/js/custom/authentication/password-reset/password-reset.js') }}"></script>-->
		<script src="{{ asset('demo/js/custom/authentication/sign-in/general.js') }}"></script>
		<!--end::Forgot Validation-->

		<script type="text/javascript">
			$(function()
			{
				// $(".preloader").fadeOut();
			});
			$(function()
			{
				$('[data-toggle="tooltip"]').tooltip()
			});

			$('#to-recover').on("click", function()
			{
				$("#kt_sign_in_form1").slideUp();
				$("#recoverform").fadeIn();
			});


			// $('#to-recover').on("click", function()
			// {
			// 	$("#kt_sign_in_form1").slideUp();
			// 	$("#recoverform").slideUp();
			// });


			$('#save-form').click(function (e) {
			});
		</script>
	</body>
</html>