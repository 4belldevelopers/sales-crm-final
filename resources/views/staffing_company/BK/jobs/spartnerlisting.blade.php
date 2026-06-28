<?php 
		use App\Helper\GeneralMethod;
?>

<?php 
		//echo '<pre>'; print_r($spartnerArr); exit; 
?>

<div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px" style="">
  <!--begin::Tabs-->
  <div class="card card-custom mx-2">
    <!--Begin::Tabs Header-->
    <div class="card-header card-header-tabs-line">
      <div class="card-toolbar">
        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
          <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#assigned"> <span class="nav-text font-weight-bold">Assigned</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#recommended"> <span class="nav-text font-weight-bold">Recommended</span> </a> </li>
          <li class="nav-item me-3"> <a class="nav-link" data-toggle="tab" href="#registered"> <span class="nav-text font-weight-bold">Registered</span> </a> </li>
        </ul>
      </div>
    </div>
    <!--end::Tabs Header-->
    <!--Begin::Tabs Body-->
    <div class="card-body py-0">
      <div class="tab-content pt-5">
        <!--begin::Assigned Content-->
        <div class="tab-pane active show" id="assigned" role="tabpanel">
          <!--begin::Working header-->
          <div class="card-header cursor-pointer px-0">
            <!--begin::Card title-->
            <div class="card-title m-0">
              <h3 class="fw-bolder m-0">Assigned</h3>
            </div>
            <!--end::Card title-->
          </div>
          <!--end::Working header-->
          <div class="card-body px-0 py-7">
            <!--begin:Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
              <!--begin::Table head-->
              <thead>
                <!--begin::Table row-->
                <tr class="fw-bolder text-muted">
                  <th class="w-10px pe-0"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-staff" value="1" />
                    </div></th>
                  <th class="">Name</th>
                  <th class="">Contact Person</th>
                  <th class="">Upload Profile</th>
                  <th class="">Interview</th>
                  <th class="">Joinings</th>
                  <th class="">Action</th>
                </tr>
                <!--end::Table row-->
              </thead>
              <!--end::Table head-->
              <!--begin::Table body-->
              <tbody class="text-gray-600 fw-bold">
                <!--begin::Table row-1-->
                <tr class="">
                  <!--begin::Checkbox-->
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name-->
                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="146" class="show-detail" id="kt_activities3_toggle">
                        <div class="symbol-label fs-3 fs-3 bg-light-danger text-danger border-radius-5">T</div>
                        </a> </div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">TCS</span> <span class="fw-bold text-muted d-block fs-7">IT Services</span> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name-->
                  <!--begin::Contact Person-->
                  <td class=""><div class="d-flex flex-column">
                      <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="One"> <img alt="One" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="Two"> <img alt="Two" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_2.jpg"> </div>
                        <div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="Three"> <img alt="Three" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_3.jpg"> </div>
                      </div>
                    </div></td>
                  <!--end::Contact Person-->
                  <!--begin::Upload Profile-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">187</span> </div></td>
                  <!--end::Upload Profile-->
                  <!--begin::Interview-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">12</span> </div></td>
                  <!--end::Interview-->
                  <!--begin::Joinings-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td>
                  <!--end::Joinings-->
                  <!--begin::Action-->
                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Remove</a> </div></td>
                  <!--end::Action-->
                </tr>
                <!--end::Table row-1-->
                <!--begin::Table row-2-->
                <tr class="">
                  <!--begin::Checkbox-->
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-staff" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name-->
                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5"> <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="logo-first-letter" class="border-radius-5 w-100"> </div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">4Bell Technology</span> <span class="fw-bold text-muted d-block fs-7">IT Consultancy</span> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name-->
                  <!--begin::Contact Person-->
                  <td class=""><div class="d-flex flex-column">
                      <div class="symbol-group symbol-hover">
                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="One"> <img alt="One" src="https://preview.keenthemes.com//metronic/theme/html/demo1/dist/assets/media/users/300_1.jpg"> </div>
                        <div class="symbol symbol-30 symbol-circle" data-toggle="tooltip" title="Amita"> <span class="symbol-label bg-warning text-inverse-warning fw-bolder">A</span> </div>
                        <div class="symbol symbol-30 symbol-circle"data-toggle="tooltip" title="Priyanka"> <span class="symbol-label bg-primary text-inverse-primary fw-bolder">P</span> </div>
                      </div>
                    </div></td>
                  <!--end::Contact Person-->
                  <!--begin::Upload Profile-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">45</span> </div></td>
                  <!--end::Upload Profile-->
                  <!--begin::Interview-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">03</span> </div></td>
                  <!--end::Interview-->
                  <!--begin::Joinings-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">6</span> </div></td>
                  <!--end::Joinings-->
                  <!--begin::Action-->
                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Remove</a> </div></td>
                  <!--end::Action-->
                </tr>
                <!--end::Table row-2-->
              </tbody>
              <!--end::Table body-->
            </table>
            <!--end::Table-->
          </div>
        </div>
        <!--end::Assigned Content-->
        <!--begin::Assigned Content-->
        <div class="tab-pane" id="recommended" role="tabpanel">
          <!--begin::Recommended header-->
          <div class="card-header cursor-pointer px-0 d-flex align-items-center justify-content-between">
            <!--begin::Card title-->
            <div class="card-title m-0 d-block">
              <h3 class="fw-bolder m-0">Recommended</h3>
              <!-- <p class="text-muted fs-6 fw-bold pt-2">Top 10</p> -->
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <!-- <a href="{{route('admin.profileedit')}}" class="btn btn-primary align-self-center">Edit Profile</a> -->
            <!--end::Action-->
          </div>
          <!--end::Recommended header-->
          <div class="card-body px-0 py-7">
            <!--begin:Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="">
              <!--begin::Table head-->
              <thead>
                <!--begin::Table row-->
                <tr class="fw-bolder text-muted">
                  <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-reco" value="1" />
                    </div></th>
                  <th class="min-w-150px">Name</th>
                  <th class="min-w-80px">Match</th>
                  <th class="min-w-50px">Similar Req.</th>
                  <th class="min-w-50px">Profiles</th>
                  <th class="min-w-100px">Hiring Type</th>
                  <th class="min-w-100px">Status</th>
                  <th class="min-w-100px">Action</th>
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
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name=-->
                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5"> <img src="https://cpb-us-w2.wpmucdn.com/blog.smu.edu/dist/0/141/files/2017/12/94e9035a11b4869bec9266f730084bac.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">Adobe</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Staffing Partners</a> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name-->
                  <!--begin::Progressbar-->
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">78%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 78%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">Wordpress</span> </div></td>
                  <!--end::Progressbar-->
                  <!--begin::Similar Req.-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">567</span> </div></td>
                  <!--end::Similar Req.-->
                  <!--begin::Profiles-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">7,651</span> </div></td>
                  <!--end::Profiles-->
                  <!--begin::Hiring Type-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Full Time</span> </div></td>
                  <!--end::Hiring Type-->
                  <!--begin::Status-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                  <!--end::Status-->
                  <!--begin::Action-->
                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Cancel Request</a> </div></td>
                  <!--end::Action-->
                </tr>
                <!--end::Table row-->
                <!--begin::Table row-->
                <tr class="">
                  <!--begin::Checkbox-->
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name=-->
                  <td class=" d-flex align-items-center"><!--begin::Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="47" class="show-detail">
                      <div class="symbol-label fs-3 bg-light-danger text-danger border-radius-5">H</div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="47" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="HCL Technologies">HCL Technologies</a> <a href="javascript:void(0);" data-row-id="47" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Recruiter</a> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name-->
                  <!--begin::Progressbar-->
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">20%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 20%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">React JS</span> </div></td>
                  <!--end::Progressbar-->
                  <!--begin::Similar Req.-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30</span> </div></td>
                  <!--end::Similar Req.-->
                  <!--begin::Profiles-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">456</span> </div></td>
                  <!--end::Profiles-->
                  <!--begin::Hiring Type-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Part Time</span> </div></td>
                  <!--end::Hiring Type-->
                  <!--begin::Status-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-muted fw-bolder fs-6">Unregistered</span> </div></td>
                  <!--end::Status-->
                  <!--begin::Action-->
                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Hire</a> </div></td>
                  <!--end::Action-->
                </tr>
                <!--end::Table row-->
                <!--begin::Table row-->
                <tr class="">
                  <!--begin::Checkbox-->
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name=-->
                  <td class="d-flex align-items-center border-0"><!--begin:: Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5"> <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Tehcnology</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Hiring Manager</a> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name-->
                  <!--begin::Progressbar-->
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">100%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">Core Java</span> </div></td>
                  <!--end::Progressbar-->
                  <!--begin::Similar Req.-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">57</span> </div></td>
                  <!--end::Similar Req.-->
                  <!--begin::Profiles-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">651</span> </div></td>
                  <!--end::Profiles-->
                  <!--begin::Hiring Type-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Part Time</span> </div></td>
                  <!--end::Hiring Type-->
                  <!--begin::Status-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-muted fw-bolder fs-6">Unregistered</span> </div></td>
                  <!--end::Status-->
                  <!--begin::Action-->
                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Cancel Request</a> </div></td>
                  <!--end::Action-->
                </tr>
                <!--end::Table row-->
                <!--begin::Table row-->
                <tr class="">
                  <!--begin::Checkbox-->
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-reco" type="checkbox" value="1" />
                    </div></td>
                  <!--end::Checkbox-->
                  <!--begin::Name=-->
                  <td class=" d-flex align-items-center"><!--begin::Avatar -->
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="47" class="show-detail">
                      <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">T</div>
                      </a> </div>
                    <!--end::Avatar-->
                    <!--begin::User details-->
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="47" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="Ibm-test2">Tech Mahindra</a> <a href="javascript:void(0);" data-row-id="47" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Team Leader</a> </div>
                    <!--begin::User details-->
                  </td>
                  <!--end::Name-->
                  <!--begin::Progressbar-->
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">70%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">Node JS</span> </div></td>
                  <!--end::Progressbar-->
                  <!--begin::Similar Req.-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">80</span> </div></td>
                  <!--end::Similar Req.-->
                  <!--begin::Profiles-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">56</span> </div></td>
                  <!--end::Profiles-->
                  <!--begin::Hiring Type-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Full Time</span> </div></td>
                  <!--end::Hiring Type-->
                  <!--begin::Status-->
                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                  <!--end::Status-->
                  <!--begin::Action-->
                  <td class=""><div class="flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Hire</a> </div></td>
                  <!--end::Action-->
                </tr>
                <!--end::Table row-->
              </tbody>
              <!--end::Table body-->
            </table>
            <!--end::Table-->
          </div>
        </div>
        <!--end::Assigned Content-->
        <!--begin::Registered Content-->
        <div class="tab-pane" id="registered" role="tabpanel">
          <!--begin::Registered header-->
          <div class="card-header cursor-pointer px-0 d-flex align-items-center justify-content-between">
            <!--begin::Card title-->
            <div class="card-title m-0 d-block">
              <h3 class="fw-bolder m-0">Registered</h3>
              <!-- <p class="text-muted fs-6 fw-bold pt-2">Top 10</p> -->
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
            <!-- <a href="{{route('admin.profileedit')}}" class="btn btn-primary align-self-center">Edit Profile</a> -->
            <!--end::Action-->
          </div>
          <!--end::Registered header-->
          <!--begin::Registered Content-->
          <div class="card-body px-0 py-7">
            <!--begin:Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="">
              <!--begin::Table head-->
              <thead>
                <!--begin::Table row-->
                <tr class="fw-bolder text-muted">
                  <th class="w-10px"> <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                      <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input-regi" value="1" />
                    </div></th>
                  <th class="min-w-150px">Name</th>
                  <th class="min-w-80px">Match</th>
                  <th class="min-w-50px">Similar Req.</th>
                  <th class="min-w-50px">Profiles</th>
                  <th class="min-w-100px">Hiring Type</th>
                  <th class="min-w-100px">Status</th>
                  <th class="min-w-100px">Action</th>
                </tr>
                <!--end::Table row-->
              </thead>
              <!--end::Table head-->
              <!--begin::Table body-->
              <tbody class="text-gray-600 fw-bold">
           
