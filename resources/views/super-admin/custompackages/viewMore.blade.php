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
</style>
<?php 

		//echo '<pre>'; print_r(@$packageDetail); exit; 
		
		
		$bgTextDecore			=	$strVal	=	'';
		$str					=	trim(@$packageDetail->company_name);
		$str	= $strVal		=	strtolower($str[0]);
		$str					=	ord($str);
		if($str >=97 && $str <=101){
			$bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
		}else if($str >=102 && $str <=106){
			$bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
		}else if($str >=107 && $str <=111){
			$bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
		}else if($str >=112 && $str <=116){
			$bgTextDecore		=	'fs-3 bg-light-success text-success';	
		}else if($str >=117 && $str <=121){
			$bgTextDecore		=	'fs-3 bg-dark text-secondary';	
		}else {
			$bgTextDecore		=	'fs-3 bg-light-secondary text-dark';	
		}
		
		$company_type			=	'';
		$company_type_arr		=	GeneralMethod::accountCompanyType();
		if(@$packageDetail->company_type == 1){
			$company_type		=	$company_type_arr[@$packageDetail->company_type];
		}else if(@$packageDetail->company_type == 2){
			$company_type		=	$company_type_arr[@$packageDetail->company_type];
		}else{
			$company_type		=	$company_type_arr[3];
		}

$cvaccess_startdate			=	$cvaccess_enddate	=	$cvaccess_status	=	$cvaccess_quantity	=	$cvaccess_price	=	$cvaccess_totalprice	=	'';
$cvaccess_startdate			=	@$packageDetail->cvaccess_startdate ? date("d M, Y", strtotime(@$packageDetail->cvaccess_startdate)): '&nbsp;';
$cvaccess_enddate			=	@$packageDetail->cvaccess_enddate	? date("d M, Y", strtotime(@$packageDetail->cvaccess_enddate)): '&nbsp;';
$cvaccess_status			=	@$packageDetail->cvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$cvaccess_quantity			=	@$packageDetail->cvaccess_quantity ? number_format(@$packageDetail->cvaccess_quantity): '&nbsp;';
$cvaccess_price				=	@$packageDetail->cvaccess_price? number_format(@$packageDetail->cvaccess_price, 2) : '&nbsp;';
$cvaccess_totalprice		=	@$packageDetail->cvaccess_totalprice > 0? number_format(@$packageDetail->cvaccess_totalprice, 2): '&nbsp;';

$users_startdate			=	$users_enddate	=	$users_status	=	$users_quantity	=	$users_price	=	$users_totalprice	=	'';
$users_startdate			=	@$packageDetail->users_startdate ? date("d M, Y", strtotime(@$packageDetail->users_startdate)): '&nbsp;';
$users_enddate				=	@$packageDetail->users_enddate	? date("d M, Y", strtotime(@$packageDetail->users_enddate)): '&nbsp;';
$users_status				=	@$packageDetail->users_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$users_quantity				=	@$packageDetail->users_quantity ? number_format(@$packageDetail->users_quantity): '&nbsp;';
$users_price				=	@$packageDetail->users_price? number_format(@$packageDetail->users_price, 2) : '&nbsp;';
$users_totalprice			=	@$packageDetail->users_totalprice > 0? number_format(@$packageDetail->users_totalprice, 2): '&nbsp;';
 
$newclientrequest_startdate	=	$newclientrequest_enddate	=	$newclientrequest_status	=	$newclientrequest_quantity	=	$newclientrequest_price	=	$newclientrequest_totalprice	=	'';
$newclientrequest_startdate	=	@$packageDetail->newclientrequest_startdate ? date("d M, Y", strtotime(@$packageDetail->newclientrequest_startdate)): '&nbsp;';
$newclientrequest_enddate	=	@$packageDetail->newclientrequest_enddate	? date("d M, Y", strtotime(@$packageDetail->newclientrequest_enddate)): '&nbsp;';
$newclientrequest_status	=	@$packageDetail->newclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$newclientrequest_quantity	=	@$packageDetail->newclientrequest_quantity ? number_format(@$packageDetail->newclientrequest_quantity): '&nbsp;';
$newclientrequest_price		=	@$packageDetail->newclientrequest_price? number_format(@$packageDetail->newclientrequest_price, 2) : '&nbsp;';
$newclientrequest_totalprice=	@$packageDetail->newclientrequest_totalprice > 0? number_format(@$packageDetail->newclientrequest_totalprice, 2): '&nbsp;';
  
