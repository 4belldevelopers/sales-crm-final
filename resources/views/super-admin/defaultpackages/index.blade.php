@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<!--<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">-->
<style>
        body
        {overflow-x: hidden;}
        .company-logo {
            max-height: 50px;
			max-width:120px;
        }

        .company-logo-div {
            border-radius: 2px;
            padding: 2px 2px 2px 2px;
            margin-bottom: 2px;
        }
        .symbol.symbol-circle, .symbol.symbol-circle .symbol-label, .symbol.symbol-circle > img
        {
            border-radius: 10%;
        }

@media only screen and (min-width: 1024px) and (max-width: 1440px)
{
  #kt_activitiesDefaultPackage
  {
    width: 1025px;
  }
}

@media only screen and (min-width: 1440px) and (max-width: 2560px)
{
  #kt_activitiesDefaultPackage
  {
    width: 1200px;
  }
}

</style>


@endpush

@section('content')
<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Default Package  <span class="h-20px border-gray-200 border-start ms-3 mx-2 d-none"></span> <small class="text-muted fs-7 fw-bold my-1 ms-1 d-none">#XRS-45670</small> </h1>
    </div>
  </div>
</div>


<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
		
		
		
		
		
<table>
					<tbody><tr>
						<td>
								<h4 class="card-title" style="text-align:left">Package</h4>
						</td>
						<td>
								<a href="javascript:void(0);" id="editDefaultPackage"> 
									&nbsp;<i class="bi bi-pencil-fill" style="color:#5DB8F9"></i>Edit
								</a>
						</td>
					</tr>
				</tbody></table>		
<div class="table-responsive">
              <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
                <thead class="">
                  <tr class="fw-bolder text-muted">
                    <th class="min-w-200px">Product</th>
					<th class="min-w-150px">Quantity</th>
                    <th class="min-w-150px">Validity(In Days)</th>
                    <th class="min-w-150px">Start Date</th>
                    <th class="min-w-150px">End Date</th>
                    <th class="min-w-150px">Status</th>
                  </tr>
                </thead>
                <tbody id="defaultPackageBody">
<?php 
		$today 		=	$status	= $cvaccess_endDate	=	'';
		$today 		= 	date('d M, Y');
		 //date("d M, Y", strtotime(@$emails_enddate))
		 
		 if(@$defualt_package->cvaccess_validity > 0){
		 	$cvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->cvaccess_validity.' days'));
		 }else{
		 	$cvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
		 }
		 
?>				
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->cvaccess_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->cvaccess_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$cvaccess_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>

<?php 		
if(@$defualt_package->users_validity > 0){
	$users_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->users_validity.' days'));
}else{
	$users_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
}		 
?>	       
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Users</a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->users_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->users_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$users_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>
		

<?php 		
if(@$defualt_package->newclientrequest_validity > 0){
	$newclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->newclientrequest_validity.' days'));
}else{
	$newclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
}		 
?>	       
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"> Contract Recruitment Lead </a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->newclientrequest_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->newclientrequest_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newclientrequest_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>
		

<?php 		
if(@$defualt_package->newbenchclientrequest_validity > 0){
	$newbenchclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->newbenchclientrequest_validity.' days'));
}else{
	$newbenchclientrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
}		 
?>	       
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->newbenchclientrequest_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->newbenchclientrequest_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newbenchclientrequest_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>
		

<?php 		
if(@$defualt_package->newjobrequest_validity > 0){
	$newjobrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->newjobrequest_validity.' days'));
}else{
	$newjobrequest_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
}		 
?>	       
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Fulltime Recruitment Leads</a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->newjobrequest_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->newjobrequest_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newjobrequest_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>

<?php 		
if(@$defualt_package->emails_validity > 0){
	$emails_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->emails_validity.' days'));
}else{
	$emails_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
}		 
?>	       
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->emails_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->emails_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$emails_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>

<?php 		
if(@$defualt_package->oldcvaccess_validity > 0){
	$oldcvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + '.@$defualt_package->oldcvaccess_validity.' days'));
}else{
	$oldcvaccess_endDate	=	date('d M, Y', strtotime(date('Y-m-d'). ' + 0 days'));
}		 
?>	       
<tr>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->oldcvaccess_quantity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$defualt_package->oldcvaccess_validity; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$today; ?></a></td>
	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$oldcvaccess_endDate; ?></a></td>
	<td class="">
<?php  
echo @$defualt_package->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>'; 
?> 
	</td>
</tr>

		         
				  
                </tbody>
              </table>
            </div>		
		
		
		
		
         
          </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post--> 


         


</div>
<!--end::Content-->


<div id="kt_activitiesDefaultPackage_toggle"></div>
<div id="kt_activitiesDefaultPackage" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDefaultPackage_toggle" data-kt-drawer-close="#kt_activitiesDefaultPackage_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Edit Default Package</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesDefaultPackage_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesDefaultPackage_body"></div>
  </div>
</div>



@endsection

@push('footer-script')
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.colVis.min.js"></script>

<script>

 

 $('body').on('click', '#editDefaultPackage', function ()
		{
			 						
			$('#kt_activitiesDefaultPackage_toggle').trigger('click');		
			var url   		=	"{{ route('superadmin.defaultpackages.editDefaultPackage') }}";
			var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}"; 
			$.ajax({
							type  		: 'GET',
							url   		: url,
							 beforeSend	: function() {
										 $('#kt_activitiesDefaultPackage_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
							},
							success 	: function (response) 
							{
									setTimeout(
									()=>
									{
											if (response.status == "success")
											{
												$('#kt_activitiesDefaultPackage_body').html(response.view);
											}
									}, 500);
									
							}
					});
		}); 
		
</script>

@endpush 