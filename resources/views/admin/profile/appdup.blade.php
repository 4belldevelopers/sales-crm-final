@extends('layouts.app')
<head>
  <title>Admin Panel | Application Duplicate Setting</title>
</head>

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<style>
.content-header{display: none;}
#btnFiterSubmitSearch
{
    padding:0.5rem 0.20rem!important;
}
.bootstrap-select>.dropdown-toggle
{
    background-color: #efefef;
    border-color: #e8e8e8;
    color: #404040;
    outline: none;
    border: none;
}
.btn-info:not(:disabled):not(.disabled).active,
.btn-info:not(:disabled):not(.disabled):active,
.show > .btn-info.dropdown-toggle
{
    background-color: #efefef;
    border-color: #e8e8e8;
    color: #000;
    outline: none;
    border: none;
}
.bootstrap-select .dropdown-toggle .filter-option:focus
{
    outline: none;
    border: none;
}

/*New CSS*/
.form-control
{
  display: block;
  width: 100%;
  padding: .2em .2rem;
}    

.w-20
{
  width: 20%;
}
.w-80
{
  width: 80%;
}

/*New CSS End*/
</style>
@endpush
 
@section('content')
<?php 
//echo '<pre>'; print_r($user->company->app_duplicate); exit; 
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2 pb-2"><i class="fa fa-cogs"></i>&nbsp;Application Duplicate Setting</h4>
        <div id="verify-mobile"> @include('sections.admin_verify_phone') </div>
        <form class="ajax-form" method="POST" id="createForm" name="createForm">
          @csrf
          <!-- NONE -->
            <div class="row display-flex mx-0 align-items-center mb-2">
                <div class="form-group w-20">
                  <!-- <label class="custom-label-color mb-0 pl-1">Duplicate Application</label> -->
                      <!-- name="app_duplicate" -->                      
                      <input id="None" class="radio-button" name="app_duplicate" type="radio"  value="0" 
                      <?php  if($user->company->app_duplicate == 0){ echo 'checked="checked"' ; }?>>
                      <label for="overall_system" class="pr-2">None</label>
                </div> 
                <div class="form-group w-80" id="div_app_duplicate_days">
                  <!-- <label for="email">Duplicate Application Days Limit</label>&nbsp; -->                  
                  <!-- value="<?php echo @$user->company->app_duplicate_days; ?>" -->
                  <input class="form-control" type="number" name="app_duplicate_days" id="app_duplicate_days" value="" placeholder="No Value" width="250px" style="width:230px;" disabled="disabled">
                </div> 
            </div>
            <!-- NONE END -->

            <!-- OVERROLL SYSTEM -->
            <div class="row display-flex mx-0 align-items-center mb-2">
                <div class="form-group w-20">
                  <!-- <label class="custom-label-color mb-0 pl-1">Duplicate Application</label> -->
                      <!-- name="app_duplicate" -->
                      <input id="Overall_System" class="radio-button" name="app_duplicate" type="radio"  value="2"
                        <?php  if($user->company->app_duplicate == 2){ echo 'checked="checked"' ; }?>/>
                      <label for="overall_system" class="pr-2">Overroll System&nbsp;(No of Days)</label>
                </div> 
                <div class="form-group w-80" id="div_app_duplicate_days">
                  <!-- <label for="email">Duplicate Application Days Limit</label>&nbsp; -->                  
                  <!-- value="<?php echo @$user->company->app_duplicate_days; ?>" -->
                  <input class="form-control" type="number" name="app_duplicate_days" id="app_duplicate_days" value="" placeholder="Duplicate Application Days Limit" width="250px" style="width:230px;">
                </div> 
            </div>
            <!-- OVERROLL SYSTEM END -->

            <!-- REQUISITION JOBWISE -->
            <div class="row display-flex mx-0 align-items-center mb-2">
                <div class="form-group w-20">
                  <!-- <label class="custom-label-color mb-0 pl-1">Duplicate Application</label> -->
                      <!-- name="app_duplicate" -->
                      <input id="Overall_System" class="radio-button" name="app_duplicate" type="radio"  value="2"
                        <?php  if($user->company->app_duplicate == 2){ echo 'checked="checked"' ; }?>/>
                      <label for="overall_system" class="pr-2">Requisition Jobwise&nbsp;(No of Days)</label>
                </div> 
                <div class="form-group w-80" id="div_app_duplicate_days">
                  <!-- <label for="email">Duplicate Application Days Limit</label>&nbsp; -->                  
                  <!-- value="<?php echo @$user->company->app_duplicate_days; ?>" -->
                  <input class="form-control" type="number" name="app_duplicate_days" id="app_duplicate_days" value="" placeholder="Duplicate Application Days Limit" width="250px" style="width:230px;">
                </div>
            </div>   
            <!-- REQUISITION JOBWISE END -->
          <div class="row">
            <div class="col-12 text-center pt-4">
              <button type="button" id="save-form" class="btn btn-light-primary btn-sm">
                <i class="fa fa-save"></i> @lang('app.submit')</button>
              <button type="reset" class="btn btn-light-warning btn-sm"> <i class="fa fa-spinner"></i> @lang('app.reset') </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php 
		//echo '<pre>'; print_r($user->company); 
?>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
	$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
	});

	$(document).ready(function(){

      $("input[name='app_duplicate']").on("click", function() {
			if($(this).val() == 0)
			{
				$('#app_duplicate_days').val(0);
				$('#div_app_duplicate_days').hide();
			}
			else
			{
				$('#div_app_duplicate_days').show();
			}
        });
	});     

        
		
		$('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.profile.appdupupdate')}}',
            container: '#createForm',
            type: "POST",
            redirect: true,
            data: $('#createForm').serialize()
        })
    });
    </script>
@endpush