$newjobrequest_startdate	=	$newjobrequest_enddate	=	$newjobrequest_status	=	$newjobrequest_quantity	=	$newjobrequest_price	=	$newjobrequest_totalprice	=	'';
$newjobrequest_startdate	=	@$packageDetail->newjobrequest_startdate ? date("d M, Y", strtotime(@$packageDetail->newjobrequest_startdate)): '&nbsp;';
$newjobrequest_enddate		=	@$packageDetail->newjobrequest_enddate	? date("d M, Y", strtotime(@$packageDetail->newjobrequest_enddate)): '&nbsp;';
$newjobrequest_status		=	@$packageDetail->newjobrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$newjobrequest_quantity		=	@$packageDetail->newjobrequest_quantity ? number_format(@$packageDetail->newjobrequest_quantity): '&nbsp;';
$newjobrequest_price		=	@$packageDetail->newjobrequest_price? number_format(@$packageDetail->newjobrequest_price, 2) : '&nbsp;';
$newjobrequest_totalprice	=	@$packageDetail->newjobrequest_totalprice > 0? number_format(@$packageDetail->newjobrequest_totalprice, 2): '&nbsp;';

$newbenchclientrequest_startdate	=	$newbenchclientrequest_enddate	=	$newbenchclientrequest_status	=	$newbenchclientrequest_quantity	=	$newbenchclientrequest_price	=	$newbenchclientrequest_totalprice	=	'';
$newbenchclientrequest_startdate	=	@$packageDetail->newbenchclientrequest_startdate ? date("d M, Y", strtotime(@$packageDetail->newbenchclientrequest_startdate)): '&nbsp;';
$newbenchclientrequest_enddate		=	@$packageDetail->newbenchclientrequest_enddate	? date("d M, Y", strtotime(@$packageDetail->newbenchclientrequest_enddate)): '&nbsp;';
$newbenchclientrequest_status		=	@$packageDetail->newbenchclientrequest_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$newbenchclientrequest_quantity		=	@$packageDetail->newbenchclientrequest_quantity ? number_format(@$packageDetail->newbenchclientrequest_quantity): '&nbsp;';
$newbenchclientrequest_price		=	@$packageDetail->newbenchclientrequest_price? number_format(@$packageDetail->newbenchclientrequest_price, 2) : '&nbsp;';
$newbenchclientrequest_totalprice	=	@$packageDetail->newbenchclientrequest_totalprice > 0? number_format(@$packageDetail->newbenchclientrequest_totalprice, 2): '&nbsp;';

$emails_startdate					=	$emails_enddate	=	$emails_status	=	$emails_quantity	=	$emails_price	=	$emails_totalprice	=	'';
$emails_startdate					=	@$packageDetail->emails_startdate ? date("d M, Y", strtotime(@$packageDetail->emails_startdate)): '&nbsp;';
$emails_enddate						=	@$packageDetail->emails_enddate	? date("d M, Y", strtotime(@$packageDetail->emails_enddate)): '&nbsp;';
$emails_status						=	@$packageDetail->emails_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$emails_quantity					=	@$packageDetail->emails_quantity ? number_format(@$packageDetail->emails_quantity): '&nbsp;';
$emails_price						=	@$packageDetail->emails_price? number_format(@$packageDetail->emails_price, 2) : '&nbsp;';
$emails_totalprice					=	@$packageDetail->emails_totalprice > 0? number_format(@$packageDetail->emails_totalprice, 2): '&nbsp;';


$oldcvaccess_startdate				=	$oldcvaccess_enddate	=	$oldcvaccess_status	=	$oldcvaccess_quantity	=	$oldcvaccess_price	=	$oldcvaccess_totalprice	=	'';
$oldcvaccess_startdate				=	@$packageDetail->oldcvaccess_startdate ? date("d M, Y", strtotime(@$packageDetail->oldcvaccess_startdate)): '&nbsp;';
$oldcvaccess_enddate				=	@$packageDetail->oldcvaccess_enddate	? date("d M, Y", strtotime(@$packageDetail->oldcvaccess_enddate)): '&nbsp;';
$oldcvaccess_status					=	@$packageDetail->oldcvaccess_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$oldcvaccess_quantity				=	@$packageDetail->oldcvaccess_quantity ? number_format(@$packageDetail->oldcvaccess_quantity): '&nbsp;';
$oldcvaccess_price					=	@$packageDetail->oldcvaccess_price? number_format(@$packageDetail->oldcvaccess_price, 2) : '&nbsp;';
$oldcvaccess_totalprice				=	@$packageDetail->oldcvaccess_totalprice > 0? number_format(@$packageDetail->oldcvaccess_totalprice, 2): '&nbsp;';


