<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Favicon icon -->
<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
<meta name="theme-color" content="#ffffff">
<title>@if (auth()->user()->is_superadmin === 1)
        @lang('app.superAdminPanel')
    @else
        @lang('app.adminPanel')
    @endif | {{ __($pageTitle) }}
</title> 

<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<!-- Simple line icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css">

<!-- Themify icons -->
<link rel="stylesheet" href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}<?php echo TM_VERSION; ?>">

<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

<!-- Theme style -->
<link href="{{ asset('froiden-helper/helper.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">
<link href="{{ asset('assets/node_modules/toast-master/css/jquery.toast.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">
<link href="{{ asset('assets/node_modules/sweetalert/sweetalert.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">
<link rel='stylesheet prefetch' href='https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/css/bootstrap-select.min.css'>
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}<?php echo TM_VERSION; ?>">
<link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}<?php echo TM_VERSION; ?>">
<link rel="stylesheet" href="{{ asset('assets/node_modules/Magnific-Popup-master/dist/magnific-popup.css') }}<?php echo TM_VERSION; ?>">

@stack('head-script')

<link rel="stylesheet" href='//cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
<link href="{{ asset('css/custom.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet">


<style>
:root
{
    --main-color: {{ $adminTheme->primary_color }};
}
.notify .point
{
    background-color: var(--main-color);
}
.notify .heartbit
{
    border-color: var(--main-color);
}
.well, pre
{
    background: #fff;
    border-radius: 0;
}
.btn-group-xs > .btn, .btn-xs
{
    padding  : .25rem .4rem;
    font-size  : .875rem;
    line-height  : .5;
    border-radius : .2rem;
}
.btn-circle
{
    width: 30px;
    height: 30px;
    padding: 6px 0;
    border-radius: 15px;
    text-align: center;
    font-size: 12px;
    line-height: 1.428571429;
}
.well
{
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    font-size: 12px;
}
.text-truncate-notify
{
    white-space: pre-wrap !important;
}
.image-container
{
    display: flex;
    align-items: center;
}
.image
{
    display: inline-block;
    position: relative;
    width: 32px;
    height: 32px;
    overflow: hidden;
    border-radius: 50%;
    margin-right: 10px;
}
.image img
{
    width: auto;
    height: 100%;
}
#top-notification-dropdown>a
{
    position: relative;
}
#top-notification-dropdown>a span
{
    position: absolute;
}
#top-notification-dropdown>a span.badge
{
    padding: 2px 5px;
}
.scrollable
{
    max-height: 250px;
    overflow-y: scroll;
}

/*New CSS*/

.form-control:focus,
.btn:focus
{
  border-color: inherit;
  -webkit-box-shadow: none;
  box-shadow: none;
}

.nav-item-custom:hover
{
  /*background: #e1f0ff;*/
}

/*New CSS End*/

{!! $adminTheme->admin_custom_css !!}
</style>

    



<style>
nav.navbar-findcond ul.navbar-nav a
{

    color: #3e3e3e;
    font-size: 15px;
    border-style: solid;
    border-width: 0;
    border-color: transparent;
    padding-top: 17.5px;
    padding-bottom: 17.5px;
}
.nav>li>a
{
    /*padding: 10px 15px;*/
}
nav.navbar-findcond .header-image
{
    width: 30px;
    height: 30px;
    border: none;
    margin: -9px 0 -7px;
    border-radius: 50%;
}

