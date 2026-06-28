<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets_ng/landing/images/favicon-16x161.png') }}"><!-- Favicon Icon -->

    <meta name="theme-color" content="#ffffff">

    <title>Talentmily | Login</title>

    <style>
        :root {
            --main-color: {{ $frontTheme->primary_color }};
        }
    </style>
    <!-- page css -->
    <link href="{{ asset('assets/dist/css/adminlte.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">
    <link href="{{ asset('assets/plugins/iCheck/square/blue.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<script src="https://www.google.com/recaptcha/api.js"></script>

</head>

<body class="hold-transition login-page" style="background-image:url({{ asset('assets/images/background/auth1.jpg') }})">

<div class="login-box" >
    <div class="card border-radius-pink">
        <div class="card-body login-card-body bg-transparent">
             @yield('content')
        </div>
    </div>
</div>


<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{ asset('assets/node_modules/jquery/jquery-3.2.1.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ asset('assets/node_modules/popper/popper.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/bootstrap/dist/js/bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!--Custom JavaScript -->
<script type="text/javascript">
    $(function() {
        // $(".preloader").fadeOut();
    });
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $('#to-recover').on("click", function() {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });

    $('#save-form').click(function (e) {
        @if(!is_null($setting->google_recaptcha_key))
            if(grecaptcha.getResponse().length == 0){
                e.preventDefault();
                alert('Please click the reCAPTCHA checkbox');
            }
        @endif
    });
</script>

</body>

</html>