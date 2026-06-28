@php 
	$userImage		=	'';
	$userImage		= 	url('public/user-uploads/team_profile_img/'.@$users->image); 	
@endphp
    <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
    <!--begin::User Details-->
    <div class="card mb-5 mb-xl-5 mx-2">
  <div class="card-body py-5 px-5">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
      <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="{{ @$userImage }}" alt="image">
          <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
        </div>
          </div>
      <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
          <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"> {{ ucwords(@$users->name) }} </span> <span class="text-muted fw-bolder fs-7">C-{{ ucwords(@$users->id) }}</span> </div>
                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                    <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                  </g>
                </svg>
                </span> {{ @$users->designation }} </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5"> <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000"/>
                  </g>
                </svg>
                </span> {{ @$users->city }}, {{ @$countryName }} </a> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2"> <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                  </g>
                </svg>
                </span> -------- </a> </div>
              </div>
          <div class="d-flex mt-4"> @if($users->status == 'active') <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light-success fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
              </g>
            </svg>
            </span>Active </a> @else <a href="javascript:void(0);" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
              </g>
            </svg>
            </span>Inactive </a> @endif <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-primary fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-primary svg-icon-2x">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"></rect>
                <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
              </g>
            </svg>
            </span>Email </a> </div>
        </div>
            <div class="d-flex flex-wrap flex-stack">
          <div class="d-flex flex-column flex-grow-1 pe-8">
                <div class="row w-75"> <span class=""> <b class="d-none">Skills/Tags :</b> @if(!empty($users->skills))
                  @php $skillArr	= explode(',', $users->skills); @endphp 
                  @foreach($skillArr as $val)
                  {{ '<span class="badge badge-light mb-2 me-2">'.$val.'</span>' }}
                  @endforeach	 
                  @endif </span> </div>
              </div>
        </div>
          </div>
    </div>
      </div>
