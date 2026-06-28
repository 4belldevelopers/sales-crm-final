@extends('layouts.service_company_layout')
@push('head-script')


@endpush
@section('content')


<!--end::Toolbar-->
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pb-0" id="kt_content">
  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card card-custom gutter-b">
        <!--begin::Body-->

        
        <div class="card-body p-0">
            <div class="col-lg-4">

                <div class="form-floating border rounded">
                    <select class="form-select" data-placeholder="..." id="city_id">
                        <option></option>
                        <option value="0" data-kt-select2-user="assets/media/avatars/300-6.jpg">Emma Smith</option>
                        <option value="1" data-kt-select2-user="assets/media/avatars/300-1.jpg">Max Smith</option>
                    </select>
                    <label for="city_id">Select a city</label>
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Address:</label>
                <select class="form-control select2 select2-hidden-accessible d-block w-100" id="kt_select2_3" name="kt_select2_3" multiple="multiple" tabindex="-1" aria-hidden="true">
                  <optgroup label="Select Address">
                  <option value="sl" selected="selected">Select Address</option>
                  <option value="1" class="kt_select2_3">1</option>
                  <option value="2" class="kt_select2_3">2</option>
                  <option value="3" class="kt_select2_3">3</option>
                  <option value="4" class="kt_select2_3">4</option>
                  <option value="5" class="kt_select2_3">5</option>
                  <option value="6" class="kt_select2_3">6</option>
                  <option value="7" class="kt_select2_3">7</option>

                  </optgroup>
                </select>
                <span class="form-text text-muted">Please select your Address</span> </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection



@push('footer-script')


<!--begin::Page Scripts-->
<script src="{{ asset('demo/v7/js/plugins.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/v7scripts.bundle.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/v7/js/pages/crud/forms/widgets/select2.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('demo/cdn/js/bootstrap-select.js') }}<?php echo TM_VERSION; ?>"></script>
<script>
   $(document).ready(function() {
    $.noConflict();  
        // Format options
        var optionFormat = function(item) {
            if ( !item.id ) {
                return item.text;
            }

            var span = document.createElement('span');
            var imgUrl = item.element.getAttribute('data-kt-select2-user');
            var template = '';

            template += '<img src="' + imgUrl + '" class="rounded-circle h-20px me-2" alt="image"/>';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        }

        // Init Select2 --- more info: https://select2.org/
        $('#city_id').select2({
            templateSelection: optionFormat,
            templateResult: optionFormat
        });
			
   
   });	
   	
   	
   
</script>

@endpush 