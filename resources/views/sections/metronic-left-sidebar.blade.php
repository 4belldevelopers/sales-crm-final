<!-- Main Sidebar Container -->
<div class="aside-menu flex-column-fluid">
  <!--begin::Aside Menu-->
  <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
  <!--begin::Menu-->
    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
      <!-- <div class="menu-item"> <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }} ">
        <div class="menu-content pb-2"> <i class="nav-icon icon-speedometer"></i>
          <p>Dashboard </p>
        </div>
        </a>
      </div> -->

      <div class="menu-item">
        <a href="{{ route('admin.dashboard') }}" class="menu-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}"> 
          <span class="menu-icon">
            <i class="fa fa-user"></i></span>
            <span class="menu-title">Dashboard</span>
          </span>
        </a>
      </div>

      <div class="menu-item"> <a href="{{ route('admin.clients.index') }}" class="menu-link {{ request()->is('admin/clients*') ? 'active' : '' }}"> 
        <span class="menu-icon"><i class="fa fa-building"></i></span> <span class="menu-title">All Clients</span></a>
      </div>
	  
	  
      @if(in_array("view_jobs", $userPermissions))
      <div class="menu-item"> <a href="{{ route('admin.jobs.index') }}" class="menu-link {{ request()->is('admin/jobs*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i class="fa fa-briefcase"></i> </span> </span> <span class="menu-title">All Requisitions</span> </a> </div>
      @endif
	  
      <div class="menu-item"> <a href="{{ route('admin.interview-schedule.table-view') }}" class="menu-link {{ request()->is('admin/interview-schedule*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i class="fa fa-laptop"></i> </span> </span> <span class="menu-title">All Interview </span> </a> </div>
	  
	  
      @if(in_array("view_job_applications", $userPermissions))
      <div class="menu-item"> <a href="{{ route('admin.cvtracker.index') }}" class="menu-link {{ request()->is('admin/cvtracker*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i class="fa fa-users"></i> </span> </span> <span class="menu-title">All Applicant </span> </a> </div>
      @endif
	  
      <div class="menu-item"> <a href="{{ route('admin.applicationoffer.index') }}" class="menu-link {{ request()->is('admin/applicationoffer*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i class="fa fa-file"></i> </span> </span> <span class="menu-title">Offer Tracker </span> </a> </div>
	  
	  
      <div class="menu-item"> <a href="{{ route('admin.job-onboard.index') }}" class="menu-link {{ request()->is('admin/job-onboard*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i style="font-size:15px" class="fa">&#xf2b5;</i> </span> </span> <span class="menu-title">Joining Tracker</span> </a> </div>
	  
      @if(in_array("view_team", $userPermissions))
      <div class="menu-item"> <a href="{{ route('admin.consultancy.index') }}" class="menu-link {{ request()->is('admin/consultancy*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i class="fa fa-outdent"></i> </span> </span> <span class="menu-title">Vendor</span> </a> </div>
      @endif
      <div class="menu-item"> <a href="{{ route('admin.customreport.index') }}" class="menu-link {{ request()->is('admin/customreport*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"> <i style="font-size:15px" class="fa">&#xf080;</i> </span> </span> <span class="menu-title">@lang('app.reports')</span> </a> </div>
      <div class="menu-item">
        <div class="menu-content pt-8 pb-2"> <span class="menu-section text-muted text-uppercase fs-8 ls-1">Subscription</span> </div>
      </div>
      <div data-kt-menu-trigger="click" class="menu-item menu-accordion"> <span class="menu-link"> <span class="menu-icon">
        <!--begin::Svg Icon | path: icons/duotone/Code/Compiling.svg-->
        <span class="svg-icon svg-icon-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
          <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        </span> <span class="menu-title">@lang('menu.subscription') </span> <span class="menu-arrow"></span> </span>
        <div class="menu-sub menu-sub-accordion menu-active-bg">
          <div data-kt-menu-trigger="click" class="menu-item menu-accordion"> <a class="nav-link" href="{{ route('admin.subscribe.index') }}"> <span class="menu-link"> <span class="menu-bullet"> <i class="fa fa-circle-o nav-icon"></i> </span> <span class="menu-title">Subscription</span>
            <!--<span class="menu-arrow"></span>-->
            </span> </a> <a class="nav-link" href="{{ route('admin.subscribe.history') }}"> <span class="menu-link"> <span class="menu-bullet"> <i class="fa fa-circle-o nav-icon"></i> </span> <span class="menu-title">History</span>
            <!--<span class="menu-arrow"></span>-->
            </span> </a> </div>
        </div>
      </div>
	  
	  
      <div class="menu-item">
        <div class="menu-content pt-8 pb-2"> <span class="menu-section text-muted text-uppercase fs-8 ls-1">@lang('menu.settings')</span> </div>
      </div>
      <div data-kt-menu-trigger="click" class="menu-item menu-accordion"> <span class="menu-link"> <span class="menu-icon">
        <!--begin::Svg Icon | path: icons/duotone/Communication/Group.svg-->
        <span class="svg-icon svg-icon-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
          <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
        </svg>
        </span>
        <!--end::Svg Icon-->
        </span> <span class="menu-title">@lang('menu.settings')</span> <span class="menu-arrow"></span> </span>
        <div class="menu-sub menu-sub-accordion"> @if(in_array("view_category", $userPermissions))
          <div class="menu-item"> <a href="{{ route('admin.job-categories.index') }}" class="menu-link {{ request()->is('admin/job-categories*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">Categories</span> </a> </div>
          @endif
          <div class="menu-item"> <a href="{{ route('admin.candidateStatus.index') }}" class="menu-link {{ request()->is('admin/candidateStatus*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">Interview Process</span> </a> </div>
          @if(in_array("view_skills", $userPermissions))
          <div class="menu-item"> <a href="{{ route('admin.skills.index') }}" class="menu-link {{ request()->is('admin/skills*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.skills')</span> </a> </div>
          @endif	
          
          
          @if(!is_null($activePackage))
          <div class="menu-item"> <a href="{{ route('admin.profile.index') }}" class="menu-link {{ request()->is('admin/profile*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.myProfile')</span> </a> </div>
          <div class="menu-item"> <a href="{{ route('admin.profile.appdup') }}" class="menu-link {{ request()->is('admin/appdup*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">App Duplicate Setting</span> </a> </div>
          @if(in_array("manage_settings", $userPermissions))
          <div class="menu-item"> <a href="{{ route('admin.settings.index') }}" class="menu-link {{ request()->is('admin/settings/settings') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.businessSettings')</span> </a> </div>
          <div class="menu-item"> <a href="{{ route('admin.application-setting.index') }}" class="menu-link {{ request()->is('admin/settings/application-setting') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.applicationFormSettings')</span> </a> </div>
          <div class="menu-item"> <a href="{{ route('admin.role-permission.index') }}" class="menu-link {{ request()->is('admin/settings/role-permission') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.rolesPermission')</span> </a> </div>
          <div class="menu-item"> <a href="{{ route('admin.theme-settings.index') }}" class="menu-link {{ request()->is('admin/settings/theme-settings') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.themeSettings')</span> </a> </div>
          @if($linkedinGlobal->status == 'enable')
          <div class="menu-item"> <a href="{{ route('admin.linkedin-settings.index') }}" class="menu-link {{ request()->is('admin/settings/linkedin-settings') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.linkedInSettings')</span> </a> </div>
          @endif    
          
          @if(is_null($global->account_delete_at))
          <div class="menu-item"> <a href="{{ route('admin.settings.delete-account') }}" class="menu-link {{ request()->is('admin/settings/delete-account') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('app.deleteAccount')</span> </a> </div>
          @endif
          
          
          
          
          @endif			
          @endif </div>
      </div>
      @if(!is_null($activePackage) && $activePackage->package->career_website)
      
      
      @php $route = !module_enabled('Subdomain') ? route('jobs.jobOpenings') : route('jobs.jobOpenings',
      $global->career_page_link)
      @endphp <a href="{{ $route }}" target="_blank" class="nav-link"> <i class="fa fa-external-link"></i>
      <div class="menu-item">
        <div class="menu-content pt-8 pb-2"> <span class="menu-section text-muted text-uppercase fs-8 ls-1">@lang('app.careerWebsite')</span> </div>
      </div>
      </a> @endif 
	  
	  
	  </div>
    <!--end::Menu-->
  </div>
  <!--end::Aside Menu-->
</div>


<script>
function openCity(evt, cityName)
{

var i, tabcontent, tablinks;

tabcontent = document.getElementsByClassName("tabcontent");

for (i = 0; i < tabcontent.length; i++) {

  tabcontent[i].style.display = "none";

}

tablinks = document.getElementsByClassName("tablinks");

for (i = 0; i < tablinks.length; i++) {

  tablinks[i].className = tablinks[i].className.replace(" active", "");

}

document.getElementById(cityName).style.display = "block";

evt.currentTarget.className += " active";

}

</script>
