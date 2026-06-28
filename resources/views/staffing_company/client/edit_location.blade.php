@extends('layouts.staffing_company_layout')

@section('content')
<div class="row">
<div class="col-12">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">Edit Location</h4>
      <form class="ajax-form" method="POST" id="createForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="name">Client Name</label>
              {{ $cl_info->client_name }} </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="name">Address</label>
              <input type="text" class="form-control" name="address" id="address" value="{{ $cl_info->address }}" placeholder="Address">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="email">City </label>
              <input type="email" class="form-control" name="city" id="city"  value="{{ $cl_info->city  }}" placeholder="City">
            </div>
          </div>
          <div class="col-md-6 mt-5">
            <div class="form-group">
              <label for="website">Postcode</label>
              <input type="text" class="form-control" name="postcode" id="postcode" value="{{ $cl_info->postcode  }}" placeholder="Postcode">
            </div>
          </div>
          <div class="col-md-6 mt-5">
            <div class="form-group">
              <label for="commission">Country</label>
              <select name="country_id" id="country_id" class="form-control select2 custom-select">
                <?php 
									$option_list ='';
									foreach($countries as $country)
									{	
											$sel 		= '';
											if($cl_info->country_id == $country->id){
												$sel 	= 'selected="selected"'; 
											}
										echo $option 		=  '<option value="'.$country->id.'" '.$sel.'>'.ucfirst($country->country_name).'</option>';
										
							 		} 
							 ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row mt-5">
          <div class="col-12 text-center">
            <button type="button" id="save-form" class="btn btn-success btn-sm"><i class="fa fa-spinner"></i> Update</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });
 </script>
<script>
      	$('#save-form').click(function () {
            $.easyAjax({
                url			: '{{route('staffing_company.clients.update_loc', $cl_info->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file		: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush