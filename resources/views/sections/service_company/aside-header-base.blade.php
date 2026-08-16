<?php
use App\Helper\GeneralMethod;
$lock_unlock = GeneralMethod::checksubscription();
$productAllowCompany = GeneralMethod::companiesType();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{ route('service_company.dashboard') }}"> <img alt="Logo"
                src="{{ asset('img/logo-transparent-white.svg') }}" class="logo" style="height:50px;" /> </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotone/Navigation/Angle-double-left.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                    height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <path
                            d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                            fill="#000000" fill-rule="nonzero"
                            transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
                        <path
                            d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                            fill="#000000" fill-rule="nonzero" opacity="0.5"
                            transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
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
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu"
            data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
                id="#kt_aside_menu" data-kt-menu="true">
                <!--begin::Dashboard-->
                <div class="menu-item"> <a href="{{ route('service_company.dashboard') }}"
                        class="menu-link {{ request()->is('service_company/dashboard*') ? 'active' : '' }}"> 
                        <span
                            class="menu-icon"> 
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8v-10h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span> 
                        <span class="menu-title">Dashboard</span> </a> </div>
                <!--end::Dashboard-->
                {{-- @if (in_array('add_clients', $userPermissions) || in_array('view_clients', $userPermissions) || in_array('edit_clients', $userPermissions) || in_array('delete_clients', $userPermissions))
        <!--begin::Clients-->
        <div class="menu-item"> <a href="{{ route('service_company.clients.index') }}" class="menu-link {{ request()->is('service_company/clients*') ? 'active' : '' }}"> <span class="menu-icon">
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
        @endif  --}}

                @if (@$productAllowCompany)
                     @if (in_array('view_clients', $userPermissions) )
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is('service_company/jobs/job_request*') || request()->is('service_company/clients*') ? 'show' : '' }} ">
                        <span class="menu-link"> <span class="menu-icon">
                                <!--begin::Svg Icon-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4 4h16v2H4V4Zm0 4h16v2H4V8Zm0 4h10v2H4v-2Zm0 4h10v2H4v-2Z"/>
                                    </svg>
                                </span>

                                <!--end::Svg Icon-->
                            </span> <span class="menu-title">CRM</span><span class="menu-arrow"></span> </span>
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin::Bench list-->
                            {{-- in_array('add_clients', $userPermissions) || --}}
                            {{-- in_array('edit_clients', $userPermissions) || --}}
                            {{-- in_array('delete_clients', $userPermissions) --}}
                           
                                <div class="menu-item"> <a href="{{ route('service_company.clients.index') }}"
                                        class="menu-link {{ request()->is('service_company/clients') ? 'active' : '' }}">
                                        <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span>
                                        <span class="menu-title">Clients</span> </a> </div>
                           
                            <!--end::Bench list-->

                            <div class="menu-item"> <a href="{{ asset('service_company/jobs/job_request/0') }}"
                                    class="menu-link {{ request()->is('service_company/jobs/job_request/0') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Job Request</span> </a> </div>

                        </div>
                    </div>
                    @endif
                @endif


                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->is('service_company/job-onboard') || request()->is('service_company/applicationoffer') || request()->is('service_company/interview-schedule/table-view') || request()->is('service_company/jobs') ? 'show' : '' }} ">
                    <span class="menu-link"> <span class="menu-icon">
                            <!--begin::Svg Icon-->
                           <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 2h4a2 2 0 0 1 2 2v2h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V9a3 3 0 0 1 3-3h2V4a2 2 0 0 1 2-2Zm0 4h4V4h-4v2ZM6 10v10h12V10H6Z"/>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span> <span class="menu-title">Jobs</span><span class="menu-arrow"></span> </span>
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin::Bench list-->
                        @if (in_array('view_jobs', $userPermissions))
                            <div class="menu-item"> <a href="{{ route('service_company.jobs.index') }}"
                                    class="menu-link {{ request()->is('service_company/jobs') ? 'active' : '' }}">
                                    <span class="menu-icon"><span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Jobs</span> </a> </div>
                        @endif
                        <!--end::Bench list-->


                        @if (in_array('view_schedule', $userPermissions))
                            <!--begin::Interview Tracker-->
                            <div class="menu-item {{ $lock_unlock ? '' : 'lock_unlock' }}"> <a
                                    href="@if ($lock_unlock) {{ route('service_company.interview-schedule.table-view') }}  @else {{ 'javascript:;' }} @endif"
                                    class="menu-link {{ request()->is('service_company/interview-schedule*') ? 'active' : '' }}">
                                    <span class="menu-icon">
                                        <!--begin::Svg Icon-->
                                        <span class="bullet bullet-dot"></span>
                                        <!--end::Svg Icon-->
                                    </span> <span class="menu-title">Interview Tracker</span>
                                    @if ($lock_unlock == false)
                                        <i class="fa fa-lock"></i>
                                    @endif
                                </a>
                            </div>
                            <!--end::Interview Tracker-->
                        @endif
                        @if (in_array('view_offer', $userPermissions))
                            <!--begin::Offer Tracker-->
                            <div class="menu-item {{ $lock_unlock ? '' : 'lock_unlock' }}"> <a
                                    href="@if ($lock_unlock) {{ route('service_company.applicationoffer.index') }}  @else {{ 'javascript:;' }} @endif"
                                    class="menu-link {{ request()->is('service_company/applicationoffer*') ? 'active' : '' }}">
                                    <span class="menu-icon">
                                        <!--begin::Svg Icon-->
                                        <span class="bullet bullet-dot"></span>
                                        <!--end::Svg Icon-->
                                    </span> <span class="menu-title">Offer Tracker</span>
                                    @if ($lock_unlock == false)
                                        <i class="fa fa-lock"></i>
                                    @endif
                                </a>
                            </div>
                            <!--end::Offer Tracker-->
                        @endif
                        @if (in_array('view_joining', $userPermissions))
                            <!--begin::Joining Tracker-->
                            <div class="menu-item {{ $lock_unlock ? '' : 'lock_unlock' }}"> <a
                                    href="@if ($lock_unlock) {{ route('service_company.job-onboard.index') }}  @else {{ 'javascript:;' }} @endif"
                                    class="menu-link {{ request()->is('service_company/job-onboard*') ? 'active' : '' }}">
                                    <span class="menu-icon"> <span class="bullet bullet-dot"></span></span> <span
                                        class="menu-title">Joining Tracker</span>
                                    @if ($lock_unlock == false)
                                        <i class="fa fa-lock"></i>
                                    @endif
                                </a>
                            </div>
                            <!--end::Joining Tracker-->
                        @endif


                    </div>
                </div>


                @if (in_array('view_job_applications', $userPermissions))
                    <!--begin::Candidates-->
                    <div class="menu-item {{ $lock_unlock ? '' : 'lock_unlock' }}">
                        <a href="@if ($lock_unlock) {{ route('service_company.cvtracker.index') }}  @else {{ 'javascript:;' }} @endif"
                            class="menu-link {{ request()->is('service_company/cvtracker*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v2h20v-2c0-3.3-6.7-5-10-5z"/>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Candidates</span>
                            @if ($lock_unlock == false)
                                <i class="fa fa-lock"></i>
                            @endif
                        </a>
                    </div>
                    <!--end::Candidates-->
                @endif


                {{-- @if (in_array('add_jobs', $userPermissions) || in_array('view_jobs', $userPermissions) || in_array('edit_jobs', $userPermissions) || in_array('delete_jobs', $userPermissions))
        <!--begin::Requisitions-->
        <div class="menu-item"> <a href="{{ route('service_company.jobs.index') }}" class="menu-link {{ request()->is('service_company/jobs*') ? 'active' : '' }}"> <span class="menu-icon">
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
        @endif --}}

                @if (in_array('view_staffing_partners', $userPermissions))
                    <!--begin::Staffing Partners-->
                    <div class="menu-item"> <a href="{{ route('service_company.consultancy.index') }}"
                            class="menu-link {{ request()->is('service_company/consultancy*') ? 'active' : '' }}">
                            <span class="menu-icon">
                               <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 22V2h18v20h-2v-2H5v2H3Zm4-4h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2ZM7 14h2v-2H7v2Zm4 0h2v-2h-2v2Zm4 0h2v-2h-2v2ZM7 8v2h2V8H7Zm4 2h2V8h-2v2Zm4 0h2V8h-2v2Z"/>
                                </svg>
                                </span>

                            </span>
                                        
                        <span class="menu-title">{{__('messages.Agencies')}}</span> </a> </div>
                    <!--end::Staffing Partners-->
                @endif

                {{-- <div class="menu-item">
          <a href="{{ route('service_company.dashboard.meeting-schedule-list') }}" class="menu-link {{ request()->is('super-admin/meetingstoreScheduleList') ? 'active' : '' }}">
             <span class="menu-icon">
                <span class="svg-icon svg-icon-2">
                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                     <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                       <rect x="0" y="0" width="24" height="24"/>
                       <path d="M5,6 L19,6 C19.5522847,6 20,6.44771525 20,7 L20,17 L4,17 L4,7 C4,6.44771525 4.44771525,6 5,6 Z" fill="#000000"/>
                       <rect fill="#000000" opacity="0.3" x="1" y="18" width="22" height="1" rx="0.5"/>
                     </g>
                   </svg>
                   </span>
             </span>
             <span class="menu-title">Meeting Tracker</span>
          </a>
        </div> --}}

                @if (in_array('view_bench', $userPermissions))
                    <!--begin::Beach Candidates-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is('service_company/interview-view/bench-candidates') || request()->is('service_company/bench-candidates*') ? 'show' : '' }} ">
                        <span class="menu-link">
                             <span class="menu-icon">
                                <!--begin::Svg Icon-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 2.01 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span>
                             <span class="menu-title">Talent Marketplace</span>
                             <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin::Bench list-->
                            <div class="menu-item">
                                <a href="{{ route('service_company.bench-candidates.index') }}"
                                    class="menu-link {{ request()->is('service_company/bench-candidates') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Inhouse Talents</span>
                                </a>
                            </div>
                            <!--end::Bench list-->

                            <!--begin::Staffing Dashboard-->
                            <div class="menu-item">
                                <a href="{{ route('service_company.bench-candidates.staffingDashboard') }}"
                                    class="menu-link {{ request()->is('service_company/bench-candidates/staffing-dashboard') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Staffing Dashboard</span>
                                </a>
                            </div>
                            <!--end::Staffing Dashboard-->

                            <div class="menu-item"> 
                                <a href="{{ route('service_company.bench-candidates.bench-table-view') }}"
                                    class="menu-link {{ request()->is('service_company/interview-view/bench-candidates') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Talent Interviews</span>
                                </a> 
                            </div>

                            <!--begin::Bench Offer list-->
                            <div class="menu-item"> 
                                <a href="{{ route('service_company.bench-candidates.offerListing') }}"
                                    class="menu-link {{ request()->is('service_company/bench-candidates/offerListing') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Talent Shortlisted</span> 
                                </a> 
                            </div>
                            <!--end::Bench Offer list-->
                            <!--begin::Bench Placed list-->
                            <div class="menu-item"> 
                                <a href="{{ route('service_company.bench-candidates.placedListing') }}"
                                    class="menu-link {{ request()->is('service_company/bench-candidates/placedListing') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Talent Placed</span> 
                                </a> 
                            </div>
                            <!--end::Bench Placed list-->
                            <!--begin::Talent Pool-->
                            <div class="menu-item"> 
                                <a href="javascript:;" id="shareBenchCandidateBtn" data-url="{{url('bench-condidates/'.str_replace(" ","-",@$companyName).'/'.@$companyId.'')}}" class="menu-link {{ request()->is('service_company/bench-candidates/talent_pool') ? 'active' : '' }}"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">Share Talent List</span> 
                                </a> 
                            </div>
                            <!--end::Talent Pool-->
                            <!--begin::All Bench Requisition/Client : vaild for products based companies-->
                            {{-- <div class="menu-item"> <a href="javascritp:void(0);" class="menu-link"> <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span class="menu-title">All Bench Requisition/Client</span> </a> </div> --}}
                            <!--end::All Bench Requisition/Client-->
                        </div>
                    </div>
                    <!--end::Beach Candidates-->
                @endif

                <!--begin::Services-->
                {{-- <div class="menu-item"> 
          <a href="{{ route('service_company.jobs.jobServices') }}" class="menu-link"> 
         <span class="menu-icon">
                  <span class="svg-icon svg-icon-primary svg-icon-2x">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                         
                          <defs/>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                              <rect x="0" y="0" width="24" height="24"/>
                              <path d="M5,2 L19,2 C20.1045695,2 21,2.8954305 21,4 L21,6 C21,7.1045695 20.1045695,8 19,8 L5,8 C3.8954305,8 3,7.1045695 3,6 L3,4 C3,2.8954305 3.8954305,2 5,2 Z M11,4 C10.4477153,4 10,4.44771525 10,5 C10,5.55228475 10.4477153,6 11,6 L16,6 C16.5522847,6 17,5.55228475 17,5 C17,4.44771525 16.5522847,4 16,4 L11,4 Z M7,6 C7.55228475,6 8,5.55228475 8,5 C8,4.44771525 7.55228475,4 7,4 C6.44771525,4 6,4.44771525 6,5 C6,5.55228475 6.44771525,6 7,6 Z" fill="#000000" opacity="0.3"/>
                              <path d="M5,9 L19,9 C20.1045695,9 21,9.8954305 21,11 L21,13 C21,14.1045695 20.1045695,15 19,15 L5,15 C3.8954305,15 3,14.1045695 3,13 L3,11 C3,9.8954305 3.8954305,9 5,9 Z M11,11 C10.4477153,11 10,11.4477153 10,12 C10,12.5522847 10.4477153,13 11,13 L16,13 C16.5522847,13 17,12.5522847 17,12 C17,11.4477153 16.5522847,11 16,11 L11,11 Z M7,13 C7.55228475,13 8,12.5522847 8,12 C8,11.4477153 7.55228475,11 7,11 C6.44771525,11 6,11.4477153 6,12 C6,12.5522847 6.44771525,13 7,13 Z" fill="#000000"/>
                              <path d="M5,16 L19,16 C20.1045695,16 21,16.8954305 21,18 L21,20 C21,21.1045695 20.1045695,22 19,22 L5,22 C3.8954305,22 3,21.1045695 3,20 L3,18 C3,16.8954305 3.8954305,16 5,16 Z M11,18 C10.4477153,18 10,18.4477153 10,19 C10,19.5522847 10.4477153,20 11,20 L16,20 C16.5522847,20 17,19.5522847 17,19 C17,18.4477153 16.5522847,18 16,18 L11,18 Z M7,20 C7.55228475,20 8,19.5522847 8,19 C8,18.4477153 7.55228475,18 7,18 C6.44771525,18 6,18.4477153 6,19 C6,19.5522847 6.44771525,20 7,20 Z" fill="#000000"/>
                          </g>
                      </svg>
                  </span>
                </span>
            <span class="menu-title">Services</span></a></div> --}}
                <!--end::Services-->


                @if (in_array('view_report_25_oct_2022', $userPermissions))
                    <!--begin::Report-->
                    <div class="menu-item"> <a href="{{ route('service_company.customreport.index') }}"
                            class="menu-link {{ request()->is('service_company/customreport*') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <!-- <span class="svg-icon svg-icon-2">
                        <i style="font-size:15px" class="fa">&#xf080;</i>
                     </span> -->
                                <!--begin::Svg Icon-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <rect fill="#000000" opacity="0.3" x="17" y="4" width="3"
                                                height="13" rx="1.5" />
                                            <rect fill="#000000" opacity="0.3" x="12" y="9" width="3"
                                                height="8" rx="1.5" />
                                            <path
                                                d="M5,19 L20,19 C20.5522847,19 21,19.4477153 21,20 C21,20.5522847 20.5522847,21 20,21 L4,21 C3.44771525,21 3,20.5522847 3,20 L3,4 C3,3.44771525 3.44771525,3 4,3 C4.55228475,3 5,3.44771525 5,4 L5,19 Z"
                                                fill="#000000" fill-rule="nonzero" />
                                            <rect fill="#000000" opacity="0.3" x="7" y="11" width="3"
                                                height="6" rx="1.5" />
                                        </g>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span> <span class="menu-title">@lang('app.reports')</span> </a> </div>
                    <!--end::Report-->
                @endif
                <!--begin::Heading-->
                <!--begin::Subscription-->
                <div class="menu-item d-none">
                    <div class="menu-content pt-8 pb-2"> <span
                            class="menu-section text-muted text-uppercase fs-8 ls-1">Subscription</span> </div>
                </div>
                <!--end::Subscription-->
                <!--begin::Settings-->
                <div class="menu-item d-none">
                    <div class="menu-content pt-8 pb-2"> <span
                            class="menu-section text-muted text-uppercase fs-8 ls-1">@lang('menu.settings')1</span> </div>
                </div>
                <!--end::Settings-->
                <!--end::Heading-->
                <!--begin::Company Profile-->
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ request()->is('service_company/role-permission') || request()->is('service_company/account*') ? 'show' : '' }} {{ request()->is('service_company/team*') ? 'show' : '' }} {{ request()->is('service_company/settings/role-permission*') ? 'show' : '' }}">
                    <span class="menu-link"> <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Zm9.94-2.41-1.66-1.26a7.07 7.07 0 0 0 .01-2.66l1.65-1.26a.5.5 0 0 0 .12-.67l-1.9-3.3a.5.5 0 0 0-.64-.21l-1.94.78a7.05 7.05 0 0 0-2.3-1.33l-.3-2.05A.5.5 0 0 0 14.5 1h-5a.5.5 0 0 0-.49.41l-.3 2.05a7.08 7.08 0 0 0-2.3 1.33l-1.94-.78a.5.5 0 0 0-.64.21l-1.9 3.3a.5.5 0 0 0 .12.67l1.65 1.26a7.07 7.07 0 0 0 .01 2.66l-1.66 1.26a.5.5 0 0 0-.12.67l1.9 3.3c.13.23.41.31.64.21l1.94-.78a7.05 7.05 0 0 0 2.3 1.33l.3 2.05c.04.25.24.41.49.41h5c.25 0 .45-.16.49-.41l.3-2.05a7.08 7.08 0 0 0 2.3-1.33l1.94.78c.23.1.51.02.64-.21l1.9-3.3a.5.5 0 0 0-.12-.67ZM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
                                </svg>
                            </span>

                            <!--end::Svg Icon-->
                        </span> <span class="menu-title">Company Profile</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @if (in_array('view_settings', $userPermissions))
                            <!--begin::Settings-->
                            <div class="menu-item"> <a href="{{ route('service_company.account.index') }}"
                                    class="menu-link {{ request()->is('service_company/account*') && !request('tab') && request('ads') !== 'show' ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Settings</span> </a> </div>
                            <!--end::Settings-->
                            @if ($productAllowCompany)
                                <!--begin::Commercials-->
                                <div class="menu-item"> <a href="{{ route('service_company.account.index', ['tab' => 'services']) }}"
                                        class="menu-link {{ request('tab') === 'services' ? 'active' : '' }}">
                                        <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                            class="menu-title">Commercials</span> </a> </div>
                                <!--end::Commercials-->
                                <!--begin::Ads Settings-->
                                <div class="menu-item"> <a href="{{ route('service_company.account.index', ['tab' => 'ads']) }}"
                                        class="menu-link {{ request('tab') === 'ads' || request('ads') === 'show' ? 'active' : '' }}">
                                        <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                            class="menu-title">Ads Settings</span> </a> </div>
                                <!--end::Ads Settings-->
                            @endif
                        @endif
                        @if (in_array('view_team', $userPermissions))
                            <!--begin::Team-->
                            <div class="menu-item"> <a href="{{ route('service_company.team.index') }}"
                                    class="menu-link {{ request()->is('service_company/team*') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">Team</span> </a> </div>
                            <!--end::Team-->
                        @endif
                        @if (in_array('view_roles_permission', $userPermissions))
                            <!--begin::Roles and Permission-->
                            <div class="menu-item"> <a href="{{ route('service_company.role-permission.index') }}"
                                    class="menu-link {{ request()->is('service_company/role-permission*') ? 'active' : '' }}">
                                    <span class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                        class="menu-title">@lang('menu.rolesPermission')</span> </a> </div>
                            <!--end::Roles and Permission-->
                        @endif
                        @if (in_array('view_website', $userPermissions))
                            <!--begin::Career Website-->
                            @if (!is_null($activePackage) && $activePackage->package->career_website)
                                <div class="menu-item"> @php $route = !module_enabled('Subdomain')
                                        ? route('jobs.jobOpenings')
                                        : route('jobs.jobOpenings', $global->career_page_link);
                                @endphp <a href="{{ $route }}"
                                        class="menu-link {{ request()->is('') ? 'active' : '' }}"> <span
                                            class="menu-bullet"> <span class="bullet bullet-dot"></span> </span> <span
                                            class="menu-title">@lang('app.careerWebsite')</span> </a> </div>
                            @endif
                            <!--end::Career Website-->
                        @endif
                    </div>
                </div>
                <!--end::Company Profile-->
                @if (in_array('view_subscription', $userPermissions))
                    <!--begin::Subscription-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion  {{ request()->is('service_company/subscribe*') ? 'show' : '' }}">
                        <span class="menu-link"> <span class="menu-icon">
                                <!--begin::Svg Icon-->
                               <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2 4h20a1 1 0 0 1 1 1v3H1V5a1 1 0 0 1 1-1Zm-1 6h22v9a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-9Zm4 6h4v2H5v-2Z"/>
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </span> <span class="menu-title">Subscription &amp; Wallets</span><span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                            <div class="menu-item">
                                <a class="" href="{{ route('service_company.subscribe.newindex') }}#tab=overview"> <span
                                        class="menu-link"> <span class="menu-bullet"><span class="bullet bullet-dot"></span></span> <span
                                            class="menu-title">Overview</span> </span> </a>
                            </div>
                            <div class="menu-item">
                                <a class="" href="{{ route('service_company.subscribe.newindex') }}#tab=transactions"> <span
                                        class="menu-link"> <span class="menu-bullet"><span class="bullet bullet-dot"></span></span> <span
                                            class="menu-title">Transactions</span> </span> </a>
                            </div>
                            <div class="menu-item">
                                <a class="" href="{{ route('service_company.subscribe.transectionLog') }}?pagetype=operationaltab&page=1">
                                    <span
                                        class="menu-link {{ request()->is('subscribe/transection-log') ? 'active' : '' }}">
                                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span> <span
                                            class="menu-title">Coins Activity</span> </span> </a>
                            </div>
                        </div>
                    </div>
                    <!--end::Subscription-->
                @endif


                    <!--begin::Career Website-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is('career-jobs*') ? 'show' : '' }}">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 2h4a2 2 0 0 1 2 2v2h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V9a3 3 0 0 1 3-3h2V4a2 2 0 0 1 2-2Zm0 4h4V4h-4v2ZM6 10v10h12V10H6Z"/>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title" id="changeTextShare">Career Website</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin::Jobs-->
                            <div class="menu-item">
                                <a id="shareJobsBtn"
                                   data-url="{{ url('career-jobs/'.str_replace(' ','-',$companyName).'/'.auth()->user()->company_id) }}"
                                   href="javascript:void(0);"
                                   class="menu-link {{ request()->is('career-jobs*') ? 'active' : '' }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Jobs</span>
                                </a>
                            </div>
                            <!--end::Jobs-->
                            <!--begin::Settings-->
                            <div class="menu-item">
                                <a href="{{ route('service_company.career.page.settings') }}"
                                   class="menu-link {{ request()->is('service_company/career-page/settings*') ? 'active' : '' }}">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Settings</span>
                                </a>
                            </div>
                            <!--end::Settings-->
                        </div>
                    </div>
                    <!--end::Career Website-->
               

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->

    <!--begin::Support-->
    <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer"> 
      <a href="{{ route('service_company.tickets.ticketList') }}" class="btn btn-custom btn-primary w-100"
            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="Subscription"> 
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24" />
                  <!-- Phone body -->
                  <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#1E88E5" opacity="0.3" />
                  <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#1E88E5" />
                  <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1" />
                  <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1" />
                  <!-- Headset -->
                  <path d="M6,8 C6,6.34314575 7.34314575,5 9,5 L15,5 C16.6568542,5 18,6.34314575 18,8 L18,9 C18,10.6568542 16.6568542,12 15,12 L14,12 L14,10 C14,9.44771525 13.5522847,9 13,9 C12.4477153,9 12,9.44771525 12,10 L12,12 L11,12 L11,10 C11,9.44771525 10.5522847,9 10,9 C9.44771525,9 9,9.44771525 9,10 L9,12 L8,12 C6.34314575,12 5,10.6568542 5,9 L5,8 Z" fill="#1E88E5" />
              </g>
          </svg>
          
          
          <span class="btn-label">{{ __('messages.suppoer_tickets') }}</span>
           
        </a> 
    </div>
    <!--end::Support-->


    <!--begin::Footer-->
    <div class="aside-footer flex-column-auto pt-5 pb-7 px-5 d-none" id="kt_aside_footer"> 
      <a href="{{ route('service_company.subscribe.index') }}" class="btn btn-custom btn-primary w-100"
            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss-="click" title="Subscription"> <span
                class="btn-label">Subscription</span>
            <!--begin::Svg Icon | path: icons/duotone/General/Clipboard.svg-->
            <span class="svg-icon btn-icon svg-icon-2">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                    height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path
                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                            fill="#000000" opacity="0.3" />
                        <path
                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                            fill="#000000" />
                        <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2"
                            rx="1" />
                        <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2"
                            rx="1" />
                    </g>
                </svg>
            </span>
            <!--end::Svg Icon-->
        </a> 
    </div>
    <!--end::Footer-->
</div>
