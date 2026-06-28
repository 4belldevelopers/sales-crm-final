@extends('layouts.staffing_company_layout')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Member Information</h4>
        <form class="ajax-form" method="POST" id="createForm" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" value="{{ $cc_info->contact_person_name }}" placeholder="Name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email </label>
                <input type="email" class="form-control" name="contact_person_email" id="contact_person_email"  value="{{ $cc_info->contact_person_email  }}" placeholder="Email">
              </div>
            </div>
            <div class="col-md-6 mt-5">
              <div class="form-group">
                <label for="website">Contact Number</label>
                <input type="text" class="form-control" name="contact_person_number" id="contact_person_number" value="{{ $cc_info->contact_person_number }}" placeholder="Contact Number">
              </div>
            </div>
            <div class="col-md-6 mt-5">
              <div class="form-group">
                <label for="commission">Designation</label>
                <input type="text" class="form-control" name="contact_person_designation" id="contact_person_designation" value="{{ $cc_info->contact_person_designation  }}" placeholder="Designation">
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
                url			: '{{route('staffing_company.clients.update_info', $cc_info->id)}}',
                container	: '#createForm',
                type		: "POST",
                redirect	: true,
				file		: true,
                data		: $('#createForm').serialize()
            })
        });
    </script>
@endpush