@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <style>
        body
        {
            overflow-x: hidden;
        }
    </style>
@endpush
@section('content')

<!--begin::toolbar-->
<div class="toolbar" id="kt_toolbar">
  <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
      <div class="d-flex flex-column">
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Update Language
       </h1>
      </div>
      <span class="h-20px border-gray-200 border-start mx-4"></span>
      <span class="text-dark-800 fw-bold" id="kt_subheader_total">Enter Language details and submit</span> </div>
  </div>
</div>
<!--end::toolbar-->

<!--begin::Form-->
<div class="post d-flex flex-column-fluid mt-5 mb-5" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
        <div class="row gy-5 g-xl-8">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form role="form" id="editLangForm"  class="ajax-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $language->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold text-dark fs-6 mb-0 required">@lang('app.language') @lang('app.name')</label>
                                <input type="text" name="language_name" id="language_name" class="form-control form-control-lg form-control-solid" value="{{ $language->language_name }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-4">
                                <label class="form-label fw-bold text-dark fs-6 mb-0 required">@lang('app.language') @lang('app.code')</label>
                                <input type="text" name="language_code" id="language_code" class="form-control form-control-lg form-control-solid" value="{{ $language->language_code }}">
                            </div>
                        </div>
                        @if ($language->language_code !== $global->locale)
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="form-label fw-bold text-dark fs-6 mb-0 required">@lang('app.language') @lang('app.status')</label>

                                    <select name="status" id="lang-status" class="form-control form-control-lg form-control-solid">
                                        <option @if ($language->status == 'enabled')
                                            selected
                                        @endif value="enabled">@lang('app.enable')</option>
                                        <option @if ($language->status == 'disabled')
                                            selected
                                        @endif value="disabled">@lang('app.disable')</option>
                                    </select>
                                </div>
                            </div>
                        @else
                            <input id="lang-status" type="hidden" name="status" value="enabled">
                        @endif
                    </div>
                    <div class="form-group form-group d-flex justify-content-center pt-10">
                        <div class="mr-2">
                        <button type="button" id="save-form" class="btn btn-success btn-light-round"><i
                                    class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                    </div>
                </form>
            </div>
         </div>
        </div>
      </div>
    </div>
</div>
<!--end::Form-->
@endsection

@push('footer-script')

<script>
    $(document).on('click', '#save-form', function (e) {
            e.preventDefault();
            

        var url = "{{route('superadmin.language-settings.update', $language->id)}}";
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var form = $('#editLangForm')[0];
        var formData = new FormData(form);
      
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT');
        $.ajax({
                url: url,
                type: "POST", 
                data: formData,
       			processData			: false,
       			contentType			: false,
                beforeSend: function() {
                    $('#kt_activitiesDefaultPackage_body').html('<img src="' + baseurl +
                        '" height="35px" width="100%">');
                },
                success: function(response) {
                     Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    });
                }
            });
    });
</script>
@endpush
