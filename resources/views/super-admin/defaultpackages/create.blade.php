@extends('layouts.superadmin_layout') 

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
<style>

@media (min-width: 768px){
  .seven-cols .col-md-1,
  .seven-cols .col-sm-1,
  .seven-cols .col-lg-1  {
    width: 100%;
    *width: 100%;
  }
}

@media (min-width: 992px) {
  .seven-cols .col-md-1,
  .seven-cols .col-sm-1,
  .seven-cols .col-lg-1 {
    width: 14.285714285714285714285714285714%;
    *width: 14.285714285714285714285714285714%;
  }
}
  
@media (min-width: 1200px) {
  .seven-cols .col-md-1,
  .seven-cols .col-sm-1,
  .seven-cols .col-lg-1 {
    width: 14.285714285714285714285714285714%;
    *width: 14.285714285714285714285714285714%;
  }
}

.mb-10 {
    margin-bottom: .5rem !important;
}

</style>

<script>
$(document).ready(function() {
    $("#cvaccess_startdate").datepicker({ startDate:'+0d' });
});

var dateToday = new Date();    
 $(function () {
     $("#cvaccess_startdate").datepicker({ 
         minDate: dateToday 
     });
 });
 
</script>

@endpush

@section('content')

<div class="row" id="createPackage">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Create New Package</h4>
        <!--<form id="editSettings" class="ajax-form">-->
		<form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
			<input type="hidden" name="company_id" id="company_id" value="<?php echo @$company_id; ?>" />
			<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          @csrf
		 
		 
		 
		 
		 
		 
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:40px; font-weight:bold;">CV Access / Received</label>                    
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control form-control-solid" name="cvaccess_startdate" id="cvaccess_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control form-control-solid" name="cvaccess_enddate" id="cvaccess_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  
				  	<div class="col-md-1">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control form-control-solid" name="cvaccess_quantity" id="cvaccess_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control form-control-solid" name="cvaccess_price" id="cvaccess_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <label class="form-label">Final Price</label>
                    <input type="number" class="form-control form-control-solid" name="cvaccess_finalprice" id="cvaccess_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <label class="form-label">Status</label>
					<select name="cvaccess_status" id="cvaccess_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>		
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:10px; font-weight:bold;">Users</label>
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">Start Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="users_startdate" id="users_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">End Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="users_enddate" id="users_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  
				  	<div class="col-md-1">
                   <!-- <label class="form-label">Quantity:</label>-->
                    <input type="number" class="form-control form-control-solid" name="users_quantity" id="users_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                   <!-- <label class="form-label">Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="users_price" id="users_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Final Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="users_finalprice" id="users_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Status:</label>-->
					<select name="users_status" id="users_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>		
		
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:10px; font-weight:bold;">New Client Request</label>
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">Start Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="clientrequest_startdate" id="clientrequest_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                   <!-- <label class="form-label">End Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="clientrequest_enddate" id="clientrequest_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  	<div class="col-md-1">
                    <!--<label class="form-label">Quantity:</label>-->
                    <input type="number" class="form-control form-control-solid" name="clientrequest_quantity" id="clientrequest_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="clientrequest_price" id="clientrequest_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Final Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="clientrequest_finalprice" id="clientrequest_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Status:</label>-->
					<select name="clientrequest_status" id="clientrequest_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:10px; font-weight:bold;">New Job Request</label>
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                   <!-- <label class="form-label">Start Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="jobrequest_startdate" id="jobrequest_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">End Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="jobrequest_enddate" id="jobrequest_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  	<div class="col-md-1">
                    <!--<label class="form-label">Quantity:</label>-->
                    <input type="number" class="form-control form-control-solid" name="jobrequest_quantity" id="jobrequest_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                   <!-- <label class="form-label">Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="jobrequest_price" id="jobrequest_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                   <!-- <label class="form-label">Final Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="jobrequest_finalprice" id="jobrequest_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Status:</label>-->
					<select name="jobrequest_status" id="jobrequest_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>						
		
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:10px; font-weight:bold;">New Bench Client Request</label>
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                   <!-- <label class="form-label">Start Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="benchclient_startdate" id="benchclient_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">End Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="benchclient_enddate" id="benchclient_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  
				  	<div class="col-md-1">
                    <!--<label class="form-label">Quantity:</label>-->
                    <input type="number" class="form-control form-control-solid" name="benchclient_quantity" id="benchclient_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                   <!-- <label class="form-label">Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="benchclient_price" id="benchclient_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                   <!-- <label class="form-label">Final Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="benchclient_finalprice" id="benchclient_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Status:</label>-->
					<select name="benchclient_status" id="benchclient_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>							
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:10px; font-weight:bold;">Emails</label>
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">Start Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="emails_startdate" id="emails_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">End Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="emails_enddate" id="emails_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  
				  	<div class="col-md-1">
                   <!-- <label class="form-label">Quantity:</label>-->
                    <input type="number" class="form-control form-control-solid" name="emails_quantity" id="emails_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="emails_price" id="emails_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Final Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="emails_finalprice" id="emails_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Status:</label>-->
					<select name="emails_status" id="emails_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>
						
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                  <div class="col-md-1">
                    <label class="form-label" style="padding-top:10px; font-weight:bold;">Old CV Access</label>
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                    <!--<label class="form-label">Start Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="oldcvaccess_startdate" id="oldcvaccess_startdate" placeholder="Start Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
                  <div class="col-md-1">
                   <!-- <label class="form-label">End Date:</label>-->
                    <input type="date" class="form-control form-control-solid" name="oldcvaccess_enddate" id="oldcvaccess_enddate" placeholder="End Date" value="" onfocus="this.showPicker()">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
				  
				  	<div class="col-md-1">
                    <!--<label class="form-label">Quantity:</label>-->
                    <input type="number" class="form-control form-control-solid" name="oldcvaccess_quantity" id="oldcvaccess_quantity" placeholder="Quantity" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="oldcvaccess_price" id="oldcvaccess_price" placeholder="&#x20b9;0.00" value="">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Final Price:</label>-->
                    <input type="number" class="form-control form-control-solid" name="oldcvaccess_finalprice" id="oldcvaccess_finalprice" placeholder="&#x20b9;0.00" value="" readonly="" style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>
					<div class="col-md-1">
                    <!--<label class="form-label">Status:</label>-->
					<select name="oldcvaccess_status" id="oldcvaccess_status" class="form-control form-control-solid"> 
						<option value="1" selected="selected" style="background-color:#FFFFFF; color:#000000">Active </option>  
						<option value="0" style="background-color:#FFFFFF; color:#000000">Inactive </option>  
					</select>			
                    <span class="form-text text-muted"></span> <div class="fv-plugins-message-container invalid-feedback"></div></div>	
                </div>			
			</div>
		</div>					
				
				
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">
							<label class="form-label" style="padding-top:10px;">Sub Total:</label>  
						  </div>
				  
							<div class="col-md-1">  
								<input type="number" class="form-control form-control-solid" name="sub_total" id="sub_total" value="0.00" style="background-color:#FFFF99;" readonly="">
							</div>
					
				 <div class="col-md-1">&nbsp;</div>
						
                </div>			
			</div>
		</div>
		
		<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">
							<label class="form-label" style="padding-top:10px;">Discount(%):</label>  
						  </div>
				  
							<div class="col-md-1">  
								<input type="number" class="form-control form-control-solid" name="discount" id="discount" value="" placeholder="0.00" >
							</div>
					
				 <div class="col-md-1">&nbsp;</div>
						
                </div>			
			</div>
		</div>
		
				
		
		