@media (min-width: 768px)
{
    .navbar-nav>li>a
    {
        padding-top: 15px;

        padding-bottom: 15px;

    }
    .nav>li>a>img
    {
        max-width: none;
    }
    img
    {
        vertical-align: middle;
    }
    .nav.navbar-nav.navbar-right .dropdown.profile-nav ul
    {
        right: 0 !important;
        left: auto;
    }
    nav.navbar-findcond .profile-nav.open > .dropdown-menu.ani-acc-menu
    {
        transition: opacity 150ms cubic-bezier(0.4, 0.0, 0.2, 1) 0ms,transform 150ms cubic-bezier(0.4, 0.0, 0.2, 1) 0ms;
        transform: scale3d(1,1,1);
        opacity: 1;
        visibility: visible;
    }
    nav.navbar-findcond ul.dropdown-menu
    {
        border: 0;
        background: #fff;
    }
}
/*Profile Css*/
/*.navbar-expand .navbar-nav .dropdown-menu
{
  position: fixed;
  right: 0%;
  top: 8%;
  left: 83%;
  width: 220px;
}*/
.navbar-expand .navbar-nav .dropdown-menu
{
    position: absolute;
    /*margin-left: -40px;*/
    right: 0%;
}
.navbar-expand .navbar-nav .dropdown-menu li:hover
{
  background: #f9f8f8;
}
.navbar-expand .navbar-nav .dropdown-menu li:hover .text-secondary
{
  color: #427bff!important;
}
.font-14
{
  font-size: 14px;
}
/*Profile Css End*/
</style>  
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

</head>
<body class="hold-transition sidebar-mini">

<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="javascript:void(0);">
                <i class="fa fa-bars"></i>
            </a>
      </li>

      @if(isset($missingItems) && $missingItems > 0)

      <li style="width: 230px; padding: 0 10px; border-radius: 3px" class="bg-white m-t-5">
        @if($smtpSetting->mail_from_email == 'your email')
        <div>
            <a class="text-uppercase" href="{{ route('superadmin.smtp-settings.index') }}">
                <small>@lang('app.add') @lang('menu.smtpSetting')
                    <i class="fa fa-arrow-right"></i>
                </small>
            </a>
          <div class="progress">
            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="{{$configCompletePercent}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$configCompletePercent}}%;">
                <span class="sr-only">{{$configCompletePercent}}% Complete</span>
            </div>
          </div>
        </div>

        @elseif(is_null($paymentSetting->paypal_client_id) || is_null($paymentSetting->paypal_secret))
        <div>
            <a class="text-uppercase" href="{{ route('superadmin.payment-settings.index') }}">
                <small>@lang('app.add') @lang('menu.paymentSettings')
                    <i class="fa fa-arrow-right"></i>
                </small>
            </a>
          <div class="progress">
            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="{{$configCompletePercent}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$configCompletePercent}}%;">
                <span class="sr-only">{{$configCompletePercent}}% Complete</span>
            </div>
          </div>
        </div>
        @endif
        </li>
      @endif
    </ul>   
    <div class="form-group mb-0">
            <!-- <h5>Advanced Search <span class="text-danger"></span></h5> -->
            <div class="controls">
                <input type="hidden" name="currentControllerName" id="currentControllerName" value="<?php echo @$currentControllerName; ?>">
                <input type="search" name="common_search" id="common_search" class="form-control rounded-0" placeholder="Search Here..." value="<?php echo @$_REQUEST['common_search']; ?>">
                <div class="help-block"></div>
            </div>
        </div>
        <div class="text-left">
            <button type="text" id="btnFiterSubmitSearch" class="btn btn-info rounded-0 border-0 p-1"> <i class="fa fa-search"></i> </button>
