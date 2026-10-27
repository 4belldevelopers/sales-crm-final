<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="{{ route('superadmin.dashboard.index') }}">
            <img alt="Logo" src="{{ asset('img/logo-transparent-white.svg') }}" class="logo"
                style="height:50px;" />
        </a>
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
                <div class="menu-item">
                    <a href="{{ route('superadmin.dashboard.index') }}"
                        class="menu-link {{ request()->is('super-admin/dashboard*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                        fill="#000000" opacity="0.3" />
                                    <path
                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                        fill="#000000" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>
                <!--end::Dashboard-->


                 <!--begin::Dashboard-->
                 <div class="menu-item">
                    <a href="{{url('/super-admin/subscription/all') }}"
                        class="menu-link {{ request()->is('super-admin/subscription/all') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                        fill="#000000" opacity="0.3" />
                                    <path
                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                        fill="#000000" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Subscription</span>
                    </a>
                </div>
                <!--end::Subscription-->

                <!--begin::Custom Plan Requests-->
                <div class="menu-item">
                    <a href="{{ route('superadmin.custom-plan-requests.index') }}"
                        class="menu-link {{ request()->is('super-admin/custom-plan-requests*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M10.8,11.2 C10.4,10.8 9.8,10.8 9.4,11.2 C9,11.6 9,12.2 9.4,12.6 L11.4,14.6 C11.8,15 12.4,15 12.8,14.6 L16.8,10.6 C17.2,10.2 17.2,9.6 16.8,9.2 C16.4,8.8 15.8,8.8 15.4,9.2 L12.1,12.5 L10.8,11.2 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Custom Plan Requests</span>
                    </a>
                </div>
                <!--end::Custom Plan Requests-->
                
                <?php
                
                //echo '<pre>'; print_r($company_type); exit;
                ?>


                <!--begin::company-->
                <div class="menu-item">
                    <a href="{{ route('superadmin.company.index') }}"
                        class="menu-link {{ request()->is('super-admin/company*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M13.5,21 L13.5,18 C13.5,17.4477153 13.0522847,17 12.5,17 L11.5,17 C10.9477153,17 10.5,17.4477153 10.5,18 L10.5,21 L5,21 L5,4 C5,2.8954305 5.8954305,2 7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,21 L13.5,21 Z M9,4 C8.44771525,4 8,4.44771525 8,5 L8,6 C8,6.55228475 8.44771525,7 9,7 L10,7 C10.5522847,7 11,6.55228475 11,6 L11,5 C11,4.44771525 10.5522847,4 10,4 L9,4 Z M14,4 C13.4477153,4 13,4.44771525 13,5 L13,6 C13,6.55228475 13.4477153,7 14,7 L15,7 C15.5522847,7 16,6.55228475 16,6 L16,5 C16,4.44771525 15.5522847,4 15,4 L14,4 Z M9,8 C8.44771525,8 8,8.44771525 8,9 L8,10 C8,10.5522847 8.44771525,11 9,11 L10,11 C10.5522847,11 11,10.5522847 11,10 L11,9 C11,8.44771525 10.5522847,8 10,8 L9,8 Z M9,12 C8.44771525,12 8,12.4477153 8,13 L8,14 C8,14.5522847 8.44771525,15 9,15 L10,15 C10.5522847,15 11,14.5522847 11,14 L11,13 C11,12.4477153 10.5522847,12 10,12 L9,12 Z M14,12 C13.4477153,12 13,12.4477153 13,13 L13,14 C13,14.5522847 13.4477153,15 14,15 L15,15 C15.5522847,15 16,14.5522847 16,14 L16,13 C16,12.4477153 15.5522847,12 15,12 L14,12 Z"
                                            fill="#000000" />
                                        <rect fill="#FFFFFF" x="13" y="8" width="3" height="3"
                                            rx="1" />
                                        <path
                                            d="M4,21 L20,21 C20.5522847,21 21,21.4477153 21,22 L21,22.4 C21,22.7313708 20.7313708,23 20.4,23 L3.6,23 C3.26862915,23 3,22.7313708 3,22.4 L3,22 C3,21.4477153 3.44771525,21 4,21 Z"
                                            fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">@lang('menu.companies')</span>
                    </a>
                </div>
                <!--end::company-->

                <!--begin::Packages-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.packages.index') }}"
                        class="menu-link {{ request()->is('super-admin/packages*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                            fill="#000000" />
                                        <path
                                            d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                            fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">@lang('menu.packages')</span>
                    </a>
                </div>
                <!--end::Packages-->

                <!--begin::Package Feature-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.package.package_feature') }}"
                        class="menu-link {{ request()->is('super-admin/package-features') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M5,6 L19,6 C19.5522847,6 20,6.44771525 20,7 L20,17 L4,17 L4,7 C4,6.44771525 4.44771525,6 5,6 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="1" y="18" width="22"
                                            height="1" rx="0.5" />
                                    </g>
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Package Feature</span>
                    </a>
                </div>
                <!--end::Package Feature-->

                <!--begin::Job Requests-->
                
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('super-admin/jobs*') ? 'show' : '' }} ">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Jobs</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        <!--begin::>All Jobs-->
                        <div class="menu-item">
                            <a href="{{ url('super-admin/jobs/job-lists/0') }}"
                                class="menu-link {{ request()->is('super-admin/jobs/job-lists/0') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">All Jobs</span>
                            </a>
                        </div>
                        <!--end::>All Jobs-->
                        <!--begin::Job Requests-->
                        <div class="menu-item">
                            <a href="{{ url('super-admin/jobs/job_request/0') }}"
                                class="menu-link {{ request()->is('super-admin/jobs/job_request/0') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Job Requests</span>
                            </a>
                        </div>
                        <!--end::Job Requests-->
                    </div>
                </div>                
                <!--end::Job Requests-->


                 <!--begin::All Candidates-->
                 <div class="menu-item">
                    <a href="{{ url('super-admin/candidate-lists') }}"
                        class="menu-link {{ request()->is('super-admin/candidate-lists') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                            fill="#000000" />
                                        <path
                                            d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                            fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">All Candidates</span>
                    </a>
                </div>
                <!--end::All Candidates-->





                <!--begin::Teams-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.team.index') }}"
                        class="menu-link {{ request()->is('super-admin/defaultpackages*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Teams</span>
                    </a>
                </div>
                 <!--end::Teams-->

                <!--begin::Roles Permission-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="javascript:;"
                        class="menu-link {{ request()->is('super-admin/defaultpackages*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7"
                                            height="2" rx="1" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">Roles Permission</span>
                    </a>
                </div>
                 <!--end::Roles Permission-->

                <!--begin::Meeting Tracker-->
                <div class="menu-item">
                    <a href="{{ route('superadmin.company.meeting-schedule-list') }}"
                        class="menu-link {{ request()->is('super-admin/meetingstoreScheduleList') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M5,6 L19,6 C19.5522847,6 20,6.44771525 20,7 L20,17 L4,17 L4,7 C4,6.44771525 4.44771525,6 5,6 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="1" y="18" width="22"
                                            height="1" rx="0.5" />
                                    </g>
                                </svg>
                            </span>
                        </span>
                        <span class="menu-title">Meeting Tracker</span>
                    </a>
                </div>
                 <!--end::Meeting Tracker-->

                  <!--begin:: JD Details-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('super-admin/jd-list*') ? 'show' : '' }} ">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">JD Details</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        <!--begin::cmsGeneral-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.jdlist', 'pending') }}"
                                class="menu-link {{ request()->is('super-admin/jd-list/pending') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">JD Pending</span>
                            </a>
                        </div>
                        <!--end::cmsGeneral-->
                        <!--begin::cmsGeneral-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.jdlist', 'approved') }}"
                                class="menu-link {{ request()->is('super-admin/jd-list/approved') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">JD Approved</span>
                            </a>
                        </div>
                        <!--end::cmsGeneral-->
                        <!--begin::cmsGeneral-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.jdlist', 'decline') }}"
                                class="menu-link {{ request()->is('super-admin/jd-list/decline') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">JD Decline</span>
                            </a>
                        </div>
                        <!--end::cmsGeneral-->
                    </div>
                </div>
                 <!--end::JD Details-->



                  <!--begin::JD Salary Breakup-->
                    <div class="menu-item">
                        <a href="{{ route('superadmin.company.offerAmountChange') }}"
                            class="menu-link {{ request()->is('super-admin/offer-amount-change') ? 'active' : '' }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M5,6 L19,6 C19.5522847,6 20,6.44771525 20,7 L20,17 L4,17 L4,7 C4,6.44771525 4.44771525,6 5,6 Z"
                                                fill="#000000" />
                                            <rect fill="#000000" opacity="0.3" x="1" y="18" width="22"
                                                height="1" rx="0.5" />
                                        </g>
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Salary Breakup</span>
                        </a>
                    </div>
                 <!--end::JD Salary Breakup-->

                <!--begin::Bench Candidates-->
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('super-admin/bench-candidates*') ? 'active' : '' }}"
                        href="{{ route('superadmin.company.new-bench-candidates', 'approve') }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Bench Candidates</span>
                    </a>
                </div>
                 <!--end::Bench Candidates-->

                <!--begin::Jobs Public Market-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('super-admin/job-public-market*') ? 'show' : '' }} ">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Jobs Public Market</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        <!--begin::Command Center-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.job-public-market', 'all') }}"
                                class="menu-link {{ (request()->is('super-admin/job-public-market/all') || request()->is('super-admin/job-public-market')) ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Command Center</span>
                            </a>
                        </div>
                        <!--end::Command Center-->
                        <!--begin::Pending-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.job-public-market', 'pending') }}"
                                class="menu-link {{ request()->is('super-admin/job-public-market/pending') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Pending Review</span>
                            </a>
                        </div>
                        <!--end::Pending-->
                        <!--begin::Approved-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.job-public-market', 'approve') }}"
                                class="menu-link {{ request()->is('super-admin/job-public-market/approve') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Live Approved Jobs</span>
                            </a>
                        </div>
                        <!--end::Approved-->
                        <!--begin::Bench Sharing-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.job-public-market', 'bench') }}"
                                class="menu-link {{ request()->is('super-admin/job-public-market/bench') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Bench Sharing Jobs</span>
                            </a>
                        </div>
                        <!--end::Bench Sharing-->
                        <!--begin::Vendor Limit-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.job-public-market', 'hold') }}"
                                class="menu-link {{ request()->is('super-admin/job-public-market/hold') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Vendor Limit Jobs</span>
                            </a>
                        </div>
                        <!--end::Vendor Limit-->
                        <!--begin::Rejected-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.job-public-market', 'reject') }}"
                                class="menu-link {{ request()->is('super-admin/job-public-market/reject') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Rejected Jobs</span>
                            </a>
                        </div>
                        <!--end::Rejected-->
                    </div>
                </div>
                 <!--end::Jobs Public Market-->



                <!--begin::Coins Settings-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('super-admin/coin-features') || request()->is('super-admin/coin-packages') || request()->is('super-admin/add-edit-coin-featutes') || request()->is('super-admin/add-edit-coin-packages') || request()->is('super-admin/coupons*') ? 'show' : '' }}  ">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Coins Settings</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        <!--begin::Coins Events-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.coinFeatures') }}"
                                class="menu-link {{ request()->is('super-admin/coin-features') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Coins Events</span>
                            </a>
                        </div>
                        <!--end::Coins Events-->
                        <!--begin::Coins Packages-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.coinPackages') }}"
                                class="menu-link {{ request()->is('super-admin/coin-packages') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Coins Packages</span>
                            </a>
                        </div>
                        <!--end::Coins Packages-->

                         <!--begin::Coins Packages-->
                         <div class="menu-item">
                            <a href="{{ route('superadmin.company.topUpCoinPackages') }}"
                                class="menu-link {{ request()->is('super-admin/topup-coin-packages*') || request()->is('super-admin/add-edit-top-up-coin-packages*') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Coin Top Up package</span>
                            </a>
                        </div>
                        <!--end::Coins Packages-->

                        <!--begin::Coupons-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.coupons.index') }}"
                                class="menu-link {{ request()->is('super-admin/coupons*') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Coupons</span>
                            </a>
                        </div>
                        <!--end::Coupons-->

                    </div>
                </div>
                <!--end::Coins Settings-->




                <!--begin::Invoices-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.invoices.index') }}"
                        class="menu-link {{ request()->is('super-admin/invoices*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7"
                                            height="2" rx="1" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">@lang('app.invoices')</span>
                    </a>
                </div>
                 <!--end::invoices-->

                <!--begin::Package Invoice-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.custompackages.index') }}"
                        class="menu-link {{ request()->is('super-admin/custompackages*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7"
                                            height="2" rx="1" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">Package Invoice</span>
                    </a>
                </div>
                 <!--end::Package Invoice-->

                <!--begin::Default Package-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.defaultpackages.index') }}"
                        class="menu-link {{ request()->is('super-admin/defaultpackages*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7"
                                            height="2" rx="1" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">Default Package</span>
                    </a>
                </div>
                <!--end::Default Package-->

                {{-- <a href="{{ route('superadmin.company.defaultTerms') }}"
                class="menu-link {{ request()->is('super-admin/default-terms') ? 'active' : '' }}"> --}}
                <!--begin::Default Terms-->
                <div class="menu-item">
                    <a href="{{ route('superadmin.company.defaultTermsList') }}"
                        class="menu-link {{ request()->is('super-admin/list-default-terms') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7"
                                            height="2" rx="1" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">Default Terms</span>
                    </a>
                </div>
                <!--end::Default Terms-->

                 <!--begin::Whatsapp Template-->
                <div class="menu-item">
                    <a href="{{ route('superadmin.company.whatsappthemes') }}"
                        class="menu-link {{ request()->is('super-admin/whatsapp-themes') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2"
                                            height="2" rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7"
                                            height="2" rx="1" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">Whatsapp Template</span>
                    </a>
                </div>
                <!--end::Whatsapp Template-->

               
                 <!--begin::All Transaction History-->
                 <div class="menu-item">
                    <a href="{{ url('super-admin/transaction-history') }}"
                        class="menu-link {{ request()->is('super-admin/transaction-history') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z"
                                            fill="#000000" />
                                        <path
                                            d="M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z"
                                            fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </span>
                        <span class="menu-title">Transaction History</span>
                    </a>
                </div>
                <!--end::All Transaction History-->

                 <!--begin:: Mail Settings-->
                 <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('super-admin/smtp-settings') || request()->is('super-admin/email-themes') || request()->is('super-admin/notification') ? 'show' : '' }} ">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">E-Mail Settings</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                         <!--begin::smtpSetting-->
                         <div class="menu-item">
                            <a href="{{ route('superadmin.smtp-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/smtp-settings') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.smtpSetting')</span>
                            </a>
                        </div>
                        <!--end::smtpSetting-->
                        
                        <!--begin::cmsGeneral-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.notification') }}"
                                class="menu-link {{ request()->is('super-admin/notification') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Email Notification</span>
                            </a>
                        </div>
                        <!--end::cmsGeneral-->
                        <!--begin::linkedInSettings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.emailthemes') }}"
                                class="menu-link {{ request()->is('super-admin/email-themes') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">E-mail Templates</span>
                            </a>
                        </div>
                        <!--end::linkedInSettings-->
                    </div>
                </div>
                 <!--end::Mail Settings-->







                <!--begin::superadmin-->
                <div class="menu-item d-none hide_unsue_menu">
                    <a href="{{ route('superadmin.superadmins.index') }}"
                        class="menu-link {{ request()->is('superadmin/superadmins*') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                        <path
                                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero" />
                                    </g>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">Superadmin</span>
                    </a>
                </div>
                <!--end::superadmin-->

                <!--begin::CMS Pages-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion d-none hide_unsue_menu">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">@lang('menu.frontCms')</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        <!--begin::frontCms-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.front-cms.index') }}"
                                class="menu-link {{ request()->is('super-admin/front-cms/index') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.frontCms')</span>
                            </a>
                        </div>
                        <!--end::frontCms-->

                        <!--begin::cmsGeneral-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.front-cms.index') }}"
                                class="menu-link {{ request()->is('super-admin/front-cms/index') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.cmsGeneral')</span>
                            </a>
                        </div>
                        <!--end::cmsGeneral-->

                        <!--begin::cms Features-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.front-cms.features') }}"
                                class="menu-link {{ request()->is('super-admin/front-cms/features') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.cmsFeatures')</span>
                            </a>
                        </div>
                        <!--end::cms Features-->

                        <!--begin::Icon Features-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.icon-features.index') }}"
                                class="menu-link {{ request()->is('super-admin/icon-features') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.iconFeatures')</span>
                            </a>
                        </div>
                        <!--end::Icon Features-->

                        <!--begin::Client Feedbacks-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.client-feedbacks.index') }}"
                                class="menu-link {{ request()->is('super-admin/client-feedbacks') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.clientFeedbacks')</span>
                            </a>
                        </div>
                        <!--end::Client Feedbacks-->
                    </div>
                </div>
                <!--end::CMS Pages-->

                <!--begin::settings-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is('super-admin/global-settings') ||  request()->is('super-admin/super-admin-access') || request()->is('super-admin/zoom-setting') || request()->is('super-admin/team-meeting-setting') || request()->is('super-admin/google-meeting-setting') || request()->is('super-admin/profile') || request()->is('super-admin/currency-settings') || request()->is('super-admin/language-settings') || request()->is('super-admin/theme-settings') || request()->is('super-admin/payment-settings') || request()->is('super-admin/skills') || request()->is('super-admin/sms-settings') || request()->is('super-admin/custom-modules') || request()->is('super-admin/features-list') || request()->is('super-admin/linkedin-settings') ? 'show' : '' }} ">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                    viewBox="0 0 24 24" version="1.1">
                                    <path
                                        d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">@lang('menu.settings')</span><span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        <!--begin::settings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.global-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/global-settings') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.settings')</span>
                            </a>
                        </div>
                        <!--end::settings-->
                         <!--begin::settings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.skillskElastic') }}"
                                class="menu-link {{ request()->is('super-admin/skills') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Skills</span>
                            </a>
                        </div>
                        <!--end::settings-->

                        <!--begin::recruiter-note-templates-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.recruiter-note.index') }}"
                                class="menu-link {{ request()->is('super-admin/recruiter-note*') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Recruiter Note Templates</span>
                            </a>
                        </div>
                        <!--end::recruiter-note-templates-->

                        <!--begin::rm-note-templates-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.rm-note.index') }}"
                                class="menu-link {{ request()->is('super-admin/rm-note*') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">RM Note Templates</span>
                            </a>
                        </div>
                        <!--end::rm-note-templates-->

                        <!--begin::settings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.zoom_setting') }}"
                                class="menu-link {{ request()->is('super-admin/zoom-setting') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Zoom @lang('menu.settings')</span>
                            </a>
                        </div>
                        <!--end::settings-->

                        <!--begin::google meeting link-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.google_meeting_setting') }}"
                                class="menu-link {{ request()->is('super-admin/google-meeting-setting') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Google Meeting @lang('menu.settings')</span>
                            </a>
                        </div>
                        <!--end::google meeting link-->

                        <!--begin::team meeting link-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.team_meeting_setting') }}"
                                class="menu-link {{ request()->is('super-admin/team-meeting-setting') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Team Meeting @lang('menu.settings')</span>
                            </a>
                        </div>
                        <!--end::team meeting link-->


                         <!--begin::settings-->
                         <div class="menu-item d-none">
                            <a href="{{ route('superadmin.company.superAdminAccess') }}"
                                class="menu-link {{ request()->is('super-admin/super-admin-access') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Access Code</span>
                            </a>
                        </div>
                        <!--end::settings-->



                        <!--begin::myProfile-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.profile.index') }}"
                                class="menu-link {{ request()->is('super-admin/profile') ? 'active' : '' }}"">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.myProfile')</span>
                            </a>
                        </div>
                        <!--end::myProfile-->

                        <!--begin::settings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.currency-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/currency-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('app.currency') . ' ' . __('menu.settings') }}</span>
                            </a>
                        </div>
                        <!--end::settings-->

                        <!--begin::Icon Features-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.language-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/language-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('app.language') @lang('menu.settings')</span>
                            </a>
                        </div>
                        <!--end::Icon Features-->

                        <!--begin::ThemeSettings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.theme-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/theme-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.themeSettings')</span>
                            </a>
                        </div>
                        <!--end::ThemeSettings-->

                        <!--begin::footerSettings-->
                        <div class="menu-item d-none">
                            <a href="{{ route('superadmin.footer-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/footer-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.footerSettings')</span>
                            </a>
                        </div>
                        <!--end::footerSettings-->

                        <!--begin::paymentSettings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.payment-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/payment-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.paymentSettings')</span>
                            </a>
                        </div>
                        <!--end::paymentSettings-->

                        <!--begin::smsSettings-->
                        {{-- <div class="menu-item">
                            <a href="{{ route('superadmin.sms-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/sms-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.smsSettings')</span>
                            </a>
                        </div> --}}
                        <!--end::smsSettings-->

                        <!--begin::customModules-->
                        {{-- <div class="menu-item">
                            <a href="{{ route('superadmin.custom-modules.index') }}"
                                class="menu-link {{ request()->is('super-admin/custom-modules') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.customModules')</span>
                            </a>
                        </div> --}}
                        <!--end::customModules-->

                        <!--begin::updateApplication-->
                        <div class="menu-item d-none hide_unsue_menu">
                            <a href="{{ route('superadmin.update-application.index') }}"
                                class="menu-link {{ request()->is('super-admin/update-application') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.updateApplication')</span>
                            </a>
                        </div>
                        <!--end::updateApplication-->

                        <!--begin::Features-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.company.featuresList') }}"
                                class="menu-link {{ request()->is('super-admin/features-list') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Features</span>
                            </a>
                        </div>
                        <!--end::Features-->



                        <!--begin::linkedInSettings-->
                        <div class="menu-item">
                            <a href="{{ route('superadmin.linkedin-settings.index') }}"
                                class="menu-link {{ request()->is('super-admin/linkedin-settings') ? 'active' : '' }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">@lang('menu.linkedInSettings')</span>
                            </a>
                        </div>
                        <!--end::linkedInSettings-->
                    </div>
                </div>
                <!--end::settings-->

                <!--begin::frontWebsite-->
                <div class="menu-item">
                    <a href="{{ url('/') }}" target="_blank"
                        class="menu-link {{ request()->is('super-admin/global-settings') ? 'active' : '' }}">
                        <span class="menu-icon">
                            <!--begin::Svg Icon-->
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-title">@lang('app.frontWebsite')</span>
                    </a>
                </div>
                <!--end::frontWebsite-->

            </div>
            <!--end::Menu-->
        </div>
        <!--end::Aside Menu-->
    </div>
    <!--end::Aside menu-->
    <!--begin::Support-->
    <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer"> 
        <a href="{{ route('superadmin.tickets.ticketList') }}" class="btn btn-custom btn-primary w-100"
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
      
    <!--begin::Footer Subscription-->
    <div class="aside-footer flex-column-auto pt-5 pb-7 px-5 d-none" id="kt_aside_footer">
        <a href="{{url('/super-admin/subscription/all') }}" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover"
            data-bs-dismiss-="click" title="Subscription">
            <span class="btn-label">Subscription</span>
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
    <!--end::Footer Subscription-->
</div>
