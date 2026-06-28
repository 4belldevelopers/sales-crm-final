@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">



    <style>
        @media (min-width: 768px) {

            .seven-cols .col-md-1,
            .seven-cols .col-sm-1,
            .seven-cols .col-lg-1 {
                width: 100%;
                *width: 100%;
            }
        }

        @media (min-width: 992px) {

            .seven-cols .col-md-1,
            .seven-cols .col-sm-1,
            .seven-cols .col-lg-1 {
                width: 14.285714285714285714285714285714%;
                *width: 14.285714285714285714285714285714%;
            }
        }

        @media (min-width: 1200px) {

            .seven-cols .col-md-1,
            .seven-cols .col-sm-1,
            .seven-cols .col-lg-1 {
                width: 14.285714285714285714285714285714%;
                *width: 14.285714285714285714285714285714%;
            }
        }

        .mb-10 {
            margin-bottom: .5rem !important;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#cvaccess_startdate").datepicker({
                startDate: '+0d'
            });
        });
        var dateToday = new Date();
        $(function() {
            $("#cvaccess_startdate").datepicker({
                minDate: dateToday
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> {{  @$update ? 'Update Feature' : 'Create New' }}</h4>

                    <form id="featureform" method="POST" class="ajax-form" name="createForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">Title</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ (@$update) ? $Packagefeature->title : '' }}">
                                        <input type="hidden" class="form-control" id="token_id" name="token_id"
                                        value="{{ (@$update) ? $Packagefeature->id : '' }}">
                                </div>

                            </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_phone">Featur Type</label>
                                        <select name="cvaccess_status" id="cvaccess_status" class="form-control form-control-solid"> 
                                            <option value="1" {{ (@$update) ? (@$Packagefeature->features_type == 1) ? 'selected' : '' : '' }} style="background-color:#FFFFFF; color:#000000">Operations </option>  
                                            <option value="2" {{ (@$update) ? (@$Packagefeature->features_type == 2) ? 'selected' : '' : '' }} style="background-color:#FFFFFF; color:#000000">Sales </option>  
                                        </select>
                                    </div>
                                </div>
                        </div>

                        <div class="row">
                                             

                            <div class="col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-sm-8">@lang('app.status')</label>

                                    <div class="col-sm-4">
                                        <div class="switchery-demo">
                                            <input type="checkbox" {{ (@$update) ? (@$Packagefeature->features_type == 1) ? 'checked' : '' : '' }}                                                 class="js-switch" name="status_pc" data-color="#99d683" data-size="small" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-row row fv-row">
                            <div class="col-12 text-center py-10">


                                <button type="button" id="save-form"
                                    class="btn btn-success waves-effect waves-light m-r-10">
                                   {{  @$update ? 'Update' : 'Save' }} 
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
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

    <script>

        $('#save-form').click(function() {

            var formData = new FormData($('#featureform')[0]);
            $("#save-form").prop("disabled", true);
                  $("#save-form").html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Waiting...`
                  );
                  $('#save-form').prop('disabled', true);
            $.ajax({
                url: '{{ route('superadmin.package.add_edit_package_feature', @$update ? @$Packagefeature->id : '0') }}' + '?_token=' +
                    '{{ csrf_token() }}',
                type: "POST",
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                //container		: '#createForm',

                redirect: true,
                file: true,
                //data			: $('#createForm').serialize(),
                success: function(response) {
                    $('#save-form').prop('disabled', false);
                    console.log(response);
                    if (response.status == 'success') {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function(result) {
                            window.location = response.url;
                        });
                    } else {
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                },
                error: function(response) {

                    Swal.fire({
                        text: "Sorry, looks like there are some errors, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            })


        });
    </script>
    <script>
        var input = document.querySelector('input[name=boostkeywords]');
        new Tagify(input)
    </script>
@endpush
