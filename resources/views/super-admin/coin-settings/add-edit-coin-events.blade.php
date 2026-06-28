@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">@lang('app.createNew')</h4>

                    <form id="editSettings" class="ajax-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label for="company_name">Event Type</label>
                                <select name="event_type" id="event_type" class="form-control form-control-solid">
                                    <option value=''>---Select Event Type---</option>
                                    <option value="operational" {{ ($update && $coin_packages->event_type == 'operational') ? 'selected' : '' }} style="background-color:#FFFFFF; color:#000000">Operational
                                    </option>
                                    <option value="sales" {{ ($update && $coin_packages->event_type == 'sales') ? 'selected' : '' }} style="background-color:#FFFFFF; color:#000000">Sales
                                    </option>
                                </select>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="coin_package">Event Name</label>
                                    <input type="text" class="form-control" id="title" name="title"  placeholder="Event name" 
                                    value="{{ ($update && $coin_packages->title) ? $coin_packages->title : '' }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_rate_label">Default Rate</label>
                                    <input type="text" class="form-control" id="default_rate" name="default_rate"
                                        value="{{ ($update && $coin_packages->default_rate) ? $coin_packages->default_rate : '0' }}">
                                </div>
                            </div>


                        </div>
                        
                        <div class="row">   
                            <label class="form-label" style="padding-top:10px;">&nbsp;</label>
                            <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="minimum_required_label">Minimum required coin</label>
                                    <input type="number" class="form-control form-control-solid" name="minimum_required"
                                        id="minimum_required" value="{{ ($update && $coin_packages->minimum_required) ? $coin_packages->minimum_required : '0' }}" placeholder="0.00" >
                                </div>
                            </div>

                           


                            <div class="col-md-4">
                                <label for="company_name">Deduction_type</label>
                                <select name="deduction_type" id="deduction_type" class="form-control form-control-solid">
                                    <option value=''>---Select Deduction_type---</option>
                                    <option value="daily" {{ ($update && $coin_packages->deduction_type == 'daily') ? 'selected' : '' }} style="background-color:#FFFFFF; color:#000000">Daily
                                    </option>
                                    <option value="onetime" {{ ($update && $coin_packages->deduction_type == 'onetime') ? 'selected' : '' }} style="background-color:#FFFFFF; color:#000000">Onetime
                                    </option>
                                </select>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description_label">Description</label>
                                <input type="text" class="form-control form-control-solid" name="description"
                                    id="description" value="{{ ($update && $coin_packages->description) ? $coin_packages->description : '' }}" placeholder="0.00">
                                </div>
                            </div>

                          
                          
                            <input type="hidden" class="form-control" value="{{ ($update && $coin_packages->id) ? $coin_packages->id : '' }}" id="token_id" name="token_id">
                        </div><br >

                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                           
                            <input class="form-check-input chkStatus" name="status" type="checkbox" name="staus" {{ (@$coin_packages->status) ? 'checked' : '' }}>
                         </div> 


                       
                              
                        <div class="form-row row fv-row">
                            <div class="col-12 text-center py-10">

                            <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                @lang('app.save')
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

       


    $(document).ready(function() {



       $('#coin_package').change(function(e){

        var selectElement = document.getElementById("coin_package");
        var selectedOption = selectElement.options[selectElement.selectedIndex];
         var daysValue = selectedOption.getAttribute("data-days");
         $('#durations').val(daysValue);
         if (daysValue > 0) {
            $('#durations').prop('readonly', true);
            $('label[for="durations_label"]').text('Duration (In Days)');
        } else {
            $('#durations').prop('readonly', false);
            $('label[for="durations_label"]').text('Custom Duration (In Days)');
        }
       });

       $('#total_coins').keyup(function(e){
        total_coins();
       });
       $('#discount').keyup(function(e){
        total_coins();
      });

        function total_coins(){
                let value = parseFloat($('#total_coins').val());
                let percentage = parseFloat($('#discount').val());

                if (isNaN(value)) {
                    value = 0; // Default to 0 if invalid
                }
                if (isNaN(percentage)) {
                    percentage = 0; // Default to 0 if invalid
                }

                var total_coins = value; // Add the percentage amount to the original value
                if(percentage !=''){
                    let additionalAmount = (percentage / 100) * value;
                    var total_coins = value - additionalAmount; // Add the percentage amount to the original value
                }
                
            
                $('#total_price').val(total_coins.toFixed(2)); // Set the value with two decimal points
        }



    });

      
      var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());

        });

      

        $('#save-form').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = new FormData($('#editSettings')[0]);
            $.ajax({
                url: '{{ route('superadmin.company.coinEventsStore') }}',
                type: "POST",
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                //container		: '#createForm',
                redirect: true,
                file: true,
                //data			: $('#createForm').serialize(),
                success: function(response) {
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
                            text: "Sorry, looks like there are some errors, please try again.",
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

                    if (response.responseJSON && response.responseJSON.errors) {
                            let errorMessages = Object.values(response.responseJSON.errors).flat().join('<br>');

                            Swal.fire({
                                html: `<p>Sorry, looks like there are some validation:</p><p style="color: red;text-align: left;">${errorMessages}</p>`,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                    } else {
                        Swal.fire({
                            text: "Sorry, looks like there was an issue. Please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                }
            })


});


        $('#save-form').click(function() {
            $.easyAjax({
                url: '{{ route('superadmin.packages.store') }}',
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>
@endpush
