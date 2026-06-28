@extends('layouts.superadmin_layout')
<style>
.content .card 
{
    box-shadow: none!important;
}
.content-header
{
    display: none;
}

#btnFiterSubmitSearch
{
    padding:0.5rem 0.20rem!important;
}    
.bootstrap-select>.dropdown-toggle
{
    color: #3f4254;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #d2d2d2;
    box-shadow: 0 1px 1px rgb(0 0 0 / 8%);
    border-radius: 3px;
}
.btn-info:not(:disabled):not(.disabled):active
{
    color: #333;
    background-color: #ececec;
    border-color: #ececec;
}

.dropify-wrapper
{
    border: 2px dashed #E5E5E5!important;
    border-radius: 3px;
    -webkit-transition: border-color .15s linear;
    transition: border-color .15s linear;
}

</style>
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2"> <i class="fa fa-plus-square"></i>&nbsp;Add Profile </h4>
        <form id="editSettings" class="ajax-form">
          @csrf
          <div class="row">
            <div class="col-md-4 offset-md-4">
              <div class="form-group">
                <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify" data-default-file="{{ asset('avatar.png')  }}"/>
                <!-- <div class="col-12 text-center">    
                                        <label for="exampleInputPassword1">Upload Profile</label>
                                    </div> -->
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">@lang('app.name')</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Your Name..">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.mobile')</label>
                <div class="form-row">
                  <div class="col-sm-5">
                    <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" data-width="100%" >
                      
                                                @foreach ($calling_codes as $code => $value)
                                                    
                      <option value="{{ $value['dial_code'] }}">{{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                      
                                                @endforeach
                                            
                    </select>
                  </div>
                  <div class="col-sm-7">
                    <input type="number" class="form-control" name="mobile" placeholder="Enter Your Mobile..">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">@lang('app.email')</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your Email..">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_phone">@lang('app.password')</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Your Password...">
              </div>
            </div>
          </div>
          <div class="col-12 mt-2 px-0">
            <div class="form-group">
              <label for="company_phone">@lang('modules.permission.roleName')</label>
              <select class="form-control" name="role_id" id="role_id">
                
                                    @foreach($roles as $role)
                                        
                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                
                                    @endforeach
                                
              </select>
            </div>
          </div>
          <div class="row mb-3 mt-3">
            <div class="col-12 text-center">
              <button type="button" id="save-form" class="btn btn-light-success btn-sm mr-2"> <i class="fa fa-save"></i>&nbsp;@lang('app.save') </button>
              <button type="reset" class="btn btn-light-warning btn-sm"> <i class="fa fa-spinner"></i>&nbsp;@lang('app.reset') </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script>
        $('.dropify').dropify({
            messages: {
                default: "Drag or Drop Your Profile",
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

		
        $('#save-form').click(function () {
            $.ajax({
						url				: '{{route('service_company.team.store')}}',
						container		: '#editSettings',
						type			: "POST",
						redirect		: true,
						data			: $('#editSettings').serialize(),
						file			: true
				})
				
        });
    </script>
@endpush