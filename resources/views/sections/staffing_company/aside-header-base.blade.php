<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
  <!--begin::Brand-->
  <div class="aside-logo flex-column-auto" id="kt_aside_logo">
    <!--begin::Logo-->
    <a href="{{ route('staffing_company.dashboard') }}"> <img alt="Logo" src="{{ asset('img/logo-transparent-white.png')}}" class="logo" style="height:50px;" /> </a>
    <!--end::Logo-->
    <!--begin::Aside toggler-->
    <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
      <!--begin::Svg Icon | path: icons/duotone/Navigation/Angle-double-left.svg-->
      <span class="svg-icon svg-icon-1 rotate-180">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <polygon points="0 0 24 0 24 24 0 24" />
          <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
          <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.5" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
        </g>
      </svg>
      </span>
      <!--end::Svg Icon-->
    </div>
    <!--end::Aside toggler-->
  </div>
  <!--end::Brand-->
  <!--begin::Aside menu-->
  <div class="aside-menu flex-column-fluid">
    <!--begin::Aside Menu-->
    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
      <!--begin::Menu-->
      <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
        <!--begin::Dashboard-->
        <div class="menu-item"> <a href="{{ route('staffing_company.dashboard') }}" class="menu-link {{ request()->is('staffing_company/dashboard*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
            <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Dashboard</span> </a> </div>
        <!--end::Dashboard-->
        <?php 

	//echo '<pre>'; print_r($company_type); exit; 
?>
        @if($global_company_type !=3)
        @if(in_array("add_clients", $userPermissions) || in_array("view_clients", $userPermissions) || in_array("edit_clients", $userPermissions) || in_array("delete_clients", $userPermissions))
        <!--begin::Clients-->
        <div class="menu-item"> <a href="{{ route('staffing_company.clients.index') }}" class="menu-link {{ request()->is('staffing_company/clients*') ? 'active' : '' }}"> <span class="menu-icon">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z" fill="#000000"/>
              <rect fill="#FFFFFF" x="13" y="8" width="3" height="3" rx="1"/>
              <path d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z" fill="#000000" opacity="0.3"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Clients</span> </a> </div>
        <!--end::Clients-->
        @endif
        @endif            
        
        @if(in_array("add_jobs", $userPermissions) || in_array("view_jobs", $userPermissions) || in_array("edit_jobs", $userPermissions) || in_array("delete_jobs", $userPermissions))
        <!--begin::Requisitions-->
        <div class="menu-item"> <a href="{{ route('staffing_company.jobs.index') }}" class="menu-link {{ request()->is('staffing_company/jobs*') ? 'active' : '' }}"> <span class="menu-icon">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
              <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Requisitions</span> </a> </div>
        <!--end::Requisitions-->
        @endif
        
        @if(in_array("add_job_applications", $userPermissions) || in_array("view_job_applications", $userPermissions) || in_array("edit_job_applications", $userPermissions) || in_array("delete_job_applications", $userPermissions))
        <!--begin::Candidates-->
        <div class="menu-item"> <a href="{{ route('staffing_company.cvtracker.index') }}" class="menu-link {{ request()->is('staffing_company/cvtracker*') ? 'active' : '' }}"> <span class="menu-icon">
          <!--<span class="svg-icon svg-icon-2"><i class="fa fa-users"></i></span>-->
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24"/>
              <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
              <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Candidates</span> </a> </div>
        <!--end::Candidates-->
        @endif 
        
        @if(in_array("add_schedule", $userPermissions) || in_array("view_schedule", $userPermissions) || in_array("edit_schedule", $userPermissions) || in_array("delete_schedule", $userPermissions))
        <!--begin::Interview Tracker-->
        <div class="menu-item"> <a href="{{ route('staffing_company.interview-schedule.table-view') }}" class="menu-link {{ request()->is('staffing_company/interview-schedule*') ? 'active' : '' }}"> <span class="menu-icon">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M5,6 L19,6 C19.5522847,6 20,6.44771525 20,7 L20,17 L4,17 L4,7 C4,6.44771525 4.44771525,6 5,6 Z" fill="#000000"/>
              <rect fill="#000000" opacity="0.3" x="1" y="18" width="22" height="1" rx="0.5"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Interview Tracker</span> </a> </div>
        <!--end::Interview Tracker-->
        @endif
        
        @if(in_array("add_offer", $userPermissions) || in_array("view_offer", $userPermissions) || in_array("edit_offer", $userPermissions))
        <!--begin::Offer Tracker-->
        <div class="menu-item"> <a href="{{ route('staffing_company.applicationoffer.index') }}" class="menu-link {{ request()->is('staffing_company/applicationoffer*') ? 'active' : '' }}"> <span class="menu-icon">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <path d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z" fill="#000000" opacity="0.3"/>
              <path d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z" fill="#000000"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Offer Tracker</span> </a> </div>
        <!--end::Offer Tracker-->
        @endif         
        
        @if(in_array("add_joining", $userPermissions) || in_array("view_joining", $userPermissions) || in_array("edit_joining", $userPermissions))
        <!--begin::Joining Tracker-->
        <div class="menu-item"> <a href="{{ route('staffing_company.job-onboard.index') }}" class="menu-link {{ request()->is('staffing_company/job-onboard*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"><i style="font-size:15px" class="fa">&#xf2b5;</i></span></span> <span class="menu-title">Joining Tracker</span> </a> </div>
        <!--end::Joining Tracker-->
        @endif
        
        @if(in_array("add_staffing_partners", $userPermissions) || in_array("view_staffing_partners", $userPermissions) || in_array("edit_staffing_partners", $userPermissions) || in_array("delete_staffing_partners", $userPermissions))
        <!--begin::Staffing Partners-->
        <div class="menu-item"> <a href="{{ route('staffing_company.consultancy.index') }}" class="menu-link {{ request()->is('staffing_company/consultancy*') ? 'active' : '' }}"> <span class="menu-icon"> <span class="svg-icon svg-icon-2"><i class="fa fa-outdent"></i></span></span> <span class="menu-title">Staffing Partners</span> </a> </div>
        <!--end::Staffing Partners-->
        @endif 			
        
        @if(in_array("view_report", $userPermissions))
        <!--begin::Report-->
        <div class="menu-item"> <a href="{{ route('staffing_company.customreport.index') }}" class="menu-link {{ request()->is('staffing_company/customreport*') ? 'active' : '' }}"> <span class="menu-icon">
          <!-- <span class="svg-icon svg-icon-2">
                        <i style="font-size:15px" class="fa">&#xf080;</i>
                     </span> -->
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24"/>
              <rect fill="#000000" opacity="0.3" x="17" y="4" width="3" height="13" rx="1.5"/>
              <rect fill="#000000" opacity="0.3" x="12" y="9" width="3" height="8" rx="1.5"/>
              <path d="M5,19 L20,19 C20.5522847,19 21,19.4477153 21,20 C21,20.5522847 20.5522847,21 20,21 L4,21 C3.44771525,21 3,20.5522847 3,20 L3,4 C3,3.44771525 3.44771525,3 4,3 C4.55228475,3 5,3.44771525 5,4 L5,19 Z" fill="#000000" fill-rule="nonzero"/>
              <rect fill="#000000" opacity="0.3" x="7" y="11" width="3" height="6" rx="1.5"/>
            </g>
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">@lang('app.reports')</span> </a> </div>
        <!--end::Report-->
        @endif
        <!--begin::Heading-->
        <div class="menu-item d-none">
          <div class="menu-content pt-8 pb-2"> <span class="menu-section text-muted text-uppercase fs-8 ls-1">Subscription</span> </div>
        </div>
        <!--begin::Settings-->
        <div class="menu-item d-none">
          <div class="menu-content pt-8 pb-2"> <span class="menu-section text-muted text-uppercase fs-8 ls-1">@lang('menu.settings')1</span> </div>
        </div>
        <!--end::Settings-->
        <!--end::Heading-->
        <!--begin::Company Profile-->
        <div data-kt-menu-trigger="click" class="menu-item menu-accordion"> <span class="menu-link"> <span class="menu-icon">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
            <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">Company Profile</span><span class="menu-arrow"></span> </span>
          <div class="menu-sub menu-sub-accordion"> @if(in_array("add_settings", $userPermissions) || in_array("view_settings", $userPermissions) || in_array("edit_settings", $userPermissions))
            <!--begin::Settings-->
            <div class="menu-item"> <a href="{{ route('staffing_company.account.index') }}" class="menu-link {{ request()->is('staffing_company/account*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">Settings</span> </a> </div>
            <!--end::Settings-->
            @endif
            
            
            @if(in_array("add_team", $userPermissions) || in_array("view_team", $userPermissions) || in_array("edit_team", $userPermissions) || in_array("delete_team", $userPermissions))
            <!--begin::Team-->
            <div class="menu-item"> <a href="{{ route('staffing_company.team.index') }}" class="menu-link {{ request()->is('staffing_company/team*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">Team</span> </a> </div>
            <!--end::Team-->
            @endif
            
            
            @if(in_array("add_roles_permission", $userPermissions) || in_array("view_roles_permission", $userPermissions) || in_array("edit_roles_permission", $userPermissions) || in_array("delete_roles_permission", $userPermissions))
            <!--begin::Roles and Permission-->
            <div class="menu-item"> <a href="{{ route('staffing_company.role-permission.index') }}" class="menu-link {{ request()->is('staffing_company/settings/role-permission*') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('menu.rolesPermission')</span> </a> </div>
            <!--end::Roles and Permission-->
            @endif
            
            
            @if(in_array("add_website", $userPermissions) || in_array("view_website", $userPermissions) || in_array("edit_website", $userPermissions))
            <!--begin::Career Website-->
            @if(!is_null($activePackage) && $activePackage->package->career_website)
            <div class="menu-item"> @php $route = !module_enabled('Subdomain') ? route('jobs.jobOpenings') : route('jobs.jobOpenings',
              $global->career_page_link)
              @endphp <a href="{{ $route }}" class="menu-link {{ request()->is('') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">@lang('app.careerWebsite')</span> </a> </div>
            @endif
            <!--end::Career Website-->
            @endif </div>
        </div>
        <!--end::Company Profile-->
        @if(in_array("add_subscription", $userPermissions) || in_array("view_subscription", $userPermissions))
        <!--begin::Subscription-->
        <div data-kt-menu-trigger="click" class="menu-item menu-accordion"> <span class="menu-link"> <span class="menu-icon">
          <!--begin::Svg Icon-->
          <span class="svg-icon svg-icon-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
            <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
          </svg>
          </span>
          <!--end::Svg Icon-->
          </span> <span class="menu-title">@lang('menu.subscription')</span><span class="menu-arrow"></span> </span>
          <div class="menu-sub menu-sub-accordion menu-active-bg">
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion"> <a class="" href="{{ route('staffing_company.subscribe.index') }}"> <span class="menu-link"> <span class="menu-bullet"><span class="bullet bullet-dot"></span></span> <span class="menu-title">Subscription</span> </span> </a> <a class="" href="{{ route('staffing_company.subscribe.history') }}"> <span class="menu-link"> <span class="menu-bullet"><span class="bullet bullet-dot"></span></span> <span class="menu-title">History</span> </span> </a> </div>
          </div>
        </div>
        <!--end::Subscription-->
        @endif </div>
      <!--end::Menu-->
    </div>
    <!--end::Aside Menu-->
  </div>
  <!--end::Aside menu-->
  <!--begin::Footer-->
  <div class="aside-footer flex-column-auto pt-5 pb-7 px-5 d-none" id="kt_aside_footer"> <a href="{{ route('staffing_company.subscribe.index') }}" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="Subscription"> <span class="btn-label">Subscription</span>
    <!--begin::Svg Icon | path: icons/duotone/General/Clipboard.svg-->
    <span class="svg-icon btn-icon svg-icon-2">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24" />
        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3" />
        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000" />
        <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1" />
        <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1" />
      </g>
    </svg>
    </span>
    <!--end::Svg Icon-->
    </a> </div>
  <!--end::Footer-->
</div>
