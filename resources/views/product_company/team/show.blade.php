<?php 

$userImage		=	'';
$userImage		= 	url('public/user-uploads/team_profile_img/'.@$users->image); 	

?>

<div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
  <!--begin::User Details-->
  <div class="card mb-5 mb-xl-5 mx-2">
    <!--begin::Card body-->
    <div class="card-body py-5 px-5">
      <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
        <!--begin:Profile Image-->
        <div class="me-7 mb-4">
          <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative"> <img src="<?php echo @$userImage; ?>" alt="image">
            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
          </div>
        </div>
        <!--end::Profile Image-->
        <!--begin::User Information-->
        <div class="flex-grow-1">
          <!--begin::Row-1-->
          <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
            <!--begin::User Details-->
            <div class="d-flex flex-column">
              <!--begin::User Name & ID-->
              <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"><?php echo ucwords(@$users->name); ?> </span> <span class="text-muted fw-bolder fs-7">C-<?php echo ucwords(@$users->id); ?></span> </div>
              <!--end::User Name & ID-->
              <!--begin::Designation | Location | Bagde-->
              <div class="d-flex flex-wrap fw-bold fs-6 pe-2">
                <!--begin::Designation-->
                <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                <!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
                <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M5.84026576,8 L18.1597342,8 C19.1999115,8 20.0664437,8.79732479 20.1528258,9.83390904 L20.8194924,17.833909 C20.9112219,18.9346631 20.0932459,19.901362 18.9924919,19.9930915 C18.9372479,19.9976952 18.8818364,20 18.8264009,20 L5.1735991,20 C4.0690296,20 3.1735991,19.1045695 3.1735991,18 C3.1735991,17.9445645 3.17590391,17.889153 3.18050758,17.833909 L3.84717425,9.83390904 C3.93355627,8.79732479 4.80008849,8 5.84026576,8 Z M10.5,10 C10.2238576,10 10,10.2238576 10,10.5 L10,11.5 C10,11.7761424 10.2238576,12 10.5,12 L13.5,12 C13.7761424,12 14,11.7761424 14,11.5 L14,10.5 C14,10.2238576 13.7761424,10 13.5,10 L10.5,10 Z" fill="#000000"/>
                    <path d="M10,8 L8,8 L8,7 C8,5.34314575 9.34314575,4 11,4 L13,4 C14.6568542,4 16,5.34314575 16,7 L16,8 L14,8 L14,7 C14,6.44771525 13.5522847,6 13,6 L11,6 C10.4477153,6 10,6.44771525 10,7 L10,8 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                <?php echo @$users->designation; ?> </a>
                <!--end::Designation-->
                <!--begin::Location-->
                <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2 me-5">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect x="0" y="0" width="24" height="24"/>
                    <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000"/>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                <?php echo @$users->city; ?>, <?php echo @$countryName; ?> </a>
                <!--end::Location-->
                <!--begin::Badge-->
                <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                <!--begin::Svg Icon-->
                <span class="svg-icon svg-icon-4 me-1">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                  </g>
                </svg>
                </span>
                <!--end::Svg Icon-->
                Badge </a>
                <!--end::Badge-->
              </div>
              <!--end::Designaiton | Location | Badge-->
            </div>
            <!--end::User Details-->
            <!--begin::Action-->
            <div class="d-flex mt-4">
              <?php 
		if($users->status == 'active')
		{
?>
              <a href="javascript:void(0);" class="btn btn-sm btn-flex btn-light-success fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
              </svg>
              </span>Active </a>
              <?php 
		}
		else
		{
?>
              <a href="javascript:void(0);" class="btn btn-sm btn-light-danger btn-active-danger fw-bolder me-3 fs-6"> <span class="svg-icon svg-icon-4 me-1">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <polygon points="0 0 24 0 24 24 0 24"></polygon>
                  <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                  <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                </g>
              </svg>
              </span>Inactive </a>
              <?php 
		} 
