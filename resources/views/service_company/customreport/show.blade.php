<!--begin::New JS-->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
<!--end::New JS-->

<div class="modal-header d-none" id="debug" contenteditable data-name="custom-text">
  <h5 class="modal-title">Job Title:&nbsp; <?php echo @$jobs->title; ?> </h5>
  <button type="button" class="close btn-sm mr-0"data-dismiss="modal" aria-hidden="true"> <i class="ti-close text-dark"></i> </button>
</div>
<div class="modal-body pt-1">
  <div class="row">
    <div class="col-12">
      <div class="pb-1">
        <div class="card-body bg-white pt-3">
          <!--begin::Toolbar-->
          <div class="row pt-2">
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
              <!--begin::Page title-->
              <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Title-->
                <div class="d-flex flex-column">
                  <h2 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"> <span class="text-capitalize"><?php echo @$jobs->title; ?><span> </h2>
                  <span class="text-gray-600" id=""> <span class="text-capitalize"><?php echo @$total->application; ?></span> </span> </div>
                <!--end::Title-->
              </div>
              <!--end::Page title-->
              <!--begin::Actions-->
              <div class="d-flex align-items-center py-1">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1 me-3" style="display:none!important;">
                  <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                  <span class="svg-icon svg-icon-1 position-absolute ms-6">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
                  </svg>
                  </span>
                  <!--end::Svg Icon-->
                  <input type="text" data-kt-user-table-filter="search" name="inputSearch" id="inputSearch" class="form-control form-control-solid w-250px ps-14" style="padding: .6rem 1rem;" placeholder="Search...">
                </div>
                <!--end::Search-->
                <div class="card-toolbar">
                  <!--begin::Toolbar-->
                  <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <!--begin::Filter-->
                    <button type="button" class="d-none btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"> <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Filter </button>
                    <!--end::Filter-->
                    <!--begin::Export to Excel-->
                    <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder"> <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                      <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                      <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Export </button>
                    <!--end::Export to Excel-->
                  </div>
                  <!--end::Toolbar-->
                </div>
              </div>
              <!--end::Actions-->
            </div>
          </div>
          <!--end::Toolbar-->

          <!--begin::Table-->
          <table id="myApplicationList" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="fw-bolder text-muted">
                  <th class="w-10px pe-0">
                      <div class="form-check form-check-sm form-check-custom form-check-solid me-7">
                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#myApplicationList .form-check-input" value="1">
                      </div>
                  </th>
                  <th class="">Candidate Name</th>
                  <th class="">Contacts</th>
                  <th class="" data-toggle="tooltip" title="Experience">Exp.</th>
                  <th class="">Salary</th>
                  <th class="">Source</th>
                  <th class="">Requisition</th>
                  <th class="">Stage</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
              <?php 
                $i=1;
                if(!empty($applicantlist))
                {					
                  foreach($applicantlist as $appVal)
                  {					
                      $className	=	'odd';
                      if($i%2==0)
                      {
                        $className	=	'even';	
                      }
                      ?>
                <tr>
                  <td tabindex="0">
                      <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1">
                      </div>
                  </td>
                  <td class=" align-items-center">
                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-2 border-radius-5" style="float:left">
                      <a href="javascript:void(0);" class="show-application-detail">
                          <div class="symbol-label fs-3 bg-light-warning text-warning border-radius-5"><?php echo ucfirst(substr(@$appVal->full_name, 0, 1)); ?></div>
                      </a>
                    </div>
                    <div class="d-flex flex-column">
                      <div class="d-flex align-items-center">
                          <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 show-application-detail text-capitalize"><?php echo @$appVal->full_name; ?></a>
                      </div>
                    </div>
                  </td>
                  <td>
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo @$appVal->phone; ?></a>
                        <a href="javascript:void(0);" class="text-muted fw-bold d-block fs-7 text-hover-primary"><?php echo @$appVal->email;  ?></a>
                      </div>
                  </td>
                  <td>
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6">
                          <i class="fa fa-inr" aria-hidden="true"></i><?php echo @$appVal->total_exp; ?>
                        </a>
                        <span class="text-muted fw-bold text-muted d-block fs-7">
                          <i class="fa fa-inr" aria-hidden="true"></i><?php echo @$appVal->rel_exp; ?>
                        </span>
                      </div>
                  </td>
                  <td>
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder fs-6"><?php echo @$appVal->ectc; ?></a>
                        <span class="text-muted fw-bold text-muted d-block fs-7"><?php echo @$appVal->ctc; ?></span>
                      </div>
                  </td>
                  <td>
                      <div class="d-flex flex-column">
                        <span class="text-muted fw-bold d-block fs-7"><?php echo ucfirst(@$appVal->source); ?></span>                        
                      </div>
                  </td>
                  <td>
                      <div class="d-flex flex-column">
                        <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo @$appVal->job_title; ?></a>
                        <span class="text-muted fw-bold d-block fs-7"><?php echo @$appVal->client_name; ?></span>
                      </div>
                  </td>
                  <td>
                      <div class="d-flex flex-column">
                        <a href="javascript.void(0);" data-row-id="808" class="d-flex flex-column stage_sub_stage stageupdate" data-toggle="modal" data-target="#stage_sub_stage" onclick="updateApplicantStage(808)">
                        <span class="badge fw-bolder fs-6 px-2 py-1 text-dark text-left" data-toggle="tooltip" title=""><?php echo @$appVal->master_status_name; ?></span>
                        <span class="text-muted fw-bold d-block fs-7" data-toggle="tooltip" title="--------">--------</span>
                        </a>
                      </div>
                  </td>
                </tr>   
                <?php 
                      $i = $i+1;							
                    }
                  }
                  else
                  {
                    ?>
                <tr role="row" class="odd">
                  <th style="width:200px" colspan="12">No Record Found !</th>
                </tr>
                <?php 
                  }
                  ?>
            </tbody>
          </table>
          <!--end::Table-->
        </div>
      </div>
    </div>
  </div>
</div>
<!--begin::Tooltip-->
<script>
$(function ()
{
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<!--end::Tooltip-->
<script>
	$(document).ready(function() {
    var table = $('#myApplicationList').removeAttr('width').DataTable( {
        scrollY         : true,
        "scrollY"       : 300,
        scrollX         : false,
        scrollCollapse  : true,
        "bFilter"       : false,
        "bLengthChange" : false,
        "bPaginate"     : true,

        columnDefs: [
            { data: 'client_name', name: 'client_name', searchable : false, orderable : false , "width": "5%"},
            { data: 'job_title', name: 'job_title', searchable : false, orderable : false , "width": "20px"},
            { data: 'source', name: 'source', searchable : false, orderable : false , "width": "20px"},
            { data: 'full_name', name: 'full_name', searchable : false, orderable : false , "width": "20px"},
            { data: 'email', name: 'email', searchable : false, orderable : false , "width": "20px"},
            { data: 'phone', name: 'phone', searchable : false, orderable : false , "width": "20px"},
            { data: 'ctc', name: 'ctc', searchable : false, orderable : false , "width": "20px"},
            { data: 'ectc', name: 'ectc', searchable : false, orderable : false , "width": "20px"},
            { data: 'total_exp', name: 'total_exp', searchable : false, orderable : false , "width": "20px"},
            { data: 'rel_exp', name: 'rel_exp', searchable : false, orderable : false , "width": "20px"},
            { data: 'master_status_name', name: 'master_status_name', searchable : false, orderable : false , "width": "20px"},
        ],
        fixedColumns: false
    } );
} );
</script>
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>
<script>
   var job_id					=	$('#job_id').val();
   var application_status		=	$('#application_status').val();
</script>
