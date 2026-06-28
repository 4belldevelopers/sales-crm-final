<?php 
   use App\Helper\GeneralMethod;
?>
<?php 

		//echo '<pre>'; print_r($company);
?>
<div class="row" id="createPackage">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Invoice</h4>
        <!--<form id="editSettings" class="ajax-form">-->
		<form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
			<input type="hidden" name="company_id" id="company_id" value="<?php echo @$company->id; ?>" />
			<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          @csrf
		 
		 































<style>

#invoice{	padding: 30px;	}
.invoice { position: relative; background-color: #FFF; min-height: 680px; padding: 15px }
.invoice header { padding: 10px 0; margin-bottom: 20px; border-bottom: 1px solid #3989c6 }
.invoice .company-details { text-align: right }
.invoice .company-details .name { margin-top: 0; margin-bottom: 0 }
.invoice .contacts { margin-bottom: 20px }
.invoice .invoice-to { text-align: left }
.invoice .invoice-to .to { margin-top: 0; margin-bottom: 0 }
.invoice .invoice-details { text-align: right }
.invoice .invoice-details .invoice-id { margin-top: 0; color: #3989c6 }
.invoice main { padding-bottom: 50px}
.invoice main .thanks { margin-top: -100px; font-size: 2em; margin-bottom: 50px }
.invoice main .notices { padding-left: 6px; border-left: 6px solid #3989c6 }
.invoice main .notices .notice { font-size: 1.2em }
.invoice table { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px }
.invoice table td,.invoice table th { padding: 15px; background: #eee; border-bottom: 1px solid #fff }
.invoice table th { white-space: nowrap; font-weight: 400; font-size: 16px}
.invoice table td h3 { margin: 0; font-weight: 400; color: #3989c6; font-size: 1.2em }
.invoice table .qty,.invoice table .total,.invoice table .unit { text-align: right; font-size: 1.2em }
.invoice table .no { color: #fff; font-size: 1.6em; background: #3989c6 }
.invoice table .unit { background: #ddd }
.invoice table .total { background: #3989c6; color: #fff}
.invoice table tbody tr:last-child td { border: none }
.invoice table tfoot td { background: 0 0; border-bottom: none; white-space: nowrap; text-align: right; padding: 10px 20px; font-size: 1.2em; border-top: 1px solid #aaa}
.invoice table tfoot tr:first-child td { border-top: none }
.invoice table tfoot tr:last-child td { color: #3989c6; font-size: 1.4em; border-top: 1px solid #3989c6 }
.invoice table tfoot tr td:first-child { border: none }
.invoice footer { width: 100%; text-align: center; color: #777; border-top: 1px solid #aaa; padding: 8px 0 }
@media print {
.invoice {font-size: 11px!important; overflow: hidden!important }
.invoice footer { position: absolute; bottom: 10px; page-break-after: always}
.invoice>div:last-child { page-break-before: always}
}

</style>
<script>
 $('#printInvoice').click(function(){
            Popup($('.invoice')[0].outerHTML);
            function Popup(data) 
            {
                window.print();
                return true;
            }
        });
</script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!--Author      : @arboshiki-->
<div id="invoice">

    <div class="toolbar hidden-print">
        <!--<div class="text-right">
            <button id="printInvoice" class="btn btn-info"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>
        </div>-->
        <hr>
    </div>
	
<?php 
		$logo_path			=  url('public/user-uploads/company-logo/'.@$company->logo);
		$company_type		=	''; 
		$company_type_arr	=	GeneralMethod::accountCompanyType(); 
		if($company->company_type== 1){
			$company_type	=	$company_type_arr[1]; 
		}else if($company->company_type== 2){
			$company_type	=	$company_type_arr[2]; 
		}else{
			$company_type	=	$company_type_arr[3];  
		}
?>	
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col">
                        <a href="javascript:void(0);">
                            &nbsp;
                            </a>
                    </div>
                    <div class="col company-details">
                        <h2 class="name">
                            <a target="_blank" href="https://lobianijs.com">
                            &nbsp;
                            </a>
                        </h2>
                        <div>&nbsp;</div>
                        <div>&nbsp;</div>
                        <div>&nbsp;</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">INVOICE TO:</div>
                        <h2 class="to"><img src="<?php echo @$logo_path; ?>" style="height:40px; width:40px;" />&nbsp;<?php echo @$company->company_name; ?></h2>
                        <div class="address"><?php echo @$company_city_name; ?>, <?php echo @$company_country_name; ?></div> 
                        <div class="email"><a href="<?php echo @$company->company_email; ?>"><?php echo @$company->company_email; ?></a></div>
                    </div>
                    <div class="col invoice-details">
                        <h1 class="invoice-id">INVOICE </h1>
                        <div class="date">Date of Invoice: <strong><?php echo $today = date("F j, Y, g:i a");  ?></strong></div>
                        <div class="date">Company Type : <strong><?php echo $company_type;  ?></strong> </div>
                    </div>
                </div>
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-left">DESCRIPTION</th>
							<th class="text-left">START DATE</th>
							<th class="text-left">END DATE</th>
							<th class="text-left">STATUS</th>
                            <th class="text-right">QUANTITY</th>  
                            <th class="text-right">PRICE</th>
                            <th class="text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>

<?php 
$i					=	1;
$finalprice			=	0;
$discountPercentage	=	0;
$discountValue		=	0;
$amount_total_paid	=	0;
$payment_mode		=	'';
$tax_html			=	'';
foreach($service as $ser)
{

	
	if($ser['item'] == 'TAX'){
		
			$inc	=	0;
			foreach($ser['label'] as $val){
				$tax_html .= '<tr>
                            <td colspan="2"></td>
                            <td colspan="2">'.$val.' '.$ser['per_text'][$inc].'%</td>
                            <td>&#x20b9;'.$ser['per_val'][$inc].'</td>
                        </tr>';	
				$inc++;		
			}
		
		
	}elseif($ser['item'] == 'payment_mode'){
		$payment_mode	=	$ser['payment_mode'];	
	}else if($ser['item'] == 'amount_total_paid'){
		$amount_total_paid	=	$ser['amount_total_paid'];	
	}else if($ser['item'] == 'discount'){
		$discountPercentage	=	$ser['discount'];	
	}else{
	
				$status 	= 'Inactive';
				$status 	= @$ser['status']==1?'Active':'Inactive';
				
				echo '<tr>
				<td class="no">0'.$i.'</td>
				<td class="text-left"><h3><a href="javascript:void();">'.@$ser['item'].'</a></h3></td>
				<td class="unit">'.@$ser['startdate'].'</td>
				<td class="unit">'.@$ser['enddate'].'</td>
				<td class="unit">'.@$status.'</td>
				<td class="unit">'.@$ser['quantity'].'</td>
				<td class="qty">&#x20b9;'.@$ser['price'].'</td>
				<td class="total">&#x20b9;'.@$ser['finalprice'].'</td>  
				</tr>';	
				$finalprice	=	$finalprice + @$ser['finalprice'];
	}


$i++;
}


if($discountPercentage > 0 && $finalprice > 0){
	
	$discountValue		=	($finalprice*$discountPercentage)/100;
}


?>	

  
							
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">SUBTOTAL</td>
                            <td>&#x20b9;<?php echo @$finalprice; ?></td>
                        </tr>
						 <tr>
                            <td colspan="2"></td>
                            <td colspan="2">DISCOUNT <?php echo @$discountPercentage; ?>%</td> 
                            <td>&#x20b9;<?php echo @$discountValue; ?></td> 
                        </tr>
                        
						
						<?php echo @$tax_html; ?>
						 <tr>
                            <td colspan="2"></td>
                            <td colspan="2">PAYMENT MODE</td>
                            <td><?php echo @$payment_mode; ?></td>
                        </tr>
						
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">GRAND TOTAL</td>
                            <td>&#x20b9;<?php echo @$amount_total_paid; ?></td>
                        </tr>
                    </tfoot>
                </table>
               <!-- <div class="thanks">Thank you!</div>
                <div class="notices">
                    <div>NOTICE:</div>
                    <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
                </div>-->
				
				
				
<div class="form-row row fv-row">
	<div class="col-12 text-center py-10">
		<button type="button" name="save" id="save-invoice-form" value="save" class="btn btn-primary">Submit</button>                   
		<a href="javascript:void(0);" class="btn btn-secondary">Cancel</a> 
	</div>
</div>	
				
            </main>
            <footer>
                Invoice was created on a computer and is valid without the signature and seal.
            </footer>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>



<?php 
		//echo '<pre>'; print_r($service);
?>
























		
		
        </form>
      </div>
    </div>
  </div>
</div>		 


<script>

$('body').on('click', '#save-invoice-form', function ()
   	{
   		
   			var company_id    					=	<?php echo @$company->id; ?>
			
			var serviceArr 						= <?php echo json_encode($service); ?>;
			//var recommendation_relation_id  =	$(this).data('row-recommendation_relation_id');
			//var status    					=	$(this).data('row-status');
		
		
			//alert(company_id); 
		
			var token       = "{{ csrf_token() }}";
   			var url   						=	"{{ route('superadmin.custompackages.saveinvoice',':company_id') }}";
   			url     						=	url.replace(':company_id', company_id);	
			var baseurl    					=	"{{ asset('assets/images/Loading2.gif') }}"; 
   		
		
													const swalWithBootstrapButtons = Swal.mixin({
													customClass: {
																		confirmButton	: 'btn btn-success',
																		cancelButton	: 'btn btn-danger'
																	},
																		buttonsStyling	: false
													})
													swalWithBootstrapButtons.fire({
																					  title				: 'Are you sure?',
																					  text				: "You won't be able to revert this!",
																					  icon				: 'warning',
																					  showCancelButton	: true,
																					  confirmButtonText	: 'Yes, Ignore it!',
																					  cancelButtonText	: 'No, cancel!',
																					  reverseButtons	: true
																				}).then((result) => {
																				
													  if (result.isConfirmed) 
														{
															
														
													  
													  
																$.ajax({
																			type  : 'POST',
																			url   : url,
																			data: {'company_id': company_id, '_token': token, 'serviceArr': serviceArr},
																			 beforeSend: function() {
																						// $('#kt_activitiesJob_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
																			},
																			success : function (response) 
																			{
																				if(response.status == 'success') 
																				{
																						swalWithBootstrapButtons.fire
																						(
																								'Success!',
																								'Invoice created successfully.',
																								'success'
																						)
																						https://bot.4bell.com/super-admin/company/260
																						window.location = response.url;
																				}
																				else
																				{
																						swalWithBootstrapButtons.fire
																						(
																								'Fail!',
																								'Opps, Something wrong please try again.',
																								'error'
																						)	
																				}
																				//$('#span_allRequisition').html(response.dataCnt.allRequisition);
																				//$('#span_clientRequisition').html(response.dataCnt.clientRequisition);
																				//$('#span_customRequisition').html(response.dataCnt.customRequisition);
																				//$('#span_jobRequest').html(response.dataCnt.jobRequest);
																				
																						
																							//if(response.status == 'success') 
																							//{
																																/*table._fnDraw();
																																tableLoad('load');	
																																if(status == 'success')
																																{
																																	swalWithBootstrapButtons.fire
																																	(
																																			'Accept!',
																																			'Selected request has been accepted successfully.',
																																			'success'
																																	)	
																																}
																																else if(status == 'ignore')
																																{
																																	swalWithBootstrapButtons.fire
																																	(
																																			'Ignore!',
																																			'Selected request has been ignored successfully.',
																																			'success'
																																	)
																																}			*/		
																																
																																
																							// }
																					
																					
																			}
																	});
													  
										  
													  
														} 
														else if (result.dismiss === Swal.DismissReason.cancel) 
														{
															swalWithBootstrapButtons.fire(
																							'Cancelled',
																							'Invoice not create :)',
																							'error'
																						 )
														}
													
													})
		
		
		
   		
   	}); 
	
	
</script>