<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary">
  <!-- Brand Logo -->
  
  <a href="{{ route('admin.dashboard') }}" class="brand-link" style="">
  <!-- <img src="{{ asset('assets_ng/media/logos/logo-dark.png')}}" alt="Talentmily Logo" class="brand-image img-fluid"> -->
  <img src="{{ asset('img/Mily_recruit.png')}}" alt="Logo" class="brand-image img-fluid"> </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-1">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" id="sidebarnav" role="menu"
    data-accordion="false">
        @if(!is_null($activePackage))
        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}"> <i class="nav-icon icon-speedometer"></i>
          <p>@lang('menu.dashboard') </p>
          </a> </li>
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->is('admin/clients*') ? 'active' : '' }}"> <i class="fa fa-building"></i>
          <p>All Clients</p>
          </a> </li>
        @if(in_array("view_jobs", $userPermissions))
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->is('admin/jobs*') ? 'active' : '' }}"> <i class="fa fa-briefcase"></i>
          <p>All Requisitions</p>
          </a> </li>
        @endif
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.interview-schedule.table-view') }}" class="nav-link {{ request()->is('admin/interview-schedule*') ? 'active' : '' }}"> <i class="fa fa-laptop"></i>
          <p> All Interview </p>
          </a> </li>
        @if(in_array("view_job_applications", $userPermissions))
        <!--<li class="nav-item border-bottomm">
      <a href="{{ route('admin.cvtracker.index') }}" class="nav-link {{ request()->
        is('admin/cvtracker*') ? 'active' : '' }}"> <span class="svg-icon menu-icon"><i class="fa fa-users"></i></span> <span class="menu-text"> All Candidate</span> </a>
        </li>
        -->
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.cvtracker.index') }}" class="nav-link {{ request()->is('admin/cvtracker*') ? 'active' : '' }}"> <i class="fa fa-users"></i>
          <!-- <p>All Candidate</p> -->
          <p>All Applicant</p>
          </a> </li>
        @endif
        <!--<li class="nav-item border-bottomm">
        <a href="{{ route('admin.offer.index') }}" class="nav-link {{ request()->
        is('admin/offer*') ? 'active' : '' }}"> <i class="fa fa-file"></i>
        <p>Offer Tracker</p>
        </a>
        </li>
        -->
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.applicationoffer.index') }}" class="nav-link {{ request()->is('admin/applicationoffer*') ? 'active' : '' }}"> <i class="fa fa-file"></i>
          <p>Offer Tracker</p>
          </a> </li>
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.job-onboard.index') }}" class="d-flex align-items-center nav-link {{ request()->is('admin/job-onboard*') ? 'active' : '' }}">
          <!-- <img src="{{asset('img/connect.png') }}" class="img-responsive img-fluid" height="15" width="15" />           -->
          <img src="{{asset('img/hand-shake.png') }}" class="img-responsive img-fluid" height="15" width="15" />
          <!-- <i class="fa fa-file"></i> -->
          &nbsp;
          <p>Joining Tracker</p>
          </a> </li>
        @if(in_array("view_team", $userPermissions))
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.team.index') }}" class="nav-link {{ request()->is('admin/team*') ? 'active' : '' }}"> <i class="nav-icon icon-people"></i>
          <p> @lang('menu.team') </p>
          </a> </li>
        @endif
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.consultancy.index') }}" class="nav-link {{ request()->is('admin/consultancy*') ? 'active' : '' }}"> <i class="fa fa-outdent"></i>
          <p> Vendor </p>
          </a> </li>
        <!-- @if(in_array("view_locations", $userPermissions))
      <li class="nav-item">
        <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->
        is('admin/locations*') ? 'active' : '' }}"> <i class="nav-icon icon-location-pin"></i>
        <p> @lang('menu.locations')</p>
        </a>
        </li>
        @endif-->      
        
        @if(in_array("view_job_applications", $userPermissions))
        <!--<li class="menu-item">
        <a href="{{ route('admin.cvtracker.index') }}" class="nav-link {{ request()->
        is('admin/cvtracker*') ? 'active' : '' }}"> <span class="svg-icon menu-icon"> <i class="fa fa-users"></i> </span> <span class="menu-text">All Candidate</span> </a>
        </li>
        -->
        <!--<li class="nav-item">
        <a href="{{ route('admin.job-applications.index') }}" class="nav-link {{ request()->
        is('admin/job-applications*') ? 'active' : '' }}"> <i class="nav-icon icon-user"></i>
        <p>@lang('menu.jobApplications')</p>
        </a>
        </li>
        -->
        <!--<li class="nav-item">
        <a href="{{ route('admin.applications-archive.index') }}" class="nav-link {{ request()->
        is('admin/applications-archive*') ? 'active' : '' }}"> <i class="nav-icon icon-drawer"></i>
        <p> @lang('menu.candidateDatabase') </p>
        </a>
        </li>
        -->
        <!-- <li class="nav-item">
        <a href="{{ route('admin.job-onboard.index') }}" class="nav-link {{ request()->
        is('admin/job-onboard*') ? 'active' : '' }}"> <i class="nav-icon icon-user"></i>
        <p> All Joining </p>
        </a>
        </li>
        -->
        @endif      
        
        @if(in_array("view_schedule", $userPermissions))
        <!-- <li class="nav-item">
        <a href="{{ route('admin.interview-schedule.index') }}" class="nav-link {{ request()->
        is('admin/interview-schedule*') ? 'active' : '' }}"> <i class="nav-icon icon-calendar"></i>
        <p>@lang('menu.interviewSchedule')</p>
        </a>
        </li>
        -->
        @endif
        <!-- @if($user->
        roles->count() > 0)
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.todo-items.index') }}" class="nav-link {{ request()->is('admin/todo-items*') ? 'active' : '' }}"> <i class="nav-icon icon-notebook"></i>
          <p>@lang('menu.todoList')</p>
          </a> </li>
        @endif -->
        
        @endif
        <li class="nav-item border-bottomm"> <a href="{{ route('admin.customreport.index') }}" class="nav-link {{ request()->is('admin/customreport*') ? 'active' : '' }}"> <i class="fa fa-bar-chart" aria-hidden="true"></i>
          <p> @lang('app.reports') </p>
          </a> </li>
        @if(!is_null($activePackage))
        <li class="hoverWrapper nav-item border-bottomm has-treeview @if(\Request()->is('admin/subscribe*'))active menu-open @endif"> <a href="#" class="nav-link"> <i class="fa fa-hand-pointer-o"></i>
          <p> @lang('menu.subscription') <i class="right fa fa-angle-right"></i> </p>
          <ul aria-expanded="false" class="nav nav-treeview" id="hoverShow1">
            <li class="nav-item border-bottomm"> <a class="nav-link" href="{{ route('admin.subscribe.index') }}"> <i class="fa fa-circle-o nav-icon"></i>Subscription</a> </li>
            <li class="nav-item border-bottomm"> <a class="nav-link" href="{{ route('admin.subscribe.history') }}"> <i class="fa fa-circle-o nav-icon"></i>History</a> </li>
          </ul>
          </a> </li>
        @endif
        <li class="hoverWrapper nav-item border-bottomm has-treeview @if(\Request()->is('admin/settings/*') || \Request()->is('admin/profile'))active menu-open @endif"> <a href="#" class="nav-link"> <i class="nav-icon icon-settings"></i>
          <p>@lang('menu.settings') <i class="right fa fa-angle-right"></i> </p>
          <ul aria-expanded="false" class="nav nav-treeview" id="hoverShow1">
            @if(in_array("view_category", $userPermissions))
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.job-categories.index') }}" class="nav-link {{ request()->is('admin/job-categories*') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>Categories</p>
              </a> </li>
            @endif
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.candidateStatus.index') }}" class="nav-link {{ request()->is('admin/candidateStatus*') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>Interview Process</p>
              </a> </li>
            @if(in_array("view_skills", $userPermissions))
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.skills.index') }}" class="nav-link {{ request()->is('admin/skills*') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.skills')</p>
              </a> </li>
            @endif
            
            @if(!is_null($activePackage))
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->is('admin/profile*') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.myProfile')</p>
              </a> </li>
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.profile.appdup') }}" class="nav-link {{ request()->is('admin/appdup*') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>App Duplicate Setting</p>
              </a> </li>
            @if(in_array("manage_settings", $userPermissions))
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->is('admin/settings/settings') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.businessSettings')</p>
              </a> </li>
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.application-setting.index') }}" class="nav-link {{ request()->is('admin/settings/application-setting') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.applicationFormSettings')</p>
              </a> </li>
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.role-permission.index') }}" class="nav-link {{ request()->is('admin/settings/role-permission') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.rolesPermission')</p>
              </a> </li>
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.theme-settings.index') }}" class="nav-link {{ request()->is('admin/settings/theme-settings') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.themeSettings')</p>
              </a> </li>
            @if($linkedinGlobal->status == 'enable')
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.linkedin-settings.index') }}" class="nav-link {{ request()->is('admin/settings/linkedin-settings') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('menu.linkedInSettings')</p>
              </a> </li>
            @endif          
            
            @if(is_null($global->account_delete_at))
            <li class="nav-item border-bottomm"> <a href="{{ route('admin.settings.delete-account') }}" class="nav-link {{ request()->is('admin/settings/delete-account') ? 'active' : '' }}"> <i class="fa fa-circle-o nav-icon"></i>
              <p>@lang('app.deleteAccount')</p>
              </a> </li>
            @endif
            @endif
            @endif
          </ul>
          </a> </li>
        @if(!is_null($activePackage) && $activePackage->package->career_website)
        <!-- <li class="nav-header">@lang('app.miscellaneous')</li> -->
        <li class="nav-item"> @php $route = !module_enabled('Subdomain') ? route('jobs.jobOpenings') : route('jobs.jobOpenings',
          $global->career_page_link)
          @endphp <a href="{{ $route }}" target="_blank" class="nav-link"> <i class="fa fa-external-link"></i>
          <p>@lang('app.careerWebsite')</p>
          </a> </li>
        @endif
        <!-- <li class="nav-item mt-4 subscribe-btn">
        <a href="{{ route('admin.subscribe.index') }}" class="nav-link {{ request()->
        is('admin/settings/theme-settings') ? 'active' : '' }}"> <i class="fa fa-hand-pointer-o"></i>
        <p>Subscription</p>
        </a>
        </li>
        -->
        <li class="nav-item mt-4 subscribe-btn"> <a href="{{ route('admin.subscribe.index') }}" class="nav-link {{ request()->is('admin/subscribe/index*') ? 'active' : '' }}"> <i class="fa fa-hand-pointer-o"></i>
          <p>Subscription</p>
          </a> </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
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