?>
              <!--end::Whatsapp-->
              <!--begin::Email-->
              <a href="javascript:void(0);" type="button" class="btn btn-sm btn-flex btn-primary fw-bolder me-3 fs-6">
              <!--begin::Svg Icon-->
              <span class="svg-icon svg-icon-primary svg-icon-2x">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24"></rect>
                  <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
                </g>
              </svg>
              <!--end::Svg Icon-->
              </span>Email </a>
              <!--end::Email-->
            </div>
            <!--end::Action-->
          </div>
          <!--end::Row-1-->
          <!--begin::Row-2-->
          <div class="d-flex flex-wrap flex-stack">
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-grow-1 pe-8">
              <div class="row w-75"> <span class=""> <b class="d-none">Skills/Tags :</b>
                <?php 
									
								if(!empty($users->skills)){
									$skillArr	= explode(',', $users->skills);
									foreach($skillArr as $val){
										echo '<span class="badge badge-light mb-2 me-2">'.$val.'</span>';
										}
									}	 
						?>
                </span> </div>
            </div>
            <!--end::Wrapper-->
          </div>
          <!--end::Row-2-->
        </div>
        <!--end::User Information-->
      </div>
    </div>
    <!--end::Card body-->
  </div>
  <!--end::User Details-->
  <!--begin::Tabs-->
  <div class="card card-custom mx-2">
    <!--Begin::Tabs Header-->
    <div class="card-header card-header-tabs-line">
      <div class="card-toolbar">
        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
          <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#user_overview"> <span class="nav-text font-weight-bold">Overview</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_requisitions"> <span class="nav-text font-weight-bold">Requisitions</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_candidate"> <span class="nav-text font-weight-bold">Candidate</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_interview_tracker"> <span class="nav-text font-weight-bold">Interview Tracker</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#user_joining"> <span class="nav-text font-weight-bold">Joinings</span> </a> </li>
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
        <!--begin::Overview header-->
        <div class="card-header cursor-pointer px-0">
          <!--begin::Card title-->
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Overview</h3>
          </div>
          <!--end::Card title-->
          <!--begin::Action-->
          <!-- <a href="{{route('product_company.profileedit')}}" class="btn btn-primary align-self-center">Edit Profile</a>-->
          <a href="{{route('product_company.team.edit', [$userId])}}" class="btn btn-primary align-self-center">Edit Profile</a>
          <!--end::Action-->
        </div>
        <!--end::Overview header-->
        <div class="card-body px-0 py-7">
          <!--begin::Full Name-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Full Name</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo ucwords($users->name); ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Full Name-->
          <!--begin::Designation-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Designation</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->designation; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Designation-->
          <!--begin::Email-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Email</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->email; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Email-->
          <!--begin::Phone-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Phone</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->mobile; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Phone-->
          <!--begin::Company Name-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Company Name</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->company_name; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Company Name-->
          <!--begin::Company Site-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Company Site</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->website; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Company Site-->
          <!--begin::Country-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">Country</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6 text-uppercase"><?php echo @$countryName; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::Country-->
          <!--begin::State-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">State</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$stateName; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::State-->
          <!--begin::City-->
          <div class="row mb-7">
            <!--begin::Label-->
            <label class="col-lg-3 fw-bold text-muted fs-6">City</label>
            <!--end::Label-->
            <!--begin::Col-->
            <div class="col-lg-9"> <span class="fw-bolder text-gray-800 fs-6"><?php echo @$users->city; ?></span> </div>
            <!--end::Col-->
          </div>
          <!--end::City-->
        </div>
      </div>
      <!--end::overview Content-->
      <!--begin::Requisitions Content-->
      <div class="tab-pane" id="user_requisitions" role="tabpanel">
        <!--begin::Requisitions header-->
        <div class="card-header cursor-pointer px-0">
          <!--begin::Card title-->
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Requisitions</h3>
          </div>
          <!--end::Card title-->
          <!--begin::Action-->
          <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Member</a> -->
          <!--end::Action-->
        </div>
        <!--end::Requisitions header-->
        <!--begin::Requisitions Content-->
        <div class="card-body px-0 py-7">
          <!--begin:Table-->
          <table class="table align-middle table-row-dashed fs-6 gy-5 d-none" id="kt_table_users">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-req" value="1" />
                  </div></th>
                <th class="">Requisition</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Date</th>
                <th class="">HM | CP</th>
                <th class="">S.Partners</th>
                <th class="">TOT</th>
                <th class="">SOU</th>
                <th class="">SCR</th>
                <th class="">EVA</th>
                <th class="">NEO</th>
                <th class="">OFF</th>
                <th class="">JOI</th>
                <th class="">REJ</th>
              </tr>
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-600 fw-bold">
              <!--begin::Table row-->
              <tr class="">
                <!--begin::Checkbox-->
                <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
                  </div></td>
                <!--end::Checkbox-->
                <!--begin::Requisitions=-->
                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label border-radius-5"> <img src="https://ayudawp.com/wp-content/uploads/2011/08/wordpress-icono.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::User details-->
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Wordpress Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Tech Mahindra</a> </div>
                  <!--begin::User details-->
                </td>
                <!--end::Requisitions-->
                <!--begin::Exp.-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">14+</span> <span class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div></td>
                <!--end::Exp.-->
                <!--begin::Salary-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">18 LPA</span> <span class="fw-bold d-block fs-7">12 LPA</span> </div></td>
                <!--end::Salary-->
                <!--begin::Location-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Greater Noida</span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span>
                      <div> </div>
                    </div>
                  </div></td>
                <!--end::Location-->
                <!--begin::Status | Requisition ID-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 active-bg text-hover-success">Active</a> <span class="fw-bold text-muted d-block fs-7">R1</span> </div></td>
                <!--end::Status | Requisition ID-->
                <!--begin::Date | Openings-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-Jul-2021</span> <span class="text-muted fw-bold d-block fs-7">3,974</span> </div></td>
                <!--end::Date | Openings-->
                <!--begin::Hiring Manager | Contact Person-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Promod"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
                <!--end::Hiring Manager | Contact Person-->
                <!--begin::Staffing Partners-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Amit Kumar"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_21.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                    </div>
                  </div></td>
                <!--end::Staffing Partners-->
                <!--begin::TOT-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">3,294</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">58</a> </div></td>
                <!--end::TOT-->
                <!--begin::SOU-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::SOU-->
                <!--begin::SCR-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">34</a> </div></td>
                <!--end::SCR-->
                <!--begin::EVA-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::EVA-->
                <!--begin::NEO-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">29</a> </div></td>
                <!--end::NEO-->
                <!--begin::OFF-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">32</a> </div></td>
                <!--end::OFF-->
                <!--begin::JOI-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::JOI-->
                <!--begin::REJ-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">384</a> </div></td>
                <!--end::REJ-->
              </tr>
              <!--end::Table row-->
              <!--begin::Table row-->
              <tr class="">
                <!--begin::Checkbox-->
                <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
                  </div></td>
                <!--end::Checkbox-->
                <!--begin::Requisitions=-->
                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">U</div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::User details-->
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">UI/UX Designer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Wipro</a> </div>
                  <!--begin::User details-->
                </td>
                <!--end::Requisitions-->
                <!--begin::Exp.-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">22+</span> <span class="text-muted fw-bold text-muted d-block fs-7">18+</span> </div></td>
                <!--end::Exp.-->
                <!--begin::Salary-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">50 LPA</span> <span class="fw-bold d-block fs-7">30 LPA</span> </div></td>
                <!--end::Salary-->
                <!--begin::Location-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Gurgaon</span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">Haryana</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span> </div>
                  </div></td>
                <!--end::Location-->
                <!--begin::Status | Requisition ID-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 hold-bg text-hover-warning">Hold</a> <span class="fw-bold text-muted d-block fs-7">R2-HCL</span> </div></td>
                <!--end::Status | Requisition ID-->
                <!--begin::Date | Openings-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">28-Apr-2021</span> <span class="text-muted fw-bold d-block fs-7">774</span> </div></td>
                <!--end::Date | Openings-->
                <!--begin::Hiring Manager | Contact Person-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Pawan"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_4.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
                <!--end::Hiring Manager | Contact Person-->
                <!--begin::Staffing Partners-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Pradeep"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Abinesh"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_24.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                    </div>
                  </div></td>
                <!--end::Staffing Partners-->
                <!--begin::TOT-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">65</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">10</a> </div></td>
                <!--end::TOT-->
                <!--begin::SOU-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::SOU-->
                <!--begin::SCR-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">23</a> </div></td>
                <!--end::SCR-->
                <!--begin::EVA-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::EVA-->
                <!--begin::NEO-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">24</a> </div></td>
                <!--end::NEO-->
                <!--begin::OFF-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">83</a> </div></td>
                <!--end::OFF-->
                <!--begin::JOI-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">49</a> </div></td>
                <!--end::JOI-->
                <!--begin::REJ-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::REJ-->
              </tr>
              <!--end::Table row-->
              <!--begin::Table row-->
              <tr class="">
                <!--begin::Checkbox-->
                <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input form-check-input-req" type="checkbox" value="1" />
                  </div></td>
                <!--end::Checkbox-->
                <!--begin::Requisitions=-->
                <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                  <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                    <div class="symbol-label border-radius-5"> <img src="https://www.hcltech.com/sites/default/files/styles/large/public/images/guideline_based_vectors-square.png?itok=KEUChzF3" alt="Company Logo" class="border-radius-5 w-100"> </div>
                    </a> </div>
                  <!--end::Avatar-->
                  <!--begin::User details-->
                  <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Frontend Developer</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">HCl Technologies</a> </div>
                  <!--begin::User details-->
                </td>
                <!--end::Requisitions-->
                <!--begin::Exp.-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30+</span> <span class="text-muted fw-bold text-muted d-block fs-7">27+</span> </div></td>
                <!--end::Exp.-->
                <!--begin::Salary-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">70 LPA</span> <span class="fw-bold d-block fs-7">67 LPA</span> </div></td>
                <!--end::Salary-->
                <!--begin::Location-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Noida</span>
                    <div class="d-flex align-items-center"> <span class="fw-bold text-muted d-block fs-7">U.P</span> &nbsp;|&nbsp; <span class="fw-bold text-muted d-block fs-7">INDIA</span> </div>
                  </div></td>
                <!--end::Location-->
                <!--begin::Status | Requisition ID-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="fs-6 closed-bg text-hover-danger">Closed</a> <span class="fw-bold text-muted d-block fs-7">R3-Custom</span> </div></td>
                <!--end::Status | Requisition ID-->
                <!--begin::Date | Openings-->
                <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">29-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">33</span> </div></td>
                <!--end::Date | Openings-->
                <!--begin::Hiring Manager | Contact Person-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Priyanka"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_20.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">1+</span> </div>
                    </div>
                  </div></td>
                <!--end::Hiring Manager | Contact Person-->
                <!--begin::Staffing Partners-->
                <td class=""><div class="d-flex flex-column">
                    <div class="symbol-group symbol-hover">
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                      <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="More users list.."> <span class="symbol-label font-weight-bold">2+</span> </div>
                    </div>
                  </div></td>
                <!--end::Staffing Partners-->
                <!--begin::TOT-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">425</a> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">64</a> </div></td>
                <!--end::TOT-->
                <!--begin::SOU-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">345</a> </div></td>
                <!--end::SOU-->
                <!--begin::SCR-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::SCR-->
                <!--begin::EVA-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">94</a> </div></td>
                <!--end::EVA-->
                <!--begin::NEO-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">03</a> </div></td>
                <!--end::NEO-->
                <!--begin::OFF-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">0</a> </div></td>
                <!--end::OFF-->
                <!--begin::JOI-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">45</a> </div></td>
                <!--end::JOI-->
                <!--begin::REJ-->
                <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6 text-hover-primary">540</a> </div></td>
                <!--end::REJ-->
              </tr>
              <!--end::Table row-->
            </tbody>
            <!--end::Table body-->
          </table>
          <!--end::Table-->
          <table id="load_jobs" class="table align-middle table-row-dashed fs-6 gy-5">
            <!--begin::Table head-->
            <thead>
              <!--begin::Table row-->
              <tr class="fw-bolder text-muted">
                <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                  </div></th>
                <th class="">Requisition</th>
                <th class="">Exp.</th>
                <th class="">Salary</th>
                <th class="">Location</th>
                <th class="">Status</th>
                <th class="">Date</th>
                <th class="">HM | CP</th>
                <th class="">S.Partners</th>
                <th class="">TOT</th>
                <th class="" >SOU</th>
                <th class="" >SCR</th>
                <th class="" >EVA</th>
                <th class="" >NEO</th>
                <th class="" >OFF</th>
                <th class="">JOI</th>
                <th class="" >REJ</th>
              </tr>
              <!--end::Table row-->
            </thead>
            <!--end::Table head-->
          </table>
        </div>
        <!--end::Requisitions Content-->
      </div>
      <!--end::Requisitions Content-->
      <!--begin::Candidate Content-->
      <div class="tab-pane" id="user_candidate" role="tabpanel">
        <!--begin::Candidate header-->
        <div class="card-header cursor-pointer px-0">
          <!--begin::Card title-->
          <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Candidate</h3>
          </div>
          <!--end::Card title-->
          <!--begin::Action-->
          <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Edit Candidate</a> -->
          <!--end::Action-->
        </div>
        <!--end::Candidate header-->
        <!--begin::Candidate Content-->
        <div class="card-body px-0 py-7">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5 d-none" id="kt_table_users">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-candidate" value="1" />
                </div></th>
              <th class="">Name</th>
              <th class="">Contacts</th>
              <th class="">Exp.</th>
              <th class="">Salary</th>
              <th class="">Notice Period</th>
              <th class="">Qualifaction</th>
              <th class="">Location</th>
              <th class="">Source</th>
              <th class="">Upload Date</th>
              <th class="">Requisitions</th>
              <th class="">Stage</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody class="text-gray-600 fw-bold">
            <!--begin::Table row-->
            <tr class="">
              <!--begin::Checkbox-->
              <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                  <input class="form-check-input form-check-input-candidate" type="checkbox" value="1" />
                </div></td>
              <!--end::Checkbox-->
              <!--begin::Name=-->
              <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
                  <div class="symbol-label fs-3 bg-light-success text-success border-radius-5">S</div>
                  </a> </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">Frontend Developer</a> </div>
                <!--begin::User details-->
              </td>
              <!--end::Name-->
              <!--begin::Contacts-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">+91 8989898998</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">sonika@test.com</a> </div></td>
              <!--end::Contacts-->
              <!--begin::Experience-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">8+</a> <span class="text-muted fw-bold text-muted d-block fs-7">5+</span> </div></td>
              <!--end::Experience-->
              <!--begin::Salary-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">18 LPA</a> <span class="text-muted fw-bold text-muted d-block fs-7">14 LPA</span> </div></td>
              <!--end::Salary-->
              <!--begin::Notice Period-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">15 Days</a> <span class="text-muted fw-bold text-muted d-block fs-7">Microsoft</span> </div></td>
              <!--end::Notice Period-->
              <!--begin::Qualifaction-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">M.Sc(IT)</a> <span class="text-muted fw-bold text-muted d-block fs-7">HTML,Css</span> </div></td>
              <!--end::Qualifaction-->
              <!--begin::Location-->
              <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Noida</a> <span class="text-muted fw-bold text-muted d-block fs-7">U.P | INDIA</span> </div></td>
              <!--end::Location-->
              <!--begin::Source-->
              <td><div class="d-flex flex-column">
                  <div class="symbol-group symbol-hover">
                    <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                  </div>
                </div>
                <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span>
          </div>
          
          </td>
          
          <!--end::Source-->
          <!--begin::Upload Date-->
          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">10-Nov-2021</a> </div></td>
            <!--end::Upload Date-->
            <!--begin::Requisitions-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Designer</a> <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span> </div></td>
            <!--end::Requisitions-->
            <!--begin::Stage-->
            <td><a href="javascript.void(0);" class="d-flex flex-column" data-bs-toggle="modal" data-bs-target="#stage_sub_stage"> <span class="badge fw-bolder fs-6 screening-bg px-2 py-1">Screening</span> <span class="text-muted fw-bold text-muted d-block fs-7">Screening - 1</span> </a> </td>
            <!--end::Stage-->
          </tr>
          <!--end::Table row-->
          <!--begin::Table row-->
          <tr class="">
            <!--begin::Checkbox-->
            <td><div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input form-check-input-candidate" type="checkbox" value="1" />
              </div></td>
            <!--end::Checkbox-->
            <!--begin::Name=-->
            <td class="d-flex align-items-center border-bottom-0"><!--begin:: Avatar -->
              <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                <div class="symbol-label border-radius-5"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="amit kumar image" class="w-100"> </div>
                </a> </div>
              <!--end::Avatar-->
              <!--begin::User details-->
              <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <span class="text-muted fw-bold text-muted d-block fs-7">Andorid Developer</span> </div>
              <!--begin::User details-->
            </td>
            <!--end::Name=-->
            <!--begin::Contacts-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">+91 9090909090</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">amit@test.com</a> </div></td>
            <!--end::Contacts-->
            <!--begin::Experience-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">15+</a> <span class="text-muted fw-bold text-muted d-block fs-7">8+</span> </div></td>
            <!--end::Experience-->
            <!--begin::Salary-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">26 LPA</a> <span class="text-muted fw-bold text-muted d-block fs-7">18 LPA</span> </div></td>
            <!--end::Salary-->
            <!--begin::Notice Period-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">1 Month</a> <span class="text-muted fw-bold text-muted d-block fs-7">HCL Technologies</span> </div></td>
            <!--end::Notice Period-->
            <!--begin::Qualifaction-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">B.Tech</a> <span class="text-muted fw-bold text-muted d-block fs-7">SQL Server, Java</span> </div></td>
            <!--end::Qualifaction-->
            <!--begin::Location-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Okhla</a> <span class="text-muted fw-bold text-muted d-block fs-7">ND | INDIA</span> </div></td>
            <!--end::Location-->
            <!--begin::Source-->
            <td><div class="d-flex flex-column">
                <div class="symbol-group symbol-hover">
                  <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Sonali kaushik"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                  <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                </div>
                <span class="text-muted fw-bold text-muted d-block fs-7">TCS</span> </div></td>
            <!--end::Source-->
            <!--begin::Upload Date-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">12-Oct-2021</a> </div></td>
            <!--end::Upload Date-->
            <!--begin::Requisitions-->
            <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Marketing</a> <span class="text-muted fw-bold text-muted d-block fs-7">Tech Mahindra</span> </div></td>
            <!--end::Requisitions-->
            <!--begin::Stage-->
            <td><a href="javascript:void(0);" class="d-flex flex-column" data-toggle="modal" data-target="#stage_sub_stage1"> <span class="badge fw-bolder fs-6 rejected-bg px-2 py-1">Rejected</span> <span class="text-muted fw-bold text-muted d-block fs-7">Rejected - 1</span> </a> </td>
            <!--end::Stage-->
          </tr>
          <!--end::Table row-->
          </tbody>
          
          <!--end::Table body-->
        </table>
        <!--end::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="load_application">
          <!--begin::Table head-->
          <thead>
            <!--begin::Table row-->
            <tr class="fw-bolder text-muted">
              <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                  <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                </div></th>
              <th class="">Name</th>
              <th class="">Contacts</th>
              <th class="">Exp.</th>
              <th class="">Salary</th>
              <th class="">Notice Period</th>
              <th class="">Qualifaction</th>
              <th class="">Location</th>
              <th class="">Source</th>
              <th class="">Upload Date</th>
              <th class="">Requisitions</th>
              <th class="">Stage</th>
            </tr>
            <!--end::Table row-->
          </thead>
          <!--end::Table head-->
        </table>
      </div>
      <!--end::Candidate Content-->
    </div>
    <!--end::Candidate Content-->
    <!--begin::Interview Tracker Content-->
    <div class="tab-pane" id="user_interview_tracker" role="tabpanel">
    <!--begin::Interview Tracker header-->
    <div class="card-header cursor-pointer px-0">
      <!--begin::Card title-->
      <div class="card-title m-0">
        <h3 class="fw-bolder m-0">Interview Tracker</h3>
      </div>
      <!--end::Card title-->
      <!--begin::Action-->
      <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
      <!--end::Action-->
    </div>
    <!--end::Interview Tracker header-->
    <!--begin::Interview Tracker Content-->
    <div class="card-body px-0 py-7">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
      <!--begin::Table head-->
      <thead>
        <!--begin::Table row-->
        <tr class="fw-bolder text-muted">
          <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
              <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-int-tra" value="1" />
            </div></th>
          <th class="">Name</th>
          <th class="">Requisitions</th>
          <th class="">Interview Mode</th>
          <th class="">Interviewers</th>
          <th class="">Source</th>
          <th class="">Interview Date</th>
          <th class="">Feedback</th>
          <th class="">Rating</th>
          <th class="">Stage</th>
        </tr>
        <!--end::Table row-->
      </thead>
      <!--end::Table head-->
      <!--begin::Table body-->
      <tbody class="text-gray-600 fw-bold">
        <!--begin::Table row-->
        <tr class="">
          <!--begin::Checkbox-->
          <td><div class="form-check form-check-sm form-check-custom form-check-solid">
              <input class="form-check-input form-check-input-int-tra" type="checkbox" value="1" />
            </div></td>
          <!--end::Checkbox-->
          <!--begin::Name=-->
          <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
              <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">S</div>
              </a> </div>
            <!--end::Avatar-->
            <!--begin::User details-->
            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <span class="text-muted fw-bold text-muted d-block fs-7">Frontend Developer</span> </div>
            <!--begin::User details-->
          </td>
          <!--end::Name=-->
          <!--begin::Requisitions=-->
          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Designer</a> <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span> </div></td>
          <!--end::Requisitions=-->
          <!--begin::Interview Mode=-->
          <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Telecom</a> <span class="text-muted fw-bold text-muted d-block fs-7">Skype/Phone</span> </div></td>
          <!--end::Interview Mode=-->
          <!--begin::Interviewers=-->
          <td><div class="d-flex flex-column">
              <div class="symbol-group symbol-hover">
                <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Amit"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Anjali"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Abu"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Rahul"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_23.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Soni"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_18.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle symbol-light" data-bs-toggle="tooltip" data-bs-placement="top" title="More users list.."> <span class="symbol-label font-weight-bold">5+</span> </div>
              </div>
            </div></td>
          </script>
          <!--end::Interviewers=-->
          <!--begin::Source=-->
          <td><div class="d-flex flex-column">
              <div class="symbol-group symbol-hover">
                <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
              </div>
            </div>
            <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span>
      </div>
      
      </td>
      
      <!--end::Source=-->
      <!--begin::Interview Date=-->
      <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">10-Nov-2021</a> <span class="text-muted fw-bold text-muted d-block fs-7">6:43 am</span> </div></td>
        <!--end::Interview Date=-->
        <!--begin::Feedback=-->
        <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6" data-bs-toggle="modal" data-bs-target="#interview_feedback">Shortlisted</a> <span class="text-muted fw-bold text-muted d-block fs-7">Available</span> </div></td>
        <!--end::Feedback=-->
        <!--begin::Rating=-->
        <td><img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem"> <span class="text-muted fw-bold text-muted d-block fs-7">Above Avarage</span> </td>
        <!--end::Rating=-->
        <!--begin::Stage-->
        <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 screening-bg px-2 py-1">Screening</span> <span class="text-muted fw-bold text-muted d-block fs-7">Screening - 1</span> </div></td>
        <!--end::Stage-->
      </tr>
      <!--end::Table row-->
      <!--begin::Table row-->
      <tr class="">
        <!--begin::Checkbox-->
        <td><div class="form-check form-check-sm form-check-custom form-check-solid">
            <input class="form-check-input form-check-input-int-tra" type="checkbox" value="1" />
          </div></td>
        <!--end::Checkbox-->
        <!--begin::Name=-->
        <td class="d-flex align-items-center"><!--begin:: Avatar -->
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
            <div class="symbol-label border-radius-5"> <img src="https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/150-26.jpg" alt="Max Smith" class="w-100"> </div>
            </a> </div>
          <!--end::Avatar-->
          <!--begin::User details-->
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Amit Kumar</a> <span class="text-muted fw-bold text-muted d-block fs-7">Andorid Developer</span> </div>
          <!--begin::User details-->
        </td>
        <!--end::Name=-->
        <!--begin::Requisitions=-->
        <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sales</a> <span class="text-muted fw-bold text-muted d-block fs-7">Tech Mahindra</span> </div></td>
        <!--end::Requisitions=-->
        <!--begin::Interview Mode=-->
        <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Video</span> <span class="text-muted fw-bold d-block fs-7">Zoom/Google Meet</span> </div></td>
        <!--end::Interview Mode=-->
        <!--begin::Interviewers=-->
        <td><div class="d-flex flex-column">
            <div class="symbol-group symbol-hover">
              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="One"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Two"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Three"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
            </div>
          </div></td>
        <!--end::Interviewers=-->
        <!--begin::Source=-->
        <td><div class="d-flex flex-column">
            <div class="symbol-group symbol-hover">
              <div class="symbol symbol-30 symbol-circle"  data-bs-toggle="tooltip" data-bs-placement="top" title="Anjali"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
            </div>
          </div>
          <span class="text-muted fw-bold d-block fs-7">HCl Technologies</span>
      </div>
      
      </td>
      
      <!--end::Source=-->
      <!--begin::Interview Date=-->
      <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">22-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">2:30 pm</span> </div></td>
        <!--end::Interview Date=-->
        <!--begin::Feedback=-->
        <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Pending</span> <span class="text-muted fw-bold d-block fs-7">No Response</span> </div></td>
        <!--end::Feedback=-->
        <!--begin::Rating=-->
        <td><img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem"> <span class="text-muted fw-bold d-block fs-7">Good</span> </td>
        <!--end::Rating=-->
        <!--begin::Stage-->
        <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 offer-bg px-2 py-1">Offer</span> <span class="text-muted fw-bold d-block fs-7">Offer - 1</span> </div></td>
        <!--end::Stage-->
      </tr>
      <!--end::Table row-->
      <!--begin::Table row-->
      <tr>
        <!--begin::Checkbox-->
        <td><div class="form-check form-check-sm form-check-custom form-check-solid">
            <input class="form-check-input form-check-input-int-tra" type="checkbox" value="1" />
          </div></td>
        <!--end::Checkbox-->
        <!--begin::Name=-->
        <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
          <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
            <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">A</div>
            </a> </div>
          <!--end::Avatar-->
          <!--begin::User details-->
          <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <span class="text-muted fw-bold text-muted d-block fs-7">PHP Developer</span> </div>
          <!--begin::User details-->
        </td>
        <!--end::Name=-->
        <!--begin::Requisitions=-->
        <td><div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">SEO</a> <span class="text-muted fw-bold text-muted d-block fs-7">4Bell Technology</span> </div></td>
        <!--end::Requisitions=-->
        <!--begin::Interview Mode=-->
        <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Face to Face</span> <span class="text-muted fw-bold d-block fs-7">Address</span> </div></td>
        <!--end::Interview Mode=-->
        <!--begin::Interviewers=-->
        <td><div class="d-flex flex-column">
            <div class="symbol-group symbol-hover">
              <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 1"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
              <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 2"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
              <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 3"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_25.jpg"> </div>
              <div class="symbol symbol-30 symbol-circle"data-bs-toggle="tooltip" data-bs-placement="top" title="Interviewer - 4"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
            </div>
          </div></td>
        <!--end::Interviewers=-->
        <!--begin::Source=-->
        <td><div class="d-flex flex-column">
            <div class="symbol-group symbol-hover">
              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Charlie Stone"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_19.jpg"> </div>
              <div class="symbol symbol-30 symbol-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Charlie Stone"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_22.jpg"> </div>
            </div>
          </div>
          <span class="text-muted fw-bold d-block fs-7">TCS</span>
      </div>
      
      </td>
      
      <!--end::Source=-->
      <!--begin::Interview Date=-->
      <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">21-Oct-2021</span> <span class="text-muted fw-bold d-block fs-7">11:35 am</span> </div></td>
        <!--end::Interview Date=-->
        <!--begin::Feedback=-->
        <td><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Rejected</span> <span class="text-muted fw-bold d-block fs-7">No Response</span> </div></td>
        <!--end::Feedback=-->
        <!--begin::Rating=-->
        <td><img src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/media/logos/stars.png" alt="image" style="width: 5.5rem"> <span class="text-muted fw-bold d-block fs-7">Best</span> </td>
        <!--end::Rating=-->
        <!--begin::Stage-->
        <td><div class="d-flex flex-column"> <span class="badge fw-bolder fs-6 joined-bg px-2 py-1">Joined</span> <span class="text-muted fw-bold text-muted d-block fs-7">Joined - 1</span> </div></td>
        <!--end::Stage-->
      </tr>
      <!--end::Table row-->
      </tbody>
      
      <!--end::Table body-->
    </table>
    <!--end::Table-->
    <table id="load_interviewSchedule" class="table align-middle table-row-dashed fs-6 gy-5">
      <thead>
        <tr class="fw-bolder text-muted">
          <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
              <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
            </div></th>
          <th class="">Name</th>
          <th class="">Requisitions</th>
          <th class="">Interview Mode</th>
          <th class="">Interviewers</th>
          <th class="">Source</th>
          <th class="">Interview Date</th>
          <th class="">Feedback</th>
          <th class="">Rating</th>
          <th class="">Stage</th>
        </tr>
      </thead>
    </table>
  </div>
  <!--end::Interview Tracker Content-->