</div>
  
  <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Todo List -->
        <li class="nav-item">
            <a class="nav-link pr-0" href="javascript:void(0);">
                <!-- javascript:showNewTodoForm(); -->  
               <!-- <i class="fa fa-comments fa-2x"></i> -->
               <!-- <span class="svg-icon svg-icon-primary svg-icon-2x">
               <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"></rect>
                    <path d="M3.5,21 L20.5,21 C21.3284271,21 22,20.3284271 22,19.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L10,7 L7.43933983,4.43933983 C7.15803526,4.15803526 6.77650439,4 6.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,19.5 C2,20.3284271 2.67157288,21 3.5,21 Z" fill="#427bff" opacity="0.3">             
                    </path>
                    <path d="M11,13 L11,11 C11,10.4477153 11.4477153,10 12,10 C12.5522847,10 13,10.4477153 13,11 L13,13 L15,13 C15.5522847,13 16,13.4477153 16,14 C16,14.5522847 15.5522847,15 15,15 L13,15 L13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 L11,15 L9,15 C8.44771525,15 8,14.5522847 8,14 C8,13.4477153 8.44771525,13 9,13 L11,13 Z" fill="#427bff">                       
                    </path>
                    </g>
                </svg>
                </span> -->
                <span class="svg-icon svg-icon-primary svg-icon-2x">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#427bff" opacity="0.3"/>
                        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#427bff"/>
                        <rect fill="#427bff" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                        <rect fill="#427bff" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                        <rect fill="#427bff" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                        <rect fill="#427bff" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                        <rect fill="#427bff" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                        <rect fill="#427bff" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                    </g>
                    </svg>
                </span>
            </a>
      </li>     

      <!-- Mail -->
      <li class="nav-item">
        <a class="nav-link pr-0" href="javascript:void(0);">
          <!-- <i class="fa fa-envelope fa-2x"></i> -->
          <span class="svg-icon svg-icon-primary svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"></rect>
              <path d="M5,9 L19,9 C20.1045695,9 21,9.8954305 21,11 L21,20 C21,21.1045695 20.1045695,22 19,22 L5,22 C3.8954305,22 3,21.1045695 3,20 L3,11 C3,9.8954305 3.8954305,9 5,9 Z M18.1444251,10.8396467 L12,14.1481833 L5.85557487,10.8396467 C5.4908718,10.6432681 5.03602525,10.7797221 4.83964668,11.1444251 C4.6432681,11.5091282 4.77972206,11.9639747 5.14442513,12.1603533 L11.6444251,15.6603533 C11.8664074,15.7798822 12.1335926,15.7798822 12.3555749,15.6603533 L18.8555749,12.1603533 C19.2202779,11.9639747 19.3567319,11.5091282 19.1603533,11.1444251 C18.9639747,10.7797221 18.5091282,10.6432681 18.1444251,10.8396467 Z" fill="#427bff">                  
              </path>

              <path d="M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z" fill="#427bff" fill-rule="nonzero" opacity="0.3" transform="translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508) ">
              </path>
            </g>
          </svg>
        </span>
        </a>
      </li>    

      <!-- Chat -->
      <li class="nav-item">
        <a class="nav-link pr-0" href="javascript:void(0);">
          <!-- <i class="fa fa-comments fa-2x"></i> -->
          <span class="svg-icon text-primary svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#427bff"/>
            <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#427bff" opacity="0.3"/></g>
          </svg>
        </span>
        </a>
      </li> 

      <!-- Notifications Dropdown Menu -->
      @if(!$user->is_superadmin)
      <li class="nav-item dropdown pr-3 d-block" id="top-notification-dropdown">
        <a class="nav-link pr-0" data-toggle="dropdown" href="#">
          <!-- <i class="fa fa-bell-o fa-2x"></i> -->
          <span class="svg-icon svg-icon-primary svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#427bff"/>
            <rect fill="#427bff" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"/>
            </g>
          </svg>
          </span>

          @if(count($user->unreadNotifications) > 0)
        <div class="notify">
          <span class="heartbit"></span>
          <span class="point"></span>
        </div>

        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <div class="scrollable"> @foreach ($user->unreadNotifications as $notification)
        @include('notifications.'.snake_case(class_basename($notification->type)))
        @endforeach
      </div>

      @if(count($user->unreadNotifications) > 0)
      <a id="mark-notification-read" href="javascript:void(0);" class="dropdown-item dropdown-footer">
        @lang('app.markNotificationRead')
        <i class="fa fa-check"></i>
      </a>

      @else
      <a  href="javascript:void(0);" class="dropdown-item dropdown-footer">
        @lang('messages.notificationNotFound')
      </a>
      @endif
    </div>
    </li>
