@extends('layouts.app')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
<style>  
.content-header
{
  display: none;
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
</style>
@endpush
@section('content')
<style>
.error{ color:#FF0000}
</style>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
		
		<?php 
				//echo '<pre>'; 
				//print_r($user->email);
		?>
        <form class="ajax-form" method="POST" id="createForm" enctype="multipart/form-data">
          @csrf
          <div class="row">
            
           <div class="col-md-12">
              <div class="form-group">
                <h4 class="card-title pt-2 pb-2">
                  <i class="fa fa-edit"></i>&nbsp;@lang('app.edit') Vendor
                </h4>               
              </div>
            </div>
      
           <div class="col-md-12">
              <div class="form-group">
                <h4 for="email" class="border-bottom p-2 bg-light-secondary">
                  <i class="fa fa-sign-in"></i>&nbsp;Login Detail
                </h4>        
              </div>
            </div>

            <div class="col-12">
              <div class="row">  
    			     <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">Email </label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="email" value="{{ @$user->email }}" readonly="" maxlength="254" inputmode="email" autocomplete="email" autocapitalize="none" spellcheck="false" style="background-color:#f2faff">
                  </div>
                </div>

    			     <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">Password </label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Your Current Password">
                  </div>
                </div>
              </div> 
            </div>
      
           <div class="col-md-12">
              <div class="form-group">
                <h4 for="email" class="border-bottom mt-3 p-2 bg-light-secondary">
                  <i class="fa fa-building"></i>&nbsp;Business Detail
                </h4>        
              </div>
            </div>   
			
            <div class="col-12 pt-1">
              <div class="row">      
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name">Consultancy Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Consultancy name" value="{{ @$user->name }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                      <div class="form-row">
                          <div class="col-sm-4">
                            <label>Country Code</label>
                              <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" data-width="100%" >
                                  @foreach ($calling_codes as $code => $value)
                                      <option @if($value['dial_code'] == $user->calling_code ) selected @endif  value="{{ $value['dial_code'] }}">{{ $value['dial_code'] . ' - ' . $value['name'] }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                          <div class="col-sm-8">
                            <label>@lang('app.mobile')</label>
                              <input type="number" class="form-control" name="mobile" value="{{ @$user->mobile }}">
                          </div>
                      </div>
                  </div>
                </div>    
              </div>   
            </div>            
			
           <div class="col-md-12 pt-2">
              <div class="row">         
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contact_person_name">Contact Person name </label>
                    <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" placeholder="Consultancy contact name" value="{{ @$consultancy->contact_person_name }}">
                  </div>
                </div>      
               <div class="col-md-6">
                  <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" name="status" id="status">
                       <option @if($user->status=="active") selected @endif  value="active">Active</option>
                       <option @if($user->status=="inactive") selected @endif  value="inactive">Inactive</option>
                    </select>
                  </div>
              </div>  
              </div>  
			     </div>

            <div class="col-12 pt-2"> <div class="row"> <div class="col-md-6">
            <div class="form-group"> <label for="website">Website</label>
            <input type="text" class="form-control" name="website"
            id="website" placeholder="Website Url" value="{{
            @$consultancy->website }}"> </div> </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="file">Document Upload</label>
                    <input type="file" class="form-control upload-document" id="input-file-now" name="image[]" accept=".png,.jpg,.jpeg" class="dropify1" data-default-file="{{ asset('avatar.png')  }}" multiple>
                  </div>
                </div>
              </div>  
            </div>  

            <div class="col-12 pt-2">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="payout">Payout(Days)</label>
                    <input type="number" class="form-control" name="payout" id="payout" placeholder="payout Days" value="{{ @$consultancy->payout }}" maxlength="5" onkeypress="return isNumberKey(event,this)">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="commission">Commission(%)</label>
                    <input type="number" class="form-control" name="commission" id="commission" placeholder="Commission(%)" value="{{ @$consultancy->commission }}" maxlength="5" onkeypress="return isNumberKey(event,this)">
                  </div>
                </div>
              </div>
            </div>    

            <div class="col-12 pt-2">
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" name="detail" rows="4" placeholder="Enter text ...">{{ @$consultancy->detail }}</textarea>
              </div>
            </div>

            <div class="col-12 pt-2">
              <div class="form-group">
                <label for="feedback">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Enter text ...">{{ @$consultancy->feedback }}</textarea>
              </div>
            </div>

            <hr>
          </div> 
          <div class="row py-4">
              <div class="col-md-12 text-center">
                  <button type="button" id="save-form" class="btn btn-light-success btn-sm py-1">
                    <i class="fa fa-save fa-sm"></i>&nbsp;@lang('app.save')
                  </button>
                  <button type="reset" class="btn btn-light-warning btn-sm py-1">
                    <i class="fa fa-spinner fa-sm"></i>&nbsp;@lang('app.reset')
                  </button>
              </div>
          </div> 
         <!-- <button type="button" id="save-form" class="btn btn-success">
            <i class="fa fa-check"></i>Update
          </button> -->
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
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

                    var url = "{{ route('admin.consultancy.deleterecord',':id') }}";
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

  $('#save-form').click(function () {
            $.easyAjax({
                url			: '{{route('admin.consultancy.update', $user->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file		: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush