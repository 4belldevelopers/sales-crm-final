@extends('layouts.app')
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}<?php echo TM_VERSION; ?>">
@endpush
@section('content')
<style>
.error{ color:#FF0000;}
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
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">      
        <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
          @csrf
          <div class="row">
            
			     <div class="col-md-12">
              <div class="form-group">
                <h4 class="card-title pt-2 pb-2">
                  <i class="fa fa-plus-square"></i>&nbsp;Add Vendor
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
                    <input type="text" class="form-control" name="email" id="email" placeholder="Enter Your Email">
                  </div>
                </div>

    			       <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">Password </label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Your Password">
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
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Your Consultancy Name">
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
                            <label>@lang('app.mobile')&nbsp;Number</label>
                              <input type="number" class="form-control" name="mobile" placeholder="Enter Your Number">
                          </div>
                      </div>
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="form-group">
                      <label>@lang('app.mobile')</label>
                      <input type="text" class="form-control" name="mobile" placeholder="Enter Mobile Number">
                  </div>
                </div> -->    
              </div>   
            </div>  
			
            <div class="col-12 pt-2">
              <div class="row">        
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contact_person_name">Contact Person name</label>
                    <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" placeholder="Enter Consultancy Contact Person Name">
                  </div>
                </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" name="status" id="status">
                    <option value="active">Active</option>
                    <option value="inactive">In-Active</option>
                       </select>
                    </div>
                  </div>
                </div>  
            </div>  					
						
            <div class="col-12 pt-2">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="website">Website</label>
                    <input type="text" class="form-control" name="website" id="website" placeholder="Enter URL">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="file">Document Upload</label>
                    <input type="file" class="form-control upload-document"  name="image[]" accept=".png,.jpg,.jpeg" multiple>
                  </div>
                </div>
              </div>  
            </div>  

            <div class="col-12 pt-2">
              <div class="row">  
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="payout">Payout(Days)</label>
                    <input type="number" class="form-control" name="payout" id="payout" placeholder="Enter Payout Days" maxlength="5" onkeypress="return isNumberKey(event,this)">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="commission">Commission(%)</label>
                    <input type="number" class="form-control filterme" name="commission" id="commission" placeholder="Enter Commission(%)" maxlength="5" onkeypress="return isNumberKey(event,this)">
                  </div>
                </div>
              </div>  
            </div>  

            <div class="col-md-12 pt-2">
              <div class="form-group">
                <label for="detail">Detail</label>
                <textarea class="form-control" id="detail" name="detail" rows="4" placeholder="Enter Description"></textarea>
              </div>
            </div>

            <div class="col-md-12 pt-2">
              <div class="form-group">
                <label for="feedback">Feedback</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Write a Message..."></textarea>
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
        </form>
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
		


        // For select 2
        $(".select2").select2();

        var room = 1;

        $('#add-more').click(function () {

            room++;
            var objTo 			= document.getElementById('education_fields')
            var divtest 		= document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv 			= 'removeclass' + room;
            divtest.innerHTML 	= '<div class="row"><div class="col-sm-9 nopadding"><div class="form-group"><div class="input-group"> <input type="text" name="name[]" class="form-control" placeholder="Client name"><div class="input-group-append"> <button class="btn btn-danger" type="button" onclick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button></div></div></div></div><div class="clear"></div></row>';

            objTo.appendChild(divtest)
        })

        function remove_education_fields(rid) {
            $('.removeclass' + rid).remove();
        }

        $('#save-form').click(function () {
		
			  

            $.easyAjax({
                url			: '{{route('admin.consultancy.store')}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file: true
               
            })
        });
    </script>

 <script type="text/javascript">

        function isNumberKey(evt, obj) {

            var charCode = (evt.which) ? evt.which : event.keyCode
            var value = obj.value;
            var dotcontains = value.indexOf(".") != -1;
            if (dotcontains)
                if (charCode == 46) return false;
            if (charCode == 46) return true;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

</script>
@endpush