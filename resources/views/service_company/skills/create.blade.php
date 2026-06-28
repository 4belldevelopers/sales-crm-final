@extends('layouts.service_company_layout')
<style>
.content-header{display: none;}
#btnFiterSubmitSearch{padding: .5em .2em!important;}

.input-group > .custom-select:not(:last-child), .input-group > .form-control:not(:last-child)
{
    margin: 0em!important;
}
</style>
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title pt-2 pb-2"><i class="fa fa-plus-square"></i>&nbsp;Add Skills</h4>
        <form class="ajax-form" method="POST" id="createForm">
          @csrf
          <div id="education_fields">
            <div class="row">
              <div class="col-sm-6 nopadding">
                <div class="form-group">
                  <div class="input-group mb-1">
                    <input type="text" name="name[]" class="form-control" placeholder="Enter Your&nbsp;@lang('menu.skills')&nbsp;@lang('app.name')..." value="">
                    <div class="input-group-append">
                      <button class="btn btn-light-success" type="button" id="add-more"> <i class="fa fa-plus"></i> </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 text-center pt-3">
              <button type="button" id="save-form" class="btn btn-light-success px-2 py-1"> <i class="fa fa-save"></i>&nbsp;@lang('app.save') </button>
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
<script>
    // For select 2
    $(".select2").select2();

    var room = 1;

    
    $('#add-more').click(function () {
        room++;

        var divtest = `
            <div class="row removeclass${room}">
                <div class="col-sm-6 mb-1">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="name[]" class="form-control" placeholder="Enter Your&nbsp;@lang('menu.skills') @lang('app.name')...">
                            <div class="input-group-append"> 
                                <button class="btn btn-light-danger" type="button" onclick="remove_education_fields(${room});">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>`;
            
        $('#education_fields').append(divtest);
        $(`.removeclass${room} input`).focus();
    })

    function remove_education_fields(rid) {
        $('.removeclass' + rid).remove();
    }

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('service_company.skills.store')}}',
            container: '#createForm',
            type: "POST",
            redirect: true,
            data: $('#createForm').serialize()
        })
    });
</script>
@endpush