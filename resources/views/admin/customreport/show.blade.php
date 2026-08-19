<!--begin::New JS-->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}<?php echo TM_VERSION; ?>"></script>
<!-- <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}<?php echo TM_VERSION; ?>"></script> -->
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('froiden-helper/helper.js') }}<?php echo TM_VERSION; ?>"></script>
<!--end::New JS-->

<div class="modal-header d-none" id="debug" contenteditable data-name="custom-text">
  <h5 class="modal-title">Job Title:&nbsp;
  	<?php echo @$jobs->title; ?>
  </h5>
  <button type="button" class="close btn-sm mr-0"data-dismiss="modal" aria-hidden="true">
  	<i class="ti-close text-dark"></i>
  </button>
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
                <h2 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                  <span class="text-capitalize"><?php echo @$jobs->title; ?><span>                    
                </h2>
                <span class="text-gray-600" id="">
                  <span class="text-capitalize"><?php echo @$total->application; ?></span>
                </span>
              </div>
              <!--end::Title-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center py-1">
              <!--begin::Search-->
              <div class="d-flex align-items-center position-relative my-1 me-3">
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
                  <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3">
                    <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                      <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                    </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Filter
                  </button>
                  <!--end::Filter-->
                  <!--begin::Export to Excel-->
                  <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder">
                    <span class="svg-icon svg-icon-2">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                        <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                        <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                      </svg>
                    </span>
                    <!--end::Svg Icon-->
                    Export
                  </button>
                  <!--end::Export to Excel-->
                </div>
                <!--end::Toolbar-->
              </div>
            </div>
            <!--end::Actions-->
          </div>
        </div>
        <!--end::Toolbar-->
        <table id="myApplicationList" class="table dataTables_wrapper dt-bootstrap4 no-footer">
          <thead>
            <tr class="table-bg-custom">
              <th>
                <a href="javascript:void(0);" class="font-weight-600" data-toggle="tooltip" data-placement="top" title="Serial Number">S.No</a>
              </th>
              <th>Client</th>
              <th>Requisitions</th>
              <th>Source</th>
              <th>Applicant </th>
              <th>Email</th>
              <th>Phone</th>
              <!-- <th>Salary</th> -->
              <th>CTC</th>
              <th>
                <a href="javascript:void(0);" class="font-weight-600" data-toggle="tooltip" data-placement="top" title="Expected CTC">E.CTC</a>
              </th>
              <th>
                <a href="javascript:void(0);" class="font-weight-600" data-toggle="tooltip" data-placement="top" title="Experience">EXP.</a>
              </th>
              <th>
                <a href="javascript:void(0);" class="font-weight-600" data-toggle="tooltip" data-placement="top" title="Relevant Experience">R.EXP</a>
              </th>
              <th>
                <a href="javascript:void(0);" class="font-weight-600" data-toggle="tooltip" data-placement="top" title="Interview Stage">I.Stage</a>
              </th>
            </tr>			
            <tbody>
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
                      <tr role="row" class="<?php echo @$className; ?>">
                          <th style="width:10px"><?php echo @$i; ?></th>
                          <th><?php echo @$appVal->client_name; ?></th>
                          <th><?php echo @$appVal->job_title; ?></th>
                          <th><?php echo @$appVal->source; ?></th>
                          <th><?php echo @$appVal->full_name; ?> </th>
                          <th><?php echo @$appVal->email; ?></th>
                          <th><?php echo @$appVal->phone; ?></th>
                          <th><?php echo @$appVal->ctc; ?></th>
                          <th><?php echo @$appVal->ectc; ?></th>
                          <th><?php echo @$appVal->total_exp; ?></th>
                          <th><?php echo @$appVal->rel_exp; ?></th>
                          <th><?php echo @$appVal->master_status_name; ?></th>		  
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
          </thead>
        </table>
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
        "bLengthChange" : false,
        "lengthMenu"    : [[10, 25, 50, 100], [10, 25, 50, 100]],
        columnDefs: [
            { data: 'job_applications', name: 'job_applications', searchable : false, orderable : false , "width": "2%", "targets": 0 },
            { data: 'client_name', name: 'client_name', searchable : false, orderable : false , "width": "5%", "targets": 1 },
            { data: 'job_title', name: 'job_title', searchable : false, orderable : false , "width": "20px", "targets": 2 },
            { data: 'source', name: 'source', searchable : false, orderable : false , "width": "20px", "targets": 3 },
            { data: 'full_name', name: 'full_name', searchable : false, orderable : false , "width": "20px", "targets": 4 },
            { data: 'email', name: 'email', searchable : false, orderable : false , "width": "20px", "targets": 5 },
            { data: 'phone', name: 'phone', searchable : false, orderable : false , "width": "20px", "targets": 6 },
            { data: 'ctc', name: 'ctc', searchable : false, orderable : false , "width": "20px", "targets": 7 },
            { data: 'ectc', name: 'ectc', searchable : false, orderable : false , "width": "20px", "targets": 8 },
            { data: 'total_exp', name: 'total_exp', searchable : false, orderable : false , "width": "20px", "targets": 9 },
            { data: 'rel_exp', name: 'rel_exp', searchable : false, orderable : false , "width": "20px", "targets": 10 },
            { data: 'master_status_name', name: 'master_status_name', searchable : false, orderable : false , "width": "20px", "targets": 11 },
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