<?php 
	if(!empty($spartnerArr)){
		foreach($spartnerArr as $sval){
			
			$bgTextDecore = $strVal = '';
			$str 			= trim(@$sval->name);
			$str 			= $strVal = strtolower($str[0]);
			$str = ord($str);
			if ($str >= 97 && $str <= 101) {
				$bgTextDecore = 'fs-3 bg-light-danger text-danger';
			} 
			else if ($str >= 102 && $str <= 106) {
				$bgTextDecore = 'fs-3 bg-light-warning text-warning';
			} 
			else if ($str >= 107 && $str <= 111) {
				$bgTextDecore = 'fs-3 bg-light-primary text-primary';
			} 
			else if ($str >= 112 && $str <= 116) {
				$bgTextDecore = 'fs-3 bg-light-success text-success';
			} 
			else if ($str >= 117 && $str <= 121) {
				$bgTextDecore = 'fs-3 bg-dark text-secondary';
			} else {
				$bgTextDecore = 'fs-3 bg-light-secondary text-dark';
			}
		
?>		   
                <tr class="">
                  <td class="">
				  		<div class="form-check form-check-sm form-check-custom form-check-solid">
                      		<input class="form-check-input form-check-input-regi" type="checkbox" value="1" />
                    	</div>
				  </td>
                  <td class="d-flex align-items-center border-0">
				  	<div class="d-flex align-items-center">
								<div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5">
									<a href="javascript:void(0);" class="">
										<div class="symbol-label <?php echo @$bgTextDecore; ?> border-radius-5"><?php echo strtoupper(@$strVal); ?></div>
									</a>
								</div>
								<div class="d-flex flex-column"> 
									<span class="text-dark fw-bolder fs-6"><?php echo ucwords(substr(@$sval->name, 0, 15));  ?></span> 
									<span class="text-muted fw-bold d-block fs-7"><?php echo substr(@$sval->email, 0, 15);  ?></span>    
								</div>
								</div>
								
                    <!--<div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> 
						<a href="javascript:void(0);">
                      		<div class="symbol-label border-radius-5"> 
								<img src="https://cpb-us-w2.wpmucdn.com/blog.smu.edu/dist/0/141/files/2017/12/94e9035a11b4869bec9266f730084bac.png" alt="Company Logo" class="border-radius-5 w-100"> 
							</div>
                      	</a> 
					</div>
                    <div class="d-flex flex-column"> 
						<a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">
							Adobe
						</a> 
						<a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">
							Staffing Partners
						</a> 
					</div>-->
                  </td>
                  <td class="">
				  	<div class="d-flex flex-column w-100 me-2">
                      	<div class="d-flex flex-stack"> 
							<span class="text-muted me-2 fs-7 fw-bold">40%</span> 
						</div>
                      	<div class="progress h-6px w-100">
                        	<div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      	</div>
                      	<span class="text-muted fw-bold d-block fs-7">Wordpress</span> 
					</div>
				  </td>
                  <td class="">
				  	<div class="d-flex flex-column"> 
						<span class="text-dark fw-bolder fs-6">567</span> 
					</div>
				  </td>
                  <td class="">
				  	<div class="d-flex flex-column"> 
						<span class="text-dark fw-bolder fs-6">7,651</span> 
					</div>
				  </td>
                  <td class="">
				  	<div class="d-flex flex-column"> 
						<span class="text-dark fw-bolder fs-6">Full Time</span> 
					</div>
				  </td>
                  <td class="">
				  	<div class="d-flex flex-column"> 
						<span class="text-success fw-bolder fs-6">Registered</span> 
					</div>
				  </td>

<td class="">
<div class="d-flex flex-column" id="<?php echo @$sval->id; ?>">
<?php 
		if(@$sval->job_assign_id > 0)
		{
			// Already Assign  
?>						 
<a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130 cp_select" id="spartner_<?php echo @$sval->id ?>" rel="<?php echo @$sval->id ?>" data-assign="0" data-jobid="<?php echo @$job_id; ?>" >
	Cancel Request
</a> 
<?php 
		}
		else
		{
			// Assign request
?>
<a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130 cp_select" id="spartner_<?php echo @$sval->id ?>" rel="<?php echo @$sval->id ?>" data-assign="1" data-jobid="<?php echo @$job_id; ?>">
	Assigned Job
</a> 
<?php } ?>		
</div>
</td>


</tr>
<?php 
		}
	}	