$boostkeywords_startdate			=	$boostkeywords_enddate	=	$boostkeywords_status	=	$boostkeywords_price	=	$boostkeywords_totalprice	=	$boostkeywords_text	= $boostkeywords_quantity = '';
$boostkeywords_startdate			=	@$packageDetail->boostkeywords_startdate ? date("d M, Y", strtotime(@$packageDetail->boostkeywords_startdate)): '&nbsp;';
$boostkeywords_enddate				=	@$packageDetail->boostkeywords_enddate	? date("d M, Y", strtotime(@$packageDetail->boostkeywords_enddate)): '&nbsp;';
$boostkeywords_status				=	@$packageDetail->boostkeywords_status == 1 ? '<label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">Active</label>': '<label class="badge text-danger fw-400 fs-6 bg-light-danger badge-sm py-1 px-3">Inactive</label>';	
$boostkeywords_text					=	@$packageDetail->boostkeywords_text ? @$packageDetail->boostkeywords_text: '&nbsp;';
$boostkeywords_price				=	@$packageDetail->boostkeywords_price? number_format(@$packageDetail->boostkeywords_price, 2) : '&nbsp;';
$boostkeywords_totalprice			=	@$packageDetail->boostkeywords_totalprice > 0? number_format(@$packageDetail->boostkeywords_totalprice, 2): '&nbsp;';

$boostkeywords_quantity				=	@$packageDetail->boostkeywords_quantity ? number_format(@$packageDetail->boostkeywords_quantity): '&nbsp;';
 			
?>

<input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo @$invoice_id;?>"  />
<div class="card-body position-relative px-0 py-0" id="kt_activities_body">
  <div id="kt_activities_scroll" class="position-relative scroll-y bg-light pt-2" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_activities_body" data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer" data-kt-scroll-offset="5px">
    <div class="card mb-5 mb-xl-5 mx-2">
      <div class="card-body pt-5 pb-0 px-5">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
          <div class="me-7 mb-4">
            <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
              <div class="symbol-label fs-3 <?php echo @$bgTextDecore; ?> text-danger border-radius-5"> <span><?php echo strtoupper($strVal); ?></span> </div>
              <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
            </div>
          </div>
		  
		   <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
              <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-2"> <span class="text-gray-900 fs-2 fw-bolder me-2"> <?php echo @$packageDetail->company_name; ?> </span> </div>
                <div class="d-flex flex-wrap fw-bold fs-6 pe-2"> <a href="javascript:void(0);" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2"> <span class="svg-icon svg-icon-4 me-1">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                      <rect x="0" y="0" width="24" height="24"></rect>
                      <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"></path>
                      <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"></path>
                      <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"></path>
                    </g>
                  </svg>
                  </span><?php echo date("d M, Y", strtotime(@$packageDetail->create_at)); ?> </a> </div>
              </div>
              <div class="d-flex mt-4">
                <?php 
						if($packageDetail->status == 'active'){
							echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-success btn-active-primary fw-bolder me-3 fs-6">'.@$packageDetail->status.'</a>'; 
						}else{
							echo '<a href="javascript:void(0);" type="button" class="btn btn-sm btn-light-danger btn-active-primary fw-bolder me-3 fs-6">'.@$packageDetail->status.'</a>'; 
						}
				?>
              </div>
            </div>
          </div>
        </div>
		
		
		<div class="separator separator-solid my-7"></div>
        <div class="d-flex align-items-center flex-wrap pb-7">
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Company Email</span> <span class="fw-bolder fs-3"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2"> <i class="bi bi-envelope"></i> </span> <?php echo @$packageDetail->company_email ? @$packageDetail->company_email : '&nbsp;'; ?> </span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Company Phone</span> <span class="fw-bolder fs-3"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2"> <i class="fa fa-phone" aria-hidden="true"></i> <span class="glyphicon glyphicon-earphone"></span> </span> <?php echo @$packageDetail->company_phone ? @$packageDetail->company_phone : '&nbsp;' ; ?></span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <div class="d-flex flex-column text-dark-75"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2"> <i class="bi bi-browser-chrome"></i> </span> <span class="fw-bolder fs-6">Company Website</span> <span class="fw-bolder fs-3"><?php echo @$packageDetail->website ? @$packageDetail->website : '&nbsp;'; ?></span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Account Type</span> <span class="fw-bolder fs-3"><?php echo @$company_type ? @$company_type:'&nbsp;'; ?></span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Company Size</span> <span class="fw-bolder fs-3"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2"> <i class="bi bi-textarea-resize"></i> </span> <?php echo @$packageDetail->company_size ? @$packageDetail->company_size : '0'; ?> </span> </div>
          </div>
          <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
            <div class="d-flex flex-column text-dark-75"> <span class="fw-bolder fs-6">Founded</span> <span class="fw-bolder fs-3"> <span class="svg-icon svg-icon-dark-800 svg-icon-3x me-2"> <i class="bi bi-trophy"></i> </span> <?php echo @$packageDetail->founded ? @$packageDetail->founded : '&nbsp;'; ?> </span> </div>
          </div>
        </div>
      </div>
    </div>
	
	 <div class="card card-custom mx-2">
      <div class="card-header card-header-tabs-line">
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x" role="tablist">
            <li class="nav-item me-3"> <a class="nav-link active show" data-toggle="tab" href="#job_description"> <span class="nav-text font-weight-bold">Custom Package</span> </a> </li>
          </ul>
        </div>
      </div>
      <div class="card-body py-0">
        <div class="tab-content pt-5">
          <div class="tab-pane active show" id="job_description" role="tabpanel">
            <div class="card-header cursor-pointer px-0">
              <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Detail</h3>
              </div>
            </div>
           
            <div class="card-body px-0 py-7">
              
              <div class="row">
			  
	<div class="card" style="margin-top:10px;">
          <div class="card-body">
            <div class="table-responsive">			  		
              <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="currentPackage">
                <thead class="">
                  <tr class="fw-bolder text-muted">
                    <th class="min-w-140px"><h4 class="card-title">Invoice ID</h4></th>
                    <th class="min-w-140px"><h4 class="card-title" style="text-align:center">Payment Mode</h4></th>
                    <th class="min-w-140px"><h4 class="card-title">Amount Paid</h4></th>
                    <th class="min-w-100px"><h4 class="card-title">Download</h4></th>
                  </tr>
                </thead>
                <tbody class="">
                  <tr>
                    <td><?php echo @$packageDetail->invoice_id ? @$packageDetail->invoice_id : '&nbsp;'; ?></td> 
                    <td style="text-align:center;"><?php echo @$packageDetail->payment_mode ? @$packageDetail->payment_mode : '&nbsp;'; ?></td>
                    <td><?php echo @$packageDetail->grand_total ? @$packageDetail->grand_total : '&nbsp;'; ?></td> 
                    <td> <a href="<?php echo url('super-admin/custompackages/invoiceDownload/'.@$packageDetail->payment_mode);?>"><i class="bi bi-download" style="color:#006600; height:35px; width:35px;"></i></a></td>
                  </tr>
                  <tr>
                    <td>Comment</td>
                    <td colspan="2"><textarea name="" cols="80" rows="3" id="comment_<?php echo @$invoice_id; ?>"></textarea></td></td>
                    <td>&nbsp;<button type="button" class="btn btn-success font-weight-bolder text-uppercase px-5 py-2 comment_submit" rel="<?php echo @$invoice_id; ?>">Submit</button></td>
                  </tr>
				  