@endif

<li class="nav-item nav-item-custom dropdown hide-from-mobile pull-left profile-nav">
    <a data-toggle="dropdown" role="button" aria-expanded="false" class="image-container nav-link waves-effect waves-light dropdown-toggle"

        @if(!$user->is_superadmin)
            href="{{ route('admin.profile.index') }}"
        @else
            href="{{ route('superadmin.profile.index') }}"
        @endif>
        <span class="text-dark font-weight-600 text-capitalize">{{ $user->name  }}</span>
        <div class="image mr-1 ml-2">
          <img src="{{ $user->profile_image_url  }}" alt="User Image">
        </div>
    </a>

    <ul class="dropdown-menu ani-acc-menu py-0 my-0" role="menu">
        <li> 
              <a class="d-flex align-items-center border-bottom py-1 font-14" href="http://127.0.0.1/server/@admin" data-load="?link1=timeline&amp;id=admin"> 
                  <img class="userImage pl-1" src="{{ $user->profile_image_url  }}" alt="User Image" height="35px" width="35px">
                  <div class="userName pl-2">
                <p class="mb-0 text-secondary text-capitalize"> {{ $user->name  }}</p>
                <p class="mb-0 text-secondary">{{ $user->email  }}</p>
          </div>
            </a>
        </li>

    <li class="p-2">
      <a class="text-secondary font-14 d-block" href="{{ url('admin/profile') }}" data-load="?link1=subscriptions">
        <i class="fa fa-user"></i>
          My Profile
        </a>
      </li>

        <li class="p-2">
            <a class="text-secondary font-14 d-block" href="http://127.0.0.1/server/@admin?page=play-lists" data-load="?link1=timeline&amp;id=admin&amp;page=play-lists">
                <i class="fa fa-envelope"></i>
                    Email Settings
            </a> 
        </li>

    <li class="p-2"> 
      <a class="text-secondary font-14 d-block" href="http://127.0.0.1/server/search_recruiter" data-load="?link1=search_recruiter">
        <i class="fa fa-calendar"></i>
        Calendar Setting
        </a>
      </li>

    <li class="p-2"> 
      <a class="text-secondary font-14 d-block" href="{{ url('admin/todo-items') }}" data-load="?link1=search_recruiter">
        <i class="fa fa-list-ol"></i>
        Todolist
        </a>
      </li>

        <li class="p-2 border-bottom">
            <a class="text-secondary font-14 d-block" href="http://127.0.0.1/server/dashboard" data-load="?link1=dashboard">
                <i class="fa fa-comments"></i>
                    Messages
            </a>
        </li>

        <li class="px-2 py-3"> 
            <a class="text-secondary font-14 d-block" class="nav-link waves-effect waves-light" href="{{ route('logout') }}" title="Logout" onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <!-- <i class="fa fa-sign-out"></i> -->
            <i class="fa fa-power-off"></i>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form> 
            Log out 
            </a>
        </li>

        <li class="divider"></li>
        <span class="headtoppoint"></span>
    </ul>    
  </li>
