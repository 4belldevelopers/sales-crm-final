<style>
[type="search"]{outline-offset:-2px;-webkit-appearance:textfield;font-size:1rem;font-weight:500;line-height:1.9;color:#181c32;background-color:#fff;background-clip:padding-box;border:1px solid #e4e6ef;appearance:none;border-radius:.6rem 0 0 .6rem;outline:none;padding:.3em .9em;transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out}header{position:fixed;top:0;left:0;display:flex;flex-direction:row;flex-wrap:wrap;width:100%;height:80px;background-color:#318ebb;box-shadow:0 0 8px rgba(212,212,212,0.4)}header .logo{display:flex;flex:2;justify-content:flex-start;align-items:center;height:100%;color:#FFF;font-size:3rem;padding-left:2rem;cursor:pointer}header nav{display:flex;flex:7}header nav .search-bar{display:flex;flex:1;max-width:800px;margin:0 auto}header nav .search-bar .search{display:flex;align-items:center;flex:1}header nav .search-bar .search .search__input{display:flex;flex:1;padding:1.2rem 1rem 1.2rem 4rem;height:40px;font-size:.9rem;color:#4a4a4a;border:none;border-radius:0;position:relative;right:0;letter-spacing:.05rem;background:url(https://cdn1.iconfinder.com/data/icons/hawcons/32/698956-icon-111-search-128.png) #ececec;background-repeat:no-repeat;background-position:1rem 50%;background-size:2rem;outline:0;transition:background .3s ease-out,opacity .5s ease-out}header nav .search-bar .search .search__input:focus{background:url(https://cdn1.iconfinder.com/data/icons/hawcons/32/698956-icon-111-search-128.png) no-repeat 1rem 50% #FFF;background-size:2rem}header nav .search-bar .search .search__btn{background:#36474f;height:40px;border:none;width:120px;color:#FFF;font-size:.8rem;text-transform:uppercase;letter-spacing:.1rem;cursor:pointer;outline:0;transition:all .4s ease-out}header nav .search-bar .search .search__btn:hover{background:#263137}header nav .search-bar .search .search__icon{display:none}header .menu{display:flex;flex:2;justify-content:flex-end;align-items:center;padding:2rem;font-size:2rem;color:#FFF;height:80px;line-height:80px;cursor:pointer}.suggestion{display:flex;position:absolute;top:55px;border-top:1px solid #F1F1F1;opacity:0;visibility:hidden;width:270px!important}.suggestion.active{opacity:1;visibility:visible;transform-origin:center top;transform:translateZ(0);-webkit-backface-visibility:hidden;backface-visibility:hidden;-webkit-animation:rotation-show .5s .3s;animation:rotation-show .5s .3s;-webkit-animation-fill-mode:backwards;animation-fill-mode:backwards}.suggestion__content{background:#fff;display:flex;flex:1;box-shadow:0 6px 8px rgba(0,0,0,0.15);padding:.5em 1em;font-size:.8rem}.suggestion__content .suggestion__content-right-side,.suggestion__content .suggestion__content-left-side{flex:1;padding:1em}.suggestion__content .suggestion__content-right-side h5,.suggestion__content .suggestion__content-left-side h5{margin:0;padding:0;font-size:.9rem;color:#757575;font-weight:600;margin-bottom:5px}.suggestion__content .suggestion__content-right-side ul,.suggestion__content .suggestion__content-left-side ul{margin:0;padding:0}.suggestion__content .suggestion__content-right-side ul li,.suggestion__content .suggestion__content-left-side ul li{list-style-type:none;padding:2px 0}.suggestion__content .suggestion__content-left-side ul li{list-style-type:none;padding:2px 0;color:#328dbb;cursor:pointer;transition:color .4s ease-out,transform .4s ease}.suggestion__content .suggestion__content-left-side ul li:hover{color:#13587b;transform:translateX(3px)}.suggestion__content .suggestion__content-right-side h5{text-align:center}.suggestion__content .suggestion__content-right-side ul{padding:0;max-width:200px;margin:auto}.suggestion__content .suggestion__content-right-side ul li{border:1px solid #f3f3f3;background:#FFF;text-align:center;border-radius:10px;margin-bottom:8px;cursor:pointer;transition:background .3s ease-out,border .4s ease-in-out}.suggestion__content .suggestion__content-right-side ul li:hover{border:1px solid #607D8B;background:#f1f1f1}.hide-element{display:none!important}.mobile-search{margin:0 1em;opacity:1!important;visibility:visible!important;width:100%}.close-icon{color:#909090!important;position:absolute!important;right:8%!important;font-size:1rem}@-webkit-keyframes rotation-show{0%{transform:perspective(1000px) rotateX(-90deg)}100%{transform:perspective(1000px) translateY(0)}}@keyframes rotation-show{0%{transform:perspective(1000px) rotateX(-90deg)}100%{transform:perspective(1000px) translateY(0)}}@media only screen and (max-width: 600px){.search__input{font-size:16px!important;opacity:0;visibility:hidden;width:0}.search__btn{display:none!important}.search__icon{display:flex!important;color:#FFF;align-items:center;flex:6;position:absolute;right:5rem;cursor:pointer;height:80px;line-height:80px}.message{position:relative;top:80px;background:#f3f3f3;padding:.5rem 1.5rem;display:block!important;text-align:center}}

.{
      background-color: #e4e6ef21;
}

    .app-header-menu .menu>.menu-item.here>.menu-link .menu-title {
        color: var(--bs-primary);
    }
    
    .activeTab{
        background-color: transparent;
        box-shadow: 0 3px 4px rgba(0, 0, 0, .03)!important;
        transition: color .2s ease;
        color: var(--bs-primary) !important;
    }
    .pck_select a{
              font-weight: 600;
              font-size: 1.1rem;
              color: var(--bs-gray-700);
              padding: 15px;
    }
    .activeTab a{
        color: var(--bs-primary) !important;
    }

</style>

<div id="kt_header" style="" class="header align-items-stretch">
<!--begin::Container-->
<div class="container-fluid d-flex align-items-stretch justify-content-between">
<!--begin::Aside mobile toggle-->
<div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
  <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
    <!--begin::Svg Icon | path: icons/duotone/Text/Menu.svg-->
    <span class="svg-icon svg-icon-2x mt-1">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24" />
        <rect fill="#000000" x="4" y="5" width="16" height="3" rx="1.5" />
        <path d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L18.5,10 C19.3284271,10 20,10.6715729 20,11.5 C20,12.3284271 19.3284271,13 18.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z" fill="#000000" opacity="0.3" />
      </g>
    </svg>
    </span>
    <!--end::Svg Icon-->
  </div>
</div>
<!--end::Aside mobile toggle-->


<!--begin::Wrapper-->
<div  class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
<!--begin::Navbar-->
<div class="d-flex align-items-stretch" id="kt_header_nav" style="margin-left: 29%;">
<!--begin::Menu wrapper-->
<div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
<!--begin::New search-->
<div class="search-bar d-flex align-items-center" style="">
  
  
	<div class="mx-auto mb-1" data-kt-buttons="true" style="display: flex;">

      {{-- <div class="btn-color-gray-600 btn-active btn-active-secondary pck_select {{ request()->is('service_company/jobs/market-jobs/0') ? 'activeTab' : '' }} py-3 me-2"  ta-kt-plan="1" {{ request()->is('service_company/jobs/market-jobs/0') ? '' : '' }}>
          <a href="{{ url('service_company/jobs/market-jobs/0') }}" style=""> Find Jobs</a>
      </div>
      <div class="btn-color-gray-600 btn-active btn-active-secondary pck_select py-3 {{ request()->is('service_company/find-talents') ? 'activeTab' : '' }} me-2" data-kt-plan="2" {{ request()->is('service_company/find-talents') ? '' : '' }}>
        <a href="{{route('service_company.apptracker.findTalent')}}" style=""> Find Talent</a>
      </div>
      @if($global_company_type !=3)
      <div class="btn-color-gray-600 btn-active btn-active-secondary pck_select py-3 {{ request()->is('service_company/find-agency') ? 'activeTab' : '' }}" data-kt-plan="2" {{ request()->is('service_company/find-agency') ? '' : '' }}>
        <a href="{{route('service_company.find.findAgency')}}" style=""> Find Agency</a>
      </div>
      @endif --}}
       
         
       
      <!-- FIND JOBS -->
      @if($global_company_type !=3)
      <div class="btn-color-gray-600 btn-active btn-active-secondary pck_select {{ request()->is('service_company/jobs/market-jobs/0') ? 'activeTab' : '' }} py-3 me-2" style="border:1px solid #e4e6eb;background:#f7f8fa;height: 40px;margin-top: 2.5px; padding: 0px 2px;border-radius:50px;font-size:16px;font-weight:500;color:#4a4a4a;display:flex;align-items:center;gap:0px;box-shadow:0 2px 4px rgba(0,0,0,0.06);">
          <div style="background:white;border:1px solid #e4e6eb;padding:6px 7px;border-radius:50%;font-size:11px;">
            <svg class="w-6 h-6 text-gray dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
               <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z"/>
            </svg>
          </div>
          <a href="{{ url('service_company/jobs/market-jobs/0') }}" style="">Find Jobs</a>
      </div>
      @endif

      <!-- FIND TALENT -->
      <div class="btn-color-gray-600 btn-active btn-active-secondary pck_select py-3 {{ request()->is('service_company/find-talents') ? 'activeTab' : '' }} me-2" style="border:1px solid #e4e6eb;background:#f7f8fa;height: 40px;margin-top: 2.5px; padding: 0px 2px;border-radius:50px;font-size:16px;font-weight:500;color:#4a4a4a;display:flex;align-items:center;gap:0px;box-shadow:0 2px 4px rgba(0,0,0,0.06);">
          <div style="background:white;border:1px solid #e4e6eb;padding:6px 7px;border-radius:50%;font-size:11px;">
          <svg class="w-6 h-6 text-gray dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
             <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.6144 7.19994c.3479.48981.5999 1.15357.5999 1.80006 0 1.6569-1.3432 3-3 3-1.6569 0-3.00004-1.3431-3.00004-3 0-.67539.22319-1.29865.59983-1.80006M6.21426 6v4m0-4 6.00004-3 6 3-6 2-2.40021-.80006M6.21426 6l3.59983 1.19994M6.21426 19.8013v-2.1525c0-1.6825 1.27251-3.3075 2.95093-3.6488l3.04911 2.9345 3-2.9441c1.7026.3193 3 1.9596 3 3.6584v2.1525c0 .6312-.5373 1.1429-1.2 1.1429H7.41426c-.66274 0-1.2-.5117-1.2-1.1429Z"/>
          </svg>
          </div>
          <a href="{{route('service_company.apptracker.findTalent')}}" style="">Find Talent</a>
      </div>

      <!-- FIND AGENCY -->
      
      <div  class="btn-color-gray-600 btn-active btn-active-secondary pck_select py-3 {{ request()->is('service_company/find-agency') ? 'activeTab' : '' }}" style="border:1px solid #e4e6eb;background:#f7f8fa;height: 40px;margin-top: 2.5px; padding: 0px 2px;border-radius:50px;font-size:16px;font-weight:500;color:#4a4a4a;display:flex;align-items:center;gap:0px;box-shadow:0 2px 4px rgba(0,0,0,0.06);">
          <div style="background:white;border:1px solid #e4e6eb;padding:6px 7px;border-radius:50%;font-size:11px;">
          <svg class="w-6 h-6 text-gray dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z"/>
          </svg>
          </div>
          <a href="{{route('service_company.find.findAgency')}}" style="">Find Agency</a>
      </div>
      





  </div>


</div>
<!--end::New search-->

</div>
<!--end::Menu wrapper-->
</div>
<!--end::Navbar-->
<!--begin::Topbar-->
<div class="d-flex align-items-stretch flex-shrink-0">
  <!--begin::Toolbar wrapper-->
  <div class="d-flex align-items-stretch flex-shrink-0">
      <?php
          // Get last part of the URL
          $currentUrl = $_SERVER['REQUEST_URI'];
          $lastPart   = basename($currentUrl);
        ?>

          <?php if ($lastPart !== 'dashboard'): ?>
              {{-- <div class="d-flex align-items-center ms-1 ms-lg-3">
                  <button class="btn btn-info d-flex align-items-center gap-2" onclick="window.history.back()">
                      <i class="fa fa-reply"></i> Back
                  </button>
              </div> --}}
      <?php endif; ?>
      
    <!--begin::User profile-->
    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
      <!--begin::Menu wrapper-->
      <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
      
      <?php 
          
        $background_image	= ''; //url('public/user-uploads/team_profile_img/avatar.png'); 
        if(!empty(@$user->image) && strlen(@$user->image)>10){
          //$background_image			=  url('public/user-uploads/team_profile_img/'.@$user->image); 
          $background_image			= @$user->image; 
        }
      ?>
       @if(!empty($background_image))
          <img src="<?php echo str_replace("index.php","",@$background_image); ?>" alt="{{ $user->name  }}" /> 
        @else
        <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-100" style="float:left">
            <a href="javascript:void(0);" class="border-radius-100" id="kt_activities3_toggle">
                  <div class="symbol-label fs-3 fs-3 bg-light-primary text-primary  border-radius-100">{{ substr($user->name, 0, 1)  }}</div>
            </a>		 
       </div>
        @endif
      
      </div>
      <!--begin::Menu-->
      <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
        <!--begin::Menu item-->
        <div class="menu-item px-3">
          <div class="menu-content d-flex align-items-center px-3">
            <!--begin::Avatar-->
            <div class="symbol symbol-50px me-5"> 
              @if(!empty($background_image))
              <img alt="Logo" src="<?php echo str_replace("index.php","",@$background_image); ?>" /> 
              @else
              <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-100" style="float:left">
                <a href="javascript:void(0);" data-row-id="11" class="show-detail border-radius-100" id="kt_activities3_toggle">
                      <div class="symbol-label fs-3 fs-3 bg-light-primary text-primary  border-radius-100">{{ substr($user->name, 0, 1)  }}</div>
                </a>		 
              </div>
              @endif
            </div>
            <!--end::Avatar-->
            <!--begin::Username-->
            <div class="d-flex flex-column">
              <div class="fw-bolder d-flex align-items-center fs-5">{{ $user->name  }} </div>
              <a href="#" class="fw-bold text-muted text-hover-primary fs-7">{{ $user->email  }}</a> </div>
            <!--end::Username-->
          </div>
        </div>
        <!--end::Menu item-->
        <!--begin::Menu separator-->
        <div class="separator my-2"></div>
        <!--end::Menu separator-->
        <!--begin::Menu item-->
        <div class="menu-item px-5"> <a href="{{ route('service_company.profile.index') }}" class="menu-link px-5">My Profile</a> </div>
        <!--end::Menu item-->
        <!--begin::Menu separator-->
        <div class="separator my-2 d-none"></div>
        <!--end::Menu separator-->
        <!--begin::Menu item-->
        <div class="menu-item px-5"> <a class="menu-link px-5 bg-light-danger text-danger bg-hover-danger text-hover-light mt-2" href="{{ route('logout') }}" title="Sign Out" onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <!-- <i class="fa fa-power-off"></i> -->
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
          Sign Out </a> </div>
        <!--end::Menu item-->
      </div>
      <!--end::Menu-->
      <!--end::Menu wrapper-->
    </div>
    <!--end::User profile-->

    <!--begin::Heaeder menu toggle-->
    <div class="d-flex align-items-center d-lg-none ms-2 me-n3 d-none" title="Show header menu">
      <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
        <!--begin::Svg Icon | path: icons/duotone/Text/Toggle-Right.svg-->
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24" />
            <path fill-rule="evenodd" clip-rule="evenodd" d="M22 11.5C22 12.3284 21.3284 13 20.5 13H3.5C2.6716 13 2 12.3284 2 11.5C2 10.6716 2.6716 10 3.5 10H20.5C21.3284 10 22 10.6716 22 11.5Z" fill="black" />
            <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M14.5 20C15.3284 20 16 19.3284 16 18.5C16 17.6716 15.3284 17 14.5 17H3.5C2.6716 17 2 17.6716 2 18.5C2 19.3284 2.6716 20 3.5 20H14.5ZM8.5 6C9.3284 6 10 5.32843 10 4.5C10 3.67157 9.3284 3 8.5 3H3.5C2.6716 3 2 3.67157 2 4.5C2 5.32843 2.6716 6 3.5 6H8.5Z" fill="black" />
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
      </div>
    </div>
    <!--end::Heaeder menu toggle-->
  </div>
  <!--end::Toolbar wrapper-->
</div>
<!--end::Topbar-->
</div>
<!--end::Wrapper-->
</div>
<!--end::Container-->
</div>
<script>
    var input 			= document.querySelector('.search__input');
    var inputWidth 		= input.offsetWidth;
    var suggestion 		= document.querySelector('.suggestion');
    var mobileSearch 	= document.querySelector('.search__icon');
    var menu 			= document.querySelector('.menu');
    var logo 			= document.querySelector('.logo');
    var logoIcon 		= logo.querySelector('.logo__icon');
      
    function equalWidth()
    {
		  var input 				= document.querySelector('.search__input');
		  var inputWidth 			= input.offsetWidth;
		  suggestion.style.width 	= inputWidth + "px" ;
    }

    function showSuggestion()
    {
      	suggestion.classList.add('active');
    }

    function hideSuggestion()
    {
      	suggestion.classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', function()
    {
		  equalWidth();
		  input.addEventListener('focus', showSuggestion);
    })

    document.addEventListener('click', function(e)
    {
		var target = e.target.className;
		e.stopPropagation();
		if (target != "search__input" && target != "suggestion__content-left-side" && target != "suggestion__content-right-side" && target != "suggestion__content" ) {
			hideSuggestion();
			console.log(target);
		}
    });

    var addEvent = function(object, type, callback)
    {
        if (object == null || typeof(object) == 'undefined') return;
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
        } else if (object.attachEvent) {
            object.attachEvent("on" + type, callback);
        } else {
            object["on"+type] = callback;
        }
    };

    addEvent(window, 'resize', function(event)
    {
		suggestion.style.width = '';
		equalWidth();
    });

    function openMobileSearch()
    {
		logo.classList.toggle('hide-element');
		menu.classList.toggle('hide-element');
		input.classList.toggle('mobile-search');
		mobileSearch.classList.toggle('ion-ios-search');
		mobileSearch.classList.toggle('ion-close-round');
		mobileSearch.classList.toggle('close-icon');
    }

    mobileSearch.addEventListener('click', function()
    {
		openMobileSearch();
    })
</script>