<?php 
	/*if($package_invoice_comment_count > 0){
		foreach($package_invoice_comment as $invComment){
			echo '<tr>
						<td>Posted Comment</td>
						<td colspan="2">'.$invComment->comment.'</td><td>'.date("d M, Y h:i A", strtotime(@$invComment->created_at)).'</td>
				 </tr>';
		}
	}*/
?>

<tr>                  
	<td colspan="3">
		<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="customInvoiceComment">
			<thead>
					<tr class="fw-bolder text-muted">
						<th class="min-w-200px">SNo.</th>
						<th class="min-w-200px">Comment</th>
						<th class="min-w-200px">Date</th>
					</tr>
			</thead>
		</table>
	</td>                   
</tr>
				  
	







				  				  
                </tbody>
              </table>
            </div>
            <div class="table-responsive">
              <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">  
                <thead class="">
                  <tr class="fw-bolder text-muted">
                    <th class="min-w-200px">Product</th>
                    <th class="min-w-100px">Start Date</th>
                    <th class="min-w-100px">End Date</th>
                    <th class="min-w-100px">Quantity</th>
                    <th class="min-w-100px">Price</th>
                    <th class="min-w-150px">Final Price</th>
                    <th class="min-w-100px">Status</th>
                  </tr>
                </thead>
                <tbody class="">
                  <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">CV Access / Received</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$cvaccess_startdate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$cvaccess_enddate; ?></a></td> 
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$cvaccess_quantity; ?></a></td> 
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$cvaccess_price; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$cvaccess_totalprice; ?></a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3"><?php echo @$cvaccess_status; ?></label></td> 
                  </tr>
				  
                  <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Users</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$users_startdate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$users_enddate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$users_quantity; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$users_price; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$users_totalprice; ?></a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3"><?php echo @$users_status; ?></label></td>
                  </tr>
				  
                  
				 
                  <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">New Bench Client Request</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newbenchclientrequest_startdate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newbenchclientrequest_enddate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newbenchclientrequest_quantity; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newbenchclientrequest_price; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$newbenchclientrequest_totalprice; ?></a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3"><?php echo @$newbenchclientrequest_status; ?></label></td>
                  </tr>
				  
                  <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Emails</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$emails_startdate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$emails_enddate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$emails_quantity; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$emails_price; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$emails_totalprice; ?></a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3"><?php echo @$emails_status; ?></label></td>
                  </tr>
				 				  
                  <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Old CV Access</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$oldcvaccess_startdate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$oldcvaccess_enddate; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$oldcvaccess_quantity; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$oldcvaccess_price; ?></a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6"><?php echo @$oldcvaccess_totalprice; ?></a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3"><?php echo @$oldcvaccess_status; ?></label></td>
                  </tr>