</div>
<!--end::Interview Tracker  Content-->
<!--begin::Joinings Content-->
<div class="tab-pane" id="user_joining" role="tabpanel">
  <!--begin::Joinings header-->
  <div class="card-header cursor-pointer px-0">
    <!--begin::Card title-->
    <div class="card-title m-0">
      <h3 class="fw-bolder m-0">Joinings</h3>
    </div>
    <!--end::Card title-->
    <!--begin::Action-->
    <!-- <a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Stage</a> -->
    <!--end::Action-->
  </div>
  <!--end::Joinings header-->
  <!--begin::Joinings Content-->
  <div class="card-body px-0 py-7">
    <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="">
      <!--begin::Table head-->
      <thead>
        <!--begin::Table row-->
        <tr class="fw-bolder text-muted">
          <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
              <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-joining" value="1" />
            </div></th>
          <th class="">Name</th>
          <th class="">Contact</th>
          <th class="">Requisitions</th>
          <th class="">Source</th>
          <th class="">Offer Amount</th>
          <th class="">Joining Date</th>
          <th class="">Stage</th>
        </tr>
        <!--end::Table row-->
      </thead>
      <!--end::Table head-->
      <!--begin::Table body-->
      <tbody class="text-gray-600 fw-bold">
        <!--begin::Table row-->
        <tr class="">
          <!--begin::Checkbox-->
          <td><div class="form-check form-check-sm form-check-custom form-check-solid">
              <input class="form-check-input form-check-input-joining" type="checkbox" value="1">
            </div></td>
          <!--end::Checkbox-->
          <!--begin::Name=-->
          <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
              <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">A</div>
              </a> </div>
            <!--end::Avatar-->
            <!--begin::User details-->
            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Abu Baker</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">PHP Developer</a> </div>
            <!--begin::User details-->
          </td>
          <!--end::Name-->
          <!--begin::Contact-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">9090909090</span> <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary">abu@test.com</a> </div></td>
          <!--end::Contact-->
          <!--begin::Requisitions-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Wordpress Developer</span> <span class="text-muted fw-bold d-block fs-7">4Bell Technology</span> </div></td>
          <!--end::Requisitions-->
          <!--begin::Source-->
          <td class=""><div class="d-flex flex-column">
              <div class="symbol-group symbol-hover">
                <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Sonali kaushik"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
              </div>
              <span class="text-muted fw-bold d-block fs-7">Wipro</span> </div></td>
          <!--end::Source-->
          <!--begin::Offer Ammount-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">65 LPA</span> </div></td>
          <!--end::Offer Ammount-->
          <!--begin::Joining Date-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">05-July-2021</span> <span class="text-muted fw-bold d-block fs-7">2:05 pm</span> </div></td>
          <!--end::Joining Date-->
          <!--begin::Stage-->
          <td class=""><div class="d-flex flex-column"> <span class="fw-bolder joined-bg fs-6 px-2">Joined</span> </div></td>
          <!--end::Stage-->
        </tr>
        <!--end::Table row-->
        <!--begin::Table row-->
        <tr class="">
          <!--begin::Checkbox-->
          <td><div class="form-check form-check-sm form-check-custom form-check-solid">
              <input class="form-check-input form-check-input-joining" type="checkbox" value="1">
            </div></td>
          <!--end::Checkbox-->
          <!--begin::Name=-->
          <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
            <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5"> <a href="javascript:void(0);">
              <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">S</div>
              </a> </div>
            <!--end::Avatar-->
            <!--begin::User details-->
            <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Sonika Sharma</a> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">Frontend Developer</a> </div>
            <!--begin::User details-->
          </td>
          <!--end::Name-->
          <!--begin::Contact-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">8788987890</span> <a href="javascript:void(0);" class="text-muted fw-bold text-muted d-block fs-7 text-hover-primary">sonika@test.com</a> </div></td>
          <!--end::Contact-->
          <!--begin::Requisitions-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">UI/UX Developer</span> <span class="text-muted fw-bold d-block fs-7 text-hover-primary">Adobe</span> </div></td>
          <!--end::Requisitions-->
          <!--begin::Source-->
          <td class=""><div class="d-flex flex-column">
              <div class="symbol-group symbol-hover">
                <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" data-placement="top" title="Abu Baker"> <img alt="Pic" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_9.jpg"> </div>
              </div>
              <span class="text-muted fw-bold d-block fs-7">Tech Mahindra</span> </div></td>
          <!--end::Source-->
          <!--begin::Offer Ammount-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45 LPA</span> </div></td>
          <!--end::Offer Ammount-->
          <!--begin::Joining Date-->
          <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">27-Aug-2021</span> <span class="text-muted fw-bold d-block fs-7">5:05 pm</span> </div></td>
          <!--end::Joining Date-->
          <!--begin::Stage-->
          <td class=""><div class="d-flex flex-column"> <span class="fw-bolder joined-bg fs-6 px-2">Joined</span> </div></td>
          <!--end::Stage-->
        </tr>
        <!--end::Table row-->
      </tbody>
      <!--end::Table body-->
    </table>
    <!--end::Table-->
    <table id="myTable_joining" class="table align-middle table-row-dashed fs-6 gy-5">
      <thead>
        <tr class="fw-bolder text-muted">
          <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
              <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
            </div></th>
          <th class="">Name</th>
          <th class="">Contact</th>
          <th class="">Requisitions</th>
          <th class="">Source</th>
          <th class="">Offer Amount</th>
          <th class="">Joining Date</th>
          <th class="">Stage</th>
        </tr>
      </thead>
    </table>
  </div>
  <!--end::Joinings Content-->
