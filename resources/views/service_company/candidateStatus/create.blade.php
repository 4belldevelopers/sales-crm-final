@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<style>
   .form-group{margin-bottom: .3rem;}
   .content-header{display: none;}
   select.form-control:not([size]):not([multiple])
   {
   padding: 0.6em .3em;
   margin-top: 0em;
   margin-bottom: 0em;
   }
   .input-group-append, .input-group-prepend
   {
   display: initial;
   }
   .form-group label, label
   {
    font-weight: 600!important;
    text-transform: capitalize;
    font-size: 13px;
    padding-top: 0em!important;
    line-height: 35px;
    padding-bottom: 0em!important;
    margin-bottom: .5em;
   }
   select.form-control:not([size]):not([multiple])
   {
    border-radius: 0rem;
   }
</style>
@endpush
@section('content')
<style>
   .message{ color:#006600; margin:10px; text-align:center; font-weight:bold}
   .error{ color:#FF0000}
   .form-control{border-radius:0px;}
</style>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 text-left">
            <h4 class="card-title pt-2 pb-2"><i class="fa fa-plus-square"></i>&nbsp;Add Interview Process</h4>
          </div>
          <div class="col-md-6">
            <ol class="breadcrumb float-sm-right mt-xs-2 small bg-white border-bottom border-top border-secondary">
              <li class="breadcrumb-item"><a href="{{ $user->is_superadmin?route('superadmin.dashboard.index'): route('service_company.dashboard')}}"><i class="fa fa-home"></i></a></li>
              <li class="breadcrumb-item active">{{ $pageTitle }}</li>
            </ol>
          </div>
        </div>
        <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-6"> @csrf
              <?php 
                  $room	=	2;
                  if(!empty($master_status))
                  {
                  foreach($master_status as $mval){
                  ?>
              <label for="cp_name" style="width:100%;background:#e9ecef;"> <i class="fa fa-users fa-sm text-dark mr-2 ml-2"></i> <span><?php echo $mval->name; ?></span>
              <button class="btn btn-light-success btn-sm" type="button" id="<?php echo $mval->name.'-add-more'?>" style="float:right;"> <i class="fa fa-plus"></i> </button>
              </label>
              <?php 
                  $parent		=	$childArr 	= '';
                  $parent 	=	$mval->id;  
                  $childArr 	= DB::table('candidate_status')
                  					->select('*')
                  					->where('parent', $parent)
                  					->where('company_id', user()->company_id)
                  					->orderBy('candidate_status.priority', 'ASC')
                  					->get();
                  if(!empty($childArr)){
                  	foreach($childArr as $cval){
                  ?>
              <div class="form-group removeclass<?php echo @$room; ?>">
                <div class="row">
                  <div class="col-sm-12 nopadding mb-1">
                    <div class="form-group">
                      <?php 
                              if($cval->delete_status == 0){
                              ?>
                      <div class="input-group">
                        <input type="text" name="" class="form-control" placeholder="" class="cp_name" value="<?php echo $cval->name;?>" readonly="">
                        <!-- style="background-color:#FFFFCC" -->
                        <div class="input-group-append"> &nbsp; </div>
                      </div>
                      <?php 
                              }else{
                              ?>
                      <div class="input-group">
                        <input type="text" name="<?php echo $mval->name.'[]' ?>" class="form-control cp_name" placeholder=""  value="<?php echo $cval->name?>">
                        &nbsp;
                        <select name="<?php echo $mval->name.'_priority[]'?>" class="form-control">
                          <option value="">Select Priority</option>
                          <?php 
                                    for($p=0; $p<=10; $p++){
                                    	$sel = '';
                                    	if($cval->priority == $p){
                                    		$sel = 'selected="selected"';	
                                    	}
                                    	echo '<option value="'.$p.'" '.$sel.'>'.$p.'</option>';
                                    }
                                    ?>
                        </select>
                        <div class="input-group-append">
                          <button class="btn btn-light-danger btn-sm pb-2" type="button" onClick="remove_education_fields_update(<?php echo $room; ?>, <?php echo $cval->id; ?>);"> <i class="fa fa-minus"></i> </button>
                        </div>
                      </div>
                      <?php 
                              $room	=	$room +1 ;
                              }
                              ?>
                    </div>
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
              <input type="hidden" name="<?php echo $mval->name.'hidden[]' ?>" value="<?php echo $cval->id?>" />
              <?php 
                  }
                  }
                  ?>
              <div id="<?php echo $mval->name.'_fields'; ?>"></div>
              <?php 
                  } }								
                  ?>
            </div>
          </div>
          <div class="row py-4">
            <div class="col-12 text-center">
              <button type="button" id="save-form" class="btn btn-light-success btn-sm"> <i class="fa fa-save fa-sm"></i>&nbsp;@lang('app.save')&nbsp;&&nbsp;Submit </button>
            </div>
          </div>
        </form>
        <textarea name="hid_option" id="hid_option" cols="5" rows="5" style="display:none"><?php echo  @$option_list; ?></textarea>
      </div>
    </div>
  </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
   $(document).ready(function() {
   
   $("#createForm").validate({
   	
       rules: {
   						name : {
   													required: true,
   													minlength: 3
   						},
   						/*email:{
   													required: true,
   													email: true
   						},*/
   						commission:{
   													required: true,
   													number: true,
   													minlength: 1,
   													maxlength: 6
   						},
   						
   						payout:{
   													required: true,
   													number: true,
   													minlength: 1,
   													maxlength: 3
   						},
   						
   						
   						'cp_email[]' : {
   													required: true,
   													email: true
   						},
   						'cp_number[]':{
   													required: true,
   													number: true,
   													minlength: 8,
   													maxlength: 14
   						},
   						'cp_designation[]' : {
   													required: true
   						},
   						
   						
   						'country_id[]' : {
   													required: true
   						},
   						'city[]' : {
   													required: true
   						},
   						'address[]' : {
   													required: true
   						}
   						
   						
   						
   						
   						
       	},
       messages : {
   						name: {
   													required: "Please enter Name",
   													minlength: "Name should be at least 3 characters"
   						},
   						/*email: {
   													required: "Please enter Email "
   						},*/
   						commission: {
   													required: "Please enter commission"
   						},
   						payout: {
   													required: "Please enter payout days"
   						},
   						
   						
   						
   						'cp_email[]': {
   													required: "Please enter email"
   						},
   						'cp_number[]': {
   													required: "Please enter number"
   						},
   						'cp_designation[]': {
   													required: "Please enter Designation"
   						},
   						
   						
   						'country_id[]': {
   													required: "Please enter Country"
   						},
   						'city[]': {
   													required: "Please enter City"
   						},
   						'address[]': {
   													required: "Please enter City"
   						}
   						
   						
       		}
     });
     
     
   });
   
   
   $('.dropify').dropify({
               messages: {
                   default: '@lang("app.dragDrop")',
                   replace: '@lang("app.dragDropReplace")',
                   remove: '@lang("app.remove")',
                   error: '@lang('app.largeFile')'
               }
           });
    
   function isNumberKey(evt){
       var charCode = (evt.which) ? evt.which : evt.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
           return false;
       return true; }
   
   		
   			
           $(".select2").select2();
           var room = <?php echo @$room; ?>;
   		
   		
   		
   		
   <?php 
      if(!empty($master_status))
      {
      	foreach($master_status as $mval){
      		
      		$parent		=	$childArr 	= '';
      		$parent 	=	$mval->id;
      		
      ?>
   		
           $("#"+"<?php echo $mval->name.'-add-more'?>").click(function () {
   
               room++;
               var objTo 			= document.getElementById("<?php echo $mval->name.'_fields'; ?>")
               var divtest 		= document.createElement("div");
               divtest.setAttribute("class", "form-group removeclass" + room);
               var rdiv 			= 'removeclass' + room;
               divtest.innerHTML 	= '<div class="row"><div class="col-sm-12 nopadding mb-1"><div class="form-group"><div class="input-group"><input type="text" name="<?php echo $mval->name?>[]" class="form-control" placeholder="" class="cp_name">&nbsp;<select name="<?php echo $mval->name.'_priority[]'?>" class="form-control"><option value="">Select Priority</option><?php for($p=0; $p<=10; $p++){echo '<option value="'.$p.'">'.$p.'</option>';}?></select><div class="input-group-append"><button class="btn btn-light-danger btn-sm pb-2" type="button" onClick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';
   
               objTo.appendChild(divtest)
           })
   
           function remove_education_fields(rid) {
               $('.removeclass' + rid).remove();
           }
   		
   <?php 
      }
      }
      
      
      
      ?>
   		
   		function remove_education_fields_update(rid, id)
   		{
   					var url 			= "<?php echo url('service_company/candidateStatus/delete') ?>";
                       var token 			= "{{ csrf_token() }}";
   					 $.easyAjax({
                           				type: 'POST',
                           				url: url,
                           				data: {'_token': token, '_method': 'POST', 'id' : id},
                           				success: function (response) {
   											$('.removeclass' + rid).remove();
   										}
                       });
   		}
   
   
           $('#save-form').click(function () 
   		{
   		
   				/*$('.cp_name').each(function() {
   					$(this).rules("add", 
   					{
   							required: true,
   							messages: {
   										required: "Name is required",
   									}
   					});
   				});
   	
   			  if( $('#createForm').valid() == false)
   				{
   					return false;
   				}*/
   
               $.easyAjax({
                   url			: '{{route('service_company.candidateStatus.store')}}',
                   container	: '#createForm',
                   type		: "POST",
                   redirect	: true,
   				file		: true
                  
               })
           });
       
</script>
@endpush