<?php 
	if(@$packageDetail->company_type != 3){
		
		echo '	 <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Contract Recruitment Lead</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_enddate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_price.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newclientrequest_totalprice.'</a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">'.@$newclientrequest_status.'</label></td>
                  </tr>
				  
				  <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Fulltime Recruitment Leads</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_enddate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_price.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$newjobrequest_totalprice.'</a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">'.@$newjobrequest_status.'</label></td>
                  </tr>
				  
				   <tr>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Boost Profile Keywords</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_startdate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_enddate.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_quantity.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_price.'</a></td>
                    <td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_totalprice.'</a></td>
                    <td class=""><label class="badge text-success fw-400 fs-6 bg-light-success badge-sm py-1 px-3">'.@$boostkeywords_status.'</label></td>
                  </tr>
				   
				  <tr>
                    	<td><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">Keywords</a></td>
                    	<td colspan="5"><a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary d-block fs-6">'.@$boostkeywords_text.'</a></td>
                  </tr>
				   
				  ';
}				  
?>				  
				 
		  
                  
				  
				  
				   
                </tbody>
              </table>
			  
			  
  			  
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

	$('body').on('click', '.comment_submit', function(){
	
			var id 			=	$(this).attr('rel');
			var comment 	=	$('#comment_'+id).val();
			
			
			var url       	=	"{{ route('superadmin.custompackages.submitcomment',':id') }}";
			url         	=	url.replace(':id', id);
			var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
			var token       =	"{{ csrf_token() }}";
			$.ajax({
								type		: 'POST',
								url			: url,
								data		: {'_token': token, '_method': 'POST', 'id' : id, 'comment': comment},
								cache		: false,
								beforeSend	: function() {
															//$('#childStatus_'+current_application_id).html('<img src="'+baseurl+'" height="35px" width="100%">');
								},
								success 	: function (response) {
												$('#comment_'+id).val('');
												if (response.status == "success") {
													  Swal.fire("Success", 'Invoice comment submit successfully !', "success")
													  customInvoiceComment._fnDraw();
												}else{
													Swal.fire("Warning", 'Opps something wrong, please try again !', "info");	
													//myPackageHistory._fnDraw();
												}
									}
					});
			
	
	});
</script>





<script>


	 var invoice_id				=	$('#invoice_id').val();
	 var customInvoiceComment 	=	$('#customInvoiceComment').dataTable({
				responsive     		: true, 
      			processing      	: true,
				ordering			: false, //disable ordering       			
      			destroy       		: true,   
      			stateSave     		: true,      
      			"bLengthChange"   	: false,   
				sDom				: 'lrtip',
				//responsive		: true,
            	// processing		: true,				
				searching			: false,
            	serverSide			: true,
            	ajax				: '{!! route('superadmin.custompackages.invoiceComment') !!}?invoice_id='+invoice_id,
            	language			: {
                						//"url": "<?php echo __("app.datatable") ?>" 
            						},
            	"fnDrawCallback"	: function( oSettings ) {
                												$("body").tooltip({
                    																selector: '[data-toggle="tooltip"]'
                													});
            						  },
				
				
				columnDefs: [
            { width: '10%', targets: 0 },
			{ width: '70%', targets: 1 },
			{ width: '20%', targets: 2 }
        ],
        fixedColumns: true,
		
		
						  
            	columns: [
										{ data: 'DT_Row_Index'},
               							{ data: 'Comment', name: 'Comment'},
										{ data: 'Date', name: 'Date'}
            		     ]
			
			
	
	
				 
        });
      new $.fn.dataTable.FixedHeader( customInvoiceComment );

</script>
 
			    