</div>
<!--end::Joinings Content-->
<!--begin::Settings Content-->
<div class="tab-pane" id="user_settings" role="tabpanel">
  <form  id="setting_content1" class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
    <!--begin::Main body div-->
    <div class="card mb-3 bg-light mb-xl-3 mx-0 shadow-none rounded-0">
      <!--begin::Main body section-->
      <div class="card-body px-0 pt-0 pb-0">
        <!--begin::Sign in method-->
        <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
          <!--begin::Card header-->
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_signin_method">
            <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Sign-in Method</h3>
            </div>
          </div>
          <!--end::Card header-->
          <!--begin::Content-->
          <div id="kt_account_signin_method" class="collapse show">
            <!--begin::Card body-->
            <div class="card-body border-top py-8 px-2">
              <!--begin::Email Address-->
              <div class="d-flex flex-wrap align-items-center">
                <!--begin::Label-->
                <div id="kt_signin_email">
                  <div class="fs-6 fw-bolder mb-1">Email Address</div>
                  <div class="fw-bold text-gray-600">client2021@test.com</div>
                </div>
                <!--end::Label-->
                <!--begin::Edit-->
                <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                  <div class="row mb-6">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                      <div class="fv-row mb-0 fv-plugins-icon-container">
                        <label for="emailaddress" class="form-label fs-6 fw-bolder mb-3">Enter New Email Address</label>
                        <input type="email" class="form-control form-control-lg form-control-solid" id="emailaddress" placeholder="Email Address" name="emailaddress" value="client2021@test.com">
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
                <!--end::Edit-->
              </div>
              <!--end::Email Address-->
              <!--begin::Separator-->
              <div class="separator separator-dashed my-6"></div>
              <!--end::Separator-->
              <!--begin::Password-->
              <div class="d-flex flex-wrap align-items-center mb-10">
                <!--begin::Label-->
                <div id="kt_signin_password">
                  <div class="fs-6 fw-bolder mb-1">Password</div>
                  <div class="fw-bold text-gray-600">************</div>
                </div>
                <!--end::Label-->
                <!--begin::Action-->
                <div id="kt_signin_password_button" class="ms-auto"> <a href="javascript:void(0);" class="btn btn-light btn-active-light-primary reset_password" data-toggle="modal" data-target="#reset_password">Reset Password</a> </div>
                <!--end::Action-->
              </div>
              <!--end::Password-->
            </div>
            <!--end::Card body-->
          </div>
          <!--end::Content-->
        </div>
        <!--end::Sign in method-->
        <!--begin::Connected Accounts-->
        <div class="card mb-3 mb-xl-3 mx-0 shadow-none rounded-0">
          <!--begin::Card header-->
          <div class="card-header border-0 cursor-pointer px-0" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_connected_accounts" aria-expanded="true" aria-controls="kt_account_connected_accounts">
            <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Connected Accounts</h3>
            </div>
          </div>
          <!--end::Card header-->
          <!--begin::Content-->
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
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" <?php if($users->google_status == 1) { echo 'checked="checked"';} ?>  disabled="disabled">
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
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="githubswitch" <?php if($users->zoom_status == 1) { echo 'checked="checked"';} ?> disabled="disabled">
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
                      <input class="form-check-input w-45px h-30px" type="checkbox" id="slackswitch" <?php if($users->microsoft_status == 1) { echo 'checked="checked"';} ?> disabled="disabled" />
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
<input type="hidden" name="userId" id="userId" value="<?php echo @$userId; ?>" />
<script>
   var userId		=	$('#userId').val();  
   var load_jobs 	=	$('#load_jobs').dataTable({   
   			responsive  	: true,
   			processing  	: true,
   			serverSide  	: true,
   			destroy   		: true,
   			ordering		: false, //disable ordering   			
   			"bLengthChange" : false, //thought this line could hide the LengthMenu
   			sDom			: 'lrtip',
   			oLanguage 		: {sLengthMenu: "_MENU_",},   
      		ajax: {'url' : '{!! route('product_company.team.jobdata') !!}?userId='+userId,
   					  "data": function ( d )
   					  {
   						d.search = $('#inputSearch').val()
   					  }
   					},   
   			language:{"url": "<?php echo __("app.datatable") ?>"},	  
   			"drawCallback": function( settings ) {   				
   						$('[data-toggle="tooltip1"]').tooltip();
   						$('[data-toggle="tooltip2"]').tooltip();
   						$('[data-toggle="tooltip3"]').tooltip();
   						$('[data-toggle="tooltip4"]').tooltip();		
   				},
   			"columnDefs":
   						  [ 
   									{ "width": "50px", "targets": 0, "width": "4%", "class":""},
   									{  "width": "200px", "targets": 1, "class":"d-flex align-items-center" },																				
   									{  "width": "50px"  , "targets": 2},		
   									{  "width": "50px", "targets": 3 },		
   									{  "width": "200px", "targets": 4 }, 
   									{  "width": "50px" ,"targets": 5 },
   									{  "width": "200px" , "targets": 6 },              
   									{  "width": "150px" , "targets": 7 },	
   									{  "width": "150px", "targets": 8 },	
   										
   									{ "width": "20px", "targets": 9 },	
   									{ "width": "20px", "targets": 10 }, 
   									{ "width": "20px", "targets": 11 },    
   									
   									{ "width": "20px", "targets": 12 },             
   									{ "width": "20px", "targets": 13 },
   									{ "width": "20px", "targets": 14 },   
   									{ "width": "20px", "targets": 15 }, 
   									{ "width": "20px", "targets": 16 }
   							  ], 
   				columns:
   				[
   				  	{ data: 'chkbox', name: 'chkbox',  orderable: false, searchable: false},  
   					{ data: 'Requisition', name: 'Requisition' , orderable: false, searchable: false},
   					{ data: 'Exp', name: 'Exp' , orderable: false, searchable: false},
   					{ data: 'Salary', name: 'Salary' , orderable: false, searchable: false},
   					{ data: 'Location', name: 'Location' , orderable: false, searchable: false},
  	
   					{ data: 'Status', name: 'Status' , orderable: false, searchable: false},
   					{ data: 'Date', name: 'Date' , orderable: false, searchable: false},
   					{ data: 'HM', name: 'HM' , orderable: false, searchable: false},
   					
   					{ data: 'Partners', name: 'Partners' , orderable: false, searchable: false},
   					
   					{ data: 'TOT', name: 'TOT' , orderable: false, searchable: false},
   					{ data: 'SOU', name: 'SOU' , orderable: false, searchable: false},					
   					{ data: 'SCR', name: 'SCR' , orderable: false, searchable: false},
   					
   					{ data: 'EVA', name: 'EVA' , orderable: false, searchable: false},
   					{ data: 'NEO', name: 'NEO' , orderable: false, searchable: false},
   					{ data: 'OFF', name: 'OFF' , orderable: false, searchable: false},
   					{ data: 'JOI', name: 'JOI' , orderable: false, searchable: false},
   					{ data: 'REJ', name: 'REJ' , orderable: false, searchable: false}
   					        
   				  	   
   				]
   				
   				});    
   new $.fn.dataTable.FixedHeader( load_jobs );







	var application_table;
    load_application('load');   
	function load_application(type) 
	{
		var userId		=	$('#userId').val();
          
     	application_table = $('#load_application').dataTable({
      								
																	responsive     		: true, 
																	processing      	: true,
																	ordering			: false, //disable ordering
																	
																	destroy       		: true,   
																	stateSave     		: true,
																	
																	"bLengthChange"   	: false,   
																	sDom				: 'lrtip',       
				
          ajax: '{!! route('product_company.team.appdata') !!}?userId='+userId,             
                   language: {
                       "url": "<?php echo __("app.datatable") ?>"
                   },
           
                   "fnDrawCallback": function (oSettings) {
                       $("body").tooltip({
                           selector: '[data-toggle="tooltip"]'
                       });
                   },         
          
          "initComplete":function( settings, json){ 
          
            $('.clientStatusLoader').html('');
            $('.jobLoader').html('');
            $('.sourceLoader').html('');
            
            $('.search_np_idLoader').html('');
            $('.search_passportLoader').html('');
            $('.statusLoader').html('');            
            
          }, 				  
            columns: [
               
						{ data: 'chkbox', name: 'chkbox', orderable: false, searchable: false},            
						{data: 'Name', name: 'Name',  orderable: false, searchable: false, width: '15%'},						
						{data: 'Contacts', name: 'Contacts', searchable : false, orderable : false, width: '15%'},						
						{data: 'Exp', name: 'Exp', searchable : false, orderable : false,  width: '5%'},
						{data: 'Salary', name: 'Salary',  searchable : false, orderable : false,  width: '5%'},   

						{data: 'NoticePeriod', name: 'NoticePeriod', searchable : false, orderable : false},
						{data: 'Qualifaction', name: 'Qualifaction',  searchable : false, orderable : false},
									
						{data: 'Location', name: 'Location', orderable : false},  												 
						{data: 'Source', name: 'Source', orderable: false},
						{data: 'UploadDate', name: 'UploadDate', orderable: false},
						
						
						{data: 'Requisitions', name: 'Requisitions', orderable: false},
						{data: 'Stage', name: 'Stage', orderable: false}
					]         
      
             }); 
               new $.fn.dataTable.FixedHeader(application_table);
           
	}		   
		   		   
   
        load_interviewSchedule();
        var table;
        function load_interviewSchedule(){
          
		  	var userId		=	$('#userId').val();

            table   = $('#load_interviewSchedule').dataTable({
            
						responsive  : true,
						processing  : true,
						serverSide  : true,
						destroy   : true,
						ordering: false, //disable ordering
						"bLengthChange" : false, //thought this line could hide the LengthMenu
						sDom: 'lrtip',
						oLanguage : {sLengthMenu: "_MENU_",},
                		ajax    : '{!! route('product_company.team.intdata') !!}?userId=' + userId, 
               
                		"fnDrawCallback": function( settings, start, end, max, total, pre ) {
					var total_record	=	this.fnSettings().fnRecordsTotal();
					$('#total_record').html(total_record);					
					$("body").tooltip({
                        selector: '[data-toggle="tooltip"]'
                    });
                },
        
      
            "columnDefs": [
							{"targets": 0 },
							{"targets": 1, "class":"align-items-center" }, 
							{"targets": 2 },
							{ "targets": 3 },
							{ "targets": 4 },
							{ "targets": 5 },            
							{ "targets": 6 },
							{ "targets": 7 },
							{ "targets": 8 },
							{ "targets": 9 }					
						  ],     
        
      columns: [
				  { data: 'chkbox', name:'chkbox',width: '1%', searchable : false, orderable: false},  
				  { data: 'Name', name: 'Name',width: '15%', searchable : false, orderable: false},
				  { data: 'Requisitions', name: 'Requisitions',width: '10%', searchable : false, orderable: false},
				  { data: 'InterviewMode', name: 'InterviewMode',width: '10%', searchable : false, orderable: false},
				  { data: 'Interviewers', name: 'Interviewers',width: '10%', searchable : false, orderable: false},
				  { data: 'Source', name: 'Source',width: '10%', searchable : false, orderable: false},
				  { data: 'InterviewDate', name: 'InterviewDate',width: '10%', searchable : false, orderable: false},
				  { data: 'Feedback', name: 'Feedback',width: '10%', searchable : false, orderable: false},
				  { data: 'Rating', name: 'Rating',width: '10%', searchable : false, orderable: false},		  
				  { data: 'Stage', name: 'Stage',width: '10%', searchable : false, orderable: false}
			   ],
               
            });
            new $.fn.dataTable.FixedHeader( table );

        }


   var jointable;
   jointableLoad('load');
   function jointableLoad()
   {
   	   var userId		=	$('#userId').val();
       table = $('#myTable_joining').dataTable(
       {
			responsive  	: true,
			processing  	: true,
			serverSide  	: true,
			destroy   		: true,
			ordering		: false, //disable ordering
   
   			"bLengthChange" : false, //thought this line could hide the LengthMenu
		   sDom			: 'lrtip',
		   oLanguage 		: {
				sLengthMenu: "_MENU_",
			},
     						
           ajax: '{!! route('product_company.team.joiningdata') !!}?userId='+userId,
           language:
           {
               "url": "<?php echo __("app.datatable") ?>"
           },
           "fnDrawCallback": function (oSettings)
           {
               $("body").tooltip(
               {
                   selector: '[data-toggle="tooltip"]'
               });
           },        
           columns:
           [
				{data: 'chkbox', name:'chkbox', width: '5%', orderable: false, searchable: false},
				{data: 'Name', name:'Name', width: '20%', orderable: false, searchable: false},
				{data: 'Contact', name:'Contact', width: '15%', orderable: false, searchable: false},
				{data: 'Requisitions', name:'Requisitions', width: '15%', orderable: false, searchable: false},
				{data: 'Source', name:'Source', width: '10%', orderable: false, searchable: false},
				{data: 'Offer', name:'Offer', width: '10%', orderable: false, searchable: false},
				{data: 'Joining', name:'Joining', width: '10%', orderable: false, searchable: false},
				{data: 'Stage', name:'Stage', width: '10%', orderable: false, searchable: false} 
   
   		]
       });
           new $.fn.dataTable.FixedHeader(jointable);
   }




</script>