</ul>
</nav>
<!-- /.navbar -->

  @if($user->is_superadmin)
        @include('sections.superadmin-left-sidebar')
  @else
        @include('sections.left-sidebar')
  @endif

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper"> @include('sections.breadcrumb')
    <!-- Main content -->
    <section class="content"> @yield('content') </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  @if($user->hasRole('admin'))
  {{--Footer sticky notes--}}
  <!--<div id="footer-sticky-notes" class="row hidden-xs hidden-sm">
    <div class="col-md-12" id="sticky-note-header">
      <div class="row">
        <div class="col-md-10" style="line-height: 30px"> @lang('app.menu.stickyNotes') <span class="badge badge-warning">{{ $stickyNotes->count() }}</span> <a href="javascript:;" onClick="showCreateNoteModal()" class="btn btn-outline-success btn-xs ml-3"> <i class="fa fa-plus"></i> @lang("modules.sticky.addNote") </a> </div>

        <div class="col-md-2"> <a href="javascript:;" class="btn btn-default btn-circle ml-2" id="open-sticky-bar"><i class="fa fa-chevron-up"></i></a> <a style="display: none;" class="btn btn-default btn-circle ml-2" href="javascript:;" id="close-sticky-bar"><i class="fa fa-chevron-down"></i></a> </div>
      </div>

    </div>

    <div id="sticky-note-list" style="display: none; width: 100%"> @foreach($stickyNotes as $note)

      <div class="col-md-12 sticky-note" id="stickyBox_{{$note->id}}">

        <div class="well

                 @if($note->colour == 'red')  bg-danger @endif

                        @if($note->colour == 'green') bg-success @endif

                        @if($note->colour == 'yellow')  bg-warning @endif

                        @if($note->colour == 'blue') bg-info @endif

                        @if($note->colour == 'purple') bg-purple @endif b-none">

          <p>{!! nl2br($note->note_text)  !!}</p>

          <hr>

          <div class="row font-12">

            <div class="col-md-9"> @lang("modules.sticky.lastUpdated"): {{ $note->updated_at->diffForHumans() }} </div>

            <div class="col-md-3"> <a href="javascript:;"  onclick="showEditNoteModal({{$note->id}})"><i class="ti-pencil-alt text-white"></i></a> <a href="javascript:;" class="m-l-5" onClick="deleteSticky({{$note->id}})" ><i class="ti-close text-white"></i></a> </div>

          </div>

        </div>

      </div>

      @endforeach </div>

  </div>-->

  {{--sticky note end--}}

  {{--sticky note modal--}}

  <div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

    <div class="modal-dialog">

      <div class="modal-content"> Loading ... </div>

    </div>

  </div>

  {{--sticky note modal ends--}}

  @endif

  {{--Ajax Modal--}}

  <div class="modal fade bs-modal-lg in" id="application-lg-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg" id="modal-data-application">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>

          <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>

        <div class="modal-body"> Loading... </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>

          <button type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>

        </div>

      </div>

      <!-- /.modal-content -->

    </div>

    <!-- /.modal-dialog -->

  </div>

  {{--Ajax Modal Ends--}}

  

  {{--Ajax Modal--}}

  <div class="modal fade bs-modal-md in" id="application-md-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-md" id="modal-data-application">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>

          <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>

        <div class="modal-body"> Loading... </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>

          <button type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>

        </div>

      </div>

      <!-- /.modal-content -->

    </div>

    <!-- /.modal-dialog -->

  </div>

  {{--Ajax Modal Ends--}}

  <div class="modal fade bs-modal-lg in" id="application-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg" id="modal-data-application">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>

          <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span> </div>

        <div class="modal-body"> Loading... </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> @lang('app.cancel')</button>

          <button type="button" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>

        </div>

      </div>

      <!-- /.modal-content -->

    </div>

    <!-- /.modal-dialog -->

  </div>

  <footer class="main-footer"> &copy; {{ \Carbon\Carbon::today()->year }} @lang('app.by') {{ $companyName }} </footer>

  @include('sections.right-sidebar') </div>

<!-- ./wrapper -->

<!-- jQuery -->






<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/popper/popper.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/fastclick/fastclick.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src='https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/js/bootstrap-select.min.js'></script>
<script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}<?php echo TM_VERSION; ?>"></script>
{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/icheck/icheck.init.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}<?php echo TM_VERSION; ?>"></script>