<div class="form-group row fv-row mb-10 fv-plugins-icon-container">
	  <div class="col-lg-6">&nbsp;</div>
	  <div class="col-lg-6">
								<div class="input_fields_wrap">
									<button class="add_field_button search__btn btn btn-sm btn-primary btn-active-light-primary">Add</button>
								</div>		  
	  </div>
 </div> 
 
 <div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">
							<label class="form-label" style="padding-top:10px;">Amount Total Paid:</label>  
						  </div>
							<div class="col-md-1">  
								<input type="number" class="form-control form-control-solid" name="amount_total_paid" id="amount_total_paid" value="0.00" style="background-color: #009900; color:#FFFFFF" readonly="">
							</div>
				 		<div class="col-md-1">&nbsp;</div>	
                </div>			
			</div>
</div>
		 
  		
<div class="mb-5">
              <div class="flex-column">
		 		<div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
						  <div class="col-md-1">&nbsp;</div>
						  <div class="col-md-1">&nbsp;</div>
						 
						 
						  <div class="col-md-1">
						  	<label class="form-label" style="padding-top:10px; font-weight:bold;">Payment Mode:</label> 
						  </div>
				  
							<div class="col-md-1">  
								<input name="payment_mode" type="radio" value="online"  class=""  checked="checked"/> Online
							</div>
							<div class="col-md-1">  
								<input name="payment_mode" type="radio" value="cash"  class="" /> Cash
							</div>
							
					
				 <div class="col-md-1">&nbsp;</div>
						
                </div>			
			</div>
</div>			



<div class="form-row row fv-row">
	<div class="col-12 text-center py-10">
		<button type="button" name="save" id="save-form" value="save" class="btn btn-primary">Submit</button>                   
		<a href="javascript:void(0);" class="btn btn-secondary">Cancel</a> 
	</div>
</div>	
				
												
         
          <!--<button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10"> Submit </button>
          <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>-->
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
 @push('footer-script')
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
<script>
    // Switchery
   /* var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());

    });*/
   /* $('#save-form').click(function () {
        $.ajax({
						url			: '{{route("superadmin.custompackages.createInvoice")}}',
						//"_token": "{{ csrf_token() }}",
						container	: '#editSettings',
						type		: "POST",
						redirect	: true,
						file		: true
        		})
    });*/
</script>


<script>
   $('#save-form').click(function () {
      
   							
   		  var formData 					=	new FormData($('#createForm')[0]);					
          $.ajax({
              		  url				: '{{route('superadmin.custompackages.createInvoice', @$company_id)}}'+'?_token=' + '{{ csrf_token() }}',	
   					  type				: "POST",
   					  data 				: formData,
   					  processData		: false,  				// tell jQuery not to process the data
   					  contentType		: false,  				// tell jQuery not to set contentType
   					  //container		: '#createForm',
   					 
   					  redirect			: true,
   					  file				: true,
   					  //data			: $('#createForm').serialize(),
   					  success			: function (response) {
   					  							//window.location = response.url;
												$('#createPackage').html(response.view);
   					  						},
              			error			: function (response) {
                  										handleFails(response);
              										}
          		})
      });
</script>



<script>

// cvaccess_quantity cvaccess_price 


</script>

<script type="text/javascript">

$(document).ready(function(){
   
   		$('body').on('keyup', '#cvaccess_quantity, #cvaccess_price', function(e){ 
				var cvaccess_quantity 		=	$('#cvaccess_quantity').val();
				var cvaccess_price 			=	$('#cvaccess_price').val();
				var finalvalue 				=	cvaccess_quantity*cvaccess_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#cvaccess_finalprice').val(finalvalue);
				sub_total();
				$( ".labelper" ).trigger( "keyup" );	
				total_amount();			
		});
		$('body').on('keyup', '#users_quantity, #users_price', function(e){ 
				var users_quantity 			=	$('#users_quantity').val();
				var users_price 			=	$('#users_price').val();
				var finalvalue 				=	users_quantity*users_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#users_finalprice').val(finalvalue);	
				sub_total();
				$( ".labelper" ).trigger( "keyup" );	
				total_amount();			
		});
		$('body').on('keyup', '#clientrequest_quantity, #clientrequest_price', function(e){ 
				var clientrequest_quantity 	=	$('#clientrequest_quantity').val();
				var clientrequest_price 	=	$('#clientrequest_price').val();
				var finalvalue 				=	clientrequest_quantity*clientrequest_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#clientrequest_finalprice').val(finalvalue);	
				sub_total();
				$( ".labelper" ).trigger( "keyup" );	
				total_amount();			
		});
		$('body').on('keyup', '#jobrequest_quantity, #jobrequest_price', function(e){ 
				var jobrequest_quantity 	=	$('#jobrequest_quantity').val();
				var jobrequest_price 		=	$('#jobrequest_price').val();
				var finalvalue 				=	jobrequest_quantity*jobrequest_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#jobrequest_finalprice').val(finalvalue);	
				sub_total();
				$( ".labelper" ).trigger( "keyup" );		
				total_amount();		
		});
		$('body').on('keyup', '#benchclient_quantity, #benchclient_price', function(e){ 
				var benchclient_quantity 	=	$('#benchclient_quantity').val();
				var benchclient_price 		=	$('#benchclient_price').val();
				var finalvalue 				=	benchclient_quantity*benchclient_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#benchclient_finalprice').val(finalvalue);	
				sub_total();
				$( ".labelper" ).trigger( "keyup" );		
				total_amount();		
		});
		$('body').on('keyup', '#emails_quantity, #emails_price', function(e){ 
				var emails_quantity 		=	$('#emails_quantity').val();
				var emails_price 			=	$('#emails_price').val();
				var finalvalue 				=	emails_quantity*emails_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#emails_finalprice').val(finalvalue);
				sub_total();
				$( ".labelper" ).trigger( "keyup" );		
				total_amount();			
		});
		$('body').on('keyup', '#oldcvaccess_quantity, #oldcvaccess_price', function(e){ 
				var oldcvaccess_quantity 	=	$('#oldcvaccess_quantity').val();
				var oldcvaccess_price 		=	$('#oldcvaccess_price').val();
				var finalvalue 				=	oldcvaccess_quantity*oldcvaccess_price;
				finalvalue					=	Number(finalvalue).toFixed(2);
				$('#oldcvaccess_finalprice').val(finalvalue);	
				sub_total();
				$( ".labelper" ).trigger( "keyup" );		
				total_amount();		
		});
		
		
		
		
		var sub_total = ()=>{
				
				var 	cvaccess  		=	$('#cvaccess_quantity').val() * $('#cvaccess_price').val();
				var 	users  			=	$('#users_quantity').val() * $('#users_price').val();
				var 	clientrequest  	=	$('#clientrequest_quantity').val() * $('#clientrequest_price').val();
				
				var 	jobrequest  	=	$('#jobrequest_quantity').val() * $('#jobrequest_price').val();
				var 	benchclient  	=	$('#benchclient_quantity').val() * $('#benchclient_price').val();
				var 	emails  		=	$('#emails_quantity').val() * $('#emails_price').val();
				
				var 	oldcvaccess  	=	$('#oldcvaccess_quantity').val() * $('#oldcvaccess_price').val();
				
				var total				=	cvaccess+users+clientrequest+jobrequest+benchclient+emails+oldcvaccess;
				total					=	Number(total).toFixed(2);
				$('#sub_total').val(total);
				total_amount();
		}
		
		$('body').on('keyup', '#discount', function(e){ 
				total_amount();		
		});
		
		
});		

		
</script>

