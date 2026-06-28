<?php 
		use App\Helper\GeneralMethod;
?>

<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">

<style>
.text-justify{text-align: justify!important;}
.symbol1 > img,
.symbol1 .symbol-label1{	width: 32px!important;	height: 32px!important; }
.table > :not(caption) > * > * { padding: 0rem 0.4rem; }

.input-xs, select.input-xs {
  height: 20px;
  line-height: 20px;
}
.ele_border{ border:1px solid #666666 !important; width:100px !important;}
</style>


<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">


<div class="card card-custom mx-2">
      <div class="card-header card-header-tabs-line">
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
            <li class="nav-item me-3"> 
				<a class="nav-link active show" data-toggle="tab" href="#job_description"> 
					<span class="nav-text font-weight-bold">
						Current Wallet Status
					</span> 
				</a> 
			</li>
          </ul>
        </div>
      </div>
      <div class="card-body py-0">
        <div class="tab-content pt-5">
          <div class="tab-pane active show" id="job_description" role="tabpanel">
				<!--<div class="card-header cursor-pointer px-0">
					  <div class="card-title m-0">
						<h3 class="fw-bolder m-0">Detail</h3>
					  </div>
				</div>-->
           
            <div class="card-body px-0 py-7">
              
              <div class="row">
			  
	<div class="card" style="margin-top:10px;">
          <div class="card-body">
            
            <div class="table-responsive">
              
			  
			  
<form class="ajax-form" method="POST" id="createForm" name="createForm">
@csrf
						  
<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
<thead class="">

<tr class="fw-bolder text-muted">
<th class="min-w-200px">Product</th>
<th class="min-w-140px">Quantity Allocated</th>
<th class="min-w-140px">Validity(In Days)</th>
<th class="min-w-140px">Status</th>
</tr>
</thead>
<tbody class="">
			
<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
<td><input type="number" id="cvaccess_quantity" name="cvaccess_quantity" value="<?php echo @$defualt_package->cvaccess_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="cvaccess_validity" name="cvaccess_validity" value="<?php echo @$defualt_package->cvaccess_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="cvaccess_status" id="cvaccess_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->cvaccess_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->cvaccess_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr>
  

<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Users</a></td>
<td><input type="number" id="users_quantity" name="users_quantity" value="<?php echo @$defualt_package->users_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="users_validity" name="users_validity" value="<?php echo @$defualt_package->users_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="users_status" id="users_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->users_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->users_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr>

<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Contract Recruitment Lead</a></td>
<td><input type="number" id="newclientrequest_quantity" name="newclientrequest_quantity" value="<?php echo @$defualt_package->newclientrequest_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="newclientrequest_validity" name="newclientrequest_validity" value="<?php echo @$defualt_package->newclientrequest_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="newclientrequest_status" id="newclientrequest_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->newclientrequest_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->newclientrequest_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr> 


<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</a></td>
<td><input type="number" id="newbenchclientrequest_quantity" name="newbenchclientrequest_quantity" value="<?php echo @$defualt_package->newbenchclientrequest_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="newbenchclientrequest_validity" name="newbenchclientrequest_validity" value="<?php echo @$defualt_package->newbenchclientrequest_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="newbenchclientrequest_status" id="newbenchclientrequest_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->newbenchclientrequest_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->newbenchclientrequest_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr>

<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Fulltime Recruitment Leads</a></td>
<td><input type="number" id="newjobrequest_quantity" name="newjobrequest_quantity" value="<?php echo @$defualt_package->newjobrequest_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="newjobrequest_validity" name="newjobrequest_validity" value="<?php echo @$defualt_package->newjobrequest_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="newjobrequest_status" id="newjobrequest_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->newjobrequest_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->newjobrequest_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr>

<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</a></td>
<td><input type="number" id="emails_quantity" name="emails_quantity" value="<?php echo @$defualt_package->emails_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="emails_validity" name="emails_validity" value="<?php echo @$defualt_package->emails_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="emails_status" id="emails_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->emails_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->emails_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr>

<tr>
<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</a></td>
<td><input type="number" id="oldcvaccess_quantity" name="oldcvaccess_quantity" value="<?php echo @$defualt_package->oldcvaccess_quantity; ?>" class="form-control form-control-sm form-control-solid ele_border" /></td>
<td><input type="number" id="oldcvaccess_validity" name="oldcvaccess_validity" value="<?php echo @$defualt_package->oldcvaccess_validity; ?>" class="form-control form-control-sm form-control-solid ele_border"  /></td> 
<td class="">
<select name="oldcvaccess_status" id="oldcvaccess_status" class="form-control form-control-sm form-control-solid ele_border">
	<option value="1" <?php echo @$defualt_package->oldcvaccess_status==1? 'selected="selected"':''; ?> >Active</option>
	<option value="0" <?php echo @$defualt_package->oldcvaccess_status!=1? 'selected="selected"':''; ?>>InActive</option> 
</select>
</td>
</tr>


<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>
<button type="button" name="update" id="update-form" value="update" class="btn btn-primary">Update</button>                   
</td>
<td><a href="javascript:void(0);" class="btn btn-secondary close" data-dismiss="modal" data-kt-drawer-dismiss="true">Cancel</a></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
				  
                </tbody>
              </table>			  
			  
</form>
			  
			  
			  
            </div>
          </div>
        </div>
		
              </div>
             
            </div>
          </div>
        </div>
      </div>
    </div>
	
	
 </div>
</div>





<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>

$('#update-form').click(function () {

	const swalWithBootstrapButtons = Swal.mixin({
		  customClass: {
							confirmButton: 'btn btn-success',
							cancelButton: 'btn btn-danger'
		  },
		  buttonsStyling: false
		})
		swalWithBootstrapButtons.fire({
										  title				: 'Are you sure?',
										  text				: "You want to default subscription package!",
										  icon				: 'warning',
										  showCancelButton	: true,
										  confirmButtonText	: 'Yes, Ignore it!',
										  cancelButtonText	: 'No, cancel!',
										  reverseButtons	: true
									}).then((result) => {
		  if (result.isConfirmed) 
		  {
			
		  								 var formData 					=	new FormData($('#createForm')[0]);					
										  $.ajax({
													  url				: '{{route('superadmin.defaultpackages.update')}}'+'?_token=' + '{{ csrf_token() }}',	
													  type				: "POST",
													  data 				: formData,
													  processData		: false,  				// tell jQuery not to process the data
													  contentType		: false,  				// tell jQuery not to set contentType
													  //container		: '#createForm',
													 
													  //redirect			: true,
													  //file				: true,
													  //data			: $('#createForm').serialize(),
													  success			: function (response) {
													  							if(response.status == 'success'){
																					 $('#defaultPackageBody').html(response.datatable);
																					
																					 swalWithBootstrapButtons.fire(
																														'Success!', 
																														'Selected request has been Updated.',
																														'success'
																													).then(function(){
																														$("#kt_activitiesDefaultPackage_close").click();
																													});
																																
																				}
																				//window.location = response.url;
																			},
														error			: function (response) {
																						handleFails(response);
																					}
												})
					
										/*var url   			= "{{ route('service_company.clients.request_update') }}";
										$.ajax({
													type	: 'GET',
													url		: url,
													data	: {'invitation_id':id, 'event_id': event_id, 'from_company': from_company},
													success	: function (response) {
																									var clientAll		=	response.clientDataCnt.clientAll;
																									var clientDirect	=	response.clientDataCnt.clientDirect;
																									var clientCustom	=	response.clientDataCnt.clientCustom;
																									var clientRequest	=	response.clientDataCnt.clientRequest;
																									
																									$('#span_clientAll').html(clientAll);
																									$('#span_clientDirect').html(clientDirect);
																									$('#span_clientCustom').html(clientCustom);
																									$('#span_clientRequest').html(clientRequest);
																									
																						if(response.status == 'success') {
													
																									table._fnDraw(); 
																									swalWithBootstrapButtons.fire(
																																	'Ignore!',
																																	'Selected request has been Ignore.',
																																	'success'
																																)
																						}
													}
										});*/
		  
		  
		  
		  
		  
		  
		  
		  
		  } else if (
			result.dismiss === Swal.DismissReason.cancel
		  ) {
			swalWithBootstrapButtons.fire(
			  'Cancelled',
			  'Selected request is safe',
			  'error'
			)
		  }
		})

 });		
</script>