</div>
    <!--end::User Details-->
    <!--begin::Tabs-->
    <div class="card card-custom mx-2">
    <!--Begin::Tabs Header-->
    <div class="card-header card-header-tabs-line">
  <div class="card-toolbar">
        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
      <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#user_overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
      {{-- <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_requisitions"> <span class="nav-text font-weight-bold">Requisitions</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_candidate"> <span class="nav-text font-weight-bold">Candidate</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_interview_tracker"> <span class="nav-text font-weight-bold">Interview Tracker</span> </a> </li>
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_joining"> <span class="nav-text font-weight-bold">Joinings</span> </a> </li>--}}
      <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_settings"> <span class="nav-text font-weight-bold">Settings</span> </a> </li>
    </ul>
      </div>
</div>
    <!--end::Tabs Header-->
    <!--Begin::Tabs Body-->
    <div class="card-body py-0">
    <div class="tab-content pt-5">
	
  <!--begin::Overview Content-->
  <div class="tab-pane active show" id="user_overview" role="tabpanel">
        <div class="card-header cursor-pointer px-0">
      <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Overview</h3>
          </div>
      <a href="{{route('superadmin.team.edit', [$userId])}}" class="btn btn-primary align-self-center">Edit Profile</a> </div>
        <div class="card-body px-0 py-7">
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Full Name</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ ucwords($users->name) }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Designation</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$users->designation }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Email</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$users->email }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Phone</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$users->mobile }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Company Name</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$users->company_name }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Company Site</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$users->website }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">Country</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6 text-uppercase">{{ @$countryName }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">State</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$stateName }}</span> </div>
          </div>
      <div class="row mb-7">
            <label class="col-lg-3 fw-bold text-muted fs-6">City</label>
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6">{{ @$users->city }}</span> </div>
          </div>
    </div>
      </div>
  <!--end::overview Content-->
  
 
  
	
	
	
	



    <!--begin::Settings Content-->
    <div class="tab-pane" id="user_settings" role="tabpanel">
  <form  id="setting_content1" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
        
        <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
     
      <div class="card-body px-0 pt-0 pb-0">
            
            <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
          
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
                <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Sign-in Method</h3>
            </div>
              </div>
          
          <div id="kt_account_signin_method" class="collapse show">
                
                <div class="card-body border-top py-8 px-2">
              
              <div class="d-flex flex-wrap align-items-center">
                   
                    <div id="kt_signin_email">
                  <div class="fs-6 fw-bolder mb-1">Email Address</div>
                  <div class="fw-bold text-gray-600"><?php echo @$users->email; ?></div>
                </div>
                    
                    <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                  <div class="row mb-6">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                      <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="emailaddress" class="form-label fs-6 fw-bolder mb-3">Enter New Email Address</label>
                            <input type="email" class="form-control form-control-lg form-control-solid" id="emailaddress" placeholder="Email Address" name="emailaddress" value="<?php echo @$users->email; ?>">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                    </div>
                        <div class="col-lg-6">
                      <div class="fv-row mb-0 fv-plugins-icon-container">
                            <label for="confirmemailpassword" class="form-label fs-6 fw-bolder mb-3">Confirm Password</label>
                            <input type="password" class="form-control form-control-lg form-control-solid" name="confirmemailpassword" id="confirmemailpassword">
                            <div class="fv-plugins-message-container invalid-feedback"></div>
                          </div>
                    </div>
                      </div>
                  <div class="d-flex">
                        <button id="kt_signin_submit" type="button" class="btn btn-primary me-2 px-6">Update Email</button>
                        <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                      </div>
                </div>
                    
                  </div>
             
              <div class="separator separator-dashed my-6"></div>
              
              <div class="d-flex flex-wrap align-items-center mb-10">
                    
                    <div id="kt_signin_password">
                  <div class="fs-6 fw-bolder mb-1">Password</div>
                  <div class="fw-bold text-gray-600">************</div>
                </div>
                    
                    <div id="kt_signin_password_button" class="ms-auto"> <a href="javascript:void(0);" class="btn btn-light btn-active-light-primary reset_password" data-toggle="modal" data-target="#reset_password">Reset Password</a> </div>
                    
                  </div>
             
            </div>
               
              </div>
          
        </div>
            
            <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
          
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
                <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Connected Accounts</h3>
            </div>
              </div>
         
          <div id="kt_account_connected_accounts" class="collapse show">
                <!--begin::Card body-->
                <div class="card-body border-top py-8 px-2">
              <!--begin::Items-->
              <div class="py-2">
                    <!--begin::Item-->
                    <div class="d-flex flex-stack">
                  <div class="d-flex"> <img src="{{ asset('img/images/google-icon.svg') }}" class="w-30px me-6" alt="" />
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Google</a>
                      <div class="fs-6 fw-bold text-gray-400">Plan properly your workflow</div>
                    </div>
                      </div>
                  <div class="d-flex justify-content-end">
                        <div class="form-check form-check-solid form-switch">
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" @if($users->google_status == 1) {{ 'checked="checked"' }} @endif  disabled="disabled">
                      <label class="form-check-label" for="googleswitch"></label>
                    </div>
                      </div>
                </div>
                    <!--end::Item-->
                    <div class="separator separator-dashed my-5"></div>
                    <!--begin::Item-->
                    <div class="d-flex flex-stack">
                  <div class="d-flex"> <img src="{{asset('img/images/github.svg')}}" class="w-30px me-6" alt="" />
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Zoom</a>
                      <div class="fs-6 fw-bold text-gray-400">Keep eye on on your Repositories</div>
                    </div>
                      </div>
                  <div class="d-flex justify-content-end">
                        <div class="form-check form-check-solid form-switch">
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="githubswitch" @if($users->zoom_status == 1) {{ 'checked="checked"' }} @endif disabled="disabled">
                      <label class="form-check-label" for="githubswitch"></label>
                    </div>
                      </div>
                </div>
                    <!--end::Item-->
                    <div class="separator separator-dashed my-5"></div>
                    <!--begin::Item-->
                    <div class="d-flex flex-stack">
                  <div class="d-flex"> <img src="{{asset('img/images/slack-icon.svg')}}" class="w-30px me-6" alt="" />
                        <div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-5 text-dark text-hover-primary fw-bolder">Microsoft</a>
                      <div class="fs-6 fw-bold text-gray-400">Integrate Projects Discussions</div>
                    </div>
                      </div>
                  <div class="d-flex justify-content-end">
                        <div class="form-check form-check-solid form-switch">
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="slackswitch" @if($users->microsoft_status == 1)  {{ 'checked="checked"' }} @endif  disabled="disabled" />
                      <label class="form-check-label" for="slackswitch"></label>
                    </div>
                      </div>
                </div>
                    <!--end::Item-->
                  </div>
              <!--end::Items-->
            </div>
                <!--end::Card body-->
              </div>
          <!--end::Content-->
        </div>
            <!--end::Connected Accounts-->
          </div>
      <!--end::Main body content-->
    </div>
        <!--end::Main body div-->
      </form>
  <!--end::Form-->
</div>
    <!--end::Settings Content-->
    <input type="hidden" name="userId" id="userId" value="{{ @$userId }}" />
@push('footer-script')
    <s<script src="{{ asset('js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/dataTables.colReorder.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets/node_modules/multiselect/js/jquery.multi-select.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>"type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/jquery.fullcalendar.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('js/schedule-calendar.js') }}<?php echo TM_VERSION; ?>"></script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>

@endpush
    