<script>

    $('form input').keydown(function (e) {

        if (e.keyCode == 13) {

            e.preventDefault();

            $( "#save-form" ).click();

            return false;

        }

    });



    $('body').on('click', '.right-side-toggle', function () {

        $("body").removeClass("control-sidebar-slide-open");

    })



    $(function () {

        $('.selectpicker').selectpicker({

            style: 'btn-info',

            size: 4

        });

    });

    

    function languageOptions() {

        return {

            processing:     "@lang('modules.datatables.processing')",

            search:         "@lang('modules.datatables.search')",

            lengthMenu:    "@lang('modules.datatables.lengthMenu')",

            info:           "@lang('modules.datatables.info')",

            infoEmpty:      "@lang('modules.datatables.infoEmpty')",

            infoFiltered:   "@lang('modules.datatables.infoFiltered')",

            infoPostFix:    "@lang('modules.datatables.infoPostFix')",

            loadingRecords: "@lang('modules.datatables.loadingRecords')",

            zeroRecords:    "@lang('modules.datatables.zeroRecords')",

            emptyTable:     "@lang('modules.datatables.emptyTable')",

            paginate: {

                first:      "@lang('modules.datatables.paginate.first')",

                previous:   "@lang('modules.datatables.paginate.previous')",

                next:       "@lang('modules.datatables.paginate.next')",

                last:       "@lang('modules.datatables.paginate.last')",

            },

            aria: {

                sortAscending:  "@lang('modules.datatables.aria.sortAscending')",

                sortDescending: "@lang('modules.datatables.aria.sortDescending')",

            },

        }

    }



    $('.language-switcher').change(function () {

        var lang = $(this).val();

        $.easyAjax({

            url: '{{ route("superadmin.language-settings.change-language") }}',

            data: {'lang': lang},

            success: function (data) {

                if (data.status == 'success') {

                    window.location.reload();

                }

            }

        });

    });



    $('#mark-notification-read').click(function () {

        var token = '{{ csrf_token() }}';

        $.easyAjax({

            type: 'POST',

            url: '{{ route("mark-notification-read") }}',

            data: {'_token': token},

            success: function (data) {

                if (data.status == 'success') {

                    $('.top-notifications').remove();

                    $('#top-notification-dropdown .notify').remove();

                    window.location.reload();

                }

            }

        });



    });



    @if($user->hasRole('admin'))

        function addOrEditStickyNote(id)

        {

            var url = '';

            var method = 'POST';

            if(id === undefined || id == "" || id == null) {

                url =  '{{ route('admin.sticky-note.store') }}'

            } else{



                url = "{{ route('admin.sticky-note.update',':id') }}";

                url = url.replace(':id', id);

                var stickyID = $('#stickyID').val();

                method = 'PUT'

            }



            var noteText = $('#notetext').val();

            var stickyColor = $('#stickyColor').val();

            $.easyAjax({

                url: url,

                container: '#responsive-modal',

                type: method,

                data:{'notetext':noteText,'stickyColor':stickyColor,'_token':'{{ csrf_token() }}'},

                success: function (response) {

                    $("#responsive-modal").modal('hide');

                    getNoteData();

                }

            })

        }



        // FOR SHOWING FEEDBACK DETAIL IN MODEL

        function showCreateNoteModal(){

            var url = '{{ route('admin.sticky-note.create') }}';

            $.ajaxModal('#responsive-modal', url);



            return false;

        }



        // FOR SHOWING FEEDBACK DETAIL IN MODEL

        function showEditNoteModal(id){

            var url = '{{ route('admin.sticky-note.edit',':id') }}';

            url  = url.replace(':id',id);

            $.ajaxModal('#responsive-modal', url);

            return false;

        }

        function selectColor(id){

            $('.icolors li.active ').removeClass('active');

            $('#'+id).addClass('active');

            $('#stickyColor').val(id);



        }



        function deleteSticky(id){



            swal({

                title: "Are you sure?",

                text: "You will not be able to recover the deleted Sticky Note!",

                type: "warning",

                showCancelButton: true,

                confirmButtonColor: "#DD6B55",

                confirmButtonText: "Yes, delete it!",

                cancelButtonText: "No, cancel please!",

                closeOnConfirm: true,

                closeOnCancel: true

            }, function(isConfirm){

                if (isConfirm) {



                    var url = "{{ route('admin.sticky-note.destroy',':id') }}";

                    url = url.replace(':id', id);



                    var token = "{{ csrf_token() }}";



                    $.easyAjax({

                        type: 'POST',

                        url: url,

                        data: {'_token': token, '_method': 'DELETE'},

                        success: function (response) {

                            $('#stickyBox_'+id).hide('slow');

                            $("#responsive-modal").modal('hide');

                            getNoteData();

                        }

                    });

                }

            });

        }





        //getting all chat data according to user

        function getNoteData(){



            var url = "{{ route('admin.sticky-note.index') }}";



            $.easyAjax({

                type: 'GET',

                url: url,

                messagePosition: '',

                data:  {},

                container: ".noteBox",

                success: function (response) {

                    //set notes in box

                    $('#sticky-note-list').html(response.stickyNotes);

                    $('#sticky-note-header span.badge').html(response.count);

                }

            });

        }



        // search input implementation

        function search($input, doneTypingInterval, type) {

            var $anchor = $input.siblings('a');

            var typingTimer, fn;



            if (type == 'data') {

                fn = loadData;

            }

            if (type == 'table') {

                fn = redrawTable;                    

            }



            $input.on('keyup', function (e) {

                if ($(this).val() !== '' || ($(this).val().length >= 0 && e.key === 'Backspace')) {

                    clearTimeout(typingTimer);

                    typingTimer = setTimeout(() => {

                        fn();

                    }, doneTypingInterval);

                }



                $(this).val() !== '' ? $anchor.removeClass('d-none') : $anchor.addClass('d-none');

            })



            $input.on('keydown', function () {

                clearTimeout(typingTimer);

            });



            $anchor.click(function(e) {

                $(this).siblings('input').val('');

                fn();

                $anchor.addClass('d-none');

                $(this).siblings('input').focus();

            })

        }



        //    sticky notes script

        var stickyNoteOpen = $('#open-sticky-bar');

        var stickyNoteClose = $('#close-sticky-bar');

        var stickyNotes = $('#footer-sticky-notes');

        var viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

        var stickyNoteHeaderHeight = stickyNotes.height();



        $('#sticky-note-list').css('max-height', viewportHeight-150);



        stickyNoteOpen.click(function () {

            $('#sticky-note-list').toggle(function () {

                $(this).animate({

                    height: (viewportHeight-150)

                })

            });

            stickyNoteClose.toggle();

            stickyNoteOpen.toggle();

        })



        stickyNoteClose.click(function () {

        $('#sticky-note-list').toggle(function () {

            $(this).animate({

                height: 0

            })

        });

        stickyNoteOpen.toggle();

        stickyNoteClose.toggle();

    })

    @endif

</script>



<script>



$( document ).ready(function() {



    var currentControllerName   =   $('#currentControllerName').val();

    //alert(currentControllerName); 

    if(currentControllerName == 'AdminAdvancedSearchController')

    {

        $('#btnFiterSubmitSearch').hide();

         $('#common_search').keyup(function() {

                        tableLoad('filter');

 

            });

    }

    else

    {

                $('#btnFiterSubmitSearch').click(function () {

                

                    var common_search       =   $('#common_search').val();          

                    var url                 =   '<?php  echo url('admin/advsearch?common_search='); ?>'+common_search;          

                    //alert(url); 

                    //return false;         

                    window.location.href    = url;

            

               });  

    }

    

/* $('#common_search').keyup(function() {

 

           alert('Handler for .keyup() called.');

            //http://talentmily.vibhanshujaiswal.com/admin/advsearch?common_search=dfdfffd

 

  });

  */

  

    

        

        



}); 

</script>

    

@stack('footer-script')

</body>

</html>