<script>

$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="row" style=" margin:10px;"><input style="width:100px; border-radius: 0.475rem; background-color: #f5f8fa; border-color: #f5f8fa;" type="text" name="label[]" id="label_'+x+'" placeholder="label"/>&nbsp;&nbsp;<input class="labelper" style="width:100px; border-radius: 0.475rem; background-color: #f5f8fa; border-color: #f5f8fa;" type="number" name="per_text[]" id="labelper_'+x+'" rel="'+x+'"/>% &nbsp;&nbsp;<input style="width:100px; border-radius: 0.475rem; background-color:#FFFF99;" type="text" name="per_val[]" id="labelval_'+x+'"/><a href="#" class="remove_field">Remove</a></div>'); //add input box 
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text 
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});



$('body').on('keyup', '.labelper', function(e){ 
		
		var labelper_id				=	$(this).attr('id');	
		var labelper_rel			=	$(this).attr('rel');	
		
		var labelper_value			=	$('#'+labelper_id).val();	
		var sub_total				=	$('#sub_total').val();
		
		var discount  				=	$('#discount').val() ;		
		var discount_amount			=	(sub_total * discount)/100;
		
		sub_total					=	sub_total - discount_amount; 
				
		var per_val					=	0; 
		if(sub_total > 0)
		{
			per_val			=	(sub_total*labelper_value)/100;	
			per_val			=	Number(per_val).toFixed(2);		
		}
		$('#labelval_'+labelper_rel).val(per_val);
		
		total_amount();
});


function total_amount()
{
				//$( ".labelper" ).trigger( "keyup" );	
				
				var 	cvaccess  			=	$('#cvaccess_quantity').val() * $('#cvaccess_price').val();
				var 	users  				=	$('#users_quantity').val() * $('#users_price').val();
				var 	clientrequest  		=	$('#clientrequest_quantity').val() * $('#clientrequest_price').val();
				
				var 	jobrequest  		=	$('#jobrequest_quantity').val() * $('#jobrequest_price').val();
				var 	benchclient  		=	$('#benchclient_quantity').val() * $('#benchclient_price').val();
				var 	emails  			=	$('#emails_quantity').val() * $('#emails_price').val();
				
				var 	oldcvaccess  		=	$('#oldcvaccess_quantity').val() * $('#oldcvaccess_price').val();
				
				var sub_total				=	cvaccess+users+clientrequest+jobrequest+benchclient+emails+oldcvaccess;
				
				// DISCOUNT 
				var 	discount  			=	$('#discount').val() ;
				
				var discount_amount			=	(sub_total * discount)/100; 
				var afterdiscount_amount	=   sub_total-discount_amount;
				
				var cvalues		=	0;
				var inps 	= document.getElementsByName('per_val[]');
				for (var i = 0; i <inps.length; i++) {
							var inp		=	inps[i];
							var loopVal = 	parseFloat(inp.value);
							if(!isNaN(loopVal)){
								cvalues		+=	loopVal;	
							}
    
				}
				
				/*var cvalues		=	0;
				var inps 		= document.getElementsByName('per_text[]');
				for (var i = 0; i <inps.length; i++) {
							var inp		 =	inps[i];
							var loopVal  = 	parseInt(inp.value);
							cvalues		+= (afterdiscount_amount*loopVal)/100;
    						
				}*/
				

				console.log(cvalues);

				afterdiscount_amount	=	afterdiscount_amount + cvalues; 
				
				afterdiscount_amount	=	Number(afterdiscount_amount).toFixed(2);
				
				//$('#amount_total_paid').val(0);	
				$('#amount_total_paid').val(afterdiscount_amount);	

}

</script>

@endpush