?>				
				
                <!--<tr class="">
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-regi" type="checkbox" value="1" />
                    </div></td>
                  <td class=" d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="47" class="show-detail">
                      <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5">I</div>
                      </a> </div>
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="47" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="Ibm-test2">IBM</a> <a href="javascript:void(0);" data-row-id="47" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Hiring Manager</a> </div>
                  </td>
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">60%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 60%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">React JS</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">30</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">456</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Part Time</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-primary w-130">Assigned Job</a> </div></td>
                </tr>-->
			   
                <!--<tr class="">
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-regi" type="checkbox" value="1" />
                    </div></td>
                  <td class="d-flex align-items-center border-0">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2  border-radius-5"> <a href="javascript:void(0);">
                      <div class="symbol-label border-radius-5"> <img src="https://www.webdesignermall.com/wp-content/uploads/2017/02/4bell.png" alt="Company Logo" class="border-radius-5 w-100"> </div>
                      </a> </div>
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6">4Bell Tehcnology</a> <a href="javascript:void(0);" class="fw-bold text-muted d-block fs-7 text-hover-primary">Hiring Manager</a> </div>
                  </td>
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">80%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">Core Java</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">57</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">651</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Part Time</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Cancel Request</a> </div></td>
                </tr>-->
               
			  
                <!--<tr class="">
                  <td class=""><div class="form-check form-check-sm form-check-custom form-check-solid">
                      <input class="form-check-input form-check-input-regi" type="checkbox" value="1" />
                    </div></td>
                  <td class=" d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left"> <a href="javascript:void(0);" data-row-id="47" class="show-detail">
                      <div class="symbol-label fs-3 bg-light-primary text-primary border-radius-5">T</div>
                      </a> </div>
                    <div class="d-flex flex-column"> <a href="javascript:void(0);" data-row-id="47" class="text-dark fw-bolder text-hover-primary fs-6 show-detail" data-toggle="tooltip1" title="" data-bs-original-title="Ibm-test2">Tech Mahindra</a> <a href="javascript:void(0);" data-row-id="47" class="fw-bold text-muted d-block fs-7 text-hover-primary show-detail">Recruiter</a> </div>
                  </td>
                  <td class=""><div class="d-flex flex-column w-100 me-2">
                      <div class="d-flex flex-stack"> <span class="text-muted me-2 fs-7 fw-bold">100%</span> </div>
                      <div class="progress h-6px w-100">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="text-muted fw-bold d-block fs-7">Node JS</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">80</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">56</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-dark fw-bolder fs-6">Full Time</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <span class="text-success fw-bolder fs-6">Registered</span> </div></td>
                  <td class=""><div class="d-flex flex-column"> <a href="javascript:void(0);" class="btn btn-sm btn-light-danger w-130">Cancel Request</a> </div></td>
                </tr>-->
               
				
				
				
              </tbody>
              <!--end::Table body-->
            </table>
            <!--end::Table-->
          </div>
          <!--end::Requisitions Content-->
        </div>
        <!--end::Requisitions Content-->
      </div>
    </div>
    <!--end::Tabs Body-->
  </div>
  <!--end::Tabs-->
</div>

<script>

$( document ).ready(function() {
	
	
	 $('.cp_select').on('click', function(e) {
			
			var id				= 	$(this).attr('rel');
			var assign			=	$('#spartner_'+id).attr("data-assign"); 
			var jobid			=	$('#spartner_'+id).attr("data-jobid"); 			
    
			 var url       		=	"{{ route('admin.jobs.spartnerAssign',':id') }}";
			 url         		=	url.replace(':id', id);
			 var token       	=	"{{ csrf_token() }}";
			 $.ajax({
							type		: 'POST',
							url			: url,
							data		: {'_token': token, '_method': 'POST', 'id':id, 'assign':assign, 'jobid':jobid},
							success		: function (response)
						    {
							  table._fnDraw();
						    }
			 });
			
			
			//alert(assign);
			
			if(assign ==0)
			{
				$(this).addClass("btn-light-primary");
				$(this).removeClass("btn-light-danger");
				$(this).text('Assigned Job');
				$(this).attr("data-assign", 1);
				//addRecruiterManager(title);
			}
			else if(assign ==1)
			{
				$(this).addClass("btn-light-danger");
				$(this).removeClass("btn-light-primary");
				$(this).text('Cancel Request');
				$(this).attr("data-assign", 0);	
				//DeleteEvent(title);
			}
			
	});
	
});

</script>