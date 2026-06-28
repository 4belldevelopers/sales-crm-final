@extends('layouts.app')
<?php 
		//echo '<pre>'; print_r($client); exit; 
?>
@section('content')
<style>
.form-group {margin-bottom: .5rem;}
.input-group-append {z-index: 0;}
</style>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row custom-m-top">
          <div class="col-md-6">
            <h4 class="card-title mb-4 text-left">@lang('app.edit')</h4>
          </div>
          <!--<div class="col-md-6">
            <ol class="breadcrumb float-sm-right mt-xs-2 small bg-white border-bottom border-top border-secondary">
              <li class="breadcrumb-item"><a href="{{ $user->is_superadmin?route('superadmin.dashboard.index'): route('admin.dashboard')}}"><i class="fa fa-home"></i></a></li>
              <li class="breadcrumb-item active">{{ $pageTitle }}</li>
            </ol>
          </div>-->
        </div>
        <!-- <h4 class="card-title">@lang('app.edit')</h4> -->
        <form class="ajax-form" method="POST" id="createForm" enctype="multipart/form-data">
          @csrf
          <input name="_method" type="hidden" value="PUT">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Client</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $client->name }}" placeholder="Client name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="website">Website</label>
                <input type="text" class="form-control" name="website" id="website" value="{{ $client->website }}" placeholder="Website Url">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" name="detail" rows="4" placeholder="Enter text ...">{{ $client->detail }}</textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="feedback">Any Other Detail</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Enter text ...">{{ $client->feedback }}</textarea>
              </div>
            </div>
			
			<div class="col-md-6">
              <div class="form-group">
                <label for="commission">Commission(%)</label>
                <input type="number" class="form-control" name="commission" id="commission" value="{{ $client->commission }}" placeholder="Commission(%)" onkeypress="return isNumberKey(event)">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="payout">Payout(Days)</label>
                <input type="number" class="form-control" name="payout" id="payout" placeholder="payout Days" value="{{ $client->payout }}"  maxlength="3" onkeypress="return isNumberKey(event)">
              </div>
            </div>
			
			
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="file">Agreement / NDA</label>
              <input type="file" class="form-control" id="input-file-now" name="image[]" accept=".png,.jpg,.jpeg" class="dropify1"  data-default-file="{{ asset('avatar.png')  }}" multiple  />
              <br />
              <?php 
			
			if(count($client_file) > 0 ){
			
	   ?>
              <div>
                <div style="clear:both; margin:10px;" >
                  <div style="float:left; width:300px; font-weight:bold">filename</div>
                  <div style="float:left; width:200px; font-weight:bold ">Action</div>
                </div>
                <?php 
									$cnt = 1; 
									foreach($client_file as $clientArr)
									{
							?>
                <div id="<?php echo @$clientArr->id;  ?>" style="clear:both; margin:10px;">
                  <div style="float:left; width:300px;"><?php echo @$clientArr->file_name;  ?></div>
                  <div style="float:left; width:150px; "> <a href="{{url('admin/clients/download', $clientArr->id )}}" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp; | &nbsp; <a href="javascript:void();" class="btn btn-danger btn-circle sa-params" data-toggle="tooltip" data-id="<?php echo @$clientArr->id;  ?>" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a> </div>
                </div>
                <?php  $cnt = $cnt+1; } ?>
              </div>
              <?php  } ?>
            </div>
          </div>
          <label for="commission">Contact Person Info </label>
          <?php 
						
						if(count($client_contact_info) > 0 )
						{
							$cnt = 1; 
							foreach($client_contact_info as $contArr)
							{
								echo '<div class="row">
			<div class="col-sm-12 nopadding">
				<div class="form-group">
					<div class="input-group">
						<span style="font-weight:bold">Name</span> :&nbsp;&nbsp;'.@$contArr->contact_person_name.'&nbsp;&nbsp;&nbsp;
						<span style="font-weight:bold">Email</span>:&nbsp;&nbsp; '.@$contArr->contact_person_email.'&nbsp;&nbsp;&nbsp;
						<span style="font-weight:bold">Contact Number</span> :&nbsp;&nbsp;'.@$contArr->contact_person_number.'&nbsp;&nbsp;&nbsp;
						<span style="font-weight:bold">Designation</span> :&nbsp;&nbsp; '.@$contArr->contact_person_designation.'
						
					</div>
				</div>
			</div>
		</div>';
							}
						}
		?>
          <div class="row" >
            <div class="col-sm-12 nopadding">
              <div class="form-group">
                <div class="input-group">
                  <input type="text" name="cp_name[]" class="form-control" placeholder="Contact Name">
                  &nbsp;
                  <input type="text" name="cp_email[]" class="form-control" placeholder="Contact Email">
                  &nbsp;
                  <input type="text" name="cp_number[]" class="form-control" placeholder="Contact Number">
                  &nbsp;
                  <input type="text" name="cp_designation[]" class="form-control" placeholder="Designation">
                  <div class="input-group-append">
                    <button class="btn btn-light-success" type="button" id="add-more"><i class="fa fa-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="education_fields"></div>
          <label for="commission">Office Address </label>
          <?php 
						
						if(count($client_location) > 0 )
						{
							$cnt = 1; 
							foreach($client_location as $locArr)
							{
								echo '<div class="row">
			<div class="col-sm-12 nopadding">
				<div class="form-group">
					<div class="input-group">
						<span style="font-weight:bold">Country</span> :&nbsp;&nbsp;'.@$locArr->country_name.'&nbsp;&nbsp;&nbsp;
						<span style="font-weight:bold">City</span>:&nbsp;&nbsp; '.@$locArr->city.'&nbsp;&nbsp;&nbsp;
						<span style="font-weight:bold">Office Address</span> :&nbsp;&nbsp;'.@$locArr->address.'&nbsp;&nbsp;&nbsp;
						<span style="font-weight:bold">Postcode</span> :&nbsp;&nbsp; '.@$locArr->postcode.'
						
					</div>
				</div>
			</div>
		</div>';
							}
						}
		?>
          <div class="row">
            <div class="col-sm-12 nopadding">
              <div class="form-group">
                <div class="input-group">
                  <select name="country_id[]" id="country_id[]" class="form-control select2 custom-select">
                    <?php 
									$option_list ='';
									foreach($countries as $country)
									{	
										echo $option 		=  '<option value="'.$country->id.'">'.ucfirst($country->country_name).'</option>';
										$option_list 	.= $option;
							 		} 
							 ?>
                  </select>
                  <input type="text" name="city[]" class="form-control" placeholder="City">
                  &nbsp;
                  <input type="text" name="address[]" class="form-control" placeholder="Office address">
                  &nbsp;
                  <input type="text" name="postcode[]" class="form-control" placeholder="Postcode">
                  <div class="input-group-append">
                    <button class="btn btn-light-success" type="button" id="add-more-location"><i
										class="fa fa-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="location_fields"></div>
          <div class="row mb-20 mt-3">
            <div class="col-12 text-center">
              <button type="button" id="save-form" class="btn btn-light-success"><i class="fa fa-spinner"></i> Update</button>
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
		$( document ).ready(function() {
		  
		  
		    $('body').on('click', '.sa-params', function(){
            var id  = $(this).attr("data-id");
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.clients.deleterecord',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'GET',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE', 'id': id},
                        success: function (response) {
								$( "#"+id ).remove();
                            if (response.status == "success") {
                                $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });
		
			
		  
				 
		});




       // For select 2
        $(".select2").select2();
        var room = 1;
	
		$('#add-more-location').click(function () {

			var option 			=	$('#hid_option').val();
			//alert(option); 
            room++;
            var objTo 			= document.getElementById('location_fields')
            var divtest 		= document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv 			= 'removeclass' + room;
            divtest.innerHTML 	= '<div class="row"><div class="col-sm-12 nopadding"><div class="form-group"><div class="input-group"><select name="country_id[]" id="country_id" class="form-control select2 custom-select">'+option+'</select><input type="text" name="city[]" class="form-control" placeholder="City">&nbsp;<input type="text" name="address[]" class="form-control" placeholder="Office address">&nbsp;<input type="text" name="postcode[]" class="form-control" placeholder="postcode"><div class="input-group-append"><button class="btn btn-danger" type="button" onClick="remove_location_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';

            objTo.appendChild(divtest)
        })
		
		 function remove_location_fields(rid) {
            $('.removeclass' + rid).remove();
        }
		
		
        $('#add-more').click(function () {

            room++;
            var objTo 			= document.getElementById('education_fields')
            var divtest 		= document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv 			= 'removeclass' + room;
            divtest.innerHTML 	= '<div class="row"><div class="col-sm-12 nopadding"><div class="form-group"><div class="input-group"><input type="text" name="cp_name[]" class="form-control" placeholder="Contact Name">&nbsp;<input type="text" name="cp_email[]" class="form-control" placeholder="Contact Email">&nbsp;<input type="text" name="cp_number[]" class="form-control" placeholder="Contact Number">&nbsp;<input type="text" name="cp_designation[]" class="form-control" placeholder="Designation"><div class="input-group-append"><button class="btn btn-danger" type="button" onClick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';

            objTo.appendChild(divtest)
        })

        function remove_education_fields(rid) {
            $('.removeclass' + rid).remove();
        }
		
		 
		
        $('#save-form').click(function () {
            $.easyAjax({
                url			: '{{route('admin.clients.update', $client->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file		: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush